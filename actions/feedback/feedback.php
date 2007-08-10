<?php
/**
 * Display a form to send feedback to the site administrator or to any registered user.
 * 
 * The sender is automatically filled in when the user is logged in. The recipient can be specified
 * in several ways. When this action is used in a userpage, feedback is sent to the corresponding user.
 * This behavior can be overridden by specifying a registered username via the optional <tt>to</tt> 
 * parameter. When no recipient is specified, feedback is sent to the admin.
 * The action validates the form and sends the message using the PHP <tt>mail()</tt> function. Note 
 * that on some servers outcoming mail via the <tt>mail()</tt> function may be disabled.
 * 
 * @package		Actions
 * @version		$Id:feedback.php 369 2007-03-01 14:38:59Z DarTar $
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @author	{@link http://wikkawiki.org/DarTar Dario Taraborelli} (fixing GUI strings, extended functionality)
 * @author	{@link http://wikkawiki.org/NilsLindenberg Nils Lindenberg} (documentation)
 * @author	{@link http://wikkawiki.org/JavaWoman Marjolein Katsma} (l10n, form accessibility)
 *
 * @input	string	$to	optional: recipient username;
 *			default: wiki administrator
 *			the default can also be overridden by embedding this action in a userpage
 *			whose owner will then become the recipient
 * @output	Contact form to send feedback to a specified recipient or to the wiki admin.
 * @uses	Wakka::FormOpen()
 * @uses	Wakka::FormClose()
 * @uses	Wakka::GetConfigValue()
 * @uses	Wakka::existsUser()
 * @uses	Wakka::loadUserData()
 * @uses	Wakka::GetUser()
 * 
 * @todo	Use central regex library for validation #34
 * @todo	Update documentation for feedback action
 * @todo	Implement antispam measures to prevent scripted form posting
 * @todo	This seems to be <b>broken</b>!
 *			The overrides for recipient are never used (and @to input isn't validated/sanitized)
 */

/**#@+
 * Default value.
 */
define('ALLOW_FEEDBACK_FROM_UNREGISTERED', TRUE); #move to action configuration file
define('DISPLAY_SENT_MESSAGE', TRUE); #move to action configuration file
define('VALID_EMAIL_RE', "%[a-zA-Z0-9._-]@%[a-zA-Z0-9._-]"); //TODO Move to central regex library #34
/**#@-*/

//only display form when feedback is allowed
#if (ALLOW_FEEDBACK_FROM_UNREGISTERED || $this->GetUser)	// @@@ just check for *registered* user
if (ALLOW_FEEDBACK_FROM_UNREGISTERED || $this->existsUser())	// just check for *registered* user
{
	//get user input
	$name = (isset($_POST['name']))? $_POST['name'] : '';
	$email = (isset($_POST['email']))? $_POST['email'] : '';
	$comments = (isset($_POST['comments']))? $_POST['comments'] : '';

	//set recipient	@@@ broken: never used
	switch(TRUE)
	{
		//recipient specified via action parameter "to"		// @@@ param needs to be validated/sanitized!!
		#case (isset($to) && $user_specified_recipient = $this->LoadUser($to)):
		case (isset($to) && $user_specified_recipient = $this->loadUserData($to)):
			$recipient_name = $to;
			// @@@ retrieve both name and email from actual record retrieved
			break;

		//recipient specified via pagename (by embedding in userpage)
		#case ($userpage_recipient = $this->LoadUser($this->tag)):
		case ($userpage_recipient = $this->loadUserData($this->tag)):
			// @@@ retrieve both name and email from actual record retrieved
			$recipient_name = $this->tag;
			break;

		//default recipient is admin
		default:
			$recipient_name = '';
			// @@@ get admin email from config *here*
			break;
	}
	// form elements
	$form_caption	= sprintf(FEEDBACK_FORM_CAPTION, $recipient_name);	// @@@ use FormatUser() here
	$form_open		= $this->FormOpen();
	$form_close		= $this->FormClose();
	$label_name		= FEEDBACK_NAME_LABEL;
	$label_email	= FEEDBACK_EMAIL_LABEL;
	$label_message	= FEEDBACK_MESSAGE_LABEL;
	$button_send	= FEEDBACK_SEND_BUTTON;

	//check if user is logged in; pre-fill data if so
	if ($user = $this->GetUser())
	{
		$username = '<input id="fb_name" name="name" value="'.$user['name'].'" type="text" readonly="readonly" />'."\n";
		$useremail = '<input id="fb_email" name="email" value="'.$user['email'].'" type="text" readonly="readonly" />'."\n";
	}
	else
	{
		$username = '<input id="fb_name" name="name" value="'.$name.'" type="text" />'."\n";
		$useremail = '<input id="fb_email" name="email" value="'.$email.'" type="text" />'."\n";
	}
	// construct form template
	$template = 
<<<TPLFEEDBACKFORM
	$form_open
	<fieldset class="feedback"><legend>$form_caption</legend>
	<input type="hidden" name="mail" value="result" />
	<label for="fb_name">$label_name</label> $username<br />
	<label for="fb_email">$label_email</label> $useremail<br />
	<label for="fb_message">$label_message</label>
	<textarea id="fb_message" name="comments" rows="15" cols="40">{$comments}</textarea><br />
	<input type="submit" value="$button_send" /><br class="clear" />
	</fieldset>
	$form_close
TPLFEEDBACKFORM;

	// action
	if (isset($_POST['mail']) && $_POST['mail'] == 'result')
	{
		list($user, $host) = sscanf($email, VALID_EMAIL_RE); //TODO use central regex library #34
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
			echo '<em class="error">'.ERROR_EMPTY_MESSAGE.'</em>';
			echo $template;
		}
		else
		{
			// send email
			$msg  = FEEDBACK_NAME_LABEL."\t".$name."\n";
			$msg .= FEEDBACK_EMAIL_LABEL."\t".$email."\n";
			$msg .= "\n".$comments."\n";
			$recipient = $this->GetConfigValue('admin_email');	// @@@ missing overrides
			$subject = sprintf(FEEDBACK_SUBJECT,$this->GetConfigValue('wakka_name'));
			$mailheaders  = "From:".$email."\n";
			$mailheaders .= "Reply-To:".$email;
			mail($recipient, $subject, $msg, $mailheaders);
	
			// display confirmation message
			echo '<em class="success">'.sprintf(FEEDBACK_SENT, $name).'</em>'."\n";
			// optionally displays the feedback text
			if (DISPLAY_SENT_MESSAGE)
			{
				echo $this->Format("---- **".$label_name."** ".$name."---**".$label_email."** ".$email."---**".$label_message."** ---".$comments); # i18n (#340)
			}
		}
	}
	else
	{
		// display form
		echo $template;
	}
}
?>