<?php
/**
 * Clone the current page and save a copy of it as a new page.
 *
 * Usage: append /clone to the URL of the page you want to clone
 *
 * This handler checks the existence of the source page, the validity of the
 * name of the target page to be created, the user's read-access to the source
 * page and write-access to the target page.
 * If the "Edit after creation" option is selected, the user is redirected to the
 * target page for editing immediately after its creation.
 * If the "Clone ACL" option is selected, ACL settings are copied to the target
 * page, otherwise default ACL are applied to the new page.
 *
 * @package 	Handlers
 * @subpackage	Page
 * @version		$Id:clone.php 407 2007-03-13 05:59:51Z DarTar $
 * @since		Wikka 1.1.6.0
 * @licens		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author		{@link http://wikkawiki.org/ChristianBarthelemy Christian Barthelemy} - original idea and code.
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli} - bugs fixed, code improved, removed popup alerts.
 * @author		{@link http://wikkawiki.org/BrianKoontz Brian Koontz} - clone ACL option
 *
 *
 * @uses	Wakka::ExistsPage()
 * @uses	Wakka::Format()
 * @uses	Wakka::FormClose()
 * @uses	Wakka::FormOpen()
 * @uses	Wakka::HasAccess()
 * @uses	Wakka::LoadPage()
 * @uses	Wakka::Href()
 * @uses	Wakka::Redirect()
 * @uses	Wakka::SavePage()
 *
 * @input	string	$to 	required: the page to be created
 *					must be a non-existing page and current user must have privs to create it
 *					default is source page name
 * @input	string	$note	optional: the note to be added to the page when created
 *					default is "Cloned from " followed by the name of the source page
 * @input	boolean	$editoption	optional: if true, the new page will be opened for edition on creation
					default is FALSE (to allow multiple cloning of the same source)
 * @input	boolean	$cloneaclsoption	optional: if true, ACLs are copied from the source page to the new page
 *					default is FALSE
 * @output	... tbd
 * @todo	use central regex library for valid pagenames #34
 * @todo	move main <div> to templating class
 * @todo	use input highlight to mark invalid values
 * @todo	add check for require_edit_note in the config file to enforce note
 * @todo	make note max. length configurable in a constant
 * @todo	standardize form layout (avoidnig table layout)
 */

/**#@+
 * Default value.
 */
if (!defined('VALID_PAGENAME_PATTERN')) define ('VALID_PAGENAME_PATTERN', '/^[A-Za-zÄÖÜßäöü]+[A-Za-z0-9ÄÖÜßäöü]*$/s'); #34
/**#@-*/

// initialization
$from = $this->tag;
$to = $this->tag;
$note = sprintf(CLONED_FROM, $from);
$editoption = '';
$cloneaclsoption = '';
$box = '<em>'.CLONE_VALID_TARGET.'</em>';

// 1. check source page existence
if (!$this->ExistsPage($from))
{
	// source page does not exist!
	$box = sprintf(WIKKA_ERROR_PAGE_NOT_EXIST, $from);
} else
{
	// 2. page exists - now check user's read-access to the source page
	if (!$this->HasAccess('read', $from))
	{
		// user can't read source page!
		$box = '<em class="error">'.WIKKA_ERROR_ACL_READ_SOURCE.'</em>';
	}
	else
	{
		// page exists and user has read-access to the source - proceed
		if (isset($_POST) && $_POST)
		{
			// get parameters
			$to = isset($_POST['to']) && $_POST['to'] ? $_POST['to'] : $to;
			$note = isset($_POST['note']) && $_POST['note'] ? $_POST['note'] : $note;
			$editoption = (isset($_POST['editoption'])) ? ' checked="checked"' : '';
			$cloneaclsoption = (isset($_POST['cloneaclsoption'])) ? ' checked="checked"' : '';

			// 3. check target pagename validity
			if (!preg_match(VALID_PAGENAME_PATTERN, $to))  //TODO use central regex library
			{
				// invalid pagename!
				$box = '<em class="error">'.sprintf(WIKKA_ERROR_INVALID_PAGENAME,$to).'</em>';
			}
			else
			{
				// 4. target page name is valid - now check user's write-access
				if (!$this->HasAccess('write', $to))
				{
					$box = '<em class="error">'.sprintf(ERROR_ACL_WRITE, $to).'</em>';
				}
				else
				{
					// 5. check target page existence
					if ($this->ExistsPage($to))
					{
						// page already exists!
						$box = '<em class="error">'.WIKKA_ERROR_PAGE_ALREADY_EXIST.'</em>';
					}
					else
					{
						// 6. Valid request - proceed to page cloning
						$thepage=$this->LoadPage($from);
						if ($thepage)
						{
							$pagecontent = $thepage['body'];
						}
						$this->SavePage($to, $pagecontent, $note);
						// Clone ACLs if requested
						if (!(false===strpos($cloneaclsoption, 'checked="checked"')))
						{
							$this->CloneACLs($from, $to);
						}
						// Open editor if requested
						print $editoption;
						if (!(false===strpos($editoption, 'checked="checked"')))
						{
							// quick edit
							$this->Redirect($this->Href('edit', $to));
						}
						else
						{
							// show confirmation message
							$box = '<em class="success">'.sprintf(SUCCESS_CLONE_CREATED, $to).'</em>';
						}
					}
				}
			}
		}
		// set up form variables
		$form_open  = $this->FormOpen('clone');
		$form_close = $this->FormClose();
		$form_legend = sprintf(CLONE_LEGEND, $this->Link($this->tag));
		$form_clone_to_label = CLONE_X_TO_LABEL;
		$form_edit_note_label = CLONE_EDIT_NOTE_LABEL;
		$form_edit_option_label = CLONE_EDIT_OPTION_LABEL;
		$form_acl_option_label = CLONE_ACL_OPTION_LABEL;
		$form_clone_button = CLONE_BUTTON;

		// build form
		$template = <<<TPLCLONEFORM
$form_open
<fieldset><legend>$form_legend</legend>
<table class="clone">
<tr><td colspan="2">$box</td></tr>
<tr>
	<td><label for="to">$form_clone_to_label</label></td>
	<td><input id="to" type="text" name="to" value="$to" size="37" maxlength="75" /></td>
</tr>
<tr>
	<td><label for="note">$form_edit_note_label</label></td>
	<td><input id="note" name="note" type="text" value="$note" size="37" maxlength="75" /></td>
</tr>
<tr>
	<td></td>
	<td>
		<input type="checkbox" name="editoption"$editoption id="editoption" /><label for="editoption">$form_edit_option_label</label>
		<input type="checkbox" name="cloneaclsoption"$cloneaclsoption id="cloneaclsoption" /><label for="cloneaclsoption">$form_acl_option_label</label>
	</td>
</tr>
<tr>
	<td></td>
	<td>
		<input type="submit" name="create" value="$form_clone_button" />
	</td>
</tr>
</table>
</fieldset>
$form_close
TPLCLONEFORM;
	}
}

echo '<div class="page">'."\n";
echo $template;
echo '</div>'."\n"
?>
