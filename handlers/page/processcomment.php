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
 * @todo		move main <div> to templating class
 */

/**
 * i18n
 */
if (!defined('ERROR_NO_RIGHT_TO_DELETE_COMMENT')) define('ERROR_NO_RIGHT_TO_DELETE_COMMENT', "Sorry, you're not allowed to delete this comment!");
if (!defined('BUTTON_DELETE_COMMENT')) define('BUTTON_DELETE_COMMENT', 'Delete Comment');
if (!defined('BUTTON_REPLY_COMMENT')) define('BUTTON_REPLY_COMMENT', 'Reply to Comment');
if (!defined('ERROR_EMPTY_COMMENT')) define ('ERROR_EMPTY_COMMENT', 'Comment body was empty -- not saved!');
if (!defined('ERROR_NO_RIGHT_TO_COMMENT')) define ('ERROR_NO_RIGHT_TO_COMMENT', "Sorry, you're not allowed to post comments to this page");
if (!defined('ADD_COMMENT_LABEL')) define('ADD_COMMENT_LABEL', 'Add a comment to this page:');
if (!defined('ADD_COMMENT')) define('ADD_COMMENT', 'Add comment');
if (!defined('BUTTON_ADD_COMMENT')) define('BUTTON_ADD_COMMENT', 'Add Comment');
if (!defined('BUTTON_NEW_COMMENT')) define('BUTTON_NEW_COMMENT', 'New Comment');

// Get comment id
$comment_id = intval(trim($_POST["comment_id"]));

// Delete comment
if($_POST['submit']==BUTTON_DELETE_COMMENT) {
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
		print('<div class="page"><em class="error">'.ERROR_NO_RIGHT_TO_DELETE_COMMENT.'</em></div>'."\n");
	}
}

// Display entry area for comment
if($_POST['submit']==BUTTON_REPLY_COMMENT ||
   $_POST['submit']==BUTTON_NEW_COMMENT) {
	// display comment form
	echo '<div class="commentform">'."\n";
	if ($this->HasAccess('comment'))
	{?>
		<?php echo $this->FormOpen('processcomment'); ?>
		<input type="hidden" name="comment_id" value="<?php echo $comment_id ?>" >
		<label for="commentbox"><?php echo ADD_COMMENT_LABEL; ?><br />
		<textarea id="commentbox" name="body" rows="6" cols="78"></textarea><br />
		<input type="submit" name="submit" value="<?php echo BUTTON_ADD_COMMENT; ?>" accesskey="s" />
		</label>
		<?php echo $this->FormClose(); ?>
	<?php
	}
}

// Save comment
if($_POST['submit']==BUTTON_ADD_COMMENT) {
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
		print('<div class="page"><em class="error">'.ERROR_NO_RIGHT_TO_COMMENT.'</em></div>'."\n");
	}
}
?>
