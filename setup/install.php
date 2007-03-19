<?php

// i18n section
if (!defined('ADDING_CONFIG_ENTRY')) define('ADDING_CONFIG_ENTRY', 'Adding a new option to the wikka.config file: %s'); // %s - name of the config option
if (!defined('DELETING_COOKIES')) define('DELETING_COOKIES', 'Deleting wikka cookies since their name has changed.');

// initialization
$config = array(); //required since PHP5, to avoid warning on array_merge #94
// fetch configuration
$config = $_POST["config"];
// merge existing configuration with new one
$config = array_merge($wakkaConfig, $config);

// test configuration
print("<h2>Testing Configuration</h2>\n");
test("Testing MySQL connection settings...", $dblink = @mysql_connect($config["mysql_host"], $config["mysql_user"], $config["mysql_password"]));
test("Looking for database...", @mysql_select_db($config["mysql_database"], $dblink), "The database you configured was not found. Remember, it needs to exist before you can install/upgrade Wakka!\n\nPress the Back button and reconfigure the settings.");
print("<br />\n");

// do installation stuff
if (!$version = trim($wakkaConfig["wakka_version"])) $version = "0";

// set upgrade note to be used when overwriting default pages
$upgrade_note = 'Upgrading from '.$version.' to '.WAKKA_VERSION;

