<?php
/**
 * Print a spam-safe mailto: link to the administrator's email address.
 *
 * @package		Actions
 * @version		$Id: contact.php 820 2007-11-23 09:21:08Z DotMG $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses		Config::$admin_email
 * 
 * Note: plain mailto links are a common source of spam.
 */

$email = $this->GetConfigValue("admin_email");

// print spam-safe mailto link
$patterns = array("'@'", "'\.'");
$replace = array("[at]", "[dot]");
echo '<a href="mailto:'.preg_replace($patterns, $replace, $email).'" title="'.CONTACTLINK_TITLE.'">'.CONTACTLINK_TEXT.'</a>';

?>
