<?php
/**
 * Display a list of recently commented pages.
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
if (!defined('COMMENT_DATE_FORMAT'))    define('COMMENT_DATE_FORMAT', 'D, d M Y');
if (!defined('COMMENT_TIME_FORMAT'))    define('COMMENT_TIME_FORMAT', 'H:i T');
if (!defined('COMMENT_SNIPPET_LENGTH')) define('COMMENT_SNIPPET_LENGTH', 120);

$readable = 0;
$user = $this->GetUser();
$show_comments = '';
if(isset($user['default_comment_display'])) {
	$show_comments = $user['default_comment_display'];
} else {
	$show_comments = $this->config['default_comment_display'];
}
	
echo '<h2>'.RECENTLYCOMMENTED_HEADING.'</h2><br />'."\n";
if ($comments = $this->LoadRecentlyCommented())
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
			if (strlen($comment_preview) > COMMENT_SNIPPET_LENGTH)
			{
				$comment_spillover_link = '<a href="'.$this->href('', $comment['page_tag'], 'show_comments='.$show_comments).'#comment_'.$comment['id'].'" title="View comment">[&#8230;]</a>'; # i18n
				$comment_preview = substr($comment_preview, 0, COMMENT_SNIPPET_LENGTH).$comment_spillover_link;
			}
			$commentlink = '<a href="'.$this->href('', $comment['page_tag'], 'show_comments='.$show_comments).'#comment_'.$comment['id'].'" title="View comment">'.$comment['page_tag'].'</a>'; # i18n
			$comment_by = $comment['user'];
			if (!$this->LoadUser($comment_by))
			{
				$comment_by .= WIKKA_ANONYMOUS_AUTHOR_CAPTION;
			}
			// print entry
			echo '&nbsp;&nbsp;&nbsp;'.$commentlink.WIKKA_COMMENT_AUTHOR_DIVIDER.$comment_by.'<blockquote>'.$comment_preview.'</blockquote>'."\n";
		}
	}
	if ($readable == 0)
	{
		echo '<em>'.RECENTLYCOMMENTED_NONE_ACCESSIBLE.'</em>';
	}
}
else
{
	echo '<em>'.RECENTLYCOMMENTED_NONE_FOUND.'</em>';
}

?>
