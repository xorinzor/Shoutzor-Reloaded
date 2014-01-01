<?php

class tracks {
	/**
	 * Retrieves a track from the database and returns its object
	 * @param id integer
	 * @return object:track
	 */
	public function getTrack($id) {
		if(!ctype_digit($id) || $id < 1) return false;

		$query = cl::g("mysql")->query("SELECT * FROM `track` WHERE id=$id");
		if(!$query || $query->num_rows == 0):
			debug::addLine("error", "Could not retrieve track from the database, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
			return false;
		endif;

		$trackData = $query->fetch_object();
		
		try {
			$trackFile = new trackFile(stripslashes($trackData->filename));
			$trackFile->setLength($trackData->length);
		} catch(Exception $e) {
			//File does not exist anymore, delete the file (just to be sure) and remove every reference in the database
			$this->removeTrack($id);
			return false;
		}

		//Retrieve all connected artist for the current track from the database
		$query = cl::g("mysql")->query("SELECT * FROM `track_artist` WHERE track=$id") or debug::addLine("error", "Could not retrieve artists for track from the database", __FILE__, __LINE__);
		$artist = array();
		if($query && $query->num_rows > 0):
			while($trackArtist = $query->fetch_object()):
				$temp = cl::g("artists")->getArtist($trackArtist->artist); //fetch artist instance using its ID
				if($temp instanceof artist) $artist[] = $temp;
			endwhile;
		endif;

		//Retrieve all connected albums for the current track from the database
		$query = cl::g("mysql")->query("SELECT * FROM `track_album` WHERE track=$id") or debug::addLine("error", "Could not retrieve albums for track from the database", __FILE__, __LINE__);
		$album = array();
		if($query && $query->num_rows > 0):
			while($trackAlbum = $query->fetch_object()):
				$temp = cl::g("albums")->getAlbum($trackAlbum->album); //fetch album instance using its ID
				if($temp instanceof album) $album[] = $temp;
			endwhile;
		endif;

		//Create track object
		$track = new track();
		$track->setId($trackData->id)
				->setTitle(stripslashes($trackData->title))
				->setArtist($artist)
				->setAlbum($album)
				->setFile($trackFile);

		//Return the track object
		return $track;
	}

	public function isRequestable($trackid) {
		if(empty($trackid) || !security::valid("NUMERIC", $trackid)) return false;

		$track = cl::g("mysql")->query("SELECT id
										FROM track t 
										WHERE NOT EXISTS 
										(
											SELECT 1
											FROM queue q
											WHERE 
												q.track = t.id
										)
										AND id=$trackid") or debug::addLine("error", "Could not select track from the database, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);

		if(!$track || $track->num_rows == 0):
			return array(
					'result' => false,
					'message' => 'This track is already in queue'
				);
		endif;

		$track = cl::g("mysql")->query("SELECT id
										FROM track t 
										WHERE NOT EXISTS
										(
											SELECT 1
											FROM queue_history h
											WHERE
											h.track = t.id AND
											h.time_played > NOW() - INTERVAL ".QUEUE_TRACK_DELAY." MINUTE
										)
										AND id=$trackid") or debug::addLine("error", "Could not select track from the database, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);

		if(!$track || $track->num_rows == 0):
			return array(
					'result' => false,
					'message' => 'This track has been played recently, please try again later'
				);
		endif;

		$track = cl::g("mysql")->query("SELECT track
										FROM queue q
										WHERE
										q.track IN (
											SELECT track
											FROM track_artist a
											WHERE
											a.artist IN (
												SELECT artist
												FROM track_artist b
												WHERE
												b.track = $trackid
											)
										)") or debug::addLine("error", "Could not select track from the database, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);

		if(!$track || $track->num_rows > 0):
			return array(
					'result' => false,
					'message' => 'This artist is already in the queue'
				);
		endif;

		$track = cl::g("mysql")->query("SELECT track
										FROM queue_history h
										WHERE
										h.track IN (
											SELECT track
											FROM track_artist a
											WHERE
											a.artist IN (
												SELECT artist
												FROM track_artist b
												WHERE
												b.track = $trackid
											)
										)
										AND h.time_played > NOW() - INTERVAL ".QUEUE_ARTIST_DELAY." MINUTE") or debug::addLine("error", "Could not select track from the database, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);

		if(!$track || $track->num_rows > 0):
			return array(
					'result' => false,
					'message' => 'This artist has been recently played, please try again later'
				);
		endif;

		$track = cl::g("mysql")->query("SELECT track
										FROM queue_history h
										WHERE
										h.track IN (
											SELECT track
											FROM track_album a
											WHERE
											a.album IN (
												SELECT album
												FROM track_album b
												WHERE
												b.track = $trackid
											)
										)
										AND h.time_played > NOW() - INTERVAL ".QUEUE_ALBUM_DELAY." MINUTE") or debug::addLine("error", "Could not select track from the database, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);

		if(!$track || $track->num_rows > 0):
			return array(
					'result' => false,
					'message' => 'This album has been recently played, please try again later'
				);
		endif;

		$track = cl::g("mysql")->query("SELECT track
										FROM queue q
										WHERE
										q.track IN (
											SELECT track
											FROM track_album a
											WHERE
											a.album IN (
												SELECT album
												FROM track_album b
												WHERE
												b.track = $trackid
											)
										)
										AND q.time_requested > NOW() - INTERVAL ".QUEUE_ALBUM_DELAY." MINUTE") or debug::addLine("error", "Could not select track from the database, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);

		if(!$track || $track->num_rows > 0):
			return array(
					'result' => false,
					'message' => 'A track in this album is already requested, please try again later'
				);
		endif;

		return true;
	}

	/**
	 * Get a random (and queable) track from the database
	 * @return track
	 **/
	public function getRandomTrack() {
		$track = cl::g("mysql")->query("SELECT id
										FROM track t 
										WHERE NOT EXISTS 
										(
											SELECT 1
											FROM queue q
											WHERE 
												q.track = t.id
										)
										AND NOT EXISTS
										(
											SELECT 1
											FROM queue q
											WHERE
											q.track IN (
												SELECT track
												FROM track_artist a
												WHERE
												a.artist IN (
													SELECT artist
													FROM track_artist b
													WHERE
													b.track = t.id
												)
											)
										)
										AND NOT EXISTS
										(
											SELECT track
											FROM queue q
											WHERE
											q.track IN (
												SELECT track
												FROM track_album a
												WHERE
												a.album IN (
													SELECT album
													FROM track_album b
													WHERE
													b.track = t.id
												)
											)
										)
										AND NOT EXISTS
										(
											SELECT 1
											FROM queue_history h
											WHERE
											h.track = t.id AND
											h.time_played > NOW() - INTERVAL ".QUEUE_TRACK_DELAY." MINUTE
										)
										AND NOT EXISTS
										(
											SELECT 1
											FROM queue_history h
											WHERE
											h.track IN (
												SELECT track
												FROM track_artist a
												WHERE
												a.artist IN (
													SELECT artist
													FROM track_artist b
													WHERE
													b.track = t.id
												)
											)
											AND h.time_played > NOW() - INTERVAL ".QUEUE_ARTIST_DELAY." MINUTE
										)
										AND NOT EXISTS
										(
											SELECT track
											FROM queue_history h
											WHERE
											h.track IN (
												SELECT track
												FROM track_album a
												WHERE
												a.album IN (
													SELECT album
													FROM track_album b
													WHERE
													b.track = t.id
												)
											)
											AND h.time_played > NOW() - INTERVAL ".QUEUE_ALBUM_DELAY." MINUTE
										)
										ORDER BY RAND() LIMIT 1") or debug::addLine("error", "Could not select track from the database, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);

		return $this->getTrack($track->fetch_object()->id);
	}

	public function search($title = '', $page = 1, $limit = 20) {
		$title = cl::g("mysql")->mres($title);

		$page = (int) $page - 1;
		$limit = (int) $limit;

		$query = "SELECT SQL_CALC_FOUND_ROWS id FROM `track` WHERE title LIKE '%$title%' OR filename LIKE '%$title%' OR id IN (SELECT track FROM `track_artist` WHERE artist IN (SELECT id FROM `artist` WHERE name LIKE '%$title%')) LIMIT ".($page * $limit).", $limit";
		$query = cl::g("mysql")->query($query) or debug::addLine("error", "could not execute search query", __FILE__, __LINE__);

		$result = array(
					"data" 			=> array(),
					"results" 		=> 0,
					"totalresults" 	=> 0
				);

		if(!$query || $query->num_rows == 0):
			return $result;
		endif;

		$result = array(
				'albums' => array(),
				'artists' => array(),
				'tracks' => array(),
				'searches' => array(),
			);

		$total = cl::g("mysql")->query("SELECT FOUND_ROWS()") or debug::addLine("error", "could not get total found rows", __FILE__, __LINE__);
		$total = $total->fetch_assoc();

		while($searchData = $query->fetch_object()):
			$temp = $this->getTrack($searchData->id)->toEmberArray();

			foreach($temp['albums'] as $album) $result['albums'][$album['id']] = $album;
			foreach($temp['artists'] as $artist) $result['artists'][$artist['id']] = $artist;
			$result['tracks'][$temp['track']['id']] = $temp['track'];
			$result['searches'][$temp['track']['id']] = $temp['track']['id'];
		endwhile;

		$result['tracks'] = array_values($result['tracks']);
		$result['albums'] = array_values($result['albums']);
		$result['artists'] = array_values($result['artists']);
		$result['searches'] = array_values($result['searches']);
		$result['totalresults'] = $total['FOUND_ROWS()'];

		return $result;
	}

	/**
	 * Add a track to the database
	 * @param track track
	 * @return int the track-ID in the database
	 **/
	public function addTrack(track $track) {
		if(!is_object($track)) return false;

		//Check if the song already exists in the database
		$query = cl::g("mysql")->query("SELECT id FROM `track` WHERE crc='{$track->getFile()->getCRC()}' OR filename='{$track->getFile()->getFileName()}'") or debug::addLine("error", "Could not select track from the database, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
		if($query->num_rows > 0):
			$query = $query->fetch_object();
			$track->setId($query->id);
		else:
			//Song doesn't exist yet, create it
			$query = cl::g("mysql")->query("INSERT INTO `track` (title, length, crc, filename) VALUES ('".addslashes($track->getTitle())."', {$track->getFile()->getLength()}, '{$track->getFile()->getCRC()}', '".addslashes($track->getFile()->getFileName())."')") or debug::addLine("error", "Could not add track to the database, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);

			//Make sure the query worked
			if(!$query || cl::g("mysql")->insert_id == 0) return false;

			//Set the track-id to match it's database ID
			$track->setId(cl::g("mysql")->insert_id);
		endif;

		//Check every artist if they exist or have to be created before connecting them to the track
		foreach($track->getArtist() as $artist):
			if($artist instanceof artist) $this->connectArtist($track, $artist);
		endforeach;

		//Check every album if it exists or have to be created before connecting it to the track
		foreach($track->getAlbum() as $album):
			if($album instanceof album) $this->connectAlbum($track, $album);
		endforeach;
	}

	/**
	 * Connect an artist to a track
	 * @param track track
	 * @param artist artist
	 * @return boolean
	 **/
	public function connectArtist(track $track, artist $artist) {
		if($artist->getId() == 0) $artist->setId(cl::g("artists")->addArtist($artist));
		$query = cl::g("mysql")->query("INSERT IGNORE INTO `track_artist` (track, artist) VALUES ({$track->getId()}, {$artist->getId()}) ON DUPLICATE KEY UPDATE track=track") or debug::addLine("error", "Could not connect artist ".$artist->getId()." with track ".$track->getId()." to the track, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
		return ($query && $query->affected_rows > 0);
	}

	/**
	 * Connect an album to a track
	 * @param track track
	 * @param album album
	 * @return boolean
	 **/
	public function connectAlbum(track $track, album $album) {
		if($album->getId() == 0) $album->setId(cl::g("albums")->addAlbum($album));
		$query = cl::g("mysql")->query("INSERT IGNORE INTO `track_album` (track, album) VALUES ({$track->getId()}, {$album->getId()}) ON DUPLICATE KEY UPDATE track=track") or debug::addLine("error", "Could not connect album ".$artist->getId()." with track ".$track->getId()." to the track, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
		return ($query && $query->affected_rows > 0);
	}

	/**
	 * Remove a track (both the file and the database references)
	 * @param trackid int
	 * @return boolean
	 **/
	public function removeTrack($trackid) {
		$data = cl::g("mysql")->query("SELECT filename FROM `track` WHERE id=$trackid");
		if(!$data || $data->num_rows == 0):
			debug::addLine("error", "Could not get track filename, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
		else:
			$data = $data->fetch_object();

			cl::g("mysql")->autocommit(false);

			try {
				$query = cl::g("mysql")->query("DELETE FROM `track_album` WHERE track=$trackid");
				if(!$query) throw new Exception(cl::g("mysql")->getError());
				$query = cl::g("mysql")->query("DELETE FROM `track_artist` WHERE track=$trackid");
				if(!$query) throw new Exception(cl::g("mysql")->getError());
				$query = cl::g("mysql")->query("DELETE FROM `queue` WHERE track=$trackid");
				if(!$query) throw new Exception(cl::g("mysql")->getError());
				$query = cl::g("mysql")->query("DELETE FROM `queue_history` WHERE track=$trackid");
				if(!$query) throw new Exception(cl::g("mysql")->getError());
				$query = cl::g("mysql")->query("DELETE FROM `track` WHERE id=$trackid");
				if(!$query) throw new Exception(cl::g("mysql")->getError());

			} catch(Exception $e) {
				debug::addLine("error", "Could not delete track, error: ".$e->getMessage(), __FILE__, __LINE__);
				cl::g("mysql")->rollback();
			}

			cl::g("mysql")->commit();
			cl::g("mysql")->autocommit(true);

			if($query):
				unlink(MUSIC_UPLOAD_DIR . $data->filename);
				return true;
			endif;
		endif;

		return false;
	}
}