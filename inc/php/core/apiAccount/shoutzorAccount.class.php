<?php

class shoutzorAccount extends socialAccount {
	public function __CONSTRUCT($aid, $name) {
		parent::__CONSTRUCT($id, $name);
	}
	
	private function updateUserInfo() {
	}

	private function randomPassword() {
		$alphabet = "0123456789";
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < 4; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass); //turn the array into a string
	}

	/*=====================================
	 * CMS USER-API ONLY METHODS
	 *====================================*/
	public function terminal() {
		$check = cl::g("permissions")->hasPermission("controlshoutzor");
		if($check === false) { die('You don\'t have permission to control shoutzor'); }

		$res = LiquidSoap::run($_POST['cmd']);

		ob_start();
			foreach($res as $line):
				if(!empty($line)) echo $line . PHP_EOL;
			endforeach;
		$res = ob_end_flush();

		return $res;
	}

	public function changevolume() {
		$check = cl::g("permissions")->hasPermission("controlshoutzor");
		if($check === false) { die('You don\'t have permission to control shoutzor'); }

		return array(
			'volume' => (LiquidSoap::setVolume($_POST['volume']))
		);
	}

	public function createaccount() {
		$check = cl::g("permissions")->hasPermission("createaccount");
		if($check === false) { return array('result' => false, 'message' => 'You don\'t have permission to create accounts'); }

		if(!security::valid("NAME", $_POST['firstname'])):
			return array('error' => 'invalid firstname, text only!');
		elseif(!security::valid("NAME", $_POST['lastname'])):
			return array('error' => 'invalid lastname, text only!');
		elseif(!security::valid("EMAIL", $_POST['email'])):
			return array('error' => 'invalid email!');
		endif;

		$password = $this->randomPassword();

		$user = (new user())->setId(0)
							->setName($_POST['lastname'])
							->setFirstName($_POST['firstname'])
							->setEmail($_POST['email'])
							->setPassword(security::encryptOneWay('password', $password))
							->setStatus(1);

		$result = cl::g("users")->addUser($user);
		if($result['error'] === true) return $result; //Check if error occured

		return array(
				'username' => $_POST['email'],
				'password' => $password
			);
	}

	public function getradioinfo() {
		return array(
				'nowplaying' 	=> cl::g("radio")->nowPlaying(),
				'nexttrack' 	=> cl::g("radio")->nextTrack(),
				'previoustrack' => cl::g("radio")->previousTrack()
			);
	}

	public function getradiohistory() {
		return cl::g("radio")->getHistory();
	}

	public function getsysteminfo() {
		$check = cl::g("permissions")->hasPermission("controlshoutzor");
		if($check === false) { return array('result' => false, 'message' => 'You don\'t have permission to control shoutzor'); }

		return array(
				'mainliquidsoap_status' 	=> LiquidSoap::isUp('main'),
				'shoutzorliquidsoap_status' => LiquidSoap::isUp()
			);
	}

	public function toggleliquidsoap() {
		$check = cl::g("permissions")->hasPermission("controlshoutzor");
		if($check === false) { return array('result' => false, 'message' => 'You don\'t have permission to control shoutzor'); }

		switch($_POST['service']):
			case "main":
				switch($_POST['operation']):
					case "start":
						if(LiquidSoap::isRunning('main')) return array('result' => false);
						exec("screen -dmS main liquidsoap /home/shoutzor/main.liq"); //Start the tracklist
					break;

					case "stop":
						if(!LiquidSoap::isRunning('main')) return array('result' => false);
						exec("screen -X -S main quit");
					break;
				endswitch;
			break;

			case "shoutzor":
				switch($_POST['operation']):
					case "start":
						if(LiquidSoap::isRunning()) return array('result' => false);
						exec("screen -dmS shoutzor liquidsoap /home/shoutzor/shoutzor.liq"); //Start the tracklist

						sleep(5);
						if(LiquidSoap::isRunning()):
							LiquidSoap::run("sound.select 0 true");

							$queues = cl::g("queue")->get();

							if(count($queues) > 1) {
								foreach($queues as $queue):
									//get the track from the queue
									$trackid = $queue['track'];

									//Play the track from the queue
									$track = cl::g("tracks")->getTrack($trackid);

									if(is_a($track, "track")) { LiquidSoap::requestTrack('replay_gain:'.$track->getFile()->getFilePath()); }
								endforeach;
							} else if(count($queues) == 1) {
								//Add 1 song to queue, call nexttrack
								$track = cl::g("tracks")->getTrack($queues[0]['track']);
								if(is_a($track, "track")) { LiquidSoap::requestTrack('replay_gain:'.$track->getFile()->getFilePath()); }

								$track = cl::g("tracks")->getRandomTrack();
								if(is_a($track, "track")) { cl::g("queue")->add($track); }
							} else {
								//Add 2 songs to queue, call nexttrack
								$track = cl::g("tracks")->getRandomTrack();
								if(is_a($track, "track")) { cl::g("queue")->add($track); }

								$track = cl::g("tracks")->getRandomTrack();
								if(is_a($track, "track")) { cl::g("queue")->add($track); }
							}

							//exec("curl ".SITEURL."liquidsoap/getnexttrack/ > /dev/null &"); //Start the tracklist
						endif;
					break;

					case "stop":
						if(!LiquidSoap::isRunning()) return array('result' => false);
						exec("screen -X -S shoutzor quit");
						//exec("killall liquidsoap > /dev/null &");
					break;

					case "next":
						if(!LiquidSoap::isRunning()) return array('result' => false);
						LiquidSoap::nextTrack();
					break;

					default:
						return array('result' => false, 'debug' => $result);
					break;
				endswitch;
			break;

			default:
				return array('result' => false, 'debug' => 'invalid service');
			break;
		endswitch;
		
		return array('result' => true);
	}

	public function tracks() {
		if(isset(cl::g("core")->GetVars['artist'])):
			$filter = cl::g("core")->GetVars['artist'];
			if(empty($filter) || !security::valid("NUMERIC", $filter)) return array('tracks' => array());

			$query = cl::g("mysql")->query("SELECT * FROM `track` WHERE id IN (SELECT track FROM `track_album` WHERE album=$filter)") or debug::addLine("fatal", "could not get tracks, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
			if(!$query || $query->num_rows == 0) return array('tracks' => array());

			$result = array(
						'albums' => array(),
						'artists' => array(),
						'tracks' => array()
					);

			while($trackData = $query->fetch_object()):
				$temp = cl::g("tracks")->getTrack($trackData->id)->toEmberArray();

				$result['tracks'][] = $temp['track'];
				foreach($temp['albums'] as $album) $result['albums'][$album['id']] = $album;
				foreach($temp['artists'] as $artist) $result['artists'][$artist['id']] = $artist;
			endwhile;

			$result['albums'] = array_values($result['albums']);
			$result['artists'] = array_values($result['artists']);
		else:
			$filter = $_GET['var3'];
			if(empty($filter) || !security::valid("NUMERIC", $filter)) return array('track' => array());

			$result = cl::g("tracks")->getTrack($filter)->toEmberArray();
		endif;

		return $result;
	}

	public function albums() {
		if(isset(cl::g("core")->GetVars['artist'])):
			$filter = cl::g("core")->GetVars['artist'];
			if(empty($filter) || !security::valid("NUMERIC", $filter)) return array('albums' => array());

			$query = cl::g("mysql")->query("SELECT * FROM `album` WHERE artist=$filter") or debug::addLine("fatal", "could not get albums, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
			if(!$query || $query->num_rows == 0) return array('albums' => array());

			$result = array(
						'albums' => array(),
						'artists' => array()
					);

			while($albumData = $query->fetch_object()):
				$temp = cl::g("albums")->getAlbum($albumData->id)->toEmberArray();
				$result['albums'][$temp['album']['id']] = $temp['album'];
				$result['artists'][$temp['artist']['id']] = $temp['artist'];
			endwhile;

			$result['albums'] = array_values($result['albums']);
			$result['artists'] = array_values($result['artists']);
		else:
			$filter = $_GET['var3'];
			if(empty($filter) || !security::valid("NUMERIC", $filter)) return array('album' => array());

			$result = cl::g("albums")->getAlbum($filter)->toEmberArray();
		endif;

		return $result;
	}

	public function artists() {
		$filter = $_GET['var3'];
		if(empty($filter) || !security::valid("NUMERIC", $filter)) return array('artist' => array());

		$result = cl::g("artists")->getArtist($filter)->toArray();
		return array(
				'artist' => $result
			);
	}

	public function histories() {
		$result  = cl::g("radio")->getEmberHistory();

		$extra = array();
		$extra['albums'] = array();
		$extra['artists'] = array();
		$extra['users'] = array();
		$extra['tracks'] = array();

		foreach($result as &$item) {
			foreach($item['albums'] as $album) $extra['albums'][$album['id']] = $album;
			foreach($item['artists'] as $artist) $extra['artists'][$artist['id']] = $artist;

			if(is_array($item['user'])):
				$extra['users'][$item['user']['id']] = $item['user'];
				$item['user'] = $item['user']['id'];
			endif;

			$extra['tracks'][$item['track']['id']] = $item['track'];
			$item['track'] = $item['track']['id'];

			unset($item['albums']);
			unset($item['artists']);
		}

		$extra['tracks'] = array_values($extra['tracks']);
		$extra['users'] = array_values($extra['users']);
		$extra['albums'] = array_values($extra['albums']);
		$extra['artists'] = array_values($extra['artists']);

		foreach($extra['tracks'] as &$track) {
			if(!is_array($track) || count($track) == 0) unset($track);
		}

		return array(
				'tracks' 	=> $extra['tracks'],
				'albums' 	=> $extra['albums'],
				'artists' 	=> $extra['artists'],
				'users' 	=> $extra['users'],
				'histories' => $result
			);
	}

	public function users() {
		$filter = $_GET['var3'];
		if(empty($filter) || !security::valid("NUMERIC", $filter)) return array('artists' => array());
	}

	public function queues() {
		$result  = cl::g("radio")->getEmberQueue();

		$extra = array();
		$extra['albums'] = array();
		$extra['artists'] = array();
		$extra['users'] = array();
		$extra['tracks'] = array();

		foreach($result as &$item) {
			foreach($item['albums'] as $album) $extra['albums'][$album['id']] = $album;
			foreach($item['artists'] as $artist) $extra['artists'][$artist['id']] = $artist;

			if(is_array($item['user'])):
				$extra['users'][$item['user']['id']] = $item['user'];
				$item['user'] = $item['user']['id'];
			endif;

			$extra['tracks'][$item['track']['id']] = $item['track'];
			$item['track'] = $item['track']['id'];

			//unset($item['track']);
			unset($item['albums']);
			unset($item['artists']);
			$i++;
		}

		$extra['tracks'] = array_values($extra['tracks']);
		$extra['users'] = array_values($extra['users']);
		$extra['albums'] = array_values($extra['albums']);
		$extra['artists'] = array_values($extra['artists']);

		foreach($extra['tracks'] as &$track) {
			if(!is_array($track) || count($track) == 0) unset($track);
		}

		$temp = array(1 => array("id" => 1),2 => array("id" => 2),3 => array("id" => 3),4 => array("id" => 4),5 => array("id" => 5),6 => array("id" => 6),7 => array("id" => 7),8 => array("id" => 8),9 => array("id" => 9),10 => array("id" => 10),11 => array("id" => 11),12 => array("id" => 12),13 => array("id" => 13),14 => array("id" => 14),15 => array("id" => 15),16 => array("id" => 16),17 => array("id" => 17),18 => array("id" => 18),19 => array("id" => 19),20 => array("id" => 20),21 => array("id" => 21),22 => array("id" => 22),23 => array("id" => 23),24 => array("id" => 24),25 => array("id" => 25),26 => array("id" => 26),27 => array("id" => 27),28 => array("id" => 28),29 => array("id" => 29),30 => array("id" => 30),31 => array("id" => 31),32 => array("id" => 32),33 => array("id" => 33),34 => array("id" => 34),35 => array("id" => 35),36 => array("id" => 36),37 => array("id" => 37),38 => array("id" => 38),39 => array("id" => 39),40 => array("id" => 40),41 => array("id" => 41),42 => array("id" => 42),43 => array("id" => 43),44 => array("id" => 44),45 => array("id" => 45),46 => array("id" => 46),47 => array("id" => 47),48 => array("id" => 48),49 => array("id" => 49),50 => array("id" => 50));
		$result = array_merge($temp, $result);

		return array(
				'tracks' => $extra['tracks'],
				'albums' => $extra['albums'],
				'artists' => $extra['artists'],
				'users' => $extra['users'],
				'queues' => $result
			);
	}

	public function request() {
		$trackid = $_GET['var3'];
		if(empty($trackid) || !security::valid("NUMERIC", $trackid)) return array('result' => false, 'message' => 'Invalid track ID provided');

		$check = cl::g("permissions")->hasPermission("requesttrack");
		if($check === false) return array('result' => false, 'message' => 'You don\'t have permission to request a track');

		$track = cl::g("tracks")->getTrack($trackid);
		if(!is_object($track)) return array('result' => false, 'message' => 'Invalid track ID provided');

		$check = cl::g("tracks")->isRequestable($trackid);
		if($check['result'] === false) return array('result' => false, 'message' => $check['message']);

		$uid = cl::g("session")->getUser()->getId();

		$query = cl::g("mysql")->query("SELECT user
										FROM queue r 
										WHERE user IN
										(
											SELECT user
											FROM queue q
											WHERE 
												q.user = $uid AND
												q.time_requested > NOW() - INTERVAL ".USER_REQUEST_DELAY." MINUTE
										)
										OR user IN
										(
											SELECT user
											FROM queue_history h
											WHERE
											h.user = $uid AND
											h.time_played > NOW() - INTERVAL ".USER_REQUEST_DELAY." MINUTE
										)") or debug::addLine("error", "Could not check if user could request track, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);

		if(!$query) return array('result' => false, 'message' => 'An internal error occured, please try again');
		if($query->num_rows > 0)  return array('result' => false, 'message' => 'You recently requested a track, try again in a couple of minutes');

		$check = cl::g("queue")->add($track, cl::g("session")->getUser()->getId());
		if($check === false) return array('result' => false, 'message' => 'Track could not be requested, if this happens often, please report it to have it solved (basically.. this error should not happen so dont worry, you did nothing wrong.)');

		return array('result' => true, 'message' => 'Track is added to the queue!');
	}

	public function searches() {
		$query = cl::g("core")->GetVars['query'];

		$search = cl::g("tracks")->search($query, 1, 25);

		return array(
				'searches' => array(
					array(
						'id' => 1, //default, DONT CHANGE!
						'tracks' => (count($search['searches']) > 0) ? $search['searches'] : array(), //Contain all result track ID's
						'albums' => array(), //Contain all result album ID's <will be empty until implemented, have nothing to do with track results>
						'artists' => array() //Contain all result artist ID's <will be empty until implemented, have nothing to do with track results>
					)
				),
				'tracks' => (count($search['tracks']) > 0) ? $search['tracks'] : array(), //Contain all tracks from search result
				'artists' => (count($search['artists']) > 0) ? $search['artists'] : array(), //Contain all artists from search result
				'albums' => (count($search['albums']) > 0) ? $search['albums'] : array() //Contain all albums from search result
			);
	}

	public function correctplaylistbackward() {
		$return = cl::g("queue")->getHistory(1);
		
		cl::g("mysql")->query("DELETE FROM `queue_history` WHERE 1=1 ORDER BY time_played DESC LIMIT 1") or debug::addLine("error", "could not remove track from history queue, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
	}

	public function correctplaylistforward() {
		$queue = cl::g("queue")->get(1);
			
		//Check if a track in the queue is set
		if(count($queue) > 0):
			//get the track from the queue
			$trackid = $queue[0]['track'];

			//Play the track from the queue
			$track = cl::g("tracks")->getTrack($trackid);

			if(!is_a($track, "track")):
				return false;
			endif;
		else:
			//No track in the queue, add one randomly
			$track = cl::g("tracks")->getRandomTrack();

			if(!is_a($track, "track")):
				return false;
			endif;

			cl::g("queue")->add($track, "null", true);
		endif;

		//Remove the track from the queue and move it to the queue_history
		$result = cl::g("queue")->remove($track->getId());

		//Check if there are no songs in the queue
		if(count(cl::g("queue")->get(1)) == 0):
			//Add random track to the queue
			$track = cl::g("tracks")->getRandomTrack();

			if(!is_a($track, "track")):
				return false;
			endif;

			cl::g("queue")->add($track, "null", true);
		endif;
	}
}