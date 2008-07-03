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
 * (...) Explain basic override mechanism @@@
 *
 * Because we are dealing with overrides here, not all of which need
 * to be defined, we do not assume any dependencies for these
 * overrides: each stands on its own.  (Of course the override file
 * itself may make use of dependencies, but that is up to the
 * administrator setting these up.)
 * All defined and valid override paths are converted to their
 * canonical absolute form.
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
 * @author	{@link http://wikkawiki.org/BrianKoontz Brian Koontz}
 *
 * @copyright	Copyright 2002-2003, Hendrik Mans <hendrik@mans.de>
 * @copyright	Copyright 2004-2005, Jason Tourtelotte <wikka-admin@jsnx.com>
 * @copyright	Copyright 2006-2008, {@link http://wikkawiki.org/CreditsPage Wikka Development Team}
 *
 * @uses	validLocalPath()	for validating path override
 * @uses	getmicrotime()		for determining page generation time
 * @uses	magicQuotesWorkaround()	to overcome magic quotes that may be imposed
 *				on a system
 * @uses	instantiate()		for a version-independent method to instantiate a class
 * @uses	getMysqlVersion()	to retrieve MySQL version for a requirements check
 *
 * @todo	use templating class for page generation;
 */

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

// ----------------------------- BASIC CONSTANTS -------------------------------
/**
 * Defines the basic name the session name will be derived from.
 */
if (!defined('BASIC_SESSION_NAME'))		define('BASIC_SESSION_NAME', 'Wikkawiki');

/**
 * Path where the Wikka installer is located.
 * This value is <b>not</b> overridable or in any way configurable: each (new)
 * Wikka installation must have its own local 'setup' directory (for now, at least).
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


// ---------------------- LOAD PATH AND DEFAULT OVERRIDES ----------------------
if ($debug) echo "load path overrides...<br/>\n";
// Include configuration override file, if it exists.
// If it exists at all, it MUST be located in the Wikka installation directory.
if (file_exists('override.config.php'))
{
	include 'override.config.php';
}
// ---------------------- END PATH AND DEFAULT OVERRIDES -----------------------

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
 * This variable can be overriden in {@link override.config.php}
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
if ($debug) echo "core component paths...<br/>\n";
/**#@+
 * String constant defining the default path to a specific type of core component.
 */
/**
 * Default path to the Wikka code library which contains core components.
 * May be overridden to enable sharing core components between Wikka installations.
 */
if (!defined('DEFAULT_LIBRARY_PATH'))	define('DEFAULT_LIBRARY_PATH', 'libs');
/**
 * Default path to the Wikka language library where language-specific files are stored.
 * May be overridden to enable sharing core components between Wikka installations.
 */
if (!defined('DEFAULT_LANG_PATH'))		define('DEFAULT_LANG_PATH', 'lang');
/**#@-*/

/**#@+
 * String constant defining the effective path to a specific type of core component.
 */
/**
 * Effective path to the Wikka code library which contains core components.
 * The path takes any optional override into account, and is used directly in
 * Wikka, including in this file.
 */
if (!defined('WIKKA_LIBRARY_PATH')) define('WIKKA_LIBRARY_PATH',
	(defined('LOCAL_LIBRARY_PATH') && ($canon_path = validLocalPath(LOCAL_LIBRARY_PATH, 'dir')))
		? $canon_path
		: DEFAULT_LIBRARY_PATH
	);
/**
 * Effective path to the Wikka language library which contains language files
 * and localized system content.
 * The path takes any optional override into account, and is used directly in
 * Wikka, including in this file.
 */
if (!defined('WIKKA_LANG_PATH')) define('WIKKA_LANG_PATH',
	(defined('LOCAL_LANG_PATH') && ($canon_path = validLocalPath(LOCAL_LANG_PATH, 'dir')))
		? $canon_path
		: DEFAULT_LANG_PATH
	);
// ---------------------------- CORE PATHS DEFINED -----------------------------


// -------------------------- COMPATIBILITY LIBRARY ----------------------------
if ($debug) echo "loading compatibility library...<br/>\n";
// ---- requires the effective library path to be defined
// Now that the "core" paths are defined, include the "compatibility functions".
// We do this as early as possible: may be used for following defines and
// derivations!
require_once WIKKA_LIBRARY_PATH.DIRECTORY_SEPARATOR.'Compatibility.lib.php';
// ------------------------ END COMPATIBILITY LIBRARY --------------------------

// @@@ include regex library HERE, so this file AND the setup process can use it!

// ---------------- DEFINE & DERIVE CONFIGURABLE COMPONENT PATHS ---------------
if ($debug) echo "default 3rd-party component paths...<br/>\n";
/*
  Although these paths are configurable, we use defined constants here for three
  reasons:
  1. Like a core path, these represent paths to components that might be
	 shared between installations, so we use the same override mechanism here.
  2. While the paths are configurable, the defaults defined here can be overridden
	 <i>before</i> they get to the configuration file (and seen by the installer),
	 so they effectively become the defaults used during the installation process.
	 This enhances consistency between "sister" installations.
  3. A (filesystem) file or directory path needs to take the local directory
	 separator into account but in PHP4 a class variable (as used in the default
	 configuration file) can only be initialized with a literal or a constant,
	 not a concatenation.
*/

/**#@+
 * Default for a (configurable) filesystem directory for a component.
 */
/**
 * Default <b>directory</b> where actions bundled with Wikka are stored.
 * May be overridden as well as configured to enable sharing Wikka components
 * between Wikka installations.
 */
if (!defined('DEFAULT_ACTION_PATH'))	define('DEFAULT_ACTION_PATH', 'actions');
/**
 * Default <b>directory</b> where handlers bundled with Wikka are stored.
 * May be overridden as well as configured to enable sharing Wikka components
 * between Wikka installations.
 */
if (!defined('DEFAULT_HANDLER_PATH'))	define('DEFAULT_HANDLER_PATH', 'handlers');
/**
 * Default <b>directory</b> where formatters and highlighters bundled with Wikka are stored.
 * May be overridden as well as configured to enable sharing Wikka components
 * between Wikka installations.
 */
if (!defined('DEFAULT_FORMATTER_PATH'))	define('DEFAULT_FORMATTER_PATH', 'formatters');
/**
 * Default <b>directory</b> where template files bundled with Wikka are stored.
 * May be overridden as well as configured to enable sharing Wikka components
 * between Wikka installations.
 */
if (!defined('DEFAULT_TEMPLATE_PATH'))	define('DEFAULT_TEMPLATE_PATH', 'templates');
/**
 * Default <b>directory</b> where 3rd-party components bundled with Wikka are stored.
 * This path isn't used directly but can be used in building other 3rd-party
 * component paths.
 * May be overridden as well as configured to enable sharing 3rd-party components
 * with other Wikka installations and other applications.
 */
if (!defined('DEFAULT_3RDPARTY_PATH'))			define('DEFAULT_3RDPARTY_PATH', '3rdparty');

/**
 * Default <b>directory</b> for 3rd-party core components; these components are required for
 * basic Wikka functionality.
 * May be overridden as well as configured to enable sharing 3rd-party components
 * with other Wikka installations and other applications.
 */
if (!defined('DEFAULT_3RDPARTY_CORE_PATH'))		define('DEFAULT_3RDPARTY_CORE_PATH', DEFAULT_3RDPARTY_PATH.DIRECTORY_SEPARATOR.'core');
/**
 * Default <b>directory</b> for 3rd-party plugin components; these components are optional
 * and extend Wikka functionality.
 * May be overridden as well as configured to enable sharing 3rd-party components
 * with other Wikka installations and other applications.
 */
if (!defined('DEFAULT_3RDPARTY_PLUGIN_PATH'))	define('DEFAULT_3RDPARTY_PLUGIN_PATH', DEFAULT_3RDPARTY_PATH.DIRECTORY_SEPARATOR.'plugins');

/**
 * Default <b>directory</b> for the FeedCreator 3rd-party core component.
 * May be overridden as well as configured to enable sharing 3rd-party components
 * with other Wikka installations and other applications.
 */
if (!defined('DEFAULT_FEEDCREATOR_PATH'))		define('DEFAULT_FEEDCREATOR_PATH', DEFAULT_3RDPARTY_CORE_PATH.DIRECTORY_SEPARATOR.'feedcreator');
/**
 * Default <b>directory</b> for the SafeHTML 3rd-party core component.
 * May be overridden as well as configured to enable sharing 3rd-party components
 * with other Wikka installations and other applications.
 */
if (!defined('DEFAULT_SAFEHTML_PATH'))			define('DEFAULT_SAFEHTML_PATH', DEFAULT_3RDPARTY_CORE_PATH.DIRECTORY_SEPARATOR.'safehtml');

/**
 * Default <b>directory</b> for the optional GeSHi 3rd-party plugin component.
 * May be overridden as well as configured to enable sharing 3rd-party components
 * with other Wikka installations and other applications.
 */
if (!defined('DEFAULT_GESHI_PATH'))				define('DEFAULT_GESHI_PATH', DEFAULT_3RDPARTY_PLUGIN_PATH.DIRECTORY_SEPARATOR.'geshi');
/**
 * Default <b>directory</b> for the language files for the GeSHi 3rd-party plugin component.
 * May be overridden as well as configured to enable sharing 3rd-party components
 * with other Wikka installations and other applications.
 */
if (!defined('DEFAULT_GESHI_LANG_PATH'))		define('DEFAULT_GESHI_LANG_PATH', DEFAULT_GESHI_PATH.DIRECTORY_SEPARATOR.'geshi');
/**
 * Default <b>directory</b> for the optional Onyx-RSS 3rd-party plugin component.
 * May be overridden as well as configured to enable sharing 3rd-party components
 * with other Wikka installations and other applications.
 */
if (!defined('DEFAULT_ONYX_PATH'))				define('DEFAULT_ONYX_PATH', DEFAULT_3RDPARTY_PLUGIN_PATH.DIRECTORY_SEPARATOR.'onyx-rss');
/**#@-*/

/**#@+
 * String constant used as (configurable) filesystem <b>directory</b> for a component.
 */
/**
 * Effective (configurable) <b>directory</b> for Wikka actions.
 * The path takes any optional override into account; used to define a value in
 * the default configuration file.
 */
if (!defined('CONFIG_ACTION_PATH')) define('CONFIG_ACTION_PATH',
	(defined('LOCAL_ACTION_PATH') && ($canon_path = validLocalPath(LOCAL_ACTION_PATH, 'dir')))
		? $canon_path
		: DEFAULT_ACTION_PATH
	);
/**
 * Effective (configurable) <b>directory</b> for Wikka handlers.
 * The path takes any optional override into account; used to define a value in
 * the default configuration file.
 */
if (!defined('CONFIG_HANDLER_PATH')) define('CONFIG_HANDLER_PATH',
	(defined('LOCAL_HANDLER_PATH') && ($canon_path = validLocalPath(LOCAL_HANDLER_PATH, 'dir')))
		? $canon_path
		: DEFAULT_HANDLER_PATH
	);
/**
 * Effective (configurable) <b>directory</b> for Wikka formatters and highlighters.
 * The path takes any optional override into account; used to define a value in
 * the default configuration file.
 */
if (!defined('CONFIG_FORMATTER_PATH')) define('CONFIG_FORMATTER_PATH',
	(defined('LOCAL_FORMATTER_PATH') && ($canon_path = validLocalPath(LOCAL_FORMATTER_PATH, 'dir')))
		? $canon_path
		: DEFAULT_FORMATTER_PATH
	);
/**
 * Effective (configurable) <b>directory</b> for Wikka templates.
 * The path takes any optional override into account; used to define a value in
 * the default configuration file.
 */
if (!defined('CONFIG_TEMPLATE_PATH')) define('CONFIG_TEMPLATE_PATH',
	(defined('LOCAL_TEMPLATE_PATH') && ($canon_path = validLocalPath(LOCAL_TEMPLATE_PATH, 'dir')))
		? $canon_path
		: DEFAULT_TEMPLATE_PATH
	);
/**
 * Effective (configurable) <b>directory</b> for 3rd-party components; these components
 * are required for basic Wikka functionality.
 * The path takes any optional override into account; used to define a value in
 * the default configuration file.
 */
if (!defined('CONFIG_3RDPARTY_PATH')) define('CONFIG_3RDPARTY_PATH',
	(defined('LOCAL_3RDPARTY_PATH') && ($canon_path = validLocalPath(LOCAL_3RDPARTY_PATH, 'dir')))
		? $canon_path
		: DEFAULT_3RDPARTY_PATH
	);
/**
 * Effective (configurable) <b>directory</b> for 3rd-party core components; these components
 * are required for basic Wikka functionality.
 * The path takes any optional override into account; used to define a value in
 * the default configuration file.
 */
if (!defined('CONFIG_3RDPARTY_CORE_PATH')) define('CONFIG_3RDPARTY_CORE_PATH',
	(defined('LOCAL_3RDPARTY_CORE_PATH') && ($canon_path = validLocalPath(LOCAL_3RDPARTY_CORE_PATH, 'dir')))
		? $canon_path
		: DEFAULT_3RDPARTY_CORE_PATH
	);
/**
 * Effective (configurable) <b>directory</b> for 3rd-party plugin components; these components
 * are optional and extend Wikka functionality.
 * The path takes any optional override into account; used to define a value in
 * the default configuration file.
 */
if (!defined('CONFIG_3RDPARTY_PLUGIN_PATH')) define('CONFIG_3RDPARTY_PLUGIN_PATH',
	(defined('LOCAL_3RDPARTY_PLUGIN_PATH') && ($canon_path = validLocalPath(LOCAL_3RDPARTY_PLUGIN_PATH, 'dir')))
		? $canon_path
		: DEFAULT_3RDPARTY_PLUGIN_PATH
	);

/**
 * Effective (configurable) <b>directory</b> for the FeedCreator 3rd-party core component.
 * The path takes any optional override into account; used to define a value in
 * the default configuration file.
 */
if (!defined('CONFIG_FEEDCREATOR_PATH')) define('CONFIG_FEEDCREATOR_PATH',
	(defined('LOCAL_FEEDCREATOR_PATH') && ($canon_path = validLocalPath(LOCAL_FEEDCREATOR_PATH, 'dir')))
		? $canon_path
		: DEFAULT_FEEDCREATOR_PATH
	);
/**
 * Effective (configurable) <b>directory</b> for the SafeHTML 3rd-party core component.
 * The path takes any optional override into account; used to define a value in
 * the default configuration file.
 */
if (!defined('CONFIG_SAFEHTML_PATH')) define('CONFIG_SAFEHTML_PATH',
	(defined('LOCAL_SAFEHTML_PATH') && ($canon_path = validLocalPath(LOCAL_SAFEHTML_PATH, 'dir')))
		? $canon_path
		: DEFAULT_SAFEHTML_PATH
	);

/**
 * Effective (configurable) <b>directory</b> for the optional GeSHi 3rd-party plugin package.
 * The path takes any optional override into account; used to define a value in
 * the default configuration file.
 */
if (!defined('CONFIG_GESHI_PATH')) define('CONFIG_GESHI_PATH',
	(defined('LOCAL_GESHI_PATH') && ($canon_path = validLocalPath(LOCAL_GESHI_PATH, 'dir')))
		? $canon_path
		: DEFAULT_GESHI_PATH
	);
/**
 * Effective (configurable) <b>directory</b> for the language files for the GeSHi 3rd-party
 * plugin component.
 * The path takes any optional override into account; used to define a value in
 * the default configuration file.
 */
if (!defined('CONFIG_GESHI_LANG_PATH')) define('CONFIG_GESHI_LANG_PATH',
	(defined('LOCAL_GESHI_LANG_PATH') && ($canon_path = validLocalPath(LOCAL_GESHI_LANG_PATH, 'dir')))
		? $canon_path
		: DEFAULT_GESHI_LANG_PATH
	);
/**
 * Effective (configurable) <b>directory</b> for the optional Onyx-RSS 3rd-party plugin component.
 * The path takes any optional override into account; used to define a value in
 * the default configuration file.
 */
if (!defined('CONFIG_ONYX_PATH')) define('CONFIG_ONYX_PATH',
	(defined('LOCAL_ONYX_PATH') && ($canon_path = validLocalPath(LOCAL_ONYX_PATH, 'dir')))
		? $canon_path
		: DEFAULT_ONYX_PATH
	);
/**#@-*/
// ------------------- CONFIGURABLE COMPONENT PATHS DEFINED --------------------

// -------------- DEFINE & DERIVE CONFIGURABLE COMPONENT URI PATHS -------------
if ($debug) echo "default 3rd-party component URI paths...<br/>\n";
/**#@+
 * Default for a (configurable) <b>URL path component</b> for a 3rd-party component.
 */
/**
 * Default <b>URL path component</b> for the WikiEdit scripts.
 * May be overridden as well as configured to enable sharing 3rd-party components
 * between Wikka installations.
 */
if (!defined('DEFAULT_WIKIEDIT_URIPATH'))	define('DEFAULT_WIKIEDIT_URIPATH', filesys2uri(DEFAULT_3RDPARTY_PLUGIN_PATH).'/wikiedit');
/**
 * Default <b>URL path component</b> for the FreeMind display applet.
 * May be overridden as well as configured to enable sharing 3rd-party components
 * between Wikka installations.
 */
if (!defined('DEFAULT_FREEMIND_URIPATH'))	define('DEFAULT_FREEMIND_URIPATH', filesys2uri(DEFAULT_3RDPARTY_PLUGIN_PATH).'/freemind');
/**#@-*/

/**#@+
 * Default for a (configurable) <b>URL path component</b> for a 3rd-party component.
 */
/**
 * Effective default (configurable) <b>URL path component</b> for the WikiEdit
 * scripts.
 * The path takes any optional override into account; used to define a value in
 * the default configuration file.
 */
if (!defined('CONFIG_WIKIEDIT_URIPATH'))	define('CONFIG_WIKIEDIT_URIPATH',
	(defined('LOCAL_WIKIEDIT_URIPATH') && ($uri_path = validUriPath(LOCAL_WIKIEDIT_URIPATH)))	// @@@ URI path
		? $uri_path
		: DEFAULT_WIKIEDIT_URIPATH
	);
/**
 * Effective default (configurable) <b>URL path component</b> for the FreeMind
 * display applet.
 * The path takes any optional override into account; used to define a value in
 * the default configuration file.
 */
if (!defined('CONFIG_FREEMIND_URIPATH'))	define('CONFIG_FREEMIND_URIPATH',
	(defined('LOCAL_FREEMIND_URIPATH') && ($uri_path = validUriPath(LOCAL_FREEMIND_URIPATH)))	// @@@ URI path
		? $uri_path
		: DEFAULT_FREEMIND_URIPATH
	);
/**#@-*/
// ----------------- CONFIGURABLE COMPONENT URI PATHS DEFINED ------------------

// ------------------------- OTHER CONFIGURABLE DEFAULTS -----------------------
/**#@+
 * String constant used as default for a configurable setting.
 */
/**
 * Defines the (configurable) default language. Wikka will attempt to load the
 * corresponding language file.
 * This value is directly used here in wikka.php but also used as the default
 * value in the default configuration file.
 */
if (!defined('CONFIG_DEFAULT_LANGUAGE'))	define('CONFIG_DEFAULT_LANGUAGE', 'en');
/**#@-*/
// ----------------------- END OTHER CONFIGURABLE DEFAULTS ---------------------


// ------------------ DEFINE & DERIVE CONFIGURATION FILE PATHS -----------------
if ($debug) echo "configuration file paths...<br/>\n";
// ---- requires WIKKA_LIBRARY_PATH to be defined so this section must come after
//      that constant is derived.
/**#@+
 * String constant used as default for the (filesystem) path for a configuration file.
 */
/**
 * Default filesystem path for the <b>default</b> configuration <b>file</b>.
 * By default located in the Wikka library directory; this setting is overridable
 * on its own, whether or not the default library location has been overridden.
 */
if (!defined('DEFAULT_DEFAULT_CONFIGFILE'))	define('DEFAULT_DEFAULT_CONFIGFILE', WIKKA_LIBRARY_PATH.DIRECTORY_SEPARATOR.'Config.class.php');
/**
 * Default filesystem path for the <b>site</b> configuration <b>file</b>.
 * By default located in the Wikka installation directory; this setting is
 * overridable to enable locating it outside the webroot (and/or sharing it with
 * another Wikka installation).
 */
if (!defined('DEFAULT_SITE_CONFIGFILE'))	define('DEFAULT_SITE_CONFIGFILE', 'wikka.config.php');
/**#@-*/

// FIXED this is an (improved and extended) version of the method introduced in
// 1.1.6.3 to avoid GetEnv #470

/**#@+
 * String constant defining the effective (filesystem) path for a configuration <b>file</b>.
 */
/**
 * Effective filesystem path for the <b>default</b> configuration <b>file</b>.
 * The path takes any optional override into account, and is used directly in
 * Wikka, including in this file.
 */
if (!defined('DEFAULT_CONFIGFILE')) define('DEFAULT_CONFIGFILE',
	(defined('LOCAL_DEFAULT_CONFIGFILE') && ($canon_path = validLocalPath(LOCAL_DEFAULT_CONFIGFILE, 'file')))
		? $canon_path
		: DEFAULT_DEFAULT_CONFIGFILE
	);
/**
 * Effective filesystem path for the <b>site</b> configuration <b>file</b>.
 * The path takes any optional override into account, and is used directly in
 * Wikka, including in this file. The file does not need to exist; if it doesn't,
 * the installer will be triggered (which will create the file).
 */
if (!defined('SITE_CONFIGFILE')) define('SITE_CONFIGFILE',
	(defined('LOCAL_SITE_CONFIGFILE') && ($canon_path = validLocalPath(LOCAL_SITE_CONFIGFILE, 'file', FALSE)))
		? $canon_path
		: DEFAULT_SITE_CONFIGFILE
	);
/**#@-*/
// ---------------------- CONFIGURATION FILE PATHS DEFINED ---------------------



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
#require_once('libs'.DIRECTORY_SEPARATOR.'Config.class.php');
require_once DEFAULT_CONFIGFILE;
$DefaultConfig = instantiate('Config');
$wakkaDefaultConfig = get_object_vars($DefaultConfig);
unset($DefaultConfig);
// . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .
/**
 * 2. Load and update the (current) user configuration if it exists.
 */
$wakkaConfig = array();	// empty array in case there's no user configuration yet

// Get any inherited configuration from Wakka - note this won't be picked up if
// SITE_CONFIGFILE points elsewhere! We assume that a deliberate overide takes
// precedence over automatic inheritance.
if (file_exists('wakka.config.php'))
{
	rename('wakka.config.php', 'wikka.config.php');
}
if (file_exists(SITE_CONFIGFILE))
{
	include SITE_CONFIGFILE;		// fills $wakkaConfig
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
 * Language files are bundled under <b>lang/</b> (default, overridable) in a
 * folder named after their ISO 639-1 code (e.g. 'en' for English).
 */
//check if a custom language definition is specified; if not, set a default
$wakkaConfig['default_lang'] = (isset($wakkaConfig['default_lang'])) ? $wakkaConfig['default_lang'] : CONFIG_DEFAULT_LANGUAGE;
// setup variables
$default_lang	= $wakkaConfig['default_lang'];
$fallback_lang	= CONFIG_DEFAULT_LANGUAGE;			// should always be available
$default_language_file  = WIKKA_LANG_PATH.DIRECTORY_SEPARATOR.$default_lang.DIRECTORY_SEPARATOR.$default_lang.'.inc.php';
$fallback_language_file = WIKKA_LANG_PATH.DIRECTORY_SEPARATOR.$fallback_lang.DIRECTORY_SEPARATOR.$fallback_lang.'.inc.php';
$language_file_not_found = sprintf(ERROR_LANGUAGE_FILE_MISSING,$default_language_file);
// load language package if it exists
if (file_exists($default_language_file))
{
	/**
	 * Language file for configured default language.
	 */
	require_once $default_language_file;
}
elseif (file_exists($fallback_language_file))
{
	/**
	 * Language file for system default language: fallback.
	 */
	require_once $fallback_language_file;	// silent fallback
}
else
{
	die($language_file_not_found);	# fatalerror - local error message in English because we don't _have_ a language file(!)
}
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
	//output result	// @@@ use paragraph
	echo '<div class="smallprint">'.sprintf(PAGE_GENERATION_TIME, $totaltime)."</div>\n";	// @@@ should be paragraph
	if ($track_errors)
	{
		echo '<p class="debuginfo">'.$debug_info.'</p>'."\n";
	}
	echo "</body>\n</html>";
}
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


// --------------------------- RELOCATION FUNCTIONS ----------------------------
// These two are needed to validate override paths before we can include the
// compatibility library.
/**
 * Check whether a given path is a URL.
 * We do this by checking whether it has a scheme.
 * NOTE: parse_url() is pretty dumb and will think a path like
 * 'J:\Server\XAMPP 1.5.0\var\wikka.config.php' is a URL and parse it, even though
 * it has <b>backslashes</b> and a single (back)slash after the ':'; so we'll check
 * for a double slash before letting it do its work!
 */
function is_uri($path)
{
	// init (assume NOT a uri)
	$result = FALSE;
	// URL?
	if (strstr('//',$path))
	{
		$a_components = parse_url($path);
		if (isset($a_components['scheme']))
		{
			$result = TRUE;
		}
/**
echo '<pre>';
print_r($a_components);
echo "</pre></br>\n";
/**/
	}
	return $result;
}
/**
 * Checks whether a given path is a valid local path.
 *
 * Returns a canonicalized absolute path if valid and local, FALSE otherwise.
 *
 * @param	string	$path	mandatory: path to be checked
 * @param	string	$type	mandatory: type of path to check; 'file' or 'dir'
 * @param	boolean	$mustexist	optional: specify whether the file or directory must already exist; default: TRUE
 * @return	mixed	valid absolute path if valid, FALSE otherwise
 */
function validLocalPath($path,$type,$must_exist=TRUE)
{
	// URL?
	if (is_uri($path))
	{
		$result = FALSE;				// URL not allowed for local path
#echo 'validLocalPath - is a URL!'."<br/>\n";
	}
	else
	{
		// realpath() fails on a non-existant file, so if it doesn't exist
		// we create it temporarily to let realpath() do its work
		$temp_created = FALSE;
		if (!file_exists($path))
		{
			// attempt to create it temporarily so realpath() can work on it
			$rc = @touch($path);
			if ($rc) $temp_created = TRUE;
		}
		$result = realpath($path);		// canonicalized absolute path
/**
if (!isset($result)) $result_txt = '(nothing)';
elseif (FALSE === $result) $result_txt = 'FALSE';
else $result_txt = $result;
echo 'validLocalPath - realpath() says: '.$result_txt."<br/>\n";
/**/
		// if a temp file was created, clear it up again
		if ($temp_created)
		{
			@unlink($path);
		}
		// if we still have a path, validate against type and existance requirements
		if (FALSE !== $result)
		{
			switch ($type)
			{
				case 'file':
					if ($must_exist && (!file_exists($result) || !is_file($result)))
					{
						$result = FALSE;
#echo 'validLocalPath - not a file or does not exist!'."<br/>\n";
					}
					break;
				case 'dir':
					if ($must_exist && (!file_exists($result) || !is_dir($result)))
					{
						$result = FALSE;
#echo 'validLocalPath - not a directory or does not exist!'."<br/>\n";
					}
					break;
				default:
					// wrong $type spec
					$result = FALSE;
#echo 'validLocalPath - invalid type parameter'."<br/>\n";
			}
		}
	}
/* Debug */
$result_txt = (FALSE === $result) ? 'FALSE' : $result;
echo 'validLocalPath - path '.$path.' is really: '.$result_txt."<br/>\n";
/**/
	return $result;
}
// ------------------------- END RELOCATION FUNCTIONS --------------------------
?>
