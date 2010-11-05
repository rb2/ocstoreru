<?php
class ControllerModuleBlinks extends Controller {
	protected function index() {
		$this->language->load('module/blinks');

      	$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['links'] = array();

		$results = explode("\r", trim( html_entity_decode($this->config->get('blinks_links'),  ENT_QUOTES, 'UTF-8')));

		foreach ($results as $result) {
		
			$this->data['links'][] = array(
				'alink'	=> trim($result),	
			);
		}

		$this->id = 'blinks';

		
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/blinks.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/blinks.tpl';
		} else {
			$this->template = 'default/template/module/blinks.tpl';
		}
		

		$this->render();
	}
}
?>