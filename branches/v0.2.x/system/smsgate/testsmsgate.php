<?php
final class testsmsgate {
	
	private $username;
	private $password;
	private $to;
	private $from;
	private $message;
	private $flash = false;

  	public function setTo($to) {
  		$this->to = $to;
  	}
  	
  	public function setFrom($from){
  		$this->from = $from;
  	}
  	
  	public function setText($text) {
  		$this->message = $text;
  	}
  	
  	public function setUsername($username) {
  		$this->username = $username;
  	}
  	
  	public function setPassword($password) {
  		$this->password = $password;
  	}
		
	public function send() {
		
	}
		
}
?>