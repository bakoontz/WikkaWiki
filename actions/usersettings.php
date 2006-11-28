<?php
/**
 * Display a form to register, login and change user settings.
 *
 * @package		Actions
 * @version		$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author		{@link http://wikkawiki.org/MinusF MinusF} (code cleanup and validation)
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli} (further cleanup, i18n, replaced JS dialogs with server-generated messages)
 * @author		{@link http://wikkawiki.org/NilsLindenberg Nils Lindenberg} (possibility to restrict registration)
 *
 * @todo			use different actions for registration / login / user settings;
 * @todo			add documentation links or short explanations for each option;
 * @todo			use error handler for displaying messages and highlighting invalid input fields;
 * @todo			remove useless redirections;
 */
/**
 * defaults
 */
if (!defined('PASSWORD_MIN_LENGTH')) define('PASSWORD_MIN_LENGTH', "5");
if (!defined('VALID_EMAIL_PATTERN')) define('VALID_EMAIL_PATTERN', "/^.+?\@.+?\..+$/"); //TODO: Use central regex library
if (!defined('REVISION_DISPLAY_LIMIT_MIN')) define('REVISION_DISPLAY_LIMIT_MIN', "0"); // 0 means no limit, 1 is the minimum number of revisions
if (!defined('REVISION_DISPLAY_LIMIT_MAX')) define('REVISION_DISPLAY_LIMIT_MAX', "20"); // keep this value within a reasonable limit to avoid an unnecessary long lists
if (!defined('RECENTCHANGES_DISPLAY_LIMIT_MIN')) define('RECENTCHANGES_DISPLAY_LIMIT_MIN', "0"); // 0 means no limit, 1 is the minimum number of changes
if (!defined('RECENTCHANGES_DISPLAY_LIMIT_MAX')) define('RECENTCHANGES_DISPLAY_LIMIT_MAX', "50"); // keep this value within a reasonable limit to avoid an unnecessary long list
if (!defined('INPUT_ERROR_STYLE')) define('INPUT_ERROR_STYLE', 'class="highlight"');
/**
 * i18n
 */
