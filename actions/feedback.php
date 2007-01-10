<?php
/**
 * Display a form to send feedback to the site administrator, as specified in wikka.config.php
 * 
 * It first validates the form, then sends it using the mail() function;
 * 
 * @package		Actions
 * @version		$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses	Wakka::FormOpen()
 * @uses	Wakka::FormClose()
 * @uses	Wakka::GetConfigValue()
 */

$form = '<p>'.FEEDBACK_FORM_LABEL.'</p>'.
            $this->FormOpen().
            "\n".FEEDBACK_NAME_LABEL.' <input name="name" value="'.$_POST["name"].'" type="text" /><br />'.
            "\n".'<input type="hidden" name="mail" value="result">'.
            "\n".FEEDBACK_EMAIL_LABEL.' <input name="email" value="'.$_POST["email"].'" type="text" /><br />'.
            "\n".FEEDBACK_COMMENT_LABEL.' <br />'."\n".'<textarea name="comments" rows="15" cols="40">'.$_POST["comments"]."</textarea><br / >".
            "\n".'<input type="submit" value="'.BUTTON_SEND.'" />'.
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
		echo '<p class="error">'.ERROR_NO_TXT.'</p>';
		echo $alert;
		echo $form;
	} else {
		// send email and display message
		$msg = FEEDBACK_NAME_LABEL."\t".$name."\n";
		$msg .= FEEDBACK_EMAIL_LABEL."\t".$email."\n";
		$msg .= "\n".$comments."\n";
		$recipient = $this->GetConfigValue("admin_email");
		$subject = "Feedback from ".$this->GetConfigValue("wakka_name"); #i18n
		$mailheaders = "From:".$email."\n";
		$mailheaders .= "Reply-To:".$email;
		mail($recipient, $subject, $msg, $mailheaders);
		echo $this->Format(sprintf(FEEDBACK_SENT, $recipient));
		echo $this->Format(sprintf(MAIN_PAGE_LINK, $this->GetConfigValue("root_page")));
		// optionally displays the feedback text
		//echo $this->Format("---- **Name:** ".$name."---**Email:** ".$email."---**Comments:** ---".$comments);
	}    
} else {
	echo $form;
}

?>