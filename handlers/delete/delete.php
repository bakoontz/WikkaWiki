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
 * @uses		Wakka::FormOpen()
 * @uses		Wakka::FormClose()
 * @uses		Wakka::GetPageTag()
 * @uses		Wakka::IsAdmin()
 * @uses		Wakka::Link()
 * @uses		Wakka::Query()
 * @uses		Wakka::Redirect()
 *
 * @todo		don't show cancel button when JavaScript is not active
 */

echo '<div class="page">'."\n";

$tag = $this->GetPageTag();

if ($this->IsAdmin() || ($this->UserIsOwner($tag) && $this->GetConfigValue('owner_delete_page') == 1))
{
	if ($_POST)
	{
		// delete the page, comments, related links, acls and referrer
		// @@@ format queries
		$this->Query("DELETE FROM ".$this->GetConfigValue('table_prefix')."pages WHERE tag = '".mysql_real_escape_string($tag)."'");
		$this->Query("DELETE FROM ".$this->GetConfigValue('table_prefix')."comments WHERE page_tag = '".mysql_real_escape_string($tag)."'");
		$this->Query("DELETE FROM ".$this->GetConfigValue('table_prefix')."links WHERE FROM_tag = '".mysql_real_escape_string($tag)."'");
		$this->Query("DELETE FROM ".$this->GetConfigValue('table_prefix')."acls WHERE page_tag = '".mysql_real_escape_string($tag)."'");
		$this->Query("DELETE FROM ".$this->GetConfigValue('table_prefix')."referrers WHERE page_tag = '".mysql_real_escape_string($tag)."'");

		// redirect back to main page
		#$this->Redirect($this->GetConfigValue('base_url'), SUCCESS_PAGE_DELETED);
		$this->Redirect($this->base_url, SUCCESS_PAGE_DELETED);
	}
	else
	{
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
				<td> <!-- nonsense input so form submission works with rewrite mode --><input type="hidden" value="" name="null">
				<input type="submit" value="<?php echo PAGE_DELETION_DELETE_BUTTON ?>"  style="width: 120px" />
				<input type="button" value="<?php echo PAGE_DELETION_CANCEL_BUTTON ?>" onclick="history.back();" style="width: 120px" />
				</td>
			</tr>
		</table>
		<?php
		print($this->FormClose());
	}
}
else
{
	echo '<em class="error">'.ERROR_NO_PAGE_DEL_ACCESS.'</em>';
}
echo '</div>'."\n"
?>