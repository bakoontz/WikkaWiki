<?php

// fetch configuration
$config = $_POST["config"];

// merge existing configuration with new one
$config = array_merge($wakkaConfig, $config);

// test configuration
print("<strong>Testing Configuration</strong><br />\n");
test("Testing MySQL connection settings...", $dblink = @mysql_connect($config["mysql_host"], $config["mysql_user"], $config["mysql_password"]));
test("Looking for database...", @mysql_select_db($config["mysql_database"], $dblink), "The database you configured was not found. Remember, it needs to exist before you can install/upgrade Wakka!\n\nPress the Back button and reconfigure the settings.");
print("<br />\n");

// do installation stuff
if (!$version = trim($wakkaConfig["wakka_version"])) $version = "0";
switch ($version)
{
// new installation
case "0":
	print("<strong>Installing Stuff</strong><br />\n");
	test("Creating page table...",
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
			") TYPE=MyISAM;", $dblink), "Already exists?", 0);
	test("Creating ACL table...",
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."acls (".
  			"page_tag varchar(75) NOT NULL default '',".
  			"read_acl text NOT NULL,".
  			"write_acl text NOT NULL,".
  			"comment_acl text NOT NULL,".
 			"PRIMARY KEY  (page_tag)".
			") TYPE=MyISAM", $dblink), "Already exists?", 0);
	test("Creating link tracking table...",
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."links (".
			"from_tag varchar(75) NOT NULL default '',".
  			"to_tag varchar(75) NOT NULL default '',".
  			"UNIQUE KEY from_tag (from_tag,to_tag),".
  			"KEY idx_from (from_tag),".
  			"KEY idx_to (to_tag)".
			") TYPE=MyISAM", $dblink), "Already exists?", 0);
	test("Creating referrer table...",
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."referrers (".
  			"page_tag varchar(75) NOT NULL default '',".
  			"referrer varchar(150) NOT NULL default '',".
  			"time datetime NOT NULL default '0000-00-00 00:00:00',".
  			"KEY idx_page_tag (page_tag),".
  			"KEY idx_time (time)".
			") TYPE=MyISAM", $dblink), "Already exists?", 0);
	test("Creating referrer blacklist table...",
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."referrer_blacklist (".
  			"spammer varchar(150) NOT NULL default '',".
  			"KEY idx_spammer (spammer)".
			") TYPE=MyISAM", $dblink), "Already exists?", 0);
	test("Creating user table...",
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
  			"PRIMARY KEY  (name),".
  			"KEY idx_signuptime (signuptime)".
			") TYPE=MyISAM", $dblink), "Already exists?", 0);
	test("Creating comment table...",
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
			") TYPE=MyISAM;", $dblink), "Already exists?", 0);

  test("Adding admin user...", 
    @mysql_query("insert into ".$config["table_prefix"]."users set name = '".$config["admin_users"]."', password = md5('".mysql_real_escape_string($_POST["password"])."'), email = '".$config["admin_email"]."', signuptime = now()", $dblink), "Hmm!", 0);

	test("Adding some pages...", 1);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = '".$config["root_page"]."', body = '=====Welcome to your Wikka site!===== \n\nThanks for installing [[Wikka:HomePage WikkaWiki]]! This site is running on version {{wikkaversion}} (see WikkaReleaseNotes). \nDouble-click on this page or click on the \"Edit page\" link at the bottom to get started. \n\nAlso don\'t forget to visit the [[Wikka:HomePage WikkaWiki website]]! \n\nUseful pages: FormattingRules, WikkaDocumentation, OrphanedPages, WantedPages, TextSearch.', user = 'WikkaInstaller', owner = '".$config["admin_users"]."', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'RecentChanges', body = '{{RecentChanges}}{{nocomments}}\n\n\n----\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'RecentlyCommented', body = '{{RecentlyCommented}}{{nocomments}}\n\n\n----\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'UserSettings', body = '{{UserSettings}}{{nocomments}}\n\n\n----\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'PageIndex', body = '{{PageIndex}}{{nocomments}}\n\n\n----\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'WikkaReleaseNotes', body = '{{wikkachanges}}{{nocomments}}\n\n\n----\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'WikkaDocumentation' , body = '=====Wikka Documentation=====\n\nComprehensive and up-to-date documentation on Wikka Wiki can be found on the [[http://wikka.jsnx.com/WikkaDocumentation main Wikka server]].', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'WantedPages', body = '{{WantedPages}}{{nocomments}}\n\n\n----\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'OrphanedPages', body = '====Orphaned Pages====\n\nThe following list shows those pages held in the Wiki that are not linked to on any other pages.\n\n{{OrphanedPages}}{{nocomments}}\n\n\n----\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'TextSearch', body = '{{TextSearch}}{{nocomments}}\n\n\n----\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'TextSearchExpanded', body = '{{textsearchexpanded}}{{nocomments}}\n\n\n----\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'MyPages', body = '{{MyPages}}{{nocomments}}\n\n\n----\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'MyChanges', body = '{{MyChanges}}{{nocomments}}\n\n\n----\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'InterWiki', body = '{{interwikilist}}\n\n\n----\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'PasswordForgotten', body = '{{emailpassword}}\n\n\n----\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'WikiCategory', body = '===This wiki is using a very flexible but simple categorizing system to keep everything properly organized.===\n\n{{Category page=\"/\"  col=\"10\"}}\n==Here\'s how it works :==\n~- The master list of the categories is **Category Category** (//without the space//) which will automatically list all known maincategories, and should never be edited. This list is easily accessed from the Wiki\'s top navigation bar. (Categories).\n~- Each category has a WikiName name of the form \"\"CategoryName\"\" for example CategoryWiki etc. (see list of maincategories above)\n~- Pages can belong to zero or more categories. Including a page in a category is done by simply mentioning the \"\"CategoryName\"\" on the page (by convention at the very end of the page).\n~- The system allows to build hierarchies of categories by referring to the parent category in the subcategory page. The parent category page will then automatically include the subcategory page in its list.\n~- A special kind of category is **\"\"Category Users\"\"** (//without the space//) to group the userpages, so your Wiki homepage should include it at the end to be included in the category-driven userlist.\n~- New categories can be created (think very hard before doing this though, we don\'t need too much of them) by creating a \"\"CategoryName\"\" page, including \"\"{{Category}}\"\" in it and placing it in the **Category Category** (//without the space//) category (for a main category or another parent category in case you want to create a subcategory).\n\n**Please help to keep this place organized by including the relevant categories in new and existing pages !**\n\n**Notes:** \n~- The above bold items above //include spaces// to prevent this page from showing up in the mentioned categories. This page only belongs in CategoryWiki (which can be safely mentioned) after all !\n~- In order to avoid accidental miscategorization you should **avoid** mentioning a non-related \"\"CategoryName\"\" on a page. This is a side-effect of how the categorizing system works: it\'s based on a textsearch and is not restricted to the footer convention.\n~- Don\'t be put of by the name of this page (WikiCategory) which is a logical name (it\'s about the Wiki and explains Category) but doesn\'t have any special role in the Categorizing system.\n~- To end with this is the **standard convention** to include the categories (both the wiki code and the result):\n\n%%==Categories==\nCategoryWiki%%\n\n==Categories==\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'CategoryWiki', body = '===Wiki Related Category===\nThis Category will contain links to pages talking about Wikis and Wikis specific topics. When creating such pages, be sure to include CategoryWiki at the bottom of each page, so that page shows listed.\n\n\n----\n\n{{Category col=\"3\"}}\n\n\n----\n[[CategoryCategory List of all categories]]', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'CategoryCategory', body = '===List of All Categories===\nBelow is the list of all Categories existing on this Wiki, granted that users did things right when they created their pages or new Categories. See WikiCategory for how the system works.\n\n----\n\n{{Category}}', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'FormattingRules', body = '======Wikka Formatting Guide======\n\n\nOnce you have read through this, test your formatting skills in the SandBox.\n----\n===1. Text Formatting===\n\n\'\'Note: Anything between 2 sets of double-quotes is not formatted.\'\' \n\n	\"\"**bold**\"\"\n	**bold **\n\n	\"\"//I\'m italic text!//\"\"\n	//I\'m italic text!//\n\n	\"\"And I\'m __underlined__!\"\"\n	And I\'m __underlined__!\n\n	\"\"##monospace text##\"\"\n	##monospace text##\n\n	\"\"\'\'highlight text\'\'\"\" (using 2 single-quotes)\n	\'\'highlight text\'\'\n\n	\"\"++Strike through text++\"\"\n	++Strike through text++\n\n	\"\"Press #%ANY KEY#%\"\"\n	Press #%ANY KEY#%\n\n	\"\"@@Center text@@\"\"\n	@@Center text@@\n\n===2. Headers===\n\nUse between five = (for the biggest header) and two = (for the smallest header) on both sides of a text.  \n\n	\"\"====== Really big header ======\"\"\n	====== Really big header ======\n	\n	\"\"===== Rather big header =====\"\"\n	===== Rather big header =====\n	\n	\"\"==== Medium header ====\"\" \n	==== Medium header ====\n	\n	\"\"=== Not-so-big header ===\"\" \n	=== Not-so-big header ===\n	\n	\"\"== Smallish header ==\"\" \n	== Smallish header ==\n\n===3. Horizontal separator===\n	\"\"----\"\"\n----\n\n===4. Forced line break===\n	\"\"---\"\"\n---\n----\n===5. Lists / Indents===\nIndent text using **4** spaces (which will auto-convert into tabs) or using \"~\". To make bulleted / ordered lists, use the following codes (you can use 4 spaces instead of \"~\"):\n\n\"\"~- bulleted list:\"\"\n	- bulleted list\n	- Line two\n\n\"\"~1) numbered list:\"\"\n	1) numbered list\n	1) Line two\n\n\"\"~A) Using uppercase characters:\"\"\n	A) Using uppercase characters\n	A) Line two\n\n\"\"~a) Using lowercase characters:\"\"\n	a) Using lowercase characters\n	a) Line two\n\n\"\"~I) using uppercase roman numerals:\"\"\n	I) using Latin numbers\n	I) Line two\n\n\"\"~i) using lowercase roman numerals:\"\"\n	i) using Latin numbers\n	i) Line two\n\n----\n\n===6. Inline comments===\n\n\"\"~& Comment\"\"\n\"\"~~& Subcomment\"\"\n\"\"~~~& Subsubcomment\"\"\n\n~& Comment\n~~& Subcomment\n~~~& Subsubcomment\n\n----\n===7. Images===\n\nTo place images on a Wiki page, use:\n\"\"{{image class=\"center\" alt=\"DVD logo\" title=\"An Image Link\" url=\"images/dvdvideo.gif\" link=\"RecentChanges\"}}\"\"\n{{image class=\"center\" alt=\"dvd logo\" title=\"An Image Link\" url=\"images/dvdvideo.gif\" link=\"RecentChanges\"}}\nLinks can be external, or internal Wiki links. You don\'t have to enter a link at all, and in that case just an image will be inserted. You can also use the classes \'left\' and \'right\' to float images left and right. You don\'t need to use all those attributes, only url is essential.\n\n----\n===8. Links===\n\nTo link to other wiki pages, write\n	- a WikiName\n	- or a forced link with \"\"[[\"\" and \"\"]]\"\" around it (everything after the first space will be shown as description)\n		- Example: \"\"[[JennaPage Jenna\'s Home Page]]\"\"\n	- or an image with a link \n\nTo link to external pages, write\n	- a http address inside the page\n	- or a forced link with \"\"[[\"\" and \"\"]]\"\" around it (everything after the first space will be shown as description)\n		- Example: \"\"[[http://example.com/jenna/ Jenna\'s Home Page]]\"\"\n	- or an image with a link\n	- or an InterWiki link (see InterWiki page for wiki list)\n		- Examples:\n			- WikiPedia:WikkaWiki\n			- Google:CSS\n			- Thesaurus:Happy\n\n----\n===9. Tables===\n\nTo create a table use this code:\n\"\"{{table columns=\"3\" cellpadding=\"1\" cells=\"BIG;GREEN;FROGS;yes;yes;no;no;no;###\"}}\"\" to give:\n\n{{table columns=\"3\" cellpadding=\"1\" cells=\"BIG;GREEN;FROGS;yes;yes;no;no;no;###\"}}\n\n\"\"###\"\" means the cell is empty.\n\n----\n===10. Colored Text===\n\n\"\"{{color c=\"blue\" text=\"This is a test.\"}}\"\" gives:\n\n{{color c=\"blue\" text=\"This is a test.\"}}\n\nIf you want to use hex values:\n\n\"\"{{color hex=\"#DD0000\" text=\"This is another test.\"}}\"\" to give:\n\n{{color hex=\"#DD0000\" text=\"This is another test.\"}}\n	\n----\n===11. Floats===\n\n **Left floated box - use two < signs before and after the block**\n	<<Some text in a floated box hanging around<<Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler.\n\n	**Right floated box, use two > characters before and after the block**\n	>>Some text in a floated box hanging around>>Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler.\n\n	\"\"Use ::c::  to clear floated blocks...\"\"\n\n----\n===12. Code formatters===\n\nSimply use \"\"%%(formatter) code %%\"\" - see below for a list of available formatters.\n\nExample:\n\"\"%%(php) PHP code%%:\"\"\n%%(php) \n<?php\nphpinfo();\necho \"Hello, World!\";\n?>\n%%\n\n{{table columns=\"2\" cellpadding=\"1\" cells=\"LANGUAGE;FORMATTER;Actionscript;actionscript;ADA;ada;Apache Log;apache;ASM;asm;ASP;asp;Bash;bash;C;c;C for Macs;c_mac;c#;csharp;C++;cpp;CAD DCL;caddcl;CadLisp;cadlisp;CSS;css;Delphi;delphi;HTML;html4strict;Java;java;Javascript;javascript;Lisp;lisp;Lua;lua;NSIS;nsis;Objective C;objc;OpenOffice BASIC;oobas;Pascal;pascal;Perl;perl;PHP;php;Python;phyton;Q(uick)BASIC;qbasic;Smarty;smarty;SQL;sql;VB.NET;vbnet;Visual BASIC;vb;Visual Fox Pro;visualfoxpro;XML;xml\"}}\n\n----\n===13. [[freemind.sourceforge.net/ FreeMind]] maps===\n\nThere are two options for including maps:\n\nOption 1: Upload a \"\"FreeMind\"\" file to a webserver, and then place a link to it on a wikka page:\n	\"\"http://yourdomain.com/freemind/freemind.mm\"\"\n	No special formatting is necessary.\n\nOption 2: Paste the \"\"FreeMind\"\" data directly into a wikka page:\n	- Open a \"\"FreeMind\"\" file with a text editor.\n	- Select all, and copy the data.\n	- Browse to your Wikka site and paste the Freemind data into a page. \n\n----\n===14. Embedded HTML===\nUse two doublequotes around the html tags. \n\n&quot&quot\n[html code]\n&quot&quot\n\nBy default, some HTML tags are removed by the \"\"SafeHTML\"\" parser to protect against potentially dangerous code.  The list of tags that are removed can be found on the Wikka:SafeHTML page.\n\nIt is possible to allow //all// HTML tags to be used, see Wikka:UsingHTML for more information.\n\n----\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'HighScores', body = '**Rankings based on quantity of OwnedPages*:**\n {{HighScores}}{{nocomments}}*//not quality.//\n\n\n----\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'OwnedPages', body = '{{ownedpages}}{{nocomments}}These numbers merely reflect how many pages you have created, not how much content you have contributed or the quality of your contributions. To see how you rank with other members, you may be interested in checking out the HighScores. \n\n\n----\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'SandBox', body = 'Test your formatting skills here.\n\n\n\n\n----\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);

	break;

// The funny upgrading stuff. Make sure these are in order! //
// And yes, there are no break;s here. This is on purpose.  //

// from 0.1 to 0.1.1
case "0.1":
	print("<strong>Wakka 0.1 to 0.1.1</strong><br />\n");
	test("Just very slightly altering the pages table...", 
		@mysql_query("alter table ".$config["table_prefix"]."pages add body_r text not null default '' after body", $dblink), "Already done? Hmm!", 0);
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
		@mysql_query("ALTER TABLE ".$config["table_prefix"]."pages ADD note varchar(50) NOT NULL default '' after latest", $dblink), "Failed.", 1);
	test("Just slightly altering the pages table...", 
		@mysql_query("alter table ".$config["table_prefix"]."pages DROP COLUMN body_r", $dblink), "Already done? Hmm!", 0);
	test("Just slightly altering the users table...", 
		@mysql_query("alter table ".$config["table_prefix"]."users DROP COLUMN motto", $dblink), "Already done? Hmm!", 0);
case "1.0":
case "1.0.1":
case "1.0.2":
case "1.0.3":
case "1.0.4":
// from 1.0.4 to 1.0.5
	print("<strong>1.0.4 to 1.0.5 changes:</strong><br />\n");
	test("Adding a new option to the wakka.config file: double_doublequote_html", 1);
	$config["double_doublequote_html"] = 'safe';
case "1.0.5":
case "1.0.6":
	print("<strong>1.0.6 to 1.1.0 changes:</strong><br />\n");
	test("Creating comment table...",
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
			") TYPE=MyISAM", $dblink), "Already done? Hmm!", 1);
	test("Copying comments from the pages table to the new comments table...", 
		@mysql_query("INSERT INTO ".$config["table_prefix"]."comments (page_tag, time, comment, user) SELECT comment_on, time, body, user FROM ".$config["table_prefix"]."pages WHERE comment_on != '';", $dblink), "Already done? Hmm!", 1);
	test("Deleting comments from the pages table...", 
		@mysql_query("DELETE FROM ".$config["table_prefix"]."pages WHERE comment_on != ''", $dblink), "Already done? Hmm!", 1);
	test("Removing comment_on field from the pages table...", 
		@mysql_query("ALTER TABLE ".$config["table_prefix"]."pages DROP comment_on", $dblink), "Already done? Hmm!", 1);
	test("Removing comment pages from the ACL table...", 
		@mysql_query("DELETE FROM ".$config["table_prefix"]."acls WHERE page_tag like 'Comment%'", $dblink), "Already done? Hmm!", 1);
