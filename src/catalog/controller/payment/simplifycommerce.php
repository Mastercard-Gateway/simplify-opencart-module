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

namespace Opencart\Catalog\Controller\Extension\SimplifyCommerce\Payment;
use Exception;  
class SimplifyCommerce extends \Opencart\System\Engine\Controller 
{
 
    private $separator = '';
		
	public function __construct($registry) {
		parent::__construct($registry);
		
		if (VERSION >= '4.0.2.0') {
			$this->separator = '.';
		} else {
			$this->separator = '|';
		}

		if (version_compare(phpversion(), '7.1', '>=')) {
			ini_set('precision', 14);
			ini_set('serialize_precision', 14);
		}
	}

    private function calcAmount($order_info)
    {
        return 100 * $this->currency->format($order_info['total'], $order_info['currency_code'],
                $order_info['currency_value'], false);
    }

    protected function attempt_transliteration($field)
    {
        $encoding = mb_detect_encoding($field);
        if ($encoding !== 'ASCII') {
            if (function_exists('transliterator_transliterate')) {
                $field = transliterator_transliterate('Any-Latin; Latin-ASCII; [\u0080-\u7fff] remove', $field);
            } else {
                // fall back to iconv if intl module not available
                $field = iconv($encoding, 'ASCII//TRANSLIT//IGNORE', $field);
                $field = str_ireplace('?', '', $field);
                $field = trim($field);
            }
        }

        return $field;
    }
    public function update_page_header($route, &$data): void {
        if (!$this->config->get('payment_simplifycommerce_status')) {
            return;
        }
    
        $requestRoute = isset($this->request->get['route']) ? $this->request->get['route'] : null;
        if ($requestRoute !== 'checkout/checkout') {
            return;
        }
    
        $this->response->addHeader('Access-Control-Allow-Origin: *');
        $this->document->addStyle('extension/SimplifyCommerce/catalog/view/stylesheet/embedded-payment-form.css');
        $this->document->addScript('https://www.simplify.com/commerce/simplify.pay.js');
        
    }
    public function index()
    {


        $this->log->write("Index called. Session data: ");
 
        $data['template'] = $this->config->get('config_theme');

        $this->load->language('extension/SimplifyCommerce/payment/simplifycommerce');
        $this->load->model('checkout/order');
 
        $data['text_card_details'] = $this->language->get('text_card_details');
        $data['text_pay'] = $this->language->get('text_pay');

        $data['entry_name_on_card'] = $this->language->get('entry_name_on_card');
        $data['entry_card_number'] = $this->language->get('entry_card_number');
        $data['entry_card_expiration'] = $this->language->get('entry_card_expiration');
        $data['entry_cvc'] = $this->language->get('entry_cvc');
        $data['button_pay'] = $this->language->get('button_pay');

        if ($this->config->get('payment_simplifycommerce_test') == 1) {
            $data['pub_key'] = trim($this->config->get('payment_simplifycommerce_testpubkey'));
        } else {
            $data['pub_key'] = trim($this->config->get('payment_simplifycommerce_livepubkey'));
        }

        $data['button_color'] = $this->config->get('payment_simplifycommerce_button_color');
        $data['integration_model'] = $this->config->get('payment_simplifycommerce_integration_model');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $data['store_name'] = $this->attempt_transliteration($order_info["store_name"]);

        $data['amount'] = $this->calcAmount($order_info);
    
        $data['currency'] = strtolower($order_info['currency_code']);

        $data['description'] = $this->session->data['order_id'];

    
     	$data['redirect_url'] = $this->url->link('extension/SimplifyCommerce/payment/simplifycommerce' . $this->separator . 'charge');

        $data['months'] = array();

        for ($i = 1; $i <= 12; $i++) {
            $timestamp = mktime(0, 0, 0, $i, 1, 2000);
            $data['months'][] = array(
                'text'  => date('F', $timestamp), // Use 'F' for full month name
                'value' => sprintf('%02d', $i),
            );
        }
        
        

        $today = getdate();

        $data['year_expire'] = array();

        for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
            $data['year_expire'][] = array(
                'text'  => date('Y', mktime(0, 0, 0, 1, 1, $i)),
                'value' => date('y', mktime(0, 0, 0, 1, 1, $i)),
            );
        }
        

