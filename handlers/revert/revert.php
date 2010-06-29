<div id="content">
<?php

/**
 * Revert to revision immediately preceding current version of page
 *
 * This handler reverts the current version of a page to the version 
 * immediately preceding the current version. The previous version is
 * re-created as the new version for auditing purposes. An optional GET 
 * parameter, "comment", is permitted:
 *
 * .../SomePage/revert?comment=Replaces%20spammed%20page
 *
 * @name	    Revert	
 *
 * @package		Handlers
 * @subpackage  Page
 * @version		$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @since		Wikka 1.1.6.4
 * @filesource
 *
 * @author		{@link http://wikkawiki.org/BrianKoontz Brian Koontz}
 *
 * Based upon the Delete handler written by DarTar, NilsLindenberg,
 * and MinusF
 *
 * @uses Wakka::IsAdmin()
 * @uses Wakka::htmlspecialchars_ent()
 * @uses RevertPageToPreviousByTag()
 * @uses Wakka::GetPageTag()
 * @uses Wakka::Redirect()
 *
 */

if (TRUE===$this->IsAdmin())
{
	include_once($this->BuildFullpathFromMultipath('..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'admin.lib.php', $this->GetConfigValue('action_path')));
	$comment = REVERT_DEFAULT_COMMENT;
	if(TRUE===isset($_GET['comment']))
	{
		$comment = $this->GetSafeVar('comment', 'get');
	}
	$tag = mysql_real_escape_string($this->GetPageTag());
	$message = RevertPageToPreviousByTag($this, $tag, $comment);
	$this->Redirect($this->Href(), $message);
}
else
{
	echo '<p><em class="error">'.ERROR_NO_REVERT_PRIVS.'</em></p>'."\n";
}
?>
</div>
