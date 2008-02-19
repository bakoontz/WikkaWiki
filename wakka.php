<?php /*dotmg modifications : contact m.randimbisoa@dotmg.net*/ ?>
<?php
define("UPPER","[A-Z\xc0-\xdf\xa8]");
define("UPPERNUM","[0-9A-Z\xc0-\xdf\xa8]");
define("LOWER","[a-z\xe0-\xff\xb8\/]");
define("ALPHA","[A-Za-z\xc0-\xff\xa8\xb8\_\-\/]");
define("ALPHANUM","[0-9A-Za-z\xc0-\xff\xa8\xb8\_\-\/]");
define("ALPHANUM_P","0-9A-Za-z\xc0-\xff\xa8\xb8\_\-\/");

/*
    Yes, most of the formatting used in this file is HORRIBLY BAD STYLE. However,
    most of the action happens outside of this file, and I really wanted the code
    to look as small as what it does. Basically. Oh, I just suck. :)
*/

//error_reporting(E_ALL);
error_reporting (E_ALL ^ E_NOTICE);

// do not change this line, you fool. In fact, don't change anything! Ever!
define("WAKKA_VERSION", "1.0.1");
function getmicrotime() { 
   list($usec, $sec) = explode(" ", microtime()); 
   return ((float)$usec + (float)$sec); 
} 

$tstart = getmicrotime();

class Wakka
{
	var $dblink;
	var $page;
	var $tag;
	var $queryLog = array();
	var $interWiki = array();
	var $VERSION;

	// constructor
	function Wakka($config)
	{
		$this->config = $config;
		$this->dblink = @mysql_connect($this->config["mysql_host"], $this->config["mysql_user"], $this->config["mysql_password"]);
            if ($this->dblink) 
            { 
            	if (!@mysql_select_db($this->config["mysql_database"], $this->dblink)) 
                  { 
                  	@mysql_close($this->dblink); 
                        $this->dblink = false; 
                  } 
            }
 		$this->VERSION = WAKKA_VERSION;
	}

	// DATABASE
	function Query($query)
	{
		$start = $this->GetMicroTime();
		if (!$result = mysql_query($query, $this->dblink))
		{
			ob_end_clean();
			die("Query failed: ".$query." (".mysql_error().")");
		}
		if($this->GetConfigValue("sql_debugging"))
		{
  			$time = $this->GetMicroTime() - $start;
  			$this->queryLog[] = array(
  				"query"		=> $query,
  				"time"		=> $time);
		}
		return $result;
	}
	function LoadSingle($query) { if ($data = $this->LoadAll($query)) return $data[0]; }
	function LoadAll($query)
	{
	$data=array();
		if ($r = $this->Query($query))
		{
			while ($row = mysql_fetch_assoc($r)) $data[] = $row;
			mysql_free_result($r);
		}
		return $data;
	}


	// MISC
	function GetMicroTime() { list($usec, $sec) = explode(" ",microtime()); return ((float)$usec + (float)$sec); }
	function IncludeBuffered($filename, $notfoundText = "", $vars = "", $path = "")
	{
		if ($path) $dirs = explode(":", $path);
		else $dirs = array("");

		foreach($dirs as $dir)
		{
			if ($dir) $dir .= "/";
			$fullfilename = $dir.$filename;
			if (file_exists($fullfilename))
			{
				if (is_array($vars)) extract($vars);

				ob_start();
				include($fullfilename);
				$output = ob_get_contents();
				ob_end_clean();
				return $output;
			}
		}
		if ($notfoundText) return $notfoundText;
		else return false;
	}

	// VARIABLES
	function GetPageTag() { return $this->tag; }
	function GetPageTime() { return $this->page["time"]; }
	function GetMethod() { return $this->method; }
	function GetConfigValue($name) { return (isset($this->config[$name])) ? $this->config[$name] : null; }
	function GetWakkaName() { return $this->GetConfigValue("wakka_name"); }
	function GetWakkaVersion() { return $this->VERSION; }

