===== Wikka Menulets =====
{{lastedit show="3"}}

In the trac as [[Ticket:28]].
 
As part of my proposal for a general overhaul of the [[WikkaMenus | menu management system]] in WikkaWiki, I suggest we replace the current system-generated items that occur in the main menu and footer menu with a series of **menulets**, i.e. mini-actions generating content for menu items.
Any of these mini-actions can of course be used in the page body as well.

Here's a list of potential menulets and their output, that I've uploaded to this server. 
Suggestions/modifications are welcome. 

----
==##""{{acls}}""##== 

Prints a link  to ##""CurrentPage/acls""## if the user has access to the ACLs: {{acls}}

%%(php)
<?php
if ($this->page){
	if ($owner = $this->GetPageOwner()){
		if ($owner == "(Public)"){
			print $this->IsAdmin() ? "<a href=\"".$this->href("acls")."\">(Edit ACLs)</a>\n" : ""; #i18n
		} elseif ($this->UserIsOwner()){
			print "<a href=\"".$this->href("acls")."\">Edit ACLs</a>\n"; #i18n
		} 
	} else {
		print ($this->GetUser()) ? " (<a href=\"".$this->href("claim")."\">Take Ownership</a>)\n" : ""; #i18n
	}
}
?>
%%---
~&My version: %%(php)<?php
// UI defines (i18n)
define('ACLS_OWNER', "Edit ACLs");		# ACLs editable by owner (includes admin)
define('ACLS_ADMIN', "(Edit ACLs)");		# public page - ACLs editable by admin only
define('ACLS_NONE', "(Take Ownership)");	# not-owned pages can be claimed
// generate output (or not)
if ($this->page)
{
	switch (TRUE)
	{
		case ($this->UserIsOwner()):
			echo '<a href="'.$this->Href('acls').'">'.ACLS_OWNER.'</a>'."\n";
			break;
		case ('(Public)' == $this->page['owner']):
			if ($this->IsAdmin()) echo '<a href="'.$this->Href('acls').'">'.ACLS_ADMIN.'</a>'."\n";
			break;
		case ('' == $this->page['owner']):
			if ($this->GetUser()) echo '<a href="'.$this->Href('claim').'">'.ACLS_NONE.'</a>'."\n";
			break;
		default:
			// print nothing
	}
}
?>%% --- A few notes about the differences:
~~-UI strings are grouped together at the top: easier maintainance; comments to give hints to translators;
~~-when $this->page is set, all data of the page is known; no need to use ""GetPageOwner()"" which would do another page load (or at least access the cache again);
~~-using a switch construct gives equal weight to the different cases, and makes it easily extensible for possible other types of ownership (groups? projects? aliens?);
~~-using single quotes wherever possible for speed (but always double quotes for strings to be internationalized);
~~-consistent spelling: the method is called Href() so we don't call it as href().
~&**Code untested - Please test!**--- --JavaWoman

----
==##""{{attachments}}""##== 

Prints a link to the FilesHandler differently depending if there are attached documents to the current page or not: {{attachments}}.

%%(php)
<?php
	// this is a menulet action relying on the files handler
	// upload path
	$upload_path = $this->GetConfigValue('upload_path').'/'.$this->GetPageTag();
	$AttachmentClass = "";
		if(is_dir($upload_path) ){
			$handle = opendir($upload_path);
			while( (gettype( $name = readdir($handle)) != "boolean")){
				$name_array[] = $name;
			}
			foreach($name_array as $temp) $folder_content .= $temp;
			closedir($handle);
			if($folder_content == "...") {
				$AttachmentClass ="emptyfolder"; // the upload path is empty
			} else {
				$AttachmentClass = "fullfolder"; // the upload path contains attachments
			}
		}
		else $AttachmentClass = "inexistingfolder"; // the upload path does not exist
		
	echo  "<a href=\"".$this->href("files")."\" title=\"Click to manage attachments\" class=\"".$AttachmentClass."\">Attachments</a>\n"; #i18n
?>
%%

Add something like this to the .css file:
%%(css)
.fullfolder { 
	font-weight: bold;
	border: 1px solid red;
}
.emptyfolder {
}
.inexistingfolder {
}
%%

