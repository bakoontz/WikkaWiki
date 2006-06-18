<?php
/*

This file is part of Wikka, a PHP wiki engine.

Copyright (C) 2002, 2003 Hendrik Mans <hendrik@mans.de>
Copyright (C) 2004, 2005 Jason Tourtelotte <wikka-admin@jsnx.com>
Copyright (C) 2006 Wikka Development Team <dartar@wikkawiki.org>

Wikka is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

Wikka is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

*/

/**
 * This file was originally written by Hendrik Mans for WakkaWiki
 * and released under the terms of the modified BSD license
 * (see docs/WakkaWiki.LICENSE).
 * WakkaWiki Copyright (c) 2002, Hendrik Mans <hendrik@mans.de>
 */

if(!defined('ERROR_WAKKA_LIBRARY_MISSING')) define ('ERROR_WAKKA_LIBRARY_MISSING','The necessary file "libs/Wakka.class.php" could not be found. To run Wikka, please make sure the file exists and is placed in the right directory!');
if(!defined('ERROR_WRONG_PHP_VERSION')) define ('ERROR_WRONG_PHP_VERSION', '$_REQUEST[] not found. Wakka requires PHP 4.1.0 or higher!');
if(!defined('ERROR_SETUP_FILE_MISSING')) define ('ERROR_SETUP_FILE_MISSING', 'A file of the installer/ upgrader was not found. Please install Wikka again!');
if(!defined('ERROR_SETUP_HEADER_MISSING')) define ('ERROR_SETUP_HEADER_MISSING', 'The file "setup/header.php" was not found. Please install Wikka again!');
if(!defined('ERROR_SETUP_FOOTER_MISSING')) define ('ERROR_SETUP_FOOTER_MISSING', 'The file "setup/footer.php" was not found. Please install Wikka again!');
if(!defined('ERROR_NO_DB_ACCESS')) define ('ERROR_NO_DB_ACCESS', 'The wiki is currently unavailable. <br /><br />Error: Unable to connect to the MySQL database.');
if(!defined('PAGE_GENERATION_TIME')) define ('PAGE_GENERATION_TIME', 'Page was generated in %.4f seconds'); // %.4f - generation time in seconds with 4 digits after the dot   
if(!defined('WIKI_UPGRADE_NOTICE')) define ('WIKI_UPGRADE_NOTICE', 'This site is currently being upgraded. Please try again later.');

ob_start();

//error_reporting(E_ALL);
error_reporting (E_ALL ^ E_NOTICE);

/**
 * Defines current version. Do not change the version number or you will have problems upgrading.
 */
if (!defined('WAKKA_VERSION')) define('WAKKA_VERSION', 'trunk');

if(!defined('BASIC_COOKIE_NAME')) define('BASIC_COOKIE_NAME', 'Wikkawiki');

function getmicrotime() {
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}

$tstart = getmicrotime();

if ( ! function_exists("mysql_real_escape_string") )
{
	function mysql_real_escape_string($string)
	{
		return mysql_escape_string($string);
	}
}

// check for main library 
if (file_exists('libs/Wakka.class.php')) require_once('libs/Wakka.class.php');
else die(ERROR_WAKKA_LIBRARY_MISSING);

// stupid version check
if (!isset($_REQUEST)) die(ERROR_WRONG_PHP_VERSION);

// workaround for the amazingly annoying magic quotes.
function magicQuotesWorkaround(&$a)
{
	if (is_array($a))
	{
		foreach ($a as $k => $v)
		{
			if (is_array($v))
				magicQuotesWorkaround($a[$k]);
			else
				$a[$k] = stripslashes($v);
		}
	}
}
set_magic_quotes_runtime(0);
if (get_magic_quotes_gpc())
{
	magicQuotesWorkaround($_POST);
	magicQuotesWorkaround($_GET);
	magicQuotesWorkaround($_COOKIE);
}


