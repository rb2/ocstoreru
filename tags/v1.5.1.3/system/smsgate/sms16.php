<?php
final class Sms16 extends SmsGate {

	public function send() {
		$number_sms = 1;

		$data = '<?xml version="1.0" encoding="utf-8"?>';
		$data .= '<request>';
		$data .= '<message type="sms">';
		$data .= '<sender>' . $this->truncate($this->encode($this->from)) . '</sender>';
		$data .= '<text>' . $this->encode($this->message) . '</text>';
		$data .= '<abonent phone="' . $this->truncate($this->encode($this->to)) . '" number_sms="' . $number_sms++ . '"/>';
			
		if ($this->copy) {
			$phones = explode(',', $this->copy);
			foreach ($phones as $phone) {
				$data .= '<abonent phone="' . $this->truncate($this->encode($phone)) . '" number_sms="' . $number_sms++ . '"/>';
			}
		}

		$data .= '</message>';
		$data .= '<security>';
		$data .= '<login value="' . $this->encode($this->username) . '" />';
		$data .= '<password value="' . $this->encode($this->password) . '" />';
		$data .= '</security>';
		$data .= '</request>';

		$result = $this->process($data);

		return $result;
	}

	private function process($data) {
		static $count = 0;
		if ($count++) sleep(1);

		$headers = array();
		$headers[] = 'Content-Type: text/xml; charset=utf-8';
		$headers[] = 'Content-Length: ' . strlen($data);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'http://xml.sms16.ru/xml/');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CRLF, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

		$result = curl_exec($ch);

		curl_close($ch);

		return $result;
	}

	private function encode($string) {
		$string = htmlentities($string, ENT_NOQUOTES, 'UTF-8');
		$string = str_replace("\n", '\n', $string);

		return $string;
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