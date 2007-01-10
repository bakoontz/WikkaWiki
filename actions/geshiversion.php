<?php
/**
 * Display current GeSHi version.
 *
 * @package		Actions
 * @version		$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 * @since		Wikka 1.1.6.2
 */

if (file_exists($this->config['geshi_path'].'/geshi.php'))
{
	include_once($this->config['geshi_path'].'/geshi.php');
	if (defined('GESHI_VERSION'))
	{
		echo GESHI_VERSION;
	}
	else
	{
		echo NOT_AVAILABLE;
	}
}
else
{
		echo NOT_INSTALLED;
}
?>