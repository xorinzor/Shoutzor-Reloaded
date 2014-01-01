<?php

abstract class module {
	private $info; //Contains information about the widget such as Title and Author
	private $pages;
	private $adminpages;

	/**
	 * Public Constructor
	 */
	public function __CONSTRUCT() {
		$this->info = new extensionInfo();
	}

	/**
	 * Returns information about the extension
	 * @return array the info array
	 */
	public function getInfo() {
		return $this->info;
	}

	public function setInfo(extensionInfo $info) {
		if($info != null) $this->info = $info;
		return $this;
	}

	/**
	 * Main function for the module
	 */
	public function main() {
	}
}