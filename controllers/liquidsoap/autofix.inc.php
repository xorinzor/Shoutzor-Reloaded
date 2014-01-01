<?php

cl::g("display")->setThemeEnabled(false);

function toggleliquidsoap($operation) {
	switch($operation):
		case "start":
			if(LiquidSoap::isRunning()) return array('result' => false);
			exec("screen -dmS shoutzor liquidsoap /home/shoutzor/shoutzor.liq"); //Start the tracklist

			sleep(5);
			if(LiquidSoap::isRunning()):
				LiquidSoap::run("sound.select 0 true");

				$queues = cl::g("queue")->get();

				if(count($queues) > 1) {
					foreach($queues as $queue):
						//get the track from the queue
						$trackid = $queue['track'];

						//Play the track from the queue
						$track = cl::g("tracks")->getTrack($trackid);

						if(is_a($track, "track")) { LiquidSoap::requestTrack('replay_gain:'.$track->getFile()->getFilePath()); }
					endforeach;
				} else if(count($queues) == 1) {
					//Add 1 song to queue, call nexttrack
					$track = cl::g("tracks")->getTrack($queues[0]['track']);
					if(is_a($track, "track")) { LiquidSoap::requestTrack('replay_gain:'.$track->getFile()->getFilePath()); }

					$track = cl::g("tracks")->getRandomTrack();
					if(is_a($track, "track")) { cl::g("queue")->add($track); }
				} else {
					//Add 2 songs to queue, call nexttrack
					$track = cl::g("tracks")->getRandomTrack();
					if(is_a($track, "track")) { cl::g("queue")->add($track); }

					$track = cl::g("tracks")->getRandomTrack();
					if(is_a($track, "track")) { cl::g("queue")->add($track); }
				}

				//exec("curl ".SITEURL."liquidsoap/getnexttrack/ > /dev/null &"); //Start the tracklist
			endif;
		break;

		case "stop":
			if(!LiquidSoap::isRunning()) return array('result' => false);
			exec("screen -X -S shoutzor quit");
			//exec("killall liquidsoap > /dev/null &");
		break;

		case "next":
			if(!LiquidSoap::isRunning()) return array('result' => false);
			LiquidSoap::nextTrack();
		break;

		default:
			return array('result' => false, 'debug' => $result);
		break;
	endswitch;
	
	return array('result' => true);
}

toggleliquidsoap("stop");

sleep(3);

toggleliquidsoap("start");

die();