<?php
cl::g("display")->setThemeEnabled(false);

$result = array(
			"result" => false, 
			"reason" => "error"
		);

$username           = $_POST['username'];
$password 	        = security::encryptOneWay("password", $_POST['password']);

$login = cl::g("session")->login($username, $password);

if($login === true) {
	$result['result'] = true;
	$result['reason'] = '';
}

die(json_encode($result));