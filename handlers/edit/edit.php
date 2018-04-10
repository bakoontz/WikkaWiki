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
 * @uses	Wakka::IsWikiName()
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

if(!defined('MAX_EDIT_NOTE_LENGTH')) define('MAX_EDIT_NOTE_LENGTH', 50);
if (!defined('EDIT_INVALID_CHARS')) define('EDIT_INVALID_CHARS', '| ? = &lt; &gt; / \ " % &amp;');

//initialization
$error = '';
$highlight_note = '';
$edit_note_field = '';
$note = '';
$ondblclick = ''; //#123
$body = '';

// cancel operation and return to page
if($this->GetSafeVar('cancel', 'post') == T_("Cancel"))
{
	$this->Redirect($this->Href());
}

if ($this->GetSafeVar('submit', 'post') == T_("Preview") &&
	($user = $this->GetUser()) &&
	($user['doubleclickedit'] != 'N'))
{
	$ondblclick = ' ondblclick=\'document.getElementById("reedit_id").click();\'';
}
?>
<div id="content"<?php echo $ondblclick;?>>
<?php
if(!$this->IsWikiName($this->GetPageTag()))
{
	echo '<em class="error">'.sprintf(T_("This page name is invalid.  Valid page names must not contain the characters %s."), EDIT_INVALID_CHARS).'<br/></em>';
}
else if ($this->HasAccess("write") && $this->HasAccess("read"))
{
	$newtag = $output = '';
	// rename action
	$newtag = $this->GetSafeVar('newtag', 'post');
	if ($newtag !== '') $this->Redirect($this->Href('edit', $newtag));

	// Process id GET param if present
	$id = $this->page['id'];
	if(isset($_GET['id']))
	{
		$page = $this->LoadPageById($this->GetSafeVar('id', 'get'));
		if($page['tag'] != $this->page['tag'])
		{
			$this->Redirect($this->Href(), T_("The revision id does not exist for the requested page"));
		}
		else
		{
			$body = $page['body'];
			$id = $page['id'];
		}
	}

	if(NULL != $_POST)
	{
		// strip CRLF line endings down to LF to achieve consistency ... plus it saves database space.
		// Note: these codes must remain enclosed in double-quotes to work! -- JsnX
		// Note 2: Don't replace this with GetSafeVar()!  The
		// formatter will take care of sanitizing the output.
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
			$note = substr(trim($this->GetSafeVar('note','post')), 0, MAX_EDIT_NOTE_LENGTH);
		}

		// only if saving:
		if ($this->GetSafeVar('submit', 'post') == T_("Store"))
		{
			// check for overwriting
			if ($this->page)
			{
				if ($this->page['id'] != $_POST['previous'])
				{
					$error = T_("OVERWRITE ALERT: This page was modified by someone else while you were editing it.").'<br />'.T_("Please copy your changes and re-edit this page.");
				}
			}
			// check for edit note if required
			if (($this->GetConfigValue('require_edit_note') == 1) && $this->GetSafeVar('note', 'post') == '')
			{
				$error .= T_("MISSING EDIT NOTE: Please fill in an edit note!");
				$highlight_note = 'class="highlight"';
			}
			// store
			if (!$error)
			{
				// only save if new body differs from old body
				if ($body != $this->page['body']) {

					// add page (revisions)
					$this->SavePage($this->GetPageTag(), $body, $note);

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
		$edit_note_field = '<input id="note"
		size="'.MAX_EDIT_NOTE_LENGTH.'"
		maxlength="'.MAX_EDIT_NOTE_LENGTH.'" type="text" name="note" value="'.Wakka::hsc_secure($note).'" '.$highlight_note.'/> <label for="note">'.T_("Please add a note on your edit").'</label><br />'."\n";	#427
	}

	// fetch fields
	$previous = $this->page['id'];
	if (isset($_POST['previous'])) $previous = $this->GetSafeVar('previous', 'post');
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
	// MySQL specific!
	$maxtaglen = db_getMaxTagLen($this);

	// PREVIEW screen
	if ($this->GetSafeVar('submit', 'post') == T_("Preview"))
	{
		$preview_buttons =	'<fieldset><legend>'.T_("Store page").'</legend>'."\n".
							$edit_note_field.
							'<input name="submit" type="submit" value="'.T_("Store").'" accesskey="'."s".'" />'."\n".
							'<input name="submit" type="submit" value="'.T_("Re-edit").'" accesskey="'."r".'" id="reedit_id" />'."\n".
							'<input type="submit" value="'.T_("Cancel").'" name="cancel" />'."\n".
							'</fieldset>'."\n";

		$output .= '<div class="previewhead">'.T_("Preview").'</div>'."\n";

		$output .= $this->Format($body);

		$output .=
			'<div class="clear">'."\n".	#683
			$this->FormOpen('edit')."\n".
			'<input type="hidden" name="previous" value="'.$previous.'" />'."\n".
			// We need to escape ALL entity refs before display so we display them _as_ entities instead of interpreting them
			// hence hsc_secure() instead of htmlspecialchars_ent() which UNescapes entities!
			// JW/2007-02-20: why is this? wouldn't it be  easier for the person editing to show actual characters instead of entities?
			'<input type="hidden" name="body" value="'.Wakka::hsc_secure($body).'" />'."\n";	# #427
		$output .= '</div>'."\n";	#683

		$output .= "<br />\n".$preview_buttons.$this->FormClose()."\n";
	}
	// RENAME screen
	elseif (!$this->page && strlen($this->GetPageTag()) > $maxtaglen)
	{
		// truncate tag to feed a backlinks-handler with the correct value. may be omited. it only works if the link to a backlinks-handler is built in the footer.
		$this->tag = substr($this->GetPageTag(), 0, $maxtaglen);

		$output  = '<em class="error">'.sprintf(T_("Page name too long! %d characters max."), $maxtaglen).'</em><br />'."\n";
		$output .= sprintf(T_("Clicking on %s will automatically truncate the page name to the correct size"), T_("Rename")).'<br /><br />'."\n";
		$output .= $this->FormOpen('edit');
		$output .= '<input name="newtag" size="'.MAX_TAG_LENGTH.'" value="'.$this->htmlspecialchars_ent($this->GetPageTag()).'" />';
		$output .= '<input name="submit" type="submit" value="'.T_("Rename").'" />'."\n";
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
			$body = trim($body)."\n\n----\n\n-- ".$this->GetUserName().' '.sprintf("(%s)",strftime("%c")).')';
		}
		$edit_buttons = '<fieldset><legend>'.T_("Store page").'</legend>'."\n".
						$edit_note_field.
						'<input name="submit" type="submit" value="'.T_("Store").'" accesskey="'."s".'" />'."\n".
						'<input name="submit" type="submit" value="'.T_("Preview").'" accesskey="'."p".'" />'."\n".
						'<input type="submit" value="'.T_("Cancel").'" name="cancel" />'."\n".
						'</fieldset>'."\n";
		$output .= $this->FormOpen('edit');
		$output .= '<input type="hidden" name="previous" value="'.$previous.'" />'."\n".
			// We need to escape ALL entity refs before display so we display them _as_ entities instead of interpreting them
			// hence hsc_secure() instead of htmlspecialchars_ent() which UNescapes entities!
			// JW/2007-02-20: why is this? wouldn't it be  easier for the person editing to show actual characters instead of entities?
			'<div id="textarea_container">'."\n".
			'<textarea id="body" name="body" rows="20" cols="100">'.Wakka::hsc_secure($body).'</textarea>'."\n".	# #427
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
	$message = '<em class="error">'.
		sprintf(T_( "You don't have write access to this page. You might need to <a href=\"%s\">login</a> or <a href=\"%s\">register an account</a> to be able to edit this page."), $this->Href('', 'UserSettings'), $this->Href('', 'UserSettings')).
			'</em><br />'."\n".
			"<br />\n".
			'<a href="'.$this->Href('showcode').'" title="'.T_("Click to view page formatting code").'">'.T_("View formatting code for this page").'</a>'.
			"<br />\n";
	echo $message;
}
?>
</div>
