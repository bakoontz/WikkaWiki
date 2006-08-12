<?php
/**
 * Display a form to edit the current page.
 *
 * @package		Handlers
 * @subpackage	Page
 * @version		$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author		{@link http://wikkawiki.org/JsnX Jason Tourtelotte} (original code)
 * @author		{@link http://wikkawiki.org/Dartar Dario Taraborelli} (preliminary code cleanup, i18n)
 * @author		{@link http://wikkawiki.org/DotMG Mahefa Randimbisoa} (bugfixes)
 *
 * @todo		move main <div> to templating class;
 * @todo		optimization using history.back();
 * @todo		use central regex library for validation;
 */
/**
 * Defaults
 */
if(!defined('VALID_PAGENAME_PATTERN')) define ('VALID_PAGENAME_PATTERN', '/^[A-Za-zÄÖÜßäöü]+[A-Za-z0-9ÄÖÜßäöü]*$/s');
if(!defined('MAX_TAG_LENGTH')) define ('MAX_TAG_LENGTH', 75);
if(!defined('MAX_EDIT_NOTE_LENGTH')) define ('MAX_EDIT_NOTE_LENGTH', 50);
/**
 * i18n
 */
if(!defined('PREVIEW_HEADER')) define('PREVIEW_HEADER', 'Preview');
if(!defined('LABEL_EDIT_NOTE')) define('LABEL_EDIT_NOTE', 'Please add a note on your edit');
if (!defined('INPUT_ERROR_STYLE')) define('INPUT_ERROR_STYLE', 'class="highlight"');
if(!defined('ERROR_INVALID_PAGENAME')) define('ERROR_INVALID_PAGENAME', 'This page name is invalid. Valid page names must start with a letter and contain only letters and numbers.');
if(!defined('ERROR_OVERWRITE_ALERT')) define('ERROR_OVERWRITE_ALERT', 'OVERWRITE ALERT: This page was modified by someone else while you were editing it.<br /> Please copy your changes and re-edit this page.');
if(!defined('ERROR_MISSING_EDIT_NOTE')) define('ERROR_MISSING_EDIT_NOTE', 'MISSING EDIT NOTE: Please fill in an edit note!');
if(!defined('ERROR_TAG_TOO_LONG')) define('ERROR_TAG_TOO_LONG', 'Tag too long! %d characters max.');
if(!defined('ERROR_NO_WRITE_ACCESS')) define('ERROR_NO_WRITE_ACCESS', 'You don\'t have write access to this page. You might need to register an account to be able to edit this page.');
if(!defined('MESSAGE_AUTO_RESIZE')) define('MESSAGE_AUTO_RESIZE', 'Clicking on %s will automatically truncate the tag to the correct size');
if(!defined('INPUT_SUBMIT_PREVIEW')) define('INPUT_SUBMIT_PREVIEW', 'Preview');
if(!defined('INPUT_SUBMIT_STORE')) define('INPUT_SUBMIT_STORE', 'Store');
if(!defined('INPUT_SUBMIT_REEDIT')) define('INPUT_SUBMIT_REEDIT', 'Re-edit');
if(!defined('INPUT_BUTTON_CANCEL')) define('INPUT_BUTTON_CANCEL', 'Cancel');
if(!defined('INPUT_SUBMIT_RENAME')) define('INPUT_SUBMIT_RENAME', 'Rename');
if(!defined('ACCESSKEY_STORE')) define('ACCESSKEY_STORE', 's');
if(!defined('ACCESSKEY_REEDIT')) define('ACCESSKEY_REEDIT', 'r');
if(!defined('ACCESSKEY_PREVIEW')) define('ACCESSKEY_PREVIEW', 'p');
if(!defined('SHOWCODE_LINK')) define('SHOWCODE_LINK', 'View formatting code for this page');
if(!defined('SHOWCODE_LINK_TITLE')) define('SHOWCODE_LINK_TITLE', 'Click to view page formatting code');

