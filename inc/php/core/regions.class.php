<?php

include(PHP_PATH . "core/region.class.php");

/**
 * Class that contains the region objects used 
 * @author Jorin Vermeulen
 * @version 0.1 Alpha
 */
class Regions {
	//Contains all Region() instances
	private $regions;

	/**
	 * Public constructor
	 */
	public function __CONSTRUCT() {
		$this->regions = array();
	}

	/**
	 * Checks if the name for a region is valid
	 * @param name the name for the region
	 * @return bool
	 */
	private function isValidRegionName($name) {
		return (is_string($name) && !empty($name)) ? true : false;
	}

	/**
	 * checks if a region exists
	 * @param name the name of the region to check
	 * @return bool
	 */
	private function regionExists($name) {
		if(!$this->isValidRegionName($name)) return false;
		return isset($this->regions[$name]);
	}

	/**
	 * Get a region
	 * @param name the name of the region
	 * @return Region the Region() instance of the region
	 */
	public function getRegion($name) {
		if(!$this->regionExists($name)) return false;
		return $this->regions[$name];
	}

	/**
	 * Get all regions
	 * @return array An array containing the Region() instances of the regions stored using their regionname as key
	 */
	public function getRegions() {
		return $this->regions;
	}

	/**
	 * Get all regions and their contents
	 * @return array An array containing the Region contents stored under their region name as key
	 */
	public function getRegionsAndContent() {
		$result = array();

		foreach($this->regions as $name=>$region):
			$result[$name] = $region->getContent();
		endforeach;

		return $result;
	}

	/**
	 * Creates new regions
	 * @param names an array containing the names of the regions to create
	 * @return bool
	 */
	public function addRegions($names) {
		if(!is_array($names)) return false;

		foreach($names as $name):
			$this->addRegion($name);
		endforeach;

		return true;
	}

	/**
	 * Creates a new region
	 * @param name the name of the region, if its a array it will be redirected to the addRegions() function
	 * @return bool
	 */
	public function addRegion($name) {
		if(is_array($name)):
			return $this->addRegions($name);
		else:
			if(!$this->isValidRegionName($name)) return false;
			if($this->regionExists($name)) return false;

			$this->regions[$name] = new Region();
			return true;
		endif;
	}
}