if (!defined('USER_ACCOUNT_LEGEND')) define('USER_ACCOUNT_LEGEND', "Your account");
if (!defined('USER_SETTINGS_LEGEND')) define('USER_SETTINGS_LEGEND', "Settings");
if (!defined('LOGIN_REGISTER_LEGEND')) define('LOGIN_REGISTER_LEGEND', "Login/Register");
if (!defined('LOGIN_LEGEND')) define('LOGIN_LEGEND', "Login");
if (!defined('RETRIEVE_PASSWORD_LEGEND')) define('RETRIEVE_PASSWORD_LEGEND', "Password forgotten");
// if (!defined('REGISTER_LABEL')) define('REGISTER_LABEL', "Register"); # to be used later for register-action
if (!defined('USER_LOGGED_OUT')) define('USER_LOGGED_OUT', "You have successfully logged out.");
if (!defined('USER_SETTINGS_STORED')) define('USER_SETTINGS_STORED', "User settings stored!");
if (!defined('ERROR_NO_BLANK')) define('ERROR_NO_BLANK', "Sorry, blanks are not permitted in the password.");
if (!defined('ERROR_PASSWORD_TOO_SHORT')) define('ERROR_PASSWORD_TOO_SHORT', "Sorry, the password must contain at least %s characters.");
if (!defined('PASSWORD_CHANGED')) define('PASSWORD_CHANGED', "Password successfully changed!");
if (!defined('ERROR_OLD_PASSWORD_WRONG')) define('ERROR_OLD_PASSWORD_WRONG', "The old password you entered is wrong.");
if (!defined('USER_LOGGED_IN_AS_LABEL')) define('USER_LOGGED_IN_AS_LABEL', "Your are logged in as %s");
if (!defined('USER_EMAIL_LABEL')) define('USER_EMAIL_LABEL', "Your email address:");
if (!defined('DOUBLECLICK_LABEL')) define('DOUBLECLICK_LABEL', "Doubleclick editing:");
if (!defined('SHOW_COMMENTS_LABEL')) define('SHOW_COMMENTS_LABEL', "Show comments by default:");
if (!defined('RECENTCHANGES_DISPLAY_LIMIT_LABEL')) define('RECENTCHANGES_DISPLAY_LIMIT_LABEL', "RecentChanges display limit:");
if (!defined('PAGEREVISION_LIST_LIMIT_LABEL')) define('PAGEREVISION_LIST_LIMIT_LABEL', "Page revisions list limit:");
if (!defined('UPDATE_SETTINGS_INPUT')) define('UPDATE_SETTINGS_INPUT', "Update Settings");
if (!defined('CHANGE_PASSWORD_HEADING')) define('CHANGE_PASSWORD_HEADING', "Change your password:");
if (!defined('CURRENT_PASSWORD_LABEL')) define('CURRENT_PASSWORD_LABEL', "Your current password:");
if (!defined('PASSWORD_REMINDER_LABEL')) define('PASSWORD_REMINDER_LABEL', "Password reminder:");
if (!defined('NEW_PASSWORD_LABEL')) define('NEW_PASSWORD_LABEL', "Your new password:");
if (!defined('NEW_PASSWORD_CONFIRM_LABEL')) define('NEW_PASSWORD_CONFIRM_LABEL', "Confirm new password:");
if (!defined('CHANGE_BUTTON_LABEL')) define('CHANGE_BUTTON_LABEL', "Change password");
if (!defined('REGISTER_BUTTON_LABEL')) define('REGISTER_BUTTON_LABEL', "Register");
if (!defined('INVITATION_CODE_LABEL')) define('INVITATION_CODE_LABEL', "<abbr title=\"In order to register, you must fill in the invitation code sent by this website's administrator.\">Invitation Code</abbr>:");
if (!defined('ERROR_WRONG_PASSWORD')) define('ERROR_WRONG_PASSWORD', "Sorry, you entered the wrong password.");
if (!defined('ERROR_WRONG_HASH')) define('ERROR_WRONG_HASH', "Sorry, you entered a wrong password reminder.");
if (!defined('ERROR_EMPTY_USERNAME')) define('ERROR_EMPTY_USERNAME', "Please fill in your user name.");
if (!defined('ERROR_NON_EXISTENT_USERNAME')) define('ERROR_NON_EXISTENT_USERNAME', "Sorry, this user name doesn't exist.");
if (!defined('ERROR_RESERVED_PAGENAME')) define('ERROR_RESERVED_PAGENAME', "Sorry, this name is reserved for a page. Please choose a different name.");
if (!defined('ERROR_WIKINAME')) define('ERROR_WIKINAME', "Username must be formatted as a ##\"\"WikiName\"\"##, e.g. ##\"\"JohnDoe\"\"##.");
if (!defined('ERROR_EMPTY_PASSWORD')) define('ERROR_EMPTY_PASSWORD', "Please fill in a password.");
if (!defined('ERROR_EMPTY_PASSWORD_OR_HASH')) define('ERROR_EMPTY_PASSWORD_OR_HASH', "Please fill your password or hash.");
if (!defined('ERROR_EMPTY_CONFIRMATION_PASSWORD')) define('ERROR_EMPTY_CONFIRMATION_PASSWORD', "Please confirm your password in order to register a new account.");
if (!defined('ERROR_EMPTY_NEW_CONFIRMATION_PASSWORD')) define('ERROR_EMPTY_NEW_CONFIRMATION_PASSWORD', "Please confirm your new password in order to update your account.");
if (!defined('ERROR_EMPTY_NEW_PASSWORD')) define('ERROR_EMPTY_NEW_PASSWORD', "You must also fill in a new password.");
if (!defined('ERROR_PASSWORD_MATCH')) define('ERROR_PASSWORD_MATCH', "Passwords don't match.");
if (!defined('ERROR_EMAIL_ADDRESS_REQUIRED')) define('ERROR_EMAIL_ADDRESS_REQUIRED', "Please specify an email address.");
if (!defined('ERROR_INVALID_EMAIL_ADDRESS')) define('ERROR_INVALID_EMAIL_ADDRESS', "That doesn't quite look like an email address.");
if (!defined('ERROR_INVALID_REVISION_DISPLAY_LIMIT')) define('ERROR_INVALID_REVISION_DISPLAY_LIMIT', "The number of page revisions should not exceed %d.");
if (!defined('ERROR_INVALID_RECENTCHANGES_DISPLAY_LIMIT')) define('ERROR_INVALID_RECENTCHANGES_DISPLAY_LIMIT', "The number of recently changed pages should not exceed %d.");
if (!defined('ERROR_INVITATION_CODE_INCORRECT')) define ('ERROR_INVITATION_CODE_INCORRECT', "This is a private wiki, only invited members can register an account! Please contact the administrator of this website for an invitation code.");
if (!defined('NO_REGISTRATION')) define('NO_REGISTRATION', "Registration on this wiki is disabled.");
if (!defined('REGISTRATION_SUCCEEDED')) define('REGISTRATION_SUCCEEDED', "You have successfully registered!");
if (!defined('REGISTERED_USER_LOGIN_LABEL')) define('REGISTERED_USER_LOGIN_LABEL', "If you're already a registered user, log in here:");
if (!defined('WIKINAME_LABEL')) define('WIKINAME_LABEL', "Your <abbr title=\"A WikiName is formed by two or more capitalized words without space, e.g. JohnDoe\">WikiName</abbr>:");
if (!defined('PASSWORD_LABEL')) define('PASSWORD_LABEL', "Password (%s+ chars):");
if (!defined('LOGIN_BUTTON_LABEL')) define('LOGIN_BUTTON_LABEL', "Login");
if (!defined('LOGOUT_BUTTON_LABEL')) define('LOGOUT_BUTTON_LABEL', "Logout");
if (!defined('NEW_USER_REGISTER_LABEL')) define('NEW_USER_REGISTER_LABEL', "Fields required if you are signing up as a new user:");
if (!defined('CONFIRM_PASSWORD_LABEL')) define('CONFIRM_PASSWORD_LABEL', "Confirm password:");
if (!defined('RETRIEVE_PASSWORD_MESSAGE')) define('RETRIEVE_PASSWORD_MESSAGE', "If you need a password reminder, click [[PasswordForgotten here]]. --- You can login here using your password reminder.");
if (!defined('TEMP_PASSWORD_LABEL')) define('TEMP_PASSWORD_LABEL', "Password reminder:");

