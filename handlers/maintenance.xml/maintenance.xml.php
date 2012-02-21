<?php
/**
 * Performs standalone maintenance
 *
 * This script is used to perform standalone maintenance of a WikkaWiki install.
 * Some of these actions may need a lot of time to run and also a lot of resources.
 * Examples of maintenance possible are: 
 *  - Mass update of DB after upgrade
 *  - Regular check (checking that URLs or images referenced on the site
 *     are still active.
 *
 * This handler can then be called through Cron job, or called directly
 * using an URL like http://example.com/wikka.php?wakka=HomePage/maintenance.xml
 */
header('Content-type: text/plain; charset=utf-8');
/**
 * Update the title value of wikka_pages upon upgrading the wiki to version 1.3.3
 * @uses Wakka::LoadAll()
 * @uses Wakka::LoadPage()
 * @uses Wakka::ParsePageTitle()
 * @uses Wakka::Query()
 *
 */
function UpdatePageTitle(&$wakka, $limit = 100)
{
	$tobeupdated = $wakka->LoadAll("select tag from ".$wakka->GetConfigValue('table_prefix')."pages where title='' and latest='Y' LIMIT ".intval($limit));
	foreach ($tobeupdated as $no_title)
	{
		set_time_limit(20);
		$tag = $no_title['tag'];
		$page = $wakka->LoadPage($tag);
		$body = $page['body'];
		$page_title = $wakka->ParsePageTitle($body);
		if ('' == $page_title)
		{
			$page_title = trim(preg_replace('![A-Z]!', " \\0", $tag));
		}
		$wakka->Query("UPDATE ".$wakka->GetConfigValue('table_prefix')."pages set title = '".mysql_real_escape_string($page_title)."' WHERE tag = '".mysql_real_escape_string($tag)."' and latest = 'Y'");
		echo "$tag $page_title \n";
	}
	echo "Done...";
}

UpdatePageTitle($this);
