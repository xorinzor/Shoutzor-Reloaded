<?php

class indexer {

	private $scandir;
	private $recursive;

	public function __CONSTRUCT() {
		$this->scandir 		= MUSIC_UPLOAD_DIR;
		$this->recursive 	= FALSE;

		debug::addLine("debug", "indexer running", __FILE__, __LINE__);
	}

	public function getScanDir() 		{ return $this->scandir; }
	public function getScanRecursive() 	{ return $this->recursive; }

	/**
	 * Sets where the indexer should scan for files
	 * @param scandir string
	 * @return instance
	 **/
	public function setScanDir($scandir) {
		if(!$this->directory_exists($scandir)) throw new exception("Invalid scan directory");
		$this->scandir = (string) $scandir;
		return $this;
	}

	/**
	 * Sets whether the indexer should scan the directory recursively or not
	 * @param recursive boolean
	 * @return instance
	 **/
	public function setScanRecursive($recursive) {
		$this->recursive = ($recursive === true);
		return $this;
	}

	/**
	 * Checks wether the given directory exists
	 * @param dir string
	 * @return boolean
	 **/
	private function directory_exists($dir) {
		//Clear PHP cache
		clearstatcache();

		return is_dir(MUSIC_UPLOAD_DIR . $dir);
	}

	/**
	 * Indexes a file
	 * @param file string
	 **/
	public function index_file($file) {
    	$trackFile 	= new trackFile($file);
    	$track 		= new track();
    	$artist 	= null;
    	$album 		= null;

    	$id3 = $trackFile->getID3();

    	if(!$trackFile->isValid()) {
    		return false;
    	}

   		$artists = array();
   		$albums = array();

    	if(count($id3['artist']) > 0):
	    	foreach($id3['artist'] as $artist):
	    		$temp = new artist();
	    		$temp->setName($artist['name']);
	    		$artists[] = $temp;
		    endforeach;


			if(!empty($id3['album']) && (count($artists) > 0)):
				foreach($id3['album'] as $album):
					if($album['cover'] != null):
						$coverimagepath = UPLOADS_PATH . "albums/";
						$coverimage = md5($album['title']).".jpg";
						if(file_exists($coverimage)):
							$result = true;
						else:
							if(!is_dir($coverimagepath)):
								debug::addLine("FATAL", "Directory does not exist", __FILE__, __LINE__);
							elseif(!is_writable($coverimagepath)):
								debug::addLine("FATAL", "Directory is not writable", __FILE__, __LINE__);
							endif;
							$result = (file_put_contents($coverimagepath . $coverimage, $album['cover']) > 0);
						endif;
					endif;

					$temp = new album();
					$temp->setTitle($album['title'])
							->setArtist($artists[0]);

					if(($album['cover'] != null) && ($result == true))	$temp->setCoverImage($coverimage);

					$albums[] = $temp;
				endforeach;
			endif;
    	endif;


    	$track->setTitle($id3['title'])
				->setArtist($artists)
				->setAlbum($albums)
				->setFile($trackFile);

		cl::g("tracks")->addTrack($track);

		return true;
	}

	public function scan() {
		$directoryContent = $this->scanDirectory();

		foreach($directoryContent as $item) {
			if($this->index_file($item) === false):
				unlink(MUSIC_UPLOAD_DIR . $item);
			endif;
		}
	}

	public function scanForBrokenTracks() {
		$directoryContent = $this->scanDirectory();

		$query = cl::g("mysql")->query("SELECT id, filename FROM `track`");
		if(!$query || $query->num_rows == 0) return false;

		//Create array of files from the DB
		$dbtracks = array();
		while($track = $query->fetch_object()) $dbtracks[$track->id] = $track->filename;

		//Will return an array containing all filenames (with their ID as key) who are not present in the musicdirectory
		$toRemove = array_diff($dbtracks, $directoryContent);

		foreach($toRemove as $trackid=>$filename) {
			cl::g("tracks")->removeTrack($trackid);
		}
	}

	public function scanDirectory($directory = '') {
		$directoryContent = array_diff(scandir($this->scandir . '/' . $directory), array('..', '.'));

		foreach($directoryContent as $item) {
			if(is_file($this->scandir . $directory . $item)):
				$directoryContent[] = $directory . $item;
			elseif($this->recursive === true && $this->directory_exists($this->scandir . '/' . $directory . $item)):
				//Scan recurisve
				$directoryContent = array_merge($directoryContent, $this->scanDirectory($directory . '/' . $item));
			else:
				//Skip directory or is not a file (for example: symlinks)
			endif;
		}

		return $directoryContent;
	}
}