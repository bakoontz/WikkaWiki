<?php
/**
 * The Wikka mainscript.
 * 
 * This file is called each time a request is made from the browser.
 * Most of the core methods used by the engine are located in the Wakka class.
 * @see Wakka
 * This file was originally written by Hendrik Mans for WakkaWiki
 * and released under the terms of the modified BSD license
 * @see /docs/WakkaWiki.LICENSE
 *
 * @package Wikka
 * @subpackage Core
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @see /docs/Wikka.LICENSE
 * @filesource
 * 
 * @author Hendrik Mans <hendrik@mans.de>
 * @author Jason Tourtelotte <wikka-admin@jsnx.com>
 * @author {@link http://wikkawiki.org/JavaWoman Marjolein Katsma}
 * @author {@link http://wikkawiki.org/NilsLindenberg Nils Lindenberg}
 * @author {@link http://wikkawiki.org/DotMG Mahefa Randimbisoa}
 * @author {@link http://wikkawiki.org/DarTar Dario Taraborelli}
 * 
 * @copyright Copyright 2002-2003, Hendrik Mans <hendrik@mans.de>
 * @copyright Copyright 2004-2005, Jason Tourtelotte <wikka-admin@jsnx.com>
 * @copyright Copyright 2006, {@link http://wikkawiki.org/CreditsPage Wikka Development Team}
 * 
 * @todo use templating class for page generation;
 * @todo add phpdoc documentation for configuration array elements;
 * @todo	replace $_REQUEST with either $_GET or $_POST (or both if really
 * 			necessary) - #312  
 */

// If you need to use this installation with a configuration file outside the 
// installation directory uncomment the following line and adapt it to reflect 
// the (filesystem) path to where your configuration file is located.
// This would make it possible to store the configuration file outside of the
// webroot, or to share one configuration file between several Wikka Wiki
// installations.
// This replaces the use of the environment variable WAKKA_CONFIG for security
// reasons. [SEC]      
#if (!defined('WAKKA_CONFIG')) define('WAKKA_CONFIG','path/to/your/wikka.config.php');

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
 * Defines the current Wikka version. Do not change the version number or you will have problems upgrading.
 */
if (!defined('WAKKA_VERSION')) define('WAKKA_VERSION', '1.1.6.3');
/**
 * Defines the default cookie name.
 */
if(!defined('BASIC_COOKIE_NAME')) define('BASIC_COOKIE_NAME', 'Wikkawiki');

/**
 * Calculate page generation time.
 */
function getmicrotime() {
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}

$tstart = getmicrotime();

if ( ! function_exists("mysql_real_escape_string") )
{
/**
 * Escape special characters in a string for use in a SQL statement.
 * 
 * This function is added for back-compatibility with MySQL 3.23.
 * @param string $string the string to be escaped
 * @return string a string with special characters escaped
 */
	function mysql_real_escape_string($string)
	{
		return mysql_escape_string($string);
	}
}

/**
 * Include main library if it exists.
 * @see /libs/Wakka.class.php
 */
if (file_exists('libs/Wakka.class.php')) require_once('libs/Wakka.class.php');
else die(ERROR_WAKKA_LIBRARY_MISSING);

// stupid version check
if (!isset($_REQUEST)) die(ERROR_WRONG_PHP_VERSION); // TODO replace with php version_compare

/** 
 * Workaround for the amazingly annoying magic quotes.
 */
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

/**
 * Default configuration.
 */
