<?php

class packageManager {
	private $repository;
	private $packages;
	private $installed;
	public $downloaded;
	public $downloadPercentage;

	private $cacheListPackages;

	public function __CONSTRUCT() {
		$this->downloaded 			= 0;
		$this->downloadPercentage 	= 0;
		$this->repository 			= array();
		$this->packages 			= array();
		$this->installed 			= array();
		$this->cacheListPackages 	= array();

		$this->updateRepositoryList();
		$this->updatePackageAvailableList();
		$this->updatePackageInstalledList();
	}

	/**
	 * Repopulate the local repository variable
	 */
	private function updateRepositoryList() {
		$repository = cl::g("mysql")->query("SELECT * FROM `".SQL_PREFIX."pkg_repositories`") or cl::g("debug")->showFatalPage("fatalerror");
		$this->repository = array();
		while($row = $repository->fetch_assoc()) 	$this->repository[$row['id']] = $row;
	}

	/**
	 * Repopulate the local packages variable
	 */
	private function updatePackageAvailableList() {
		$packages 	= cl::g("mysql")->query("SELECT * FROM `".SQL_PREFIX."pkg_packagelist`") or cl::g("debug")->showFatalPage("fatalerror");
		$this->packages = array();
		while($row = $packages->fetch_assoc())		$this->packages[$row['name']] = $row;
	}

	/**
	 * Repopulate the local installed variable
	 */
	private function updatePackageInstalledList() {
		$installed 	= cl::g("mysql")->query("SELECT * FROM `".SQL_PREFIX."pkg_installed`") or cl::g("debug")->showFatalPage("fatalerror");
		$this->installed = array();
		while($row = $installed->fetch_assoc()) 	$this->installed[$row['id']] = $row;
	}

	/**
	 * Check if a package exists
	 */
	private function packageExists($packageName) {
		return isset($this->packages[$packageName]);
	}

