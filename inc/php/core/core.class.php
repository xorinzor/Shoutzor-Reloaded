<?php
/**
 * class core
 * loads all required classes and contains main functions
 * @param string $var1 contains the 1st part of the URL
 * @param string $var2 contains the 2nd part of the URL
 * @param string $var3 contains the 3rd part of the URL
 * @param string $var4 contains the 4th part of the URL
 * @param array $GetVars replacement for $_GET
 * @param array $urlParts contains the parse_url() result of the URL in the browser
 * @param bool $themeEnabled enables or disables the theme (enabled by default)
 * @param mixed $header Contains the header class
 */
class core
{
	public $var1;
	public $var2;
	public $var3;
	public $var4;
	public $GetVars;
	public $urlParts;

	public $themeEnabled = TRUE;

	public $header;

	/**
	 * function __CONSTRUCT
	 * core-class constructor
	 */
	public function __CONSTRUCT()
	{
        
        
		$this->var1 = cl::g("mysql")->mres($_GET['var1']);
		$this->var2 = cl::g("mysql")->mres($_GET['var2']);
		$this->var3 = cl::g("mysql")->mres($_GET['var3']);
		$this->var4 = cl::g("mysql")->mres($_GET['var4']);

		$url = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 
		$this->urlParts = parse_url($url);
		$this->GetVars = $this->retrieveGetVars($this->urlParts['query']);
	}

	/**
	 * function retrieveGetVars
	 * this functions converts the input string into an array of GET vars
	 * @param string $string text-version of the GET (example: ?var=something&more=this)
	 * @param array $output array of GET variables
	 */
	private function retrieveGetVars($string)
	{
		$output = array();

		$string = explode("&", $string);
		foreach($string as $parts):
			$parts = explode("=", $parts);
			$output[$parts[0]] = $parts[1];
		endforeach;

		return $output;
	}
}