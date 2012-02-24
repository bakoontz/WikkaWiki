<?php
/**
 * Synonym for color.php.
 *
 * @see			color.php
 *
 * @package		Actions
 * @version		$Id:colour.php 369 2007-03-01 14:38:59Z DarTar $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 */

/**
 * color.php action.
 */
include($this->BuildFullpathFromMultipath('color'.DIRECTORY_SEPARATOR.'color.php', $this->GetConfigValue('action_path')));
?>
