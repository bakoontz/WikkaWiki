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
 * @uses		Wakka::GetMethod()
 * @uses		Wakka::Href()
 * @uses		Wakka::htmlspecialchars_ent()
 * @uses		Wakka::IsAdmin()
 * @uses		Wakka::Link
 * @uses		Wakka::LoadReferrers()
 * 
 * @todo		move main <div> to templating class
 * @todo		better separation between data gathering and output
 * @todo		$heading should become heading, not bold text
 */

$global = '';
$IsAdmin = $this->IsAdmin();

#if ($global = $_REQUEST["global"])
if (isset($_GET["global"])) #312
{
	// referrers to this wiki
	// prepare UI
	#$global = $_REQUEST["global"];
	$global = $_GET["global"]; #312
	$title = "Sites linking to this wiki (<a href=\"".$this->Href("referrers_sites", "", "global=1")."\">see list of domains</a>):";
	$referrers = $this->LoadReferrers();
}
else
{
	// referrers to this page
	// prepare UI
	$title = "External pages linking to ".$this->Link($this->GetPageTag()).
		($this->GetConfigValue("referrers_purge_time") ? " (last ".($this->GetConfigValue("referrers_purge_time") == 1 ? "24 hours" : $this->GetConfigValue("referrers_purge_time")." days").")" : "")." (<a href=\"".$this->Href("referrers_sites")."\">see list of domains</a>):";
	$referrers = $this->LoadReferrers($this->GetPageTag());
}

echo '<div class="page">'."\n"; //TODO: move to templating class

echo '<strong>'.$title.'</strong><br />'."\n";
echo '<em>Note to spammers: This page is not indexed by search engines, so don\'t waste your time.</em><br /><br />'."\n";

if ($this->GetUser())
{
	if ($referrers)
	{
		echo '<table border="0" cellspacing="0" cellpadding="0">'."\n";
		foreach ($referrers as $referrer)
		{
			echo '<tr>'."\n";
			echo '<td width="30" align="right" valign="top" style="padding-right: 10px">'.$referrer['num'].'</td>'."\n";
			echo '<td valign="top"><a href="'.$this->htmlspecialchars_ent($referrer['referrer']).'">'.$this->htmlspecialchars_ent($referrer['referrer']).'</a> '.($IsAdmin ? '[<a href="'.$this->href('delete_referrer', '', 'spam_link=').$this->htmlspecialchars_ent($referrer['referrer']).'&redirect='.$this->GetMethod().'">Blacklist</a>]' : '').'</td>'."\n";
			echo '</tr>'."\n";
		}
		echo '</table>'."\n";
	}
	else
	{
		echo '<em>None</em><br />'."\n";
	}
}
else
{
	echo '<em>You need to login to see referring sites</em><br />'."\n";
}

if ($global)
{
	echo '<br />[<a href="'.$this->href('referrers_sites').'">View referring sites for '.$this->GetPageTag().' only</a> | <a href="'.$this->href('referrers').'">View referrers for '.$this->GetPageTag().' only</a> | <a href="'.$this->href('review_blacklist').'">View referrer blacklist</a>]'."\n";
}
else
{
	echo '<br />[<a href="'.$this->href('referrers_sites', '', 'global=1').'">View global referring sites</a> | <a href="'.$this->href('referrers', '', 'global=1').'">View global referrers</a> | <a href="'.$this->href('review_blacklist').'">View referrer blacklist</a>]'."\n";
}

echo '</div>'."\n" //TODO: move to templating class
?>