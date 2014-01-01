<?php

class cl {
	private static $classLoader;
	private $instances;
	private $interfaces;
	private $protected;

	private function __CONSTRUCT() {
		$this->instances = array();
		$this->protected = array();
	}

	public static function getInstance() {
		if(!self::$classLoader) {
			self::$classLoader = new cl();
		}

		return self::$classLoader;
	}

	public static function g($instanceName) {
		return self::$classLoader->get($instanceName);
	}

	public function load($instanceName, $classname, $filepath = '', $parameters = array(), $protected = true) {
		if(!is_string($instanceName) || empty($instanceName)) return false;
		if(!is_string($classname) || empty($classname)) return false;
		if(!is_string($filepath)) return false;
		if(!is_array($parameters)) return false;

		//Check if the class instance can be changed
		if(isset($this->protected[$instanceName]) && $this->protected[$instanceName] === true) return false;

		//If no filepath is-set, use the default classes directory
		if(empty($filepath)) $filepath = BASECLASS_PATH . $instanceName . ".class.php";

		if(file_exists($filepath)):
			require_once($filepath);
		else:
			debug::addLine("FATAL", "[CLassLoader] file '$filepath' does not exist", __FILE__, __LINE__);
		endif;

		try {
			$class = new ReflectionClass($classname);

			if($class->getConstructor() == null):
				$this->instances[$instanceName] = $class->newInstance();
				if(count($parameters) > 0):
					debug::addLine("ERROR", "[CLassLoader] class '".$classname."' does not contain a constructor but constructor-parameters were passed", __FILE__, __LINE__);
				endif;
			else:
				$this->instances[$instanceName] = $class->newInstanceArgs($parameters);
			endif;

			$this->protected[$instanceName] = $protected;
		} catch(ReflectionException $e) {
			debug::addLine("FATAL", $e, __FILE__, __LINE__);
		}
	}

	public function loadInterface($interfaceName, $filepath = '') {
		if(!is_string($interfaceName) || empty($interfaceName)) return false;
		if(!is_string($filepath)) return false;

		//Check if the interface is already loaded
		if(isset($this->interfaces[$interfaceName])) return false;

		//If no filepath is-set, use the default interfaces directory
		if(empty($filepath)) $filepath = INTERFACE_PATH . $interfaceName . ".interface.php";

		if(file_exists($filepath)):
			require_once($filepath);
		else:
			debug::addLine("FATAL", "[CLassLoader] file '".$filepath."' does not exist", __FILE__, __LINE__);
		endif;
	}
    
    public function isLoaded($instanceName) {
        if(!is_string($instanceName) || empty($instanceName)) return false;
    	if(!isset($this->instances[$instanceName])) return false;
        
        return true;
    }

	public function get($instanceName) {
		if(!$this->isLoaded($instanceName)) return false;

		return $this->instances[$instanceName];
	}
}