----
==##""{{contact}}""##== 

Prints a ##mailto:## link to the Wikka administrator's address: {{contact}}
__Note__: plain mailto links are a common source of spam.

%%(php)
<?php
/**
 * Print a spam-safe mailto: link to the administrator's email address. 
 * 
 * Note: plain mailto links are a common source of spam.
 */
$email = $this->GetConfigValue("admin_email");

// print spam-safe mailto link
$patterns = array("'@'", "'\.'");
$replace = array("[at]", "[dot]"); 
echo "<a href=\"mailto:".preg_replace($patterns, $replace, $email)."\" title=\"Send us your feedback\">Contact</a>"; #i18n

// print plain mailto link
//echo "<a href=\"mailto:".$email."\" title=\"Send us your feedback\">Contact</a>"; #i18n

// print contact link only to registered users
// echo ($this->GetUser()) ? "<a href=\"mailto:".$email."\" title=\"Send us your feedback\">Contact</a>" : ""; #i18n

?>
%%

----
==##""{{countcomments}}""##== 

Prints the total number of comments: {{countcomments}}

%%(php)
<?php
/**
 * Print total number of comments in this wiki.
 */
$commentsdata = $this->LoadSingle("SELECT count(*) as num FROM ".$this->GetConfigValue('table_prefix')."comments");
echo $commentsdata["num"];
?>
%%

Compact version using the ##[[WikkaCountingRecords | getCount()]]## method:

%%(php)
<?php
/**
 * Print total number of comments in this wiki.
 */
echo $this->getCount('comments');
?>
%%

----
==##""{{countowned}}""##== 

Prints the number of pages owned by the current user: {{countowned}}

%%(php)
<?php
/**
 * Print number of pages owned by the current user.
 */
$str = 'SELECT COUNT(*) FROM '.$this->GetConfigValue('table_prefix').'pages WHERE `owner` ';
$str .= "= '" . $this->GetUserName() . "' AND `latest` = 'Y'";
$countquery = $this->Query($str);
$count  = mysql_result($countquery, 0);
echo $this->Link('MyPages', '', $count,'','','Display a list of the pages you currently own');
?>
%%

Compact version using the ##[[WikkaCountingRecords | getCount()]]## method:

%%(php)
<?php
/**
 * Print number of pages owned by the current user.
 */
$where = "`owner` = '".$this->GetUserName()."' AND `latest` = 'Y'";
$count = $this->getCount('pages', $where);
echo $this->Link('MyPages', '', $count,'','','Display a list of the pages you currently own');
?>
%%


----
==##""{{countpages}}""##== 

Prints the total number of pages: {{countpages}}

%%(php)
<?php
/** 
 * Print the total number of pages in this wiki.
 */
$pagedata = $this->LoadSingle("SELECT count(*) as num FROM ".$this->GetConfigValue('table_prefix')."pages WHERE latest = 'Y'");
echo $this->Link('PageIndex', '', $pagedata['num'],'','','Display an alphabetical page index');
?>
%%

Compact version using the ##[[WikkaCountingRecords | getCount()]]## method:

%%(php)
<?php
/**
 * Print the total number of pages in this wiki.
 */
$where = "`latest` = 'Y'";
$count = $this->getCount('pages', $where);
echo $this->Link('PageIndex', '', $count,'','','Display an alphabetical page index');
?>
%%

----
==##""{{countreferrers}}""##== 

Prints the total number of referrers to the wiki: {{countreferrers}}

Compact version using the ##[[WikkaCountingRecords | getCount()]]## method:

%%(php)
<?php
/**
 * Print number of referrers to this site.
 */
echo $this->getCount('referrers');
?>
%%

----
==##""{{countusers}}""##== 

Prints the number of registered users: {{countusers}}

%%(php)
<?php
/**
 * Print number of registered users.
 */ 
$userdata = $this->LoadSingle("SELECT count(*) as num FROM ".$this->GetConfigValue('table_prefix')."users ");
echo $userdata["num"];
?>
%%

Compact version using the ##[[WikkaCountingRecords | getCount()]]## method:

