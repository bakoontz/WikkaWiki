<?php
/**
 * Print a spam-safe mailto: link to the administrator's email address. 
 * 
 * Note: plain mailto links are a common source of spam.
 * 
 * @package		Actions
 * @name		contact.php
 * @version		$Id$
 * 
 * @uses	Wakka::GetConfigValue()
 */

if(!defined('SEND_FEEDBACK_LINK_TITLE')) define('SEND_FEEDBACK_LINK_TITLE', 'Send us your feedback');
if(!defined('SEND_FEEDBACK_LINK_TEXT')) define('SEND_FEEDBACK_LINK_TEXT', 'Contact');

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