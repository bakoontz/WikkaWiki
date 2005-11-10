<?php

if (!$wakkaConfig["wakka_version"])
{
?>
<script language="JavaScript">
function check() {
var f = document.forms.form1;
var re;
re = new RegExp("^[A-Z][a-z]+[A-Z0-9][A-Za-z0-9]*$");
if (f.elements["config[admin_users]"].value.search(re)==-1) {
 alert("Admin name must be a WikiName. This means it must start with a capital letter, then have some lowercase letters, then have an uppercase letter.\nExamples: JohnSmith or JsnX");
 return false;
}
if (f.elements["password"].value.length<5) {
 alert("Password is too short.  It must be at least five (5) characters long.");
 return false;
}
if (f.elements["password"].value!=f.elements["password2"].value) {
 alert("Passwords don't match.");
 return false;
}
re = new RegExp("[a-z]+@[a-z]+\.[a-z]+", "i");
if (f.elements["config[admin_email]"].value.search(re)==-1) {
 alert("Email address appears incorrect.");
 return false;
}
return true;
}
</script>
<?php } else {?>
<script language="JavaScript">
function check() {
 return true;
}
</script>
<?php } ?>
<form action="<?php echo myLocation() ?>?installAction=install" name="form1" method="POST">
<table>

	<tr><td></td><td><strong>Wikka Installation</strong></td></tr>

	<?php
	if ($wakkaConfig["wakka_version"])
	{
		print("<tr><td></td><td>Your installed Wikka is reporting itself as ".$wakkaConfig["wakka_version"].". You are about to <strong>upgrade</strong> to Wikka ".WAKKA_VERSION.". Please review your configuration settings below.</td></tr>\n");
	}
	else
	{
		print("<tr><td></td><td>Since there is no existing Wikka configuration, this probably is a fresh Wikka install. You are about to install Wikka ".WAKKA_VERSION.". Please configure your Wikka site using the form below.</td></tr>\n");
	}
	?>

	<tr><td></td><td><br />NOTE: This installer will try to write the configuration data to the file <tt>wikka.config.php</tt>, located in your Wikka directory. In order for this to work, you must make sure the web server has write access to that file! If you can't do this, you will have to edit the file manually (the installer will tell you how).<br /><br />See <a href="http://wikka.jsnx.com/WikkaInstallation" target="_blank">Wikka:WakkaInstallation</a> for details.</td></tr>

	<?php
	 if (!$wakkaConfig["wakka_version"])
 	{
	?>
	<tr><td></td><td><br /><strong>Database Configuration</strong></td></tr>
	<tr><td></td><td>The host your MySQL server is running on. Usually "localhost" (ie, the same machine your Wikka site is on).</td></tr>
	<tr><td align="right" nowrap>MySQL host:</td><td><input type="text" size="50" name="config[mysql_host]" value="<?php echo $wakkaConfig["mysql_host"] ?>" /></td></tr>
	<tr><td></td><td>The MySQL database Wikka should use. This database needs to exist already before you continue!</td></tr>
	<tr><td align="right" nowrap>MySQL database:</td><td><input type="text" size="50" name="config[mysql_database]" value="<?php echo $wakkaConfig["mysql_database"] ?>" /></td></tr>
	<tr><td></td><td>Name and password of the MySQL user used to connect to your database.</td></tr>
	<tr><td align="right" nowrap>MySQL user name:</td><td><input type="text" size="50" name="config[mysql_user]" value="<?php echo $wakkaConfig["mysql_user"] ?>" /></td></tr>
	<tr><td align="right" nowrap>MySQL password:</td><td><input type="password" size="50" name="config[mysql_password]" value="<?php echo $wakkaConfig["mysql_password"] ?>" /></td></tr>
	<tr><td></td><td>Prefix of all tables used by Wikka. This allows you to run multiple Wikka installations using the same MySQL database by configuring them to use different table prefixes.</td></tr>
	<tr><td align="right" nowrap>Table prefix:</td><td><input type="text" size="50" name="config[table_prefix]" value="<?php echo $wakkaConfig["table_prefix"] ?>" /></td></tr>
	<?php
	 }
	?>
	<tr><td></td><td><br /><strong>Wikka Site Configuration</strong></td></tr>

	<tr><td></td><td>The name of your Wikka site. It usually is a WikiName and looks SomethingLikeThis.</td></tr>
	<tr><td align="right" nowrap>Your Wikka's name:</td><td><input type="text" size="50" name="config[wakka_name]" value="<?php echo $wakkaConfig["wakka_name"] ?>" /></td></tr>

	<tr><td></td><td>Your Wikka site's home page. Should be a WikiName.</td></tr>
	<tr><td align="right" nowrap>Home page:</td><td><input type="text" size="50" name="config[root_page]" value="<?php echo $wakkaConfig["root_page"] ?>" /></td></tr>

	<tr><td></td><td>META Keywords/Description that get inserted into the HTML headers.</td></tr>
	<tr><td align="right" nowrap>Meta Keywords:</td><td><input type="text" size="50" name="config[meta_keywords]" value="<?php echo $wakkaConfig["meta_keywords"] ?>" /></td></tr>
	<tr><td align="right" nowrap>Meta Description:</td><td><input type="text" size="50" name="config[meta_description]" value="<?php echo $wakkaConfig["meta_description"] ?>" /></td></tr>

	<?php
	 $curversion_num = ($wakkaConfig['wakka_version']) ? str_replace('.','',$wakkaConfig['wakka_version']) : 0;
	 if ((int)$curversion_num < 1160)		# only for new installation or if previous version is lower than 1.1.6.0
	 {
	?>
	<tr><td></td><td><br /><strong>GeSHi Configuration</strong></td></tr>
	<tr><td></td><td>GeSHi comes bundled with Wikka to provide syntax highlighting for code. If you already have GeSHi installed and would like to use that installation instead of the Wikka-bundled one, you may change the paths below.</td></tr>
	<tr><td align="right" nowrap>GeSHi path:</td><td><input type="text" size="50" name="config[geshi_path]" value="<?php echo $wakkaConfig['geshi_path'] ?>" /></td></tr>
	<tr><td align="right" nowrap>GeSHi language files path:</td><td><input type="text" size="50" name="config[geshi_languages_path]" value="<?php echo $wakkaConfig['geshi_languages_path'] ?>" /></td></tr>
	<tr><td></td><td>Wikka provides some basic GeSHi configuration. You may change the default parameters below.<br /></td></tr>
	<tr><td></td><td>GeSHi can wrap a code block in either a div tag (default) or a pre tag (simpler markup but won't allow line wrapping).</td></tr>
	<tr><td align="right" nowrap>Code wrapper (div or pre):</td><td><input type="text" size="50" name="config[geshi_header]" value="<?php echo $wakkaConfig['geshi_header'] ?>" /></td></tr>
	<tr><td></td><td>GeSHi can add line numbers to code; if you enable this, users can "turn on" line numbers by setting a start line number.</td></tr>
	<tr><td align="right">Disable line numbers (0), or enable normal (1) or fancy (2) line numbers:</td><td><input type="text" size="50" name="config[geshi_line_numbers]" value="<?php echo $wakkaConfig['geshi_line_numbers'] ?>" /></td></tr>
	<tr><td></td><td>GeSHi assumes a tab width of 8 positions; for code, 4 is more usual though. You can define the tab width to be used below.</td></tr>
	<tr><td align="right" nowrap>Tab width:</td><td><input type="text" size="50" name="config[geshi_tab width]" value="<?php echo $wakkaConfig['geshi_tab_width'] ?>" /></td></tr>
	<?php
	 }
	?>

	<?php
	 if (!$wakkaConfig["wakka_version"])
	 {
	?>
	 <tr><td></td><td><br /><strong>Administrative account configuration</strong></td></tr>

	 <tr><td></td><td>Enter admin username. Should be a WikiName.</td></tr>
	 <tr><td align="right" nowrap>Admin name:</td><td><input type="text" size="50" name="config[admin_users]" value="<?php echo $wakkaConfig["admin_users"] ?>" /></td></tr>

	 <tr><td></td><td>Choose a password for administrator (5+ chars)</td></tr>
	 <tr><td align="right" nowrap>Enter password:</td><td><input type="password" size="50" name="password" value="" /></td></tr>
	 <tr><td align="right" nowrap>Repeat password:</td><td><input type="password" size="50" name="password2" value="" /></td></tr>

	 <tr><td></td><td>Administrator email.</td></tr>
	 <tr><td align="right" nowrap>Email:</td><td><input type="text" size="50" name="config[admin_email]" value="<?php echo $wakkaConfig["admin_email"] ?>" /></td></tr>
	<tr><td></td><td><br /><strong>Wikka URL Configuration</strong><?php echo $wakkaConfig["wakka_version"] ? "" : "<br />Since this is a new installation, the installer tried to guess the proper values. Change them only if you know what you're doing!" ?></td></tr>

	<tr><td></td><td>Your Wikka site's base URL. Page names get appended to it, so it should include the "?wakka=" parameter stuff if the funky URL rewriting stuff doesn't work on your server.</td></tr>
	<tr><td align="right" nowrap>Base URL:</td><td><input type="text" size="50" name="config[base_url]" value="<?php echo $wakkaConfig["base_url"] ?>" /></td></tr>

	<tr><td></td><td>Rewrite mode should be enabled if you are using Wikka with URL rewriting.</td></tr>
	<tr><td align="right" nowrap>Rewrite Mode:</td><td><input type="hidden" name="config[rewrite_mode]" value="0"><input type="checkbox" name="config[rewrite_mode]" value="1" <?php echo $wakkaConfig["rewrite_mode"] ? "checked" : "" ?> /> Enabled</td></tr>

	<?php
	 }
	?>

	<tr><td></td><td><input type="submit" value="Continue" onclick="return check();" /></td></tr>

</table>
</form>