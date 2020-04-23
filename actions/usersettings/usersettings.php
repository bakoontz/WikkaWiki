<?php
/**
 * Display a form to register, login and change user settings.
 *
 * @package		Actions
 * @version		$Id:usersettings.php 369 2007-03-01 14:38:59Z DarTar $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author		{@link http://wikkawiki.org/MinusF MinusF} (code cleanup and validation)
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli} (further cleanup, i18n, replaced JS dialogs with server-generated messages)
 * @author		{@link http://wikkawiki.org/NilsLindenberg Nils Lindenberg} (possibility to restrict registration)
 *
 * @uses	Wakka::htmlspecialchars_ent()
 * @uses	Wakka::loadUserData()
 * @uses	Wakka::existsUser()
 *
 * @todo	use different actions for registration / login / user settings;
 * @todo	add documentation links or short explanations for each option;
 * @todo	use error handler for displaying messages and highlighting
 * 			invalid input fields;
 * @todo	remove useless redirections;
 * @todo	[accessibility] make logout independent of JavaScript
 * @todo	complete @uses
 *
 * @todo    Wow, we really need to separate presentation from logic! And
 * the way PHP handles scope inside of functions is rather insane...
 */

//initialize variables
$params = '';
$url = '';
$email = '';
$doubleclickedit = '';
$show_comments = '';
$default_comment_display = '';
$revisioncount = 0;
$changescount = 0;
$password = '';
$oldpass = '';
$password_confirm = '';
global $pw_selected;
global $hash_selected;
global $passerror;
global $passsuccess;

$username_highlight = '';
$username_temp_highlight = '';
$password_temp_highlight = '';
$email_highlight = '';
global $password_highlight;
global $password_new_highlight;
global $password_confirm_highlight;
$revisioncount_highlight = '';
$changescount_highlight = '';
$invitation_code_highlight = '';

function outputChangePasswordHTML() {
    global $pw_selected;
    global $hash_selected;
    global $password_highlight;
    global $password_new_highlight;
    global $password_confirm_highlight;
    global $passerror;
    global $passsuccess;

    $output = '<fieldset class="usersettings" id="changepassword"><legend>';
    $output .= T_("Change your password");
    $output .= '</legend>
    <input type="hidden" name="action" value="changepass" />';
    if (isset($passerror))
    {
        $output .= '<em class="error">'.$passerror.'</em><br />'."\n";
    }
    else if (isset($passsuccess))
    {
        $output .= '<em class="success">'.$passsuccess.'</em><br />'."\n";
    }
    $output .= '<select id="update_option" name="update_option">
        <option value="pw" '.$pw_selected.'>';
    $output .= T_("Your current password");
    $output .= '</option>
        <option value="hash">';
    $output .= $hash_selected;
    $output .= T_("Password reminder");
    $output .= '</option>
    </select>
    <input ';
    $output .= $password_highlight;
    $output .= 'type="password" name="oldpass" size="40" />
    <br />
    <label for="password">';
    $output .= T_("Your new password:");
    $output .= '</label>
        <input id="password" ';
    $output .= $password_new_highlight;
    $output .= 'type="password" name="password" size="40" />
    <br />
    <label for="password_confirm">';
    $output .= T_("Confirm new password:");
    $output .= '</label>
        <input id="password_confirm"';
    $output .= $password_confirm_highlight;
    $output .= 'type="password" name="password_confirm" size="40" />
    <br />
    <input id="changepasswordsubmit" type="submit" value="';
    $output .= T_("Change password");
    $output .= '" size="40" />
    <br />
    </fieldset>';
    return $output; 
}

