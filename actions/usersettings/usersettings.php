<?php
/**
 * Display a form to register, login and change user settings.
 *
 * @package		Actions
 * @version		$Id:usersettings.php 369 2007-03-01 14:38:59Z DarTar $
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author		{@link http://wikkawiki.org/MinusF MinusF} (code cleanup and validation)
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli} (further cleanup, i18n, replaced JS dialogs with server-generated messages)
 * @author		{@link http://wikkawiki.org/NilsLindenberg Nils Lindenberg} (possibility to restrict registration)
 *
 * @uses		Wakka::htmlspecialchars_ent()
 * 
 * @todo		use different actions for registration / login / user settings;
 * @todo		add documentation links or short explanations for each option;
 * @todo		use error handler for displaying messages and highlighting
 * 				invalid input fields;
 * @todo		remove useless redirections;
 * @todo		[accessibility] make logout independent of JavaScript
 * @todo	replace $_REQUEST with either $_GET or $_POST (or both if really
 * 			necessary) - #312  
 */

// defaults
if (!defined('PASSWORD_MIN_LENGTH')) define('PASSWORD_MIN_LENGTH', "5");
if (!defined('VALID_EMAIL_PATTERN')) define('VALID_EMAIL_PATTERN', "/^.+?\@.+?\..+$/"); //TODO: Use central regex library
if (!defined('REVISION_DISPLAY_LIMIT_MIN')) define('REVISION_DISPLAY_LIMIT_MIN', "0"); // 0 means no limit, 1 is the minimum number of revisions
if (!defined('REVISION_DISPLAY_LIMIT_MAX')) define('REVISION_DISPLAY_LIMIT_MAX', "20"); // keep this value within a reasonable limit to avoid an unnecessary long lists
if (!defined('RECENTCHANGES_DISPLAY_LIMIT_MIN')) define('RECENTCHANGES_DISPLAY_LIMIT_MIN', "0"); // 0 means no limit, 1 is the minimum number of changes
if (!defined('RECENTCHANGES_DISPLAY_LIMIT_MAX')) define('RECENTCHANGES_DISPLAY_LIMIT_MAX', "50"); // keep this value within a reasonable limit to avoid an unnecessary long list
if (!defined('INPUT_ERROR_STYLE')) define('INPUT_ERROR_STYLE', 'class="highlight"');

//initialize variables
$params = '';
$url = '';
$email = '';
$doubleclickedit = '';
$show_comments = '';
$default_comment_display = '';
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

$wikiname_expanded = '<abbr title="'.WIKINAME_LONG.'">'.WIKINAME_SHORT.'</abbr>';

//create URL
$url = $this->Href();

//Remember referring page if internal.
// - Getting correct regex to find the tag of referring page
preg_match('/^(.*)ReferrerMarker/', $this->Href('', 'ReferrerMarker'), $match);
$regex_referrer = '/^'.preg_quote($match[1], '/')."([^\\/\\?&]*)/";
if (isset($_SERVER['HTTP_REFERER']) && preg_match($regex_referrer, $_SERVER['HTTP_REFERER'], $match))
{
	if (strcasecmp($this->tag, $match[1]))
	{
		$_SESSION['go_back'] = $_SERVER['HTTP_REFERER'];
		//We save the tag of the referring page, this tag is to be shown in label <Go back to ...>. We must use a session here because if the user 
		//Refresh the page by hitting <Enter> on the address bar, the value would be lost.
		$_SESSION['go_back_tag'] = $match[1];
	}
}

// append URL params depending on rewrite_mode
$params = ($this->config['rewrite_mode'] == 1) ? '?' : '&';

// BEGIN *** Logout ***
// is user trying to log out?
if (isset($_POST['logout']) && $_POST['logout'] == LOGOUT_BUTTON)		// replaced with normal form button #353, #312
{
	$this->LogoutUser();
}
// END *** Logout ***

