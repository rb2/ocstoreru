<?php 
class ControllerPaymentQiwi extends Controller {
	private $error = array(); 

	public function index() {
		$this->load->language('payment/qiwi');

		$this->document->title = $this->language->get('heading_title');
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->load->model('setting/setting');
			
			$this->model_setting_setting->editSetting('qiwi', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect(HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token']);
		}

		$this->data['heading_title'] = $this->language->get('heading_title');
		
		
		
		$this->data['entry_shop_id'] = $this->language->get('entry_shop_id');
		$this->data['entry_password'] = $this->language->get('entry_password');
		
		
		
		
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
		
 		if (isset($this->error['shop_id'])) {
			$this->data['error_shop_id'] = $this->error['shop_id'];
		} else {
			$this->data['error_shop_id'] = '';
		}
		
		if (isset($this->error['password'])) {
			$this->data['error_password'] = $this->error['password'];
		} else {
			$this->data['error_password'] = '';
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
       		'href'      => HTTPS_SERVER . 'index.php?route=payment/qiwi&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);
				
		$this->data['action'] = HTTPS_SERVER . 'index.php?route=payment/qiwi&token=' . $this->session->data['token'];
		
		$this->data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token'];
		
		
		// Номер магазина 
		if (isset($this->request->post['qiwi_shop_id'])) {
			$this->data['qiwi_shop_id'] = $this->request->post['qiwi_shop_id'];
		} else {
			$this->data['qiwi_shop_id'] = $this->config->get('qiwi_shop_id');
		}
		
		// zp_merhant_key
		if (isset($this->request->post['qiwi_password'])) {
			$this->data['qiwi_password'] = $this->request->post['qiwi_password'];
		} else {
			$this->data['qiwi_password'] = $this->config->get('qiwi_password');
		}
		
		
		// URL
		$this->data['webmoney_result_url'] 		= HTTP_CATALOG . 'index.php?route=payment/qiwi/callback';
		$this->data['webmoney_success_url'] 	= HTTP_CATALOG . 'index.php?route=payment/qiwi/success';
		$this->data['webmoney_fail_url'] 		= HTTP_CATALOG . 'index.php?route=payment/qiwi/fail';
		

		if (isset($this->request->post['qiwi_order_status_id'])) {
			$this->data['qiwi_order_status_id'] = $this->request->post['qiwi_order_status_id'];
		} else {
			$this->data['qiwi_order_status_id'] = $this->config->get('qiwi_order_status_id'); 
		} 
		

		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['qiwi_geo_zone_id'])) {
			$this->data['qiwi_geo_zone_id'] = $this->request->post['qiwi_geo_zone_id'];
		} else {
			$this->data['qiwi_geo_zone_id'] = $this->config->get('qiwi_geo_zone_id'); 
		} 
		
		$this->load->model('localisation/geo_zone');
										
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['qiwi_status'])) {
			$this->data['qiwi_status'] = $this->request->post['qiwi_status'];
		} else {
			$this->data['qiwi_status'] = $this->config->get('qiwi_status');
		}
		
		if (isset($this->request->post['qiwi_sort_order'])) {
			$this->data['qiwi_sort_order'] = $this->request->post['qiwi_sort_order'];
		} else {
			$this->data['qiwi_sort_order'] = $this->config->get('qiwi_sort_order');
		}
		
		$this->template = 'payment/qiwi.tpl';
		$this->children = array(
			'common/header',	
			'common/footer'	
		);
		
		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/qiwi')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		
		// TODO проверку на валидность номера!
		if (!$this->request->post['qiwi_shop_id']) {			
			$this->error['shop_id'] = $this->language->get('error_shop_id');
		}
		
		if (!$this->request->post['qiwi_password']) {
			$this->error['password'] = $this->language->get('error_password');
		}
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
}
?>