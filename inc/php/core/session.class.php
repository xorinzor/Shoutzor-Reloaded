<?php

/**
 * @author Jorin Vermeulen
 * @version 0.1 alpha
 * @copyright you are NOT allowed, under any circumstances, to use any code of this system on different websites or distribute this code!
 **/

/**
 * - Session ID min-length 16 characters to prevent brute-force Session matching
 * - Dont store passwords or such in the session information, store these information thingys in a database
 * - Check if SSL is enabled, if so, force SSL connections
 **/
class session
{
	private $user; 						//Contains user object
	private $connectedApiAccounts; 	//Contains ID's and types from connected social network accounts

	public function __CONSTRUCT() {
		$this->user 					= null;
		$this->connectedApiAccounts 	= array();
	}

	public function altConstructor() {
		//If the user is loggedin, get it's userid from the session table and fetch the according user instance
		$sessiondata = cl::g("sessionHandler")->getSessionData(session_id());
		if($sessiondata != false && $sessiondata['userid'] > 0) $this->forceLogin((int) $sessiondata['userid'], (bool) ($sessiondata['remember'] == 'yes'));        

		if(!$this->sessionExists()) $this->initializeSession();
	}

	public function isLoggedin() {
		if(!is_object($this->user)) return false;
		return (!$this->sessionExists()) ? false : $this->checkToken();
	}
	
	public function forceLogin($uid, $remembersession = false) {

		if(!security::valid("NUMERIC", $uid) || !security::valid("BOOLEAN", $remembersession) || empty($uid))
			return false;

		$user = cl::g("users")->getUser($uid);
		if($user === false) return false;


		$this->setUser($user);
		$this->setToken();
			
		//Remember the session or expire after 24 minutes?
		$_SESSION['remembersession'] = ($remembersession === true) ? true : false;

		return true;
	}

	public function login($email, $password, $remembersession = false) {
		if(!security::valid("EMAIL", $email) || !security::valid("PASSWORD", $password) || !security::valid("BOOLEAN", $remembersession) || empty($email) || empty($password))
			return false;
		
		$user = cl::g("users")->getUserByLogin($email, $password);
		if($user === false) return false;

		$this->setUser($user);
		$this->setToken();

		//Remember the session or expire after 24 minutes?
		$_SESSION['remembersession'] = ($remembersession === true) ? true : false;

		return true;
	}

	public function logout() {
		//Start a new session with no data
		session_regenerate_id(true);

		$this->setUser(null);
		$this->setToken();
		$this->setConnectedApiNetworks();

		return true;
	}

	public function getUserIP() {
		//Check if cloudflare is used, if so, use alternate IP header from the server
		$ip = $_SERVER['REMOTE_ADDR'];
		
		return cl::g("mysql")->mres($ip);
	}

	public function getUser() {
		return $this->user;
	}

	/**
	 * Return the connected social networks
	 **/
	public function getConnectedApiNetworks() {
		return $this->connectedApiAccounts;
	}

	/**
	 * Returns an array of sessions for the current user
	 */
	public function getUserSessions() {
		if($this->isLoggedin()):
			$query = cl::g("mysql")->query("SELECT * FROM `".SQL_PREFIX."sessions` WHERE userid='".$this->getUser()->getId()."'") or debug::addLine("error", "Could not fetch user session list, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
			if($query && $query->num_rows > 0):
				$result = array();
				while($row = $query->fetch_assoc()) {
					$result[$row['id']] = array(
							"id"            => $row['id'],
							"ip"            => $row['ip'],
							"last_active"   => $row['last_accessed']
						);
				}
				
				return $result;
			endif;
		endif;
		
		return array();
	}

	private function initializeSession() {
		if($this->sessionExists()) return false;

		$_SESSION = array(
						'token' 		=> '',
						'initialized' 	=> true
					);

		return true;
	}

	public function sessionExists() {
		if(!isset($_SESSION['initialized'])) return false;
		if($_SESSION['initialized'] === true) return true;
		return false;
	}

	private function checkToken() {
		return $_SESSION['token'] == security::encryptOneWay('token', $_SERVER['HTTP_USER_AGENT'] . $this->getUserIP());
	}

	private function setUser($user) {
		if(!($user instanceof user)) $user = null;
		$this->user = $user;

		//Update user last active
		$query = cl::g("mysql")->query("UPDATE `".SQL_PREFIX."users` SET last_active=NOW() WHERE id={$this->user->getId()} ") or debug::addLine("FATAL", "Could not update user in DB, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
		
		if($user != null) cl::g("sessionHandler")->write(session_id(), session_encode()); //Update the userID in the database
		return $this;
	}

	private function setToken() {
		$_SESSION['token'] = security::encryptOneWay('token', $_SERVER['HTTP_USER_AGENT'] . $this->getUserIP());
		return $this;
	}
	
	/**
	 * Parse data in the database and connect the set users to this account
	 */
	public function setConnectedApiNetworks() {
		//Add system related API functions
		$this->connectedApiAccounts['system'] = new basicAccount();

		if(!$this->isLoggedin()) return $this;

		//Add user related API functions
		$this->connectedApiAccounts['user'] = new userAccount($this->getUser()->getId(), $this->getUser()->getFullname());
		$this->connectedApiAccounts['shoutzor'] = new shoutzorAccount($this->getUser()->getId(), $this->getUser()->getFullname());

		return $this;
	}
}