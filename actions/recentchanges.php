<?php
if (!defined('PAGE_EDITOR_DIVIDER')) define ('PAGE_EDITOR_DIVIDER', '&#8594;');
if (!defined('UNREGISTERED_USER')) define('UNREGISTERED_USER', 'unregistered user');
if (!defined('LABEL_HISTORY')) define('LABEL_HISTORY', 'history');
if (!defined('TITLE_REVISION_LINK')) define('TITLE_REVISION_LINK', 'View recent revisions list for %s');
if (!defined('TITLE_HISTORY_LINK')) define('TITLE_HISTORY_LINK', 'View edit history of %s');
if (!defined('WIKIPING_ENABLED')) define('WIKIPING_ENABLED', 'WikiPing enabled: Changes on this wiki are broadcast to <a href="http://%1$s">http://%1$s</a>]');

if ($pages = $this->LoadRecentlyChanged())
{
	$curday = "";
	print("<p><a href=\"".$this->href("recentchanges.xml", $this->page["tag"])."\"><img src=\"images/xml.png\" width=\"36\" height=\"14\" alt=\"XML\" /></a></p>\n");

	if ($user = $this->GetUser()) 
	{
		$max = $user["changescount"];
	} else 
	{
		$max = 50;
	}

	foreach ($pages as $i => $page)
	{
		if (($i < $max) || !$max)
		{
			// day header
			list($day, $time) = explode(" ", $page["time"]);
			if ($day != $curday)
			{
				$dateformatted = date("D, d M Y", strtotime($day));

				if ($curday) print("</span><br />\n");
				print("<strong>$dateformatted:</strong><br />\n<span class=\"recentchanges\">");
				$curday = $day;
			}

			$timeformatted = date("H:i T", strtotime($page["time"]));
			$page_edited_by = $page["user"];	
			if (!$this->LoadUser($page_edited_by)) $page_edited_by .= ' ('.UNREGISTERED_USER.')';

			// print entry
			if ($page["note"]) $note=" <span class=\"pagenote\">[".$this->htmlspecialchars_ent($page["note"])."]</span>"; else $note ="";
			$pagetag = $page["tag"];
			if ($this->HasAccess("read", $pagetag)) 
			{
				print("&nbsp;&nbsp;&nbsp;&nbsp;(".$this->Link($pagetag, "revisions", $timeformatted, 0, 1, sprintf(TITLE_REVISION_LINK, $pagetag)).") [".$this->Link($pagetag, 'history', LABEL_HISTORY, 0, 1, sprintf(TITLE_HISTORY_LINK, $pagetag)).'] - &nbsp;'.$this->Link($pagetag, '', '', 0).' '.PAGE_EDITOR_DIVIDER.' '.$page_edited_by.' '.$note.'<br />');
			} else 
			{
				print('&nbsp;&nbsp;&nbsp;&nbsp;('.$timeformatted.') ['.LABEL_HISTORY.'] - &nbsp;'.$page['tag'].' '.PAGE_EDITOR_DIVIDER.' '.$page_edited_by.' '.$note.'<br />');
			}
		}
	}
	print "</span>\n";

	$wikipingserver = $this->config["wikiping_server"];
	if ($wikipingserver) 
	{
		$wikipingserver_url_parsed = parse_url($wikipingserver);
		$wikipingserver_host = $wikipingserver_url_parsed["host"];
		printf('<br /><br />['.WIKIPING_ENABLED.']', $wikipingserver_host);
	}
}
?>
