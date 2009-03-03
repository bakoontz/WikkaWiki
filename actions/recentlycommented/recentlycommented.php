<?php
/**
 * Display a list of recently commented pages.
 *
 * @package		Actions
 * @version		$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli} (preliminary code cleanup, ACL check)
 *
 * @uses	Wakka::Format()
 * @uses	Wakka::GetUser()
 * @uses	Wakka::LoadRecentlyCommented()
 * @uses	Wakka::HasAccess()
 * @uses	Wakka::FormatUser()
 *
 * @todo	make datetime format configurable;
 */

/**#@+
 * Default value.
 */
if (!defined('COMMENT_DATE_FORMAT'))    define('COMMENT_DATE_FORMAT', 'D, d M Y');	// @@@ make configurable
if (!defined('COMMENT_TIME_FORMAT'))    define('COMMENT_TIME_FORMAT', 'H:i T');	// @@@ make configurable
if (!defined('COMMENT_SNIPPET_LENGTH')) define('COMMENT_SNIPPET_LENGTH', 120);
/**#@-*/

$readable = 0;
$user = $this->GetUser();
$show_comments = '';
if (isset($user['default_comment_display']))
{
	$show_comments = $user['default_comment_display'];
}
else
{
	$show_comments = $this->GetConfigValue('default_comment_display');
}

$username = '';
if(isset($_GET['user']))
{
    $username = $this->htmlspecialchars_ent($_GET['user']);
}

echo '<h2>'.RECENTLYCOMMENTED_HEADING.'</h2><br />'."\n";
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
			$comment_preview = str_replace('<br />', '', $comment['comment']);	// @@@ use single space instead of empty string
			if (strlen($comment_preview) > COMMENT_SNIPPET_LENGTH)
			{
				$comment_spillover_link = '<a href="'.$this->href('', $page_tag, 'show_comments='.$show_comments).'#comment_'.$comment['id'].'" title="View comment">[&#8230;]</a>'; # i18n
				$comment_preview = substr($comment_preview, 0, COMMENT_SNIPPET_LENGTH).$comment_spillover_link;
			}
			$commentlink = '<a href="'.$this->Href('', $page_tag, 'show_comments='.$show_comments).'#comment_'.$comment['id'].'" title="View comment">'.$page_tag.'</a>'; # i18n
			echo $commentlink.WIKKA_COMMENT_AUTHOR_DIVIDER.$this->FormatUser($comment['user']).'<blockquote>'.$comment_preview.'</blockquote>'."\n";
		}
	}
	if ($readable == 0)
	{
		echo '<em class="error">'.RECENTLYCOMMENTED_NONE_ACCESSIBLE.'</em>';
	}
}
else
{
	if(!empty($username))    
	{        
		echo '<em class="error">'.sprintf(RECENTLYCOMMENTED_NONE_FOUND, " by $username.").'</em>'; 
    } 
	else
	{
		echo '<em class="error">'.RECENTLYCOMMENTED_NONE_FOUND.'</em>';
	}
}
?>
