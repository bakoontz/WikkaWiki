-- MySQL dump 10.9
--
-- Host: localhost    Database: wikka_1166
-- ------------------------------------------------------
-- Server version	4.1.10

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

--
-- Table structure for table `wikka_acls`
--

DROP TABLE IF EXISTS `wikka_acls`;
CREATE TABLE `wikka_acls` (
  `page_tag` varchar(75) NOT NULL default '',
  `read_acl` text NOT NULL,
  `write_acl` text NOT NULL,
  `comment_acl` text NOT NULL,
  PRIMARY KEY  (`page_tag`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `wikka_acls`
--


/*!40000 ALTER TABLE `wikka_acls` DISABLE KEYS */;
LOCK TABLES `wikka_acls` WRITE;
INSERT INTO `wikka_acls` VALUES ('UserSettings','*','+','+'),('AdminUsers','!*','!*','!*'),('AdminPages','!*','!*','!*'),('DatabaseInfo','!*','!*','!*');
UNLOCK TABLES;
/*!40000 ALTER TABLE `wikka_acls` ENABLE KEYS */;

--
-- Table structure for table `wikka_comments`
--

DROP TABLE IF EXISTS `wikka_comments`;
CREATE TABLE `wikka_comments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `page_tag` varchar(75) NOT NULL default '',
  `time` datetime NOT NULL default '0000-00-00 00:00:00',
  `comment` text NOT NULL,
  `user` varchar(75) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `idx_page_tag` (`page_tag`),
  KEY `idx_time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `wikka_comments`
--


/*!40000 ALTER TABLE `wikka_comments` DISABLE KEYS */;
LOCK TABLES `wikka_comments` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `wikka_comments` ENABLE KEYS */;

--
-- Table structure for table `wikka_links`
--

DROP TABLE IF EXISTS `wikka_links`;
CREATE TABLE `wikka_links` (
  `from_tag` varchar(75) NOT NULL default '',
  `to_tag` varchar(75) NOT NULL default '',
  UNIQUE KEY `from_tag` (`from_tag`,`to_tag`),
  KEY `idx_to` (`to_tag`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `wikka_links`
--


/*!40000 ALTER TABLE `wikka_links` DISABLE KEYS */;
LOCK TABLES `wikka_links` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `wikka_links` ENABLE KEYS */;

--
-- Table structure for table `wikka_pages`
--

DROP TABLE IF EXISTS `wikka_pages`;
CREATE TABLE `wikka_pages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tag` varchar(75) NOT NULL default '',
  `time` datetime NOT NULL default '0000-00-00 00:00:00',
  `body` mediumtext NOT NULL,
  `owner` varchar(75) NOT NULL default '',
  `user` varchar(75) NOT NULL default '',
  `latest` enum('Y','N') NOT NULL default 'N',
  `note` varchar(100) NOT NULL default '',
  `handler` varchar(30) NOT NULL default 'page',
  PRIMARY KEY  (`id`),
  KEY `idx_tag` (`tag`),
  KEY `idx_time` (`time`),
  KEY `idx_latest` (`latest`),
  FULLTEXT KEY `body` (`body`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `wikka_pages`
--


/*!40000 ALTER TABLE `wikka_pages` DISABLE KEYS */;
LOCK TABLES `wikka_pages` WRITE;
INSERT INTO `wikka_pages` VALUES (1,'HomePage','2009-02-14 20:41:39','{{image url=\"images/wikka_logo.jpg\" alt=\"wikka logo\" title=\"Welcome to your Wikka site!\"}}\n{{checkversion}}\nThanks for installing [[Wikka:HomePage WikkaWiki]]! This site is running on version ##{{wikkaversion}}## (see WikkaReleaseNotes). \nYou need to [[UserSettings login]] and then double-click on any page or click on the \"Edit page\" link at the bottom to get started. \n\nAlso don\'t forget to visit the [[Wikka:HomePage WikkaWiki website]]! \n\nUseful pages: FormattingRules, WikkaDocumentation, OrphanedPages, WantedPages, TextSearch.','AdminUser','WikkaInstaller','Y','','page'),(2,'RecentChanges','2009-02-14 20:41:39','{{RecentChanges}}{{nocomments}}\n\n\n----\nCategoryWiki','(Public)','WikkaInstaller','Y','','page'),(3,'RecentlyCommented','2009-02-14 20:41:39','{{RecentlyCommented}}{{nocomments}}\n\n\n----\nCategoryWiki','(Public)','WikkaInstaller','Y','','page'),(4,'UserSettings','2009-02-14 20:41:39','{{UserSettings}}{{nocomments}}\n\n\n----\nCategoryWiki','(Public)','WikkaInstaller','Y','','page'),(5,'PageIndex','2009-02-14 20:41:39','{{PageIndex}}{{nocomments}}\n\n\n----\nCategoryWiki','(Public)','WikkaInstaller','Y','','page'),(6,'WikkaReleaseNotes','2009-02-14 20:41:39','{{wikkachanges}}{{nocomments}}\n\n\n----\nCategoryWiki','(Public)','WikkaInstaller','Y','','page'),(7,'WikkaDocumentation','2009-02-14 20:41:39','=====Wikka Documentation=====\n\nComprehensive and up-to-date documentation on Wikka Wiki can be found on the [[http://docs.wikkawiki.org/ Wikka Documentation server]].','(Public)','WikkaInstaller','Y','','page'),(8,'WantedPages','2009-02-14 20:41:39','{{WantedPages}}{{nocomments}}\n\n\n----\nCategoryWiki','(Public)','WikkaInstaller','Y','','page'),(9,'OrphanedPages','2009-02-14 20:41:39','====Orphaned Pages====\n\nThe following list shows those pages held in the Wiki that are not linked to on any other pages.\n\n{{OrphanedPages}}{{nocomments}}\n\n\n----\nCategoryWiki','(Public)','WikkaInstaller','Y','','page'),(10,'TextSearch','2009-02-14 20:41:39','{{TextSearch}}{{nocomments}}\n\n\n----\nCategoryWiki','(Public)','WikkaInstaller','Y','','page'),(11,'TextSearchExpanded','2009-02-14 20:41:39','{{textsearchexpanded}}{{nocomments}}\n\n\n----\nCategoryWiki','(Public)','WikkaInstaller','Y','','page'),(12,'MyPages','2009-02-14 20:41:39','{{MyPages}}{{nocomments}}\n\n\n----\nCategoryWiki','(Public)','WikkaInstaller','Y','','page'),(13,'MyChanges','2009-02-14 20:41:39','{{MyChanges}}{{nocomments}}\n\n\n----\nCategoryWiki','(Public)','WikkaInstaller','Y','','page'),(14,'InterWiki','2009-02-14 20:41:39','{{interwikilist}}\n\n\n----\nCategoryWiki','(Public)','WikkaInstaller','Y','','page'),(15,'PasswordForgotten','2009-02-14 20:41:39','{{emailpassword}}\n\n\n----\nCategoryWiki','(Public)','WikkaInstaller','Y','','page'),(16,'WikiCategory','2009-02-14 20:41:39','=====How to use categories=====\nThis wiki is using a very flexible but simple categorizing system to keep everything properly organized.\n\n====1. Adding a page to an existing category====\nTo \'\'add a page to an existing category\'\' simply add a link to the relevant category page. For example, to mark page ##\"\"MyPage\"\"## as a child of category ##\"\"MyCategory\"\"##, just add a link to ##\"\"MyCategory\"\"## from ##\"\"MyPage\"\"##. This will automatically add ##\"\"MyPage\"\"## to the list of pages belonging to that category. Category links are put by convention at the end of the page, but the position of these links does not affect their behavior.\n\n====2. Adding a subcategory to an existing category====\nTo \'\'create a hierarchy of categories\'\', you can follow the same instructions to add pages to categories. For example, to mark category ##\"\"Category2\"\"## as a child (or subcategory) of another category ##\"\"Category1\"\"##, just add a link to ##\"\"Category1\"\"## in ##\"\"Category2\"\"##. This will automatically add ##\"\"Category2\"\"## to the list of ##\"\"Category1\"\"##\'s children.\n\n====3. Creating new categories====\nTo \'\'start a new category\'\' just create a page containing ##\"\"{{category}}\"\"##. This will mark the page as a special //category page// and will output a list of pages belonging to the category. Category page names start by convention with the word ##Category## but you can also create categories without following this convention. To add a new category to the master list of categories just add a link from it to CategoryCategory.\n\n====4. Browsing categories====\nTo \'\'browse the categories\'\' available on your wiki you can start from CategoryCategory. If all pages and subcategories are properly linked as described above, you will be able to browse the whole hierarchy of categories starting from this page.\n\n----\nCategoryWiki','(Public)','WikkaInstaller','Y','','page'),(17,'CategoryWiki','2009-02-14 20:41:39','===Wiki Related Category===\nThis Category will contain links to pages talking about Wikis and Wikis specific topics. When creating such pages, be sure to include CategoryWiki at the bottom of each page, so that page shows listed.\n\n\n----\n\n{{category col=\"3\"}}\n\n\n----\n[[CategoryCategory List of all categories]]','(Public)','WikkaInstaller','Y','','page'),(18,'CategoryAdmin','2009-02-14 20:41:39','=====Wiki Administration Category=====\nThis category links to pages for wiki administration.\n\n\n----\n\n{{category}}\n\n\n----\n[[CategoryCategory List of all categories]]','(Public)','WikkaInstaller','Y','','page'),(19,'CategoryCategory','2009-02-14 20:41:39','===List of All Categories===\nBelow is the list of all Categories existing on this Wiki, granted that users did things right when they created their pages or new Categories. See WikiCategory for how the system works.\n\n----\n\n{{Category}}','(Public)','WikkaInstaller','Y','','page'),(20,'FormattingRules','2009-02-14 20:41:39','======Wikka Formatting Guide======\n\n<<**Note:** Anything between 2 sets of double-quotes is not formatted.<<::c::\nOnce you have read through this, test your formatting skills in the SandBox.\n----\n===1. Text Formatting===\n\n~##\"\"**I\'m bold**\"\"##\n~**I\'m bold **\n\n~##\"\"//I\'m italic text!//\"\"##\n~//I\'m italic text!//\n\n~##\"\"And I\'m __underlined__!\"\"##\n~And I\'m __underlined__!\n\n~##\"\"##monospace text##\"\"##\n~##monospace text##\n\n~##\"\"\'\'highlight text\'\'\"\"## (using 2 single-quotes)\n~\'\'highlight text\'\'\n\n~##\"\"++Strike through text++\"\"##\n~++Strike through text++\n\n~##\"\"Press #%ANY KEY#%\"\"##\n~Press #%ANY KEY#%\n\n~##\"\"@@Center text@@\"\"##\n~@@Center text@@\n\n===2. Headers===\n\nUse between six ##=## (for the biggest header) and two ##=## (for the smallest header) on both sides of a text to render it as a header.\n\n~##\"\"====== Really big header ======\"\"##\n~====== Really big header ======\n  \n~##\"\"===== Rather big header =====\"\"##\n~===== Rather big header =====\n\n~##\"\"==== Medium header ====\"\"##\n~==== Medium header ====\n\n~##\"\"=== Not-so-big header ===\"\"##\n~=== Not-so-big header ===\n\n~##\"\"== Smallish header ==\"\"##\n~== Smallish header ==\n\n===3. Horizontal separator===\n~##\"\"----\"\"##\n----\n\n===4. Forced line break===\n~##\"\"---\"\"##\n---\n\n===5. Lists and indents===\n\nYou can indent text using a **~**, a **tab** or **4 spaces** (which will auto-convert into a tab).\n\n##\"\"~This text is indented<br />~~This text is double-indented<br />&nbsp;&nbsp;&nbsp;&nbsp;This text is also indented\"\"##\n\n~This text is indented\n~~This text is double-indented\n	This text is also indented\n\nTo create bulleted/ordered lists, use the following markup (you can always use 4 spaces instead of a ##**~**##):\n\n**Bulleted lists**\n##\"\"~- Line one\"\"##\n##\"\"~- Line two\"\"##\n\n	- Line one\n	- Line two\n\n**Numbered lists**\n##\"\"~1) Line one\"\"##\n##\"\"~1) Line two\"\"##\n\n	1) Line one\n	1) Line two\n\n**Ordered lists using uppercase characters**\n##\"\"~A) Line one\"\"##\n##\"\"~A) Line two\"\"##\n\n	A) Line one\n	A) Line two\n\n**Ordered lists using lowercase characters**\n##\"\"~a) Line one\"\"##\n##\"\"~a) Line two\"\"##\n\n	a) Line one\n	a) Line two\n\n**Ordered lists using roman numerals**\n##\"\"~I) Line one\"\"##\n##\"\"~I) Line two\"\"##\n\n	I) Line one\n	I) Line two\n\n**Ordered lists using lowercase roman numerals**\n##\"\"~i) Line one\"\"##\n##\"\"~i) Line two\"\"##\n\n	i) Line one\n	i) Line two\n\n===6. Inline comments===\n\nTo format some text as an inline comment, use an indent ( **~**, a **tab** or **4 spaces**) followed by a **\"\"&amp;\"\"**.\n\n**Example:**\n\n##\"\"~&amp; Comment\"\"##\n##\"\"~~&amp; Subcomment\"\"##\n##\"\"~~~&amp; Subsubcomment\"\"##\n\n~& Comment\n~~& Subcomment\n~~~& Subsubcomment\n\n===7. Images===\n\nTo place images on a Wiki page, you can use the ##image## action.\n\n**Example:**\n\n~##\"\"{{image class=\"center\" alt=\"DVD logo\" title=\"An Image Link\" url=\"images/dvdvideo.gif\" link=\"RecentChanges\"}}\"\"##\n~{{image class=\"center\" alt=\"dvd logo\" title=\"An Image Link\" url=\"images/dvdvideo.gif\" link=\"RecentChanges\"}}\n\nLinks can be external, or internal Wiki links. You don\'t need to enter a link at all, and in that case just an image will be inserted. You can use the optional classes ##left## and ##right## to float images left and right. You don\'t need to use all those attributes, only ##url## is required while ##alt## is recommended for accessibility.\n\n===8. Links===\n\nTo create a **link to a wiki page** you can use any of the following options: ---\n~1) type a ##\"\"WikiName\"\"##: --- --- ##\"\"FormattingRules\"\"## --- FormattingRules --- ---\n~1) add a forced link surrounding the page name by ##\"\"[[\"\"## and ##\"\"]]\"\"## (everything after the first space will be shown as description): --- --- ##\"\"[[SandBox Test your formatting skills]]\"\"## --- [[SandBox Test your formatting skills]] --- --- ##\"\"[[SandBox &#27801;&#31665;]]\"\"## --- [[SandBox &#27801;&#31665;]] --- ---\n~1) add an image with a link (see instructions above).\n\nTo **link to external pages**, you can do any of the following: ---\n~1) type a URL inside the page: --- --- ##\"\"http://www.example.com\"\"## --- http://www.example.com --- --- \n~1) add a forced link surrounding the URL by ##\"\"[[\"\"## and ##\"\"]]\"\"## (everything after the first space will be shown as description): --- --- ##\"\"[[http://example.com/jenna/ Jenna\'s Home Page]]\"\"## --- [[http://example.com/jenna/ Jenna\'s Home Page]] --- --- ##\"\"[[mail@example.com Write me!]]\"\"## --- [[mail@example.com Write me!]] --- ---\n~1) add an image with a link (see instructions above);\n~1) add an interwiki link (browse the [[InterWiki list of available interwiki tags]]): --- --- ##\"\"WikiPedia:WikkaWiki\"\"## --- WikiPedia:WikkaWiki --- --- ##\"\"Google:CSS\"\"## --- Google:CSS --- --- ##\"\"Thesaurus:Happy\"\"## --- Thesaurus:Happy --- ---\n\n===9. Tables===\n\nTo create a table, you can use the ##table## action.\n\n**Example:**\n\n~##\"\"{{table columns=\"3\" cellpadding=\"1\" cells=\"BIG;GREEN;FROGS;yes;yes;no;no;no;###\"}}\"\"##\n\n~{{table columns=\"3\" cellpadding=\"1\" cells=\"BIG;GREEN;FROGS;yes;yes;no;no;no;###\"}}\n\nNote that ##\"\"###\"\"## must be used to indicate an empty cell.\nComplex tables can also be created by embedding HTML code in a wiki page (see instructions below).\n\n===10. Colored Text===\n\nColored text can be created using the ##color## action:\n\n**Example:**\n\n~##\"\"{{color c=\"blue\" text=\"This is a test.\"}}\"\"##\n~{{color c=\"blue\" text=\"This is a test.\"}}\n\nYou can also use hex values:\n\n**Example:**\n\n~##\"\"{{color hex=\"#DD0000\" text=\"This is another test.\"}}\"\"##\n~{{color hex=\"#DD0000\" text=\"This is another test.\"}}\n\nAlternatively, you can specify a foreground and background color using the ##fg## and ##bg## parameters (they accept both named and hex values):\n\n**Examples:**\n\n~##\"\"{{color fg=\"#FF0000\" bg=\"#000000\" text=\"This is colored text on colored background\"}}\"\"##\n~{{color fg=\"#FF0000\" bg=\"#000000\" text=\"This is colored text on colored background\"}}\n\n~##\"\"{{color fg=\"yellow\" bg=\"black\" text=\"This is colored text on colored background\"}}\"\"##\n~{{color fg=\"yellow\" bg=\"black\" text=\"This is colored text on colored background\"}}\n\n\n===11. Floats===\n\nTo create a **left floated box**, use two ##<## characters before and after the block.\n\n**Example:**\n\n~##\"\"&lt;&lt;Some text in a left-floated box hanging around&lt;&lt; Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler.\"\"##\n\n<<Some text in a left-floated box hanging around<<Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler.\n\n::c::To create a **right floated box**, use two ##>## characters before and after the block.\n\n**Example:**\n\n~##\"\">>Some text in a right-floated box hanging around>> Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler.\"\"##\n\n   >>Some text in a right-floated box hanging around>>Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler. Some more text as a filler.\n\n::c:: Use ##\"\"::c::\"\"##  to clear floated blocks.\n\n===12. Code formatters===\n\nYou can easily embed code blocks in a wiki page using a simple markup. Anything within a code block is displayed literally. \nTo create a **generic code block** you can use the following markup:\n\n~##\"\"%% This is a code block %%\"\"##. \n\n%% This is a code block %%\n\nTo create a **code block with syntax highlighting**, you need to specify a //code formatter// (see below for a list of available code formatters). \n\n~##\"\"%%(\"\"{{color c=\"red\" text=\"php\"}}\"\")<br />&lt;?php<br />echo \"Hello, World!\";<br />?&gt;<br />%%\"\"##\n\n%%(php)\n<?php\necho \"Hello, World!\";\n?>\n%%\n\nYou can also specify an optional //starting line// number.\n\n~##\"\"%%(php;\"\"{{color c=\"red\" text=\"15\"}}\"\")<br />&lt;?php<br />echo \"Hello, World!\";<br />?&gt;<br />%%\"\"##\n\n%%(php;15)\n<?php\necho \"Hello, World!\";\n?>\n%%\n\nIf you specify a //filename//, this will be used for downloading the code.\n\n~##\"\"%%(php;15;\"\"{{color c=\"red\" text=\"test.php\"}}\"\")<br />&lt;?php<br />echo \"Hello, World!\";<br />?&gt;<br />%%\"\"##\n\n%%(php;15;test.php)\n<?php\necho \"Hello, World!\";\n?>\n%%\n\n**List of available code formatters:**\n{{table columns=\"6\" cellpadding=\"1\" cells=\"LANGUAGE;FORMATTER;LANGUAGE;FORMATTER;LANGUAGE;FORMATTER;ABAP;abap;Actionscript;actionscript;ADA;ada;Apache Log;apache;AppleScript; applescript;ASM;asm;ASP;asp;AutoIT;autoit;Axapta/Dynamics Ax X++;xpp;Bash;bash;BlitzBasic;blitzbasic;BNF;bnf;C;c;C for Macs;c_mac;c#;csharp;C++;cpp;C++ (QT extensions);cpp-qt;CAD DCL;caddcl;CadLisp;cadlisp;CFDG;cfdg;ColdFusion;cfm; CSS;css;D;d;Delphi;delphi;Diff-Output;diff;DIV; div;DOS;dos;dot;dot;Eiffel;eiffel;Fortran;fortran;FOURJ\'s Genero 4GL;genero;FreeBasic;freebasic;GML;gml;Groovy;groovy;Haskell;haskell;HTML;html4strict;INI;ini;IO;io;Inno Script;inno;Java 5;java5;Java;java;Javascript;javascript;LaTeX;latex;Lisp;lisp;Lua;lua;Matlab;matlab;Microchip Assembler;mpasm;Microsoft Registry;reg;mIRC;mirc;Motorola 68000 Assembler;m68k;MySQL;mysql;NSIS;nsis;Objective C;objc;OpenOffice BASIC;oobas;Objective Caml;ocaml;Objective Caml (brief);ocaml-brief;Oracle 8;oracle8;Pascal;pascal;Per (forms);per;Perl;perl;PHP;php;PHP (brief);php-brief;PL/SQL;plsql;Python;phyton;Q(uick)BASIC;qbasic;robots.txt;robots;Ruby;ruby;Ruby on Rails;rails;SAS;sas;Scheme;scheme;sdlBasic;sdlbasic;SmallTalk;smalltalk;Smarty;smarty;SQL;sql;TCL/iTCL;tcl;T-SQL;tsql;Text;text;thinBasic;thinbasic;Unoidl;idl;VB.NET;vbnet;VHDL;vhdl;Visual BASIC;vb;Visual Fox Pro;visualfoxpro;WinBatch;winbatch;XML;xml;ZiLOG Z80;z80;###\"}}\n\n===13. Mindmaps===\n\nWikka has native support for [[Wikka:FreeMind mindmaps]]. There are two options for embedding a mindmap in a wiki page.\n\n**Option 1:** Upload a \"\"FreeMind\"\" file to a webserver, and then place a link to it on a wikka page:\n  ##\"\"http://yourdomain.com/freemind/freemind.mm\"\"##\nNo special formatting is necessary.\n\n**Option 2:** Paste the \"\"FreeMind\"\" data directly into a wikka page:\n~- Open a \"\"FreeMind\"\" file with a text editor.\n~- Select all, and copy the data.\n~- Browse to your Wikka site and paste the Freemind data into a page. \n\n===14. Embedded HTML===\n\nYou can easily paste HTML in a wiki page by wrapping it into two sets of doublequotes. \n\n~##&quot;&quot;[html code]&quot;&quot;##\n\n**Examples:**\n\n~##&quot;&quot;y = x<sup>n+1</sup>&quot;&quot;##\n~\"\"y = x<sup>n+1</sup>\"\"\n\n~##&quot;&quot;<acronym title=\"Cascade Style Sheet\">CSS</acronym>&quot;&quot;##\n~\"\"<acronym title=\"Cascade Style Sheet\">CSS</acronym>\"\"\n\nBy default, some HTML tags are removed by the \"\"SafeHTML\"\" parser to protect against potentially dangerous code.  The list of tags that are stripped can be found on the Wikka:SafeHTML page.\n\nIt is possible to allow //all// HTML tags to be used, see Wikka:UsingHTML for more information.\n\n----\nCategoryWiki','(Public)','WikkaInstaller','Y','','page'),(21,'OwnedPages','2009-02-14 20:41:39','{{ownedpages}}{{nocomments}}These numbers merely reflect how many pages you have created, not how much content you have contributed or the quality of your contributions. To see how you rank with other members, you may be interested in checking out the HighScores. \n\n\n----\nCategoryWiki','(Public)','WikkaInstaller','Y','','page'),(22,'SandBox','2009-02-14 20:41:39','Test your formatting skills here.\n\n\n\n\n----\nCategoryWiki','(Public)','WikkaInstaller','Y','','page'),(23,'SysInfo','2009-02-14 20:41:39','{{checkversion}}\n===== System Information =====\n\n~-Wikka version: ##{{wikkaversion}}##\n~-PHP version: ##{{phpversion}}##\n~-\"\"MySQL\"\" version: ##{{mysqlversion}}##\n~-\"\"GeSHi\"\" version: ##{{geshiversion}}##\n~-Server:\n~~-Host: ##{{system show=\"host\"}}##\n~~-Operative System: ##{{system show=\"os\"}}##\n~~-Machine: ##{{system show=\"machine\"}}##\n\n{{wikkaconfig}}\n\n----\nCategoryWiki','(Public)','WikkaInstaller','Y','','page'),(24,'AdminUsers','2009-02-14 20:41:39','{{checkversion}}\n{{adminusers}}\n\n----\nCategoryAdmin','(Public)','WikkaInstaller','Y','','page'),(25,'AdminPages','2009-02-14 20:41:39','{{checkversion}}\n{{adminpages}}\n\n----\nCategoryAdmin','(Public)','WikkaInstaller','Y','','page'),(26,'DatabaseInfo','2009-02-14 20:41:39','{{dbinfo}}\n\n----\nCategoryAdmin','(Public)','WikkaInstaller','Y','','page'),(27,'HighScores','2009-02-14 20:41:39','{{highscores}}\n\n----\nCategoryWiki','(Public)','WikkaInstaller','Y','','page');
UNLOCK TABLES;
/*!40000 ALTER TABLE `wikka_pages` ENABLE KEYS */;

--
-- Table structure for table `wikka_referrer_blacklist`
--

DROP TABLE IF EXISTS `wikka_referrer_blacklist`;
CREATE TABLE `wikka_referrer_blacklist` (
  `spammer` varchar(150) NOT NULL default '',
  KEY `idx_spammer` (`spammer`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `wikka_referrer_blacklist`
--


/*!40000 ALTER TABLE `wikka_referrer_blacklist` DISABLE KEYS */;
LOCK TABLES `wikka_referrer_blacklist` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `wikka_referrer_blacklist` ENABLE KEYS */;

--
-- Table structure for table `wikka_referrers`
--

DROP TABLE IF EXISTS `wikka_referrers`;
CREATE TABLE `wikka_referrers` (
  `page_tag` varchar(75) NOT NULL default '',
  `referrer` varchar(150) NOT NULL default '',
  `time` datetime NOT NULL default '0000-00-00 00:00:00',
  KEY `idx_page_tag` (`page_tag`),
  KEY `idx_time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `wikka_referrers`
--


/*!40000 ALTER TABLE `wikka_referrers` DISABLE KEYS */;
LOCK TABLES `wikka_referrers` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `wikka_referrers` ENABLE KEYS */;

--
-- Table structure for table `wikka_sessions`
--

DROP TABLE IF EXISTS `wikka_sessions`;
CREATE TABLE `wikka_sessions` (
  `sessionid` varchar(32) NOT NULL default '',
  `userid` varchar(75) NOT NULL default '',
  `session_start` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`sessionid`,`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `wikka_sessions`
--


/*!40000 ALTER TABLE `wikka_sessions` DISABLE KEYS */;
LOCK TABLES `wikka_sessions` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `wikka_sessions` ENABLE KEYS */;

--
-- Table structure for table `wikka_users`
--

DROP TABLE IF EXISTS `wikka_users`;
CREATE TABLE `wikka_users` (
  `name` varchar(75) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `email` varchar(50) NOT NULL default '',
  `revisioncount` int(10) unsigned NOT NULL default '20',
  `changescount` int(10) unsigned NOT NULL default '50',
  `doubleclickedit` enum('Y','N') NOT NULL default 'Y',
  `signuptime` datetime NOT NULL default '0000-00-00 00:00:00',
  `show_comments` enum('Y','N') NOT NULL default 'N',
  `status` enum('invited','signed-up','pending','active','suspended','banned','deleted') default NULL,
  PRIMARY KEY  (`name`),
  KEY `idx_signuptime` (`signuptime`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `wikka_users`
--


/*!40000 ALTER TABLE `wikka_users` DISABLE KEYS */;
LOCK TABLES `wikka_users` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `wikka_users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

