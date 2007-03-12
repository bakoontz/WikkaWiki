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
 * @uses		Wakka::GetHandler()
 * @uses		Wakka::GetUser()
 * @uses		Wakka::Href()
 * @uses		Wakka::htmlspecialchars_ent()
 * 
 * @todo		move <div> to template
 * @todo		better separation between data gathering and output
 * @todo		$heading should become heading, not bold text
 * @todo	replace $_REQUEST with either $_GET or $_POST (or both if really
 * 			necessary) - #312  
 */

$global = '';
$IsAdmin = $this->IsAdmin();

// set up output variables
$thispage = $this->GetPageTag();
$ref_domains_to_wiki_url = $this->Href('referrers_sites','','global=1');
$ref_domains_to_page_url = $this->Href('referrers_sites');
$ref_urls_to_wiki_url = $this->Href('referrers','','global=1');
$ref_urls_to_page_url = $this->Href('referrers');
$ref_blacklist_url = $this->Href('review_blacklist');
#if (isset($_REQUEST["global"]))
if (isset($_GET["global"])) #312
{
	// referrers to this wiki
	// prepare UI
	#$global = $_REQUEST["global"];
	$global = $_GET["global"]; #312
	$referrers_urls_link = '<a href="'.$ref_urls_to_wiki_url.'">'.REFERRERS_URLS_LINK_DESC.'</a>';
	$heading = sprintf(REFERRERS_DOMAINS_TO_WIKI, $referrers_urls_link);

	$ref_domains_to_page_link = '<a href="'.$ref_domains_to_page_url.'">'.sprintf(REFERRERS_DOMAINS_TO_PAGE_LINK_DESC,$thispage).'</a>';
	$ref_urls_to_page_link = '<a href="'.$ref_urls_to_page_url.'">'.sprintf(REFERRERS_URLS_TO_PAGE_LINK_DESC,$thispage).'</a>';
	$ref_blacklist_link = '<a href="'.$ref_blacklist_url.'">'.REFERRER_BLACKLIST_LINK_DESC.'</a>';
	$menu = '['.$ref_domains_to_page_link.' | '.$ref_urls_to_page_link.' | '.$ref_blacklist_link.']';
}
else
{
	// referrers to this page
	// prepare UI
	switch (intval($this->GetConfigValue('referrers_purge_time')))
	{
		case 0: 
			$referrers_purge_time = '';
			break;
		case 1:
			$referrers_purge_time = REFERRERS_PURGE_24_HOURS;
			break;
		default:
			$referrers_purge_time = sprintf(REFERRERS_PURGE_N_DAYS, $this->GetConfigValue('referrers_purge_time'));
	}
	$referrers_urls_link = '<a href="'.$ref_urls_to_page_url.'">'.REFERRERS_URLS_LINK_DESC.'</a>';
	$heading = sprintf(REFERRERS_DOMAINS_TO_PAGE, $this->Link($thispage), $referrers_purge_time, $referrers_urls_link);

	$ref_domains_to_wiki_link = '<a href="'.$ref_domains_to_wiki_url.'">'.REFERRERS_DOMAINS_TO_WIKI_LINK_DESC.'</a>';
	$ref_urls_to_wiki_link = '<a href="'.$ref_urls_to_wiki_url.'">'.REFERRERS_URLS_TO_WIKI_LINK_DESC.'</a>';
	$ref_blacklist_link = '<a href="'.$ref_blacklist_url.'">'.REFERRER_BLACKLIST_LINK_DESC.'</a>';
	$menu = '['.$ref_domains_to_wiki_link.' | '.$ref_urls_to_wiki_link.' | '.$ref_blacklist_link.']';
}

echo '<div class="page">'."\n"; //TODO: move to templating class
echo '<strong>'.$heading.'</strong><br />'."\n";
echo '<em>'.REFERRERS_NO_SPAM.'</em><br /><br />'."\n";

if ($this->GetUser())
{
	// get data
	$referrers = ($global !== '') ? $this->LoadReferrers() : $this->LoadReferrers($thispage);
	if ($referrers)
	{
		// produce statistics
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

		// present data
		print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n");
		foreach ($referrer_sites as $site => $site_count)
		{
			$site_esc = $this->htmlspecialchars_ent($site);
			print("<tr>");
			print("<td width=\"30\" align=\"right\" valign=\"top\" style=\"padding-right: 10px\">$site_count</td>");
			print("<td valign=\"top\">" . (($site != "unknown") ? "<a href=\"http://".$site_esc."\">".$site_esc."</a>" : $site) . "</a> ".($IsAdmin ? "[<a href=\"".$this->href("delete_referrer", "", "spam_site=").$site_esc."&amp;redirect=".$this->GetHandler().'">'.BLACKLIST_LINK_DESC."</a>]" : "")."</td>");
			print("</tr>\n");
		}
		print("</table>\n");
	}
	else
	{
		print('<em>'.NONE_CAPTION."</em><br />\n");
	}
}
else
{
	print('<em>'.PLEASE_LOGIN_CAPTION."</em><br />\n");
}

echo '<br />'.$menu;
echo '</div>'."\n" //TODO: move to templating class
?>
