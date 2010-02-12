<?php
/**
 * Show a simple mindmap of recent changes in the wiki as an XML File.
 *
 * Only the date and the page of the change are shown.
 *
 * @package		Handlers
 * @subpackage	Mindmaps
 * @version		$Id:recentchanges_simple.xml.mm.php 407 2007-03-13 05:59:51Z DarTar $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	in_iarray()
 * @uses	Wakka::Href()
 * @uses	Wakka::LoadRecentlyChanged()
 * @uses	Wakka::HasAccess()
 *
 * @todo	replace numbers by constants: no "magic numbers"!)
 */
header('Content-type: text/xml');

/**
 * Checks if an item is contained in an array (case insensitive).
 *
 * @name	in_iarray()
 * @return	TRUE or FALSE
 */
function in_iarray($item, $array)
{
	$item = &strtoupper($item);
	foreach ($array as $element)
	{
		if ($item == strtoupper($element))
		{
			return TRUE;
		}
	}
	return FALSE;
}

$xml  = '<map version="0.7.1">'."\n";
$xml .= '<node TEXT="'.FIRST_NODE_LABEL.'">'."\n";
$xml .= '<node TEXT="Date" POSITION="right">'."\n";

if ($pages = $this->LoadRecentlyChanged())
{
	$users = array();
	$curday = '';
	$max = 20;		// @@@
	// $max = $this->GetConfigValue("xml_recent_changes");
	//if ($user = $this->GetUser()) {
	//	$max = $user["changescount"];
	//} else {
	//	$max = 50;
	//}

	$c = 0;
	foreach ($pages as $page)
	{
		$c++;
		if (($this->HasAccess('read', $page['tag'])) && (($c <= $max) || !$max))
		{

			// day header
			list($day, $time) = explode(' ', $page['time']);
			if ($day != $curday)
			{
				if ($curday) $xml .= "</node>\n";
				$xml .= '<node TEXT="'.$day.'">'."\n";
				$curday = $day;
			}

			$xml .= '<node TEXT="'.$page['tag'].'">'."\n";
			// $xml .= "<arrowlink ENDARROW=\"Default\" DESTINATION=\"Freemind_Link_".$page["user"]."\" STARTARROW=\"None\"/>\n";
			$xml .= "</node>\n";
			if (is_array($users[$page['user']]))
			{
				$u_count = count($users[$page['user']]);
				$users[$page['user']][$u_count] = $page['tag'];
			}
			else
			{
				$users[$page['user']][0] = $page['user'];
				$users[$page['user']][1] = $page['tag'];
			}

		//	if (!in_iarray($page["user"], $users)) {
		//		$users[$c] = $page["user"];
		//	} else {
		//		$u_count = count($users[$c]);
		//		$users[$c][$u_count] = $page["tag"];
		//	}
		}
	}

	$xml .= '</node></node><node TEXT="Author" POSITION="left">'."\n";
	// $pages = $this->LoadAll("select DISTINCT user from ".$this->config["table_prefix"]."pages where latest = 'Y' order by time desc");
	foreach ($users as $user)
	{
		$start_loop = true;
		foreach ($user as $user_page)
		{
			if (!$start_loop)
			{
				$xml .= '<node TEXT="'.$user_page.'"/>'."\n";
			}
			else
			{
				$xml .= '<node TEXT="'.$user_page.'"/>'."\n";
				$start_loop = FALSE;
			}
		}
		$xml .= "</node>\n";
		// $xml .= "<node ID=\"Freemind_Link_".$user["user"]."\" TEXT=\"".$page["user"]."\"/>\n";
	}

}
else
{
	$xml .= "<item>\n";
	$xml .= '<title>'.WIKKA_ERROR_CAPTION."</title>\n";
	$xml .= '<link>'.$this->Href()."</link>\n";
	$xml .= '<description>'.WIKKA_ERROR_ACL_READ_INFO."</description>\n";
	$xml .= "</item>\n";
}

$xml .= "</node></node>\n";
$xml .= "</map>\n";

echo $xml;
?>