<?php
/**
 * Display a form to send feedback to the site administrator or to any registered user.
 *
 * The sender is automatically filled in when the user is logged in. The recipient
 * can be specified in several ways. When this action is used in a userpage,
 * feedback is sent to the corresponding user. This behavior can be overridden
 * by specifying a registered username via the optional <b>to</b> parameter.
 * When no recipient is specified, and the action is not on a user page, feedback
 * is sent to the admin.
 *
 * The action validates the form and sends the message using the PHP <b>mail()</b>
 * function.
 *
 * Notes:<br/>
 * - On some servers outgoing mail via the <b>mail()</b> function may be disabled.
 * - While this action tries to comply with RFC 2822, it does <b>not</b>
 * cater for non-complying mail servers like GMail (does not handle CRLF between
 * headers) or Postfix (converts a single \r\n into double new lines). It does,
 * however, attempt to take into account the differences between sending mail from
 * a Windows platform and sending from a Unix platform. See {@link http://php.net/mail}.
 * 
 * @package		Actions
 * @version		$Id:feedback.php 369 2007-03-01 14:38:59Z DarTar $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author	{@link http://wikkawiki.org/DarTar Dario Taraborelli} (fixing GUI strings, extended functionality)
 * @author	{@link http://wikkawiki.org/NilsLindenberg Nils Lindenberg} (documentation)
 * @author	{@link http://wikkawiki.org/JavaWoman Marjolein Katsma} (i18n, form accessibility, RFC2822/3986 compliance)
 *
 * @uses	Wakka::FormOpen()
 * @uses	Wakka::FormClose()
 * @uses	Wakka::loadUserData()
 * @uses	Wakka::GetConfigValue()
 * @uses	Wakka::existsUser()
 * @uses	Wakka::GetUser()
 * @uses	normalizeEol()
 * 
 * @input	string	$to	optional: recipient username;
 *			default: wiki administrator
 *			the default can also be overridden by embedding this action in a
 *			userpage whose owner will then become the recipient; the $to parameter
 *			takes precedence over user page in which the action may be embedded.
 * @output	Contact form to send feedback to a specified recipient or to the wiki
 *			admin,.and/or a success or error message
 * @todo	Use central regex library for validation #34
 *			NOTE: the VALID_EMAIL_FORMAT is not actually a regular expression but
 *			a <b>format string</b> as used in (s)printf and sscanf; if something
 *			like this is to be moved to a RE library, it should be converted to
 *			an actual RE and used with preg_match() before doing that! The format
 *			string used is also not strictly validating (though a reasonable sanity
 *			check). See also codeslinger's security note at {@link http://php.net/sscanf}
 * @todo	Update documentation for feedback action
 * @todo	Implement antispam measures to prevent scripted form posting
 */

/**#@+
 * Default value.
 */
