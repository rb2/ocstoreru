<?php
final class Sms48 extends SmsGate {

	public function send() {
		$results = array();

		$to   = substr($this->to, 0, 11);
		$auth = $this->username . md5($this->password);

		$data = array(
			'login'  => $this->username,
			'to'     => $to,
			'msg'    => $this->message,
			'from'   => $this->from,
			'check2' => md5($auth . $to)
		);

		$results[] = $this->process($data);

		if ($this->copy) {
			$numbers = explode(',', $this->copy);
			foreach ($numbers as $number) {
				$data['to']     = substr($number, 0, 11);
				$data['check2'] = md5($auth . $data['to']);

				$results[] = $this->process($data);
			}
		}

		return $results;
	}

	private function process($data) {
		$url = "http://sms48.ru/send_sms.php?" . http_build_query($data);

		return @file_get_contents($url);
	}
}
?>