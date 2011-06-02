<?php 
class ControllerPaymentZpayment extends Controller {
	private $error = array(); 

	public function index() {
		$this->load->language('payment/zpayment');

		$this->document->title = $this->language->get('heading_title');
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->load->model('setting/setting');
			
			$this->model_setting_setting->editSetting('zpayment', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect( HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token']);
		}

		$this->data['heading_title'] = $this->language->get('heading_title');
		
		
		
		$this->data['entry_shop_id'] = $this->language->get('entry_shop_id');
		$this->data['entry_merchant_key'] = $this->language->get('entry_merchant_key');
		$this->data['entry_init_password'] = $this->language->get('entry_init_password');
		$this->data['entry_result_url'] = $this->language->get('entry_result_url');
		$this->data['entry_success_url'] = $this->language->get('entry_success_url');
		$this->data['entry_fail_url'] = $this->language->get('entry_fail_url');
		

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');

		$this->data['entry_order_status'] = $this->language->get('entry_order_status');
		$this->data['entry_order_wait'] = $this->language->get('entry_order_wait');	
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
		
		if (isset($this->error['merchant_key'])) {
			$this->data['error_merchant_key'] = $this->error['merchant_key'];
		} else {
			$this->data['error_merchant_key'] = '';
		}
		
		if (isset($this->error['init_password'])) {
			$this->data['error_init_password'] = $this->error['init_password'];
		} else {
			$this->data['error_init_password'] = '';
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
       		'href'      => HTTPS_SERVER . 'index.php?route=payment/zpayment&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);
				
		$this->data['action'] = HTTPS_SERVER . 'index.php?route=payment/zpayment&token=' . $this->session->data['token'];
		
		$this->data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/payment&token=' . $this->session->data['token'];
		
		
		// Номер магазина
		if (isset($this->request->post['zpayment_shop_id'])) {
			$this->data['zpayment_shop_id'] = $this->request->post['zpayment_shop_id'];
		} else {
			$this->data['zpayment_shop_id'] = $this->config->get('zpayment_shop_id');
		}
		
		// zp_merhant_key
		if (isset($this->request->post['zpayment_merchant_key'])) {
			$this->data['zpayment_merchant_key'] = $this->request->post['zpayment_merchant_key'];
		} else {
			$this->data['zpayment_merchant_key'] = $this->config->get('zpayment_merchant_key');
		}
		
		//init_password
		if (isset($this->request->post['zpayment_init_password'])) {
			$this->data['zpayment_init_password'] = $this->request->post['zpayment_init_password'];
		} else {
			$this->data['zpayment_init_password'] = $this->config->get('zpayment_init_password');
		}
		
		// URL
		$this->data['zp_result_url'] 	= HTTP_CATALOG . 'index.php?route=payment/zpayment/callback';
		$this->data['zp_success_url'] 	= HTTP_CATALOG . 'index.php?route=payment/zpayment/success';
		$this->data['zp_fail_url'] 		= HTTP_CATALOG . 'index.php?route=payment/zpayment/fail';
		

		if (isset($this->request->post['zpayment_order_status_id'])) {
			$this->data['zpayment_order_status_id'] = $this->request->post['zpayment_order_status_id'];
		} else {
			$this->data['zpayment_order_status_id'] = $this->config->get('zpayment_order_status_id'); 
		}
		 
		if (isset($this->request->post['zpayment_order_wait_id'])) {
        	$this->data['zpayment_order_wait_id'] = $this->request->post['zpayment_order_wait_id'];
        } else {
            $this->data['zpayment_order_wait_id'] = $this->config->get('zpayment_order_wait_id');
        }


		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['zpayment_geo_zone_id'])) {
			$this->data['zpayment_geo_zone_id'] = $this->request->post['zpayment_geo_zone_id'];
		} else {
			$this->data['zpayment_geo_zone_id'] = $this->config->get('zpayment_geo_zone_id'); 
		} 
		
		$this->load->model('localisation/geo_zone');
										
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['zpayment_status'])) {
			$this->data['zpayment_status'] = $this->request->post['zpayment_status'];
		} else {
			$this->data['zpayment_status'] = $this->config->get('zpayment_status');
		}
		
		if (isset($this->request->post['zpayment_sort_order'])) {
			$this->data['zpayment_sort_order'] = $this->request->post['zpayment_sort_order'];
		} else {
			$this->data['zpayment_sort_order'] = $this->config->get('zpayment_sort_order');
		}
		
		$this->template = 'payment/zpayment.tpl';
		$this->children = array(
			'common/header',	
			'common/footer'	
		);
		
		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/zpayment')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!(int)$this->request->post['zpayment_shop_id']) {			
			$this->error['shop_id'] = $this->language->get('error_shop_id');
		}
		
		if (!$this->request->post['zpayment_merchant_key']) {
			$this->error['merchant_key'] = $this->language->get('error_merchant_key');
		}
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
}
?>