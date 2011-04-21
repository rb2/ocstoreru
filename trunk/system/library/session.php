<?php
final class Session {
	public $data = array();
			
  	public function __construct() {		
		if (!session_id()) {
			ini_set('session.use_cookies', 'On');
			ini_set('session.use_trans_sid', 'Off');
		
			// TODO: Добавить переменную для включения/отключения этой опции			
			ini_set('session.save_path', DIR_SYSTEM . 'sessions');
		
			session_set_cookie_params(0, '/');
			session_start();
		}
		
		$this->data =& $_SESSION;
	}
}
?>