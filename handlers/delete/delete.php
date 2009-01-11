<?php
/**
 * Delete a page if the user is an admin.
 *
 * @package		Handlers
 * @subpackage	Page
 * @version		$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	WIKKA_BASE_URL
 * @uses	Wakka::FormOpen()
 * @uses	Wakka::FormClose()
 * @uses	Wakka::IsAdmin()
 * @uses	Wakka::UserIsOwner()
 * @uses	Wakka::GetConfigValue()
 * @uses	Wakka::Link()
 * @uses	Wakka::Query()
 * @uses	Wakka::Redirect()
 *
 * @todo	don't show cancel button when JavaScript is not active
 * @todo	avoid layout table (there are not even virtual columns!)
 * @todo	check if the "nonsense input" is really needed for rewrite mode;
 * 			if not (likely) remove!
 */

#$tag = $this->GetPageTag();
$tag = $this->tag;

if ($this->IsAdmin() || ($this->UserIsOwner($tag) && (bool) $this->GetConfigValue('owner_delete_page')))
{

	if(isset($_POST['cancel']) && ($_POST['cancel'] == PAGE_DELETION_CANCEL_BUTTON))
	{
		$this->Redirect($this->Href());
	}

	if (isset($_POST['delete']) && $_POST['delete'] == PAGE_DELETION_DELETE_BUTTON) // delete button pressed
	{
		// delete the page, comments, related "from" links, acls and referrer
		// @@@ format queries
		$this->Query("DELETE FROM ".$this->GetConfigValue('table_prefix')."pages WHERE tag = '".mysql_real_escape_string($tag)."'");
		$this->Query("DELETE FROM ".$this->GetConfigValue('table_prefix')."comments WHERE page_tag = '".mysql_real_escape_string($tag)."'");
		$this->Query("DELETE FROM ".$this->GetConfigValue('table_prefix')."links WHERE FROM_tag = '".mysql_real_escape_string($tag)."'");
		$this->Query("DELETE FROM ".$this->GetConfigValue('table_prefix')."acls WHERE page_tag = '".mysql_real_escape_string($tag)."'");
		$this->Query("DELETE FROM ".$this->GetConfigValue('table_prefix')."referrers WHERE page_tag = '".mysql_real_escape_string($tag)."'");

		// redirect back to main page
		#$this->Redirect($this->GetConfigValue('base_url'), SUCCESS_PAGE_DELETED);
		$this->Redirect(WIKKA_BASE_URL, SUCCESS_PAGE_DELETED);
	}
	else
	{
		echo '<div class="page">'."\n";

		// show form
		?>
		<h3><?php printf(PAGE_DELETION_HEADER,$this->Link($tag));?></h3>
		<br />

		<?php echo $this->FormOpen('delete') ?>
		<table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td><?php echo PAGE_DELETION_CAPTION ?></td>
			</tr>
			<tr>
				<td>
				<input name="delete" type="submit" value="<?php echo PAGE_DELETION_DELETE_BUTTON ?>"  style="width: 120px" />
				<input type="submit" value="<?php echo PAGE_DELETION_CANCEL_BUTTON ?>" name="cancel" style="width: 120px" />
				</td>
			</tr>
		</table>
		<?php
		echo $this->FormClose();

		echo '</div>'."\n";
	}
}
else
{
	echo '<div class="page">'."\n";
	echo '<em class="error">'.ERROR_NO_PAGE_DEL_ACCESS.'</em>';
	echo '</div>'."\n";
}
?>
