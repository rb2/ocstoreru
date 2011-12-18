<?php
final class AvisoSms extends SmsGate {

	public function send() {
		$results = array();

		$data = array(
			'destination_address' => $this->truncate($this->to),
			'source_address'      => $this->truncate($this->from),
			'message'             => $this->message,
			'username'            => $this->username,
			'password'            => $this->password,
			'request_type'        => 'send_message',
			'flash'               => false
		);

		$json = json_encode($data);

		$result = $this->process($json);
		$results[] = (array)json_decode($result);

		if ($this->copy) {
			$numbers = explode(',', $this->copy);
			foreach ($numbers as $number) {
				$data['destination_address'] = $this->truncate($number);

				$json = json_encode($data);

				$result = $this->process($json);
				$results[] = (array)json_decode($result);
			}
		}

		return $results;
	}

	private function process($data) {
		$headers = array();
		$headers[] = 'Content-Type: text/xml; charset=utf-8';
		$headers[] = 'Content-Length: ' . strlen($data);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'http://api.avisosms.ru/sms/json/');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_COOKIE, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

		$result = curl_exec($ch);

		curl_close($ch);

		return $result;
	}

	private function truncate($string) {
		if (preg_match('/[^0-9]/', $string)) {
			$string = utf8_truncate($string, 11, '');
		} else {
			$string = substr($string, 0, 15);
		}

		return $string;
	}
}
?>