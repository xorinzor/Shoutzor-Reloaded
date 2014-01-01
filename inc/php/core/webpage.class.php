<?php

/**
 * Returns part of websites to be used in template files for consistency
 */
class webpage {
	private $metatags;

	public function __CONSTRUCT() {
		//initialize metatags array
		$this->metatags = array();

		//Get metatag values from the settings
		foreach(cl::g("settings")->filter("meta_") as $key=>$value) {
			$this->setMetaTag(substr($key, 5), $value);
		}
	}

	public function setMetaTag($name, $value) {
		if(!is_string($name) || empty($name)) return false;
		if(!is_string($value)) return false;

		$this->metatags[$name] = $value;
	}

	public function getMetaTags() {
		$meta = '';
		foreach($this->metatags as $key=>$value) $meta .= "<meta name='$key' content='$value' />";
		return $meta;
	}
}