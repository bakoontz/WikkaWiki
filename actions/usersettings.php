<?php
/**
 * Display a form to register, login and change user settings.
 *
 * @package		Actions
 * @name			UserSettings
 *
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli} (first overhaul, i18n)
 * @author		{@link http://wikkawiki.org/MinusF MinusF} (code cleanup and validation)
 * @since		Wikka 1.1.6.2
 *
 * @input		none
 * @todo			-different actions for registration / login / user settings
 */

// defaults
define('PASSWORD_MIN_LENGTH', "5");
define('VALID_EMAIL_PATTERN', "/^.+?\@.+?\..+$/"); //TODO: Use central regex library

// i18n strings
define('USER_LOGGED_OUT', "You are now logged out.");
define('USER_SETTINGS_STORED', "User settings stored!");
define('ERROR_NO_BLANK', "Sorry, blanks are not permitted in the password.");
define('ERROR_PASSWORD_TOO_SHORT', "Sorry, the password must contain at least %s characters.");
define('PASSWORD_CHANGED', "Password changed!");
define('ERROR_OLD_PASSWORD_WRONG', "The old password you entered is wrong.");
define('USER_EMAIL_LABEL', "Your email address:");
define('DOUBLECLICK_LABEL', "Doubleclick Editing:");
define('SHOW_COMMENTS_LABEL', "Show comments by default:");
define('RECENTCHANGES_DISPLAY_LIMIT_LABEL', "RecentChanges display limit:");
define('PAGEREVISION_LIST_LIMIT_LABEL', "Page revisions list limit:");
define('UPDATE_SETTINGS_INPUT', "Update Settings");
define('CHANGE_PASSWORD_LABEL', "Change your password:");
define('CURRENT_PASSWORD_LABEL', "Your current password:");
define('NEW_PASSWORD_LABEL', "Your new password:");
define('CHANGE_BUTTON_LABEL', "Change");
define('REGISTER_BUTTON_LABEL', "Register");
define('QUICK_LINKS', "See a list of pages you own (MyPages) and pages you've edited (MyChanges).");
define('ERROR_WRONG_PASSWORD', "Sorry, you entered the wrong password.");
define('ERROR_EMPTY_USERNAME', "Please fill in your user name.");
define('ERROR_RESERVED_PAGENAME', "Sorry, this name is reserved for a page. Please choose a different name.");
define('ERROR_WIKINAME', "User name must be ##\"\"WikiName\"\"## formatted, e.g. ##\"\"JohnDoe\"\"##.");
define('ERROR_EMPTY_PASSWORD', "Please fill in a password.");
define('ERROR_EMPTY_CONFIRMATION_PASSWORD', "You must confirm your password to register a new account.");
define('ERROR_PASSWORD_MATCH', "Passwords didn't match.");
define('ERROR_EMAIL_ADDRESS_REQUIRED', "You must specify an email address.");
define('ERROR_INVALID_EMAIL_ADDRESS', "That doesn't quite look like an email address.");
define('REGISTERED_USER_LOGIN_LABEL', "If you're already a registered user, log in here!");
define('REGISTER_HEADING', "===Login/Register===");
define('WIKINAME_LABEL', "Your <abbr title=\"A WikiName is formed by two or more capitalized words without space, e.g. JohnDoe\">WikiName</abbr>:");
define('PASSWORD_LABEL', "Password (%s+ chars):");
define('LOGIN_BUTTON_LABEL', "Login");
define('NEW_USER_REGISTER_LABEL', "Stuff you only need to fill in when you're logging in for the first time (and thus signing up as a new user on this site).");
define('CONFIRM_PASSWORD_LABEL', "Confirm password:");
define('RETRIEVE_PASSWORD_HEADING', "===Forgot your password?===");
define('RETRIEVE_PASSWORD_MESSAGE', "Log in here with the temporary password. --- If you need a temporary password, click [[PasswordForgotten here]].");
define('TEMP_PASSWORD_LABEL', "Your temp password:");

