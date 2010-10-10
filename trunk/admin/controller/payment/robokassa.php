<?php 
class ControllerPaymentRobokassa extends Controller {
	private $error = array(); 

	public function index() {
	
		$this->load->language('payment/robokassa');

		$this->document->title = $this->language->get('heading_title');
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->load->model('setting/setting');
			
			$this->model_setting_setting->editSetting('robokassa', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect(HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token']);
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		$this->data['text_liqpay'] = $this->language->get('text_liqpay');
		$this->data['text_card'] = $this->language->get('text_card');
		$this->data['text_yes'] = $this->language->get('text_yes');
		$this->data['text_no'] = $this->language->get('text_no');
		
		// ROBOKASSA ENTER
		$this->data['entry_login'] = $this->language->get('entry_login');
		$this->data['entry_password1'] = $this->language->get('entry_password1');
		$this->data['entry_password2'] = $this->language->get('entry_password2');
		
		// URL
		$this->data['copy_result_url'] 	= HTTP_CATALOG . 'index.php?route=payment/robokassa/callback';
		$this->data['copy_success_url']	= HTTP_CATALOG . 'index.php?route=payment/robokassa/success';
		$this->data['copy_fail_url'] 	= HTTP_CATALOG . 'index.php?route=checkout/fail';
		
		// TEST MODE
		$this->data['entry_test'] = $this->language->get('entry_test');
		
		
		
		
					
		$this->data['entry_order_status'] = $this->language->get('entry_order_status');
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['tab_general'] = $this->language->get('tab_general');
		
		//
		if (isset($this->error['warning'])) { 
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}


		// 
		if (isset($this->error['login'])) { 
			$this->data['error_login'] = $this->error['login'];
		} else {
			$this->data['error_login'] = '';
		}
		
		if (isset($this->error['password1'])) { 
			$this->data['error_password1'] = $this->error['password1'];
		} else {
			$this->data['error_password1'] = '';
		}
		
		if (isset($this->error['password2'])) { 
			$this->data['error_password2'] = $this->error['password2'];
		} else {
			$this->data['error_password2'] = '';
		}
		
		if (isset($this->request->post['robokassa_test'])) {
			$this->data['robokassa_test'] = $this->request->post['robokassa_test'];
		} else {
			$this->data['robokassa_test'] = $this->config->get('robokassa_test');
		}

		$this->document->breadcrumbs = array();

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=common/home&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('text_payment'),
      		'separator' => ' :: '
   		);

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=payment/robokassa&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);
				
		$this->data['action'] = HTTPS_SERVER . 'index.php?route=payment/robokassa&token=' . $this->session->data['token'];
		
		$this->data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token'];
		
		
		// 
		
		// 
		if (isset($this->request->post['robokassa_login'])) {
			$this->data['robokassa_login'] = $this->request->post['robokassa_login'];
		} else {
			$this->data['robokassa_login'] = $this->config->get('robokassa_login');
		}


		// 
		if (isset($this->request->post['robokassa_password1'])) {
			$this->data['robokassa_password1'] = $this->request->post['robokassa_password1'];
		} else {
			$this->data['robokassa_password1'] = $this->config->get('robokassa_password1');
		}
		
		//
		if (isset($this->request->post['robokassa_password2'])) {
			$this->data['robokassa_password2'] = $this->request->post['robokassa_password2'];
		} else {
			$this->data['robokassa_password2'] = $this->config->get('robokassa_password2');
		}
		
		if (isset($this->request->post['robokassa_order_status_id'])) {
			$this->data['robokassa_order_status_id'] = $this->request->post['robokassa_order_status_id'];
		} else {
			$this->data['robokassa_order_status_id'] = $this->config->get('robokassa_order_status_id'); 
		} 

		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['robokassa_geo_zone_id'])) {
			$this->data['robokassa_geo_zone_id'] = $this->request->post['robokassa_geo_zone_id'];
		} else {
			$this->data['robokassa_geo_zone_id'] = $this->config->get('robokassa_geo_zone_id'); 
		} 		
		
		$this->load->model('localisation/geo_zone');
										
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['robokassa_status'])) {
			$this->data['robokassa_status'] = $this->request->post['robokassa_status'];
		} else {
			$this->data['robokassa_status'] = $this->config->get('robokassa_status');
		}
		
		if (isset($this->request->post['robokassa_sort_order'])) {
			$this->data['robokassa_sort_order'] = $this->request->post['robokassa_sort_order'];
		} else {
			$this->data['robokassa_sort_order'] = $this->config->get('robokassa_sort_order');
		}
		
		$this->template = 'payment/robokassa.tpl';
		$this->children = array(
			'common/header',	
			'common/footer'	
		);
		
		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/robokassa')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->request->post['robokassa_login']) {
			$this->error['login'] = $this->language->get('error_login');
		}

		if (!$this->request->post['robokassa_password1']) {
			$this->error['password1'] = $this->language->get('error_password1');
		}
		
		if (!$this->request->post['robokassa_password2']) {
			$this->error['password2'] = $this->language->get('error_password2');
		}
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
}
?>