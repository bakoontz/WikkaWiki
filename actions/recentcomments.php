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
 *
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli} (preliminary code cleanup)
 * @author		{@link http://wikkawiki.org/NickDamoulakis Nick Damoulakis} (ACL check)
 * @since		Wikka 1.1.6.2
 *
 * @input		none
 * @todo			- make datetime format configurable
 */
//constants
if(!defined('COMMENT_DATE_FORMAT')) define('COMMENT_DATE_FORMAT', 'D, d M Y');
if(!defined('COMMENT_TIME_FORMAT')) define('COMMENT_TIME_FORMAT', 'H:i T');
if(!defined('COMMENT_SNIPPET_LENGTH')) define('COMMENT_SNIPPET_LENGTH', 120);

//i18n
if (!defined('RECENT_COMMENTS_HEADING')) define('RECENT_COMMENTS_HEADING', '=====Recent comments=====');
if (!defined('COMMENT_AUTHOR_DIVIDER')) define ('COMMENT_AUTHOR_DIVIDER', ', comment by ');
if (!defined('NO_RECENT_COMMENTS')) define ('NO_RECENT_COMMENTS', 'There are no recent comments%s');
if (!defined('NO_READABLE_RECENT_COMMENTS')) define ('NO_READABLE_RECENT_COMMENTS', 'There are no recent comments you can read.');
$readable = 0;

$username = '';
if(isset($_GET['user']))
{
	$username = $this->htmlspecialchars_ent($_GET['user']);
}

echo $this->Format(RECENT_COMMENTS_HEADING.' --- ');
if ($comments = $this->LoadRecentComments(50, $username))
{
	$curday = '';
	foreach ($comments as $comment)
	{
		if ($this->HasAccess('comment', $comment['page_tag']))
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
			$comment_preview = substr($comment_preview, 0, COMMENT_SNIPPET_LENGTH);
			if (strlen($comment['comment']) > COMMENT_SNIPPET_LENGTH)
			{
				$comment_preview = $comment_preview.'&#8230;';
			}
			// print entry
			echo '&nbsp;&nbsp;&nbsp; <span class="datetime">'.$timeformatted.'</span> <a href="'.$this->href('', $comment['page_tag'], 'show_comments=1').'#comment_'.$comment['id'].'">'.$comment['page_tag'].'</a>'.COMMENT_AUTHOR_DIVIDER.$this->Format($comment['user'])."\n<blockquote>".$comment_preview."</blockquote>\n";
		}
	}
	if ($readable == 0)
	{
		echo '<em class="error">'.NO_READABLE_RECENT_COMMENTS.'</em>';
	}
}
else
{
	if(!empty($username))
	{
		echo '<em class="error">'.sprintf(NO_RECENT_COMMENTS, " by $username.").'</em>';
	}
	else
	{
		echo '<em class="error">'.sprintf(NO_RECENT_COMMENTS, ".").'</em>';
	}
}
?>
