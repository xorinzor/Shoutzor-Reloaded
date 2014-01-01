<?php

/**
 * @author Jorin Vermeulen
 * @version 0.1 alpha
 * @copyright you are NOT allowed, under any circumstances, to use any code of this system on different websites or distribute this code!
 **/

// Prevent early output
ob_start();

//System Variable
$_CMS = array();

//Define system paths
define("DEFAULT_PATH", 		realpath(dirname(__FILE__)) . "/");
define("LANGUAGE_PATH",		DEFAULT_PATH . "inc/lang/");
define("LOG_PATH", 			DEFAULT_PATH . "inc/log/");
define("PHP_PATH", 			DEFAULT_PATH . "inc/php/");
define("BASECLASS_PATH",	DEFAULT_PATH . "inc/php/base/");
define("INTERFACE_PATH",	DEFAULT_PATH . "inc/php/interfaces/");
define("LIB_PATH", 			DEFAULT_PATH . "inc/php/lib/");
define("CONFIG_PATH", 		DEFAULT_PATH . "inc/php/config/");
define("PACKAGES_PATH", 	DEFAULT_PATH . "inc/packages/");
define("TPL_CACHE_PATH", 	DEFAULT_PATH . "templates_c/");
define("VIEW_PATH", 		DEFAULT_PATH . "views/");
define("THEME_PATH",		VIEW_PATH 	 . "themes/");
define("CONTROLLER_PATH", 	DEFAULT_PATH . "controllers/");
define("ADDON_PATH", 		DEFAULT_PATH . "addons/");
define("TEMP_PATH", 		DEFAULT_PATH . "temp/");
define("UPLOADS_PATH", 		DEFAULT_PATH . "static/uploads/");

//Load required files
include(PHP_PATH . "includes.php");

//Give only the server access to the "/liquidsoap" url for server-specific tasks
if(!in_array(cl::g("session")->getUserIP(), array("127.0.0.1", $_SERVER['SERVER_ADDR'])) && $_GET['page'] == "liquidsoap"):
	cl::g("display")
				->setRequestedThemeType("liquidsoap")
				->setRequestedUrl($_GET['var1'])
				->setTheme(cl::g("themes")->getTheme('cms', '1'))
				->setPage(cl::g("pages")->getPage('', 'cms', '403'))
				->displayPage();
	die();
endif;

//Check whether to show the default website or another partff
switch($_GET['page']):
	case "api":
		cl::g("display")
				->setRequestedThemeType("api")
				->setRequestedUrl($_GET['var1'])
				->setTheme(cl::g("themes")->getTheme('api', '1'))
				->setPage(cl::g("pages")->getPage('', 'api', $_GET['var1']));
	break;

	case "dashboard":
		cl::g("display")
				->setRequestedThemeType("dashboard")
				->setRequestedUrl($_GET['var1'])
				->setTheme(cl::g("themes")->getTheme('dashboard', '1'))
				->setPage(cl::g("pages")->getPage('', 'dashboard', $_GET['var1']));
	break;

	case "liquidsoap":
		cl::g("display")
				->setRequestedThemeType("liquidsoap")
				->setRequestedUrl($_GET['var1'])
				->setTheme(cl::g("themes")->getTheme('liquidsoap', '1'))
				->setPage(cl::g("pages")->getPage('', 'liquidsoap', $_GET['var1']));
	break;

	default:
		cl::g("display")
				->setRequestedThemeType("default")
				->setRequestedUrl($_GET['page'])
				->setTheme(cl::g("themes")->getTheme('default', '1'))
				->setPage(cl::g("pages")->getPage('', 'default', $_GET['page']));
	break;
endswitch;

/**************************************
 * Check if maintenance mode is active
 *************************************/
if((cl::g("settings")->get("maintenance") == '1') && (cl::g("display")->getRequestedThemeType() != 'default' || cl::g("display")->getRequestedUrl() != 'login')):
	if(!cl::g("session")->isLoggedin() || !cl::g("permissions")->hasPermission("maintenance")):
		cl::g("display")
				->setTheme(cl::g("themes")->getTheme('cms', '1'))
				->setPage(cl::g("pages")->getPage('', 'cms', 'maintenance'))
				->displayPage();
		die();
	endif;
endif;
/*************************************/

//Output result
cl::g("display")->displayPage();