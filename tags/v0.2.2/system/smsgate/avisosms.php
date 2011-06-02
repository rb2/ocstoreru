<?php
final class avisosms {
	
	private $url = 'http://api.avisosms.ru/sms/json/';
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
  	
	private function GetPageByUrl($headers, $post_body) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this -> url); // урл страницы
		curl_setopt($ch, CURLOPT_FAILONERROR, 1); //  завершать при ошибке > 300
		curl_setopt($ch, CURLOPT_COOKIE, 1); // пишем куки
		curl_setopt($ch, CURLOPT_VERBOSE, 1); // показывать подробную инфу
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // отправить заголовки из массива $headers
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // вернуть результат запроса в переменную
			
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body); // передаём post-данные
			
		$result = curl_exec($ch); // получить результат в переменную
		curl_close($ch);
		return $result;
	}	
		
	public function send() {
		
		$http_body = array(
			'username' => $this->username,
			'password' => $this->password,
			'request_type' => 'send_message',
			'destination_address' => $this->to,
			'message' => $this->message,
			'source_address' => $this->from,
			'flash' => $this->flash	
		);

		
		$http_body = json_encode($http_body);
		$headers[] = 'Content-Type: text/xml; charset=utf-8';
		$headers[] = 'Content-Length: ' . strlen($http_body);
		$server_answer = $this->GetPageByUrl($headers, $http_body);
		$server_answer = (array)json_decode($server_answer);
		return $server_answer;
	}
		
}
?>