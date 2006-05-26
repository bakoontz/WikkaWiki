<?php
/** 
 * Send the user a reminder with the md5 checksum of his or her password via email.
 * 
 * @author	{@link http://comawiki.martignier.net Costal Martignier} initial action
 * @author	{@link http://wikkawiki.org/NilsLindenberg Nils Lindenberg} rewritten
 * @author	{@link http://wikkawiki.org/DarTar Dario Taraborelli} further cleanup, error styling and improved logical structure
 * @license http://comawiki.martignier.net/LizenzenUndBedingungen
 * @email 	actions@martignier.net
 * 
 */ 

// *** constant section ***
if (!defined('INPUT_ERROR_STYLE')) define('INPUT_ERROR_STYLE', 'class="highlight"');
if (!defined('PW_FORGOTTEN_HEADING')) define('PW_FORGOTTEN_HEADING', '==== Password reminder ==== ');
if (!defined('PW_CHK_SENT')) define('PW_CHK_SENT', "A password reminder has been sent to %s's registered email address."); // %s - username
if (!defined('PW_FORGOTTEN_MAIL')) define('PW_FORGOTTEN_MAIL', "Hello, %1\$\n\n\nSomeone requested that we send to this email address a password reminder to login at %2\$s. If you did not request this reminder, disregard this email. -- No action is necessary. -- Your password will stay the same.\n\nYour wikiname: %1\$s \nPassword reminder: %3\$s \nURL: %4\$s \n\nDo not forget to change the password immediately after logging in."); // %1\$ - username; %2\$s - wiki name; %3\$s - md5 sum of pw; %4\$s - login url of the wiki  
if (!defined('PW_FORGOTTEN_MAIL_REF')) define('PW_FORGOTTEN_MAIL_REF', 'Password reminder for %s'); // %s - wiki name
if (!defined('PW_FORM_TEXT')) define('PW_FORM_TEXT', 'Enter your WikiName and a password reminder will be sent to your registered email address.');
if (!defined('ERROR_EMPTY_USER')) define('ERROR_EMPTY_USER', 'Please fill in your username!');
if (!defined('ERROR_UNKNOWN_USER')) define('ERROR_UNKNOWN_USER', 'You have entered a non-existent user!');
if (!defined('ERROR_MAIL_NOT_SENT')) define('ERROR_MAIL_NOT_SENT', 'An error occured while trying to send the password. Outgoing mail might be disabled. Please contact your server administrator.');
if (!defined('BUTTON_SEND_PW_LABEL')) define('BUTTON_SEND_PW_LABEL', 'Send reminder');
if (!defined('USERSETTINGS_LINK')) define('USERSETTINGS_LINK', 'Return to the [[UserSettings login]] screen.');


// *** initialization ***
$input = $output = '';
$highlight = '';
$user = FALSE;
$mailsent = FALSE;

//print heading
$output .= $this->Format(PW_FORGOTTEN_HEADING);

if (isset($_POST['wikiname'])) // get posted values
{
	$input = $_POST['wikiname'];
	$user = $this->LoadUser($input);

	switch(TRUE)
	{
		case ($input == ''): // empty user
			$output .= '<em class="error">'.ERROR_EMPTY_USER.'</em><br />'."\n";
			$highlight = INPUT_ERROR_STYLE;
			break;
		case ($input != '' && !$user): // non-existing user
			$output .= '<em class="error">'.ERROR_UNKNOWN_USER.'</em><br />'."\n";
			$highlight = INPUT_ERROR_STYLE;
			break;
		case ($input != '' && $user): // user exists, proceed
			$header = "From: ".$this->config['wakka_name']." <".$this->config['admin_email'].">";
			$reference = sprintf(PW_FORGOTTEN_MAIL_REF, $user['name']);
			$mail = sprintf(PW_FORGOTTEN_MAIL, $user['name'], $this->config['wakka_name'], $user['password'], $this->config['base_url'].'UserSettings')."\n";
			if (mail($user['email'], $reference, $mail, $header))
			{
				$mailsent = TRUE;
				$output .= '<br /><em class="success">'.sprintf(PW_CHK_SENT, $user['name']).'</em><br />'."\n";
				$output .= $this->Format(USERSETTINGS_LINK);
			}
			else 
			{
				$output .= '<em class="error">'.ERROR_MAIL_NOT_SENT.'</em><br />'."\n";
			}
			break;
	}
}

// display input form
if (!$mailsent)
{
	$output .= '<p>'.PW_FORM_TEXT.'</p>'; 
//	$output .= '<form name="getwikiname" action="'.$this->href().'" method="post">';
	$output .= $this->FormOpen();
	$output .= '<input '.$highlight.' type="text" name="wikiname" value="" />';
	$output .= '<input type="submit" value="'.BUTTON_SEND_PW_LABEL.'" />';
	$output .= $this->FormClose();   
}

// *** output section ***
if ($output !== '') echo $output;
?>