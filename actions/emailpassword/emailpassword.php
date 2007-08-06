<?php
/** 
 * Send the user a reminder with the md5 checksum of his or her password via email.
 * 
 * @package		Actions
 * @version		$Id:emailpassword.php 369 2007-03-01 14:38:59Z DarTar $
 * @license http://comawiki.martignier.net/LizenzenUndBedingungen
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *  
 * @author	{@link http://comawiki.martignier.net Costal Martignier} initial action
 * @author	{@link http://wikkawiki.org/NilsLindenberg Nils Lindenberg} rewritten
 * @author	{@link http://wikkawiki.org/DarTar Dario Taraborelli} further cleanup, error styling and improved logical structure
 * 
 * @uses	Wakka::Format()
 * @uses	Wakka::LoadUser()
 * @uses	Wakka::FormOpen()
 * @uses	Wakka::FormClose()
 */ 

if (!defined('INPUT_ERROR_STYLE')) define('INPUT_ERROR_STYLE', 'class="highlight"');

// *** initialization ***
$input = $output = '';
$highlight = '';
$user = FALSE;
$mailsent = FALSE;

// print heading
#$output .= $this->Format(PW_FORGOTTEN_HEADING);
$output .= '<h3>'.PW_FORGOTTEN_HEADING.'</h3>';

// process input
if (isset($_POST['wikiname'])) // get posted values
{
	$input = $_POST['wikiname'];
	$user = $this->LoadUser($input);

	switch(TRUE)
	{
		case ($input == ''): // empty user
			$output .= '<em class="error">'.WIKKA_ERROR_EMPTY_USERNAME.'</em>'."\n";
			$highlight = INPUT_ERROR_STYLE;
			break;
		case ($input != '' && !$user): // non-existing user
			$output .= '<em class="error">'.ERROR_UNKNOWN_USER.'</em>'."\n";
			$highlight = INPUT_ERROR_STYLE;
			break;
		case ($input != '' && $user): // user exists, proceed
			$header = "From: ".$this->GetConfigValue('wakka_name')." <".$this->GetConfigValue('admin_email').">";
			$reference = sprintf(PW_FORGOTTEN_MAIL_REF, $user['name']);
			#$mail = sprintf(PW_FORGOTTEN_MAIL, $user['name'], $this->GetConfigValue('wakka_name'), $user['password'], $this->GetConfigValue('base_url').'UserSettings')."\n";
			$mail = sprintf(PW_FORGOTTEN_MAIL, $user['name'], $this->GetConfigValue('wakka_name'), $user['password'], $this->wikka_url.'UserSettings')."\n";
			if (mail($user['email'], $reference, $mail, $header))
			{
				$mailsent = TRUE;
				$output .= '<br /><em class="success">'.sprintf(PW_CHK_SENT, $user['name']).'</em>'."\n";
				$usersettings_wlink = '[[UserSettings '.WIKKA_LOGIN_LINK_DESC.']]';
				$output .= $this->Format(sprintf(USERSETTINGS_REF,$usersettings_wlink));
			}
			else 
			{
				$output .= '<em class="error">'.ERROR_MAIL_NOT_SENT.'</em><br />'."\n";
			}
			break;
	}
}

// display input form
if (!$mailsent)
{
	$output .= '<p>'.PW_FORM_TEXT.'</p>'."\n";
	$output .= $this->FormOpen();
	$output .= '<fieldset>'."\n";
	$output .= '<legend>'.PW_FORM_FIELDSET_LEGEND.'</legend>'."\n";
	$output .= '<input '.$highlight.' type="text" name="wikiname" value="" />'."\n";
	$output .= '<input type="submit" value="'.BUTTON_SEND_PW.'" />'."\n";
	$output .= '</fieldset>'."\n";
	$output .= $this->FormClose();   
}

// *** output section ***
if ($output !== '') echo $output;
?>