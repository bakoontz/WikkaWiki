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
 */
/**
 * i18n
 */
if(!defined('ERROR_WAKKA_LIBRARY_MISSING')) define ('ERROR_WAKKA_LIBRARY_MISSING','The necessary file "libs/Wakka.class.php" could not be found. To run Wikka, please make sure the file exists and is placed in the right directory!');
if(!defined('ERROR_WRONG_PHP_VERSION')) define ('ERROR_WRONG_PHP_VERSION', '$_REQUEST[] not found. Wakka requires PHP 4.1.0 or higher!');
if(!defined('ERROR_SETUP_FILE_MISSING')) define ('ERROR_SETUP_FILE_MISSING', 'A file of the installer/ upgrader was not found. Please install Wikka again!');
if(!defined('ERROR_SETUP_HEADER_MISSING')) define ('ERROR_SETUP_HEADER_MISSING', 'The file "setup/header.php" was not found. Please install Wikka again!');
if(!defined('ERROR_SETUP_FOOTER_MISSING')) define ('ERROR_SETUP_FOOTER_MISSING', 'The file "setup/footer.php" was not found. Please install Wikka again!');
if(!defined('ERROR_NO_DB_ACCESS')) define ('ERROR_NO_DB_ACCESS', 'The wiki is currently unavailable. <br /><br />Error: Unable to connect to the MySQL database.');
/**
 * Display page generation time in seconds with 4 decimals (%.4f)
 */
if(!defined('PAGE_GENERATION_TIME')) define ('PAGE_GENERATION_TIME', 'Page was generated in %.4f seconds');
if(!defined('WIKI_UPGRADE_NOTICE')) define ('WIKI_UPGRADE_NOTICE', 'This site is currently being upgraded. Please try again later.');

ob_start();

//error_reporting(E_ALL);
error_reporting (E_ALL ^ E_NOTICE);

/**
 * Defines the current Wikka version. Do not change the version number or you will have problems upgrading.
 */
if (!defined('WAKKA_VERSION')) define('WAKKA_VERSION', 'trunk');
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
if (!isset($_REQUEST)) die(ERROR_WRONG_PHP_VERSION);

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
 * Load the configuration.
 */
require_once('libs/Config.class.php');
$buff = new Config;
$wakkaDefaultConfig = get_object_vars($buff);
unset($buff);
$wakkaConfig = array();
if (file_exists('wakka.config.php')) rename('wakka.config.php', 'wikka.config.php');
if (!$configfile = GetEnv('WAKKA_CONFIG')) $configfile = 'wikka.config.php';
if (file_exists($configfile)) include($configfile);

$wakkaConfigLocation = $configfile;
$wakkaConfig = array_merge($wakkaDefaultConfig, $wakkaConfig);

/**
 * Check for locking.
 */
if (file_exists('locked'))
{
	// read password from lockfile
	$lines = file('locked');
	$lockpw = trim($lines[0]);

	// is authentification given?
	if (isset($_SERVER['PHP_AUTH_USER'])) {
		if (!(($_SERVER['PHP_AUTH_USER'] == 'admin') && ($_SERVER['PHP_AUTH_PW'] == $lockpw))) {
			$ask = 1;
		}
	} else {
		$ask = 1;
	}

	if ($ask) {
		header('WWW-Authenticate: Basic realm="'.$wakkaConfig['wakka_name'].' Install/Upgrade Interface"');
		header('HTTP/1.0 401 Unauthorized');
		print WIKI_UPGRADE_NOTICE;
		exit;
    }
}

/**
 * Compare versions, start installer if necessary.
 */
if (!isset($wakkaConfig['wakka_version'])) $wakkaConfig['wakka_version'] = 0;
if ($wakkaConfig['wakka_version'] !== WAKKA_VERSION)
{
	/**
	 * Start installer.
	 */
	$installAction = 'default';
	if (isset($_REQUEST['installAction'])) $installAction = trim($_REQUEST['installAction']);
	if (file_exists('setup/header.php')) include('setup/header.php'); else print '<em>'.ERROR_SETUP_HEADER_MISSING.'</em>';
	if (file_exists('setup/'.$installAction.'.php')) include('setup/'.$installAction.'.php'); else print '<em>'.ERROR_SETUP_FILE_MISSING.'</em>';
	if (file_exists('setup/footer.php')) include('setup/footer.php'); else print '<em>'.ERROR_SETUP_FOOTER_MISSING.'</em>';
	exit;
}

/**
 * Start session.
 */
session_name(md5(BASIC_COOKIE_NAME.$wakkaConfig['wiki_suffix']));
session_start();

/**
 * Fetch wakka location
 */
$wakka = $_REQUEST["wakka"];

/**
 * Remove leading slash.
 */
$wakka = preg_replace("/^\//", "", $wakka);

/**
 * Split into page/method
 */
if (preg_match("#^(.+?)/(.*)$#", $wakka, $matches)) list(, $page, $method) = $matches;
else if (preg_match("#^(.*)$#", $wakka, $matches)) list(, $page) = $matches;
//Fix lowercase mod_rewrite bug: URL rewriting makes pagename lowercase. #135
if (strtolower($page) == $page)
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
if (!preg_match('/(xml|raw|mm|grabcode)$/', $method))
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

/** 
 * Send HTTP headers.
 */
// header("Cache-Control: pre-check=0");
header("Cache-Control: no-cache");
// header("Pragma: ");
// header("Expires: ");

$etag =  md5($content);
header('ETag: '.$etag);

/** 
 * Ticket #152.
 * We no longer send Content-Length header, due to a bug in PHP {@link http://wush.net/trac/wikka/ticket/152}.
 * The Webserver will be clever enough to use chunked transfer encoding.
 * fixme: remove $page_length calculation above.
 */
//header('Content-Length: '.$page_length);
ob_end_clean();

/** 
 * Output the page.
 */
echo $page_output;
?>