case "1.1.0":
	print("<strong>1.1.0 to 1.1.2 changes:</strong><br />\n");
	test("Dropping current ACL table structure...", 
		@mysql_query("DROP TABLE ".$config["table_prefix"]."acls", $dblink), "Already done? Hmm!", 0);
	test("Creating new ACL table structure...",
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."acls (".
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
		@mysql_query("ALTER TABLE ".$config["table_prefix"]."pages CHANGE tag tag varchar(75) NOT NULL default ''", $dblink), "Failed. ?", 1);
	test("Altering pages table structure...",
		@mysql_query("ALTER TABLE ".$config["table_prefix"]."pages CHANGE user user varchar(75) NOT NULL default ''", $dblink), "Failed. ?", 1);
	test("Altering pages table structure...",
		@mysql_query("ALTER TABLE ".$config["table_prefix"]."pages CHANGE owner owner varchar(75) NOT NULL default ''", $dblink), "Failed. ?", 1);
	test("Altering pages table structure...",
		@mysql_query("ALTER TABLE ".$config["table_prefix"]."pages CHANGE note note varchar(100) NOT NULL default ''", $dblink), "Failed. ?", 1);
	test("Altering user table structure...",
		@mysql_query("ALTER TABLE ".$config["table_prefix"]."users CHANGE name name varchar(75) NOT NULL default ''", $dblink), "Failed. ?", 1);
	test("Altering comments table structure...",
		@mysql_query("ALTER TABLE ".$config["table_prefix"]."comments CHANGE page_tag page_tag varchar(75) NOT NULL default ''", $dblink), "Failed. ?", 1);
	test("Altering comments table structure...",
		@mysql_query("ALTER TABLE ".$config["table_prefix"]."comments CHANGE user user varchar(75) NOT NULL default ''", $dblink), "Failed. ?", 1);
	test("Altering acls table structure...",
		@mysql_query("ALTER TABLE ".$config["table_prefix"]."acls CHANGE page_tag page_tag varchar(75) NOT NULL default ''", $dblink), "Failed. ?", 1);
	test("Altering links table structure...",
		@mysql_query("ALTER TABLE ".$config["table_prefix"]."links CHANGE from_tag from_tag varchar(75) NOT NULL default ''", $dblink), "Failed. ?", 1);
	test("Altering links table structure...",
		@mysql_query("ALTER TABLE ".$config["table_prefix"]."links CHANGE to_tag to_tag varchar(75) NOT NULL default ''", $dblink), "Failed. ?", 1);
	test("Altering referrers table structure...",
		@mysql_query("ALTER TABLE ".$config["table_prefix"]."referrers CHANGE page_tag page_tag varchar(75) NOT NULL default ''", $dblink), "Failed. ?", 1);
	test("Creating referrer_blacklist table...",
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."referrer_blacklist (".
  			"spammer varchar(150) NOT NULL default '',".
  			"KEY idx_spammer (spammer)".
			") TYPE=MyISAM", $dblink), "Already exists? Hmm!", 1);
	test("Altering a pages table index...", 
		@mysql_query("alter table ".$config["table_prefix"]."pages DROP INDEX tag", $dblink), "Already done? Hmm!", 0);
	test("Altering a pages table index...", 
		@mysql_query("alter table ".$config["table_prefix"]."pages ADD FULLTEXT body (body)", $dblink), "Already done? Hmm!", 0);
	test("Altering a users table index...", 
		@mysql_query("alter table ".$config["table_prefix"]."users DROP INDEX idx_name", $dblink), "Already done? Hmm!", 0);