%%(php)
<?php
/**
 * Print number of registered users.
 */
echo $this->getCount('users');
?>
%%


----
==##""{{delete}}""##== 

Prints a link  to ##""CurrentPage/delete""## if the user has delete privileges: {{delete}}

%%(php)
<?php
echo  ($this->IsAdmin())? "<a href=\"".$this->href("delete")."\" title=\"Click to delete this page\">Delete this page</a>\n" : "";  #i18n
?>
%%

----
==##""{{edit}}""##== 

Prints a link to ##""CurrentPage/edit""##: {{edit}}

%%(php)
<?php
if ($this->HasAccess("write")) {
		echo  "<a href=\"".$this->href("edit")."\" title=\"Click to edit this page\">Edit this page</a>\n"; #i18n
} else {
		echo  "<a href=\"".$this->href("showcode")."\" title=\"Click to display the page source\">Show code</a>\n"; #i18n
}
?>
%%

----
==##""{{history}}""##==

Prints a link to ##""CurrentPage/history""##: {{history}}

%%(php)
<?php
		echo "<a href=\"".$this->href("history")."\" title=\"Click to view recent edits to this page\">Page History</a>\n"; #i18n
?>
%%

----
==##""{{homepage}}""##==

Prints a link to the wiki homepage specified in the config file: {{homepage}}

%%(php)
<?php
echo $this->Link($this->GetConfigValue('root_page'));
?>
%%

----
==##""{{lasteditauthor}}""##==

Prints the author of the last page version: {{lasteditauthor}}

%%(php)
<?php
$page = $this->LoadSingle("SELECT * FROM ".$this->GetConfigValue('table_prefix')."pages WHERE tag='".$this->GetPageTag()."' AND latest = 'Y'");
$user = ($this->LoadUser($page["user"]))? $this->Link($page["user"]) : "anonymous";
echo $user;
?>
%%

----
==##""{{linkcount}}""##==

