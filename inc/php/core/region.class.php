<?php

/**
 * The class for a region, this class will contain all modifier functions and the content of the region
 * @author Jorin Vermeulen
 * @version 0.1 Alpha
 */
class Region {
	//The variable that will contain the content of the region
	private $content;

	/**
	 * Public constructor
	 */
	public function __CONSTRUCT() {
		$this->content = '';
	}

	/**
	 * Add content to the region
	 * @param content the content to add
	 * @return bool
	 */
	public function addContent($content) {
		if(!is_string($content)) return false;
		$this->content .= $content;
		return true;
	}

	/**
	 * Returns the content from the region
	 * @return string
	 */
	public function getContent() {
		return $this->content;
	}
}