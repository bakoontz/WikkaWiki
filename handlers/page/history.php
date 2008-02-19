<?php
 // i18n strings
if (!defined('DIFF_ADDITIONS'))  define('DIFF_ADDITIONS', 'Additions:');
if (!defined('DIFF_DELETIONS'))  define('DIFF_DELETIONS', 'Deletions:');
if (!defined('DIFF_NO_DIFFERENCES'))  define('DIFF_NO_DIFFERENCES', 'No differences.');
if (!defined('EDITED_ON'))  define('EDITED_ON', 'Edited on %1$s by %2$s');
if (!defined('ERROR_ACL_READ'))  define('ERROR_ACL_READ', 'You aren\'t allowed to read this page.');
if (!defined('HISTORY_PAGE_VIEW'))  define('HISTORY_PAGE_VIEW', 'Page view:');
if (!defined('OLDEST_VERSION_EDITED_ON_BY'))  define('OLDEST_VERSION_EDITED_ON_BY', 'Oldest known version of this page was edited on %1$s by %2$s');
if (!defined('MOST_RECENT_EDIT'))  define('MOST_RECENT_EDIT', 'Most recent edit on %1$s by %2$s');
if (!defined('UNREGISTERED_USER')) define('UNREGISTERED_USER', 'unregistered user');

?>
<div class="page">
<?php
if ($this->HasAccess("read")) {
	// load revisions for this page
	if ($pages = $this->LoadRevisions($this->tag))
	{
		if ($user = $this->GetUser()) {
			$max = $user["revisioncount"];
		} else {
			$max = 20;
		}
		$output = "";
		$c = 0;
		foreach ($pages as $page)
		{
			$c++;
			if (($c <= $max) || !$max)
			{
				$pageB = $this->LoadPageById($page["id"]);
				$bodyB = explode("\n", $pageB["body"]);

				if (isset($pageA))
				{
					// show by default. We'll do security checks next.
					$allowed = 1;

					// check if the loaded pages are actually revisions of the current page! (fixes #0000046)
					//print_r($pageA); print_r($this->page); exit;
					if (($pageA['tag'] != $this->page['tag']) || ($pageB['tag'] != $this->page['tag'])) {
						$allowed = 0;
					}
					// show if we're still allowed to see the diff
					if ($allowed) {

						// prepare bodies
						$bodyA = explode("\n", $pageA["body"]);
						// $bodyB = explode("\n", $pageB["body"]);

						$added = array_diff($bodyA, $bodyB);
						$deleted = array_diff($bodyB, $bodyA);

      if (strlen($pageA['note']) == 0) $note = ''; else $note = '['.$this->htmlspecialchars_ent($pageA['note']).']';

						if ($c == 2) {
							$output .= '<strong>'.sprintf(MOST_RECENT_EDIT, '<a href="'.$this->Href('', '', 'time='.urlencode($pageA['time'])).'">'.$pageA['time'].'</a>', $EditedByUser).'</strong> <span class="pagenote smaller">'.$note."</span><br />\n";
						}
						else {
							$output .= '<strong>'.sprintf(EDITED_ON,        '<a href="'.$this->Href('', '', 'time='.urlencode($pageA['time'])).'">'.$pageA['time'].'</a>', $EditedByUser).'</strong> <span class="pagenote smaller">'.$note."</span><br />\n";
						}

						if ($added)
						{
							// remove blank lines
							$output .= "<br />\n<strong>".DIFF_ADDITIONS."</strong><br />\n";
							$output .= "<span class=\"additions\">".$this->Format(implode("\n", $added))."</span><br />";
						}

						if ($deleted)
						{
							$output .= "<br />\n<strong>".DIFF_DELETIONS."</strong><br />\n";
							$output .= "<span class=\"deletions\">".$this->Format(implode("\n", $deleted))."</span><br />";
						}

						if (!$added && !$deleted)
						{
							$output .= "<br />\n".DIFF_NO_DIFFERENCES;
						}
						$output .= "<br />\n<hr /><br />\n";
					}
				}
				else {
					// $output .= "<DIV class=\"revisioninfo\">Current page:</div>".$this->Format($pageB["body"])."<br />\n<HR><br />\n";
				}
				$pageA = $this->LoadPageById($page["id"]);
				$EditedByUser  = $page['user'];	
				if (!$this->LoadUser($EditedByUser)) $EditedByUser .= ' ('.UNREGISTERED_USER.')';				
			}
		}
		if ($page['note']) $note='['.$this->htmlspecialchars_ent($page['note']).']'; else $note ='';
		$output .= '<strong>'.sprintf(OLDEST_VERSION_EDITED_ON_BY, '<a href="'.$this->href('', '', 'time='.urlencode($pageB['time'])).'">'.$pageB['time'].'</a>', $EditedByUser).'</strong> <span class="pagenote smaller">'.$note."</span></strong><br />\n";
		$output .= '<div class="revisioninfo">'.HISTORY_PAGE_VIEW.'</div>'.$this->Format(implode("\n", $bodyB));
		print($output);
	}
} else {
	print('<em class="error">'.ERROR_ACL_READ.'</em>');
}
?>
</div>
