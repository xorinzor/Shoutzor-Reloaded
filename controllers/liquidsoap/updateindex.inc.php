<?php

	require_once(PHP_PATH . "core/indexer.class.php");

	set_time_limit(0);

	$indexer = new indexer();

	$indexer->scan();
	$indexer->scanForBrokenTracks();

	die();