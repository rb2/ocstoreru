<?php 
class ControllerCheckoutManual extends Controller {
	public function index() {
		/*
		$json = array();
		
		$this->load->library('user');
		
		$this->user = new User($this->registry);
		
		if ($this->user->isLogged()) {		
			// Settings
			$this->load->model('setting/setting');
			
			$settings = $this->model_setting_setting->getSetting('config', $this->request->post['store_id']);
			
			foreach ($settings as $key => $value) {
				$this->config->set($key, $value);
			}
			
			// Products
			if (isset($this->request->post['order_product'])) {
				foreach ($this->request->post['order_product'] as $order_product) {
					$this->cart->add($order_product['product_id'], $order_product['quantity'], $order_product['order_option']);
				}
			}
			
			$json['order_product'] = array();
			
			foreach ($this->cart->getProducts() as $product) {
				$json['order_product'][] = array(
					'name'     => $product['name'],
					'model'    => $product['model'], 
					'quantity' => $product['quantity'],
					'price'    => $product['price'],	
					'total'    => $product['total'],	
					'tax'      => $this->tax->getTax($product['total'], $product['tax'])			
				);
			}
			
			$this->load->model('setting/extension');
			
			$this->load->model('localisation/country');
		
			$this->load->model('localisation/zone');
			
			// Shipping
			$json['shipping_methods'] = array();
			
			if ($this->cart->hasShipping()) {
				$country_info = $this->model_localisation_country->getCountry($this->request->post['shipping_country_id']);
				
				if ($country_info) {
					$country = $country_info['name'];
					$iso_code_2 = $country_info['iso_code_2'];
					$iso_code_3 = $country_info['iso_code_3'];
					$address_format = $country_info['address_format'];
				} else {
					$country = '';
					$iso_code_2 = '';
					$iso_code_3 = '';	
					$address_format = '';
				}
			
				$zone_info = $this->model_localisation_zone->getZone($this->request->post['shipping_zone_id']);
				
				if ($zone_info) {
					$zone = $zone_info['name'];
					$code = $zone_info['code'];
				} else {
					$zone = '';
					$code = '';
				}					
				
				$address_data = array(
					'firstname'      => $this->request->post['shipping_firstname'],
					'lastname'       => $this->request->post['shipping_lastname'],
					'company'        => $this->request->post['shipping_company'],
					'address_1'      => $this->request->post['shipping_address_1'],
					'address_2'      => $this->request->post['shipping_address_2'],
					'postcode'       => $this->request->post['shipping_postcode'],
					'city'           => $this->request->post['shipping_city'],
					'zone_id'        => $this->request->post['shipping_zone_id'],
					'zone'           => $zone,
					'zone_code'      => $code,
					'country_id'     => $this->request->post['shipping_country_id'],
					'country'        => $country,	
					'iso_code_2'     => $iso_code_2,
					'iso_code_3'     => $iso_code_3,
					'address_format' => $address_format
				);
				
				$results = $this->model_setting_extension->getExtensions('shipping');
				
				foreach ($results as $result) {
					if ($this->config->get($result['code'] . '_status')) {
						$this->load->model('shipping/' . $result['code']);
						
						$quote = $this->{'model_shipping_' . $result['code']}->getQuote($address_data); 
			
						if ($quote) {
							$json['shipping_methods'][$result['code']] = array( 
								'title'      => $quote['title'],
								'quote'      => $quote['quote'], 
								'sort_order' => $quote['sort_order'],
								'error'      => $quote['error']
							);
						}
					}
				}
		
				$sort_order = array();
			  
				foreach ($json['shipping_methods'] as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}
		
				array_multisort($sort_order, SORT_ASC, $json['shipping_methods']);
			}
			
			if (!empty($this->request->post['shipping_method'])) {
				$shipping = explode('.', $this->request->post['shipping_method']);
				
				if (isset($shipping[0]) && isset($shipping[1]) && isset($json['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
					$this->session->data['shipping_method'] = $json['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
					$json['shipping_method'] = $json['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];				
				} else {
					$json['shipping_method'] = '';
				}
			}
			
			// Totals
			$total_data = array();					
			$total = 0;
			$taxes = $this->cart->getTaxes();
			
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
			
			$json['order_total'] = $total_data;			
			
			// Payment
			$json['payment_methods'] = array();
				
			$country_info = $this->model_localisation_country->getCountry($this->request->post['payment_country_id']);
			
			if ($country_info) {
				$country = $country_info['name'];
				$iso_code_2 = $country_info['iso_code_2'];
				$iso_code_3 = $country_info['iso_code_3'];
				$address_format = $country_info['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';	
				$address_format = '';
			}
			
			$zone_info = $this->model_localisation_zone->getZone($this->request->post['payment_zone_id']);
			
			if ($zone_info) {
				$zone = $zone_info['name'];
				$code = $zone_info['code'];
			} else {
				$zone = '';
				$code = '';
			}					
			
			$address_data = array(
				'firstname'      => $this->request->post['payment_firstname'],
				'lastname'       => $this->request->post['payment_lastname'],
				'company'        => $this->request->post['payment_company'],
				'address_1'      => $this->request->post['payment_address_1'],
				'address_2'      => $this->request->post['payment_address_2'],
				'postcode'       => $this->request->post['payment_postcode'],
				'city'           => $this->request->post['payment_city'],
				'zone_id'        => $this->request->post['payment_zone_id'],
				'zone'           => $zone,
				'zone_code'      => $code,
				'country_id'     => $this->request->post['payment_country_id'],
				'country'        => $country,	
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'address_format' => $address_format
			);
							
			$results = $this->model_setting_extension->getExtensions('payment');
	
			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					$this->load->model('payment/' . $result['code']);
					
					$method = $this->{'model_payment_' . $result['code']}->getMethod($address_data, $total); 
					
					if ($method) {
						$json['payment_methods'][$result['code']] = $method;
					}
				}
			}
						 
			$sort_order = array(); 
		  
			foreach ($json['payment_methods'] as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}
	
			array_multisort($sort_order, SORT_ASC, $json['payment_methods']);			
		}
	
		$this->response->setOutput(json_encode($json));	
		*/	
	}
}
?>