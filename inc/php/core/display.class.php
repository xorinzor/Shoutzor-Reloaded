<?php
/**
 * This class handles all page display related functions
 * @author Jorin Vermeulen
 * @version 0.1 alpha
 * @copyright you are NOT allowed, under any circumstances, to use any code of this system on different websites or distribute this code!
 **/

class display {
	private $page;
	private $theme;
	private $themeEnabled;
	private $requestedUrl;
	private $requestedThemeType;

	public function __CONSTRUCT() { 
		$this->page 		= null;
		$this->theme 		= null;
		$this->themeEnabled = true;

		$this->requestedUrl = '';
		$this->requestedThemeType = '';

		return true;
	}

	public function getPage() 			{ return $this->page; }
	public function getTheme() 			{ return $this->theme; }
	public function getThemeEnabled() 	{ return $this->themeEnabled; }

	public function setPage(page $page) {
		$this->page = $page;
		return $this;
	}

	public function setTheme(theme $theme) {
		if($theme != null) $this->theme = $theme;
		return $this;
	}

	public function setThemeEnabled($enable) {
		$this->themeEnabled = ($enable === true) ? true : false;
		return $this;
	}

	public function setRequestedUrl($url) {
		$this->requestedUrl = $url;
		return $this;
	}

	public function setRequestedThemeType($type) {
		$this->requestedThemeType = $type;
		return $this;
	}

	private function getNoViewAvailablePath() {
		//system template removed, add error to log file
		if($this->getTheme()->getType() != 'cms' && file_exists(VIEW_PATH . $this->getTheme()->getType() . '/cms/noViewAvailable.tpl')):
			$tpl = VIEW_PATH . $this->getTheme()->getType() . '/cms/noViewAvailable.tpl';
		else:
			$this->setTheme(cl::g("themes")->getTheme('cms', '1'));
			$tpl = VIEW_PATH . 'cms/noViewAvailable.tpl';
		endif;

		return $tpl;
	}

	public function getRequestedUrl() {
		return $this->requestedUrl;
	}

	public function getRequestedThemeType() {
		return $this->requestedThemeType;
	}

	public function displayPage() {
		if($this->page == null) $this->setPage(cl::g("pages")->getPage('', 'cms', '404'));

		if($this->getPage()->useView()):
			//Check if view for the page exists.
			if(file_exists(VIEW_PATH . $this->getTheme()->getType(). '/' . $this->getTheme()->getName() . '/' . $this->getPage()->getTemplateFileName().'.tpl') && $this->getPage()->getSite() != 'cms' && $this->getTheme()->getType() != 'cms'):
				//Template exists
				$tpl = VIEW_PATH . $this->getTheme()->getType(). '/' . $this->getTheme()->getName() . '/' . $this->getPage()->getTemplateFileName().'.tpl';
			else:
				//Template doesn't exist or is a system template
				//Check if it's a system file and the current theme is not the system itself
				//this would explain the template not beeing found since we would have to check the "cms" subdirectory of the theme
				if($this->getPage()->getSite() == 'cms'):
					if(file_exists(VIEW_PATH . $this->getTheme()->getType() . '/cms/' . $this->getPage()->getTemplateFileName().'.tpl')):
						//use overridden template file
						$tpl = VIEW_PATH . $this->getTheme()->getType() . '/cms/' . $this->getPage()->getTemplateFileName().'.tpl';
					else:
						//Check if original system template exists
						if(file_exists(VIEW_PATH . '/cms/' . $this->getPage()->getTemplateFileName().'.tpl')):
							//use original system template
							$this->setTheme(cl::g("themes")->getTheme('cms', '1'));
							$tpl = VIEW_PATH . '/cms/' . $this->getPage()->getTemplateFileName().'.tpl';
						else:
							//system template removed, add error to log file
							$tpl = $this->getNoViewAvailablePath();
							debug::addLine("ERROR", "system template file for page '{$this->getPage()->getUrl()}' missing!", __FILE__, __LINE__);
						endif;
					endif;
				else:
					//Check if original system template exists
					if(file_exists(VIEW_PATH . $this->getTheme()->getType() . '/' . $this->getPage()->getTemplateFileName().'.tpl')):
						//use original system template
						$tpl = VIEW_PATH . $this->getTheme()->getType() . '/' . $this->getPage()->getTemplateFileName().'.tpl';
					else:
						//system template removed, add error to log file
						$tpl = $this->getNoViewAvailablePath();
						debug::addLine("ERROR", "system template file for page '{$this->getPage()->getUrl()}' missing!", __FILE__, __LINE__);
					endif;
				endif;
			endif;
		endif;

		$this->loadPageControllers(); //Load the controller files

		$body = ($this->getPage()->useView()) ? cl::g("tpl")->fetch($tpl) : $this->getPage()->getContent();

		//Check whether to include the theme or just to display the view of the page
		if($this->getThemeEnabled()):
			$header = cl::g("tpl")->fetch(THEME_PATH . $this->getTheme()->getType() . '/' . $this->getTheme()->getName() . '/header_' . $this->getTheme()->getStyle() . '.tpl');
			$footer = cl::g("tpl")->fetch(THEME_PATH . $this->getTheme()->getType() . '/' . $this->getTheme()->getName() . '/footer_' . $this->getTheme()->getStyle() . '.tpl');
		else:
			$header = '';
			$footer = '';
		endif;

		$this->sendHeaders();

		//Remove earlier output
		ob_end_clean();
		
		//Display page
		echo $header . $body . $footer;
	}

	private function loadPageControllers() {
		// Include the default CMS controller file
		include(CONTROLLER_PATH . 'cms/default.inc.php');
		//If a non-CMS page is requested, Check if a default controller file for the template exists
		if(($this->getTheme()->getType() != "cms") && file_exists(CONTROLLER_PATH . $this->getTheme()->getType().'/default.inc.php')):
			include(CONTROLLER_PATH . $this->getTheme()->getType().'/default.inc.php');
		endif;

		//Check if a default CMS controller file exists for the given page
		if(file_exists(CONTROLLER_PATH . 'cms/'.$this->getPage()->getTemplateFileName().'.inc.php')):
			include(CONTROLLER_PATH . 'cms/'.$this->getPage()->getTemplateFileName().'.inc.php');
		endif;

		//If a non-CMS page is requested, Check if a controller file exists for the given page
		if(($this->getTheme()->getType() != "cms") && file_exists(CONTROLLER_PATH . $this->getTheme()->getType().'/'.$this->getPage()->getTemplateFileName().'.inc.php')):
			include(CONTROLLER_PATH . $this->getTheme()->getType().'/'.$this->getPage()->getTemplateFileName().'.inc.php');
		endif;

		//Include default CMS Template variables
		include(CONTROLLER_PATH . 'cms/defaultTPLVariables.inc.php');
		include(CONTROLLER_PATH . 'cms/extraTPLVariables.inc.php');

		//If a non-CMS page is requested, Include controller file to define Template variables from custom system
		if(($this->getTheme()->getType() != "cms") && file_exists(CONTROLLER_PATH . $this->getTheme()->getType().'/defineTPLVariables.inc.php')):
			include(CONTROLLER_PATH . $this->getTheme()->getType().'/defineTPLVariables.inc.php');
		endif;
	}

	private function sendHeaders() {
		switch($this->getPage()->getUrl()):
				case 403: 
					header("Status: 403 Forbidden");
				break;
				case 404: 
					header("Status: 404 Not Found");
				break;
				case 500: 
					header("Status: 500 Internal Server Error");
				break;
				default: 
					header("Status: 200 OK");
				break;
		endswitch;
	}
}