<?php
// i18n strings
if (!defined('BUTTON_RETURN_TO_NODE')) define('BUTTON_RETURN_TO_NODE', 'Return To Node / Cancel');
if (!defined('BUTTON_SHOW_DIFFERENCES')) define('BUTTON_SHOW_DIFFERENCES', 'Show Differences');
if (!defined('ERROR_ACL_READ')) define('ERROR_ACL_READ', 'You aren\'t allowed to read this page.');
if (!defined('SIMPLE_DIFF')) define('SIMPLE_DIFF', 'Simple Diff');
if (!defined('WHEN_BY_WHO')) define('WHEN_BY_WHO', '%1$s by %2$s');
if (!defined('UNREGISTERED_USER')) define('UNREGISTERED_USER', 'unregistered user');
if (!defined('REVISIONS_NO_REVISIONS_YET')) define('REVISIONS_NO_REVISIONS_YET', 'There are no revisions for this page yet');

?>
<div class="page">
<?php
if ($this->HasAccess("read"))
{
	if(isset($_POST['cancel']) && ($_POST['cancel'] == BUTTON_RETURN_TO_NODE))
	{
		$this->Redirect($this->Href());
	}

	$output = '';
	$pages = $this->LoadRevisions($this->tag);
	// load revisions for this page
	if (count($pages)>1)
	{
		$output .= $this->FormOpen("diff", "", "get");
		$output .= "<table border=\"0\" cellspacing=\"0\" cellpadding=\"1\">\n";
		$output .= "<tr>\n";
		$output .= '<td><input type="submit" value="'.BUTTON_SHOW_DIFFERENCES.'" /></td>';
		$output .= '<td><input value="1" type="checkbox" checked="checked" name="fastdiff" id="fastdiff" />'."\n".'<label for="fastdiff">'.SIMPLE_DIFF.'</label></td>';
		$output .= "</tr>\n";
		$output .= "</table>\n";
		$output .= "<table border=\"0\" cellspacing=\"0\" cellpadding=\"1\">\n";
		if ($user = $this->GetUser())
		{
			$max = $user["revisioncount"];
		}
		else
		{
			$max = 20;
		}

		$c = 0;
		foreach ($pages as $page)
		{
			$c++;
			if (($c <= $max) || !$max)
			{
				$page_edited_by = $page['user'];	
				if (!$this->LoadUser($page_edited_by)) $page_edited_by .= ' ('.UNREGISTERED_USER.')';
				if ($page['note']) $note='['.$this->htmlspecialchars_ent($page['note']).']'; else $note ='';
				$output .= "<tr>";
				$output .= "<td><input type=\"radio\" name=\"a\" value=\"".$page["id"]."\" ".($c == 1 ? "checked=\"checked\"" : "")." /></td>";
				$output .= "<td><input type=\"radio\" name=\"b\" value=\"".$page["id"]."\" ".($c == 2 ? "checked=\"checked\"" : "")." /></td>";
				$output .= '<td><a href="'.$this->Href('show','','time='.urlencode($page["time"])).'">['.$page["id"].']</a> '.sprintf(WHEN_BY_WHO, '<a class="datetime" href="'.$this->Href('show','','time='.urlencode($page["time"])).'">'.$page['time'].'</a>', $page_edited_by).' <span class="pagenote smaller">'.$note.'</span></td>';
				$output .= "</tr>\n";
			}
		}
		$output .= "</table><br />\n";
		$output .= $this->FormClose()."\n";
		$output .= $this->FormOpen("revisions", "", "post")."\n";
		$output .= '<input type="submit" value="'.BUTTON_RETURN_TO_NODE.'" name="cancel"/>'."\n";
		$output .= $this->FormClose()."\n";
	}
	else
	{
		$output .= '<em>'.REVISIONS_NO_REVISIONS_YET.'</em>'."\n";
	}
	print($output);
} else {
	print('<em class="error">'.ERROR_ACL_READ.'</em>');
}
?>
</div>
