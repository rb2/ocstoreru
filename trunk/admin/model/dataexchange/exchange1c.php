<?php
class ModelDataexchangeExchange1c extends Model {

	private $CAT = array();
	private $$PROPERTIES = array();

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
										//Берем первую часть uuid т.к. могут быть и uuid#id
										$uuid = explode("#", $product->readString());
										$data['id'] = $uuid[0];
										$data['uuid'] = $uuid[0];
									break;
									
									case 'Наименование':
										$data['name'] = $product->readString();
										//echo 1;
									break;
									
									// Изображение
									case 'Картинка':
										//Обрабатываем несколько изображений
										if (isset($data['image'])) {
											$data['product_image'][] = $product->readString();
										}
										else {
											$data['image'] = $product->readString();
										}
									break;
									
									case 'Группы':
										
										$xml = $product->readOuterXML();
										$xml = simplexml_load_string($xml);
										$data['category_1c_id'] = strval($xml->Ид);
										unset($xml);
										
										$product->next();
									break;
									
									case 'Модель':
										$data['model'] = $product->readString();
									break;
									
									case 'Описание':
										$data['description'] = $product->readString();	
									break;
									
									case 'ЗначенияСвойств':
										$xml = simplexml_load_string($product->readOuterXML());
										foreach($xml as $property ){
											if(isset($PROPERTIES[(string)$property->Ид])){
												switch($PROPERTIES[(string)$property->Ид]){
													case 'Псевдоним':
														$data['keyword'] = $property->Значение;
													break;
													case 'Производитель':
														$query = $this->db->query('SELECT manufacturer_id FROM manufacturer WHERE name="'. (string)$property->Значение .'"');
														if($query->num_rows){
															$data['manufacturer_id'] = $query->row['manufacturer_id'];
														}else{
															$data['manufacturer_id'] = 0;
														}
													break;
													case 'h1':
														$data['h1'] = $property->Значение;
													break;
													case 'title':
														$data['title'] = $property->Значение;
													break;
												}
											}
										}
										unset($xml);
										$product->next();
									break;
									case 'ХарактеристикиТовара':
									case 'СтавкиНалогов':
										$product->next();
									break;

									case 'ЗначенияРеквизитов':
										$product->next();
										
									break;
									
