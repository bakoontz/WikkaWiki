<?php

//constant section
define('COMMENT_BODY_EMPTY', 'Comment body was empty -- not saved!');
define('COMMENT_NOT_ALLOWED', "Sorry, you're not allowed to post comments to this page.");

if ($this->HasAccess("comment") || $this->IsAdmin())
{
	$redirectmessage = "";

	$body = nl2br($this->htmlspecialchars_ent(trim($_POST["body"])));

	if (!$body)
	{
		$redirectmessage = COMMENT_BODY_EMPTY;
	}
	else
	{
		// store new comment
		$this->SaveComment($this->tag, $body);
	}

	// redirect to page
	$this->redirect($this->Href(), $redirectmessage);
}
else
{
	print('<div class="page"><em>'.COMMENT_NOT_ALLOWED."</em></div>\n");
}

?>