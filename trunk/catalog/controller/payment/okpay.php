<?php
class ControllerPaymentOkpay extends Controller {
	protected function index() {
		$this->data['button_confirm'] = $this->language->get('button_confirm');
		$this->data['button_back'] = $this->language->get('button_back');

		$this->data['action'] = 'https://www.okpay.com/process.html';

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$this->data['ok_receiver']       = $this->config->get('okpay_receiver');
		$this->data['ok_invoice']        = $this->session->data['order_id'];
		$this->data['ok_currency']       = $order_info['currency'];
		$this->data['ok_item_1_name']    = $this->config->get('config_name') . ' - #' . $this->session->data['order_id'];
		$this->data['ok_item_1_price']   = $this->currency->format($order_info['total'], $order_info['currency'], $order_info['value'], FALSE);
		$this->data['ok_ipn']            = HTTPS_SERVER . 'index.php?route=payment/okpay/callback';
		$this->data['ok_return_success'] = HTTPS_SERVER . 'index.php?route=checkout/success';

		if ($this->request->get['route'] != 'checkout/guest_step_3') {
			$this->data['ok_return_fail'] = HTTPS_SERVER . 'index.php?route=checkout/payment';
		} else {
			$this->data['ok_return_fail'] = HTTPS_SERVER . 'index.php?route=checkout/guest_step_2';
		}

		if ($this->request->get['route'] != 'checkout/guest_step_3') {
			$this->data['back'] = HTTPS_SERVER . 'index.php?route=checkout/payment';
		} else {
			$this->data['back'] = HTTPS_SERVER . 'index.php?route=checkout/guest_step_2';
		}

		$this->id = 'payment';

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/okpay.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/okpay.tpl';
		} else {
			$this->template = 'default/template/payment/okpay.tpl';
		}

		$this->render();
	}

	public function callback() {

		if (isset($this->request->post['ok_invoice'])) {
			$order_id = $this->request->post['ok_invoice'];
		} else {
			$order_id = 0;
		}

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($order_id);

		if ($order_info) {
			$request = 'ok_verify=true';

			foreach ($this->request->post as $key => $value) {
				$request .= '&' . $key . '=' . urlencode(stripslashes(html_entity_decode($value, ENT_QUOTES, 'UTF-8')));
			}

			if (extension_loaded('curl')) {
				$ch = curl_init('https://www.okpay.com/ipn-verify.html');

				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_TIMEOUT, 30);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

				$response = curl_exec($ch);

				if (strcmp($response, 'VERIFIED') == 0 || $this->request->post['ok_txn_status'] == 'completed') {
					$this->model_checkout_order->confirm($order_id, $this->config->get('okpay_order_status_id'), 'OKPAY');
				} else {
					$this->model_checkout_order->confirm($order_id, $this->config->get('config_order_status_id'), 'OKPAY');
				}

				curl_close($ch);
			} else {
				$header  = 'POST /ipn-verify.html HTTP/1.0' . "\r\n";
				$header  = 'Host: www.okpay.com'."\r\n";
				$header .= 'Content-Type: application/x-www-form-urlencoded' . "\r\n";
				$header .= 'Content-Length: ' . strlen(utf8_decode($request)) . "\r\n";
				$header .= 'Connection: close'  ."\r\n\r\n";

				$fp = fsockopen('www.okpay.com', 80, $errno, $errstr, 30);

				if ($fp) {
					fputs($fp, $header . $request);

					while (!feof($fp)) {
						$response = fgets($fp, 1024);

						if (strcmp($response, 'VERIFIED') == 0 || $this->request->post['ok_txn_status'] == 'completed') {
							$this->model_checkout_order->confirm($order_id, $this->config->get('okpay_order_status_id'), 'OKPAY');
						} else {
							$this->model_checkout_order->confirm($order_id, $this->config->get('config_order_status_id'), 'OKPAY');
						}
					}

					fclose($fp);
				}
			}
		}
		else // !$order_info
		{
			echo 'ERROR: Order not found!';
			return 0;
		}
	}

}
?>