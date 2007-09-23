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

echo '<div class="page">'."\n"; //TODO: move to templating class

#if ($IsAdmin && isset($_REQUEST["whitelist"]))
if ($IsAdmin && isset($_GET["whitelist"]))
{
	#$whitelist = $_REQUEST["whitelist"];
	$whitelist = $_GET['whitelist'];
	$this->Query('DELETE FROM '.$this->config['table_prefix'].'referrer_blacklist WHERE spammer = "'.mysql_real_escape_string($whitelist).'"');
	$this->redirect($this->Href('review_blacklist'));
}
else
{
	echo '<strong>Referrer Blacklist:</strong><br /><br />'."\n";
	$blacklist = $this->LoadAll('SELECT * FROM '.$this->config['table_prefix'].'referrer_blacklist');

	if ($blacklist)
	{
		echo '<table border="0" cellspacing="0" cellpadding="0">'."\n";
		foreach ($blacklist as $spammer)
		{
			echo '<tr>'."\n";
			echo '<td valign="top">'.$spammer['spammer'].' '.($IsAdmin ? '[<a href="'.$this->Href('review_blacklist', '', 'whitelist=').$this->htmlspecialchars_ent($spammer['spammer']).'">Remove</a>]' : '').'</td>'."\n";
			echo '</tr>'."\n";
		}
		echo '</table><br />'."\n";
	}
	else
	{
		echo '<em>Blacklist is empty.</em><br /><br />'."\n";
	}
}

echo '<br />[<a href="'.$this->Href('referrers_sites', '', 'global=1').'">View global referring sites</a> | <a href="'.$this->Href('referrers', '', 'global=1').'">View global referrers</a>]'."\n";

echo '</div>'."\n" //TODO: move to templating class
?>