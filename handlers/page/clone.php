<div class="page">
<?php
/**
 * Clone the current page and save a copy of it as a new page.
 *
 * Usage: append /clone to the URL of the page you want to clone
 * 
 * This handler checks the existence of the source page, the validity of the 
 * name of the target page to be created, the user's read-access to the source 
 * page and write-access to the target page.
 * If the edit option is selected, the user is redirected to the target page for
 * edition immediately after its creation.
 *
 * @package         Handlers
 * @subpackage        
 * @name              clone
 *
 * @author            {@link http://wikkawiki.org/ChristianBarthelemy Christian Barthelemy} - original idea and code.
 * @author            {@link http://wikkawiki.org/DarTar Dario Taraborelli} - bugs fixed, code improved, removed popup alerts.  
 * @version           0.4
 * @since             Wikka 1.1.6.0
 *                      
 * @input             string  $to  required: the page to be created
 *                            must be a non existing page and current user must be authorized to create it
 *                            default is source page name 				
 * 
 * @input             string  $note  optional: the note to be added to the page when created
 *                            default is "Cloned from " followed by the name of the source page
 * 
 * @input             boolean $editoption optional: if true, the new page will be opened for edition on creation
 *                            default is false (to allow multiple cloning of the same source)
 *
 * @todo              Use central library for valid pagenames.
 *        
 */
// defaults
if(!defined('VALID_PAGENAME_PATTERN')) define ('VALID_PAGENAME_PATTERN', '/^[A-Za-zÄÖÜßäöü]+[A-Za-z0-9ÄÖÜßäöü]*$/s');

// i18n
define('CLONE_HEADER', '==== Clone current page ====');
define('CLONE_SUCCESSFUL', '%s was succesfully created!');
define('CLONE_X_TO', 'Clone %s to:');
define('CLONED_FROM', 'Cloned from %s');
define('EDIT_NOTE', 'Edit note:');
define('ERROR_ACL_READ', 'You are not allowed to read the source of this page.');
define('ERROR_ACL_WRITE', 'Sorry! You don\'t have write-access to %s');
define('ERROR_INVALID_PAGENAME', 'This page name is invalid. Valid page names must start with a letter and contain only letters and numbers.');
define('ERROR_PAGE_ALREADY_EXIST', 'Sorry, the destination page already exists');
define('ERROR_PAGE_NOT_EXIST', ' Sorry, page %s does not exist.');
define('LABEL_CLONE', 'Clone');
define('LABEL_EDIT_OPTION', ' Edit after creation ');
define('PLEASE_FILL_VALID_TARGET', 'Please fill in a valid target ""PageName"" and an (optional) edit note.');

// initialization
$from = $this->tag;
$to = $this->tag;
$note = sprintf(CLONED_FROM, $from);
$editoption = ''; 
$box = PLEASE_FILL_VALID_TARGET;

// print header
echo $this->Format(CLONE_HEADER);

// 1. check source page existence
if (!$this->ExistsPage($from))
{
	// source page does not exist!
	$box = sprintf(ERROR_PAGE_NOT_EXIST, $from);
} else 
{
	// 2. page exists - now check user's read-access to the source page
	if (!$this->HasAccess('read', $from))
	{
		// user can't read source page!
		$box = ERROR_ACL_READ;
	} else
	{
		// page exists and user has read-access to the source - proceed
		if (isset($_POST) && $_POST)
		{
			// get parameters
			$to = isset($_POST['to']) && $_POST['to'] ? $_POST['to'] : $to;
			$note = isset($_POST['note']) && $_POST['note'] ? $_POST['note'] : $note;
			$editoption = (isset($_POST['editoption']))? 'checked="checked"' : '';
		
			// 3. check target pagename validity
			if (!preg_match(VALID_PAGENAME_PATTERN, $to))  //TODO use central regex library
			{
				// invalid pagename!
				$box = '""<em class="error">'.ERROR_INVALID_PAGENAME.'</em>""';
			} else
			{
				// 4. target page name is valid - now check user's write-access
				if (!$this->HasAccess('write', $to))  
				{
					$box = '""<em class="error">'.sprintf(ERROR_ACL_WRITE, $to).'</em>""';
				} else
				{
					// 5. check target page existence
					if ($this->ExistsPage($to))
					{ 
						// page already exists!
						$box = '""<em class="error">'.ERROR_PAGE_ALREADY_EXIST.'</em>""';
					} else
					{
						// 6. Valid request - proceed to page cloning
						$thepage=$this->LoadPage($from); # load the source page
						if ($thepage) $pagecontent = $thepage['body']; # get its content
						$this->SavePage($to, $pagecontent, $note); #create target page
						if ($editoption == 'checked="checked"')
						{
							// quick edit
							$this->Redirect($this->href('edit', $to));
						} else
						{
							// show confirmation message
							$box = '""<em class="success">'.sprintf(CLONE_SUCCESSFUL, $to).'</em>""';
						}
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
			'<td>'.EDIT_NOTE.'</strong></td>'.
			'<td><input type="text" name="note" value="'.$note.'" size="37" maxlength="75" /></td>'."\n".
			'</tr>'."\n".
			'<tr>'."\n".
			'<td></td>'."\n".
			'<td>'."\n".
			'<input type="checkbox" name="editoption" '.$editoption.' id="editoption" /><label for="editoption">'.LABEL_EDIT_OPTION.'</label>'."\n".
			'<input type="submit" name="create" value="'.LABEL_CLONE.'" />'."\n".
			'</td>'."\n".
			'</tr>'."\n".
			'</table>'."\n";
		$form .= $this->FormClose();
	}
}

// display messages
if (isset($box)) echo $this->Format(' --- '.$box.' --- --- ');
// print form
if (isset($form)) print $form;
?>
</div>
