<?php

if ($this->HasAccess("comment") || $this->IsAdmin())
{
	$body = trim($_POST["body"]);
	if (!$body)
	{
		$this->SetMessage("Comment body was empty -- not saved!");
	}
	else
	{
		// store new comment
		$this->SaveComment($this->tag, $body);
	}

	
	// redirect to page
	$this->redirect($this->href());
}
else
{
	print("<div class=\"page\"><em>Sorry, you're not allowed to post comments to this page.</em></div>\n");
}

?>