        $data['payment_title'] = $this->config->get('payment_simplifycommerce_title');
        setcookie("simplifycookie", $this->session->getId(), $this->config->get('config_session_expire') ? time() + (int)$this->config->get('config_session_expire') : 0, "/");
    if ($this->session->data['payment_method']['code'] == 'simplifycommerce.simplifycommerce') {
        return $this->load->view('extension/SimplifyCommerce/payment/simplifycommerce', $data);
    }  
        
}
   
 

    public function charge()
    {
        require_once(DIR_EXTENSION . 'SimplifyCommerce/system/library/simplifycommerce/lib/Simplify.php');
       
        $this->language->load('extension/payment/simplifycommerce');
        $this->load->model('checkout/order');

        if ($this->config->get('payment_simplifycommerce_test') == 1) {
            $secret_key = trim($this->config->get('payment_simplifycommerce_testsecretkey'));
            $public_key = trim($this->config->get('payment_simplifycommerce_testpubkey'));
        } else {
            $secret_key = trim($this->config->get('payment_simplifycommerce_livesecretkey'));
            $public_key = trim($this->config->get('payment_simplifycommerce_livepubkey'));
        }

     
        try {
            if (isset($this->session->data['order_id'])) {
                $order_id = $this->session->data['order_id'];
            } 
            if (!isset($order_id) && isset($this->request->get['reference'])) {
                $order_id =$this->request->get['reference'];
            }
            

            if (!isset($order_id)) {
                throw new Exception($this->language->get('message_no_order'));
            }
       

            $order_info = $this->model_checkout_order->getOrder($order_id);
            $this->setSimplifyCookie();
            $order_status = $order_info['order_status_id'];

            $c = array(
                'token'       => $_REQUEST['cardToken'],
                'amount'      => $this->calcAmount($order_info),
                'description' => 'OpenCart - order id: '.$order_id,
                'reference'   => $order_id,
                'currency'    => strtoupper($order_info['currency_code']),
            );
            if ($order_info) {
                    $txnMode = $this->config->get('payment_simplifycommerce_txn_mode') ?: 'payment';
                    if ($txnMode == 'authorization') {
                        $charge = \Simplify_Authorization::createAuthorization($c, $public_key, $secret_key);
               
                        $status = 'Open';
                    } else {
                        $charge =  \Simplify_Payment::createPayment($c, $public_key, $secret_key);
                        $status = 'Completed';
                    }
                    if ($charge->paymentStatus != "APPROVED") {
                        $this->log->write("payment not approved; status: ".$charge->paymentStatus);
                        throw new \Simplify_ApiException($this->language->get('payment_declined'));
                    }
                    $this->db->query("INSERT INTO ".DB_PREFIX."simplifycommerce_order_transaction SET order_id ='".$this->db->escape($order_id)."', transaction_id = '".$this->db->escape($charge->id)."', type = '".$txnMode."', status = '".$status."', amount = '".$this->db->escape($charge->amount)."', date_added = NOW()");
                    $this->model_checkout_order->addHistory($order_id, $this->config->get('payment_simplifycommerce_order_status_id'), '', true);

                } else {
                    throw new Exception($this->language->get('message_no_order'));
                }
        }
         catch (\Simplify_ApiException $e) {
            $this->log->write($e->describe());
            $mess = "";
            $t = $e->getErrorData();
            if (isset($t["error"]["fieldErrors"][0]["message"])) {
                $mess = $t["error"]["fieldErrors"][0]["message"];
            } else {
                $mess = $e->getMessage();
            }

            $this->session->data['error'] = $mess;


           return $this->response->redirect($this->url->link('checkout/failure', '', 'SSL')); 
        }
        
        
        catch (Exception  $e) {
            $this->log->write($e->getMessage());
            $this->session->data['error'] = $e->getMessage();
            

            return $this->response->redirect($this->url->link('checkout/failure', '', 'SSL')); 
        }

        return $this->response->redirect($this->url->link('checkout/success'));
    }

    private function setSimplifyCookie(){
        $simplifyCookie = $_COOKIE['simplifycookie'];
        setcookie('OCSESSID', $simplifyCookie, $this->config->get('config_session_expire') ? time() + (int)$this->config->get('config_session_expire') : 0, '/');
    }
        
}
