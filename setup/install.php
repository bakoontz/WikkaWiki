<?php
/**
 * Perform the operations required for installing/upgrading Wikka.
 * 
 * @package	Setup
 * @version	$Id$
 * @license	http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @todo i18n;
 */

// test configuration
if ($wakkaConfig['wakka_version'])
{
	echo '<h1>'.__('Wikka Upgrade').' (4/5)</h1>'."\n";
}
else
{
	echo '<h1>'.__('Wikka Installation').' (4/5)</h1>'."\n";
}

// do installation stuff
if (!$version = trim($wakkaConfig["wakka_version"])) $version = "0";

// set upgrade note to be used when overwriting default pages
$upgrade_note = sprintf(__('Upgrading from %1$s to %2$s'), $version, WAKKA_VERSION);
$_SESSION['sconfig']['logged_in_navigation_links'] = str_replace('_rootpage', $config['root_page'], $_SESSION['sconfig']['logged_in_navigation_links']);
$_SESSION['sconfig']['navigation_links'] = str_replace('_rootpage', $config['root_page'], $_SESSION['sconfig']['navigation_links']);

$lang_defaults_fallback_path = WIKKA_LANG_PATH.DIRECTORY_SEPARATOR.CONFIG_DEFAULT_LANGUAGE.DIRECTORY_SEPARATOR.'defaults'.DIRECTORY_SEPARATOR;
test('Checking availability of default pages...', is_dir($lang_defaults_fallback_path), 'default pages not found at '.$lang_defaults_fallback_path, 1);
$lang_defaults_path = WIKKA_LANG_PATH.DIRECTORY_SEPARATOR.$config['default_lang'].DIRECTORY_SEPARATOR.'defaults'.DIRECTORY_SEPARATOR;
// @@@ use test() here, too? (without stop on error but reporting back we're using sytem default language)
if (!is_dir($lang_defaults_path))
{
	// no directory for selected language: set equal to fallback so we can continue
	$lang_defaults_path = $lang_defaults_fallback_path;
}

