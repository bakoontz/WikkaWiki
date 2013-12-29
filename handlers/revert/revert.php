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

$tag = $this->GetPageTag();
// cancel operation and return to the page
if ($this->GetSafeVar('cancel', 'post') == T_("Cancel"))
{
	$this->Redirect($this->Href());
}

if ($this->HasAccess('write'))
{
	if (NULL != $_POST)
    {
		include_once($this->BuildFullpathFromMultipath('..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'admin.lib.php', $this->GetConfigValue('action_path')));
		$comment = T_("Reverted to previous revision");
		$tag = mysql_real_escape_string($this->GetPageTag());
		$message = RevertPageToPreviousByTag($this, $tag, $comment);
		$this->Redirect($this->Href(), $message);
	}
	else
	{
		// show form
		?>
		<h3><?php printf(T_("Revert %s to previous version"),$this->Link($tag));?></h3>
		<br />

		<?php echo $this->FormOpen('revert') ?>
		<table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td><?php echo T_("Revert this page to the previous version?") ?></td>
			</tr>
			<tr>
				<td>
				<!-- nonsense input so form submission works with rewrite mode -->
				<input type="hidden" value="" name="null">
				<input name="revert" type="submit" value="<?php echo T_("Revert Page") ?>"  style="width: 120px" />
				<input type="submit" value="<?php echo T_("Cancel") ?>" name="cancel" style="width: 120px" />
				</td>
			</tr>
		</table>
		<?php
		echo $this->FormClose();
	}
}
else
{
	echo '<p><em class="error">'.T_("Sorry, you don't have privileges to revert this page").'</em></p>'."\n";
}
?>
</div>
