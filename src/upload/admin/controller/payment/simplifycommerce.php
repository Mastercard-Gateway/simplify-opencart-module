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
			
			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		$this->data['text_yes'] = $this->language->get('text_yes');
		$this->data['text_no'] = $this->language->get('text_no');
		$this->data['text_successful'] = $this->language->get('text_successful');
		$this->data['text_declined'] = $this->language->get('text_declined');
		$this->data['text_off'] = $this->language->get('text_off');
		$this->data['text_test'] = $this->language->get('text_test');
		$this->data['text_prod'] = $this->language->get('text_prod');
		
		$this->data['entry_livesecretkey'] = $this->language->get('entry_livesecretkey');
		$this->data['entry_livepubkey'] = $this->language->get('entry_livepubkey');
		$this->data['entry_testsecretkey'] = $this->language->get('entry_testsecretkey');
		$this->data['entry_testpubkey'] = $this->language->get('entry_testpubkey');
		$this->data['entry_title'] = $this->language->get('entry_title');
		$this->data['entry_title_help'] = $this->language->get('entry_title_help');
		$this->data['entry_check_address_line_1'] = $this->language->get('entry_check_address_line_1');
		$this->data['entry_check_zip'] = $this->language->get('entry_check_zip');
		$this->data['entry_webhook_url'] = $this->language->get('entry_webhook_url');
		$this->data['entry_webhook_url_help'] = $this->language->get('entry_webhook_url_help');
		$this->data['entry_test'] = $this->language->get('entry_test');
		$this->data['entry_order_status'] = $this->language->get('entry_order_status');
		$this->data['entry_declined_order_status'] = $this->language->get('entry_declined_order_status');	
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['permission'])) {
			$this->data['error_permission'] = $this->error['permission'];
		} else {
			$this->data['error_permission'] = '';
		}
		
		if (isset($this->error['testsecretkey'])) {
			$this->data['error_testsecretkey'] = $this->error['testsecretkey'];
		} else {
			$this->data['error_testsecretkey'] = '';
		}
		
		if (isset($this->error['testpubkey'])) {
			$this->data['error_testpubkey'] = $this->error['testpubkey'];
		} else {
			$this->data['error_testpubkey'] = '';
		}
		
		if (isset($this->error['title'])) {
			$this->data['error_title'] = $this->error['title'];
		} else {
			$this->data['error_title'] = '';
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][]  = array(
       		'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);

   		$this->data['breadcrumbs'][]  = array(
       		'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
       		'text'      => $this->language->get('text_payment'),
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][]  = array(
       		'href'      => $this->url->link('payment/simplifycommerce', 'token=' . $this->session->data['token'], 'SSL'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);
				
		$this->data['action'] = $this->url->link('payment/simplifycommerce', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
		
		if (isset($this->request->post['simplifycommerce_test'])) {
			$this->data['simplifycommerce_test'] = trim($this->request->post['simplifycommerce_test']);
		} else {
			$this->data['simplifycommerce_test'] = $this->config->get('simplifycommerce_test'); 
		}

		if (isset($this->request->post['simplifycommerce_livesecretkey'])) {
			$this->data['simplifycommerce_livesecretkey'] = trim($this->request->post['simplifycommerce_livesecretkey']);
		} else {
			$this->data['simplifycommerce_livesecretkey'] = $this->config->get('simplifycommerce_livesecretkey');
		}
		
		if (isset($this->request->post['simplifycommerce_livepubkey'])) {
			$this->data['simplifycommerce_livepubkey'] = trim($this->request->post['simplifycommerce_livepubkey']);
		} else {
			$this->data['simplifycommerce_livepubkey'] = $this->config->get('simplifycommerce_livepubkey');
		}
		
		if (isset($this->request->post['simplifycommerce_testsecretkey'])) {
			$this->data['simplifycommerce_testsecretkey'] = trim($this->request->post['simplifycommerce_testsecretkey']);
		} else {
			$this->data['simplifycommerce_testsecretkey'] = $this->config->get('simplifycommerce_testsecretkey');
		}
		
		if (isset($this->request->post['simplifycommerce_testpubkey'])) {
			$this->data['simplifycommerce_testpubkey'] = trim($this->request->post['simplifycommerce_testpubkey']);
		} else {
			$this->data['simplifycommerce_testpubkey'] = $this->config->get('simplifycommerce_testpubkey');
		}
		
		if (isset($this->request->post['simplifycommerce_allow_stored'])) {
			$this->data['simplifycommerce_allow_stored'] = $this->request->post['simplifycommerce_allow_stored'];
		} else {
			$this->data['simplifycommerce_allow_stored'] = $this->config->get('simplifycommerce_allow_stored');
		}
		
		if (isset($this->request->post['simplifycommerce_check_cvc'])) {
			$this->data['simplifycommerce_check_cvc'] = $this->request->post['simplifycommerce_check_cvc'];
		} else {
			$this->data['simplifycommerce_check_cvc'] = $this->config->get('simplifycommerce_check_cvc');
		}

		$this->data['webhook_url'] = HTTPS_CATALOG . 'index.php?route=payment/simplifycommerce/callback';
		
		if (isset($this->request->post['simplifycommerce_title'])) {
			$this->data['simplifycommerce_title'] = $this->request->post['simplifycommerce_title'];
		} else {
			$this->data['simplifycommerce_title'] = $this->config->get('simplifycommerce_title');
		}

		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['simplifycommerce_order_status_id'])) {
			$this->data['simplifycommerce_order_status_id'] = $this->request->post['simplifycommerce_order_status_id'];
		} else {
			$this->data['simplifycommerce_order_status_id'] = $this->config->get('simplifycommerce_order_status_id'); 
		}
		
		if (isset($this->request->post['simplifycommerce_declined_order_status_id'])) {
			$this->data['simplifycommerce_declined_order_status_id'] = $this->request->post['simplifycommerce_declined_order_status_id'];
		} else {
			$this->data['simplifycommerce_declined_order_status_id'] = $this->config->get('simplifycommerce_declined_order_status_id'); 
		}
		
		$this->load->model('localisation/geo_zone');
										
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['simplifycommerce_geo_zone_id'])) {
			$this->data['simplifycommerce_geo_zone_id'] = $this->request->post['simplifycommerce_geo_zone_id'];
		} else {
			$this->data['simplifycommerce_geo_zone_id'] = $this->config->get('simplifycommerce_geo_zone_id'); 
		} 
		
		if (isset($this->request->post['simplifycommerce_status'])) {
			$this->data['simplifycommerce_status'] = $this->request->post['simplifycommerce_status'];
		} else {
			$this->data['simplifycommerce_status'] = $this->config->get('simplifycommerce_status');
		}
		
		if (isset($this->request->post['simplifycommerce_sort_order'])) {
			$this->data['simplifycommerce_sort_order'] = $this->request->post['simplifycommerce_sort_order'];
		} else {
			$this->data['simplifycommerce_sort_order'] = $this->config->get('simplifycommerce_sort_order');
		}
		
		$this->template = 'payment/simplifycommerce.tpl';
		$this->children = array(
			'common/header',	
			'common/footer'	
		);
		
		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
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
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
}
?>
