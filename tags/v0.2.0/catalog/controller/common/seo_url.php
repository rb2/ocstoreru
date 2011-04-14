<?php
class ControllerCommonSeoUrl extends Controller {
	public function index() {
		if (isset($this->request->get['_route_'])) {
			$parts = explode('/', trim($this->request->get['_route_'], '/'));

			foreach ($parts as $part) {

				if( $this->config->get('config_seo_url_replace') ) {
					$part = str_replace('_', ' ', $part);
				}

				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $this->db->escape($part) . "'");

				if ($query->num_rows) {
					$url = explode('=', $query->row['query']);

					switch ($url[0]) {
						case 'product_id':
							$this->request->get['product_id'] = $url[1];
							break;

						case 'category_id':
							if (!isset($this->request->get['path'])) {
								$this->request->get['path'] = $url[1];
							} else {
								$this->request->get['path'] .= '_' . $url[1];
							}
							break;

						case 'manufacturer_id':
						case 'information_id':
							$this->request->get[$url[0]] = $url[1];
							break;

						default:
							break;
					}
				} else {
					$this->request->get['route'] = 'error/not_found';
					return $this->forward('error/not_found');
				}
			}

			if (isset($this->request->get['product_id'])) {
				$reference = 'index.php?route=product/product&product_id=' . $this->request->get['product_id'];
				if ($this->checkUrl($reference) === false) {
					$this->request->get['route'] = 'error/not_found';
				} else {
					$this->request->get['route'] = 'product/product';
				}
			} elseif (isset($this->request->get['path'])) {
				$reference = 'index.php?route=product/category&path=' . $this->request->get['path'];
				if ($this->checkUrl($reference) === false) {
					$this->request->get['route'] = 'error/not_found';
				} else {
					$this->request->get['route'] = 'product/category';
				}
			} elseif (isset($this->request->get['manufacturer_id'])) {
				$this->request->get['route'] = 'product/manufacturer';
			} elseif (isset($this->request->get['information_id'])) {
				$this->request->get['route'] = 'information/information';
			}

			if (isset($this->request->get['route'])) {
				return $this->forward($this->request->get['route']);
			}
		} elseif ($this->config->get('config_seo_url') && isset($this->request->get['route'])) {
			if (isset($this->request->get['product_id']) && $this->request->get['route'] == 'product/product') {
				$reference = 'index.php?route=product/product&product_id=' . $this->request->get['product_id'];
			} elseif (isset($this->request->get['path']) && $this->request->get['route'] == 'product/category') {
				$reference = 'index.php?route=product/category&path=' . $this->request->get['path'];
			}

			if (isset($reference) && $this->checkUrl($reference) === false) {
				$this->request->get['route'] = 'error/not_found';
				return $this->forward('error/not_found');
			}
		}
	}

	private function checkUrl($reference) {
		$this->load->model('tool/seo_url');

		$seo_url = $this->model_tool_seo_url->rewrite(HTTP_SERVER . $reference, 'validate');

		if ($seo_url === false) return false;

		if (isset($this->request->get['_route_'])) {
			$url = HTTP_SERVER . $this->request->get['_route_'];
		} else {
			$url = HTTP_SERVER . 'index.php?' . urldecode(http_build_query($this->request->get));
		}

		if ($url != $seo_url) {
			header($this->request->server['SERVER_PROTOCOL'] . ' 301 Moved Permanently');
			$this->redirect($seo_url);
		}

		return true;
	}
}
?>