<?php
/**
 * Display a list of pages owned by the current user.
 *
 * If the current user is logged-in and owns at least one page, a list of pages owned by the current user
 * is displayed, ordered alphabetically or by date and time (last edit first).
 *
 * @package		Actions
 * @version		$Id:mypages.php 369 2007-03-01 14:38:59Z DarTar $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author	{@link http://web.archive.org/web/20040616194824/http://www.wakkawiki.com/CarloZottmann Carlo Zottmann}
 *
 * @uses	Wakka::reg_username
 * @uses	Wakka::existsUser()
 * @uses	Wakka::LoadPagesByOwner()
 * @uses	Wakka::GetUserName()
 * @uses	Wakka::IsAdmin()
 * @uses	Wakka::Link()
 * 
 */

$username = '';
if(isset($_GET['user']))
{
	$username = $this->GetSafeVar('user', 'get'); 
}
if (($this->IsAdmin() && !empty($username)) ||
	($this->existsUser() && $username = $this->GetUserName()))
{
	printf('<div class="floatl">'.MYPAGES_HEADER.'</div><br/><br/>'."\n", $username);

	$my_pages_count = 0;

	if ($pages = $this->LoadPagesByOwner($username))
	{
		$curChar = '';
		foreach ($pages as $page)
		{
				$firstChar = strtoupper($page["tag"][0]);
				if (!preg_match("/[A-Z,a-z]/", $firstChar)) 
				{
					$firstChar = "#";
				}

				if ($firstChar != $curChar)
				{
					if ($curChar != '') echo "<br />\n";
					echo '<strong>'.$firstChar."</strong><br />\n";
					$curChar = $firstChar;
				}

				echo $this->Link($page['tag'])."<br />\n";
		}
	}
	else
	{
		echo '<em class="error">'.MYPAGES_NONE_OWNED.'</em>';
	}
}
else
{
	echo '<em class="error">'.MYPAGES_NOT_LOGGED_IN.'</em>';
}
?>
