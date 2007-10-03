<?php
/**
 * Display a list of pages edited by the current user.
 *
 * If the current user is logged-in and has edited at least one page, a list of pages edited by the current user
 * is displayed, ordered alphabetically or by date and time (last edit first).
 *
 * @package		Actions
 * @version		$Id:mychanges.php 369 2007-03-01 14:38:59Z DarTar $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author	{@link http://web.archive.org/web/20040616194824/http://www.wakkawiki.com/CarloZottmann Carlo Zottmann}
 * @author	{@link http://wikkawiki.org/NilsLindenberg Nils Lindenberg} (rewrite, i18n)
 *
 * @uses	Wakka::existsUser()
 * @uses	Wakka::GetUserName()
 * @uses	Wakka::GetPageTag()
 * @uses	Wakka::href()
 * @uses	Wakka::LoadAll()
 * @uses	Wakka::Link()
 * @todo	fix RE (#104 etc.); also lose the comma in there!
 */

// order alphabetically or by time?
$alpha = FALSE;
if (isset($_GET["alphabetically"]) && $_GET["alphabetically"] == 1) $alpha = TRUE;

$tag = $this->GetPageTag();
$output = '';

#if ($user = $this->GetUser())
if ($this->existsUser())
{
	$my_edits_count = 0;

	// header
	$output .= '<div class="floatl">';
	if ($alpha)
	{
		$output .= MYCHANGES_ALPHA_LIST.' (<a href="'.$this->href("", $tag).'">'.ORDER_DATE_LINK_DESC;
	}
	else
	{
		$output .= MYCHANGES_DATE_LIST.' (<a href="'.$this->href("", $tag, "alphabetically=1").'">'.ORDER_ALPHA_LINK_DESC;
	}
	$output .= '</a>)</div><div class="clear">&nbsp;</div>'."\n";

	// get the pages
	/*
	$query = "SELECT tag, time FROM ".$this->GetConfigValue('table_prefix')."pages WHERE user = '".mysql_real_escape_string($this->GetUserName())."' AND latest = 'Y' ";
	if ($alpha) $query .= "ORDER BY tag ASC, time DESC";
	else $query .= "ORDER BY time DESC, tag ASC";
	*/
	$order = ($alpha) ? "tag ASC, time DESC" : "time DESC, tag ASC";
	$query = "
		SELECT tag, time
		FROM ".$this->GetConfigValue('table_prefix')."pages
		WHERE user = '".mysql_real_escape_string($this->reg_username)."'
			AND latest = 'Y'
		ORDER BY ".$order;

	if ($pages = $this->LoadAll($query))
	{
		$current = '';

		// build the list of pages
		foreach ($pages as $page)
		{
			// order alphabetically
			if ($alpha)
			{
				$firstChar = strtoupper($page["tag"][0]);
				if (!preg_match("/[A-Z,a-z]/", $firstChar)) //TODO: (#104 #340, #34) Internationalization (allow other starting chars, make consistent with Formatter REs)
				{
					$firstChar = "#";
				}

				if ($firstChar != $current)
				{
					if ($current) $output .= "<br />\n";
					$output .= '<strong>'.$firstChar."</strong><br />\n";
					$current = $firstChar;
				}
			}
			// order by time
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
			$output .= "&nbsp;&nbsp;&nbsp;(".$page["time"].") (".$this->Link($page["tag"], 'revisions', WIKKA_HISTORY, 0).") ".$this->Link($page["tag"], "", "", 0)."<br />\n"; # @@@ TODO link text should be WIKKA_REVISIONS, not WIKKA_HISTORY
			$my_edits_count++;
		}

		if ($my_edits_count == 0)
		{
			$output .= '<em>'.STATUS_NO_PAGES_EDITED.'</em>';
		}
	}
	else
	{
		$output .= '<em>'.WIKKA_NO_PAGES_FOUND.'</em>';
	}
}
else
{
	$output .= '<em>'.MYCHANGES_NOT_LOGGED_IN.'</em>';
}

// *** output section ***
print $output;
?>