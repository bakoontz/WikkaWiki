<?php
/**
 * Print current Wikka version.
 *
 * By default this only displays for Wikka admins. This option can be
 * changed by setting 'public_sysinfo' to '1' in the Wikka
 * configuration file.
 *
 * Syntax: {{wikkaversion}}
 *
 * @package		Actions
 * @name		Wikka Version	
 * @version		$Id: wikkaversion.php 1105 2008-05-29 21:32:15Z DarTar $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 *
 *
 * @author		{@link http://wikkawiki.org/Jsnx Jason Tourtelotte}
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 * @author		{@link http://wikkawiki.org/JavaWoman JavaWoman}
 * @author		{@link http://wikkawiki.org/BrianKoontz Brian Koontz}
 * @filesource
 */

// defaults
$out = '<abbr title="'.WIKKA_ADMIN_ONLY_TITLE.'">'.WIKKA_NOT_AVAILABLE.'</abbr>';

//check privs
if ($this->config['public_sysinfo'] == '1' || $this->IsAdmin())
{
	$out = $this->VERSION;
}
echo $out;
?>
