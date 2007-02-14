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
 * @uses		Config::$anony_delete_own_comments
 * @uses		Config::$hide_comments
 * 
 * @todo		move <div> to template;
 */


// i18n strings
if (!defined('ERROR_NO_ACCESS')) define('ERROR_NO_ACCESS', "You aren't allowed to read this page.");
if (!defined('COMMENTS_HEADER')) define('COMMENTS_HEADER', 'Comments');
if (!defined('HIDE_COMMENTS')) define('HIDE_COMMENTS', 'Hide comments/form');
if (!defined('DISPLAY_COMMENT')) define('DISPLAY_COMMENT', 'Display comment');
if (!defined('DISPLAY_COMMENTS')) define('DISPLAY_COMMENTS', 'Display comments: ');
if (!defined('DISPLAY_COMMENTS_EARLIEST')) define('DISPLAY_COMMENTS_EARLIEST', 'Earliest first');
if (!defined('DISPLAY_COMMENTS_LATEST')) define('DISPLAY_COMMENTS_LATEST', 'Latest first');
if (!defined('DISPLAY_COMMENTS_THREADED')) define('DISPLAY_COMMENTS_THREADED', 'Threaded');
if (!defined('BUTTON_NEW_COMMENT')) define('BUTTON_NEW_COMMENT', 'New Comment');
if (!defined('NO_COMMENTS')) define('NO_COMMENTS', 'There are no comments on this page.');
if (!defined('ONE_COMMENT')) define('ONE_COMMENT', 'There is one comment on this page.');
if (!defined('SOME_COMMENTS')) define('SOME_COMMENTS', 'There are %d comments on this page. ');
if (!defined('BUTTON_RE_EDIT')) define('BUTTON_RE_EDIT', 'Re-edit this old revision');
if (!defined('BUTTON_DELETE_COMMENT')) define('BUTTON_DELETE_COMMENT', 'Delete Comment');
if (!defined('BUTTON_REPLY_COMMENT')) define('BUTTON_REPLY_COMMENT', 'Reply to Comment');
if (!defined('LABEL_ASK_CREATE_PAGE')) define('LABEL_ASK_CREATE_PAGE', 'This page doesn\'t exist yet. Maybe you want to <a href="%s">create</a> it?');
if (!defined('LABEL_OLD_REVISION')) define('LABEL_OLD_REVISION', 'This is an old revision of <a href="%1$s">%2$s</a> from %3$s.');

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
		printf ('<p>'.LABEL_ASK_CREATE_PAGE.'</p></div>', $this->Href('edit'));
	}
	else
	{
		if ($this->page['latest'] == 'N')
		{
			printf ('<div class="revisioninfo">'.LABEL_OLD_REVISION.'</div>', $this->Href(), $this->GetPageTag(), $this->page['time']);
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
 				<input type="submit" value="<?php echo BUTTON_RE_EDIT ?>" />
 				<?php echo $this->FormClose(); ?>
<?php
			}
		}
		echo '</div>'."\n";
		if ($this->GetConfigValue('hide_comments') != 1)
		{
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
				case COMMENT_NO_DISPLAY:
					$_SESSION['show_comments'][$tag] = COMMENT_NO_DISPLAY;
					break;
				case COMMENT_ORDER_DATE_ASC:
					$_SESSION['show_comments'][$tag] = COMMENT_ORDER_DATE_ASC;
					break;
				case COMMENT_ORDER_DATE_DESC: 
					$_SESSION['show_comments'][$tag] = COMMENT_ORDER_DATE_DESC;
					break;
				case COMMENT_ORDER_THREADED:
					$_SESSION['show_comments'][$tag] = COMMENT_ORDER_THREADED;
					break;
				}
			}

			// display comments!
			if ($_SESSION['show_comments'][$tag] != COMMENT_NO_DISPLAY)
			{
				// load comments for this page
				$comments = $this->LoadComments($this->tag, $_SESSION['show_comments'][$tag]);

				// display comments header
?>
				<div class="commentsheader">
				<span id="comments">&nbsp;</span><?php echo COMMENTS_HEADER; ?> [<a href="<?php echo $this->Href('', '', 'show_comments='.COMMENT_NO_DISPLAY) ?>"><?php echo HIDE_COMMENTS; ?></a>]
				[<a href="<?php echo $this->Href('', '', 'show_comments='.COMMENT_ORDER_DATE_ASC.'#comments') ?>"><?php echo DISPLAY_COMMENTS_EARLIEST ?></a>]
				[<a href="<?php echo $this->Href('', '', 'show_comments='.COMMENT_ORDER_DATE_DESC.'#comments') ?>"><?php echo DISPLAY_COMMENTS_LATEST ?></a>]
				[<a href="<?php echo $this->Href('', '', 'show_comments='.COMMENT_ORDER_THREADED.'#comments') ?>"><?php echo DISPLAY_COMMENTS_THREADED ?></a>]

				<?php echo $this->FormOpen("processcomment") ?>
				<input type="submit" name="submit" value="<?php echo BUTTON_NEW_COMMENT ?>" />
				<?php echo $this->FormClose() ?>

				</div>
<?php
				// display comments themselves
				if ($comments) {
					displayComments($this, $comments, $tag);
				}
				echo '</div>'."\n";
			}
			else
			{
				echo '<div class="commentsheader">'."\n";
				$commentCount = $this->CountComments($this->tag);
				switch ($commentCount)
				{
				case 0:
					$comments_message = NO_COMMENTS.' ';
					$showcomments_text = $this->FormOpen("processcomment");
					$showcomments_text .= '<input type="submit" name="submit" value="'.BUTTON_NEW_COMMENT.'">';
					$showcomments_text .= $this->FormClose();
					$comment_form_link  = ($this->HasAccess('comment')) ? 1 : 0;
					break;
				case 1:
					$comments_message = ONE_COMMENT.' ';
					$showcomments_text = '[<a href="'.$this->Href('', '', 'show_comments=1#comments').'">'.DISPLAY_COMMENT.'</a>]';
					$comment_form_link = 1;
					break;
				default:
					$comments_message = sprintf(SOME_COMMENTS, $commentCount);
					$showcomments_text = DISPLAY_COMMENTS;
					$showcomments_text = DISPLAY_COMMENTS.'[<a href="'.$this->Href('', '', 'show_comments='.COMMENT_ORDER_DATE_ASC.'#comments').'">'.DISPLAY_COMMENTS_EARLIEST.'</a>]
					[<a href="'.$this->Href('', '', 'show_comments='.COMMENT_ORDER_DATE_DESC.'#comments').'">'.DISPLAY_COMMENTS_LATEST.'</a>]
					[<a href="'.$this->Href('', '', 'show_comments='.COMMENT_ORDER_THREADED.'#comments').'">'.DISPLAY_COMMENTS_THREADED.'</a>]'; 
					$comment_form_link = 1;
				}

				echo $comments_message;
				if ($comment_form_link == 1)
				{
					echo $showcomments_text;
				}
				echo "\n".'</div>'."\n";//TODO: move to templating class
			}
		}
	}
}

