<?php 
class ControllerPaymentFlSberBank extends Controller {
	private $error = array(); 

	public function index() {
		$this->load->language('payment/fl_sberbank');

		$this->document->title = $this->language->get('heading_title');
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->load->model('setting/setting');
			
			$this->model_setting_setting->editSetting('fl_sberbank', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect(HTTPS_SERVER.'index.php?route=extension/payment&token=' . $this->session->data['token']);
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		
		$this->data['entry_bank'] = $this->language->get('entry_bank');
		$this->data['entry_inn'] = $this->language->get('entry_inn');
		$this->data['entry_rs'] = $this->language->get('entry_rs');
		$this->data['entry_bankuser'] = $this->language->get('entry_bankuser');
		$this->data['entry_bik'] = $this->language->get('entry_bik');
		$this->data['entry_ks'] = $this->language->get('entry_ks');
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
		
		$this->load->model('localisation/language');
		
		$languages = $this->model_localisation_language->getLanguages();
		
		foreach ($languages as $language) {
			if (isset($this->error['bank_' . $language['language_id']])) {
				$this->data['error_bank_' . $language['language_id']] = $this->error['bank_' . $language['language_id']];
			} else {
				$this->data['error_bank_' . $language['language_id']] = '';
			}
			
			if (isset($this->error['inn_' . $language['language_id']])) {
				$this->data['error_inn_' . $language['language_id']] = $this->error['inn_' . $language['language_id']];
			} else {
				$this->data['error_inn_' . $language['language_id']] = '';
			}
			
			if (isset($this->error['rs_' . $language['language_id']])) {
				$this->data['error_rs_' . $language['language_id']] = $this->error['rs_' . $language['language_id']];
			} else {
				$this->data['error_rs_' . $language['language_id']] = '';
			}
			
			if (isset($this->error['bankuser_' . $language['language_id']])) {
				$this->data['error_bankuser_' . $language['language_id']] = $this->error['bankuser_' . $language['language_id']];
			} else {
				$this->data['error_bankuser_' . $language['language_id']] = '';
			}
			
			if (isset($this->error['bik_' . $language['language_id']])) {
				$this->data['error_bik_' . $language['language_id']] = $this->error['bik_' . $language['language_id']];
			} else {
				$this->data['error_bik_' . $language['language_id']] = '';
			}
			
			if (isset($this->error['ks_' . $language['language_id']])) {
				$this->data['error_ks_' . $language['language_id']] = $this->error['ks_' . $language['language_id']];
			} else {
				$this->data['error_ks_' . $language['language_id']] = '';
			}
		}
		
  		$this->document->breadcrumbs = array();

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER.'index.php?route=common/home&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER.'index.php?route=extension/payment&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('text_payment'),
      		'separator' => ' :: '
   		);

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER.'index.php?route=payment/fl_sberbank&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);
				
		$this->data['action'] = HTTPS_SERVER.'index.php?route=payment/fl_sberbank&token=' . $this->session->data['token'];
		
		$this->data['cancel'] = HTTPS_SERVER.'index.php?route=extension/payment&token=' . $this->session->data['token'];

		$this->load->model('localisation/language');
		
		foreach ($languages as $language) {
			
			//название организации ООО Пупкин
			if (isset($this->request->post['fl_sberbank_bank_' . $language['language_id']])) {
				$this->data['fl_sberbank_bank_' . $language['language_id']] = $this->request->post['fl_sberbank_bank_' . $language['language_id']];
			} else {
				$this->data['fl_sberbank_bank_' . $language['language_id']] = $this->config->get('fl_sberbank_bank_' . $language['language_id']);
				}
			//ИНН	
			if (isset($this->request->post['fl_sberbank_inn_' . $language['language_id']])) {
				$this->data['fl_sberbank_inn_' . $language['language_id']] = $this->request->post['fl_sberbank_inn_' . $language['language_id']];
			} else {
				$this->data['fl_sberbank_inn_' . $language['language_id']] = $this->config->get('fl_sberbank_inn_' . $language['language_id']);
			}
			//Расчетный счет
			if (isset($this->request->post['fl_sberbank_rs_' . $language['language_id']])) {
				$this->data['fl_sberbank_rs_' . $language['language_id']] = $this->request->post['fl_sberbank_rs_' . $language['language_id']];
			} else {
				$this->data['fl_sberbank_rs_' . $language['language_id']] = $this->config->get('fl_sberbank_rs_' . $language['language_id']);
			}
			//Наименование банка получателя платежа
			if (isset($this->request->post['fl_sberbank_bankuser_' . $language['language_id']])) {
				$this->data['fl_sberbank_bankuser_' . $language['language_id']] = $this->request->post['fl_sberbank_bankuser_' . $language['language_id']];
			} else {
				$this->data['fl_sberbank_bankuser_' . $language['language_id']] = $this->config->get('fl_sberbank_bankuser_' . $language['language_id']);
			}
			//БИК
			if (isset($this->request->post['fl_sberbank_bik_' . $language['language_id']])) {
				$this->data['fl_sberbank_bik_' . $language['language_id']] = $this->request->post['fl_sberbank_bik_' . $language['language_id']];
			} else {
				$this->data['fl_sberbank_bik_' . $language['language_id']] = $this->config->get('fl_sberbank_bik_' . $language['language_id']);
			}
			//Номер кор./сч. банка получателя платежа
			if (isset($this->request->post['fl_sberbank_ks_' . $language['language_id']])) {
				$this->data['fl_sberbank_ks_' . $language['language_id']] = $this->request->post['fl_sberbank_ks_' . $language['language_id']];
			} else {
				$this->data['fl_sberbank_ks_' . $language['language_id']] = $this->config->get('fl_sberbank_ks_' . $language['language_id']);
			}
		}
		
		$this->data['languages'] = $languages;
		
		if (isset($this->request->post['fl_sberbank_order_status_id'])) {
			$this->data['fl_sberbank_order_status_id'] = $this->request->post['fl_sberbank_order_status_id'];
		} else {
			$this->data['fl_sberbank_order_status_id'] = $this->config->get('fl_sberbank_order_status_id'); 
		} 
		
		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['fl_sberbank_geo_zone_id'])) {
			$this->data['fl_sberbank_geo_zone_id'] = $this->request->post['fl_sberbank_geo_zone_id'];
		} else {
			$this->data['fl_sberbank_geo_zone_id'] = $this->config->get('fl_sberbank_geo_zone_id'); 
		} 
		
		$this->load->model('localisation/geo_zone');
										
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['fl_sberbank_status'])) {
			$this->data['fl_sberbank_status'] = $this->request->post['fl_sberbank_status'];
		} else {
			$this->data['fl_sberbank_status'] = $this->config->get('fl_sberbank_status');
		}
		
		if (isset($this->request->post['fl_sberbank_sort_order'])) {
			$this->data['fl_sberbank_sort_order'] = $this->request->post['fl_sberbank_sort_order'];
		} else {
			$this->data['fl_sberbank_sort_order'] = $this->config->get('fl_sberbank_sort_order');
		}
		
		$this->template = 'payment/fl_sberbank.tpl';
		$this->children = array(
			'common/header',	
			'common/footer'	
		);
		
		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/fl_sberbank')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('localisation/language');

		$languages = $this->model_localisation_language->getLanguages();
		
		foreach ($languages as $language) {
			if (!$this->request->post['fl_sberbank_bank_' . $language['language_id']]) {
				$this->error['bank_' .  $language['language_id']] = $this->language->get('error_bank');
			}
			if (!$this->request->post['fl_sberbank_inn_' . $language['language_id']]) {
				$this->error['inn_' .  $language['language_id']] = $this->language->get('error_bank');
			}
			if (!$this->request->post['fl_sberbank_rs_' . $language['language_id']]) {
				$this->error['rs_' .  $language['language_id']] = $this->language->get('error_bank');
			}
			if (!$this->request->post['fl_sberbank_bankuser_' . $language['language_id']]) {
				$this->error['bankuser_' .  $language['language_id']] = $this->language->get('error_bank');
			}
			if (!$this->request->post['fl_sberbank_bik_' . $language['language_id']]) {
				$this->error['bik_' .  $language['language_id']] = $this->language->get('error_bank');
			}
			if (!$this->request->post['fl_sberbank_ks_' . $language['language_id']]) {
				$this->error['ks_' .  $language['language_id']] = $this->language->get('error_bank');
			}
		}
			
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
}
?>