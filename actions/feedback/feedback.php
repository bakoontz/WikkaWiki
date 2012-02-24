<?php
/** 
 * Displays a form to send feedback to the site administrator, as specified in wikka.config.php
 *
 * It first validates the form, then sends it using the mail() function;
 *
 * @package		Actions
 * @version		$Id:feedback.php$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	Wakka::FormClose()
 * @uses	Wakka::FormOpen()
 * @uses	Wakka::GetSafeVar()
 * @uses	Wakka::GetWakkaName()
 * @uses	Wakka::Href()
 * @uses	Config::$admin_email
 * @uses	Config::$root_page
 */


$name = $this->GetSafeVar('name', 'post');
$email = $this->GetSafeVar('email', 'post');
$comments = $this->GetSafeVar('comments', 'post');

$form = T_("<p>Fill in the form below to send us your comments:</p>\n").
	$this->FormOpen().
	'<label for="name">'.T_("Name: ").'</label><input name="name" value="'.$name.'" type="text" /><br />'."\n".
	'<input type="hidden" name="mail" value="result">'."\n".
	'<label for="email">'.T_("Email: ").'</label><input name="email" value="'.$email.'" type="text" /><br />'."\n".
	'<label for="comments">'.T_("Comments:").'</label><br />'."\n".'<textarea name="comments" rows="15" cols="40">'.$comments.'</textarea><br / >'."\n".
	'<input type="submit" value="'.T_("Send").'" />'."\n".
	$this->FormClose();

if ($this->GetSafeVar('mail', 'post')=='result') 
{

	list($user, $host) = sscanf($email, "%[a-zA-Z0-9._-]@%[a-zA-Z0-9._-]");
	if (!$name) 
	{
		// a valid name must be entered
		echo '<p class="error">'.T_("Please enter your name").'</p>'."\n";
		echo $form;
	} elseif (!$email || !strchr($email, '@') || !$user || !$host)
	{
		// a valid email address must be entered
		echo '<p class="error">'.T_("Please enter a valid email address").'</p>'."\n";
		echo $form;
	} elseif (!$comments)
	{
		// some text must be entered
		echo '<p class="error">'.T_("Please enter some text").'</p>'."\n";
		echo $alert;
		echo $form;
	} else 
	{
		// send email and display message
		$msg = 'Name:\t'.$name."\n";
		$msg .= 'Email:\t'.$email."\n";
		$msg .= "\n".$comments."\n";
		$recipient = $this->GetConfigValue('admin_email');
		$subject = sprintf(T_("Feedback from %s"),$this->GetWakkaName());
		$mailheaders = 'From:'.$email."\n";
		$mailheaders .= 'Reply-To:'.$email;
		mail($recipient, $subject, $msg, $mailheaders);
		echo sprintf(T_('Thanks for your interest! Your feedback has been sent to %s. Return to the <a href=\"%s\">main page</a>'),$recipient, $this->Href('', $this->GetConfigValue('root_page')));
		// optionally displays the feedback text
		//echo $this->Format('---- **'.T_("Name: ").'** '.$name.'---**'.T_("Email: ").'** '.$email.'---**'.T_("Comments:").'** ---'.$comments');
	}    
} else 
{
	echo $form;
}
?>
