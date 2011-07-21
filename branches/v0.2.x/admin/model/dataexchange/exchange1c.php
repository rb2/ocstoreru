<?php
class ModelDataexchangeExchange1c extends Model {

	private $CAT = array();
	private $PROPERTIES = array();
	private $REFERENCE_VALUES = array();

	public function parseOffers() {

		$importFile = DIR_CACHE . 'exchange1c/offers.xml';

		$xml = simplexml_load_file($importFile);

		$data = array();

		foreach( $xml->ПакетПредложений->Предложения->Предложение as $offer ){

			//UUID без номера после #
			$uuid = explode("#", $offer->Ид);
			$data['id'] = $uuid[0];

			//Цена за единицу
			if($offer->Цены) $data['price'] = (float)$offer->Цены->Цена->ЦенаЗаЕдиницу;

			//Количество
			$data['quantity'] = $offer->Количество ? (int)$offer->Количество : 0 ;

			//Характеристики
			if($offer->ХарактеристикиТовара){
				//Заполняем массив с Атрибутами данными по умолчанию
				$product_option_value_description_data[1] = array('name' => '');
				$product_option_value_data[0] = array(
					'language'                => $product_option_value_description_data,
					'quantity'                => isset($data['quantity'])?$data['quantity']:0,
					'subtract'                => 1,
					// Пока записываем полную цену продукта с данной характеристикой, потом будем считать разницу цен.
					'price'                   => isset($data['price']) ? $data['price']:10 ,
					'prefix'                  => '+',
					'sort_order'              => isset($offer->ХарактеристикиТовара->Сортировка) ? (int)$offer->ХарактеристикиТовара->Сортировка : 0
				);

				//Если характеристика одна, то незачем объединять их и потому название и значения запишем как надо

				$count_options = count($offer->ХарактеристикиТовара->ХарактеристикаТовара);

				$data['product_option'][0] = array(
					//Название Атрибута
					'language'             => array( 1 => array( 'name' => ($count_options == 1 ) ? (string)$offer->ХарактеристикиТовара->ХарактеристикаТовара->Наименование : 'Варианты')),
					'product_option_value' => $product_option_value_data,
					'sort_order'           => 0
				);

				//Считываем характеристики и объединяем, если их больше 1-й
				if($count_options == 1){
					$data['product_option'][0]['product_option_value'][0]['language'][1]['name'] = (string)$offer->ХарактеристикиТовара->ХарактеристикаТовара->Значение;
				}else{
					foreach($offer->ХарактеристикиТовара->ХарактеристикаТовара as $option ){
						$data['product_option'][0]['product_option_value'][0]['language'][1]['name'].= (string)$option->Наименование. ': '. (string)$option->Значение.' ';
				}}

				//Если 1С выгружает значение СортировкаХарактеристики, то считываем его, если нет, то топаем дальше и этот код никому не мешает.
				if($offer->ХарактеристикиТовара->СортировкаХарактеристики) $data['product_option'][0]['product_option_value'][0]['sort_order'] = (int)$offer->ХарактеристикиТовара->СортировкаХарактеристики;
			}

            if($offer->СкидкиНаценки){
                $value = array();
                foreach($offer->СкидкиНаценки->СкидкаНаценка as $discount){
                    if($discount->ЗначениеУсловия){
                        $value['customer_group_id'] = 8;
                        $value['quantity'] = (int)$discount->ЗначениеУсловия;
                        $value['priority'] = (isset($discount->Приоритет)) ? (int)$discount->Приоритет : 0;
                        $value['price'] = (int)(($data['price']*(100-(float)str_replace(',','.',(string)$discount->Процент)))/100);
                        $value['date_start'] = (isset($discount->ДатаНачала)) ? (string)$discount->ДатаНачала : '2011-01-01';
                        $value['date_end'] = (string)$discount->ДатаОкончания;
                        $data['product_discount'][] = $value;
                    }else{
                        $value['customer_group_id'] = 8;
                        $value['priority'] = (isset($discount->Приоритет)) ? (int)$discount->Приоритет : 0;
                        $value['price'] = (int)(($data['price']*(100-(float)str_replace(',','.',(string)$discount->Процент)))/100);
                        $value['date_start'] = (isset($discount->ДатаНачала)) ? (string)$discount->ДатаНачала : '2011-01-01';
                        $value['date_end'] = (string)$discount->ДатаОкончания;
                        $data['product_special'][] = $value;
                    }
                    unset($value);
                }
            }
            
			$data['status'] = 1;
			$this->updateProduct($data);
			unset($data);
		}

		$this->cache->delete('product');

	}

