<?php

/* DB update file for mysql
 * 
 * Not intended for standalone use!
 */

switch($version) {
	// new installation
	case "0":
		print("<h2>Installing Stuff</h2>");
		test("Setting up database for UTF-8...", true);
		$dblink->exec("ALTER DATABASE ".$config['dbms_database']." DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;");
		test("Creating page table...",
			db_query(
				"CREATE TABLE ".$config['table_prefix']."pages (".
				"id int(10) unsigned NOT NULL auto_increment,".
				"tag varchar(75) NOT NULL default '',".
				"title varchar(75) NOT NULL default '',".
				"time datetime NOT NULL default '1900-01-01 00:00:00',".
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
				") CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=MyISAM", NULL, $dblink), "Already exists?", 0);
		test("Creating ACL table...",
			db_query(
				"CREATE TABLE ".$config['table_prefix']."acls (".
				"page_tag varchar(75) NOT NULL default '',".
				"read_acl text NOT NULL,".
				"write_acl text NOT NULL,".
				"comment_read_acl text NOT NULL,".
				"comment_post_acl text NOT NULL,".
				"PRIMARY KEY  (page_tag)".
				") CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=MyISAM", NULL, $dblink), "Already exists?", 0);
		test("Creating link tracking table...",
			db_query(
				"CREATE TABLE ".$config['table_prefix']."links (".
				"from_tag varchar(75) NOT NULL default '',".
				"to_tag varchar(75) NOT NULL default '',".
				"UNIQUE KEY from_tag (from_tag,to_tag),".
				"KEY idx_to (to_tag)".
				") CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=MyISAM", NULL, $dblink), "Already exists?", 0);
		test("Creating referrer table...",
			db_query(
				"CREATE TABLE ".$config['table_prefix']."referrers (".
				"page_tag varchar(75) NOT NULL default '',".
				"referrer varchar(255) NOT NULL default '',".
				"time datetime NOT NULL default '1900-01-01 00:00:00',".
				"KEY idx_page_tag (page_tag),".
				"KEY idx_time (time)".
				") CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=MyISAM", NULL, $dblink), "Already exists?", 0);
		test("Creating referrer blacklist table...",
			db_query(
				"CREATE TABLE ".$config['table_prefix']."referrer_blacklist (".
				"spammer varchar(255) NOT NULL default '',".
				"KEY idx_spammer (spammer)".
				") CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=MyISAM", NULL, $dblink), "Already exists?", 0);
		test("Creating user table...",
			db_query(
				"CREATE TABLE ".$config['table_prefix']."users (".
				"name varchar(75) NOT NULL default '',".
				"password varchar(32) NOT NULL default '',".
				"email varchar(50) NOT NULL default '',".
				"revisioncount int(10) unsigned NOT NULL default '20',".
				"changescount int(10) unsigned NOT NULL default '50',".
				"doubleclickedit enum('Y','N') NOT NULL default 'Y',".
				"signuptime datetime NOT NULL default '1900-01-01 00:00:00',".
				"show_comments enum('Y','N') NOT NULL default 'N',".
				"status enum('invited','signed-up','pending','active','suspended','banned','deleted'),".
				"theme varchar(50) default '',".
				"default_comment_display int(1) NOT NULL default '3',". // threaded is default
				"challenge varchar(8) default '',". // refs #1023
				"PRIMARY KEY  (name),".
				"KEY idx_signuptime (signuptime)".
				") CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=MyISAM", NULL, $dblink), "Already exists?", 0);
		test("Creating comment table...",
			db_query(
				"CREATE TABLE ".$config['table_prefix']."comments (".
				"id int(10) unsigned NOT NULL auto_increment,".
				"page_tag varchar(75) NOT NULL default '',".
				"time datetime NOT NULL default '1900-01-01 00:00:00',".
				"comment text NOT NULL,".
				"user varchar(75) NOT NULL default '',".
				"parent int(10) unsigned default NULL,". 
				"status enum('deleted') default NULL,".
				"deleted char(1) default NULL,".
				"PRIMARY KEY  (id),".
				"KEY idx_page_tag (page_tag),".
				"KEY idx_time (time)".
				") CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=MyISAM", NULL, $dblink), "Already exists?", 0);
		test("Creating session tracking table...",
			db_query(
				"CREATE TABLE ".$config['table_prefix']."sessions (".
				"sessionid char(32) NOT NULL,".
				"userid varchar(75) NOT NULL,".
				"PRIMARY KEY (sessionid, userid),".
				"session_start datetime NOT NULL".
				") CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=MyISAM", NULL, $dblink), "Already exists?", 0);

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
		'TextSearch', 
		'TextSearchExpanded', 
		'UserSettings', 
		'WantedPages', 
		'WikiCategory', 
		'WikkaConfig', 
		'WikkaDocumentation', 
        'WikkaInstaller',
		'WikkaMenulets',
		'WikkaReleaseNotes'), $dblink, $config, $lang_defaults_path, $lang_defaults_fallback_path); 

		test('Building links table...', 1);
		/**
		 * Script for (re)building links table.
		 */
		require('setup/links.php');

		// @@@	?? *default* ACLs are in the configuration file; settings on UserSettings page are irrelevant for default ACLs!
		//		use page-specific "ACL" files to create page-specific ACLs (in update_default_page()!).
		// @@@	use test() function to report actual results instead of assuming success!
		test("Setting default ACL...", 1);
		db_query("insert into ".$config['table_prefix']."acls set page_tag = 'UserSettings', read_acl = '*', write_acl = '+', comment_read_acl = '*', comment_post_acl = '+'", NULL, $dblink);
		db_query("insert into ".$config['table_prefix']."acls set page_tag = 'AdminUsers', read_acl = '!*', write_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", NULL, $dblink);
		db_query("insert into ".$config['table_prefix']."acls set page_tag = 'AdminPages', read_acl = '!*', write_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", NULL, $dblink);
		db_query("insert into ".$config['table_prefix']."acls set page_tag = 'SysInfo', read_acl = '!*', write_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", NULL, $dblink);
		db_query("insert into ".$config['table_prefix']."acls set page_tag = 'WikkaConfig', read_acl = '!*', write_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", NULL, $dblink);
		db_query("insert into ".$config['table_prefix']."acls set page_tag = 'DatabaseInfo', read_acl = '!*', write_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", NULL, $dblink);
		db_query("insert into ".$config['table_prefix']."acls set page_tag = 'WikkaMenulets', read_acl = '!*', write_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", NULL, $dblink);
		db_query("insert into ".$config['table_prefix']."acls set page_tag = 'AdminBadWords', read_acl = '!*', write_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", NULL, $dblink);
		db_query("insert into ".$config['table_prefix']."acls set page_tag = 'AdminSpamLog', read_acl = '!*', write_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", NULL, $dblink);
		db_query("insert into ".$config['table_prefix']."acls set page_tag = 'WikkaInstaller', read_acl = '!*', write_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", NULL, $dblink);

		// Register admin user
		$challenge = dechex(crc32(time()));
		$pass_val = md5($challenge.$_POST['password']);
		$name = $config['admin_users'];
		$email = $config['admin_email'];
		// Delete existing admin user in case installer was run twice
		db_query('delete from '.$config['table_prefix'].'users where name = :name', array(':name' => $name), $dblink);
		test(__('Adding admin user').'...',
				db_query("insert into ".$config["table_prefix"]."users set name = :name, password = :pass_val, email = :email, signuptime = now(), challenge= :challenge", array(':name' => $name, ':pass_val' => $pass_val, ':email' => $email, ':challenge' => $challenge), $dblink), "Hmm!", 0);

		// Register WikkaInstaller user
		$challenge = dechex(crc32(time()));
		$pass_val = md5($challenge.$_POST['password']);
		$name = 'WikkaInstaller';
		$email = $config['admin_email'];
		// Delete existing WikkaInstaller user in case installer was run twice
		db_query('delete from '.$config['table_prefix'].'users where name = :name', array(':name' => $name), $dblink);
		test(__('Adding WikkaInstaller user').'...',
				db_query("insert into ".$config["table_prefix"]."users set name = :name, password = :pass_val, email = :email, signuptime = now(), challenge= :challenge", array(':name' => $name, ':pass_val' => $pass_val, ':email' => $email, ':challenge' => $challenge), $dblink), "Hmm!", 0);

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
			db_query("alter table ".$config['table_prefix']."pages add body_r text not null default '' after body", NULL, $dblink), "Already done? Hmm!", 0);
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
			db_query("ALTER TABLE ".$config['table_prefix']."pages ADD note varchar(50) NOT NULL default '' after latest", NULL, $dblink), "Failed.", 1);
		test("Just slightly altering the pages table...",
			db_query("alter table ".$config['table_prefix']."pages DROP COLUMN body_r", NULL, $dblink), "Already done? Hmm!", 0);
		test("Just slightly altering the users table...",
			db_query("alter table ".$config['table_prefix']."users DROP COLUMN motto", NULL, $dblink), "Already done? Hmm!", 0);
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
			db_query(
				"CREATE TABLE ".$config['table_prefix']."comments (".
				"id int(10) unsigned NOT NULL auto_increment,".
				"page_tag varchar(75) NOT NULL default '',".
				"time datetime NOT NULL default '1900-01-01 00:00:00',".
				"comment text NOT NULL,".
				"user varchar(75) NOT NULL default '',".
				"PRIMARY KEY  (id),".
				"KEY idx_page_tag (page_tag),".
				"KEY idx_time (time)".
				") ENGINE=MyISAM", NULL, $dblink), "Already done? Hmm!", 1);
		test("Copying comments from the pages table to the new comments table...",
			db_query("INSERT INTO ".$config['table_prefix']."comments (page_tag, time, comment, user) SELECT comment_on, time, body, user FROM ".$config['table_prefix']."pages WHERE comment_on != '';", NULL, $dblink), "Already done? Hmm!", 1);
		test("Deleting comments from the pages table...",
			db_query("DELETE FROM ".$config['table_prefix']."pages WHERE comment_on != ''", NULL, $dblink), "Already done? Hmm!", 1);
		test("Removing comment_on field from the pages table...",
			db_query("ALTER TABLE ".$config['table_prefix']."pages DROP comment_on", NULL, $dblink), "Already done? Hmm!", 1);
		test("Removing comment pages from the ACL table...",
			db_query("DELETE FROM ".$config['table_prefix']."acls WHERE page_tag like 'Comment%'", NULL, $dblink), "Already done? Hmm!", 1);
	case "1.1.0":
		print("<strong>1.1.0 to 1.1.2 changes:</strong><br />\n");
		test("Dropping current ACL table structure...",
			db_query("DROP TABLE ".$config['table_prefix']."acls", NULL, $dblink), "Already done? Hmm!", 0);
		test("Creating new ACL table structure...",
			db_query(
				"CREATE TABLE ".$config['table_prefix']."acls (".
				"page_tag varchar(75) NOT NULL default '',".
				"read_acl text NOT NULL,".
				"write_acl text NOT NULL,".
				"comment_acl text NOT NULL,".
				"PRIMARY KEY  (page_tag)".
				") ENGINE=MyISAM", NULL, $dblink), "Already exists?", 1);
	case "1.1.2":
	case "1.1.3":
		print("<strong>1.1.3 to 1.1.3.1 changes:</strong><br />\n");
		test("Altering pages table structure...",
			db_query("ALTER TABLE ".$config['table_prefix']."pages CHANGE tag tag varchar(75) NOT NULL default ''", NULL, $dblink), "Failed. ?", 1);
		test("Altering pages table structure...",
			db_query("ALTER TABLE ".$config['table_prefix']."pages CHANGE user user varchar(75) NOT NULL default ''", NULL, $dblink), "Failed. ?", 1);
		test("Altering pages table structure...",
			db_query("ALTER TABLE ".$config['table_prefix']."pages CHANGE owner owner varchar(75) NOT NULL default ''", NULL, $dblink), "Failed. ?", 1);
		test("Altering pages table structure...",
			db_query("ALTER TABLE ".$config['table_prefix']."pages CHANGE note note varchar(100) NOT NULL default ''", NULL, $dblink), "Failed. ?", 1);
		test("Altering user table structure...",
			db_query("ALTER TABLE ".$config['table_prefix']."users CHANGE name name varchar(75) NOT NULL default ''", NULL, $dblink), "Failed. ?", 1);
		test("Altering comments table structure...",
			db_query("ALTER TABLE ".$config['table_prefix']."comments CHANGE page_tag page_tag varchar(75) NOT NULL default ''", NULL, $dblink), "Failed. ?", 1);
		test("Altering comments table structure...",
			db_query("ALTER TABLE ".$config['table_prefix']."comments CHANGE user user varchar(75) NOT NULL default ''", NULL, $dblink), "Failed. ?", 1);
		test("Altering acls table structure...",
			db_query("ALTER TABLE ".$config['table_prefix']."acls CHANGE page_tag page_tag varchar(75) NOT NULL default ''", NULL, $dblink), "Failed. ?", 1);
		test("Altering links table structure...",
			db_query("ALTER TABLE ".$config['table_prefix']."links CHANGE from_tag from_tag varchar(75) NOT NULL default ''", NULL, $dblink), "Failed. ?", 1);
		test("Altering links table structure...",
			db_query("ALTER TABLE ".$config['table_prefix']."links CHANGE to_tag to_tag varchar(75) NOT NULL default ''", NULL, $dblink), "Failed. ?", 1);
		test("Altering referrers table structure...",
			db_query("ALTER TABLE ".$config['table_prefix']."referrers MODIFY referrer varchar(150) NOT NULL default ''", NULL, $dblink), "Failed. ?", 1);
		test("Creating referrer_blacklist table...",
			db_query(
				"CREATE TABLE ".$config['table_prefix']."referrer_blacklist (".
				"spammer varchar(150) NOT NULL default '',".
				"KEY idx_spammer (spammer)".
				") ENGINE=MyISAM", NULL, $dblink), "Already exists? Hmm!", 1);
		test("Altering a pages table index...",
			db_query("alter table ".$config['table_prefix']."pages DROP INDEX tag", NULL, $dblink), "Already done? Hmm!", 0);
		test("Altering a pages table index...",
			db_query("alter table ".$config['table_prefix']."pages ADD FULLTEXT body (body)", NULL, $dblink), "Already done? Hmm!", 0);
		test("Altering a users table index...",
			db_query("alter table ".$config['table_prefix']."users DROP INDEX idx_name", NULL, $dblink), "Already done? Hmm!", 0);
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
		db_query("alter table ".$config['table_prefix']."users add column status enum ('invited','signed-up','pending','active','suspended','banned','deleted')", NULL, $dblink), "Already done? OK!", 0); 
		test("Adding sessions tracking table...",
		db_query("create table ".$config['table_prefix']."sessions (sessionid char(32) NOT NULL, userid varchar(75) NOT NULL, PRIMARY KEY (sessionid, userid), session_start datetime NOT NULL)", NULL, $dblink),	"Already done? OK!", 0); 
		test('Dropping obsolete index `from_tag`...',
		db_query('alter table '.$config['table_prefix'].'links drop index `idx_from`', NULL, $dblink), 'Already done?  OK!', 0);
	case "1.1.6.4":
	case "1.1.6.5":
	case "1.1.6.6":
	case "1.1.6.7":
		print("<strong>1.1.6.7 to 1.2 changes:</strong><br />\n");
		test("Adding theme field to user preference table...",
		db_query("ALTER TABLE ".$config['table_prefix']."users ADD theme varchar(50) default ''", NULL, $dblink), "Already done? OK!", 0);
		test("Setting default UserSettings ACL...",
		db_query("insert into ".$config['table_prefix']."acls set page_tag = 'UserSettings', comment_read_acl = '*', comment_post_acl = '+'", NULL, $dblink), __('Already done? OK!'), 0);
		test("Setting default AdminUsers ACL...",
		db_query("insert into ".$config['table_prefix']."acls set page_tag = 'AdminUsers', read_acl = '!*', write_acl = '!*', comment_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", NULL, $dblink), __('Already done? OK!'), 0);
		test("Setting default AdminPages ACL...",
		db_query("insert into ".$config['table_prefix']."acls set page_tag = 'AdminPages', read_acl = '!*', write_acl = '!*', comment_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", NULL, $dblink), __('Already done? OK!'), 0);
		test("Setting default DatabaseInfo ACL...",
		db_query("insert into ".$config['table_prefix']."acls set page_tag = 'DatabaseInfo', read_acl = '!*', write_acl = '!*', comment_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", NULL, $dblink), __('Already done? OK!'), 0);
		update_default_page('FormattingRules', $dblink, $config, $lang_defaults_path, $lang_defaults_fallback_path, $upgrade_note);
	case "1.2":
		print("<strong>1.2 to 1.3.1 changes:</strong><br />\n");
		// Dropping obsolete "handler" field from pages table, refs #452
		test('Removing handler field from the pages table...',
		db_query("ALTER TABLE ".$config["table_prefix"]."pages DROP handler", NULL, $dblink), __('Already done? OK!'), 0);
		// Support for threaded comments
		test("Adding fields to comments table to enable threading...",  
		db_query("alter table ".$config["table_prefix"]."comments add parent int(10) unsigned default NULL", NULL, $dblink), "Already done? OK!", 0);
		test("Adding fields to comments table to enable threading...",
		db_query("alter table ".$config["table_prefix"]."users add default_comment_display enum('date_asc', 'date_desc', 'threaded') NOT NULL default 'threaded'", NULL, $dblink), "Already done? OK!", 0);
		test("Adding fields to comments table to enable threading...",  
		db_query("alter table ".$config["table_prefix"]."comments add status enum('deleted') default NULL", NULL, $dblink), "Already done? OK!", 0);
		// Create new fields for comment_read_acl and comment_post_acl, 
		// and copy existing comment_acl values to these new fields 
		test('Creating new comment_read_acl field...', 
		db_query("alter table ".$config['table_prefix']."acls add comment_read_acl text not null", NULL, $dblink), __('Already done?  OK!'), 0); 
		test('Creating new comment_post_acl field...', 
		db_query("alter table ".$config['table_prefix']."acls add comment_post_acl text not null", NULL, $dblink), __('Already done?  OK!'), 0); 
		test('Copying existing comment_acls to new fields...', 
		db_query("update ".$config['table_prefix']."acls as a inner join(select page_tag, comment_acl from ".$config['table_prefix']."acls) as b on a.page_tag = b.page_tag set a.comment_read_acl=b.comment_acl, a.comment_post_acl=b.comment_acl", NULL, $dblink), __('Already done?  OK!'), 0);
		test('Drop old comment acl...', 
		db_query("alter table ".$config['table_prefix']."acls drop comment_acl", NULL, $dblink), __('Already done?  OK!'), 0);
		test(__('Creating index on owner column').'...', 
		db_query('alter table '.$config['table_prefix'].'pages add index `idx_owner` (`owner`)', NULL, $dblink), __('Already done?  OK!'), 0); 
		test(__('Altering referrers table structure').'...',
		db_query("ALTER TABLE ".$config['table_prefix']."referrers MODIFY referrer varchar(255) NOT NULL default ''", NULL, $dblink), __('Already done?  OK!'), 0);
		test(__('Altering referrer blacklist table structure').'...',
		db_query("ALTER TABLE ".$config['table_prefix']."referrer_blacklist MODIFY spammer varchar(255) NOT NULL default ''", NULL, $dblink), __('Already done?  OK!'), 0);
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
		db_query("insert into ".$config['table_prefix']."acls set page_tag = 'WikkaMenulets', read_acl = '!*', write_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", NULL, $dblink);
		db_query("insert into ".$config['table_prefix']."acls set page_tag = 'AdminBadWords', read_acl = '!*', write_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", NULL, $dblink);
		db_query("insert into ".$config['table_prefix']."acls set page_tag = 'AdminSpamLog', read_acl = '!*', write_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", NULL, $dblink);
		// Converting DB UTF-8 (but data remains
		// unchanged -- this is handled by a standalone script)	
		test("Setting up database for UTF-8...", true);
		db_query("ALTER DATABASE ".$config['dbms_database']." DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci", NULL, $dblink);
		// Converting pages table and fields to UTF-8
		test("Setting up pages table and fields for UTF-8...", true);
		db_query("ALTER TABLE ".$config['table_prefix']."pages DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."pages CHANGE `tag` `tag` VARCHAR( 75 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."pages CHANGE `body` `body` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."pages CHANGE `owner` `owner` VARCHAR( 75 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."pages CHANGE `user` `user` VARCHAR( 75 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."pages CHANGE `latest` `latest` ENUM( 'Y','N' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default 'N'", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."pages CHANGE `note` `note` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", NULL, $dblink); // refs #1021
		// Converting acls table and fields to UTF-8
		test("Setting up acls table and fields for UTF-8...", true);
		db_query("ALTER TABLE ".$config['table_prefix']."acls DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."acls CHANGE `page_tag` `page_tag` VARCHAR( 75 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."acls CHANGE `read_acl` `read_acl` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."acls CHANGE `write_acl` `write_acl` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."acls CHANGE `comment_read_acl` `comment_read_acl` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."acls CHANGE `comment_post_acl` `comment_post_acl` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL", NULL, $dblink);
		test("Setting up links table and fields for UTF-8...", true);
		db_query("ALTER TABLE ".$config['table_prefix']."links DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."links CHANGE `from_tag` `from_tag` VARCHAR( 75 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."links CHANGE `to_tag` `to_tag` VARCHAR( 75 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", NULL, $dblink);
		// Converting referrers table and fields to UTF-8
		test("Setting up referrers table and fields for UTF-8...", true);
		db_query("ALTER TABLE ".$config['table_prefix']."referrers DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."referrers CHANGE `page_tag` `page_tag` VARCHAR( 75 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."referrers CHANGE `referrer` `referrer` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", NULL, $dblink);
		// Converting referrer_blacklist table and fields to UTF-8
		test("Setting up referrer_blacklist table and fields for UTF-8...", true);
		db_query("ALTER TABLE ".$config['table_prefix']."referrer_blacklist DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."referrer_blacklist CHANGE `spammer` `spammer` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", NULL, $dblink);
		// Converting users table and fields to UTF-8
		test("Setting up users table and fields for UTF-8...", true);
		db_query("ALTER TABLE ".$config['table_prefix']."users DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."users CHANGE `name` `name` VARCHAR( 75 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."users CHANGE `password` `password` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."users CHANGE `email` `email` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."users CHANGE `doubleclickedit` `doubleclickedit` ENUM( 'Y','N' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default 'Y'", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."users CHANGE `show_comments` `show_comments` ENUM( 'Y','N' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default 'N'", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."users CHANGE `default_comment_display` `default_comment_display` ENUM( 'date_asc','date_desc','threaded' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default 'threaded'", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."users CHANGE `status` `status` ENUM( 'invited','signed-up','pending','active','suspended','banned','deleted') CHARACTER SET utf8 COLLATE utf8_unicode_ci", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."users CHANGE `theme` `theme` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci default ''", NULL, $dblink); // refs #1022
		// Converting comments table and fields to UTF-8
		test("Setting up comments table and fields for UTF-8...", true);
		db_query("ALTER TABLE ".$config['table_prefix']."comments DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."comments CHANGE `page_tag` `page_tag` VARCHAR( 75 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."comments CHANGE `comment` `comment` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."comments CHANGE `user` `user` VARCHAR( 75 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL default ''", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."comments CHANGE `status` `status` ENUM( 'deleted' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci default NULL", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."comments CHANGE `deleted` `deleted` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci default NULL", NULL, $dblink);
		// Converting sessions table and fields to UTF-8
		test("Setting up sessions table and fields for UTF-8...", true);
		db_query("ALTER TABLE ".$config['table_prefix']."sessions DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci", NULL, $dblink);
		db_query("ALTER TABLE ".$config['table_prefix']."sessions CHANGE `sessionid` `sessionid` CHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL", NULL, $dblink); // refs #1022
		db_query("ALTER TABLE ".$config['table_prefix']."sessions CHANGE `userid` `userid` VARCHAR( 75 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL", NULL, $dblink);
		// Adding challenge, refs #1023
		test("Adding/updating challenge field to users table to improve security...",  
		db_query("alter table ".$config["table_prefix"]."users ADD challenge varchar(8) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT ''", NULL, $dblink), __("Already done? OK!"), 0);
		db_query("alter table ".$config["table_prefix"]."users CHANGE `challenge` `challenge` varchar(8) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT ''", NULL, $dblink);
		db_query("UPDATE ".$config['table_prefix']."users SET challenge='' WHERE challenge='00000000'", NULL, $dblink);
	case "1.3.2": 
		print("<strong>1.3.2 to 1.3.3 changes:</strong><br />\n");
		test("Adding/updating title field to users page ...",  
		db_query("alter table `".$config["table_prefix"]."pages` ADD `title` varchar(75) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '' AFTER `tag`", NULL, $dblink), __("Already done? OK!"), 0); // refs #529
	case "1.3.6":
		print("<strong>1.3.5 to 1.3.6 changes:</strong><br />\n");
		test("Changing \"default\" theme references to \"classic\" theme ...",  
		db_query("UPDATE `".$config["table_prefix"]."users` SET theme='classic' WHERE theme='default'", NULL, $dblink), __("Already done? OK!"), 0);
	case "1.3.7":
		print("<strong>1.3.7 to 1.4.0 changes:</strong><br />\n");
		// delete file removed from previous version
		@unlink('lang/en/defaults/TableMarkupReference.php');
		// Change datetime default to '1900-01-01' for MySQL > 5.7 compatibility
		test("Altering pages table structure...",
			db_query("ALTER TABLE ".$config['table_prefix']."pages CHANGE time time datetime NOT NULL default '1900-01-01 00:00:00'", NULL, $dblink), "Failed. ?", 1);
		test("Altering referrers table structure...",
			db_query("ALTER TABLE ".$config['table_prefix']."referrers CHANGE time time datetime NOT NULL default '1900-01-01 00:00:00'", NULL, $dblink), "Failed. ?", 1);
		test("Altering users table structure...",
			db_query("ALTER TABLE ".$config['table_prefix']."users CHANGE signuptime signuptime datetime NOT NULL default '1900-01-01 00:00:00'", NULL, $dblink), "Failed. ?", 1);
		test("Altering comments table structure...",
			db_query("ALTER TABLE ".$config['table_prefix']."comments CHANGE time time datetime NOT NULL default '1900-01-01 00:00:00'", NULL, $dblink), "Failed. ?", 1);
	case "1.4.0":
        print("<strong>1.4.0 to 1.4.1 changes:</strong><br />\n");
		update_default_page(array('WikkaInstaller'), $dblink, $config, $lang_defaults_path, $lang_defaults_fallback_path); 
		#db_query("insert into ".$config['table_prefix']."acls set page_tag = 'WikkaInstaller', read_acl = '!*', write_acl = '!*', comment_read_acl = '!*', comment_post_acl = '!*'", NULL, $dblink);
		test("Altering users table structure (default_comment_display)...",
			db_query("ALTER TABLE ".$config['table_prefix']."users CHANGE default_comment_display default_comment_display int NOT NULL default 3", NULL, $dblink), "Failed. ?", 1);
		// Register WikkaInstaller user
		$challenge = dechex(crc32(time()));
		$pass_val = md5($challenge.$_POST['password']);
		$name = 'WikkaInstaller';
		$email = $config['admin_email'];
		// Delete existing WikkaInstaller user in case installer was run twice
		db_query('delete from '.$config['table_prefix'].'users where name = :name', array(':name' => $name), $dblink);
		test(__('Adding WikkaInstaller user').'...', db_query("insert into ".$config["table_prefix"]."users set name = :name, password = :pass_val, email = :email, signuptime = now(), challenge= :challenge", array(':name' => $name, ':pass_val' => $pass_val, ':email' => $email, ':challenge' => $challenge), $dblink), "Hmm!", 0);
    case "1.4.1":
    case "1.4.2":
    case "master":
}

?>
