<?php
/**
 * Show external referrers linking to the current page or to this wiki.
 * 
 * @package		Handlers
 * @subpackage	Referrers
 * @version		$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses  Config::$referrers_purge_time
 * @uses		Wakka::GetConfigValue()
 * @uses		Wakka::GetPageTag()
 * @uses		Wakka::GetUser()
 * @uses		Wakka::GetHandler()
 * @uses		Wakka::Href()
 * @uses		Wakka::htmlspecialchars_ent()
 * @uses		Wakka::IsAdmin()
 * @uses		Wakka::Link
 * @uses		Wakka::LoadReferrers()
 * 
 * @todo		move main <div> to templating class
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
	$referrers_domains_link = '<a href="'.$ref_domains_to_wiki_url.'">'.REFERRERS_DOMAINS_LINK_DESC.'</a>';
	$heading = sprintf(REFERRERS_URLS_TO_WIKI, $referrers_domains_link);

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
			$referrers_purge_time = ' '.REFERRERS_PURGE_24_HOURS;
			break;
		default:
			$referrers_purge_time = sprintf(' '.REFERRERS_PURGE_N_DAYS, $this->GetConfigValue('referrers_purge_time'));
	}
	$referrers_domains_link = '<a href="'.$ref_domains_to_page_url.'">'.REFERRERS_DOMAINS_LINK_DESC.'</a>';
	$heading = sprintf(REFERRERS_URLS_TO_PAGE, $this->Link($thispage), $referrers_purge_time, $referrers_domains_link);

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
		// present data
		print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n");
		foreach ($referrers as $referrer)
		{
			print("<tr>");
			print("<td width=\"30\" align=\"right\" valign=\"top\" style=\"padding-right: 10px\">".$referrer["num"]."</td>");
			print("<td valign=\"top\"><a href=\"".$this->htmlspecialchars_ent($referrer["referrer"])."\">".$this->htmlspecialchars_ent($referrer["referrer"])."</a> ".($IsAdmin ? "[<a href=\"".$this->Href("delete_referrer", "", "spam_link=").$this->htmlspecialchars_ent($referrer["referrer"])."&amp;redirect=".$this->GetHandler()."\">".BLACKLIST_LINK_DESC."</a>]" : "")."</td>");
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
