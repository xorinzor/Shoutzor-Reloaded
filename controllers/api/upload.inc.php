<?php

	header("Content-Type: application/octet-stream");

	#include the indexer class
	require_once(PHP_PATH . "core/indexer.class.php");

	class uploadFile {
		public function __CONSTRUCT() {
		}

		private function getCRC($file) {
			return hash_file('crc32b', $file['tmp_name']);
		}

		private function getTrackLength($file) {
			// Execute the command
	        $strResult = exec('ffmpeg -i "' . $file['tmp_name'] . '" 2>&1', $arrResult);
	        
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
	                
	                // Return the playtime
	                return $intTotal;
	        }

	        debug::addLine("debug", "Could not determine track length for track: ".$file['tmp_name'], __FILE__, __LINE__);
	        return 0;
		}

		private function checkFile($file) {
			if(file_exists(MUSIC_UPLOAD_DIR . $file['name'])) return false;

			$crc = $this->getCRC($file);

			$query = cl::g("mysql")->query("SELECT id FROM `track` WHERE crc='$crc'") or debug::addLine("error", "Could not select track from the database, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
			if(!$query || $query->num_rows > 0) return false;

			//Make sure track is not longer as 6 minutes and not shorter as 30 seconds
			if($this->getTrackLength($file) < 30) return false;
			if($this->getTrackLength($file) > (60*6)) return false;

			return true;
		}

		private function checkUploadStatus($file) {
			$message = 'Error uploading file';
			switch( $file['error'] ) {
				case UPLOAD_ERR_OK:
					return true;
					break;
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					$message .= ' - file too large.';
					break;
				case UPLOAD_ERR_PARTIAL:
					$message .= ' - file upload was not completed.';
					break;
				case UPLOAD_ERR_NO_FILE:
					$message .= ' - zero-length file uploaded.';
					break;
				default:
					$message .= ' - internal error #'.$file['error'];
					break;
			}

			return $message;
		}

		public function upload($file) {
			$file['name'] = preg_replace("([^a-zA-Z0-9-_. \(\)])","",$file['name']);

			$uploadCheck = $this->checkUploadStatus($file);
			if($uploadCheck !== true) return $uploadCheck;
			if(!$this->checkFile($file)) return 'Error while uploading file'; //Dont tell the user the file already exists, they might try to avoid the security

			if( !is_uploaded_file($file['tmp_name']) ) {
				$message = 'Error uploading file - unknown error.';
			} else {
				// Let's see if we can move the file...
				$dest = MUSIC_UPLOAD_DIR . $file['name'];

				if(!move_uploaded_file($file['tmp_name'], $dest)) { // No error supporession so we can see the underlying error.
					$message = 'Error uploading file - could not save upload (this will probably be a permissions problem in '.$dest.')';
				} else {
					/* Index the file */
					$indexer = new indexer;
					$indexer->index_file($file['name']);

					$message = 'File uploaded okay.';
				}
			}

			return $message;
		}
	}

	$upload = new uploadFile;
	$result = $upload->upload($_FILES['audiofile']);

	die($result);