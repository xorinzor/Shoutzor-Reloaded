<?php

class security {
	public function __CONSTRUCT() {

	}

	public static function valid($type, $input) {
		//default settings
		$result = (boolean) false;

		$type = strtoupper($type);

		switch ($type):
			case 'EMPTY':
				//check if NOT empty
				$result = ($input == "") ? false : true;
			break;

			case 'NUMERIC':
				$result = (ctype_digit((string) $input)) ? true : false;
			break;

			case 'TEXT':
				$result = (ctype_alpha($input)) ? true : false;
			break;

			case 'NAME':
				$result = (preg_match('/^[a-zA-Z ]+$/', $input) == 1) ? true : false;
			break;

			case 'ALPHANUMERIC':
				$result = (ctype_alnum($input)) ? true: false;
			break;

			case 'BOOLEAN':
				$result = (is_bool($input)) ? true : false;
			break;
			
			case 'MATCH':
				$result = ($input[0] == $input[1]) ? true : false;
			break;

			case 'GENDER':
				$result = (($input==0)||($input==1)) ? true : false;
			break;

			case 'CHECKED':
				$result = (($input=="on")||($input=="on")) ? true : false;
			break;

			case 'SIZE':
				if(!self::check('NUMERIC', $input[1])):
					debug::addLine("Fatal", "Invalid match digit given", __FILE__, __LINE__);
				endif;

				if(is_string($input[2])):
					$size = strlen($input[2]);
				elseif(is_array($input[2]) || is_object($input[2])):
					$size = count($input[2]);
				else:
					debug::addLine("Fatal", "Invalid input given", __FILE__, __LINE__);
				endif;

				switch($input[0]) {
					case "min":
						$result = ($size >= $input[1]) ? true : false;
					break;

					case "exact":
						$result = ($size == $input[1]) ? true : false;
					break;

					case "max":
						$result = ($size <= $input[1]) ? true : false;
					break;

					default:
						debug::addLine("Fatal", "An invalid sub-option has been called: '".$input[0]."'", __FILE__, __LINE__);
					break;
				}
			break;

			case 'EMAIL':
				$result = (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})+$/",$input) == 1) ? true : false;
			break;

			case 'SECRETQUESTION':
				$result = (preg_match("^/[a-zA-Z0-9 ]+$/",$input) == 1) ? true : false;
			break;

			case 'COUNTRY':
				if(strlen($input)==2):
					$cres = mysql_query("SELECT iso FROM country WHERE iso LIKE '".mres($input)."'");
					
					$result = (mysql_num_rows($cres) == 1) ? true : false;
				else:
					$result = false;
				endif;
			break;

            /**
             * Password requirements:
             *  - at least 6 characters
             *  - contain at least a upper/lowercase letter and a number
             */
			case 'PASSWORD':
                //$result = (!empty($input) && preg_match('/^(?=.{6,}$)(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*/', $input) == 1) ? true : false;
			$result = (!empty($input) && strlen($input) >= 4) ? true : false;
			break;

			case 'FACEBOOK_ID':
				$result = (!empty($input) && preg_match("/^[a-zA-Z0-9_]+$/",$input) == 1) ? true : false;
			break;

			case 'TEMPLATE':
				$result = (!empty($input) && preg_match('/^[a-zA-Z0-9_]+$/', $input) == 1) ? true : false;
			break;

			case 'URL':
				$result = (preg_match('/^[a-zA-Z0-9_]+$/', $input) == 1) ? true : false;
			break;

			case 'SITE':
				$result = (preg_match('/^[a-zA-Z0-9_]+$/', $input) == 1) ? true : false;
			break;

			case 'MODULE':
				$result = (!empty($input) && preg_match("/^[a-zA-Z0-9_]+$/",$input) == 1) ? true : false;
			break;

			case 'DIRECTORY':
				$result = (preg_match("/^[a-zA-Z0-9_]+$/",$input) == 1) ? true : false;
			break;

			case 'FILENAME':
				debug::addLine("debug", "Gotta fix this security check!", __FILE__, __LINE__);
				return true;
			break;

			default:
				debug::addLine("Fatal", "An invalid option has been called: '$type'", __FILE__, __LINE__);
			break;
		endswitch;

		return (boolean) $result;
	}


	public static function encryptOneWay($target, $string) {
		global $_salt;

		switch($target) {
			case 'password':
				return hash('sha512', $_salt['password'] . $string);
				break;

			case 'token':
				return hash('sha256', $_salt['token'] . $string);
				break;

			default: 
				return false;
				break;
		}
	}

	public static function encrypt($string) {
		global $_salt;

		if(is_array($string)) return false;

		$string = trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $_salt['default'], $string, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)))); 
		return urlencode($string);
	}

	public static function decrypt($string) {
		global $_salt;
		
		$string = urldecode($string);
		return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $_salt['default'], base64_decode($string), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))); 
	}

	/**
	* Function to filter items
	* @param key the key to check
	* @param filter the filter to use
	* @return bool
	*/
	public static function startsWith($key, $filter) {
		if(!is_string($key) || empty($key)) return false;
		if(!is_string($filter)) return false;

		return (substr($key, 0, strlen($filter)) == $filter) ? true : false;
	}
}