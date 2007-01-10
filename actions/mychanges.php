<?php
/**
 * Display a list of pages edited by the current user.
 * 
 * If the current user is logged-in and has edited at least one page, a list of pages edited by the current user 
 * is displayed, ordered alphabetically or by date and time (last edit first).
 * 
 * @package Actions
 * @version	$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @author Carlo Zottmann
 * @author	{@link http://wikkawiki.org/NilsLindenberg Nils Lindenberg} (rewrite, i18n)
 * 
 * @uses	Wakka::GetUser()
 * @uses	Wakka::GetUserName()
 * @uses	Wakka::GetPageTag()
 * @uses	Wakka::href()
 * @uses	Wakka::LoadAll()
 * @uses	Wakka::Link()
 */

// order alphabetically or after time?
$alpha = FALSE;
if (isset($_GET["alphabetically"]) && $_GET["alphabetically"] == 1) $alpha = TRUE;

$tag = $this->GetPageTag();
$output = '';

if ($user = $this->GetUser())
{
	$my_edits_count = 0;
	
	// header
	$output .= '<strong>';
	if ($alpha) $output .= ALPHA_PAGES_CHANGE_LIST.' (<a href="'.$this->href("", $tag).'">'.ORDER_DATE;
 	else $output .= TIME_PAGES_CHANGE_LIST.' (<a href="'.$this->href("", $tag, "alphabetically=1").'">'.ORDER_ALPHA;
	$output .= '</a>)</strong><br /><br />'."\n";

	// get the pages
	$query = "SELECT tag, time FROM ".$this->config["table_prefix"]."pages WHERE user = '".mysql_real_escape_string($this->GetUserName())."' AND latest = 'Y' ";
	if ($alpha) $query .= "ORDER BY tag ASC, time DESC";
	else $query .= "ORDER BY time DESC, tag ASC";
	
	if ($pages = $this->LoadAll($query))
	{
		$current = '';
		
		// build the list of pages
		foreach ($pages as $page) 
		{
			// order alphabetically
			if($alpha)
			{
				$firstChar = strtoupper($page["tag"][0]);
				if (!preg_match("/[A-Z,a-z]/", $firstChar)) $firstChar = "#";
		
				if ($firstChar != $current) 
				{
					if ($current) $output .= "<br />\n";
					$output .= '<strong>'.$firstChar."</strong><br />\n";
					$current = $firstChar;
				}
			}
			// order after time
			else
			{
				// day header
				list($day, $time) = explode(" ", $page["time"]);		
				if ($day != $current)
				{
					if ($current) print("<br />\n");
					$output .= '<strong>'.$day.':</strong><br />'."\n";
					$current = $day;
				}
			}
			$output .= "&nbsp;&nbsp;&nbsp;(".$page["time"].") (".$this->Link($page["tag"], "revisions", "history", 0).") ".$this->Link($page["tag"], "", "", 0)."<br />\n";
			$my_edits_count++;
		}
		
		if ($my_edits_count == 0)
		{
			$output .= '<em>'.NO_PAGES_EDITED.'</em>';
		}
	}
	else
	{
		$output .= '<em>'.NO_PAGES_FOUND.'</em>';
	}
}
else
{
	$output .= '<em>'.NOT_LOGGED_IN.'</em>';
}

// *** output section ***
print $output;
?>