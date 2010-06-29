<?php
/**
 * Display a list of recent comments.
 *
 * Usage: {{recentcomments}}
 *
 * Optionally, setting the "user=" GET param will display recent
 * comments only for the specified user.
 *
 * @package		Actions
 * @name		RecentComments
 * @version		$Id:recentcomments.php 369 2007-03-01 14:38:59Z DarTar $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli} (preliminary code cleanup)
 * @author		{@link http://wikkawiki.org/NickDamoulakis Nick Damoulakis} (ACL check)
 * @since		Wikka 1.1.6.2
 *
 * @input		none
 * @todo			- make datetime format configurable
 */

$readable = 0;

$username = $this->GetSafeVar('user', 'get');

echo $this->Format(RECENT_COMMENTS_HEADING.' --- ');
if ($comments = $this->LoadRecentComments(50, $username))
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
			$comment_preview = str_replace('<br />', '', $comment['comment']);	// @@@ use single space instead of empty string
			$comment_preview = substr($comment_preview, 0, COMMENT_SNIPPET_LENGTH);
			if (strlen($comment['comment']) > COMMENT_SNIPPET_LENGTH)
			{
				$comment_preview = $comment_preview.'&#8230;';
			}
			// print entry
			echo
			'<span class="datetime">'.sprintf(RECENTCOMMENTS_TIMESTAMP_CAPTION, $timeformatted).'</span> <a href="'.$this->href('', $page_tag, 'show_comments=1').'#comment_'.$comment['id'].'">'.$page_tag.'</a>'.WIKKA_COMMENT_AUTHOR_DIVIDER.$this->FormatUser($comment['user'])."\n<blockquote>".$comment_preview."</blockquote>\n";
		}
	}
	if ($readable == 0)
	{
		echo '<p class="error">'.RECENTCOMMENTS_NONE_ACCESSIBLE.'</p>';
	}
}
else
{
	if(!empty($username))
	{
		echo '<p class="error">'.sprintf(RECENTCOMMENTS_NONE_FOUND_BY, $username).'</p>';
	}
	else
	{
		echo '<p class="error">'.RECENTCOMMENTS_NONE_FOUND.'</p>';
	}
}
?>
