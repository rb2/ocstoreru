<?php
class ControllerCommonFooter extends Controller {   
	protected function index() {
		$this->load->language('common/footer');
		
		$version = $this->user->isLogged() ? VERSION : 'for WEB';
		$this->data['text_footer'] = sprintf($this->language->get('text_footer'), $version);
		
		$this->id       = 'footer';
		$this->template = 'common/footer.tpl';
	
    	$this->render();
  	}
}
?>