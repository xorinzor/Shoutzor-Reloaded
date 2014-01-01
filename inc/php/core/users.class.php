<?php

class users {
	public function getUser($id = '', $email = '') {
		if(!security::valid("NUMERIC", $id) || $id < 1) $id = '';
		if(!security::valid("EMAIL", $email)) $email = '';
		if(empty($id) && empty($email)) return false;

		$filter = (!empty($id)) ? "id=$id" : "email='$email'";

		$query = cl::g("mysql")->query("SELECT * FROM `".SQL_PREFIX."users` WHERE $filter") or debug::addLine("ERROR", "Could not get user from the database, mysql error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
		if(!$query || $query->num_rows == 0) return false;

		$user = $query->fetch_object();

		$user = (new user())->setId($user->id)
							->setName($user->name)
							->setFirstName($user->firstname)
							->setEmail($user->email)
							->setPassword($user->password)
							->setJoined($user->joined)
							->setLastActive($user->last_active)
							->setStatus($user->status);
							
		return $user;
	}

	public function getUserByLogin($email, $password) {
		if(!security::valid("EMAIL", $email) || !security::valid("PASSWORD", $password) || empty($email) || empty($password)) 
			return false;

		$query = cl::g("mysql")->query("SELECT id FROM `".SQL_PREFIX."users` WHERE email='".cl::g("mysql")->mres($email)."' AND password='$password'") or debug::addLine("FATAL", "Could not select user from DB, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);

		if($query->num_rows > 0):
			$user = $query->fetch_object();
			return $this->getUser($user->id);
		endif;

		return false;
	}

	public function addUser(user $user) {
		if(!security::valid("NAME", $user->getFirstName())):
			return array('error' => true, 'msg' => 'invalid firstname, text only!');
		elseif(!security::valid("NAME", $user->getName())):
			return array('error' => true, 'msg' => 'invalid lastname, text only!');
		elseif(!security::valid("EMAIL", $user->getEmail())):
			return array('error' => true, 'msg' => 'invalid email!');
		elseif(!security::valid("NUMERIC", $user->getStatus())):
			return array('error' => true, 'msg' => 'invalid status!');
		endif;

		$query = cl::g("mysql")->query("SELECT * FROM `".SQL_PREFIX."users` WHERE email='{$user->getEmail()}'") or debug::addLine("FATAL", "Could not select user from DB, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
		if($query->num_rows > 0) return array('error' => true, 'msg' => 'email already in use'); //user already exists

		$query = cl::g("mysql")->query("INSERT INTO `".SQL_PREFIX."users` 
											(name, firstname, email, password, joined, status) 
										VALUES 
											('{$user->getName()}', '{$user->getFirstName()}', '{$user->getEmail()}', '{$user->getPassword()}', NOW(), {$user->getStatus()})
										") or debug::addLine("FATAL", "Could not add user to DB, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);

		$user->setId($query->insert_id);
		return array('error' => false, 'msg' => 'Account created');

		$query = cl::g("mysql")->query("INSERT INTO `".SQL_PREFIX."user_roles`
											(uid, rid)
										VALUES
											({$user->getId()}, 1)
										") or debug::addLine("FATAL", "Could not assign user role to user in db, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);

	}
}