// BEGIN *** Usersettings ***
// user is still logged in
if ($user = $this->GetUser())
{
	// is user trying to update user settings?
	if (isset($_POST['action']) && ($_POST['action'] == 'update'))
	{
		// get POST parameters
		$email = $this->GetSafeVar('email', 'post');
		$doubleclickedit = $this->GetSafeVar('doubleclickedit', 'post');
		$show_comments = $this->GetSafeVar('show_comments', 'post');
		$default_comment_display = $this->GetSafeVar('default_comment_display', 'post');
		$revisioncount = (int) $this->GetSafeVar('revisioncount', 'post');
		$changescount = (int) $this->GetSafeVar('changescount', 'post');

		// validate form input
		switch (TRUE)
		{
			case (strlen($email) == 0): //email is empty
				$error = ERROR_EMPTY_EMAIL_ADDRESS;
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
					"default_comment_display = '".mysql_real_escape_string($default_comment_display)."', ".
					"revisioncount = '".mysql_real_escape_string($revisioncount)."', ".
					"changescount = '".mysql_real_escape_string($changescount)."' ".
					"WHERE name = '".$user['name']."' LIMIT 1");
				unset($this->specialCache['user'][strtolower($user['name'])]);  //invalidate cache if exists #368
				$this->SetUser($this->LoadUser($user["name"]));
		}
	}
	//user just logged in
	else
	{
		// get stored settings
		$email = $user['email'];
		$doubleclickedit = $user['doubleclickedit'];
		$show_comments = $user['show_comments'];
		$default_comment_display = $user['default_comment_display'];
		$revisioncount = $user['revisioncount'];
		$changescount = $user['changescount'];
	}

	// display user settings form
	echo $this->FormOpen();
?>
	<fieldset id="account"><legend><?php echo USER_ACCOUNT_LEGEND ?></legend>
	<span id="account_info">
	<?php printf(USER_LOGGED_IN_AS_CAPTION, $this->Link($user['name'])); ?>
	</span><input id="logout" name="logout" type="submit" value="<?php echo LOGOUT_BUTTON; ?>" /><!-- #353,#312-->
	<br class="clear" />
	</fieldset>	
	<fieldset id="usersettings" class="usersettings"><legend><?php echo USER_SETTINGS_LEGEND ?></legend>
<?php

	// create confirmation message if needed
	switch(TRUE)
	{
		case (isset($_SESSION['usersettings_registered']) && $_SESSION['usersettings_registered'] == 'true'):
			unset($_SESSION['usersettings_registered']);
			$success = USER_REGISTERED_SUCCESS;
			break;
		//case (isset($_GET['stored']) && $_GET['stored'] == 'true'):
		case (isset($_POST['action']) && $_POST['action'] == 'update' && !isset($error)):
			$success = USER_SETTINGS_STORED_SUCCESS;
			break;
	}

	// display error or confirmation message
	switch(TRUE)
	{
		case (isset($error)):
			echo '<em class="error">'.$error.'</em><br />'."\n";
			break;
		case (isset($success)):
			echo '<em class="success">'.$success.'</em><br />'."\n";
			break;
	}

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
			case (($update_option == 'pw') && md5($oldpass) != $user['password']): //wrong old password
				$passerror = ERROR_INVALID_OLD_PASSWORD;
				$pw_selected = 'selected="selected"';
				$password_highlight = INPUT_ERROR_STYLE;			
				break;
			case (($update_option == 'hash') && $oldpass != $user['password']): //wrong reminder (hash)
				$passerror = ERROR_INVALID_HASH;
				$hash_selected = 'selected="selected"';
				$password_highlight = INPUT_ERROR_STYLE;			
				break;
			case (strlen($password) == 0):
				$passerror = ERROR_EMPTY_NEW_PASSWORD;
				$password_highlight = INPUT_ERROR_STYLE;			
				$password_new_highlight = INPUT_ERROR_STYLE;
				break;
			case (preg_match("/ /", $password)):
				$passerror = ERROR_PASSWORD_NO_BLANK;
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
				$this->Query('UPDATE '.$this->config['table_prefix'].'users SET '."password = md5('".mysql_real_escape_string($password)."') "."WHERE name = '".$user['name']."'");
				unset($this->specialCache['user'][strtolower($name)]);  //invalidate cache if exists #368
				$user['password'] = md5($password);
				$this->SetUser($user);
				$passsuccess = USER_PASSWORD_CHANGED_SUCCESS;
		}
	}

