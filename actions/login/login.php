<?php
/**
 * Alias for logout action.
 *
 * @package Actions
 * @version		$Id$
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 * @since Wikka 1.3
 */
include($this->BuildFullpathFromMultipath('logout'.DIRECTORY_SEPARATOR.'logout.php', $this->GetConfigValue('action_path'))); 
?>