switch ($version)
{
// new installation
case "0":
	print('<h2>'.__('Installing Stuff').'</h2>');
	test(sprintf(__('Creating %s table'), __('page')).'...',
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."pages (".
			 "id int(10) unsigned NOT NULL auto_increment,".
			 "tag varchar(75) NOT NULL default '',".
			 "time datetime NOT NULL default '0000-00-00 00:00:00',".
			 "body mediumtext NOT NULL,".
			 "owner varchar(75) NOT NULL default '',".
			 "user varchar(75) NOT NULL default '',".
			 "latest enum('Y','N') NOT NULL default 'N',".
			 "note varchar(100) NOT NULL default '',".
			 "title varchar(75) default NULL,".
			 "PRIMARY KEY  (id),".
			 "KEY idx_tag (tag),".
			 "FULLTEXT KEY body (body),".
			 "KEY idx_time (time),".
			 "KEY idx_owner (owner),".
			 "KEY idx_latest (latest)".
			") TYPE=MyISAM;", $dblink), __('Already exists?'), 0);
	test(sprintf(__('Creating %s table'), __('ACL')).'...',
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."acls (".
			 "page_tag varchar(75) NOT NULL default '',".
			 "read_acl text NOT NULL,".
			 "write_acl text NOT NULL,".
			 "comment_read_acl text NOT NULL,".
			 "comment_post_acl text NOT NULL,".
			 "PRIMARY KEY  (page_tag)".
			") TYPE=MyISAM", $dblink), __('Already exists?'), 0);
	test(sprintf(__('Creating %s table'), __('link tracking')).'...',
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."links (".
			 "from_tag varchar(75) NOT NULL default '',".
			 "to_tag varchar(75) NOT NULL default '',".
			 "UNIQUE KEY from_tag (from_tag,to_tag),".
			 "KEY idx_to (to_tag)".
			") TYPE=MyISAM", $dblink), __('Already exists?'), 0);
	test(sprintf(__('Creating %s table'), __('referrer')).'...',
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."referrers (".
			 "page_tag varchar(75) NOT NULL default '',".
			 "referrer varchar(150) NOT NULL default '',".
			 "time datetime NOT NULL default '0000-00-00 00:00:00',".
			 "KEY idx_page_tag (page_tag),".
			 "KEY idx_time (time)".
			") TYPE=MyISAM", $dblink), __('Already exists?'), 0);
	test(sprintf(__('Creating %s table'), __('referrer blacklist')).'...',
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."referrer_blacklist (".
			 "spammer varchar(150) NOT NULL default '',".
			 "KEY idx_spammer (spammer)".
			") TYPE=MyISAM", $dblink), __('Already exists?'), 0);
	test(sprintf(__('Creating %s table'), __('user')).'...',
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."users (".
			 "name varchar(75) NOT NULL default '',".
			 "password varchar(32) NOT NULL default '',".
			 "email varchar(50) NOT NULL default '',".
			 "revisioncount int(10) unsigned NOT NULL default '20',".
			 "changescount int(10) unsigned NOT NULL default '50',".
			 "doubleclickedit enum('Y','N') NOT NULL default 'Y',".
			 "signuptime datetime NOT NULL default '0000-00-00 00:00:00',".
			 "show_comments enum('Y','N') NOT NULL default 'N',".
		     "status enum('invited','signed-up','pending','active','suspended','banned','deleted'),".
			 "default_comment_display int(10) unsigned NOT NULL default '1',".
			 "challenge char(8) NOT NULL default '00000000',".
			 "PRIMARY KEY  (name),".
			 "KEY idx_signuptime (signuptime)".
			") TYPE=MyISAM", $dblink), __('Already exists?'), 0);
	test(sprintf(__('Creating %s table'), __('comment')).'...',
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."comments (".
			 "id int(10) unsigned NOT NULL auto_increment,".
			 "page_tag varchar(75) NOT NULL default '',".
			 "time datetime NOT NULL default '0000-00-00 00:00:00',".
			 "comment text NOT NULL,".
			 "user varchar(75) NOT NULL default '',".
			 "parent int(10) unsigned default NULL,".
			 "status varchar(10) default NULL,".
			 "PRIMARY KEY  (id),".
			 "KEY idx_page_tag (page_tag),".
			 "KEY idx_time (time)".
			") TYPE=MyISAM;", $dblink), __('Already exists?'), 0);
	test(sprintf(__('Creating %s table'), __('session')).'...',
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."sessions (".
			 "sessionid char(40) NOT NULL,".
			 "userid varchar(75) NOT NULL,".
			 "PRIMARY KEY (sessionid, userid),".
			 "session_start datetime NOT NULL".
			") TYPE=MyISAM;", $dblink), __('Already exists?'), 0);

	$challenge = dechex(crc32(time()));
	$pass_val = md5($challenge.md5(mysql_real_escape_string($_SESSION['wikka']['install']['password'])));
	setcookie('user_name'.$config['wiki_suffix'], $config['admin_users'], time() + DEFAULT_COOKIE_EXPIRATION_HOURS * 60 * 60, WIKKA_COOKIE_PATH);
	$_COOKIE['user_name'.$config['wiki_suffix']] = $config['admin_users'];
	setcookie('pass'.$config['wiki_suffix'], $pass_val, time() + DEFAULT_COOKIE_EXPIRATION_HOURS * 60 * 60, WIKKA_COOKIE_PATH);
	$_COOKIE['pass'.$config['wiki_suffix']] = $pass_val;
	# first, I delete a previous entry in the table _users, in case this setup's
	# script was run twice. If the following insert fails, the new Admin won't be auto-logged in.
	@mysql_query('delete from '.$config['table_prefix'].'users where name = \''.$config['admin_users'].'\'', $dblink);
	test(__('Adding admin user').'...', 
		@mysql_query("insert into ".$config["table_prefix"]."users set name = '".$config["admin_users"]."', password = md5('".mysql_real_escape_string($_SESSION['wikka']['install']['password'])."'), email = '".$config["admin_email"]."', signuptime = now(), challenge='".$challenge."'", $dblink), "Hmm!", 0);

	update_default_page(array(
	'_rootpage', 
	'AdminPages',
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
	'RecentlyCommented', 
	'SandBox', 
	'SysInfo',
	'TextSearch', 
	'TextSearchExpanded', 
	'UserSettings', 
	'WantedPages', 
	'WikiCategory', 
	'WikkaDocumentation', 
	'WikkaReleaseNotes'), $dblink, $config, $lang_defaults_path, $lang_defaults_fallback_path); 

	// @@@	?? *default* ACLs are in the configuration file; settings on UserSettings page are irrelevant for default ACLs!
	//		use page-specific "ACL" files to create page-specific ACLs (in update_default_page()!).
	// @@@	use test() function to report actual results instead of assuming success!
	test(__('Setting default ACL').'...', 1);
	mysql_query("insert into ".$config["table_prefix"]."acls set page_tag = 'UserSettings', read_acl = '*', write_acl = '+', comment_read_acl = '*', comment_post_acl = '+'", $dblink);
	test(__('Building links table').'...', 1);
	/**
	 * Script for (re)building links table.
	 */
	include('links.php');

	break;