?>
	<input type="hidden" name="action" value="update" />
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
	<fieldset><legend><?php echo DEFAULT_COMMENT_STYLE_LABEL ?></legend>
	<input id="default_comment_flat_asc" type="radio" name="default_comment_display" value="1" <?php echo ($default_comment_display==1) ? 'checked="checked"' : '' ?> /><label for="default_comment_flat_asc"><?php echo COMMENT_ASC_LABEL ?></label><br />
	<input id="default_comment_flat_desc" type="radio" name="default_comment_display" value="2" <?php echo ($default_comment_display==2) ? 'checked="checked"' : '' ?> /><label for="default_comment_flat_desc"><?php echo COMMENT_DEC_LABEL ?></label><br />
	<input id="default_comment_threaded" type="radio" name="default_comment_display" value="3" <?php echo ($default_comment_display==3) ? 'checked="checked"' : '' ?> /><label for="default_comment_threaded"><?php echo COMMENT_THREADED_LABEL ?></label><br /> 
	</fieldset>
	<br />
	<label for="revisioncount"><?php echo PAGEREVISION_LIST_LIMIT_LABEL ?></label>
	<input id="revisioncount" type="text" <?php echo $revisioncount_highlight; ?> name="revisioncount" value="<?php echo $this->htmlspecialchars_ent($revisioncount) ?>" size="40" />
	<br />
	<label for="changescount"><?php echo RECENTCHANGES_DISPLAY_LIMIT_LABEL ?></label>
	<input id="changescount" type="text" <?php echo $changescount_highlight; ?> name="changescount" value="<?php echo $this->htmlspecialchars_ent($changescount) ?>" size="40" />
	<br />
	<input id="updatesettingssubmit" type="submit" value="<?php echo UPDATE_SETTINGS_BUTTON ?>" />
	<br />
	</fieldset>
<?php	
	echo $this->FormClose(); //close user settings form

	//display password update form
	echo $this->FormOpen();
?>
	<fieldset class="usersettings" id="changepassword"><legend><?php echo CHANGE_PASSWORD_LEGEND ?></legend>
	<input type="hidden" name="action" value="changepass" />
<?php
		if (isset($passerror))
		{
			echo '<em class="error">'.$passerror.'</em><br />'."\n";
		}
		else if (isset($passsuccess))
		{
			echo '<em class="success">'.$passsuccess.'</em><br />'."\n";			
		}
?>
	<select id="update_option" name="update_option">
		<option value="pw" <?php echo $pw_selected; ?>><?php echo CURRENT_PASSWORD_OPTION; ?></option>
		<option value="hash" <?php echo $hash_selected; ?>><?php echo PASSWORD_REMINDER_OPTION; ?></option>
	</select>
	<input <?php echo $password_highlight; ?> type="password" name="oldpass" size="40" />
	<br />
	<label for="password"><?php echo NEW_PASSWORD_LABEL ?></label>
	<input id="password" <?php echo $password_new_highlight; ?> type="password" name="password" size="40" />
	<br />
	<label for="password_confirm"><?php echo NEW_PASSWORD_CONFIRM_LABEL ?></label>
	<input id="password_confirm" <?php echo $password_confirm_highlight; ?> type="password" name="password_confirm" size="40" />
	<br />
	<input id="changepasswordsubmit" type="submit" value="<?php echo CHANGE_PASSWORD_BUTTON ?>" size="40" />
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
	if (isset($_POST['logout']) && $_POST['logout'] == LOGOUT_BUTTON)
	{
		$success = USER_LOGGED_OUT_SUCCESS;
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
					$error = ERROR_INVALID_PASSWORD;
					$password_highlight = INPUT_ERROR_STYLE;
					break;
				default:
					$this->SetUser($existingUser);
					if ((isset($_SESSION['go_back'])) && (isset($_POST['do_redirect'])))
					{
						$go_back = $_SESSION['go_back'];
						unset($_SESSION['go_back']);
						unset($_SESSION['go_back_tag']);
						$this->Redirect($go_back);
					}
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
					$error = $this->Format(sprintf(ERROR_WIKINAME,'##""WikiName""##','##""'.WIKKA_SAMPLE_WIKINAME.'""##'));
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
					$error = ERROR_INVALID_INVITATION_CODE;
					$invitation_code_highlight = INPUT_ERROR_STYLE;
					break;
				default: //valid input, create user
					$this->Query("INSERT INTO ".$this->config['table_prefix']."users SET ".
						"signuptime = now(), ".
						"name = '".mysql_real_escape_string($name)."', ".
						"email = '".mysql_real_escape_string($email)."', ".
						"password = md5('".mysql_real_escape_string($_POST['password'])."')");
					unset($this->specialCache['user'][strtolower($name)]);  //invalidate cache if exists #368

					// log in
					$this->SetUser($this->LoadUser($name));
					if ((isset($_SESSION['go_back'])) && (isset($_POST['do_redirect'])))
					{
						$go_back = $_SESSION['go_back'];
						unset($_SESSION['go_back']);
						$this->Redirect($go_back);
					}
					$_SESSION['usersettings_registered'] = true;
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
			$newerror = WIKKA_ERROR_EMPTY_USERNAME;
			$username_temp_highlight = INPUT_ERROR_STYLE;
		}
		elseif (!$this->IsWikiName($name)) // check if name is WikiName style	
		{
			$newerror = ERROR_WIKINAME;
			$username_temp_highlight = INPUT_ERROR_STYLE;
		}
		elseif (!($this->LoadUser($_POST['yourname']))) //check if user exists
		{
			$newerror = ERROR_NONEXISTENT_USERNAME;
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
			echo '<em class="error">'.$error.'</em><br />'."\n";
			break;
		case (isset($success)):
			echo '<em class="success">'.$success.'</em><br />'."\n";
			break;
	}
