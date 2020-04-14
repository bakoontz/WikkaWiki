<div id="content">
<?php
/**
 * Show a list of revisions for the page sorted after time.
 *
 * <p>Like {@link history.php the history handler}, this handler shows the history of revisions of a
 * given page : it lists the date of each revision, the user who modified the page and the edit note
 * supplied for that revision, but the output is more compact as it doesn't show additions and
 * deletions nor any content of the page. The date of modification is linked to the version of the
 * page at that revision.</p>
 * <p>The user can also pick two revisions from the list and compare them by passing their id's to the
 * {@link diff.php diff handler}. Two kinds of diffs are proposed: the normal diff and the simple
 * diff. This is done by choosing revisions using the checkboxes and by clicking the button <kbd>
 * Show Differences</kbd>.
 * The maximum number of revisions shown is configured through the user preference or at the
 * wikka configuration. See {@link Wakka::LoadRevisions()}. If the page has more revisions than this
 * configured value, a button <kbd>Next ...</kbd> will appear to allow viewing more revisions. The
 * revision marked at the left column will appear at the top of the next page, this will let the user
 * compare any revision of the current page with another revision on the next (or later) page.</p>
 *
 * @package		Handlers
 * @subpackage	Page
 * @version		$Id:revisions.php 407 2007-03-13 05:59:51Z DarTar $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 *
 * @uses		Wakka::HasAccess()
 * @uses		Wakka::LoadRevisions()
 * @uses		Wakka::FormOpen()
 * @uses		Wakka::FormClose()
 * @uses		Wakka::GetSafeVar()
 * @uses		Wakka::LoadPageById()
 * @uses		Wakka::Link()
 * @uses		Wakka::htmlspecialchars_ent()
 * @uses		Wakka::Href()
 * @uses		Wakka::FormatUser()
 *
 * @todo		make operation (buttons) independent of JavaScript
 */

$start = 0;

if ($this->HasAccess('read'))
{

    if($this->GetSafeVar('cancel', 'post') == T_("Return To Node / Cancel"))
    {
        $this->Redirect($this->Href());
    }

	if (isset($_GET['start']))
	{
		$start = (int) $this->GetSafeVar('start', 'get');
		$a = (int) $this->GetSafeVar('a', 'get');
		if ($a)
		{
			$pageA = $this->LoadPageById($a);
		}
	}
	$pages = $this->LoadRevisions($this->GetPageTag(), $start);
	if (isset($pageA) && is_array($pageA) && is_array($pages))
	{
		array_unshift($pages, $pageA);
	}
	// load revisions for this page - only if there is actually more than one version
	if (count($pages) > 1)
	{
		$output  = $this->FormOpen('diff', '', 'get');
		$output .= "<fieldset>\n";
		$output .= "<legend>".sprintf(T_("Revisions for %s"), $this->Link($this->GetPageTag()))."</legend>"."\n";
		$output .= '<table border="0" cellspacing="0" cellpadding="1">'."\n";
		$output .= "<tr>\n";
		$output .= '<td><input type="submit" value="'.T_("Show Differences").'" /></td>';
		$output .= '<td><input value="1" type="checkbox" checked="checked" name="fastdiff" id="fastdiff" /><label for="fastdiff">'.T_("Simple Diff").'</label></td>';
		$output .= "</tr>\n";
		$output .= "</table>\n";
		$output .= '<table border="0" cellspacing="0" cellpadding="1">'."\n";

		$c = 0;
		foreach ($pages as $page)
		{
			$c++;
			if ($page['note'])
			{
				$note='['.$this->htmlspecialchars_ent($page['note']).']';
			}
			else
			{
				$note ='';
			}
			$output .= '<tr>';
			$output .= '<td><input type="radio" name="a" value="'.$page['id'].'" '.($c == 1 ? 'checked="checked"' : '').' /></td>';
			$output .= '<td><input type="radio" name="b" value="'.$page['id'].'" '.($c == 2 ? 'checked="checked"' : '').' /></td>';
			$output .= '<td><a href="'.$this->Href('show','','time='.urlencode($page["time"])).'">['.$page["id"].']</a> '.sprintf(T_("%s by %s"), '<a class="datetime" href="'.$this->Href('show','','time='.urlencode($page['time'])).'">'.$page['time'].'</a>', $this->FormatUser($page['user'])).((strlen($note)>0)? ' <span class="pagenote smaller">'.$note.'</span>' : '').'</td>';
			$output .= "</tr>\n";
		}
		$output .= "</table>\n";
		$output .= "</fieldset>\n";
		$oldest_revision = $this->LoadOldestRevision($this->GetPageTag());
		if ($oldest_revision['id'] != $page['id'])
		{
			$output .= '<input type="hidden" name="start" value="'.($start + $c).'" />'."\n";
			$output .= '<br />'.sprintf(T_("There are more revisions that were not shown here, click the button labeled %s below to view these entries"), T_("Next..."));
			$output .= "\n".'<br /><input type="submit" name="more_revisions" value="'.T_("Next...").'" onclick="this.form.action=\''.$this->Href('revisions').'\'; return true;" />';	// @@@
		}
		$output .= $this->FormClose()."\n";
        $output .= $this->FormOpen("revisions", "", "post")."\n";
        $output .= '<input type="submit" value="'.T_("Return To Node / Cancel").'" name="cancel"/>'."\n";
        $output .= $this->FormClose()."\n";
	}
	else
	{
		$output  = '<h3>'.sprintf(T_("Revisions for %s"), $this->Link($this->GetPageTag()))."</h3>\n";
		$output .= '<em>'.T_("There are no revisions for this page yet").'</em>'."\n";
	}

	echo $output;
}
else
{
	echo '<em class="error">'.T_("You are not allowed to read this page.").'</em>'."\n";
}
?>
</div>
