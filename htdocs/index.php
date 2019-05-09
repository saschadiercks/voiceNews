<?php
	header('Content-type: text/html; charset=utf-8');

	// Setup
	$projectConfigUrl ='config/config.php';
	require_once($projectConfigUrl);
?>

<!DOCTYPE html>
<html <?php
	echo isset($projectLanguage) ? 'lang="'.$projectLanguage.'"' : FALSE;
	echo isset($projectDirection) ? 'dir="'.$projectDirection.'"' : FALSE;
	echo isset($manifestUrl)? 'manifest="'.$manifestUrl.'"' : FALSE;
?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php
		echo isset($projectTitle) ? '<title>'.$projectTitle.'</title>' : FALSE;
		echo isset($projectDescription) ? '<meta name="description" content="'.$projectDescription.'"/>' : FALSE;
		echo isset($projectKeywords) ? '<meta name="keywords" content="'.$projectKeywords.'"/>' : FALSE;
		echo isset($projectLanguage) ? '<meta name="language" content="'.$projectLanguage.'"/>' : FALSE;
	?>

	<!-- mobile scaling -->
	<meta name="viewport" content="width=device-width, initial-scale=1" />

	<!-- IE-Stuff -->
	<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" />
	<meta name="MSSmartTagsPreventParsing" content="TRUE" />

	<?php if($serveAsApplication === TRUE) { ?>
		<!-- Website as app -->
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="black" />

		<!-- Short Names -->
		<meta name="apple-mobile-web-app-title" content="<?php echo($applicationName); ?>" />
		<meta name="application-name" content="<?php echo($applicationNameShort); ?>" />

		<!-- Mobile Manifest -->
		<link rel="manifest" href="manifest.json" />
	<?php } ?>

	<!-- Icons -->
	<link rel="apple-touch-icon" href="apple-touch-icon-foto-228x228-precomposed.png" sizes="228x228" />
	<link rel="shortcut icon" href="favicon.ico" />
</head>

