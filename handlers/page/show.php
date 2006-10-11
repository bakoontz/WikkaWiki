<?php
/**
 * Display a page if the user has read access or is an admin.
 * 
 * This is the default page handler used by Wikka when no other handler is specified.
 * Depending on user privileges, it displays the page body or an error message. It also
 * displays footer comments and a form to post comments, depending on ACL and general 
 * config settings.
 * 
 * @package		Handlers
 * @subpackage	Page
 * @version		$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses		Wakka::Format()
 * @uses		Wakka::FormClose()
 * @uses		Wakka::FormOpen()
 * @uses		Wakka::GetConfigValue()
 * @uses		Wakka::GetPageTag()
 * @uses		Wakka::GetUser()
 * @uses		Wakka::GetUserName()
 * @uses		Wakka::HasAccess()
 * @uses		Wakka::Href()
 * @uses		Wakka::htmlspecialchars_ent()
 * @uses		Wakka::LoadComments()
 * @uses		Wakka::LoadPage()
 * @uses		Wakka::LoadUser()
 * @uses		Wakka::UserIsOwner()
 * 
 * @todo		move <div> to template;
 * @todo		i18n;
 */

/**
 * i18n 
 */
if (!defined('ERROR_NO_ACCESS')) define('ERROR_NO_ACCESS', "You aren't allowed to read this page.");
if (!defined('COMMENTS_HEADER')) define('COMMENTS_HEADER', 'Comments');
if (!defined('HIDE_COMMENTS')) define('HIDE_COMMENTS', 'Hide comments/form');
if (!defined('ADD_COMMENT_LABEL')) define('ADD_COMMENT_LABEL', 'Add a comment to this page:');
if (!defined('ADD_COMMENT')) define('ADD_COMMENT', 'Add comment');
if (!defined('BUTTON_ADD_COMMENT')) define('BUTTON_ADD_COMMENT', 'Add Comment');
if (!defined('DISPLAY_COMMENT')) define('DISPLAY_COMMENT', 'Display comment');
if (!defined('DISPLAY_COMMENTS')) define('DISPLAY_COMMENTS', 'Display comments');
if (!defined('NO_COMMENTS')) define('NO_COMMENTS', 'There are no comments on this page.');
if (!defined('ONE_COMMENT')) define('ONE_COMMENT', 'There is one comment on this page.');
if (!defined('SOME_COMMENTS')) define('SOME_COMMENTS', 'There are %d comments on this page. ');
 
echo '<div class="page"';
echo (($user = $this->GetUser()) && ($user['doubleclickedit'] == 'N') || !$this->HasAccess('write')) ? '' : 'ondblclick="document.location=\''.$this->Href('edit').'\';" '; #268
echo '>'."\n";//TODO: move to templating class

