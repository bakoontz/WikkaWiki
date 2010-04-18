<?php
/**
 * Display a form to edit the current page.
 *
 * @package		Handlers
 * @subpackage	Page
 * @version		$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author	{@link http://wikkawiki.org/JsnX Jason Tourtelotte} (original code)
 * @author	{@link http://wikkawiki.org/Dartar Dario Taraborelli} (preliminary code cleanup, i18n)
 * @author	{@link http://wikkawiki.org/DotMG Mahefa Randimbisoa} (bugfixes)
 *
 * @uses	Config::$edit_buttons_position
 * @uses	Config::$require_edit_note
 * @uses	Config::$gui_editor
 * @uses	Wakka::ClearLinkTable()
 * @uses	Wakka::Footer()
 * @uses	Wakka::Format()
 * @uses	Wakka::FormClose()
 * @uses	Wakka::FormOpen()
 * @uses	Wakka::GetUser()
 * @uses	Wakka::GetUserName()
 * @uses	Wakka::HasAccess()
 * @uses	Wakka::Header()
 * @uses	Wakka::Href()
 * @uses	Wakka::htmlspecialchars_ent()
 * @uses	Wakka::hsc_secure()
 * @uses	Wakka::LoadSingle()
 * @uses	Wakka::Redirect()
 * @uses	Wakka::SavePage()
 * @uses	Wakka::StartLinkTracking()
 * @uses	Wakka::StopLinkTracking()
 * @uses	Wakka::WriteLinkTable()
 *
 * @todo	use central regex library for validation;
 * @todo	replace $_REQUEST with either $_GET or $_POST (or both if really
 * 			necessary) - #312 => NOT CLEAR here what to do; see also #449
 */

//initialization
$error = '';
$highlight_note = '';
$edit_note_field = '';
$note = '';
$ondblclick = ''; //#123
$body = '';

// cancel operation and return to page
if(isset($_POST['cancel']) && ($this->GetSafeVar('cancel', 'post') == EDIT_CANCEL_BUTTON))
{
	$this->Redirect($this->Href());
}

