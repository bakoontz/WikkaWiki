<?php

//select comment and delete it
$comment_id = intval(trim($_POST["comment_id"]));
$comment = $this->LoadSingle("select user from ".$this->config["table_prefix"]."comments where id = '".$comment_id."' limit 1");
$current_user = $this->GetUserName();
if ($this->UserIsOwner() || $comment["user"]==$current_user || $this->IsAdmin())
{
	$deleted = $this->LoadSingle("delete from ".$this->config["table_prefix"]."comments where id = '".$comment_id."' limit 1");
	// redirect to page
	$this->redirect($this->href());
}
else
{
	print("<div class=\"page\"><em>Sorry, you're not allowed to delete this comment!</em></div>\n");
}

?>