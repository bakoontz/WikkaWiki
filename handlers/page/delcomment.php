<?php
/**
 * Delete a comment if the user is an admin, page owner or has posted the comment.
 * 
 * @package		Handlers
 * @subpackage	Page
 * @version		$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses	Config::table_prefix
 * @uses	Wakka::UserIsOwner()
 * @uses	Wakka::LoadSingle()
 * @uses	Wakka::GetUserName()
 * @uses	Wakka::getSessionKey()
 * @uses	Wakka::hasValidSessionKey()
 * @uses	Wakka::Query()
 * @uses	Href()
 * 
 */

if (!defined('ERROR_COMMENT_NO_KEY')) define('ERROR_COMMENT_NO_KEY', "Your comment cannot be saved. Please contact the wiki administrator(1).");
if (!defined('ERROR_COMMENT_INVALID_KEY')) define('ERROR_COMMENT_INVALID_KEY', "Your comment cannot be saved. Please contact the wiki administrator(2).");
if(!defined('ERROR_COMMENT_INVALID_USER')) define('ERROR_COMMENT_INVALID_USER', "Sorry, you\'re not allowed to delete this comment!");

if(isset($_POST['form_id']) && isset($_POST["comment_id"])) 
{
	//select comment
	$comment_id = intval(trim($_POST["comment_id"]));
	$comment = $this->LoadSingle("select user from ".$this->config["table_prefix"]."comments where id = '".$comment_id."' limit 1");
	$current_user = $this->GetUserName();	
	
    if (FALSE == ($aKey = $this->getSessionKey($_POST['form_id'])))	# check if form key was stored in session
	{
		$this->Redirect($this->Href(), ERROR_COMMENT_NO_KEY);
	}
	else if (TRUE != ($rc = $this->hasValidSessionKey($aKey)))	# check if correct name,key pair was passed
	{
		$this->Redirect($this->Href(), ERROR_COMMENT_INVALID_KEY);
	}
	else if (!$this->UserIsOwner() && !$comment["user"]==$current_user)
	{
		$this->Redirect($this->Href(), ERROR_COMMENT_INVALID_USER);
	}
	else
	{
		$this->Query("DELETE FROM ".$this->config["table_prefix"]."comments WHERE id = '".$comment_id."' LIMIT 1");
		// redirect to page
		$this->redirect($this->Href(), 'Comment succesfully deleted.');
	}
}
?>
