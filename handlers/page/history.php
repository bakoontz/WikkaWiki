<?php
/**
 * Display the history of a page.
 * 
 * <p>The history of a page shows all the additions and deletions that were done by each revision,
 * the time when the revision was committed and the user who revised the page.
 * Those additions and deletions are presented html-formatted, it is not guaranteed that changes 
 * appear emphasized on screen.</p>
 * <p>The number of revisions to show is configured in user preference (Page revisions list limit)
 * or, if viewed by anonymous user, the value of the config entry {@link Config::$default_revisioncount default_revisioncount}. 
 * If both are missing, a hard-coded value of 20 is used. The maximum size of the output html can also be
 * configured (config entry {@link Config::$pagesize_max pagesize_max}) to prevent the history to
 * be unaccessible if the page size and revisions are too big. If more revisions than those that 
 * can be shown exist, a link is provided to view the rest of history.</p>
 * <p>If $_GET['param'] is specified, it must be a positive number and the history must start
 * from this value, ie ignoring the $start newest revisions. This is useful to present the
 * history of the page step by step, in case the full history cannot be displayed within a
 * single file.</p>
 * 
 * @package		Handlers
 * @subpackage	Page
 * @version		$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses		Config::$pagesize_max
 * @uses		Wakka::GetSafeVar()
 * @uses		Wakka::HasAccess()
 * @uses		Wakka::LoadRevisions()
 * @uses		Wakka::LoadOldestRevision()
 * @uses		Wakka::LoadPageById()
 * @uses		Wakka::Format()
 * @todo		move <div> to template;
 * @todo  make sure resulting XHTML is valid
 */
/**
 * i18n
 */
 define('DIFF_ADDITIONS', 'Additions:');
 define('DIFF_DELETIONS', 'Deletions:');
 define('DIFF_NO_DIFFERENCES', 'No differences.');
 define('EDITED_ON', 'Edited on %1$s by %2$s');
 define('ERROR_ACL_READ', 'You aren\'t allowed to read this page.');
 define('HISTORY_PAGE_VIEW', 'Page view:');
 define('OLDEST_VERSION_EDITED_ON_BY', 'Oldest known version of this page was edited on %1$s by %2$s');
 define('MOST_RECENT_EDIT', 'Most recent edit on %1$s by %2$s');
 define('HISTORY_MORE', 'Full history for this page cannot be displayed within a single page, click <a href="%1$s">here</a> to view more.');
 
echo '<div class="page">'."\n"; //TODO: move to templating class
$start = intval($this->GetSafeVar('start', 'get'));
if ($start) $start .= ', ';
else $start = '';
if ($this->HasAccess("read")) {
	// load revisions for this page
	if ($pages = $this->LoadRevisions($this->tag, $start))
	{
		$output = "";
		$additional_output = '';
		$c = 0;
		foreach ($pages as $page)
		{
			$c++;
			$pageB = $this->LoadPageById($page["id"]);
			$bodyB = explode("\n", $pageB["body"]);

			if (isset($pageA))
			{
				// show by default. We'll do security checks next.
				$allowed = 1;

				// check if the loaded pages are actually revisions of the current page! (fixes #0000046)
				if (($pageA['tag'] != $this->page['tag']) || ($pageB['tag'] != $this->page['tag'])) 
				{
					$allowed = 0;
				}
				// show if we're still allowed to see the diff
				if ($allowed) 
				{

					// prepare bodies
					$bodyA = explode("\n", $pageA["body"]);

					$added = array_diff($bodyA, $bodyB);
					$deleted = array_diff($bodyB, $bodyA);

					if (strlen($pageA['note']) == 0) $note = ''; else $note = '['.$this->htmlspecialchars_ent($pageA['note']).']';

					if (($c == 2) && (!$start))
					{
						$output .= '<strong>'.sprintf(MOST_RECENT_EDIT, '<a href="'.$this->Href('', '', 'time='.urlencode($pageA['time'])).'">'.$pageA['time'].'</a>', $EditedByUser).'</strong> <span class="pagenote smaller">'.$note."</span><br />\n";
					}
					else 
					{
						$output .= '<strong>'.sprintf(EDITED_ON,        '<a href="'.$this->Href('', '', 'time='.urlencode($pageA['time'])).'">'.$pageA['time'].'</a>', $EditedByUser).'</strong> <span class="pagenote smaller">'.$note."</span><br />\n";
					}

					if ($added)
					{
						// remove blank lines
						$output .= "<br />\n<strong>".DIFF_ADDITIONS."</strong><br />\n";
						$output .= "<ins>".$this->Format(implode("\n", $added))."</ins><br />";
					}

					if ($deleted)
					{
						$output .= "<br />\n<strong>".DIFF_DELETIONS."</strong><br />\n";
						$output .= "<del>".$this->Format(implode("\n", $deleted))."</del><br />";
					}

					if (!$added && !$deleted)
					{
						$output .= "<br />\n".DIFF_NO_DIFFERENCES;
					}
					$output .= "<br />\n<hr /><br />\n";
				}
			}
			if (($pagesize_max = $this->GetConfigValue('pagesize_max')) && (strlen($output) > $pagesize_max))
			{
				break;
			}
			$pageA = $this->LoadPageById($page["id"]);
			$EditedByUser = $this->Format($page["user"]);
		}
		$oldest_revision = $this->LoadOldestRevision($this->tag);
		if ($oldest_revision['id'] != $pageB['id'])
		{
			$additional_output .= "\n".'<br /><strong>'.sprintf(HISTORY_MORE, $this->Href('history', '', 'start='.($c-1))).'</strong>';
			$output .= '<strong>'.sprintf(EDITED_ON, '<a href="'.$this->href('', '', 'time='.urlencode($pageB['time'])).'">'.$pageB['time'].'</a>', $this->Format($pageB['user'])).'</strong> <span class="pagenote smaller">['.$this->htmlspecialchars_ent($pageB['note'])."]</span><br />\n";
		}
		else
		{
			$output .= '<strong>'.sprintf(OLDEST_VERSION_EDITED_ON_BY, '<a href="'.$this->href('', '', 'time='.urlencode($pageB['time'])).'">'.$pageB['time'].'</a>', $this->Format($pageB['user'])).'</strong> <span class="pagenote smaller">['.$this->htmlspecialchars_ent($pageB['note'])."]</span><br />\n";
		}
		$output .= '<div class="revisioninfo">'.HISTORY_PAGE_VIEW.'</div>'.$this->Format(implode("\n", $bodyB));
		print($output.$additional_output);
	}
} else {
	print('<em class="error">'.ERROR_ACL_READ.'</em>');
}
echo '</div>'."\n" //TODO: move to templating class
?>
