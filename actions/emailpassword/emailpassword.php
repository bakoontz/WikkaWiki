<?php
/** 
 * Send the user a reminder with the md5 checksum of his or her password via email.
 *
 * @package		Actions
 * @version		$Id:emailpassword.php 369 2007-03-01 14:38:59Z DarTar $
 * @license		http://comawiki.martignier.net/LizenzenUndBedingungen
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author	{@link http://comawiki.martignier.net Costal Martignier} initial action
 * @author	{@link http://wikkawiki.org/NilsLindenberg Nils Lindenberg} rewritten
 * @author	{@link http://wikkawiki.org/DarTar Dario Taraborelli} further cleanup, error styling and improved logical structure
 *
 * @uses	Wakka::Format()
 * @uses	Wakka::loadUserData()
 * @uses	Wakka::FormOpen()
 * @uses	Wakka::FormClose()
 * @uses	Wakka::GetConfigValue()
 */

// *** initialization ***
$input = $output = '';
$highlight = '';
$user = FALSE;
$mailsent = FALSE;

// print heading
$output .= '<h3>'.T_("Password reminder").'</h3>';

// process input
if (isset($_POST['wikiname'])) // get posted values
{
	$input = $this->GetSafeVar('wikiname', 'post');
	$user = $this->loadUserData($input);

	switch(TRUE)
	{
		case ($input == ''): // empty user
			$output .= '<em class="error">'.T_("Please fill in your username!").'</em><br />'."\n";
			$highlight = 'class="highlight"';
			break;
		case ($input != '' && !$user): // non-existing user
			$output .= '<em class="error">'.T_("You have entered a non-existent user!").'</em><br />'."\n";
			$highlight = 'class="highlight"';
			break;
		case ($input != '' && $user): // user exists, proceed
			$header = "From: ".$this->GetConfigValue('wakka_name')." <".$this->GetConfigValue('admin_email').">";
			$header .= "\r\nContent-Type: text/plain; charset=UTF-8";
			$reference = sprintf(T_("Password reminder for %s"), $user['name']);
			$mail = sprintf(T_('Hello, %s!  Someone requested that we send to this email address a password reminder to login at %s. If you did not request this reminder, disregard this email, no action is necessary. Your password will stay the same.  Your wikiname: %s Password reminder: %s URL: %s Do not forget to change the password immediately after logging in.'), $user['name'], $this->GetConfigValue('wakka_name'), $user['name'], $user['password'], $this->Href('', 'UserSettings'))."\n";
			if (mail($user['email'], $reference, $mail, $header))
			{
				$mailsent = TRUE;
				$output .= '<br /><em class="success">'.sprintf(T_("A password reminder has been sent to %s's registered email address."), $user['name']).'</em><br />'."\n";
				$output .= sprintf(T_("Return to the <a href=\"%s\">login</a> screen."), $this->Href('', 'UserSettings'));
			}
			else
			{
				$output .= '<em class="error">'.T_("An error occurred while trying to send the password. Outgoing mail might be disabled. Please try to contact your wiki administrator by posting a page comment.").'</em><br />'."\n";
			}
			break;
	}
}

// display input form
if (!$mailsent)
{
	$output .= '<p>'.T_("Enter your WikiName and a password reminder will be sent to your registered email address.").'</p>'."\n";
	$output .= $this->FormOpen();
	$output .= '<fieldset>'."\n";
	$output .= '<legend>'.T_("Your WikiName:").'</legend>'."\n";
	$output .= '<input '.$highlight.' type="text" name="wikiname" value="" />'."\n";
	$output .= '<input type="submit" value="'.T_("Send reminder").'" />'."\n";
	$output .= '</fieldset>'."\n";
	$output .= $this->FormClose();
}

// *** output section ***
if ($output !== '') echo $output;
?>
