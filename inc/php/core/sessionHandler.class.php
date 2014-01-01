<?php
/*
	Session class by Stephen McIntyre
	http://stephenmcintyre.net
*/
class session_Handler
{
	public function __construct()
	{
		if((!$_SERVER['SSL_PROTOCOL']) && (cl::g("settings")->get("https_support")->getValue() == 1) && (cl::g("settings")->get("force_ssl")->getValue() == 1)):
			cl::g("debug")->addLine("fatal", "a https connection is required", __FILE__, __LINE__);
		endif;

		/* In a nutshell, this is a quick way to ensure your sessions are difficult to attack. There may be
		 * ways to improve this configuration but it's a good starting point, I feel.
		 *
		 * Code released in accordance with the ZAP > http://tlwsd.info/LICENSE.txt
		 *
		 * Requirements: HTTPS (get a free cert from StartSSL.com if you have no money :P)
		 * A well-configured webserver (see: Calomel.org)
		 * Access to server config is a bonus because you can just change php.ini and not have to make a bunch of runtime calls to ini_set() thus boosting performance
		 */

		if(cl::g("settings")->get("https_support")->getValue() == 1) ini_set('session.cookie_secure', true); //Tells the user's browser to not expose session cookie contents to unencrypted HTTP
		ini_set('session.cookie_path', '/');
		ini_set('session.use_only_cookies', true);
		ini_set('session.cookie_httponly', true); //Tells the user's browser to not expose session cookie contents to Javascript
        
        //Session lifetime
        ini_set('session.gc_maxlifetime', 1440); //24 minutes
        
        //How often should the Garbace Collector be run?
        ini_set('session.gc_probability', 1);   //A chance of 1 in
        ini_set('session.gc_divisor', 100);     //100 times

		switch(strtolower(PHP_OS)):
			case 'unix':
				ini_set('session.entropy_file', '/dev/urandom');
				break;
			case 'linux':
				ini_set('session.entropy_file', '/dev/urandom');
				break;
			case 'freebsd':
				ini_set('session.entropy_file', '/dev/arandom');
				break;
			case 'netbsd':
				ini_set('session.entropy_file', '/dev/arandom');
				break;
			case 'openbsd':
				ini_set('session.entropy_file', '/dev/arandom');
				break;
			default:
				ini_set('session.entropy_file', '');
				break;
		endswitch;

		ini_set('session.entropy_length', '32');
		ini_set('session.hash_function', 'sha256');
		ini_set('session.hash_bits_per_character', '6'); //Use strong pseudorandom data in the session IDs to prevent session fixation
		ini_set('session.use_trans_sid', false);
	}
 
	public function __destruct()
	{
		$this->shutdown();
	}

	public function shutdown() {
		session_write_close();
	}
 
	public function delete()
	{
		if(ini_get('session.use_cookies'))
		{
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
		}
 
		session_destroy();
	}
 
	public function open()
	{   
		return true;
	}
 
	public function close()
	{
		return true;
	}

	public function getSessionData($sid) {
		$query = cl::g("mysql")->query("SELECT * FROM `".SQL_PREFIX."sessions` WHERE (id='".cl::g("mysql")->mres($sid)."' AND last_accessed >= DATE_SUB(NOW(), INTERVAL 1440 SECOND) AND remember='no') OR (id='".cl::g("mysql")->mres($sid)."' AND last_accessed >= DATE_SUB(NOW(), INTERVAL 10 DAY) AND remember='yes') LIMIT 1") or debug::addLine("fatal", cl::g("mysql")->getError(), __FILE__, __LINE__);
		if($query->num_rows == 1):
			$data = $query->fetch_assoc();
			return $data;
		else:
			return false;
		endif;
	}
 
	public function read($sid)
	{
		$result = $this->getSessionData($sid);
		return ($result === false) ? '' : $result['data'];
	}
 
	public function write($sid, $data)
	{
        if(empty($data)) return 0; //Prevent massive amounts of empty rows

        $userid = (cl::g("session")->isLoggedin()) ? cl::g("session")->getUser()->getId() : 0;

		$query = cl::g("mysql")->query("REPLACE INTO `".SQL_PREFIX."sessions` 
                                            (
                                                `id`,
                                                `userid`,
                                                `ip`, 
                                                `data`, 
                                                `remember`, 
                                                `last_accessed`
                                            ) 
                                            VALUES 
                                            (
                                                '".cl::g("mysql")->mres($sid)."', 
                                                $userid,
                                                '".cl::g("session")->getUserIP()."', 
                                                '".cl::g("mysql")->mres($data)."', 
                                                '". (($_SESSION['remembersession']) ? "yes" : "no") ."',
                                                NOW()
                                            )
                                        ") or debug::addLine("fatal", cl::g("mysql")->getError(), __FILE__, __LINE__);
		return $query->affected_rows;
	}
 
	public function destroy($sid)
	{
		$query = cl::g("mysql")->query("DELETE FROM `".SQL_PREFIX."sessions` WHERE id='".cl::g("mysql")->mres($sid)."'") or debug::addLine("fatal", cl::g("mysql")->getError(), __FILE__, __LINE__);
		$_SESSION = array();
		return $query->affected_rows;
	}
 
	public function clean($expire)
	{
		$query = cl::g("mysql")->query("DELETE FROM `".SQL_PREFIX."sessions` WHERE (last_accessed < DATE_SUB(NOW(), INTERVAL ".$expire." SECOND) AND remember='no') OR (last_accessed < DATE_SUB(NOW(), INTERVAL 10 DAY) AND remember='yes')") or debug::addLine("fatal", cl::g("mysql")->getError(), __FILE__, __LINE__);
		return $query->affected_rows;
	}
}