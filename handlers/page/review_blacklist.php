<?php
/**
 * Show a list of blacklisted referrers.
 * 
 * Admins have the possibility to remove entries.
 * 
 * @package		Handlers
 * @subpackage	Referrers
 * @version		$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses		Wakka::IsAdmin()
 * @uses		Wakka::Href()
 * @uses		Wakka::htmlspecialchars_ent()
 * @uses		Wakka::LoadAll()
 * @uses		Wakka::Query()
 * @uses		Wakka::redirect()
 * 
 * @todo		move main <div> to templating class
 */
echo '<div class="page">'."\n"; //TODO: move to templating class

$IsAdmin = $this->IsAdmin();
if ($IsAdmin && isset($_REQUEST["whitelist"]))
{
	$whitelist = $_REQUEST["whitelist"];
	$this->Query("DELETE FROM ".$this->config["table_prefix"]."referrer_blacklist WHERE spammer = '".mysql_real_escape_string($whitelist)."'");
	$this->redirect($this->Href("review_blacklist"));
}
else
{
	print("<strong>Referrer Blacklist:</strong><br /><br />\n"); # i18n
	$blacklist = $this->LoadAll("SELECT * FROM ".$this->config["table_prefix"]."referrer_blacklist");

	if ($blacklist)
	{
		print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><ul>\n");
		foreach ($blacklist as $spammer)
		{
			print("<tr>");
			print("<td valign=\"top\"><li>".$spammer["spammer"]." ".($IsAdmin ? "[<a href=\"".$this->Href("review_blacklist", "", "whitelist=").$this->htmlspecialchars_ent($spammer["spammer"])."\">Remove</a>]" : "")."</li></td>");
			print("</tr>\n");
		}
		print("</ul></table><br />\n");
	}
	else
	{
		print("<em>Blacklist is empty.</em><br /><br />\n"); # i18n
	}
}

print("<br />[<a href=\"".$this->Href("referrers_sites", "", "global=1")."\">View global referring sites</a> | <a href=\"".$this->Href("referrers", "", "global=1")."\">View global referrers</a>]"); # i18n

echo '</div>'."\n" //TODO: move to templating class
?>