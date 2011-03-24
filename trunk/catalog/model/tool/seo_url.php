<?php
class ModelToolSeoUrl extends Model {
	public function rewrite($link) {
		if ($this->config->get('config_seo_url')) {
			$url_data = parse_url(str_replace('&amp;', '&', $link));

			$url = '';

			$data = array();

			parse_str($url_data['query'], $data);

			// FIX: дубли станиц
			//
			//if(isset($data['product_id'])) {
			//	$category_id = $this->getCategories($data['product_id']);
			//	$data['path'] = $this->genPath($category_id);
			//}

			foreach ($data as $key => $value) {
				if (($key == 'product_id') || ($key == 'manufacturer_id') || ($key == 'information_id')) {
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "'");

					if ($query->num_rows) {
						$url .= '/' . $query->row['keyword'];

						unset($data[$key]);
					}
				} elseif ($key == 'path') {
					$categories = explode('_', $value);

					foreach ($categories as $category) {
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = 'category_id=" . (int)$category . "'");

						if ($query->num_rows) {
							$url .= '/' . $query->row['keyword'];
						} else {
							return $link;
						}
					}

					unset($data[$key]);
				}
			}

			if ($url) {
				unset($data['route']);

				$query = '';

				if ($data) {
					foreach ($data as $key => $value) {
						$query .= '&' . $key . '=' . $value;
					}

					if ($query) {
						$query = '?' . trim($query, '&');
					}
				}

				if( $this->config->get('config_seo_url_replace') ) {
					$url = str_replace(' ', '_', $url);
				}

				$seo_url_postfix = '';
				if ($this->config->get('config_seo_url_auto') == '1')
				{
				    $seo_url_postfix = '/';
				};

				return $url_data['scheme'] . '://' . $url_data['host'] . (isset($url_data['port']) ? ':' . $url_data['port'] : '') . str_replace('/index.php', '', $url_data['path']) . $url . $seo_url_postfix . $query;
			} else {
				return $link;
			}
		} else {
			return $link;
		}
	}

	private function genPath($category_id, $path = null) {

		if( ! $category_id ) {
			return $path;
		}

		$path = ($path) ? $category_id . '_' . $path : $category_id;

		//  Получаем родителя
		$query = $this->db->query('SELECT parent_id FROM ' . DB_PREFIX . 'category WHERE category_id = ' . (int)$category_id);

		$parent_id = (int)$query->row['parent_id'];

		return $this->genPath($parent_id, $path);

	}

	private function getCategories($product_id) {
		$query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		return (int)$query->row['category_id'];
	}

}
?>