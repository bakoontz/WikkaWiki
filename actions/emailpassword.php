<?php
/** 
 * Enables a user to get the md5 sum of his password via email.
 * 
 * @author	{@link http://comawiki.martignier.net Costal Martignier} initial action
 * @author	{@link http://www.wikkawiki.org/NilsLindenberg Nils Lindenberg} rewritten
 * @license http://comawiki.martignier.net/LizenzenUndBedingungen
 * @email 	actions@martignier.net
 * 
 * @todo 	- use FormOpen() when it supports form-names
 */ 

// *** constant section ***
if (!defined('TMP_PW_SEND')) define('PW_CHK_SEND', 'The checksum of %s password was sent to his registered email address.'); // %s - username
if (!defined('PW_FORGOTTEN_MAIL')) define('PW_FORGOTTEN_MAIL', "Hello, %1\$ \n\nYou or someone else requested that we send the checksum of your password to login to %2\$s. If you did not request this, disregard this email. -- No action is necessary. -- Your password will stay the same.\n\nYour wikiname: %1\$s \nEncrypted password: %3\$s \n %4\$s \n\nDo not forget to change the password immediately after logging in."); // %1\$ - username; %2\$s - wiki name; %3\$s - md5 sum of pw; %4\$s - base url of the wiki  
if (!defined('PW_FORGOTTEN_MAIL_REF')) define('PW_FORGOTTEN_MAIL_REF', 'Change of password for %s'); // %s - wiki name
if (!defined('PW_FORM_TEXT')) define('PW_FORM_TEXT', 'Enter your WikiName and the checksum of your password will be sent to the registered email address.');
if (!defined('ERROR_UNKNOWN_USER')) define('ERROR_UNKNOWN_USER', 'You have entered a non-existent user!');
if (!defined('ERROR_MAIL_NOT_SEND')) define('ERROR_MAIL_NOT_SEND', 'An error occured while trying to send the password. mail() does not work. Please contact your administrator.');
if (!defined('BUTTON_SEND_PW_LABEL')) define('SEND_PW_BUTTON', 'Send password');

// *** initialization ***
$input = $output = '';
$user = FALSE;
if (isset($_POST["wikiname"])) $input = strtolower($_POST["wikiname"]);
$user = $this->LoadUser($input);

// *** prepare the output ***
// was a username entered and did he exist?
if ($input !== '' && $user){ 
	$header = "From: ".$this->config['wakka_name']." <".$this->config['admin_email'].">";
	$reference = sprintf(PW_FORGOTTEN_MAIL_REF, $this->config['base_url']); 
	$mail = sprintf(PW_FORGOTTEN_MAIL, $user['name'], $this->config['wakka_name'], $user['password'], $this->config['base_url'])."\n";
	
	if (mail($user['email'], $reference, $mail, $header)) $output .= '<br />'.sprintf(PW_CHK_SEND, $user['name']).'<br /><br />';
	else $output .= '<em class="error">'.ERROR_MAIL_NOT_SEND.'</em>';
}

// non-existing user
else if ($input !== '' && !$user) $output .= '<em class="error">'.ERROR_UNKNOWN_USER.'</em>';
 
// input form
if (!$user){
	if ($input == '') $output .= PW_FORM_TEXT."<br /><br />"; 
	$output .= '<form name="getwikiname" action="'.$this->href().'" method="post">';
	$output .= '<input type="text" name="wikiname" value="" />'."<br />";
	$output .= '<input type="submit" value="'.BUTTON_SEND_PW_LABEL.'" />';
	$output .= $this->FormClose();   
}

// *** output section ***
if ($output !== '') print $output;
?>