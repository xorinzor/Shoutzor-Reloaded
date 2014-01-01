<?php

	//Let the GoogleBot and other webcrawlers know the website is currently unavailable and should not be indexed in the current state
	header('HTTP/1.1 503 Service Temporarily Unavailable');
	header('Status: 503 Service Temporarily Unavailable');
	header('Retry-After: 7200');//2 hours, should be enough time for a basic maintenance task to complete

	cl::g("display")->getPage()->setTitle("Maintenance");