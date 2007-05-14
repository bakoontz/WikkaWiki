<?php
/**
 * Display a form to send feedback to the site administrator, as specified in wikka.config.php
 * 
 * It first validates the form, then sends it using the PHP mail() function. Note that on some
 * servers outcoming mail via the mail() function may be disabled.
 * 
 * @package		Actions
 * @version		$Id:feedback.php 369 2007-03-01 14:38:59Z DarTar $
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @author	{@link http://wikkawiki.org/DarTar Dario Taraborelli} (fixing GUI strings, functionality for registered users)
 * @author	{@link http://wikkawiki.org/NilsLindenberg Nils Lindenberg} (documentation)
 * @author	{@link http://wikkawiki.org/JavaWoman Marjolein Katsma} (l10n, form accessibility)
 * 
 * @uses	Wakka::FormOpen()
 * @uses	Wakka::FormClose()
 * @uses	Wakka::GetConfigValue()
 * 
 * @todo	Use central regex library for validation #34
 * @todo	Add config option to toggle feedback off for unregistered users;
 * @todo	Add preview screen;
 * @todo	Improve layout;
 * @todo	See #486
 */

// set up form  variables
$form_caption	= FEEDBACK_FORM_CAPTION;
$form_open		= $this->FormOpen();
$form_close		= $this->FormClose();
$label_name		= FEEDBACK_NAME_LABEL;
$label_email	= FEEDBACK_EMAIL_LABEL;
$label_message	= FEEDBACK_MESSAGE_LABEL;
$button_send	= FEEDBACK_SEND_BUTTON;

//check if user is logged in
if ($user = $this->GetUser())
{
	$username = '<input id="fb_name" name="name" value="'.$user['name'].'" type="hidden" />'.$user['name']."\n";
	$useremail = '<input id="fb_email" name="email" value="'.$user['email'].'" type="hidden" /><tt>'.$user['email'].'</tt>'."\n";
}
else
{
	$username = '<input id="fb_name" name="name" value="'.$_POST['name'].'" type="text" />'."\n";
	$useremail = '<input id="fb_email" name="email" value="'.$_POST['email'].'" type="text" />'."\n";
}
// construct form template
$template = <<<TPLFEEDBACKFORM
	$form_open
	<fieldset><legend>$form_caption</legend>
	<input type="hidden" name="mail" value="result" />
	<label for="fb_name">$label_name</label> $username<br />
	<label for="fb_email">$label_email</label> $useremail<br />
	<label for="fb_message">$label_message</label><br />
	<textarea id="fb_message" name="comments" rows="15" cols="40">{$_POST['comments']}</textarea><br />
	<input type="submit" value="$button_send" />
	</fieldset>
	$form_close
TPLFEEDBACKFORM;

// action
if ($_POST['mail'] == 'result')
{
	// process input
	$name = $_POST['name'];
	$email = $_POST['email'];
	$comments = $_POST['comments'];
	list($user, $host) = sscanf($email, "%[a-zA-Z0-9._-]@%[a-zA-Z0-9._-]"); //TODO use central regex library
	if (strlen($name) == 0)
	{
		// a non empty name must be entered
		echo '<em class="error">'.ERROR_EMPTY_NAME.'</em>';
		echo $template;    
	}
	else if (strlen($email) == 0 || !strchr($email, "@") || strlen($user) == 0 || strlen($host) == 0)
	{
		// a valid email address must be entered
		echo '<em class="error">'.ERROR_INVALID_EMAIL.'</em>'; 
		echo $template;
	}
	else if (strlen($comments) == 0)
	{
		// some text must be entered
		echo '<em class="error">'.ERROR_EMPTY_COMMENT.'</em>';
		echo $alert;
		echo $template;
	}
	else
	{
		// send email
		$msg  = FEEDBACK_NAME_LABEL."\t".$name."\n";
		$msg .= FEEDBACK_EMAIL_LABEL."\t".$email."\n";
		$msg .= "\n".$comments."\n";
		$recipient = $this->GetConfigValue('admin_email');
		$subject = sprintf(FEEDBACK_SUBJECT,$this->GetConfigValue('wakka_name'));
		$mailheaders  = "From:".$email."\n";
		$mailheaders .= "Reply-To:".$email;
		mail($recipient, $subject, $msg, $mailheaders);

		// display confirmation message
		echo '<em class="success">'.FEEDBACK_SENT.'</em>'."\n";
		// optionally displays the feedback text
		//echo $this->Format("---- **Name:** ".$name."---**Email:** ".$email."---**Comments:** ---".$comments); # i18n (#340)
	}
}
else
{
	// display form
	echo $template;
}
?>