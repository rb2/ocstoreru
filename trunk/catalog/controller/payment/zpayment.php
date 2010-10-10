<?php
class ControllerPaymentZpayment extends Controller {
	protected function index() {
    	$this->data['button_confirm'] = $this->language->get('button_confirm');
		$this->data['button_back'] = $this->language->get('button_back');

		
    	$this->data['action'] = 'https://z-payment.ru/merchant.php';
  				
		$this->load->model('checkout/order');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
	
		$this->data['shop_id'] = $this->config->get('zpayment_shop_id');
		$this->data['order_id'] = $this->session->data['order_id'];
		$this->data['init_password'] = $this->config->get('zpayment_init_password');
		$this->data['email'] = $order_info['email'];
		$this->data['amount'] = $this->currency->format($order_info['total'], $order_info['currency'], $order_info['value'], FALSE);
		$this->data['description'] = html_entity_decode($this->config->get('config_name') . ' - №' . $this->session->data['order_id'], ENT_QUOTES, 'UTF-8');		
		
		$this->data['return'] = HTTP_SERVER . 'index.php?route=checkout/success';
		
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

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/zpayment.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/zpayment.tpl';
		} else {
			$this->template = 'default/template/payment/zpayment.tpl';
		}	
		
		$this->render();	
	}
	
	public function fail() {
	
		$this->redirect(HTTP_SERVER . 'index.php?route=checkout/payment');
		
		return TRUE;
	}
	
	public function success() {
	
		$LMI_PAYMENT_NO			= $this->request->post['LMI_PAYMENT_NO'];
		$LMI_SYS_INVS_NO		= $this->request->post['LMI_SYS_INVS_NO'];
		$LMI_SYS_TRANS_NO		= $this->request->post['LMI_SYS_TRANS_NO'];
		$LMI_SYS_TRANS_DATE		= $this->request->post['LMI_SYS_TRANS_DATE'];
		$ZP_SIGN				= $this->request->post['ZP_SIGN'];
		
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($LMI_PAYMENT_NO);
		
		if( $order_info['order_status_id'] == $this->config->get('config_order_status_id') ) { 
			$this->redirect(HTTP_SERVER . 'index.php?route=checkout/success');
			return TRUE;
		} else { 
			$this->model_checkout_order->confirm($LMI_PAYMENT_NO, $this->config->get('config_order_status_id'), 'Z-payment');
			$this->redirect(HTTP_SERVER . 'index.php?route=checkout/wait');
			return TRUE;
		}
	}
	
	
	public function callback() {
	
		if( isset($this->request->post['LMI_PREREQUEST']) ) { 
			echo 'YES';
			exit;
		}
	
		$LMI_PAYEE_PURSE 		= $this->request->post['LMI_PAYEE_PURSE']; 		// Идентификатор магазина	
		$LMI_PAYMENT_AMOUNT		= $this->request->post['LMI_PAYMENT_AMOUNT'];	// Сумма плаьежа в валюте магазина
		$LMI_PAYMENT_NO			= $this->request->post['LMI_PAYMENT_NO'];		// Номер заказа в магазине
		$LMI_MODE				= $this->request->post['LMI_MODE'];
		$LMI_SYS_INVS_NO		= $this->request->post['LMI_SYS_INVS_NO'];
		$LMI_SYS_TRANS_NO		= $this->request->post['LMI_SYS_TRANS_NO'];
		$LMI_SYS_TRANS_DATE		= $this->request->post['LMI_SYS_TRANS_DATE'];
		$LMI_PAYER_PURSE 		= $this->request->post['LMI_PAYER_PURSE'];
		$LMI_PAYER_WM 			= $this->request->post['LMI_PAYER_WM'];
		$LMI_HASH				= $this->request->post['LMI_HASH'];
		
		$merhant_key = $this->config->get('zpayment_merhant_key');
	
		// LMI_PAYMENT_NO - Номер счета 
		// LMI_MODE - всегда равно 0 
		// LMI_SYS_INVS_NO  - номер платежа в z-payment
		// LMI_SYS_TRANS_NO - Номер счечета 
		// LMI_SYS_TRANS_DATE - Дата и время платежа
		// LMI_PAYER_WM - Номер кошелька или email
	
		$crc = strtoupper(md5($LMI_PAYEE_PURSE.$LMI_PAYMENT_AMOUNT.$LMI_PAYMENT_NO.$LMI_MODE.$LMI_SYS_INVS_NO.$LMI_SYS_TRANS_NO.$LMI_SYS_TRANS_DATE.$merhant_key.$LMI_PAYER_PURSE.$LMI_PAYER_WM));
		
		if( $LMI_HASH != $crc) {
			echo 'ERROR: Ошибка проверки crc!';
			return 0;
		}
		
		$this->load->model('checkout/order');
				
		$order_info = $this->model_checkout_order->getOrder($LMI_PAYMENT_NO);
		
		if ( ! $order_info) {
			echo 'ERROR:  Нет такого заказа!';
			return 0;			
		}
		
		if( $order_info['order_status_id'] == 0) {
			$this->model_checkout_order->confirm($LMI_PAYMENT_NO, $this->config->get('zpayment_order_status_id'), 'Z-payment');
			echo 'YES';
		}
			
		if( $order_info['order_status_id'] != $this->config->get('zpayment_order_status_id')) {
			$this->model_checkout_order->update($LMI_PAYMENT_NO, $this->config->get('zpayment_order_status_id'),'Z-payment',TRUE);
			echo 'YES';			
		} else {
			echo 'YES';
		}
	}
}
?>