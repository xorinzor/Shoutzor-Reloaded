<?php

/**
 * Using this class you are able to fetch, update and remove any page related data
 **/
class pages {
	public function getPage($id = '', $site = '', $url = '', $ifFalseReturn404 = true) {
		//Security check
		if(!security::valid("NUMERIC", $id)) 	$id = '';
		if(!security::valid("SITE", $site)) 	$site = '';
		if(!security::valid("URL", $url)) 		$url = 'home';

		if(empty($id)):
			if(empty($site) || empty($url)) return false;
			$filter = "site='$site' AND url='$url'";
		else:
			$filter = "id=$id";
		endif;

		$query = cl::g("mysql")->query("SELECT * FROM `".SQL_PREFIX."pages` WHERE ".$filter) or debug::addLine("FATAL", "Could not fetch page from database, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
		if(!$query) return false;
		if($query->num_rows == 0 && $site == 'cms' && $url == '404'):
			//FATAL CMS ERROR! 404 page does not exist in the database
			debug::addLine("FATAL", "the 404 page does not exist in the database", __FILE__, __LINE__);
			return false;
		elseif($query->num_rows == 0):
			return ($ifFalseReturn404 === true) ? $this->getPage('', 'cms', '404') : false;
		endif;

		$data = $query->fetch_object();

		$page = (new page())->setId($data->id)
							->setTitle($data->title)
							->setUrl($data->url)
							->setSite($data->site)
							->setContent($data->content)
							->setUseView($data->useview)
							->setTemplateFileName($data->templatefile)
							->setStatus($data->status)
							->setProtected($data->protected)
							->setHidden($data->hidden);

		return $page;
	}

	/**
	 * Returns an array of pages matching the given parameters
	 * @param string the name of the site (for example: default or admin)
	 * @return array contains the pages matching the given parameters
	 */
	public function getPageList($site = '') {
		$result = array();

		if(!empty($site)):
			if(!security::valid("SITE", $site)) return $result;
		endif;

		$query = cl::g("mysql")->query("SELECT * FROM `".SQL_PREFIX."pages` " . (($site == '') ? '' : "WHERE site='$site")) or debug::addLine("FATAL", "Could not fetch pages from database, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);

		while($data = $query->fetch_object()) {
			$result[] = (new page())->setId($data->id)
							->setTitle($data->title)
							->setUrl($data->url)
							->setSite($data->site)
							->setContent($data->content)
							->setUseView($data->useview)
							->setTemplateFileName($data->templatefile)
							->setStatus($data->status)
							->setProtected($data->protected)
							->setHidden($data->hidden);
		}

		return $result;
	}
}