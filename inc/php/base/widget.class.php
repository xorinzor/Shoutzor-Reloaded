<?php

abstract class widget {
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
	 * Return the widget content
	 * @return string the widget content
	 */
	public function getWidget() {
		return "<p><strong>Error:</strong> You have to override the default getWidget() function in your custom widget class!</p>";
	}
}