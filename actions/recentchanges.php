<?php

// constant section
define('XML_BUTTON_LABEL', 'XML');
define('PAGE_EDITOR_UNREGISTERED', '(unregistered user)');
define('HISTORY_LINK_TXT', 'history');
define('VIEW_RECENT_REVISIONS_LABEL', 'View recent revisions list for %s'); // %s - pagename
define('VIEW_EDIT_HISTORY_LABEL', 'View edit history of %s'); // %s - pagename
define('WIKIPING_ENABLED_INFO', '[WikiPing enabled: Changes on this wiki are broadcast to %s]'); // %s - link to wikiping-server

define('MAX_CHANGECOUNT_DEFAULT', '50');
define('PAGE_EDITOR_DIVIDER', '&rArr');

if ($pages = $this->LoadRecentlyChanged())
{
	$curday = "";
	print('<p><a href="'.$this->href("recentchanges.xml", $this->page["tag"]).'"><img src="images/xml.png" width="36" height="14" alt="'.XML_BUTTON_LABEL."\" /></a></p>\n");

	if ($user = $this->GetUser()) {
		$max = $user["changescount"];
	} else {
		$max = MAX_CHANGECOUNT_DEFAULT;
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
			if (!$this->LoadUser($page_edited_by)) $page_edited_by .= PAGE_EDITOR_UNREGISTERED;

			// print entry
			if ($page["note"]) $note=' <span class="pagenote">['.$page["note"].']</span>'; else $note ="";
			$pagetag = $page["tag"];
			if ($this->HasAccess("read", $pagetag)) {
					print("&nbsp;&nbsp;&nbsp;&nbsp;(".$this->Link($pagetag, "revisions", $timeformatted, 0, 1, sprintf(VIEW_RECENT_REVISIONS_LABEL, $pagetag)).") [".$this->Link($pagetag, "history",HISTORY_LINK_TXT,  0, 1, sprintf(VIEW_EDIT_HISTORY_LABEL, $pagetag))."] - &nbsp;".$this->Link($pagetag, "", "", 0).' '.PAGE_EDITOR_DIVIDER.' '.$page_edited_by.$note."<br />");
			} else {
					print("&nbsp;&nbsp;&nbsp;&nbsp;($timeformatted) [".HISTORY_LINK_TXT.'] - &nbsp;'.$page["tag"].' '.PAGE_EDITOR_DIVIDER.' '.$page_edited_by.$note."<br />");
			}
		}
	}
	print "</span>\n";

	$wikipingserver = $this->config["wikiping_server"];
	if ($wikipingserver) {
		$wikipingserver_url_parsed = parse_url($wikipingserver);
		$wikipingserver_host = $wikipingserver_url_parsed["host"];
		echo '<br /><br />'.WIKIPING_ENABLED_INFO.'<a href="http://'.$wikipingserver_host.'">http://'.$wikipingserver_host.'</a>]';
	}
}
?>