case "1.1.3.1":
case "1.1.3.2":
	print("<strong>1.1.3.2 to 1.1.3.3 changes:</strong><br />\n");
	test("Adding a new option to the wikka.config file: wikiping_server", 1);
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
	test("Adding WikkaReleaseNotes page...", 
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'WikkaReleaseNotes', body = '{{wikkachanges}}{{nocomments}}\n\n\n----\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink), "Already done? OK!", 0);
	test("Adding WikkaDocumentation page...", 
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'WikkaDocumentation' , body = '=====Wikka Documentation=====\n\nComprehensive and up-to-date documentation on Wikka Wiki can be found on the [[http://wikka.jsnx.com/WikkaDocumentation main Wikka server]].', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink), "Already done? OK!", 0);
	// cookie names have changed -- logout user and delete the old cookies
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

}

?>

<p>
In the next step, the installer will try to write the updated configuration file, <tt><?php echo $wakkaConfigLocation ?></tt>.
Please make sure the web server has write access to the file, or you will have to edit it manually.
Once again, see <a href="http://wikka.jsnx.com/WikkaInstallation" target="_blank">Wikka:WakkaInstallation</a> for details.
</p>

<form action="<?php echo myLocation(); ?>?installAction=writeconfig" method="POST">
<input type="hidden" name="config" value="<?php echo htmlspecialchars(serialize($config)) ?>" />
<input type="submit" value="Continue" />
</form>