//initialization
$error = '';
$highlight_note = '';
$edit_note_field = '';
$note = '';
$ondblclick = ''; //#123
if (isset($_POST['submit']) && ($_POST['submit'] == 'Preview') && ($user = $this->GetUser()) && ($user['doubleclickedit'] != 'N'))
{
	$ondblclick = ' ondblclick=\'document.getElementById("reedit_id").click();\'';
	//history.back() not working on IE. (changes are lost)
	//however, history.back() works fine in FF, and this is the optimized choice
	//TODO Optimization: Look $_SERVER['HTTP_USER_AGENT'] and use history.back() for good browsers like FF.
}
?>
<div class="page"<?php echo $ondblclick;?>>
<?php
if (!(preg_match(VALID_PAGENAME_PATTERN, $this->tag))) { //TODO use central regex library
	echo '<em>'.ERROR_INVALID_PAGENAME.'</em>';
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

		$body = preg_replace("/\n[ ]{4}/", "\n\t", $body);	// @@@ FIXME: misses first line and multiple sets of four spaces

		// we don't need to escape here, we do that just before display (i.e., treat note just like body!)
		$note = trim($_POST['note']);

		// only if saving:
		if ($_POST['submit'] == 'Store')
		{
			// check for overwriting
			if ($this->page)
			{
				if ($this->page['id'] != $_POST['previous'])
				{
					$error = ERROR_OVERWRITE_ALERT;
				}
			}
			// check for edit note
			if (($this->config['require_edit_note'] == 1) && $_POST['note'] == '')
			{
				$error .= ERROR_MISSING_EDIT_NOTE;
				$highlight_note= INPUT_ERROR_STYLE;
			}
			// store
			if (!$error)
			{
				// only save if new body differs from old body
				if ($body != $this->page['body']) {

					// add page (revisions)
					$this->SavePage($this->tag, $body, $note);

					// now we render it internally so we can write the updated link table.
					$this->ClearLinkTable();
					$this->StartLinkTracking();
					$dummy = $this->Header();
					$dummy .= $this->Format($body);
					$dummy .= $this->Footer();
					$this->StopLinkTracking();
					$this->WriteLinkTable();
					$this->ClearLinkTable();
				}

				// forward
				$this->Redirect($this->Href());
			}
		}
	}

	 //check if edit_notes are enabled
	if ($this->config['require_edit_note'] != 2)
	{
		$edit_note_field = '<input size="'.MAX_EDIT_NOTE_LENGTH.'" type="text" name="note" value="'.htmlspecialchars($note).'" '.$highlight_note.'/> '.LABEL_EDIT_NOTE.'<br />'."\n";
	}

	// fetch fields
	$previous = $this->page['id'];
	if (isset($_POST['previous'])) $previous = $_POST['previous'];
	if (!isset($body)) $body = $this->page['body'];
	$body = preg_replace("/\n[ ]{4}/", "\n\t", $body);	// @@@ FIXME: misses first line and multiple sets of four spaces - JW 2005-01-16


	if ($result = mysql_query("describe ".$this->config['table_prefix']."pages tag")) {
		$field = mysql_fetch_assoc($result);
		if (preg_match("/varchar\((\d+)\)/", $field['Type'], $matches)) $maxtaglen = $matches[1];
	}
	else
	{
		$maxtaglen = MAX_TAG_LENGTH;
	}
	
	// PREVIEW screen
	if (isset($_POST['submit']) && $_POST['submit'] == INPUT_SUBMIT_PREVIEW)
	{
		// We need to escape ALL entity refs before display so we display them _as_ entities instead of interpreting them
		// so we use htmlspecialchars on the edit note (as on the body)
		$preview_buttons = '<fieldset><legend>Store page</legend>'.$edit_note_field.'<input name="submit" type="submit" value="'.INPUT_SUBMIT_STORE.'" accesskey="'.ACCESSKEY_STORE.'" />'."\n".
			'<input name="submit" type="submit" value="'.INPUT_SUBMIT_REEDIT.'" accesskey="'.ACCESSKEY_REEDIT.'" id="reedit_id" />'."\n".
			'<input type="button" value="'.INPUT_BUTTON_CANCEL.'" onclick="document.location=\''.$this->href('').'\';" /></fieldset>'."\n";

		//build page
		$output .= '<div class="previewhead">'.PREVIEW_HEADER.'</div>'."\n";
		$output .= $this->Format($body);
		$output .= $this->FormOpen('edit')."\n";
		$output .= '<input type="hidden" name="previous" value="'.$previous.'" />'."\n".
			// We need to escape ALL entity refs before display so we display them _as_ entities instead of interpreting them
			// hence htmlspecialchars() instead of htmlspecialchars_ent() which UNescapes entities!
			'<input type="hidden" name="body" value="'.htmlspecialchars($body).'" />'."\n";
		$output .= $preview_buttons."\n";
		$output .= $this->FormClose()."\n";
	}
	elseif (!$this->page && strlen($this->tag) > $maxtaglen) # rename page
	{
		$this->tag = substr($this->tag, 0, $maxtaglen); // truncate tag to feed a backlinks-handler with the correct value. may be omited. it only works if the link to a backlinks-handler is built in the footer.
		$output  = '<em class="error">'.sprintf(ERROR_TAG_TOO_LONG, $maxtaglen).'</em><br />'."\n";
		$output .= sprintf(MESSAGE_AUTO_RESIZE, INPUT_SUBMIT_RENAME).'<br /><br />'."\n";
		$output .= $this->FormOpen('edit');
		$output .= '<input name="newtag" size="'.MAX_TAG_LENGTH.'" value="'.$this->htmlspecialchars_ent($this->tag).'" />';
		$output .= '<input name="submit" type="submit" value="'.INPUT_SUBMIT_RENAME.'" />'."\n";
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
		if (isset($_REQUEST['appendcomment']))
		{
			$body = trim($body)."\n\n----\n\n--".$this->GetUserName().' ('.strftime("%c").')';
		}
		$edit_buttons =	'<fieldset><legend>Store page</legend>'.$edit_note_field.'<input name="submit" type="submit" value="'.INPUT_SUBMIT_STORE.'" accesskey="'.ACCESSKEY_STORE.'" /> <input name="submit" type="submit" value="'.INPUT_SUBMIT_PREVIEW.'" accesskey="'.ACCESSKEY_PREVIEW.'" /> <input type="button" value="'.INPUT_BUTTON_CANCEL.'" onclick="document.location=\''.$this->Href('').'\';" />'."</fieldset>\n";
		$output .= $this->FormOpen('edit');
		if ($this->config['edit_buttons_position'] != 'bottom')
		{
			$output .= $edit_buttons;
		}
		$output .= '<input type="hidden" name="previous" value="'.$previous.'" />'."\n".
			// We need to escape ALL entity refs before display so we display them _as_ entities instead of interpreting them
			// hence htmlspecialchars() instead of htmlspecialchars_ent() which UNescapes entities!
			'<textarea id="body" name="body">'.htmlspecialchars($body).'</textarea><br />'."\n";
			//note add Edit
			// We need to escape ALL entity refs before display so we display them _as_ entities instead of interpreting them
			// so we use htmlspecialchars on the edit note (as on the body)
		if ($this->config['edit_buttons_position'] != 'top')
		{
			$output .= $edit_buttons;			
		}
		$output .=	$this->FormClose();

		if ($this->config['gui_editor'] == 1) 
		{
			$output .= '<script type="text/javascript" src="3rdparty/plugins/wikiedit/protoedit.js"></script>'."\n".
					'<script type="text/javascript" src="3rdparty/plugins/wikiedit/wikiedit2.js"></script>'."\n";
			$output .= '<script type="text/javascript">'."  wE = new WikiEdit(); wE.init('body','WikiEdit','editornamecss');".'</script>'."\n";
		}
	}

	echo $output;
}
else
{
	$message =	'<em>'.ERROR_NO_WRITE_ACCESS.'</em><br />'."\n".
			"<br />\n".
			'<a href="'.$this->Href('showcode').'" title="'.SHOWCODE_LINK_TITLE.'">'.SHOWCODE_LINK.'</a>'.
			"<br />\n";
	echo $message;
}
echo '</div>'."\n" //TODO: move to templating class
?>