<?php
/**
 * Copyright (c) 2013-2021 Mastercard
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

class ControllerExtensionPaymentSimplifyCommerce extends Controller
{
    private $error = array();

    public function index()
    {
        // Install must not fail even if executed multiple times
        $this->install();

        $this->load->language('extension/payment/simplifycommerce');

        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->addScript('view/javascript/simplifycommerce/spectrum.js');
        $this->document->addStyle('view/javascript/simplifycommerce/spectrum.css');

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('payment_simplifycommerce', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension',
                'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
        }

        $data = array();
        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_successful'] = $this->language->get('text_successful');
        $data['text_declined'] = $this->language->get('text_declined');
        $data['text_off'] = $this->language->get('text_off');
        $data['text_test'] = $this->language->get('text_test');
        $data['text_prod'] = $this->language->get('text_prod');
        $data['text_payment_hosted'] = $this->language->get('text_payment_hosted');
        $data['text_payment_standard'] = $this->language->get('text_payment_standard');
        $data['text_txn_mode_authorize'] = $this->language->get('text_txn_mode_authorize');
        $data['text_txn_mode_pay'] = $this->language->get('text_txn_mode_pay');

        $data['entry_livesecretkey'] = $this->language->get('entry_livesecretkey');
        $data['entry_livepubkey'] = $this->language->get('entry_livepubkey');
        $data['entry_testsecretkey'] = $this->language->get('entry_testsecretkey');
        $data['entry_testpubkey'] = $this->language->get('entry_testpubkey');
        $data['entry_title'] = $this->language->get('entry_title');
        $data['entry_title_help'] = $this->language->get('entry_title_help');
        $data['entry_check_address_line_1'] = $this->language->get('entry_check_address_line_1');
        $data['entry_check_zip'] = $this->language->get('entry_check_zip');
        $data['entry_webhook_url'] = $this->language->get('entry_webhook_url');
        $data['entry_webhook_url_help'] = $this->language->get('entry_webhook_url_help');
        $data['entry_test'] = $this->language->get('entry_test');
        $data['entry_payment_mode'] = $this->language->get('entry_payment_mode');
        $data['entry_button_color'] = $this->language->get('entry_button_color');
        $data['entry_order_status'] = $this->language->get('entry_order_status');
        $data['entry_declined_order_status'] = $this->language->get('entry_declined_order_status');
        $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['entry_txn_mode'] = $this->language->get('entry_txn_mode');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $err_arr = array(
            'warning',
            'permission',
            'livesecretkey',
            'livepubkey',
            'testsecretkey',
            'testpubkey',
            'title',
            'button_color'
        );

        foreach ($err_arr as $val) {
            $data['error_' . $val] = isset($this->error[$val]) ? $this->error[$val] : '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension',
                'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/simplifycommerce',
                'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/payment/simplifycommerce',
            'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension',
            'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

        if (isset($this->request->post['payment_simplifycommerce_test'])) {
            $data['payment_simplifycommerce_test'] = trim($this->request->post['payment_simplifycommerce_test']);
        } else {
            $data['payment_simplifycommerce_test'] = $this->config->get('payment_simplifycommerce_test');
        }

        if (isset($this->request->post['payment_simplifycommerce_button_color'])) {
            $data['payment_simplifycommerce_button_color'] = trim($this->request->post['payment_simplifycommerce_button_color']);
        } else {
            $data['payment_simplifycommerce_button_color'] = $this->config->get('payment_simplifycommerce_button_color');
        }

        // set to light blue if not set
        if (!$data['payment_simplifycommerce_button_color']) {
            $data['payment_simplifycommerce_button_color'] = "#1f90bb";
        }

        if (isset($this->request->post['payment_simplifycommerce_livesecretkey'])) {
            $data['payment_simplifycommerce_livesecretkey'] = trim($this->request->post['payment_simplifycommerce_livesecretkey']);
        } else {
            $data['payment_simplifycommerce_livesecretkey'] = $this->config->get('payment_simplifycommerce_livesecretkey');
        }

        if (isset($this->request->post['payment_simplifycommerce_livepubkey'])) {
            $data['payment_simplifycommerce_livepubkey'] = trim($this->request->post['payment_simplifycommerce_livepubkey']);
        } else {
            $data['payment_simplifycommerce_livepubkey'] = $this->config->get('payment_simplifycommerce_livepubkey');
        }

        if (isset($this->request->post['payment_simplifycommerce_testsecretkey'])) {
            $data['payment_simplifycommerce_testsecretkey'] = trim($this->request->post['payment_simplifycommerce_testsecretkey']);
        } else {
            $data['payment_simplifycommerce_testsecretkey'] = $this->config->get('payment_simplifycommerce_testsecretkey');
        }

        if (isset($this->request->post['payment_simplifycommerce_testpubkey'])) {
            $data['payment_simplifycommerce_testpubkey'] = trim($this->request->post['payment_simplifycommerce_testpubkey']);
        } else {
            $data['payment_simplifycommerce_testpubkey'] = $this->config->get('payment_simplifycommerce_testpubkey');
        }

        if (isset($this->request->post['payment_simplifycommerce_allow_stored'])) {
            $data['payment_simplifycommerce_allow_stored'] = $this->request->post['payment_simplifycommerce_allow_stored'];
        } else {
            $data['payment_simplifycommerce_allow_stored'] = $this->config->get('payment_simplifycommerce_allow_stored');
        }

        if (isset($this->request->post['payment_simplifycommerce_check_cvc'])) {
            $data['payment_simplifycommerce_check_cvc'] = $this->request->post['payment_simplifycommerce_check_cvc'];
        } else {
            $data['payment_simplifycommerce_check_cvc'] = $this->config->get('payment_simplifycommerce_check_cvc');
        }

        if (isset($this->request->post['payment_simplifycommerce_title'])) {
            $data['payment_simplifycommerce_title'] = $this->request->post['payment_simplifycommerce_title'];
        } else {
            $data['payment_simplifycommerce_title'] = $this->config->get('payment_simplifycommerce_title') ?: 'Pay with Card';
        }

        if (isset($this->request->post['payment_simplifycommerce_integration_model'])) {
            $data['payment_simplifycommerce_integration_model'] = $this->request->post['payment_simplifycommerce_integration_model'];
        } else {
            $data['payment_simplifycommerce_integration_model'] = $this->config->get('payment_simplifycommerce_integration_model') ?: 'modal';
        }

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['payment_simplifycommerce_order_status_id'])) {
            $data['payment_simplifycommerce_order_status_id'] = $this->request->post['payment_simplifycommerce_order_status_id'];
        } else {
            $data['payment_simplifycommerce_order_status_id'] = $this->config->get('payment_simplifycommerce_order_status_id');
        }

        if (isset($this->request->post['payment_simplifycommerce_declined_order_status_id'])) {
            $data['payment_simplifycommerce_declined_order_status_id'] = $this->request->post['payment_simplifycommerce_declined_order_status_id'];
        } else {
            $data['payment_simplifycommerce_declined_order_status_id'] = $this->config->get('payment_simplifycommerce_declined_order_status_id');
        }

        if (isset($this->request->post['payment_simplifycommerce_txn_mode'])) {
            $data['payment_simplifycommerce_txn_mode'] = $this->request->post['payment_simplifycommerce_txn_mode'];
        } else {
            $data['payment_simplifycommerce_txn_mode'] = $this->config->get('payment_simplifycommerce_txn_mode') ?: ModelExtensionPaymentSimplifyCommerce::PAYMENT;
        }

        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->request->post['payment_simplifycommerce_geo_zone_id'])) {
            $data['payment_simplifycommerce_geo_zone_id'] = $this->request->post['payment_simplifycommerce_geo_zone_id'];
        } else {
            $data['payment_simplifycommerce_geo_zone_id'] = $this->config->get('payment_simplifycommerce_geo_zone_id');
        }

        if (isset($this->request->post['payment_simplifycommerce_status'])) {
            $data['payment_simplifycommerce_status'] = $this->request->post['payment_simplifycommerce_status'];
        } else {
            $data['payment_simplifycommerce_status'] = $this->config->get('payment_simplifycommerce_status');
        }

        if (isset($this->request->post['payment_simplifycommerce_sort_order'])) {
            $data['payment_simplifycommerce_sort_order'] = $this->request->post['payment_simplifycommerce_sort_order'];
        } else {
            $data['payment_simplifycommerce_sort_order'] = $this->config->get('payment_simplifycommerce_sort_order');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/simplifycommerce', $data));
    }

    private function testRegex($param, $regex)
    {
        $this->request->post['payment_simplifycommerce_' . $param] = $val = trim($this->request->post['payment_simplifycommerce_' . $param]);
        if (!$val || $val && !preg_match($regex, $val)) {
            $this->error[$param] = $this->language->get('error_' . $param);
        }
        return;
    }

    private function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/payment/simplifycommerce')) {
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

        // If in live mode, check that we have private and pub live keys
        if (!$this->request->post['payment_simplifycommerce_test']) {
            $this->testRegex('livesecretkey', "/^[a-zA-Z0-9\/=+]{10,}$/");
            $this->testRegex('livepubkey', "/^lvpb_.*$/");
        }
        return !$this->error;
    }

    public function install()
    {
        $this->load->model('extension/payment/simplifycommerce');
        $this->model_extension_payment_simplifycommerce->install();
        $this->model_extension_payment_simplifycommerce->deleteEvents();
        $this->model_extension_payment_simplifycommerce->addEvents();
    }

    public function uninstall()
    {
        $this->load->model('extension/payment/simplifycommerce');
        $this->load->model('setting/event');
        $this->model_extension_payment_simplifycommerce->uninstall();
        $this->model_extension_payment_simplifycommerce->deleteEvents();
    }

    public function order() {
        $this->load->model('extension/payment/simplifycommerce');
        $order = $this->model_extension_payment_simplifycommerce->getOrder(
            $this->request->get['order_id']
        );

        if ($order) {
            $this->load->language('extension/payment/simplifycommerce');

            $data['simplifycommerce_order'] = array(
                'transactions' => $this->model_extension_payment_simplifycommerce->getTransactions(
                    $this->request->get['order_id']
                )
            );

            $data['order_id'] = $this->request->get['order_id'];
            $data['user_token'] = $this->request->get['user_token'];

            return $this->load->view('extension/payment/simplifycommerce_order', $data);
        }
    }

    public function capture()
    {
        $this->load->language('extension/payment/simplifycommerce');
        $json = array();

        if (!isset($this->request->post['order_id']) || empty($this->request->post['order_id'])) {
            $json['error'] = true;
            $json['msg'] = 'Missing data';
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('extension/payment/simplifycommerce');
        require_once(DIR_SYSTEM . 'library/simplifycommerce/lib/Simplify.php');

        $authTxn = $this->model_extension_payment_simplifycommerce->getTransaction(
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
            $charge = Simplify_Payment::createPayment(array(
                'authorization' => $authTxn['transaction_id'],
                'reference' => $authTxn['order_id'],
                'currency' => strtoupper($order_info['currency_code']),
                'amount' => ((float) $authTxn['amount']) * 100
            ), $public_key, $secret_key);

            if ($charge->paymentStatus != "APPROVED") {
                throw new Exception('Not approved');
            }
        } catch (Exception $e) {
            $json['error'] = true;
            $json['msg'] = 'Simplify Exception: ' . $e->getMessage();
            $this->response->setOutput(json_encode($json));
            return;
        }

        $txnMode = 'capture';
        $status = 'Completed';

        // Create capture txn
        $this->db->query("INSERT INTO " . DB_PREFIX . "simplifycommerce_order_transaction SET order_id ='" . $this->db->escape($authTxn['order_id']) . "', transaction_id = '" . $this->db->escape($charge->id) . "', type = '" . $txnMode . "', status = '" . $status . "', amount = '" . $this->db->escape($charge->amount). "', date_added = NOW()");

        // Close auth txn
        $this->db->query("UPDATE " . DB_PREFIX . "simplifycommerce_order_transaction SET status = 'Closed' WHERE transaction_id = '".$authTxn['transaction_id']."' AND order_id = '".$authTxn['order_id']."' LIMIT 1");

        $json = array(
            'error' => false,
            'msg' => 'Transaction created successfully'
        );

        $this->response->setOutput(json_encode($json));
    }
}
