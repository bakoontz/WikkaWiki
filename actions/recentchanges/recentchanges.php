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


//initialization
$max = 0;
$readable = 0;

echo $this->Format(RECENT_CHANGES_HEADING.' --- ');
if ($pages = $this->LoadRecentlyChanged())
{
	$curday = '';
	//print feed link icon
	echo '<p><a href="'.$this->href('recentchanges.xml', $this->page['tag']).'"><img src="images/feed.png" class="icon" width="14" height="14" alt="feed icon" /></a></p>'."\n";

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
				echo '&nbsp;&nbsp;&nbsp;&nbsp;<a class="datetime" href="'.$this->Href('revisions', $page['tag']).'" title="'.sprintf(TITLE_REVISION_LINK, $page['tag']).'">'.$timeformatted.'</a> '.$this->Link($page['tag'], 'revisions', '['.$page['id'].']', 0, 1, sprintf(TITLE_REVISION_LINK, $page['tag'])).' ['.$this->Link($page['tag'], 'history', LABEL_HISTORY, 0, 1, sprintf(TITLE_HISTORY_LINK, $page['tag'])).'] - &nbsp;'.$this->Link($page['tag'], '', '', 0).' '.PAGE_EDITOR_DIVIDER.' '.$page_edited_by.' '.$note.'<br />'."\n";
		}
	}
	if ($readable == 0)
	{
		echo '<em class="error">'.NO_READABLE_RECENTLY_CHANGED_PAGES.'</em>';
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
	echo '<em class="error">'.NO_RECENTLY_CHANGED_PAGES.'</em>';
}

?>
