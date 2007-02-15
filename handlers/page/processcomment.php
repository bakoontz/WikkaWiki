<?php
/**
 * Process a comment.
 * 
 * @package	Handlers
 * @subpackage	Comments
 * @version $Id: processcomment.php,v 1.3.1.3 2007/02/11 11:39:16 brian Exp brian $
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses	Wakka::GetUserName()
 * @uses	Wakka::Href()
 * @uses	Wakka::LoadSingle()
 * @uses	Wakka::Query()
 * @uses	Wakka::redirect()
 * @uses	Wakka::SaveComment()
 * @uses	Wakka::UserIsOwner()
 * @todo	move main <div> to templating class
 */

// Get comment id
$comment_id = intval(trim($_POST["comment_id"]));

// Delete comment
if($_POST['submit']==COMMENT_DELETE_BUTTON) {
	$comment = $this->LoadSingle("select user, parent from ".$this->config["table_prefix"]."comments where id = '".$comment_id."' limit 1");
	$current_user = $this->GetUserName();

	if ($this->UserIsOwner() || $comment["user"]==$current_user)
	{
		$this->Query("UPDATE ".$this->config["table_prefix"]."comments SET deleted='Y' WHERE id='".$comment_id."' LIMIT 1");
		// redirect to page
		$this->redirect($this->Href());
	}
	else
	{
		print('<div class="page"><em class="error">'.ERROR_NO_COMMENT_DEL_ACCESS.'</em></div>'."\n");
	}
}

// Display entry area for comment
if($_POST['submit']==COMMENT_REPLY_BUTTON ||
   $_POST['submit']==COMMENT_NEW_BUTTON) {
	// display comment form
	echo '<div class="commentform">'."\n";
	if ($this->HasAccess('comment'))
	{?>
		<?php echo $this->FormOpen('processcomment'); ?>
		<input type="hidden" name="comment_id" value="<?php echo $comment_id ?>" >
		<label for="commentbox"><?php echo ADD_COMMENT_LABEL; ?><br />
		<textarea id="commentbox" name="body" rows="6" cols="78"></textarea><br />
		<input type="submit" name="submit" value="<?php echo COMMENT_ADD_BUTTON; ?>" accesskey="s" />
		</label>
		<?php echo $this->FormClose(); ?>
	<?php
	}
}

// Save comment
if($_POST['submit']==COMMENT_ADD_BUTTON) {
	$parent_id = intval(trim($_POST["comment_id"]));
	if ($this->HasAccess("comment") || $this->IsAdmin())
	{
		$redirectmessage = "";

		$body = nl2br($this->htmlspecialchars_ent(trim($_POST["body"])));

		if (!$body)
		{
			$redirectmessage = ERROR_EMPTY_COMMENT;
		}
		else
		{
			// store new comment
			$this->SaveComment($this->tag, $body, $parent_id);
		}
		
		// redirect to page
		$this->redirect($this->Href(), $redirectmessage);
	}
	else
	{
		print('<div class="page"><em class="error">'.ERROR_NO_COMMENT_WRITE_ACCESS.'</em></div>'."\n");
	}
}
?>
