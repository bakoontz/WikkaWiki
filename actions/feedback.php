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

// set up form  variables
$form_caption	= FEEDBACK_FORM_CAPTION;
$form_open		= $this->FormOpen();
$form_close		= $this->FormClose();
$label_name		= FEEDBACK_NAME_LABEL;
$label_email	= FEEDBACK_EMAIL_LABEL;
$label_comment	= FEEDBACK_COMMENT_LABEL;
$button_send	= FEEDBACK_SEND_BUTTON;
// construct form template
$template = <<<TPLFEEDBACKFORM 
	<p>$form_caption</p>
	$form_open
	<input type="hidden" name="mail" value="result" />
	<label for="fb_name">$label_name</label> <input id="fb_name" name="name" value="{$_POST['name']}" type="text" /><br />
	<label for="fb_email">$label_email</label> <input id="fb_email" name="email" value="{$_POST['email']}" type="text" /><br />
	<label for="fb_comments">$label_comment</label><br />
	<textarea id="fb_comments" name="comments" rows="15" cols="40">{$_POST['comments']}</textarea><br />
	<input type="submit" value="$button_send" />
	$form_close
TPLFEEDBACKFORM;

// action
if ($_POST["mail"]=="result") {
	// process input
	$name = $_POST["name"];
	$email = $_POST["email"];
	$comments = $_POST["comments"];
	list($user, $host) = sscanf($email, "%[a-zA-Z0-9._-]@%[a-zA-Z0-9._-]");
	if (!$name) {
		// a valid name must be entered
		echo '<p class="error">'.ERROR_EMPTY_NAME.'</p>';
		echo $form;    
	} elseif (!$email || !strchr($email, "@") || !$user || !$host) {
		// a valid email address must be entered
		echo '<p class="error">'.ERROR_INVALID_FEEDBACK_EMAIL.'</p>'; 
		echo $form;
	} elseif (!$comments) {
		// some text must be entered
		echo '<p class="error">'.ERROR_EMPTY_COMMENT.'</p>';
		echo $alert;
		echo $form;
	} else {
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
		$f_recipient_link = '[['.$recipient.']]';
		$f_mainpage_link  = '[['.$this->GetConfigValue('root_page').' '.WIKKA_MAINPAGE_LINK_DESC.']]';
		echo $this->Format(sprintf(FEEDBACK_SENT.' ---',$f_recipient_link));
		echo $this->Format(sprintf(MAIN_PAGE_REF,$f_mainpage_link));
		// optionally displays the feedback text
		//echo $this->Format("---- **Name:** ".$name."---**Email:** ".$email."---**Comments:** ---".$comments); # i18n (#340)
	}    
} else {
	// display form
	echo $template;
}

?>