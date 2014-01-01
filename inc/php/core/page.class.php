<?php

/**
 * This class is used for creating instances of a page
 * @author Jorin Vermeulen
 * @version 0.1 alpha
 */

class page {
	private $id;
	private $title;
	private $url;
	private $site;
	private $content;
	private $useview;
	private $templatefile;
	private $status;
	private $protected;
	private $hidden;

	public function __CONSTRUCT() { 
		$this->id 			= 0;
		$this->title 		= '';
		$this->url 			= '';
		$this->site 		= '';
		$this->content 		= '';
		$this->useview 		= 1;
		$this->templatefile = '';
		$this->status 		= 0;
		$this->protected 	= 0;
		$this->hidden 		= 0;
	}

	/**
	 * GETTERS
	 **/
	public function getId() 				{ return $this->id; 			}
	public function getTitle() 				{ return $this->title; 			}
	public function getUrl() 				{ return $this->url; 			}
	public function getSite() 				{ return $this->site; 			}
	public function getContent() 			{ return $this->content; 		}
	public function useView() 				{ return $this->useview == 1;	}
	public function getTemplateFileName() 	{ return $this->templatefile; 	}
	public function getStatus()				{ return $this->status; 		}
	public function getProtected()			{ return $this->protected; 		}
	public function getHidden() 			{ return $this->hidden; 		}

	/**
	 * SETTERS
	 **/
	public function setId($id) {
		if(security::valid("NUMERIC", $id)) $this->id = $id;
		return $this;
	}

	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}

	public function setUrl($url) {
		if(security::valid("URL", $url)) $this->url = $url;
		return $this;
	}

	public function setSite($site) {
		if(security::valid("SITE", $site)) $this->site = $site;
		return $this;
	}

	public function setContent($content) {
		$this->content = $content;
		return $this;
	}

	public function setUseView($view) {
		$this->useview = ($view) ? 1 : 0;
		return $this;
	}

	public function setTemplateFileName($view) { 
		if(security::valid("TEMPLATE", $view)) $this->templatefile = $view; 
		return $this;
	}

	public function setStatus($status) {
		$this->status = ($status) ? 1 : 0;
		return $this;
	}

	public function setProtected($protected) {
		$this->protected = ($protected) ? 1 : 0;
		return $this;
	}

	public function setHidden($hidden) {
		$this->hidden = ($hidden) ? 1 : 0;
		return $this;
	}
}