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
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses		Config::$anony_delete_own_comments
 * @uses		Config::$hide_comments
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
 * @uses		Wakka::FormatUser()
 * @uses		Wakka::UserIsOwner()
 * 
 * @todo 	validate all $_GET parameters
 */

//constants
define('SHOW_OLD_REVISION_SOURCE', 0); # if set to 1 shows by default the source of an old revision instead of the rendered version

//validate URL parameters
$raw = (!empty($_GET['raw']))? (int) $_GET['raw'] : SHOW_OLD_REVISION_SOURCE;

echo "\n".'<!--starting page content-->'."\n";
echo '<div class="page"';
echo (($user = $this->GetUser()) && ($user['doubleclickedit'] == 'N') || !$this->HasAccess('write')) ? '' : ' ondblclick="document.location=\''.$this->Href('edit', '', 'id='.$this->page['id']).'\';" '; #268
echo '>'."\n";

if (!$this->HasAccess('read'))
{
	echo '<p><em class="error">'.WIKKA_ERROR_ACL_READ.'</em></p>';
	echo "\n".'</div><!--closing page content-->'."\n";
}
else
{
	if (!$this->page)
	{
		$createlink = '<a href="'.$this->Href('edit').'">'.WIKKA_PAGE_CREATE_LINK_DESC.'</a>';
		echo '<p>'.sprintf(SHOW_ASK_CREATE_PAGE_CAPTION,$createlink).'</p>'."\n";
		echo '</div><!--closing page content-->'."\n";
	}
	else
	{
		if ($this->page['latest'] == 'N')
		{
			$pagelink = '<a href="'.$this->Href().'">'.$this->tag.'</a>';
			echo '<div class="revisioninfo">'."\n";
			echo '<h4 class="clear">'.sprintf(WIKKA_REVISION_NUMBER, '<a href="'.$this->Href('', '', 'time='.urlencode($this->page['time'])).'">['.$this->page['id'].']</a>').'</h4>'."\n";
			echo '<p>';
			echo sprintf(SHOW_OLD_REVISION_CAPTION, $pagelink, $this->FormatUser($this->page['user']), $this->Link($this->tag, 'revisions', $this->page['time'], TRUE, TRUE, '', 'datetime'));
			
			// added if encapsulation: in case where some pages were brutally deleted from database
			if ($latest = $this->LoadPage($this->tag))
			{
?>
				<br />
				<?php echo $this->FormOpen('show', '', 'GET', '', 'left') ?>
				<input type="hidden" name="time" value="<?php echo $_GET['time'] ?>" />
				<input type="hidden" name="raw" value="<?php echo ($raw == 1)? '0' :'1' ?>" />
				<input type="submit" value="<?php echo ($raw == 1)? SHOW_FORMATTED_BUTTON : SHOW_SOURCE_BUTTON ?>" />&nbsp;
				<?php echo $this->FormClose(); ?>
<?php
				// if this is an editable revision, display form
				if ($this->HasAccess('write'))
				{
?>
					<?php echo $this->FormOpen('edit') ?>
					<input type="hidden" name="previous" value="<?php echo $latest['id'] ?>" />
					<input type="hidden" name="body" value="<?php echo $this->htmlspecialchars_ent($this->page['body']) ?>" />
					<input type="submit" value="<?php echo SHOW_RE_EDIT_BUTTON ?>" />
					<?php echo $this->FormClose(); ?>
<?php
				}
			}
			echo '</p><div class="clear"></div></div>';
		}

		// display page
		if ($raw == 1)
		{
			echo '<div class="wikisource">'.nl2br($this->htmlspecialchars_ent($this->page["body"], ENT_QUOTES)).'</div>';
		}
		else
		{
			echo $this->Format($this->page['body'], 'wakka', 'page');
		}
		//clear floats at the end of the main div
		echo "\n".'<div style="clear: both"></div>'."\n";
		echo "\n".'</div><!--closing page content-->'."\n\n";

		if ($this->GetConfigValue('hide_comments') != 1 &&
			$this->HasAccess('comment_read')
		   )
		{
			echo '<!-- starting comments block-->'."\n";
			echo '<div id="comments">'."\n";
			// store comments display in session
			$tag = $this->GetPageTag();
			$wantComments = $this->UserWantsComments($tag);
			if (!isset($_SESSION['show_comments'][$tag]) && $wantComments !== FALSE)
			{
				$_SESSION['show_comments'][$tag] = $wantComments;
			}

			// A GET comment style always overrides the SESSION comment style
			if (isset($_GET['show_comments']))
			{
				switch($_GET['show_comments'])
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
			if (isset($_SESSION['show_comments'][$tag]) && $_SESSION['show_comments'][$tag] != COMMENT_NO_DISPLAY)
			{
				// load comments for this page
				$comments = $this->LoadComments($this->tag, $_SESSION['show_comments'][$tag]);
				$display_mode = $_SESSION['show_comments'][$tag];
				// set up icons
				$sort_desc_icon_url = $this->StaticHref('images/icons/sort_desc.gif');
				$sort_asc_icon_url = $this->StaticHref('images/icons/sort_asc.gif');
				$sort_comment_icon_url = $this->StaticHref('images/icons/comment.gif');


				// display comments header
?>
				<!--starting comments header (show)-->
				<div class="commentsheader">
					<div id="commentsnav">
						<a href="<?php echo $this->Href('', '', 'show_comments='.COMMENT_ORDER_DATE_DESC.'#comments') ?>"><?php echo '<img class="'.($display_mode==COMMENT_ORDER_DATE_DESC ?  "icon_on" : "icon").'" alt="flat" title="Flat (newest first)" src="'.$sort_desc_icon_url.'" />' ?></a>
						<a href="<?php echo $this->Href('', '', 'show_comments='.COMMENT_ORDER_DATE_ASC.'#comments') ?>"><?php echo '<img class="'.($display_mode==COMMENT_ORDER_DATE_ASC ?  "icon_on" : "icon").'" alt="flat" title="Flat (oldest first)" src="'.$sort_asc_icon_url.'" />' ?></a>
						<a href="<?php echo $this->Href('', '', 'show_comments='.COMMENT_ORDER_THREADED.'#comments') ?>"><?php echo '<img class="'.($display_mode==COMMENT_ORDER_THREADED ?  "icon_on" : "icon").'" alt="threaded" title="Threaded" src="'.$sort_comment_icon_url.'" />' ?></a> 
					</div><!-- closing commentsnav div -->
				<?php echo COMMENTS_CAPTION ?>
				[<a href="<?php echo $this->Href('', '', 'show_comments='.COMMENT_NO_DISPLAY) ?>"><?php echo HIDE_COMMENTS_LINK_DESC ?></a>]
<?php
			if ($this->HasAccess('comment_post'))
			{
?>
				<?php echo $this->FormOpen('processcomment') ?>
				<input type="submit" name="submit" value="<?php echo COMMENT_NEW_BUTTON ?>" />
				<?php echo $this->FormClose() ?>
<?php
			}
?>
				</div><!--closing commentsheader (show)-->

<?php
				// display comments themselves
				if ($comments)
				{
					displayComments($this, $comments, $tag);
				}
			}
			else
			{

				echo '<!--starting comments header (hide)-->'."\n";
				echo '<div class="commentsheader">'."\n";
				$commentCount = $this->CountComments($this->tag);
				$showcomments_text = '';
				switch ($commentCount)
				{
					case 0:
						$comments_message = STATUS_NO_COMMENTS.' ';
						if ($this->HasAccess('comment_post'))
						{
							$showcomments_text  = $this->FormOpen("processcomment");
							$showcomments_text .= '<input type="submit" name="submit" value="'.COMMENT_NEW_BUTTON.'" />';
							$showcomments_text .= $this->FormClose();
						}
						break;
					case 1:
						$comments_message = STATUS_ONE_COMMENT.' ';
						$showcomments_text = '[<a href="'.$this->Href('', '', 'show_comments=1#comments').'">'.DISPLAY_COMMENT_LINK_DESC.'</a>]';
						break;
					default:
						$comments_message = sprintf(STATUS_SOME_COMMENTS, $commentCount).' ';
						$comment_ordering = NULL;
						if (isset($user['default_comment_display']))
						{
							$comment_ordering = $user['default_comment_display'];
						}
						elseif (NULL !== $this->GetConfigValue('default_comment_display'))
						{
							$comment_ordering = $this->GetConfigValue('default_comment_display');
						}
						else
						{
							$comment_ordering = COMMENT_ORDER_DATE_ASC;
						}
						$showcomments_text = '[<a href="'.$this->Href('', '', 'show_comments='.$comment_ordering.'#comments').'">'.DISPLAY_COMMENTS_LABEL.'</a>]';
				}

				echo $comments_message;
				echo $showcomments_text;
				echo "\n".'</div><!--closing commentsheader (hide)-->'."\n";
			}
			echo '</div><!--closing comments block-->'."\n\n";
		}
	}
}

/**
 * Display comments for ...
 *
 * @uses	Wakka::reg_username
 * @uses	Wakka::GetUserName()
 * @uses	Wakka::UserIsOwner()
 * @uses	Wakka::FormatUser()
 *
 * @todo	document (including short description!
 */
function displayComments(&$obj, &$comments, $tag)
{
	$current_user = $obj->GetUserName();
	$is_owner = $obj->UserIsOwner();
	$prev_level = NULL;
	$threaded = 0;
	if ($_SESSION['show_comments'][$tag] == COMMENT_ORDER_THREADED)
	{
		$threaded = 1;
	}

	?>
	<div class="commentscontainer">
	<?php
	foreach ($comments as $comment)
	{
		# Handle legacy or non-threaded comments
		if (!isset($comment['level']) || !$threaded)
		{
			$comment['level'] = 0;
		}

		# Keep track of closing <div> tags to effect nesting
		if (isset($prev_level) && ($comment['level'] <= $prev_level))
		{
			for ($i=0; $i<$prev_level-$comment['level']+1; ++$i)
			{
				echo '</div><!--closing comment level '.$i.'-->'."\n";
			}
		}

		# Alternate light/dark comment styles per level
		$comment_class = '';
		if ($comment['level'] % 2 == 1)
		{
			$comment_class = "comment-layout-1";
		}
		else
		{
			$comment_class = "comment-layout-2";
		}

		if ($comment['status'] == 'deleted') {
?>
	<div class="<?php echo $comment_class ?>">
		<div class="commentdeleted"><?php echo COMMENT_DELETED_LABEL ?></div>
<?php
		}
		else
		{
			# Some stats
			//$comment_author = $obj->LoadUser($comment['user'])? $obj->Format($comment['user']) : $comment['user'];
			$comment_author = $obj->FormatUser($comment['user']);
			$comment_ts = sprintf(COMMENT_TIME_CAPTION,$comment['time']);
?>
	<div id="comment_<?php echo $comment['id'] ?>" class="<?php echo $comment_class ?>" >
		<div class="commentheader">
		<div class="commentauthor"><?php echo COMMENT_BY_LABEL.$comment_author ?></div>
		<div class="commentinfo"><?php echo $comment_ts ?></div>
		</div>
		<div class="commentbody">
		<?php echo $comment['comment'] ?>
		</div>
<?php
			if ($obj->HasAccess('comment_post'))
			{
				echo '<div class="commentaction">';
				echo $obj->FormOpen("processcomment");
?>
		<input type="hidden" name="comment_id" value="<?php echo $comment['id'] ?>" />
<?php
?>
		<input type="submit" name="submit" value="<?php echo COMMENT_REPLY_BUTTON ?>" />
<?php
				/*
				$user = $obj->GetUser();
				if (isset($user))
				{
					$name = $user['name'];
				}
				*/
				#if ($is_owner || $name == $comment['user'] || ($obj->config['anony_delete_own_comments'] && $current_user == $comment['user']))
				if ($is_owner || $obj->reg_username == $comment['user'] || ($obj->config['anony_delete_own_comments'] && $current_user == $comment['user']))
				{
?>
		<input type="submit" name="submit" value="<?php echo COMMENT_DELETE_BUTTON ?>" />
<?php
				}
				echo $obj->FormClose();
				echo "</div>";
			}
		}
		$prev_level = $comment['level'];
	}
	for ($i=0; $i<$prev_level+1; ++$i)
	{
		print '</div><!--closing comment level (end)-->'."\n";
	}
	?>
	</div>
	<?php
}
?>
