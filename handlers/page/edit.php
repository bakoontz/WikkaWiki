<div class="page">
<?php

// constant section
define('ERROR_INVALID_PAGE_NAME', 'The page name is invalid. Valid page names must start with a letter and contain only letters and numbers.');
define('ERROR_OVERWRITE_ALERT', 'OVERWRITE ALERT: This page was modified by someone else while you were editing it. --- Please copy your changes and re-edit this page.'); // gets wikka-formatted
define('ERROR_NO_WRITE_ACCESS', "You don't have write access to this page. You might need to register an account to get write access.");
//define('ERROR_PAGE_NAME_TOO_LONG', '');
define('PAGE_EDIT_NOTE_FORM_LABEL', 'Please add a note on your edit.');
define('PAGE_PREVIEW_LABEL', 'Preview');
define('PAGE_PREVIEW_NOTE_FORM_LABEL', 'Note on your edit.');
define('STORE_BUTTON', 'Store');
define('PREVIEW_BUTTON', 'Preview');
define('RE_EDIT_BUTTON', 'Re-Edit');
define('CANCEL_BUTTON', 'Cancel');
define('VIEW_FORMATTING_CODE_LINK_TITLE', 'View formatting code for this page');
define('VIEW_FORMATTING_CODE_LINK_LABEL', 'Click to view page formatting code');

if (!(preg_match("/^[A-Za-zÄÖÜßäöü]+[A-Za-z0-9ÄÖÜßäöü]*$/s", $this->tag))) {
	echo '<em>'.ERROR_INVALID_PAGE_NAME.'</em>';
}
elseif ($this->HasAccess("write") && $this->HasAccess("read"))
{
	if ($newtag = $_POST['newtag']) $this->Redirect($this->Href('edit', $newtag));
	if ($_POST)
	{
		// strip CRLF line endings down to LF to achieve consistency ... plus it saves database space.
		// Note: these codes must remain enclosed in double-quotes to work! -- JsnX
		$body = str_replace("\r\n", "\n", $_POST['body']);

		$body = preg_replace("/\n[ ]{4}/", "\n\t", $body);						# @@@ FIXME: misses first line and multiple sets of four spaces - JW 2005-01-16

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
					$error = $this->Format(OVERWRITE_ALERT);
				}
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

	// fetch fields
	if (!$previous = $_POST['previous']) $previous = $this->page['id'];
	if (!$body) $body = $this->page['body'];
	$body = preg_replace("/\n[ ]{4}/", "\n\t", $body);						# @@@ FIXME: misses first line and multiple sets of four spaces - JW 2005-01-16


	if ($result = mysql_query("describe ".$this->config['table_prefix']."pages tag")) {
		$field = mysql_fetch_assoc($result);
		if (preg_match("/varchar\((\d+)\)/", $field['Type'], $matches)) $maxtaglen = $matches[1];
	}
	else
	{
		$maxtaglen = 75;
	}

	// preview?
	if ($_POST['submit'] == 'Preview')										# preview page
	{
		$previewButtons =
			"<hr />\n".
			// We need to escape ALL entity refs before display so we display them _as_ entities instead of interpreting them
			// so we use htmlspecialchars on the edit note (as on the body)
			'<input size="50" type="text" name="note" value="'.htmlspecialchars($note).'"/> '.PAGE_PREVIEW_NOTE_FORM_LABEL.'<br />'."\n".
			'<input name="submit" type="submit" value="'.STORE_BUTTON.'" accesskey="s" />'."\n".
			'<input name="submit" type="submit" value="'.RE_EDIT_BUTTON.'" accesskey="p" />'."\n".
			'<input type="button" value="'.CANCEL_BUTTON.'" onclick="document.location=\''.$this->href('').'\';" />'."\n";

		$output .= '<div class="previewhead">'.PAGE_PREVIEW_LABEL.'</div>'."\n";

		$output .= $this->Format($body);

		$output .=
			$this->FormOpen('edit')."\n".
			'<input type="hidden" name="previous" value="'.$previous.'" />'."\n".
			// We need to escape ALL entity refs before display so we display them _as_ entities instead of interpreting them
			// hence htmlspecialchars() instead of htmlspecialchars_ent() which UNescapes entities!
			'<input type="hidden" name="body" value="'.htmlspecialchars($body).'" />'."\n";


		$output .=
			"<br />\n".
			$previewButtons.
			$this->FormClose()."\n";
	}
	elseif (!$this->page && strlen($this->tag) > $maxtaglen)				# rename page
	{
		$this->tag = substr($this->tag, 0, $maxtaglen); // truncate tag to feed a backlinks-handler with the correct value. may be omited. it only works if the link to a backlinks-handler is built in the footer.
		$output  = '<div class="error">Tag too long! $maxtaglen characters max.</div><br />'."\n"; #i18n
		$output .= 'FYI: Clicking on Rename will automatically truncate the tag to the correct size.<br /><br />'."\n"; #i18n
		$output .= $this->FormOpen('edit');
		$output .= '<input name="newtag" size="75" value="'.$this->htmlspecialchars_ent($this->tag).'" />';
		$output .= '<input name="submit" type="submit" value="Rename" />'."\n";
		$output .= $this->FormClose();
	}
	else																	# edit page
	{
		// display form
		if ($error)
		{
			$output .= '<div class="error">'.$error.'</div>'."\n";
		}

		// append a comment?
		if ($_REQUEST['appendcomment'])
		{
			$body = trim($body)."\n\n----\n\n--".$this->GetUserName().' ('.strftime("%c").')';
		}

		$output .=
			$this->FormOpen('edit').
			'<input type="hidden" name="previous" value="'.$previous.'" />'."\n".
			// We need to escape ALL entity refs before display so we display them _as_ entities instead of interpreting them
			// hence htmlspecialchars() instead of htmlspecialchars_ent() which UNescapes entities!
			'<textarea id="body" name="body" style="width: 100%; height: 500px">'.htmlspecialchars($body).'</textarea><br />'."\n".
			//note add Edit
			// We need to escape ALL entity refs before display so we display them _as_ entities instead of interpreting them
			// so we use htmlspecialchars on the edit note (as on the body)
			'<input size="40" type="text" name="note" value="'.htmlspecialchars($note).'" /> '.PAGE_EDIT_NOTE_FORM_LABEL.'<br />'."\n".
			//finish
			'<input name="submit" type="submit" value="'.STORE_BUTTON.'" accesskey="s" /> <input name="submit" type="submit" value="'.PREVIEW_BUTTON.'" accesskey="p" /> <input type="button" value="'.CANCEL_BUTTON.'" onclick="document.location=\''.$this->Href('').'\';" />'."\n".
			$this->FormClose();

		if ($this->GetConfigValue('gui_editor') == 1) {
			$output .=
					'<script language="JavaScript" src="3rdparty/plugins/wikiedit/protoedit.js"></script>'."\n".
					'<script language="JavaScript" src="3rdparty/plugins/wikiedit/wikiedit2.js"></script>'."\n";
			$output .= '<script type="text/javascript">'."  wE = new WikiEdit(); wE.init('body','WikiEdit','editornamecss');".'</script>'."\n";
		}
	}

	echo $output;
}
else
{
	$message =	'<em>'.ERROR_NO_WRITE_ACCESS.'</em><br />'."\n".
			"<br />\n".
			'<a href="'.$this->Href('showcode').'" title="'.VIEW_FORMATTING_CODE_LINK_LABEL.'">'.VIEW_FORMATTING_CODE_LINK_TITLE.'</a>'.
			"<br />\n"; #i18n
	echo $message;
}
?>
</div>