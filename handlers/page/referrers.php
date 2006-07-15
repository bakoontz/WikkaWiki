<?php
/**
 * Show external referrers linking to the current page or to this wiki.
 * 
 * @package		Handlers
 * @subpackage	Referrers
 * @version		$Id$
 * 
 * @uses		Wakka::GetConfigValue()
 * @uses		Wakka::GetPageTag()
 * @uses		Wakka::GetUser()
 * @uses		Wakka::Href()
 * @uses		Wakka::htmlspecialchars_ent()
 * @uses		Wakka::IsAdmin()
 * @uses		Wakka::Link
 * @uses		Wakka::LoadReferrers()
 * 
 * @todo		move main <div> to templating class
 * @filesource
 */
echo '<div class="page">'."\n"; //TODO: move to templating class

$global = '';
$IsAdmin = $this->IsAdmin();
if (isset($_REQUEST["global"]))
{
	$global = $_REQUEST["global"];
	$title = "Sites linking to this wiki (<a href=\"".$this->Href("referrers_sites", "", "global=1")."\">see list of domains</a>):"; # i18n
	$referrers = $this->LoadReferrers();
}
else
{
	$title = "External pages linking to ".$this->Link($this->GetPageTag()).
		($this->GetConfigValue("referrers_purge_time") ? " (last ".($this->GetConfigValue("referrers_purge_time") == 1 ? "24 hours" : $this->GetConfigValue("referrers_purge_time")." days").")" : "")." (<a href=\"".$this->Href("referrers_sites")."\">see list of domains</a>):"; # i18n
	$referrers = $this->LoadReferrers($this->GetPageTag());
}

print("<strong>$title</strong><br />\n");
print("<em>Note to spammers: This page is not indexed by search engines, so don't waste your time.</em><br /><br />"); # i18n

if ($this->GetUser()) {
	if ($referrers)
	{
		print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n");
		foreach ($referrers as $referrer)
		{
			print("<tr>");
			print("<td width=\"30\" align=\"right\" valign=\"top\" style=\"padding-right: 10px\">".$referrer["num"]."</td>");
			print("<td valign=\"top\"><a href=\"".$this->htmlspecialchars_ent($referrer["referrer"])."\">".$this->htmlspecialchars_ent($referrer["referrer"])."</a> ".($IsAdmin ? "[<a href=\"".$this->href("delete_referrer", "", "spam_link=").$this->htmlspecialchars_ent($referrer["referrer"])."&redirect=".$this->GetMethod()."\">Blacklist</a>]" : "")."</td>");
			print("</tr>\n");
		}
		print("</table>\n");
	}
	else
	{
		print("<em>None</em><br />\n");
	}
} else {
	print("<em>You need to login to see referring sites</em><br />\n");
}

if ($global !== '')
{
	print("<br />[<a href=\"".$this->href("referrers_sites")."\">View referring sites for ".$this->GetPageTag()." only</a> | <a href=\"".$this->href("referrers")."\">View referrers for ".$this->GetPageTag()." only</a> | <a href=\"".$this->href("review_blacklist")."\">View referrer blacklist</a>]"); #18n
}
else
{
	print("<br />[<a href=\"".$this->href("referrers_sites", "", "global=1")."\">View global referring sites</a> | <a href=\"".$this->href("referrers", "", "global=1")."\">View global referrers</a> | <a href=\"".$this->href("review_blacklist")."\">View referrer blacklist</a>]"); # i18n
}

echo '</div>'."\n" //TODO: move to templating class
?>