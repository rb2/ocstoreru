<?php 
class ControllerPaymentWebmoney extends Controller {
	private $error = array(); 

	public function index() {
		$this->load->language('payment/webmoney');

		$this->document->title = $this->language->get('heading_title');
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->load->model('setting/setting');
			
			$this->model_setting_setting->editSetting('webmoney', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect(HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token']);
		}

		$this->data['heading_title'] = $this->language->get('heading_title');
		
		
		
		$this->data['entry_merch_r'] = $this->language->get('entry_merch_r');
		$this->data['entry_secret_key'] = $this->language->get('entry_secret_key');
		$this->data['entry_result_url'] = $this->language->get('entry_result_url');
		$this->data['entry_success_url'] = $this->language->get('entry_success_url');
		$this->data['entry_fail_url'] = $this->language->get('entry_fail_url');	

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');

		$this->data['entry_order_status'] = $this->language->get('entry_order_status');	
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['tab_general'] = $this->language->get('tab_general');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
 		if (isset($this->error['merch_r'])) {
			$this->data['error_merch_r'] = $this->error['merch_r'];
		} else {
			$this->data['error_merch_r'] = '';
		}
		
		if (isset($this->error['secret_key'])) {
			$this->data['error_secret_key'] = $this->error['secret_key'];
		} else {
			$this->data['error_secret_key'] = '';
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
       		'href'      => HTTPS_SERVER . 'index.php?route=payment/webmoney&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);
				
		$this->data['action'] = HTTPS_SERVER . 'index.php?route=payment/webmoney&token=' . $this->session->data['token'];
		
		$this->data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token'];
		
		
		// Номер магазина 
		if (isset($this->request->post['webmoney_merch_r'])) {
			$this->data['webmoney_merch_r'] = $this->request->post['webmoney_merch_r'];
		} else {
			$this->data['webmoney_merch_r'] = $this->config->get('webmoney_merch_r');
		}
		
		// zp_merhant_key
		if (isset($this->request->post['webmoney_secret_key'])) {
			$this->data['webmoney_secret_key'] = $this->request->post['webmoney_secret_key'];
		} else {
			$this->data['webmoney_secret_key'] = $this->config->get('webmoney_secret_key');
		}
		
		
		// URL
		$this->data['webmoney_result_url'] 		= HTTP_CATALOG . 'index.php?route=payment/webmoney/callback';
		$this->data['webmoney_success_url'] 	= HTTP_CATALOG . 'index.php?route=payment/webmoney/success';
		$this->data['webmoney_fail_url'] 		= HTTP_CATALOG . 'index.php?route=payment/webmoney/fail';
		

		if (isset($this->request->post['webmoney_order_status_id'])) {
			$this->data['webmoney_order_status_id'] = $this->request->post['webmoney_order_status_id'];
		} else {
			$this->data['webmoney_order_status_id'] = $this->config->get('webmoney_order_status_id'); 
		} 
		

		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['webmoney_geo_zone_id'])) {
			$this->data['webmoney_geo_zone_id'] = $this->request->post['webmoney_geo_zone_id'];
		} else {
			$this->data['webmoney_geo_zone_id'] = $this->config->get('webmoney_geo_zone_id'); 
		} 
		
		$this->load->model('localisation/geo_zone');
										
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['webmoney_status'])) {
			$this->data['webmoney_status'] = $this->request->post['webmoney_status'];
		} else {
			$this->data['webmoney_status'] = $this->config->get('webmoney_status');
		}
		
		if (isset($this->request->post['webmoney_sort_order'])) {
			$this->data['webmoney_sort_order'] = $this->request->post['webmoney_sort_order'];
		} else {
			$this->data['webmoney_sort_order'] = $this->config->get('webmoney_sort_order');
		}
		
		$this->template = 'payment/webmoney.tpl';
		$this->children = array(
			'common/header',	
			'common/footer'	
		);
		
		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/webmoney')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		
		// TODO проверку на валидность номера!
		if (!$this->request->post['webmoney_merch_r']) {			
			$this->error['merch_r'] = $this->language->get('error_merch_r');
		}
		
		if (!$this->request->post['webmoney_secret_key']) {
			$this->error['secret_key'] = $this->language->get('error_secret_key');
		}
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
}
?>