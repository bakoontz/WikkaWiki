<div class="page">
<?php
if ($this->HasAccess("write") && $this->HasAccess("read"))
{
	if ($_POST)
	{
		$note = trim($_POST["note"]);
		// prepare body
		$body = str_replace("\r", "", $_POST["body"]);

		// replace 4 consecutive spaces with tab character
		$body = str_replace("    ", "\t", $body);

		// only if saving:
		if ($_POST["submit"] == "Store")
		{
			// check for overwriting
			if ($this->page)
			{
				if ($this->page["id"] != $_POST["previous"])
				{
					$error = "OVERWRITE ALERT: This page was modified by someone else while you were editing it.<br />\nPlease copy your changes and re-edit this page.";
				}
			}


			// store
			if (!$error)
			{
				// only save if new body differs from old body
				if (trim($body) != $this->page['body']) {
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
				$this->Redirect($this->href());
			}
		}
	}

	// fetch fields
	if (!$previous = $_POST["previous"]) $previous = $this->page["id"];
	if (!$body) $body = $this->page["body"];

	// preview?
	if ($_POST["submit"] == "Preview")
	{
		$previewButtons =
		    "<hr />\n".
		    "<input size=\"40\" type=\"text\" name=\"note\" value=\"".htmlspecialchars($note)."\"/> Note on your edit.<br />\n".
			"<input name=\"submit\" type=\"submit\" value=\"Store\" accesskey=\"s\" />\n".
			"<input name=\"submit\" type=\"submit\" value=\"Re-Edit\" accesskey=\"p\" />\n".
			"<input type=\"button\" value=\"Cancel\" onClick=\"document.location='".$this->href("")."';\" />\n";
		
		$output .= "<div class=\"previewhead\">Preview</div>\n";

		$output .= $this->Format($body);

		$output .=
			$this->FormOpen("edit")."\n".
			"<input type=\"hidden\" name=\"previous\" value=\"".$previous."\" />\n".
			"<input type=\"hidden\" name=\"body\" value=\"".htmlspecialchars($body)."\" />\n";
		
		

		$output .=
			"<br />\n".
			$previewButtons.
			$this->FormClose()."\n";
	}
	else
	{
		// display form
		if ($error)
		{
			$output .= "<div class=\"error\">$error</div>\n";
		}

		// append a comment?
		if ($_REQUEST["appendcomment"])
		{
			$body = trim($body)."\n\n----\n\n--".$this->UserName()." (".strftime("%c").")";
		}

		$output .=
			$this->FormOpen("edit").
			"<input type=\"hidden\" name=\"previous\" value=\"".$previous."\" />\n".
			"<textarea onKeyDown=\"fKeyDown()\" id=\"body\" name=\"body\" style=\"width: 100%; height: 500px\">".htmlspecialchars($body)."</textarea><br />\n".
			//note add Edit
			"<input size=\"40\" type=\"text\" name=\"note\" value=\"".htmlspecialchars($note)."\" /> Please add a note on your edit.<br />\n".
			//finsih
			"<input name=\"submit\" type=\"submit\" value=\"Store\" accesskey=\"s\" /> <input name=\"submit\" type=\"submit\" value=\"Preview\" accesskey=\"p\" /> <input type=\"button\" value=\"Cancel\" onClick=\"document.location='".$this->href("")."';\" />\n".
			$this->FormClose();

		if ($this->GetConfigValue("gui_editor") == 1) {
	     		$output .=
	    	     	"<script language=\"JavaScript\" src=\"wikiedit2/protoedit.js\"></script>\n".
     			"<script language=\"JavaScript\" src=\"wikiedit2/wikiedit2.js\"></script>\n";
 	       	$output .= "<script type=\"text/javascript\">  wE = new WikiEdit(); wE.init('body','WikiEdit','editornamecss');</script>\n";
		}
	}


	print($output);
}
else
{
	print("<em>You don't have write access to this page.</em>");
}
?>
</div>
