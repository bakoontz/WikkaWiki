<?php
/**
 * Display a list of recently changed pages.
 *
 * @package		Actions
 * @name		RecentChanges
 * @version		$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author		{@link http://www.mornography.de/ Hendrik Mans} (wakka code)
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli} (preliminary code cleanup)
 *
 * @uses		Wakka::Format()
 * @uses		Wakka::LoadRecentlyChanged()
 * @uses		Wakka::Href()
 * @uses		Wakka::GetUser()
 * @uses		Wakka::HasAccess()
 * @uses		Wakka::LoadUser()
 * @uses		Wakka::FormatUser()
 * @uses		Wakka::htmlspecialchars_ent()
 *
 * @todo		make datetime format configurable;
 * @todo		add configurable option for non-accessible pages {@link http://wush.net/trac/wikka/ticket/178 #178};
 * @todo		added extensive logging of events such as page deletion, cloning, ACL change {@link http://wush.net/trac/wikka/ticket/143 #143};
 */

/**
 * defaults
 */
if (!defined('REVISION_DATE_FORMAT')) define('REVISION_DATE_FORMAT', 'D, d M Y');	// @@@ make configurable
if (!defined('REVISION_TIME_FORMAT')) define('REVISION_TIME_FORMAT', 'H:i T');		// @@@ make configurable
if (!defined('PAGE_EDITOR_DIVIDER'))  define('PAGE_EDITOR_DIVIDER', '&#8594;');
if (!defined('MAX_REVISION_NUMBER'))  define('MAX_REVISION_NUMBER', '50');

//initialization
$max = 0;
$readable = 0;

echo '<h2>'.RECENTCHANGES_HEADING.'</h2><br />'."\n";
if ($pages = $this->LoadRecentlyChanged())
{
	$curday = '';
	//print feed link icon
	$xmlicon_url = StaticHref('images/xml.png');
	echo '<p><a href="'.$this->Href('recentchanges.xml', $this->page['tag']).'"><img src="'.$xmlicon_url.'" width="36" height="14" alt="XML" /></a></p>'."\n";

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
			if (!$this->LoadUser($page_edited_by)) $page_edited_by .= ' '.WIKKA_ANONYMOUS_AUTHOR_CAPTION; // @@@ or WIKKA_ANONYMOUS_USER

			// print entry
			if ($page['note'])
			{
				$note = ' <span class="pagenote">['.$this->htmlspecialchars_ent($page['note']).']</span>';
			}
			else
			{
				$note = '';
			}
				echo '&nbsp;&nbsp;&nbsp;&nbsp;('.$this->Link($page['tag'], 'revisions', $timeformatted, 0, 1, sprintf(REVISIONS_LINK_TITLE, $page['tag'])).') ['.$this->Link($page['tag'], 'history', WIKKA_HISTORY, 0, 1, sprintf(HISTORY_LINK_TITLE, $page['tag'])).'] - &nbsp;'.$this->Link($page['tag'], '', '', 0).' '.PAGE_EDITOR_DIVIDER.' '.$this->FormatUser($page_edited_by).' '.$note.'<br />'."\n";
		}
	}
	if ($readable == 0)
	{
		echo '<em>'.RECENTCHANGES_NONE_ACCESSIBLE.'</em>';
	}
	echo '</span>'."\n";

	//wikiping instructions
	$wikipingserver = $this->GetConfigValue('wikiping_server');
	if (!$wikipingserver == '') 
	{
		$wikipingserver_url_parsed = parse_url($wikipingserver);
		$wikipingserver_host = $wikipingserver_url_parsed['host'];
		$wikiping_link = '<a href="http://'.$wikipingserver_host.'">http://'.$wikipingserver_host.'</a>';
		printf('<br /><br />['.WIKIPING_ENABLED.']',$wikiping_link);
	}
}
else
{
	echo '<em>'.RECENTCHANGES_NONE_FOUND.'</em>';
}

?>