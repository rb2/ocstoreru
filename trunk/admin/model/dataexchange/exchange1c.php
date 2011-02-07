<?php
class ModelDataexchangeExchange1c extends Model {

	private $CAT = array();


	public function parseOffers() {
	
		$importFile = DIR_CACHE . 'exchange1c/offers.xml';
		
		$reader = new XMLReader();
		$reader->open($importFile);
		
		$offer = new XMLReader();
		
		$data = array();
		
		while($reader->read()) {
				
			if($reader->nodeType == XMLReader::ELEMENT ) {
				
				switch($reader->name) {
					case 'Предложение':
						$offer->XML($reader->readOuterXML());
						
						while($offer->read()) {
						
							if($offer->nodeType == XMLReader::ELEMENT ) {
						
								switch($offer->name) {
									case 'Ид': 
										$data['id'] = $offer->readString();
									break;
									
									case 'Наименование':
										$data['name'] = trim($offer->readString());
									break;
									
									case 'ЦенаЗаЕдиницу': 
										$data['price'] = $offer->readString();
									break;
									
									case 'Количество': 
										$data['quantity'] = $offer->readString();
									break;									
								
								}
								
							}
						
						}
						
						$this->updateProduct($data);
						
					break;
				}
							
			}
					
		}
		
		$this->cache->delete('product');
		
	}
	
