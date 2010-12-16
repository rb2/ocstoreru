<?php
final class Sms{

	private $smsgate;
	
	public function __construct($gate, $param = array()) {
	
			
		if(!$gate) $gate = 'testsmsgate';
		
  		if(! defined('DIR_SMSGATE')) define('DIR_SMSGATE', DIR_SYSTEM . 'smsgate/');
  	
		if (file_exists(DIR_SMSGATE . $gate . '.php')) {
			require_once(DIR_SMSGATE . $gate . '.php');
		} else {
			exit('Error: Could not load database file ' . $gate . '!');
		}
				
		$this->smsgate = new $gate();
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