	public function parseImport() {		

		$importFile = DIR_CACHE . 'exchange1c/import.xml';

		$xml = simplexml_load_file($importFile);
		$data = array();

		$this->load->model('dataexchange/exchange1c');

		// Группы
		if($xml->Классификатор->Группы) $this->insertCategory($xml->Классификатор->Группы->Группа);

		// Свойства
		if($xml->Классификатор->Свойства->Свойство){
			foreach($xml->Классификатор->Свойства->Свойство as $property){
				$PROPERTIES[(string)$property->Ид] = (string)$property->Наименование;
				if((string)$property->ТипыЗначений){
					if((string)$property->ТипыЗначений->ТипЗначений->Тип == 'Справочник'){
						foreach($property->ТипыЗначений->ТипЗначений->ВариантыЗначений->ВариантЗначения as $option_value){
							$REFERENCE_VALUES[(string)$option_value->Ид] = (string)$option_value->Значение;

						}
					}
				}
			}
		}

		$this->load->model('catalog/manufacturer');

		// Товары
		if($xml->Каталог->Товары->Товар){
			foreach($xml->Каталог->Товары->Товар as $product){

				$uuid = explode('#', (string)$product->Ид);
				$data['id'] = $uuid[0];
				$data['uuid'] = $uuid[0];

				$data['model'] = $product->Артикул?(string)$product->Артикул :'не задана';

				$data['name'] = $product->Наименование?(string)$product->Наименование:'не задано';
			
				if($product->Картинка){
					$data['image'] =(string)$product->Картинка[0];
					unset($product->Картинка[0]);
					foreach($product->Картинка as $image){
					  $data['product_image'][] = (string)$image;
					}
				}

				if($product->Группы) $data['category_1c_id'] = (string)$product->Группы->Ид;

				if($product->Описание) $data['description'] = (string)$product->Описание;

				if($product->Статус) $data['status'] = (string)$product->Статус;

				// Свойства продукта
				if($product->ЗначенияСвойств){
					foreach($product->ЗначенияСвойств->ЗначенияСвойства as $property){
						if(isset($PROPERTIES[(string)$property->Ид])){
							switch($PROPERTIES[(string)$property->Ид]){
								case 'Псевдоним':

									$data['keyword'] = $property->Значение?(string)$property->Значение:$REFERENCE_VALUES[(string)$property->ИдЗначения];
								break;
								case 'Производитель':
									$manufacturer_name = ($property->Значение ? str_replace("/","-", (string)$property->Значение) : str_replace("/","-", $REFERENCE_VALUES[(string)$property->ИдЗначения]));
									$query = $this->db->query("SELECT manufacturer_id FROM ". DB_PREFIX ."manufacturer WHERE name='". $manufacturer_name ."'");
									if($query->num_rows){
										$data['manufacturer_id'] = $query->row['manufacturer_id'];
									}else{
                                        $manufacturer_keyword = str_replace("&","and", $manufacturer_name);
                                        $manufacturer_keyword = str_replace("/","-", $manufacturer_keyword);
                                        $data_manufacturer = array('name' => $manufacturer_name ,'sort_order' => 0, 'keyword' => $manufacturer_keyword, 'manufacturer_store' => array( 0 => 0));
										$manufacturer_id = $this->model_catalog_manufacturer->addManufacturer($data_manufacturer);
										$data['manufacturer_id'] = $manufacturer_id;
									}
								break;
								case 'h1':
									$data['h1'] = $property->Значение?(string)$property->Значение:$REFERENCE_VALUES[(string)$property->ИдЗначения];
								break;
								case 'title':
									$data['title'] = $property->Значение?(string)$property->Значение:$REFERENCE_VALUES[(string)$property->ИдЗначения];
								break;
								case 'Сортировка':
									$data['sort_order'] = $property->Значение?(string)$property->Значение:$REFERENCE_VALUES[(string)$property->ИдЗначения];
								break;
							}
						}
					}
				}

				// Реквезиты продукта
				if($product->ЗначенияРеквизитов){
					foreach($product->ЗначенияРеквизитов->ЗначениеРеквизита as $requisite){
						switch($requisite->Наименование){
							case 'Вес':
								$data['weight'] = $requisite->Значение ? (float)$requisite->Значение : 0;
							break;
						}
					}
				}

				$this->setProduct($data);
				unset($data);
			}
		}

		unset($xml);
	}


