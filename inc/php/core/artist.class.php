<?php

class artist {

	private $id = 0;
	private $name;
	private $profileimage;

	/**
	 * getter functions
	 * these can be used to return the instance variables
	 **/
	public function getId() 			{ return $this->id; 			}
	public function getName() 			{ return $this->name; 			}
	public function getProfileImage() 	{ return $this->profileimage; 	}

	/**
	 * sets the ID for the artist
	 * @param id integer
	 **/
	public function setId($id) {
		$this->id = (int) $id;
		return $this;
	}

	/**
	 * sets the name for the artist
	 * @param name string
	 **/
	public function setName($name) {
		$this->name = (string) $name;
		return $this;
	}

	/**
	 * sets the profileimage for the artist
	 * note: this should be filename only, not the full path!
	 * @param profileimage string
	 **/
	public function setProfileImage($profileimage) {
		$this->profileimage = (string) $profileimage;
		return $this;
	}

	public function toArray() {
		return array(
				'id' 			=> $this->getId(),
				'name' 			=> $this->getName(),
				'profileImage' 	=> $this->getProfileImage()
			);
	}
}