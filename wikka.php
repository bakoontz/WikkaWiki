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
 * @uses	getmicrotime()	for determining page generation time
 * @uses	magicQuotesWorkaround()	to overcome magic quotes that may be imposed on a system
 * @uses	instantiate()	for a version-independent method to instantiate a class
 *
 * @todo	use templating class for page generation;
 * @todo	add phpdoc documentation for configuration array elements; @see Config !
 */

ob_start();

/**
 * Display PHP errors only, or errors and warnings.
 *
 * @todo	make this configurable
 */
//error_reporting(E_ALL);
error_reporting (E_ALL ^ E_NOTICE);

/**
 * Defines the current Wikka version. Do not change the version number or you will have problems upgrading.
 */
if (!defined('WAKKA_VERSION')) define('WAKKA_VERSION', 'trunk');
/**#@+
 * String constant used as default. May be made a configurable value.
 */
/**
 * Defines the default cookie name.
 */
if (!defined('BASIC_COOKIE_NAME')) define('BASIC_COOKIE_NAME', 'Wikkawiki');
/**
 * Defines the default language.
 */
if (!defined('DEFAULT_LANGUAGE')) define('DEFAULT_LANGUAGE', 'en');
/**#@-*/
/**#@+
 * String constant defining a directory where a specific type of components can be found.
 *
 * @todo	more elements need to be defined (if not configurable!)
 */
/**
 * Defines the default path where libraries are stored.
 *
 * May be overridden to share libraries between installations.
 */
if (!defined('WIKKA_LIBRARY_PATH')) define('WIKKA_LIBRARY_PATH','libs');
if (!defined('WIKKA_SETUP_PATH')) define('WIKKA_SETUP_PATH','setup');
if (!defined('WIKKA_LANG_PATH')) define('WIKKA_LANG_PATH','lang');
/**#@-*/

/**#@+
 * Numeric constant used as default. May be made a configurable value.
 */
/**
 * Length to use for generated part of id attribute.
 * @todo move to Wakka class
 */
if (!defined('ID_LENGTH')) define('ID_LENGTH',10);				// @@@ maybe make length configurable
/**#@-*/

// @@@	maybe include this bit from a file in the installation directory?
//		that would make it easier to edit, and keep this fuile "cleaner"
/**#@+
 * String constant used override for a standard Wikka path.
 *
 * 'LOCAL' in this context means applicable to this particular instance of Wikka,
 * although it these constants can actually be used to share system files between
 * different installations, by pointing this all at the same location in all instances.
 *
 * May be used for sites where local configuration is (to be) stored outside the
 * web root or files are shared between different installations on the same server.
 */
/**
 * Alternative path for local configuration file.
 *
 * If you need to use this installation with a site configuration file outside the
 * installation directory uncomment the following line and adapt it to reflect
 * the (filesystem) path to where your site configuration file is located.
 * This would make it possible to store the configuration file outside of the
 * webroot, or to share one configuration file between several Wikka Wiki
 * installations.
 *
 * This replaces the use of the environment variable WAKKA_CONFIG for security
 * reasons. [SEC]
 * @todo		also allow an override for the path to the Class that (now) defines the *default* settings: should be shareable between installations, for instance!
 */
#if (!defined('LOCAL_CONFIG')) define('LOCAL_CONFIG','path/to/your/wikka.config.php');
/**
 * Alternative path where (all) libaries are stored.
 *
 * The default is 'libs' within the installation directory. The path must be
 * a (filesystem) path for a directory, and must <b>not</b> end in a slash.
 * Use this only if <b>all</b> your libraries (including the default configuration
 * and the main Wakka class are to be found in a different location.
 *
 * Note that the location of (some?) other directories can be defined in the
 * configuration - but that configuration must be found first.
 */
#if (!defined('LOCAL_LIBRARY_PATH')) define('LOCAL_LIBRARY_PATH','path/to/your/libs')
/**
 * Alternative path for default configuration file.
 *
 * If you define a LOCAL_LIBRARY_PATH you don't need this unless Config.class.php
 * is somewhere else again. If relocate (or share) only Config.class.php, use
 * this constant to define its full (filesystem) path.
 */
