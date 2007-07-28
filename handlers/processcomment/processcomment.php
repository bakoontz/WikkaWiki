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
 * @author {http://wikkawiki.org/BrianKoontz Brian Koontz}
 * 
 * @uses	Wakka::GetUserName()
 * @uses	Wakka::Href()
 * @uses	Wakka::LoadSingle()
 * @uses	Wakka::Query()
 * @uses	Wakka::redirect()
 * @uses	Wakka::SaveComment()
 * @uses	Wakka::UserIsOwner()
 * @uses	Wakka::htmlspecialchars_ent()
 * 
 * @todo	move main <div> to templating class
 */

// Get comment id
$comment_id = intval(trim($_POST['comment_id']));

// Delete comment
if ($_POST['submit']==COMMENT_DELETE_BUTTON && $this->HasAccess('comment_post'))
{
	$comment = $this->LoadSingle("SELECT user, parent FROM ".$this->GetConfigValue('table_prefix')."comments WHERE id = '".$comment_id."' LIMIT 1");
	$current_user = $this->GetUserName();

	if ($this->UserIsOwner() || $comment["user"]==$current_user)
	{
		$this->Query("UPDATE ".$this->GetConfigValue('table_prefix')."comments SET status='deleted' WHERE id='".$comment_id."' LIMIT 1");
		// redirect to page
		$this->redirect($this->Href());
	}
	else
	{
		echo '<div class="page"><em class="error">'.ERROR_NO_COMMENT_DEL_ACCESS.'</em></div>'."\n";
	}
}

// Display entry area for comment
if(($_POST['submit']==COMMENT_REPLY_BUTTON || $_POST['submit']==COMMENT_NEW_BUTTON) && $this->HasAccess('comment_post'))
{
	// display comment form
	$comment = '';
	if(isset($comment_id))
	{
		$comment = $this->LoadSingle("SELECT user, comment FROM ".$this->GetConfigValue('table_prefix')."comments WHERE id = '".$comment_id."' LIMIT 1");
	}
?>
	<div class="commentform">
	<?php echo $this->FormOpen('processcomment'); ?>
	<input type="hidden" name="comment_id" value="<?php echo $comment_id ?>" />
	<?php if($_POST['submit']==COMMENT_REPLY_BUTTON) { ?>
	<label for="commentbox"><?php printf(ADD_COMMENT_LABEL, $this->FormatUser($comment['user'])); ?></label><br />
	<div class="commentparent"><?php echo $comment['comment']; ?></div>
	<?php } else { ?>
	<label for="commentbox"><?php echo NEW_COMMENT_LABEL; ?></label><br />
	<?php } ?>	
	<textarea id="commentbox" name="body" rows="6" cols="78"></textarea><br />
	<input type="submit" name="submit" value="<?php echo COMMENT_ADD_BUTTON; ?>" accesskey="s" />
	<?php echo $this->FormClose(); ?>
	</div>
<?php
}

// Save comment
if ($_POST['submit']==COMMENT_ADD_BUTTON)
{
	$parent_id = intval(trim($_POST['comment_id']));
	if ($this->HasAccess('comment_post') || $this->IsAdmin())
	{
		$redirectmessage = '';
		$body = nl2br($this->htmlspecialchars_ent(trim($_POST['body'])));
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
		echo '<div class="page"><em class="error">'.ERROR_NO_COMMENT_WRITE_ACCESS.'</em></div>'."\n";
	}
}
?>
