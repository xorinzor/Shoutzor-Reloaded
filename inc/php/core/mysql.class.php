<?php

/**
 * Custom MySQLi wrapper class
 * @author Jorin Vermeulen
 * @version 0.1 Alpha
 */
class MySQL extends mysqli {

	private $last_result; //contains the latest result from a mysqli_query
	private $last_query; //contains the latest query executed (the (string) SQL)	
	const charset = 'utf8'; //sets the charset to be used

	/**
	 * The public constructor
	 * @param host the hostname of the MySQLi server
	 * @param user the username of the MySQLi server
	 * @param pass the password of the MySQLi server
	 * @param db the database to use from the MySQLi server
	 */
	public function __CONSTRUCT($host, $user, $pass, $db) {
		//Turn error reporting off
		mysqli_report(MYSQLI_REPORT_OFF);

		parent::__construct($host, $user, $pass, $db);
		parent::set_charset(self::charset);

		if(mysqli_connect_error()) cl::g("debug")->addLine("FATAL", "database connect Error: (".mysqli_connect_errno().") - ".mysqli_connect_error());
	}

	public function __DESTRUCT() {
		parent::close();
	}

	/**
	 * Execute a MySQLi Query
	 * @param sql The query to execute
	 */
	public function query($sql) {
		$this->last_query = $sql;
		$this->last_result = parent::query($sql);
		return ($this->last_result) ? $this->last_result : false;
	}

	/**
	 * Prepare a MySQLi Query
	 * @param sql The query to prepare
	 */
	public function prepare($sql) {
		$this->last_query = $sql;
		$this->last_result = parent::prepare($sql);
		return $this->last_result;
	}

	public function autocommit($bool) {
		parent::autocommit($bool);
	}

	public function commit() {
		parent::commit();
	}

	/**
	 * Gets the latest error that occured (if any)
	 * IMPORTANT! this error does not have to be related to the latest query, if the latest error didnt generate any errors
	 */
	public function getError() {
		return mysqli_error($this);
	}

	/**
	 * MySQLi Real_escape_string function with shortened name
	 * @param string the input string to be escaped
	 */
	public function mres($string) {
		return $this->real_escape_string($string);
	}

	/**
	 * MySQLi delete function to allow quick deletion of records from a specified table
	 * @param table the table to delete records of
	 * @param filter array of table key => value combinations to use
	 * @param use the MySQLi Real_escape_string function on the filter key/value combinations? (default: true)
	 * @param override safeguard if no valid filter is provided to prevent accidental removal of records (ONLY SET TO TRUE WHEN ABSOLUTELY NESCESARRY!)
	 */
	public function delete($table, $filter, $mres = true, $override = false) {
		if((count($filter) == 0) && ($override === false)) return false; //A filter must be provided!

		if(count($filter) > 0) {
			//Check the filters
			$filterSQL = 'WHERE ';
			foreach($filter as $filterName=>$filterValue) {
				$filterSQL .= self::mres($filtername)." = '".self::mres($filterValue)."' AND";
			}
			$filterSQL = substr($filterSQL, 0, -4);
		} else {
			$filterSQL = '';
		}

		return $this->query("DELETE FROM `".self::mres($table)."` ".$filterSQL);
	}
}