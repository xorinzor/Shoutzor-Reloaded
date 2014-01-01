<?php

	$queue = cl::g("queue")->get(1);
		
	//Check if a track in the queue is set
	if(count($queue) > 0):
		//get the track from the queue
		$trackid = $queue[0]['track'];

		//Play the track from the queue
		$track = cl::g("tracks")->getTrack($trackid);

		if(!is_a($track, "track")):
			die('error');
		endif;
	else:
		//No track in the queue, add one randomly
		$track = cl::g("tracks")->getRandomTrack();

		if(!is_a($track, "track")):
			die('error');
		endif;

		cl::g("queue")->add($track);
	endif;

	//Remove the track from the queue and move it to the queue_history
	$result = cl::g("queue")->remove($track->getId());

	//Return the filepath to the MP3 for liquidsoap
	//echo $track->getFile()->getFilePath();

	//Check if there are songs in the queue
	if(count(cl::g("queue")->get(1)) == 0):
		//Add random track to the queue
		$track = cl::g("tracks")->getRandomTrack();

		if(!is_a($track, "track")):
			die('');
		endif;

		cl::g("queue")->add($track);
	else:
		//Get next song in the queue
		/*
		$track = cl::g("queue")->get(1);

		$track = cl::g("tracks")->getTrack($track[0]['track']);

		if(!is_a($track, "track")):
			die('');
		endif;
		*/
	endif;
	
	die();