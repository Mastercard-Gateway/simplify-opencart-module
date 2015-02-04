<?php
/*
@LICENSE@
 */
class ControllerPaymentSimplifyCommerce extends Controller {

	private function calcAmount($order_info) {
		return 100 * $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
	}

	public function index() {
		$this->load->language('payment/simplifycommerce');
		$this->load->model('checkout/order');

		$data['text_card_details'] = $this->language->get('text_card_details');
		$data['text_pay'] = $this->language->get('text_pay');

		$data['entry_name_on_card'] = $this->language->get('entry_name_on_card');
		$data['entry_card_number'] = $this->language->get('entry_card_number');
		$data['entry_card_expiration'] = $this->language->get('entry_card_expiration');
		$data['entry_cvc'] = $this->language->get('entry_cvc');
		$data['button_pay'] = $this->language->get('button_pay');

		if ($this->config->get('simplifycommerce_test') == 1) {
			$data['pub_key'] = trim($this->config->get('simplifycommerce_testpubkey'));
		} else {
			$data['pub_key'] = trim($this->config->get('simplifycommerce_livepubkey'));
		}		
		
		$data['simplifycommerce_payment_mode'] = $this->config->get('simplifycommerce_payment_mode');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$data['store_name'] = $order_info["store_name"];

		$data['amount'] = $this->calcAmount($order_info);

		$data['currency'] = strtolower($order_info['currency_code']);

		$data['description'] = $this->session->data['order_id'];

		$data['redirect_url'] =  $link = $this->url->link('payment/simplifycommerce/charge', '', 'SSL');

		$data['months'] = array();
		
		for ($i = 1; $i <= 12; $i++) {
			$data['months'][] = array(
				'text'  => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)), 
				'value' => sprintf('%02d', $i)
			);
		}
		
		$today = getdate();

		$data['year_expire'] = array();

		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
			$data['year_expire'][] = array(
				'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
				'value' => strftime('%y', mktime(0, 0, 0, 1, 1, $i)) 
			);
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/simplifycommerce.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/payment/simplifycommerce.tpl', $data);
		} else {
			return $this->load->view('default/template/payment/simplifycommerce.tpl', $data);
		}
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

			if(isset($this->session->data['order_id'])){
				$order_id = $this->session->data['order_id'];
			} else {
				throw new Exception($this->language->get('message_no_order'));
			}

			$order_info = $this->model_checkout_order->getOrder($order_id);
			$order_status = $order_info['order_status_id'];

			$c = array(
				'token' => $_REQUEST['cardToken'],
				'amount' => $this->calcAmount($order_info),
				'description' => 'OpenCart - order id: '.$order_id,
				'reference' => $order_id,
				'currency' => strtoupper($order_info['currency_code'])
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
				$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('simplifycommerce_order_status_id'), '', true);
			}
			else{
				throw new Exception($this->language->get('message_no_order'));
			}

		} catch(Simplify_ApiException $e) {
			$this->log->write($e->describe());
			$mess = "";
			$t = $e->getErrorData();
			if(isset($t["error"]["fieldErrors"][0]["message"])){
				$mess = $t["error"]["fieldErrors"][0]["message"];
			} else {
				$mess = $e->getMessage();
			}
			if($this->config->get("simplifycommerce_payment_mode")){
				$this->session->data['error'] = $mess;
				return $this->response->redirect($this->url->link('checkout/checkout','','SSL'));
			} else {
				$json['error'] = $mess;
				echo json_encode($json);
				exit();
			}
		} catch (Exception  $e) {
			$this->log->write($e->getMessage());
			if($this->config->get("simplifycommerce_payment_mode")){
				$this->session->data['error'] = $e->getMessage();
				return $this->response->redirect($this->url->link('checkout/checkout','','SSL'));
			} else {
				$json['error'] = $e->getMessage();
				echo json_encode($json);
				exit();
			}
		}

		if($this->config->get("simplifycommerce_payment_mode")){
			//hosted payments
			return $this->response->redirect($this->url->link('checkout/success'));
		} else {
			//standard payments mode
			$json['success'] = $this->url->link('checkout/success');
			echo json_encode($json);
		}
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
