<?php
class ControllerDataexchangeExchange1c extends Controller {
	private $error = array(); 

	public function index() {
	
		$this->load->language('dataexchange/exchange1c');

		$this->document->title = $this->language->get('heading_title');
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->load->model('setting/setting');
			
			$this->model_setting_setting->editSetting('exchange1c', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect(HTTPS_SERVER . 'index.php?route=extension/dataexchange&token=' . $this->session->data['token']);
		}

		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['text_info'] = $this->language->get('text_info');
		$this->data['text_homepage'] = $this->language->get('text_homepage');
		$this->data['text_forum'] = $this->language->get('text_forum');
		$this->data['text_donate'] = $this->language->get('text_donate');
		
		
		$this->data['entry_username'] = $this->language->get('entry_username');
		$this->data['entry_password'] = $this->language->get('entry_password');
		
		$this->data['entry_lic_type'] = $this->language->get('entry_lic_type');
		
		
		
		// --

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		
		
		$this->data['entry_status'] = $this->language->get('entry_status');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

  		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['exchange1c_username'])) {
			$this->data['error_exchange1c_username'] = $this->error['exchange1c_username'];
		} else {
			$this->data['error_exchange1c_username'] = '';
		}

 		if (isset($this->error['exchange1c_password'])) {
			$this->data['error_exchange1c_password'] = $this->error['exchange1c_password'];
		} else {
			$this->data['error_exchange1c_password'] = '';
		}
		
  		$this->document->breadcrumbs = array();

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=common/home&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=extension/dataexchange&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('text_dataexchange'),
      		'separator' => ' :: '
   		);

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=dataexchange/exchange1c&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);
				
		$this->data['action'] = HTTPS_SERVER . 'index.php?route=dataexchange/exchange1c&token=' . $this->session->data['token'];
		
		$this->data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/dataexchange&token=' . $this->session->data['token'];
		
		if (isset($this->request->post['exchange1c_username'])) {
			$this->data['exchange1c_username'] = $this->request->post['exchange1c_username'];
		} else {
			$this->data['exchange1c_username'] = $this->config->get('exchange1c_username');
		}
		
		if (isset($this->request->post['exchange1c_password'])) {
			$this->data['exchange1c_password'] = $this->request->post['alertpay_order_status_id'];
		} else {
			$this->data['exchange1c_password'] = $this->config->get('alertpay_order_status_id'); 
		} 
		
		if (isset($this->request->post['exchange1c_status'])) {
			$this->data['exchange1c_status'] = $this->request->post['exchange1c_status'];
		} else {
			$this->data['exchange1c_status'] = $this->config->get('exchange1c_status');
		}	
		
		$this->data['exchange1c_lic_type'] = 'TEST';	
				
		$this->template = 'dataexchange/exchange1c.tpl';
		$this->children = array(
			'common/header',	
			'common/footer'	
		);
		
		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/alertpay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
	
	// --- 
	public function modeCheckauth() {
	
		//  Проверяем включен или нет модуль
		if( ! $this->config->get('exchange1c_status') ) {
			echo "failure\n";
			echo "1c module OFF";
			exit;
		}
		
		// Проверяем логин и пароль на доступ
		if( ! isset($_SERVER['PHP_AUTH_USER']) OR ! isset($_SERVER['PHP_AUTH_PW']) ) {
			echo "failure\n";
			echo "no login/password";
			exit;
		}
		
		// Авторизуем
		if( $_SERVER['PHP_AUTH_USER'] != $this->config->get('exchange1c_username') ) {
			echo "failure\n";
			echo "error login";
		}
		
		if( $_SERVER['PHP_AUTH_PW'] != $this->config->get('exchange1c_password') ) {
			echo "failure\n";
			echo "error password";
			exit;
		}
		
		echo "success\n";
		echo session_name()."\n";
		echo session_id() ."\n";
	}
	
	public function modeInit() {
		
		// чистим кеш, убиваем старые данные
		$this->cleanCacheDir();
		
		// Очищает таблицы от всех товаров
		$this->db->query('TRUNCATE TABLE ' . DB_PREFIX . 'category'); 
		$this->db->query('TRUNCATE TABLE ' . DB_PREFIX . 'category_description');
		$this->db->query('TRUNCATE TABLE ' . DB_PREFIX . 'category_to_store');
		
		
		$this->db->query('TRUNCATE TABLE ' . DB_PREFIX . 'product'); 
		$this->db->query('TRUNCATE TABLE ' . DB_PREFIX . 'product_description');
		$this->db->query('TRUNCATE TABLE ' . DB_PREFIX . 'product_to_store');
		$this->db->query('TRUNCATE TABLE ' . DB_PREFIX . 'product_to_category');
					
		$limit = 1000 * 1024;
	
		echo "zip=no\n";
		echo "file_limit=".$limit."\n";
	
	}
	
	public function modeFile() {
	
		$cache = DIR_CACHE . 'exchange1c/';
		
		// Проверяем на наличие имени файла
		if( isset($this->request->get['filename']) ) {
			$uplod_file = $cache . $this->request->get['filename'];
		} else {
			echo "failure\n";
			echo "ERROR 10: No file name variable";
			return;
		}
		
		// Прочеряем XML или изображения
		if( strpos( $this->request->get['filename'], 'import_files') !== false ) {
			$cache = DIR_IMAGE;
			$uplod_file = $cache . $this->request->get['filename'];
			$this->checkUploadFileTree( dirname($this->request->get['filename']) , $cache);
		}
				
		// Получаем данные
		$DATA = file_get_contents("php://input");
		
		if($DATA !== false) 
		{
			if($fp = fopen($uplod_file, "ab")) 
			{
				$result = fwrite($fp, $DATA);
				if($result === strlen($DATA))
				{
					echo "success\n";
					
					chmod($uplod_file , 0777);
					//echo "success\n";
				}
				else
				{
					echo "failure\n";
				}
			}
			else
			{
				echo "failure\n";
				echo "Can not open file: $uplod_file\n";
				echo $cache;
			}
		}
		else
		{
			echo "failure\n";
			echo "No data file\n";
		}

	
	}
	
	public function modeImport() {
		
		$cache = DIR_CACHE . 'exchange1c/';
	
		// Проверяем на наличие имени файла
		if( isset($this->request->get['filename'])) {
			$importFile = $cache . $this->request->get['filename'];
		} else {
			echo "failure\n";
			echo "ERROR 10: No file name variable" . $this->request->get['filename'];
			return 0;
		}
		
		$this->load->model('dataexchange/exchange1c');
		
		if($this->request->get['filename'] == 'import.xml') {
			
			$this->model_dataexchange_exchange1c->parseImport();
			echo "success\n";
			
		} elseif($this->request->get['filename'] == 'offers.xml') {
			
			$this->model_dataexchange_exchange1c->parseOffers();
			echo "success\n";
			
		} else {
		
			echo "failure\n";
			echo $this->request->get['filename'];
			
		}
		
		return;
	}
	
	
	// -- Системные процедуры
	private function cleanCacheDir() {
	
		// Проверяем есть ли директория
		if( file_exists(DIR_CACHE . 'exchange1c')) {
			if(is_dir(DIR_CACHE . 'exchange1c')) { return $this->cleanDir(DIR_CACHE . 'exchange1c/'); }
			else { unlink(DIR_CACHE . 'exchange1c'); }
		}
		
		mkdir(DIR_CACHE . 'exchange1c'); 
		
		return 0;
	}
	
	private function checkUploadFileTree($path, $curDir = null) {
		
		if(!$curDir) $curDir = DIR_CACHE . 'exchange1c/';
		
		foreach( explode('/', $path) as $name) {
			
			if( ! $name ) continue;
			
			if(file_exists( $curDir . $name ) ) {
				// Есть такое поделие
				if(is_dir( $curDir . $name ) ) {
					$curDir = $curDir . $name . '/';
					continue;
				}
				
				unlink($curDir . $name);				
			} 
			
			mkdir($curDir . $name );
			
			$curDir = $curDir . $name . '/';
		}
		
	}
	
	
	private function cleanDir($root, $self = false) {
	
		$dir = dir($root);
		
		while( $file = $dir->read() ) {
			
			if($file == '.' OR $file == '..') continue;
			
			if( file_exists($root . $file)) {
				
				if(is_file($root . $file)) { unlink($root . $file); continue; }
				
				if(is_dir($root . $file)) { $this->cleanDir($root . $file . '/', true); continue; }
				
				var_dump($file);	
			} 
			
			var_dump($file);
		}
		
		if($self) {
			
			if(file_exists($root) AND is_dir($root)) { rmdir($root); return 0; }
			
			var_dump($root);
		}
		
		return 0;
	}
	
	
	
	
	
	
}
?>