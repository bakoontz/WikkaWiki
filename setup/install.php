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
  			"tag varchar(50) NOT NULL default '',".
  			"time datetime NOT NULL default '0000-00-00 00:00:00',".
  			"body mediumtext NOT NULL,".
  			"owner varchar(50) NOT NULL default '',".
  			"user varchar(50) NOT NULL default '',".
  			"latest enum('Y','N') NOT NULL default 'N',".
  			"note varchar(50) NOT NULL default '',".
  			"handler varchar(30) NOT NULL default 'page',".
  			"PRIMARY KEY  (id),".
  			"FULLTEXT KEY tag (tag,body),".
  			"KEY idx_tag (tag),".
  			"KEY idx_time (time),".
  			"KEY idx_latest (latest),".
			") TYPE=MyISAM;", $dblink), "Already exists?", 0);
	test("Creating ACL table...",
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."acls (".
  			"page_tag varchar(50) NOT NULL default '',".
			"privilege varchar(20) NOT NULL default '',".
  			"list text NOT NULL,".
 			"PRIMARY KEY  (page_tag,privilege)".
			") TYPE=MyISAM", $dblink), "Already exists?", 0);
	test("Creating link tracking table...",
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."links (".
			"from_tag char(50) NOT NULL default '',".
  			"to_tag char(50) NOT NULL default '',".
  			"UNIQUE KEY from_tag (from_tag,to_tag),".
  			"KEY idx_from (from_tag),".
  			"KEY idx_to (to_tag)".
			") TYPE=MyISAM", $dblink), "Already exists?", 0);
	test("Creating referrer table...",
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."referrers (".
  			"page_tag char(50) NOT NULL default '',".
  			"referrer char(150) NOT NULL default '',".
  			"time datetime NOT NULL default '0000-00-00 00:00:00',".
  			"KEY idx_page_tag (page_tag),".
  			"KEY idx_time (time)".
			") TYPE=MyISAM", $dblink), "Already exists?", 0);
	test("Creating user table...",
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."users (".
  			"name varchar(80) NOT NULL default '',".
  			"password varchar(32) NOT NULL default '',".
  			"email varchar(50) NOT NULL default '',".
  			"revisioncount int(10) unsigned NOT NULL default '20',".
  			"changescount int(10) unsigned NOT NULL default '50',".
  			"doubleclickedit enum('Y','N') NOT NULL default 'Y',".
  			"signuptime datetime NOT NULL default '0000-00-00 00:00:00',".
  			"show_comments enum('Y','N') NOT NULL default 'N',".
  			"show_spaces enum('Y','N') NOT NULL default 'N',".
  			"PRIMARY KEY  (name),".
  			"KEY idx_name (name),".
  			"KEY idx_signuptime (signuptime)".
			") TYPE=MyISAM", $dblink), "Already exists?", 0);
	test("Creating comment table...",
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."comments (".
  			"id int(10) unsigned NOT NULL auto_increment,".
  			"page_tag varchar(50) NOT NULL default '',".
  			"time datetime NOT NULL default '0000-00-00 00:00:00',".
  			"comment text NOT NULL,".
  			"user varchar(50) NOT NULL default '',".
  			"PRIMARY KEY  (id),".
  			"KEY idx_page_tag (page_tag),".
  			"KEY idx_time (time),".
			") TYPE=MyISAM;", $dblink), "Already exists?", 0);

  test("Adding admin user...", 
    @mysql_query("insert into ".$config["table_prefix"]."users set name = '".$config["admin_users"]."', password = md5('".mysql_escape_string($_POST["password"])."'), email = '".$config["admin_email"]."', signuptime = now()", $dblink), "Hmm!", 0);

	mysql_query("insert into ".$config["table_prefix"]."pages set tag = '".$config["root_page"]."', body = '".mysql_escape_string("Welcome to your Wakka site! Click on the \"Edit page\" link at the bottom to get started.\n\nAlso don't forget to visit [[WakkaWiki:WakkaWiki WakkaWiki]]!\n\nUseful pages: FormattingRules, OrphanedPages, WantedPages, TextSearch.")."', user = '".$config["admin_users"]."', owner = '".$config["admin_users"]."', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'RecentChanges', body = '{{RecentChanges}}{{nocomments}}\n\n\n----\nCategoryWiki', user = '".$config["admin_users"]."', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'RecentlyCommented', body = '{{RecentlyCommented}}{{nocomments}}\n\n\n----\nCategoryWiki', user = '".$config["admin_users"]."', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'UserSettings', body = '{{UserSettings}}{{nocomments}}\n\n\n----\nCategoryWiki', user = '".$config["admin_users"]."', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'PageIndex', body = '{{PageIndex}}{{nocomments}}\n\n\n----\nCategoryWiki', user = '".$config["admin_users"]."', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'WantedPages', body = '{{WantedPages}}{{nocomments}}\n\n\n----\nCategoryWiki', user = '".$config["admin_users"]."', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'OrphanedPages', body = '====Orphaned Pages====\n\nThe following list shows those pages held in the Wiki that are not linked to on any other pages.\n\n{{OrphanedPages}}{{nocomments}}\n\n\n----\nCategoryWiki', user = '".$config["admin_users"]."', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'TextSearch', body = '{{TextSearch}}{{nocomments}}\n\n\n----\nCategoryWiki', user = '".$config["admin_users"]."', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'TextSearchExpanded', body = '{{textsearchexpanded}}{{nocomments}}\n\n\n----\nCategoryWiki', user = '".$config["admin_users"]."', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'MyPages', body = '{{MyPages}}{{nocomments}}\n\n\n----\nCategoryWiki', user = '".$config["admin_users"]."', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'MyChanges', body = '{{MyChanges}}{{nocomments}}\n\n\n----\nCategoryWiki', user = '".$config["admin_users"]."', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'InterWiki', body = '{{interwikilist}}\n\n\n----\nCategoryWiki', user = '".$config["admin_users"]."', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'PasswordForgotten', body = '{{emailpassword}}\n\n\n----\nCategoryWiki', user = '".$config["admin_users"]."', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'WikiCategory', body = '===This wiki is using a very flexible but simple categorizing system to keep everything properly organized.===\n\n{{Category page=\"/\"  col=\"10\"}}\n==Here\'s how it works :==\n~- The master list of the categories is **Category Category** (//without the space//) which will automatically list all known maincategories, and should never be edited. This list is easily accessed from the Wiki\'s top navigation bar. (Categories).\n~- Each category has a WikiName name of the form \"\"CategoryName\"\" for example CategoryWiki etc. (see list of maincategories above)\n~- Pages can belong to zero or more categories. Including a page in a category is done by simply mentioning the \"\"CategoryName\"\" on the page (by convention at the very end of the page).\n~- The system allows to build hierarchies of categories by referring to the parent category in the subcategory page. The parent category page will then automatically include the subcategory page in its list.\n~- A special kind of category is **\"\"Category Users\"\"** (//without the space//) to group the userpages, so your Wiki homepage should include it at the end to be included in the category-driven userlist.\n~- New categories can be created (think very hard before doing this though, we don\'t need too much of them) by creating a \"\"CategoryName\"\" page, including \"\"{{Category}}\"\" in it and placing it in the **Category Category** (//without the space//) category (for a main category or another parent category in case you want to create a subcategory).\n\n**Please help to keep this place organized by including the relevant categories in new and existing pages !**\n\n**Notes:** \n~- The above bold items above //include spaces// to prevent this page from showing up in the mentioned categories. This page only belongs in CategoryWiki (which can be safely mentioned) after all !\n~- In order to avoid accidental miscategorization you should **avoid** mentioning a non-related \"\"CategoryName\"\" on a page. This is a side-effect of how the categorizing system works: it\'s based on a textsearch and is not restricted to the footer convention.\n~- Don\'t be put of by the name of this page (WikiCategory) which is a logical name (it\'s about the Wiki and explains Category) but doesn\'t have any special role in the Categorizing system.\n~- To end with this is the **standard convention** to include the categories (both the wiki code and the result):\n\n%%==Categories==\nCategoryWiki%%\n\n==Categories==\nCategoryWiki', user = '".$config["admin_users"]."', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'CategoryWiki', body = '======Wiki Related Category======\nThis Category will contain links to pages talking about Wikis and Wikis specific topics. When creating such pages, be sure to include CategoryWiki at the bottom of each page, so that page shows listed.\n\n\n----\n\n{{Category col=\"3\"}}\n\n\n----\n[[CategoryCategory List of all categories]]', user = '".$config["admin_users"]."', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'CategoryCategory', body = '======List of All Categories======\nBelow is the list of all Categories existing on this Wiki, granted that users did things right when they created their pages or new Categories. See WikiCategory for how the system works.\n\n----\n\n{{Category}}', user = '".$config["admin_users"]."', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'FormattingRules', body = '==== The Wiki Formatting Guide ====\n\nAnything between 2 sets of double-quotes is ignored and presented exactly as typed (that means the formatting commands below are ignored whenever surrounded by double double-quotes.)\n\nOnce you\'ve read through this, test your formatting skills in the SandBox.\n----\n===Basic formatting:===\n\n	\"\"**I\'m bold text!**\"\"\n	**I\'m bold text!**\n\n	\"\"//I\'m italic text!//\"\"\n	//I\'m italic text!//\n\n	\"\"And I\'m __underlined__!\"\"\n	And I\'m __underlined__!\n\n	\"\"##monospace text##\"\"\n	##monospace text##\n\n	\"\"\'\'highlight text\'\'\"\" (using 2 single-quotes)\n	\'\'highlight text\'\'\n\n	\"\"++Strike through text++\"\"\n	++Strike through text++\n\n	\"\"Press #%ANY KEY#%\"\"\n	Press #%ANY KEY#%\n\n	\"\"@@Center text@@\"\"\n	@@Center text@@\n\n ===Headers:===\n	\"\"====== Really big header ======\"\"\n	====== Really big header ======\n	\n	\"\"===== Rather big header =====\"\"\n	===== Rather big header =====\n	\n	\"\"==== Medium header ====\"\" \n	==== Medium header ====\n	\n	\"\"=== Not-so-big header ===\"\" \n	=== Not-so-big header ===\n	\n	\"\"== Smallish header ==\"\" \n	== Smallish header ==\n\n===Horizontal separator:===\n	\"\"----\"\"\n----\n\n===Forced line break:===\n	\"\"---\"\"\n---\n----\n===Lists / Indents:===\nIndent text using **4** spaces (which will auto-convert into tabs) or using \"~\". To make bulleted / ordered lists, use the following codes (you can use 4 spaces instead of \"~\"):\n\n\"\"~- bulleted list:\"\"\n	- bulleted list\n	- Line two\n\n\"\"~1) numbered list:\"\"\n	1) numbered list\n	1) Line two\n\n\"\"~A) Using uppercase characters:\"\"\n	A) Using uppercase characters\n	A) Line two\n\n\"\"~a) Using lowercase characters:\"\"\n	a) Using lowercase characters\n	a) Line two\n\n\"\"~I) using uppercase roman numerals:\"\"\n	I) using Latin numbers\n	I) Line two\n\n\"\"~i) using lowercase roman numerals:\"\"\n	i) using Latin numbers\n	i) Line two\n\n----\n===Wiki Extensions:===\n\n==Images:==\n\nTo place images on a Wiki page, use:\n\"\"{{image class=\"center\" alt=\"DVD logo\" title=\"An Image Link\" url=\"images/dvdvideo.gif\" link=\"RecentChanges\"}}\"\"\n{{image class=\"center\" alt=\"dvd logo\" title=\"An Image Link\" url=\"images/dvdvideo.gif\" link=\"RecentChanges\"}}\nLinks can be external, or internal Wiki links. You don\'t have to enter a link at all, and in that case just an image will be inserted. You can also use the classes \'left\' and \'right\' to float images left and right. You don\'t need to use all those attributes, only url is essential.\n\n==Tables:==\n\nTo create a table use this code:\n\"\"{{table columns=\"3\" cellpadding=\"1\" cells=\"BIG;GREEN;FROGS;yes;yes;no;no;no;###\"}}\"\" to give:\n\n{{table columns=\"3\" cellpadding=\"1\" cells=\"BIG;GREEN;FROGS;yes;yes;no;no;no;###\"}}\n\n\"\"###\"\" means the cell is empty.\n\n==Coloured Text:==\n\n\"\"{{colour c=\"blue\" text=\"This is a test.\"}}\"\" gives:\n\n{{colour c=\"blue\" text=\"This is a test.\"}}\n\nIf you want to use hex values:\n\n\"\"{{colour hex=\"#DD0000\" text=\"This is another test.\"}}\"\" to give:\n\n{{colour hex=\"#DD0000\" text=\"This is another test.\"}}\n	\n\n----\n\n **Left floated box - use two < signs before and after the block**\n	<<Some text in a floated box hanging around<<Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler.\n\n	**Right floated box, use two > characters before and after the block**\n	>>Some text in a floated box hanging around>>Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler.\n\n	\"\"Use ::c::  to clear floated blocks...\"\"\n\n----\n===Code Formatters:===\n\n\"\"%%code%%:\"\"\n%%\nint main(int arc,char **argv)\n{\n	printf(\"Hello, %s!\n\", (argc>1) ? argv[1] : \"World\");\n	return 0;\n}\n%%\n\n	\"\"%%(ini) INI file contents%%:\"\"\n%%(ini)\n; Menu specification file for Opera 7.0\n\n[Version]\nFile Version=2\n\n[Info]  #background info\nName=Munin++ Menu\nDescription=Munin++ Menu\nAuthor=NonTroppo (originally by Rijk van Geijtenbeek)\nVersion=1.9\n%%\n	\"\"%%(php) PHP code%%:\"\"\n%%(php) \n<?php\nphpinfo();\n$s = \"Hello, World!\n\";\nprint \"$s\";?>\n%%\n\n	\"\"%%(email) Email message%%:\"\" \n%%(email) \nHi!\n>>>> My Llama loves foot massage.\n>>> You really think so?\n>> Yes, I know he does.\n>Are you sure?\n\nOf course, yes!\n\nMr. Scruff\n%%\n\n----\n===Forced links:===\n	\"\"[[http://wikka.jsnx.com]]\"\"\n	[[http://wikka.jsnx.com]]\n\n	\"\"[[http://wikka.jsnx.com My Wiki Site]]\"\"\n	[[http://wikka.jsnx.com My Wiki Site]]\n\n\n----\n\n===Inter Wiki Links:===\n	See the InterWiki page for a full list of available engines. Here are some examples:\n\n	WikiPedia:Perception\n	CssRef:overflow\n	Google:CSS\n	Thesaurus:Dilate\n	Dictionary:Dream\n\n----\n\n===FAQ:===\n//Question: How do you un-WikiName a word ?//\nAnswer: Add two pair of double-quotes around the word: \"\"WikiName\"\"\n\n//Question: How do you get a pair of double-quotes (without any text between them) to display properly ?//\nAnswer: Use the entity literal ##&amp;quot;## - ##&amp;quot&amp;quot;##\n\n//Question: How does Wakka Wiki know to what URL to send a visitor to if it wasn\'t specified ?//\nAnswer: The link is to a forced WikiPage. That means a link to a page in this wiki is generated.\n\n//Question: So why does \"\"[[LALA_LELE]]\"\" send me to http://LALA_LELE ?//\nAnswer: The underscore breaks things. \"\"[[LALALELE]]\"\" doesn\'t have this problem.\n\n\n==Back Links==\n{{backlinks}}\n\n----\nCategoryWiki', user = '".$config["admin_users"]."', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'HighScores', body = '**Rankings based on quanity of OwnedPages*:**\n {{HighScores}}{{nocomments}}*//not quality.//\n\n\n----\nCategoryWiki', user = '".$config["admin_users"]."', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'OwnedPages', body = '{{ownedpages}}{{nocomments}}These numbers merely reflect how many pages you have created, not how much content you have contributed or the quality of your contributions. To see how you rank with other members, you may be interested in checking out the HighScores. \n\n\n----\nCategoryWiki', user = '".$config["admin_users"]."', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'SandBox', body = 'Test your formatting skills here.\n\n\n\n\n----\nCategoryWiki', user = '".$config["admin_users"]."', time = now(), latest = 'Y'", $dblink);
	test("Adding some pages...", 1);
	test("Reticulating splines...", 1);
	test("Writing TPS report...", 1);
	break;

