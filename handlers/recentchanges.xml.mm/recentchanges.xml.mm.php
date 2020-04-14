<?php
/**
 * Show a mindmap of the recent changes in the wiki as an XML File.
 *
 * @package		Handlers
 * @subpackage	Mindmaps
 * @version		$Id:recentchanges.xml.mm.php 407 2007-03-13 05:59:51Z DarTar $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	Wakka::Href()
 * @uses	Wakka::htmlspecialchars_ent()
 * @uses	Wakka::LoadRecentlyChanged()
 *
 * @todo	i18n
 * @todo	replace numbers by constants: no "magic numbers"!)
 */

header("Content-type: text/xml");

$xml  = '<map version="0.7.1">'."\n";
$xml .= '<node TEXT="'.T_("Recent Changes").'">'."\n";
$xml .= '<node TEXT="Date" POSITION="right">'."\n";

if ($pages = $this->LoadRecentlyChanged())
{
	$users = array();
	$curday = '';
	$max = 20;		// @@@
	//$max = $this->GetConfigValue('xml_recent_changes');
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
			$pageuser = $this->htmlspecialchars_ent($page['user'],ENT_COMPAT,'XML'); #Just in case...
			$pagetag = $this->htmlspecialchars_ent($page['tag'],ENT_COMPAT,'XML');

			// day header
			list($day, $time) = explode(' ', $page['time']);
			if ($day != $curday)
			{
				$dateformatted = date('D, d M Y', strtotime($day));
				if ($curday) $xml .= "</node>\n";
				$xml .= '<node TEXT="'.$dateformatted.'">'."\n";
				$curday = $day;
			}

			$pagelink = WIKKA_BASE_URL.urlencode($page['tag']);
			$xml .= '<node LINK="'.$pagelink.'" TEXT="'.$pagetag.'" FOLDED="true">'."\n";
			$timeformatted = date('H:i T', strtotime($page['time']));
			$xml .= '<node LINK="'.$pagelink.'/revisions" TEXT="'.sprintf(T_("Revision time: %s"),$timeformatted).'"/>'."\n";
			if ($pagenote = $this->htmlspecialchars_ent($page['note'],ENT_COMPAT,'XML'))
			{
				$xml .= '<node TEXT="'.$pageuser.': '.$pagenote.'"/>'."\n";
			}
			else
			{
				$xml .= '<node TEXT="'.sprintf(T_("Author: %s"),$pageuser).'"/>'."\n";	#i18n
			}

			$xml .= '<node LINK="'.$pagelink.'/history" TEXT="'.T_("View History").'"/>'."\n";	# i18n
			$xml .= "</node>\n";
			// $xml .= "<arrowlink ENDARROW=\"Default\" DESTINATION=\"Freemind_Link_".$page["user"]."\" STARTARROW=\"None\"/>\n";
			if (isset($users[$pageuser]) && (is_array($users[$pageuser])))
			{
				$u_count = count($users[$pageuser]);
				$users[$pageuser][$u_count] = $pagetag;
			}
			else
			{
				$users[$pageuser][0] = $pageuser;
				$users[$pageuser][1] = $pagetag;
			}

		}
	}

	$xml .= '</node></node><node TEXT="Author" POSITION="left">'."\n";
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
	$xml .= '<title>'.T_("Error")."</title>\n";
	$xml .= '<link>'.$this->Href()."</link>\n";
	$xml .= '<description>'.T_("You are not allowed to access this information.")."</description>\n";
	$xml .= "</item>\n";
}

$xml .= "</map>\n";

print($xml);

?>
