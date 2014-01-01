<?php

/**
 * @author Jorin Vermeulen
 * @version 0.1 alpha
 * @copyright you are NOT allowed, under any circumstances, to use any code of this system on different websites or distribute this code!
 **/

class settings {
	private $settings;

	public function __CONSTRUCT() {
		$this->settings = array();
	}

	public function setMySQLSettings() {
		$query = cl::g("mysql")->query("SELECT * FROM `".SQL_PREFIX."settings`");

		if($query):
			while($row = $query->fetch_assoc()) $this->settings[$row['name']] = new mysqlSetting($row['name'], $row['value']);
		else:
			debug::addLine("ERROR", "Could not fetch CMS settings, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
			die("An fatal CMS error occured, please check the log file for details");
		endif;
	}

	public function filter($filter) {
		$array = array();

		foreach($this->settings as $key=>$value) {
			if(security::startsWith($key, $filter)) $array[$key] = $value;
		}

		return $array;
	}

	public function get($name) {
		if($name == "") return "";

		//We are going to throw a fatal error because a setting got requested that has not been set yet
		//However, it is possible that loading the error page would require a certain setting that does not exist anymore
		//This would in turn trigger the error again and cause an infinite loop resulting in the user receiving a "no data received" error.
		//Therefor we are going to create an empty value for the non-existing value to prevent the error from happening again
		if(!isset($this->settings[$name])):
			$this->settings[$name] = new mysqlSetting($name, '');
			debug::addLine("FATAL", "invalid setting '$name' requested", __FILE__, __LINE__);
		endif;

		return $this->settings[$name];
	}
}