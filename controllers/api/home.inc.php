<?php
	//We dont need a theme for the API since we want to output raw data
	cl::g("display")->setThemeEnabled(false);

	cl::getInstance()->load("api", "api", PHP_PATH . "core/api/api.class.php");
    
    //Execute API call
    echo cl::g("api")->doMethod($_POST);

die();