?>
	<em class="usersettings_info"><?php echo REGISTERED_USER_LOGIN_CAPTION; ?></em>
	<br />
	<label for="name"><?php printf(WIKINAME_LABEL,$wikiname_expanded) ?></label>
	<input id="name" type="text" <?php echo $username_highlight; ?> name="name" size="40" value="<?php echo $this->GetSafeVar('name', 'post'); ?>" />
	<br />
	<label for="password"><?php printf(PASSWORD_LABEL, PASSWORD_MIN_LENGTH) ?></label>
	<input id="password" <?php echo $password_highlight; ?> type="password" name="password" size="40" />
	<br />
<?php
	if (isset($_SESSION['go_back']))
	{
		// FIXME @@@ label for a checkbox should come AFTER it, not before
	?>
	<label for="do_redirect"><?php printf(USERSETTINGS_REDIRECT_AFTER_LOGIN, $_SESSION['go_back_tag']); ?></label>
	<input type="checkbox" name="do_redirect" id="do_redirect"<?php if (isset($_POST['do_redirect']) || empty($_POST)) echo ' checked="checked"';?> />
	<br />
<?php
	}
?>
	<input id="loginsubmit" type="submit" value="<?php echo LOGIN_BUTTON ?>" size="40" />
	<br /><br />
<?php
	// END *** Login/Logout ***
	$register = $this->GetConfigValue('allow_user_registration');
	if ($register == '1' || $register == '2')
	{
?>
	<em class="usersettings_info"><?php echo NEW_USER_REGISTER_CAPTION; ?></em>
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
			$invitation_code_expanded = '<abbr title="'.INVITATION_CODE_LONG.'">'.INVITATION_CODE_SHORT.'</abbr>'
?>
	<label for="invitation_code"><?php printf(INVITATION_CODE_LABEL,$invitation_code_expanded) ?></label>
	<input id="invitation_code" type="text" <?php echo $invitation_code_highlight; ?> size="20" name="invitation_code" />
	<br />
<?php
		}
?>
	<input id="registersubmit" type="submit" value="<?php echo REGISTER_BUTTON ?>" size="40" />
	<br />
<?php
	}
	echo	'	</fieldset>'."\n";
	print($this->FormClose());
	// END *** Register ***
	print($this->FormOpen());
?>
	<fieldset id="password_forgotten" class="usersettings"><legend><?php echo RETRIEVE_PASSWORD_LEGEND ?></legend>
	<input type="hidden" name="action" value="updatepass" />
<?php   
	if (isset($newerror))
	{
		echo '<em class="error">'.$newerror.'</em><br />'."\n";
	}
	$retrieve_password_link = 'PasswordForgotten';
	$retrieve_password_caption = $this->Format(sprintf(RETRIEVE_PASSWORD_CAPTION,$retrieve_password_link));
?>
	<em class="usersettings_info"><?php echo $retrieve_password_caption ?></em>
	<br />
	<label for="yourname"><?php printf(WIKINAME_LABEL,$wikiname_expanded) ?></label>
	<input id="yourname" type="text" <?php echo $username_temp_highlight; ?> name="yourname" value="<?php echo $this->GetSafeVar('yourname', 'post'); ?>" size="40" />
	<br />
	<label for="temppassword"><?php echo TEMP_PASSWORD_LABEL ?></label>
	<input id="temppassword" type="text" <?php echo $password_temp_highlight; ?> name="temppassword" size="40" />
	<br />
	<input id="temppassloginsubmit" type="submit" value="<?php echo LOGIN_BUTTON ?>" size="40" />
	<br class="clear" />
	</fieldset>
<?php
	print($this->FormClose());
}
?>
