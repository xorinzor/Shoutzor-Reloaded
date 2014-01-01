<?php

class debug
{
	private static $logFile;

	public function __CONSTRUCT() 
	{
		//Allow all errors EXCEPT E_Notice
		error_reporting(E_ALL ^ E_NOTICE);

		self::$logFile = LOG_PATH . "log_" . date("d-m-Y") . ".txt";
		self::checkLogFile();

		set_error_handler(array($this, 'ErrorHandler'));
		register_shutdown_function(array($this, 'ErrorHandler'));
	}

	// Custom error handling function
	public static function ErrorHandler($errno = 0, $msg = '', $file = '', $line = 0) {
		$error = error_get_last();

		if($errno == 0) 	$errno = $error['type'];
		if($msg == '') 		$msg = $error['message'];
		if($file == '') 	$file = $error['file'];
		if($line == 0) 	$line = $error['line'];

		switch($errno):
			case E_CORE_ERROR:
				$type = "FATAL CORE ERROR";
			break;

			case E_ERROR:
				$type = "FATAL";
			break;

			case E_WARNING:
				$type = "ERROR";
			break;

			case E_NOTICE:
				//because there are too many of these (and they are quite useless) we dont log these.
				return true;
			break;

			case E_STRICT:
				//We dont want this error to be logged
				return true;
			break;

			default:
				$type = "UNKOWN";
			break;
		endswitch;

		if($type == '') return false;

		self::addLine($type, "errorcode: $errno - message: $msg", $file, $line);
				
		return true;
	}

	private static function checkLogFile()
	{
		if(@file_exists(self::$logFile)):
			return (boolean) true;
		else:
			#file doesnt exists, creating file.
			$result = @file_put_contents(self::$logFile, "[".date("H:i:s")."][NOTIFICATION] - Log file created. \r\n", LOCK_EX) or die("Could not create log file");

			return $result;
		endif;
	}

	public static function addLine($type, $error, $file, $line)
	{
		if(empty($error) || empty($file) || empty($line)) return false;

		if(!@file_exists(self::$logFile)):
			self::checkLogFile();
			return false;
		else:
			switch(strtolower($type)):
				case "fatal core error":
					$type = "fatal core error";
				break;

				case "fatal":
					$type = "fatal";
				break;

				case "error":
					$type = "error";
				break;

				case "warning":
					$type = "warning";
				break;

				case "notice":
					$type = "notice";
				break;

				case "debug":
					$type = "debug";
				break;

				default:
					$type = "invalid_type";
				break;
			endswitch;

			$type = strtoupper($type);

			if(!ctype_alnum($error)):
				ob_start();
				var_dump($error);
				$error = ob_get_contents();
				ob_end_clean();
			endif;

			#file does exists, add line.
			$file = (!empty($file)) ? "- in $file at line $line \r\n" : '';

			file_put_contents(self::$logFile, "[".date("H:i:s")."][$type] $error $file",  FILE_APPEND | LOCK_EX);

            if($type == "FATAL" || $type == "FATAL CORE ERROR") self::fatalError("fatalerror");

			return true;
		endif;
	}
    
    public static function fatalError($type) {
        //Turn off and remove all previous output buffers
        while (ob_get_level()) ob_end_clean();
        
        //Check if TPL file for given type exists, otherwise show fatalerror page
        if(!file_exists(VIEW_PATH . 'cms/' . $type . '.tpl')):
            $type = "fatalerror";
        endif;
        
        include(CONTROLLER_PATH . 'cms/defaultTPLVariables.inc.php');
        include(CONTROLLER_PATH . 'cms/fatalerror.inc.php');
        
        //Check if controller file for given page exists
        if(file_exists(CONTROLLER_PATH . 'cms/'.$type.'.inc.php') && $type != "fatalerror"):
            include(CONTROLLER_PATH . 'cms/'.$type.'.inc.php');
        endif;

        //Get contents of the errorpage view and parse them with the template parser
        $header = cl::g("tpl")->fetch(VIEW_PATH . 'themes/cms/1/header_error.tpl');
        $body   = cl::g("tpl")->fetch(VIEW_PATH . 'cms/'.$type.'.tpl');
        $footer = cl::g("tpl")->fetch(VIEW_PATH . 'themes/cms/1/footer_error.tpl');
        
        //Output errorpage to the user
        echo $header.$body.$footer;
        
        //Stop further PHP execution and show the page to the user
        die();
    }
}