<?php
class ControllerExportYml extends Controller {
	
	private $eof = "\n";

	public function index() {
		if ($this->config->get('yandex_market_status')) {
			$output  = '<?xml version="1.0" encoding="utf-8" ?>';
			$output .= '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">';
			$output .= '<yml_catalog date="' . date("Y-m-d H:m") . '">';
			$output .= '<shop>'  . "\n";
			$output .= '<name>' . $this->config->get('config_name') . '</name>';
			$output .= '<company>' . $this->config->get('config_meta_description') . '</company>';
			$output .= '<url>' . HTTP_SERVER . '</url>'. "\n";

			// Перечесляем валюту магазина
			// TODO: Добавить возможность настраивать проценты.
			$output .= '<currencies>';
			$output .= '<currency id="RUR" rate="1"/>';
			$output .= '<currency id="USD" rate="CBRF" plus="3"/>';
			$output .= '<currency id="EUR" rate="CBRF" plus="3"/>';
			$output .= '</currencies>';

			// Категории товаров
			$this->load->model('catalog/category');
			$output .= '<categories>';
			$output .= $this->getCat();
			$output .= '</categories>';

			// Товарные позиции
			$this->load->model('catalog/product');
			$this->load->model('tool/image');
			$output .= '<offers>';
			
			$products = $this->model_catalog_product->getProducts();
			foreach ($products as $product) {
				$output .= '<offer id="'.$product['product_id'].'" type="vendor.model" available="true" bid="13">' . $this->eof;
				$output .= '<url>'.(HTTP_SERVER . 'index.php?route=product/product&amp;product_id=' . $product['product_id']).'</url>';
				$output .= '<price>' . $this->tax->calculate($product['price'], $product['tax_class_id']) . '</price>';
				$output .= '<currencyId>RUR</currencyId>';

				// Определяем категорию для товара
				$categories = $this->model_catalog_product->getCategories($product['product_id']);
				$output .= '<categoryId>'.$categories[0]['category_id'].'</categoryId>';

				// Определеяме изображение
				if ($product['image']) {
					$output .= '<picture>' . $this->model_tool_image->resize($product['image'], 500, 500) . '</picture>';
				} else {
					$output .= '<picture>' . $this->model_tool_image->resize('no_image.jpg', 500, 500) . '</picture>';
				}

				$output .= '<delivery> true </delivery>';
				$output .= '<local_delivery_cost>300</local_delivery_cost>';
			//	$output .= '<typePrefix>  </typePrefix>';
				$output .= '<vendor>' . $product['manufacturer'] . '</vendor>';
			//	$output .= '<vendorCode>  </vendorCode>';
				$output .= '<model>'.$product['model'].'</model>';
				$output .= '<description>'.$product['description'].'</description>';
				$output .= '<manufacturer_warranty>true</manufacturer_warranty>';
				$output .= '<country_of_origin>Япония</country_of_origin>';
				$output .= '</offer>';
			}

			$output .= '</offers>';
			$output .= '</shop>';
			$output .= '</yml_catalog>';
			$this->response->addHeader('Content-Type: application/xml');
			$this->response->setOutput($output);
		}
	}

	// Возвращает массив категорий
	protected function getCat($pi=0) {
		$categories = $this->model_catalog_category->getCategories($pi);
		$out = '';

		foreach ($categories as $category) {
			$out .= '<category id="'.$category['category_id'].'"';
			if($pi != 0) $out .= ' parentId="'.$pi.'"';
			$out .='>'.$category['name'].'</category>';
			if($e =  $this->getCat($category['category_id'])) $out .= $e;
		}
		return $out;
	}
}
?>