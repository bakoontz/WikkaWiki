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
 * @uses	Wakka::Query()
 * @uses	Href()
 * 
 */

if(isset($_POST['form_id']) && isset($_POST["comment_id"])) 
{
	//select comment
	$comment_id = intval(trim($this->GetSafeVar('comment_id', 'post')));
	$comment = $this->LoadSingle("select user from ".$this->config["table_prefix"]."comments where id = '".$comment_id."' limit 1");
	$current_user = $this->GetUserName();	
	
	$delete = FALSE;
	if ($this->UserIsOwner() || $comment["user"]==$current_user)
	{
		$delete = TRUE;
	}
			
	// delete comment
	if (TRUE === $delete)
	{
		$this->Query("DELETE FROM ".$this->config["table_prefix"]."comments WHERE id = '".$comment_id."' LIMIT 1");
		// redirect to page
		$this->redirect($this->Href(), 'Comment succesfully deleted.');
	}
	else
	{
		echo '<div id="content"><em class="error">Sorry, you are not allowed to delete this comment!</em></div>'."\n";
	}
}
?>