if (isset($_POST['submit']) && ($this->GetSafeVar('submit', 'post') == EDIT_PREVIEW_BUTTON) && ($user = $this->GetUser()) && ($user['doubleclickedit'] != 'N'))
{
	$ondblclick = ' ondblclick=\'document.getElementById("reedit_id").click();\'';
}
?>
<div id="content"<?php echo $ondblclick;?>>
<?php
if ($this->HasAccess("write") && $this->HasAccess("read"))
{
	$newtag = $output = '';
	// rename action
	if (isset($_POST['newtag']))
	{
		$newtag = $_POST['newtag'];
		if ($newtag !== '') $this->Redirect($this->Href('edit', $newtag));
	}

	// Process id GET param if present
	$id = $this->page['id'];
	if(isset($_GET['id']))
	{
		$page = $this->LoadPageById(mysql_real_escape_string($_GET['id']));
		if($page['tag'] != $this->page['tag'])
		{
			$this->Redirect($this->Href(), ERROR_INVALID_PAGEID);
		}
		else
		{
			$body = $page['body'];
			$id = $page['id'];
		}
	}

	if (isset($_POST['form_id']))
	{
		// strip CRLF line endings down to LF to achieve consistency ... plus it saves database space.
		// Note: these codes must remain enclosed in double-quotes to work! -- JsnX
		$body = str_replace("\r\n", "\n", $_POST['body']);
		// replace each 4 consecutive spaces at the start of a line with a tab
		#$body = preg_replace("/\n[ ]{4}/", "\n\t", $body);						# @@@ FIXME: misses first line and multiple sets of four spaces - JW 2005-01-16
		# JW FIXED 2005-07-12
		$pattern = '/^(\t*) {4}/m';					# m modifier: match ^ at start of line *and* at start of string;
		$replace = "$1\t";
		while (preg_match($pattern,$body))
		{
			$body = preg_replace($pattern,$replace,$body);
		}
		// we don't need to escape here, we do that just before display (i.e., treat note just like body!)
		if (isset($_POST['note']))
		{
			$note = trim($this->GetSafeVar('note','post'));
		}

		// only if saving:
		if (isset($_POST['submit']) && $this->GetSafeVar('submit', 'post') == EDIT_STORE_BUTTON)
		{
			if (FALSE != ($aKey = $this->getSessionKey($_POST['form_id'])))	# check if form key was stored in session
			{
				if (TRUE != ($rc = $this->hasValidSessionKey($aKey)))	# check if correct name,key pair was passed
				{
					$error = 'Something went wrong with your credentials. Page was not saved';
				}
			}
			
			// check for overwriting
			if ($this->page)
			{
				if ($this->page['id'] != $_POST['previous'])
				{
					$error = ERROR_OVERWRITE_ALERT1.'<br />'.ERROR_OVERWRITE_ALERT2;
				}
			}
			// check for edit note if required
			if (($this->GetConfigValue('require_edit_note') == 1) && $this->GetSafeVar('note', 'post') == '')
			{
				$error .= ERROR_MISSING_EDIT_NOTE;
				$highlight_note = INPUT_ERROR_STYLE;
			}
			// store
			if (!$error)
			{
				// only save if new body differs from old body
				if ($body != $this->page['body']) {

					// add page (revisions)
					$this->SavePage($this->tag, $body, $note);

					// now we render it internally so we can write the updated link table.
					// if we no longer do link tracking for header and footer why are we creating dummy output?
					$this->ClearLinkTable();
					$dummy = $this->Header();		// @@@
					$this->StartLinkTracking();
					$dummy .= $this->Format($body);
					$this->StopLinkTracking();
					$dummy .= $this->Footer();		// @@@
					$this->WriteLinkTable();
					$this->ClearLinkTable();
				}

				// forward
				$this->Redirect($this->Href());
			}
		}
	}

	// create edit note field if edit_notes are enabled
	if ($this->GetConfigValue('require_edit_note') != 2)
	{
		// We need to escape ALL entity refs before display so we display them _as_ entities instead of interpreting them
		// so we use hsc_secure() on the edit note (as on the body)
		// JW/2007-02-20: why is this? wouldn't it be  easier for the person editing to show actual characters instead of entities?
		$edit_note_field = '<input id="note" size="'.MAX_EDIT_NOTE_LENGTH.'" maxlength="'.MAX_EDIT_NOTE_LENGTH.'" type="text" name="note" value="'.$this->hsc_secure($note).'" '.$highlight_note.'/> <label for="note">'.EDIT_NOTE_LABEL.'</label><br />'."\n";	#427
	}

	// fetch fields
	$previous = $this->page['id'];
	if (isset($_POST['previous'])) $previous = $_POST['previous'];
	if (empty($body)) $body = $this->page['body'];
	// replace each 4 consecutive spaces at the start of a line with a tab
	#$body = preg_replace("/\n[ ]{4}/", "\n\t", $body);						# @@@ FIXME: misses first line and multiple sets of four spaces - JW 2005-01-16
	# JW FIXED 2005-07-12
	$pattern = '/^(\t*) {4}/m';					# m modifier: match ^ at start of line *and* at start of string;
	$replace = "$1\t";
	while (preg_match($pattern,$body))
	{
		$body = preg_replace($pattern,$replace,$body);
	}

	// derive maximum length for a page name from the table structure if possible
	if ($result = mysql_query("describe ".$this->GetConfigValue('table_prefix')."pages tag")) {
		$field = mysql_fetch_assoc($result);
		if (preg_match("/varchar\((\d+)\)/", $field['Type'], $matches)) $maxtaglen = $matches[1];
	}
	else
	{
		$maxtaglen = MAX_TAG_LENGTH;
	}

	// PREVIEW screen
	if (isset($_POST['submit']) && $this->GetSafeVar('submit', 'post') == EDIT_PREVIEW_BUTTON)
	{
		$preview_buttons =	'<fieldset><legend>'.EDIT_STORE_PAGE_LEGEND.'</legend>'."\n".
							$edit_note_field.
							'<input name="submit" type="submit" value="'.EDIT_STORE_BUTTON.'" accesskey="'.ACCESSKEY_STORE.'" />'."\n".
							'<input name="submit" type="submit" value="'.EDIT_REEDIT_BUTTON.'" accesskey="'.ACCESSKEY_REEDIT.'" id="reedit_id" />'."\n".
							'<input type="submit" value="'.EDIT_CANCEL_BUTTON.'" name="cancel" />'."\n".
							'</fieldset>'."\n";

		$output .= '<div class="previewhead">'.EDIT_PREVIEW_HEADER.'</div>'."\n";

		$output .= $this->Format($body);

		$output .=
			'<div class="clear">'."\n".	#683
			$this->FormOpen('edit')."\n".
			'<input type="hidden" name="previous" value="'.$previous.'" />'."\n".
			// We need to escape ALL entity refs before display so we display them _as_ entities instead of interpreting them
			// hence hsc_secure() instead of htmlspecialchars_ent() which UNescapes entities!
			// JW/2007-02-20: why is this? wouldn't it be  easier for the person editing to show actual characters instead of entities?
			'<input type="hidden" name="body" value="'.$this->hsc_secure($body).'" />'."\n";	# #427
		$output .= '</div>'."\n";	#683

		$output .= "<br />\n".$preview_buttons.$this->FormClose()."\n";
	}
	// RENAME screen
	elseif (!$this->page && strlen($this->tag) > $maxtaglen)
	{
		// truncate tag to feed a backlinks-handler with the correct value. may be omited. it only works if the link to a backlinks-handler is built in the footer.
		$this->tag = substr($this->tag, 0, $maxtaglen);

		$output  = '<em class="error">'.sprintf(ERROR_TAG_TOO_LONG, $maxtaglen).'</em><br />'."\n";
		$output .= sprintf(MESSAGE_AUTO_RESIZE, INPUT_SUBMIT_RENAME).'<br /><br />'."\n";
		$output .= $this->FormOpen('edit');
		$output .= '<input name="newtag" size="'.MAX_TAG_LENGTH.'" value="'.$this->htmlspecialchars_ent($this->tag).'" />';
		$output .= '<input name="submit" type="submit" value="'.EDIT_RENAME_BUTTON.'" />'."\n";
		$output .= $this->FormClose();
	}
	// EDIT Screen
	else
	{
		// display form
		if (!empty($error))
		{
			$output .= '<em class="error">'.$error.'</em>'."\n";
		}

		// append a comment?
		// TODO not clear if this is/was intended as a URL parameter (GET), or a check box on the edito form (POST) ....
		// would be nice as a checkbox, provided it is acted upon only when user is actually submitting - NOT on preview or re-edit
		if (isset($_POST['appendcomment'])) #312, #449
		{
			$body = trim($body)."\n\n----\n\n-- ".$this->GetUserName().' '.sprintf(EDIT_COMMENT_TIMESTAMP_CAPTION,strftime("%c")).')';
		}
		$edit_buttons = '<fieldset><legend>'.EDIT_STORE_PAGE_LEGEND.'</legend>'."\n".
						$edit_note_field.
						'<input name="submit" type="submit" value="'.EDIT_STORE_BUTTON.'" accesskey="'.ACCESSKEY_STORE.'" />'."\n".
						'<input name="submit" type="submit" value="'.EDIT_PREVIEW_BUTTON.'" accesskey="'.ACCESSKEY_PREVIEW.'" />'."\n".
						'<input type="submit" value="'.EDIT_CANCEL_BUTTON.'" name="cancel" />'."\n".
						'</fieldset>'."\n";
		$output .= $this->FormOpen('edit');
		$output .= '<input type="hidden" name="previous" value="'.$previous.'" />'."\n".
			// We need to escape ALL entity refs before display so we display them _as_ entities instead of interpreting them
			// hence hsc_secure() instead of htmlspecialchars_ent() which UNescapes entities!
			// JW/2007-02-20: why is this? wouldn't it be  easier for the person editing to show actual characters instead of entities?
			'<div id="textarea_container">'."\n".
			'<textarea id="body" name="body" rows="100" cols="20">'.$this->hsc_secure($body).'</textarea>'."\n".	# #427
			'</div>'."\n";
		$output .= $edit_buttons;
		$output .= $this->FormClose();

		if ($this->GetConfigValue('gui_editor') == 1)	// @@@ cast to boolean and compare to TRUE
		{
			$output .= '<script type="text/javascript" src="3rdparty/plugins/wikkaedit/wikkaedit_data.js"></script>'."\n";
			$output .= '<script type="text/javascript" src="3rdparty/plugins/wikkaedit/wikkaedit_search.js"></script>'."\n";
			$output .= '<script type="text/javascript" src="3rdparty/plugins/wikkaedit/wikkaedit.js"></script>'."\n";
		}
	}

	echo $output;
}
else
{
	$message = '<em class="error">'.$this->Format(ERROR_NO_WRITE_ACCESS).'</em><br />'."\n".
			"<br />\n".
			'<a href="'.$this->Href('showcode').'" title="'.SHOWCODE_LINK_TITLE.'">'.SHOWCODE_LINK.'</a>'.
			"<br />\n";
	echo $message;
}
?>
</div>