//initialize variables
$params = '';
$url = '';
$email = '';
$doubleclickedit = '';
$show_comments = '';
$revisioncount = '';
$changescount = '';
$password = '';
$oldpass = '';
$password_confirm = '';
$pw_selected = '';
$hash_selected = '';
$username_highlight = '';
$username_temp_highlight = '';
$password_temp_highlight = '';
$email_highlight = '';
$password_highlight = '';
$password_new_highlight = '';
$password_confirm_highlight = '';
$revisioncount_highlight = '';
$changescount_highlight = '';
$invitation_code_highlight = '';

//create URL
$url = $this->config['base_url'].$this->tag;

// append URL params depending on rewrite_mode
$params = ($this->config['rewrite_mode'] == 1)? '?' : '&';

// BEGIN *** Login/Logout ***
// is user trying to log out?
if (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'logout'))
{
	$this->LogoutUser();
	$params .= 'out=true';
	$this->Redirect($url.$params);
}
// END *** Login/Logout ***
// BEGIN *** Usersettings ***
// user is still logged in
else if ($user = $this->GetUser())
{
	// is user trying to update user settings?
	if (isset($_POST['action']) && ($_POST['action'] == 'update'))
	{
		// get POST parameters
		$email = $this->GetSafeVar('email', 'post');
		$doubleclickedit = $this->GetSafeVar('doubleclickedit', 'post');
		$show_comments = $this->GetSafeVar('show_comments', 'post');
		$revisioncount = (int) $this->GetSafeVar('revisioncount', 'post');
		$changescount = (int) $this->GetSafeVar('changescount', 'post');
		
		switch(TRUE) // validate form input
		{
			case (strlen($email) == 0): //email is empty
				$error = ERROR_EMAIL_ADDRESS_REQUIRED;
				$email_highlight = INPUT_ERROR_STYLE;
				break;
			case (!preg_match(VALID_EMAIL_PATTERN, $email)): //invalid email
				$error = ERROR_INVALID_EMAIL_ADDRESS;
				$email_highlight = INPUT_ERROR_STYLE;
				break;
			case (($revisioncount < REVISION_DISPLAY_LIMIT_MIN) || ($revisioncount > REVISION_DISPLAY_LIMIT_MAX)): //invalid revision display limit
				$error = sprintf(ERROR_INVALID_REVISION_DISPLAY_LIMIT, REVISION_DISPLAY_LIMIT_MAX);
				$revisioncount_highlight = INPUT_ERROR_STYLE;
				break;
			case (($changescount < RECENTCHANGES_DISPLAY_LIMIT_MIN) || ($changescount > RECENTCHANGES_DISPLAY_LIMIT_MAX)): //invalid recentchanges display limit
				$error = sprintf(ERROR_INVALID_RECENTCHANGES_DISPLAY_LIMIT, RECENTCHANGES_DISPLAY_LIMIT_MAX);
				$changescount_highlight = INPUT_ERROR_STYLE;
				break;
			default: // input is valid
				$this->Query('UPDATE '.$this->config['table_prefix'].'users SET '.
					"email = '".mysql_real_escape_string($email)."', ".
					"doubleclickedit = '".mysql_real_escape_string($doubleclickedit)."', ".
					"show_comments = '".mysql_real_escape_string($show_comments)."', ".
					"revisioncount = '".mysql_real_escape_string($revisioncount)."', ".
					"changescount = '".mysql_real_escape_string($changescount)."' ".
					"WHERE name = '".$user['name']."' LIMIT 1");
				$this->SetUser($this->LoadUser($user["name"]));
			
				// forward
				$params .= 'stored=true';
				$this->Redirect($url.$params);
		}
	}
	else //user just logged in
	{
		// get stored settings
		$email = $user['email'];
		$doubleclickedit = $user['doubleclickedit'];
		$show_comments = $user['show_comments'];
		$revisioncount = $user['revisioncount'];
		$changescount = $user['changescount'];
	}

	// display user settings form
	echo $this->FormOpen();
?>
	<fieldset id="account" class="usersettings"><legend><?php echo USER_ACCOUNT_LEGEND; ?></legend>
	<input type="hidden" name="action" value="update" />
	<label for="logout"><?php echo sprintf(USER_LOGGED_IN_AS_LABEL, $this->Link($user['name'])); ?></label>
	<input id="logout" type="button" value="<?php echo LOGOUT_BUTTON_LABEL; ?>" onclick="document.location='<?php echo $this->href('', '', 'action=logout'); ?>'" />
	</fieldset>
	
	<fieldset id="usersettings" class="usersettings"><legend><?php echo USER_SETTINGS_LEGEND; ?></legend>
<?php

	// create confirmation message if needed
	switch(TRUE)
	{
		case (isset($_GET['registered']) && $_GET['registered'] == 'true'):
			$success = REGISTRATION_SUCCEEDED;
			break;
		case (isset($_GET['stored']) && $_GET['stored'] == 'true'):
			$success = USER_SETTINGS_STORED;
			break;
		case (isset($_GET['newpassword']) && $_GET['newpassword'] == 'true'):
			$success = PASSWORD_CHANGED;
	}

	// display error or confirmation message
	switch(TRUE)
	{
		case (isset($error)):
			echo '<em class="error">'.$this->Format($error).'</em><br />'."\n";
			break;
		case (isset($success)):
			echo '<em class="success">'.$this->Format($success).'</em><br />'."\n";
			break;
		default:
	}
?>
	<label for="email"><?php echo USER_EMAIL_LABEL ?></label>
	<input id="email" type="text" <?php echo $email_highlight; ?> name="email" value="<?php echo $this->htmlspecialchars_ent($email) ?>" size="40" />
	<br />
	<label for="doubleclick"><?php echo DOUBLECLICK_LABEL ?></label>
	<input type="hidden" name="doubleclickedit" value="N" />
	<input id="doubleclick" type="checkbox" name="doubleclickedit" value="Y" <?php echo $doubleclickedit == 'Y' ? 'checked="checked"' : '' ?> />
	<br />
	<label for="showcomments"><?php echo SHOW_COMMENTS_LABEL ?></label>
	<input type="hidden" name="show_comments" value="N" />
	<input id="showcomments" type="checkbox" name="show_comments" value="Y" <?php echo $show_comments == 'Y' ? 'checked="checked"' : '' ?> />
	<br />
	<label for="revisioncount"><?php echo PAGEREVISION_LIST_LIMIT_LABEL ?></label>
	<input id="revisioncount" type="text" <?php echo $revisioncount_highlight; ?> name="revisioncount" value="<?php echo $this->htmlspecialchars_ent($revisioncount) ?>" size="40" />
	<br />
	<label for="changescount"><?php echo RECENTCHANGES_DISPLAY_LIMIT_LABEL ?></label>
	<input id="changescount" type="text" <?php echo $changescount_highlight; ?> name="changescount" value="<?php echo $this->htmlspecialchars_ent($changescount) ?>" size="40" />
	<br />
	<input id="updatesettings" type="submit" value="<?php echo UPDATE_SETTINGS_INPUT ?>" />
	<br />
	</fieldset>
<?php	
	echo $this->FormClose(); //close user settings form

	if (isset($_POST['action']) && ($_POST['action'] == 'changepass'))
	{
		// check password
		$oldpass = $_POST['oldpass']; //can be current password or hash sent as password reminder
		$password = $_POST['password'];
		$password_confirm = $_POST['password_confirm'];
		$update_option = $this->GetSafeVar('update_option', 'post');
		
		switch (TRUE)
		{
			case (strlen($oldpass) == 0):
				$passerror = ERROR_EMPTY_PASSWORD_OR_HASH;
				$password_highlight = INPUT_ERROR_STYLE;
				break;
			case (($update_option == 'pw') && md5($oldpass) != $user['password']): //wrong password
				$passerror = ERROR_WRONG_PASSWORD;
				$pw_selected = 'selected="selected"';
				$password_highlight = INPUT_ERROR_STYLE;			
				break;
			case (($update_option == 'hash') && $oldpass != $user['password']): //wrong hash
				$passerror = ERROR_WRONG_HASH;
				$hash_selected = 'selected="selected"';
				$password_highlight = INPUT_ERROR_STYLE;			
				break;
			case (strlen($password) == 0):
				$passerror = ERROR_EMPTY_NEW_PASSWORD;
				$password_highlight = INPUT_ERROR_STYLE;			
				$password_new_highlight = INPUT_ERROR_STYLE;
				break;
			case (preg_match("/ /", $password)):
				$passerror = ERROR_NO_BLANK;
				$password_highlight = INPUT_ERROR_STYLE;			
				$password_new_highlight = INPUT_ERROR_STYLE;
				break;
			case (strlen($password) < PASSWORD_MIN_LENGTH):
				$passerror = sprintf(ERROR_PASSWORD_TOO_SHORT, PASSWORD_MIN_LENGTH);
				$password_highlight = INPUT_ERROR_STYLE;			
				$password_new_highlight = INPUT_ERROR_STYLE;
				break;
			case (strlen($password_confirm) == 0):
				$passerror = ERROR_EMPTY_NEW_CONFIRMATION_PASSWORD;
				$password_highlight = INPUT_ERROR_STYLE;			
				$password_new_highlight = INPUT_ERROR_STYLE;
				$password_confirm_highlight = INPUT_ERROR_STYLE;
				break;
			case ($password_confirm != $password):
				$passerror = ERROR_PASSWORD_MATCH;
				$password_highlight = INPUT_ERROR_STYLE;
				$password_new_highlight = INPUT_ERROR_STYLE;			
				$password_confirm_highlight = INPUT_ERROR_STYLE;
				break;
			default:
				$this->Query('UPDATE '.$this->config['table_prefix'].'users set '."password = md5('".mysql_real_escape_string($password)."') "."WHERE name = '".$user['name']."'");
				$user['password'] = md5($password);
				$this->SetUser($user);
				$params .= 'newpassword=true';
				$this->Redirect($url.$params);
		}
	}

	//display password update form
	echo $this->FormOpen();
?>
	<fieldset class="usersettings" id="changepassword"><legend><?php echo CHANGE_PASSWORD_HEADING ?></legend>
	<input type="hidden" name="action" value="changepass" />
<?php
		if (isset($passerror))
		{
			print('<em class="error">'.$this->Format($passerror).'</em><br />'."\n");
		}
?>
	<select id="update_option" name="update_option">
		<option value="pw" <?php echo $pw_selected; ?>><?php echo CURRENT_PASSWORD_LABEL; ?></option>
		<option value="hash" <?php echo $hash_selected; ?>><?php echo PASSWORD_REMINDER_LABEL; ?></option>
	</select>
	<input <?php echo $password_highlight; ?> type="password" name="oldpass" size="40" />
	<br />
	<label for="password"><?php echo NEW_PASSWORD_LABEL ?></label>
	<input id="password" <?php echo $password_new_highlight; ?> type="password" name="password" size="40" />
	<br />
	<label for="password_confirm"><?php echo NEW_PASSWORD_CONFIRM_LABEL ?></label>
	<input id="password_confirm" <?php echo $password_confirm_highlight; ?> type="password" name="password_confirm" size="40" />
	<br />
	<input type="submit" value="<?php echo CHANGE_BUTTON_LABEL ?>" size="40" />
	<br />
	</fieldset>
<?php
	echo $this->FormClose();
}
// END *** Usersettings ***
// BEGIN *** LOGIN/LOGOUT ***
else // user is not logged in
{
	// print confirmation message on successful logout
	if (isset($_GET['out']) && ($_GET['out'] == 'true'))
	{
		$success = USER_LOGGED_OUT;
	}

	// is user trying to log in or register?
	$register = $this->GetConfigValue('allow_user_registration'); 
	if (isset($_POST['action']) && ($_POST['action'] == 'login'))
	{
		// if user name already exists, check password
		if (isset($_POST['name']) && $existingUser = $this->LoadUser($_POST['name']))
		{
			// check password
			switch(TRUE){
				case (strlen($_POST['password']) == 0):
					$error = ERROR_EMPTY_PASSWORD;
					$password_highlight = INPUT_ERROR_STYLE;
					break;
				case (md5($_POST['password']) != $existingUser['password']):
					$error = ERROR_WRONG_PASSWORD;
					$password_highlight = INPUT_ERROR_STYLE;
					break;
				default:
					$this->SetUser($existingUser);
					$this->Redirect($url, '');
			}
		}
		// END *** Login/Logout ***
		// BEGIN *** Register ***
		else if ($register == '1' || $register == '2') // otherwise, proceed to registration
		{
			$name = trim($_POST['name']);
			$email = trim($this->GetSafeVar('email', 'post'));
			$password = $_POST['password'];
			$confpassword = $_POST['confpassword'];

			// validate input
			switch(TRUE)
			{
				case (strlen($name) == 0):
					$error = ERROR_EMPTY_USERNAME;
					$username_highlight = INPUT_ERROR_STYLE;
					break;
				case (!$this->IsWikiName($name)):
					$error = ERROR_WIKINAME;
					$username_highlight = INPUT_ERROR_STYLE;
					break;
				case ($this->ExistsPage($name)):
					$error = ERROR_RESERVED_PAGENAME;
					$username_highlight = INPUT_ERROR_STYLE;
					break;
				case (strlen($password) == 0):
					$error = ERROR_EMPTY_PASSWORD;
					$password_highlight = INPUT_ERROR_STYLE;
					break;
				case (preg_match("/ /", $password)):
					$error = ERROR_NO_BLANK;
					$password_highlight = INPUT_ERROR_STYLE;
					break;
				case (strlen($password) < PASSWORD_MIN_LENGTH):
					$error = sprintf(ERROR_PASSWORD_TOO_SHORT, PASSWORD_MIN_LENGTH);
					$password_highlight = INPUT_ERROR_STYLE;
					break;
				case (strlen($confpassword) == 0):
					$error = ERROR_EMPTY_CONFIRMATION_PASSWORD;
					$password_highlight = INPUT_ERROR_STYLE;
					$password_confirm_highlight = INPUT_ERROR_STYLE;
					break;
				case ($confpassword != $password):
					$error = ERROR_PASSWORD_MATCH;
					$password_highlight = INPUT_ERROR_STYLE;
					$password_confirm_highlight = INPUT_ERROR_STYLE;
					break;
				case (strlen($email) == 0):
					$error = ERROR_EMAIL_ADDRESS_REQUIRED;
					$email_highlight = INPUT_ERROR_STYLE;
					$password_highlight = INPUT_ERROR_STYLE;
					$password_confirm_highlight = INPUT_ERROR_STYLE;
					break;
				case (!preg_match(VALID_EMAIL_PATTERN, $email)):
					$error = ERROR_INVALID_EMAIL_ADDRESS;
					$email_highlight = INPUT_ERROR_STYLE;
					$password_highlight = INPUT_ERROR_STYLE;
					$password_confirm_highlight = INPUT_ERROR_STYLE;
					break;
				case ($register == '2' && $_POST['invitation_code'] !==  $this->GetConfigValue('invitation_code')):
				    $error = ERROR_INVITATION_CODE_INCORRECT;
                	$invitation_code_highlight = INPUT_ERROR_STYLE;
                	break;
				default: //valid input, create user
					$this->Query("INSERT INTO ".$this->config['table_prefix']."users SET ".
						"signuptime = now(), ".
						"name = '".mysql_real_escape_string($name)."', ".
						"email = '".mysql_real_escape_string($email)."', ".
						"password = md5('".mysql_real_escape_string($_POST['password'])."')");

					// log in
					$this->SetUser($this->LoadUser($name));
					$params .= 'registered=true';
					$this->Redirect($url.$params);
			}
		}
	} 
	// END *** Register ***
	// BEGIN *** Usersettings ***
	elseif  (isset($_POST['action']) && ($_POST['action'] == 'updatepass'))
	{
        	$name = trim($_POST['yourname']);
		if (strlen($name) == 0) // empty username	
		{
			$newerror = ERROR_EMPTY_USERNAME;
			$username_temp_highlight = INPUT_ERROR_STYLE;
		}
		elseif (!$this->IsWikiName($name)) // check if name is WikiName style	
		{
			$newerror = ERROR_WIKINAME;
			$username_temp_highlight = INPUT_ERROR_STYLE;
		}
		elseif (!($this->LoadUser($_POST['yourname']))) //check if user exists
		{
			$newerror = ERROR_NON_EXISTENT_USERNAME;
			$username_temp_highlight = INPUT_ERROR_STYLE;
		}
		elseif ($existingUser = $this->LoadUser($_POST['yourname']))  // if user name already exists, check password
		{
			// updatepassword
			if ($existingUser['password'] == $_POST['temppassword'])
			{
				$this->SetUser($existingUser, $_POST['remember']);
				$this->Redirect($url);
			}
			else
			{
				$newerror = ERROR_WRONG_PASSWORD;
				$password_temp_highlight = INPUT_ERROR_STYLE;
			}
		}
	}
	// END *** Usersettings ***
	// BEGIN *** Login/Logout *** 
	// BEGIN ***  Register ***
	print($this->FormOpen());
?>
	<fieldset id="register" class="usersettings"><legend><?php  echo ($register == '1' || $register == '2') ? LOGIN_REGISTER_LEGEND : LOGIN_LEGEND; ?></legend>
	<input type="hidden" name="action" value="login" />
<?php
	switch (true)
	{
		case (isset($error)):
			echo '<em class="error">'.$this->Format($error).'</em>'."\n";
			break;
		case (isset($success)):
			echo '<em class="success">'.$this->Format($success).'</em>'."\n";
			break;
	}
?>
	<em><?php echo $this->Format(REGISTERED_USER_LOGIN_LABEL); ?></em>
	<br />
	<label for="name"><?php echo WIKINAME_LABEL ?></label>
	<input id="name" type="text" <?php echo $username_highlight; ?> name="name" size="40" value="<?php echo $this->GetSafeVar('name', 'post'); ?>" />
	<br />
	<label for="password"><?php echo sprintf(PASSWORD_LABEL, PASSWORD_MIN_LENGTH) ?></label>
	<input id="password" <?php echo $password_highlight; ?> type="password" name="password" size="40" />
	<br />
	<input id="login" type="submit" value="<?php echo LOGIN_BUTTON_LABEL ?>" size="40" />
	<br /><br />
<?php
	// END *** Login/Logout ***
	$register = $this->GetConfigValue('allow_user_registration');
    if ($register == '1' || $register == '2')
    {
?>
	<em><?php echo $this->Format(NEW_USER_REGISTER_LABEL); ?></em>
	<br />
	<label for="confpassword"><?php echo CONFIRM_PASSWORD_LABEL ?></label>
	<input id="confpassword" <?php echo $password_confirm_highlight; ?> type="password" name="confpassword" size="40" />
	<br />
	<label for="email"><?php echo USER_EMAIL_LABEL ?></label>
	<input id="email" type="text" <?php echo $email_highlight; ?> name="email" size="40" value="<?php echo $email; ?>" />
	<br />
<?php
	    if ($register == '2')
	    {
?>
	<label for="invitation_code"><?php echo INVITATION_CODE_LABEL ?></label>
	<input id="invitation_code" type="text" <?php echo $invitation_code_highlight; ?> size="20" name="invitation_code" />
	<br />
<?php
		}
?>
	<input type="submit" value="<?php echo REGISTER_BUTTON_LABEL ?>" size="40" />
	<br />
	</fieldset>
<?php
    }
	print($this->FormClose());
	// END *** Register ***
	print($this->FormOpen());
?>
	<fieldset id="password_forgotten" class="usersettings"><legend><?php echo RETRIEVE_PASSWORD_LEGEND; ?></legend>
	<input type="hidden" name="action" value="updatepass" />
<?php   
	if (isset($newerror))
	{
		print('<em class="error">'.$this->Format($newerror).'</em>'."\n");
	}
?>
	<em><?php echo $this->Format(RETRIEVE_PASSWORD_MESSAGE) ?></em>
	<br />
	<label for="yourname"><?php echo WIKINAME_LABEL ?></label>
	<input id="yourname" type="text" <?php echo $username_temp_highlight; ?> name="yourname" value="<?php echo $this->GetSafeVar('yourname', 'post'); ?>" size="40" />
	<br />
	<label for="temppassword"><?php echo TEMP_PASSWORD_LABEL ?></label>
	<input id="temppassword" type="text" <?php echo $password_temp_highlight; ?> name="temppassword" size="40" />
	<br />
	<input type="submit" value="<?php echo LOGIN_BUTTON_LABEL ?>" size="40" />
	<br />
	</fieldset>
<?php
	print($this->FormClose());
}
?>