<?php 

/*
@LICENSE@
 */

class ControllerPaymentSimplifyCommerce extends Controller {
	private $error = array(); 

	public function index() {
		$this->load->language('payment/simplifycommerce');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->load->model('setting/setting');
			
			$this->model_setting_setting->editSetting('simplifycommerce', $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
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
		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_declined_order_status'] = $this->language->get('entry_declined_order_status');	
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$err_arr = array(
			'warning',
			'permission',
			'livesecretkey',
			'livepubkey',
			'testsecretkey',
			'testpubkey',
			'title'
		);

		foreach($err_arr as $val){
			$data['error_' .$val] = isset($this->error[$val]) ? $this->error[$val] : '';
		}

  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][]  = array(
       		'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
       		'text'      => $this->language->get('text_home')
   		);

   		$data['breadcrumbs'][]  = array(
       		'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
       		'text'      => $this->language->get('text_payment')
   		);

   		$data['breadcrumbs'][]  = array(
       		'href'      => $this->url->link('payment/simplifycommerce', 'token=' . $this->session->data['token'], 'SSL'),
       		'text'      => $this->language->get('heading_title')
   		);
				
		$data['action'] = $this->url->link('payment/simplifycommerce', 'token=' . $this->session->data['token'], 'SSL');
		
		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
		
		if (isset($this->request->post['simplifycommerce_test'])) {
			$data['simplifycommerce_test'] = trim($this->request->post['simplifycommerce_test']);
		} else {
			$data['simplifycommerce_test'] = $this->config->get('simplifycommerce_test'); 
		}

		if (isset($this->request->post['simplifycommerce_livesecretkey'])) {
			$data['simplifycommerce_livesecretkey'] = trim($this->request->post['simplifycommerce_livesecretkey']);
		} else {
			$data['simplifycommerce_livesecretkey'] = $this->config->get('simplifycommerce_livesecretkey');
		}
		
		if (isset($this->request->post['simplifycommerce_livepubkey'])) {
			$data['simplifycommerce_livepubkey'] = trim($this->request->post['simplifycommerce_livepubkey']);
		} else {
			$data['simplifycommerce_livepubkey'] = $this->config->get('simplifycommerce_livepubkey');
		}
		
		if (isset($this->request->post['simplifycommerce_testsecretkey'])) {
			$data['simplifycommerce_testsecretkey'] = trim($this->request->post['simplifycommerce_testsecretkey']);
		} else {
			$data['simplifycommerce_testsecretkey'] = $this->config->get('simplifycommerce_testsecretkey');
		}
		
		if (isset($this->request->post['simplifycommerce_testpubkey'])) {
			$data['simplifycommerce_testpubkey'] = trim($this->request->post['simplifycommerce_testpubkey']);
		} else {
			$data['simplifycommerce_testpubkey'] = $this->config->get('simplifycommerce_testpubkey');
		}
		
		if (isset($this->request->post['simplifycommerce_allow_stored'])) {
			$data['simplifycommerce_allow_stored'] = $this->request->post['simplifycommerce_allow_stored'];
		} else {
			$data['simplifycommerce_allow_stored'] = $this->config->get('simplifycommerce_allow_stored');
		}
		
		if (isset($this->request->post['simplifycommerce_check_cvc'])) {
			$data['simplifycommerce_check_cvc'] = $this->request->post['simplifycommerce_check_cvc'];
		} else {
			$data['simplifycommerce_check_cvc'] = $this->config->get('simplifycommerce_check_cvc');
		}

		$data['webhook_url'] = HTTPS_CATALOG . 'index.php?route=payment/simplifycommerce/callback';
		
		if (isset($this->request->post['simplifycommerce_title'])) {
			$data['simplifycommerce_title'] = $this->request->post['simplifycommerce_title'];
		} else {
			$data['simplifycommerce_title'] = $this->config->get('simplifycommerce_title');
		}

		$this->load->model('localisation/order_status');
		
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['simplifycommerce_order_status_id'])) {
			$data['simplifycommerce_order_status_id'] = $this->request->post['simplifycommerce_order_status_id'];
		} else {
			$data['simplifycommerce_order_status_id'] = $this->config->get('simplifycommerce_order_status_id'); 
		}
		
		if (isset($this->request->post['simplifycommerce_declined_order_status_id'])) {
			$data['simplifycommerce_declined_order_status_id'] = $this->request->post['simplifycommerce_declined_order_status_id'];
		} else {
			$data['simplifycommerce_declined_order_status_id'] = $this->config->get('simplifycommerce_declined_order_status_id'); 
		}
		
		$this->load->model('localisation/geo_zone');
										
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['simplifycommerce_geo_zone_id'])) {
			$data['simplifycommerce_geo_zone_id'] = $this->request->post['simplifycommerce_geo_zone_id'];
		} else {
			$data['simplifycommerce_geo_zone_id'] = $this->config->get('simplifycommerce_geo_zone_id'); 
		} 
		
		if (isset($this->request->post['simplifycommerce_status'])) {
			$data['simplifycommerce_status'] = $this->request->post['simplifycommerce_status'];
		} else {
			$data['simplifycommerce_status'] = $this->config->get('simplifycommerce_status');
		}
		
		if (isset($this->request->post['simplifycommerce_sort_order'])) {
			$data['simplifycommerce_sort_order'] = $this->request->post['simplifycommerce_sort_order'];
		} else {
			$data['simplifycommerce_sort_order'] = $this->config->get('simplifycommerce_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payment/simplifycommerce.tpl', $data));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/simplifycommerce')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->request->post['simplifycommerce_title']) {
			$this->error['title'] = $this->language->get('error_title');
		}
		
		if (!$this->request->post['simplifycommerce_testsecretkey']) {
			$this->error['testsecretkey'] = $this->language->get('error_testsecretkey');
		}
		
		if (!$this->request->post['simplifycommerce_testpubkey']) {
			$this->error['testpubkey'] = $this->language->get('error_testpubkey');
		}

		// If in live mode, check that we have private and pub live keys
		if(!$this->request->post['simplifycommerce_test']){
			if(!$this->request->post['simplifycommerce_livesecretkey']){
				$this->error['livesecretkey'] = $this->language->get('error_livesecretkey');
			}
			if(!$this->request->post['simplifycommerce_livepubkey']){
				$this->error['livepubkey'] = $this->language->get('error_livepubkey');
			}
		}
		return !$this->error;
	}
}
?>