#if (!defined('LOCAL_DEFAULT_CONFIG')) define('LOCAL_DEFAULT_CONFIG','path/to/your/Config.class.php');
/**#@-*/

// More intelligent version check, more intelligently placed ;)
// Basically if we are going to fail, we want to do that as soon as possible.
if (!function_exists('version_compare'))
{
	die(ERROR_WRONG_PHP_VERSION);		# fatalerror
}
elseif (version_compare(phpversion(),'4.1.0','<'))		// < PHP 4.1.0?
{
	die(ERROR_WRONG_PHP_VERSION);		# fatalerror
}
// @@@ similar check here for MySQL

// derive paths, taking optional overrides into account
// this is an (improved and extended) version of the method introduced in 1.1.6.3 to avoid GetEnv #470
$wikka_library_path = (defined('LOCAL_LIBARY_PATH')) ? LOCAL_LIBARY_PATH : WIKKA_LIBRARY_PATH;
$default_config = (defined('LOCAL_DEFAULT_CONFIG')) ? LOCAL_DEFAULT_CONFIG : $wikka_library_path.DIRECTORY_SEPARATOR.'Config.class.php';
// local configuration by default in installation directory
$configfile = (defined('LOCAL_CONFIG')) ? LOCAL_CONFIG : 'wikka.config.php';

// include the "compatibility functions
require_once $wikka_library_path.'Compatibility.lib.php';


// start page generation timer
$tstart = getmicrotime(TRUE);

/**
 * Include main library if it exists.
 *
 * @see		/libs/Wakka.class.php
 */
if (file_exists($wikka_library_path.DIRECTORY_SEPARATOR.'Wakka.class.php'))
{
	require_once $wikka_library_path.DIRECTORY_SEPARATOR.'Wakka.class.php';
}
else
{
	die(ERROR_WAKKA_LIBRARY_MISSING);	#fatalerror
}

// stupid version check - see above ;)
//if (!isset($_REQUEST))
//{
//	die(ERROR_WRONG_PHP_VERSION);
//}

set_magic_quotes_runtime(0);
if (get_magic_quotes_gpc())
{
	magicQuotesWorkaround($_POST);
	magicQuotesWorkaround($_GET);
	magicQuotesWorkaround($_COOKIE);
}

/**
 * Get the default configuration.
 *
 * @todo	this hard-coded path to the class should also be overrideable with an alternative one (like what we had with the (now replaced) GetEnv)
 */
#require_once('libs'.DIRECTORY_SEPARATOR.'Config.class.php');
require_once $default_config;
#$buff = new Config;		// pass by ref in PHP4!
$buff = instantiate('Config');
$wakkaDefaultConfig = get_object_vars($buff);
unset($buff);

/**
 * Load the configuration.
 *
 * Here, the default configuration is merged with any already-existing site configuration.
 * If a new install or upgrade is needed, the installer will ultimately write an (updated) site configuration.
 *
 * @todo	make a more structural replacement for GetEnv() than the preliminary one we have now
 */
$wakkaConfig = array();
if (file_exists('wakka.config.php'))
{
	rename('wakka.config.php', 'wikka.config.php');
}
/*
if (!$configfile = GetEnv('WAKKA_CONFIG'))
{
	$configfile = 'wikka.config.php';
}
if (file_exists($configfile))
{
	include($configfile);
}
$wakkaConfigLocation = $configfile;
$wakkaConfig = array_merge($wakkaDefaultConfig, $wakkaConfig);
*/
if (file_exists($configfile))
{
	include $configfile;
}
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
	if (isset($_SERVER['PHP_AUTH_USER']))
	{
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
	$wakkaConfigLocation = $configfile;		// record (intended) config location for the installer
	$htaccessLocation = str_replace('\\', '/', dirname(__FILE__)).DIRECTORY_SEPARATOR.'.htaccess';
	#if (file_exists('setup'.DIRECTORY_SEPARATOR.'index.php'))	#89
	if (file_exists(WIKKA_SETUP_PATH.DIRECTORY_SEPARATOR.'index.php'))	#89
	{
		// run the installer
		#include 'setup'.DIRECTORY_SEPARATOR.'index.php';		#89
		include WIKKA_SETUP_PATH.DIRECTORY_SEPARATOR.'index.php';		#89
	}
	else
	{
		// installer can not be run
		print '<em>'.ERROR_SETUP_FILE_MISSING.'</em>'; #fatalerror
	}
	die();
}