// The funny upgrading stuff. Make sure these are in order! //
// And yes, there are no break;s here. This is on purpose.  //

// from 0.1 to 0.1.1
case "0.1":
	print('<strong>'.sprintf(__('Wakka %1$s to %2$s'), '0.1', '0.1.1').'</strong><br />'."\n");
	test(sprintf(__('Just slightly altering the %s table'), 'pages').'...', 
		@mysql_query("alter table ".$config["table_prefix"]."pages add body_r text not null default '' after body", $dblink), __('Already done? Hmm!'), 0);
	test(__('Claiming all your base').'...', 1);

// from 0.1.1 to 0.1.2
case "0.1.1":
case "0.1.2":
case "0.1.3-dev":
	print('<strong>'.sprintf(__('Wakka %1$s to %2$s'), '0.1.3-dev', 'Wikka 1.0.0').':</strong><br />'."\n");
	test(__('Adding note column to the pages table').'...',
		@mysql_query("ALTER TABLE ".$config["table_prefix"]."pages ADD note varchar(50) NOT NULL default '' after latest", $dblink), __('Failed.'), 1);
	test(sprintf(__('Just slightly altering the %s table'), 'pages').'...', 
		@mysql_query("alter table ".$config["table_prefix"]."pages DROP COLUMN body_r", $dblink), __('Already done? Hmm!'), 0);
	test(sprintf(__('Just slightly altering the %s table'), 'users').'...', 
		@mysql_query("alter table ".$config["table_prefix"]."users DROP COLUMN motto", $dblink), __('Already done? Hmm!'), 0);
case "1.0":
case "1.0.1":
case "1.0.2":
case "1.0.3":
case "1.0.4":
// from 1.0.4 to 1.0.5
	print('<strong>'.sprintf(__('%1$s to %2$s changes'), '1.0.4', '1.0.5').':</strong><br />'."\n");
