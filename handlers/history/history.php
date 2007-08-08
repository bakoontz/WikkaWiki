<?php
/**
 * Display the history of a page.
 *
 * <p>The history of a page shows all the additions and deletions that were done
 * by each revision, the time when the revision was committed and the user who
 * revised the page. Those additions and deletions are presented html-formatted,
 * it is not guaranteed that changes appear emphasized on screen.</p>
 * <p>The number of revisions to show is configured in user preference (Page
 * revisions list limit) or, if viewed by anonymous user, the value of the
 * config entry {@link Config::$default_revisioncount default_revisioncount}.
 * If both are missing, a hard-coded value of 20 is used. The maximum size of
 * the output html can also be configured (config entry
 * {@link Config::$pagesize_max pagesize_max}) to prevent the history to be
 * unaccessible if the page size and revisions are too big. If more revisions
 * than those that can be shown exist, a link is provided to view the rest of
 * the history.</p>
 * <p>If $_GET['start'] is specified, it must be a positive number and the
 * history must start from this value, ie ignoring the $start newest revisions.
 * This is useful to present the history of the page step by step, in case the
 * full history cannot be displayed within a single file.</p>
 *
 * @package		Handlers
 * @subpackage	Page
 * @version		$Id:history.php 407 2007-03-13 05:59:51Z DarTar $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	Config::$pagesize_max
 * @uses	Wakka::GetSafeVar()
 * @uses	Wakka::HasAccess()
 * @uses	Wakka::LoadRevisions()
 * @uses	Wakka::LoadOldestRevision()
 * @uses	Wakka::LoadPageById()
 * @uses	Wakka::Format()
 * @todo	make sure resulting XHTML is valid
 * @todo	validate the $start parameter (must be positive); use a default if it isn't;
 */
 
echo '<div class="page">'."\n";
$start = (int) $this->GetSafeVar('start', 'get');	// @@@ accept only positive value here
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
			$pageB = $this->LoadPageById($page['id']);
			$bodyB = explode("\n", $pageB['body']);

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
					$bodyA = explode("\n", $pageA['body']);

					$added = array_diff($bodyA, $bodyB);
					$deleted = array_diff($bodyB, $bodyA);

					if (strlen($pageA['note']) == 0) $note = ''; else $note = '['.$this->htmlspecialchars_ent($pageA['note']).']';

					if (($c == 2) && (!$start))
					{
						$output .= '<div class="history_revisioninfo">'.sprintf(MOST_RECENT_EDIT, '<a href="'.$this->Href('', '', 'time='.urlencode($pageA['time'])).'">'.$pageA['time'].'</a>', $EditedByUser).' <span class="pagenote smaller">'.$note.'</span></div><br class="clear" />'."\n";
					}
					else 
					{
						$output .= '<div class="history_revisioninfo">'.sprintf(EDITED_ON,        '<a href="'.$this->Href('', '', 'time='.urlencode($pageA['time'])).'">'.$pageA['time'].'</a>', $EditedByUser).' <span class="pagenote smaller">'.$note.'</span></div><br class="clear" />'."\n";
					}

					if ($added)
					{
						// remove blank lines
						$output .= '<br />'."\n".'<strong>'.DIFF_ADDITIONS_HEADER.'</strong><br />'."\n";
						$output .= '<ins>'.$this->Format(implode("\n", $added)).'</ins>';
					}

					if ($deleted)
					{
						$output .= '<br />'."\n".'<strong>'.DIFF_DELETIONS_HEADER.'</strong><br />'."\n";
						$output .= '<del>'.$this->Format(implode("\n", $deleted)).'</del>';
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
			$EditedByUser = $this->FormatUser($page["user"]);
		}
		$oldest_revision = $this->LoadOldestRevision($this->tag);
		if ($oldest_revision['id'] != $pageB['id'])
		{
			$history_more_link = '<a href="'.$this->Href('history', '', 'start='.($c > 1 ? $c+$start-1 : $c+$start)).'">'.HISTORY_MORE_LINK_DESC.'here</a>';
			$additional_output .= "\n".'<br /><div class="history_revisioninfo">'.sprintf(HISTORY_MORE,$history_more_link).'</div><br class="clear" />'."\n";
			$output .= '<div class="history_revisioninfo">'.sprintf(EDITED_ON, '<a href="'.$this->href('', '', 'time='.urlencode($pageB['time'])).'">'.$pageB['time'].'</a>', $this->FormatUser($pageB['user'])).' <span class="pagenote smaller">['.$this->htmlspecialchars_ent($pageB['note']).']</span></div><br class="clear" />'."\n";
		}
		else
		{
			$output .= '<div class="history_revisioninfo">'.sprintf(OLDEST_VERSION_EDITED_ON_BY, '<a href="'.$this->href('', '', 'time='.urlencode($pageB['time'])).'">'.$pageB['time'].'</a>', $this->FormatUser($pageB['user'])).'<span class="pagenote smaller">['.$this->htmlspecialchars_ent($pageB['note']).']</span></div><br class="clear" />'."\n";
		}
		$output .= '<div class="revisioninfo">'.sprintf(HISTORY_PAGE_VIEW, $this->Link($this->tag)).'</div>'.$this->Format(implode("\n", $bodyB));
		echo $output.$additional_output;
	}
}
else
{
	echo '<em class="error">'.WIKKA_ERROR_ACL_READ.'</em>';
}
echo '</div>'."\n";
?>
