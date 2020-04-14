<?php

// Start session
session_set_cookie_params(0, '/');
session_name(md5('WikkaWiki'));
session_start();

include_once('setup/inc/functions.inc.php');
	
// Copy POST params to SESSION in preparation for redirect to install page
$_SESSION['post'] = array();
$_SESSION['post'] = array_merge($_SESSION['post'], $_POST);

//Override default values with posted values
if (isset($_POST['config']))
{
	/* debug */
	//print_r($_POST['config']);
	foreach($_POST['config'] as $key => $value)
	{
		$wakkaConfig[$key] = $value;
	}
}
if (!isset($wakkaConfig['dbms_password']))
{
	$wakkaConfig['dbms_password'] = '';
}

// Validate data
$error['flag'] = false;
if(isset($_SESSION['error_flag']))
{
	if (isset($_POST['config']['dbms_host']) && strlen($_POST['config']['dbms_host']) == 0)
	{
		$error['dbms_host'] = "Please fill in a valid DB host."; 
		$error['flag'] = true;
	}
	if (isset($_POST['config']['dbms_database']) && strlen($_POST['config']['dbms_database']) == 0)
	{
		$error['dbms_database'] = "Please fill in a valid database."; 
		$error['flag'] = true;
	}
	if	(isset($_POST['config']['dbms_user']) && strlen($_POST['config']['dbms_user']) == 0)
	{
		$error['dbms_user'] = "Please fill in a valid DB username."; 
		$error['flag'] = true;
	}
	if	(isset($_POST['config']['wakka_name']) && strlen($_POST['config']['wakka_name']) == 0)
	{
		$error['wakka_name'] = "Please fill in a title for your wiki. For example: <em>My Wikka website</em>"; 
		$error['flag'] = true;
	}
	if	(isset($_POST['config']['root_page']))
	{
		if  (strlen($_POST['config']['root_page']) == 0 || preg_match('/^[A-Za-z0-9]{3,}$/', $_POST['config']['root_page']) == 0)
		{
			$error['root_page'] = "Please fill a valid name for your wiki's homepage. For example: <em>start</em> or <em>HomePage</em>"; 
			$error['flag'] = true;
		}
	}
	if (isset($_POST['config']['admin_users']))
	{ 
		if (strlen($_POST['config']['admin_users']) == 0)
		{
			$error['admin_users'] = "Please fill in an admin name."; 
			$error['flag'] = true;
		}
		else if (strlen($_POST['config']['admin_users']) > 0 && preg_match('/^[A-Z][a-z]+[A-Z0-9][A-Za-z0-9]*$/', $_POST['config']['admin_users']) == 0)
		{
			$error['admin_users'] = "Admin name must be formatted as a WikiName. For example: <em>JohnSmith</em> or <em>AbC</em> or <em>Ted22</em>"; 
			$error['flag'] = true;
		}
	}
	if (isset($_POST['password']))
	{
		if (strlen($_POST['password']) == 0)
		{
			$error['password'] = "Please fill in a password.";
			$error['flag'] = true;
		}
		else if (strlen($_POST['password']) < 5)
		{
			$error['password'] = "Password must be at least five (5) characters long.";
			$error['flag'] = true;
		}
	}
	if (isset($_POST['password2']))
	{
		if (strlen($_POST['password2']) == 0)
		{
			$error['password2'] = "Please confirm your password.";
			$error['flag'] = true;
		}
		else if (strcmp($_POST['password'], $_POST['password2']) != 0)
		{
			$error['password2'] = "Passwords don't match.";
			$error['flag'] = true;
		}
	}
	if (isset($_POST['config']['admin_email']))
	{
		if (strlen($_POST['config']['admin_email']) == 0)
		{
			$error['admin_email'] = "Please fill in your email address.";
			$error['flag'] = true;
		}
		else if (preg_match("/^[A-Za-z0-9.!#$%&'*+\/=?^_`{|}~-]+@[A-Za-z0-9.-]+$/i", $_POST['config']['admin_email']) == 0)
		{
			$error['admin_email'] = "Please fill in a valid email address.";
			$error['flag'] = true;
		}
	}
}
// i18n section
if (!defined('SITE_SUFFIX_INFO')) define ('SITE_SUFFIX_INFO', 'Suffix used for cookies and part of the session name. This allows you to run multiple Wikka installations on the same server by configuring them to use different wiki prefixes.');
if (!defined('SITE_SUFFIX_LABEL')) define ('SITE_SUFFIX_LABEL', 'Your Wiki suffix:');

if (!$wakkaConfig["wakka_version"])
{
	$_SESSION['error_flag'] = $error['flag'];
} 

// Only redirect as a result of this page being POSTed!
if(isset($_SESSION['error_flag']) && false === $_SESSION['error_flag'] && isset($_POST['submit']))
{
	header("Location: ".myLocation()."?installAction=install");
}