									case 'Статус':
										$data['status'] = $product->readString();	
									break;
								
								}
							}
						
						}
						
						
						// Добавляем/Обновляем продукт
						$this->setProduct($data);
						
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
						'description' => '',
						'title'	=> '',
						'h1' => ''
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
			
				$query = $this->db->query('SELECT * FROM `' . DB_PREFIX . 'category_to_1c` WHERE `1c_category_id` = "' . $this->db->escape($id) . '"');
				
				if($query->num_rows) {	
					$category_id = (int)$query->row['category_id'];
					$this->model_catalog_category->editCategory($category_id, $data);
				} else {
					$category_id = $this->model_catalog_category->addCategory($data);
					$this->db->query('INSERT INTO `' . DB_PREFIX . 'category_to_1c` SET category_id = ' . (int)$category_id . ', `1c_category_id` = "' . $this->db->escape($id) . '"');
				}
			
				$this->CAT[$id] = $category_id;
				
			}
			
			if( $category->Группы ) $this->inserCategory($category->Группы->asXML(), $category_id);
		}
		
		unset($xml);
		
			
	}
	
	
	/**
	*	Функция работы с продуктом
	* 	@param array $product(
	*							'id'	идентификатор передаваемый из 1С
	*						)
	*	@return array
	*/
	
	private function initProduct($product, $data = array()) {
	
		$data['product_description'] = array(
			1 => array(
				'name' => isset($product['name']) ? trim($product['name']): (isset($data['product_description'][1]['name'])? $data['product_description'][1]['name']: 'Имя не задано'),
				'meta_keywords' => isset($product['meta_keywords']) ? trim($product['meta_keywords']): (isset($data['product_description'][1]['meta_keywords'])? $data['product_description'][1]['meta_keywords']: ''),
				'meta_description' => isset($product['meta_description']) ? trim($product['meta_description']): (isset($data['product_description'][1]['meta_description'])? $data['product_description'][1]['meta_description']: ''),
				'description' => isset($product['description']) ? trim($product['description']): (isset($data['product_description'][1]['description'])? $data['product_description'][1]['description']: ''),
				'title' => isset($product['title']) ? $product['title']: (isset($data['product_description'][1]['title'])? $data['product_description'][1]['title']: ''),
				'h1' => isset($product['h1']) ? $product['h1']: (isset($data['product_description'][1]['h1'])? $data['product_description'][1]['h1']: '')
			),
		);
		
		// Модель
		$data['model'] = (isset($product['model'])) ?$product['model'] : (isset($data['model'])? $data['model']: '');
		
		// SKU
		$data['sku'] = (isset($product['sku'])) ?$product['sku'] : (isset($data['sku'])? $data['sku']: '123');
		
		$data['location'] = (isset($product['location'])) ?$product['location'] : (isset($data['location'])? $data['location']: '');
		
		// Магазин в который выгружаем
		$data['product_store'] = array(0);
		
		$data['keyword'] = (isset($product['keyword'])) ?$product['keyword'] : (isset($data['keyword'])? $data['keyword']: '');
		
		$data['product_tags'] = (isset($product['product_tags'])) ?$product['product_tags'] : (isset($data['product_tags'])? $data['product_tags']: array());
		
		// Изображение
		$data['image'] = (isset($product['image'])) ?$product['image'] : (isset($data['image'])? $data['image']: '');
		
		// Доп. изображения
		
		$data['product_image'] = (isset($product['product_image'])) ?$product['product_image'] : (isset($data['product_image'])? $data['product_image']: array());
		//$data['product_image'] = (isset($product['product_image'])) ?$product['product_image'] : array();
		//isset($data['product_image']) ? 1: array();
		$this->load->model('tool/image');
		
		$data['preview'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		
		$data['manufacturer_id'] = (isset($product['manufacturer_id'])) ?$product['manufacturer_id'] : (isset($data['manufacturer_id'])? $data['manufacturer_id']: 0);
		
		$data['shipping'] = (isset($product['shipping'])) ?$product['shipping'] : (isset($data['shipping'])? $data['shipping']: 1);
		
		$data['date_available'] = date('Y-m-d', time()-86400);
		
		$data['quantity'] = (isset($product['quantity'])) ?$product['quantity'] : (isset($data['quantity'])? $data['quantity']: 0);
		
		$data['minimum'] = (isset($product['minimum'])) ?$product['minimum'] : (isset($data['minimum'])? $data['minimum']: 1);
		
		$data['subtract'] = (isset($product['subtract'])) ?$product['subtract'] : (isset($data['subtract'])? $data['subtract']: 1);
		
		$data['sort_order'] = (isset($product['sort_order'])) ?$product['sord_order'] : (isset($data['sord_order'])? $data['sord_order']: 1);
		
		$data['stock_status_id'] = $this->config->get('config_stock_status_id');
		
		$data['price'] = (isset($product['price'])) ?$product['price'] : (isset($data['price'])? $data['price']: 0);
		
		$data['cost'] = (isset($product['cost'])) ?$product['cost'] : (isset($data['cost'])? $data['cost']: 0);
		
		$data['status'] = (isset($product['status'])) ?$product['status'] : (isset($data['status'])? $data['status']: 1);
		
		$data['tax_class_id'] = (isset($product['tax_class_id'])) ?$product['tax_class_id'] : (isset($data['tax_class_id'])? $data['tax_class_id']: 0);
		
		$data['weight'] = (isset($product['weight'])) ?$product['weight'] : (isset($data['weight'])? $data['weight']: '');
		
		$data['weight_class_id'] = (isset($product['weight_class_id'])) ?$product['weight_class_id'] : (isset($data['weight_class_id'])? $data['weight_class_id']: 1);
		
		$data['length'] = (isset($product['length'])) ?$product['length'] : (isset($data['length'])? $data['length']: '');
		
		$data['width'] = (isset($product['width'])) ?$product['width'] : (isset($data['width'])? $data['width']: '');
		
		$data['height'] = (isset($product['height'])) ?$product['height'] : (isset($data['height'])? $data['height']: '');
		
		$data['length_class_id'] = (isset($product['length_class_id'])) ?$product['length_class_id'] : (isset($data['length_class_id'])? $data['length_class_id']: 1);
		
		$data['product_options'] = (isset($product['product_options'])) ?$product['product_options'] : (isset($data['product_options'])? $data['product_options']: array());
		
		$data['product_discounts'] = (isset($product['product_discounts'])) ?$product['product_discounts'] : (isset($data['product_discounts'])? $data['product_discounts']: array());
		
		$data['product_specials'] = (isset($product['product_specials'])) ?$product['product_specials'] : (isset($data['product_specials'])? $data['product_specials']: array());
		
		$data['product_download'] = (isset($product['product_download'])) ?$product['product_download'] : (isset($data['product_download'])? $data['product_download']: array());
		
		if( isset($product['category_1c_id']) AND isset($this->CAT[$product['category_1c_id']]) ) {
			$data['product_category'] = array( (int)$this->CAT[$product['category_1c_id']] );
			$data['main_category_id'] = (int)$this->CAT[$product['category_1c_id']];
		} else {
			$data['product_category'] = isset($data['product_category']) ? $data['product_category']: array();
			$data['main_category_id'] = isset($data['main_category_id']) ? $data['main_category_id']: '';
		}
		
		$data['product_related'] = (isset($product['product_related'])) ?$product['product_related'] : (isset($data['product_related'])? $data['product_related']: array());
		
		return $data;
	}

	
	
	/**
	*	Функция работы с продуктом
	* 	@param array $product (
	*							'id' => идентификатор передаваемый из 1С,
	*							'model' => поле модель из 1С 
	*						)
	*/
	private function setProduct($product) {
	
		if(!$product) return;
		
		//Проверяем есть ли такой товар в БД
		$query = $this->db->query('SELECT * FROM `' . DB_PREFIX . 'product_to_1c` WHERE `1c_id` = "' . $this->db->escape($product['id']) . '"');
		
		if($query->num_rows) {	
			return $this->updateProduct($product, (int)$query->row['product_id']);
		} 	
		
		// Заполняем значения продукта
		$data = $this->initProduct($product);
		
		$this->load->model('catalog/product');
		
		$product_id = $this->model_catalog_product->addProduct($data);
		
		// Добавляемя линкт в дб
		$this->db->query('INSERT INTO `' .  DB_PREFIX . 'product_to_1c` SET product_id = ' . (int)$product_id . ', `1c_id` = "' . $this->db->escape($product['uuid']) . '"');		
		
		
	}
	
	
	private function updateProduct($product, $product_id = 0) {
		
		$this->load->model('catalog/product');
		
		// Проверяем что обновлять?
		if( ! $product_id ) {
			
			$query = $this->db->query('SELECT * FROM `' . DB_PREFIX . 'product_to_1c` WHERE `1c_id` = "' . $this->db->escape($product['id']) . '"');
			
			if($query->num_rows) {	
				$product_id = (int)$query->row['product_id'];
			} else {

				echo '<pre>';
				var_dump($product);
				exit;
			}
		}
		
		// Обновляем описание продукта
		$product_old = $this->model_catalog_product->getProduct($product_id);
		$results = $this->model_catalog_product->getProductImages($product_id);
		foreach ($results as $result) {
				$product_old['product_image'][] = $result['image'];
			}
		/*if(!empty($product['product_images'])){
			
			$fr = fopen('/tmp/test.txt','a');
			fwrite($fr, serialize($product['product_images']));
			fclose($fr);
		}*/
		//$fr = fopen('/tmp/test.txt','a');
		//isset($product_old['product_images'])?fwrite($fr, var_dump($product_old['product_images'])): $a=1; ;
		//fclose($fr);
		//$product_old['product_description'] = $this->model_catalog_product->getProductDescriptions($product_id);
		$product_old = $this->initProduct($product, $product_old);
		
		$this->load->model('catalog/product');
		// Редактируем продукт
		$product_id = $this->model_catalog_product->editProduct($product_id, $product_old);
		
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
	
	
	
	
	
	// Утилиты 
	public function checkDbSheme() {
	
		// 
		$query = $this->db->query('SHOW TABLES LIKE "' . DB_PREFIX . 'product_to_1c"');
		
		if( ! $query->num_rows ) {
			// Создаем БД
			
			$this->db->query(
					'CREATE TABLE 
						`' . DB_PREFIX . 'product_to_1c` ( 
							`product_id` int(10) unsigned NOT NULL,
 							`1c_id` varchar(255) NOT NULL,
 							KEY (`product_id`),
 							KEY `1c_id` (`1c_id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8'
			);			
		}


		// 
		$query = $this->db->query('SHOW TABLES LIKE "' . DB_PREFIX . 'category_to_1c"');
		
		if( ! $query->num_rows ) {
			// Создаем БД
			
			$this->db->query(
					'CREATE TABLE 
						`' . DB_PREFIX . 'category_to_1c` ( 
							`category_id` int(10) unsigned NOT NULL,
 							`1c_category_id` varchar(255) NOT NULL,
 							KEY (`category_id`),
 							KEY `1c_id` (`1c_category_id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8'
			);			
		}		
		
		return 0;
	
	}

}
?>