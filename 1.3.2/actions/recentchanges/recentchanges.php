<?php
/**
 * Display a list of recently changed pages.
 *
 * @package		Actions
 * @name		RecentChanges
 * @version		$Id: recentchanges.php 1132 2008-06-05 10:59:39Z DotMG $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author		{@link http://www.mornography.de/ Hendrik Mans} (wakka code)
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli} (preliminary code cleanup)
 *
 * @uses		Wakka::Format()
 * @uses		Wakka::LoadRecentlyChanged()
 * @uses		Wakka::Href()
 * @uses		Wakka::StaticHref()
 * @uses		Wakka::GetUser()
 * @uses		Wakka::HasAccess()
 * @uses		Wakka::FormatUser()
 * @uses		Wakka::htmlspecialchars_ent()
 *
 * @todo		make datetime format configurable;
 * @todo		add configurable option for non-accessible pages {@link http://wush.net/trac/wikka/ticket/178 #178};
 * @todo		add extensive logging of events such as page deletion, cloning, ACL change {@link http://wush.net/trac/wikka/ticket/143 #143};
 */

/**#@+
 * Default value.
 */
if (!defined('PAGE_EDITOR_DIVIDER'))  define('PAGE_EDITOR_DIVIDER', '&#8594;');
if (!defined('MAX_REVISION_NUMBER'))  define('MAX_REVISION_NUMBER', '50');
if (!defined('DATE_FORMAT')) define('DATE_FORMAT', 'D, d M Y'); #TODO make this system-configurable
if (!defined('TIME_FORMAT')) define('TIME_FORMAT', 'H:i T'); #TODO make this system-configurable

/**#@-*/

//initialization
$max = 0;
$readable = 0;

echo '<h2>'.T_("Recently changed pages").'</h2>'."\n";
if ($pages = $this->LoadRecentlyChanged())
{
	$curday = '';
	//print feed link icon
	$xmlicon_url = $this->StaticHref('images/feed.png');
	echo '<p><a href="'.$this->Href('recentchanges.xml', $this->page['tag']).'"><img class="icon" src="'.$xmlicon_url.'" width="14" height="14" alt="feed icon" /></a></p>'."\n";

	if ($user = $this->GetUser())
	{
		$max = $user['changescount'];
	}
	else
	{
		$max = MAX_REVISION_NUMBER;
	}

	foreach ($pages as $i => $page)
	{
		if (($i < $max) && $this->HasAccess('read', $page['tag']))
		{
			$readable++;
			// print day header
			list($day, $time) = explode(' ', $page['time']);
			if ($day != $curday)
			{
				if ($curday)
				{
					echo '</ul>'."\n";
				}
				$dateformatted = date(DATE_FORMAT, strtotime($day));
				echo '<strong>'.$dateformatted.':</strong><br />'."\n";
				echo '<ul class="recentchanges">'."\n";
				$curday = $day;
			}

			$timeformatted = date(TIME_FORMAT, strtotime($page["time"]));
			/*
			$page_edited_by = $page['user'];
			if (!$this->LoadUser($page_edited_by))
			// @@@	we don't need the whole user record here! We merely need to know
			//		whether $page['user'] is a registered user (see http://wush.net/trac/wikka/ticket/368)
			//		In addition, it's possible a page was edited by a user who is
			//		*no longer* registered but was at the time of editing - how
			//		do we handle that?
			//		#368, #452
			{
				$page_edited_by .= ' '.T_("(.T_("unregistered user").'"); // @@@ or T_("anonymous")
			}
			// @@@ instead of all this ^ just use FormatUser() with $page['user'] as input!! vv
			*/

			// get note
			if ($page['note'])
			{
				$note = ' <span class="pagenote">['.$this->htmlspecialchars_ent($page['note']).']</span>';
			}
			else
			{
				$note = '';
			}

			$page_url = $this->Href('', $page['tag']);
			$revision_number_link = '<a href="'.$this->Href('revisions', $page['tag']).'" title="'.sprintf(T_("View recent revisions list for %s"), $page['tag']).'">['.$page['id'].']</a> ';
			$revision_time_link = '<a class="datetime" href="'.$page_url.'/revisions" title="'.sprintf(T_("View recent revisions list for %s"), $page['tag']).'">'.$timeformatted.'</a>';
			$history_link  = '<a href="'.$page_url.'/history" title="'.sprintf(T_("View edit history of %s"), $page['tag']).'">'.T_("history").'</a>';
			$page_link     = '<a href="'.$page_url.'">'.$page['tag'].'</a>';
			$editor        = $this->FormatUser($page['user']);
			echo '<li>'.$revision_time_link.' '.$revision_number_link.' ['.$history_link.'] - &nbsp;'.$page_link.' '.PAGE_EDITOR_DIVIDER.' '.$editor.' '.$note.'</li>'."\n";
		}
	}
	if ($readable == 0)
	{
		echo '<em class="error">'.T_("There are no recently changed pages you have access to.").'</em>';
	}
	echo '</ul>'."\n";

	//wikiping instructions
	$wikipingserver = $this->GetConfigValue('wikiping_server');
	if (!$wikipingserver == '') 
	{
		$wikipingserver_url_parsed = parse_url($wikipingserver);
		$wikipingserver_host = $wikipingserver_url_parsed['host'];
		$wikiping_link = '<a href="http://'.$wikipingserver_host.'">http://'.$wikipingserver_host.'</a>';
		printf('<p>['.T_("WikiPing enabled: Changes on this wiki are broadcast to %s").']</p>',$wikiping_link);
	}
}
else
{
	echo '<em class="error">'.T_("There are no recently changed pages.").'</em>';
}
?>