// BEGIN *** PASSWORD UPDATE ***
if ($this->GetSafeVar('action', 'post') == 'changepass')
{
    $user = $this->GetUser();
    if($user == NULL) {
        $user = $this->LoadUser($_SESSION['temp_username']);
    }

    // check password
    $oldpass = $this->GetSafeVar('oldpass', 'post'); //can be current password or hash sent as password reminder
    $password = $this->GetSafeVar('password', 'post');
    $password_confirm = $this->GetSafeVar('password_confirm', 'post');
    $update_option = $this->GetSafeVar('update_option', 'post');
    $authenticated = FALSE;
    if(strlen($oldpass) == 0) {
        $passerror = T_("Please fill your password or password reminder.");
        $password_highlight = 'class="highlight"';
    }
    elseif($update_option == 'pw' &&
           isset($user['password']) && 
           $user['password'] != '') {
        if(password_verify($oldpass, $user['password'])) {
            $authenticated = TRUE;
         } else {
             $passerror = T_("The old password you entered is wrong.");
             $pw_selected = 'selected="selected"';
             $password_highlight = 'class="highlight"';
         }
    }
    # MD5 deprecated as of 1.4.2
    elseif($update_option == 'pw') {
        if(md5($user['challenge'].$oldpass) == $user['md5_password']) {
            $authenticated = TRUE;
        } else {
            $passerror = T_("The old MD5 password you entered is wrong.");
            $pw_selected = 'selected="selected"';
            $password_highlight = 'class="highlight"';
        }
    }
    elseif(($update_option == 'hash') && $oldpass != $user['md5_password']) { //wrong reminder (hash)
        $passerror = T_("Sorry, you entered a wrong password reminder.");
        $hash_selected = 'selected="selected"';
        $password_highlight = 'class="highlight"';
    }
    elseif(strlen($password) == 0) {
        $passerror = T_("You must also fill in a new password.");
        $password_highlight = 'class="highlight"';
        $password_new_highlight = 'class="highlight"';
    }
    elseif(preg_match("/ /", $password)) {
        $passerror = T_("Sorry, blanks are not permitted in the password.");
        $password_highlight = 'class="highlight"';
        $password_new_highlight = 'class="highlight"';
    }
    elseif(preg_match('/\0/', $password)) {
        $passerror = T_("Sorry, null characters are not permitted in the password.");
        $password_highlight = 'class="highlight"';
        $password_new_highlight = 'class="highlight"';
    }
    elseif(strlen($password) < PASSWORD_MIN_LENGTH) {
       $passerror = sprintf(T_("Sorry, the password must contain at least %d characters."), PASSWORD_MIN_LENGTH);
       $password_highlight = 'class="highlight"';
       $password_new_highlight = 'class="highlight"';
    }
    elseif(strlen($password_confirm) == 0) {
        $passerror = T_("Please confirm your new password in order to update your account.");
        $password_highlight = 'class="highlight"';
        $password_new_highlight = 'class="highlight"';
        $password_confirm_highlight = 'class="highlight"';
    }
    elseif($password_confirm != $password) {
        $passerror = T_("Passwords don't match.");
        $password_highlight = 'class="highlight"';
        $password_new_highlight = 'class="highlight"';
        $password_confirm_highlight = 'class="highlight"';
    }

    if($authenticated) {
        # More secure PHP password hashing
        $user['password'] = password_hash($password, PASSWORD_DEFAULT);
        $this->Query("
            UPDATE ".$this->GetConfigValue('table_prefix')."users
            SET password = :password, force_password_reset=false WHERE name = :name",
            array(':password' => $user['password'],
                  ':name' => $user['name'])
            );
        $this->SetUser($user);
        $passsuccess = T_("Password successfully changed!");
    }

    if(isset($_SESSION['temp_username'])) {
        unset($_SESSION['temp_username']);
    }
}


$wikiname_expanded = '<abbr title="'.T_("A WikiName is formed by two or more capitalized words without space, e.g. JohnDoe").'">'.T_("WikiName").'</abbr>';

// Create URAuth object
include_once('libs/userregistration.class.php');
$urobj = new URAuth($this);

