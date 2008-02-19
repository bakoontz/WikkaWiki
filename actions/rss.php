<?php
// Action usage:
// {{rss url="http://domain.com/feed.xml" cache="cachefilename.xml"}}
// The cache parameter is optional, leave it out to disable caching

//Action configuration
$rss_path = $vars['url'];
$rss_cache_file = $vars['cache'];
$rss_cache_path = "/tmp"; // set this to a writable directory to store the cache files in
$rss_cache_time = "30"; //cache period in minutes

//Load the RSS Feed
include_once('xml/onyx-rss.php');
$rss =& new ONYX_RSS();
$rss->setCachePath($rss_cache_path);
$rss->parse($rss_path, $rss_cache_file, $rss_cache_time);
$meta = $rss->getData(ONYX_META);

//List the feed's items
echo "<h3>".$meta['title']."</h3>";
echo "<ul>\n";
while ($item = $rss->getNextItem())
{
  echo "<li><a href=\"".$item['link']."\">".$item['title']."</a><br />\n";
  echo $item['description']."</li>\n";
}
echo "</ul>\n";
?>