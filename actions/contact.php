<?php
/**
 * Print a spam-safe mailto: link to the administrator's email address.
 *
 * Note: plain mailto links are a common source of spam.
 */

//constants section
define('SEND_FEEDBACK_LINK_LABEL', 'Send us your feedback');
define('SEND_FEEDBACK_LINK_TITLE', 'Contact');

$email = $this->GetConfigValue("admin_email");

// print spam-safe mailto link
$patterns = array("'@'", "'\.'");
$replace = array("[at]", "[dot]");
echo '<a href="mailto:'.preg_replace($patterns, $replace, $email).'" title="'.SEND_FEEDBACK_LINK_LABEL.'">'.SEND_FEEDBACK_LINK_TITLE.'</a>';

// print plain mailto link
//echo '<a href="mailto:'.$email.'" title="'.SEND_FEEDBACK_LINK_LABEL.'">'.SEND_FEEDBACK_LINK_TITLE.'</a>';

// print contact link only to registered users
// echo ($this->GetUser()) ? '<a href="mailto:'.$email.'" title="'.SEND_FEEDBACK_LINK_LABEL.'">'.SEND_FEEDBACK_LINK_TITLE.'</a>' : "";

?>