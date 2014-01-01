<?php

/**
 * @author Jorin Vermeulen
 * @version 0.1 alpha
 * @copyright you are NOT allowed, under any circumstances, to use any code of this system on different websites or distribute this code!
 **/

class user {
	private $id;
	private $name;
	private $firstname;
	private $email;
	private $password;
	private $joined;
	private $lastActive;
	private $status;

	public function __CONSTRUCT() {
		$this->id 			= 0;
		$this->name 		= '';
		$this->firstname 	= '';
		$this->email 		= '';
		$this->password 	= '';
		$this->joined 		= '';
		$this->lastActive 	= '';
		$this->status 		= '';
	}

	public function getId() 		{ return $this->id; 							}
	public function getFullName() 	{ return $this->firstname . ' ' . $this->name; 	}
	public function getName() 		{ return $this->name; 							}
	public function getFirstName() 	{ return $this->firstname; 						}
	public function getEmail() 		{ return $this->email; 							}
	public function getPassword() 	{ return $this->password; 						}
	public function getJoined() 	{ return $this->joined; 						}
	public function getLastActive() { return $this->lastActive; 					}
	public function getStatus() 	{ return $this->status; 						}

	public function setId($id) {
		if(security::valid("NUMERIC", $id)) $this->id = $id;
		return $this;
	}

	public function setName($name) {
		if(security::valid("NAME", $name)) $this->name = $name;
		return $this;
	}

	public function setFirstName($firstname) {
		if(security::valid("NAME", $firstname)) $this->firstname = $firstname;
		return $this;
	}

	public function setEmail($email) {
		if(security::valid("EMAIL", $email)) $this->email = $email;
		return $this;
	}

	public function setPassword($password) {
		$this->password = (string) $password;
		return $this;
	}

	public function setJoined($joined) {
		$this->joined = (string) $joined;
		return $this;
	}

	public function setLastActive($lastactive) {
		$this->lastActive = (string) $lastactive;
		return $this;
	}

	public function setStatus($status) {
		if(security::valid("NUMERIC", $status)) $this->status = $status;
		return $this;
	}

	public function toArray() {
		return array(
				'id' 			=> $this->getId(),
				'name' 			=> $this->getName(),
				'firstname' 	=> $this->getFirstName(),
				'email' 		=> $this->getEmail(),
				'password' 		=> $this->getPassword(),
				'joined' 		=> $this->getJoined(),
				'lastActive' 	=> $this->getLastActive(),
				'status' 		=> $this->getStatus()
			);
	}
}