function displayComments(&$obj, &$comments, $tag) {
	$current_user = $obj->GetUserName(); 
	$is_owner = $obj->UserIsOwner();
	$prev_level = null;
	$threaded = 0;
	$flipflop = 0;
	if($_SESSION['show_comments'][$tag] == COMMENT_ORDER_THREADED)
		$threaded = 1;

	foreach ($comments as $comment)
	{
		# Blank out deleted comments (they never really go away at
		# this point; they're just flagged as deleted)
		if($comment['deleted'] == 'Y') {
			$comment['user'] = NULL;
			$comment['comment'] = "Comment deleted";
			$comment['time'] = NULL;
		}

		# Handle legacy or non-threaded comments
		if(!isset($comment['level']) || !$threaded)
			$comment['level'] = 0;

		# Keep track of closing <div> tags to effect nesting
		if(isset($prev_level) && ($comment['level'] <= $prev_level)) {
			for($i=0; $i<$prev_level-$comment['level']+1; ++$i) {
				echo '</div>'."\n";
			}
		}

		# Alternate light/dark comment styles
		$flipflop ? $comment_class = "comment" : $comment_class = "comment2";
		$flipflop ^= 1;

		echo '<div class="'.$comment_class.'">'."\n".
			'<span id="comment_'.$comment['id'].'"></span>'.$comment['comment']."\n".
			"\t".'<div class="commentinfo">'."\n";
			if($comment['deleted'] != 'Y') echo "-- ";
		echo ($obj->LoadUser($comment['user']))? $obj->Format($comment['user']) : $comment['user']; // #84
		if($comment['deleted'] != 'Y')
			echo ' ('.$comment['time'].')'."\n";
		if($obj->HasAccess('comment'))
		{
			echo $obj->FormOpen("processcomment");
?>
<input type="hidden" name="comment_id" value="<?php echo $comment['id'] ?>" />
<?php if($comment['deleted'] != 'Y') { ?>
<input type="submit" name="submit" value="<?php echo BUTTON_REPLY_COMMENT ?>" />
<?php if($is_owner || $user['name'] == $comment['user'] || ($obj->config['anony_delete_own_comments'] && $current_user == $comment['user'])) { ?>
<input type="submit" name="submit" value="<?php echo BUTTON_DELETE_COMMENT ?>" />
<?php }
}
			echo $obj->FormClose();
		}
		echo "\n\t".'</div>'."\n";
		$prev_level = $comment['level'];
	}
	for($i=0; $i<$prev_level+1; ++$i) {
		print "</div>\n";
	}
}

?>
