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

echo '<div class="page"';
echo (($user = $this->GetUser()) && ($user['doubleclickedit'] == 'N') || !$this->HasAccess('write')) ? '' : 'ondblclick="document.location=\''.$this->Href('edit').'\';" '; #268
echo '>'."\n"; //TODO: move to templating class

if (!$this->HasAccess('read'))
{
	echo '<p><em class="error">'.WIKKA_ERROR_ACL_READ.'</em></p>';
	echo "\n".'</div>'."\n"; //TODO: move to templating class
}
else
{
	if (!$this->page)
	{
		$createlink = '<a href="'.$this->Href('edit').'">'.WIKKA_PAGE_CREATE_LINK_DESC.'</a>';
		echo '<p>'.sprintf(SHOW_ASK_CREATE_PAGE_CAPTION,$createlink).'</p>'."\n";
		echo '</div>'."\n"; //TODO: move to templating class
	}
	else
	{
		if ($this->page['latest'] == 'N')
		{
			$pagelink = '<a href="'.$this->Href().'">'.$this->tag.'</a>';
			echo '<div class="revisioninfo">'.printf(SHOW_OLD_REVISION_CAPTION,$pagelink,$this->page['time']).'</div>';
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
				<input type="submit" value="<?php echo SHOW_RE_EDIT_BUTTON ?>" />
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
				<div class="commentsheader"><?php // TODO what is this span for?? ?>
				<span id="comments">&nbsp;</span><?php echo COMMENTS_CAPTION ?> [<a href="<?php echo $this->Href('', '', 'show_comments='.COMMENT_NO_DISPLAY) ?>"><?php echo HIDE_COMMENTS_LINK_DESC ?></a>]
				[<a href="<?php echo $this->Href('', '', 'show_comments='.COMMENT_ORDER_DATE_ASC.'#comments') ?>"><?php echo DISPLAY_COMMENTS_EARLIEST_LINK_DESC ?></a>]
				[<a href="<?php echo $this->Href('', '', 'show_comments='.COMMENT_ORDER_DATE_DESC.'#comments') ?>"><?php echo DISPLAY_COMMENTS_LATEST_LINK_DESC ?></a>]
				[<a href="<?php echo $this->Href('', '', 'show_comments='.COMMENT_ORDER_THREADED.'#comments') ?>"><?php echo DISPLAY_COMMENTS_THREADED_LINK_DESC ?></a>]

				<?php echo $this->FormOpen("processcomment") ?>
				<input type="submit" name="submit" value="<?php echo COMMENT_NEW_BUTTON ?>">
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
					$comments_message = STATUS_NO_COMMENTS.' ';
					$showcomments_text  = $this->FormOpen("processcomment");
					$showcomments_text .= '<input type="submit" name="submit" value="'.COMMENT_NEW_BUTTON.'">';
					$showcomments_text .= $this->FormClose();
					$comment_form_link  = ($this->HasAccess('comment')) ? 1 : 0;
					break;
				case 1:
					$comments_message = STATUS_ONE_COMMENT.' ';
					$showcomments_text = '[<a href="'.$this->Href('', '', 'show_comments=1#comments').'">'.DISPLAY_COMMENT_LINK_DESC.'</a>]';
					$comment_form_link = 1;
					break;
				default:
					$comments_message = sprintf(STATUS_SOME_COMMENTS, $commentCount);
					$showcomments_text  = DISPLAY_COMMENTS_LABEL;
					$showcomments_text .= '[<a href="'.$this->Href('', '', 'show_comments='.COMMENT_ORDER_DATE_ASC.'#comments').'">'.DISPLAY_COMMENTS_EARLIEST_LINK_DESC.'</a>]'.
										  '[<a href="'.$this->Href('', '', 'show_comments='.COMMENT_ORDER_DATE_DESC.'#comments').'">'.DISPLAY_COMMENTS_LATEST_LINK_DESC.'</a>]'.
										  '[<a href="'.$this->Href('', '', 'show_comments='.COMMENT_ORDER_THREADED.'#comments').'">'.DISPLAY_COMMENTS_THREADED_LINK_DESC.'</a>]'; 
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

function displayComments(&$obj, &$comments, $tag)
{
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
		if($comment['deleted'] != 'Y')
		{
			echo "-- ";	
		}
		echo ($obj->LoadUser($comment['user']))? $obj->Format($comment['user']) : $comment['user']; // #84
		if($comment['deleted'] != 'Y')
		{
			echo ' '.sprintf(COMMENT_TIME_CAPTION,$comment['time'])."\n";
		}
		if($obj->HasAccess('comment'))
		{
			echo $obj->FormOpen("processcomment");
?>
	<input type="hidden" name="comment_id" value="<?php echo $comment['id'] ?>" />
<?php
			if($comment['deleted'] != 'Y')
			{
?>
	<input type="submit" name="submit" value="<?php echo COMMENT_REPLY_BUTTON ?>" />
<?php
				if($is_owner || $user['name'] == $comment['user'] || ($obj->config['anony_delete_own_comments'] && $current_user == $comment['user']))
				# FIXME 'The local variable $user may not have been initialized'
				{
?>
	<input type="submit" name="submit" value="<?php echo COMMENT_DELETE_BUTTON ?>" />
<?php
				}
			}
			echo $obj->FormClose();
		}
		echo "\n\t".'</div>'."\n";
		$prev_level = $comment['level'];
	}
	for($i=0; $i<$prev_level+1; ++$i)
	{
		print "</div>\n";
	}
}
?>
