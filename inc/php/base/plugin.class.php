<?php

abstract class plugin {
	private $info; //Contains information about the widget such as Title and Author

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
	 * Main runner for the plugin
	 */
	public function main() {
		
	}
}