//create URL
$url = $this->Href();

// Remember referring page if internal.
// - Getting correct regex to find the tag of referring page
preg_match('/^(.*)ReferrerMarker/', $this->Href('', 'ReferrerMarker'), $match);	// @@@ use wikka_url here!
$regex_referrer = '@^'.preg_quote($match[1], '@').'([^\/\?&]*)@';
if (isset($_SERVER['HTTP_REFERER']) && preg_match($regex_referrer, $_SERVER['HTTP_REFERER'], $match))
{
	if (strcasecmp($this->GetPageTag(), $match[1]))
	{
		$_SESSION['go_back'] = $_SERVER['HTTP_REFERER'];
		//We save the tag of the referring page, this tag is to be shown in label <Go back to ...>. We must use a session here because if the user
		//Refresh the page by hitting <Enter> on the address bar, the value would be lost.
		$_SESSION['go_back_tag'] = $match[1];
	}
}

// append URL params depending on rewrite_mode
$params = ($this->GetConfigValue('rewrite_mode') == 1) ? '?' : '&';

// BEGIN *** LOGOUT ***
// is user trying to log out?
if (T_("Logout") == $this->GetSafeVar('logout', 'post'))	// replaced with normal form button #353, #312
{
	$this->LogoutUser();
}
// END *** LOGOUT ***

