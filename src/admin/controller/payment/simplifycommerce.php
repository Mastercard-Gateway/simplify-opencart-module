<?php
/**
 * Copyright (c) 2013-2023 Mastercard
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Opencart\Admin\Controller\Extension\SimplifyCommerce\Payment;
use Opencart\Admin\Model\Extension\SimplifyCommerce\Payment;
use Opencart\System\Library\Mail;
class SimplifyCommerce extends \Opencart\System\Engine\Controller
{
    private array $error = [];
    private $separator = '';

    public function __construct($registry) {
        parent::__construct($registry);

		if (VERSION >= '4.0.2.0') {
			$this->separator = '.';
		} else {
			$this->separator = '|';
		}
    }

    public function index()
    {
        $this->install();

        $this->load->language('extension/SimplifyCommerce/payment/simplifycommerce');
        $this->load->model('extension/SimplifyCommerce/payment/simplifycommerce');

        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->addScript('../extension/SimplifyCommerce/admin/view/javascript/spectrum.js');
        $this->document->addStyle('../extension/SimplifyCommerce/admin/view/stylesheet/spectrum.css');

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('payment_simplifycommerce', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension',
                'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
        }

        $data = [
            'heading_title' => $this->language->get('heading_title'),
            'text_edit' => $this->language->get('text_edit'),
            'text_enabled' => $this->language->get('text_enabled'),
            'text_disabled' => $this->language->get('text_disabled'),
            'text_all_zones' => $this->language->get('text_all_zones'),
            'text_yes' => $this->language->get('text_yes'),
            'text_no' => $this->language->get('text_no'),
            'text_successful' => $this->language->get('text_successful'),
            'text_declined' => $this->language->get('text_successful'),
            'text_off' => $this->language->get('text_off'),
            'text_test' => $this->language->get('text_test'),
            'text_prod' => $this->language->get('text_prod'),
            'text_payment_hosted' => $this->language->get('text_payment_hosted'),
            'text_payment_standard' => $this->language->get('text_payment_standard'),
            'text_txn_mode_authorize' => $this->language->get('text_txn_mode_authorize'),
            'text_txn_mode_pay' => $this->language->get('text_txn_mode_pay'),

            'entry_livesecretkey' => $this->language->get('entry_livesecretkey'),
            'entry_livepubkey' => $this->language->get('entry_livepubkey'),
            'entry_testsecretkey' => $this->language->get('entry_testsecretkey'),
            'entry_testpubkey' => $this->language->get('entry_testpubkey'),
            'entry_title' => $this->language->get('entry_title'),
            'entry_title_help' => $this->language->get('entry_title_help'),
            'entry_check_address_line_1' => $this->language->get('entry_check_address_line_1'),
            'entry_check_zip' => $this->language->get('entry_check_zip'),
            'entry_webhook_url' => $this->language->get('entry_webhook_url'),
            'entry_webhook_url_help' => $this->language->get('entry_webhook_url_help'),
            'entry_test' => $this->language->get('entry_test'),
            'entry_payment_mode' => $this->language->get('entry_payment_mode'),
            'entry_button_color' => $this->language->get('entry_button_color'),
            'entry_order_status' => $this->language->get('entry_order_status'),
            'entry_declined_order_status' => $this->language->get('entry_declined_order_status'),
            'entry_geo_zone' => $this->language->get('entry_geo_zone'),
            'entry_status' => $this->language->get('entry_status'),
            'entry_sort_order' => $this->language->get('entry_sort_order'),
            'entry_txn_mode' => $this->language->get('entry_txn_mode'),

            'button_save' => $this->language->get('button_save'),
            'button_cancel' => $this->language->get('button_cancel'),
        ];

        $err_arr = [
            'warning',
            'permission',
            'livesecretkey',
            'livepubkey',
            'testsecretkey',
            'testpubkey',
            'title',
            'button_color'
        ];

        foreach ($err_arr as $val) {
            $data['error_' . $val] = $this->error[$val] ?? '';
        }
        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        ];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension',
                'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
        ];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/SimplifyCommerce/payment/simplifycommerce',
                'user_token=' . $this->session->data['user_token'], true)
        ];
        
        $data['action'] = $this->url->link('extension/SimplifyCommerce/payment/simplifycommerce',
            'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension',
            'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

        $data['payment_simplifycommerce_test'] = $this->request->post['payment_simplifycommerce_test'] ?? $this->config->get('payment_simplifycommerce_test');

        $data['payment_simplifycommerce_button_color'] = $this->request->post['payment_simplifycommerce_button_color'] ?? $this->config->get('payment_simplifycommerce_button_color');

        $data['payment_simplifycommerce_button_color'] = !empty($data['payment_simplifycommerce_button_color']) ? $data['payment_simplifycommerce_button_color'] : "#1f90bb";

        $data['payment_simplifycommerce_livesecretkey'] = $this->request->post['payment_simplifycommerce_livesecretkey'] ?? $this->config->get('payment_simplifycommerce_livesecretkey');

        $data['payment_simplifycommerce_livepubkey'] = $this->request->post['payment_simplifycommerce_livepubkey'] ?? $this->config->get('payment_simplifycommerce_livepubkey');

        $data['payment_simplifycommerce_testsecretkey'] = $this->request->post['payment_simplifycommerce_testsecretkey'] ?? $this->config->get('payment_simplifycommerce_testsecretkey');

        $data['payment_simplifycommerce_testpubkey'] = $this->request->post['payment_simplifycommerce_testpubkey'] ?? $this->config->get('payment_simplifycommerce_testpubkey');

        $data['payment_simplifycommerce_allow_stored'] = $this->request->post['payment_simplifycommerce_allow_stored'] ?? $this->config->get('payment_simplifycommerce_allow_stored');

        $data['payment_simplifycommerce_check_cvc'] = $this->request->post['payment_simplifycommerce_check_cvc'] ?? $this->config->get('payment_simplifycommerce_check_cvc');

        $data['payment_simplifycommerce_title'] = $this->request->post['payment_simplifycommerce_title'] ?? $this->config->get('payment_simplifycommerce_title', 'Pay with Card');

        $data['payment_simplifycommerce_integration_model'] = $this->request->post['payment_simplifycommerce_integration_model'] ?? $this->config->get('payment_simplifycommerce_integration_model', 'modal');

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $data['payment_simplifycommerce_order_status_id'] = $this->request->post['payment_simplifycommerce_order_status_id']
            ?? $this->config->get('payment_simplifycommerce_order_status_id');

        $data['payment_simplifycommerce_declined_order_status_id'] = $this->request->post['payment_simplifycommerce_declined_order_status_id']
            ?? $this->config->get('payment_simplifycommerce_declined_order_status_id');

        $data['payment_simplifycommerce_txn_mode'] = $this->request->post['payment_simplifycommerce_txn_mode']
            ?? $this->config->get('payment_simplifycommerce_txn_mode', \Opencart\Admin\Model\Extension\SimplifyCommerce\Payment\SimplifyCommerce::PAYMENT);
        
        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        $data['payment_simplifycommerce_geo_zone_id'] = $this->request->post['payment_simplifycommerce_geo_zone_id']
            ?? $this->config->get('payment_simplifycommerce_geo_zone_id');

        $data['payment_simplifycommerce_status'] = $this->request->post['payment_simplifycommerce_status']
            ?? $this->config->get('payment_simplifycommerce_status');

        $data['payment_simplifycommerce_sort_order'] = $this->request->post['payment_simplifycommerce_sort_order']
            ?? $this->config->get('payment_simplifycommerce_sort_order');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/SimplifyCommerce/payment/simplifycommerce', $data));
    }

    private function testRegex($param, $regex)
    {
        $this->request->post['payment_simplifycommerce_' . $param] = $val = trim($this->request->post['payment_simplifycommerce_' . $param]);
        if (!$val || $val && !preg_match($regex, $val)) {
            $this->error[$param] = $this->language->get('error_' . $param);
        }
        return;
    }

    private function validate():  array|bool
    {
        if (!$this->user->hasPermission('modify', 'extension/SimplifyCommerce/payment/simplifycommerce')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        if (!$this->request->post['payment_simplifycommerce_title']) {
            $this->error['title'] = $this->language->get('error_title');
        }

        $this->testRegex('button_color', "/^#[0-9a-fA-F]{6}$/");
        $this->testRegex('testsecretkey', "/^[a-zA-Z0-9\/=+]{10,}$/");
        $this->testRegex('testpubkey', "/^sbpb_.*$/");

        if (!$this->request->post['payment_simplifycommerce_test']) {
            $this->testRegex('livesecretkey', "/^[a-zA-Z0-9\/=+]{10,}$/");
            $this->testRegex('livepubkey', "/^lvpb_.*$/");
        }

        return !$this->error;
    }


    public function install()
    {
        $this->load->model('extension/SimplifyCommerce/payment/simplifycommerce');
        $this->model_extension_SimplifyCommerce_payment_simplifycommerce->install();
        $this->model_extension_SimplifyCommerce_payment_simplifycommerce->deleteEvents();
        $this->model_extension_SimplifyCommerce_payment_simplifycommerce->addEvents();
    }

    public function uninstall()
    {
        $this->load->model('extension/SimplifyCommerce/payment/simplifycommerce');
        $this->load->model('setting/event');
        $this->model_extension_SimplifyCommerce_payment_simplifycommerce->uninstall();
        $this->model_extension_SimplifyCommerce_payment_simplifycommerce->deleteEvents();
    }

    public function order() {
        $this->load->model('extension/SimplifyCommerce/payment/simplifycommerce');
        $this->load->model('localisation/currency');
        $this->document->addScript('../extension/SimplifyCommerce/admin/view/javascript/simplify.js');
        $this->document->addStyle('../extension/SimplifyCommerce/admin/view/stylesheet/spectrum.css');
        $order = $this->model_extension_SimplifyCommerce_payment_simplifycommerce->getOrder(
            $this->request->get['order_id']
        );
        $currencies = $this->model_localisation_currency->getCurrencies();
        $defaultCurrencyCode = $this->config->get('config_currency');
        $currencyInfo = $this->model_localisation_currency->getCurrencyByCode($defaultCurrencyCode);
        if ($currencyInfo) {
            $currencySymbol = $currencyInfo['symbol_left'];
            $data['currency'] = $currencyInfo['symbol_left'];
            if (empty($currencySymbol)) {
                $data['currency'] = $currencyInfo['symbol_right'];
            }
        }

        if ($order) {
            $this->load->language('extension/SimplifyCommerce/payment/simplifycommerce');

            $data['simplifycommerce_order'] = array(
                'transactions' => $this->model_extension_SimplifyCommerce_payment_simplifycommerce->getTransactions(
                    $this->request->get['order_id']
                )
            );

            $data['order_id'] = $this->request->get['order_id'];
            $data['user_token'] = $this->request->get['user_token'];

            $data['transaction_id'] = array();
            $data['amount'] = array();

            $simplifycommerceOrder = $data['simplifycommerce_order']['transactions'];

            foreach ($simplifycommerceOrder as $transaction) {
                if ($transaction['type'] === 'capture' && $transaction['status'] === 'Closed') {
                    $data['capture_id'] = $transaction['transaction_id'];
                    $data['capture_amount'] = $transaction['amount'];
                }
            }

            foreach ($simplifycommerceOrder as $transaction) {
                if ($transaction['type'] === 'payment' && $transaction['status'] === 'Closed') {
                    $data['capture_id'] = $transaction['transaction_id'];
                    $data['capture_amount'] = $transaction['amount'];
                }
            }

            foreach ($simplifycommerceOrder as $transaction) {
                if ($transaction['type'] === 'refund' && $transaction['status'] === 'Partially Refunded') {
                    $data['last_refund_transaction_id'] = $transaction['transaction_id'];
                }
            }
            $data['total_refunded_amount'] = 0;
            foreach ($simplifycommerceOrder as $transaction) {
                if ($transaction['type'] === 'refund' && ($transaction['status'] === 'Closed' || $transaction['status'] === 'Partially Refunded')) {
                    $amount = floatval(str_replace(',', '', $transaction['amount']));
                    $data['total_refunded_amount'] += $amount;
                }
            }
            
            return $this->load->view('extension/SimplifyCommerce/payment/simplifycommerce_order', $data);
        }
    }

    /**
    * This Method allows to capture the authorized payment.
    *
    * @version 2.5.0
    */

    public function capture()
    {
        error_reporting(E_ALL);
        $this->load->language('extension/SimplifyCommerce/payment/simplifycommerce');
        $this->load->model('localisation/currency');
        $currencies = $this->model_localisation_currency->getCurrencies();
        $defaultCurrencyCode = $this->config->get('config_currency');
        $currencyInfo = $this->model_localisation_currency->getCurrencyByCode($defaultCurrencyCode);
        if ($currencyInfo) {
            $currencySymbol = $currencyInfo['symbol_left'];
            if (empty($currencySymbol)) {
                $currencySymbol = $currencyInfo['symbol_right'];
            }
        }
        $json = array();

        if (!isset($this->request->post['order_id']) || empty($this->request->post['order_id'])) {
            $json['error'] = true;
            $json['msg'] = 'Missing data';
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('extension/SimplifyCommerce/payment/simplifycommerce');
      
        require_once(DIR_EXTENSION . 'SimplifyCommerce/system/library/simplifycommerce/lib/Simplify.php');

        $authTxn = $this->model_extension_SimplifyCommerce_payment_simplifycommerce->getTransaction(
            $this->request->post['order_id'],
            $this->request->post['txn_id']
        );
        $amount_without_commas = str_replace(',', '', $authTxn['amount']);
        $capturing_amount = (int)($amount_without_commas * 100);
        $this->load->model('sale/order');
        $order_info = $this->model_sale_order->getOrder($this->request->post['order_id']);

        if ($this->config->get('payment_simplifycommerce_test') == 1) {
            $secret_key = trim($this->config->get('payment_simplifycommerce_testsecretkey'));
            $public_key = trim($this->config->get('payment_simplifycommerce_testpubkey'));
        } else {
            $secret_key = trim($this->config->get('payment_simplifycommerce_livesecretkey'));
            $public_key = trim($this->config->get('payment_simplifycommerce_livepubkey'));
        }

        try {
            $charge =  \Simplify_Payment::createPayment(array(
                'authorization' => $authTxn['transaction_id'],
                'reference' => $authTxn['order_id'],
                'currency' => strtoupper($order_info['currency_code']),
                'amount' =>$capturing_amount
            ), $public_key, $secret_key);
            if ($charge->paymentStatus === "APPROVED") {
                $this->process_capture_order_status( $charge );
            }
            elseif ($charge->declineReason === "AUTHORIZATION_EXPIRED" ){
                throw new \Simplify_ApiException('Payment was declined by your gateway - please try another card', $charge->id);
            }
            else{
                throw new \Exception('Payment was declined by your gateway - System Error Occured');
            }
        } 
        
        catch (\Simplify_ApiException $e) {
            $errorMessage = $e->getMessage();
            if ($errorMessage == "System error occurred processing a request") {
                $comment = "Error Occured in capturing transaction";
                $payment_succes_status = $this->config->get('payment_simplifycommerce_order_status_id');
                $notify = 1 ;
                $this->model_extension_SimplifyCommerce_payment_simplifycommerce->addOrderHistory($this->request->post['order_id'], $payment_succes_status, $comment, $notify);
                $json['error'] = true;
                $json['msg'] =$e->getMessage();
                http_response_code(401); 
                $this->response->setOutput(json_encode($json));
                return;
            }
            else{
                $comment = "System Error :- Authorization Expired for this order";
                $notify = 1;
                $capture_failedstatus_id = $this->model_extension_SimplifyCommerce_payment_simplifycommerce->getOrderStatusIdByName("Expired");
                $this->db->query("UPDATE " . DB_PREFIX . "order SET order_status_id = '" . (int)$capture_failedstatus_id . "' WHERE order_id = '" . (int)$this->request->post['order_id'] . "'");
                $this->db->query("UPDATE " . DB_PREFIX . "simplifycommerce_order_transaction SET status = 'Declined' WHERE  order_id = '".$this->request->post['order_id']."' LIMIT 1");
                $this->model_extension_SimplifyCommerce_payment_simplifycommerce->addOrderHistory($this->request->post['order_id'], $capture_failedstatus_id, $comment, $notify);
                $order_info = $this->model_sale_order->getOrder($this->request->post['order_id']);
                $customer_email = $order_info['email'];
                $customer_name = $order_info['firstname'] . ' ' . $order_info['lastname'];
                $mail_type = "Expired";
                $expired_order_id = 1 ;
                $order_id = $this->request->post['order_id'];
                if ($this->config->get('config_mail_engine')) {
                    $this->sendCustomEmail($customer_email,$customer_name ,  $order_id , $expired_order_id, $mail_type );
                }
                $json['error'] = true;
                $json['msg'] = $e->getMessage();
                http_response_code(401); 
                $this->response->setOutput(json_encode($json));
            }
        }
        catch (\Exception $e) {
            $comment = "Error occured in capturing transaction";
            $payment_succes_status = $this->config->get('payment_simplifycommerce_order_status_id');
            $notify = 1 ;
            $this->model_extension_SimplifyCommerce_payment_simplifycommerce->addOrderHistory($this->request->post['order_id'], $payment_succes_status, $comment, $notify);
            $json['error'] = true;
            $json['msg'] =$e->getMessage();
            http_response_code(401); 
            $this->response->setOutput(json_encode($json));
            return;
        }
    }
    public function process_capture_order_status ($charge){

        $this->load->language('extension/SimplifyCommerce/payment/simplifycommerce');
        $this->load->model('localisation/currency');
        $this->load->model('extension/SimplifyCommerce/payment/simplifycommerce');
        $currencies = $this->model_localisation_currency->getCurrencies();
        $defaultCurrencyCode = $this->config->get('config_currency');
        $currencyInfo = $this->model_localisation_currency->getCurrencyByCode($defaultCurrencyCode);
        if ($currencyInfo) {
            $currencySymbol = $currencyInfo['symbol_left'];
            if (empty($currencySymbol)) {
                $currencySymbol = $currencyInfo['symbol_right'];
            }
        }

        $authTxn = $this->model_extension_SimplifyCommerce_payment_simplifycommerce->getTransaction(
            $this->request->post['order_id'],
            $this->request->post['txn_id']
        );
        $amount_without_commas = str_replace(',', '', $authTxn['amount']);
        $capturing_amount = (int)($amount_without_commas * 100);
        $this->load->model('sale/order');
        $order_info = $this->model_sale_order->getOrder($this->request->post['order_id']);

        $customer_email = $order_info['email'];
        $customer_name = $order_info['firstname'] . ' ' . $order_info['lastname'];
        $mail_type = "Capture";
        $order_id = $this->request->post['order_id'];
        $capture_order_id = $charge;
        $txnMode = 'capture';
        $status = 'Completed';

        $historyamount = $charge->amount / 100 ; 
        $comment = $currencySymbol . number_format($historyamount, 2) . ' ' . $this->language->get('text_txn_capture_successful');
        $notify = "1";
        $capture_status_id = $this->model_extension_SimplifyCommerce_payment_simplifycommerce->getOrderStatusIdByName("Complete");

        $this->db->query("INSERT INTO " . DB_PREFIX . "simplifycommerce_order_transaction SET order_id ='" . $this->db->escape($authTxn['order_id']) . "', transaction_id = '" . $this->db->escape($charge->id) . "', type = '" . $txnMode . "', status = '" . $status . "', amount = '" . $this->db->escape($charge->amount). "', date_added = NOW()");

        $this->db->query("UPDATE " . DB_PREFIX . "simplifycommerce_order_transaction SET status = 'Closed' WHERE transaction_id = '".$authTxn['transaction_id']."' AND order_id = '".$authTxn['order_id']."' LIMIT 1");
        $this->db->query("UPDATE " . DB_PREFIX . "order SET order_status_id = '" . (int)$capture_status_id . "' WHERE order_id = '" . (int)$authTxn['order_id'] . "'");
        $this->model_extension_SimplifyCommerce_payment_simplifycommerce->addOrderHistory($authTxn['order_id'], $capture_status_id, $comment, $notify);
        if ($this->config->get('config_mail_engine')) {
            $this->sendCustomEmail($customer_email,$customer_name ,  $order_id , $capture_order_id, $mail_type );
        }

        $json = array(
            'error' => false,
            'msg' => 'Transaction created successfully'
        );
        http_response_code(201);

        $this->response->setOutput(json_encode($json));

    }

    /**
    * This Method allows to fully refund the captured payment.
    *
    * @version 2.5.0
    */

    public function process_refund()
    {
        $amount_without_commas = str_replace(',', '',  $this->request->post['amount']);
        $refund_amount = (int)($amount_without_commas * 100);
        $totalcapturedAmount = str_replace(',', '',  $this->request->post['total_amount']);
        $totalcapturedAmount = (int)($totalcapturedAmount * 100);
        $this->load->language('extension/SimplifyCommerce/payment/simplifycommerce');
        $json = array();

       

        if (!isset($this->request->post['order_id']) || empty($this->request->post['order_id'])) {
            $json['error'] = true;
            $json['msg'] = 'Missing data';
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('extension/SimplifyCommerce/payment/simplifycommerce');
        $this->load->model('localisation/currency');
        $currencies = $this->model_localisation_currency->getCurrencies();
        $defaultCurrencyCode = $this->config->get('config_currency');
        $currencyInfo = $this->model_localisation_currency->getCurrencyByCode($defaultCurrencyCode);
        if ($currencyInfo) {
            $currencySymbol = $currencyInfo['symbol_left'];
            if (empty($currencySymbol)) {
                $currencySymbol = $currencyInfo['symbol_right'];
            }
        }
      
        require_once(DIR_EXTENSION . 'SimplifyCommerce/system/library/simplifycommerce/lib/Simplify.php');

        $refundTxn = $this->model_extension_SimplifyCommerce_payment_simplifycommerce->getTransaction(
            $this->request->post['order_id'],
            $this->request->post['txn_id']
        );
        $this->load->model('sale/order');
        $order_info = $this->model_sale_order->getOrder($this->request->post['order_id']);

        

        if ($this->config->get('payment_simplifycommerce_test') == 1) {
            $secret_key = trim($this->config->get('payment_simplifycommerce_testsecretkey'));
            $public_key = trim($this->config->get('payment_simplifycommerce_testpubkey'));
        } else {
            $secret_key = trim($this->config->get('payment_simplifycommerce_livesecretkey'));
            $public_key = trim($this->config->get('payment_simplifycommerce_livepubkey'));
        }

        try {
            $charge =  \Simplify_Refund::createRefund(array(
                'amount' => $refund_amount,
                'payment' => $refundTxn['transaction_id'],
                'reason' => isset($this->request->post['message']) ? $this->request->post['message'] : $this->language->get('text_refund_default'),
                'reference' => $refundTxn['order_id']
            ), $public_key, $secret_key);

            $customer_email = $order_info['email'];
            $customer_name = $order_info['firstname'] . ' ' . $order_info['lastname'];
           
            $order_id = $this->request->post['order_id'];
            $capture_order_id = $charge;

            if ($charge->paymentStatus === "APPROVED") {
                if ($totalcapturedAmount == $charge->amount) {
                    $transactionStatus = "Refunded";
                    $mail_type = "Refunded";
                    $txnMode = 'Refund';
                    $status = 'Completed';

                    $historyamount = ($charge->amount / 100);
                    $comment = $currencySymbol . number_format($historyamount, 2) . ' ' . ' ' . $this->language->get('text_txn_refund_successful');
                    if (!empty($this->request->post['message'])) {
                        $comment .= "\nRefund reason: " . $this->request->post['message'];
                    }
                    $notify = "1";
                    $refund_status_id = $this->model_extension_SimplifyCommerce_payment_simplifycommerce->getOrderStatusIdByName("Refunded");
                    $this->db->query("UPDATE " . DB_PREFIX . "simplifycommerce_order_transaction SET status = 'Closed' WHERE transaction_id = '".$refundTxn['transaction_id']."' AND order_id = '".$refundTxn['order_id']."' LIMIT 1");
                    $this->db->query("INSERT INTO " . DB_PREFIX . "simplifycommerce_order_transaction SET order_id ='" . $this->db->escape($refundTxn['order_id']) . "', transaction_id = '" . $this->db->escape($charge->id) . "', type = 'Refund', status = '" . $transactionStatus . "', amount = '" . $this->db->escape($charge->amount). "', date_added = NOW()");
                    $this->db->query("UPDATE " . DB_PREFIX . "order SET order_status_id = '" . (int)$refund_status_id . "' WHERE order_id = '" . (int)$refundTxn['order_id'] . "'");
                    $this->model_extension_SimplifyCommerce_payment_simplifycommerce->addOrderHistory($refundTxn['order_id'], $refund_status_id, $comment, $notify);
                } elseif ($totalcapturedAmount > $charge->amount) {
                    $transactionStatus = "Partially Refunded";
        
                    $txnMode = 'Refund';
                    $mail_type = "Partially Refunded";
                    $status = 'Completed';
                    $refund_reason = $this->request->post['message'];

                    $historyamount = ($charge->amount / 100);
                    $comment = $currencySymbol . number_format($historyamount, 2) . ' ' . $this->language->get('text_txn_refund_successful');
                    if (!empty($this->request->post['message'])) {
                        $comment .= "\nRefund reason: " . $this->request->post['message'];
                    }
                    $notify = "1";
                    $refund_status_id = $this->model_extension_SimplifyCommerce_payment_simplifycommerce->getOrderStatusIdByName("Refunded");
                    $this->db->query("UPDATE " . DB_PREFIX . "simplifycommerce_order_transaction SET status = 'Closed' WHERE transaction_id = '".$refundTxn['transaction_id']."' AND order_id = '".$refundTxn['order_id']."' LIMIT 1");
                    $this->db->query("INSERT INTO " . DB_PREFIX . "simplifycommerce_order_transaction SET order_id ='" . $this->db->escape($refundTxn['order_id']) . "', transaction_id = '" . $this->db->escape($charge->id) . "', type = 'Refund', status = '" . $transactionStatus . "', amount = '" . $this->db->escape($charge->amount). "', date_added = NOW()");
                    $this->db->query("UPDATE " . DB_PREFIX . "order SET order_status_id = '" . (int)$refund_status_id . "' WHERE order_id = '" . (int)$refundTxn['order_id'] . "'");
                    $this->model_extension_SimplifyCommerce_payment_simplifycommerce->addOrderHistory($refundTxn['order_id'], $refund_status_id, $comment, $notify);
        
        
                } else {
                    $transactionStatus = "Unknown";
                }

                if ($this->config->get('config_mail_engine')) {
                    $this->sendCustomEmail($customer_email,$customer_name ,  $order_id , $capture_order_id, $mail_type );
                }
               
                $json = array(
                    'error' => false,
                    'msg' => 'Transaction refunded successfully'
                );
                http_response_code(201);
        
                $this->response->setOutput(json_encode($json));

            }

            elseif ($charge->paymentStatus === "DECLINED") {
                throw new \Simplify_ApiException('Refund was declined by your gateway - please try another card', $charge->id);
            }
        
        
        } catch (\Simplify_ApiException $e) {
            $comment = "System Error :- Refund was declined by your gateway";
            if (!empty($this->request->post['message'])) {
                        $comment .= "\nRefund reason: " . $this->request->post['message'];
            }
            $error_refund_status_id = $this->model_extension_SimplifyCommerce_payment_simplifycommerce->getOrderStatusIdByName("Complete");
            $notify = 1 ;
            $this->model_extension_SimplifyCommerce_payment_simplifycommerce->addOrderHistory($refundTxn['order_id'], $error_refund_status_id, $comment, $notify);
            $json['error'] = true;
            $json['msg'] = $e->getMessage();
            http_response_code(401); 
            $this->response->setOutput(json_encode($json));
        }

        catch (Exception $e) {
            $comment = "System Error :- Refund was declined by your gateway";
            if (!empty($this->request->post['message'])) {
                        $comment .= "\nRefund reason: " . $this->request->post['message'];
            }
            $error_refund_status_id = $this->model_extension_SimplifyCommerce_payment_simplifycommerce->getOrderStatusIdByName("Complete");
            $notify = 1 ;
            $this->model_extension_SimplifyCommerce_payment_simplifycommerce->addOrderHistory($refundTxn['order_id'], $error_refund_status_id, $comment, $notify);
            $json['error'] = true;
            $json['msg'] = 'Simplify Exception: ' . $e->getMessage();
            http_response_code(401); 
            $this->response->setOutput(json_encode($json));
            return;
        }
    }

    /**
    * This Method allows to partially refund the captured payment.
    *
    * @version 2.5.0
    */


    public function process_Partialrefund()
    {
        $this->load->model('extension/SimplifyCommerce/payment/simplifycommerce');
        $this->load->model('localisation/currency');
        $this->load->model('sale/order');
        $this->load->language('extension/SimplifyCommerce/payment/simplifycommerce');
        $json = array();
        $order_info = $this->model_sale_order->getOrder($this->request->post['order_id']);

        if (!isset($this->request->post['order_id']) || empty($this->request->post['order_id'])) {
            $json['error'] = true;
            $json['msg'] = 'Missing data';
            $this->response->setOutput(json_encode($json));
            return;
        }

        $requestedRefundAmount =  $this->request->post['amount'];
        $amount_without_commas = str_replace(',', '',  $this->request->post['amount']);
        $requestedRefundAmount = (int)($amount_without_commas * 100);

       
        $totalcapturedAmount =  str_replace(',', '',  $this->request->post['total_capture_amount']);
        $totalcapturedAmount =  (int)($totalcapturedAmount * 100);

        $partialRefundTransactionID = $this->request->post['txn_id'];

        $lastRefundedTransaction = $this->request->post['last_refund_txn'];

        $totalrefundedAmount = str_replace(',', '',  $this->request->post['total_refunded_amount']);
        $totalrefundedAmount = (int)($totalrefundedAmount * 100);

        $currencies = $this->model_localisation_currency->getCurrencies();
        $defaultCurrencyCode = $this->config->get('config_currency');
        $currencyInfo = $this->model_localisation_currency->getCurrencyByCode($defaultCurrencyCode);
        if ($currencyInfo) {
            $currencySymbol = $currencyInfo['symbol_left'];
            if (empty($currencySymbol)) {
                $currencySymbol = $currencyInfo['symbol_right'];
            }
        }

        require_once(DIR_EXTENSION . 'SimplifyCommerce/system/library/simplifycommerce/lib/Simplify.php');

        $refundTxn = $this->model_extension_SimplifyCommerce_payment_simplifycommerce->getTransaction(
            $this->request->post['order_id'],
            $this->request->post['txn_id']
        );


        $order_info = $this->model_sale_order->getOrder($this->request->post['order_id']);

        if ($this->config->get('payment_simplifycommerce_test') == 1) {
            $secret_key = trim($this->config->get('payment_simplifycommerce_testsecretkey'));
            $public_key = trim($this->config->get('payment_simplifycommerce_testpubkey'));
        } else {
            $secret_key = trim($this->config->get('payment_simplifycommerce_livesecretkey'));
            $public_key = trim($this->config->get('payment_simplifycommerce_livepubkey'));
        }

        try {
            $charge =  \Simplify_Refund::createRefund(array(
                'amount' => $requestedRefundAmount ,
                'payment' =>$partialRefundTransactionID,
                'reason' => isset($this->request->post['message']) ? $this->request->post['message'] : $this->language->get('text_refund_default'),
                'reference' => $refundTxn['order_id']
            ), $public_key, $secret_key);

            $customer_email = $order_info['email'];
            $customer_name = $order_info['firstname'] . ' ' . $order_info['lastname'];
           
            $order_id = $this->request->post['order_id'];
            $capture_order_id = $charge;

            if ($charge->paymentStatus == "APPROVED") {
                $totalrefundedAmount = $totalrefundedAmount + $charge->amount ;

                if ($totalcapturedAmount > $totalrefundedAmount) {
                    $transactionStatus = "Partially Refunded";

                    $txnMode = 'Refund';
                    $mail_type = "Partially Refunded";
                    $status = 'Completed';
                    $amount = $charge->amount / 100;
                    $comment = $currencySymbol . number_format($amount, 2) . ' ' . $this->language->get('text_txn_refund_successful');
                    if (!empty($this->request->post['message'])) {
                        $comment .= "\nRefund reason: " . $this->request->post['message'];
                    }
                    $notify = "1";
                    $refund_status_id = $this->model_extension_SimplifyCommerce_payment_simplifycommerce->getOrderStatusIdByName("Refunded");
                    $this->db->query("UPDATE " . DB_PREFIX . "simplifycommerce_order_transaction SET status = 'Closed' WHERE transaction_id = '".$lastRefundedTransaction."' AND order_id = '".$refundTxn['order_id']."' LIMIT 1");
                    $this->db->query("INSERT INTO " . DB_PREFIX . "simplifycommerce_order_transaction SET order_id ='" . $this->db->escape($refundTxn['order_id']) . "', transaction_id = '" . $this->db->escape($charge->id) . "', type = 'Refund', status = '" . $transactionStatus . "', amount = '" . $this->db->escape($charge->amount). "', date_added = NOW()");
                    $this->db->query("UPDATE " . DB_PREFIX . "order SET order_status_id = '" . (int)$refund_status_id . "' WHERE order_id = '" . (int)$refundTxn['order_id'] . "'");
                    $this->model_extension_SimplifyCommerce_payment_simplifycommerce->addOrderHistory($refundTxn['order_id'], $refund_status_id, $comment, $notify);
            
                } elseif ($totalcapturedAmount ==  $totalrefundedAmount)  {
                    $transactionStatus = "Refunded";
                    $mail_type = "Refunded";

                    $txnMode = 'Refund';
                    $status = 'Completed';
                    $amount = $charge->amount / 100;
                    $comment = $currencySymbol . number_format($amount, 2) . ' ' . $this->language->get('text_txn_refund_successful');
                    if (!empty($this->request->post['message'])) {
                        $comment .= "\nRefund reason: " . $this->request->post['message'];
                    }
                    $notify = "1";
                    $refund_status_id = $this->model_extension_SimplifyCommerce_payment_simplifycommerce->getOrderStatusIdByName("Refunded");
                    $this->db->query("UPDATE " . DB_PREFIX . "simplifycommerce_order_transaction SET status = 'Closed' WHERE transaction_id = '".$lastRefundedTransaction."' AND order_id = '".$refundTxn['order_id']."' LIMIT 1");
                    $this->db->query("INSERT INTO " . DB_PREFIX . "simplifycommerce_order_transaction SET order_id ='" . $this->db->escape($refundTxn['order_id']) . "', transaction_id = '" . $this->db->escape($charge->id) . "', type = 'Refund', status = '" . $transactionStatus . "', amount = '" . $this->db->escape($charge->amount). "', date_added = NOW()");
                    $this->db->query("UPDATE " . DB_PREFIX . "order SET order_status_id = '" . (int)$refund_status_id . "' WHERE order_id = '" . (int)$refundTxn['order_id'] . "'");
                    $this->model_extension_SimplifyCommerce_payment_simplifycommerce->addOrderHistory($refundTxn['order_id'], $refund_status_id, $comment, $notify);
                }
            
                $json = array(
                    'error' => false,
                    'msg' => 'Transaction refunded successfully'
                );
                http_response_code(201);

                if ($this->config->get('config_mail_engine')) {
                    $this->sendCustomEmail($customer_email,$customer_name ,  $order_id , $capture_order_id, $mail_type );
                }

                $this->response->setOutput(json_encode($json));
            }

            elseif ($charge->paymentStatus === "DECLINED") {
                throw new \Simplify_ApiException('Refund was declined by your gateway - please try another card', $charge->id);
            }

        } catch (\Simplify_ApiException $e) {
            $comment = "System Error :- Refund was declined by your gateway";
            if (!empty($this->request->post['message'])) {
                        $comment .= "\nRefund reason: " . $this->request->post['message'];
            }
            $error_refund_status_id = $this->model_extension_SimplifyCommerce_payment_simplifycommerce->getOrderStatusIdByName("Complete");
            $notify = 1 ;
            $this->model_extension_SimplifyCommerce_payment_simplifycommerce->addOrderHistory($refundTxn['order_id'], $error_refund_status_id, $comment, $notify);
            $json['error'] = true;
            $json['msg'] = $e->getMessage();
            http_response_code(401);
            $this->response->setOutput(json_encode($json));
        }
        
        catch (Exception $e) {
            $comment = "System Error :- Refund was declined by your gateway";
            if (!empty($this->request->post['message'])) {
                        $comment .= "\nRefund reason: " . $this->request->post['message'];
            }
            $error_refund_status_id = $this->model_extension_SimplifyCommerce_payment_simplifycommerce->getOrderStatusIdByName("Complete");
            $notify = 1 ;
            $this->model_extension_SimplifyCommerce_payment_simplifycommerce->addOrderHistory($refundTxn['order_id'], $error_refund_status_id, $comment, $notify);
            $json['error'] = true;
            $json['msg'] = 'Simplify Exception: ' . $e->getMessage();
            http_response_code(401);
            $this->response->setOutput(json_encode($json));
            return;
        }

       
    }

    /**
    * This Method handles the sending custom email alerts.
    *
    * @version 2.5.0
    */


    private function sendCustomEmail($reciever_address, $customer_name ,  $order_id  , $capture_order_id , $mail_type) {
        $this->load->model('extension/SimplifyCommerce/payment/simplifycommerce');
        $this->load->model('localisation/currency');
    
        $order = $this->model_extension_SimplifyCommerce_payment_simplifycommerce->getOrder( $order_id );
        $store_name = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
    
       
        $currencies = $this->model_localisation_currency->getCurrencies();
        $defaultCurrencyCode = $this->config->get('config_currency');
        $currencyInfo = $this->model_localisation_currency->getCurrencyByCode($defaultCurrencyCode);
        if ($currencyInfo) {
            $currencySymbol = $currencyInfo['symbol_left'];
            $data['currency'] = $currencyInfo['symbol_left'];
            if (empty($currencySymbol)) {
                $data['currency'] = $currencyInfo['symbol_right'];
            }
        }

        if ($order) {
            $this->load->language('extension/payment/mpgs_hosted_checkout');
            $data['simplifycommerce_order'] = array(
                'transactions' => $this->model_extension_SimplifyCommerce_payment_simplifycommerce->getTransactions(
                    $order_id
                )
            );
            $data['order_id'] = $order_id ;
            $data['user_token'] = $this->request->get['user_token'];
            $data['customer_name']  =$customer_name;
            $data['receiver_address']  = $reciever_address;
            $data['mail_type'] = $mail_type;
            if ($this->config->get('config_mail_engine')) {
                $mail_option = [
                    'parameter'     => $this->config->get('config_mail_parameter'),
                    'smtp_hostname' => $this->config->get('config_mail_smtp_hostname'),
                    'smtp_username' => $this->config->get('config_mail_smtp_username'),
                    'smtp_password' => html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8'),
                    'smtp_port'     => $this->config->get('config_mail_smtp_port'),
                    'smtp_timeout'  => $this->config->get('config_mail_smtp_timeout')
                ];
            
                $mail = new \Opencart\System\Library\Mail($this->config->get('config_mail_engine'), $mail_option);
                $mail->setTo($reciever_address);
                $mail->setFrom($this->config->get('config_email'));
                $mail->setSender(html_entity_decode( $store_name , ENT_QUOTES, 'UTF-8'));
                $mail->setSubject("Simplify Order" . " " .$mail_type);
                $mail->setHtml($this->load->view('extension/SimplifyCommerce/payment/simplifycommerce_order_mail', $data));
                $mail->send();
            }
        }

    }
    
}
