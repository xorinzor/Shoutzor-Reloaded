<?php

class themes {
	//The directory is created as follow: <basedir>/views/themes/<type>/<name>/header_<style>.tpl
	public function getTheme($type, $name, $style = 'default') {
		if(!security::valid("DIRECTORY", $type) || !security::valid("DIRECTORY", $name) || !security::valid("DIRECTORY", $style)):
			debug::addLine("FATAL", "Invalid theme requested (parameters contained illegal characters", __FILE__, __LINE__);
			return false;
		endif;

		//Clear PHP cache
		clearstatcache();

		//Check if the directory is indeed a directory (not a virtual link) and exists
		if(!is_dir(THEME_PATH . $type . '/' . $name)):
			debug::addLine("FATAL", "Theme '$name' from type '$type' does not exist", __FILE__, __LINE__);
			return false;
		endif;

		if(!file_exists(THEME_PATH . $type . '/' . $name . '/header_'.$style.'.tpl') || !file_exists(THEME_PATH . $type . '/' . $name . '/footer_'.$style.'.tpl')):
			debug::addLine("FATAL", "Header and/or footer template file from theme '".$this->getTheme()."' for style '".$this->getThemeStyle()."' from type '".$this->getThemeType()."' are missing or corrupt", __FILE__, __LINE__);
			return false;
		endif;

		$theme = (new theme())->setName($name)
								->setType($type)
								->setStyle($style);

		return $theme;
	}
}