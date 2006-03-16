<?php

// constant section
define(ERROR_DELETE_COMMENT_NOT_ALLOWED', "Sorry, you're not allowed to delete this comment!");

//select comment and delete it
$comment_id = intval(trim($_POST["comment_id"]));
$comment = $this->LoadSingle("select user from ".$this->config["table_prefix"]."comments where id = '".$comment_id."' limit 1");
$current_user = $this->GetUserName();

if ($this->UserIsOwner() || $comment["user"]==$current_user)
{
	$this->Query("DELETE FROM ".$this->config["table_prefix"]."comments WHERE id = '".$comment_id."' LIMIT 1");

	// redirect to page
	$this->redirect($this->Href());
}
else
{
	print('<div class="page"><em>'.ERROR_DELETE_COMMENT_NOT_ALLOWED."</em></div>\n");
}

?>