define('ALLOW_FEEDBACK_FROM_UNREGISTERED', TRUE); #move to action configuration file
define('DISPLAY_SENT_MESSAGE', TRUE); #move to action configuration file
#define('VALID_EMAIL_FORMAT', "%[a-zA-Z0-9._-]@%[a-zA-Z0-9._-]"); // Move to central regex library #34
define('RFC_RECOMMENDED_EMAIL_LINE_LENGTH', 78);	// RFC 2822	@@@ move to global constants
define('RFC_MAX_EMAIL_LINE_LENGTH', 998);	// RFC 2822	@@@ move to global constants
define('RFC_MAX_URI_LENGTH', 255);	// RFC 3696	@@@ move to global constants
/**#@-*/
// build pattern for validating email	@@@ move to regex lib #34
// 1: local part
// We do not use FWS, CFWS, quoted-string or domain-literal (for now)
$pattern_atext_2822 = "[a-zA-Z0-9\^\$\/\\!#%&'*+=?`{|}~_-]+";	// RFC 2822
$pattern_dot_atom_text_2822 = $pattern_atext_2822.'(\.'.$pattern_atext_2822.')*';	// RFC 2822
$pattern_valid_local_part_2822	= $pattern_dot_atom_text_2822;	// RFC 2822	# officially: dot-atom / quoted-string
#$pattern_valid_domain_2822	= $pattern_dot_atom_text_2822;	// RFC 2822, but not used here	# officially: dot-atom / domain-literal
// 2: domain
// We do not use IP-literal or IPv4address (for now); only "registered name",
// while excepting sub-delims as they are not allowed in (sub)domain labels: IOW,
// we allow unreserved and pct-encoded for each domain label, and for the TLD only
// only unreserved chars [a-z0-9-] (RFC 3986/3696).
// Note that while RFC 822 still says that for SMTP an IP address must be in [],
// (see 822/6.2.3 and 1123/5.2.17), since IPv6 and RFC 2822 this is actually no
// longer allowed for IPv4 - only for IPv6!
// There must be at least one dot in the whole domain while the whole email address
// must not be longer than 255 chars (test length separately!)
$pattern_unreserved = '[a-z0-9-]';	// NOTE: RFC 3986 but *excluding* the dot, underscore and tilde (cannot occur in a domain label - cf. rfc3696.txt); case-insensitive
$pattern_pct_encoded_3986 = '%[0-9a-f]{2}';	// NOTE: case-insensitive
$pattern_domain_label = '(('.$pattern_unreserved.'|'.$pattern_pct_encoded_3986.'){2,252})';	// the whole domain must not be more than 255; TLD must be at least 2 chars
$pattern_tld_label = $pattern_unreserved.'{2,}';	// RFC 3696
$pattern_reg_name_3986 = $pattern_domain_label.'(\.'.$pattern_domain_label.')*\.'.$pattern_tld_label;	// RFC 3986 3.2.2 & RFC 3696	# registered name: officially *( unreserved / pct-encoded / sub-delims ) for a URI
$pattern_valid_domain_3986 = $pattern_reg_name_3986;	// may be extended later with IP address forms
// 3: result, using RFC 2822 for local part and RFC 3986 (etc.) for the domain part
#$pattern_valid_email = '^('.$pattern_valid_local_part_2822.')@('.$pattern_valid_domain_3986.')$';	// the brackets allow parsing into parts
$pattern_valid_email = '('.$pattern_valid_local_part_2822.')@('.$pattern_valid_domain_3986.')';	// the brackets allow parsing into parts
// 4. validating REs for email addresses
// Everything that matches RE_VALID_EMAIL is valid, but not everything that is
// valid will match! See exceptions listed with building blocks.
// Note that the domain part is case-insensitive, the local part is not! BUT for
// parsing and validating an email address, a case-insensitive RE can be used.
define('RE_VALID_EMAIL_LOCAL', '/'.$pattern_valid_local_part_2822.'/');
define('RE_VALID_EMAIL_DOMAIN', '/'.$pattern_valid_domain_3986.'/i');
define('RE_VALID_EMAIL', '/'.$pattern_valid_email.'/i');	// must also not be longer than 255 characters

