<?php

//print("<xmp>"); print_r($_REQUEST); exit;

if ($this->HasAccess("comment") || $this->IsAdmin())
{
	// find number
	if ($latestComment = $this->LoadSingle("select tag, id from ".$this->config["table_prefix"]."pages where comment_on != '' order by id desc limit 1"))
	{
		preg_match("/^Comment([0-9]+)$/", $latestComment["tag"], $matches);
		$num = $matches[1] + 1;
	}
	else
	{
		$num = "1";
	}

	$body = trim($_POST["body"]);
	if (!$body)
	{
		$this->SetMessage("Comment body was empty -- not saved!");
	}
	else
	{
		// store new comment
		$this->SavePage("Comment".$num, $body, "", $this->tag);
	}

	
	// redirect to page
	$this->redirect($this->href());
}
else
{
	print("<div class=\"page\"><em>Sorry, you're not allowed to post comments to this page.</em></div>\n");
}

?>