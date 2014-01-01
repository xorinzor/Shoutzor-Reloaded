<?php

class Liquidsoap {
    
	private static $socket = '/home/shoutzor/socket'; //must have www-data:www-data permission

    private static function sendCommand($command, $customsocket = '') {
		$sock = socket_create(AF_UNIX,SOCK_STREAM,0);

		if ($sock == FALSE){
			throw new Exception("Unable to create socket: " . socket_strerror(socket_last_error()));
		}

		if (!socket_connect($sock, self::$socket.$customsocket, null)){
			throw new Exception('Unable to connect to '.self::$socket.$customsocket .socket_strerror(socket_last_error()));
		}

		$msg = "$command\n\0";
		$length = strlen($msg);
		$retval=array();
		$sent = socket_write($sock,$msg,$length);

		if($sent === false) {
			throw Exception("Unable to write to socket: " .socket_strerror(socket_last_error()));
			return false;
		}

		if($sent < $length) {
			$msg = substr($msg, $sent);
			$length -= $sent;
			debug::addLine("DEBUG", "Message truncated: Resending: $msg", __FILE__, __LINE__);
		} else if($noResponse) {
			return null;
		} else {
			while ($buffer = socket_read($sock, 4096, PHP_NORMAL_READ)){
				if ($buffer == "END\r"){ //Liquidsoap send an END\r message for each interaction
					socket_write($sock,"exit\n\0",$length);
					break;
				}

				$retval[] = trim($buffer);
			}

			socket_close($sock);

			return $retval;
		}
    }

    public static function run($cmd, $customsocket = '') {
		try {
    		return self::sendCommand($cmd, $customsocket);
    	} catch(Exception $e) {
    		debug::addLine("DEBUG", $e->getMessage(), __FILE__, __LINE__);
    		return false;
    	}
    }

	public static function isRunning($customsocket = '') {
		return (self::run('uptime', $customsocket) !== false);
	}

    public static function setVolume($volume) {
    	return self::run('sound.volume 0 '.$volume);
    }

	public static function nextTrack() {
    	return self::run('shoutzorqueue.skip');
    }

    public static function requestTrack($filename) {
    	return self::run('shoutzorqueue.push '.$filename);
    }
    
    public static function isUp($customsocket = '') {
		return self::run('uptime', $customsocket);
    }
    

}