//only display form when feedback is allowed
if (ALLOW_FEEDBACK_FROM_UNREGISTERED || $this->existsUser())	// just check for *registered* user
{
	// init
	$error = '';
	$success = '';
	$b_admin = FALSE;
	$sender_name = '';
	$sender_email = '';
	$comments = '';
	//set recipient
	switch (TRUE)
	{
		//recipient specified via action parameter "to"
		case (isset($to) && $user_specified_recipient = $this->loadUserData($to)):
			$recipient_name = $user_specified_recipient['name'];	// pick up retrieved name (not input)
			$recipient_email = $user_specified_recipient['email'];
			break;

		//recipient specified via pagename (by embedding in userpage)
		case ($userpage_recipient = $this->loadUserData($this->tag)):
			$recipient_name = $userpage_recipient['name'];			// pick up retrieved name
			$recipient_email = $userpage_recipient['email'];
			break;

		//default recipient is admin
		default:
			$adminarray = preg_split('/\s*,\s*/',trim($this->GetConfigValue('admin_users')), -1, PREG_SPLIT_NO_EMPTY);
			$recipient_name = $adminarray[0];
			$admin = $this->loadUserData($this->tag);
			$recipient_email = $admin['admin_email'];	// get email from DB rather than config!
			$b_admin = TRUE;
			break;
	}
	// format recipient
	$disp_recipient = ($b_admin) ? $this->FormatUser($recipient_name).' (admin)' : $this->FormatUser($recipient_name);	#i18n

	// process
	if (isset($_POST['feedback']) && $_POST['feedback'] == FEEDBACK_SEND_BUTTON)	// Send button pressed
	{
		//get user input
		$sender_name = (isset($_POST['name'])) ? trim($_POST['name']) : '';
		$sender_email = (isset($_POST['email'])) ? trim($_POST['email']) : '';
		$comments = (isset($_POST['comments'])) ? trim($_POST['comments']) : '';

		// validate user input
		#list($local_part, $domain) = sscanf($email, VALID_EMAIL_FORMAT); // use central regex library #34
		$email_local_part = NULL;
		$email_domain_part = NULL;
		if (preg_match(RE_VALID_EMAIL, $sender_email, $matches))
		{
#echo 'email valid'."<br/>\n";
			$email_local_part = $matches[1];
			$email_domain_part = $matches[3];
#echo 'local part: '.$email_local_part."<br/>\n";
#echo 'domain part: '.$email_domain_part."<br/>\n";
		}
#echo '<pre>';
#print_r($matches);
#echo "</pre>\n";
		if (strlen($sender_name) == 0)
		{
			// a non-empty name must be entered
			$error = '<em class="error">'.ERROR_EMPTY_NAME.'</em>';
		}
		#elseif (strlen($sender_email) == 0 || !strchr($sender_email, "@") || strlen($local_part) == 0 || strlen($domain) == 0)
		elseif (strlen($sender_email) == 0 ||
				strlen($sender_email) > RFC_MAX_URI_LENGTH ||
				is_null($email_local_part) ||
				is_null($email_domain_part)
			   )
		{
			// a valid email address must be entered
			$error = '<em class="error">'.ERROR_INVALID_EMAIL.'</em>';
		}
		elseif (strlen($comments) == 0)
		{
			// some text must be entered
			$error = '<em class="error">'.ERROR_EMPTY_MESSAGE.'</em>';
		}
		else
		{
			// everything validates, try to send an email

			// init
			$sender		= $sender_name.' <'.$sender_email.'>';	// MUST comply with RFC 2822 3.4
			$recipient	= $recipient_name.' <'.$recipient_email.'>';	// MUST comply with RFC 2822 3.4
			$eol = PHP_EOL;	// takes different operation of send mail between Windows and *nix into account
			$comments = normalizeEol($comments,$eol,RFC_RECOMMENDED_EMAIL_LINE_LENGTH);	// normalize line endings and wrap at 78 (as recommended in RFC 2822)
			$add_headers = '';
			$add_params  = '';

			// specify address to be used as "envelope from"
			ini_set('sendmail_from',$sender_email);	// Windows, Linux
			if (strtoupper(substr(PHP_OS,0,3) == 'WIN'))
			{
				$add_params .= '-r '.$sender_email;	// Unix
			}

			// build email
			// @@@ maybe add a Cc: to self for registered user (NOT for anonymous!)
			$subject = sprintf(FEEDBACK_SUBJECT,$this->GetConfigValue('wakka_name'));
			$msg  = FEEDBACK_NAME_LABEL."\t".$sender_name.$eol;
			$msg .= FEEDBACK_EMAIL_LABEL."\t".$sender_email.$eol;
			$msg .= $eol.$comments.$eol;
			#$add_headers .= 'To: '.$recipient.$eol;			// adding full address - may lead to duplicate mails: suppressing for now
			$add_headers .= 'From: '.$sender.$eol;			// adding full address
			$add_headers .= 'Reply-To: '.$sender.$eol;		// adding full address

			// Send email (using $recipient_email instead of $recipient since the
			// latter may fail on Windows or (even) from IE - basically we use only
			// an "envelope-to" here.
			// Suppress warning with @ - we only show user-oriented error message
			// if mail() fails (but remove @ for debugging!).
			#$rc = @mail($recipient_email, $subject, $msg, $add_headers,$add_params);	// normal check if successful!
			$rc = mail($recipient_email, $subject, $msg, $add_headers, $add_params);	// DEBUG check if successful!
#echo 'email sent as:'."<br/>\n";
#echo '$recipient_email: '.$recipient_email."<br/>\n";
#echo '$subject: '.$subject."<br/>\n";
#echo '$msg:<br/>|'.$msg."|<br/>\n";
#echo '$add_headers:<br/>|'.$add_headers."|<br/>\n";
#echo '$add_params: '.$add_params."<br/>\n";

			// restore ini setting
			ini_restore('sendmail_from');

			// display confirmation message if message was sent successfully, or error if not
			if ($rc)
			{
				$success = '<em class="success">'.sprintf(SUCCESS_FEEDBACK_SENT, $sender_name).'</em>'."\n";
			}
			else
			{
				$error = '<em class="error">'.sprintf(ERROR_FEEDBACK_MAIL_NOT_SENT, $disp_recipient).'</em>'."\n";
			}
		}
	}

	// output

	// form elements
	$form_open		= $this->FormOpen();
	$form_legend	= sprintf(FEEDBACK_FORM_LEGEND, $disp_recipient);
	$label_name		= FEEDBACK_NAME_LABEL;
	$label_email	= FEEDBACK_EMAIL_LABEL;
	$label_message	= FEEDBACK_MESSAGE_LABEL;
	$button_send	= FEEDBACK_SEND_BUTTON;
	$form_close		= $this->FormClose();
	
	// check if user is logged in; pre-fill sender data if so
	// @@@	use special styling for read-only as it's not obvious now!
	//		or make it text display combined with hidden fields
	if ($user = $this->GetUser())
	{
		$form_sender_name = '<input id="fb_name" name="name" value="'.$user['name'].'" type="text" readonly="readonly" />'."\n";
		$form_sender_email = '<input id="fb_email" name="email" value="'.$user['email'].'" type="text" readonly="readonly" />'."\n";
	}
	else
	{
		$form_sender_name = '<input id="fb_name" name="name" value="'.$sender_name.'" type="text" />'."\n";
		$form_sender_email = '<input id="fb_email" name="email" value="'.$sender_email.'" type="text" />'."\n";
	}

	// construct success template
	$tpl_mailsent =
<<<MAILSENT
	<hr/>
	<strong>$label_name</strong>	{$sender_name}<br />
	<strong>$label_email</strong>	{$sender_email}<br />
	<strong>$label_message</strong><br />{$comments}
MAILSENT;

	// construct form template
	// @@@ Why so narrow? setting cols to 78 (maybe better 80) doesn't help, apparently
	// overridden by the stylesheet. The narrow textarea is really annoying; the form
	// should re-display the mail as it's word-wrapped (line length 78).
	$tpl_form =
<<<TPLFEEDBACKFORM
	$form_open
	<fieldset class="feedback"><legend>$form_legend</legend>
	<label for="fb_name">$label_name</label> $form_sender_name<br />
	<label for="fb_email">$label_email</label> $form_sender_email<br />
	<label for="fb_message">$label_message</label>
	<textarea id="fb_message" name="comments" rows="15" cols="40">{$comments}</textarea><br />
	<input type="submit" name="feedback" value="$button_send" /><br class="clear" />
	</fieldset>
	$form_close
TPLFEEDBACKFORM;

	// produce output
	if ('' != $success)
	{
		// display success message
		echo $success;
		if (DISPLAY_SENT_MESSAGE)
		{
			echo $tpl_mailsent;
		}
	}
	else
	{
		// display error message, if any
		if ('' != $error)
		{
			echo $error;
		}
		// display form
		echo $tpl_form;
	}
}
?>