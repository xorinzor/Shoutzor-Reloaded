<?php

class artists {
	/**
	 * Retrieves an artist from the database and returns its object
	 * @param id integer
	 * @return object:artist
	 */
	public function getArtist($id) {
		if(!ctype_digit($id) || $id < 1) return null;

		$query = cl::g("mysql")->query("SELECT * FROM `artist` WHERE id=$id");
		if(!$query || $query->num_rows == 0):
			debug::addLine("error", "Could not retrieve artist from the database, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
			return false;
		endif;

		$artistData = $query->fetch_object();

		//Create artist object
		$artist = new artist();
		$artist->setId($artistData->id)
				->setName(stripslashes($artistData->name))
				->setProfileImage(stripslashes($artistData->profileimage));

		//Return the artist object
		return $artist;
	}

	/**
	 * Add an artist to the database
	 * @param artist artist
	 * @return int the artist-ID in the database
	 **/
	public function addArtist(artist $artist) {
		if(!is_object($artist)) return false;

		$query = cl::g("mysql")->query("SELECT id FROM `artist` WHERE name='".addslashes($artist->getName())."'") or debug::addLine("error", "Could not select artist from the database, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
		if(!$query) return false;
		if($query->num_rows > 0):
			$query = $query->fetch_object();
			return $query->id;
		endif;

		$query = cl::g("mysql")->query("INSERT INTO `artist` (name, profileimage) VALUES ('".addslashes($artist->getName())."', '".addslashes($artist->getProfileImage())."')") or debug::addLine("error", "Could not add artist to the database, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);

		//Make sure the query worked
		if(!$query || cl::g("mysql")->insert_id == 0) return false;

		//Return the artists database ID
		return cl::g("mysql")->insert_id;
	}
}