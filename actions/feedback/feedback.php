<?php

// Displays a form to send feedback to the site administrator, as specified in wikka.config.php
// It first validates the form, then sends it using the mail() function;

$name = $this->GetSafeVar('name', 'post');
$email = $this->GetSafeVar('email', 'post');
$comments = $this->GetSafeVar('comments', 'post');

$form = FILL_FORM.
	$this->FormOpen().
	'<label for="name">'.FEEDBACK_NAME_LABEL.'</label><input name="name" value="'.$name.'" type="text" /><br />'."\n".
	'<input type="hidden" name="mail" value="result">'."\n".
	'<label for="email">'.FEEDBACK_EMAIL_LABEL.'</label><input name="email" value="'.$email.'" type="text" /><br />'."\n".
	'<label for="comments">'.FEEDBACK_COMMENTS_LABEL.'</label><br />'."\n".'<textarea name="comments" rows="15" cols="40">'.$comments.'</textarea><br / >'."\n".
	'<input type="submit" value="'.FEEDBACK_SEND_BUTTON.'" />'."\n".
	$this->FormClose();

if ($this->GetSafeVar('mail', 'post')=='result') 
{

	list($user, $host) = sscanf($email, "%[a-zA-Z0-9._-]@%[a-zA-Z0-9._-]");
	if (!$name) 
	{
		// a valid name must be entered
		echo '<p class="error">'.ERROR_EMPTY_NAME.'</p>'."\n";
		echo $form;
	} elseif (!$email || !strchr($email, '@') || !$user || !$host)
	{
		// a valid email address must be entered
		echo '<p class="error">'.ERROR_INVALID_EMAIL.'</p>'."\n";
		echo $form;
	} elseif (!$comments)
	{
		// some text must be entered
		echo '<p class="error">'.ERROR_EMPTY_MESSAGE.'</p>'."\n";
		echo $alert;
		echo $form;
	} else 
	{
		// send email and display message
		$msg = 'Name:\t'.$name."\n";
		$msg .= 'Email:\t'.$email."\n";
		$msg .= "\n".$comments."\n";
		$recipient = $this->GetConfigValue('admin_email');
		$subject = sprintf(FEEDBACK_SUBJECT,$this->GetConfigValue("wakka_name"));
		$mailheaders = 'From:'.$email."\n";
		$mailheaders .= 'Reply-To:'.$email;
		mail($recipient, $subject, $msg, $mailheaders);
		echo $this->Format(sprintf(SUCCESS_FEEDBACK_SENT,$recipient, $this->GetConfigValue('root_page')));
		// optionally displays the feedback text
		//echo $this->Format('---- **'.FEEDBACK_NAME_LABEL.'** '.$name.'---**'.FEEDBACK_EMAIL_LABEL.'** '.$email.'---**'.FEEDBACK_COMMENTS_LABEL.'** ---'.$comments');
	}    
} else 
{
	echo $form;
}
?>