/**
 * Include language file if it exists.
 *
 * Language files are bundled under <tt>lang/</tt> (default) in a folder named
 * after their ISO 639-1 code (e.g. 'en' for English).
 *
 * @todo: Should try to fall back to default language ...
 */
//check if a custom language definition is specified
$wakkaConfig['default_lang'] = (isset($wakkaConfig['default_lang'])) ? $wakkaConfig['default_lang'] : DEFAULT_LANGUAGE;
//check if language package exists
#if (file_exists('lang'.DIRECTORY_SEPARATOR.$wakkaConfig['default_lang'].DIRECTORY_SEPARATOR.$wakkaConfig['default_lang'].'.inc.php'))
if (file_exists(WIKKA_LANG_PATH.DIRECTORY_SEPARATOR.$wakkaConfig['default_lang'].DIRECTORY_SEPARATOR.$wakkaConfig['default_lang'].'.inc.php'))
{
	#require_once('lang'.DIRECTORY_SEPARATOR.$wakkaConfig['default_lang'].DIRECTORY_SEPARATOR.$wakkaConfig['default_lang'].'.inc.php');
	require_once WIKKA_LANG_PATH.DIRECTORY_SEPARATOR.$wakkaConfig['default_lang'].DIRECTORY_SEPARATOR.$wakkaConfig['default_lang'].'.inc.php';
}
else
{
	#$error_message = 'Language file (lang'.DIRECTORY_SEPARATOR.$wakkaConfig['default_lang'].DIRECTORY_SEPARATOR.$wakkaConfig['default_lang'].'.inc.php) not found!'; //TODO i18n
	$error_message = 'Language file ('.WIKKA_LANG_PATH.DIRECTORY_SEPARATOR.$wakkaConfig['default_lang'].DIRECTORY_SEPARATOR.$wakkaConfig['default_lang'].'.inc.php) not found!'; //TODO i18n
	// @@@ attempt fallback if unsuccessful, create a different error message and *then* die
	#if (($wakkaConfig['default_lang'] != DEFAULT_LANGUAGE) && (file_exists('lang'.DIRECTORY_SEPARATOR.DEFAULT_LANGUAGE.DIRECTORY_SEPARATOR.DEFAULT_LANGUAGE.'.inc.php'))) {}/** @todo: Should try to fall back to default language ... */
	die($error_message);
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
 *
 * @todo	use different name - $wakka clashes with $wakka object (which should be #Wakka)
 */
$wakka = preg_replace("/^\//", "", $wakka);

/**
 * Extract pagename and handler from URL
 *
 * Note this splits at the FIRST / so $method may contain one or more slashes;
 * this is not allowed, and ultimately handled in the Method() method. [SEC]
 *
 * @todo	use different name - $wakka clashes with $wakka object (which should be #Wakka)
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
 * Create Wakka object.
 *
 * @todo	use name with Capital for object; also clashes with $wakka (above) now
 */
/*
if (version_compare(phpversion(),'5','>='))		// >= PHP 5?
{
	$wakka =  new Wakka($wakkaConfig);			// [558] / #496 - comment 3
}
else
{
	$wakka =& new Wakka($wakkaConfig);			// reverting [558] see #496 - comment 4
}
*/
$wakka = instantiate('Wakka',$wakkaConfig);

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
 *
 * @todo	use name with Capital for object; also clashes with $wakka (above) now
 */
if (!isset($handler))
{
	$handler='';
}
$wakka->Run($page, $handler);
/**
 * Calculate elapsed microtime
 *
 * @todo	move handler check to handler configuration
 */
if (!preg_match('/(xml|raw|mm|grabcode|mindmap_fullscreen)$/', $handler))
{
	// get new microtime
	$tend = getmicrotime(TRUE);
	//calculate the difference
	$totaltime = ($tend - $tstart);
	//output result	/// @@@ use paragraph
	print '<div class="smallprint">'.sprintf(PAGE_GENERATION_TIME, $totaltime)."</div>\n</body>\n</html>";
}

$content =  ob_get_contents();
/**
 * Use gzip compression if possible.
 *
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

$etag = md5($content);
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