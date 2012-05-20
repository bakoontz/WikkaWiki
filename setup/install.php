<?php

// Start session
session_set_cookie_params(0, '/');
session_name(md5('WikkaWiki'));
session_start();

require_once('setup/inc/functions.inc.php');

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
test("Testing MySQL connection settings...", $dblink = @mysql_connect($config["mysql_host"], $config["mysql_user"], $config["mysql_password"]));
test("Looking for database...", @mysql_select_db($config["mysql_database"], $dblink), "The database you configured was not found. Remember, it needs to exist before you can install/upgrade Wakka!\n\nPress the Back button and reconfigure the settings.");
@mysql_query("SET NAMES 'utf8'", $dblink); // refs #1024 
print("<br />\n");

// do installation stuff
if (!$version = trim($wakkaConfig["wakka_version"])) $version = "0";

// set upgrade note to be used when overwriting default pages
$upgrade_note = 'Upgrading from '.$version.' to '.WAKKA_VERSION;

$lang_defaults_path = 'lang'.DIRECTORY_SEPARATOR.$config['default_lang'].DIRECTORY_SEPARATOR.'defaults'.DIRECTORY_SEPARATOR;
$lang_defaults_fallback_path = $fallback_lang_path.DIRECTORY_SEPARATOR.'defaults'.DIRECTORY_SEPARATOR;
test('Checking availability of default pages...', is_dir($lang_defaults_path), 'default pages not found at '.$lang_defaults_path, 0);

