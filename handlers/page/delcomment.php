<?php
/**
 * Handle the deletion of a comment.
 * 
 * @package	Handlers
 * @subpackage	Comments
 * @version $Id$
 * 
 * @uses	Wakka::GetUserName()
 * @uses	Wakka::Href()
 * @uses	Wakka::LoadSingle()
 * @uses	Wakka::Query()
 * @uses	Wakka::redirect()
 * @uses	Wakka::UserIsOwner()
 * @todo		move main <div> to templating class
 * @filesource
 */
/**
 * i18n
 */
if (!defined('ERROR_NO_RIGHT_TO_DELETE_COMMENT')) define('ERROR_NO_RIGHT_TO_DELETE_COMMENT', "Sorry, you're not allowed to delete this comment!");

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
	print('<div class="page"><em class="error">'.ERROR_NO_RIGHT_TO_DELETE_COMMENT.'</em></div>'."\n");
}

?>