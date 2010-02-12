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
 */

$IsAdmin = $this->IsAdmin();

echo '<div id="content">'."\n"; //TODO: move to templating class

if ($IsAdmin && isset($_GET["whitelist"]))
{
	$whitelist = $this->GetSafeVar('whitelist');
	$this->Query('DELETE FROM '.$this->GetConfigValue('table_prefix').'referrer_blacklist WHERE spammer = "'.mysql_real_escape_string($whitelist).'"');
	$this->redirect($this->Href('review_blacklist'));
}
else
{
	echo '<strong>'.BLACKLIST_HEADING.'</strong><br /><br />'."\n";
	$blacklist = $this->LoadAll('SELECT * FROM '.$this->GetConfigValue('table_prefix').'referrer_blacklist');

	if ($blacklist)
	{
		echo '<table border="0" cellspacing="0" cellpadding="0">'."\n";
		foreach ($blacklist as $spammer)
		{
			echo '<tr>'."\n";
			echo '<td valign="top">'.$spammer['spammer'].' '.($IsAdmin ? '[<a href="'.$this->Href('review_blacklist', '', 'whitelist=').$this->htmlspecialchars_ent($spammer['spammer']).'">'.BLACKLIST_REMOVE_LINK_DESC.'</a>]' : '').'</td>'."\n";
			echo '</tr>'."\n";
		}
		echo '</table><br />'."\n";
	}
	else
	{
		echo '<em class="error">'.STATUS_BLACKLIST_EMPTY.'</em><br /><br />'."\n";
	}
}

echo '<br />[<a href="'.$this->Href('referrers_sites', '', 'global=1').'">'.BLACKLIST_VIEW_GLOBAL_SITES.'</a> | <a href="'.$this->Href('referrers', '', 'global=1').'">'.BLACKLIST_VIEW_GLOBAL.'</a>]'."\n";

echo '</div>'."\n" //TODO: move to templating class
?>
