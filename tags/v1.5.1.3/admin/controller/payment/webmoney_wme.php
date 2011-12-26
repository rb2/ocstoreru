<?php
class ControllerPaymentWebmoneyWME extends Controller {
	private $error = array();
	
	public function index() {
		$this->load->language('payment/webmoney_wme');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('webmoney_wme', $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['entry_merch_e'] = $this->language->get('entry_merch_e');
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
		$this->data['entry_donate_me'] = $this->language->get('entry_donate_me');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		
		$this->data['tab_general'] = $this->language->get('tab_general');
		
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->error['merch_e'])) {
			$this->data['error_merch_e'] = $this->error['merch_e'];
		} else {
			$this->data['error_merch_e'] = '';
		}
		
		if (isset($this->error['secret_key'])) {
			$this->data['error_secret_key'] = $this->error['secret_key'];
		} else {
			$this->data['error_secret_key'] = '';
		}
		
		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/webmoney_wme', 'token=' . $this->session->data['token'], 'SSL'),      		
      		'separator' => ' :: '
   		);
				
		$this->data['action'] = $this->url->link('payment/webmoney_wme', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
		
		
		// Номер магазина
		if (isset($this->request->post['webmoney_wme_merch_e'])) {
			$this->data['webmoney_wme_merch_e'] = $this->request->post['webmoney_wme_merch_e'];
		} else {
			$this->data['webmoney_wme_merch_e'] = $this->config->get('webmoney_wme_merch_e');
		}
		
		// zp_merhant_key
		if (isset($this->request->post['webmoney_wme_secret_key'])) {
			$this->data['webmoney_wme_secret_key'] = $this->request->post['webmoney_wme_secret_key'];
		} else {
			$this->data['webmoney_wme_secret_key'] = $this->config->get('webmoney_wme_secret_key');
		}
		
		
		// URL
		$this->data['webmoney_wme_result_url'] 		= HTTP_CATALOG . 'index.php?route=payment/webmoney_wme/callback';
		$this->data['webmoney_wme_success_url'] 	= HTTP_CATALOG . 'index.php?route=payment/webmoney_wme/success';
		$this->data['webmoney_wme_fail_url'] 		= HTTP_CATALOG . 'index.php?route=payment/webmoney_wme/fail';
		
		
		if (isset($this->request->post['webmoney_wme_order_status_id'])) {
			$this->data['webmoney_wme_order_status_id'] = $this->request->post['webmoney_wme_order_status_id'];
		} else {
			$this->data['webmoney_wme_order_status_id'] = $this->config->get('webmoney_wme_order_status_id'); 
		}
		
		
		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['webmoney_wme_geo_zone_id'])) {
			$this->data['webmoney_wme_geo_zone_id'] = $this->request->post['webmoney_wme_geo_zone_id'];
		} else {
			$this->data['webmoney_wme_geo_zone_id'] = $this->config->get('webmoney_wme_geo_zone_id'); 
		}
		
		$this->load->model('localisation/geo_zone');
		
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['webmoney_wme_status'])) {
			$this->data['webmoney_wme_status'] = $this->request->post['webmoney_wme_status'];
		} else {
			$this->data['webmoney_wme_status'] = $this->config->get('webmoney_wme_status');
		}
		
		if (isset($this->request->post['webmoney_wme_sort_order'])) {
			$this->data['webmoney_wme_sort_order'] = $this->request->post['webmoney_wme_sort_order'];
		} else {
			$this->data['webmoney_wme_sort_order'] = $this->config->get('webmoney_wme_sort_order');
		}
		
		$this->template = 'payment/webmoney_wme.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/webmoney_wme')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		
		// TODO проверку на валидность номера!
		if (!$this->request->post['webmoney_wme_merch_e']) {
			$this->error['merch_e'] = $this->language->get('error_merch_e');
		}
		
		if (!$this->request->post['webmoney_wme_secret_key']) {
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