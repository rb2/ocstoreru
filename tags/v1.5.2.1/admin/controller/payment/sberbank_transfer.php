<?php
class ControllerPaymentSberBankTransfer extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('payment/sberbank_transfer');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('sberbank_transfer', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['entry_inn'] = $this->language->get('entry_inn');
		$this->data['entry_rs'] = $this->language->get('entry_rs');
		$this->data['entry_bankuser'] = $this->language->get('entry_bankuser');
		$this->data['entry_bik'] = $this->language->get('entry_bik');
		$this->data['entry_ks'] = $this->language->get('entry_ks');

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');

		$this->data['entry_bank'] = $this->language->get('entry_bank');
		$this->data['entry_total'] = $this->language->get('entry_total');
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
			'href'      => $this->url->link('payment/sberbank_transfer', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$this->data['action'] = $this->url->link('payment/sberbank_transfer', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		foreach ($languages as $language) {
			//название организации ООО Пупкин
			if (isset($this->request->post['sberbank_transfer_bank_' . $language['language_id']])) {
				$this->data['sberbank_transfer_bank_' . $language['language_id']] = $this->request->post['sberbank_transfer_bank_' . $language['language_id']];
			} else {
				$this->data['sberbank_transfer_bank_' . $language['language_id']] = $this->config->get('sberbank_transfer_bank_' . $language['language_id']);
			}
			//ИНН
			if (isset($this->request->post['sberbank_transfer_inn_' . $language['language_id']])) {
				$this->data['sberbank_transfer_inn_' . $language['language_id']] = $this->request->post['sberbank_transfer_inn_' . $language['language_id']];
			} else {
				$this->data['sberbank_transfer_inn_' . $language['language_id']] = $this->config->get('sberbank_transfer_inn_' . $language['language_id']);
			}
			//Расчетный счет
			if (isset($this->request->post['sberbank_transfer_rs_' . $language['language_id']])) {
				$this->data['sberbank_transfer_rs_' . $language['language_id']] = $this->request->post['sberbank_transfer_rs_' . $language['language_id']];
			} else {
				$this->data['sberbank_transfer_rs_' . $language['language_id']] = $this->config->get('sberbank_transfer_rs_' . $language['language_id']);
			}
			//Наименование банка получателя платежа
			if (isset($this->request->post['sberbank_transfer_bankuser_' . $language['language_id']])) {
				$this->data['sberbank_transfer_bankuser_' . $language['language_id']] = $this->request->post['sberbank_transfer_bankuser_' . $language['language_id']];
			} else {
				$this->data['sberbank_transfer_bankuser_' . $language['language_id']] = $this->config->get('sberbank_transfer_bankuser_' . $language['language_id']);
			}
			//БИК
			if (isset($this->request->post['sberbank_transfer_bik_' . $language['language_id']])) {
				$this->data['sberbank_transfer_bik_' . $language['language_id']] = $this->request->post['sberbank_transfer_bik_' . $language['language_id']];
			} else {
				$this->data['sberbank_transfer_bik_' . $language['language_id']] = $this->config->get('sberbank_transfer_bik_' . $language['language_id']);
			}
			//Номер кор./сч. банка получателя платежа
			if (isset($this->request->post['sberbank_transfer_ks_' . $language['language_id']])) {
				$this->data['sberbank_transfer_ks_' . $language['language_id']] = $this->request->post['sberbank_transfer_ks_' . $language['language_id']];
			} else {
				$this->data['sberbank_transfer_ks_' . $language['language_id']] = $this->config->get('sberbank_transfer_ks_' . $language['language_id']);
			}
		}

		$this->load->model('localisation/language');

		foreach ($languages as $language) {
			if (isset($this->request->post['sberbank_transfer_bank_' . $language['language_id']])) {
				$this->data['sberbank_transfer_bank_' . $language['language_id']] = $this->request->post['sberbank_transfer_bank_' . $language['language_id']];
			} else {
				$this->data['sberbank_transfer_bank_' . $language['language_id']] = $this->config->get('sberbank_transfer_bank_' . $language['language_id']);
			}
		}

		$this->data['languages'] = $languages;

		if (isset($this->request->post['sberbank_transfer_total'])) {
			$this->data['sberbank_transfer_total'] = $this->request->post['sberbank_transfer_total'];
		} else {
			$this->data['sberbank_transfer_total'] = $this->config->get('sberbank_transfer_total');
		}

		if (isset($this->request->post['sberbank_transfer_order_status_id'])) {
			$this->data['sberbank_transfer_order_status_id'] = $this->request->post['sberbank_transfer_order_status_id'];
		} else {
			$this->data['sberbank_transfer_order_status_id'] = $this->config->get('sberbank_transfer_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['sberbank_transfer_geo_zone_id'])) {
			$this->data['sberbank_transfer_geo_zone_id'] = $this->request->post['sberbank_transfer_geo_zone_id'];
		} else {
			$this->data['sberbank_transfer_geo_zone_id'] = $this->config->get('sberbank_transfer_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['sberbank_transfer_status'])) {
			$this->data['sberbank_transfer_status'] = $this->request->post['sberbank_transfer_status'];
		} else {
			$this->data['sberbank_transfer_status'] = $this->config->get('sberbank_transfer_status');
		}

		if (isset($this->request->post['sberbank_transfer_sort_order'])) {
			$this->data['sberbank_transfer_sort_order'] = $this->request->post['sberbank_transfer_sort_order'];
		} else {
			$this->data['sberbank_transfer_sort_order'] = $this->config->get('sberbank_transfer_sort_order');
		}

		$this->template = 'payment/sberbank_transfer.tpl';
		$this->children = array(
			'common/header',
			'common/footer',
		);

		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/sberbank_transfer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('localisation/language');

		$languages = $this->model_localisation_language->getLanguages();

		foreach ($languages as $language) {
			if (!$this->request->post['sberbank_transfer_bank_' . $language['language_id']]) {
				$this->error['bank_' .  $language['language_id']] = $this->language->get('error_bank');
			}
			if (!$this->request->post['sberbank_transfer_bank_' . $language['language_id']]) {
				$this->error['bank_' .  $language['language_id']] = $this->language->get('error_bank');
			}
			if (!$this->request->post['sberbank_transfer_inn_' . $language['language_id']]) {
				$this->error['inn_' .  $language['language_id']] = $this->language->get('error_bank');
			}
			if (!$this->request->post['sberbank_transfer_rs_' . $language['language_id']]) {
				$this->error['rs_' .  $language['language_id']] = $this->language->get('error_bank');
			}
			if (!$this->request->post['sberbank_transfer_bankuser_' . $language['language_id']]) {
				$this->error['bankuser_' .  $language['language_id']] = $this->language->get('error_bank');
			}
			if (!$this->request->post['sberbank_transfer_bik_' . $language['language_id']]) {
				$this->error['bik_' .  $language['language_id']] = $this->language->get('error_bank');
			}
			if (!$this->request->post['sberbank_transfer_ks_' . $language['language_id']]) {
				$this->error['ks_' .  $language['language_id']] = $this->language->get('error_bank');
			}
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
?>