	// Инициализируем данные для категории дабы обновлять данные, а не затирать.
	private function initCategory($category, $parent, $data = array()){
		$name = (string)$category->Наименование;
        
		$data['category_description'] = array(
				1 => array(
				'name' =>  $name,
				'meta_keywords' => (isset($data['category_description'][1]['meta_keywords'])) ? $data['category_description'][1]['meta_keywords'] : '',
				'meta_description' => (isset($data['category_description'][1]['meta_description'])) ? $data['category_description'][1]['meta_description'] : '',
				'description' => (isset($category->Описание))? (string)$category->Описание : ((isset($data['category_description'][1]['description'])) ? $data['category_description'][1]['description'] : ''),
				'title'	=> (isset($data['category_description'][1]['title'])) ? $data['category_description'][1]['title'] : '',
				'h1' => (isset($data['category_description'][1]['h1'])) ? $data['category_description'][1]['h1'] : ''
			),
		);
		$data['status'] = (isset($data['status'])) ? $data['status'] : 1;
		$data['parent_id'] = $parent;
		$data['category_store'] = (isset($data['category_store'])) ? $data['category_store'] : array(0);
		$data['keyword'] = (isset($data['keyword'])) ? $data['keyword'] : str_replace("/","-", $name);
		$data['image'] = (isset($category->Картинка))? (string)$category->Картинка :((isset($data['image'])) ? $data['image'] : '');
		$data['sort_order'] =(isset($category->Сортировка)) ? (int)$category->Сортировка : ((isset($data['sort_order'])) ? $data['sort_order'] : 0);

		return $data;
	}
	// Функция добавляет корневую категорию и всех детей
	private function insertCategory($xml, $parent = 0) {

		$this->load->model('catalog/category');

		foreach($xml as $category){

			if( isset($category->Ид) AND isset($category->Наименование) ){ 
				$id =  (string)$category->Ид;

                $data = array();

                $query = $this->db->query('SELECT * FROM `' . DB_PREFIX . 'category_to_1c` WHERE `1c_category_id` = "' . $this->db->escape($id) . '"');

				if($query->num_rows) {
					$category_id = (int)$query->row['category_id'];
                    $data = $this->model_catalog_category->getCategory($category_id);
                    $data['category_description'] = $this->model_catalog_category->getCategoryDescriptions($category_id);
                    $data = $this->initCategory($category, $parent, $data);
					$this->model_catalog_category->editCategory($category_id, $data);
				} else {
                    $data = $this->initCategory($category, $parent);
					$category_id = $this->model_catalog_category->addCategory($data);
					$this->db->query('INSERT INTO `' . DB_PREFIX . 'category_to_1c` SET category_id = ' . (int)$category_id . ', `1c_category_id` = "' . $this->db->escape($id) . '"');
				}

				$this->CAT[$id] = $category_id;

			}

			if( $category->Группы ) $this->insertCategory($category->Группы->Группа, $category_id);
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

	private function getProductWithAllData($product_id) {
		$this->load->model('catalog/product');
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		if ($query->num_rows) {
			$data = array();

			$data = $query->row;

			$data = array_merge($data, array('product_description' => $this->model_catalog_product->getProductDescriptions($product_id)));
			$data = array_merge($data, array('product_option' => $this->model_catalog_product->getProductOptions($product_id)));

			$data['product_image'] = array();

			$results = $this->model_catalog_product->getProductImages($product_id);

			foreach ($results as $result) {
				$data['product_image'][] = $result['image'];
			}

			$data = array_merge($data, array('product_discount' => $this->model_catalog_product->getProductDiscounts($product_id)));
			$data = array_merge($data, array('product_special' => $this->model_catalog_product->getProductSpecials($product_id)));
			$data = array_merge($data, array('product_download' => $this->model_catalog_product->getProductDownloads($product_id)));
			$data = array_merge($data, array('product_category' => $this->model_catalog_product->getProductCategories($product_id)));
			$data = array_merge($data, array('product_store' => $this->model_catalog_product->getProductStores($product_id)));
			$data = array_merge($data, array('product_related' => $this->model_catalog_product->getProductRelated($product_id)));
			$data = array_merge($data, array('product_tags' => $this->model_catalog_product->getProductTags($product_id)));

		}

		$query = $this->db->query('SELECT * FROM ' . DB_PREFIX . 'url_alias WHERE query LIKE "product_id='.$product_id.'"');
		if ($query->num_rows) $data['keyword'] = $query->row['keyword'];

		return $data;
	}

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

		$data['quantity'] = (isset($product['quantity'])) ? $product['quantity'] : (isset($data['quantity'])? $data['quantity']: 0);

		$data['minimum'] = (isset($product['minimum'])) ?$product['minimum'] : (isset($data['minimum'])? $data['minimum']: 1);

		$data['subtract'] = (isset($product['subtract'])) ?$product['subtract'] : (isset($data['subtract'])? $data['subtract']: 1);

		$data['sort_order'] = (isset($product['sort_order'])) ?$product['sort_order'] : (isset($data['sort_order'])? $data['sort_order']: 1);

		$data['stock_status_id'] = $this->config->get('config_stock_status_id');

		$data['price'] = (isset($product['price'])) ?$product['price'] : (isset($data['price'])? $data['price']: 0);

		$data['cost'] = (isset($product['cost'])) ?$product['cost'] : (isset($data['cost'])? $data['cost']: 0);

		$data['status'] = (isset($product['status'])) ?$product['status'] : (isset($data['status'])? $data['status']: 0);

		$data['tax_class_id'] = (isset($product['tax_class_id'])) ?$product['tax_class_id'] : (isset($data['tax_class_id'])? $data['tax_class_id']: 0);

		$data['weight'] = (isset($product['weight'])) ?$product['weight'] : (isset($data['weight'])? $data['weight']: 0);

		$data['weight_class_id'] = (isset($product['weight_class_id'])) ?$product['weight_class_id'] : (isset($data['weight_class_id'])? $data['weight_class_id']: 1);

		$data['length'] = (isset($product['length'])) ?$product['length'] : (isset($data['length'])? $data['length']: '');

		$data['width'] = (isset($product['width'])) ?$product['width'] : (isset($data['width'])? $data['width']: '');

		$data['height'] = (isset($product['height'])) ?$product['height'] : (isset($data['height'])? $data['height']: '');

		$data['length_class_id'] = (isset($product['length_class_id'])) ?$product['length_class_id'] : (isset($data['length_class_id'])? $data['length_class_id']: 1);

		if(isset($product['product_option'])){
			if(!empty($data['product_option'])){
				$data['product_option'][0]['product_option_value'][] = $product['product_option'][0]['product_option_value'][0];
			}else{
				$data['product_option'] = $product['product_option'];
			}
		}

		$data['product_discount'] = (isset($product['product_discount'])) ?$product['product_discount'] : (isset($data['product_discount'])? $data['product_discount']: array());

		$data['product_special'] = (isset($product['product_special'])) ?$product['product_special'] : (isset($data['product_special'])? $data['product_special']: array());

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
			$product['price'] = 0;
			// Удаляем атрибуты т.к. еще не придумал как их сравнивать и обновлять.
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$query->row['product_id'] . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_description WHERE product_id = '" . (int)$query->row['product_id'] . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$query->row['product_id'] . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value_description WHERE product_id = '" . (int)$query->row['product_id'] . "'");
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
		$product_old = $this->getProductWithAllData($product_id);

		// Работаем с ценой на разные варианты товаров.
		if((!empty($product['product_option'])) AND ((float)$product_old['price'] != 0)){
			$product['product_option'][0]['product_option_value'][0]['price'] = (float)$product['product_option'][0]['product_option_value'][0]['price'] - (float)$product_old['price'];
			$product['price'] = (float)$product_old['price'];
			$product['quantity'] = (int)$product['quantity'] + (int)$product_old['quantity'];
		}elseif((!empty($product['product_option'])) AND ((float)$product_old['price'] == 0)){
				$product['product_option'][0]['product_option_value'][0]['price'] = 0;
		}
		$this->load->model('catalog/product');

		$product_old = $this->initProduct($product, $product_old);

		//Редактируем продукт
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
			// Меняем тип таблицы чтоб поддерживались внешние ключи
			//$this->db->query('ALTER TABLE  `'. DB_PREFIX .'product` ENGINE = INNODB');
			// Создаем БД

			$this->db->query(
					'CREATE TABLE
						`' . DB_PREFIX . 'product_to_1c` (
							`product_id` int(11) NOT NULL,
							`1c_id` varchar(255) NOT NULL,
							KEY (`product_id`),
							KEY `1c_id` (`1c_id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8'
			);			
		}


		// 
		$query = $this->db->query('SHOW TABLES LIKE "' . DB_PREFIX . 'category_to_1c"');

		if( ! $query->num_rows ) {
			// Меняем тип таблицы чтоб поддерживались внешние ключи
			//$this->db->query('ALTER TABLE  `'. DB_PREFIX .'category` ENGINE = INNODB');
			// Создаем БД

			$this->db->query(
					'CREATE TABLE
						`' . DB_PREFIX . 'category_to_1c` (
							`category_id` int(11) NOT NULL,
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