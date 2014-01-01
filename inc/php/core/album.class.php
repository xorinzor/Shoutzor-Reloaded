<?php

class album {

	private $id = 0;
	private $title;
	private $artist;
	private $coverimage;

	/**
	 * getter functions
	 * these can be used to return the instance variables
	 **/
	public function getId() 			{ return $this->id; 			}
	public function getTitle() 			{ return $this->title;			} 
	public function getArtist() 		{ return $this->artist;			}
	public function getCoverImage() 	{ return $this->coverimage; 	}

	/**
	 * sets the ID for the album
	 * @param id integer
	 **/
	public function setId($id) {
		$this->id = (int) $id;
		return $this;
	}

	/**
	 * sets the title for the album
	 * @param title string
	 **/
	public function setTitle($title) {
		$this->title = (string) $title;
		return $this;
	}

	/**
	 * sets the artist for the album
	 * @param artist artist
	 **/
	public function setArtist(artist $artist) {
		$this->artist = $artist;
		return $this;
	}

	/**
	 * sets the coverimage for the album
	 * note: this should be filename only, not the full path!
	 * @param coverimage string
	 **/
	public function setCoverImage($coverimage) {
		$this->coverimage = (string) $coverimage;
		return $this;
	}

	public function toArray() {
		return array(
				'id' 			=> $this->getId(),
				'title' 		=> $this->getTitle(),
				'coverImage' 	=> $this->getCoverImage(),
				'artist' 		=> $this->getArtist()->toArray()
			);
	}

	public function toEmberArray() {
		$coverimage = $this->getCoverImage();
		$coverimage = (empty($coverimage)) ? '' : SITEURL . "static/uploads/albums/" . $this->getCoverImage();

		$album = array(
				'id' 			=> $this->getId(),
				'title' 		=> $this->getTitle(),
				'coverImage' 	=> $coverimage,
				'artist' 		=> $this->getArtist()->getId()
			);

		return array(
				'album' => $album,
				'artist' => $this->getArtist()->toArray()
			);
	}
}