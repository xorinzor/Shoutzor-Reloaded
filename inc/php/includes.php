<?php

	//Load custom functions
	require_once(PHP_PATH . "functions.php");

	//Load Configuration file
	require_once(CONFIG_PATH . "config.inc.php");

	//=====================================
	//      Load custom Exceptions
	//=====================================
	
	require_once(PHP_PATH . "core/exceptions/permissionException.class.php");
	require_once(PHP_PATH . "core/exceptions/packageManagerException.class.php");

	//=====================================
	//      Required files
	//=====================================
	require_once(PHP_PATH . "core/debug.class.php");
	require_once(PHP_PATH . "resultCode.php");

	//=====================================
	//      Classloader
	//=====================================
	require_once(PHP_PATH . "core/classLoader.class.php");

	//Initialize the classLoader
	$_CMS['cl'] = cl::getInstance();

	//=====================================
	//      Load non-dependent CMS classes
	//=====================================
	$_CMS['cl']->load("debug",         	"debug", 			PHP_PATH . "core/debug.class.php"); //Debugging
	$_CMS['cl']->load("security", 		"security", 		PHP_PATH . "core/security.class.php"); //Variable checking
	$_CMS['cl']->load("tpl", 			"SmartyBC", 		LIB_PATH . "smarty/SmartyBC.class.php"); //Template parser
	$_CMS['cl']->g("tpl")->setCacheDir(TPL_CACHE_PATH);

	//=====================================
	//      Start loading CMS classes
	//=====================================    
	require_once(PHP_PATH . "core/abstract/setting.class.php");
	require_once(PHP_PATH . "core/settings/mysqlSetting.class.php");
	
	$_CMS['cl']->load("settings",       "settings",         PHP_PATH . "core/settings.class.php"); //CMS Settings
	
	//MySQLi connection to main Database
	$_CMS['cl']->load("mysql", 			"MySQL", 			PHP_PATH . "core/mysql.class.php", array(
																									$_configuration['mysql']['default']['host'],
																									$_configuration['mysql']['default']['user'],
																									$_configuration['mysql']['default']['pass'],
																									$_configuration['mysql']['default']['db']
																								));
													  
	//Remove mysql login data from the configuration variable to prevent further use
	unset($_configuration['mysql']); 

	$_CMS['cl']->g("settings")->setMySQLSettings();

	//Load user classes
	require_once(PHP_PATH . "core/user.class.php");
	$_CMS['cl']->load("users", "users", PHP_PATH . "core/users.class.php");


	//Set the custom Session Handler
	$_CMS['cl']->load("sessionHandler", "session_Handler",   PHP_PATH . "core/sessionHandler.class.php");
	session_set_save_handler(
			array($_CMS['cl']->g("sessionHandler"), 'open'),
			array($_CMS['cl']->g("sessionHandler"), 'close'),
			array($_CMS['cl']->g("sessionHandler"), 'read'),
			array($_CMS['cl']->g("sessionHandler"), 'write'),
			array($_CMS['cl']->g("sessionHandler"), 'destroy'),
			array($_CMS['cl']->g("sessionHandler"), 'clean')
		);

	//Not a SessionHandlerInterface so set this function manually
	register_shutdown_function(array($_CMS['cl']->g("sessionHandler"), 'shutdown'));
	
	//Start the session
	session_start();
	
	/**
	 * End of manual updating
	 */
	$_CMS['cl']->load("session", 		"session", 			PHP_PATH . "core/session.class.php"); //Session class
	
	//Alternate constructor, needed because the sessionHandler::write is called in this method and that method uses the session instance, 
	//but until the __construct() is completed that instance wont exist yet
	$_CMS['cl']->g("session")->altConstructor();
	
	$_CMS['cl']->load("permissions", 	"permissions", 		PHP_PATH . "core/permissions.class.php"); //permissions class
	$_CMS['cl']->load("core",     		"core", 			PHP_PATH . "core/core.class.php"); //CMS Core

	require_once(PHP_PATH . "core/page.class.php");
	require_once(PHP_PATH . "core/theme.class.php");
	$_CMS['cl']->load("themes",         "themes",           PHP_PATH . "core/themes.class.php"); //Page class
	$_CMS['cl']->load("pages",          "pages",            PHP_PATH . "core/pages.class.php"); //Page class
	$_CMS['cl']->load("display",        "display",          PHP_PATH . "core/display.class.php"); //Page class

	//Load language file
	require_once(LANGUAGE_PATH . "en/lang.php");

	//Add template regions
	$_CMS['cl']->load("regions", 		"Regions", 			PHP_PATH . "core/regions.class.php");

	//Create template Regions for the modules/plugins to add content to
	$_CMS['cl']->g("regions")->addRegion(array(
							"htmlBefore",
							"htmlStart",
							"htmlEnd",
							"htmlAfter",
							"headBefore",
							"headEnd",
							"bodyBefore",
							"bodyBegin",
							"bodyEnd",
							"navBarStart",
							"navBarEnd",
							"navBarNavigation",
							"containerBegin",
							"containerEnd",
							"commentsBegin",
							"commentsEnd",
							"replyBegin",
							"replyEnd",
							"captchaBegin",
							"captchaMain",
							"captchaEnd"
						));
	
	$_CMS['cl']->load("mail",     		"mailer", 			PHP_PATH . "core/mail.class.php"); //Mailer class
	$_CMS['cl']->load("webpage", 		"webpage", 			PHP_PATH . "core/webpage.class.php"); //Page elements (meta tags, etc)
	$_CMS['cl']->load("menu",			"menu", 			PHP_PATH . "core/menu.class.php"); //Menu
	$_CMS['cl']->load("format",    		"format", 			PHP_PATH . "core/format.class.php"); //Menu

	require_once(PHP_PATH . "lib/id3/getid3.php");
	require_once(PHP_PATH . "core/trackfile.class.php");
	require_once(PHP_PATH . "core/artist.class.php");
	require_once(PHP_PATH . "core/album.class.php");
	require_once(PHP_PATH . "core/track.class.php");
	require_once(PHP_PATH . "core/radio.class.php");

	$_CMS['cl']->load("search", 		"search",			PHP_PATH . "core/search.class.php");
	$_CMS['cl']->load("artists", 		"artists",			PHP_PATH . "core/artists.class.php");
	$_CMS['cl']->load("albums", 		"albums",			PHP_PATH . "core/albums.class.php");
	$_CMS['cl']->load("tracks", 		"tracks",			PHP_PATH . "core/tracks.class.php");
	$_CMS['cl']->load("queue", 			"queue",			PHP_PATH . "core/queue.class.php");

	$_CMS['cl']->load("radio", 			"radio",			PHP_PATH . "core/radio.class.php");

	//Load classes for social network accounts
	require_once(PHP_PATH . "core/abstract/socialAccount.class.php");
	require_once(PHP_PATH . "core/apiAccount/basicAccount.class.php");
	require_once(PHP_PATH . "core/apiAccount/userAccount.class.php");
	require_once(PHP_PATH . "core/apiAccount/shoutzorAccount.class.php");
	
	$_CMS['cl']->g("session")->setConnectedApiNetworks();

	//Load custom interfaces for Widgets, Plugins and Modules
	$_CMS['cl']->loadInterface("extensionBase");
	$_CMS['cl']->loadInterface("widgetBase");
	$_CMS['cl']->loadInterface("pluginBase");
	$_CMS['cl']->loadInterface("moduleBase");

	require_once(PHP_PATH . "base/extensionInfo.class.php");

	require_once(PHP_PATH . "base/module.class.php");
	require_once(PHP_PATH . "base/plugin.class.php");
	require_once(PHP_PATH . "base/widget.class.php");

	//LiquidSoap class
	require_once(PHP_PATH . "core/liquidsoap.class.php");