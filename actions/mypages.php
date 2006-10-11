<?php
/**
 * Display a list of pages owned by the current user.
 * 
 * If the current user is logged-in and owns at least one page, a list of pages owned by the current user
 * is displayed, ordered alphabetically or by date and time (last edit first).
 * 
 * @package Actions
 * @version	$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @author Carlo Zottmann
 * 
 * @uses	Wakka::GetUser()
 * @uses	Wakka::LoadAllPages()
 * @uses	Wakka::GetUserName() 
 * @uses	Wakka::Link()
 */
/**
 * i18n 
 */
if (!defined('OWNED_PAGES_TXT')) define('OWNED_PAGES_TXT', "This is the list of pages you own."); 
if (!defined('NO_OWNED_PAGES')) define('NO_OWNED_PAGES', "You don't own any pages.");
if (!defined('NO_PAGES_FOUND')) define('NO_PAGES_FOUND', "No pages found.");
if (!defined('USER_NOT_LOGGED_IN')) define('USER_NOT_LOGGED_IN', "You're not logged in, thus the list of your pages couldn't be retrieved.");

if ($user = $this->GetUser())
{
	print '<strong>'.OWNED_PAGES_TXT.'</strong><br /><br />'."\n";

	$my_pages_count = 0;

	if ($pages = $this->LoadAllPages())
	{
		foreach ($pages as $page)
		{
			if ($this->GetUserName() == $page["owner"]) 
			{
				$firstChar = strtoupper($page["tag"][0]);
				if (!preg_match("/[A-Z,a-z]/", $firstChar)) 
				{
					$firstChar = "#";
				}
	
				if ($firstChar != $curChar) 
				{
					if ($curChar) print("<br />\n");
					print("<strong>$firstChar</strong><br />\n");
					$curChar = $firstChar;
				}
	
				print($this->Link($page["tag"])."<br />\n");
				
				$my_pages_count++;
			}
		}
		
		if ($my_pages_count == 0)
		{
			print '<em>'.NO_OWNED_PAGES.'</em>';
		}
	}
	else
	{
		print '<em>'.NO_PAGES_FOUND.'</em>';
	}
}
else
{
	print '<em>'.USER_NOT_LOGGED_IN.'</em>';
}
?>