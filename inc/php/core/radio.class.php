<?php

	class radio {
		public function nowPlaying() {
			$track = cl::g("queue")->getHistory(1);
			if(count($track) < 0) return array();

			return cl::g("tracks")->getTrack($track[0]['track'])->toArray();
		}

		public function nextTrack() {
			$track = cl::g("queue")->get(1);
			if(count($track) < 0) return array();

			return cl::g("tracks")->getTrack($track[0]['track'])->toArray();
		}

		public function previousTrack() {
			$track = cl::g("queue")->getHistory(2);
			if(count($track) < 2) return array();

			return cl::g("tracks")->getTrack($track[1]['track'])->toArray();
		}

		public function getHistory() {
			$tracks = cl::g("queue")->getHistory(15);
			if(count($tracks) < 1) return array();

			$result = array();

			foreach($tracks as $track):
				$user = null;

				if($track['user'] != null && !empty($track['user'])):
					$user = cl::g("users")->getUser($track['user'])->toArray();
					if(is_array($user)) unset($user['password']);
				endif;

				if(!is_array($user)) $user = null;

				$result[] = array(
								'track' 		=> cl::g("tracks")->getTrack($track['track'])->toArray(),
								'time_played' 	=> $track['time_played'],
								'user' 			=> $user
							);

				$track = array();
			endforeach;

			return $result;
		}

		public function getEmberHistory() {
			$tracks = cl::g("queue")->getHistory(15);
			if(count($tracks) < 1) return array();

			$result = array();
			$i = 1;

			foreach($tracks as $track):
				$user = null;

				if($track['user'] != null && !empty($track['user'])):
					$user = cl::g("users")->getUser($track['user'])->toArray();
					if(is_array($user)) unset($user['password']);
				endif;

				if(!is_array($user)) $user = null;

				$temp = cl::g("tracks")->getTrack($track['track'])->toEmberArray();

				$temp['id'] = $i;
				$temp['time_played'] = $track['time_played'];

				$temp['user'] = (is_array($user)) ? $user : null;

				$result[] = $temp;

				unset($temp);
				$track = array();

				$i++;
			endforeach;

			return $result;
		}

		public function getEmberQueue() {
			$tracks = cl::g("queue")->get();
			if(count($tracks) < 1) return array();

			$result = array();
			$i = 1;

			foreach($tracks as $track):
				$user = null;

				if($track['user'] != null && !empty($track['user'])):
					$user = cl::g("users")->getUser($track['user'])->toArray();
					if(is_array($user)) unset($user['password']);
				endif;

				if(!is_array($user)) $user = null;

				$temp = cl::g("tracks")->getTrack($track['track'])->toEmberArray();

				$temp['id'] = $i;

				$temp['user'] = (is_array($user)) ? $user : null;
				$temp['time_requested'] = $track['time_requested'];

				$result[] = $temp;

				unset($temp);
				$track = array();

				$i++;
			endforeach;

			return $result;
		}
	}