// is user logging out?
if (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'logout'))
{
	$this->LogoutUser();
	$this->Redirect($this->href(), USER_LOGGED_OUT);
}
else if ($user = $this->GetUser())
{
	
	// is user trying to update?
	if (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'update'))
	{
		$this->Query('UPDATE '.$this->config['table_prefix'].'users SET '.
			"email = '".mysql_real_escape_string($_POST['email'])."', ".
			"doubleclickedit = '".mysql_real_escape_string($_POST['doubleclickedit'])."', ".
			"show_comments = '".mysql_real_escape_string($_POST['show_comments'])."', ".
			"revisioncount = '".mysql_real_escape_string($_POST['revisioncount'])."', ".
			"changescount = '".mysql_real_escape_string($_POST['changescount'])."' ".
			"WHERE name = '".$user['name']."' LIMIT 1");
		$this->SetUser($this->LoadUser($user["name"]));
		
		// forward
		$this->Redirect($this->href(), USER_SETTINGS_STORED);
	}
	
	if (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'changepass'))
	{
		// check password
		$password = $_POST['password'];			
		if (preg_match("/ /", $password))
		{
			$passerror = ERROR_NO_BLANK;
		}
		else if (strlen($password) < PASSWORD_MIN_LENGTH)
		{
			$passerror = sprintf(ERROR_PASSWORD_TOO_SHORT, PASSWORD_MIN_LENGTH);
		}
		else if (($user['password'] == md5($_POST['oldpass'])) || ($user['password'] == $_POST['oldpass']))
		{
			$this->Query('UPDATE '.$this->config['table_prefix'].'users set '."password = md5('".mysql_real_escape_string($password)."') "."WHERE name = '".$user['name']."'");
			$user['password'] = md5($password);
			$this->SetUser($user);
			$this->Redirect($this->href(), PASSWORD_CHANGED);
		}
		else
		{
			$passerror = ERROR_OLD_PASSWORD_WRONG;
		}
	}

	// user is logged in; display config form
	print($this->FormOpen());
?>
	<input type="hidden" name="action" value="update" />
	<table class="usersettings">
		<tr>
			<td>&nbsp;</td>
			<td>Hello, <?php echo $this->Link($user['name']) ?>!</td>
		</tr>
		<tr>
			<td align="right"><?php echo USER_EMAIL_LABEL ?></td>
			<td><input name="email" value="<?php echo $this->htmlspecialchars_ent($user['email']) ?>" size="40" /></td>
		</tr>
		<tr>
			<td align="right"><?php echo DOUBLECLICK_LABEL ?></td>
			<td><input type="hidden" name="doubleclickedit" value="N" /><input type="checkbox" name="doubleclickedit" value="Y" <?php echo $user['doubleclickedit'] == 'Y' ? 'checked="checked"' : '' ?> /></td>
		</tr>
		<tr>
			<td align="right"><?php echo SHOW_COMMENTS_LABEL ?></td>
			<td><input type="hidden" name="show_comments" value="N" /><input type="checkbox" name="show_comments" value="Y" <?php echo $user["show_comments"] == "Y" ? "checked=\"checked\"" : "" ?> /></td>
		</tr>
		<tr>
			<td align="right"><?php echo RECENTCHANGES_DISPLAY_LIMIT_LABEL ?></td>
			<td><input name="changescount" value="<?php echo $this->htmlspecialchars_ent($user['changescount']) ?>" size="40" /></td>
		</tr>
		<tr>
			<td align="right"><?php echo PAGEREVISION_LIST_LIMIT_LABEL ?></td>
			<td><input name="revisioncount" value="<?php echo $this->htmlspecialchars_ent($user['revisioncount']) ?>" size="40" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" value="<?php echo UPDATE_SETTINGS_INPUT ?>" /> <input type="button" value="Logout" onclick="document.location='<?php echo $this->href('', '', 'action=logout'); ?>'" /></td>
		</tr>
	</table>
<?php
	print($this->FormClose());

	print($this->FormOpen());
?>
	<input type="hidden" name="action" value="changepass" />
	<table class="usersettings">
		<tr>
			<td align="left"><b><?php echo CHANGE_PASSWORD_LABEL ?></b></td>
			<td><br /><br />&nbsp;</td>
		</tr>
<?php
		if (isset($passerror))
		{
			print('<tr><td></td><td><em class="error">'.$this->Format($passerror).'</em></td></tr>'."\n");
		}
?>
		<tr>
			<td align="left"><?php echo CURRENT_PASSWORD_LABEL ?></td>
			<td><input type="password" name="oldpass" size="40" /></td>
		</tr>
		<tr>
			<td align="left"><?php echo NEW_PASSWORD_LABEL ?></td>
			<td><input type="password" name="password" size="40" /></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="<?php echo CHANGE_BUTTON_LABEL ?>" size="40" /></td>
		</tr>
	</table>
	<br />
