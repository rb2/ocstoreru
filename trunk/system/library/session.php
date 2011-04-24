<?php
final class Session {
	public $data = array();
			
  	public function __construct() {		
		if (!session_id()) {
			ini_set('session.use_cookies', 'On');
			ini_set('session.use_trans_sid', 'Off');
		
			if (CONF_SESSION_DIR === 'opencart' && ini_get('session.save_handler') === 'files') {
				ini_set('session.save_path', DIR_SYSTEM . 'sessions');
				
				ini_set('session.gc_maxlifetime', (int)CONF_SESSION_LIFETIME * 60);
				ini_set('session.gc_probability', 1);
				ini_set('session.gc_divisor', 100);
			}
		
			session_set_cookie_params(0, '/');
			session_start();
		}
		
		$this->data =& $_SESSION;
	}
}
?>