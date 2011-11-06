<?php
/**
 * Display current GeSHi version.
 *
 * By default this only displays for Wikka admins. This option can be
 * changed by setting 'public_sysinfo' to '1' in the Wikka
 * configuration file.
 * 
 * Syntax: {{geshiversion}}
 *
 * @package		Actions
 * @name		GeSHiVersion
 * @version		$Id: geshiversion.php 1105 2008-05-29 21:32:15Z DarTar $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * 
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 * @author		{@link http://wikkawiki.org/JavaWoman JavaWoman}
 * @author		{@link http://wikkawiki.org/BrianKoontz Brian Koontz}
 * @since		Wikka 1.1.6.2
 * @filesource
 */

// defaults
$out = '<abbr title="'.T_("Sorry, only wiki administrators can display this information").'">'.T_("n/a").'</abbr>'."\n"; 

//check privs
if ($this->config['public_sysinfo'] == '1' || $this->IsAdmin())
{
	if (file_exists($this->GetConfigValue('geshi_path').DIRECTORY_SEPARATOR.'geshi.php'))
	{
		/**
		 * GeSHi core.
		 */
		include_once($this->GetConfigValue('geshi_path').DIRECTORY_SEPARATOR.'geshi.php');
		if (defined('GESHI_VERSION'))
		{
			$out = GESHI_VERSION;
		}
		else
		{
			$out = T_("n/a");
		}
	}
	else
	{
			$out = T_("not installed");
	}
}
echo $out;
?>
