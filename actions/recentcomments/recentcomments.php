<?php
/**
 * Display a list of recent comments.
 *
 * @package		Actions
 * @name		RecentComments
 * @version		$Id:recentcomments.php 369 2007-03-01 14:38:59Z DarTar $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli} (preliminary code cleanup)
 * @author		{@link http://wikkawiki.org/NickDamoulakis Nick Damoulakis} (ACL check)
 * @since		Wikka 1.0.0
 *
 * @todo		make datetime format configurable
 */

/**
 * defaults
 */
if (!defined('COMMENT_DATE_FORMAT'))    define('COMMENT_DATE_FORMAT', 'D, d M Y');	// @@@ make configurable
if (!defined('COMMENT_TIME_FORMAT'))    define('COMMENT_TIME_FORMAT', 'H:i T');	// @@@ make configurable
if (!defined('COMMENT_SNIPPET_LENGTH')) define('COMMENT_SNIPPET_LENGTH', 120);

$readable = 0;

echo '<h2>'.RECENTCOMMENTS_HEADING.'</h2><br />'."\n";
if ($comments = $this->LoadRecentComments())
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
			sprintf(RECENTCOMMENTS_TIMESTAMP_CAPTION,$timeformatted).' <a href="'.$this->href('', $page_tag, 'show_comments=1').'#comment_'.$comment['id'].'">'.$page_tag.'</a>'.WIKKA_COMMENT_AUTHOR_DIVIDER.$this->FormatUser($comment['user'])."\n<blockquote>".$comment_preview."</blockquote>\n";
		}
	}
	if ($readable == 0)
	{
		echo '<em>'.RECENTCOMMENTS_NONE_ACCESSIBLE.'</em>';
	}
}
else
{
	echo '<em>'.RECENTCOMMENTS_NONE_FOUND.'</em>';
}
?>