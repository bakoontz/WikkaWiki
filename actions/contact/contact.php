<?php
/**
 * Print a spam-safe mailto: link to the administrator's email address.
 *
 * @package		Actions
 * @version		$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	Wakka::GetConfigValue()
 */

$email = $this->GetConfigValue("admin_email");

// print spam-safe mailto link
$patterns = array("'@'", "'\.'");
$replace = array("[at]", "[dot]");
echo '<a href="mailto:'.preg_replace($patterns, $replace, $email).'" title="'.SEND_FEEDBACK_LINK_TITLE.'">'.SEND_FEEDBACK_LINK_TEXT.'</a>';

// print plain mailto link
//echo '<a href="mailto:'.$email.'" title="'.SEND_FEEDBACK_LINK_TITLE.'">'.SEND_FEEDBACK_LINK_TEXT.'</a>';

// print contact link only to registered users
// echo ($this->GetUser()) ? <a href="mailto:'.$email.'" title="'.SEND_FEEDBACK_LINK_TITLE.'">'.SEND_FEEDBACK_LINK_TEXT.'</a>' : "";

?>