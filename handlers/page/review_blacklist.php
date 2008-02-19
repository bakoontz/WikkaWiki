<div class="page">
<?php

$IsAdmin = $this->IsAdmin();
if ($IsAdmin && ($whitelist = $_REQUEST["whitelist"]))
{
	$this->Query("DELETE FROM ".$this->config["table_prefix"]."referrer_blacklist WHERE spammer = '".mysql_real_escape_string($whitelist)."'");
	$this->redirect($this->href("review_blacklist"));
}
else
{
	print("<strong>Referrer Blacklist:</strong><br /><br />\n");
	$blacklist = $this->LoadAll("SELECT * FROM ".$this->config["table_prefix"]."referrer_blacklist");

	if ($blacklist)
	{
		print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><ul>\n");
		foreach ($blacklist as $spammer)
		{
			print("<tr>");
			print("<td valign=\"top\"><li>".$spammer["spammer"]." ".($IsAdmin ? "[<a href=\"".$this->href("review_blacklist", "", "whitelist=").htmlspecialchars($spammer["spammer"])."\">Remove</a>]" : "")."</li></td>");
			print("</tr>\n");
		}
		print("</ul></table><br />\n");
	}
	else
	{
		print("<em>Blacklist is empty.</em><br /><br />\n");
	}
}

print("<br />[<a href=\"".$this->href("referrers_sites", "", "global=1")."\">View global referring sites</a> | <a href=\"".$this->href("referrers", "", "global=1")."\">View global referrers</a>]");

?>
</div>
