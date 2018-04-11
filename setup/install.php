<?php

// Start session
session_set_cookie_params(0, '/');
session_name(md5('WikkaWiki'));
session_start();

require_once 'setup/inc/functions.inc.php';

// Copy POST params from SESSION, then destroy SESSION
if(isset($_SESSION['post']))
{
	$_POST = array_merge($_POST, $_SESSION['post']);
}
$_SESSION=array();
if(isset($_COOKIE[session_name()]))
{
	setcookie(session_name(), '', time()-42000, '/');
}
session_destroy();

/*
foreach($_POST as $key=>$value)
{
	print $key.":".$value."<br/>";
}
foreach($_POST['config'] as $key=>$value)
{
	print $key.":".$value."<br/>";
}
exit;
*/

// i18n section
if (!defined('ADDING_CONFIG_ENTRY')) define('ADDING_CONFIG_ENTRY', 'Adding a new option to the wikka.config file: %s'); // %s - name of the config option
if (!defined('DELETING_COOKIES')) define('DELETING_COOKIES', 'Deleting wikka cookies since their name has changed.');

// initialization
$config = array(); //required since PHP5, to avoid warning on array_merge #94
// fetch configuration
$config = $_POST["config"];

/*
print "\$config:<br/>";
foreach($config as $key=>$value)
{
	print $key.":".$value."<br/>";
}
exit;
*/

// if the checkbox was not checked, $_POST['config']['enable_version_check'] would not be defined. We must explicitly set it to "0" to overwrite any value already set (if exists).
if (!isset($config["enable_version_check"]))
{
	$config["enable_version_check"] = "0";
}
// merge existing configuration with new one
$config = array_merge($wakkaConfig, $config);

/*
print "\$config:<br/>";
foreach($config as $key=>$value)
{
	print $key.":".$value."<br/>";
}
exit;
*/

// test configuration
print("<h2>Testing Configuration</h2>\n");
test("Testing DB connection settings...", $dblink = db_connect($config));
/* 
test("Looking for database...", @mysql_select_db($config["mysql_database"], $dblink), "The database you configured was not found. Remember, it needs to exist before you can install/upgrade Wakka!\n\nPress the Back button and reconfigure the settings.");
*/
print("<br />\n");

// do installation stuff
if (!$version = trim($wakkaConfig["wakka_version"])) $version = "0";

// set upgrade note to be used when overwriting default pages
$upgrade_note = 'Upgrading from '.$version.' to '.WAKKA_VERSION;

$lang_defaults_path = 'lang/'.$config['default_lang'].'/defaults/';
$lang_defaults_fallback_path = $fallback_lang_path.'/defaults/';
test('Checking availability of default pages...', is_dir($lang_defaults_path), 'default pages not found at '.$lang_defaults_path, 0);

// DB-specific updates
$file = 'setup/inc/db_update_'.$config['dbms_type'].'.php';
require $file;

// #600: Force reloading of stylesheet.
// #6: Append this to individual theme stylesheets
$config['stylesheet_hash'] = substr(md5(time()),1,5);
?>

<p>
In the next step, the installer will try to write the updated configuration file, <tt><?php echo $wakkaConfigLocation ?></tt>.
Please make sure the web server has write access to the file, or you will have to edit it manually.
Once again, see <a href="http://docs.wikkawiki.org/WikkaInstallation" target="_blank">WikkaInstallation</a> for details.
</p>

<form action="<?php echo myLocation(); ?>?installAction=writeconfig" method="post">
<input type="hidden" name="config" value="<?php echo Wakka::hsc_secure(serialize($config)) ?>" /><?php /* #427 */ ?>
<input type="submit" value="Continue" />
</form>
