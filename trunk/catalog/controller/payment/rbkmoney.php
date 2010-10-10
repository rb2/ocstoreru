<?php
class ControllerPaymentRbkmoney extends Controller {
	protected function index() {
    	$this->data['button_confirm'] = $this->language->get('button_confirm');
		$this->data['button_back'] = $this->language->get('button_back');

		
		$this->data['action'] = 'https://rbkmoney.ru/acceptpurchase.aspx';
	
		$this->load->model('checkout/order');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		$products = '';
		
		foreach ($this->cart->getProducts() as $product) {
    		$products .= $product['quantity'] . ' x ' . $product['name'] . ', ';
    	}		

		
		//
		$this->data['eshopId'] = $this->config->get('rbkmoney_eshopid');
		$this->data['orderId'] = $this->session->data['order_id'];
		
		$this->data['serviceName'] = $products;
		$this->data['recipientAmount'] = $this->currency->format($order_info['total'], $order_info['currency'], $order_info['value'], FALSE);
		
		$this->data['recipientCurrency'] = $order_info['currency'];
		$this->data['successUrl'] = HTTP_SERVER . 'index.php?route=checkout/success';
		$this->data['failUrl'] = HTTP_SERVER . 'index.php?route=checkout/payment';
		
				
		if ($this->request->get['route'] != 'checkout/guest_step_3') {
			$this->data['cancel_return'] = HTTP_SERVER . 'index.php?route=checkout/payment';
		} else {
			$this->data['cancel_return'] = HTTP_SERVER . 'index.php?route=checkout/guest_step_2';
		}
				
		if ($this->request->get['route'] != 'checkout/guest_step_3') {
			$this->data['back'] = HTTP_SERVER . 'index.php?route=checkout/payment';
		} else {
			$this->data['back'] = HTTP_SERVER . 'index.php?route=checkout/guest_step_2';
		}
		
		$this->id = 'payment';

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/rbkmoney.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/rbkmoney.tpl';
		} else {
			$this->template = 'default/template/payment/rbkmoney.tpl';
		}	
		
		$this->render();	
	}
	
	
	public function callback() {
	
		if( ! isset($this->request->post['hash']) ) { 
			echo 'ERROR';
			exit;
		}
		
		$this->load->model('checkout/order');
		
		
		$order_info = $this->model_checkout_order->getOrder($this->request->post['orderId']);
		
		if ( ! $order_info) {
			echo 'ERROR:  Нет такого заказа!';
			return 0;			
		}
		
		
		$hash = $this->config->get('rbkmoney_eshopid') . 
				'::' . $order_info['order_id'] . 
				'::' . $this->request->post['serviceName'] . 
				'::' . $this->request->post['eshopAccount'] .
				'::' . $this->request->post['recipientAmount'] .
				'::' . $this->request->post['recipientCurrency'] .
				'::' . $this->request->post['paymentStatus'] .
				'::' . $this->request->post['userName'] .
				'::' . $this->request->post['userEmail'] .
				'::' . $this->request->post['paymentData'] .
				'::' . $this->config->get('rbkmoney_secret_key');
		
		$hash = md5($hash);
		
		if($this->request->post['hash'] != $hash ) { return; }
		
		
		if( $order_info['order_status_id'] == 0) {
			$this->model_checkout_order->confirm($order_info['order_id'], $this->config->get('rbkmoney_order_status_id'), 'RBKmoney');
			return TRUE;
		}
			
		if( $order_info['order_status_id'] != $this->config->get('rbkmoney_order_status_id')) {
			$this->model_checkout_order->update($order_info['order_id'], $this->config->get('rbkmoney_order_status_id'),'RBKmoney',TRUE);			
		}

		return TRUE;
		
	}

}
?>