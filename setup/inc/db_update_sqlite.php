<?php

/* DB update file for sqlite
 * 
 * Not intended for standalone use!
 */

switch($version) {
	// new installation
	case "0":
		print("<h2>Installing Stuff</h2>");
		test("Creating ACL table...",
			db_query(
			  'CREATE TABLE IF NOT EXISTS "'.$config['table_prefix'].'acls" (
			  "page_tag" varchar(75) NOT NULL DEFAULT \'\',
			  "read_acl" text NOT NULL,
			  "write_acl" text NOT NULL,
			  "comment_read_acl" text NOT NULL,
			  "comment_post_acl" text NOT NULL,
			  PRIMARY KEY ("page_tag"))', NULL, $dblink), "Already exists?", 0);
		test("Create comments table...",
			db_query(
			  'CREATE TABLE IF NOT EXISTS "'.$config['table_prefix'].'comments" (
			  "id" INTEGER  NOT NULL ,
			  "page_tag" varchar(75) NOT NULL DEFAULT \'\',
			  "time" datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\',
			  "comment" text NOT NULL,
			  "user" varchar(75) NOT NULL DEFAULT \'\',
			  "parent" int(10)  DEFAULT NULL,
			  "status" text  DEFAULT NULL,
			  "deleted" char(1) DEFAULT NULL,
			  PRIMARY KEY ("id" ASC))', NULL, $dblink), "Already exists?", 0);
		test("Create links table...",
			db_query(
			  'CREATE TABLE IF NOT EXISTS "'.$config['table_prefix'].'links" (
			  "from_tag" varchar(75) NOT NULL DEFAULT \'\',
			  "to_tag" varchar(75) NOT NULL DEFAULT \'\')', NULL, $dblink), "Already exists?", 0);
		test("Create pages table...",
			db_query(
			  'CREATE TABLE IF NOT EXISTS "'.$config['table_prefix'].'pages" (
			  "id" INTEGER  NOT NULL ,
			  "tag" varchar(75) NOT NULL DEFAULT \'\',
			  "title" varchar(75) NOT NULL DEFAULT \'\',
			  "time" datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\',
			  "body" mediumtext NOT NULL,
			  "owner" varchar(75) NOT NULL DEFAULT \'\',
			  "user" varchar(75) NOT NULL DEFAULT \'\',
			  "latest" text  NOT NULL DEFAULT \'N\',
			  "note" varchar(100) NOT NULL DEFAULT \'\',
			  PRIMARY KEY ("id" ASC))', NULL, $dblink), "Already exists?", 0);
		test("Create blacklist table...",
			db_query(
			  'CREATE TABLE IF NOT EXISTS "'.$config['table_prefix'].'referrer_blacklist" (
			  "spammer" varchar(255) NOT NULL DEFAULT \'\')', NULL, $dblink), "Already exists?", 0);
		test("Create referrers table...",
			db_query(
			  'CREATE TABLE IF NOT EXISTS "'.$config['table_prefix'].'referrers" (
			  "page_tag" varchar(75) NOT NULL DEFAULT \'\',
			  "referrer" varchar(255) NOT NULL DEFAULT \'\',
			  "time" datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\')', NULL, $dblink), "Already exists?", 0);
		test("Create sessions table...",
			db_query(
			  'CREATE TABLE IF NOT EXISTS "'.$config['table_prefix'].'sessions" (
			  "sessionid" char(32) NOT NULL,
			  "userid" varchar(75) NOT NULL,
			  "session_start" datetime NOT NULL,
			  PRIMARY KEY ("sessionid","userid"))', NULL, $dblink), "Already exists?", 0);
		test("Create users table...",
			db_query(
			  'CREATE TABLE IF NOT EXISTS "'.$config['table_prefix'].'users" (
			  "name" varchar(75) NOT NULL DEFAULT \'\',
			  "password" varchar(32) NOT NULL DEFAULT \'\',
			  "email" varchar(50) NOT NULL DEFAULT \'\',
			  "revisioncount" int(10)  NOT NULL DEFAULT \'20\',
			  "changescount" int(10)  NOT NULL DEFAULT \'50\',
			  "doubleclickedit" text  NOT NULL DEFAULT \'Y\',
			  "signuptime" datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\',
			  "show_comments" text  NOT NULL DEFAULT \'N\',
			  "status" text  DEFAULT NULL,
			  "theme" varchar(50) DEFAULT \'\',
			  "default_comment_display" int(1)  NOT NULL DEFAULT \'3\',
			  "challenge" varchar(8) DEFAULT \'\',
			  PRIMARY KEY ("name"))', NULL, $dblink), "Already exists?", 0);
		$indices = 13;
		$index = 1;
		test("Create referrers index ".$index++." of ".$indices."...", db_query('CREATE INDEX "wikka_referrers_idx_page_tag" ON "'.$config['table_prefix'].'referrers" ("page_tag")', NULL, $dblink), "Already exists?", 0);
		test("Create referrers index ".$index++." of ".$indices."...", db_query('CREATE INDEX "wikka_referrers_idx_time" ON "'.$config['table_prefix'].'referrers" ("time")', NULL, $dblink), "Already exists?", 0);
		test("Create comments index ".$index++." of ".$indices."...", db_query('CREATE INDEX "wikka_comments_idx_page_tag" ON "'.$config['table_prefix'].'comments" ("page_tag")', NULL, $dblink), "Already exists?", 0);
		test("Create comments index ".$index++." of ".$indices."...", db_query('CREATE INDEX "wikka_comments_idx_time" ON "'.$config['table_prefix'].'comments" ("time")', NULL, $dblink), "Already exists?", 0);
		test("Create users index ".$index++." of ".$indices."...", db_query('CREATE INDEX "wikka_users_idx_signuptime" ON "'.$config['table_prefix'].'users" ("signuptime")', NULL, $dblink), "Already exists?", 0);
		test("Create links index ".$index++." of ".$indices."...", db_query('CREATE INDEX "wikka_links_from_tag" ON "'.$config['table_prefix'].'links" ("from_tag","to_tag")', NULL, $dblink), "Already exists?", 0);
		test("Create links index ".$index++." of ".$indices."...", db_query('CREATE INDEX "wikka_links_idx_to" ON "'.$config['table_prefix'].'links" ("to_tag")', NULL, $dblink), "Already exists?", 0);
		test("Create pages index ".$index++." of ".$indices."...", db_query('CREATE INDEX "wikka_pages_idx_tag" ON "'.$config['table_prefix'].'pages" ("tag")', NULL, $dblink), "Already exists?", 0);
		test("Create pages index ".$index++." of ".$indices."...", db_query('CREATE INDEX "wikka_pages_idx_time" ON "'.$config['table_prefix'].'pages" ("time")', NULL, $dblink), "Already exists?", 0);
		test("Create pages index ".$index++." of ".$indices."...", db_query('CREATE INDEX "wikka_pages_idx_owner" ON "'.$config['table_prefix'].'pages" ("owner")', NULL, $dblink), "Already exists?", 0);
		test("Create pages index ".$index++." of ".$indices."...", db_query('CREATE INDEX "wikka_pages_idx_latest" ON "'.$config['table_prefix'].'pages" ("latest")', NULL, $dblink), "Already exists?", 0);
		test("Create pages index ".$index++." of ".$indices."...", db_query('CREATE INDEX "wikka_pages_body" ON "'.$config['table_prefix'].'pages" ("body")', NULL, $dblink), "Already exists?", 0);
		test("Create referrer index ".$index++." of ".$indices."...", db_query('CREATE INDEX "wikka_referrer_blacklist_idx_spammer" ON "'.$config['table_prefix'].'referrer_blacklist" ("spammer")', NULL, $dblink), "Already exists?", 0);

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

		// TODO: Test each case!
		test("Setting default ACL...", 1);
		db_query("insert into ".$config['table_prefix']."acls (page_tag, read_acl, write_acl, comment_read_acl, comment_post_acl) VALUES ('UserSettings', '*', '+', '*', '+')", NULL, $dblink);
		db_query("insert into ".$config['table_prefix']."acls (page_tag, read_acl, write_acl, comment_read_acl, comment_post_acl) VALUES ('AdminUsers', '!*', '!*', '!*', '!*')", NULL, $dblink);
		db_query("insert into ".$config['table_prefix']."acls (page_tag, read_acl, write_acl, comment_read_acl, comment_post_acl) VALUES ('AdminPages', '!*', '!*', '!*', '!*')", NULL, $dblink);
		db_query("insert into ".$config['table_prefix']."acls (page_tag, read_acl, write_acl, comment_read_acl, comment_post_acl) VALUES ('SysInfo', '!*', '!*', '!*', '!*')", NULL, $dblink);
		db_query("insert into ".$config['table_prefix']."acls (page_tag, read_acl, write_acl, comment_read_acl, comment_post_acl) VALUES ('WikkaConfig', '!*', '!*', '!*', '!*')", NULL, $dblink);
		db_query("insert into ".$config['table_prefix']."acls (page_tag, read_acl, write_acl, comment_read_acl, comment_post_acl) VALUES ('DatabaseInfo', '!*', '!*', '!*', '!*')", NULL, $dblink);
		db_query("insert into ".$config['table_prefix']."acls (page_tag, read_acl, write_acl, comment_read_acl, comment_post_acl) VALUES ('WikkaMenulets', '!*', '!*', '!*', '!*')", NULL, $dblink);
		db_query("insert into ".$config['table_prefix']."acls (page_tag, read_acl, write_acl, comment_read_acl, comment_post_acl) VALUES ('AdminBadWords', '!*', '!*', '!*', '!*')", NULL, $dblink);
		db_query("insert into ".$config['table_prefix']."acls (page_tag, read_acl, write_acl, comment_read_acl, comment_post_acl) VALUES ('AdminSpamLog', '!*', '!*', '!*', '!*')", NULL, $dblink);
		db_query("insert into ".$config['table_prefix']."acls (page_tag, read_acl, write_acl, comment_read_acl, comment_post_acl) VALUES ('WikkaInstaller', '!*', '!*', '!*', '!*')", NULL, $dblink);

		// Register admin user
		$challenge = dechex(crc32(time()));
		$pass_val = md5($challenge.$_POST['password']);
		$name = $config['admin_users'];
		$email = $config['admin_email'];
		// Delete existing admin user in case installer was run twice
		db_query('delete from '.$config['table_prefix'].'users where name = :name', array(':name' => $name), $dblink);
		test(__('Adding admin user').'...',
				db_query("insert into ".$config["table_prefix"]."users (name, password, email, signuptime, challenge) VALUES (:name, :pass_val, :email, ".db_now($dblink).", :challenge)", array(':name' => $name, ':pass_val' => $pass_val, ':email' => $email, ':challenge' => $challenge), $dblink), "Hmm!", 0);

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

		// Register WikkaInstaller user
		$challenge = dechex(crc32(time()));
		$pass_val = md5($challenge.$_POST['password']);
		$name = 'WikkaInstaller';
		$email = $config['admin_email'];
		// Delete existing WikkaInstaller user in case installer was run twice
		db_query('delete from '.$config['table_prefix'].'users where name = :name', array(':name' => $name), $dblink);
		test(__('Adding WikkaInstaller user').'...',
				db_query("insert into ".$config["table_prefix"]."users (name, password, email, signuptime, challenge) VALUES (:name, :pass_val, :email, ".db_now($dblink).", :challenge)", array(':name' => $name, ':pass_val' => $pass_val, ':email' => $email, ':challenge' => $challenge), $dblink), "Hmm!", 0);
		break;
	// Don't remove the break here ^^^, but don't include any after this point!
	case "1.4.0":
	case "master":
}

?>
