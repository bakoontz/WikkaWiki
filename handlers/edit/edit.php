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
 * @uses	Wakka::ExistsPage()
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
 * @uses	Wakka::StaticHref()
 *
 * @todo	optimization using history.back();
 * @todo	use central regex library for validation;
 * @todo	document edit_button_position
 * @todo	don't show cancel button if JavaScript is not available
 * @todo	replace $_REQUEST with either $_GET or $_POST (or both if really
 * 			necessary) - #312 => NOT CLEAR here what to do; see also #449
 */

/**#@+
 * Default value.
 */
if (!defined('VALID_PAGENAME_PATTERN')) define ('VALID_PAGENAME_PATTERN', '/^[A-Za-zÄÖÜßäöü]+[A-Za-z0-9ÄÖÜßäöü]*$/s'); //TODO not needed: use IsWikiName() to validate @@@ FIRST move to regex library #34
if (!defined('MAX_TAG_LENGTH')) define ('MAX_TAG_LENGTH', 75);
if (!defined('MAX_EDIT_NOTE_LENGTH')) define ('MAX_EDIT_NOTE_LENGTH', 50);
if (!defined('DEFAULT_BUTTONS_POSITION')) define('DEFAULT_BUTTONS_POSITION', 'bottom');
/**
 * Class attribute to enable styling as error.
 */
if (!defined('INPUT_ERROR_STYLE')) define('INPUT_ERROR_STYLE', 'class="highlight"');
/**#@-*/

//initialization
$error = '';
$highlight_note = '';
$edit_note_field = '';
$note = '';
$ondblclick = ''; //#123
if ($this->GetConfigValue('edit_buttons_position') == 'top' || $this->GetConfigValue('edit_buttons_position') == 'bottom')
{
	$buttons_position = $this->GetConfigValue('edit_buttons_position');
}
else
{
	$buttons_position = DEFAULT_BUTTONS_POSITION;
}

