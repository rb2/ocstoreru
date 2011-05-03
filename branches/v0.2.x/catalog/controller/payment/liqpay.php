<?php
class ControllerPaymentLiqPay extends Controller {
	protected function index() {
		$this->data['button_confirm'] = $this->language->get('button_confirm');
		$this->data['button_back'] = $this->language->get('button_back');
		
		$this->load->model('checkout/order');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		$this->data['action'] = 'https://liqpay.com/?do=clickNbuy';
		
		// Переопределяем 
		if($order_info['currency'] == 'RUB') {
			$currency = 'RUR';
		} else {
			$currency = $order_info['currency'];
		}
		
		$xml  = '<request>';
		$xml .= '	<version>1.2</version>';
		$xml .= '	<result_url>' . HTTPS_SERVER . 'index.php?route=checkout/success' . '</result_url>';
		$xml .= '	<server_url>' . HTTPS_SERVER . 'index.php?route=payment/liqpay/callback' . '</server_url>';
		$xml .= '	<merchant_id>' . $this->config->get('liqpay_merchant') . '</merchant_id>';
		$xml .= '	<order_id>' . $this->session->data['order_id'] . '</order_id>';
		$xml .= '	<amount>' . $this->currency->format($order_info['total'], $order_info['currency'], $order_info['value'], FALSE) . '</amount>';
		$xml .= '	<currency>' . $currency . '</currency>';
		$xml .= '	<description>' . $this->config->get('config_name') . '</description>';
		$xml .= '</request>';
		
		$this->data['xml'] = base64_encode($xml);
		$this->data['signature'] = base64_encode(sha1($this->config->get('liqpay_signature') . $xml . $this->config->get('liqpay_signature'), 1));
		
		if ($this->request->get['route'] != 'checkout/guest_step_3') {
			$this->data['back'] = HTTPS_SERVER . 'index.php?route=checkout/payment';
		} else {
			$this->data['back'] = HTTPS_SERVER . 'index.php?route=checkout/guest_step_2';
		}
		
		$this->id = 'payment';

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/liqpay.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/liqpay.tpl';
		} else {
			$this->template = 'default/template/payment/liqpay.tpl';
		}	
		
		$this->render();
	}
	
	public function confirm() {
      return;
   	}  
	
	private function getTag($text, $tag) {
		preg_match('#<'.$tag.'>(.*)</'.$tag.'>#i', $text, $out);
		return $out[1];
	}

	public function callback() {
		$xml = base64_decode($this->request->post['operation_xml']);
		$signature = base64_encode(sha1($this->config->get('liqpay_signature') . $xml . $this->config->get('liqpay_signature'), TRUE));
		
		
		if ( $signature != $this->request->post['signature'] ) return;
		
		if( $this->getTag($xml, 'status') != 'success')  return;
		
		$order_id = intval( $this->getTag($xml, 'order_id') );

		$this->load->model('checkout/order');
	
		$this->model_checkout_order->confirm($order_id, $this->config->get('config_order_status_id'));			
		
	}
	
}
?>