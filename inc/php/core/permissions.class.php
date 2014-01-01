<?php

class permissions {

	private $permissions;
	private $temp;

	public function __CONSTRUCT() {
		$this->fetchPermissions();
	}

	public function getPermissions() {
		return $this->permissions;
	}

	public function hasPermission($permission) {
		if(!cl::g("security")->valid("ALPHANUMERIC", $permission) || !isset($this->permissions[$permission])) return false;

		return $this->permissions[$permission] == 1;
	}

	private function fetchPermissions() {
		$permissions = array();

		//Assign all default permissions
		$query = cl::g("mysql")->query("SELECT * FROM `".SQL_PREFIX."perms`") or debug::addLine("FATAL", "Could not select permissions for user from DB, reason:".cl::g("mysql")->getError(), __FILE__, __LINE__);
		while($row = $query->fetch_assoc()):
			$this->temp[$row['id']] = array(
								'name' => $row['name'],
								'value' => $row['default_value']
							);

			$this->permissions[$row['name']] = $row['default_value'];
		endwhile;

		if(cl::g("session")->isLoggedin()):
			//Get user roles
			$query = cl::g("mysql")->query("SELECT id FROM `".SQL_PREFIX."perm_roles` WHERE id IN (SELECT rid FROM `".SQL_PREFIX."user_roles` WHERE uid='".cl::g("session")->getUser()->getId()."') AND name!='custom'");
			if($query && $query->num_rows > 0) while($row = $query->fetch_object()) $this->fetchRolePermissions($row->id);

			//if the user has custom permissions, override the other
			$query = cl::g("mysql")->query("SELECT id FROM `".SQL_PREFIX."perm_roles` WHERE id IN (SELECT rid FROM `".SQL_PREFIX."user_roles` WHERE uid='".cl::g("session")->getUser()->getId()."') AND name='custom'") or debug::addLine("FATAL", "Could not select custom permission roles for user from DB, reason:".cl::g("mysql")->getError(), __FILE__, __LINE__);
			if($query && $query->num_rows > 0):
				//User has a custom role assigned, fetch custom permissions
				$query = cl::g("mysql")->query("SELECT * FROM `".SQL_PREFIX."user_perms` WHERE uid='".cl::g("session")->getUser()->getId()."'") or debug::addLine("FATAL", "Could not select custom permissions for user from DB, reason:".cl::g("mysql")->getError(), __FILE__, __LINE__);
				while($row = $query->fetch_object()) $this->permissions[$this->temp[$row->pid]['name']] = $row->value;
			endif;
		endif;
	}

	/**
	 * Get role specific permissions and their values
	 */
	private function fetchRolePermissions($role) {
		if(!cl::g("security")->valid("NUMERIC", $role) || empty($role)) return false;
		
		//Get parent permissions before getting the current permissions (due to overriding)
		$query = cl::g("mysql")->query("SELECT prid FROM `".SQL_PREFIX."perm_roles_parent` WHERE rid=$role") or debug::addLine("FATAL", "Could not select permission parent roles for user from DB, reason:".cl::g("mysql")->getError(), __FILE__, __LINE__);
		if($query->num_rows > 0):
			while($data = $query->fetch_object()) $this->fetchRolePermissions($data->prid);
		endif;

		//Get user-role permissions, a value 1 will override value 0 (notice, custom user permissions will not be loaded here)
		$query = cl::g("mysql")->query("SELECT pid, value FROM `".SQL_PREFIX."role_perms` WHERE rid=$role") or debug::addLine("FATAL", "Could not select permission roles for user from DB, reason:".cl::g("mysql")->getError(), __FILE__, __LINE__);

		while($row = $query->fetch_object()):
            $this->permissions[$this->temp[$row->pid]['name']] = $row->value;
        endwhile;
	}
}