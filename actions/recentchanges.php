<?php
/**
 * Display a list of recently changed pages.
 *
 * @package		Actions
 * @name			RecentChanges
 *
 * @author    	{@link http://www.mornography.de/ Hendrik Mans} (wakka code)
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli} (preliminary code cleanup)
 * @todo			- make datetime format configurable;
 * 				- add configurable option for non-accessible pages {@link http://wush.net/trac/wikka/ticket/178 #178};
 * 				- added extensive logging of events such as page deletion, cloning, ACL change {@link http://wush.net/trac/wikka/ticket/143 #143};
 */

//defaults
if(!defined('REVISION_DATE_FORMAT')) define('REVISION_DATE_FORMAT', 'D, d M Y');
if(!defined('REVISION_TIME_FORMAT')) define('REVISION_TIME_FORMAT', 'H:i T');
if (!defined('PAGE_EDITOR_DIVIDER')) define ('PAGE_EDITOR_DIVIDER', '&#8594;');
if (!defined('MAX_REVISION_NUMBER')) define ('MAX_REVISION_NUMBER', '50');

//i18n
if (!defined('RECENT_CHANGES_HEADING')) define('RECENT_CHANGES_HEADING', '=====Recently changed pages=====');
if (!defined('UNREGISTERED_USER')) define('UNREGISTERED_USER', 'unregistered user');
if (!defined('LABEL_HISTORY')) define('LABEL_HISTORY', 'history');
if (!defined('TITLE_REVISION_LINK')) define('TITLE_REVISION_LINK', 'View recent revisions list for %s');
if (!defined('TITLE_HISTORY_LINK')) define('TITLE_HISTORY_LINK', 'View edit history of %s');
if (!defined('WIKIPING_ENABLED')) define('WIKIPING_ENABLED', 'WikiPing enabled: Changes on this wiki are broadcast to <a href="http://%1$s">http://%1$s</a>');
if (!defined('NO_RECENTLY_CHANGED_PAGES')) define ('NO_RECENTLY_CHANGED_PAGES', 'There are no recently changed pages.');
if (!defined('NO_READABLE_RECENTLY_CHANGED_PAGES')) define ('NO_READABLE_RECENTLY_CHANGED_PAGES', 'There are no recently changed pages you have access to.');

//initialization
$max = 0;
$readable = 0;

echo $this->Format(RECENT_CHANGES_HEADING.' --- ');
if ($pages = $this->LoadRecentlyChanged())
{
	$curday = '';
	//print feed link icon
	echo '<p><a href="'.$this->href('recentchanges.xml', $this->page['tag']).'"><img src="images/xml.png" width="36" height="14" alt="XML" /></a></p>'."\n";

	if ($user = $this->GetUser()) 
	{
		$max = $user['changescount'];
	} else 
	{
		$max = MAX_REVISION_NUMBER;
	}

	foreach ($pages as $i => $page)
	{
		if (($i < $max) && $this->HasAccess('read', $page['tag']))
		{
			$readable++;
			// day header
			list($day, $time) = explode(' ', $page['time']);
			if ($day != $curday)
			{
				$dateformatted = date(REVISION_DATE_FORMAT, strtotime($day));

				if ($curday)
				{
					echo '</span><br />'."\n";
				}
				echo '<strong>'.$dateformatted.':</strong><br />'."\n".'<span class="recentchanges">'."\n";
				$curday = $day;
			}

			$timeformatted = date(REVISION_TIME_FORMAT, strtotime($page["time"]));
			$page_edited_by = $page['user'];	
			if (!$this->LoadUser($page_edited_by)) $page_edited_by .= ' ('.UNREGISTERED_USER.')';

			// print entry
			if ($page['note'])
			{
				$note = ' <span class="pagenote">['.$this->htmlspecialchars_ent($page['note']).']</span>';
			}
			else
			{
				$note = '';
			}
				echo '&nbsp;&nbsp;&nbsp;&nbsp;('.$this->Link($page['tag'], 'revisions', $timeformatted, 0, 1, sprintf(TITLE_REVISION_LINK, $page['tag'])).') ['.$this->Link($page['tag'], 'history', LABEL_HISTORY, 0, 1, sprintf(TITLE_HISTORY_LINK, $page['tag'])).'] - &nbsp;'.$this->Link($page['tag'], '', '', 0).' '.PAGE_EDITOR_DIVIDER.' '.$page_edited_by.' '.$note.'<br />'."\n";
		}
	}
	if ($readable == 0)
	{
		echo '<em>'.NO_READABLE_RECENTLY_CHANGED_PAGES.'</em>';
	}
	echo '</span>'."\n";

	//wikiping instructions
	$wikipingserver = $this->config['wikiping_server'];
	if (!$wikipingserver == '') 
	{
		$wikipingserver_url_parsed = parse_url($wikipingserver);
		$wikipingserver_host = $wikipingserver_url_parsed['host'];
		printf('<br /><br />['.WIKIPING_ENABLED.']', $wikipingserver_host);
	}
}
else
{
	echo '<em>'.NO_RECENTLY_CHANGED_PAGES.'</em>';
}

?>