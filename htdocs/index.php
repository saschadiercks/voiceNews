<?php
	// Setup
	$projectConfigUrl = "config/config.php";
	require_once($projectConfigUrl);

	// Data-Environment
	$dataUrl = "data/data.json";				// Set the url to retrieve the data from
	$jsonContent = file_get_contents($dataUrl);		// Get the data
	$json = json_decode($jsonContent, true);		// (true) returns the json as array-structure

	// Array-Konstrukte aufbauen
	//$header = array_keys($json['content']);		// Build header-array (keys of first layer only, because we only need the keys as title)
	$content = $json['content'];					// Get content-array directly
	//$footer = $json['footer'];					// Get footer-array



	// +++++ Functions +++++++
	$feedItems = array();	// collect all feeds in array

	// get the rootUrl
	function getRootUrl($url) {
		$url = explode('/', $url);		// explode original url
		$url = $url[2];					// simply use the rootUrl
		return $url;
	}

	// get the RSS
	function getRSS($content) {
		foreach($content as $key) {
			foreach($key as $rssUrl) {
				$xml = file_get_contents($rssUrl['url']);			// get url from json
				$xml = simplexml_load_string($xml);					// load rss to object

				// get data to push to every feedItem
				$xmlAuthorLink = getRootUrl((string)$xml->channel[0]->link);			// get source-link from rss
				$xmlAuthorDescription = $xmlAuthorLink;									// get description from rss
				$xmlAuthorIcon = '//' . $xmlAuthorLink . "/favicon.ico";				// set up favicon from sourcelink

				foreach($xml->channel[0]->item as $item) {
					$feedItems[] = array(
						'itemAuthorLink' => '//' . $xmlAuthorLink,						// get authorlink (from feed)
						'itemAuthorDescription' => $xmlAuthorDescription,				// get author (from feed)
						'itemAuthorIcon' => $xmlAuthorIcon,								// get authorIcon (from feed)
						'itemLink' => strip_tags($item->link),							// get the link
						'itemTitle' => strip_tags($item->title),						// get the title
						'itemTimestamp' => strtotime($item->pubDate),					// get timestamp to make timeline sortable
						'itemDate' => date("d.m.Y (H:m)", strtotime($item->pubDate)),	// get releasedate an transform to readable date
						'itemDescription' => strip_tags($item->description)				// get description of item (usually news-short-description)
					);
				}
			}
		}
		return $feedItems;
	}

	// sort RSS by releaseDate/timestamp
	function sortRss($feedItems) {
		// echo $feedItems[0]['itemDate'];
		//var_dump($feedItems);
		return $feedItems;
	}

	// render RSS
	function renderRss($feedItems) {
		foreach ($feedItems as $feedItem) {
			// render output
			echo '<li>';
			echo '<a href="' . $feedItem['itemAuthorLink'] . '" class="icon" rel="noopener"><img src="' . $feedItem['itemAuthorIcon'] . '" alt="' . $feedItem['itemAuthorDescription'] . '" height="32" width="32" /></a>';
			echo '<h2 class="title"><a href="' .  $feedItem['itemLink'] . '" rel="noopener">' . $feedItem['itemTitle'] .'</a></h2>';
			echo '<p class="info"><span class="date">' . $feedItem['itemDate'] . '</span> / <a href="' . $feedItem['itemAuthorLink'] . '" class="source">' . $feedItem['itemAuthorDescription'] . '</a></p>';
			echo '<p class="excerpt js-folddown"><a href="' .  $feedItem['itemLink'] . '" rel="noopener">' . $feedItem['itemDescription'] . '</a></p>';
			echo '</li>';
		}
	}
?>

<!DOCTYPE html>
<html dir="ltr" lang="de" manifest="<?php if(isset($manifestUrl)) { echo($manifestUrl); }  ?>">
<head>
	<title><?php echo($projectTitle); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="description" content="<?php echo($projectdescription); ?>" />
	<meta name="language" content="de" />
	<meta name="MSSmartTagsPreventParsing" content="TRUE" />
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Short Names -->
	<meta name="apple-mobile-web-app-title" content="<?php echo($applicationName); ?>" />
	<meta name="application-name" content="<?php echo($applicationNameShort); ?>" />

	<!-- Icons -->
	<link rel="apple-touch-icon" href="apple-touch-icon-foto-114x114-precomposed.png" />
	<link rel="shortcut icon" href="favicon.ico" />

	<!-- CSS -->
	<style type="text/css">
		<?php require_once($cssUrl); ?>
	</style>

	<!-- JS -->
	<script type="text/javascript">
		<?php require_once($jsUrl); ?>
	</script>

	<!-- Mobile Manifest -->
	<link rel="manifest" href="manifest.json" />
</head>

<body id="home">

	<!-- header -->
	<header>
		<!-- nothing here -->
	</header>

	<!-- content -->
	<main id="content">
		<ul>
			<?php
				$feedItems = getRss($content);
				//$feedItems = sortRss($feedItems);
				renderRss($feedItems);
			?>
		</ul>
	</main>

	<!-- footer -->
	<footer>
	</footer>
</body>
</html>