<body>

	<!-- content -->
	<main id="content">
	<?php
		// +++++ Functions +++++++

		// get the rootUrl
		function getRootUrl($url) {
			$url = explode('/', $url);		// explode original url
			$url = $url[2];					// simply use rootUrl
			return $url;
		}

		// check if there are channel or entry-nodes and filter them (channel=rss / entry=atom)
		function checkFormat($xml) {
			foreach($xml->children() as $key=>$value) {
				switch($key) {
					case 'channel':
						return 'rss';
						break;
					case 'entry' :
						return 'atom';
						break;
				}
			}
		}

		// get the Feed
		function getFeed($content) {
			global $itemDescriptionLength;

			foreach($content as $feed) {

				$xml = @file_get_contents($feed);		// get url from json
				if($xml != false) {
					$xml = simplexml_load_string($xml);				// load rss to object
					$feedFormat = checkFormat($xml);				// let's check, if this is a rss or atom-feed

					// get values from feed (depending on the result of $feedFormat)
					if($feedFormat === 'rss') {
						// get data to push to every feedItem
						$xmlAuthorLink = $xml->channel[0]->link;
						$xmlAuthorLink = getRootUrl($xmlAuthorLink);					// get source-link from feed
						$xmlAuthorDescription = $xmlAuthorLink;							// get description from feed

						if($icon) {
							$xmlAuthorIcon = $icon;
						} else {
							$xmlAuthorIcon = '//' . $xmlAuthorLink . "/favicon.ico";		// set up favicon from sourcelink
						}

						foreach($xml->channel[0]->item as $item) {
							$feedItems[] = array(
								'itemAuthorLink' => '//' . $xmlAuthorLink,								// get authorlink (from feed)
								'itemAuthorDescription' => $xmlAuthorDescription,						// get author (from feed)
								'itemAuthorIcon' => $xmlAuthorIcon,										// get authorIcon (from feed)
								'itemLink' => strip_tags($item->link),									// get the link
								'itemTitle' => strip_tags($item->title),								// get the title
								'itemTimestamp' => strtotime($item->pubDate),							// get timestamp to make timeline sortable
								'itemDate' => date("d.m.Y (H:i)", strtotime($item->pubDate)),			// get releasedate an transform to readable date
								'itemDescription' => shortenText(strip_tags($item->description), $itemDescriptionLength)	// get description of item (usually news-short-description)
							);
						}
					} elseif($feedFormat === 'atom') {

						// get data to push to every feedItem
						$xmlAuthorLink = $xml->link['href'];						// extract href from element
						$xmlAuthorLink = getRootUrl($xmlAuthorLink);				// get source-link from feed
						$xmlAuthorDescription = $xmlAuthorLink;						// get description from feed

						if($icon) {
							$xmlAuthorIcon = $icon;
						} else {
							$xmlAuthorIcon = '//' . $xmlAuthorLink . "/favicon.ico";		// set up favicon from sourcelink
						}

						foreach($xml->entry as $item) {
							$feedItems[] = array(
								'itemAuthorLink' => '//' . $xmlAuthorLink,								// get authorlink (from feed)
								'itemAuthorDescription' => $xmlAuthorDescription,						// get author (from feed)
								'itemAuthorIcon' => $xmlAuthorIcon,										// get authorIcon (from feed)
								'itemLink' => strip_tags($item->id),									// get the link
								'itemTitle' => strip_tags($item->title),								// get the title
								'itemTimestamp' => strtotime($item->updated),							// get timestamp to make timeline sortable
								'itemDate' => date("d.m.Y (H:i)", strtotime($item->updated)),			// get releasedate an transform to readable date
								'itemDescription' => shortenText(strip_tags($item->content))			// get description of item (usually news-short-description)
							);
						}
					}
				}
			}
			return $feedItems;
		}

		// filter feedItems with blacklist
		function filterFeed($feedItems) {
			global $blacklistItems;

			foreach($feedItems as $feedItem => $key) {
				foreach($blacklistItems as $blacklistItem) {
					$keysCombined = $key['itemLink'] .' '. $key['itemTitle'] .' '. $key['itemDescription']; 	// were combining url, title, description to search them in one go
					if(strpos($keysCombined, $blacklistItem) !== FALSE) {
						$feedItems[$feedItem]['itemBlacklistHit'] = $blacklistItem;		// if one blacklistItem is in the keys, the array is expanded with it. It get's sorted out later
					}
				}
			}
			return $feedItems;
		}

		// sort feed by releaseDate/timestamp
		function sortFeed($feedItems) {
			foreach ($feedItems as $feedItem => $key) {
				$itemTimestamp[$feedItem] = $key['itemTimestamp'];
			}
			array_multisort($itemTimestamp, SORT_DESC, $feedItems);
			return $feedItems;
		}

		function shortenText($text) {
			global $itemDescriptionLength;
			global $readMoreIcon;
			$text = preg_replace('!\s+!', ' ', $text);	// remove unnesseccary whitespace
			if(strlen($text) > $itemDescriptionLength) {
				$text = substr($text, 0, strpos($text,'.',$itemDescriptionLength)) . ". " . $readMoreIcon;
			}
			return $text;
		}

		// render Output
		function renderFeed($feedItems) {
			$feedItemCount = 0;
			foreach ($feedItems as $feedItem) {
				if($feedItem['itemBlacklistHit']) {
					// output if part of feedItemTitle is in blacklist
				} else {
					// standard ouput of feed
					echo '<section id="ts-' . $feedItem['itemTimestamp'] . '" data-count="' . $feedItemCount . '" data-ts="' . $feedItem['itemTimestamp'] .'">';	// add timestamp to use as anchor for unread news
					//echo 	'<div>';
					//echo 		'<a href="' . $feedItem['itemAuthorLink'] . '" class="icon" rel="noopener" target="pn-blank"><img src="' . $feedItem['itemAuthorIcon'] . '" alt="' . $feedItem['itemAuthorDescription'] . '" height="128" width="128" /></a>';
					//echo 	'</div>';
					//echo		'<header>';
					echo			'<h2 class="title">'. $feedItem['itemTitle'] .'.</h2>';
					//echo			'<p class="info"><span class="date">' . $feedItem['itemDate'] . '</span> / <a href="' . $feedItem['itemAuthorLink'] . '" class="source">' . $feedItem['itemAuthorDescription'] . '</a></p>';
					//echo		'</header>';
					echo		'<p class="excerpt">' . $feedItem['itemDescription'] . '</p>';
					//echo	'<div>';
					//echo	'</div>';
					echo '</section>';
					$feedItemCount++;
				}
			}
		}
		?>


		<!-- output -->
		<article>
			<h1>Das war heute los!</h1>
			<?php
				$feedItems = getFeed($content);
				$feedItems = filterFeed($feedItems);
				$feedItems = sortFeed($feedItems);
				renderFeed($feedItems);
			?>
		</article>
	</main>

	<!-- footer -->
	<footer>
	</footer>

</body>
</html>
