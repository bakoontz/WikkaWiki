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
 * @version		$Id: wikka.php 1293 2009-01-12 17:06:21Z DarTar $
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
 * @author	{@link http://wikkawiki.org/BrianKoontz Brian Koontz}
 * @author	{@link http://www.tormodh.net/ Tormod Haugen}
 *
 * @copyright	Copyright 2002-2003, Hendrik Mans <hendrik@mans.de>
 * @copyright	Copyright 2004-2005, Jason Tourtelotte <wikka-admin@jsnx.com>
 * @copyright	Copyright 2006-2009, {@link http://wikkawiki.org/CreditsPage Wikka Development Team}
 *
 * @uses	getmicrotime()		for determining page generation time
 * @uses	magicQuotesWorkaround()	to overcome magic quotes that may be imposed
 *				on a system
 * @uses	instantiate()		for a version-independent method to instantiate a class
 * @uses	getMysqlVersion()	to retrieve MySQL version for a requirements check
 *
 * @todo	use templating class for page generation;
 */

require_once('libs'.DIRECTORY_SEPARATOR.'Compatibility.lib.php');

// ---------------------- DEBUGGING AND ERROR REPORTING -----------------------
/**
 * Controls generation of debug messages.
 *
 * May cause quirks mod in browser!
 * By using a 'debug' key file during development (and keeping this out of the
 * repository!) it's is simple to trigger debug messages without needing to edit
 * (this) code.
 */
$debug = (file_exists('debug')) ? TRUE : FALSE;
/**
 * Controls error_reporting mode: E_ALL if TRUE, E_ALL ^ E_NOTICE if FALSE.
 *
 * By using a 'errors' key file during development (and keeping this out of the
 * repository!) it's is simple to test a system without needing to edit (this)
 * code.
 */
$track_errors = (file_exists('errors')) ? TRUE : FALSE;
ob_start();							// need to pick up tracing messages
/**
 * Interpret debugging and development/production modes.
 */
if ($debug || $track_errors)
{
	error_reporting(E_ALL);				// always on for debug mode
}
else
{
	error_reporting(E_ALL ^ E_NOTICE);	// production mode
}
// -------------------- END DEBUGGING AND ERROR REPORTING ---------------------

// ---------------------------- VERSIONING ------------------------------------
include_once('./version.php');

/**#@-*/ 
/** 
 * Defines the current Wikka patch level. This should be 0 by default,  
 * and does not need to be changed for major/minor releases. 
 */ 
if(!defined('WIKKA_PATCH_LEVEL')) define('WIKKA_PATCH_LEVEL', '1'); 

// ----------------------------- BASIC CONSTANTS -------------------------------
/**
 * Defines the basic name the session name will be derived from.
 */
if (!defined('BASIC_SESSION_NAME'))		define('BASIC_SESSION_NAME', 'Wikkawiki');

/**
 * Path where the Wikka installer is located.
 */
if (!defined('WIKKA_SETUP_PATH'))		define('WIKKA_SETUP_PATH', 'setup');

/**#@+
 * Minimum version requirement.
 */
if (!defined('MINIMUM_PHP_VERSION'))	define('MINIMUM_PHP_VERSION', '4.1');
if (!defined('MINIMUM_MYSQL_VERSION'))	define('MINIMUM_MYSQL_VERSION', '3.23');	// 3.23.23 referred to in commented-out code
/**#@-*/

// The following error messages are in English only, because at the point these
// messages need to be shown, no language preference may have been set yet, no
// language file loaded yet, or even the language file could not be loaded(!).

/**#@+
 * Error message constant.
 */
define('ERROR_WRONG_PHP_VERSION', 'Wikka requires PHP %s or higher!');	// %s - version number
define('ERROR_LANGUAGE_FILE_MISSING', 'Language file (%s) not found! Please add the file.'); // %s - path to default language file
/**#@-*/
// --------------------------- END BASIC CONSTANTS -----------------------------


// --------------------------- VERSION CHECK (PHP) -----------------------------
// More intelligent version check, more intelligently placed ;)
// Basically if we are going to fail, we want to do that as soon as possible.
// BUT: to have the error message localized, the desied language file must have
// been loaded already; and that in turn depends on a bunch of other things...
//
// So here we use one of the few exceptions: an error message in English because
// we don't have a language file loaded yet. (The installer needs to start in
// English anyway, and the error message when the language file *cannot* be loaded
// will also have to be English!)
//
// Given the above, it doesn't need any of the constant definitions, except the
// error message and version constants, so it can go way up now!
if ($debug) echo "PHP version check...<br/>\n";
if (!function_exists('version_compare') ||
	version_compare(phpversion(),MINIMUM_PHP_VERSION,'<')	// < PHP minimum version??
   )
{
	$php_version_error = sprintf(ERROR_WRONG_PHP_VERSION,MINIMUM_PHP_VERSION);
	die($php_version_error);		# fatalerror	!!! default error in English
}
// ------------------------- END VERSION CHECK (PHP) ---------------------------

// ---------------------- DEFINE URL DOMAIN / PATH -----------------------------
/**#@+*
 * URL or URL component, derived just once for later usage.
 */
// first derive domain, path and base_url, as well as cookie path just once
// so they are ready for later use.
// detect actual scheme (might be https!)	@@@ TEST
// please recopy modif into setup/test/test-mod-rewrite.php
$scheme = ((isset($_SERVER['HTTPS'])) && !empty($_SERVER['HTTPS']) && 'off' != $_SERVER['HTTPS']) ? 'https://' : 'http://';
$server_port = ':'.$_SERVER['SERVER_PORT'];
if ((('http://' == $scheme) && (':80' == $server_port)) || (('https://' == $scheme) && (':443' == $server_port)))
{
	$server_port = '';
}
/**
 * URL fragment consisting of scheme + domain part.
 * Represents the domain URL where the current instance of Wikka is located.
 *
 * @var string
 */
if (!defined('WIKKA_BASE_DOMAIN_URL')) define('WIKKA_BASE_DOMAIN_URL', $scheme.$_SERVER['SERVER_NAME'].$server_port);
/**
 * URL fragment consisting of a path component.
 * Points to the instance of Wikka within {@link WIKKA_BASE_DOMAIN_URL}.
 *
 * @var string
 */
define('WIKKA_BASE_URL_PATH', preg_replace('/wikka\\.php/', '', $_SERVER['SCRIPT_NAME']));
/**
 * Base URL consisting of {@link WIKKA_BASE_DOMAIN_URL} and {@link WIKKA_BASE_URL_PATH} concatenated.
 * Ready to append a relative path to a "static" file to.
 *
 * @var string
 */
define('WIKKA_BASE_URL', WIKKA_BASE_DOMAIN_URL.WIKKA_BASE_URL_PATH);
/**
 * Path to be used for cookies.
 * Derived from {@link WIKKA_BASE_URL_PATH}
 *
 * @var string
 */
define('WIKKA_COOKIE_PATH', ('/' == WIKKA_BASE_URL_PATH) ? '/' : substr(WIKKA_BASE_URL_PATH, 0, -1)); 
/**
 * Default number of hours after which a permanent cookie is to expire: corresponds to 90 days.
 */
if (!defined('DEFAULT_COOKIE_EXPIRATION_HOURS')) define('DEFAULT_COOKIE_EXPIRATION_HOURS',90 * 24);

/**#@-*/
// ----------------------- END URL DOMAIN / PATH -------------------------------

// ------------------------ DEFINE & DERIVE CORE PATHS -------------------------
// Universal path divider; path parser also recognizes Unix-style
// dividers (:) and Windows-style dividers (;)
define('PATH_DIVIDER', ',');

if ($debug) echo "default filesystem paths...<br/>\n";

/**
  * Default filesystem path for the <b>site</b> configuration
  * <b>file</b>.
  */
if(!defined('DEFAULT_SITE_CONFIGFILE')) define ('DEFAULT_SITE_CONFIGFILE', 'wikka.config.php');
// For backwards compatibility with existing code...probably needs to
// be removed at some point
if(!defined('SITE_CONFIGFILE')) define ('SITE_CONFIGFILE', DEFAULT_SITE_CONFIGFILE);

if ($debug) echo "core component paths...<br/>\n";

/**
 * Effective path to the Wikka code library which contains core components.
 */
if (!defined('WIKKA_LIBRARY_PATH')) define('WIKKA_LIBRARY_PATH', 'libs');

/**
 * Effective path to the Wikka language library which contains language files
 * and localized system content.
 */
if (!defined('WIKKA_LANG_PATH')) define('WIKKA_LANG_PATH', 'lang');

/**
  * Default <b>directory</b> where actions bundled with Wikka are
  * stored.
  */
if(!defined('DEFAULT_ACTION_PATH')) define('DEFAULT_ACTION_PATH', 'plugins/actions'.PATH_DIVIDER.'actions');

/**
  * Default <b>directory</b> where handlers bundled with Wikka are
  * stored.
  */
if(!defined('DEFAULT_HANDLER_PATH')) define('DEFAULT_HANDLER_PATH', 'plugins/handlers'.PATH_DIVIDER.'handlers');

/**
  * Default <b>directory</b> where formatters bundled with Wikka are
  * stored.
  */
if(!defined('DEFAULT_FORMATTER_PATH')) define('DEFAULT_FORMATTER_PATH', 'plugins/formatters'.PATH_DIVIDER.'formatters');

/**
  * Default <b>directory</b> where templates bundled with Wikka are
  * stored.
  */
if(!defined('DEFAULT_TEMPLATE_PATH')) define('DEFAULT_TEMPLATE_PATH', 'plugins/templates'.PATH_DIVIDER.'templates');

/**
  * Directory for 3rd-party components
  */
if(!defined('DEFAULT_3RDPARTY_PATH')) define('DEFAULT_3RDPARTY_PATH', '3rdparty');

/** Default <b>directory</b> where 3rdparty core components bundled
  * with Wikka are stored.  These components are required for basic
  * Wikka functionality.
  */
if(!defined('DEFAULT_3RDPARTY_CORE_PATH')) define('DEFAULT_3RDPARTY_CORE_PATH', DEFAULT_3RDPARTY_PATH.DIRECTORY_SEPARATOR.'core');

/** Default <b>directory</b> where 3rdparty plugin components bundled
  * with Wikka are stored.  These components are optional and extend
  * Wikka functionality.
  */
if(!defined('DEFAULT_3RDPARTY_PLUGIN_PATH')) define('DEFAULT_3RDPARTY_PLUGIN_PATH', DEFAULT_3RDPARTY_PATH.DIRECTORY_SEPARATOR.'plugin');

/** 
  * Default <b>directory</b> for the FeedCreator 3rd party component.
  */
if(!defined('DEFAULT_FEEDCREATOR_PATH')) define('DEFAULT_FEEDCREATOR_PATH', DEFAULT_3RDPARTY_CORE_PATH.DIRECTORY_SEPARATOR.'feedcreator');

/** 
  * Default <b>directory</b> for the SafeHTML 3rd party component.
  */
if(!defined('DEFAULT_SAFEHTML_PATH')) define('DEFAULT_SAFEHTML_PATH', DEFAULT_3RDPARTY_CORE_PATH.DIRECTORY_SEPARATOR.'safehtml');

/** 
  * Default <b>directory</b> for the optional GeSHi 3rd party plugin component.
  */
if(!defined('DEFAULT_GESHI_PATH')) define('DEFAULT_GESHI_PATH', DEFAULT_3RDPARTY_PLUGIN_PATH.DIRECTORY_SEPARATOR.'geshi');

/** 
  * Default <b>directory</b> for the language files for the GeSHi 3rd
  * party plugin component.
  */
if(!defined('DEFAULT_GESHI_LANG_PATH')) define('DEFAULT_GESHI_LANG_PATH', DEFAULT_GESHI_PATH.DIRECTORY_SEPARATOR.'geshi');

/** 
  * Default <b>directory</b> for the optional Onyx-RSS 3rd party
  * plugin component.
  */
if(!defined('DEFAULT_ONYX_PATH')) define('DEFAULT_ONYX_PATH', DEFAULT_3RDPARTY_PLUGIN_PATH.DIRECTORY_SEPARATOR.'onyx-rss');

  /**
   * <b>URL path component</b> pointing to the location of the WikiEdit scripts.
   * This path will be extended by the system with the file name for each of
   * the required scripts.
   */
if(!defined('DEFAULT_WIKIEDIT_URIPATH')) define('DEFAULT_WIKIEDIT_URIPATH', filesys2uri(DEFAULT_3RDPARTY_PLUGIN_PATH).DIRECTORY_SEPARATOR.'wikiedit');

  /**
   * <b>URL path component</b> for the FreeMind display applet.
   * This path will be extended by the system with the file name for the
   * applet's jar archive.
   */
if(!defined('DEFAULT_FREEMIND_URIPATH')) define('DEFAULT_FREEMIND_URIPATH', filesys2uri(DEFAULT_3RDPARTY_PLUGIN_PATH).DIRECTORY_SEPARATOR.'freemind');

// ------------------- COMPONENT PATHS DEFINED --------------------

// -------------------------------- START TIMER --------------------------------
if ($debug) echo "start timer...<br/>\n";
// --- this requires a function from the Compatibility library, so it must come
//     at least after loading that library
// Now that all paths and other basic settings are known, start page generation
// timer.
$tstart = getmicrotime(TRUE);
// ------------------------------- TIMER STARTED -------------------------------

// ----------------------------- GATHER CONFIGURATION --------------------------
if ($debug) echo "gather configuration...<br/>\n";
// --- this requires a function from the Compatibility library, so it must come
//     at least after loading that library; it also uses the effective locations
//     for the configuration files, so these locations must have been derived.
/**
 * 1. Get the default configuration.
 */
require_once('libs'.DIRECTORY_SEPARATOR.'Config.class.php');
$DefaultConfig = instantiate('Config');
$wakkaDefaultConfig = get_object_vars($DefaultConfig);
unset($DefaultConfig);
// . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .
/**
 * 2. Load and update the (current) user configuration if it exists.
 */
$wakkaConfig = array();	// empty array in case there's no user configuration yet

// Get any inherited configuration from Wakka
if (file_exists('wakka.config.php'))
{
	rename('wakka.config.php', 'wikka.config.php');
}
if(file_exists(SITE_CONFIGFILE))
{
	include SITE_CONFIGFILE;
}

// migrate some old to new variable names (should come before merge!)
//TODO move these checks to a directive file to be used by the installer/upgrader, #97
if (isset($wakkaConfig['action_path']) && !isset($wakkaConfig['wikka_action_path']))
{
	$wakkaConfig['wikka_action_path'] = $wakkaConfig['action_path'];
	unset($wakkaConfig['action_path']); //since 1.1.7
}
if (isset($wakkaConfig['handler_path']) && !isset($wakkaConfig['wikka_handler_path']))
{
	$wakkaConfig['wikka_handler_path'] = $wakkaConfig['handler_path'];
	unset($wakkaConfig['handler_path']); //since 1.1.7
}

// remove obsolete config settings (should come before merge!)
//TODO move these checks to a directive file to be used by the installer/upgrader, #97
if (isset($wakkaConfig['header_action']))
{
	unset($wakkaConfig['header_action']); //since 1.1.7
}
if (isset($wakkaConfig['footer_action'])) //since 1.1.7
{
	unset($wakkaConfig['footer_action']);
}
if (isset($wakkaConfig['stylesheet']))
{
	unset($wakkaConfig['stylesheet']); //since 1.2
}
if (isset($wakkaConfig['navigation_links']))
{
	unset($wakkaConfig['navigation_links']); //since 1.2
}
if (isset($wakkaConfig['logged_in_navigation_links']))
{
	unset($wakkaConfig['logged_in_navigation_links']); //since 1.2
}

// . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .
/**
 * 3. Now, the default configuration is merged with any already-existing (and
 * maybe updated) site configuration.
 * If a new install or upgrade is needed, the installer will ultimately write an
 * (updated) site configuration.
 */
$wakkaConfig = array_merge($wakkaDefaultConfig, $wakkaConfig);
//--------------------- (DEFAULT) CONFIGURATION READY --------------------------


// ------------------------------ LANGUAGE FILE --------------------------------
// --- In an existing installation, the actual language choice is picked up from
//     the configuration; otherwise an initial 'default_language' setting is
//     created using the system language default.
// --- Dependencies:
//     - CONFIG_DEFAULT_LANGUAGE and WIKKA_LANG_PATH must have been defined
//     - Error message to show when no language file could be loaded must
//       have been defined
// @@@ maybe NOT load this when we don't have a configuration file (and thus
// need to do a NEW install)? isset($wakkaConfig['default_lang']) - it would
// prevent dynamic language switching during installation...?
if ($debug) echo "get language file...<br/>\n";
/**
 * Include language file if one exists.
 *
 * Language files are bundled under <b>lang/</b> in a
 * folder named after their ISO 639-1 code (e.g. 'en' for English).
 *
 * Other language files that exist in the language folder will be
 * included as well (useful for plugins that define their own
 * language strings).
 */
$default_lang = $wakkaConfig['default_lang'];
$lang_base_dir = 'lang'; 
$fallback_lang	= 'en';			// should always be available
$default_language_file  = $lang_base_dir.DIRECTORY_SEPARATOR.$default_lang.DIRECTORY_SEPARATOR.$default_lang.'.inc.php';
$fallback_language_file = $lang_base_dir.$fallback_lang.DIRECTORY_SEPARATOR.$fallback_lang.'.inc.php';
$language_file_not_found = sprintf(ERROR_LANGUAGE_FILE_MISSING,$default_language_file);
// load language package if it exists
if (file_exists($default_language_file))
{
	/**
	 * Check for other language files (i.e., for plugins)
	 */
	$lang_dir = $lang_base_dir.DIRECTORY_SEPARATOR.$default_lang;
	$hdir = opendir($lang_dir);
	if(FALSE !== $hdir)
	{
		while(FALSE !== ($file = readdir($hdir)))
		{
			if(0 == preg_match('/\.inc\.php$/', $file))
			{
				continue; // .inc.php files only!
			}
			require_once $lang_dir.DIRECTORY_SEPARATOR.$file;
		}
	}

	/**
	 * Language file for configured default language.
	 */
	require_once $default_language_file;
}
elseif (file_exists($fallback_language_file))
{
	/**
	 * Check for other language files (i.e., for plugins)
	 */
	$lang_dir = $lang_base_dir.DIRECTORY_SEPARATOR.$fallback_lang;
	$hdir = opendir($lang_dir);
	if(FALSE !== $hdir)
	{
		while(FALSE !== ($file = readdir($hdir)))
		{
			if(0 == preg_match('/\.inc\.php$/', $file))
			{
				continue; // .inc.php files only!
			}
			require_once $lang_dir.DIRECTORY_SEPARATOR.$file;
		}
	}

	/**
	 * Language file for system default language: fallback.
	 */
	require_once $fallback_language_file;	// silent fallback
}
else
{
	die($language_file_not_found);	# fatalerror - local error message in English because we don't _have_ a language file(!)
}

/*
 * Defines the (configurable) default language. Wikka will attempt to
 * oad the corresponding language file.  This value is directly used
 * here in wikka.php but also used as the default value in the default
 * configuration file.
 */
if (!defined('DEFAULT_FALLBACK_LANGUAGE')) define('DEFAULT_FALLBACK_LANGUAGE' , $fallback_lang);

// ---------------------------- END LANGUAGE FILE ------------------------------


//------------------------------ ENVIRONMENT -----------------------------------
if ($debug) echo "handle magic quotes...<br/>\n";
// --- needed (just) before we start looking at cookies and get / post parameters
set_magic_quotes_runtime(0);
if (get_magic_quotes_gpc())
{
	magicQuotesWorkaround($_POST);
	magicQuotesWorkaround($_GET);
	magicQuotesWorkaround($_COOKIE);
}
//---------------------------- END ENVIRONMENT ---------------------------------


// ----------------------------- MAINTENANCE LOCK ------------------------------
if ($debug) echo "maintenance lock (if needed)...<br/>\n";
// --- requires SOME language file to be loaded with error message definition
// --- BUT requires a USER-defined config value, so installer needs to have run
//     at some time - IOW we need an existing installation for this to work
/**
 * Check for locking.
 */
if (file_exists('locked'))
{
	// init
	$ask = 0;

	// read password from lockfile
	$lines = file('locked');
	$lockpw = trim($lines[0]);

	// is authentication given?
	if (isset($_SERVER['PHP_AUTH_USER']))
	{
		if (!(($_SERVER['PHP_AUTH_USER'] == 'admin') && ($_SERVER['PHP_AUTH_PW'] == $lockpw)))
		{
			$ask = 1;
		}
		// authenticated
	}
	else
	{
		$ask = 1;
	}

	// request authentication
	if ($ask)
	{
		header('WWW-Authenticate: Basic realm="'.$wakkaConfig['wakka_name'].' Install/Upgrade Interface"');
		header('HTTP/1.0 401 Unauthorized');
		die(STATUS_WIKI_UPGRADE_NOTICE); #fatalerror
	}
}
// --------------------------- END MAINTENANCE LOCK ----------------------------


// ---------------------------------- INSTALLER --------------------------------
if ($debug) echo "installer (if needed)...<br/>\n";
// --- requires at least default configuration to be present
//     constants WAKKA_VERSION, SITE_CONFIGFILE and WIKKA_SETUP_PATH must have been defined
//     language file must be loaded for error message
/**
 * Compare versions, start installer if necessary.
 */
if (!isset($wakkaConfig['wakka_version']))
{
	$wakkaConfig['wakka_version'] = 0;
}
$version1 = array();
$version2 = array();
// We are interested in the version root (i.e., given "trunk-r1009" or
// "trunk_r1009", we only want to compare against "trunk"). 
$version1 = preg_split('/-|_/', WAKKA_VERSION);
// There is some weirdness with the way PHP either (1) returns from
// preg_replace when the search string is "0", or (2) a type
// comparison beetween a string and an integer 0.  In any case, 
// remove the if clause below at your own risk.
if(0 === $wakkaConfig['wakka_version'])
{
	$version2[0] = $wakkaConfig['wakka_version'];
}
else	
{
	$version2 = preg_split('/-|_/', $wakkaConfig['wakka_version']);
}
if ( $version1[0] !== $version2[0] )
//if($wakkaConfig['wakka_version'] !== WAKKA_VERSION)
{
	// set up (intended) config location for the installer
	#$wakkaConfigLocation = SITE_CONFIGFILE;		// @@@ use directly in installer
	if ($debug) echo 'site configuration file (to be) lodated at: '.SITE_CONFIGFILE."<br/>\n";
	$htaccessLocation = str_replace('\\', '/', dirname(__FILE__)).DIRECTORY_SEPARATOR.'.htaccess';
	#if (file_exists('setup'.DIRECTORY_SEPARATOR.'index.php'))	#89
	if (file_exists(WIKKA_SETUP_PATH.DIRECTORY_SEPARATOR.'index.php'))	# #89
	{
		// run the installer
		#include 'setup'.DIRECTORY_SEPARATOR.'index.php';		#89
		include WIKKA_SETUP_PATH.DIRECTORY_SEPARATOR.'index.php';		# #89
		return;				// prevent "fall-through"
	}
	else
	{
		// installer can not be run
		die(WIKKA_ERROR_SETUP_FILE_MISSING);	#fatalerror
	}
}
// -------------------------------- END INSTALLER ------------------------------


// ---------------------------- GET READY TO ROLL ------------------------------
if ($debug) echo "get ready to roll...<br/>\n";
if ($debug) echo '=> register_globals: '.ini_get('register_globals')."</br>\n";
// --- - requires SOME language file to be loaded for error messages
//     - constants WIKKA_LIBRARY_PATH, BASIC_SESSION_NAME and MINIMUM_MYSQL_VERSION
//       must have been defined
//     - Compatibility library must be included for MySQL version check
/**
 * Include main library if it exists.
 *
 * @see		libs/Wakka.class.php
 */
$wakka_library = WIKKA_LIBRARY_PATH.DIRECTORY_SEPARATOR.'Wakka.class.php';
$wakka_library_missing = sprintf(ERROR_WAKKA_LIBRARY_MISSING, $wakka_library);
if (file_exists($wakka_library))
{
	require_once $wakka_library;
}
else
{
	die($wakka_library_missing);	#fatalerror
}

/**
 * Start session.
 * @todo	consider whether to derive the complete set of paths (but earlier)
 *			and pass them to the Wakka object; that way we still derive only once
 *			but the installer can use them as well.
 */

// start session
session_name(md5(BASIC_SESSION_NAME.$wakkaConfig['wiki_suffix']));
session_set_cookie_params(0, WIKKA_COOKIE_PATH);	// default path is '/' - we don't want that! this call overrides php.ini settings
session_cache_limiter(''); # #279
session_start();

/**
 * Fetch wakka value (requested page + parameters)
 *
 * @todo files action uses POST, everything else uses GET #312
 * @todo use different name - $wakka clashes with $wakka object (which should be #Wakka)
 */
$wakka_request = $_GET['wakka']; # #312

/**
 * Remove leading slash.
 *
 * @todo	use different name - $wakka_request clashes with $wakka_request object (which should be #Wakka)
 */
$wakka_request = preg_replace("/^\//", "", $wakka_request);

/**
 * Extract pagename and handler from URL
 *
 * Note this splits at the FIRST '/', so $method may contain one or more slashes;
 * this is not allowed, and ultimately handled in the Method() method. [SEC]
 *
 * @todo	devise a more intelligent page and handler derivation and error out
 *			when URL syntax isn't correct. E.g., explode at '/' - there may only
 *			be one or two elements in the resulting array [SEC]
 */
// init
$handler = '';
// analyze request to derive page and handler
if (preg_match("#^(.+?)/(.*)$#", $wakka_request, $matches))
{
	list(, $page, $handler) = $matches;
}
else if (preg_match("#^(.*)$#", $wakka_request, $matches))
{
	list(, $page) = $matches;
}

//Fix lowercase mod_rewrite bug: URL rewriting makes pagename lowercase. #135
if ((strtolower($page) == $page) && (isset($_SERVER['REQUEST_URI']))) # #38
{
	$pattern = preg_quote($page, '/');
	if (preg_match('/('.$pattern.')/i', urldecode($_SERVER['REQUEST_URI']), $match_url))
	{
		$page = $match_url[1];
	}
}
if ($debug) echo 'page: '.$page."<br/>\n";
if ($debug) echo 'handler: '.$handler."<br/>\n";

/**
 * Create Wakka object.
 *
 * @todo	use name with Capital for object
 */
$wakka = instantiate('Wakka',$wakkaConfig);

/**
 * Check if we have database access.
 * @todo	use name with Capital for object
 */
if (!$wakka->dblink)
{
	$mysql_access_error = STATUS_WIKI_UNAVAILABLE.'<br/>'.ERROR_NO_DB_ACCESS;
	die($mysql_access_error);		#FatalErrorAfterLangFileIncluded
}

/**
 * We have database access: now check if the version is one we support.
 */
$errors = array();
$mysql_version = getMysqlVersion($errors);
if (($n = count($errors)) > 0)
{
	for ($i=0; $i <= $n; $i++)
	{
		#echo 'MySQL error: '.$errors['no'][$i].' - '.$errors['txt'][$i]."<br/>\n";	# i18n	@@@
		printf(WIKKA_ERROR_MYSQL_ERROR, $errors['no'][$i], $errors['txt'][$i]);
		echo "<br/>\n";
	}
	$mysql_version_retrieval_error = ERROR_RETRIEVAL_MYSQL_VERSION;
	die($mysql_version_retrieval_error);
}
if ($debug) echo 'MySQL version: '.$mysql_version."<br/>\n";
if ($mysql_version !== FALSE &&
	version_compare($mysql_version, MINIMUM_MYSQL_VERSION,'<')	// < MYSQL minimum version??
   )
{
	$mysql_version_error = sprintf(ERROR_WRONG_MYSQL_VERSION, MINIMUM_MYSQL_VERSION);
	die($mysql_version_error);		#FatalErrorAfterLangFileIncluded
}

/**
 * Save session ID
 */
$user = $wakka->GetUser(); 
// Only store sessions for real users! 
if(NULL != $user) 
{ 
	$res = $wakka->LoadSingle("SELECT * FROM ".$wakka->config['table_prefix']."sessions WHERE sessionid='".session_id()."' AND userid='".$user['name']."'");  
	if(!empty($res)) 
	{ 
		// Just update the session_start time 
		$wakka->Query("UPDATE ".$wakka->config['table_prefix']."sessions SET session_start=NOW() WHERE sessionid='".session_id()."' AND userid='".$user['name']."'"); 
	} 
	else 
	{ 
		// Create new session record 
		$wakka->Query("INSERT INTO ".$wakka->config['table_prefix']."sessions (sessionid, userid, session_start) VALUES('".session_id()."', '".$user['name']."', NOW())"); 
	} 
}

// ---------------------------- READY TO ROLL NOW ------------------------------
// ---------------------------------- ROLL! ------------------------------------
// --- Dependencies:
//     - language file loaded
// ....- configuration defined
//     - Wakka class instantiated
//     - Compatibility library loaded
if ($debug) echo "roll...<br/>\n";
$debug_info = '';
if (!$debug)
{
	$debug_info = ob_get_contents();
	@ob_end_clean();	// in case there was a previous buffer left from debug mode!
	ob_start();		// start buffering output
}
/**
 * Run the engine.
 *
 * @todo	use name with Capital for object; also clashes with $wakka (above) now
 */
$wakka->Run($page, $handler);				// This is where it all happens!
// ------------------------------- PAGE ROLLED ---------------------------------


// ------------------------- WRAP UP AND DISPLAY PAGE --------------------------
if ($debug) echo "wrap up...<br/>\n";
$content =  ob_get_contents();				// pick up contents of buffer
/**
 * Use gzip compression if possible.
 *
 * @todo	use config value to optionally turn off gzip-encoding here #541
 */
if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) &&
	strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') &&
	function_exists('gzencode') # #38
   )
{
	// Tell the browser the content is compressed with gzip
	header('Content-Encoding: gzip');
	$page_output = gzencode($content);
	//$page_length = strlen($page_output);	// We no longer send Content-Length header - see below
}
else
{
	$page_output = $content;
	//$page_length = strlen($page_output);	// We no longer send Content-Length header - see below
}

$etag = md5($content);
header('ETag: '.$etag);		// @@@ should not contain footer with page generation time!

if (!isset($wakka->do_not_send_anticaching_headers) ||
	(!$wakka->do_not_send_anticaching_headers) # #279
   )
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
@ob_end_clean();

/**
 * Output the page.
 */
echo $page_output;
// --------------------------------- ALL DONE ----------------------------------
?>
