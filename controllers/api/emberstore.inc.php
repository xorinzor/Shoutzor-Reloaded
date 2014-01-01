<?php
	//We dont need a theme for the API since we want to output raw data
	cl::g("display")->setThemeEnabled(false);

	//header('access-control-allow-origin: *');
	header('Content-type: application/json');

	cl::getInstance()->load("api", "api", PHP_PATH . "core/api/api.class.php");
    
	$array = array(
			'method' => $_GET['var2'],
			'network' => 'shoutzor',
			'format' => 'phparray1337'
		);

    //Execute API call
    $result = cl::g("api")->doMethod($array);
    echo cl::g("format")->convert("json", $result['shoutzor']['result']);

die();