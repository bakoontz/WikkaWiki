<?php
/**
 * The Wikka mainscript.
 * 
 * This file is called each time a request is made from the browser.
 * Most of the core methods used by the engine are located in the Wakka class.
 * @see	Wakka
 * This file was originally written by Hendrik Mans for WakkaWiki
 * and released under the terms of the modified BSD license
 * @see	/docs/WakkaWiki.LICENSE
 *
 * @package		Wikka
 * @subpackage	Core
 * @version		$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @see			/docs/Wikka.LICENSE
 * @filesource
 * 
 * @author	{@link http://www.mornography.de/ Hendrik Mans}
 * @author	{@link http://wikkawiki.org/JsnX Jason Tourtelotte}
 * @author	{@link http://wikkawiki.org/JavaWoman Marjolein Katsma}
 * @author	{@link http://wikkawiki.org/NilsLindenberg Nils Lindenberg}
 * @author	{@link http://wikkawiki.org/DotMG Mahefa Randimbisoa}
 * @author	{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 * 
 * @copyright	Copyright 2002-2003, Hendrik Mans <hendrik@mans.de>
 * @copyright	Copyright 2004-2005, Jason Tourtelotte <wikka-admin@jsnx.com>
 * @copyright	Copyright 2006-2007, {@link http://wikkawiki.org/CreditsPage Wikka Development Team}
 * 
 * @todo	use templating class for page generation;
 * @todo	add phpdoc documentation for configuration array elements;
 */

ob_start();

/**
 * Display PHP errors only, or errors and warnings
 * @todo	make this configurable
 */
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
 * Length to use for generated part of id attribute.
 */
if(!defined('ID_LENGTH')) define('ID_LENGTH',10);

/**
 * Calculate page generation time.
 */
function getmicrotime() {
	list($usec, $sec) = explode(' ', microtime());
	return ((float)$usec + (float)$sec);
}

$tstart = substr(microtime(),11).substr(microtime(),1,9); 

if (!function_exists('mysql_real_escape_string'))
{
/**
 * Escape special characters in a string for use in a SQL statement.
 * 
 * This function is added for back-compatibility with MySQL 3.23.
 * @param	string	$string	the string to be escaped
 * @return	string	a string with special characters escaped
 */
	function mysql_real_escape_string($string)
	{
		return mysql_escape_string($string);
	}
}

/**
 * Include main library if it exists.
 * @see		/libs/Wakka.class.php
 */
if (file_exists('libs'.DIRECTORY_SEPARATOR.'Wakka.class.php'))
{
	require_once('libs'.DIRECTORY_SEPARATOR.'Wakka.class.php');
}
else
{
	die(ERROR_WAKKA_LIBRARY_MISSING); #fatalerror
}
// stupid version check
if (!isset($_REQUEST))
{
	die(ERROR_WRONG_PHP_VERSION); //TODO replace with php version_compare #fatalerror
}
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
			{
				magicQuotesWorkaround($a[$k]);
			}
			else
			{
				$a[$k] = stripslashes($v);
			}
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
 * @todo	remove the parts that are no longer used (config files)
 *			installer should take care of picking up old files - just once
 * @todo	then let Wikka class just include the Config class, and remove
 *			this whole section!
 */
require_once('libs'.DIRECTORY_SEPARATOR.'Config.class.php');
$buff = new Config;
$wakkaDefaultConfig = get_object_vars($buff);
unset($buff);
$wakkaConfig = array();
if (file_exists('wakka.config.php'))
{
	rename('wakka.config.php', 'wikka.config.php');	
}
if (!$configfile = GetEnv('WAKKA_CONFIG'))
{
	$configfile = 'wikka.config.php';
}
if (file_exists($configfile))
{
	include($configfile);
}
$wakkaConfigLocation = $configfile;		// @@@ won't work if doesn't exist
$wakkaConfig = array_merge($wakkaDefaultConfig, $wakkaConfig);
$htaccessLocation = str_replace('\\', '/', dirname(__FILE__)).DIRECTORY_SEPARATOR.'.htaccess';

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
		if (!(($_SERVER['PHP_AUTH_USER'] == 'admin') && ($_SERVER['PHP_AUTH_PW'] == $lockpw)))
		{
			$ask = 1;
		}
	}
	else
	{
		$ask = 1;
	}
	if ($ask)
	{
		header('WWW-Authenticate: Basic realm="'.$wakkaConfig['wakka_name'].' Install/Upgrade Interface"');
		header('HTTP/1.0 401 Unauthorized');
		die(STATUS_WIKI_UPGRADE_NOTICE); #fatalerror
	}
}

/**
 * Compare versions, start installer if necessary.
 */
if (!isset($wakkaConfig['wakka_version']))
{
	$wakkaConfig['wakka_version'] = 0;
}
if ($wakkaConfig['wakka_version'] !== WAKKA_VERSION)
{
	if (file_exists('setup'.DIRECTORY_SEPARATOR.'index.php'))	#89
	{
		include('setup'.DIRECTORY_SEPARATOR.'index.php');		#89
	}
	else
	{
		print '<em>'.ERROR_SETUP_FILE_MISSING.'</em>'; #fatalerror
	}
	die();
}

/**
 * Include language file if it exists.
 * 
 * Language files are bundled under <tt>lang/</tt> in a folder named after their ISO 639-1 code (e.g. 'en' for English).
 */
