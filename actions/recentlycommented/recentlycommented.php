<?php
/**
 * Display a list of recently commented pages.
 *
 * Usage: {{recentlycommented}}
 *
 * Optionally, setting the "user=" GET param will display recently
 * commented pages only for the specified user.
 *
 * @package		Actions
 * @version		$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli} (preliminary code cleanup, ACL check)
 *
 * @uses		Wakka::Format()
 * @uses		Wakka::LoadRecentlyCommented()
 * @uses		Wakka::HasAccess()
 * @uses		Wakka::LoadUser()
 */
/**
 * defaults
 */
$readable = 0;

$user = $this->GetUser();
$username = '';
if(isset($_GET['user']))
{
	$username = $this->htmlspecialchars_ent($_GET['user']);
}
else
	$username = $this->GetUserName(); 

$show_comments = ''; 
if(isset($user['default_comment_display'])) { 
        $show_comments = $user['default_comment_display']; 
} else { 
        $show_comments = $this->config['default_comment_display']; 
} 

echo $this->Format(RECENTLY_COMMENTED_HEADING.' --- ');
if ($comments = $this->LoadRecentlyCommented(50, $username))
{
	$curday = '';
	foreach ($comments as $comment)
	{
		$page_tag = $comment['page_tag'];
		if ($this->HasAccess('read', $page_tag) &&
	        $this->HasAccess('comment_read', $page_tag))
		{
			$readable++;
			// day header
			list($day, $time) = explode(' ', $comment['time']);
			if ($day != $curday)
			{
				$dateformatted = date(COMMENT_DATE_FORMAT, strtotime($day));
	
				if ($curday)
				{
					echo "<br />\n";
				}
				echo '<strong>'.$dateformatted.':</strong><br />'."\n";
				$curday = $day;
			}
			$timeformatted = date(COMMENT_TIME_FORMAT, strtotime($comment['time']));
			$comment_preview = str_replace('<br />', '', $comment['comment']);
			if (strlen($comment_preview) > COMMENT_SNIPPET_LENGTH)
			{
				$comment_spillover_link = '<a href="'.$this->href('', $page_tag, 'show_comments='.$show_comments).'#comment_'.$comment['id'].'" title="View comment">[&#8230;]</a>'; # i18n
				$comment_preview = substr($comment_preview, 0, COMMENT_SNIPPET_LENGTH).$comment_spillover_link;
			}
			$commentlink = '<a href="'.$this->href('', $page_tag, 'show_comments='.$show_comments).'#comment_'.$comment['id'].'" title="View comment">'.$page_tag.'</a>'; # i18n
			$comment_by = $comment['user'];
			if (!$this->LoadUser($comment_by))
			{
				$comment_by .= ANONYMOUS_COMMENT_AUTHOR;
			}
			// print entry
			echo '&nbsp;&nbsp;&nbsp;'.$commentlink.COMMENT_AUTHOR_DIVIDER.$comment_by.'<blockquote>'.$comment_preview.'</blockquote>'."\n";
		}
	}
	if ($readable == 0)
	{
		echo '<em class="error">'.NO_READABLE_RECENTLY_COMMENTED.'</em>';
	}
}
else
{
	if(!empty($username))
	{
		echo '<em class="error">'.sprintf(NO_RECENTLY_COMMENTED, " by $username.").'</em>';
	}
	else
	{
		echo '<em class="error">'.sprintf(NO_RECENTLY_COMMENTED, ".").'</em>';
	}
}

?>