if (!$this->HasAccess('read'))
{
	echo '<p><em class="error">'.ERROR_NO_ACCESS.'</em></p></div>';
}
else
{
	if (!$this->page)
	{
		echo '<p>This page doesn\'t exist yet. Maybe you want to <a href="'.$this->Href('edit').'">create</a> it?</p></div>'; #i18n
	}
	else
	{
		if ($this->page['latest'] == 'N')
		{
			echo '<div class="revisioninfo">This is an old revision of <a href="'.$this->Href().'">'.$this->GetPageTag().'</a> from '.$this->page['time'].'.</div>'; #i18n
		}

		// display page
		echo $this->Format($this->page['body'], 'wakka');

		// if this is an old revision, display some buttons
		if ($this->page['latest'] == 'N' && $this->HasAccess('write'))
		{
			// added if encapsulation : in case where some pages were brutally deleted from database
			if ($latest = $this->LoadPage($this->tag))
			{
?>
		        <br />
 				<?php echo $this->FormOpen('edit') ?>
 				<input type="hidden" name="previous" value="<?php echo $latest['id'] ?>" />
 				<input type="hidden" name="body" value="<?php echo $this->htmlspecialchars_ent($this->page['body']) ?>" />
 				<input type="submit" value="Re-edit this old revision" />
 				<?php echo $this->FormClose(); ?>
<?php
			}
		}
		echo '</div>'."\n";
		if ($this->GetConfigValue('hide_comments') != 1)
		{
			// load comments for this page
			$comments = $this->LoadComments($this->tag);

			// store comments display in session
			$tag = $this->GetPageTag();
			if (!isset($_SESSION['show_comments'][$tag]))
			{
				$_SESSION['show_comments'][$tag] = ($this->UserWantsComments()) ? '1' : '0';
			}
			if (isset($_REQUEST['show_comments']))
			{	
				switch($_REQUEST['show_comments'])
				{
				case "0":
					$_SESSION['show_comments'][$tag] = 0;
					break;
				case "1":
					$_SESSION['show_comments'][$tag] = 1;
					break;
				}
			}
			// display comments!
			if ($_SESSION['show_comments'][$tag] == 1)
			{
				// display comments header
?>
				<div class="commentsheader">
				<span id="comments">&nbsp;</span><?php echo COMMENTS_HEADER; ?> [<a href="<?php echo $this->Href('', '', 'show_comments=0') ?>"><?php echo HIDE_COMMENTS; ?></a>]
				</div>
<?php
				// display comments themselves
				if ($comments)
				{
					$current_user = $this->GetUserName(); 
					$is_owner = $this->UserIsOwner();
		 			foreach ($comments as $comment)
					{
						echo '<div class="comment">'."\n".
							'<span id="comment_'.$comment['id'].'"></span>'.$comment['comment']."\n".
							"\t".'<div class="commentinfo">'."\n-- ";
						echo ($this->LoadUser($comment['user']))? $this->Format($comment['user']) : $comment['user']; // #84
						echo ' ('.$comment['time'].')'."\n";
   						if ($is_owner || $user['name'] == $comment['user'] || ($this->config['anony_delete_own_comments'] && $current_user == $comment['user']))
						{
							echo $this->FormOpen("delcomment");
?>
   <input type="hidden" name="comment_id" value="<?php echo $comment['id'] ?>" />
   <input type="submit" value="Delete Comment" />
<?php 
							echo $this->FormClose();
						}
						echo "\n\t".'</div>'."\n";
						echo '</div>'."\n";
					}
				}
				// display comment form
				echo '<div class="commentform">'."\n";
				if ($this->HasAccess('comment'))
				{?>
		    			<?php echo $this->FormOpen('addcomment'); ?>
					<label for="commentbox"><?php echo ADD_COMMENT_LABEL; ?><br />
					<textarea id="commentbox" name="body" rows="6" cols="78"></textarea><br />
					<input type="submit" value="<?php echo BUTTON_ADD_COMMENT; ?>" accesskey="s" />
            			</label>
					<?php echo $this->FormClose(); ?>
				<?php
				}
				echo '</div>'."\n";
			}
			else
			{
				echo '<div class="commentsheader">'."\n";
				switch (count($comments))
				{
				case 0:
					$comments_message = NO_COMMENTS.' ';
					$showcomments_text = ADD_COMMENT;
					$comment_form_link  = ($this->HasAccess('comment')) ? 1 : 0;
					break;
				case 1:
					$comments_message = ONE_COMMENT.' ';
					$showcomments_text = DISPLAY_COMMENT;
					$comment_form_link = 1;
					break;
				default:
					$comments_message = sprintf(SOME_COMMENTS, count($comments));
					$showcomments_text = DISPLAY_COMMENTS;
					$comment_form_link = 1;
				}

				echo $comments_message;
				if ($comment_form_link == 1)
				{
					echo '[<a href="'.$this->Href('', '', 'show_comments=1#comments').'">'.$showcomments_text.'</a>]';
				}
				echo "\n".'</div>'."\n";//TODO: move to templating class
			}
		}
	}
}
?>