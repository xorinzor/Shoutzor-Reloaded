<?php

class extensionInfo {
	private $title;
	private $author;
	private $website;
	private $version;

	public function __CONSTRUCT() {
		$this->title 	= 'Undefined';
		$this->author 	= 'Undefined';
		$this->website 	= 'Undefined';
		$this->version 	= '1.0';
	}

	public function getTitle() 		{ return $this->title; }
	public function getAuthor() 	{ return $this->author; }
	public function getWebsite() 	{ return $this->website; }
	public function getVersion() 	{ return $this->version; }

	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}

	public function setAuthor($author) {
		$this->author = $author;
		return $this;
	}

	public function setWebsite($website) {
		$this->website = $website;
		return $this;
	}

	public function setVersion($version) {
		$this->version = $version;
		return $this;
	}
}