case "1.0.5":
case "1.0.6":
	print('<strong>'.sprintf(__('%1$s to %2$s changes'), '1.0.6', '1.1.0').':</strong><br />'."\n");
	test(sprintf(__('Creating %s table'), 'comment').'...',
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."comments (".
			 "id int(10) unsigned NOT NULL auto_increment,".
			 "page_tag varchar(75) NOT NULL default '',".
			 "time datetime NOT NULL default '0000-00-00 00:00:00',".
			 "comment text NOT NULL,".
			 "user varchar(75) NOT NULL default '',".
			 "PRIMARY KEY  (id),".
			 "KEY idx_page_tag (page_tag),".
			 "KEY idx_time (time)".
			") TYPE=MyISAM", $dblink), __('Already done? Hmm!'), 1);
	test(__('Copying comments from the pages table to the new comments table').'...', 
		@mysql_query("INSERT INTO ".$config["table_prefix"]."comments (page_tag, time, comment, user) SELECT comment_on, time, body, user FROM ".$config["table_prefix"]."pages WHERE comment_on != '';", $dblink), __('Already done? Hmm!'), 1);
	test(__('Deleting comments from the pages table').'...', 
		@mysql_query("DELETE FROM ".$config["table_prefix"]."pages WHERE comment_on != ''", $dblink), __('Already done? Hmm!'), 1);
	test(__('Removing comment_on field from the pages table').'...', 
		@mysql_query("ALTER TABLE ".$config["table_prefix"]."pages DROP comment_on", $dblink), __('Already done? Hmm!'), 1);
	test(__('Removing comment pages from the ACL table').'...', 
		@mysql_query("DELETE FROM ".$config["table_prefix"]."acls WHERE page_tag like 'Comment%'", $dblink), __('Already done? Hmm!'), 1);
case "1.1.0":
	print('<strong>'.sprintf(__('%1$s to %2$s changes'), '1.1.0', '1.1.2').':</strong><br />'."\n");
	test(__('Dropping current ACL table structure').'...', 
		@mysql_query("DROP TABLE ".$config["table_prefix"]."acls", $dblink), __('Already done? Hmm!'), 0);
	test(__('Creating new ACL table structure').'...',
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."acls (".
			 "page_tag varchar(75) NOT NULL default '',".
			 "read_acl text NOT NULL,".
			 "write_acl text NOT NULL,".
			 "comment_acl text NOT NULL,".
			 "PRIMARY KEY  (page_tag)".
			") TYPE=MyISAM", $dblink), __('Already exists?'), 1);
case "1.1.2":
case "1.1.3":
	print('<strong>'.sprintf(__('%1$s to %2$s changes'), '1.1.3', '1.1.3.1').':</strong><br />'."\n");
	test(sprintf(__('Altering %s table structure'), 'pages').'...',
		@mysql_query("ALTER TABLE ".$config["table_prefix"]."pages CHANGE tag tag varchar(75) NOT NULL default ''", $dblink), __('Failed').'. ?', 1);
	test(sprintf(__('Altering %s table structure'), 'pages').'...',
		@mysql_query("ALTER TABLE ".$config["table_prefix"]."pages CHANGE user user varchar(75) NOT NULL default ''", $dblink), __('Failed').'. ?', 1);
	test(sprintf(__('Altering %s table structure'), 'pages').'...',
		@mysql_query("ALTER TABLE ".$config["table_prefix"]."pages CHANGE owner owner varchar(75) NOT NULL default ''", $dblink), __('Failed').'. ?', 1);
	test(sprintf(__('Altering %s table structure'), 'pages').'...',
		@mysql_query("ALTER TABLE ".$config["table_prefix"]."pages CHANGE note note varchar(100) NOT NULL default ''", $dblink), __('Failed').'. ?', 1);
	test(sprintf(__('Altering %s table structure'), 'user').'...',
		@mysql_query("ALTER TABLE ".$config["table_prefix"]."users CHANGE name name varchar(75) NOT NULL default ''", $dblink), __('Failed').'. ?', 1);
	test(sprintf(__('Altering %s table structure'), 'comments').'...',
		@mysql_query("ALTER TABLE ".$config["table_prefix"]."comments CHANGE page_tag page_tag varchar(75) NOT NULL default ''", $dblink), __('Failed').'. ?', 1);
	test(sprintf(__('Altering %s table structure'), 'comments').'...',
		@mysql_query("ALTER TABLE ".$config["table_prefix"]."comments CHANGE user user varchar(75) NOT NULL default ''", $dblink), __('Failed').'. ?', 1);
	test(sprintf(__('Altering %s table structure'), 'acls').'...',
		@mysql_query("ALTER TABLE ".$config["table_prefix"]."acls CHANGE page_tag page_tag varchar(75) NOT NULL default ''", $dblink), __('Failed').'. ?', 1);
	test(sprintf(__('Altering %s table structure'), 'links').'...',
		@mysql_query("ALTER TABLE ".$config["table_prefix"]."links CHANGE from_tag from_tag varchar(75) NOT NULL default ''", $dblink), __('Failed').'. ?', 1);
	test(sprintf(__('Altering %s table structure'), 'links').'...',
		@mysql_query("ALTER TABLE ".$config["table_prefix"]."links CHANGE to_tag to_tag varchar(75) NOT NULL default ''", $dblink), __('Failed').'. ?', 1);
	test(sprintf(__('Altering %s table structure'), 'referrers').'...',
		@mysql_query("ALTER TABLE ".$config["table_prefix"]."referrers CHANGE page_tag page_tag varchar(75) NOT NULL default ''", $dblink), __('Failed').'. ?', 1);
	test(sprintf(__('Creating %s table'), 'referrer_blacklist').'...',
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."referrer_blacklist (".
			 "spammer varchar(150) NOT NULL default '',".
			 "KEY idx_spammer (spammer)".
			") TYPE=MyISAM", $dblink), __('Already done? Hmm!'), 1);
	test(sprintf(__('Altering a %s table index'), 'pages').'...', 
		@mysql_query("alter table ".$config["table_prefix"]."pages DROP INDEX tag", $dblink), __('Already done? Hmm!'), 0);
	test(sprintf(__('Altering a %s table index'), 'pages').'...', 
		@mysql_query("alter table ".$config["table_prefix"]."pages ADD FULLTEXT body (body)", $dblink), __('Already done? Hmm!'), 0);
	test(sprintf(__('Altering a %s table index'), 'users').'...', 
		@mysql_query("alter table ".$config["table_prefix"]."users DROP INDEX idx_name", $dblink), __('Already done? Hmm!'), 0);