switch ($version)
{
// new installation
case "0":
	print("<h2>Installing Stuff</h2>");
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

	test("Adding default pages...", 1);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = '".$config["root_page"]."', body = '{{image url=\"images/wikka_logo.jpg\" alt=\"wikka logo\" title=\"Welcome to your Wikka site!\"}}\n\nThanks for installing [[Wikka:HomePage WikkaWiki]]! This site is running on version ##{{wikkaversion}}## (see WikkaReleaseNotes). \nYou need to [[UserSettings login]] and then double-click on any page or click on the \"Edit page\" link at the bottom to get started. \n\nAlso don\'t forget to visit the [[Wikka:HomePage WikkaWiki website]]! \n\nUseful pages: FormattingRules, WikkaDocumentation, OrphanedPages, WantedPages, TextSearch.', user = 'WikkaInstaller', owner = '".$config["admin_users"]."', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'RecentChanges', body = '{{RecentChanges}}{{nocomments}}\n\n\n----\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'RecentlyCommented', body = '{{RecentlyCommented}}{{nocomments}}\n\n\n----\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'UserSettings', body = '{{UserSettings}}{{nocomments}}\n\n\n----\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'PageIndex', body = '{{PageIndex}}{{nocomments}}\n\n\n----\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'WikkaReleaseNotes', body = '{{wikkachanges}}{{nocomments}}\n\n\n----\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'WikkaDocumentation' , body = '=====Wikka Documentation=====\n\nComprehensive and up-to-date documentation on Wikka Wiki can be found on the [[http://wikkawiki.org/WikkaDocumentation main Wikka server]].', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
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
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'FormattingRules', body = '======Wikka Formatting Guide======\n\n<<**Note:** Anything between 2 sets of double-quotes is not formatted.<<::c::\nOnce you have read through this, test your formatting skills in the SandBox.\n----\n===1. Text Formatting===\n\n~##\"\"**I\'m bold**\"\"##\n~**I\'m bold **\n\n~##\"\"//I\'m italic text!//\"\"##\n~//I\'m italic text!//\n\n~##\"\"And I\'m __underlined__!\"\"##\n~And I\'m __underlined__!\n\n~##\"\"##monospace text##\"\"##\n~##monospace text##\n\n~##\"\"\'\'highlight text\'\'\"\"## (using 2 single-quotes)\n~\'\'highlight text\'\'\n\n~##\"\"++Strike through text++\"\"##\n~++Strike through text++\n\n~##\"\"Press #%ANY KEY#%\"\"##\n~Press #%ANY KEY#%\n\n~##\"\"@@Center text@@\"\"##\n~@@Center text@@\n\n===2. Headers===\n\nUse between five ##=## (for the biggest header) and two ##=## (for the smallest header) on both sides of a text to render it as a header.\n\n~##\"\"====== Really big header ======\"\"##\n~====== Really big header ======\n  \n~##\"\"===== Rather big header =====\"\"##\n~===== Rather big header =====\n\n~##\"\"==== Medium header ====\"\"##\n~==== Medium header ====\n\n~##\"\"=== Not-so-big header ===\"\"##\n~=== Not-so-big header ===\n\n~##\"\"== Smallish header ==\"\"##\n~== Smallish header ==\n\n===3. Horizontal separator===\n~##\"\"----\"\"##\n----\n\n===4. Forced line break===\n~##\"\"---\"\"##\n---\n\n===5. Lists and indents===\n\nYou can indent text using a **~**, a **tab** or **4 spaces** (which will auto-convert into a tab).\n\n##\"\"~This text is indented<br />~~This text is double-indented<br />&nbsp;&nbsp;&nbsp;&nbsp;This text is also indented\"\"##\n\n~This text is indented\n~~This text is double-indented\n   This text is also indented\n\nTo create bulleted/ordered lists, use the following markup (you can always use 4 spaces instead of a ##**~**##):\n\n**Bulleted lists**\n##\"\"~- Line one\"\"##\n##\"\"~- Line two\"\"##\n\n- Line one\n- Line two\n\n**Numbered lists**\n##\"\"~1) Line one\"\"##\n##\"\"~1) Line two\"\"##\n\n1) Line one\n1) Line two\n\n**Ordered lists using uppercase characters**\n##\"\"~A) Line one\"\"##\n##\"\"~A) Line two\"\"##\n\nA) Line one\nA) Line two\n\n**Ordered lists using lowercase characters**\n##\"\"~a) Line one\"\"##\n##\"\"~a) Line two\"\"##\n\na) Line one\na) Line two\n\n**Ordered lists using roman numerals**\n##\"\"~I) Line one\"\"##\n##\"\"~I) Line two\"\"##\n\n   I) Line one\nI) Line two\n\n**Ordered lists using lowercase roman numerals**\n##\"\"~i) Line one\"\"##\n##\"\"~i) Line two\"\"##\n\n i) Line one\ni) Line two\n\n===6. Inline comments===\n\nTo format some text as an inline comment, use an indent ( **~**, a **tab** or **4 spaces**) followed by a **\"\"&amp;\"\"**.\n\n**Example:**\n\n##\"\"~&amp; Comment\"\"##\n##\"\"~~&amp; Subcomment\"\"##\n##\"\"~~~&amp; Subsubcomment\"\"##\n\n~& Comment\n~~& Subcomment\n~~~& Subsubcomment\n\n===7. Images===\n\nTo place images on a Wiki page, you can use the ##image## action.\n\n**Example:**\n\n~##\"\"{{image class=\"center\" alt=\"DVD logo\" title=\"An Image Link\" url=\"images/dvdvideo.gif\" link=\"RecentChanges\"}}\"\"##\n~{{image class=\"center\" alt=\"dvd logo\" title=\"An Image Link\" url=\"images/dvdvideo.gif\" link=\"RecentChanges\"}}\n\nLinks can be external, or internal Wiki links. You don\'t need to enter a link at all, and in that case just an image will be inserted. You can use the optional classes ##left## and ##right## to float images left and right. You don\'t need to use all those attributes, only ##url## is required while ##alt## is recommended for accessibility.\n\n===8. Links===\n\nTo create a **link to a wiki page** you can use any of the following options: ---\n~1) type a ##\"\"WikiName\"\"##: --- --- ##\"\"FormattingRules\"\"## --- FormattingRules --- ---\n~1) add a forced link surrounding the page name by ##\"\"[[\"\"## and ##\"\"]]\"\"## (everything after the first space will be shown as description): --- --- ##\"\"[[SandBox Test your formatting skills]]\"\"## --- [[SandBox Test your formatting skills]] --- --- ##\"\"[[SandBox &#27801;&#31665;]]\"\"## --- [[SandBox &#27801;&#31665;]] --- ---\n~1) add an image with a link (see instructions above).\n\nTo **link to external pages**, you can do any of the following: ---\n~1) type a URL inside the page: --- --- ##\"\"http://www.example.com\"\"## --- http://www.example.com --- --- \n~1) add a forced link surrounding the URL by ##\"\"[[\"\"## and ##\"\"]]\"\"## (everything after the first space will be shown as description): --- --- ##\"\"[[http://example.com/jenna/ Jenna\'s Home Page]]\"\"## --- [[http://example.com/jenna/ Jenna\'s Home Page]] --- --- ##\"\"[[mail@example.com Write me!]]\"\"## --- [[mail@example.com Write me!]] --- ---\n~1) add an image with a link (see instructions above);\n~1) add an interwiki link (browse the [[InterWiki list of available interwiki tags]]): --- --- ##\"\"WikiPedia:WikkaWiki\"\"## --- WikiPedia:WikkaWiki --- --- ##\"\"Google:CSS\"\"## --- Google:CSS --- --- ##\"\"Thesaurus:Happy\"\"## --- Thesaurus:Happy --- ---\n\n===9. Tables===\n\nTo create a table, you can use the ##table## action.\n\n**Example:**\n\n~##\"\"{{table columns=\"3\" cellpadding=\"1\" cells=\"BIG;GREEN;FROGS;yes;yes;no;no;no;###\"}}\"\"##\n\n~{{table columns=\"3\" cellpadding=\"1\" cells=\"BIG;GREEN;FROGS;yes;yes;no;no;no;###\"}}\n\nNote that ##\"\"###\"\"## must be used to indicate an empty cell.\nComplex tables can also be created by embedding HTML code in a wiki page (see instructions below).\n\n===10. Colored Text===\n\nColored text can be created using the ##color## action:\n\n**Example:**\n\n~##\"\"{{color c=\"blue\" text=\"This is a test.\"}}\"\"##\n~{{color c=\"blue\" text=\"This is a test.\"}}\n\nYou can also use hex values:\n\n**Example:**\n\n~##\"\"{{color hex=\"#DD0000\" text=\"This is another test.\"}}\"\"##\n~{{color hex=\"#DD0000\" text=\"This is another test.\"}}\n\nAlternatively, you can specify a foreground and background color using the ##fg## and ##bg## parameters (they accept both named and hex values):\n\n**Examples:**\n\n~##\"\"{{color fg=\"#FF0000\" bg=\"#000000\" text=\"This is colored text on colored background\"}}\"\"##\n~{{color fg=\"#FF0000\" bg=\"#000000\" text=\"This is colored text on colored background\"}}\n\n~##\"\"{{color fg=\"lightgreen\" bg=\"black\" text=\"This is colored text on colored background\"}}\"\"##\n~{{color fg=\"lightgreen\" bg=\"black\" text=\"This is colored text on colored background\"}}\n\n\n===11. Floats===\n\nTo create a **left floated box**, use two ##<## characters before and after the block.\n\n**Example:**\n\n~##\"\"&lt;&lt;Some text in a left-floated box hanging around&lt;&lt; Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler.\"\"##\n\n<<Some text in a left-floated box hanging around<<Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler.\n\n::c::To create a **right floated box**, use two ##>## characters before and after the block.\n\n**Example:**\n\n~##\"\">>Some text in a right-floated box hanging around>> Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler.\"\"##\n\n   >>Some text in a right-floated box hanging around>>Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler.\n\n::c:: Use ##\"\"::c::\"\"##  to clear floated blocks.\n\n===12. Code formatters===\n\nYou can easily embed code blocks in a wiki page using a simple markup. Anything within a code block is displayed literally. \nTo create a **generic code block** you can use the following markup:\n\n~##\"\"%% This is a code block %%\"\"##. \n\n%% This is a code block %%\n\nTo create a **code block with syntax highlighting**, you need to specify a //code formatter// (see below for a list of available code formatters). \n\n~##\"\"%%(\"\"{{color c=\"red\" text=\"php\"}}\"\")<br />&lt;?php<br />echo \"Hello, World!\";<br />?&gt;<br />%%\"\"##\n\n%%(php)\n<?php\necho \"Hello, World!\";\n?>\n%%\n\nYou can also specify an optional //starting line// number.\n\n~##\"\"%%(php;\"\"{{color c=\"red\" text=\"15\"}}\"\")<br />&lt;?php<br />echo \"Hello, World!\";<br />?&gt;<br />%%\"\"##\n\n%%(php;15)\n<?php\necho \"Hello, World!\";\n?>\n%%\n\nIf you specify a //filename//, this will be used for downloading the code.\n\n~##\"\"%%(php;15;\"\"{{color c=\"red\" text=\"test.php\"}}\"\")<br />&lt;?php<br />echo \"Hello, World!\";<br />?&gt;<br />%%\"\"##\n\n%%(php;15;test.php)\n<?php\necho \"Hello, World!\";\n?>\n%%\n\n**List of available code formatters:**\n{{table columns=\"6\" cellpadding=\"1\" cells=\"LANGUAGE;FORMATTER;LANGUAGE;FORMATTER;LANGUAGE;FORMATTER;Actionscript;actionscript;ADA;ada;Apache Log;apache;AppleScript; applescript;ASM;asm;ASP;asp;AutoIT;autoit;Bash;bash;BlitzBasic;blitzbasic;BNF;bnf;C;c;C for Macs;c_mac;c#;csharp;C++;cpp;C++ (QT extensions);cpp-qt;CAD DCL;caddcl;CadLisp;cadlisp;CFDG;cfdg;ColdFusion;cfm; CSS;css;D;d;Delphi;delphi;Diff-Output;diff;DIV; div;DOS;dos;Eiffel;eiffel;Fortran;fortran;FreeBasic;freebasic;GML;gml;Groovy;groovy;HTML;html4strict;INI;ini;IO;io;Inno Script;inno;Java 5;java5;Java;java;Javascript;javascript;LaTeX;latex;Lisp;lisp;Lua;lua;Matlab;matlab;Microchip Assembler;mpasm;Microsoft Registry;reg;mIRC;mirc;MySQL;mysql;NSIS;nsis;Objective C;objc;OpenOffice BASIC;oobas;Objective Caml;ocaml;Objective Caml (brief);ocaml-brief;Oracle 8;oracle8;Pascal;pascal;Perl;perl;PHP;php;PHP (brief);php-brief;PL/SQL;plsql;Python;phyton;Q(uick)BASIC;qbasic;robots.txt;robots;Ruby;ruby;SAS;sas;Scheme;scheme;sdlBasic;sdlbasic;SmallTalk;smalltalk;Smarty;smarty;SQL;sql;TCL/iTCL;tcl;T-SQL;tsql;Text;text;thinBasic;thinbasic;Unoidl;idl;VB.NET;vbnet;VHDL;vhdl;Visual BASIC;vb;Visual Fox Pro;visualfoxpro;WinBatch;winbatch;XML;xml;ZiLOG Z80;z80\"}}\n\n===13. Mindmaps===\n\nWikka has native support for [[Wikka:FreeMind mindmaps]]. There are two options for embedding a mindmap in a wiki page.\n\n**Option 1:** Upload a \"\"FreeMind\"\" file to a webserver, and then place a link to it on a wikka page:\n  ##\"\"http://yourdomain.com/freemind/freemind.mm\"\"##\nNo special formatting is necessary.\n\n**Option 2:** Paste the \"\"FreeMind\"\" data directly into a wikka page:\n~- Open a \"\"FreeMind\"\" file with a text editor.\n~- Select all, and copy the data.\n~- Browse to your Wikka site and paste the Freemind data into a page. \n\n===14. Embedded HTML===\n\nYou can easily paste HTML in a wiki page by wrapping it into two sets of doublequotes. \n\n~##&quot;&quot;[html code]&quot;&quot;##\n\n**Examples:**\n\n~##&quot;&quot;y = x<sup>n+1</sup>&quot;&quot;##\n~\"\"y = x<sup>n+1</sup>\"\"\n\n~##&quot;&quot;<acronym title=\"Cascade Style Sheet\">CSS</acronym>&quot;&quot;##\n~\"\"<acronym title=\"Cascade Style Sheet\">CSS</acronym>\"\"\n\nBy default, some HTML tags are removed by the \"\"SafeHTML\"\" parser to protect against potentially dangerous code.  The list of tags that are stripped can be found on the Wikka:SafeHTML page.\n\nIt is possible to allow //all// HTML tags to be used, see Wikka:UsingHTML for more information.\n\n----\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'OwnedPages', body = '{{ownedpages}}{{nocomments}}These numbers merely reflect how many pages you have created, not how much content you have contributed or the quality of your contributions. To see how you rank with other members, you may be interested in checking out the HighScores. \n\n\n----\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'SandBox', body = 'Test your formatting skills here.\n\n\n\n\n----\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'SysInfo', body = '===== System Information =====\n\n~-Wikka version: ##{{wikkaversion}}##\n~-PHP version: ##{{phpversion}}##\n~-\"\"MySQL\"\" version: ##{{mysqlversion}}##\n~-\"\"GeSHi\"\" version: ##{{geshiversion}}##\n~-Server:\n~~-Host: ##{{system show=\"host\"}}##\n~~-Operative System: ##{{system show=\"os\"}}##\n~~-Machine: ##{{system show=\"machine\"}}##\n\n----\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink);

	test("Setting default ACL...", 1);
	mysql_query("insert into ".$config["table_prefix"]."acls set page_tag = 'UserSettings', read_acl = '*', write_acl = '+', comment_acl = '+'", $dblink);

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
	test(sprintf(ADDING_CONFIG_ENTRY, 'double_doublequote_html'), 1);
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
	test("Adding WikkaReleaseNotes page...", 
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'WikkaReleaseNotes', body = '{{wikkachanges}}{{nocomments}}\n\n\n----\nCategoryWiki', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink), "Already done? OK!", 0);
	test("Adding WikkaDocumentation page...", 
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'WikkaDocumentation' , body = '=====Wikka Documentation=====\n\nComprehensive and up-to-date documentation on Wikka Wiki can be found on the [[http://wikkawiki.org/WikkaDocumentation main Wikka server]].', owner = '(Public)', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink), "Already done? OK!", 0);
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
	$config["wiki_suffix"] = '@wikka';
	test(sprintf(ADDING_CONFIG_ENTRY, 'require_edit_note'), 1);
	$config["require_edit_note"] = '0';
	test(sprintf(ADDING_CONFIG_ENTRY, 'public_sysinfo'), 1);
	$config["public_sysinfo"] = '0';
	// cookie names have changed -- logout user and delete the old cookies
	test(DELETING_COOKIES, 1);
	DeleteCookie("wikka_user_name");
	DeleteCookie("wikka_pass"); 
	//adding SysInfo page
	test("Adding SysInfo page...",
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'SysInfo', body = '===== System Information =====\n\n~-Wikka version: ##{{wikkaversion}}##\n~-PHP version: ##{{phpversion}}##\n~-\"\"MySQL\"\" version: ##{{mysqlversion}}##\n~-\"\"GeSHi\"\" version: ##{{geshiversion}}##\n~-Server:\n~~-Host: ##{{system show=\"host\"}}##\n~~-Operative System: ##{{system show=\"os\"}}##\n~~-Machine: ##{{system show=\"machine\"}}##\n\n----\nCategoryWiki', owner = '(Public)', note='".$upgrade_note."',  user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink), "Already done? OK!", 0);
case "1.1.6.2-alpha":
case "1.1.6.2-beta":
case "1.1.6.2":
	test("Archiving latest FormattingRules revision...", 
	mysql_query("update ".$config["table_prefix"]."pages set latest = 'N' where tag = 'FormattingRules'"), "Already done? OK!", 0);
	test("Updating FormattingRules page...",
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'FormattingRules', body = '======Wikka Formatting Guide======\n\n<<**Note:** Anything between 2 sets of double-quotes is not formatted.<<::c::\nOnce you have read through this, test your formatting skills in the SandBox.\n----\n===1. Text Formatting===\n\n~##\"\"**I\'m bold**\"\"##\n~**I\'m bold **\n\n~##\"\"//I\'m italic text!//\"\"##\n~//I\'m italic text!//\n\n~##\"\"And I\'m __underlined__!\"\"##\n~And I\'m __underlined__!\n\n~##\"\"##monospace text##\"\"##\n~##monospace text##\n\n~##\"\"\'\'highlight text\'\'\"\"## (using 2 single-quotes)\n~\'\'highlight text\'\'\n\n~##\"\"++Strike through text++\"\"##\n~++Strike through text++\n\n~##\"\"Press #%ANY KEY#%\"\"##\n~Press #%ANY KEY#%\n\n~##\"\"@@Center text@@\"\"##\n~@@Center text@@\n\n===2. Headers===\n\nUse between five ##=## (for the biggest header) and two ##=## (for the smallest header) on both sides of a text to render it as a header.\n\n~##\"\"====== Really big header ======\"\"##\n~====== Really big header ======\n  \n~##\"\"===== Rather big header =====\"\"##\n~===== Rather big header =====\n\n~##\"\"==== Medium header ====\"\"##\n~==== Medium header ====\n\n~##\"\"=== Not-so-big header ===\"\"##\n~=== Not-so-big header ===\n\n~##\"\"== Smallish header ==\"\"##\n~== Smallish header ==\n\n===3. Horizontal separator===\n~##\"\"----\"\"##\n----\n\n===4. Forced line break===\n~##\"\"---\"\"##\n---\n\n===5. Lists and indents===\n\nYou can indent text using a **~**, a **tab** or **4 spaces** (which will auto-convert into a tab).\n\n##\"\"~This text is indented<br />~~This text is double-indented<br />&nbsp;&nbsp;&nbsp;&nbsp;This text is also indented\"\"##\n\n~This text is indented\n~~This text is double-indented\n   This text is also indented\n\nTo create bulleted/ordered lists, use the following markup (you can always use 4 spaces instead of a ##**~**##):\n\n**Bulleted lists**\n##\"\"~- Line one\"\"##\n##\"\"~- Line two\"\"##\n\n- Line one\n- Line two\n\n**Numbered lists**\n##\"\"~1) Line one\"\"##\n##\"\"~1) Line two\"\"##\n\n1) Line one\n1) Line two\n\n**Ordered lists using uppercase characters**\n##\"\"~A) Line one\"\"##\n##\"\"~A) Line two\"\"##\n\nA) Line one\nA) Line two\n\n**Ordered lists using lowercase characters**\n##\"\"~a) Line one\"\"##\n##\"\"~a) Line two\"\"##\n\na) Line one\na) Line two\n\n**Ordered lists using roman numerals**\n##\"\"~I) Line one\"\"##\n##\"\"~I) Line two\"\"##\n\n   I) Line one\nI) Line two\n\n**Ordered lists using lowercase roman numerals**\n##\"\"~i) Line one\"\"##\n##\"\"~i) Line two\"\"##\n\n i) Line one\ni) Line two\n\n===6. Inline comments===\n\nTo format some text as an inline comment, use an indent ( **~**, a **tab** or **4 spaces**) followed by a **\"\"&amp;\"\"**.\n\n**Example:**\n\n##\"\"~&amp; Comment\"\"##\n##\"\"~~&amp; Subcomment\"\"##\n##\"\"~~~&amp; Subsubcomment\"\"##\n\n~& Comment\n~~& Subcomment\n~~~& Subsubcomment\n\n===7. Images===\n\nTo place images on a Wiki page, you can use the ##image## action.\n\n**Example:**\n\n~##\"\"{{image class=\"center\" alt=\"DVD logo\" title=\"An Image Link\" url=\"images/dvdvideo.gif\" link=\"RecentChanges\"}}\"\"##\n~{{image class=\"center\" alt=\"dvd logo\" title=\"An Image Link\" url=\"images/dvdvideo.gif\" link=\"RecentChanges\"}}\n\nLinks can be external, or internal Wiki links. You don\'t need to enter a link at all, and in that case just an image will be inserted. You can use the optional classes ##left## and ##right## to float images left and right. You don\'t need to use all those attributes, only ##url## is required while ##alt## is recommended for accessibility.\n\n===8. Links===\n\nTo create a **link to a wiki page** you can use any of the following options: ---\n~1) type a ##\"\"WikiName\"\"##: --- --- ##\"\"FormattingRules\"\"## --- FormattingRules --- ---\n~1) add a forced link surrounding the page name by ##\"\"[[\"\"## and ##\"\"]]\"\"## (everything after the first space will be shown as description): --- --- ##\"\"[[SandBox Test your formatting skills]]\"\"## --- [[SandBox Test your formatting skills]] --- --- ##\"\"[[SandBox &#27801;&#31665;]]\"\"## --- [[SandBox &#27801;&#31665;]] --- ---\n~1) add an image with a link (see instructions above).\n\nTo **link to external pages**, you can do any of the following: ---\n~1) type a URL inside the page: --- --- ##\"\"http://www.example.com\"\"## --- http://www.example.com --- --- \n~1) add a forced link surrounding the URL by ##\"\"[[\"\"## and ##\"\"]]\"\"## (everything after the first space will be shown as description): --- --- ##\"\"[[http://example.com/jenna/ Jenna\'s Home Page]]\"\"## --- [[http://example.com/jenna/ Jenna\'s Home Page]] --- --- ##\"\"[[mail@example.com Write me!]]\"\"## --- [[mail@example.com Write me!]] --- ---\n~1) add an image with a link (see instructions above);\n~1) add an interwiki link (browse the [[InterWiki list of available interwiki tags]]): --- --- ##\"\"WikiPedia:WikkaWiki\"\"## --- WikiPedia:WikkaWiki --- --- ##\"\"Google:CSS\"\"## --- Google:CSS --- --- ##\"\"Thesaurus:Happy\"\"## --- Thesaurus:Happy --- ---\n\n===9. Tables===\n\nTo create a table, you can use the ##table## action.\n\n**Example:**\n\n~##\"\"{{table columns=\"3\" cellpadding=\"1\" cells=\"BIG;GREEN;FROGS;yes;yes;no;no;no;###\"}}\"\"##\n\n~{{table columns=\"3\" cellpadding=\"1\" cells=\"BIG;GREEN;FROGS;yes;yes;no;no;no;###\"}}\n\nNote that ##\"\"###\"\"## must be used to indicate an empty cell.\nComplex tables can also be created by embedding HTML code in a wiki page (see instructions below).\n\n===10. Colored Text===\n\nColored text can be created using the ##color## action:\n\n**Example:**\n\n~##\"\"{{color c=\"blue\" text=\"This is a test.\"}}\"\"##\n~{{color c=\"blue\" text=\"This is a test.\"}}\n\nYou can also use hex values:\n\n**Example:**\n\n~##\"\"{{color hex=\"#DD0000\" text=\"This is another test.\"}}\"\"##\n~{{color hex=\"#DD0000\" text=\"This is another test.\"}}\n\nAlternatively, you can specify a foreground and background color using the ##fg## and ##bg## parameters (they accept both named and hex values):\n\n**Examples:**\n\n~##\"\"{{color fg=\"#FF0000\" bg=\"#000000\" text=\"This is colored text on colored background\"}}\"\"##\n~{{color fg=\"#FF0000\" bg=\"#000000\" text=\"This is colored text on colored background\"}}\n\n~##\"\"{{color fg=\"lightgreen\" bg=\"black\" text=\"This is colored text on colored background\"}}\"\"##\n~{{color fg=\"lightgreen\" bg=\"black\" text=\"This is colored text on colored background\"}}\n\n\n===11. Floats===\n\nTo create a **left floated box**, use two ##<## characters before and after the block.\n\n**Example:**\n\n~##\"\"&lt;&lt;Some text in a left-floated box hanging around&lt;&lt; Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler.\"\"##\n\n<<Some text in a left-floated box hanging around<<Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler.\n\n::c::To create a **right floated box**, use two ##>## characters before and after the block.\n\n**Example:**\n\n~##\"\">>Some text in a right-floated box hanging around>> Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler.\"\"##\n\n   >>Some text in a right-floated box hanging around>>Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler.\n\n::c:: Use ##\"\"::c::\"\"##  to clear floated blocks.\n\n===12. Code formatters===\n\nYou can easily embed code blocks in a wiki page using a simple markup. Anything within a code block is displayed literally. \nTo create a **generic code block** you can use the following markup:\n\n~##\"\"%% This is a code block %%\"\"##. \n\n%% This is a code block %%\n\nTo create a **code block with syntax highlighting**, you need to specify a //code formatter// (see below for a list of available code formatters). \n\n~##\"\"%%(\"\"{{color c=\"red\" text=\"php\"}}\"\")<br />&lt;?php<br />echo \"Hello, World!\";<br />?&gt;<br />%%\"\"##\n\n%%(php)\n<?php\necho \"Hello, World!\";\n?>\n%%\n\nYou can also specify an optional //starting line// number.\n\n~##\"\"%%(php;\"\"{{color c=\"red\" text=\"15\"}}\"\")<br />&lt;?php<br />echo \"Hello, World!\";<br />?&gt;<br />%%\"\"##\n\n%%(php;15)\n<?php\necho \"Hello, World!\";\n?>\n%%\n\nIf you specify a //filename//, this will be used for downloading the code.\n\n~##\"\"%%(php;15;\"\"{{color c=\"red\" text=\"test.php\"}}\"\")<br />&lt;?php<br />echo \"Hello, World!\";<br />?&gt;<br />%%\"\"##\n\n%%(php;15;test.php)\n<?php\necho \"Hello, World!\";\n?>\n%%\n\n**List of available code formatters:**\n{{table columns=\"6\" cellpadding=\"1\" cells=\"LANGUAGE;FORMATTER;LANGUAGE;FORMATTER;LANGUAGE;FORMATTER;Actionscript;actionscript;ADA;ada;Apache Log;apache;AppleScript; applescript;ASM;asm;ASP;asp;AutoIT;autoit;Bash;bash;BlitzBasic;blitzbasic;BNF;bnf;C;c;C for Macs;c_mac;c#;csharp;C++;cpp;C++ (QT extensions);cpp-qt;CAD DCL;caddcl;CadLisp;cadlisp;CFDG;cfdg;ColdFusion;cfm; CSS;css;D;d;Delphi;delphi;Diff-Output;diff;DIV; div;DOS;dos;Eiffel;eiffel;Fortran;fortran;FreeBasic;freebasic;GML;gml;Groovy;groovy;HTML;html4strict;INI;ini;IO;io;Inno Script;inno;Java 5;java5;Java;java;Javascript;javascript;LaTeX;latex;Lisp;lisp;Lua;lua;Matlab;matlab;Microchip Assembler;mpasm;Microsoft Registry;reg;mIRC;mirc;MySQL;mysql;NSIS;nsis;Objective C;objc;OpenOffice BASIC;oobas;Objective Caml;ocaml;Objective Caml (brief);ocaml-brief;Oracle 8;oracle8;Pascal;pascal;Perl;perl;PHP;php;PHP (brief);php-brief;PL/SQL;plsql;Python;phyton;Q(uick)BASIC;qbasic;robots.txt;robots;Ruby;ruby;SAS;sas;Scheme;scheme;sdlBasic;sdlbasic;SmallTalk;smalltalk;Smarty;smarty;SQL;sql;TCL/iTCL;tcl;T-SQL;tsql;Text;text;thinBasic;thinbasic;Unoidl;idl;VB.NET;vbnet;VHDL;vhdl;Visual BASIC;vb;Visual Fox Pro;visualfoxpro;WinBatch;winbatch;XML;xml;ZiLOG Z80;z80\"}}\n\n===13. Mindmaps===\n\nWikka has native support for [[Wikka:FreeMind mindmaps]]. There are two options for embedding a mindmap in a wiki page.\n\n**Option 1:** Upload a \"\"FreeMind\"\" file to a webserver, and then place a link to it on a wikka page:\n  ##\"\"http://yourdomain.com/freemind/freemind.mm\"\"##\nNo special formatting is necessary.\n\n**Option 2:** Paste the \"\"FreeMind\"\" data directly into a wikka page:\n~- Open a \"\"FreeMind\"\" file with a text editor.\n~- Select all, and copy the data.\n~- Browse to your Wikka site and paste the Freemind data into a page. \n\n===14. Embedded HTML===\n\nYou can easily paste HTML in a wiki page by wrapping it into two sets of doublequotes. \n\n~##&quot;&quot;[html code]&quot;&quot;##\n\n**Examples:**\n\n~##&quot;&quot;y = x<sup>n+1</sup>&quot;&quot;##\n~\"\"y = x<sup>n+1</sup>\"\"\n\n~##&quot;&quot;<acronym title=\"Cascade Style Sheet\">CSS</acronym>&quot;&quot;##\n~\"\"<acronym title=\"Cascade Style Sheet\">CSS</acronym>\"\"\n\nBy default, some HTML tags are removed by the \"\"SafeHTML\"\" parser to protect against potentially dangerous code.  The list of tags that are stripped can be found on the Wikka:SafeHTML page.\n\nIt is possible to allow //all// HTML tags to be used, see Wikka:UsingHTML for more information.\n\n----\nCategoryWiki', owner = '(Public)', note='".$upgrade_note."', user = 'WikkaInstaller', time = now(), latest = 'Y'", $dblink), "Already done? OK!", 0);
	break;
}
?>

<p>
In the next step, the installer will try to write the updated configuration file, <tt><?php echo $wakkaConfigLocation ?></tt>.
Please make sure the web server has write access to the file, or you will have to edit it manually.
Once again, see <a href="http://wikkawiki.org/WikkaInstallation" target="_blank">Wikka:WikkaInstallation</a> for details.
</p>

<form action="<?php echo myLocation(); ?>?installAction=writeconfig" method="post">
<input type="hidden" name="config" value="<?php echo Wakka::hsc_secure(serialize($config)) ?>" /><!--#427-->
<input type="submit" value="Continue" />
</form>