//sets default language
if (!defined('DEFAULT_LANGUAGE')) define('DEFAULT_LANGUAGE', 'en');
//check if a custom language definition is specified
$wakkaConfig['default_lang'] = (isset($wakkaConfig['default_lang']))? $wakkaConfig['default_lang'] : DEFAULT_LANGUAGE;
//check if language package exists
if (file_exists('lang'.DIRECTORY_SEPARATOR.$wakkaConfig['default_lang'].DIRECTORY_SEPARATOR.$wakkaConfig['default_lang'].'.inc.php'))
{
	require_once('lang'.DIRECTORY_SEPARATOR.$wakkaConfig['default_lang'].DIRECTORY_SEPARATOR.$wakkaConfig['default_lang'].'.inc.php');
}
else
{
	$error_message = 'Language file (lang'.DIRECTORY_SEPARATOR.$wakkaConfig['default_lang'].DIRECTORY_SEPARATOR.$wakkaConfig['default_lang'].'.inc.php) not found!'; //TODO i18n
	#if (($wakkaConfig['default_lang'] != DEFAULT_LANGUAGE) && (file_exists('lang'.DIRECTORY_SEPARATOR.DEFAULT_LANGUAGE.DIRECTORY_SEPARATOR.DEFAULT_LANGUAGE.'.inc.php'))) {}/** @todo: Should try to fall back to default language ... */
	die ($error_message);
}
/**
 * Start session.
 */
session_name(md5(BASIC_COOKIE_NAME.$wakkaConfig['wiki_suffix']));
session_cache_limiter(''); #279
session_start();

/**
 * Fetch wakka location (requested page + parameters)
 * 
 * @todo files action uses POST, everything else uses GET #312
 * @todo use different name - $wakka clashes with $wakka object (which should be #Wakka)
 */
$wakka = $_GET['wakka']; #312

/**
 * Remove leading slash.
 * @todo use different name - $wakka clashes with $wakka object (which should be #Wakka)
 */
$wakka = preg_replace("/^\//", "", $wakka);

/**
 * Extract pagename and handler from URL
 * @todo use different name - $wakka clashes with $wakka object (which should be #Wakka)
 */
if (preg_match("#^(.+?)/(.*)$#", $wakka, $matches))
{
	list(, $page, $handler) = $matches;
}
else if (preg_match("#^(.*)$#", $wakka, $matches))
{
	list(, $page) = $matches;
}
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
 * @todo use name with Capital for object; also clashes with $wakka (above) now
 */
if (version_compare(phpversion(),'5','>='))		// >= PHP 5?
{
	$wakka =  new Wakka($wakkaConfig);			// [558] / #496 - comment 3
}
else
{
	$wakka =& new Wakka($wakkaConfig);			// reverting [558] see #496 - comment 4
}

/**
 * Check for database access.
 * @todo use name with Capital for object; also clashes with $wakka (above) now
 */
if (!$wakka->dblink)
{
	// set up template variables
	$wiki_unavail = STATUS_WIKI_UNAVAILABLE; #FatalErrorAfterLangFileIncluded
	$err_no_db = ERROR_NO_DB_ACCESS; #FatalErrorAfterLangFileIncluded

	// define template @@@ FIXME: make more structural code JW
	$template = <<<TPLERRDBACCESS
<em class="error">{$err_wiki_unavail}{$err_no_db}</em>
TPLERRDBACCESS;

	// print template
	die($template);
}

/** 
 * Run the engine.
 * @todo use name with Capital for object; also clashes with $wakka (above) now
 */
if (!isset($handler))
{
	$handler='';
}
$wakka->Run($page, $handler);
/**
 * Calculate microtime
 * @todo move handler check to handler configuration
 */
if (!preg_match('/(xml|raw|mm|grabcode|mindmap_fullscreen)$/', $handler))
{
	$tend = substr(microtime(),11).substr(microtime(),1,9); 
	//calculate the difference
	$totaltime = ($tend - $tstart);
	//output result
	print '<div class="smallprint">'.sprintf(PAGE_GENERATION_TIME, $totaltime)."</div>\n</body>\n</html>";
}

$content =  ob_get_contents();
/** 
 * Use gzip compression if possible.
 * @todo	use config value to optionally turn off gzip-encoding here #541
 */
if ( isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strstr ($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') && function_exists('gzencode') ) #38
{
	// Tell the browser the content is compressed with gzip
	header('Content-Encoding: gzip');
	$page_output = gzencode($content);
	$page_length = strlen($page_output);
}
else
{
	$page_output = $content;
	$page_length = strlen($page_output);
}

$etag =  md5($content);
header('ETag: '.$etag);

if (!isset($wakka->do_not_send_anticaching_headers) || (!$wakka->do_not_send_anticaching_headers)) #279
{
	header('Expires: Thu, 19 Nov 1981 08:52:00 GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
	header('Pragma: no-cache');
}

/**
 * We no longer send Content-Length header, due to a bug in PHP {@link http://wush.net/trac/wikka/ticket/152}.
 * The Webserver will be clever enough to use chunked transfer encoding. #152
 * @todo remove $page_length calculation above.
 */
//header('Content-Length: '.$page_length);
ob_end_clean();

/**
 * Output the page.
 */
echo $page_output;
?>