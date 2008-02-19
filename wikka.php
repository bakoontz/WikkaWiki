<?php
/*

Copyright (c) 2002, Hendrik Mans <hendrik@mans.de>
Copyright 2003 Carlo Zottmann
Copyright 2004 Jason Tourtelotte <wikka-admin@jsnx.com>
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
   notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
   notice, this list of conditions and the following disclaimer in the
   documentation and/or other materials provided with the distribution.
3. The name of the author may not be used to endorse or promote products
   derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

*/

ob_start(); 

//error_reporting(E_ALL);
error_reporting (E_ALL ^ E_NOTICE);

// Do not change the version number or you will have problems upgrading.
define("WAKKA_VERSION", "1.1.3.9");
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
	function CheckMySQLVersion($major, $minor, $subminor)
	{
		$result = @mysql_query('SELECT VERSION() AS version'); 
		if ($result != FALSE && @mysql_num_rows($result) > 0) 
		{ 
			$row   = mysql_fetch_array($result); 
			$match = explode('.', $row['version']); 
		} 
		else 
		{ 
			$result = @mysql_query('SHOW VARIABLES LIKE \'version\''); 
			if ($result != FALSE && @mysql_num_rows($result) > 0) { 
				$row   = mysql_fetch_row($result); 
				$match = explode('.', $row[1]); 
			} else { 
				return 0; 
			} 
		} 

		$mysql_major = $match[0];
		$mysql_minor = $match[1];
		$mysql_subminor = $match[2][0].$match[2][1];

		if ($mysql_major > $major) {
			return 1; 
		} else {
			if (($mysql_major == $major) && ($mysql_minor >= $minor) && ($mysql_subminor >= $subminor)) {
				return 1; 
			} else {
				return 0; 
			}
		}
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
	function ReturnSafeHTML($html)
	{
		require_once('safehtml/classes/HTMLSax.php');
		require_once('safehtml/classes/safehtml.php');

		// Save all "<" symbols
		$filtered_output = preg_replace("/<(?=[^a-zA-Z\/\!\?\%])/", "&lt;", $html);
			
		// Opera6 bug workaround
		$filtered_output = str_replace("\xC0\xBC", "&lt;", $filtered_output);

		// Instantiate the handler
		$handler=& new safehtml();

		// Instantiate the parser
		$parser=& new XML_HTMLSax();

		// Register the handler with the parser
		$parser->set_object($handler);

		// Set the handlers
		$parser->set_element_handler('openHandler','closeHandler');
		$parser->set_data_handler('dataHandler');
		$parser->set_escape_handler('escapeHandler');

		$parser->parse($filtered_output);

		$filtered_output = $handler->getXHTML(); 
		return $filtered_output;
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
		if (!$time && $cache) {
			$page = isset($this->pageCache[$tag]) ? $this->pageCache[$tag] : null; 
			if ($page=="cached_nonexistent_page") return null;
		}
		// load page
		if (!isset($page)) $page = $this->LoadSingle("select * from ".$this->config["table_prefix"]."pages where tag = '".mysql_escape_string($tag)."' ".($time ? "and time = '".mysql_escape_string($time)."'" : "and latest = 'Y'")." limit 1");
		// cache result
		if ($page && !$time) {
			$this->pageCache[$page["tag"]] = $page;
		} elseif (!$page) {
			$this->pageCache[$tag] = "cached_nonexistent_page";
		}
		return $page;
	}
	function IsLatestPage() { 
		return $this->latest;
	}
	function GetCachedPage($tag) { return (isset($this->pageCache[$tag])) ? $this->pageCache[$tag] : null; }
	function CachePage($page) { $this->pageCache[$page["tag"]] = $page; }
	function SetPage($page) { $this->page = $page; if ($this->page["tag"]) $this->tag = $this->page["tag"]; }
	function LoadPageById($id) { return $this->LoadSingle("select * from ".$this->config["table_prefix"]."pages where id = '".mysql_escape_string($id)."' limit 1"); }
	function LoadRevisions($page) { return $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where tag = '".mysql_escape_string($page)."' order by time desc"); }
	function LoadPagesLinkingTo($tag) { return $this->LoadAll("select from_tag as tag from ".$this->config["table_prefix"]."links where to_tag = '".mysql_escape_string($tag)."' order by tag"); }
	function LoadRecentlyChanged()
	{
		if ($pages = $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where latest = 'Y' order by time desc"))
		{
			foreach ($pages as $page)
			{
				$this->CachePage($page);
			}
			return $pages;
		}
	}
	function LoadWantedPages() { return $this->LoadAll("select distinct ".$this->config["table_prefix"]."links.to_tag as tag,count(".$this->config["table_prefix"]."links.from_tag) as count from ".$this->config["table_prefix"]."links left join ".$this->config["table_prefix"]."pages on ".$this->config["table_prefix"]."links.to_tag = ".$this->config["table_prefix"]."pages.tag where ".$this->config["table_prefix"]."pages.tag is NULL group by tag order by count desc"); }
	function IsWantedPage($tag) 
	{
		if ($pages = $this->LoadWantedPages())
		{
			foreach ($pages as $page)
			{
				if ($page["tag"] == $tag) return true;
			}
		}
		return false;
	}
	function LoadOrphanedPages() { return $this->LoadAll("select distinct tag from ".$this->config["table_prefix"]."pages left join ".$this->config["table_prefix"]."links on ".$this->config["table_prefix"]."pages.tag = ".$this->config["table_prefix"]."links.to_tag where ".$this->config["table_prefix"]."links.to_tag is NULL order by tag"); }
	function LoadPageTitles() { return $this->LoadAll("select distinct tag from ".$this->config["table_prefix"]."pages order by tag"); }
	function LoadAllPages() { return $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where latest = 'Y' order by tag"); }
	// function FullTextSearch($phrase) { return $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where latest = 'Y' and match(tag, body) against('".mysql_escape_string($phrase)."')"); }
	function FullTextSearch($phrase)
	{
		// return $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where latest = 'Y' and match(tag, body) against('".mysql_escape_string($phrase)."') order by time DESC");

		$data = "";
		if ($this->CheckMySQLVersion(4,00,01))
		{	
			$data = $this->LoadAll("(select * from "
			.$this->config["table_prefix"]
			."pages where latest = 'Y' and 
			tag like('%".mysql_escape_string($phrase)."%')) 
				UNION (select * from ".$this->config["table_prefix"]
				."pages where latest = 'Y' and match(body) against('".mysql_escape_string($phrase)."'))");
		}
		// else if ($this->CheckMySQLVersion(3,23,23))
		// {
		//	$data = $this->LoadAll("select * from "
		//	.$this->config["table_prefix"]
		//	."pages where latest = 'Y' and
		//		  match(tag, body)
		//		  against('".mysql_escape_string($phrase)."')
		//		  order by time DESC");
		// } 

		/* if no results perform a more general search */
		if (!$data)  {
				$data = $this->LoadAll("select * from "
				.$this->config["table_prefix"]
				."pages where latest = 'Y' and
				  (tag like '%".mysql_escape_string($phrase)."%' or
				   body like '%".mysql_escape_string($phrase)."%')
				   order by time DESC");
		}
		
		return($data);
	}
	function FullCategoryTextSearch($phrase) { return $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where latest = 'Y' and match(body) against('".mysql_escape_string($phrase)."' IN BOOLEAN MODE) or tag like '%".mysql_escape_string($phrase)."%'"); }
	function SavePage($tag, $body, $note)
	{
		// get current user
		$user = $this->GetUserName();

		// TODO: check write privilege
		if ($this->HasAccess("write", $tag))
		{
			// is page new?
			if (!$oldPage = $this->LoadPage($tag))
			{
				// current user is owner if user is logged in, otherwise, no owner.
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
			if (preg_match("/^([\t]+)(-|([1aiAI]\))?)/", $body, $matches)) $lead = $matches[1]; // save tab(s) of first list-definition, if any at the begining
			$body = $lead.trim($body); //and write it back to the trimmed body

			$this->Query("insert into ".$this->config["table_prefix"]."pages set ".
				"tag = '".mysql_escape_string($tag)."', ".
 				"time = now(), ".
  				"owner = '".mysql_escape_string($owner)."', ".
 				"user = '".mysql_escape_string($user)."', ".
				"note = '".mysql_escape_string($note)."', ".
 				"latest = 'Y', ".
 				"body = '".mysql_escape_string($body)."'"); 

			if ($pingdata = $this->GetPingParams($this->config["wikiping_server"], $tag, $user, $note))
				$this->WikiPing($pingdata);
		}
	}

	// WIKI PING  -- Coded by DreckFehler
	function HTTPpost($host, $data, $contenttype="application/x-www-form-urlencoded", $maxAttempts = 5) {
		$attempt =0; $status = 300; $result = "";
		while ($status >= 300 && $status < 400 && $attempt++ <= $maxAttempts) {
			$url = parse_url($host);
			if (isset($url["path"]) == false) $url["path"] = "/";
			if (isset($url["port"]) == false) $url["port"] = 80;

			if ($socket = fsockopen ($url["host"], $url["port"], $errno, $errstr, 15)) {
				$strQuery = "POST ".$url["path"]." HTTP/1.1\n";
				$strQuery .= "Host: ".$url["host"]."\n";
				$strQuery .= "Content-Length: ".strlen($data)."\n";
				$strQuery .= "Content-Type: ".$contenttype."\n";
				$strQuery .= "Connection: close\n\n";
				$strQuery .= $data;

				// send request & get response
				fputs($socket, $strQuery);
				$bHeader = true;
				while (!feof($socket)) {
					$strLine = trim(fgets($socket, 512));
					if (strlen($strLine) == 0) $bHeader = false; // first empty line ends header-info
					if ($bHeader) {
						if (!$status) $status = $strLine;
						if (preg_match("/^Location:\s(.*)/", $strLine, $matches)) $location = $matches[1];
					} else $result .= trim($strLine)."\n";
				}
				fclose ($socket);
			} else $status = "999 timeout";

			if ($status) {
				if(preg_match("/(\d){3}/", $status, $matches)) $status = $matches[1];
			} else $status = 999;
			$host = $location;
		}
		if (preg_match("/^[\da-fA-F]+(.*)$/", $result, $matches)) $result = $matches[1];
		return $result;
	}
	function WikiPing($ping, $debug = false) {
		if ($ping) {
			$rpcRequest .= "<methodCall>\n";
			$rpcRequest .= "<methodName>wiki.ping</methodName>\n";
			$rpcRequest .= "<params>\n";
			$rpcRequest .= "<param>\n<value>\n<struct>\n";
			$rpcRequest .= "<member>\n<name>tag</name>\n<value>".$ping["tag"]."</value>\n</member>\n";
			$rpcRequest .= "<member>\n<name>url</name>\n<value>".$ping["taglink"]."</value>\n</member>\n";
			$rpcRequest .= "<member>\n<name>wiki</name>\n<value>".$ping["wiki"]."</value>\n</member>\n";
			if ($ping["author"]) {
				$rpcRequest .= "<member>\n<name>author</name>\n<value>".$ping["author"]."</value>\n</member>\n";
				if ($ping["authorpage"]) $rpcRequest .= "<member>\n<name>authorpage</name>\n<value>".$ping["authorpage"]."</value>\n</member>\n";
			}
			if ($ping["history"]) $rpcRequest .= "<member>\n<name>history</name>\n<value>".$ping["history"]."</value>\n</member>\n";
			if ($ping["changelog"]) $rpcRequest .= "<member>\n<name>changelog</name>\n<value>".$ping["changelog"]."</value>\n</member>\n";
			$rpcRequest .= "</struct>\n</value>\n</param>\n";
			$rpcRequest .= "</params>\n";
			$rpcRequest .= "</methodCall>\n";

			foreach (explode(" ", $ping["server"]) as $server) {
				$response = $this->HTTPpost($server, $rpcRequest, "text/xml");
				if ($debug) print $response;
			}
		}
	}
	function GetPingParams($server, $tag, $user, $changelog = "") {
		$ping = array();
		if ($server) {
			$ping["server"] = $server;
			if ($tag) $ping["tag"] = $tag; else return false; // set page-title
 			if (!$ping["taglink"] = $this->href("", $tag)) return false; // set page-url
            	if (!$ping["wiki"] = $this->config["wakka_name"]) return false; // set site-name
			$ping["history"] = $this->href("revisions", $tag); // set url to history

			if ($user) {
				$ping["author"] = $user; // set username
				if ($this->LoadPage($user)) $ping["authorpage"] = $this->href("", $user); // set link to user page
			}
			if ($changelog) $ping["changelog"] = $changelog;
			return $ping;
		} else return false;
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
	function Link($tag, $method = "", $text = "", $track = 1, $escapeText = 1, $title="") {
		if (!$text) $text = $tag;
		// escape text?
		if ($escapeText) $text = htmlspecialchars($text);
		$url = '';

		// is this a wiki link?
		if (preg_match("/^[A-Za-z0-9]+$/", $tag))
		{
			if ($_SESSION["linktracking"] && $track) $this->TrackLinkTo($tag);
			$linkedPage = $this->LoadPage($tag);
			// return ($linkedPage ? "<a href=\"".$this->href($method, $linkedPage['tag'])."\">".$text."</a>" : "<span class=\"missingpage\">".$text."</span><a href=\"".$this->href("edit", $tag)."\" title=\"Create this page\">?</a>");
			return ($linkedPage ? "<a href=\"".$this->href($method, $linkedPage['tag'])."\" title=\"$title\">".$text."</a>" : "<a href=\"".$this->href("edit", $tag)."\" title=\"Create this page\"><span class=\"missingpage\">".$text."</span></a>");
		} 
		elseif (preg_match("/^(http|https|ftp):\/\/([^\\s\"<>]+)$/", $tag))
		{
			$url = $tag; // this is a vaild external URL
		}	
		// is this an interwiki link?
		elseif (preg_match("/^([A-Z,ÄÖÜ][A-Z,a-z,ÄÖÜ,ßäöü]+)[:]([A-Z,a-z,0-9,ÄÖÜ,ßäöü]*)$/", $tag, $matches))
		{
			$url = $this->GetInterWikiUrl($matches[1], $matches[2]);
		}
		// is this a full link? ie, does it contain alpha-numeric characters?
		elseif (preg_match("/[^[:alnum:],ÄÖÜ,ßäöü]/", $tag))
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
		$external_link_tail = $this->GetConfigValue("external_link_tail");
		return $url ? "<a class=\"ext\" href=\"$url\">$text</a>$external_link_tail" : $text;
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
	function FormOpen($method = "", $tag = "", $formMethod = "post")
	{
		$result = "<form action=\"".$this->href($method, $tag)."\" method=\"".$formMethod."\">\n";
		if (!$this->config["rewrite_mode"]) $result .= "<input type=\"hidden\" name=\"wakka\" value=\"".$this->MiniHref($method, $tag)."\" />\n";
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
			$parsed_url = parse_url($referrer);
			$spammer = $parsed_url["host"];
			$blacklist = $this->LoadSingle("select * from ".$this->config["table_prefix"]."referrer_blacklist WHERE spammer = '".mysql_escape_string($spammer)."'");
			if (!$blacklist) {
			$this->Query("insert into ".$this->config["table_prefix"]."referrers set ".
				"page_tag = '".mysql_escape_string($tag)."', ".
				"referrer = '".mysql_escape_string($referrer)."', ".
				"time = now()");
			}
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

		// only search for parameters if there is a space
		if (is_int(strpos($action, ' ')))
		{
			// treat everything after the first whitespace as parameter
			preg_match("/^([A-Za-z0-9]*)\s+(.*)$/", $action, $matches);
			// extract $action and $vars_temp ("raw" attributes)
			list(, $action, $vars_temp) = $matches;

			if ($action) { 
				// match all attributes (key and value)
				preg_match_all("/([A-Za-z0-9]*)=\"(.*)\"/U", $vars_temp, $matches);
	
				// prepare an array for extract() to work with (in $this->IncludeBuffered())
    				if (is_array($matches)) {
        				for ($a = 0; $a < count($matches[0]); $a++) {
            				$vars[$matches[1][$a]] = $matches[2][$a];
	        			}
    				}
				$vars["wakka_vars"] = trim($vars_temp); // <<< add the buffered parameter-string to the array
			} else {
   				return "<span class='error'><i>Unknown action; the action name must not contain special characters.</i></span>"; // <<< the pattern ([A-Za-z0-9])\s+ didn't match!
			}
		}
		if (!preg_match("/^[a-zA-Z0-9]+$/", $action)) return "<span class='error'><i>Unknown action; the action name must not contain special characters.</i></span>";
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
	function GetUser() { return (isset($_SESSION["user"])) ? $_SESSION["user"] : null; }
	function SetUser($user) { $_SESSION["user"] = $user; $this->SetPersistentCookie("name", $user["name"]); $this->SetPersistentCookie("password", $user["password"]); }
	function LogoutUser() { $_SESSION["user"] = ""; $this->DeleteCookie("name"); $this->DeleteCookie("password"); }
	function UserWantsComments() { if (!$user = $this->GetUser()) return false; return ($user["show_comments"] == "Y"); }


	// COMMENTS
	function LoadComments($tag) { return $this->LoadAll("SELECT * FROM ".$this->config["table_prefix"]."comments WHERE page_tag = '".mysql_escape_string($tag)."' ORDER BY time"); }
	function LoadRecentComments($limit = 50) { return $this->LoadAll("SELECT * FROM ".$this->config["table_prefix"]."comments ORDER BY time DESC LIMIT ".$limit); }
	function LoadRecentlyCommented($limit = 50)
	{
		$sql = "SELECT comments.id, comments.page_tag, comments.time, comments.user"
        	. " FROM ".$this->config["table_prefix"]."comments AS comments"
        	. " LEFT JOIN ".$this->config["table_prefix"]."comments AS c2 ON comments.page_tag = c2.page_tag AND comments.id < c2.id"
        	. " WHERE c2.page_tag IS NULL "
        	. " ORDER BY time DESC "
        	. " LIMIT ".$limit; 
		return $this->LoadAll($sql);
	}
	function SaveComment($page_tag, $comment)
	{
		// get current user
		$user = $this->GetUserName();

		// add new comment
		$this->Query("INSERT INTO ".$this->config["table_prefix"]."comments SET ".
			"page_tag = '".mysql_escape_string($page_tag)."', ".
			"time = now(), ".
			"comment = '".mysql_escape_string($comment)."', ".
			"user = '".mysql_escape_string($user)."'");
	}

	// ACCESS CONTROL
	// returns true if logged in user is owner of current page, or page specified in $tag
	function UserIsOwner($tag = "")
	{
		// check if user is logged in
		if (!$this->GetUser()) return false;

		// if user is admin, return true. Admin can do anything!
		if ($this->IsAdmin()) return true;

		// set default tag
		if (!$tag = trim($tag)) $tag = $this->GetPageTag();

		// check if user is owner
		if ($this->GetPageOwner($tag) == $this->GetUserName()) return true;
	}
	//returns true if user is listed in configuration list as admin
	function IsAdmin() {
		$adminstring = $this->config["admin_users"];
		$adminarray = explode(',' , $adminstring);
	
		foreach ($adminarray as $admin) {
			if (trim($admin) == $this->GetUserName()) return true;
		}
	}
	function GetPageOwner($tag = "", $time = "") { if (!$tag = trim($tag)) $tag = $this->GetPageTag(); if ($page = $this->LoadPage($tag, $time)) return $page["owner"]; }
	function SetPageOwner($tag, $user)
	{
		// check if user exists
		if( $user <> '' && ($this->LoadUser($user) || $user == "(Public)"))
		{
			// update latest revision with new owner
		$this->Query("update ".$this->config["table_prefix"]."pages set owner = '".mysql_escape_string($user)."' where tag = '".mysql_escape_string($tag)."' and latest = 'Y' limit 1");
		}
	}
	function LoadACL($tag, $privilege, $useDefaults = 1)
	{
		if ((!$acl = $this->LoadSingle("SELECT ".mysql_escape_string($privilege)."_acl FROM ".$this->config["table_prefix"]."acls WHERE page_tag = '".mysql_escape_string($tag)."' LIMIT 1")) && $useDefaults)
		{
			$acl = array("page_tag" => $tag, $privilege."_acl" => $this->GetConfigValue("default_".$privilege."_acl"));
		}
		return $acl;
	}
	function LoadAllACLs($tag, $useDefaults = 1)
	{
		if ((!$acl = $this->LoadSingle("SELECT * FROM ".$this->config["table_prefix"]."acls WHERE page_tag = '".mysql_escape_string($tag)."' LIMIT 1")) && $useDefaults)
		{
			$acl = array("page_tag" => $tag, "read_acl" => $this->GetConfigValue("default_read_acl"), "write_acl" => $this->GetConfigValue("default_write_acl"), "comment_acl" => $this->GetConfigValue("default_comment_acl"));
		}
		return $acl;
	}
	function SaveACL($tag, $privilege, $list) {
		if ($this->LoadACL($tag, $privilege, 0)) $this->Query("UPDATE ".$this->config["table_prefix"]."acls SET ".mysql_escape_string($privilege)."_acl = '".mysql_escape_string(trim(str_replace("\r", "", $list)))."' WHERE page_tag = '".mysql_escape_string($tag)."' LIMIT 1");
		else $this->Query("INSERT INTO ".$this->config["table_prefix"]."acls SET page_tag = '".mysql_escape_string($tag)."', ".mysql_escape_string($privilege)."_acl = '".mysql_escape_string(trim(str_replace("\r", "", $list)))."'");
	}
	function TrimACLs($list) {
		foreach (explode("\n", $list) as $line)
		{
			$line = trim($line);
			$trimmed_list .= $line."\n";
		}
		return $trimmed_list;
	}
	// returns true if $user (defaults to current user) has access to $privilege on $page_tag (defaults to current page)
	function HasAccess($privilege, $tag = "", $user = "")
	{
		// set defaults
		if (!$tag) $tag = $this->GetPageTag();
		if (!$user) $user = $this->GetUserName();

		// if current user is owner, return true. owner can do anything!
		if ($this->UserIsOwner($tag)) return true;

		// see whether user is registered and logged in
		if ($this->GetUser()) $registered = true;

		// load acl
		if ($tag == $this->GetPageTag())
		{	
			$acl = $this->ACLs[$privilege."_acl"];
		}
		else
		{
			$tag_ACLs = $this->LoadAllACLs($tag);
			$acl = $tag_ACLs[$privilege."_acl"];
		}

		// fine fine... now go through acl
		foreach (explode("\n", $acl) as $line)
		{
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
					// return ($registered) ? !$negate : false;
					return ($registered) ? !$negate : $negate;
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
			$this->Query("DELETE FROM ".$this->config["table_prefix"]."referrers WHERE time < date_sub(now(), interval '".mysql_escape_string($days)."' day)");
		}

		// purge old page revisions
		if ($days = $this->GetConfigValue("pages_purge_time")) {
			$this->Query("delete from ".$this->config["table_prefix"]."pages where time < date_sub(now(), interval '".mysql_escape_string($days)."' day) and latest = 'N'");
		}
	}

	// THE BIG EVIL NASTY ONE!
	function Run($tag, $method = "")
	{
		// do our stuff!
		if (!$this->method = trim($method)) $this->method = "show";
		if (!$this->tag = trim($tag)) $this->Redirect($this->href("", $this->config["root_page"]));
		if ((!$this->GetUser() && isset($_COOKIE["name"])) && ($user = $this->LoadUser($_COOKIE["name"], $_COOKIE["password"]))) $this->SetUser($user);
		$this->SetPage($this->LoadPage($tag, (isset($_REQUEST["time"]) ? $_REQUEST["time"] :'')));

		$this->LogReferrer();
		$this->ACLs = $this->LoadAllACLs($this->tag);
		$this->ReadInterWikiConfig();
		if(!($this->GetMicroTime()%3)) $this->Maintenance(); 

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
	"base_url"				=> "http://".$_SERVER["SERVER_NAME"].($_SERVER["SERVER_PORT"] != 80 ? ":".$_SERVER["SERVER_PORT"] : "").$_SERVER["REQUEST_URI"].(preg_match("/".preg_quote("wikka.php")."$/", $_SERVER["REQUEST_URI"]) ? "?wakka=" : ""),
	"rewrite_mode"			=> (preg_match("/".preg_quote("wikka.php")."$/", $_SERVER["REQUEST_URI"]) ? "0" : "1"),

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
	"hide_comments"			=> 0,
	"anony_delete_own_comments"	=> 1,
	"double_doublequote_html"	=> "safe",
	"external_link_tail" 		=> "<span class='exttail'>&#8734;</span>",
	"sql_debugging"			=> 0,
	"admin_users" 			=> "",
	"admin_email" 			=> "",
	"upload_path" 			=> "uploads",
	"mime_types" 			=> "mime_types.txt",

	"wikiping_server" 		=> "",

	"default_write_acl"		=> "*",
	"default_read_acl"		=> "*",
	"default_comment_acl"		=> "*");


// load config
if (file_exists("wakka.config.php")) rename("wakka.config.php", "wikka.config.php");
if (!$configfile = GetEnv("WAKKA_CONFIG")) $configfile = "wikka.config.php";
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

$content =  ob_get_contents();
if (strstr ($HTTP_SERVER_VARS['HTTP_ACCEPT_ENCODING'], 'gzip') && function_exists('gzencode') )
{ 
   // Tell the browser the content is compressed with gzip 
	header ("Content-Encoding: gzip"); 
	$page_output = gzencode($content); 
	$page_length = strlen($page_output); 
} else {
	$page_output = $content;
	$page_length = strlen($page_output); 
}

// header("Cache-Control: pre-check=0");
header("Cache-Control: no-cache");
// header("Pragma: ");
// header("Expires: ");

$etag =  md5($content);
header('ETag: '.$etag); 

header('Content-Length: '.$page_length); 
ob_end_clean(); 
echo $page_output;

?>