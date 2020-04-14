<?php
/**
 * Display a box with information on the last edit.
 *
 * @package		Actions
 * @version		$Id:lastedit.php 369 2007-03-01 14:38:59Z DarTar $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli} (first draft)
 * @author		{@link http://wikkawiki.org/MinusF MinusF} (code cleanup and validation)
 * @since		Wikka 1.1.6.0
 *
 * @input		integer  $show  optional: amount of details to be displayed;
 *				default: 3
 *				0: show user only
 *				1: show user and notes (if available)
 *				2: show user, notes (if available) and date
 *				3: show user, notes (if available), date and quickdiff link
 * @output		box with lastedit information
 *
 * @uses	Wakka::Link()
 * @uses	Wakka::GetPageTag()
 * @uses	Wakka::Href()
 * @uses	Wakka::htmlspecialchars_ent()
 * @uses	Wakka::FormatUser()
 *
 * @todo		make date/time format system-configurable;
 */
if (!defined('DEFAULT_SHOW')) define('DEFAULT_SHOW', '3');
if (!defined('DATE_FORMAT')) define('DATE_FORMAT', 'D, d M Y'); #TODO make this system-configurable
if (!defined('TIME_FORMAT')) define('TIME_FORMAT', 'H:i T'); #TODO make this system-configurable

//Initialisation to avoid notices
$difflink = '';
$dateformatted = '';
$timeformatted = '';
$note = '';

if (!isset($show))
{
	$show = DEFAULT_SHOW;
}

if ($this->GetHandler() == 'show')
{
	$page = $this->page;
	$pagetag = $page['tag'];
	$user = $this->FormatUser($page['user']);

	switch($show)
	{
		case 3:
		$oldpage = $this->LoadSingle("SELECT * FROM ".$this->GetConfigValue('table_prefix')."pages WHERE tag='".$this->GetPageTag()."' AND latest = 'N' ORDER BY time desc LIMIT 1");
		$newid = $page['id'];
		$oldid = $oldpage['id'];
		$difflink = ' [<a title="'.T_("Show differences from last revision").'" href="'.$this->Href('diff', $pagetag, 'a='.$page['id'].'&amp;b='.$oldpage['id'].'&amp;fastdiff=1').'">diff</a>]';

		case 2:
		list($day, $time) = explode(' ', $page['time']);
		$dateformatted = date(DATE_FORMAT, strtotime($day));
		$timeformatted = date(TIME_FORMAT, strtotime($page['time']));

		case 1:
		$note = ($page['note']) ? ':<br/><span class="'.T_("lastedit_notes").'">'.
		$this->htmlspecialchars_ent($page['note']).'</span>' : '';

		default:
	}
	echo '<div class="'.T_("lastedit").'">'.sprintf(T_("Last edited by %s"), $user).$note.'<br /> '.$dateformatted.' '.$timeformatted.$difflink.'</div>';
}
?>
