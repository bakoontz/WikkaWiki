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
if ($_POST['submit']==T_("Delete") && $this->HasAccess('comment_post'))
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
		echo '<div class="page"><em class="error">'.T_("Sorry, you're not allowed to delete this comment!'").'</em></div>'."\n";
	}
}

// Display entry area for comment
if(($_POST['submit']==T_("Reply") || $_POST['submit']==T_("New Comment")) && $this->HasAccess('comment_post'))
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
	<?php echo $this->FormOpen('addcomment'); ?>
	<input type="hidden" name="comment_id" value="<?php echo $comment_id ?>" />
	<?php if($_POST['submit']==T_("Reply")) { ?>
	<label for="commentbox"><?php printf(T_("In reply to %s:"), $this->FormatUser($comment['user'])); ?></label><br />
	<div class="commentparent"><?php echo $comment['comment']; ?></div>
	<?php } else { ?>
	<label for="commentbox"><?php echo T_("Post a new comment:"); ?></label><br />
	<?php } ?>
	<textarea id="commentbox" name="body" rows="6" cols="78"></textarea><br />
	<input type="submit" name="submit" value="<?php echo T_("Add Comment"); ?>" accesskey="s" />
<?php
	/*<?php echo $keyfield; ?>*/
?>
	<?php echo $this->FormClose(); ?>
	</div>
	</div>
<?php
}

// Save comment
if ($_POST['submit']==T_("Add Comment"))
{
	$parent_id = (int) trim($this->GetSafeVar('comment_id', 'post'));
	if (($this->HasAccess('comment_post') || $this->IsAdmin()) && $this->existsPage($this->tag))
	{
		$redirectmessage = '';
		$body = nl2br($this->htmlspecialchars_ent(trim($this->GetSafeVar('body', 'post'))));	// @@@ check for empty before converting
		if (!$body)
		{
			$redirectmessage = T_("Comment body was empty -- not saved!");
		}
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
		echo '<div class="page"><em class="error">'.T_("Sorry, you're not allowed to post comments to this page'").'</em></div>'."\n";
	}
}
?>