// default configuration values
$wakkaDefaultConfig = array(
	'mysql_host'				=> 'localhost',
	'mysql_database'			=> 'wikka',
	'mysql_user'				=> 'wikka',
	'table_prefix'			=> 'wikka_',

	'root_page'				=> 'HomePage',
	'wakka_name'				=> 'MyWikkaSite',
	'base_url'				=> 'http://'.$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT'] != 80 ? ':'.$_SERVER['SERVER_PORT'] : '').$_SERVER['REQUEST_URI'].(preg_match('/'.preg_quote('wikka.php').'$/', $_SERVER['REQUEST_URI']) ? '?wakka=' : ''),
	'rewrite_mode'			=> (preg_match('/'.preg_quote('wikka.php').'$/', $_SERVER['REQUEST_URI']) ? '0' : '1'),
	'wiki_suffix'			=> '@wikka',

	'action_path'			=> 'actions',
	'handler_path'			=> 'handlers',
	'gui_editor'				=> '1',
	'stylesheet'				=> 'wikka.css',

	// formatter and code highlighting paths
	'wikka_formatter_path' 	=> 'formatters',		# (location of Wikka formatter - REQUIRED)
	'wikka_highlighters_path'	=> 'formatters',		# (location of Wikka code highlighters - REQUIRED)
	'geshi_path' 			=> '3rdparty/plugins/geshi',				# (location of GeSHi package)
	'geshi_languages_path' 	=> '3rdparty/plugins/geshi/geshi',		# (location of GeSHi language highlighting files)

	'header_action'			=> 'header',
	'footer_action'			=> 'footer',

	'navigation_links'		=> '[[CategoryCategory Categories]] :: PageIndex ::  RecentChanges :: RecentlyCommented :: [[UserSettings Login/Register]]',
	'logged_in_navigation_links' => '[[CategoryCategory Categories]] :: PageIndex :: RecentChanges :: RecentlyCommented :: [[UserSettings Change settings/Logout]]',

	'referrers_purge_time'	=> '30',
	'pages_purge_time'		=> '0',
	'xml_recent_changes'		=> '10',
	'hide_comments'			=> '0',
	'anony_delete_own_comments'	=> '1',
	'public_sysinfo'			=> '0',		# enable or disable public display of system information in SysInfo
	'require_edit_note'		=> '0',		# edit note optional (0, default), edit note required (1) edit note disabled (2)
	'allow_user_registration' => '1',	# user registration disabled (0), enabled (1) or only possible with register code (2)
	'registercode' => '',	# used by 'allow_user_registration' => '2'
	'double_doublequote_html'	=> 'safe',
	'external_link_tail' 		=> '<span class="exttail">&#8734;</span>',
	'sql_debugging'			=> '0',
	'admin_users' 			=> '',
	'admin_email' 			=> '',
	'upload_path' 			=> 'uploads',
	'mime_types' 			=> 'mime_types.txt',

	// code hilighting with GeSHi
	'geshi_header'			=> 'div',				# 'div' (default) or 'pre' to surround code block
	'geshi_line_numbers'		=> '1',			# disable line numbers (0), or enable normal (1) or fancy line numbers (2)
	'geshi_tab_width'		=> '4',				# set tab width
	'grabcode_button'		=> '1',				# allow code block downloading

	'wikiping_server' 		=> '',

	'default_write_acl'		=> '+',
	'default_read_acl'		=> '*',
	'default_comment_acl'		=> '*');

// load config
$wakkaConfig = array();
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
		print WIKI_UPGRADE_NOTICE;
		exit;
    }
}

// compare versions, start installer if necessary
if (!isset($wakkaConfig["wakka_version"])) $wakkaConfig["wakka_version"] = 0;
if ($wakkaConfig["wakka_version"] !== WAKKA_VERSION)
{
	// start installer
	$installAction = "default";
	if (isset($_REQUEST["installAction"])) $installAction = trim($_REQUEST["installAction"]);
	if (file_exists("setup/header.php")) include("setup/header.php"); else print '<em>'.ERROR_SETUP_HEADER_MISSING.'</em>';
	if (file_exists("setup/".$installAction.".php")) include("setup/".$installAction.".php"); else print '<em>'.ERROR_SETUP_FILE_MISSING.'</em>';
	if (file_exists("setup/footer.php")) include("setup/footer.php"); else print '<em>'.ERROR_SETUP_FOOTER_MISSING.'</em>';
	exit;
}

// start session
session_name(md5(BASIC_COOKIE_NAME.$wakkaConfig['wiki_suffix']));
session_start();

// fetch wakka location
$wakka = $_REQUEST["wakka"];

// remove leading slash
$wakka = preg_replace("/^\//", "", $wakka);

// split into page/method
if (preg_match("#^(.+?)/(.*)$#", $wakka, $matches)) list(, $page, $method) = $matches;
else if (preg_match("#^(.*)$#", $wakka, $matches)) list(, $page) = $matches;
#Fix lowercase mod_rewrite bug: Url rewritting lowercases the page name. #135
if (strtolower($page) == $page)
{
 $pattern = preg_quote($page, '/');
 if (preg_match("/($pattern)/i", urldecode($_SERVER['REQUEST_URI']), $match_url))
 {
  $page = $match_url[1];
 }
}

// create wakka object
$wakka =& new Wakka($wakkaConfig);								# create object by reference
// check for database access
if (!$wakka->dblink)
{
	echo '<em class="error">'.ERROR_NO_DB_ACCESS.'</em>';
      exit;
}


// go!
if (!isset($method)) $method='';
$wakka->Run($page, $method);
if (!preg_match("/(xml|raw|mm|grabcode)$/", $method))
{
	   $tend = getmicrotime();
	//Calculate the difference
	    $totaltime = ($tend - $tstart);
	//Output result
	    print '<div class="smallprint">'.sprintf(PAGE_GENERATION_TIME, $totaltime)."</div>\n</body>\n</html>";
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