// attempt to derive base URL fragments and whether rewrite mode is enabled (#438)
$t_domain	= $_SERVER['SERVER_NAME'];
$t_port		= $_SERVER['SERVER_PORT'] != 80 ? ':'.$_SERVER['SERVER_PORT'] : '';
$t_request = $_SERVER['REQUEST_URI'];
if (preg_match('@\.php$@', $t_request) && !preg_match('@wikka\.php$@', $t_request))
{
	$t_request = preg_replace('@/[^.]+\.php@', '/wikka.php', $t_request);	// handle "overridden" redirect from index.php (or plain wrong file name!)
}
if ( !preg_match('@wakka=@',$_SERVER['REQUEST_URI']) && isset($_SERVER['QUERY_STRING']) && preg_match('@wakka=@',$_SERVER['QUERY_STRING']))
{
	// looks like we got a rewritten request via .htaccess 
	$t_query = '';
	$t_request = preg_replace('@'.preg_quote('wikka.php').'@', '', $t_request);
	$t_rewrite_mode = 1;
}
else
{
	// no rewritten request apparent
	$t_query = '?wakka=';
	$t_rewrite_mode = 0;
}
$wakkaDefaultConfig = array(
	'mysql_host'				=> 'localhost',
	'mysql_database'			=> 'wikka',
	'mysql_user'				=> 'wikka',
	'table_prefix'			=> 'wikka_',

	'root_page'				=> 'HomePage',
	'wakka_name'				=> 'MyWikkaSite',
#	'base_url'				=> 'http://'.$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT'] != 80 ? ':'.$_SERVER['SERVER_PORT'] : '').$_SERVER['REQUEST_URI'].(preg_match('/'.preg_quote('wikka.php').'$/', $_SERVER['REQUEST_URI']) ? '?wakka=' : ''),
#	'rewrite_mode'			=> (preg_match('/'.preg_quote('wikka.php').'$/', $_SERVER['REQUEST_URI']) ? '0' : '1'),
	'base_url'				=> 'http://'.$t_domain.$t_port.$t_request.$t_query,
	'rewrite_mode'			=> $t_rewrite_mode,
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
	'require_edit_note'		=> '0',		# edit note optional (0, default), edit note required (1) edit note disabled (2)
	'anony_delete_own_comments'	=> '1',
	'public_sysinfo'			=> '0',		# enable or disable public display of system information in SysInfo
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
#if (!$configfile = GetEnv("WAKKA_CONFIG")) $configfile = "wikka.config.php";
if (defined('WAKKA_CONFIG'))	// use a define instead of GetEnv [SEC] 
{
	$configfile = WAKKA_CONFIG;
}
else
{
	$configfile = 'wikka.config.php';
}
if (file_exists($configfile)) include($configfile);

$wakkaConfigLocation = $configfile;
$wakkaConfig = array_merge($wakkaDefaultConfig, $wakkaConfig);

/**
 * Check for locking.
 */
if (file_exists('locked'))
{
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

/**
 * Compare versions, start installer if necessary.
 */
if (!isset($wakkaConfig["wakka_version"])) $wakkaConfig["wakka_version"] = 0;
if ($wakkaConfig["wakka_version"] !== WAKKA_VERSION)
{
	/**
	 * Start installer.
	 * 
	 * Data entered by the user is submitted in $_POST, next action for the
	 * installer (which will receive this data) is passed as a $_GET parameter!
	 */
	$installAction = 'default';
	#if (isset($_REQUEST['installAction'])) $installAction = trim($_REQUEST['installAction']);
	if (isset($_GET['installAction'])) $installAction = trim($_GET['installAction']);	#312
	if (file_exists('setup'.DIRECTORY_SEPARATOR.'header.php')) include('setup'.DIRECTORY_SEPARATOR.'header.php'); else print '<em>'.ERROR_SETUP_HEADER_MISSING.'</em>'; #89
	if (file_exists('setup'.DIRECTORY_SEPARATOR.$installAction.'.php')) include('setup'.DIRECTORY_SEPARATOR.$installAction.'.php'); else print '<em>'.ERROR_SETUP_FILE_MISSING.'</em>'; #89
	if (file_exists('setup'.DIRECTORY_SEPARATOR.'footer.php')) include('setup'.DIRECTORY_SEPARATOR.'footer.php'); else print '<em>'.ERROR_SETUP_FOOTER_MISSING.'</em>'; #89
	exit;
}

/**
 * Start session.
 */
session_name(md5(BASIC_COOKIE_NAME.$wakkaConfig['wiki_suffix']));
session_start();

// fetch wakka location
/**
 * Fetch wakka location (requested page + parameters)
 * 
 * @todo files action uses POST, everything else uses GET #312
 */
#$wakka = $_REQUEST["wakka"];
$wakka = $_GET['wakka']; #312

/**
 * Remove leading slash.
 */
$wakka = preg_replace("/^\//", "", $wakka);

/**
 * Split into page/method.
 * 
 * Note this splits at the FIRST / so $method may contain one or more slashes;
 * this is not allowed, and ultimately handled in the Method() method. [SEC]
 */
if (preg_match("#^(.+?)/(.*)$#", $wakka, $matches)) list(, $page, $method) = $matches;
else if (preg_match("#^(.*)$#", $wakka, $matches)) list(, $page) = $matches;
//Fix lowercase mod_rewrite bug: URL rewriting makes pagename lowercase. #135
if ((strtolower($page) == $page) && (isset($_SERVER['REQUEST_URI']))) #38
{
 $pattern = preg_quote($page, '/');
 if (preg_match("/($pattern)/i", urldecode($_SERVER['REQUEST_URI']), $match_url))
 {
  $page = $match_url[1];
 }
}

/**
 * Create Wakka object
 */
$wakka =& new Wakka($wakkaConfig);

/** 
 * Check for database access.
 */
if (!$wakka->dblink)
{
	echo '<em class="error">'.ERROR_NO_DB_ACCESS.'</em>';
      exit;
}


/** 
 * Run the engine.
 */
if (!isset($method)) $method='';
$wakka->Run($page, $method);
if (!preg_match("/(xml|raw|mm|grabcode)$/", $method))
{
	$tend = getmicrotime();
	//calculate the difference
	$totaltime = ($tend - $tstart);
	//output result
	print '<div class="smallprint">'.sprintf(PAGE_GENERATION_TIME, $totaltime)."</div>\n</body>\n</html>";
}

$content =  ob_get_contents();
/** 
 * Use gzip compression if possible.
 */
if ( isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strstr ($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') && function_exists('gzencode') ) #38
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

/** 
 * Output the page.
 */
echo $page_output;
?>
