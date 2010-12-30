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
 * @uses 	Wakka::FormatUser();
 * @todo	make sure resulting XHTML is valid
 * @todo	validate the $start parameter (must be positive); use a default if it isn't;
 * @todo	use pager for histories
 */
 
echo '<div id="content">'."\n";
$start = (int) $this->GetSafeVar('start', 'get');	// @@@ accept only positive value here

if ($this->HasAccess("read")) {
	// load revisions for this page
	if ($pages = $this->LoadRevisions($this->tag, $start))
	{
		$note = '';
		$output = "";
		$additional_output = '';
		$c = 0;
		foreach ($pages as $page)
		{
			$c++;
			$pageB = $this->LoadPageById($page['id']);
			$bodyB = explode("\n", $this->htmlspecialchars_ent($pageB['body']));

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
					$bodyA = explode("\n", $this->htmlspecialchars_ent($pageA['body']));

					$added = array_diff($bodyA, $bodyB);
					$deleted = array_diff($bodyB, $bodyA);

					if (strlen($pageA['note']) > 0)
					{
						$note = '['.$this->htmlspecialchars_ent($pageA['note']).']';
					}

					$output .= '<div class="revisioninfo">'."\n";					
					$output .= '<h4 class="clear">'.sprintf(T_("Revision %s"), '<a href="'.$this->Href('', '', 'time='.urlencode($pageA['time'])).'">['.$pageA['id'].']</a>').'</h4>'."\n";
					
					if ($c == 2)
					{
						$output .= sprintf(T_("Last edited on %s by %s"), '<a class="datetime" href="'.$this->Href('', '', 'time='.urlencode($pageA['time'])).'">'.$pageA['time'].'</a>', $EditedByUser).' <span class="pagenote smaller">'.$note."</span>\n";
					}
					else
					{
						$output .= sprintf(T_("Edited on %s by %s"), '<a class="datetime" href="'.$this->Href('', '', 'time='.urlencode($pageA['time'])).'">'.$pageA['time'].'</a>', $EditedByUser).' <span class="pagenote smaller">'.$note."</span>\n";
					}
					$output .= '</div>'."\n";
					
					if ($added)
					{
						// remove blank lines
						$output .= "\n".'<h5 class="clear">'.T_("Additions:").'</h5>'."\n";
						//$output .= '<ins>'.$this->Format(implode("\n", $added)).'</ins>';
						$output .= '<div class="wikisource"><ins>'.nl2br(implode("\n", $added)).'</ins></div>';
					}

					if ($deleted)
					{
						$output .= "\n".'<h5 class="clear">'.T_("Deletions:").'</h5>'."\n";
						//$output .= '<del>'.$this->Format(implode("\n", $deleted)).'</del>';
						$output .= '<div class="wikisource"><del>'.nl2br(implode("\n", $deleted)).'</del></div>';
					}

					if (!$added && !$deleted)
					{
						$output .= "<br />\n".T_("No Differences");
					}
					$output .= '<br /><hr class="clear" />'."\n";
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
		if (strlen($pageB['note']) == 0)
		{
			$note_oldest = '';
		}
		else
		{
			$note_oldest = ' <span class="pagenote smaller">['.$this->htmlspecialchars_ent($pageB['note']).']</span>';
		}		
		if ($oldest_revision['id'] != $pageB['id'])
		{
			$history_more_link = '<a href="'.$this->Href('history', '', 'start='.($c > 1 ? $c+$start-1 : $c+$start)).'">'.T_("here").'</a>';
			$additional_output .= "\n".'<br /><div class="history_revisioninfo">'.sprintf(T_("Full history for this page cannot be displayed within a single page, click %s to view more."),$history_more_link).'</div><br class="clear" />'."\n";
		}
		else
		{
			$output .= '<div class="revisioninfo">'."\n";
			$output .= '<h4 class="clear">'.sprintf(T_("Revision %s"), '<a href="'.$this->Href('', '', 'time='.urlencode($pageB['time'])).'">['.$pageB['id'].']</a>').'</h4>'."\n";
			$output .= sprintf(T_("The oldest known version of this page was created on %s by %s"), '<a class="datetime" href="'.$this->href('', '', 'time='.urlencode($pageB['time'])).'">'.$pageB['time'].'</a>', $EditedByUser).' <span class="pagenote smaller">'.$note."</span>\n";
			$output .= '</div>'."\n";		
		}
		echo $output.$additional_output;
	}
}
else
{
	echo '<em class="error">'.T_("You are not allowed to read this page.").'</em>';
}
echo '</div>'."\n";
?>
