<?php

// Displays a form to send feedback to the site administrator, as specified in wikka.config.php
// It first validates the form, then sends it using the mail() function;

$name='';
$email='';
$comments='';

if(isset($_POST["name"])) $name = $_POST["name"];
if(isset($_POST["email"])) $email = $_POST["email"];
if(isset($_POST["comments"])) $comments = $_POST["comments"];

$form = "<p>Fill in the form below to send us your comments:</p>".
            $this->FormOpen().
            "\nName: <input name=\"name\" value=\"".$this->htmlspecialchars_ent($name)."\" type=\"text\" /><br />".
            "\n<input type=\"hidden\" name=\"mail\" value=\"result\">".
            "\nEmail: <input name=\"email\" value=\"".$this->htmlspecialchars_ent($email)."\" type=\"text\" /><br />".
            "\nComments:<br />\n<textarea name=\"comments\" rows=\"15\" cols=\"40\">".$this->htmlspecialchars_ent($comments)."</textarea><br / >".
            "\n<input type=\"submit\" value=\"Send\" />".
            $this->FormClose();

if (isset($_POST["mail"]) && $_POST["mail"]=="result") 
{

	list($user, $host) = sscanf($email, "%[a-zA-Z0-9._-]@%[a-zA-Z0-9._-]");
	if (!$name) 
	{
		// a valid name must be entered
		echo "<p class=\"error\">Please enter your name</p>";    
		echo $form;    
	} elseif (!$email || !strchr($email, "@") || !$user || !$host)
	{
		// a valid email address must be entered
		echo "<p class=\"error\">Please enter a valid email address</p>";    
		echo $form;
	} elseif (!$comments) 
	{
		// some text must be entered
		echo "<p class=\"error\">Please enter some text</p>";    
		echo $alert;
		echo $form;
	} else 
	{
		// send email and display message
		$msg = "Name:\t".$name."\n";
		$msg .= "Email:\t".$email."\n";
		$msg .= "\n".$comments."\n";
		$recipient = $this->GetConfigValue("admin_email");
		$subject = "Feedback from ".$this->GetConfigValue("wakka_name");
		$mailheaders = "From:".$email."\n";
		$mailheaders .= "Reply-To:".$email;
		mail($recipient, $subject, $msg, $mailheaders);
		echo $this->Format("Thanks for your interest! Your feedback has been sent to [[".$recipient."]] ---");
		echo $this->Format("Return to the [[".$this->GetConfigValue("root_page")." main page]]");
		// optionally displays the feedback text
		//echo $this->Format("---- **Name:** ".$name."---**Email:** ".$email."---**Comments:** ---".$comments);
	}    
} else 
{
	echo $form;
}
?>