?>
<form action="<?php echo myLocation() ?>?installAction=default" name="form1" method="post">
<table>

	<tr><td></td><td><h1>Wikka Installation</h1></td></tr>

	<?php
	if ($wakkaConfig["wakka_version"])
	{
		print("<tr><td></td><td>Your installed Wikka is reporting itself as <tt>".$wakkaConfig["wakka_version"]."</tt>. You are about to <strong>upgrade</strong> to Wikka ".WAKKA_VERSION.". Please review your configuration settings below.</td></tr>\n");
		// This needs to be set to false for redirect to install page
		$_SESSION['error_flag'] = false;
	}
	else
	{
		print("<tr><td></td><td>Since there is no existing Wikka configuration, this probably is a fresh Wikka install. You are about to install Wikka <tt>".WAKKA_VERSION."</tt>. Installing Wikka will take only a few minutes. To start the installation, please fill in the form below.</td></tr>\n");
	}
	?>

	<tr><td></td><td><span class="note">NOTE: This installer will try to write the configuration data to a file called <tt>wikka.config.php</tt>, located in your Wikka directory. In order for this to work, you must make sure the web server has write access to that file! If you can't do this, you will have to edit the file manually (the installer will tell you how). Once Wikka is correctly installed, you will be able to modify its configuration by editing this file. See the <a href="http://docs.wikkawiki.org/WikkaInstallation" target="_blank">documentation</a> for details.</span></td></tr>
	<?php if($error['flag'])
	{
	?>
	<tr><td></td><td><em class="error">Please correct the errors below to proceed with the installation.</em></td></tr>
	<?php
	}
	 if (!$wakkaConfig["wakka_version"])
 	{
	?>
	<tr><td></td><td><br /><h2>1. Database Configuration</h2></td></tr>
	<tr><td></td><td>The DB server.  Only those listed in the dropdown are supported.</td></tr>
	<tr><td align="right" nowrap="nowrap">DB server type:</td><td><?php SelectDB($wakkaConfig); ?></td></tr>
	<tr><td></td><td>The host your DB server is running on. Usually "localhost" (ie, the same machine your Wikka site is on).</td></tr>
	<?php if(isset($error['dbms_host'])) { ?>
	<tr><td></td><td><em class="error"><?php echo $error['dbms_host']; ?></em></td></tr>
	<?php } ?>
	<tr><td align="right" nowrap="nowrap">DB host:</td><td><input type="text" size="50" name="config[dbms_host]" value="<?php echo $wakkaConfig["dbms_host"] ?>" /></td></tr>
	<tr><td></td><td>The database Wikka should use. This database needs to exist already before you continue!</td></tr>
	<?php if(isset($error['dbms_database'])) { ?>
	<tr><td></td><td><em class="error"><?php echo $error['dbms_database']; ?></em></td></tr>
	<?php } ?>
	<tr><td align="right" nowrap="nowrap">Database:</td><td><input type="text" size="50" name="config[dbms_database]" value="<?php echo $wakkaConfig["dbms_database"] ?>" /></td></tr>
	<tr><td></td><td>Name and password of the DB user used to connect to your database.</td></tr>
	<?php if(isset($error['dbms_user'])) { ?>
	<tr><td></td><td><em class="error"><?php echo $error['dbms_user']; ?></em></td></tr>
	<?php } ?>
	<tr><td align="right" nowrap="nowrap">DB user name:</td><td><input type="text" size="50" name="config[dbms_user]" value="<?php echo $wakkaConfig["dbms_user"] ?>" /></td></tr>
	<tr><td align="right" nowrap="nowrap">DB password:</td><td><input type="password" size="50" name="config[dbms_password]" value="<?php echo $wakkaConfig["dbms_password"] ?>" /></td></tr>
	<tr><td></td><td>Prefix of all tables used by Wikka. This allows you to run multiple Wikka installations using the same database by configuring them to use different table prefixes.</td></tr>
	<tr><td align="right" nowrap="nowrap">Table prefix:</td><td><input type="text" size="50" name="config[table_prefix]" value="<?php echo $wakkaConfig["table_prefix"] ?>" /></td></tr>
	<?php
	 }
	?>
	<tr><td></td><td><br /><h2>2. Wiki Configuration</h2></td></tr>
	<tr><td></td><td>The name of your wiki, as it will be displayed in the title.</td></tr>
	<?php if(isset($error['wakka_name'])) { ?>
	<tr><td></td><td><em class="error"><?php echo $error['wakka_name']; ?></em></td></tr>
	<?php } ?>
	<tr><td align="right" nowrap="nowrap">Your wiki's name:</td><td><input type="text" size="50" name="config[wakka_name]" value="<?php echo $wakkaConfig["wakka_name"] ?>" /></td></tr>
	<tr><td></td><td>Your wiki's home page. It should not contain any space or special character and be at least 3 characters long. It is typically formatted as a <abbr title="A WikiName is formed by two or more capitalized words without space, e.g. HomePage">WikiName</abbr>.</td></tr>
	<?php if(isset($error['root_page'])) { ?>
	<tr><td></td><td><em class="error"><?php echo $error['root_page']; ?></em></td></tr>
	<?php } ?>
	<tr><td align="right" nowrap="nowrap">Home page:</td><td><input type="text" size="50" name="config[root_page]" value="<?php echo $wakkaConfig["root_page"] ?>" /></td></tr>

	<tr><td></td><td><?php echo SITE_SUFFIX_INFO; ?></td></tr>
	<tr><td align="right" nowrap="nowrap"><?php echo SITE_SUFFIX_LABEL; ?></td><td><input type="text" size="50" name="config[wiki_suffix]" value="<?php echo $wakkaConfig["wiki_suffix"] ?>" /></td></tr>

	<tr><td></td><td>Optional keywords/description to insert into the HTML meta headers.</td></tr>
	<tr><td align="right" nowrap="nowrap">Meta Keywords:</td><td><input type="text" size="50" name="config[meta_keywords]" value="<?php if(isset($wakkaConfig["meta_keywords"])) echo $wakkaConfig["meta_keywords"] ?>" /></td></tr>
	<tr><td align="right" nowrap="nowrap">Meta Description:</td><td><input type="text" size="50" name="config[meta_description]" value="<?php if(isset($wakkaConfig["meta_description"])) echo $wakkaConfig["meta_description"] ?>" /></td></tr>
	<tr><td></td><td>Choose the <em>look and feel</em> of your wiki (you'll be able to change this later).</td></tr>
	<tr><td align="right" nowrap="nowrap">Theme:</td><td><?php SelectTheme($wakkaConfig["theme"]); ?></td></tr>
	<tr><td align="right" nowrap="nowrap">Language pack:</td><td><?php Language_selectbox($wakkaConfig["default_lang"]); ?></td></tr>

	<?php
	 $curversion_num = ($wakkaConfig['wakka_version']) ? str_replace('.','',$wakkaConfig['wakka_version']) : 0;
	 if (!$wakkaConfig["wakka_version"])
	 {
	?>
	 <tr><td></td><td><br /><h2>3. Administrative Account Configuration</h2></td></tr>

	 <tr><td></td><td>This is the username of the person running this wiki. Later you'll be able to add other admins. The admin username should be formatted as a <abbr title="A WikiName is formed by two or more capitalized words without space, e.g. JohnDoe">WikiName</abbr>.</td></tr>
	<?php if(isset($error['admin_users'])) { ?>
	<tr><td></td><td><em class="error"><?php echo $error['admin_users']; ?></em></td></tr>
	<?php } ?>
	 <tr><td align="right" nowrap="nowrap">Admin name:</td><td><input type="text" size="50" name="config[admin_users]" value="<?php echo $wakkaConfig["admin_users"] ?>" /></td></tr>
	 <tr><td></td><td>Choose a password for the wiki administrator (5+ chars)</td></tr>
	<?php if(isset($error['password'])) { ?>
	<tr><td></td><td><em class="error"><?php echo $error['password']; ?></em></td></tr>
	<?php } ?>
	 <tr><td align="right" nowrap="nowrap">Enter password:</td><td><input type="password" size="50" name="password" value="<?php echo (isset($_POST['password']))? $_POST['password'] : ''; ?>" /></td></tr>
	<?php if(isset($error['password2'])) { ?>
	<tr><td></td><td><em class="error"><?php echo $error['password2']; ?></em></td></tr>
	<?php } ?>
	 <tr><td align="right" nowrap="nowrap">Confirm password:</td><td><input type="password" size="50" name="password2" value="<?php echo (isset($_POST['password2']))? $_POST['password2'] : ''; ?>" /></td></tr>
	 <tr><td></td><td>Administrator email.</td></tr>
	<?php if(isset($error['admin_email'])) { ?>
	<tr><td></td><td><em class="error"><?php echo $error['admin_email']; ?></em></td></tr>
	<?php } ?>
	 <tr><td align="right" nowrap="nowrap">Email:</td><td><input type="text" size="50" name="config[admin_email]" value="<?php echo $wakkaConfig["admin_email"] ?>" /></td></tr>
<?php } ?>

	<tr><td></td><td><br /><h2>4. Version update check</h2></td></tr>
	<tr><td></td><td><span class="note">It is <strong>strongly recommended</strong> that you leave this option checked if your run your wiki on the internet. Administrator(s) will be notified automatically on the wiki if a new version of WikkaWiki is available for download. 	See the <a href="http://docs.wikkawiki.org/CheckVersionActionInfo" target="_blank">documentation</a> for details. Please note that if you leave this option enabled, your installation will periodically contact a WikkaWiki server for update information.  As a result, your IP address and/or domain name may be recorded in our referrer logs.  </span></td></tr>
	<tr><td align="right" nowrap="nowrap"><label for="id_enable_version_check">Enable version checking:</label></td><td><input type="checkbox"<?php echo !isset($wakkaConfig["enable_version_check"]) || $wakkaConfig["enable_version_check"] == "1" ? ' checked="checked"' : ""; ?> name="config[enable_version_check]" value="1" id="id_enable_version_check" /></td></tr>
	<tr><td></td><td><input type="submit" name="submit" value="Continue" /></td></tr>

</table>
</form>