<?php
	echo $this->Format(QUICK_LINKS);
	print($this->FormClose());
}
else // user is not logged in
{
	// is user trying to log in or register?
	if (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'login'))
	{
		// if user name already exists, check password
		if ($existingUser = $this->LoadUser($_POST['name']))
		{
			// check password
			if ($existingUser['password'] == md5($_POST['password']))
			{
				$this->SetUser($existingUser);
				$this->Redirect($this->href());
			}
			else
			{
				$error = ERROR_WRONG_PASSWORD;
			}
		}
		else // otherwise, create new account
		{
			$name = trim($_POST['name']);
			$email = trim($_POST['email']);
			$password = $_POST['password'];
			$confpassword = $_POST['confpassword'];

			// validate input
			switch(TRUE){
			case (strlen($name) == 0):
				$error = ERROR_EMPTY_USERNAME;
				break;
			case (!$this->IsWikiName($name)):
				$error = ERROR_WIKINAME;
				break;
			case ($this->ExistsPage($name)):
				$error = ERROR_RESERVED_PAGENAME;
				break;
			case (strlen($password) == 0):
				$error = ERROR_EMPTY_PASSWORD;
				break;
			case (preg_match("/ /", $password)):
				$error = ERROR_NO_BLANK;
				break;
			case (strlen($password) < PASSWORD_MIN_LENGTH):
				$error = sprintf(ERROR_PASSWORD_TOO_SHORT, PASSWORD_MIN_LENGTH);
				break;
			case (strlen($confpassword)==0):
				$error = ERROR_EMPTY_CONFIRMATION_PASSWORD;
				break;
			case ($confpassword != $password):
				$error = ERROR_PASSWORD_MATCH;
				break;
			case (!isset($email)):
				$error = ERROR_EMAIL_ADDRESS_REQUIRED;
				break;
			case (!preg_match(VALID_EMAIL_PATTERN, $email)):
				$error = ERROR_INVALID_EMAIL_ADDRESS;
				break;
			default: //valid input, create user
				$this->Query("INSERT INTO ".$this->config['table_prefix']."users SET ".
					"signuptime = now(), ".
					"name = '".mysql_real_escape_string($name)."', ".
					"email = '".mysql_real_escape_string($email)."', ".
					"password = md5('".mysql_real_escape_string($_POST['password'])."')");

				// log in
				$this->SetUser($this->LoadUser($name));
				$this->Redirect($this->href());
			}
		}
	}
	elseif  (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'updatepass'))
	{
		// check if name is WikiName style
        $name = trim($_POST['yourname']);
		if (!$this->IsWikiName($name))
		{
			$newerror = ERROR_WIKINAME;
		}
		// if user name already exists, check password
		elseif ($existingUser = $this->LoadUser($_POST['yourname']))   
		{
			// updatepassword
			if ($existingUser['password'] == $_POST['temppassword'])
			{
				$this->SetUser($existingUser, $_POST["remember"]);
				$this->Redirect($this->href());
			}
			else
			{
				$newerror = ERROR_WRONG_PASSWORD;
			}
		}
	}

	print($this->FormOpen());
?>
	<input type="hidden" name="action" value="login" />
	<table class="usersettings">
   	<tr>
   		<td colspan="2"><?php echo $this->Format(REGISTER_HEADING) ?></td>
   		<td>&nbsp;</td>
   	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><?php echo $this->Format(REGISTERED_USER_LOGIN_LABEL); ?></td>
	</tr>
<?php
	if (isset($error))
	{
		print('<tr><td></td><td><em class="error">'.$this->Format($error).'</em></td></tr>'."\n");
	}
?>
	<tr>
		<td align="right"><?php echo WIKINAME_LABEL ?></td>
		<td><input name="name" size="40" value="<?php if (isset($name)){ echo $name; }?>" /></td>
	</tr>
	<tr>
		<td align="right"><?php echo sprintf(PASSWORD_LABEL, PASSWORD_MIN_LENGTH) ?></td>
		<td><input type="password" name="password" size="40" /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" value="<?php echo LOGIN_BUTTON_LABEL ?>" size="40" /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td width="500"><?php echo $this->Format(NEW_USER_REGISTER_LABEL); ?></td>
	</tr>
	<tr>
		<td align="right"><?php echo CONFIRM_PASSWORD_LABEL ?></td>
		<td><input type="password" name="confpassword" size="40" /></td>
	</tr>
	<tr>
		<td align="right"><?php echo USER_EMAIL_LABEL ?></td>
		<td><input name="email" size="40" value="<?php if (isset($email)){ echo $email; }?>" /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" value="<?php echo REGISTER_BUTTON_LABEL ?>" size="40" /></td>
	</tr>
	</table>
<?php
	print($this->FormClose());
	print($this->FormOpen());
?>
	<input type="hidden" name="action" value="updatepass" />
	<table class="usersettings">
	<tr>
		<td colspan="2"><br /><hr /><?php echo $this->Format(RETRIEVE_PASSWORD_HEADING) ?></td><td></td>
	</tr>
	<tr>
		<td align="left"></td>
		<td><?php echo $this->Format(RETRIEVE_PASSWORD_MESSAGE) ?></td>
	</tr>
<?php   
	if (isset($newerror))
	{
		print('<tr><td></td><td><em class="error">'.$this->Format($newerror).'</em></td></tr>'."\n");
	}
?>
	<tr>
		<td align="right"><?php echo WIKINAME_LABEL ?></td>
		<td><input name="yourname" value="<?php if (isset($_POST['yourname'])){ echo $_POST["yourname"]; }?>" size="40" /></td>
	</tr>
	<tr>
		<td align="right"><?php echo TEMP_PASSWORD_LABEL ?></td>
		<td><input name="temppassword" size="40" /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" value="<?php echo LOGIN_BUTTON_LABEL ?>" size="40" /></td>
	</tr>
   </table>
<?php
	print($this->FormClose());
}
?>