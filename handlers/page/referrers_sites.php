<?php
/**
 * Show domains linking to the page/ the wiki.
 * 
 * @package		Handlers
 * @subpackage	Referrers	
 * @version		$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses  Config::$referrers_purge_time
 * @uses		Wakka::IsAdmin()
 * @uses		Wakka::LoadReferrers()
 * @uses		Wakka::GetConfigValue()
 * @uses		Wakka::GetMethod()
 * @uses		Wakka::GetUser()
 * @uses		Wakka::Href()
 * 
 * @todo		move <div> to template
 */
//i18n
if (!defined('REFERRERS_SITES_DOMAINS_LINKING_TO')) define('REFERRERS_SITES_DOMAINS_LINKING_TO', 'Domains/sites linking to this wiki (<a href="%s">see list of different URLs</a>):');
if (!defined('REFERRERS_PURGE_24_HOURS')) define('REFERRERS_PURGE_24_HOURS', 'last 24 hours');
if (!defined('REFERRERS_PURGE_N_DAYS')) define('REFERRERS_PURGE_N_DAYS', 'last %d days');
if (!defined('REFERRERS_SITES_EXTERNAL_PAGES')) define('REFERRERS_SITES_EXTERNAL_PAGES', 'Domains/sites pages linking to %1$s%2$s (<a href="%3$s">see list of different URLs</a>):');
if (!defined('REFERRERS_NO_SPAM')) define('REFERRERS_NO_SPAM', '<em>Note to spammers: This page is not indexed by search engines, so don\'t waste your time.</em><br /><br />');
if (!defined('LABEL_BLACKLIST')) define('LABEL_BLACKLIST', 'Blacklist');
if (!defined('LABEL_NONE')) define('LABEL_NONE', 'None');
if (!defined('LABEL_PLEASE_LOGIN')) define('LABEL_PLEASE_LOGIN', 'You need to login to see referring sites');
if (!defined('REFERRERS_CHOICE')) define('REFERRERS_CHOICE', '<a href="%1$s">View referring sites for %2$s only</a> | <a href="%3$s">View referrers for %2$s only</a> | <a href="%4$s">View referrer blacklist</a>');
if (!defined('REFERRERS_CHOICE_GLOBAL')) define('REFERRERS_CHOICE_GLOBAL', '<a href="%1$s">View global referring sites</a> | <a href="%2$s">View global referrers</a> | <a href="%3$s">View referrer blacklist</a>');

echo '<div class="page">'."\n"; //TODO: move to templating class

$global = '';
$IsAdmin = $this->IsAdmin();
if (isset($_REQUEST["global"]))
{
	$global = $_REQUEST["global"];
	$title = sprintf(REFERRERS_SITES_DOMAINS_LINKING_TO, $this->Href("referrers", "", "global=1"));
	$referrers = $this->LoadReferrers();
}
else
{
	switch (intval($this->GetConfigValue('referrers_purge_time')))
	{
		case 0: 
			$referrers_purge_time = '';
			break;
		case 1:
			$referrers_purge_time = ' '.REFERRERS_PURGE_24_HOURS;
			break;
		default:
			$referrers_purge_time = sprintf(' '.REFERRERS_PURGE_N_DAYS, $this->GetConfigValue('referrers_purge_time'));
	}
	$title = sprintf(REFERRERS_SITES_EXTERNAL_PAGES, $this->Link($this->GetPageTag()), $referrers_purge_time, $this->Href('referrers'));
	$referrers = $this->LoadReferrers($this->GetPageTag());
}

print("<strong>$title</strong><br />\n");
print(REFERRERS_NO_SPAM);

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
			print("<td valign=\"top\">" . (($site != "unknown") ? "<a href=\"http://".$this->htmlspecialchars_ent($site)."\">".$this->htmlspecialchars_ent($site)."</a>" : $site) . "</a> ".($IsAdmin ? "[<a href=\"".$this->href("delete_referrer", "", "spam_site=").$this->htmlspecialchars_ent($site)."&redirect=".$this->GetMethod().'">'.LABEL_BLACKLIST."</a>]" : "")."</td>");
			print("</tr>\n");
		}
		print("</table>\n");
	}
	else
	{
		print('<em>'.LABEL_NONE."</em><br />\n");
	}
}
else
{
	print('<em>'.LABEL_PLEASE_LOGIN."</em><br />\n");
}

if ($global !== '')
{
	printf('<br />['.REFERRERS_CHOICE.']', $this->Href('referrers_sites'), $this->GetPageTag(), $this->Href('referrers'), $this->Href('review_blacklist'));
}
else
{
	printf('<br />['.REFERRERS_CHOICE_GLOBAL.']', $this->href("referrers_sites", "", "global=1"), $this->href("referrers", "", "global=1"), $this->href("review_blacklist"));
}

echo '</div>'."\n" //TODO: move to templating class
?>
