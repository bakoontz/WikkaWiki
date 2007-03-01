<?php
/**
 * Display a list of recent comments.
 *
 * @package		Actions
 * @version		$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli} (preliminary code cleanup)
 * @author		{@link http://wikkawiki.org/NickDamoulakis Nick Damoulakis} (ACL check)
 *
 * @todo			make datetime format configurable
 */

/**
 * defaults
 */
if (!defined('COMMENT_DATE_FORMAT'))    define('COMMENT_DATE_FORMAT', 'D, d M Y');
if (!defined('COMMENT_TIME_FORMAT'))    define('COMMENT_TIME_FORMAT', 'H:i T');
if (!defined('COMMENT_SNIPPET_LENGTH')) define('COMMENT_SNIPPET_LENGTH', 120);

$readable = 0;

echo '<h2>'.RECENTCOMMENTS_HEADING.'</h2><br />'."\n";
if ($comments = $this->LoadRecentComments())
{
	$curday = '';
	foreach ($comments as $comment)
	{
		if ($this->HasAccess('read', $comment['page_tag']))
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
			echo '&nbsp;&nbsp;&nbsp;'.sprintf(RECENTCOMMENTS_TIMESTAMP_CAPTION,$timeformatted).' <a href="'.$this->href('', $comment['page_tag'], 'show_comments=1').'#comment_'.$comment['id'].'">'.$comment['page_tag'].'</a>'.WIKKA_COMMENT_AUTHOR_DIVIDER.$this->Format($comment['user'])."\n<blockquote>".$comment_preview."</blockquote>\n";
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