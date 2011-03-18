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
 * If the "Clone ACL" option is selected, ACL settings are copied to the target page,  
 * otherwise default ACL are applied to the new page.
 *
 * @package		Handlers
 * @name		clone
 *
 * @author		{@link http://wikkawiki.org/ChristianBarthelemy Christian Barthelemy} - original idea and code.
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli} - bugs fixed, code improved, removed popup alerts.
 * @author		{@link http://wikkawiki.org/BrianKoontz Brian Koontz} - clone ACL option
 * @since		Wikka 1.1.6.0
 *
 * @uses	Wakka::HasAccess()
 * @uses	Wakka::IsWikiName()
 * @uses	Wakka::existsPage()
 * @uses	Wakka::LoadPage()
 * @uses	Wakka::SavePage()
 * @uses	Wakka::Redirect()
 * @uses	Wakka::Link()
 * @uses	Wakka::GetPageTag()
 * @uses	Wakka;:FormOpen()
 * @uses	Wakka::FormClose()
 *
 * @input	string	$to	required: the page to be created
 *					must be a non existing page and current user must have privs 
 *					to create it
 *					default is source page name
 * @input	string	$note	optional: the note to be added to the page when created
 *					default is "Cloned from " followed by the name of the source page
 * @input	boolean	$editoption	optional: if true, the new page will be opened for
 *					editing on creation
 *					default is FALSE (to allow multiple cloning of the same source)
 * @input   boolean $cloneaclsoption optional: if true, ACLs are copied from the source page to the new page; default is false 
 *
 * @todo	Use central regex library for valid pagenames.
 * @todo	Decide whether it's allowed to "re-create" a page of which only non-active versions exist
 *
 */
// initialization
$from = $this->tag;
$to = $this->tag;
$note = sprintf(CLONED_FROM, $from);
$editoption = '';
$cloneaclsoption = '';
$box = PLEASE_FILL_VALID_TARGET;

echo '<div id="content">'."\n";

// print header
#echo $this->Format(CLONE_HEADER);
echo '<h3>'.CLONE_HEADER.'</h3>'."\n";

// 1. check source page existence
if (!$this->existsPage($from))		// name change, interface change (allows only active page (if not, LoadPage() will fail!))
{
	// source page does not exist!
	$box = '<em class="error">'.sprintf(ERROR_PAGE_NOT_EXIST, $from).'</em>';
} else
{
	// 2. page exists - now check user's read-access to the source page
	if (!$this->HasAccess('read', $from))
	{
		// user can't read source page!
		$box = '<em class="error">'.ERROR_ACL_READ.'</em>';
	} else
	{
		// page exists and user has read-access to the source - proceed
		if (isset($_POST) && $_POST)	// @@@ ??? what are we testing here?
		{
			// get parameters
			$to = isset($_POST['to']) && $_POST['to'] ?  $this->GetSafeVar('to', 'post') : $to;
			$note = isset($_POST['note']) && $_POST['note'] ?  $this->GetSafeVar('note', 'post') : $note;
			$editoption = (isset($_POST['editoption'])) ? ' checked="checked"' : '';
			$cloneaclsoption = (isset($_POST['cloneaclsoption']))? 'checked="checked"' : ''; 

			// 3. Check user's write access
			if (!$this->HasAccess('write', $to))
			{
				$box = '<em class="error">'.sprintf(ERROR_ACL_WRITE, $to).'</em>';
			} 
			// 3a. Check target pagename validity
			else if(!$this->IsWikiName($to))
			{
				$box = '<em class="error">'.ERROR_INVALID_PAGENAME.'</em>';
			}
			else
			{
				// 5. check target page existence
				if ($this->existsPage($to, NULL, NULL, FALSE))	// name change, interface change (checks for non-active page, too) @@@
				{
					// page already exists!
					$box = '<em class="error">'.ERROR_PAGE_ALREADY_EXIST.'</em>';
				} else
				{
					// 6. Valid request - proceed to page cloning
					$thepage=$this->LoadPage($from); 
					if ($thepage) $pagecontent = $thepage['body']; 
					$this->SavePage($to, $pagecontent, $note); 
					// Clone ACLs if requested 
					if ($cloneaclsoption == 'checked="checked"') 
					{ 
						echo $from;
						$acl = $this->LoadAllACLs($from, 0); 
						var_dump($acl);
						$this->SaveACL($to, 'read', $this->TrimACLs($acl['read_acl'])); 
						$this->SaveACL($to, 'write', $this->TrimACLs($acl['write_acl'])); 
						$this->SaveACL($to, 'comment_read', $this->TrimACLs($acl['comment_read_acl'])); 
						$this->SaveACL($to, 'comment_post', $this->TrimACLs($acl['comment_post_acl'])); 
					} 

					// Open editor if requested 
					if ($editoption == ' checked="checked"')
					{
						// quick edit
						$this->Redirect($this->href('edit', $to));
					} else
					{
						//remove target page from cache
						unset($this->pageCache[$to]);
						// show confirmation message
						$box = '<em class="success">'.sprintf(CLONE_SUCCESSFUL, $this->Link($to)).'</em>';
					}
				}
			}
		}
		// build form
		$form = $this->FormOpen('clone');
		$form .= '<table class="clone">'."\n".
			'<tr>'."\n".
			'<td>'.sprintf(CLONE_X_TO, $this->Link($this->GetPageTag())).'</td>'."\n".
			'<td><input type="text" name="to" value="'.$to.'" size="37" maxlength="75" /></td>'."\n".
			'</tr>'."\n".
			'<tr>'."\n".
			'<td>'.EDIT_NOTE.'</td>'.
			'<td><input type="text" name="note" value="'.$note.'" size="37" maxlength="75" /></td>'."\n".
			'</tr>'."\n".
			'<tr>'."\n".
			'<td></td>'."\n".
			'<td>'."\n".
			'<input type="checkbox" name="editoption"'.$editoption.' id="editoption" /><label for="editoption">'.LABEL_EDIT_OPTION.'</label>'."\n".
            '<input type="checkbox" name="cloneaclsoption" '.$cloneaclsoption.' id="cloneaclsoption" /><label for="cloneaclsoption">'.LABEL_ACL_OPTION.'</label>'."\n". 
			'</tr>'."\n". 
			'<tr>'."\n". 
			'<td></td>'."\n". 
			'<td>'."\n". 
			'<input type="submit" name="create" value="'.LABEL_CLONE.'" />'."\n".
			'</td>'."\n".
			'</tr>'."\n".
			'</table>'."\n";
		$form .= $this->FormClose();
	}
}

// display messages
#if (isset($box)) echo $this->Format(' --- '.$box.' --- --- ');
if (isset($box)) echo '<p>'.$box.'</p><br />'."\n";
// print form
if (isset($form)) print $form;
echo '</div>'."\n";
?>