Count (and display) the number of Wikka-formatted links (##""[[http:...]]""##) in a page. Needs some optimization work.

**actions/linkcount.php**
%%(php)
<?php
/**
 * Link count action
 *
 * Maintain a count of links on a page (could be extended to track
 * other entities)
 * 
 * Usage:
 * {{linkcount preformatted="0"}}
 * ...
 * {{linkcount start="1"}}
 * link1
 * link2
 * ...
 * linkn
 * {{linkcount stop="1"}}
 *
 * Optionally: {{linkcount preformatted="1"
 *						  pretext="Some pretext"
 *						  posttext="Some posttext"}}
 *
 * where preformatted = "1" produces "+++Link count: 9999 link(s)+++"
 *	   pretext is the text to precede the link count (overrides
 *	   preformatted)
 *	   posttest is the text to follow the link count (overrides
 *	   preformatted)
 *
 * TODO: Performance is miserable beyond a couple hundred links, so
 * the next step would be to optimize it.
 *
 *
 * @author		{@link http://wikkawiki.org/BrianKoontz Brian Koontz} 
 * @copyright	Copyright (c) 2006, Brian Koontz <brian@pongonova.net>
 * @name		LinkcountAction 
 * @package		Actions
 * @license		http://www.gnu.org/copyleft/gpl.html
 * @since		Wikka 1.1.7
 * @uses		/actions/linkcount.php
 * @version		$Id: linkcount.php,v 1.1.1.1 2006/10/07 16:30:42 brian Exp brian $
 *
 */

// Necessary to prevent "already defined" errors when the Wikka parser
// comes across a "start" or "stop" action tag
include_once("actions/linkcount.inc.php");

if(isset($vars['start']) || isset($vars['stop'])) {
	return;
}

$preformatted = $vars['preformatted'];
$pretext = $vars['pretext'];
$posttext = $vars['posttext'];

if( isset($pretext) || isset($posttext)) {
	$pretext ? 1 : $pretext = '';
	$posttext ? 1 : $posttext = '';
} else if(isset($preformatted)) {
	$pretext = "+++Link count: ";
	$posttext = " link(s)+++";
}

$body = explode("\n", $this->page['body']);
parse($body);

?>
%%

**actions/linkcount.inc.php**
%%(php)
<?php
/**
 * Link count action
 *
 * Maintain a count of links on a page (could be extended to track
 * other entities)
 * 
 * @see LinkcountAction
 * @see actions/linkcount.php
 *
 *
 * @author		{@link http://wikkawiki.org/BrianKoontz Brian Koontz}
 * @copyright	Copyright (c) 2006, Brian Koontz <brian@pongonova.net>
 * @name		LinkcountAction 
 * @package		Actions
 * @license		http://www.gnu.org/copyleft/gpl.html
 * @since		Wikka 1.1.7
 * @uses		/actions/linkcount.inc.php
 * @version		$Id: linkcount.inc.php,v 1.1.1.1 2006/10/07 16:32:17 brian Exp brian $
 *
 */
function parse(&$body) {
	$linkcount = 0;
	if(!$body) {
		return;
	}
	$state = null; #null, start
	foreach($body as $line) {
		switch($state) {
			case null:
				if(preg_match('/\{\{linkcount[\s]+start.*\}\}/', $line)) {
					$state = 'start';
				}
				break;
			case 'start':
				if (preg_match('/\{\{linkcount[\s]+stop.*\}\}/', $line)) {
					$state = null; 
				} elseif (preg_match('/\[\[http:.*\]\]/', $line)) {
					$linkcount++;
				}
				break;
		}
	}
	echo $pretext.$linkcount.$posttext;
}
?>
%%

----
==##""{{lasteditnotes}}""##==

Prints the last edit notes: {{lasteditnotes}}

%%(php)
<?php
$page = $this->LoadSingle("SELECT * FROM ".$this->GetConfigValue('table_prefix')."pages WHERE tag='".$this->GetPageTag()."' AND latest = 'Y'");
echo ($page["note"])? $this->Format("//".$page["note"]."//") : "";
?>
%%

----
==##""{{logo}}""##==

Displays a wikka logo set by admins (in the future the path should be set in the config file): 
{{logo}}

%%(php)
<?php
echo $this->Format('{{image alt="logo" title="'.$this->GetWakkaName().'" url="images/wizard.gif" link="'.$this->GetConfigValue('root_page').'"}}');
?>
%%

My version (which needs the config entry: "wiki_logo => "yourlogo.jpg" [must be on the same server]): 
%%(php)
<?php
//UI defines (i18n)
define('LOGO_ALT_TEXT', "logo");    # alt-text for the logo image

//print the image
if ($this->GetConfigValue('wiki_logo') && file_exists($this->GetConfigValue('wiki_logo'))) echo $this->Format('{{image alt="LOGO_ALT_TEXT" title="'.$this->GetWakkaName().'" url="'.$this->GetConfigValue('wiki_logo').'" link="'.$this->GetConfigValue('root_page').'"}}');
else echo $this->Format('{{image alt="LOGO_ALT_TEXT" title="'.$this->GetWakkaName().'" url="images/wizard.gif" link="'.$this->GetConfigValue('root_page').'"}}');
?>
%%
--NilsLindenberg

----
==##""{{mysqlversion}}""##== 

Displays the current MySQL version: {{mysqlversion}}

%%(php)
<?php
/**
 * Print current MySQL version.
 */
echo mysql_get_server_info();
?>
%%

----
==##""{{owner}}""##== 

Displays page ownership infos: {{owner}}

%%(php)
<?php
if ($this->page) {
	if ($owner = $this->GetPageOwner()){
		if ($owner == "(Public)"){
			print "Public page"; #i18n
		}
		elseif ($this->UserIsOwner()) {
			if ($this->IsAdmin()) {
				print "Owner: ".$this->Link($owner, "", "", 0)."\n"; #i18n
				} else {
				print"You own this page.\n"; #i18n
			}
		} else {
			print "Owner: ".$this->Link($owner, "", "", 0)."\n"; #i18n
		}
	} else {
		print "Nobody\n"; #i18n
	}
}
?>
%%---
~&My version: %%(php)<?php
//UI defines (i18n)
define('OWNER_CURRENT', "You own this page");	# current user owns the page
define('OWNER_PUBLIC', "Public page");		# page is public
define('OWNER_NONE', "Nobody");			# no page owner
define('OWNER_OTHER', "Owner: %1");		# different owner

// generate output (or not)
if ($this->page)
{
	switch (TRUE)
	{
		case ($this->UserIsOwner() && !$this->IsAdmin()):
			echo OWNER_CURRENT."\n";
			break;
		case ('(Public)' == $this->page['owner']):
			echo OWNER_PUBLIC."\n";
			break;
		case ('' == $this->page['owner']):
			echo OWNER_NONE."\n";
			break;
		case ($this->IsAdmin()):
		case ('' != $this->page['owner']):
			echo sprintf(OWNER_OTHER, $this->Link($owner, '', '', 0))."\n";
			break;
		default:
			// just in case
	}
}
?>%% --- Note: this has a similar approach and structure to my version of ##""{{acls}}""## above; see my notes there. The order of the cases is important!
~&**Code untested - Please test!**--- --JavaWoman

----
==##""{{phpversion}}""##== 

Displays the current php version: {{phpversion}}

%%(php)
<?php
/**
 * Print current PHP version.
 */
echo phpversion();
?>
%%

----
==##""{{recentchangesrss}}""##== 

Prints a link (RSS feed for recent changes) to ##""CurrentPage/recentchanges.xml""##: {{recentchangesrss}}

%%(php)
<?php
echo "<a href=\"".$this->href("recentchanges.xml")."\" title=\"Click to view recent changes in XML format.\"><img src=\"images/xml.png\" width=\"36\" height=\"14\" align=\"middle\" style=\"border : 0px;\" alt=\"XML\" /></a>"; #i18n
?>
%%

----
==##""{{referrers}}""##== 

Prints a link  to ##""CurrentPage/referrers""##: {{referrers}}

%%(php)
<?php 
if ($this->GetUser()) {
	echo "<a href='".$this->href("referrers")."' title='Click to view a list of URLs referring to this page.'>Referrers</a>\n";  #i18n
}
?> 
%%

----
==##""{{revisions}}""##==

Prints a link  to ##""CurrentPage/revisions""##: {{revisions}}

%%(php)
<?php
if ($this->GetPageTime()) {
		echo "<a href=\"".$this->href("revisions")."\" title=\"Click to view recent revisions list for this page\">".$this->GetPageTime()."</a>\n"; #i18n
}
?>
%%

----
==##""{{revisionsrss}}""##== 

Prints a link (RSS feed for page revisions) to ##""CurrentPage/revisions.xml""##: {{revisionsrss}}

%%(php)
<?php
if ($this->GetPageTime()) {
	echo "<a href=\"".$this->href("revisions.xml")."\" title=\"Click to view recent page revisions in XML format.\"><img src=\"images/xml.png\" width=\"36\" height=\"14\" align=\"middle\" style=\"border : 0px;\" alt=\"XML\" /></a>"; #i18n	
}
?>
%%

----
==##""{{search}}""##== 

Prints a searchbox: {{search}}

%%(php)
<?php
echo $this->FormOpen("", "TextSearch", "get"); 
echo 'Search: <input name="phrase" size="15" class="searchbox" />'; #i18n
echo $this->FormClose();
?>
%%

----
==##""{{skin}}""##== 

~& Needs to be updated for new ##templates/## directory structure

Displays a clickable link to the current skin: ""{{skin}}""

%%(php)
<?php
#$skin = ($this->GetCookie("wikiskin"))? $this->GetCookie("wikiskin") : $this->GetConfigValue("stylesheet");
$defaultskin = $this->GetConfigValue('stylesheet');
$skin = (!$this->GetCookie('wikiskin')) ? $defaultskin : $this->GetCookie('wikiskin'); # JW 2005-07-08 FIX possibly undefined cookie
echo '<a href="'.WIKKA_BASE_URL.'css/'.$skin.'" title="Display stylesheet">'.$skin.'</a>';
?>
%%---
~&Updated version - see comments on MySkin --JavaWoman

----
==##""{{time}}""##==

Prints the current time: {{time}}

%%(php)
<?php
echo date("H:i");
?>
%%

~& I created a similar action, but with an offset.  It can be found on the TimeWithOffset page. --JasonHuebel

----
==##""{{title}}""##==

Prints the title of the current page: {{title}}

%%(php)
<?php
echo $this->PageTitle();
?>
%%

----
==##""{{today}}""##==

Prints the current date: {{today}}

%%(php)
<?php
echo date("F j, Y");
?>
%%

----
==##""{{url}}""##==

Prints the complete URL of the current page: {{url}}

%%(php)
<?php
echo $this->href("", $this->GetPageTag());

//clickable link
//echo $this->Link($this->href("", $this->GetPageTag()));
?>
%%


----
==##""{{who}}""##==

Prints the name of the current user: {{who}}

%%(php)
<?php
if ($this->GetUser()) {
		echo "You are ".$this->Format($this->GetUserName()); #i18n
}
?>
%%

~&Here's my take (untested!): %%(php)<?php
// define output format (i18n)
define('WHO_OUT', "You are %s");    # %s is either user name or remote address
// get name or remote address
if ($this->GetUser())
{
	$who = $this->Format($this->GetUserName());
}
else
{
	if (isset($_SERVER['REMOTE_HOST']))
	{
		 $who = $_SERVER['REMOTE_HOST'];
	}
	else
	{
		 $who = $_SERVER['REMOTE_ADDR'];
	}
}
// display output
echo str_replace(' ','&nbsp;',sprintf(WHO_OUT, $who));
?>%% --- By putting the whole **phrase** in a define, we are taking care of differences in word order in different languages; at the output stage we replace spaces by no-breaking spaces to keep the whole string together. This also takes care of usage of ##""{{who}}""## in the main_menu (displaying address instead of user name). --JavaWoman
~~&Nice way of doing it JavaWoman.%%(php)<?php
// define output format (i18n)
define('WHO_OUT', "You are %s");    # %s is either user name or remote address
// get name or remote address
$who = ($this->GetUser()) ? $this->Format($this->GetUserName()) : ( (isset($_SERVER['remote_host'])) ? $_SERVER['remote_host'] : $_SERVER['remote_addr'] ) ;
// display output
echo str_replace( ' ', '&nbsp;', sprintf(WHO_OUT, $who));
?>%%---Here is a smaller take on the same code... -- GeorgePetsagourakis
~~~&Ya, it's smaller - but not faster. In fact, it's exactly the same thing, just harder to read. I just happen to like readable code. I wrote it like that for a reason: it's faster to maintain. ;-) --JavaWoman
~~~~&It is probably cause I never had the chance to use the (condition)? smth : oth_th ; syntax and I am so excited about it .. :D --GeorgePetsagourakis
~~~~~&It is indeed a very handy syntax and I do use it - but IMO usage beyond a //single// if-then-else level should be avoided: that's where it gets hard to parse for humans although PHP has no problem with it. ;-) --JavaWoman

----
==##""{{wikkaname}}""##==

Prints the name of the current Wikka: {{wikkaname}}

%%(php)
<?php
echo $this->GetWakkaName();
?>
%%

----
==##""{{wikkaversion}}""##== 

Prints the current wikka version: {{wikkaversion}}

%%(php)
<?php
/**
 * Print current Wikka version.
 */
echo $this->VERSION;
?>
%%

...

~&DarTar, can you post your code for these actions here? The way some of the menu items are formatted now should be improved - but if this little project moves ahead (I hope) we might as well do it in the "menulets" action code instead of the current code. And new code should be ready for i18n as well (I don't know if yours is...) --JavaWoman

----
==##""{{system}}""##==

Prints system information about the system Wikka is running on:
""{{system show="os"}}"" - prints the operating system information
""{{system show="machine"}}"" - prints information about the machine
""{{system show="host"}}"" - prints information about the host
""{{system}}"" - prints all of the above


%%(php)<?php
/**
 * Prints information about the system Wikka is running on.
 *
 * Syntax:
 *	{{system [show="OS|machine|host"]}}
 *
 * @package		Actions
 * @subpackage	SysInfo
 * @name		Systedm
 *
 * @author		{@link http://wikka.jsnx.com/JavaWoman JavaWoman}
 * @copyright	Copyright (c) 2005, Marjolein Katsma
 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @since		Wikka 1.1.6.0 ???
 *
 * @input		string	$show	optional: what type of system info to show (OS, machine or host);
 *				default: empty, shows all information
 */

// default
$show = '';

// get param and validation
$valid_show = array('os','machine','host');
if (is_array($vars))
{
	foreach ($vars as $param => $value)
	{
		switch ($param)
		{
			case 'show':
				if (in_array($value, $valid_show)) $show = strtolower($value);
				break;
		}
	}
}

// get data
$host		= php_uname('n');
$os			= php_uname('s');
$release	= php_uname('r');
$version	= php_uname('v');
$machine	= php_uname('m');

// build output
$out = '';
switch ($show)
{
	case '':
		if (isset($os)) $out .= $os.' ';
		if (isset($release)) $out .= $release.' ';
		if (isset($version)) $out .= $version.' ';
		if (isset($machine)) $out .= $machine.' ';
		if (isset($host)) $out .= '('.$host.')';
		break;
	case 'os':
		if (isset($os)) $out .= $os.' ';
		if (isset($release)) $out .= $release.' ';
		if (isset($version)) $out .= $version.' ';
		break;
	case 'machine':
		if (isset($machine)) $out .= $machine.' ';
		break;
	case 'host':
		if (isset($host)) $out .= $host.' ';
		break;
}

// show result
echo trim($out);
?>
%%

Installed as a beta feature on this site.

--JavaWoman

----
==##""{{randompage}}""##==//like the "Random Article" on wikipedia...//
~ ##""{{randompage}}""##
~~ Prints a link to a random page on this wiki. e.g. [[HomePage | RandomPage]]
~ ##""{{randompage title="Random"}}""##
~~ Change the title, use ##""{{randompage title=&#34;&#34;}}""## to display the real pagename.
~ ##""{{randompage pos="ActionInfo, HandlerInfo, FunctionInfo"}}""##
~~ Make a positive list to choose from, don't use all pages. (note the default neg list)
~ ##""{{randompage neg="HomePage, PageIndex"}}""##
~~ Use all pages exept the ones used in this negative list / override the default neg list in $neg_list_default
~ ##""{{randompage title="Random Link" pos="ActionInfo, HandlerInfo, FunctionInfo" neg=&#34;&#34;}}""##
~~ Clear the default negative list, use only pages from the pos list and use "Random Link" as title.
~ ##""{{randompage pos="HomePage|DonateNow|DonateNow|DonateNow"}}""##
~~ You can influence the random ratio... (+other way of defining the list)

~& **Original idea**
~& %%(php)<?php
$all = $this->LoadAll("select distinct tag from ".$this->GetConfigValue('table_prefix')."pages");
print $this->Link( $all[array_rand($all)]['tag'], '', 'RandomPage', FALSE, TRUE, 'A random page on this site' );
?>
%%

%%(php)<?php
/**
 * Prints a link to a random page.
 *
 * syntax:
 *      {{randompage [title="string"] [pos="PageName, PageName2"] [neg="PageName3, PageName4"]}}
 *
 * @package     Actions
 * @subpackage  Menulets
 * @name        RandomPage
 *
 * @author      {@link http://wikkawiki.org/OnegWR OnegWR}
 * @copyright   Copyright (c) 2006, OnegWR
 * @license     http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @since       Wikka 1.1.6.0
 *
 * @input       string  $title  optional: alternative for "RandomPage"
 *                              if set to "" the real pagename is used
 * @input       string  $pos    optional: comma separated list of pages
 *                              only pages out of this list will be chosen
 *                              default: all pages
 * @input       string  $neg    optional: comma separated list of pages
 *                              pages in this list will not be used
 *                              exeption: if the errorpage is in the list
 *                              default: defined by $neg_list_default array
 * If no match could be found, HomePage/config["root_page"] is returned.
 * @ToDo        Get list of pages from the tagCache of the ExistsPage function by IanAndolina
 */

$errorpage = $this->GetConfigValue('root_page');
$neg_list_default = array("HomePage","UserSettings","TextSearch","TextSearchExpanded","PageIndex");
$title = isset($vars['title']) ? $this->htmlspecialchars_ent($vars['title']) : "RandomPage"; //i18n

foreach( $this->LoadAll("select distinct tag from ".$this->GetConfigValue('table_prefix')."pages") as $key => $val ){
		$all[]=$val['tag'];
}
$pos_list = isset($vars['pos']) ? split('[|,]', preg_replace( "/[\ ]/", '', $vars['pos'] ) ) : $all ;
$neg_list = isset($vars['neg']) ? split('[|,]', preg_replace( "/[\ ]/", '', $vars['neg'] ) ) : $neg_list_default ;

$try = 0;
while ( $try < 5 ) {
		$try++;
		$page = $pos_list[array_rand($pos_list)];
		if( !in_array($page, $all) ) continue;
		if( in_array($page, $neg_list) ) continue;
		break;
}
if( $try > 4 ) $page = $errorpage;
if( $title=='' ) $title = $page;
print $this->Link( $page, '', $title, FALSE, TRUE, "$page, a random page on this site" ); //i18n
?>%%
-- OnegWR

----
==##""{{contributors}}""##==
~ Prints a space separated list of all users that have edited the current page, the most active user first.
~ A bit like ##""{{lasteditauthor}}""##, but with all editing authors...
%%(php)<?php
/**
 * Shows the contributors of this page, most active user first.
 *
 * syntax:      {{contributors}}
 *
 * @package     Actions
 * @subpackage  Menulets
 * @name        Contributors
 * @author      {@link http://wikkawiki.org/OnegWR OnegWR}
 * @copyright   Copyright (c) 2006, OnegWR
 * @license     http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @since       Wikka 1.1.6.0
 */

$q = 'SELECT Count(*) AS cnt, `user` FROM '.$this->GetConfigValue('table_prefix').'pages '.
		'WHERE `tag`="'.$this->GetPageTag().'" GROUP BY user ORDER BY cnt DESC;';
$all = $this->LoadAll( $q );

foreach($all as $key=>$val)
{
		print $this->Link($val['user'],'',$val['user'], FALSE, TRUE, '('.$val['cnt'].')') ." \n";
}
?>%%
-- OnegWR

----
==##""{{wordcount}}""##==

Inserts a wordcount (my format, your format, or no format) into a document.  Needed something quick and dirty, so there's some refinement yet to be done.

##""+++Word count: 9999 word(s)+++""##

==actions/wordcount.php==
%%(php)
<?php
/*
 * Word count action
 * Author: Brian Koontz <brian@pongonova.net>
 * 
 * Replace instances of {{wordcount}} with the number of words in the
 * text
 *
 * Optionally: {{wordcount preformatted="1" 
 *                          pretext="Some pretext" 
 *                          posttext="Some posttext"}}
 *
 * where preformatted = "1" produces "+++Word count: 9999 word(s)+++"
 *       pretext is the text to precede the word count (overrides preformatted) 
 *       posttest is the text to follow the word count (overrides preformatted) 
 *
 * @package     Actions
 * @subpackage  Menulets
 * @name        Contributors
 * @author      {@link http://wikkawiki.org/BrianKoontz Brian Koontz}
 * @copyright   Copyright (c) 2006, Brian Koontz <brian@pongonova.net>
 * @license     http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @since       Wikka 1.1.6.2
 *
 * TODO: Probably counts a lot of things it should, and a lot of
 * things it shouldn't.  Probably shouldn't count actions or other
 * special items.
 */

$preformatted = $vars['preformatted'];
$pretext = $vars['pretext'];
$posttext = $vars['posttext'];

if( $pretext || $posttext) {
	$pretext ? 1 : $pretext = '';
	$posttext ? 1 : $posttext = '';
} else if($preformatted) {
	$pretext = "+++Word count: ";
	$posttext = " word(s)+++";
}

$wc = str_word_count($this->page['body']);
echo $pretext.$wc.$posttext;

?>
%%

----
CategoryAdmin
