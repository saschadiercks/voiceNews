<?php

	// Project Title
	$projectTitle = 'voiceNews';
	$projectTitleSub = 'your newsfeds to be read byy your digital assistant';
	$projectDescription = $projectTitle . ' | ' . $projectTitleSub;
	$projectKeywords = 'news, timeline, newsstream';
	$projectLanguage = 'de';					// langugage of your content (see: http://www.iana.org/assignments/language-subtag-registry/language-subtag-registry)
	$projectDirection = 'ltr';					// Reading direction ltr || rtl

	// Application-Settings (mobile)
	$serveAsApplication = FALSE;					// allow running as standalone application (TRUE || FALSE)
	$applicationName = 'pvoiceNewsNews';
	$applicationNameShort = $applicationName;

	// Set Environemt
	// $manifestUrl = 'application.manifest.php';	// This is the name of the manifest-file
	$cssUrl = 'assets/css/site.css';			// Set the url to retreive the css from
	$jsUrl = 'assets/js/script.js';				// Set the url to retrieve the js from

	// Set php requirements
	ini_set('allow_url_fopen', true);			// allow loading external files
	date_default_timezone_set('Europe/Berlin');  // set Timezone to avoid errors on feed-fetching

	// Set Data-Environment
	$dataUrl = 'data/data.json';					// Set the url to retrieve the data from
	$jsonContent = file_get_contents($dataUrl);		// Get the data
	$json = json_decode($jsonContent, true);		// (true) returns the json as array-structure
	$headline = $json['headline'];					// set the title for the article
	$content = $json['feeds'];					// Get content of json-array directly (used to ease extension of the json later (header, footer...))
	$blacklist = $json['blacklist'];				// get Blacklist array

	// size to shortenText
	$itemDescriptionLength = 300;
	$readMoreIcon = '...';

	// +++++ Functions +++++++
	$channelUrlParameter = urldecode($_GET['channel']);		// get the channelparamter, if there's one
	$channelItems = array();								// collect all channels in array
	$feedItems = array();									// collect all feeds in array

	// get all channels and put them in array
	function getChannelItems($content) {
		$channelItems = array_keys($content);
		return $channelItems;
	}

	// get all blacklist items and put them in array
	function getBlacklistItems($blacklist) {
		$blacklist = explode(',', $blacklist['keywords']);
		return $blacklist;
	}

	// get the headline
	function getHeadline($headline) {
		return $headline;
	}

	// calls
	$channelItems = getChannelItems($content);
	$blacklistItems = getBlacklistItems($blacklist);
	$articleHeadline = getHeadline($headline);
?>
