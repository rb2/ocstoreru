<?php
final class Sms{

	private $smsgate;
	
	public function __construct($registry) {
	
		$this->config = $registry->get('config');
		
		$smsgate = $this->config->get('sms_gatename');
		
		if(!$smsgate) $smsgate = 'testsmsgate';
		
  		if(! defined('DIR_SMSGATE')) define('DIR_SMSGATE', DIR_SYSTEM . 'smsgate/');
  	
		if (file_exists(DIR_SMSGATE . $smsgate . '.php')) {
			require_once(DIR_SMSGATE . $smsgate . '.php');
		} else {
			exit('Error: Could not load database file ' . $smsgate . '!');
		}
				
		$this->smsgate = new $smsgate();
		
		$this->smsgate->setUsername($this->config->get('sms_username'));
		$this->smsgate->setPassword($this->config->get('sms_password'));
	}
	
	public function setTo($to) {
		$this->smsgate->setTo($to);
	}
	
	public function setFrom($from) {
		$this->smsgate->setFrom($from);
	}
	
	public function setText($text) {
		$this->smsgate->setText($text);
	}
	
	public function setUsername($username) {
  		$this->smsgate->setUsername($username);
  	}
  	
  	public function setPassword($password) {
  		$this->smsgate->setPassword($password);
  	}
	
	public function send() {
		$this->smsgate->send();
	}
}
?>