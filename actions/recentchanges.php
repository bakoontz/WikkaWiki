<?php
if ($pages = $this->LoadRecentlyChanged())
{
	$curday = "";
	print("<p><a href=\"".$this->href("recentchanges.xml", $this->page["tag"])."\"><img src=\"images/xml.png\" width=\"36\" height=\"14\" alt=\"XML\" /></a></p>\n");
	if ($user = $this->GetUser()) {
		$max = $user["changescount"];
	} else {
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
				if ($curday) print("</span><br />\n");
				print("<strong>$day:</strong><br />\n<span class=\"recentchanges\">");
				$curday = $day;
			}

			// print entry
			if ($page["note"]) $note=" <span class=\"pagenote\">[".$page["note"]."]</span>"; else $note ="";
			if ($this->HasAccess("read", $page["tag"])) {
					print("&nbsp;&nbsp;&nbsp;&nbsp;(".$page["time"].") (".$this->Link($page["tag"], "revisions", "history", 0).") &nbsp;".$this->Link($page["tag"], "", "", 0)." &rArr; ".$page["user"]." ".$note."<br />");
		} else {
					print("&nbsp;&nbsp;&nbsp;&nbsp;(".$page["time"].") (history) &nbsp;".$page["tag"]." &rArr; ".$page["user"]." ".$note."<br />");
			}
		}
	}
	print "</p>\n";
}
?>