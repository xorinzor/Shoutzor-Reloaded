<?php

class trackFile {

	private $file;
	private $filename;
	private $length;

	public function __CONSTRUCT($file) {
		if(!file_exists(MUSIC_UPLOAD_DIR . $file)) throw new exception("File not found");
		$this->filename = $file;
		$this->file 	= MUSIC_UPLOAD_DIR . $file;
	}

	/**
	 * Return the filename
	 * @return string
	 **/
	public function getFileName() {
		return $this->filename;
	}

	/**
	 * Return the full file path
	 * @return string
	 **/
	public function getFilePath() {
		return $this->file;
	}

	/**
	 * Calculate and return the CRC hash of the track
	 * @return string
	 **/
	public function getCRC() {
		return hash_file('crc32b', $this->file);
	}

	/**
	 * checks if the music file is an MP3 file, if it is, retrieve any possible ID3 tags and return these, else return an empty array
	 * since only MP3 files can contain ID3 tags we need to have this check.
	 * @return array
	 **/
	public function getID3() {
		// Initialize getID3 engine
		$getID3 = new getID3;
		$getID3->option_md5_data        = true;
		$getID3->option_md5_data_source = true;
		$getID3->encoding               = 'UTF-8';

		// Analyze file
		$info = $getID3->analyze($this->file);
		getid3_lib::CopyTagsToComments($info);

		$result = array(
				'title' 	=> 'Untitled',
				'artist' 	=> array(),
				'album' 	=> array()
			);

		if(!isset($info['error'])):
			if(!empty($info['comments_html']['title'][0])): 
				$result['title'] = ucwords(preg_replace("/[a-zA-Z]*[:\/\/]*[A-Za-z0-9\-_]+\.+[A-Za-z0-9\.\/%&=\?\-_]+/i", '', html_entity_decode(strtolower($info['comments_html']['title'][0]))));
			endif;

			if(is_array($info['comments_html']['artist'])):
				foreach($info['comments_html']['artist'] as $artist):
					$artist = preg_replace("/[a-zA-Z]*[:\/\/]*[A-Za-z0-9\-_]+\.+[A-Za-z0-9\.\/%&=\?\-_]+/i", '', $artist);
					$artist = preg_replace('/(\(\)|\[\])/', '', $artist);

					if(!empty($artist)):
						$artist = ucwords(html_entity_decode(strtolower($artist)));
						$artist = preg_split('/\s*(Feat\.|Vs\.|Ft\.)\s*/', $artist);
						$artist = implode(" /// ", $artist);
						$artist = preg_split('/\s*(&|Feat|Vs|Ft|\/\/\/)\s*/', $artist);

						foreach($artist as $a):
							$result['artist'][] = array(
									'name' => html_entity_decode($a)
								);
						endforeach;
					endif;
				endforeach;
			endif;

			if(is_array($info['comments_html']['album'])):
				$a = 0;
				foreach($info['comments_html']['album'] as $album):
					$album = preg_replace("/[a-zA-Z]*[:\/\/]*[A-Za-z0-9\-_]+\.+[A-Za-z0-9\.\/%&=\?\-_]+/i", '', $album);
					$album = preg_replace('/(\(\)|\[\])/', '', $album);

					if(!empty($album)):
						$result['album'][] = array(
								'title' => html_entity_decode($album),
								'cover' => (isset($info['comments']['picture'][$a]['data'])) ? $info['comments']['picture'][$a]['data'] : null
							);
					endif;
					$a++;
				endforeach;
			endif;
		endif;

		return $result;
	}

	public function setLength($length) {
		$this->length = $length;
		return $this;
	}

	/**
	 * Get the audio file playlength
	 **/
	public function getLength() {
		if($this->length > 0) return $this->length;

        // Execute the command
        $strResult = exec('ffmpeg -i "' . $this->file . '" 2>&1', $arrResult);
        
        // Successful executed
        if($strResult) {
                // Result variable
                $intTotal = 0;
                
                // Check each line of the result
                foreach($arrResult as $strInfo) {
                        // Duration line found
                        if(preg_match('/Duration: ((\d+):(\d+):(\d+))/s', $strInfo, $arrFound)) {
                                // Save the playtime in seconds
                                $intTotal = ($arrFound[2] * 3600) + ($arrFound[3] * 60) + $arrFound[4];
                        }
                }
                
                $this->setLength($intTotal);

                // Return the playtime
                return $intTotal;
        }
        
        // Execution failed
        return 0;
	}

	public function isValid() {
		$strResult = exec('ffmpeg -i "' . $this->file . '" 2>&1 > /dev/null | grep \'\' | tail -n +5 | head -2 >> '. LOG_PATH . "ffmpeg_log.txt", $arrResult);
		foreach($arrResult as $strInfo):
			if(strpos($strInfo, 'Header missing') !== false) {
				debug::addLine("ERROR", "File: {$this->file} is corrupt", __FILE__, __LINE__);
				return false;
			}
		endforeach;

		return true;
	}

	public function toArray() {
		return array(
				'filename' 	=> $this->getFileName(),
				'filepath' 	=> $this->getFilePath(),
				'crc' 		=> $this->getCRC(),
				'length' 	=> $this->getLength(),
				'id3' 		=> $this->getID3()
			);
	}
}