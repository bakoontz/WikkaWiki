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
 * @todo		make valid XHTML (can't mix table with ul)
 * @todo	replace $_REQUEST with either $_GET or $_POST (or both if really
 * 			necessary) - #312  
 * @todo		replace with new handlers - #1
 */

$IsAdmin = $this->IsAdmin();

#if ($IsAdmin && isset($_REQUEST["whitelist"]))
if ($IsAdmin && isset($_GET["whitelist"]))
{
	#$whitelist = $_REQUEST["whitelist"];
	$whitelist = $_GET['whitelist'];
	$this->Query('DELETE FROM '.$this->config['table_prefix'].'referrer_blacklist WHERE spammer = "'.mysql_real_escape_string($whitelist).'"');
	$this->Redirect($this->Href("review_blacklist"));
}

// set up output variables
$ref_domains_to_wiki_url = $this->Href('referrers_sites','','global=1');
$ref_urls_to_wiki_url = $this->Href('referrers','','global=1');
$ref_domains_to_wiki_link = '<a href="'.$ref_domains_to_wiki_url.'">'.REFERRERS_DOMAINS_TO_WIKI_LINK_DESC.'</a>';
$ref_urls_to_wiki_link = '<a href="'.$ref_urls_to_wiki_url.'">'.REFERRERS_URLS_TO_WIKI_LINK_DESC.'</a>';
$menu = '['.$ref_domains_to_wiki_link.' | '.$ref_urls_to_wiki_link.']';

// get data
$blacklist = $this->LoadAll("SELECT * FROM ".$this->config["table_prefix"]."referrer_blacklist");

echo '<div class="page">'."\n"; //TODO: move to templating class
echo '<strong>'.BLACKLIST_HEADING.'</strong><br /><br />'."\n";

// present data
if ($blacklist)
{
	echo '<table border="0" cellspacing="0" cellpadding="0">'."\n";
	foreach ($blacklist as $spammer)
	{
		echo '<tr>'."\n";
		echo '<td valign="top"><li>'.$spammer['spammer'].' '.($IsAdmin ? '[<a href="'.$this->Href('review_blacklist', '', 'whitelist=').$this->htmlspecialchars_ent($spammer['spammer']).'">'.BLACKLIST_REMOVE_LINK_DESC.'</a>]' : '').'</li></td>'."\n";
		echo '</tr>'."\n";
	}
	echo '</table><br />'."\n";
}
else
{
	echo '<em>'.STATUS_BLACKLIST_EMPTY.'</em><br /><br />'."\n";
}

echo '<br />'.$menu;
echo '</div>'."\n" //TODO: move to templating class
?>