<?php
 // i18n strings
if (!defined('DIFF_ADDITIONS'))  define('DIFF_ADDITIONS', 'Additions:');
if (!defined('DIFF_DELETIONS'))  define('DIFF_DELETIONS', 'Deletions:');
if (!defined('DIFF_NO_DIFFERENCES'))  define('DIFF_NO_DIFFERENCES', 'No differences.');
if (!defined('REVISION_NUMBER'))  define('REVISION_NUMBER', 'Revision %s');
if (!defined('EDITED_ON'))  define('EDITED_ON', 'Edited on %1$s by %2$s');
if (!defined('ERROR_ACL_READ'))  define('ERROR_ACL_READ', 'You aren\'t allowed to read this page.');
if (!defined('OLDEST_VERSION_EDITED_ON_BY'))  define('OLDEST_VERSION_EDITED_ON_BY', 'The oldest known version of this page was created on %1$s by %2$s');
if (!defined('MOST_RECENT_EDIT'))  define('MOST_RECENT_EDIT', 'Last edited on %1$s by %2$s');
if (!defined('UNREGISTERED_USER')) define('UNREGISTERED_USER', 'unregistered user');
?>
<div class="page">
<?php
if ($this->HasAccess('read'))
	{
	// load revisions for this page
	if ($pages = $this->LoadRevisions($this->tag))
	{
		if ($user = $this->GetUser())
		{
			$max = $user['revisioncount'];
		}
		else
		{
			$max = 20;
		}
		$output = '';
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
					if (($pageA['tag'] != $this->page['tag']) || ($pageB['tag'] != $this->page['tag']))
					{
						$allowed = 0;
					}
					// show if we're still allowed to see the diff
					if ($allowed)
					{
						// prepare bodies
						$bodyA = explode("\n", $pageA["body"]);
						// $bodyB = explode("\n", $pageB["body"]);

						$added = array_diff($bodyA, $bodyB);
						$deleted = array_diff($bodyB, $bodyA);

						if (strlen($pageA['note']) == 0)
						{
							$note = '';
						}
						else
						{
							$note = '['.$this->htmlspecialchars_ent($pageA['note']).']';
						}

						$output .= '<div class="revisioninfo">'."\n";					
						$output .= '<h4 class="clear">'.sprintf(REVISION_NUMBER, '<a href="'.$this->Href('', '', 'time='.urlencode($pageA['time'])).'">['.$pageA['id'].']</a>').'</h4>'."\n";

						if ($c == 2)
						{
							$output .= sprintf(MOST_RECENT_EDIT, '<a class="datetime" href="'.$this->Href('', '', 'time='.urlencode($pageA['time'])).'">'.$pageA['time'].'</a>', $EditedByUser).' <span class="pagenote smaller">'.$note."</span>\n";
						}
						else
						{
							$output .= sprintf(EDITED_ON, '<a class="datetime" href="'.$this->Href('', '', 'time='.urlencode($pageA['time'])).'">'.$pageA['time'].'</a>', $EditedByUser).' <span class="pagenote smaller">'.$note."</span>\n";
						}
						$output .= '</div>'."\n";
						
						if ($added)
						{
							// remove blank lines
							$output .= '<h5 class="clear">'.DIFF_ADDITIONS.'</h5>'."\n";
							$output .= '<div class="wikisource"><ins>'.nl2br(implode("\n", $added)).'</ins></div>'."\n";
						}

						if ($deleted)
						{
							$output .= '<h5 class="clear">'.DIFF_DELETIONS.'</h5>'."\n";
							$output .= '<div class="wikisource"><del>'.nl2br(implode("\n", $deleted)).'</del></div>'."\n";
						}

						if (!$added && !$deleted)
						{
							$output .= "<br />\n".DIFF_NO_DIFFERENCES;
						}
						$output .= "\n".'<br /><hr class="clear" />'."\n";
					}
				}
				else
				{
					// $output .= "<DIV class=\"revisioninfo\">Current page:</div>".$this->Format($pageB["body"])."<br />\n<HR><br />\n";
				}
				$pageA = $this->LoadPageById($page["id"]);
				$EditedByUser  = $page['user'];	
				if (!$this->LoadUser($EditedByUser))
				{
					$EditedByUser .= ' ('.UNREGISTERED_USER.')';				
				}
			}
		}
		if ($page['note'])
		{
			$note='['.$this->htmlspecialchars_ent($page['note']).']';
		}
		else
		{
			$note ='';
		}

		$output .= '<div class="revisioninfo">'."\n";
		$output .= '<h4 class="clear">'.sprintf(REVISION_NUMBER, '<a href="'.$this->Href('', '', 'time='.urlencode($pageB['time'])).'">['.$pageB['id'].']</a>').'</h4>'."\n";
		$output .= sprintf(OLDEST_VERSION_EDITED_ON_BY, '<a class="datetime" href="'.$this->href('', '', 'time='.urlencode($pageB['time'])).'">'.$pageB['time'].'</a>', $EditedByUser).' <span class="pagenote smaller">'.$note."</span>\n";
		$output .= '</div>'."\n";
		echo $output;
	}
}
else
{
	echo '<em class="error">'.ERROR_ACL_READ.'</em>';
}
?>
</div>
