<?php
class ModelToolSeoUrl extends Model {
	public function rewrite($link, $action = '') {
		if (!$this->config->get('config_seo_url')) return $link;

		$url_query = parse_url(str_replace('&amp;', '&', $link), PHP_URL_QUERY);

		$data = array();
		parse_str($url_query, $data);

		$seo_url_postfix = ($this->config->get('config_seo_url_auto') == '1') ? '/' : '';

		if (array_key_exists('product_id', $data)) {
			$url = $this->getProductSeoUrl($data['product_id']);

			if ($this->config->get('config_seo_url_replace')) {
				$url = str_replace(' ', '_', $url);
			}

			if (strpos($url, '&') === false) $url .= $seo_url_postfix;

			return rtrim(HTTP_SERVER, '/') . str_replace('&amp;', '&', $url);
		} elseif (array_key_exists('path', $data)) {
			$parts = explode('_', $data['path']);
			$category_id = array_pop($parts);
			$path = $this->getPath($category_id);

			if ($path === false && $action == 'validate') return false;

			if ($data['path'] != $path) {
				$data['path'] = $path;
				$link = HTTP_SERVER . 'index.php?' . urldecode(http_build_query($data));
			}
		}

		$url = '';
		foreach ($data as $key => $value) {
			switch ($key)
			{
				case 'manufacturer_id':
				case 'information_id':
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "'");

					if ($query->num_rows) {
						$url .= '/' . $query->row['keyword'];

						unset($data[$key]);
					}
					break;

				case 'path':
					$seo_path = $this->getSeoPath($value);

					if (!$seo_path) return $link;

					$url .= $seo_path;

					unset($data[$key]);
					break;

				default:
					break;
			}
		}

		if (!$url) return $link;

		unset($data['route']);

		$query = sizeof($data) ? '?' . urldecode(http_build_query($data)) : '';

		if ($this->config->get('config_seo_url_replace')) {
			$url = str_replace(' ', '_', $url);
		}

		$url = str_replace('&amp;', '&', $url);

		return rtrim(HTTP_SERVER, '/') . $url . $seo_url_postfix . $query;
	}


	private function getPath($category_id) {
		$category_id = (int)$category_id;
		if ($category_id < 1) return false;

		$path = $this->cache->get('category.seo.path.' . $category_id . '.' . (int)$this->config->get('config_store_id'));

		if ($path === null) {
			$path = '';

			$max_level = 30;

			$sql = "SELECT CONCAT_WS('_'";
			for ($i = $max_level-1; $i >= 0; --$i) $sql .= ",t$i.category_id";
			$sql .= ") AS path FROM " . DB_PREFIX . "category t0";
			for ($i = 1; $i < $max_level; ++$i) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "category t$i ON (t$i.category_id = t" . ($i-1) . ".parent_id)";
			    }
			$sql .= " WHERE t0.category_id = '" . $category_id . "'";

			$query = $this->db->query($sql);

			$path = $query->num_rows ? $query->row['path'] : false;

			$this->cache->set('category.seo.path.' . $category_id . '.' . (int)$this->config->get('config_store_id'), $path);
		}

		return $path;
	}


	private function getSeoPath($path) {
		$seo_path = $this->cache->get('category.seo.' . $path . '.' . (int)$this->config->get('config_store_id'));

		if ($seo_path === null) {
			$seo_path = '';

			$categories = explode('_', $path);

			foreach ($categories as $category) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = 'category_id=" . (int)$category . "'");

				if ($query->num_rows) {
					$seo_path .= '/' . $query->row['keyword'];
				} else {
					$seo_path = false;
					break;
				}
			}

			$this->cache->set('category.seo.' . $path . '.' . (int)$this->config->get('config_store_id'), $seo_path);
		}

		return $seo_path;
	}


       private function getProductSeoUrl($product_id) {
		$product_id = (int)$product_id;
		if ($product_id < 1) return false;

		$seo_url = $this->cache->get('product.seo.' . $product_id . '.' . (int)$this->config->get('config_store_id'));

		if ($seo_url === null) {
			$seo_url = false;

			$query = $this->db->query("SELECT IFNULL(p.main_category_id, p2c.category_id) AS category_id FROM " . DB_PREFIX . "product AS p JOIN " . DB_PREFIX . "product_to_category AS p2c USING (product_id) WHERE p.product_id = '" . (int)$product_id . "' LIMIT 1");

			if ($query->num_rows) {
				$category_id = (int)$query->row['category_id'];
				if ($path = $this->getPath($category_id)) {
					$product_alias = '';
					if ($seo_path = $this->getSeoPath($path)) {
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = 'product_id=" . $product_id . "'");

						if ($query->num_rows) $product_alias = $query->row['keyword'];
					}
					if ($product_alias) {
						$seo_url = $seo_path . '/' . $product_alias;
					} else {
						$seo_url = '/index.php?route=product/product&path=' . $path . '&product_id=' . $product_id;
					}
				}
			}

			$this->cache->set('product.seo.' . $product_id . '.' . (int)$this->config->get('config_store_id'), $seo_url);
		}

		return $seo_url;
	}

}
?>