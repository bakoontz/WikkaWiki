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
 * @uses	Wakka::GetConfigValue()
 * @uses	Wakka::GetUserName()
 * @uses	Wakka::GetPageTag()
 * @uses	Wakka::Href()
 * @uses	Wakka::htmlspecialchars_ent()
 * @uses	Wakka::LoadAll()
 * @uses	Wakka::Link()
 * @uses	Wakka::IsAdmin()
 * @todo	fix RE (#104 etc.); also lose the comma in there!
 */
if (!defined('DATE_FORMAT')) define('DATE_FORMAT', 'D, d M Y'); #TODO make this system-configurable
if (!defined('TIME_FORMAT')) define('TIME_FORMAT', 'H:i T'); #TODO make this system-configurable

// order alphabetically or by time?
$alpha = FALSE;
if ($this->GetSafeVar('alphabetically', 'get') == 1) $alpha = TRUE;

$tag = $this->GetPageTag();
$output = '';
$time_output = '';

$params = ''; 
$username = ''; 
if(isset($_GET['user'])) 
{ 
	$username = $this->GetSafeVar('user', 'get'); 
	$params .= "user=$username&"; 
} 
else
	$username = $this->GetUserName();

$action = ''; 
if(isset($_GET['action'])) 
{ 
	$action = $this->GetSafeVar('action', 'get'); 
	$params .= "action=$action&"; 
} 
$params = substr($params, 0, -1); 
 
if (($this->IsAdmin() && !empty($username)) ||
		($this->existsUser() &&  $username = $this->GetUserName())) 
{
	// header
	$output .= '<div class="floatl">';
	if ($alpha)
	{
	$output .= sprintf(T_("This is a list of pages edited by %s, along with the time of the last change."), $username).' (<a href="'.$this->Href("", $tag, $params).'">'.T_("order by date");
	}
	else
	{
		if(!empty($params)) 
		{ 
			$params .= "&alphabetically=1"; 
		} 
		else 
		{ 
			$params = "alphabetically=1"; 
		} 

		$output .= sprintf(T_("This is a list of pages edited by %s, ordered by the time of the last change."), $username).' (<a href="'.$this->href("", $tag, $params).'">'.T_("order alphabetically"); 
	}
	$output .= '</a>)</div><div class="clear">&nbsp;</div>'."\n";

	$order = ($alpha) ? "tag ASC, time DESC" : "time DESC, tag ASC";
	$query = "
		SELECT id, tag, time
		FROM ".$this->GetConfigValue('table_prefix')."pages
		WHERE user = '".mysql_real_escape_string($username)."'
		AND latest = 'Y'
		ORDER BY ".$order;

	if ($pages = $this->LoadAll($query))
	{
		$current = '';

		// build the list of pages
		foreach ($pages as $page)
		{
			list($day, $time) = explode(" ", $page["time"]);
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
					$output .= '<h5>'.$firstChar."</h5>\n";
					$current = $firstChar;
				}
				$time_output = $page["time"];		
				$output .= "&nbsp;&nbsp;&nbsp;".$this->Link($page["tag"], "", "", 0)." ".$this->Link($page["tag"], 'revisions', "[".$page['id']."]", 0).' <a class="datetime" href="'.$this->Href('revisions', $page['tag']).'" title="'.T_("Click to view recent revisions list for this page").'">'.$time_output."</a><br />\n";
			}
			// order by time
			else
			{
				// day header
				if ($day != $current)
				{
					if ($current) $output .= "<br />\n";
					$current = $day;
					$output .= '<h5>'.date(DATE_FORMAT, strtotime($day)).'</h5>'."\n";
				}
				$time_output = date(TIME_FORMAT, strtotime($time));
				$output .= '&nbsp;&nbsp;&nbsp;<a class="datetime" href="'.$this->Href('revisions', $page['tag']).'" title="'.T_("Click to view recent revisions list for this page").'">'.$time_output.'</a> '.$this->Link($page["tag"], 'revisions', "[".$page['id']."]", 0)." ".$this->Link($page["tag"], "", "", 0)."<br />\n";	
			}
		}
	}
	else
	{
		$output .= '<em class="error">'.T_("No pages found.").'</em>';
	}
}
else
{
	$output .= '<em class="error">'.T_("You're not logged in, thus the list of pages you've edited couldn't be retrieved.").'</em>';
}

// *** output section ***
print $output;
?>