// BEGIN *** LOGOUT/USERSETTINGS/PASSWORD UPDATE ***
// user is still logged in
if ($user = $this->GetUser())
{
	// is user trying to update user settings?
	if ($this->GetSafeVar('action', 'post') == 'update')
	{
		// get POST parameters
		$email = $this->GetSafeVar('email', 'post');
		$doubleclickedit = $this->GetSafeVar('doubleclickedit', 'post');
		$show_comments = $this->GetSafeVar('show_comments', 'post');
		$default_comment_display = $this->GetSafeVar('default_comment_display', 'post');
		$revisioncount = (int) $this->GetSafeVar('revisioncount', 'post');
		$changescount = (int) $this->GetSafeVar('changescount', 'post');
		$usertheme = $this->GetSafeVar('theme', 'post');

		// validate form input
		switch (TRUE)
		{
			case (strlen($email) == 0): //email is empty
				$error = T_("Please specify an email address.");
				$email_highlight = 'class="highlight"';
				break;
			case (!preg_match(VALID_EMAIL_PATTERN, $email)): //invalid email
				$error = T_("That doesn't quite look like an email address.");
				$email_highlight = 'class="highlight"';
				break;
			case (($revisioncount < REVISION_DISPLAY_LIMIT_MIN) || ($revisioncount > REVISION_DISPLAY_LIMIT_MAX)): //invalid revision display limit
				$error = sprintf(T_("The number of page revisions should not exceed %d."), REVISION_DISPLAY_LIMIT_MAX);
				$revisioncount_highlight = 'class="highlight"';
				break;
			case (($changescount < RECENTCHANGES_DISPLAY_LIMIT_MIN) || ($changescount > RECENTCHANGES_DISPLAY_LIMIT_MAX)): //invalid recentchanges display limit
				$error = sprintf(T_("The number of recently changed pages should not exceed %d."), RECENTCHANGES_DISPLAY_LIMIT_MAX);
				$changescount_highlight = 'class="highlight"';
				break;
			// @@@ validate doubleclickedit, show-comments and (especially) default_comment_display
			default: // input is valid
				$name = $user['name'];
				$this->Query("
					UPDATE ".$this->GetConfigValue('table_prefix')."users
					SET	email = :email,
						doubleclickedit = :doubleclickedit,
						show_comments = :show_comments,
						default_comment_display = :default_comment_display,
						revisioncount = :revisioncount,
						changescount = :changescount,
						theme = :usertheme
					WHERE name = :name
					LIMIT 1",
					array(':email' => $email,
					      ':doubleclickedit' => $doubleclickedit,
						  ':show_comments' => $show_comments,
						  ':default_comment_display' => $default_comment_display,
						  ':revisioncount' => $revisioncount,
						  ':changescount' => $changescount,
						  ':usertheme' => $usertheme,
						  ':name' => $name)
					);
				$this->SetUser($this->loadUserData($user['name']));
                // Update changed settings
                $user['doubleclickedit'] = $doubleclickedit;
                $user['show_comments'] = $show_comments;
                $user['default_comment_display'] = $default_comment_display;
                $user['revisioncount'] = $revisioncount;
                $user['changescount'] = $changescount;
                $user['usertheme'] = $usertheme;
                $user['name'] = $name;
		}
	}
	// user just logged in, or just went to this page
	else
	{
		// get stored settings
		$email = $user['email'];
		$doubleclickedit = $user['doubleclickedit'];
		$show_comments = $user['show_comments'];
		$default_comment_display = $user['default_comment_display'];
		$revisioncount = $user['revisioncount'];
		$changescount = $user['changescount'];
		$usertheme = (isset($user['theme']) && $user['theme']!= '')? $user['theme'] : $this->GetConfigValue('theme');
	}

	// *** BEGIN LOGOUT/USERSETTINGS
	echo $this->FormOpen();	// open logout/usersettings form
	// *** BEGIN LOGOUT ***
?>
	<fieldset id="account"><legend><?php echo T_("Your account") ?></legend>
	<span id="account_info">
	<?php printf(T_("You are logged in as %s"), $this->Link($user['name'])); ?>
	</span><input id="logout" name="logout" type="submit" value="<?php echo T_("Logout"); ?>" />
	<br class="clear" />
	</fieldset>
<?php
	// *** END LOGOUT ***

	// *** BEGIN USERSETTINGS/PASSWORD UPDATE ***
?>

	<fieldset id="usersettings" class="usersettings"><legend><?php echo T_("Settings") ?></legend>
<?php

	// create confirmation message if needed
	switch(TRUE)
	{
		case (isset($_SESSION['usersettings_registered']) && $_SESSION['usersettings_registered'] === TRUE):
			unset($_SESSION['usersettings_registered']);
			$success = T_("You have successfully registered!");
			break;
		//case (isset($_GET['stored']) && $_GET['stored'] == 'true'):
		case ($this->GetSafeVar('action', 'post') == 'update' && !isset($error)):
			$success = T_("User settings stored!");
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


	// BEGIN *** USERSETTINGS ***
	// @@@ replace hidden "action" by name on submit button
?>
	<input type="hidden" name="action" value="update" />
	<label for="email"><?php echo T_("Your email address:") ?></label>
	<input id="email" type="text" <?php echo $email_highlight; ?> name="email" value="<?php echo $this->htmlspecialchars_ent($email) ?>" size="40" />
	<br />
	<label for="doubleclick"><?php echo T_("Doubleclick editing:") ?></label>
	<input type="hidden" name="doubleclickedit" value="N" />
	<input id="doubleclick" type="checkbox" name="doubleclickedit" value="Y" <?php echo $doubleclickedit == 'Y' ? 'checked="checked"' : '' ?> />
	<br />
	<label for="showcomments"><?php echo T_("Show comments by default:") ?></label>
	<input type="hidden" name="show_comments" value="N" />
	<input id="showcomments" type="checkbox" name="show_comments" value="Y" <?php echo $show_comments == 'Y' ? 'checked="checked"' : '' ?> />
	<fieldset><legend><?php echo T_("Comment style") ?></legend>
    <input id="default_comment_flat_asc" type="radio" name="default_comment_display" value="<?php echo COMMENT_ORDER_DATE_ASC ?>"<?php echo($default_comment_display==COMMENT_ORDER_DATE_ASC ?  'checked="checked"' : '') ?> /><label for="default_comment_flat_asc"><?php echo T_("Flat (oldest first)") ?></label><br />
    <input id="default_comment_flat_desc" type="radio" name="default_comment_display" value="<?php echo COMMENT_ORDER_DATE_DESC ?>"<?php echo($default_comment_display==COMMENT_ORDER_DATE_DESC ?  'checked="checked"' : '') ?> /><label for="default_comment_flat_desc"><?php echo T_("Flat (newest first)") ?></label><br />
    <input id="default_comment_threaded" type="radio" name="default_comment_display" value="<?php echo COMMENT_ORDER_THREADED ?>"<?php echo($default_comment_display==COMMENT_ORDER_THREADED ?  'checked="checked"' : '') ?> /><label for="default_comment_threaded"><?php echo T_("Threaded") ?></label><br />
	</fieldset>
	<br />
	<label for="revisioncount"><?php echo T_("Page revisions list limit:") ?></label>
	<input id="revisioncount" type="text" <?php echo $revisioncount_highlight; ?> name="revisioncount" value="<?php echo $this->htmlspecialchars_ent($revisioncount) ?>" size="40" />
	<br />
	<label for="changescount"><?php echo T_("RecentChanges display limit:") ?></label>
	<input id="changescount" type="text" <?php echo $changescount_highlight; ?> name="changescount" value="<?php echo $this->htmlspecialchars_ent($changescount) ?>" size="40" />
	<br />
	<label for="selecttheme"><?php echo T_("Theme:") ?></label>
	<?php $this->SelectTheme($usertheme); ?>
	<br />
	<input id="updatesettingssubmit" type="submit" value="<?php echo T_("Update Settings") ?>" />
	<br />
	</fieldset>
<?php
	// END *** USERSETTINGS ***
	echo $this->FormClose();	// close logout/usersettings form
	// END *** LOGOUT/USERSETTINGS ***


	echo $this->FormOpen();
    echo outputChangePasswordHTML();
	echo $this->FormClose();	// close password update form

	// END *** PASSWORD UPDATE ***
}
// END *** LOGOUT/USERSETTINGS/PASSWORD UPDATE ***

// BEGIN *** LOGOUT 2/LOGIN/REGISTER/PASSWORD UPDATE ***
// user is not logged in
else
{
	// BEGIN *** LOGOUT 2 ***
	if ($this->GetSafeVar('logout', 'post') == T_("Logout"))
	{
		// print confirmation message on successful logout
		$success = T_("You have successfully logged out.");
	}
	// END *** LOGOUT 2 ***

	// is user trying to log in or register?
	// BEGIN *** LOGIN/REGISTER ***
	$register = $this->GetConfigValue('allow_user_registration');
	if ($this->GetSafeVar('submit', 'post') == T_("Login"))
	{
		// BEGIN *** LOGIN ***
		// if user name already exists, check password
		#if (isset($this->GetSafeVar('name', 'post')) && $existingUser = $this->LoadUser($this->GetSafeVar('name', 'post')))
		if (isset($_POST['name']) && $existingUser = $this->loadUserData($this->GetSafeVar('name', 'post')))
		{
			// check password
			$status = $existingUser['status'];
            $authenticated = FALSE;
            if($status == 'deleted' ||
               $status == 'suspended' ||
               $status == 'banned') {
    				$error = T_("Sorry, this account has been suspended. Please contact an administrator for further details.");
            } 
            elseif(strlen($this->GetSafeVar('password','post')) == 0) {
					$error = T_("Please fill in a password.");
					$password_highlight = 'class="highlight"';
            }
            # More secure PHP hashing algorithm
            elseif((isset($existingUser['password'])) &&
                      $existingUser['password'] != '') {
                  if(password_verify($this->GetSafeVar('password', 'post'), 
                                     $existingUser['password'])) {
                      $authenticated = TRUE;
                  } else {
                      $error = T_("Sorry, you entered the wrong password.");
                      $password_highlight = 'class="highlight"';
                  }
            }
            # MD5 (deprecated as of 1.4.2)
            elseif(md5($existingUser['challenge'].$this->GetSafeVar('password', 'post')) == $existingUser['md5_password']) {
                $authenticated = TRUE;
            }
            else {
                $error = T_("Sorry, you entered the wrong password.");
                $password_highlight = 'class="highlight"';
            }

            if($authenticated === TRUE) {
                if($existingUser['force_password_reset'] == TRUE) {
                    $passerror = T_("The site admin is requesting that you reset your password.");
                    $_SESSION['temp_username'] = $existingUser['name'];
                    echo $this->FormOpen();
                    echo outputChangePasswordHTML();
                    echo $this->FormClose();
                    return;
                }
                $this->SetUser($existingUser);
                if ((isset($_SESSION['go_back'])) && (isset($_POST['do_redirect'])))
                {
                    $go_back = $_SESSION['go_back'];
                    unset($_SESSION['go_back']);
                    unset($_SESSION['go_back_tag']);
                    $this->Redirect($go_back);
                }
                else
                {
                    $this->Redirect($url);
                }
			}
		}
		else
		{
			$error = T_("Sorry, this user name doesn't exist.");
			$username_highlight = 'class="highlight"';
		}
	}

	// END *** LOGIN ***
	// BEGIN *** REGISTER ***
	if ($this->GetSafeVar('submit', 'post') == T_("Register") && $register == '1')
	{
		$name = trim($this->GetSafeVar('name', 'post'));
		$email = trim($this->GetSafeVar('email', 'post'));
		$password = $this->GetSafeVar('password', 'post');
		$confpassword = $this->GetSafeVar('confpassword', 'post');

//echo $this->GetSafeVar('name', 'post')."<br/>\n";
//echo $this->LoadUser($this->GetSafeVar('name', 'post'));
//exit;

		// validate input
		switch(TRUE)
		{
			case (FALSE===$urobj->URAuthVerify()):
				$error = T_("Registration validation failed, please try again!");
				break;
			case (isset($_POST['name']) && TRUE === $this->existsUser($this->GetSafeVar('name', 'post'))):
				$error = T_("Sorry, this user name is unavailable.");
				$username_highlight = 'class="highlight"';
				break;
			case (strlen($name) == 0):
				$error = T_("Please fill in your user name.");
				$username_highlight = 'class="highlight"';
				break;
			case (!$this->IsWikiName($name)):
				$error = $this->Format(sprintf(T_("Username must be formatted as a %s, e.g. %s."),'##""WikiName""##','##""'.T_("JohnDoe").'""##'));
				$username_highlight = 'class="highlight"';
				break;
			case ($this->ExistsPage($name)):
				$error = T_("Sorry, this name is reserved for a page. Please choose a different name.");
				$username_highlight = 'class="highlight"';
				break;
			case (strlen($password) == 0):
				$error = T_("Please fill in a password.");
				$password_highlight = 'class="highlight"';
				break;
			case (preg_match("/ /", $password)):
				$error = T_("Sorry, blanks are not permitted in the password.");
				$password_highlight = 'class="highlight"';
				break;
			case (preg_match('/\0/', $password)):
				$error = T_("Sorry, null characters are not permitted in the password.");
				$password_highlight = 'class="highlight"';
				break;
			case (strlen($password) < PASSWORD_MIN_LENGTH):
				$error = sprintf(T_("Sorry, the password must contain at least %d characters."), PASSWORD_MIN_LENGTH);
				$password_highlight = 'class="highlight"';
				break;
			case (strlen($confpassword) == 0):
				$error = T_("Please confirm your password in order to register a new account.");
				$password_highlight = 'class="highlight"';
				$password_confirm_highlight = 'class="highlight"';
				break;
			case ($confpassword != $password):
				$error = T_("Passwords don't match.");
				$password_highlight = 'class="highlight"';
				$password_confirm_highlight = 'class="highlight"';
				break;
			case (strlen($email) == 0):
				$error = T_("Please specify an email address.");
				$email_highlight = 'class="highlight"';
				$password_highlight = 'class="highlight"';
				$password_confirm_highlight = 'class="highlight"';
				break;
			case (!preg_match(VALID_EMAIL_PATTERN, $email)):
				$error = T_("That doesn't quite look like an email address.");
				$email_highlight = 'class="highlight"';
				$password_highlight = 'class="highlight"';
				$password_confirm_highlight = 'class="highlight"';
				break;
			default: //valid input, create user
				$password = $this->GetSafeVar('password', 'post');
                $password = password_hash($password, PASSWORD_DEFAULT);
				$default_comment_display = $this->GetConfigValue('default_comment_display');
				$this->Query("INSERT INTO
				".$this->GetConfigValue('table_prefix')."users ( 
					signuptime,
					name,
					email,
					default_comment_display,
					password) VALUES (now(), :name, :email,
					:default_comment_display, :password)",
					array(':name' => $name,
					      ':email' => $email,
						  ':default_comment_display' => $default_comment_display,
						  ':password' => $password));

				// log in
				#$this->SetUser($this->LoadUser($name));
				$this->SetUser($this->loadUserData($name));
				if ((isset($_SESSION['go_back'])) && (isset($_POST['do_redirect'])))
				{
					$go_back = $_SESSION['go_back'];
					unset($_SESSION['go_back']);
					unset($_SESSION['go_back_tag']);
					$this->Redirect($go_back);
				}
				$_SESSION['usersettings_registered'] = TRUE;
				$this->Redirect($url.$params);
		}
	}
	// END *** REGISTER ***

	// BEGIN *** USERSETTINGS PW ***
	elseif ($this->GetSafeVar('action', 'post') == 'updatepass')
	{
			$name = trim($this->GetSafeVar('yourname', 'post'));
		if (strlen($name) == 0)	// empty username
		{
			$newerror = T_("Please fill in your username!");
			$username_temp_highlight = 'class="highlight"';
		}
		elseif (!$this->IsWikiName($name))	// check if name is WikiName style
		{
			$newerror = T_("Username must be formatted as a %s, e.g. %s.");
			$username_temp_highlight = 'class="highlight"';
		}
		#elseif (!($this->LoadUser($this->GetSafeVar('yourname', 'post'))))	//check if user exists
		elseif (!($this->existsUser($this->GetSafeVar('yourname', 'post'))))	//check if user exists
		{
			$newerror = T_("Sorry, this user name doesn't exist.");
			$username_temp_highlight = 'class="highlight"';
		}
		#elseif ($existingUser = $this->LoadUser($this->GetSafeVar('yourname', 'post')))	// if user name already exists, check password
		elseif ($existingUser = $this->loadUserData($this->GetSafeVar('yourname', 'post')))	// if user name already exists, check password
		{
			// updatepassword
			if ($existingUser['password'] == $this->GetSafeVar('temppassword', 'post'))
			{
				$this->SetUser($existingUser, $this->GetSafeVar('remember', 'post'));
				$this->Redirect($url);
			}
			else
			{
				$newerror = T_("Sorry, you entered the wrong password.");
				$password_temp_highlight = 'class="highlight"';
			}
		}
	}
	// END *** USERSETTINGS PW ***

	// BEGIN *** LOGIN/REGISTER ***
	print($this->FormOpen());	// open login/registration form
	// @@@ replace hidden "action" by name on submit button
?>
	<fieldset id="register" class="usersettings"><legend><?php  echo ($register == '1') ? T_("Login/Register") : T_("Login"); ?></legend>
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
	<em class="usersettings_info"><?php echo T_("If you already have a login, sign in here:"); ?></em>
	<br />
	<label for="name"><?php printf(T_("Your %s:"),$wikiname_expanded) ?></label>
	<input id="name" type="text" <?php echo $username_highlight; ?> name="name" size="40" value="<?php echo $this->GetSafeVar('name', 'post'); ?>" />
	<br />
	<label for="password"><?php printf(T_("Password (%s+ chars)"), PASSWORD_MIN_LENGTH) ?></label>
	<input id="password" <?php echo $password_highlight; ?> type="password" name="password" size="40" />
	<br />
<?php
	if (isset($_SESSION['go_back']))
	{
		// FIXME @@@ label for a checkbox should come AFTER it, not before
	?>
	<label for="do_redirect"><?php printf(T_("Redirect to %s after login"), urldecode($_SESSION['go_back_tag'])); ?></label>
	<input type="checkbox" name="do_redirect" id="do_redirect"<?php if (isset($_POST['do_redirect']) || empty($_POST)) echo ' checked="checked"';?> />
	<br />
<?php
	}
?>
	<input name="submit" id="loginsubmit" type="submit" value="<?php echo T_("Login") ?>" size="40" />
	<br /><br />
<?php
	// END *** LOGIN ***

	// BEGIN *** REGISTER ***
	$register = $this->GetConfigValue('allow_user_registration');
	if ($register == '1')
	{
?>
	<em class="usersettings_info"><?php echo T_("If you are signing up as a new user:"); ?></em>
	<br />
	<?php $urobj->URAuthDisplay(); ?>
	<label for="confpassword"><?php echo T_("Confirm password:") ?></label>
	<input id="confpassword" <?php echo $password_confirm_highlight; ?> type="password" name="confpassword" size="40" />
	<br />
	<label for="email"><?php echo T_("Your email address:") ?></label>
	<input id="email" type="text" <?php echo $email_highlight; ?> name="email" size="40" value="<?php echo $email; ?>" />
	<br />
	<input name="submit" id="registersubmit" type="submit" value="<?php echo T_("Register") ?>" size="40" />
	<br />
<?php
	}
	echo '	</fieldset>'."\n";
	print($this->FormClose());	// close login/registration form
	// END *** REGISTER ***

	// BEGIN *** LOGIN PW FORGOTTEN ***
	print($this->FormOpen());	// open login pw forgotten form
	// @@@ replace hidden "action" by name on submit button
?>
	<fieldset id="password_forgotten" class="usersettings"><legend><?php echo T_("Password forgotten") ?></legend>
	<input type="hidden" name="action" value="updatepass" />
<?php
	if (isset($newerror))
	{
		echo '<em class="error">'.$newerror.'</em><br />'."\n";
	}
	$retrieve_password_link = 'PasswordForgotten';
	$retrieve_password_caption = sprintf(T_("Log in with your <a href=\"%s\">password reminder</a>:"),$this->Href('', $retrieve_password_link));
?>
	<em class="usersettings_info"><?php echo $retrieve_password_caption ?></em>
	<br />
	<label for="yourname"><?php printf(T_("Your %s:"),$wikiname_expanded) ?></label>
	<input id="yourname" type="text" <?php echo $username_temp_highlight; ?> name="yourname" value="<?php echo $this->GetSafeVar('yourname', 'post'); ?>" size="40" />
	<br />
	<label for="temppassword"><?php echo T_("Password reminder:") ?></label>
	<input id="temppassword" type="text" <?php echo $password_temp_highlight; ?> name="temppassword" size="40" />
	<br />
	<input id="temppassloginsubmit" type="submit" value="<?php echo T_("Login") ?>" size="40" />
	<br class="clear" />
	</fieldset>
<?php
	print($this->FormClose());	// close login pw forgotten form
	// END *** LOGIN PW FORGOTTEN ***
}
// END *** LOGOUT 2/LOGIN/REGISTER/PASSWORD UPDATE ***

?>
