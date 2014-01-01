<?php

class theme {
	//The directory is created as follow: <basedir>/views/themes/<type>/<name>/header_<style>.tpl
	private $type; //ie: default, admin, api, etc
	private $name; //ie: the theme (directory) to use (default: "1")
	private $style; //ie: default, clean, error, etc

	public function __CONSTRUCT() {
		$this->type 	= 'default';
		$this->name 	= 1;
		$this->style 	= 'default';
	}

	public function getType() 	{ return $this->type;  }
	public function getName() 	{ return $this->name;  }
	public function getStyle() 	{ return $this->style; }

	public function setType($type) {
		if(security::valid("DIRECTORY", $type)) $this->type = $type;
		return $this;
	}

	public function setName($name) {
		if(security::valid("DIRECTORY", $name)) $this->name = $name;
		return $this;
	}

	public function setStyle($style) {
		if(security::valid("DIRECTORY", $style)) $this->style = $style;
		return $this;
	}
}