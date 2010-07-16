<?php
/**
 * Process a comment.
 *
 * @package		Handlers
 * @subpackage	Comments
 * @version		$Id: processcomment.php,v 1.3.1.3 2007/02/11 11:39:16 brian Exp brian $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
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
//include antispam library
//include_once('libs/antispam.lib.php');

// Get comment id
$comment_id = (int) trim($this->GetSafeVar('comment_id', 'post'));

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
	if ($comment_id)
	{
		$comment = $this->LoadSingle("SELECT user, comment FROM ".$this->GetConfigValue('table_prefix')."comments WHERE id = '".$comment_id."' LIMIT 1");
	}

	//$keyfield = createSessionKeyFieldset($this, createSessionKey($this, $this->tag.'_commentkey'));
?>
	<div id="content">
	<?php echo $this->Format($this->page['body'], 'wakka', 'page'); ?>
	<div style="clear: both"></div>
	</div><!--closing page content-->
	<div id="comments">		
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
<?php
	/*<?php echo $keyfield; ?>*/
?>
	<?php echo $this->FormClose(); ?>
	</div>
	</div>
<?php
}

// Save comment
if ($_POST['submit']==COMMENT_ADD_BUTTON)
{
	$parent_id = (int) trim($this->GetSafeVar('comment_id', 'post'));
	if (($this->HasAccess('comment_post') || $this->IsAdmin()) && $this->existsPage($this->tag))
	{
		$redirectmessage = '';
		$body = nl2br($this->htmlspecialchars_ent(trim($this->GetSafeVar('body', 'post'))));	// @@@ check for empty before converting
		if (!$body)
		{
			$redirectmessage = ERROR_EMPTY_COMMENT;
		}
/*
		elseif (FALSE === ($aKey = getSessionKey($this, $this->tag.'_commentkey'))) # check if page key was stored in session
		{
			$redirectmessage = ERROR_COMMENT_NO_KEY;
		}
		elseif (TRUE !== ($rc = hasValidSessionKey($this, $aKey)))  # check if correct name,key pair was passed
		{
			$redirectmessage = ERROR_COMMENT_INVALID_KEY;
		}
*/
		else
		{
			// store new comment
			$this->SaveComment($this->tag, $body, $parent_id);
		}

		// redirect to page
		$this->redirect($this->Href()."#comments", $redirectmessage);
	}
	else
	{
		echo '<div class="page"><em class="error">'.ERROR_NO_COMMENT_WRITE_ACCESS.'</em></div>'."\n";
	}
}
?>
