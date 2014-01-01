<?php

class files {

	public function getFile($file) {
		$file = realpath($file);
		if(!$file) return false;

		return new file(basename($file), dirname($file));
	}

	public function getDirectory() {
		
	}

}