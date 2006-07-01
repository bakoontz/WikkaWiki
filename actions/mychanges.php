<?php
/**
 * Shows a logged-in user a list of pages he has edited.
 * 
 * If a user is logged-in and has at least changed one page, the changed 
 * page(s) are presented as a list, ordered either alpabetically or after 
 * date and time (last edit first).
 * 
 * @package Actions
 * @name	mychanges.php
 * 
 * @author Carlo Zottmann
 * @version	$Id$
 * 
 * @uses	Wakka::GetUser()
 * @uses	Wakka::LoadAll()
 * @uses	Wakka::Link()
 * @uses	Wakka::href()
 * @uses	Wakka::GetUserName()
 */

if ($user = $this->GetUser())
{
	$my_edits_count = 0;

	if ($_REQUEST["alphabetically"] == 1)
	{
		print("<strong>This is a list of pages you've edited, along with the time of your last change (<a href=\"".$this->href("", $tag)."\">order by date</a>).</strong><br /><br />\n");	#i18n

		if ($pages = $this->LoadAll("SELECT tag, time FROM ".$this->config["table_prefix"]."pages WHERE user = '".mysql_real_escape_string($this->GetUserName())."' ORDER BY tag ASC, time DESC"))
		{
			foreach ($pages as $page)
			{
				if ($last_tag != $page["tag"]) {
					$last_tag = $page["tag"];
					$firstChar = strtoupper($page["tag"][0]);
					if (!preg_match("/[A-Z,a-z]/", $firstChar)) {
						$firstChar = "#";
					}
		
					if ($firstChar != $curChar) {
						if ($curChar) print("<br />\n");
						print("<strong>$firstChar</strong><br />\n");
						$curChar = $firstChar;
					}
	
					// print entry
					print("&nbsp;&nbsp;&nbsp;(".$page["time"].") (".$this->Link($page["tag"], "revisions", "history", 0).") ".$this->Link($page["tag"], "", "", 0)."<br />\n");
	
					$my_edits_count++;
				}
			}
			
			if ($my_edits_count == 0)
			{
				print("<em>You have not edited any pages yet.</em>"); #i18n
			}
		}
		else
		{
			print("<em>No pages found.</em>"); #i18n
		}
	}
	else
	{
		print("<strong>This is a list of pages you've edited, ordered by the time of your last change (<a href=\"".$this->href("", $tag, "alphabetically=1")."\">order alphabetically</a>).</strong><br /><br />\n"); #i18n	

		if ($pages = $this->LoadAll("SELECT tag, time FROM ".$this->config["table_prefix"]."pages WHERE user = '".mysql_real_escape_string($this->GetUserName())."' ORDER BY time ASC, tag ASC"))
		{
			foreach ($pages as $page)
			{
				$edited_pages[$page["tag"]] = $page["time"];
			}

			$edited_pages = array_reverse($edited_pages);

			foreach ($edited_pages as $page["tag"] => $page["time"])
			{
				// day header
				list($day, $time) = explode(" ", $page["time"]);
				if ($day != $curday)
				{
					if ($curday) print("<br />\n");
					print("<strong>$day:</strong><br />\n");
					$curday = $day;
				}

				// print entry
				print("&nbsp;&nbsp;&nbsp;($time) (".$this->Link($page["tag"], "revisions", "history", 0).") ".$this->Link($page["tag"], "", "", 0)."<br />\n");

				$my_edits_count++;
			}
			
			if ($my_edits_count == 0)
			{
				print("<em>You have not edited any pages yet.</em>"); #i18n
			}
		}
		else
		{
			print("<em>No pages found.</em>"); #i18n
		}
	}
}
else
{
	print("<em>You're not logged in, thus the list of pages you've edited couldn't be retrieved.</em>"); #i18n
}

?>