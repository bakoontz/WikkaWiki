<?php
/**
 * Print the name of this WikkaWiki.
 * 
 * @package	Actions
 * @version	$Id$
 * @license	http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses	Wakka::GetConfigValue()
 * @uses	Config::$wakka_name
 */
echo $this->GetConfigValue('wakka_name');
?>