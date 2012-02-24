<?php
/**
 * Print current MySQL version.
 *
 * By default this only displays for Wikka admins. This option can be
 * changed by setting 'public_sysinfo' to '1' in the Wikka
 * configuration file.
 *
 * Syntax: {{mysqlversion}}
 *
 * @package		Actions
 * @name		MySQL Version	
 * @version		$Id: mysqlversion.php 1105 2008-05-29 21:32:15Z DarTar $
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 *
 * @author		{@link http://wikkawiki.org/Jsnx Jason Tourtelotte}
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 * @author		{@link http://wikkawiki.org/JavaWoman JavaWoman}
 * @author		{@link http://wikkawiki.org/BrianKoontz Brian Koontz}
 * @filesource
 */

// defaults
$out = '<abbr title="'.T_("Sorry, only wiki administrators can display this information").'">'.T_("n/a").'</abbr>'."\n";

//check privs
if ($this->GetConfigValue('public_sysinfo') == '1' || $this->IsAdmin())
{
	$out = mysql_get_server_info();
}
echo $out;
?>