	public function parseImport() {		
		
		$importFile = DIR_CACHE . 'exchange1c/import.xml';
		
		$reader = new XMLReader();
		$reader->open($importFile);
		
		$group = new XMLReader();
		$product = new XMLReader();
		
		$data = array();
		
		$this->load->model('dataexchange/exchange1c');
				
		while($reader->read()) {
		
			if($reader->nodeType == XMLReader::ELEMENT) {
		
				switch($reader->name) {
					case 'Группы':
						// Подочернее добавление групп
						$this->inserCategory($reader->readOuterXML());
						$reader->next();
					break;
				
					case 'Товар':
				
						$product->XML($reader->readOuterXML());
						
						$data = array();
						
						//echo '<pre>';
					
						while($product->read()) {
							
							if($product->nodeType == XMLReader::ELEMENT ) {
								switch($product->name) {
									
									case 'Ид':
										$data['id'] = $product->readString();
									break;
									
									case 'Наименование':
										$data['name'] = $product->readString();
										//echo 1;
									break;
									
									// Изображение
									case 'Картинка':
										$data['image'] = $product->readString();
									break;
									
									case 'Группы':
										
										$xml = $product->readOuterXML();
										$xml = simplexml_load_string($xml);
										$data['category_1c_id'] = strval($xml->Ид);
										unset($xml);
										
										$product->next();
									break;
									
									case 'ЗначенияСвойств':
									case 'ХарактеристикиТовара':
									case 'СтавкиНалогов':
										$product->next();
									break;

									case 'ЗначенияРеквизитов':
										$product->next();
										
									break;
								
								}
							}
						
						}
						
						$this->insertProduct($data);
						
					break;
				}
			}
		}
		
		$reader->close();
	}
	
	
	// Функция добавляет корневую диреторию и всех детей
	private function inserCategory($xml, $parent = 0) {
	
		//var_dump($xml);
	
		$xml = simplexml_load_string($xml);
		
		$this->load->model('catalog/category');
		
		foreach($xml as $category){		
		
			if( isset($category->Ид) AND isset($category->Наименование) ){ 
				$id =  strval($category->Ид);
				$name = $category->Наименование;
			
				$data = array();
				$data['category_description'] = array(
					1 => array(
						'name' =>  strval($category->Наименование),
						'meta_keywords' => '',
						'meta_description' => '',
						'description' => ''
					),
				);
				$data['status'] = 1;
				$data['parent_id'] = $parent;
				$data['category_store'] = array(0);
				$data['keyword'] = '';
				$data['image'] = '';
				$data['sort_order'] = 0;
			
			//	echo 'ADD: <br/>';
			//	echo 'ID: ' . $id . '<br/>';
			//	echo 'NAME: ' . $name . '<br/>';
			//	echo 'PARENT: ' . $parent . '<br/>'; 
			
				$category_id = $this->model_catalog_category->addCategory($data);
				
				$this->CAT[$id] = $category_id;
				
			}
			
			if( $category->Группы ) $this->inserCategory($category->Группы->asXML(), $category_id);
		}
		
		unset($xml);
		
			
	}
	
	
	private function insertProduct($product) {
	
		if(!$product) return;
		
		// Описание продукта
		$data['product_description'] = array(
			1 => array(
				'name' => trim($product['name']),
				'meta_keywords' => '',
				'meta_description' => '',
				'description' => '',
			),
		);
		
		// Модель
		$data['model'] = '';
		
		// SKU
		$data['sku'] = '123';
		
		$data['location'] = '';
		
		// Магазин в который выгружаем
		$data['product_store'] = array(0);
		
		$data['keyword'] = '';
		
		$data['product_tags'] = array();
		
		
		// Изображение
		if(isset( $product['image'] ) AND  $product['image'] ) {
			$data['image'] = $product['image'];
		} else {
			$data['image'] = '';
		}
		
		$this->load->model('tool/image');
		$data['preview'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		
		$data['manufacturer_id'] = 0;
		
		$data['shipping'] = 1;
		
		$data['date_available'] = date('Y-m-d', time()-86400);
		
		$data['quantity'] = 1;
		
		$data['minimum'] = 1;
		
		$data['subtract'] = 1;
		
		$data['sort_order'] = 1;
		
		$data['stock_status_id'] = $this->config->get('config_stock_status_id');
		
		$data['price'] = 10;
		
		$data['cost'] = 10;
		
		$data['status'] = 0;
		
		$data['tax_class_id'] = 0;
		
		$data['weight'] = '';
		
		$data['weight_class_id'] = '';
		
		$data['length'] = '';
		
		$data['width'] = '';
		
		$data['height'] = '';
		
		$data['length_class_id'] = '';
		
		$data['product_options'] = array();
		
		$data['product_discounts'] = array();
		
		$data['product_specials'] = array();
		
		$data['product_download'] = array();
		
		$data['product_category'] = array( ($this->CAT[$product['category_1c_id']]?$this->CAT[$product['category_1c_id']]:0));
		
		$data['product_related'] = array();
		
		$this->load->model('catalog/product');
		
		$this->model_catalog_product->addProduct($data);
		
		//var_dump($data);
	}
	
	
	private function updateProduct($data) {
		
		$this->load->model('catalog/product');
		
		$product_id = $this->getProductIdBy1CProductName($data['name']);
		
		
		
		if($product_id) {
		
		
			
			$sql = 'UPDATE ' . DB_PREFIX . 'product SET quantity = ' . (int)$data['quantity'] . ', price = ' . (int)$data['price'] . ', cost = ' . (int)$data['price'] . ', status = 1 WHERE product_id =' . $product_id;
			
	
			
			$this->db->query($sql);
		
		}
		
		//var_dump($product_id);
		
		//getProduct
		
		
		//var_dump($data);
	}
	
	// --- Специальные функции
	private function getProductIdBy1CProductId($id) {}
	
	private function getProductIdBy1CProductName($name) {
		$sql = 'SELECT p.product_id FROM ' . DB_PREFIX . 'product p LEFT JOIN ' . DB_PREFIX . 'product_description pd ON (p.product_id = pd.product_id) WHERE pd.name LIKE "'.$this->db->escape($name).'"';
		
		$query = $this->db->query($sql);
		
		//var_dump($query);
		
		if( ! $query->num_rows) return 0;
		
		return (int)$query->row['product_id'];
	}
	
	
	
	
	
	

}
?>