// The funny upgrading stuff. Make sure these are in order! //
// And yes, there are no break;s here. This is on purpose.  //

// from 0.1 to 0.1.1
case "0.1":
	print("<strong>0.1 to 0.1.1</strong><br />\n");
	test("Just very slightly altering the pages table...", 
		@mysql_query("alter table ".$config["table_prefix"]."pages add body_r text not null default '' after body", $dblink), "Already done? Hmm!", 0);
	test("Claiming all your base...", 1);

// from 0.1.1 to 0.1.2
case "0.1.1":
	print("<strong>0.1.1 to 0.1.2</strong><br />\n");
	test("Sending hatemail to the Wakka developers...", 1);
	test("Writing a negative C&C Generals review...", 1);
	test("Generating world peace...", 1);

// from 0.1.2 to 0.1.3-dev (will be 0.1.3)
case "0.1.2":
	print("<strong>0.1.2 to 0.1.3-dev</strong><br />\n");
	test("Villagers need food...", 1);

// from 1.0.4 to 1.0.5
case "1.0.4":
	print("<strong>1.0.4 to 1.0.5 changes:</strong><br />\n");
	test("Adding a new option to the wakka.config file: double_doublequote_html", 1);
	$config["double_doublequote_html"] = 'safe';

case "1.0.6":
	print("<strong>1.0.6 to 1.1.0 changes:</strong><br />\n");
	test("Creating comment table...",
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."comments (".
  			"id int(10) unsigned NOT NULL auto_increment,".
  			"page_tag varchar(50) NOT NULL default '',".
  			"time datetime NOT NULL default '0000-00-00 00:00:00',".
  			"comment text NOT NULL,".
  			"user varchar(50) NOT NULL default '',".
  			"PRIMARY KEY  (id),".
  			"KEY idx_page_tag (page_tag),".
  			"KEY idx_time (time),".
			") TYPE=MyISAM;", $dblink), "Already done? Hmm!", 1);
	test("Copying comments from the pages table to the new comments table...", 
		@mysql_query("INSERT INTO ".$config["table_prefix"]."comments (page_tag, time, comment, user) SELECT comment_on, time, body, user FROM ".$config["table_prefix"]."pages WHERE comment_on != '';", $dblink), "Already done? Hmm!", 1);
	test("Deleting comments from the pages table...", 
		@mysql_query("DELETE FROM ".$config["table_prefix"]."pages WHERE comment_on != ''", $dblink), "Already done? Hmm!", 1);
	test("Removing comment_on field from the pages table...", 
		@mysql_query("ALTER TABLE ".$config["table_prefix"]."pages DROP comment_on", $dblink), "Already done? Hmm!", 1);
	test("Removing comment pages from the ACL table...", 
		@mysql_query("DELETE FROM ".$config["table_prefix"]."acls WHERE page_tag like 'Comment%'", $dblink), "Already done? Hmm!", 1);

}

?>

<p>
In the next step, the installer will try to write the updated configuration file, <tt><?php echo $wakkaConfigLocation ?></tt>.
Please make sure the web server has write access to the file, or you will have to edit it manually.
Once again, see <a href="http://www.wakkawiki.com/WakkaInstallation" target="_blank">WakkaWiki:WakkaInstallation</a> for details.
</p>

<form action="<?php echo myLocation(); ?>?installAction=writeconfig" method="POST">
<input type="hidden" name="config" value="<?php echo htmlspecialchars(serialize($config)) ?>" />
<input type="submit" value="Continue" />
</form>