if (isset($_POST['submit']) && ($_POST['submit'] == EDIT_PREVIEW_BUTTON) && ($user = $this->GetUser()) && ($user['doubleclickedit'] != 'N'))
{
	$ondblclick = ' ondblclick=\'document.getElementById("reedit_id").click();\'';
	//history.back() not working on IE. (changes are lost)
	//however, history.back() works fine in FF, and this is the optimized choice
	//TODO Optimization: Look at $_SERVER['HTTP_USER_AGENT'] and use history.back() for good browsers like FF.
	// JW: this page may have a solution: http://forums.oracle.com/forums/thread.jspa?messageID=210396
}
?>
<div class="page"<?php echo $ondblclick;?>>
<?php
if (!(preg_match(VALID_PAGENAME_PATTERN, $this->tag))) { //TODO use central regex library or (better!) IsWikiName()
	echo '<em>'.sprintf(WIKKA_ERROR_INVALID_PAGENAME,$this->tag).'</em>';
}
elseif ($this->HasAccess("write") && $this->HasAccess("read"))
{
	$newtag = $output = '';
	if (isset($_POST['newtag'])) $newtag = $_POST['newtag'];
	if ($newtag !== '') $this->Redirect($this->Href('edit', $newtag));

	if ($_POST)
	{
		// strip CRLF line endings down to LF to achieve consistency ... plus it saves database space.
		// Note: these codes must remain enclosed in double-quotes to work!
		$body = str_replace("\r\n", "\n", $_POST['body']);

		$body = preg_replace("/\n[ ]{4}/", "\n\t", $body);	// FIXME: misses first line and multiple sets of four spaces - solution in formatter??

		// we don't need to escape here, we do that just before display (i.e., treat note just like body!)
		$note = trim($_POST['note']);

		// only if saving:
		if ($_POST['submit'] == EDIT_STORE_BUTTON)
		{
			// check for overwriting
			if ($this->page)
			{
				if ($this->page['id'] != $_POST['previous'])
				{
					$error = ERROR_OVERWRITE_ALERT1.'<br />'.ERROR_OVERWRITE_ALERT2;
				}
			}
			// check for edit note
			if (($this->GetConfigValue('require_edit_note') == 1) && $_POST['note'] == '')
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
					#$dummy = $this->Header();		// !!!
					$this->StartLinkTracking();
					$dummy .= $this->Format($body);
					$this->StopLinkTracking();
					#$dummy .= $this->Footer();		// !!!
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
		$edit_note_field = '<input id="note" size="'.MAX_EDIT_NOTE_LENGTH.'" type="text" name="note" value="'.$this->hsc_secure($note).'" '.$highlight_note.'/> <label for="note">'.EDIT_NOTE_LABEL.'</label><br />'."\n";	#427
	}

	// fetch fields
	$previous = $this->page['id'];
	if (isset($_POST['previous'])) $previous = $_POST['previous'];
	if (!isset($body)) $body = $this->page['body'];
	$body = preg_replace("/\n[ ]{4}/", "\n\t", $body);	// FIXME misses first line and multiple sets of four spaces - JW 2005-01-16


	$maxtaglen = MAX_TAG_LENGTH; #38 - #376
	if ( ($field = $this->LoadSingle("describe ".$this->GetConfigValue('table_prefix')."pages tag"))
	   && (preg_match("/varchar\((\d+)\)/", $field['Type'], $matches)) )
	{
		$maxtaglen = $matches[1];
	}

	// PREVIEW screen
	if (isset($_POST['submit']) && $_POST['submit'] == EDIT_PREVIEW_BUTTON)
	{
		$preview_buttons =	'<fieldset><legend>'.EDIT_STORE_PAGE_LEGEND.'</legend>'."\n".
							$edit_note_field.
							'<input name="submit" type="submit" value="'.EDIT_STORE_BUTTON.'" accesskey="'.ACCESSKEY_STORE.'" />'."\n".
							'<input name="submit" type="submit" value="'.EDIT_REEDIT_BUTTON.'" accesskey="'.ACCESSKEY_REEDIT.'" id="reedit_id" />'."\n".
							'<input type="button" value="'.EDIT_CANCEL_BUTTON.'" onclick="document.location=\''.$this->href('').'\';" />'."\n".
							'</fieldset>'."\n";

		$preview_form = $this->FormOpen('edit')."\n";
		$preview_form .= '<input type="hidden" name="previous" value="'.$previous.'" />'."\n".
			// We need to escape ALL entity refs before display so we display them _as_ entities instead of interpreting them
			// hence hsc_secure() instead of htmlspecialchars_ent() which UNescapes entities!
			// JW/2007-02-20: why is this? wouldn't it be  easier for the person editing to show actual characters instead of entities?
			'<input type="hidden" name="body" value="'.$this->hsc_secure($body).'" />'."\n";	# #427
		$preview_form .= $preview_buttons."\n";
		$preview_form .= $this->FormClose()."\n";

		//build page
		$output .= '<div class="previewhead">'.EDIT_PREVIEW_HEADER.'</div>'."\n";
		if ($buttons_position == 'top')
		{
			$output .= $preview_form;
		}
		$output .= $this->Format($body, 'wakka', 'page');
		if ($buttons_position == 'bottom')
		{
			$output .= $preview_form;
		}

	}
	// RENAME screen
	elseif (!$this->page && strlen($this->tag) > $maxtaglen)
	{
		$this->tag = substr($this->tag, 0, $maxtaglen); // truncate tag to feed a backlinks-handler with the correct value. may be omitted. it only works if the link to a backlinks-handler is built in the footer.
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
		if ($error)
		{
			$output .= '<em class="error">'.$error.'</em>'."\n";
		}

		// append a comment?
		// TODO not clear if this is/was intended as a URL parameter (GET), or a check box on the edito form (POST) ....
		// would be nice as a checkbox, provided it is acted upon only when user is actually submitting - NOT on preview or re-edit
		if (isset($_REQUEST['appendcomment'])) #312, #449
		{
			$body = trim($body)."\n\n----\n\n-- ".$this->GetUserName().' '.sprintf(EDIT_COMMENT_TIMESTAMP_CAPTION,strftime("%c")).')';
		}
		$edit_buttons = '<fieldset><legend>'.EDIT_STORE_PAGE_LEGEND.'</legend>'."\n".
						$edit_note_field.
						'<input name="submit" type="submit" value="'.EDIT_STORE_BUTTON.'" accesskey="'.ACCESSKEY_STORE.'" />'."\n".
						'<input name="submit" type="submit" value="'.EDIT_PREVIEW_BUTTON.'" accesskey="'.ACCESSKEY_PREVIEW.'" />'."\n".
						'<input type="button" value="'.EDIT_CANCEL_BUTTON.'" onclick="document.location=\''.$this->Href('').'\';" />'."\n".
						'</fieldset>'."\n";
		$output .= $this->FormOpen('edit');
		if ($buttons_position == 'top')
		{
			$output .= $edit_buttons;
		}
		$output .= '<input type="hidden" name="previous" value="'.$previous.'" />'."\n".
			// We need to escape ALL entity refs before display so we display them _as_ entities instead of interpreting them
			// hence hsc_secure() instead of htmlspecialchars_ent() which UNescapes entities!
			// JW/2007-02-20: why is this? wouldn't it be  easier for the person editing to show actual characters instead of entities?
			'<div id="textarea_container">'."\n".
			'<textarea id="body" name="body" rows="100" cols="20">'.$this->hsc_secure($body).'</textarea>'."\n".	# #427
			'</div>'."\n";
		if ($buttons_position == 'bottom')
		{
			$output .= $edit_buttons;
		}
		$output .=	$this->FormClose();

		if ($this->GetConfigValue('gui_editor') == 1)	// @@@ cast to boolean and compare to TRUE
		{
			$Wikkaedit_baseURL = preg_replace('/3rdparty$/', '', $this->StaticHref('3rdparty')); 
			$output .= '<script type="text/javascript">Wikkaedit_baseURL = "'.$Wikkaedit_baseURL.'";</script>'."\n";
			$output .= '<script type="text/javascript" src="'.$this->StaticHref('3rdparty/plugins/wikkaedit/wikkaedit_data.js').'"></script>'."\n";
			$output .= '<script type="text/javascript" src="'.$this->StaticHref('3rdparty/plugins/wikkaedit/wikkaedit_search.js').'"></script>'."\n";
			$output .= '<script type="text/javascript" src="'.$this->StaticHref('3rdparty/plugins/wikkaedit/wikkaedit_actions.js').'"></script>'."\n";
			$output .= '<script type="text/javascript" src="'.$this->StaticHref('3rdparty/plugins/wikkaedit/wikkaedit.js').'"></script>'."\n";			
		}
	}

	echo $output;
}
else
{
	$message = '<em>'.$this->Format(ERROR_NO_WRITE_ACCESS).'</em><br />'."\n<br />\n";
	if ($this->ExistsPage($this->tag)) $message .= '<a href="'.$this->Href('showcode').'" title="'.SHOWCODE_LINK_TITLE.'">'.SHOWCODE_LINK.'</a>'."<br />\n";
	echo $message;
}
echo '</div>'."\n"
?>
