<?php
/**
 * Displays a form to send feedback to the site administrator, as specified in wakka.config.php
 * It first validates the form, then sends it using the mail() function;
 */

// constant section
define('FEEDBACK_FORM_HEADER', 'Fill in the form below to send us your comments:');
define('FEEDBACK_FORM_LABEL_NAME', 'Name: ');
define('FEEDBACK_FORM_LABEL_EMAIL', 'Email: ');
define('FEEDBACK_FORM_LABEL_COMMENTS', 'Comments: ');
define('FEEDBACK_FORM_BUTTON', 'Send');

define('FEEDBACK_MAIL_SUBJECT', 'Feedback from : %s'); // %s - user who entered feedback
define('FEEDBACK_MAIL_SEND', 'Thanks for your interest! Your feedback has been sent to [[%1\s]] --- Return to the [[ %2\s main page]]'); // %1\s - admin-email %2\s - root page

define('ERROR_NO_NAME', 'Please enter your name');
define('ERROR_NO_EMAIL', 'Please enter a valid email address');
define('ERROR_NO_TEXT', 'Please enter some text');

$form = '<p>'.FEEDBACK_FORM_HEADER.'</p>'.
            $this->FormOpen().
            "\n".FEEDBACK_FORM_LABEL_NAME.'<input name="name" value="'.$_POST["name"].'" type="text" /><br />'.
            "\n".'<input type="hidden" name="mail" value="result">'.
            "\n".FEEDBACK_FORM_LABEL_EMAIL.'<input name="email" value="'.$_POST["email"].'" type="text" /><br />'.
            "\n".FEEDBACK_FORM_LABEL_COMMENTS."<br />\n".'<textarea name="comments" rows="15" cols="40">'.$_POST["comments"].'</textarea><br / >'.
            "\n".'<input type="submit" value="'.FEEDBACK_FORM_BUTTON.'" />'.
            $this->FormClose();

if ($_POST["mail"]=="result") {
	$name = $_POST["name"];
	$email = $_POST["email"];
	$comments = $_POST["comments"];
	list($user, $host) = sscanf($email, "%[a-zA-Z0-9._-]@%[a-zA-Z0-9._-]");
	if (!$name) {
		// a valid name must be entered
		echo '<p class="error">'.ERROR_NO_NAME.'</p>';
		echo $form;
	} elseif (!$email || !strchr($email, "@") || !$user || !$host) {
		// a valid email address must be entered
		echo '<p class="error">'.ERROR_NO_EMAIL.'</p>';
		echo $form;
	} elseif (!$comments) {
		// some text must be entered
		echo '<p class="error">'.ERROR_NO_TEXT.'</p>';
		echo $alert;
		echo $form;
	} else {
		// send email and display message
		$msg = FEEDBACK_FORM_LABEL_NAME."\t".$name."\n";
		$msg .= FEEDBACK_FORM_LABEL_EMAIL."\t".$email."\n";
		$msg .= "\n".$comments."\n";
		$recipient = $this->GetConfigValue("admin_email");
		$subject = sprintf(FEEDBACK_MAIL_SUBJECT, $this->GetConfigValue("wakka_name"));
		$mailheaders = "From:".$email."\n";
		$mailheaders .= "Reply-To:".$email;
		mail($recipient, $subject, $msg, $mailheaders);
                 print(sprintf(FEEDBACK_MAIL_SEND, $recipient, $this->GetConfigValue("root_page")));
		// optionally displays the feedback text
		//echo $this->Format("---- **Name:** ".$name."---**Email:** ".$email."---**Comments:** ---".$comments); # i18n
	}
} else {
	echo $form;
}

?>