switch ($version)
{
// new installation
case "0":
	print("<h2>Installing Stuff</h2>");
	test("Setting up database for UTF-8...", true);
	@mysql_query( "ALTER DATABASE ".$config['mysql_database']." DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;", $dblink);
	test("Creating page table...",
		@mysql_query(
			"CREATE TABLE ".$config['table_prefix']."pages (".
			"id int(10) unsigned NOT NULL auto_increment,".
			"tag varchar(75) NOT NULL default '',".
			"title varchar(75) NOT NULL default '',".
			"time datetime NOT NULL default '0000-00-00 00:00:00',".
			"body mediumtext NOT NULL,".
			"owner varchar(75) NOT NULL default '',".
			"user varchar(75) NOT NULL default '',".
			"latest enum('Y','N') NOT NULL default 'N',".
			"note varchar(100) NOT NULL default '',".
			"PRIMARY KEY  (id),".
			"KEY idx_tag (tag),".
			"FULLTEXT KEY body (body),".
			"KEY idx_time (time),".
			"KEY idx_owner (owner), ".
			"KEY idx_latest (latest)".
			") TYPE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci", $dblink), "Already exists?", 0);
	test("Creating ACL table...",
		@mysql_query(
			"CREATE TABLE ".$config['table_prefix']."acls (".
			"page_tag varchar(75) NOT NULL default '',".
			"read_acl text NOT NULL,".
			"write_acl text NOT NULL,".
			"comment_read_acl text NOT NULL,".
			"comment_post_acl text NOT NULL,".
			"PRIMARY KEY  (page_tag)".
			") TYPE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci", $dblink), "Already exists?", 0);
	test("Creating link tracking table...",
		@mysql_query(
			"CREATE TABLE ".$config['table_prefix']."links (".
			"from_tag varchar(75) NOT NULL default '',".
			"to_tag varchar(75) NOT NULL default '',".
			"UNIQUE KEY from_tag (from_tag,to_tag),".
			"KEY idx_to (to_tag)".
			") TYPE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci", $dblink), "Already exists?", 0);
	test("Creating referrer table...",
		@mysql_query(
			"CREATE TABLE ".$config['table_prefix']."referrers (".
			"page_tag varchar(75) NOT NULL default '',".
			"referrer varchar(255) NOT NULL default '',".
			"time datetime NOT NULL default '0000-00-00 00:00:00',".
			"KEY idx_page_tag (page_tag),".
			"KEY idx_time (time)".
			") TYPE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci", $dblink), "Already exists?", 0);
	test("Creating referrer blacklist table...",
		@mysql_query(
			"CREATE TABLE ".$config['table_prefix']."referrer_blacklist (".
			"spammer varchar(255) NOT NULL default '',".
			"KEY idx_spammer (spammer)".
			") TYPE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci", $dblink), "Already exists?", 0);
	test("Creating user table...",
		@mysql_query(
			"CREATE TABLE ".$config['table_prefix']."users (".
			"name varchar(75) NOT NULL default '',".
			"password varchar(32) NOT NULL default '',".
			"email varchar(50) NOT NULL default '',".
			"revisioncount int(10) unsigned NOT NULL default '20',".
			"changescount int(10) unsigned NOT NULL default '50',".
			"doubleclickedit enum('Y','N') NOT NULL default 'Y',".
			"signuptime datetime NOT NULL default '0000-00-00 00:00:00',".
			"show_comments enum('Y','N') NOT NULL default 'N',".
			"status enum('invited','signed-up','pending','active','suspended','banned','deleted'),".
			"theme varchar(50) default '',".
			"default_comment_display enum ('date_asc', 'date_desc', 'threaded') NOT NULL default 'threaded',".
			"challenge varchar(8) default '',". // refs #1023
			"PRIMARY KEY  (name),".
			"KEY idx_signuptime (signuptime)".
			") TYPE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci", $dblink), "Already exists?", 0);
	test("Creating comment table...",
		@mysql_query(
			"CREATE TABLE ".$config['table_prefix']."comments (".
			"id int(10) unsigned NOT NULL auto_increment,".
			"page_tag varchar(75) NOT NULL default '',".
			"time datetime NOT NULL default '0000-00-00 00:00:00',".
			"comment text NOT NULL,".
			"user varchar(75) NOT NULL default '',".
			"parent int(10) unsigned default NULL,". 
			"status enum('deleted') default NULL,".
			"deleted char(1) default NULL,".
			"PRIMARY KEY  (id),".
			"KEY idx_page_tag (page_tag),".
			"KEY idx_time (time)".
			") TYPE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci", $dblink), "Already exists?", 0);
	test("Creating session tracking table...",
		@mysql_query(
			"CREATE TABLE ".$config['table_prefix']."sessions (".
			"sessionid char(32) NOT NULL,".
			"userid varchar(75) NOT NULL,".
			"PRIMARY KEY (sessionid, userid),".
			"session_start datetime NOT NULL".
			") TYPE=MyISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci", $dblink), "Already exists?", 0);

	update_default_page(array(
	'_rootpage', 
	'AdminBadWords',
	'AdminPages',
	'AdminSpamLog',
	'AdminUsers',
	'CategoryAdmin',
	'CategoryCategory', 
	'CategoryWiki', 
	'DatabaseInfo',
	'FormattingRules', 
	'HighScores', 
	'InterWiki', 
	'MyChanges', 
	'MyPages', 
	'OrphanedPages', 
	'OwnedPages', 
	'PageIndex', 
	'PasswordForgotten', 
	'RecentChanges', 
	'RecentComments',
	'RecentlyCommented', 
	'SandBox', 
	'SysInfo',
	'TableMarkup',
	'TableMarkupReference',
	'TextSearch', 
	'TextSearchExpanded', 
	'UserSettings', 
	'WantedPages', 
	'WikiCategory', 
	'WikkaConfig', 
	'WikkaDocumentation', 
	'WikkaMenulets',
	'WikkaReleaseNotes'), $dblink, $config, $lang_defaults_path, $lang_defaults_fallback_path); 

	test('Building links table...', 1);
	/**
	 * Script for (re)building links table.
	 */
	include('links.php');

	// @@@	?? *default* ACLs are in the configuration file; settings on UserSettings page are irrelevant for default ACLs!
	//		use page-specific "ACL" files to create page-specific ACLs (in update_default_page()!).
	// @@@	use test() function to report actual results instead of assuming success!
	test("Setting default ACL...", 1);
	mysql_query("insert into ".$config['table_prefix']."acls set page_tag = 'UserSettings', read_acl = '*', write_acl = '+', comment_read_acl = '*', comment_post_acl = '+'", $dblink);
	mysql_query("insert into ".$config['table_prefix']."acls set page_tag = 'AdminUsers', read_acl = '!*', write_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", $dblink);
	mysql_query("insert into ".$config['table_prefix']."acls set page_tag = 'AdminPages', read_acl = '!*', write_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", $dblink);
	mysql_query("insert into ".$config['table_prefix']."acls set page_tag = 'SysInfo', read_acl = '!*', write_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", $dblink);
	mysql_query("insert into ".$config['table_prefix']."acls set page_tag = 'WikkaConfig', read_acl = '!*', write_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", $dblink);
	mysql_query("insert into ".$config['table_prefix']."acls set page_tag = 'DatabaseInfo', read_acl = '!*', write_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", $dblink);
	mysql_query("insert into ".$config['table_prefix']."acls set page_tag = 'WikkaMenulets', read_acl = '!*', write_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", $dblink);
	mysql_query("insert into ".$config['table_prefix']."acls set page_tag = 'AdminBadWords', read_acl = '!*', write_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", $dblink);
	mysql_query("insert into ".$config['table_prefix']."acls set page_tag = 'AdminSpamLog', read_acl = '!*', write_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", $dblink);

	// Register admin user
	$challenge = dechex(crc32(time()));
	$pass_val = md5($challenge.(mysql_real_escape_string($_POST['password'])));
	// Delete existing admin user in case installer was run twice
	@mysql_query('delete from '.$config['table_prefix'].'users where name = \''.$config['admin_users'].'\'', $dblink);
    test(__('Adding admin user').'...',
	        @mysql_query("insert into ".$config["table_prefix"]."users set name = '".$config["admin_users"]."', password = '".$pass_val."', email = '".$config["admin_email"]."', signuptime = now(), challenge='".$challenge."'", $dblink), "Hmm!", 0);

	// Auto-login wiki admin
	// Set default cookie path
	test("Setting initial session cookies for auto-login...", 1);
	$base_url_path = preg_replace('/wikka\.php/', '', $_SERVER['SCRIPT_NAME']);
	$wikka_cookie_path = ('/' == $base_url_path) ? '/' : substr($base_url_path,0,-1);

	// Set cookies
	SetCookie('user_name@wikka', $config['admin_users'], time() + PERSISTENT_COOKIE_EXPIRY, $wikka_cookie_path); 
	$_COOKIE['user_name'] = $config['admin_users']; 
	SetCookie('pass@wikka', $pass_val, time() + PERSISTENT_COOKIE_EXPIRY, $wikka_cookie_path); 
	$_COOKIE['pass'] = $pass_val; 

	break;

// The funny upgrading stuff. Make sure these are in order! //
// And yes, there are no breaks here. This is on purpose.  //

// from 0.1 to 0.1.1
case "0.1":
	print("<strong>Wakka 0.1 to 0.1.1</strong><br />\n");
	test("Just very slightly altering the pages table...",
		@mysql_query("alter table ".$config['table_prefix']."pages add body_r text not null default '' after body", $dblink), "Already done? Hmm!", 0);
	test("Claiming all your base...", 1);

// from 0.1.1 to 0.1.2
case "0.1.1":
	print("<strong>Wakka 0.1.1 to 0.1.2</strong><br />\n");
	test("Keep rolling...", 1);

// from 0.1.2 to 0.1.3-dev (will be 0.1.3)
case "0.1.2":
	print("<strong>Wakka 0.1.2 to 0.1.3-dev</strong><br />\n");
	test("Keep rolling...", 1);

case "0.1.3-dev":
	print("<strong>Wakka 0.1.3-dev to Wikka 1.0.0 changes:</strong><br />\n");
	test("Adding note column to the pages table...",
		@mysql_query("ALTER TABLE ".$config['table_prefix']."pages ADD note varchar(50) NOT NULL default '' after latest", $dblink), "Failed.", 1);
	test("Just slightly altering the pages table...",
		@mysql_query("alter table ".$config['table_prefix']."pages DROP COLUMN body_r", $dblink), "Already done? Hmm!", 0);
	test("Just slightly altering the users table...",
		@mysql_query("alter table ".$config['table_prefix']."users DROP COLUMN motto", $dblink), "Already done? Hmm!", 0);
case "1.0":
case "1.0.1":
case "1.0.2":
case "1.0.3":
case "1.0.4":
// from 1.0.4 to 1.0.5
	print("<strong>1.0.4 to 1.0.5 changes:</strong><br />\n");
	test(sprintf(ADDING_CONFIG_ENTRY, 'double_doublequote_html'), 1);
	$config["double_doublequote_html"] = 'safe';
case "1.0.5":
case "1.0.6":
	print("<strong>1.0.6 to 1.1.0 changes:</strong><br />\n");
	test("Creating comment table...",
		@mysql_query(
			"CREATE TABLE ".$config['table_prefix']."comments (".
			"id int(10) unsigned NOT NULL auto_increment,".
			"page_tag varchar(75) NOT NULL default '',".
			"time datetime NOT NULL default '0000-00-00 00:00:00',".
			"comment text NOT NULL,".
			"user varchar(75) NOT NULL default '',".
			"PRIMARY KEY  (id),".
			"KEY idx_page_tag (page_tag),".
			"KEY idx_time (time)".
			") TYPE=MyISAM", $dblink), "Already done? Hmm!", 1);
	test("Copying comments from the pages table to the new comments table...",
		@mysql_query("INSERT INTO ".$config['table_prefix']."comments (page_tag, time, comment, user) SELECT comment_on, time, body, user FROM ".$config['table_prefix']."pages WHERE comment_on != '';", $dblink), "Already done? Hmm!", 1);
	test("Deleting comments from the pages table...",
		@mysql_query("DELETE FROM ".$config['table_prefix']."pages WHERE comment_on != ''", $dblink), "Already done? Hmm!", 1);
	test("Removing comment_on field from the pages table...",
		@mysql_query("ALTER TABLE ".$config['table_prefix']."pages DROP comment_on", $dblink), "Already done? Hmm!", 1);
	test("Removing comment pages from the ACL table...",
		@mysql_query("DELETE FROM ".$config['table_prefix']."acls WHERE page_tag like 'Comment%'", $dblink), "Already done? Hmm!", 1);
case "1.1.0":
	print("<strong>1.1.0 to 1.1.2 changes:</strong><br />\n");
	test("Dropping current ACL table structure...",
		@mysql_query("DROP TABLE ".$config['table_prefix']."acls", $dblink), "Already done? Hmm!", 0);
	test("Creating new ACL table structure...",
		@mysql_query(
			"CREATE TABLE ".$config['table_prefix']."acls (".
			"page_tag varchar(75) NOT NULL default '',".
			"read_acl text NOT NULL,".
			"write_acl text NOT NULL,".
			"comment_acl text NOT NULL,".
			"PRIMARY KEY  (page_tag)".
			") TYPE=MyISAM", $dblink), "Already exists?", 1);
case "1.1.2":
case "1.1.3":
	print("<strong>1.1.3 to 1.1.3.1 changes:</strong><br />\n");
	test("Altering pages table structure...",
		@mysql_query("ALTER TABLE ".$config['table_prefix']."pages CHANGE tag tag varchar(75) NOT NULL default ''", $dblink), "Failed. ?", 1);
	test("Altering pages table structure...",
		@mysql_query("ALTER TABLE ".$config['table_prefix']."pages CHANGE user user varchar(75) NOT NULL default ''", $dblink), "Failed. ?", 1);
	test("Altering pages table structure...",
		@mysql_query("ALTER TABLE ".$config['table_prefix']."pages CHANGE owner owner varchar(75) NOT NULL default ''", $dblink), "Failed. ?", 1);
	test("Altering pages table structure...",
		@mysql_query("ALTER TABLE ".$config['table_prefix']."pages CHANGE note note varchar(100) NOT NULL default ''", $dblink), "Failed. ?", 1);
	test("Altering user table structure...",
		@mysql_query("ALTER TABLE ".$config['table_prefix']."users CHANGE name name varchar(75) NOT NULL default ''", $dblink), "Failed. ?", 1);
	test("Altering comments table structure...",
		@mysql_query("ALTER TABLE ".$config['table_prefix']."comments CHANGE page_tag page_tag varchar(75) NOT NULL default ''", $dblink), "Failed. ?", 1);
	test("Altering comments table structure...",
		@mysql_query("ALTER TABLE ".$config['table_prefix']."comments CHANGE user user varchar(75) NOT NULL default ''", $dblink), "Failed. ?", 1);
	test("Altering acls table structure...",
		@mysql_query("ALTER TABLE ".$config['table_prefix']."acls CHANGE page_tag page_tag varchar(75) NOT NULL default ''", $dblink), "Failed. ?", 1);
	test("Altering links table structure...",
		@mysql_query("ALTER TABLE ".$config['table_prefix']."links CHANGE from_tag from_tag varchar(75) NOT NULL default ''", $dblink), "Failed. ?", 1);
	test("Altering links table structure...",
		@mysql_query("ALTER TABLE ".$config['table_prefix']."links CHANGE to_tag to_tag varchar(75) NOT NULL default ''", $dblink), "Failed. ?", 1);
	test("Altering referrers table structure...",
		@mysql_query("ALTER TABLE ".$config['table_prefix']."referrers MODIFY referrer varchar(150) NOT NULL default ''", $dblink), "Failed. ?", 1);
	test("Creating referrer_blacklist table...",
		@mysql_query(
			"CREATE TABLE ".$config['table_prefix']."referrer_blacklist (".
			"spammer varchar(150) NOT NULL default '',".
			"KEY idx_spammer (spammer)".
			") TYPE=MyISAM", $dblink), "Already exists? Hmm!", 1);
	test("Altering a pages table index...",
		@mysql_query("alter table ".$config['table_prefix']."pages DROP INDEX tag", $dblink), "Already done? Hmm!", 0);
	test("Altering a pages table index...",
		@mysql_query("alter table ".$config['table_prefix']."pages ADD FULLTEXT body (body)", $dblink), "Already done? Hmm!", 0);
	test("Altering a users table index...",
		@mysql_query("alter table ".$config['table_prefix']."users DROP INDEX idx_name", $dblink), "Already done? Hmm!", 0);
case "1.1.3.1":
case "1.1.3.2":
	print("<strong>1.1.3.2 to 1.1.3.3 changes:</strong><br />\n");
	test(sprintf(ADDING_CONFIG_ENTRY, 'wikiping_server'), 1);
	$config["wikiping_server"] = '';
case "1.1.3.3":
case "1.1.3.4":
case "1.1.3.5":
case "1.1.3.6":
case "1.1.3.7":
case "1.1.3.8":
case "1.1.3.9":
case "1.1.4.0":
case "1.1.5.0":
case "1.1.5.1":
case "1.1.5.2":
case "1.1.5.3":
	update_default_page(array(
		'WikkaReleaseNotes', 
		'WikkaDocumentation'), $dblink, $config, $lang_defaults_path, $lang_defaults_fallback_path, $upgrade_note);
	// cookie names have changed -- logout user and delete the old cookies
	test(DELETING_COOKIES, 1);
	DeleteCookie("name");
	DeleteCookie("password");
	// delete files removed from previous version
	@unlink('actions/wakkabug.php');
	// delete directories that have been moved
	rmdirr("freemind");
	rmdirr("safehtml");
	rmdirr("wikiedit2");
	rmdirr("xml");
case "1.1.6.0":
case "1.1.6.1":
	test(sprintf(ADDING_CONFIG_ENTRY, 'grabcode_button' ), 1);
	$config["grabcode_button"] = '1';
	test(sprintf(ADDING_CONFIG_ENTRY, 'wiki_suffix'), 1);
	$config["wiki_suffix"] = '_wikka';
	test(sprintf(ADDING_CONFIG_ENTRY, 'require_edit_note'), 1);
	$config["require_edit_note"] = '0';
	test(sprintf(ADDING_CONFIG_ENTRY, 'public_sysinfo'), 1);
	$config["public_sysinfo"] = '0';
	// cookie names have changed -- logout user and delete the old cookies
	test(DELETING_COOKIES, 1);
	DeleteCookie("wikka_user_name");
	DeleteCookie("wikka_pass");
case "1.1.6.2-alpha":
case "1.1.6.2-beta":
case "1.1.6.2":
case "1.1.6.3":
	test(sprintf(ADDING_CONFIG_ENTRY, 'allow_user_registration' ), 1);
	$config['allow_user_registration'] = '1';
	test(sprintf(ADDING_CONFIG_ENTRY, 'wikka_template_path' ), 1);
	$config["wikka_template_path"] = 'templates';
	update_default_page(array(
		'HighScores', 
		'CategoryAdmin', 
		'AdminUsers', 
		'AdminPages', 
		'DatabaseInfo', 
		'WikiCategory'), $dblink, $config, $lang_defaults_path, $lang_defaults_fallback_path, $upgrade_note);
	test("Adding status field to users table...",
	mysql_query("alter table ".$config['table_prefix']."users add column status enum ('invited','signed-up','pending','active','suspended','banned','deleted')"), "Already done? OK!", 0); 
	test("Adding sessions tracking table...",
	mysql_query("create table ".$config['table_prefix']."sessions (sessionid char(32) NOT NULL, userid varchar(75) NOT NULL, PRIMARY KEY (sessionid, userid), session_start datetime NOT NULL)"),	"Already done? OK!", 0); 
	test('Dropping obsolete index `from_tag`...',
	mysql_query('alter table '.$config['table_prefix'].'links drop index `idx_from`'), 'Already done?  OK!', 0);
case "1.1.6.4":
case "1.1.6.5":
case "1.1.6.6":
case "1.1.6.7":
	print("<strong>1.1.6.7 to 1.2 changes:</strong><br />\n");
	test("Adding theme field to user preference table...",
	@mysql_query("ALTER TABLE ".$config['table_prefix']."users ADD
	theme varchar(50) default ''", $dblink), "Already done? OK!", 0);
	test("Setting default UserSettings ACL...",
	@mysql_query("insert into ".$config['table_prefix']."acls set page_tag = 'UserSettings', comment_read_acl = '*', comment_post_acl = '+'", $dblink), __('Already done? OK!'), 0);
	test("Setting default AdminUsers ACL...",
	@mysql_query("insert into ".$config['table_prefix']."acls set page_tag = 'AdminUsers', read_acl = '!*', write_acl = '!*', comment_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", $dblink), __('Already done? OK!'), 0);
	test("Setting default AdminPages ACL...",
	@mysql_query("insert into ".$config['table_prefix']."acls set page_tag = 'AdminPages', read_acl = '!*', write_acl = '!*', comment_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", $dblink), __('Already done? OK!'), 0);
	test("Setting default DatabaseInfo ACL...",
	@mysql_query("insert into ".$config['table_prefix']."acls set page_tag = 'DatabaseInfo', read_acl = '!*', write_acl = '!*', comment_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", $dblink), __('Already done? OK!'), 0);
	update_default_page('FormattingRules', $dblink, $config, $lang_defaults_path, $lang_defaults_fallback_path, $upgrade_note);
case "1.2":
	print("<strong>1.2 to 1.3.1 changes:</strong><br />\n");
	test(sprintf(ADDING_CONFIG_ENTRY, 'enable_user_host_lookup' ), 1);
	$config['enable_user_host_lookup'] = '1';
	update_default_page(array(
		'SysInfo', 
		'TableMarkup', 
		'TableMarkupReference', 
		'WikkaConfig'), $dblink, $config, $lang_defaults_path, $lang_defaults_fallback_path, $upgrade_note);
	// Dropping obsolete "handler" field from pages table, refs #452
	test('Removing handler field from the pages table...',
	@mysql_query("ALTER TABLE ".$config["table_prefix"]."pages DROP handler", $dblink), __('Already done? OK!'), 0);
	// Support for threaded comments
	test("Adding fields to comments table to enable threading...",  
	mysql_query("alter table ".$config["table_prefix"]."comments add parent int(10) unsigned default NULL", $dblink), "Already done? OK!", 0);
	test("Adding fields to comments table to enable threading...",
	mysql_query("alter table ".$config["table_prefix"]."users add default_comment_display enum('date_asc', 'date_desc', 'threaded') NOT NULL default 'threaded'", $dblink), "Already done? OK!", 0);
	test("Adding fields to comments table to enable threading...",  
	mysql_query("alter table ".$config["table_prefix"]."comments add status enum('deleted') default NULL", $dblink), "Already done? OK!", 0);
	// Create new fields for comment_read_acl and comment_post_acl, 
	// and copy existing comment_acl values to these new fields 
	test('Creating new comment_read_acl field...', 
	@mysql_query("alter table ".$config['table_prefix']."acls add comment_read_acl text not null", $dblink), __('Already done?  OK!'), 0); 
	test('Creating new comment_post_acl field...', 
	@mysql_query("alter table ".$config['table_prefix']."acls add comment_post_acl text not null", $dblink), __('Already done?  OK!'), 0); 
	test('Copying existing comment_acls to new fields...', 
	@mysql_query("update ".$config['table_prefix']."acls as a inner join(select page_tag, comment_acl from ".$config['table_prefix']."acls) as b on a.page_tag = b.page_tag set a.comment_read_acl=b.comment_acl, a.comment_post_acl=b.comment_acl", $dblink), __('Already done?  OK!'), 0);
	test('Drop old comment acl...', 
	@mysql_query("alter table ".$config['table_prefix']."acls drop comment_acl", $dblink), __('Already done?  OK!'), 0);
	test(__('Creating index on owner column').'...', 
	@mysql_query('alter table '.$config['table_prefix'].'pages add index `idx_owner` (`owner`)', $dblink), __('Already done?  OK!'), 0); 
  	test(__('Altering referrers table structure').'...',
	@mysql_query("ALTER TABLE ".$config['table_prefix']."referrers MODIFY referrer varchar(255) NOT NULL default ''", $dblink), __('Already done?  OK!'), 0);
	test(__('Altering referrer blacklist table structure').'...',
	@mysql_query("ALTER TABLE ".$config['table_prefix']."referrer_blacklist MODIFY spammer varchar(255) NOT NULL default ''", $dblink), __('Already done?  OK!'), 0);
	update_default_page(array(
		'FormattingRules',
		'SysInfo', 
		'WikkaReleaseNotes',
		'TableMarkup', 
		'TableMarkupReference', 
		'WikkaConfig'), $dblink, $config, $lang_defaults_path, $lang_defaults_fallback_path, $upgrade_note);
	// Backup config/ files
	if(file_exists("config/main_menu.admin.inc"))
		brute_copy("config/main_menu.admin.inc", 
			 "config/main_menu.admin.inc.prev");	
	if(file_exists("config/main_menu.inc"))
		brute_copy("config/main_menu.inc", 
			 "config/main_menu.inc.prev");	
	if(file_exists("config/main_menu.user.inc"))
		brute_copy("config/main_menu.user.inc", 
			 "config/main_menu.user.inc.prev");	
	if(file_exists("config/options_menu.admin.inc"))
		brute_copy("config/options_menu.admin.inc", 
			 "config/options_menu.admin.inc.prev");	
	if(file_exists("config/options_menu.inc"))
		brute_copy("config/options_menu.inc", 
			 "config/options_menu.inc.prev");	
	if(file_exists("config/options_menu.user.inc"))
		brute_copy("config/options_menu.user.inc", 
			 "config/options_menu.user.inc.prev");
case "1.3":
case "1.3.1":
	print("<strong>1.3.1 to 1.3.2 changes:</strong><br />\n");
	update_default_page(array(
	'AdminBadWords',
	'AdminSpamLog',
	'WikkaMenulets'), $dblink, $config, $lang_defaults_path, $lang_defaults_fallback_path); 
	test("Setting default ACL...", 1);
	mysql_query("insert into ".$config['table_prefix']."acls set page_tag = 'WikkaMenulets', read_acl = '!*', write_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", $dblink);
	mysql_query("insert into ".$config['table_prefix']."acls set page_tag = 'AdminBadWords', read_acl = '!*', write_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", $dblink);
	mysql_query("insert into ".$config['table_prefix']."acls set page_tag = 'AdminSpamLog', read_acl = '!*', write_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", $dblink);
	// Converting DB UTF-8 (but data remains
	// unchanged -- this is handled by a standalone script)	
	test("Setting up database for UTF-8...", true);
	@mysql_query("ALTER DATABASE ".$config['mysql_database']." DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci", $dblink);
	// Converting pages table and fields to UTF-8
	test("Setting up pages table and fields for UTF-8...", true);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."pages DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."pages CHANGE `tag` `tag` VARCHAR( 75 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."pages CHANGE `body` `body` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."pages CHANGE `owner` `owner` VARCHAR( 75 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."pages CHANGE `user` `user` VARCHAR( 75 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."pages CHANGE `latest` `latest` ENUM( 'Y','N' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default 'N'", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."pages CHANGE `note` `note` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", $dblink); // refs #1021
	// Converting acls table and fields to UTF-8
	test("Setting up acls table and fields for UTF-8...", true);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."acls DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."acls CHANGE `page_tag` `page_tag` VARCHAR( 75 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."acls CHANGE `read_acl` `read_acl` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."acls CHANGE `write_acl` `write_acl` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."acls CHANGE `comment_read_acl` `comment_read_acl` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."acls CHANGE `comment_post_acl` `comment_post_acl` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL", $dblink);
	test("Setting up links table and fields for UTF-8...", true);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."links DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."links CHANGE `from_tag` `from_tag` VARCHAR( 75 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."links CHANGE `to_tag` `to_tag` VARCHAR( 75 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", $dblink);
	// Converting referrers table and fields to UTF-8
	test("Setting up referrers table and fields for UTF-8...", true);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."referrers DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."referrers CHANGE `page_tag` `page_tag` VARCHAR( 75 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."referrers CHANGE `referrer` `referrer` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", $dblink);
	// Converting referrer_blacklist table and fields to UTF-8
	test("Setting up referrer_blacklist table and fields for UTF-8...", true);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."referrer_blacklist DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."referrer_blacklist CHANGE `spammer` `spammer` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", $dblink);
	// Converting users table and fields to UTF-8
	test("Setting up users table and fields for UTF-8...", true);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."users DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."users CHANGE `name` `name` VARCHAR( 75 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."users CHANGE `password` `password` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."users CHANGE `email` `email` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."users CHANGE `doubleclickedit` `doubleclickedit` ENUM( 'Y','N' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default 'Y'", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."users CHANGE `show_comments` `show_comments` ENUM( 'Y','N' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default 'N'", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."users CHANGE `default_comment_display` `default_comment_display` ENUM( 'date_asc','date_desc','threaded' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default 'threaded'", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."users CHANGE `status` `status` ENUM( 'invited','signed-up','pending','active','suspended','banned','deleted') CHARACTER SET utf8 COLLATE utf8_unicode_ci", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."users CHANGE `theme` `theme` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci default ''", $dblink); // refs #1022
	// Converting comments table and fields to UTF-8
	test("Setting up comments table and fields for UTF-8...", true);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."comments DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."comments CHANGE `page_tag` `page_tag` VARCHAR( 75 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."comments CHANGE `comment` `comment` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."comments CHANGE `user` `user` VARCHAR( 75 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."comments CHANGE `status` `status` ENUM( 'deleted' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci default NULL", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."comments CHANGE `deleted` `deleted` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci default NULL", $dblink);
	// Converting sessions table and fields to UTF-8
	test("Setting up sessions table and fields for UTF-8...", true);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."sessions DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci", $dblink);
	@mysql_query("ALTER TABLE ".$config['table_prefix']."sessions CHANGE `sessionid` `sessionid` CHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL", $dblink); // refs #1022
	@mysql_query("ALTER TABLE ".$config['table_prefix']."sessions CHANGE `userid` `userid` VARCHAR( 75 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL", $dblink);
	// Adding challenge, refs #1023
	test("Adding/updating challenge field to users table to improve security...",  
	@mysql_query("alter table ".$config["table_prefix"]."users ADD challenge varchar(8) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT ''", $dblink), __("Already done? OK!"), 0);
	@mysql_query("alter table ".$config["table_prefix"]."users CHANGE `challenge` `challenge` varchar(8) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT ''", $dblink);
	@mysql_query("UPDATE ".$config['table_prefix']."users SET challenge='' WHERE challenge='00000000'", $dblink);
case "1.3.2": 
	print("<strong>1.3.2 to 1.3.3 changes:</strong><br />\n");
	test("Adding/updating title field to users page ...",  
	@mysql_query("alter table `".$config["table_prefix"]."pages` ADD `title` varchar(75) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '' AFTER `tag`", $dblink), __("Already done? OK!"), 0); // refs #529
case "1.4":
}

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