	// PAGES
	function LoadPage($tag, $time = "", $cache = 1) {
		// retrieve from cache
		if (!$time && $cache && ($cachedPage = $this->GetCachedPage($tag))) $page = $cachedPage;
		// load page
		if (!isset($page)) $page = $this->LoadSingle("select * from ".$this->config["table_prefix"]."pages where tag = '".mysql_escape_string($tag)."' ".($time ? "and time = '".mysql_escape_string($time)."'" : "and latest = 'Y'")." limit 1");
		// cache result
		if (!$time) $this->CachePage($page);
		return $page;
	}
	function GetCachedPage($tag) { return (isset($this->pageCache[$tag])) ? $this->pageCache[$tag] : null; }
	function CachePage($page) { $this->pageCache[$page["tag"]] = $page; }
	function SetPage($page) { $this->page = $page; if ($this->page["tag"]) $this->tag = $this->page["tag"]; }
	function LoadPageById($id) { return $this->LoadSingle("select * from ".$this->config["table_prefix"]."pages where id = '".mysql_escape_string($id)."' limit 1"); }
	function LoadRevisions($page) { return $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where tag = '".mysql_escape_string($page)."' order by time desc"); }
	function LoadPagesLinkingTo($tag) { return $this->LoadAll("select from_tag as tag from ".$this->config["table_prefix"]."links where to_tag = '".mysql_escape_string($tag)."' order by tag"); }
	function LoadRecentlyChanged()
	{
		if ($pages = $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where latest = 'Y' and comment_on = '' order by time desc"))
		{
			foreach ($pages as $page)
			{
				$this->CachePage($page);
			}
			return $pages;
		}
	}
	function LoadWantedPages() { return $this->LoadAll("select distinct ".$this->config["table_prefix"]."links.to_tag as tag,count(".$this->config["table_prefix"]."links.from_tag) as count from ".$this->config["table_prefix"]."links left join ".$this->config["table_prefix"]."pages on ".$this->config["table_prefix"]."links.to_tag = ".$this->config["table_prefix"]."pages.tag where ".$this->config["table_prefix"]."pages.tag is NULL group by tag order by count desc"); }
	function LoadOrphanedPages() { return $this->LoadAll("select distinct tag from ".$this->config["table_prefix"]."pages left join ".$this->config["table_prefix"]."links on ".$this->config["table_prefix"]."pages.tag = ".$this->config["table_prefix"]."links.to_tag where ".$this->config["table_prefix"]."links.to_tag is NULL and ".$this->config["table_prefix"]."pages.comment_on = '' order by tag"); }
	function LoadPageTitles() { return $this->LoadAll("select distinct tag from ".$this->config["table_prefix"]."pages order by tag"); }
	function LoadAllPages() { return $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where latest = 'Y' order by tag"); }
	function FullTextSearch($phrase) { return $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where latest = 'Y' and match(tag, body) against('".mysql_escape_string($phrase)."')"); }
	function FullTextSearchAndLikeTags($phrase) { return $this->LoadAll("(select * from ".$this->config["table_prefix"]."pages where latest = 'Y' and tag like('%".mysql_escape_string($phrase)."%')) UNION (select * from ".$this->config["table_prefix"]."pages where latest = 'Y' and match(tag, body) against('".mysql_escape_string($phrase)."'))"); } 
	function FullCategoryTextSearch($phrase) { return $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where latest = 'Y' and match(tag, body) against('".mysql_escape_string($phrase)."' IN BOOLEAN MODE)"); }
	function SavePage($tag, $body, $note, $comment_on = "")
	{
		// get current user
		$user = $this->GetUserName();

		//die($tag);

		// TODO: check write privilege
		if ($this->HasAccess("write", $tag))
		{
			// is page new?
			if (!$oldPage = $this->LoadPage($tag))
			{
				// create default write acl. store empty write ACL for comments.
				$this->SaveAcl($tag, "write", ($comment_on ? "" : $this->GetConfigValue("default_write_acl")));

				// create default read acl
				$this->SaveAcl($tag, "read", $this->GetConfigValue("default_read_acl"));

				// create default comment acl.
				$this->SaveAcl($tag, "comment", $this->GetConfigValue("default_comment_acl"));

				// current user is owner; if user is logged in! otherwise, no owner.
				if ($this->GetUser()) $owner = $user;
			}
			else
			{
				// aha! page isn't new. keep owner!
				$owner = $oldPage["owner"];
			}


			// set all other revisions to old
			$this->Query("update ".$this->config["table_prefix"]."pages set latest = 'N' where tag = '".mysql_Escape_string($tag)."'");

			// add new revision
			$this->Query("insert into ".$this->config["table_prefix"]."pages set ".
				"tag = '".mysql_escape_string($tag)."', ".
				($comment_on ? "comment_on = '".mysql_escape_string($comment_on)."', " : "").
				"time = now(), ".
				"owner = '".mysql_escape_string($owner)."', ".
				"user = '".mysql_escape_string($user)."', ".
				"note = '".mysql_escape_string($note)."', ".
				"latest = 'Y', ".
				"body = '".mysql_escape_string(trim($body))."'");
				
 		}
	}

	// COOKIES
	function SetSessionCookie($name, $value) { SetCookie($name, $value, 0, "/"); $_COOKIE[$name] = $value; }
	function SetPersistentCookie($name, $value) { SetCookie($name, $value, time() + 90 * 24 * 60 * 60, "/"); $_COOKIE[$name] = $value; }
	function DeleteCookie($name) { SetCookie($name, "", 1, "/"); $_COOKIE[$name] = ""; }
	function GetCookie($name) { return $_COOKIE[$name]; }

	// HTTP/REQUEST/LINK RELATED
	function SetMessage($message) { $_SESSION["message"] = $message; }
	function GetMessage() { $message = $_SESSION["message"]; $_SESSION["message"] = ""; return $message; }
	function Redirect($url) { header("Location: $url"); exit; }
	// returns just PageName[/method].
	function MiniHref($method = "", $tag = "") { if (!$tag = trim($tag)) $tag = $this->tag; return $tag.($method ? "/".$method : ""); }
	// returns the full url to a page/method.
	function Href($method = "", $tag = "", $params = "")
	{
		$href = $this->config["base_url"].$this->MiniHref($method, $tag);
		if ($params)
		{
			$href .= ($this->config["rewrite_mode"] ? "?" : "&amp;").$params;
		}
		return $href;
	}
	function Link($tag, $method = "", $text = "", $track = 1, $escapeText = 1, $LinkSpaces = 1) {
		if (!$text) $text = $tag;

		// escape text?
		if ($escapeText)
		{
			$text = htmlspecialchars($text);
		}

		$url = '';

		// is this an interwiki link?
		if (preg_match("/^([A-Z,ÄÖÜ][A-Z,a-z,ÄÖÜ,ßäöü]+)[:]([A-Z,a-z,0-9,ÄÖÜ,ßäöü]*)$/", $tag, $matches))
		{
			$url = $this->GetInterWikiUrl($matches[1], $matches[2]);
		}
		else if (preg_match("/^(http|https|ftp):\/\/([^\\s\"<>&]+)$/", $tag))
		{
			$url = $tag; // this is a vaild external URL
		}	
		// is this a full link? ie, does it contain alpha-numeric characters?
		else if (preg_match("/[^[:alnum:],ÄÖÜ,ßäöü]/", $tag))
		{
			// check for email addresses
			if (preg_match("/^.+\@.+$/", $tag))
			{
				$url = "mailto:".$tag;
			}
			// check for protocol-less URLs
			else if (!preg_match("/:/", $tag))
			{
				$url = "http://".$tag;
			}
		}
		elseif (preg_match("/^[A-Za-z0-9]+$/", $tag))
		{
			// it's a Wakka link!
			if ($_SESSION["linktracking"] && $track) $this->TrackLinkTo($tag);
			$linkedPage = $this->LoadPage($tag);
			// return ($linkedPage ? "<a href=\"".$this->href($method, $linkedPage['tag'])."\">".$text."</a>" : "<span class=\"missingpage\">".$text."</span><a href=\"".$this->href("edit", $tag)."\" title=\"Create this page\">?</a>");
			// return ($linkedPage ? "<a href=\"".$this->href($method, $linkedPage['tag'])."\">".$text."</a>" : "<a href=\"".$this->href("edit", $tag)."\" title=\"Create this page\"><span class=\"missingpage\">".$text."</span></a>");
			return ($linkedPage ? "<a href=\"".$this->href($method, $linkedPage['tag'])."\">".($LinkSpaces ? $this->AddSpaces($text) : $text)."</a>" : "<a href=\"".$this->href("edit", $tag)."\" title=\"Create this page\"><span class=\"missingpage\">".$text."</span></a>");
		}
		$external_link_tail = $this->GetConfigValue("external_link_tail");
		return $url ? "<a class=\"ext\" href=\"$url\">$text</a>$external_link_tail" : $text;
	}

	function AddSpaces($text)
  	{
   		if ($user = $this->GetUser()) $show = $user["show_spaces"];
   		else $show = $this->GetConfigValue("show_spaces");
   		if ($show=='Y') {
     			$text = preg_replace("/(".ALPHANUM.")(".UPPERNUM.")/","\\1&nbsp;\\2",$text);
     			$text = preg_replace("/(".UPPERNUM.")(".UPPERNUM.")/","\\1&nbsp;\\2",$text);
     			$text = preg_replace("/(".ALPHANUM.")\//","\\1&nbsp;/",$text);
     			$text = preg_replace("/(".UPPER.")&nbsp;(?=".UPPER."&nbsp;".UPPERNUM.")/","\\1",$text);
     			$text = preg_replace("/(".UPPER.")&nbsp;(?=".UPPER."&nbsp;\/)/","\\1",$text);
     			$text = preg_replace("/\/(".ALPHANUM.")/","/&nbsp;\\1",$text);
     			$text = preg_replace("/(".UPPERNUM.")&nbsp;(".UPPERNUM.")($|\b)/","\\1\\2",$text);
     			$text = preg_replace("/([0-9])(".ALPHA.")/","\\1&nbsp;\\2",$text);
     			$text = preg_replace("/(".ALPHA.")([0-9])/","\\1&nbsp;\\2",$text);
     			$text = preg_replace("/([0-9])&nbsp;(?=[0-9])/","\\1",$text);
   		}
   		return $text;
  	}

	// function PregPageLink($matches) { return $this->Link($matches[1]); }
	function IsWikiName($text) { return preg_match("/^[A-Z,ÄÖÜ][a-z,ßäöü]+[A-Z,0-9,ÄÖÜ][A-Z,a-z,0-9,ÄÖÜ,ßäöü]*$/", $text); }
	function TrackLinkTo($tag) { $_SESSION["linktable"][] = $tag; }
	function GetLinkTable() { return $_SESSION["linktable"]; }
	function ClearLinkTable() { $_SESSION["linktable"] = array(); }
	function StartLinkTracking() { $_SESSION["linktracking"] = 1; }
	function StopLinkTracking() { $_SESSION["linktracking"] = 0; }
	function WriteLinkTable()
	{
		// delete old link table
		$this->Query("delete from ".$this->config["table_prefix"]."links where from_tag = '".mysql_escape_string($this->GetPageTag())."'");
		if ($linktable = $this->GetLinkTable())
		{
			$from_tag = mysql_escape_string($this->GetPageTag());
			foreach ($linktable as $to_tag)
			{
				$lower_to_tag = strtolower($to_tag);
				if (!$written[$lower_to_tag])
				{
					$this->Query("insert into ".$this->config["table_prefix"]."links set from_tag = '".$from_tag."', to_tag = '".mysql_escape_string($to_tag)."'");
					$written[$lower_to_tag] = 1;
				}
			}
		}
	}
	function Header() { return $this->Action($this->GetConfigValue("header_action"), 0); } 
	function Footer() { return $this->Action($this->GetConfigValue("footer_action"), 0); }

	// FORMS
	function FormOpen($method = "", $tag = "", $formMethod = "POST")
	{
		$result = "<form action=\"".$this->href($method, $tag)."\" method=\"".$formMethod."\">\n";
		if (!$this->config["rewrite_mode"]) $result .= "<input type=\"hidden\" name=\"wakka\" value=\"".$this->MiniHref($method, $tag)."\">\n";
		return $result;
	}
	function FormClose()
	{
		return "</form>\n";
	}

	// INTERWIKI STUFF
	function ReadInterWikiConfig()
	{
		if ($lines = file("interwiki.conf"))
		{
			foreach ($lines as $line)
			{
				if ($line = trim($line))
				{
					list($wikiName, $wikiUrl) = explode(" ", trim($line));
					$this->AddInterWiki($wikiName, $wikiUrl);
				}
			}
		}
	}
	function AddInterWiki($name, $url)
	{
		$this->interWiki[strtolower($name)] = $url;
	}
	function GetInterWikiUrl($name, $tag) {
		if (isset($this->interWiki[strtolower($name)]))
		{
			return $this->interWiki[strtolower($name)].$tag;
		} 
	}

	// REFERRERS
	function LogReferrer($tag = "", $referrer = "")
	{
		// fill values
		if (!$tag = trim($tag)) $tag = $this->GetPageTag();
		if (!$referrer = trim($referrer)) $referrer = $_SERVER["HTTP_REFERER"];

		// check if it's coming from another site
		if ($referrer && !preg_match("/^".preg_quote($this->GetConfigValue("base_url"), "/")."/", $referrer))
		{
			$this->Query("insert into ".$this->config["table_prefix"]."referrers set ".
				"page_tag = '".mysql_escape_string($tag)."', ".
				"referrer = '".mysql_escape_string($referrer)."', ".
				"time = now()");
		}
	}
	function LoadReferrers($tag = "")
	{
		return $this->LoadAll("select referrer, count(referrer) as num from ".$this->config["table_prefix"]."referrers ".($tag = trim($tag) ? "where page_tag = '".mysql_escape_string($tag)."'" : "")." group by referrer order by num desc");
	}

	// PLUGINS
	function Action($action, $forceLinkTracking = 0)
	{
		$action = trim($action);
		$vars=array();

		// stupid attributes check
		if (stristr($action, "=\""))
		{
			// extract $action and $vars_temp ("raw" attributes)
			preg_match("/^([A-Za-z0-9]*)(.*)$/", $action, $matches);
			list(, $action, $vars_temp) = $matches;

			// match all attributes (key and value)
			preg_match_all("/([A-Za-z0-9]*)=\"(.*)\"/U", $vars_temp, $matches);

			// prepare an array for extract() to work with (in $this->IncludeBuffered())
			if (is_array($matches))
			{
				for ($a = 0; $a < count($matches[0]); $a++)
				{
					$vars[$matches[1][$a]] = $matches[2][$a];
					// ?? $vars[$a] = $matches[2][$a];
				}

			}		
		}
		if (!$forceLinkTracking) $this->StopLinkTracking();
		$result = $this->IncludeBuffered(strtolower($action).".php", "<i>Unknown action \"$action\"</i>", $vars, $this->config["action_path"]);
		$this->StartLinkTracking();
		return $result;
	}
	function Method($method)
	{
		if (strstr('/', $method))
		{
			$method = substr($method, strrpos('/', $method));
		}
		if (!$handler = $this->page["handler"]) $handler = "page";
		$methodLocation = $handler."/".$method.".php";
		return $this->IncludeBuffered($methodLocation, "<i>Unknown method \"$methodLocation\"</i>", "", $this->config["handler_path"]);
	}
	function Format($text, $formatter = "wakka") { return $this->IncludeBuffered("formatters/".$formatter.".php", "<i>Formatter \"$formatter\" not found</i>", compact("text")); }

	// USERS
	function LoadUser($name, $password = 0) { return $this->LoadSingle("select * from ".$this->config["table_prefix"]."users where name = '".mysql_escape_string($name)."' ".($password === 0 ? "" : "and password = '".mysql_escape_string($password)."'")." limit 1"); }
	function LoadUsers() { return $this->LoadAll("select * from ".$this->config["table_prefix"]."users order by name"); }
	function GetUserName() { if ($user = $this->GetUser()) $name = $user["name"]; else if (!$name = gethostbyaddr($_SERVER["REMOTE_ADDR"])) $name = $_SERVER["REMOTE_ADDR"]; return $name; }
	function UserName() { /* deprecated! */ return $this->GetUserName(); }
	function GetUser() { return (isset($_SESSION["user"])) ? $_SESSION["user"] : null; }
	function SetUser($user) { $_SESSION["user"] = $user; $this->SetPersistentCookie("name", $user["name"]); $this->SetPersistentCookie("password", $user["password"]); }
	function LogoutUser() { $_SESSION["user"] = ""; $this->DeleteCookie("name"); $this->DeleteCookie("password"); }
	function UserWantsComments() { if (!$user = $this->GetUser()) return false; return ($user["show_comments"] == "Y"); }


	// COMMENTS
	function LoadComments($tag) { return $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where comment_on = '".mysql_escape_string($tag)."' and latest = 'Y' order by time"); }
	function LoadRecentComments() { return $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where comment_on != '' and latest = 'Y' order by time desc"); }
	function LoadRecentlyCommented($limit = 50)
	{
		// NOTE: this is really stupid. Maybe my SQL-Fu is too weak, but apparently there is no easier way to simply select
		//       all comment pages sorted by their first revision's (!) time. ugh!
		//#dotmg [20 lines removed, 1 line uncommented and modified] : I ignore in which versions of mySQL this works, mine is too old and it works fine! actions/recentlycommented.php modified.
		return $this->LoadAll("select comment_on as tag, max(time) as comment_time, tag as comment_tag, user as comment_user from ".$this->config["table_prefix"]."pages where comment_on != '' group by comment_on order by comment_time desc");
	}

	// ACCESS CONTROL
	// returns true if logged in user is owner of current page, or page specified in $tag
	function UserIsOwner($tag = "")
	{
		// check if user is logged in
		if (!$this->GetUser()) return false;

		// set default tag
		if (!$tag = trim($tag)) $tag = $this->GetPageTag();

		// if user is admin, return true. Admin can do anything!
        if ($this->IsAdmin()) return true;

		// check if user is owner
		if ($this->GetPageOwner($tag) == $this->GetUserName()) return true;
	}
	//returns true if user is listed in configuration list as admin
    function IsAdmin() {
        $adminstring = $this->config["admin_users"];
        $adminarray = explode(',' , $adminstring);

        foreach ($adminarray as $stritem)
        $stritem = trim($stritem);

        foreach ($adminarray as $admin) {
            if ($admin == $this->GetUserName()) {
                return true;
            }
        }
    }
	function GetPageOwner($tag = "", $time = "") { if (!$tag = trim($tag)) $tag = $this->GetPageTag(); if ($page = $this->LoadPage($tag, $time)) return $page["owner"]; }
	function SetPageOwner($tag, $user)
	{
		// check if user exists
		if( $user <> '' && ! $this->LoadUser( $user ) ) return;

		// updated latest revision with new owner
		$this->Query("update ".$this->config["table_prefix"]."pages set owner = '".mysql_escape_string($user)."' where tag = '".mysql_escape_string($tag)."' and latest = 'Y' limit 1");
	}
	function LoadAcl($tag, $privilege, $useDefaults = 1)
	{
		if ((!$acl = $this->LoadSingle("select * from ".$this->config["table_prefix"]."acls where page_tag = '".mysql_escape_string($tag)."' and privilege = '".mysql_escape_string($privilege)."' limit 1")) && $useDefaults)
		{
			$acl = array("page_tag" => $tag, "privilege" => $privilege, "list" => $this->GetConfigValue("default_".$privilege."_acl"));
		}
		return $acl;
	}
	function SaveAcl($tag, $privilege, $list) {
		if ($this->LoadAcl($tag, $privilege, 0)) $this->Query("update ".$this->config["table_prefix"]."acls set list = '".mysql_escape_string(trim(str_replace("\r", "", $list)))."' where page_tag = '".mysql_escape_string($tag)."' and privilege = '".mysql_escape_string($privilege)."' limit 1");
		else $this->Query("insert into ".$this->config["table_prefix"]."acls set list = '".mysql_escape_string(trim(str_replace("\r", "", $list)))."', page_tag = '".mysql_escape_string($tag)."', privilege = '".mysql_escape_string($privilege)."'");
	}
	// returns true if $user (defaults to current user) has access to $privilege on $page_tag (defaults to current page)
	function HasAccess($privilege, $tag = "", $user = "")
	{
		// see whether user is registered and logged in
		if ($user = $this->GetUser()) $registered = true;

		// set defaults
		if (!$tag = trim($tag)) $tag = $this->GetPageTag();
		if (!$user = $this->GetUserName());

		// load acl
		$acl = $this->LoadAcl($tag, $privilege);

		// if current user is owner, return true. owner can do anything!
		if ($this->UserIsOwner($tag)) return true;

		// fine fine... now go through acl
		foreach (explode("\n", $acl["list"]) as $line)
		{
			$line = trim($line);

			// check for inversion character "!"
			if (preg_match("/^[!](.*)$/", $line, $matches))
			{
				$negate = 1;
				$line = $matches[1];
			}
			else
			{
				$negate = 0;
			}

			// if there's still anything left... lines with just a "!" don't count!
			if ($line)
			{
				switch ($line[0])
				{
				// comments
				case "#":
					break;
				// everyone
				case "*":
					return !$negate;
				// only registered users
				case "+":
					return ($registered) ? !$negate : false;
				// aha! a user entry.
				default:
					if ($line == $user)
					{
						return !$negate;
					}
				}
			}
		}

		// tough luck.
		return false;
	}

	// MAINTENANCE
	function Maintenance()
	{
		// purge referrers
		if ($days = $this->GetConfigValue("referrers_purge_time")) {
			$this->Query("delete from ".$this->config["table_prefix"]."referrers where time < date_sub(now(), interval '".mysql_escape_string($days)."' day)");
		}

		// purge old page revisions
		if ($days = $this->GetConfigValue("pages_purge_time")) {
			$this->Query("delete from ".$this->config["table_prefix"]."pages where time < date_sub(now(), interval '".mysql_escape_string($days)."' day) and latest = 'N'");
		}
	}

	// THE BIG EVIL NASTY ONE!
	function Run($tag, $method = "")
	{
		if(!($this->GetMicroTime()%3)) $this->Maintenance(); 

		$this->ReadInterWikiConfig();

		// do our stuff!
		if (!$this->method = trim($method)) $this->method = "show";
		if (!$this->tag = trim($tag)) $this->Redirect($this->href("", $this->config["root_page"]));
		if ((!$this->GetUser() && isset($_COOKIE["name"])) && ($user = $this->LoadUser($_COOKIE["name"], $_COOKIE["password"]))) $this->SetUser($user);
		$this->SetPage($this->LoadPage($tag, (isset($_REQUEST["time"]) ? $_REQUEST["time"] :'')));
		$this->LogReferrer();

		if (preg_match('/\.xml$/', $this->method))
		{
			print($this->Method($this->method));
		}
		elseif (preg_match('/\.(gif|jpg|png)$/', $this->method))
		{
			header('Location: images/' . $this->method);
		}
		elseif (preg_match('/\.css$/', $this->method))
		{
			header('Location: css/' . $this->method);
		}
		else
		{
			print($this->Header().$this->Method($this->method).$this->Footer());
		}
	}
}

// stupid version check
if (!isset($_REQUEST)) die('$_REQUEST[] not found. Wakka requires PHP 4.1.0 or higher!');

// workaround for the amazingly annoying magic quotes.
function magicQuotesSuck(&$a)
{
	if (is_array($a))
	{
		foreach ($a as $k => $v)
		{
			if (is_array($v))
				magicQuotesSuck($a[$k]);
			else
				$a[$k] = stripslashes($v);
		}
	}
}
set_magic_quotes_runtime(0);
if (get_magic_quotes_gpc())
{
	magicQuotesSuck($_POST);
	magicQuotesSuck($_GET);
	magicQuotesSuck($_COOKIE);
}


// default configuration values
$wakkaDefaultConfig = array(
	"mysql_host"			=> "localhost",
	"mysql_database"			=> "wikka",
	"mysql_user"			=> "wikka",
	"table_prefix"			=> "wikka_",

	"root_page"				=> "HomePage",
	"wakka_name"			=> "MyWikkaSite",
	"base_url"				=> "http://".$_SERVER["SERVER_NAME"].($_SERVER["SERVER_PORT"] != 80 ? ":".$_SERVER["SERVER_PORT"] : "").$_SERVER["REQUEST_URI"].(preg_match("/".preg_quote("wakka.php")."$/", $_SERVER["REQUEST_URI"]) ? "?wakka=" : ""),
	"rewrite_mode"			=> (preg_match("/".preg_quote("wakka.php")."$/", $_SERVER["REQUEST_URI"]) ? "0" : "1"),

	"action_path"			=> "actions",
	"handler_path"			=> "handlers",
	"gui_editor" 			=> 1,
	"stylesheet"			=> "wikka.css",

	"header_action"			=> "header",
	"footer_action"			=> "footer",
	
	"navigation_links" => "[[CategoryCategory Categories]] :: PageIndex ::  RecentChanges :: RecentlyCommented :: [[UserSettings Login/Register]]",
	"logged_in_navigation_links" => "[[CategoryCategory Categories]] :: PageIndex :: RecentChanges :: RecentlyCommented :: [[UserSettings Change settings/Logout]]", 

	"referrers_purge_time"		=> 30,
	"pages_purge_time"		=> 0,
	"xml_recent_changes"		=> 10,
	"show_spaces"			=> "N",
	"hide_comments"			=> 0,
	"anony_delete_own_comments"	=> 1,
	"allow_doublequote_html"	=> 0,
	"external_link_tail" 		=> "<span class='exttail'>&#8734;</span>",
	"sql_debugging"			=> 0,
	"admin_users" 			=> "",
	"admin_email" 			=> "",
	"upload_path" 			=> "uploads",
	"mime_types" 			=> "mime_types.txt",

	"default_write_acl"		=> "*",
	"default_read_acl"		=> "*",
	"default_comment_acl"		=> "*");


// load config
if (!$configfile = GetEnv("WAKKA_CONFIG")) $configfile = "wakka.config.php";
if (file_exists($configfile)) include($configfile);
$wakkaConfigLocation = $configfile;
$wakkaConfig = array_merge($wakkaDefaultConfig, $wakkaConfig);

// check for locking
if (file_exists("locked")) {
	// read password from lockfile
	$lines = file("locked");
	$lockpw = trim($lines[0]);

	// is authentification given?
	if (isset($_SERVER["PHP_AUTH_USER"])) {
		if (!(($_SERVER["PHP_AUTH_USER"] == "admin") && ($_SERVER["PHP_AUTH_PW"] == $lockpw))) {
			$ask = 1;
		}
	} else {
		$ask = 1;
	}

	if ($ask) {
		header("WWW-Authenticate: Basic realm=\"".$wakkaConfig["wakka_name"]." Install/Upgrade Interface\"");
		header("HTTP/1.0 401 Unauthorized");
		print("This site is currently being upgraded. Please try again later.");
		exit;
    }
}


// compare versions, start installer if necessary
if ($wakkaConfig["wakka_version"] != WAKKA_VERSION)
{
	// start installer
	if (!$installAction = trim($_REQUEST["installAction"])) $installAction = "default";
	include("setup/header.php");
	if (file_exists("setup/".$installAction.".php")) include("setup/".$installAction.".php"); else print("<em>Invalid action</em>");
	include("setup/footer.php");
	exit;
}



// start session
session_start();

// fetch wakka location
$wakka = $_REQUEST["wakka"];

// remove leading slash
$wakka = preg_replace("/^\//", "", $wakka);

// split into page/method
if (preg_match("#^(.+?)/(.*)$#", $wakka, $matches)) list(, $page, $method) = $matches;
else if (preg_match("#^(.*)$#", $wakka, $matches)) list(, $page) = $matches;

// create wakka object
$wakka = new Wakka($wakkaConfig);
// check for database access 
if (!$wakka->dblink) 
{ 
	echo "<p>The wiki is currently unavailable. <br /><br />Error: Unable to connect to the MySQL database.</p>"; 
      exit; 
} 

function compress_output($output) 
{ 
    return gzencode($output); 
} 

// Check if the browser supports gzip encoding, HTTP_ACCEPT_ENCODING 
if (strstr ($HTTP_SERVER_VARS['HTTP_ACCEPT_ENCODING'], 'gzip') && function_exists('gzencode') )
{ 
    // Start output buffering, and register compress_output() (see 
    // below) 
    ob_start ("compress_output"); 

    // Tell the browser the content is compressed with gzip 
     header ("Content-Encoding: gzip"); 
} 

// go!
if (!isset($method)) $method='';
$wakka->Run($page, $method);
if (!preg_match("/(xml|raw)/",$method))
{
	   $tend = getmicrotime();
	//Calculate the difference 
	    $totaltime = ($tend - $tstart);     
	//Output result 
	    printf ("<div class=\"smallprint\">Page was generated in %.4f seconds</div>\n</body>\n</html>", $totaltime);
}
?>