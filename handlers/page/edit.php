<div class="page">
<?php
if ($this->HasAccess("write") && $this->HasAccess("read"))
{
	if ($newtag = $_POST["newtag"]) $this->Redirect($this->href("edit", $newtag));
	if ($_POST)
	{
		// prepare body
		$body = str_replace("\r", "", $_POST["body"]);
		// replace 4 consecutive spaces with tab character
		$body = str_replace("    ", "\t", $body);

		$note = trim($_POST["note"]);

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
				if (preg_match("/^([\t ]+)(-|([1aiAI]\))?)/", $body, $matches)) $lead = $matches[1]; 
				if ($lead.trim($body) != $this->page['body']) { 
	
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


	if ($result = mysql_query("describe ".$this->config["table_prefix"]."pages tag")) {
		$field = mysql_fetch_assoc($result);
		if (preg_match("/varchar\((\d+)\)/", $field["Type"], $matches)) $maxtaglen = $matches[1];
	}
	else
	{
		$maxtaglen = 75;
	}

	// preview?
	if ($_POST["submit"] == "Preview")
	{
		$previewButtons =
		    "<hr />\n".
		    "<input size=\"50\" type=\"text\" name=\"note\" value=\"".htmlspecialchars($note)."\"/> Note on your edit.<br />\n".
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
	elseif (!$this->page && strlen($this->tag) > $maxtaglen) 
	{
		$this->tag = substr($this->tag, 0, $maxtaglen); // truncate tag to feed a backlinks-handler with the correct value. may be omited. it only works if the link to a backlinks-handler is built in the footer.	
		$output  = "<div class='error'>Tag too long! $maxtaglen characters max.</div>\n";
		$output .= "<br />FYI: Clicking on Rename will automatically truncate the tag to the correct size.<br /><br />\n";
		$output .= $this->FormOpen("edit");
		$output .= "<input name='newtag' size='75' value='".htmlspecialchars($this->tag)."' />"; // use htmlspecialchars($this->tag, ENT_QUOTES) if you need single quotation marks (i.e. apostrophies) in tags
		$output .= "<input name='submit' type='submit' value='Rename' /> ";
		$output .= $this->FormClose();
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
			$body = trim($body)."\n\n----\n\n--".$this->GetUserName()." (".strftime("%c").")";
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
