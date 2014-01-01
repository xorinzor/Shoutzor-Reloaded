<?php

class queue {

	/**
	 * Get the current queue
	 * @param limit integer
	 * @return array
	 **/
	public function get($limit = 0) {
		$limit = ($limit > 0) ? "LIMIT ".$limit : "";
		$query = cl::g("mysql")->query("SELECT * FROM `queue` ORDER BY time_requested ASC ".$limit) or debug::addLine("error", "could not get track from queue, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
		if($query):
			//Query worked
			if($query->num_rows == 0) return array(); //No songs in queue

			$queue = array();
			while($track = $query->fetch_assoc()) $queue[] = $track;

			return $queue;
		else:
			//Query failed
			return array();
		endif;
	}

	/**
	 * Get the history list
	 * @param limit integer
	 * @return array
	 **/
	public function getHistory($limit = 0) {
		$limit = ($limit > 0) ? "LIMIT ".$limit : "";
		$query = cl::g("mysql")->query("SELECT * FROM `queue_history` ORDER BY time_played DESC ".$limit) or debug::addLine("error", "could not get track from queue, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
		if($query):
			//Query worked
			if($query->num_rows == 0) return array(); //No songs in queue

			$queue = array();
			while($track = $query->fetch_assoc()) $queue[] = $track;

			return $queue;
		else:
			//Query failed
			return array();
		endif;
	}

	/**
	 * Add a track to the queue
	 * @param track track
	 * @param userid integer
	 * @return boolean
	 **/
	public function add(track $track, $userid = "null", $silent = false, $timeoverride = 'NOW()') {
		if(!is_object($track)) return false;

		if($silent == false):
			LiquidSoap::requestTrack('replay_gain:'.$track->getFile()->getFilePath());
		endif;

		$query = cl::g("mysql")->query("INSERT INTO `queue` (track, user, time_requested) VALUES ({$track->getId()}, {$userid}, {$timeoverride})") or debug::addLine("error", "could not add track to queue, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
		return ($query);
	}

	/**
	 * Remove track from the queue and (optionally) move it to the queue_history table
	 * @param trackid int
	 * @param silent boolean
	 **/
	public function remove($trackid, $silent = false) {
		if(!security::valid("NUMERIC", $trackid) || $trackid < 1) return false;

		if(!$silent):
			$query = cl::g("mysql")->query("SELECT * FROM `queue` WHERE track=$trackid") or debug::addLine("error", "could not get track from queue, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
			if($query->num_rows > 0):
				$data = $query->fetch_object();
			endif;
		endif;

		cl::g("mysql")->autocommit(false);

		if($data->user < 1 || $data->user == null) $data->user = "null";

		try {
			$query = cl::g("mysql")->query("DELETE FROM `queue` WHERE track=$trackid") or debug::addLine("error", "could not remove track from queue, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
			if(!$query) throw new Exception("mysql error");

			if(!$silent):
				$query = cl::g("mysql")->query("INSERT INTO `queue_history` (track, user, time_played, time_requested) VALUES ($trackid, {$data->user}, NOW(), '{$data->time_requested}')") or debug::addLine("error", "could not add track to queue history, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
				if(!$query) throw new Exception("mysql error");
			endif;
		} catch(Exception $e) {
			cl::g("mysql")->rollback();
		}

		cl::g("mysql")->commit();
		cl::g("mysql")->autocommit(true);
	}
}