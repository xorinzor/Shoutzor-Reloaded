<?php

class albums {
	/**
	 * Retrieves an album from the database and returns its object
	 * @param id integer
	 * @return object:album
	 */
	public function getAlbum($id) {
		if(!ctype_digit($id) || $id < 1) return null;

		$query = cl::g("mysql")->query("SELECT * FROM `album` WHERE id=$id");
		if(!$query || $query->num_rows == 0):
			debug::addLine("error", "Could not retrieve album from the database, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
			return false;
		endif;

		$albumData = $query->fetch_object();

		//Retrieve artist object for the album
		$artist = cl::g("artists")->getArtist($albumData->artist);

		if($artist == null) return null;

		//Create album object
		$album = new album();
		$album->setId($albumData->id)
				->setTitle($albumData->title)
				->setArtist($artist)
				->setCoverImage($albumData->coverimage);

		//Return the album object
		return $album;
	}

	/**
	 * Add a album to the database
	 * @param album album
	 * @return int the track-ID in the database
	 **/
	public function addAlbum(album $album) {
		if(!is_object($album)) return false;
		if(!is_object($album->getArtist())) return false;

		if($album->getArtist()->getId() == 0) $album->getArtist()->setId(cl::g("artists")->addArtist($album->getArtist()));

		$query = cl::g("mysql")->query("SELECT id FROM `album` WHERE title='{$album->getTitle()}'") or debug::addLine("error", "Could not select album from the database, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
		if(!$query) return false;
		if($query->num_rows > 0):
			$query = $query->fetch_object();
			return $query->id;
		endif;

		$query = cl::g("mysql")->query("INSERT IGNORE INTO `album` (artist, title, coverimage) VALUES ({$album->getArtist()->getId()}, '{$album->getTitle()}', '{$album->getCoverImage()}')") or debug::addLine("error", "Could not add album to the database, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);

		//Make sure the query worked
		if(!$query->affected_rows == 0) return false;

		//Return the artists database ID
		return cl::g("mysql")->insert_id;
	}
}