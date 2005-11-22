<?php
// @@@ JW should use makeList to generate the list; maybe better convert to using Magpie first, though

// Action usage:
// {{rss http://domain.com/feed.xml}} or {{rss url="http://domain.com/feed.xml" cachetime="30"}}

// NOTE1 : in Onyx-RSS default is "debugMode" which results in all errors being printed
//		this could be suppressed by turning debug mode off, but then we'd never have a
//		clue about the cause of any error.
//		A better (preliminary) approach seems to be to override the raiseError() method
//		still providing the text of any error message, only within an HTML comment:
//		that way normal display will look clean but you can look at the HTML source to
//		find the cause of any problem.
// NOTE 2: no solution for timeout problems with non-existing feeds yet...

$max_items = 30; // set this to the maximum items the RSS action should ever display

$caching = true; // change this to false to disable caching
$rss_cache_path = "/tmp"; // set this to a writable directory to store the cache files in
$lowest_cache_time_allowed = "5"; // set this to the lowest caching time allowed

$rss_cache_time = (int)trim($vars['cachetime']);
if (!$rss_cache_time) {
	$rss_cache_time = 30; // set this for default cache time
} elseif ($rss_cache_time < $lowest_cache_time_allowed) {
	$rss_cache_time = $lowest_cache_time_allowed;
}
$rss_cache_file = ""; // initial value, no need to ever change

//Action configuration
$rss_path = $vars['url'];
if ((!$rss_path) && $wikka_vars) $rss_path = $wikka_vars;
$rss_path = $this->cleanUrl(trim($rss_path));

// override
if (preg_match("/^(http|https):\/\/([^\\s\"<>]+)$/i", $rss_path))
{
	include_once('3rdparty/plugins/onyx-rss/onyx-rss.php');
	if (!class_exists(Wikka_Onyx))
	{
		class Wikka_Onyx extends ONYX_RSS
		{
			//private function raiseError($line, $err)
			function raiseError($line, $err)
			{
				if ($this->debugMode)
				{
					$errortext = sprintf($this->error, $line, $err);
					echo '<!-- '.$errortext.' -->'."\n";
				}
			}
		}
	}
}

if (preg_match("/^(http|https):\/\/([^\\s\"<>]+)$/i", $rss_path))
{
	if ($caching) {
		// Create unique cache file name based on URL
		$rss_cache_file = md5($rss_path).".xml";
	}

	//Load the RSS Feed: workaround to hide error messages within HTML comments:
	$rss =& new Wikka_Onyx();
	$rss->setCachePath($rss_cache_path);
	$rss->parse($rss_path, $rss_cache_file, $rss_cache_time);
	$meta = $rss->getData(ONYX_META);

	//List the feed's items
	$cached_output = "<h3>".$meta['title']."</h3>";
	$cached_output .= "<ul>\n";
	while ($max_items > 0 && ($item = $rss->getNextItem()))
	{
		$cached_output .= "<li><a href=\"".$item['link']."\">".$item['title']."</a><br />\n";
		$cached_output .= $item['description']."</li>\n";
		$max_items = $max_items - 1;
	}
	$cached_output .= "</ul>\n";
	echo $this->ReturnSafeHTML($cached_output);
} else {
	echo '<span class="error"><em>Error: Invalid RSS action syntax. <br /> Proper usage: {{rss http://domain.com/feed.xml}} or {{rss url="http://domain.com/feed.xml"}}</em></span>';
}

?>