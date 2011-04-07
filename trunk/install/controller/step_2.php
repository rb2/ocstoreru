<?php
class ControllerStep2 extends Controller {
	private $error = array();
	
	public function index() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->redirect(HTTP_SERVER . 'index.php?route=step_3');
		}

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';	
		}
		
		$this->data['action'] = HTTP_SERVER . 'index.php?route=step_2';

		$this->data['config_catalog'] = DIR_OPENCART . 'config.php';
		$this->data['config_admin'] = DIR_OPENCART . 'admin/config.php';
		$this->data['config_files_cmd'] = DIR_OPENCART . '{config.php,admin/config.php}';
		
		$this->data['cache'] = DIR_SYSTEM . 'cache';
		$this->data['logs'] = DIR_SYSTEM . 'logs';
		$this->data['sessions'] = DIR_SYSTEM . 'sessions';
		$this->data['config'] = DIR_CONFIG;
		$this->data['image'] = DIR_OPENCART . 'image';
		$this->data['image_cache'] = DIR_OPENCART . 'image/cache';
		$this->data['image_data'] = DIR_OPENCART . 'image/data';
		$this->data['download'] = DIR_OPENCART . 'download';
		$this->data['config_dirs_cmd'] = DIR_SYSTEM . '{cache,logs,sessions} ' . DIR_OPENCART . '{system/config,image,image/cache,image/data,download}';

		$this->children = array(
			'header',
			'footer'
		);
		
		$this->template = 'step_2.tpl';
		
		$this->response->setOutput($this->render(TRUE));
	}
	
	private function validate() {
		if (phpversion() < '5.0') {
			$this->error['warning'] = 'Для работы OpenCart необходим PHP5 или новее!';
		}

		if (!ini_get('file_uploads')) {
			$this->error['warning'] = 'Необходимо включить file_uploads!';
		}
	
		if (ini_get('session.auto_start')) {
			$this->error['warning'] = 'OpenCart не будет работать со включенной session.auto_start!';
		}

		if (!extension_loaded('mysql')) {
			$this->error['warning'] = 'Для работы OpenCart расширение MySQL должно быть загружено!';
		}

		if (!extension_loaded('gd')) {
			$this->error['warning'] = 'Для работы OpenCart расширение GD должно быть загружено!';
		}

		if (!extension_loaded('zlib')) {
			$this->error['warning'] = 'Для работы OpenCart расширение ZLIB должно быть загружено!';
		}
	
		if (!is_writable(DIR_OPENCART . 'config.php')) {
			$this->error['warning'] = 'Для установки OpenCart файл config.php должен быть доступен для записи!';
		}
				
		if (!is_writable(DIR_OPENCART . 'admin/config.php')) {
			$this->error['warning'] = 'Для установки OpenCart файл admin/config.php должен быть доступен для записи!';
		}

		if (!is_writable(DIR_SYSTEM . 'cache')) {
			$this->error['warning'] = 'Для работы OpenCart директория Cache должна быть доступна для записи!';
		}
		
		if (!is_writable(DIR_SYSTEM . 'logs')) {
			$this->error['warning'] = 'Для работы OpenCart директория Logs должна быть доступна для записи!';
		}
		
		if (!is_writable(DIR_CONFIG)) {
			$this->error['warning'] = 'Для работы OpenCart директория Config должна быть доступна для записи!';
		}
		
		if (!is_writable(DIR_OPENCART . 'image')) {
			$this->error['warning'] = 'Для работы OpenCart директория Image должна быть доступна для записи!';
		}

		if (!is_writable(DIR_OPENCART . 'image/cache')) {
			$this->error['warning'] = 'Для работы OpenCart директория Image/cache должна быть доступна для записи!';
		}
		
		if (!is_writable(DIR_OPENCART . 'image/data')) {
			$this->error['warning'] = 'Для работы OpenCart директория Image/data должна быть доступна для записи!';
		}
		
		if (!is_writable(DIR_OPENCART . 'download')) {
			$this->error['warning'] = 'Для работы OpenCart директория Download должна быть доступна для записи!';
		}
		
    	if (!$this->error) {
      		return TRUE;
    	} else {
      		return FALSE;
    	}
	}
}
?>