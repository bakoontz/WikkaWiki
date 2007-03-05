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
 * @todo	replace $_REQUEST with either $_GET or $_POST (or both if really
 * 			necessary) - #312  
 */

echo "\n".'<!--starting page content-->'."\n";
echo '<div class="page"';
echo (($user = $this->GetUser()) && ($user['doubleclickedit'] == 'N') || !$this->HasAccess('write')) ? '' : 'ondblclick="document.location=\''.$this->Href('edit').'\';" '; #268
echo '>'."\n"; //TODO: move to templating class

if (!$this->HasAccess('read'))
{
	echo '<p><em class="error">You aren\'t allowed to read this page.</em></p></div>';
	echo "\n".'</div><!--closing page content-->'."\n"; //TODO: move to templating class
}
else
{
	if (!$this->page)
	{
		echo '<p>This page doesn\'t exist yet. Maybe you want to <a href="'.$this->Href('edit').'">create</a> it?</p></div>';
		echo '</div><!--closing page content-->'."\n"; //TODO: move to templating class
	}
	else
	{
		if ($this->page['latest'] == 'N')
		{
			echo '<div class="revisioninfo">This is an old revision of <a href="'.$this->Href().'">'.$this->GetPageTag().'</a> from '.$this->page['time'].'.</div>';
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
				$_SESSION['show_comments'][$tag] = ($this->UserWantsComments() ? '1' : '0');
			#if (isset($_REQUEST['show_comments']))
			if (isset($_GET['show_comments'])) #312
			{
				#switch($_REQUEST['show_comments'])
				switch($_GET['show_comments']) #312
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
			if ($_SESSION['show_comments'][$tag])
			{
				// display comments header
?>
				<div class="commentsheader">
				<span id="comments">&nbsp;</span>Comments [<a href="<?php echo $this->Href('', '', 'show_comments=0') ?>">Hide comments/form</a>]
				</div>
<?php
				// display comments themselves
				if ($comments)
				{
					$current_user = $this->GetUserName(); 
		 			foreach ($comments as $comment)
					{
						echo '<div class="comment">'."\n".
							'<span id="comment_'.$comment['id'].'"></span>'.$comment['comment']."\n".
							"\t".'<div class="commentinfo">'."\n-- ";
						echo ($this->LoadUser($comment['user']))? $this->Format($comment['user']) : $comment['user']; // #84
						echo ' ('.$comment['time'].')'."\n";
						$current_user = $this->GetUserName(); 
     						if ($this->UserIsOwner() || $current_user == $comment['user'] || ($this->config['anony_delete_own_comments'] && $current_user == $comment['user']) )
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
					<label for="commentbox">Add a comment to this page:<br />
					<textarea id="commentbox" name="body" rows="6" cols="78"></textarea><br />
					<input type="submit" value="Add Comment" accesskey="s" />
            			</label>
					<?php echo $this->FormClose(); ?>
				<?php
				}
				echo '</div>'."\n";
			}
			else
			{
			?>
				<div class="commentsheader">
				<?php
				switch (count($comments))
				{
				case 0:
					echo '<p>There are no comments on this page. ';
					$showcomments_text = 'Add comment';
					break;
				case 1:
					echo '<p>There is one comment on this page. ';
					$showcomments_text = 'Display comment';
					break;
				default:
					echo '<p>There are '.count($comments).' comments on this page. ';
					$showcomments_text = 'Display comments';
				}
				?>
				[<a href="<?php echo $this->Href('', '', 'show_comments=1#comments'); ?>"><?php echo $showcomments_text; ?></a>]</p>
				</div>
				<?php
			}
		}
	}
}
?>