	/**
	 * Install the given package if it exists
     * @permission packagemanager_global
     * @permission packagemanager_install
	 */
	public function installPackage($packageName) {
        if(!cl::g("session")->hasPermission('packagemanager_global')) throw new permissionException(permissionException::INSUFFICIENT_PERMISSIONS);
        if(!cl::g("session")->hasPermission('packagemanager_install')) throw new permissionException(permissionException::INSUFFICIENT_PERMISSIONS);
        
		$packageName = strtolower($packageName);
		$packageParts = parse_url($packageName);
		$filters = explode("?", $parts['query']);

		$packageName = $packageParts['path'];

		if(!$this->packageExists($packageName)) return false;

		//Get package information
		$package = $this->packages[$packageName];
		$pid = $this->getPackageId($packageName);

		if(isset($this->installed[$package['id']])):
			if($this->version_compare($package['version'], $this->installed[$package['id']]['version'])):
				echo "Package '".$packageName."' is already installed, a update is available, use pkg-upgrade to update it".PHP_EOL;
			else:
				echo "Package '".$packageName."' is already installed and is the latest version".PHP_EOL;
			endif;
			return false;
		endif;

		echo "Checking for dependencies for '$packageName'..".PHP_EOL;

		//Check for dependencies
		$query = cl::g("mysql")->query("SELECT * FROM `".SQL_PREFIX."pkg_dependencylist` WHERE pid='$pid'") or cl::g("debug")->addLine("error", "mysql error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
		if($query->num_rows > 0):
			echo "Dependencies found, Installing..".PHP_EOL;
			//Install dependencies
			while($row = $query->fetch_assoc()):
                try {
    				$this->installPackage($row['name']);
                } catch(Exception $e) {
                    //pass the error on
                    throw $e;
                }
			endwhile;
		else:
			echo "Installing package '".$packageName."'..".PHP_EOL;
			//No dependencies, continue installation of the packet
			echo "Downloading package..".PHP_EOL;
            
            try {
    			$result = $this->downloadPackage($package);
            } catch(Exception $e) {
                //pass the error on
                throw $e;
            }
            
			echo "Done".PHP_EOL;

			if(!$result):
				cl::g("debug")->addLine("ERROR", "Could not install package '".$package['name']."', download failed!", __FILE__, __LINE__);
				return false;
			endif;

			echo "Extracting contents..".PHP_EOL;
			//Get package from the temp directory
			$zip = new ZipArchive;
			$res = $zip->open(TEMP_PATH . $pid.'.zip');
			if($res === TRUE):
				$zip->extractTo(PACKAGES_PATH . $pid. "/");
				$zip->close();
				echo "Done".PHP_EOL;

				echo "Installing package..";
				$query = cl::g("mysql")->query("INSERT INTO `".SQL_PREFIX."pkg_installed` (id, version) VALUES ('$pid', '".$package['version']."')") or cl::g("debug")->addLine("error", "mysql error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
				if(!$query):
					echo "An error occured while installing the package, any changes will be undone";
					rmdir(PACKAGES_PATH . $pid ."/");
				else:
					echo "Done";
				endif;
			else:
				cl::g("debug")->addLine("ERROR", "Could not install package '".$package['name']."': unzip failed! (code: $res)", __FILE__, __LINE__);
				echo "Failed! Please try again, if this error persists check the write permissions of the '".TEMP_PATH."' directory".PHP_EOL;
			endif;

			unlink(TEMP_PATH . $pid.".zip"); //remove ZIP file from package
		endif;
	}

	/**
	 * Downloads a package from the repository
     * @permission packagemanager_global
     * @permission packagemanager_install
	 */
	private function downloadPackage($package) {
        if(!cl::g("session")->hasPermission('packagemanager_global')) throw new permissionException(permissionException::INSUFFICIENT_PERMISSIONS);
        if(!cl::g("session")->hasPermission('packagemanager_install')) throw new permissionException(permissionException::INSUFFICIENT_PERMISSIONS);
        
		//Get the server to fetch the package from
		$repository = $this->repository[$package['sid']]['host'];
		$packageId = $this->getPackageId($package['name']);
        
        try {
    		$this->downloadFile($repository . "packages/" . $packageId.".zip", TEMP_PATH . $packageId.".zip");
        } catch(Exception $e) {
            //pass the error on
            throw $e;
        }

		return file_exists(TEMP_PATH . $packageId.".zip") == true;
	}

	/**
	 * Function for downloading a file
     * @permission filedownload
	 */
	private function downloadFile($downloadFilePath, $saveFilePath, $presentation = '') {
        if(!cl::g("session")->hasPermission('filedownload')) throw new permissionException(permissionException::INSUFFICIENT_PERMISSIONS);
        
		if (!ini_get('safe_mode') && strpos(ini_get('disable_functions'), 'set_time_limit') === FALSE) {
			set_time_limit(0); //Prevent PHP timeout
		}
        
        $starttime = microtime(true);

		$fp = fopen ($saveFilePath, 'w+');//This is the file where we save the    information
		$ch = curl_init(str_replace(" ","%20",$downloadFilePath));//Here is the file we are downloading, replace spaces with %20

		curl_setopt($ch, CURLOPT_TIMEOUT, 50);
		curl_setopt($ch, CURLOPT_FILE, $fp); // here it sais to curl to just save it
		curl_setopt($ch, CURLOPT_BUFFERSIZE, 128);

		if(strlen($presentation)>0):
            echo sprintf($presentation, 0, 0) . PHP_EOL;;
            
			curl_setopt($ch, CURLOPT_NOPROGRESS,false);
			curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function($download_size, $downloaded, $upload_size, $uploaded) use ($presentation, $starttime) {

				$percentage = (double) (($downloaded == 0) || ($download_size == 0)) ? 0.0 : round($downloaded / $download_size * 100, 2);
				$message = sprintf($presentation, $percentage, round(($downloaded / (microtime(true) - $starttime) * 8 / 1024), 2)) . PHP_EOL;

				if(cl::g("pkg")->downloadPercentage != $percentage):
					cl::g("pkg")->downloadPercentage = $percentage;
					cl::g("pkg")->downloaded = $downloaded;
					echo $message;
				endif;
			});
		endif;

		curl_exec($ch);//get curl response
		curl_close($ch);
		fclose($fp);
        
	}

	/**
	 * Update the packagelist from the given repository
     * @permission packagemanager_global
	 * @param string repository the repository to download the list from
	 */
	private function updateList($repository) {
        if(!cl::g("session")->hasPermission('packagemanager_global')) throw new permissionException(permissionException::INSUFFICIENT_PERMISSIONS);
        
        //Get the repository ID for the current list (will return false if it doesnt exists)
		$sid = $this->getRepositoryId($repository);
        
        //If the repository doesn't exists, return Exception, the user will have to add it manually
        //normally this wouldn't be able to happen since the packagelist is connected to the repository, therefor it should exist.
        if($sid == false) throw new packageManagerException(packageManagerException::REPOSITORY_DOESN_EXIST);        

        try {
    		$this->downloadFile($repository . "packagelist.xml", TEMP_PATH . 'packagelist.xml', 'Downloading list from '.$repository.'.. %1$d&#37; [%2$skb/s]');
        } catch(Exception $e) {
            //pass the error on
            throw $e;
        }

		$xmlstr = file_get_contents(TEMP_PATH . 'packagelist.xml');
		$xmlcont = new SimpleXMLElement($xmlstr);

		foreach($xmlcont->package as $package):
			$this->cacheListPackages[strtolower($package->title)] = $package;
			$this->cacheListPackages[strtolower($package->title)]->sid = $sid;
		endforeach;

		unlink(TEMP_PATH . 'packagelist.xml'); //remove packagelist
	}

	/**
	 * Update the stored packagelist, redownload the list from every configured repository
     * @permission packagemanager_global
	 */
	public function updatePackageList() {
        if(!cl::g("session")->hasPermission('packagemanager_global')) throw new permissionException(permissionException::INSUFFICIENT_PERMISSIONS);
        
		if(count($this->repository) == 0):
			throw new packageManagerException(packageManagerException::NO_REPOSITORY_AVAILABLE);
		else:
			echo 'Downloading package lists..'.PHP_EOL;

			foreach($this->repository as $repository):
                try {
    				$this->updateList($repository['host']);
                } catch(Exception $e) {
                    //pass the error on
                    throw $e;
                }
			endforeach;

			echo "Updating packages..".PHP_EOL;

			foreach($this->cacheListPackages as $package):
                try {
    				$this->addPackage($package);
                } catch(Exception $e) {
                    //pass the error on
                    throw $e;
                }
			endforeach;

			echo "Done".PHP_EOL;

			echo "Reloading Package cache..".PHP_EOL;
            
    		$this->updateRepositoryList();
    		$this->updatePackageAvailableList();
    		$this->updatePackageInstalledList();
            
			echo "Done";
		endif;
	}

    /**
     * Search for a package in the packagelist
     * @permission packagemanager_global
     * @param packageName the package search query
     */
	public function search($packageName) {
        if(!cl::g("session")->hasPermission('packagemanager_global')) throw new permissionException(permissionException::INSUFFICIENT_PERMISSIONS);
        
		$packageName = strtolower($packageName);

		if(empty($packageName)):
			throw new packageManagerException(packageManagerException::INVALID_SEARCH_QUERY);
		else:
			$packageName = cl::g("mysql")->mres($packageName);
			$query = cl::g("mysql")->query("SELECT * FROM `".SQL_PREFIX."pkg_packagelist` WHERE name LIKE '%".$packageName."%'") or cl::g("debug")->addLine("error", "mysql error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
			if($query->num_rows == 0):
				echo 'No packages found with the given search parameter';
			else:
				while($package = $query->fetch_assoc()):
					echo $package['name'] . " - Version: " . $package['version'] . PHP_EOL;
				endwhile;
			endif;
		endif;
	}

	/**
	 * Add a repository from where packages will be fetched
     * @permission packagemanager_global
     * @permission packagemanager_repository
	 */
	public function addRepository($repository) {
        if(!cl::g("session")->hasPermission('packagemanager_global')) throw new permissionException(permissionException::INSUFFICIENT_PERMISSIONS);
        if(!cl::g("session")->hasPermission('packagemanager_repository')) throw new permissionException(permissionException::INSUFFICIENT_PERMISSIONS);
        
		$url = parse_url($repository);
		$url = $url['scheme'] . "://" . $url['host'] . $url['path'] . "/";
        
        if(substr($url, -2) == '//') $url = substr($url, 0, -1);//We dont want double backslashes at the end
        
		if(preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url) == 1):
			//valid url
			$url = cl::g("mysql")->mres($url);
			$query = cl::g("mysql")->query("INSERT INTO `".SQL_PREFIX."pkg_repositories` (host) VALUES ('$url')");
			if(!$query):
				echo "An internal error occured while attempting to execute your command.";
			else:
				echo "Repository added, use pkg-update to update the packagelist";

                try {
                    //Update the local list of repository's
    				$this->updateRepositoryList();
                } catch(Exception $e) {
                    //pass the error on
                    throw $e;
                }
                
				return true;
			endif;
		else:
			throw new packageManagerException(packageManagerException::INVALID_REPOSITORY_URL);
		endif;

		return false;
	}

	/**
	 * Add a package and its dependencies to the DB
     * @permission packagemanager_global
	 */
	private function addPackage($package) {
        if(!cl::g("session")->hasPermission('packagemanager_global')) throw new permissionException(permissionException::INSUFFICIENT_PERMISSIONS);
        
		if(empty($package)) return false;
		if(!is_object($package)) return false;

		$packageName = (string) cl::g("mysql")->mres(strtolower($package->title));
		$package->version = (string) cl::g("mysql")->mres($package->version);
		$package->sid = (int) cl::g("mysql")->mres($package->sid);

		cl::g("mysql")->autocommit(false); //Wait with commit until all query's are finished

		$query = cl::g("mysql")->query("SELECT id FROM `".SQL_PREFIX."pkg_packagelist` WHERE id='".$this->getPackageId($package)."'") or cl::g("debug")->addLine("error", "mysql error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);

		if($query->num_rows == 0):
			$query = cl::g("mysql")->query("INSERT INTO `".SQL_PREFIX."pkg_packagelist` (id, name, version, sid) VALUES ('".$this->getPackageId($package)."', '$packageName', '".$package->version."', '".$package->sid."')") or cl::g("debug")->addLine("error", "mysql error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
		else:
            try {
    			$this->updatePackage($packageName);
            } catch(Exception $e) {
                //pass the error on
                throw $e;
            }
		endif;

        try {
    		$this->addDependency($package->dependencies, $this->getPackageId($package));		
        } catch(Exception $e) {
            //pass the error on
            throw $e;
        }

		cl::g("mysql")->commit(); //Store changes in the database
		cl::g("mysql")->autocommit(true); //reset to default

		return true;
	}

	/**
	 * Add dependencies from a package to the DB
     * @permission packagemanager_global
     * @permission packagemanager_install
	 */
	private function addDependency($packageName, $pid) {
        if(!cl::g("session")->hasPermission('packagemanager_global')) throw new permissionException(permissionException::INSUFFICIENT_PERMISSIONS);
        if(!cl::g("session")->hasPermission('packagemanager_install')) throw new permissionException(permissionException::INSUFFICIENT_PERMISSIONS);
        
		if(!is_object($packageName) || !is_array($packageName)) return false;

		foreach($packageName as $value):
			if(!empty($value) || !is_string($value)):
				$value = strtolower($value);
				$dep = $this->cacheListPackages[$value];

				$value = cl::g("mysql")->mres($value);
				$dep->title = cl::g("mysql")->mres($dep->title);

				$package = cl::g("mysql")->query("SELECT id FROM `".SQL_PREFIX."pkg_packagelist` WHERE id='".$this->getPackageId($dep)."'") or cl::g("debug")->addLine("error", "mysql error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);

				if($package->num_rows == 0):
                    try {
    					//Add dependency and check for updates
    					$depid = $this->addPackage($dep);
                    } catch(Exception $e) {
                        //pass the error on
                        throw $e;
                    }
				else:
                    try {
    					//Dependency already exists update it and check for updates to the dependencies
    					$this->updatePackage($value);
    					$this->addDependency($packageName);
                    } catch(Exception $e) {
                        //pass the error on
                        throw $e;
                    }
				endif;

				$dep = cl::g("mysql")->query("INSERT INTO `".SQL_PREFIX."pkg_dependencylist` (pid,did) VALUES ('$pid','".$this->getPackageId($dep)."')") or cl::g("debug")->addLine("error", "mysql error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
			endif;
		endforeach;
	}

    /**
     * Update a package from the packagelist in the database
     * @permission packagemanager_global
     * @param packageName the name of the package
     */
	private function updatePackage($packageName) {
        if(!cl::g("session")->hasPermission('packagemanager_global')) throw new permissionException(permissionException::INSUFFICIENT_PERMISSIONS);
        
		if(!is_string($packageName)) return false;
		$pkg = $this->cacheListPackages[strtolower($packageName)];
		$pkg->title = (string) cl::g("mysql")->mres($pkg->title);
		$pkg->version = (string) cl::g("mysql")->mres($pkg->version);
		$pkg->sid = (int) cl::g("mysql")->mres($pkg->sid);

		$package = cl::g("mysql")->query("UPDATE `".SQL_PREFIX."pkg_packagelist` SET name='".strtolower($pkg->title)."', version='".$pkg->version."', sid='".$pkg->sid."' WHERE id='".$this->getPackageId($pkg)."'") or cl::g("debug")->addLine("error", "mysql error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
	}

    /**
     * Get the ID from the given repository
     * @permission packagemanager_global
     * @param repository the URL of the repository to get the ID from
     */
	private function getRepositoryId($repository) {
        if(!cl::g("session")->hasPermission('packagemanager_global')) throw new permissionException(permissionException::INSUFFICIENT_PERMISSIONS);
        
		$url = parse_url($repository);
		$repository = $url['scheme'] . "://" . $url['host'] . $url['path'] . "/";
        
        if(substr($repository, -2) == '//') $repository = substr($repository, 0, -1);//We dont want double backslashes at the end

		$query = cl::g("mysql")->query("SELECT id FROM `".SQL_PREFIX."pkg_repositories` WHERE host='$repository'");
		return ($query->num_rows == 0) ? false : $query->fetch_object()->id;
	}

    /**
     * Get the ID of a package
     * @param package the object of a package
     * @return string
     */
	private function getPackageId($package) {
		return md5(strtolower($package));
	}

    /**
     * Check which version is newer
     * @return bool
     */
	private function version_compare($a, $b) {
		return version_compare($a, $b, '>');
	} 
}