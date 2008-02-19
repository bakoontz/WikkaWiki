<?php

if ($comments = $this->LoadRecentlyCommented())
{
	$curday = "";
	foreach ($comments as $comment)
	{
		// day header
		list($day, $time) = explode(" ", $comment["time"]);
		if ($day != $curday)
		{
			if ($curday) print("<br />\n");
			print("<strong>$day:</strong><br />\n");
			$curday = $day;
		}

		// print entry
		print("&nbsp;&nbsp;&nbsp;(".$comment["time"].") <a href=\"".$this->href("", $comment["page_tag"], "show_comments=1")."#".$comment["id"]."\">".$comment["page_tag"]."</a> . . . . latest comment by ".$this->Format($comment["user"])."<br />\n");
	}
}
else
{
	print("<em>There are no recently commented pages.</em>");
}

?>