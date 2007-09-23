<?php

// i18n section
if (!defined('SITE_SUFFIX_INFO')) define ('SITE_SUFFIX_INFO', 'Suffix used for cookies and part of the session name. This allows you to run multiple Wikka installations on the same server by configuring them to use different wiki prefixes.');
if (!defined('SITE_SUFFIX_LABEL')) define ('SITE_SUFFIX_LABEL', 'Your Wiki suffix:');

if (!$wakkaConfig["wakka_version"])
{
?>
<script type="text/javascript">
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
<script  type="text/javascript">
function check() {
 return true;
}
</script>
<?php } ?>
<form action="<?php echo myLocation() ?>?installAction=install" name="form1" method="post">
<table>

	<tr><td></td><td><h1>Wikka Installation</h1></td></tr>

	<?php
	if ($wakkaConfig["wakka_version"])
	{
		print("<tr><td></td><td>Your installed Wikka is reporting itself as <tt>".$wakkaConfig["wakka_version"]."</tt>. You are about to <strong>upgrade</strong> to Wikka ".WAKKA_VERSION.". Please review your configuration settings below.</td></tr>\n");
	}
	else
	{
		print("<tr><td></td><td>Since there is no existing Wikka configuration, this probably is a fresh Wikka install. You are about to install Wikka <tt>".WAKKA_VERSION."</tt>. Installing Wikka will take only a few minutes. To start the installation, please fill in the form below.</td></tr>\n");
	}
	?>

	<tr><td></td><td><span class="note">NOTE: This installer will try to write the configuration data to a file called <tt>wikka.config.php</tt>, located in your Wikka directory. In order for this to work, you must make sure the web server has write access to that file! If you can't do this, you will have to edit the file manually (the installer will tell you how). Once Wikka is correctly installed, you will be able to modify its configuration by editing this file.</span>See <a href="http://wikkawiki.org/WikkaInstallation" target="_blank">Wikka:WikkaInstallation</a> for details.</td></tr>

	<?php
	 if (!$wakkaConfig["wakka_version"])
 	{
	?>
	<tr><td></td><td><br /><h2>Database Configuration</h2></td></tr>
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
	<tr><td></td><td><br /><h2>Wikka Site Configuration</h2></td></tr>
	<tr><td></td><td>The name of your Wikka site, as it will be displayed in the title.</td></tr>
	<tr><td align="right" nowrap>Your Wikka site's name:</td><td><input type="text" size="50" name="config[wakka_name]" value="<?php echo $wakkaConfig["wakka_name"] ?>" /></td></tr>

	<tr><td></td><td>Your Wikka site's home page. Should be formatted as a <abbr title="A WikiName is formed by two or more capitalized words without space, e.g. JohnDoe">WikiName</abbr>.</td></tr>
	<tr><td align="right" nowrap>Home page:</td><td><input type="text" size="50" name="config[root_page]" value="<?php echo $wakkaConfig["root_page"] ?>" /></td></tr>

	<tr><td></td><td><?php echo SITE_SUFFIX_INFO; ?></td></tr>
	<tr><td align="right" nowrap><?php echo SITE_SUFFIX_LABEL; ?></td><td><input type="text" size="50" name="config[wiki_suffix]" value="<?php echo $wakkaConfig["wiki_suffix"] ?>" /></td></tr>

	<tr><td></td><td>META Keywords/Description that get inserted into the HTML headers.</td></tr>
	<tr><td align="right" nowrap>Meta Keywords:</td><td><input type="text" size="50" name="config[meta_keywords]" value="<?php echo $wakkaConfig["meta_keywords"] ?>" /></td></tr>
	<tr><td align="right" nowrap>Meta Description:</td><td><input type="text" size="50" name="config[meta_description]" value="<?php echo $wakkaConfig["meta_description"] ?>" /></td></tr>

	<?php
	 $curversion_num = ($wakkaConfig['wakka_version']) ? str_replace('.','',$wakkaConfig['wakka_version']) : 0;
	 if ((int)$curversion_num < 1160)		# only for new installation or if previous version is lower than 1.1.6.0
	 { //TODO move GeSHi configuration to another screen and make it optional
	?>
	<tr><td></td><td><br /><h2>Syntax Highlighting</h2></td></tr>
	<tr><td></td><td><span class="note">If you are not interested in changing the default settings for syntax highlighting you can skip this section.</span>GeSHi comes bundled with Wikka to provide syntax highlighting for code. If you already have GeSHi installed and would like to use that installation instead of the Wikka-bundled one, you may change the paths below.</td></tr>
	<tr><td align="right" nowrap>GeSHi path:</td><td><input type="text" size="50" name="config[geshi_path]" value="<?php echo $wakkaConfig['geshi_path'] ?>" /></td></tr>
	<tr><td align="right" nowrap>GeSHi language files path:</td><td><input type="text" size="50" name="config[geshi_languages_path]" value="<?php echo $wakkaConfig['geshi_languages_path'] ?>" /></td></tr>
	<tr><td></td><td>Wikka provides some basic GeSHi configuration. You may change the default parameters below.<br /></td></tr>
	<tr><td></td><td>GeSHi can wrap a code block in either a div tag (default) or a pre tag (simpler markup but won't allow line wrapping).</td></tr>
	<tr><td align="right" nowrap>Code wrapper (div or pre):</td><td><input type="text" size="50" name="config[geshi_header]" value="<?php echo $wakkaConfig['geshi_header'] ?>" /></td></tr>
	<tr><td></td><td>GeSHi can add line numbers to code; if you enable this, users can "turn on" line numbers by setting a start line number.</td></tr>
	<tr><td align="right">Disable line numbers (0), or enable normal (1) or fancy (2) line numbers:</td><td><input type="text" size="50" name="config[geshi_line_numbers]" value="<?php echo $wakkaConfig['geshi_line_numbers'] ?>" /></td></tr>
	<tr><td></td><td>GeSHi assumes a tab width of 8 positions; for code, 4 is more usual though. You can define the tab width to be used below.</td></tr>
	<tr><td align="right" nowrap>Tab width:</td><td><input type="text" size="50" name="config[geshi_tab_width]" value="<?php echo $wakkaConfig['geshi_tab_width'] ?>" /></td></tr>
	<?php
	 }
	?>

	<?php
	 if (!$wakkaConfig["wakka_version"])
	 {
	?>
	 <tr><td></td><td><br /><h2>Administrative Account Configuration</h2></td></tr>

	 <tr><td></td><td>This is the username of the person running this wiki. Later you'll be able to add other admins. The admin username should be formatted as a <abbr title="A WikiName is formed by two or more capitalized words without space, e.g. JohnDoe">WikiName</abbr>.</td></tr>
	 <tr><td align="right" nowrap>Admin name:</td><td><input type="text" size="50" name="config[admin_users]" value="<?php echo $wakkaConfig["admin_users"] ?>" /></td></tr>

	 <tr><td></td><td>Choose a password for administrator (5+ chars)</td></tr>
	 <tr><td align="right" nowrap>Enter password:</td><td><input type="password" size="50" name="password" value="" /></td></tr>
	 <tr><td align="right" nowrap>Confirm password:</td><td><input type="password" size="50" name="password2" value="" /></td></tr>
	 <tr><td></td><td>Administrator email.</td></tr>
	 <tr><td align="right" nowrap>Email:</td><td><input type="text" size="50" name="config[admin_email]" value="<?php echo $wakkaConfig["admin_email"] ?>" /></td></tr>

	<tr><td></td><td><br /><h2>Wikka URL Configuration</h2><?php echo $wakkaConfig["wakka_version"] ? "" : "<span class=\"note\">Since this is a new installation, the installer tried to guess the proper values.<br />Change them only if you know what you're doing!</span>" ?></td></tr>

	<tr><td></td><td>First you'll need to set up your Wikka site's base URL. Page names get appended to it, so: <ul>
		<li>if Rewrite Mode is not available on your server, the base URL should include <tt>"?wakka="</tt><br />e.g. <tt>http://www.example.com/wikka.php?wakka=</tt></li>
		<li>if Rewrite Mode is enabled, make sure the base URL ends with a slash "/",<br />e.g. <tt>http://www.example.com/</tt></li>
		</ul></td>
	</tr>
	<tr><td align="right" nowrap>Base URL:</td><td><input type="text" size="50" name="config[base_url]" value="<?php echo $wakkaConfig["base_url"] ?>" /></td></tr>

	<tr><td></td><td>Rewrite mode is used to produce a nicer URL for your Wikka site. If Rewrite mode is modified, the base URL should be changed accordingly.
	<ul>
		<li>With Rewrite mode <em>disabled</em>, your site's URL will look like the following:<br /><tt>http://www.example.com/wikka.php?wakka=HomePage</tt></li>
		<li>If Rewrite mode is <em>enabled</em>,  your site's URL will look like the following:<br /><tt>http://www.example.com/HomePage</tt></li>
	</ul></td></tr>
	<tr><td align="right" nowrap>Rewrite mode:</td><td><input type="hidden" name="config[rewrite_mode]" value="0" /><input type="checkbox" name="config[rewrite_mode]" value="1" <?php echo $wakkaConfig["rewrite_mode"] ? "checked" : "" ?> /> Enabled</td></tr>

	<?php
	 }
	?>

	<tr><td></td><td><input type="submit" value="Continue" onclick="return check();" /></td></tr>

</table>
</form>
