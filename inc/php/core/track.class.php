<?php

class track {

	private $id = 0;
	private $title;
	private $artist;
	private $album;
	private $file;
	
	/**
	 * getter functions
	 * these can be used to return the instance variables
	 **/
	public function getId() 	{ return $this->id; 	}
	public function getTitle() 	{ return $this->title; 	}
	public function getArtist() { return $this->artist; }
	public function getAlbum() 	{ return $this->album; 	}
	public function getFile() 	{ return $this->file; 	}

	/**
	 * sets the ID for the track
	 * @param id integer
	 **/
	public function setId($id) {
		$this->id = (int) $id;
		return $this;
	}

	/**
	 * sets the title for the track
	 * @param title string
	 **/
	public function setTitle($title) {
		$this->title = (string) $title;
		return $this;
	}

	/**
	 * sets the artist(s) for the track
	 * @param artist array
	 **/
	public function setArtist(array $artist) {
		$this->artist = $artist;
		return $this;
	}

	/**
	 * sets the album for the track
	 * @param album array
	 **/
	public function setAlbum(array $album) {
		$this->album = $album;
		return $this;
	}

	/**
	 * sets the file for the track
	 * @param file trackFile
	 **/
	public function setFile(trackFile $file) {
		$this->file = $file;
		return $this;
	}

	public function toArray() {
		$result = array(
				'id' 		=> $this->getId(),
				'title' 	=> $this->getTitle(),
				'file' 		=> $this->getFile()->toArray(),
				'artist' 	=> array(),
				'album' 	=> array()
			);

		foreach($this->getArtist() as $artist) {
			$result['artist'][] = $artist->toArray();
		}

		foreach($this->getAlbum() as $album) {
			$result['album'][] = $album->toArray();
		}

		return $result;
	}

	public function toEmberArray() {
		$track = array(
				'id' 			=> $this->getId(),
				'title' 		=> $this->getTitle(),
				'length' 		=> $this->getFile()->getLength(),
				'artist' 		=> '',
				'album' 		=> ''
			);

		foreach($this->getArtist() as $artist) {
			$track['artist'][] = $artist->getId();
		}

		foreach($this->getAlbum() as $album) {
			$track['album'][] = $album->getId();
		}

		$artists = array();
		$albums = array();

		foreach($this->getArtist() as $artist) {
			$artists[$artist->getId()] = $artist->toArray();
		}

		foreach($this->getAlbum() as $album) {
			$temp = $album->toEmberArray();
			$albums[$temp['album']['id']] = $temp['album'];
			$artists[$temp['artist']['id']] = $temp['artist'];
		}

		return array(
				'track' => $track,
				'artists' => $artists,
				'albums' => $albums
			);
	}
}