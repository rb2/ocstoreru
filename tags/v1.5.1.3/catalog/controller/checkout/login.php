<?php  
class ControllerCheckoutLogin extends Controller { 
	public function index() {
		$this->language->load('checkout/checkout');
		
		$json = array();
		
		if ((!$this->cart->hasProducts() && (!isset($this->session->data['vouchers']) || !$this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart');
		}	
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if (isset($this->request->post['account'])) {
				$this->session->data['account'] = $this->request->post['account'];
			}
	
			if (isset($this->request->post['email']) && isset($this->request->post['password'])) {
				if ($this->customer->login($this->request->post['email'], $this->request->post['password'])) {
					unset($this->session->data['guest']);
					
					// Calculate Totals
					$total_data = array();					
					$total = 0;
					$taxes = $this->cart->getTaxes();
					
					if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {						 
						$this->load->model('setting/extension');
						
						$sort_order = array(); 
						
						$results = $this->model_setting_extension->getExtensions('total');
						
						foreach ($results as $key => $value) {
							$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
						}
						
						array_multisort($sort_order, SORT_ASC, $results);
						
						foreach ($results as $result) {
							if ($this->config->get($result['code'] . '_status')) {
								$this->load->model('total/' . $result['code']);
					
								$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
							}
						}
						
						$sort_order = array(); 
					  
						foreach ($total_data as $key => $value) {
							$sort_order[$key] = $value['sort_order'];
						}
				
						array_multisort($sort_order, SORT_ASC, $total_data);
					}
					
					$json['logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', 'SSL'), $this->customer->getFirstName(), $this->url->link('account/logout', '', 'SSL'));

					$json['total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total));					
				} else {
					$json['error']['warning'] = $this->language->get('error_login');
				}
			}
		} else {
			$this->data['text_new_customer'] = $this->language->get('text_new_customer');
			$this->data['text_returning_customer'] = $this->language->get('text_returning_customer');
			$this->data['text_checkout'] = $this->language->get('text_checkout');
			$this->data['text_register'] = $this->language->get('text_register');
			$this->data['text_guest'] = $this->language->get('text_guest');
			$this->data['text_i_am_returning_customer'] = $this->language->get('text_i_am_returning_customer');
			$this->data['text_register_account'] = $this->language->get('text_register_account');
			$this->data['text_forgotten'] = $this->language->get('text_forgotten');
	 
			$this->data['entry_email'] = $this->language->get('entry_email');
			$this->data['entry_password'] = $this->language->get('entry_password');
			
			$this->data['button_continue'] = $this->language->get('button_continue');
			$this->data['button_login'] = $this->language->get('button_login');
			
			$this->data['guest_checkout'] = ($this->config->get('config_guest_checkout') && !$this->config->get('config_customer_price') && !$this->cart->hasDownload());
			
			if (isset($this->session->data['account'])) {
				$this->data['account'] = $this->session->data['account'];
			} else {
				$this->data['account'] = 'register';
			}
			
			$this->data['forgotten'] = $this->url->link('account/forgotten', '', 'SSL');
			
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/login.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/checkout/login.tpl';
			} else {
				$this->template = 'default/template/checkout/login.tpl';
			}
					
			$json['output'] = $this->render();
		}
		
		$this->response->setOutput(json_encode($json));		
	}
}
?>