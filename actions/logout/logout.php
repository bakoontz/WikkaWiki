<?php
/**
 * Display a login/logout button.
 *
 * @package Actions
 * @version		$Id$
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 * @since Wikka 1.3
 *
 * @todo	styling (buttons should integrate seamlessly with menus)
 * @todo	update menu config files
 * @todo	(optional) auto redirect to current page (sending a flag to UserSettings for seamless redirection?)
 * @todo	create /logout and /login handlers instead of depending on a default page such as UserSettings
 */

if ($this->GetUser())
{
	echo $this->FormOpen('', 'UserSettings', 'post', 'logout', 'logout');
	echo '<input name="logout" type="submit" value="'.T_("Logout").'" />';
	echo $this->FormClose();
}
else
{
	echo $this->FormOpen('', 'UserSettings', 'login', 'login');
	echo '<input name="login" type="submit" value="'.T_("Login").'" />';
	echo $this->FormClose();
}
?>