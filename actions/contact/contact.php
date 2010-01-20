<?php
/**
 * Print a spam-safe mailto: link to the administrator's email address.
 *
 * @package		Actions
 * @version		$Id: contact.php 820 2007-11-23 09:21:08Z DotMG $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	Wakka::GetConfigValue()
 * 
 * Note: plain mailto links are a common source of spam.
 */

$email = $this->GetConfigValue("admin_email");

// print spam-safe mailto link
$patterns = array("'@'", "'\.'");
$replace = array("[at]", "[dot]");
echo '<a href="mailto:'.preg_replace($patterns, $replace, $email).'" title="'.CONTACTLINK_TITLE.'">'.CONTACTLINK_TEXT.'</a>';

// print plain mailto link
//echo '<a href="mailto:'.$email.'" title="'.CONTACTLINK_TITLE.'">'.CONTACTLINK_TEXT.'</a>';

// print contact link only to registered users
// echo ($this->GetUser()) ? <a href="mailto:'.$email.'" title="'.CONTACTLINK_TITLE.'">'.CONTACTLINK_TEXT.'</a>' : "";

?>