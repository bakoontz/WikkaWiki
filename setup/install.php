<?php
/**
 * Perform the operations required for installing/upgrading Wikka.
 * 
 * @package	Setup
 * @version	$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @todo i18n;
 * @todo drop handler column from pages table as part of #452
 */

// i18n section
if (!defined('ADDING_CONFIG_ENTRY')) define('ADDING_CONFIG_ENTRY', 'Adding a new option to the Wikka config file: %s'); // %s - name of the config option
# Removed DELETING_COOKIES, as Cookies must not be deleted on install, but on wikka.php

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
  			"handler varchar(30) NOT NULL default 'page',".
  			"PRIMARY KEY  (id),".
  			"KEY idx_tag (tag),".
  			"FULLTEXT KEY body (body),".
  			"KEY idx_time (time),".
  			"KEY idx_latest (latest)".
			") TYPE=MyISAM;", $dblink), __('Already exists?'), 0);
	test(sprintf(__('Creating %s table'), __('ACL')).'...',
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."acls (".
  			"page_tag varchar(75) NOT NULL default '',".
  			"read_acl text NOT NULL,".
  			"write_acl text NOT NULL,".
  			"comment_acl text NOT NULL,".
 			"PRIMARY KEY  (page_tag)".
			") TYPE=MyISAM", $dblink), __('Already exists?'), 0);
	test(sprintf(__('Creating %s table'), __('link tracking')).'...',
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."links (".
			"from_tag varchar(75) NOT NULL default '',".
  			"to_tag varchar(75) NOT NULL default '',".
  			"UNIQUE KEY from_tag (from_tag,to_tag),".
  			"KEY idx_from (from_tag),".
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
  			"deleted char(1) default NULL,".
  			"PRIMARY KEY  (id),".
  			"KEY idx_page_tag (page_tag),".
  			"KEY idx_time (time)".
			") TYPE=MyISAM;", $dblink), __('Already exists?'), 0);

    test(__('Adding admin user').'...', 
    @mysql_query("insert into ".$config["table_prefix"]."users set name = '".$config["admin_users"]."', password = md5('".mysql_real_escape_string($_SESSION['wikka']['install']['password'])."'), email = '".$config["admin_email"]."', signuptime = now()", $dblink), "Hmm!", 0);

	update_default_page(array(
    $config["root_page"], 
    'RecentChanges', 
    'RecentlyCommented', 
    'UserSettings', 
    'PageIndex', 
    'WikkaReleaseNotes', 
    'WikkaDocumentation', 
    'WantedPages', 
    'OrphanedPages', 
    'TextSearch', 
    'TextSearchExpanded', 
    'MyPages', 
    'MyChanges', 
    'InterWiki', 
    'PasswordForgotten', 
    'WikiCategory', 
    'CategoryWiki', 
    'CategoryCategory', 
    'FormattingRules', 
    'HighScores', 
    'OwnedPages', 
    'SandBox', 
    'SysInfo'), $dblink, $config); 

	test(__('Setting default ACL').'...', 1);
	mysql_query("insert into ".$config["table_prefix"]."acls set page_tag = 'UserSettings', read_acl = '*', write_acl = '+', comment_acl = '+'", $dblink);
	test(__('Building links table').'...', 1);
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
	test(sprintf(ADDING_CONFIG_ENTRY, 'double_doublequote_html'), 1);
	$config['double_doublequote_html'] = 'safe';
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
	update_default_page(array('WikkaReleaseNotes', 'WikkaDocumentation'));
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
	$config["wiki_suffix"] = '@wikka';
	test(sprintf(ADDING_CONFIG_ENTRY, 'require_edit_note'), 1);
	$config["require_edit_note"] = '0';
	test(sprintf(ADDING_CONFIG_ENTRY, 'public_sysinfo'), 1);
	$config["public_sysinfo"] = '0';
	//adding SysInfo page
	update_default_page('SysInfo', $dblink, $config);
case "1.1.6.2":
	test(sprintf(ADDING_CONFIG_ENTRY, '<tt>allow_user_registration</tt>'), 1);
	$config["allow_user_registration"] = '1';
	test(sprintf(ADDING_CONFIG_ENTRY, '<tt>invitation_code</tt>'), 1);
	$config["invitation_code"] = '';
	test(sprintf(ADDING_CONFIG_ENTRY, '<tt>enable_user_host_lookup</tt>'), 1);
	$config["enable_user_host_lookup"] = '1';	
	test(sprintf(ADDING_CONFIG_ENTRY, '<tt>enable_rss_autodiscovery</tt>'), 1);
	$config["enable_rss_autodiscovery"] = '1';	
	test(sprintf(ADDING_CONFIG_ENTRY, '<tt>edit_buttons_position</tt>'), 1);
	$config["edit_buttons_position"] = 'bottom';
	test(sprintf(ADDING_CONFIG_ENTRY, '<tt>owner_delete_page</tt>'), 1);
	$config["owner_delete_page"] = '0';	
	update_default_page(array('HighScores', 'FormattingRules'), $dblink, $config);
	test("Altering table users : adding field named `challenge'...", 
	@mysql_query("alter table ".$config["table_prefix"]."users add `challenge` char( 8 ) default '00000000' null", $dblink), "", 0); 
	test(__('Rebuilding links table').'...', 1);
	include('links.php');
	test(__('Adding fields to comments table to enable threading').'...', 
	mysql_query("alter table ".$config["table_prefix"]."comments add parent int(10) unsigned default NULL", $dblink), "Already done? OK!", 0);
	test(__('Adding fields to comments table to enable threading').'...', 
	mysql_query("alter table ".$config["table_prefix"]."comments add deleted char(1) default NULL", $dblink), "Already done? OK!", 0);
case "trunk": //latest development version from the SVN repository - do not remove
	break;
}

if (!file_exists($wakkaConfigLocation) || !is_writeable($wakkaConfigLocation))
{
?>
<p><?php printf(__('In the next step, the installer will try to write the updated configuration file, %s').'. ', '<tt>'.$wakkaConfigLocation.'</tt>');
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
