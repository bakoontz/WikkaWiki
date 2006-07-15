<?php
/**
 * Show domains linking to the page/ the wiki.
 * 
 * @package		Handlers
 * @subpackage	Referrers	
 * @version		$Id$
 * 
 * @uses		Wakka::IsAdmin()
 * @uses		Wakka::LoadReferrers()
 * @uses		Wakka::GetConfigValue()
 * @uses		Wakka::GetMethod()
 * @uses		Wakka::GetUser()
 * @uses		Wakka::Href()
 * @todo		move <div> to template
 * @filesource 
 */
echo '<div class="page">'."\n"; //TODO: move to templating class

$global = '';
$IsAdmin = $this->IsAdmin();
if (isset($_REQUEST["global"]))
{
	$global = $_REQUEST["global"];
	$title = "Domains/sites linking to this wiki (<a href=\"".$this->Href("referrers", "", "global=1")."\">see list of different URLs</a>):"; # i18n
	$referrers = $this->LoadReferrers();
}
else
{
	$title = "Domains/sites pages linking to ".$this->Link($this->GetPageTag()).
		($this->GetConfigValue("referrers_purge_time") ? " (last ".($this->GetConfigValue("referrers_purge_time") == 1 ? "24 hours" : $this->GetConfigValue("referrers_purge_time")." days").")" : "")." (<a href=\"".$this->Href("referrers")."\">see list of different URLs</a>):";
	$referrers = $this->LoadReferrers($this->GetPageTag());
}

print("<strong>$title</strong><br />\n");
print("<em>Note to spammers: This page is not indexed by search engines, so don't waste your time.</em><br /><br />"); # i18n

if ($this->GetUser()) {
	if ($referrers)
	{
		for ($a = 0; $a < count($referrers); $a++)
		{
			$temp_parse_url = parse_url($referrers[$a]["referrer"]);
			$temp_parse_url = ($temp_parse_url["host"] != "") ? strtolower(preg_replace("/^www\./Ui", "", $temp_parse_url["host"])) : "unknown";

			if (isset($referrer_sites["$temp_parse_url"]))
			{
				$referrer_sites["$temp_parse_url"] += $referrers[$a]["num"];
			}
			else
			{
				$referrer_sites["$temp_parse_url"] = $referrers[$a]["num"];
			}
		}

		array_multisort($referrer_sites, SORT_DESC, SORT_NUMERIC);
		reset($referrer_sites);

		print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n");
		foreach ($referrer_sites as $site => $site_count)
		{
			print("<tr>");
			print("<td width=\"30\" align=\"right\" valign=\"top\" style=\"padding-right: 10px\">$site_count</td>");
			print("<td valign=\"top\">" . (($site != "unknown") ? "<a href=\"http://".$this->htmlspecialchars_ent($site)."\">".$this->htmlspecialchars_ent($site)."</a>" : $site) . "</a> ".($IsAdmin ? "[<a href=\"".$this->href("delete_referrer", "", "spam_site=").$this->htmlspecialchars_ent($site)."&redirect=".$this->GetMethod()."\">Blacklist</a>]" : "")."</td>"); #i18n
			print("</tr>\n");
		}
		print("</table>\n");
	}
	else
	{
		print("<em>None</em><br />\n"); #i18n
	}
} else {
	print("<em>You need to login to see referring sites</em><br />\n"); # i18n
}

if ($global !== '')
{
	print("<br />[<a href=\"".$this->Href("referrers_sites")."\">View referring sites for ".$this->GetPageTag()." only</a> | <a href=\"".$this->Href("referrers")."\">View referrers for ".$this->GetPageTag()." only</a> | <a href=\"".$this->Href("review_blacklist")."\">View referrer blacklist</a>]"); # i18n
}
else
{
	print("<br />[<a href=\"".$this->Href("referrers_sites", "", "global=1")."\">View global referring sites</a> | <a href=\"".$this->Href("referrers", "", "global=1")."\">View global referrers</a> | <a href=\"".$this->Href("review_blacklist")."\">View referrer blacklist</a>]"); # i18n
}

echo '</div>'."\n" //TODO: move to templating class
?>