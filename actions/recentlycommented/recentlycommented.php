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
if (!defined('COMMENT_SNIPPET_LENGTH')) define('COMMENT_SNIPPET_LENGTH', 120);
if (!defined('DATE_FORMAT')) define('DATE_FORMAT', 'D, d M Y'); #TODO make this system-configurable
if (!defined('TIME_FORMAT')) define('TIME_FORMAT', 'H:i T'); #TODO make this system-configurable
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

$username = $this->GetSafeVar('user', 'get');

echo '<h2>'.T_("Recently commented pages").'</h2><br />'."\n";
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
				$dateformatted = date(DATE_FORMAT, strtotime($day));

				if ($curday)
				{
					echo "<br />\n";
				}
				echo '<strong>'.$dateformatted.':</strong><br />'."\n";
				$curday = $day;
			}
			$timeformatted = date(TIME_FORMAT, strtotime($comment['time']));
			$comment_preview = str_replace('<br />', '', $comment['comment']);	// @@@ use single space instead of empty string
			if (strlen($comment_preview) > COMMENT_SNIPPET_LENGTH)
			{
				$comment_spillover_link = '<a href="'.$this->href('', $page_tag, 'show_comments='.$show_comments).'#comment_'.$comment['id'].'" title="View comment">[&#8230;]</a>'; # i18n
				$comment_preview = substr($comment_preview, 0, COMMENT_SNIPPET_LENGTH).$comment_spillover_link;
			}
			$commentlink = '<a href="'.$this->Href('', $page_tag, 'show_comments='.$show_comments).'#comment_'.$comment['id'].'" title="View comment">'.$page_tag.'</a>'; # i18n
			echo $commentlink.T_(", comment by ").$this->FormatUser($comment['user']).'<blockquote>'.$comment_preview.'</blockquote>'."\n";
		}
	}
	if ($readable == 0)
	{
		echo '<p class="error">'.T_("There are no recently commented pages you have access to.").'</p>';
	}
}
else
{
	if(!empty($username))    
	{        
		echo '<p class="error">'.sprintf(T_("There are no recently by %s commented pages."), $username).'</p>'; 
    } 
	else
	{
		echo '<p class="error">'.T_("There are no recently commented pages.").'</p>';
	}
}
?>