case "1.1.3.1":
case "1.1.3.2":
	print('<strong>'.sprintf(__('%1$s to %2$s changes'), '1.1.3.2', '1.1.3.3').':</strong><br />'."\n");
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
	update_default_page(array('WikkaReleaseNotes', 'WikkaDocumentation'), $dblink, $config, $lang_defaults_path, $lang_defaults_fallback_path);
	// delete files removed from previous version
	@unlink('actions/wakkabug.php');
	// delete directories that have been moved
	rmdirr("freemind");
	rmdirr("safehtml");
	rmdirr("wikiedit2");
	rmdirr("xml");
case "1.1.6.0":
case "1.1.6.1":
	//adding SysInfo page
	update_default_page('SysInfo', $dblink, $config, $lang_defaults_path, $lang_defaults_fallback_path);
case "1.1.6.2":
case "1.1.6.3":
	update_default_page(array('HighScores', 'FormattingRules'), $dblink, $config, $lang_defaults_path, $lang_defaults_fallback_path);
	test("Altering table users : adding field named `challenge'...", 
	@mysql_query("alter table ".$config["table_prefix"]."users add `challenge` char( 8 ) default '00000000' null", $dblink), "", 0); 
	// @@@	use test() function to report actual results instead of assuming success!
	test(__('Rebuilding links table').'...', 1);
	/**
	 * Script for (re)building links table.
	 */
	include('links.php');
	test(__('Adding fields to comments table to enable threading').'...', 
	mysql_query("alter table ".$config["table_prefix"]."comments add parent int(10) unsigned default NULL", $dblink), "Already done? OK!", 0);
	test(__('Adding fields to comments table to enable threading').'...', 
	mysql_query("alter table ".$config["table_prefix"]."comments add deleted char(1) default NULL", $dblink), "Already done? OK!", 0);
	//dropping obsolete "handler" field from pages table #452
	test(__('Removing handler field from the pages table').'...', 
	@mysql_query("ALTER TABLE ".$config["table_prefix"]."pages DROP handler", $dblink), __('Already done? Hmm!'), 0);
	// Comment threading
	test(__('Adding field to comment table to enable threading').'...', 
	@mysql_query("alter table ".$config["table_prefix"]."comments add status varchar(10) default NULL", $dblink), __('Already done?  OK!'), 0);
	test(__('Adding field to users table to specify comment display default').'...', 
	@mysql_query("alter table ".$config["table_prefix"]."users add default_comment_display int(10) unsigned NOT NULL default '1'", $dblink), __('Already done?  OK!'), 0);
	// Create new fields for comment_read_acl and comment_post_acl,
	// and copy existing comment_acl values to these new fields
	test(__('Creating new comment_read_acl field').'...',
	@mysql_query("alter table ".$config['table_prefix']."acls add comment_read_acl text not null", $dblink), __('Already done?  OK!'), 0);
	test(__('Creating new comment_post_acl field').'...',
	@mysql_query("alter table ".$config['table_prefix']."acls add comment_post_acl text not null", $dblink), __('Already done?  OK!'), 0);
	test(__('Copying existing comment_acls to new fields').'...',
	@mysql_query("update ".$config['table_prefix']."acls as a inner join(select page_tag, comment_acl from ".$config['table_prefix']."acls) as b on a.page_tag = b.page_tag set a.comment_read_acl=b.comment_acl, a.comment_post_acl=b.comment_acl", $dblink), __('Failed').'. ?', 0);
	test(__('Creating new page title field').'...',
	@mysql_query("alter table ".$config['table_prefix']."pages add title varchar(75) default null", $dblink), __('Already done?  OK!'), 0);	// @@@ column position?
	test(__('Creating index on owner column').'...',
	@mysql_query('alter table '.$config['table_prefix'].'pages add index `idx_owner` (`owner`)', $dblink), __('Already done?  OK!'), 0);
	test(__('Dropping unnecessary index `from_tag`').'...',
	@mysql_query('alter table '.$config['table_prefix'].'links drop index `idx_from`', $dblink), __('Already done?  OK!'), 0);
	test("Adding sessions tracking table...",
	mysql_query("create table ".$config['table_prefix']."sessions (sessionid char(40) NOT NULL, userid varchar(75) NOT NULL, PRIMARY KEY (sessionid, userid), session_start datetime NOT NULL)"), "Already done? OK!", 0);
	test("Adding AdminUsers page...", 
	mysql_query("insert into ".$config['table_prefix']."pages set tag = 'AdminUsers', body = '{{adminusers}}\n\n----\nCategoryAdmin', owner = '(Public)', note='".$upgrade_note."', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink), "Already done? OK!", 0); 
	test("Adding AdminPages page...", 
	mysql_query("insert into ".$config['table_prefix']."pages set tag = 'AdminPages', body = '{{adminpages}}\n\n----\nCategoryAdmin', owner = '(Public)', note='".$upgrade_note."', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink), "Already done? OK!", 0); 
case "1.1.6.4":
	update_default_page('FormattingRules', $dblink, $config, $lang_defaults_path, $lang_defaults_fallback_path);
case "trunk": //latest development version from the SVN repository - do not remove
	break;
}

// (directly) use configured location SITE_CONFIGFILE
if (!file_exists(SITE_CONFIGFILE) || !is_writeable(SITE_CONFIGFILE))
{
?>
<p><?php
	printf(__('In the next step, the installer will try to write the updated configuration file, %s').'. ', '<tt>'.SITE_CONFIGFILE.'</tt>');
echo __('Please make sure the web server has write access to the file, or you will have to edit it manually').'.';
printf(__('Once again, see %s for details'), '<a href="http://docs.wikkawiki.org/WikkaInstallation" target="_blank">Wikka:WikkaInstallation</a>');
?>.
</p>
<?php
}
?>

<form action="<?php echo $action_target; ?>" method="post">
<input type="hidden" name="installAction" value="writeconfig" />
<input type="submit" value="<?php echo _p('Continue');?>" />
</form>
<?php /* echo '<pre>'; print_r($_SESSION); echo '</pre>'; */ ?>
