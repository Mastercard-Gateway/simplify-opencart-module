<?php
/*
@LICENSE@
 */
class ControllerPaymentSimplifyCommerce extends Controller {
	protected function index() {
		$this->language->load('payment/simplifycommerce');

		$this->data['text_title'] = htmlentities($this->config->get('simplifycommerce_title'));
		$this->data['text_payment_title'] = "Pay by  " .  $this->config->get('simplifycommerce_title');
		$this->data['text_existing'] = $this->language->get('text_existing');
		$this->data['text_forget'] = $this->language->get('text_forget');
		$this->data['text_title'] = $this->language->get('text_title');
		$this->data['text_new'] = $this->language->get('text_new');
		$this->data['text_pay'] = $this->language->get('text_pay');
		$this->data['text_processing'] = $this->language->get('text_processing');
		$this->data['text_pay_by'] = $this->language->get('text_pay_by');
		$this->data['text_success'] = $this->language->get('text_success');
		$this->data['text_failure'] = $this->language->get('text_failure');

		$this->data['entry_name_on_card'] = $this->language->get('entry_name_on_card');
		$this->data['entry_card_number'] = $this->language->get('entry_card_number');
		$this->data['entry_card_expiration'] = $this->language->get('entry_card_expiration');
		$this->data['entry_cvc'] = $this->language->get('entry_cvc');
		$this->data['entry_save_card'] = $this->language->get('entry_save_card');
		$this->data['button_pay'] = $this->language->get('button_pay');

		if ($this->config->get('simplifycommerce_test') == 1) {
			$this->data['pub_key'] = trim($this->config->get('simplifycommerce_testpubkey'));
		} else {
			$this->data['pub_key'] = trim($this->config->get('simplifycommerce_livepubkey'));
		}		
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		$this->data['amount'] = 100 * $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		$this->data['currency'] = strtolower($order_info['currency_code']);
		$this->data['description'] = $this->session->data['order_id'];

		$this->data['months'] = array();
		
		for ($i = 1; $i <= 12; $i++) {
			$this->data['months'][] = array(
				'text'  => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)), 
				'value' => sprintf('%02d', $i)
			);
		}
		
		$today = getdate();

		$this->data['year_expire'] = array();

		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
			$this->data['year_expire'][] = array(
				'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
				'value' => strftime('%y', mktime(0, 0, 0, 1, 1, $i)) 
			);
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/simplifycommerce.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/simplifycommerce.tpl';
		} else {
			$this->template = 'default/template/payment/simplifycommerce.tpl';
		}	
		
		$this->render();
	}

	public function charge() {

		error_reporting(E_ALL);

		require_once('system/library/simplifycommerce/lib/Simplify.php');

		$this->language->load('payment/simplifycommerce');
		$this->load->model('checkout/order');

		if ($this->config->get('simplifycommerce_test') == 1) {
			$secret_key = trim($this->config->get('simplifycommerce_testsecretkey'));
			$public_key = trim($this->config->get('simplifycommerce_testpubkey'));
		} else {
			$secret_key = trim($this->config->get('simplifycommerce_livesecretkey'));
			$public_key = trim($this->config->get('simplifycommerce_livepubkey'));
		}	
		try{

			$order_id = $this->session->data['order_id'];
			$order_info = $this->model_checkout_order->getOrder($order_id);
			$order_status = $order_info['order_status_id'];

			$c = array(
				'token' => $_POST['token'],
				'amount' => $_POST['amount'],		        
				'description' => 'OpenCart - order id: '.$order_id,
				'reference' => $order_id,
				'currency' => strtoupper($_POST['currency'])
			);

			// check if the order has already been processed by checking the status
			if ($order_status == $this->config->get('simplifycommerce_order_status_id')){

				// clean the session
				if (isset($this->session->data['order_id'])) {
					$this->cart->clear();

					unset($this->session->data['shipping_method']);
					unset($this->session->data['shipping_methods']);
					unset($this->session->data['payment_method']);
					unset($this->session->data['payment_methods']);
					unset($this->session->data['guest']);
					unset($this->session->data['comment']);
					unset($this->session->data['order_id']);	
					unset($this->session->data['coupon']);
					unset($this->session->data['reward']);
					unset($this->session->data['voucher']);
					unset($this->session->data['vouchers']);
				}

				// order already complete
				throw new Exception($this->language->get('message_already_processed'));
			}else if ($order_info){
				$charge = Simplify_Payment::createPayment($c, $public_key, $secret_key);
                if ($charge->paymentStatus != "APPROVED") {
                    $this->log->write("payment not approved; status: " . $charge->paymentStatus);
                    throw new Exception($this->language->get('payment_declined'));
                }

				$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('simplifycommerce_order_status_id'), '', true);
			}
			else{
				throw new Exception($this->language->get('message_no_order'));
			}

		} catch(Exception $e) {
			  $json['error'] = $e->getMessage();
			  echo json_encode($json);
			  $this->log->write($e->getMessage());
			  exit();
		}

		$json['success'] = $this->url->link('checkout/success', '', 'SSL');
		
		echo json_encode($json);
	}

	public function callback() {
		header("HTTP/1.1 200 OK");	

		try{

			$body = @file_get_contents('php://input');
                        $event = Simplify_Event::createEvent(array('payload' => $body));
			$this->log->write($event->name);

			switch($event->name) {
				case 'charge.create':
					$this->model_checkout_order->update($event->data->description, $this->config->get('simplifycommerce_order_status_id'), '', true);
					break;
				case 'charge.failure':
					$this->model_checkout_order->update($event->data->description, $this->config->get('simplifycommerce_declined_order_status_id'), '', true);
					break;			
				default:
					break;
			}

		} catch(Exception $e) {
			  $this->log->write($e->getMessage());
			  exit();
		}
	
	}
}
?>
