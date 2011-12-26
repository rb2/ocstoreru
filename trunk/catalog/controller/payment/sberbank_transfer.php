<?php
class ControllerPaymentSberBankTransfer extends Controller {
	protected function index() {
		$this->language->load('payment/sberbank_transfer');

		$this->data['text_printpay'] = str_replace('{href}', $this->url->link('payment/sberbank_transfer/printpay', '', 'SSL'), $this->language->get('text_printpay'));
		$this->data['text_instruction'] = $this->language->get('text_instruction');
		$this->data['text_payment'] = $this->language->get('text_payment');
		$this->data['text_payment_coment'] = $this->language->get('text_payment_coment');
		if ($this->customer->isLogged()) {
			$this->data['text_order_history'] = str_replace('{href}', $this->url->link('account/order', '', 'SSL'), $this->language->get('text_order_history'));
		} else {
			$this->data['text_order_history'] = '';
		}

		$this->data['button_confirm'] = $this->language->get('button_confirm');

		$this->data['continue'] = $this->url->link('checkout/success');

		$this->template = $this->config->get('config_template') . '/template/payment/sberbank_transfer.tpl';

		if (!file_exists(DIR_TEMPLATE . $this->template)) {
			$this->template = 'default/template/payment/sberbank_transfer.tpl';
		}

		$this->render();
	}

	public function printpay() {

		if (!empty($this->request->get['order_id'])) {
			$this->load->model('account/order');

			$order_info = $this->model_account_order->getOrder($this->request->get['order_id']);
		} else {
			$this->load->model('checkout/order');

			$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		}

		if (!$order_info) {
			return $this->forward('account/order');
		}

		$this->language->load('payment/sberbank_transfer');

		$this->data['text_confirm'] = $this->language->get('text_confirm');

		$this->data['button_confirm'] = $this->language->get('button_confirm');
		$this->data['button_back'] = $this->language->get('button_back');

		$this->data['bank'] = nl2br($this->config->get('sberbank_transfer_bank_' . $this->config->get('config_language_id')));
		$this->data['inn'] = nl2br($this->config->get('sberbank_transfer_inn_' . $this->config->get('config_language_id')));
		$this->data['rs'] = nl2br($this->config->get('sberbank_transfer_rs_' . $this->config->get('config_language_id')));
		$this->data['bankuser'] = nl2br($this->config->get('sberbank_transfer_bankuser_' . $this->config->get('config_language_id')));
		$this->data['bik'] = nl2br($this->config->get('sberbank_transfer_bik_' . $this->config->get('config_language_id')));
		$this->data['ks'] = nl2br($this->config->get('sberbank_transfer_ks_' . $this->config->get('config_language_id')));

		$rur_order_total = $this->currency->convert($order_info['total'], $order_info['currency_code'], 'RUB');

		$this->data['amount'] = $this->currency->format($rur_order_total, 'RUB', $order_info['currency_value'], FALSE);

		$this->data['order_id'] = $order_info['order_id'];

		$this->data['name'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];

		if (!$order_info['payment_address_2']) {
			$this->data['address'] = $order_info['payment_zone'] . ', ' . $order_info['payment_city'] . ', ' .$order_info['payment_address_1'];
		} else {
			$this->data['address'] = $order_info['payment_zone'] . ', ' . $order_info['payment_city'] . ', ' . $order_info['payment_address_1'] . ', ' . $order_info['payment_address_2'];
		}

		$this->data['postcode'] = $order_info['payment_postcode'];

		$this->template = $this->config->get('config_template') . '/template/payment/sberbank_transfer_printpay.tpl';

		if (!file_exists(DIR_TEMPLATE . $this->template)) {
			$this->template = 'default/template/payment/sberbank_transfer_printpay.tpl';
		}

		$this->response->setOutput($this->render());
	}

	public function confirm() {
		$this->language->load('payment/sberbank_transfer');

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		if ($order_info) {
			$comment  = $this->language->get('text_instruction') . "\n\n";
			$comment .= str_replace('{href}', $this->url->link('payment/sberbank_transfer/printpay', 'order_id=' . $order_info['order_id'], 'SSL'), $this->language->get('text_printpay')) . "\n\n";
			$comment .= $this->language->get('text_payment_coment');

			$this->model_checkout_order->confirm($order_info['order_id'], $this->config->get('sberbank_transfer_order_status_id'), $comment, true);
		}
	}
}
?>