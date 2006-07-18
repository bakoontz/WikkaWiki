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

$form = "<p>Fill in the form below to send us your comments:</p>".
            $this->FormOpen().
            "\nName: <input name=\"name\" value=\"".$_POST["name"]."\" type=\"text\" /><br />".
            "\n<input type=\"hidden\" name=\"mail\" value=\"result\">".
            "\nEmail: <input name=\"email\" value=\"".$_POST["email"]."\" type=\"text\" /><br />".
            "\nComments:<br />\n<textarea name=\"comments\" rows=\"15\" cols=\"40\">".$_POST["comments"]."</textarea><br / >".
            "\n<input type=\"submit\" value=\"Send\" />".
            $this->FormClose();

if ($_POST["mail"]=="result") {
	$name = $_POST["name"];
	$email = $_POST["email"];
	$comments = $_POST["comments"];
	list($user, $host) = sscanf($email, "%[a-zA-Z0-9._-]@%[a-zA-Z0-9._-]");
	if (!$name) {
		// a valid name must be entered
		echo "<p class=\"error\">Please enter your name</p>";    #i18n
		echo $form;    
	} elseif (!$email || !strchr($email, "@") || !$user || !$host) {
		// a valid email address must be entered
		echo "<p class=\"error\">Please enter a valid email address</p>"; #i18n  
		echo $form;
	} elseif (!$comments) {
		// some text must be entered
		echo "<p class=\"error\">Please enter some text</p>"; #i18n
		echo $alert;
		echo $form;
	} else {
		// send email and display message
		$msg = "Name:\t".$name."\n";
		$msg .= "Email:\t".$email."\n";
		$msg .= "\n".$comments."\n";
		$recipient = $this->GetConfigValue("admin_email");
		$subject = "Feedback from ".$this->GetConfigValue("wakka_name");
		$mailheaders = "From:".$email."\n";
		$mailheaders .= "Reply-To:".$email;
		mail($recipient, $subject, $msg, $mailheaders);
		echo $this->Format("Thanks for your interest! Your feedback has been sent to [[".$recipient."]] ---"); #i18n
		echo $this->Format("Return to the [[".$this->GetConfigValue("root_page")." main page]]"); #i18n
		// optionally displays the feedback text
		//echo $this->Format("---- **Name:** ".$name."---**Email:** ".$email."---**Comments:** ---".$comments);
	}    
} else {
	echo $form;
}

?>