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
 * @author	{@link http://www.mornography.de/ Hendrik Mans}
 * @author	{@link http://wikkawiki.org/JsnX Jason Tourtelotte}
 * @author	{@link http://wikkawiki.org/JavaWoman Marjolein Katsma}
 * @author	{@link http://wikkawiki.org/NilsLindenberg Nils Lindenberg}
 * @author	{@link http://wikkawiki.org/DotMG Mahefa Randimbisoa}
 * @author	{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 * @author	{@link http://wikkawiki.org/BrianKoontz Brian Koontz}
 * @author	{@link http://wikkawiki.org/TormodHaugen Tormod Haugen}
 *
 * @copyright Copyright 2002-2003, Hendrik Mans <hendrik@mans.de>
 * @copyright Copyright 2004-2005, Jason Tourtelotte <wikka-admin@jsnx.com>
 * @copyright Copyright 2006-2010, {@link http://wikkawiki.org/CreditsPage Wikka Development Team}
 *
 * @todo use templating class for page generation;
 * @todo add phpdoc documentation for configuration array elements;
 */

// ---------------------- DEBUGGING AND ERROR REPORTING -----------------------
error_reporting(E_ALL);
//error_reporting (E_ALL & E_NOTICE & E_DEPRECATED);
// ---------------------- END DEBUGGING AND ERROR REPORTING -------------------

// ---------------------------- VERSIONING ------------------------------------
/**#@+
 * Defines current Wikka version.
 */
include_once('version.php');

// ----------------------------- BASIC CONSTANTS -------------------------------
/**#@+
 * Simple constant. May be made a configurable value.
 */
/**
 * Defines the default cookie name.
 */
if (!defined('BASIC_COOKIE_NAME')) define('BASIC_COOKIE_NAME', 'Wikkawiki');
/**
 * Length to use for generated part of id attribute.
 */
define('ID_LENGTH',10);			// @@@ maybe make length configurable
/**
 * Character used for multi-path lists
 */
if(!defined('PATH_DIVIDER')) define('PATH_DIVIDER', ',');
/**#@-*/
/**#@+
 * Minimum version requirement.
 */
if (!defined('MINIMUM_PHP_VERSION'))	define('MINIMUM_PHP_VERSION', '5.0');
if (!defined('MINIMUM_MYSQL_VERSION'))	define('MINIMUM_MYSQL_VERSION', '4.1');
/**#@-*/
// ----------------------------- END BASIC CONSTANTS ---------------------------

// ----------------------------- SANITY CHECKS ---------------------------------

// More intelligent version check, more intelligently placed ;)
if (!function_exists('version_compare') ||
	version_compare(phpversion(),MINIMUM_PHP_VERSION,'<')	// < PHP minimum version??
   )
{
	$php_version_error = sprintf(ERROR_WRONG_PHP_VERSION,MINIMUM_PHP_VERSION);
	die($php_version_error);		# fatalerror	!!! default error in English
}
// MySQL needs to be installed and available
// @@@ message could be refined by detecting detect OS (mention module name) and maybe server name
if (!function_exists('mysql_connect'))
{
	die(ERROR_MYSQL_SUPPORT_MISSING);
}

/**
 * Include main library if it exists.
 * @see libs/Wakka.class.php
 */
if (file_exists('libs/Wakka.class.php'))
{
	require_once('libs/Compatibility.lib.php');
	require_once('libs/Wakka.class.php');
}
else
{
	die(ERROR_WAKKA_LIBRARY_MISSING);
}

// ----------------------------- END SANITY CHECKS ----------------------------

ob_start();
global $tstart;
$tstart = getmicrotime();
ini_set('magic_quotes_runtime', 0);
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
$t_scheme = ((isset($_SERVER['HTTPS'])) && !empty($_SERVER['HTTPS']) && 'off' != $_SERVER['HTTPS']) ? 'https://' : 'http://';
$t_port = ':'.$_SERVER['SERVER_PORT'];
if ((('http://' == $t_scheme) && (':80' == $t_port)) || (('https://' == $t_scheme) && (':443' == $t_port)))
{
	$t_port = '';
}
$t_request	= $_SERVER['REQUEST_URI'];
// append slash if $t_request does not end with either a slash or the string .php
if (!preg_match('@(\\.php|/)$@i', $t_request))
{
//	$t_request .= '/';
}

if (preg_match('@\.php$@', $t_request) && !preg_match('@wikka\.php$@', $t_request))
{
	// handle "overridden" redirect from index.php
	$t_request = preg_replace('@/[^.]+\.php@', '/wikka.php', $t_request);	// handle "overridden" redirect from index.php
}

if ( !preg_match('@wakka=@',$_SERVER['REQUEST_URI']) && isset($_SERVER['QUERY_STRING']) && preg_match('@wakka=@',$_SERVER['QUERY_STRING']))
{
	// looks like we got a rewritten request via .htaccess
	// remove 'wikka.php' and request (page name) from 'request' part: should not be part of base_url!
	$query_part = preg_replace('@wakka=@', '', $_SERVER['QUERY_STRING']);
	$t_request  = preg_replace('@'.preg_quote('wikka.php').'@', '', $t_request);
	$t_request  = preg_replace('@'.preg_quote($query_part).'@', '', $t_request);
	$t_query = '';
	$t_rewrite_mode = 1;
}
else
{
	// no rewritten request apparent
	$t_query = '?wakka=';
	$t_rewrite_mode = 0;
}

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
/**
 * Path for Wikka libs
 *
 * @var string
 */
if(!defined('WIKKA_LIBRARY_PATH')) define('WIKKA_LIBRARY_PATH', 'lib');

/**#@-*/
// ----------------------- END URL DOMAIN / PATH -------------------------------


$wakkaDefaultConfig = array(
	'mysql_host'				=> 'localhost',
	'mysql_database'			=> 'wikka',
	'mysql_user'				=> 'wikka',
	'table_prefix'				=> 'wikka_',

	'root_page'					=> 'HomePage',
	'wakka_name'				=> 'MyWikkaSite',
	'base_url'					=> $t_scheme.$t_domain.$t_port.$t_request.$t_query,
	'rewrite_mode'				=> $t_rewrite_mode,
	'wiki_suffix'				=> '@wikka',
	'enable_user_host_lookup'	=> '1',	#enable (1, default) or disable (0) lookup of user hostname from IP address

	'action_path'				=> 'plugins/actions'.PATH_DIVIDER.'actions',
	'handler_path'				=> 'plugins/handlers'.PATH_DIVIDER.'handlers',
	'lang_path'					=> 'plugins/lang',
	'gui_editor'				=> '1',
	'default_comment_display'	=> 'threaded',
	'theme'						=> 'light',

	// formatter and code highlighting paths
	'wikka_formatter_path' 		=> 'plugins/formatters'.PATH_DIVIDER.'formatters',		# (location of Wikka formatter - REQUIRED)
	'wikka_highlighters_path'	=> 'formatters',		# (location of Wikka code highlighters - REQUIRED)
	'geshi_path' 				=> '3rdparty/plugins/geshi',				# (location of GeSHi package)
	'geshi_languages_path' 		=> '3rdparty/plugins/geshi/geshi',		# (location of GeSHi language highlighting files)

	// template
	'wikka_template_path' 		=> 'plugins/templates'.PATH_DIVIDER.'templates',		# (location of Wikka template files - REQUIRED)
	'feedcreator_path'			=> '3rdparty/core/feedcreator',
   	'menu_config_path'			=> 'config', #858
	'safehtml_path'				=> '3rdparty/core/safehtml',
	'referrers_purge_time'		=> '30',
	'pages_purge_time'			=> '0',
	'xml_recent_changes'		=> '10',
	'hide_comments'				=> '0',
	'comment_stylesheet'		=> 'boxed-comments.css',
	'require_edit_note'			=> '0',		# edit note optional (0, default), edit note required (1) edit note disabled (2)
	'anony_delete_own_comments'	=> '1',
	'public_sysinfo'			=> '0',		# enable or disable public display of system information in SysInfo
	'double_doublequote_html'	=> 'safe',
	'external_link_tail' 		=> '<span class="exttail">&#8734;</span>',
	'sql_debugging'				=> '0',
	'admin_users' 				=> '',
	'admin_email' 				=> '',
	'upload_path' 				=> 'uploads',
	'mime_types' 				=> 'mime_types.txt',

	// code hilighting with GeSHi
	'geshi_header'				=> 'div',	# 'div' (default) or 'pre' to surround code block
	'geshi_line_numbers'		=> '1',		# disable line numbers (0), or enable normal (1) or fancy line numbers (2)
	'geshi_tab_width'			=> '4',		# set tab width
	'grabcode_button'			=> '1',		# allow code block downloading

	'wikiping_server' 			=> '',

	'default_write_acl'			=> '+',
	'default_read_acl'			=> '*',
	'default_comment_read_acl'		=> '*',
	'default_comment_post_acl'		=> '+',
	'allow_user_registration'	=> '1',
	'enable_version_check'      => '1',
	'version_check_interval'	=> '1h',
	'default_lang' => 'en'
	);

// load config
$wakkaConfig = array();
if (file_exists('wakka.config.php')) rename('wakka.config.php', 'wikka.config.php"');	// upgrade from Wakka
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

// remove obsolete config settings (should come before merge!)
//TODO move these checks to a directive file to be used by the installer/upgrader, #97
if (isset($wakkaConfig['header_action']))
{
	unset($wakkaConfig['header_action']); //since 1.1.6.4
}
if (isset($wakkaConfig['footer_action'])) //since 1.1.6.4
{
	unset($wakkaConfig['footer_action']);
}

// Remove old stylesheet, #6
if(isset($wakkaConfig['stylesheet']))
{
	unset($wakkaConfig['stylesheet']); // since 1.2
}

// Add plugin paths if they do not already exist
if(isset($wakkaConfig['action_path']) && preg_match('/plugins\/actions/', $wakkaConfig['action_path']) <= 0)
	$wakkaConfig['action_path'] = "plugins/actions," .  $wakkaConfig['action_path'];
if(isset($wakkaConfig['handler_path']) && preg_match('/plugins\/handlers/', $wakkaConfig['handler_path']) <= 0)
	$wakkaConfig['handler_path'] = "plugins/handlers," .  $wakkaConfig['handler_path'];
if(isset($wakkaConfig['wikka_template_path']) && preg_match('/plugins\/templates/', $wakkaConfig['wikka_template_path']) <= 0)
	$wakkaConfig['wikka_template_path'] = "plugins/templates," .  $wakkaConfig['wikka_template_path'];
if(isset($wakkaConfig['wikka_formatter_path']) && preg_match('/plugins\/formatters/', $wakkaConfig['wikka_formatter_path']) <= 0)
	$wakkaConfig['wikka_formatter_path'] = "plugins/formatters," .  $wakkaConfig['wikka_formatter_path'];
if(isset($wakkaConfig['lang_path']) && preg_match('/plugins\/lang/', $wakkaConfig['lang_path']) <= 0)
	$wakkaConfig['lang_path'] = "plugins/lang," .  $wakkaConfig['lang_path'];

$wakkaConfig = array_merge($wakkaDefaultConfig, $wakkaConfig);	// merge defaults with config from file

// ---------------------------- LANGUAGE DEFAULTS -----------------------------
/**
 * Include language file(s) if it/they exist(s).
 * @see /lang/en.inc.php
 *
 * Note that all lang_path entries in wikka.config.php are scanned for
 * default_lang files in the order specified in lang_path, with the
 * fallback language pack scanned last to pick up any undefined
 * strings.
 *
 * TODO: Handlers and actions that use their own language packs are
 * responsible for loading their own translation strings.  This
 * process should be unified across the application.
 *
 */
$default_lang = $wakkaConfig['default_lang'];
$fallback_lang = 'en';
$default_lang_path = 'lang'.DIRECTORY_SEPARATOR.$default_lang;
$plugin_lang_path = $wakkaConfig['lang_path'].DIRECTORY_SEPARATOR.$default_lang;
$fallback_lang_path = 'lang'.DIRECTORY_SEPARATOR.$fallback_lang;
$default_lang_strings = $default_lang_path.DIRECTORY_SEPARATOR.$default_lang.'.inc.php';
$plugin_lang_strings = $plugin_lang_path.DIRECTORY_SEPARATOR.$default_lang.'.inc.php';
$fallback_lang_strings = $fallback_lang_path.DIRECTORY_SEPARATOR.$fallback_lang.'.inc.php';
$lang_packs_found = false;
if (file_exists($plugin_lang_strings))
{
	require_once($plugin_lang_strings);
	$lang_packs_found = true;
}
if (file_exists($default_lang_strings))
{
	require_once($default_lang_strings);
	$lang_packs_found = true;
}
if (file_exists($fallback_lang_strings))
{
	require_once($fallback_lang_strings);
	$lang_packs_found = true;
}
if(!$lang_packs_found)
{
	die('Language file '.$default_lang_strings.' not found! In addition, the default language file '.$fallback_lang_strings.' is missing. Please add the file(s).');
}

if(!defined('WIKKA_LANG_PATH')) define('WIKKA_LANG_PATH', $default_lang_path);
// ------------------------- END LANGUAGE DEFAULTS -----------------------------

/**
 * To activate multisite deployment capabilities, just create an empty file multi.config.php in
 * your Wikkawiki installation directory. This file can contain an array definition for
 * $multiConfig.
 * Relevant keys in the array are a global directory for local settings 'local_config' and
 * designated directories for different host requests, e.g. you may want http://example.com
 * and http://www.example.com using the same local config file.
 * 'http_www_example_com' => 'http.example.com'
 * 'http_example_com' => 'http.example.com'
*/
$multisite_configfile = 'multi.config.php';
if (file_exists($multisite_configfile))
{
	$wakkaGlobalConfig = $wakkaConfig;	// copy config file, #878
	$multiDefaultConfig = array(
		'local_config'            => 'wikka.config' # path to local configs
	);
	$multiConfig = array();

    include($multisite_configfile);

    $multiConfig = array_merge($multiDefaultConfig, $multiConfig);    // merge default multi config with config from file

    $configkey = str_replace('://','_',$t_scheme).str_replace('.','_',$t_domain);
    if($t_port != '') $configkey .= '_'.$t_port;


/**
 * Admin can decide to put a specific local config in a more readable and shorter directory.
 * The $configkey is created as 'protocol_thirdleveldomain_secondleveldomain_topleveldomain'
 * Subdirectories are not supported at the moment, but should be easy to implement.
 * If no designated directory is found in multi.config.php, the script uses the $configkey
 * value and replaces all underscore by dots:
 * protocol.thirdleveldomain.secondleveldomain.topleveldomain e.g.
 * http.www.example.com
*/
    if (isset($multiConfig[$configkey])) $configpath = $multiConfig[$configkey];
    else
    {
        $requested_host = str_replace('_','.',$configkey);
        $configpath = $multiConfig['local_config'].DIRECTORY_SEPARATOR.$requested_host;
        $multiConfig[$configkey] = $requested_host;
    }

    $local_configfile = $configpath.DIRECTORY_SEPARATOR.'local.config.php';
/**
 * As each site may differ in its configuration and capabilities, we should consider using
 * plugin directories below the $configpath. Effectively, this replaces the 1.1.6.6 plugins
 * folder. It goes even a little bit further by providing a site specific upload directory.
*/

    $localDefaultConfig = array(
    	'menu_config_path'			=> $configpath.DIRECTORY_SEPARATOR.'config'.PATH_DIVIDER.'config',
        'action_path'				=> $configpath.DIRECTORY_SEPARATOR.'actions'.PATH_DIVIDER.'plugins'.DIRECTORY_SEPARATOR.'actions'.PATH_DIVIDER.'actions',
        'handler_path'				=> $configpath.DIRECTORY_SEPARATOR.'handlers'.PATH_DIVIDER.'plugins'.DIRECTORY_SEPARATOR.'handlers'.PATH_DIVIDER.'handlers',
        'wikka_formatter_path'		=> $configpath.DIRECTORY_SEPARATOR.'formatters'.PATH_DIVIDER.'plugins'.DIRECTORY_SEPARATOR.'formatters'.PATH_DIVIDER.'formatters',        # (location of Wikka formatter - REQUIRED)
        'wikka_highlighters_path'	=> $configpath.DIRECTORY_SEPARATOR.'formatters'.PATH_DIVIDER.'plugins'.DIRECTORY_SEPARATOR.'formatters'.PATH_DIVIDER.'formatters',        # (location of Wikka code highlighters - REQUIRED)
        'wikka_template_path'		=> $configpath.DIRECTORY_SEPARATOR.'templates'.PATH_DIVIDER.'plugins'.DIRECTORY_SEPARATOR.'templates'.PATH_DIVIDER.'templates',        # (location of Wikka template files - REQUIRED)
        'upload_path'				=> $configpath.DIRECTORY_SEPARATOR.'uploads'
    );
    $localConfig = array();
    if (!file_exists($configpath))
    {
        $path_parts = explode(DIRECTORY_SEPARATOR,$configpath);
        $partialpath = '';
        foreach($path_parts as $part)
        {
            $partialpath .= $part;
            if (!file_exists($partialpath)) mkdir($partialpath,0755);
            $partialpath .= DIRECTORY_SEPARATOR;
        }
        mkdir($configpath.DIRECTORY_SEPARATOR.'config',0700);
        mkdir($configpath.DIRECTORY_SEPARATOR.'actions',0700);
        mkdir($configpath.DIRECTORY_SEPARATOR.'handlers',0700);
        mkdir($configpath.DIRECTORY_SEPARATOR.'handlers'.DIRECTORY_SEPARATOR.'page',0700);
        mkdir($configpath.DIRECTORY_SEPARATOR.'formatters',0700);
        mkdir($configpath.DIRECTORY_SEPARATOR.'templates',0700);
        mkdir($configpath.DIRECTORY_SEPARATOR.'uploads',0755);
//        if(file_exists($wakkaConfig['stylesheet'])) copy($wakkaConfig['stylesheet'],$localDefaultConfig['stylesheet']);
    }
    else if (file_exists($local_configfile)) include($local_configfile);

    $wakkaGlobalConfig = array_merge($wakkaGlobalConfig, $localDefaultConfig);    // merge global config with default local config

    $wakkaConfigLocation = $local_configfile;

    $wakkaConfig = array_merge($wakkaGlobalConfig, $wakkaConfig);    // merge localized global config with local config from file
}

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
if (!isset($wakkaConfig['wakka_version'])) $wakkaConfig['wakka_version'] = 0;
if ($wakkaConfig['wakka_version'] !== WAKKA_VERSION)
{
	/**
	 * Start installer.
	 *
	 * Data entered by the user is submitted in $_POST, next action for the
	 * installer (which will receive this data) is passed as a $_GET parameter!
	 */
	$installAction = 'default';
	if (isset($_GET['installAction'])) $installAction = trim($_GET['installAction']);	#312
	if (file_exists('setup'.DIRECTORY_SEPARATOR.'header.php'))
	include('setup'.DIRECTORY_SEPARATOR.'header.php'); else print '<em class="error">'.ERROR_SETUP_HEADER_MISSING.'</em>'; #89
	if
	(file_exists('setup'.DIRECTORY_SEPARATOR.$installAction.'.php'))
	include('setup'.DIRECTORY_SEPARATOR.$installAction.'.php'); else print '<em class="error">'.ERROR_SETUP_FILE_MISSING.'</em>'; #89
	if (file_exists('setup'.DIRECTORY_SEPARATOR.'footer.php'))
	include('setup'.DIRECTORY_SEPARATOR.'footer.php'); else print '<em class="error">'.ERROR_SETUP_FOOTER_MISSING.'</em>'; #89
	exit;
}

/**
 * Start session.
 */
$base_url_path = preg_replace('/wikka\.php/', '', $_SERVER['SCRIPT_NAME']);
$wikka_cookie_path = ('/' == $base_url_path) ? '/' : substr($base_url_path,0,-1);
session_set_cookie_params(0, $wikka_cookie_path);
session_name(md5(BASIC_COOKIE_NAME.$wakkaConfig['wiki_suffix']));
session_start();

// fetch wakka location
/**
 * Fetch wakka location (requested page + parameters)
 *
 * @todo files action uses POST, everything else uses GET #312
 */
$wakka = $_GET['wakka']; #312

/**
 * Remove leading slash.
 */
$wakka = preg_replace("/^\//", "", $wakka);

/**
 * Extract pagename and handler from URL
 *
 * Note this splits at the FIRST / so $handler may contain one or more slashes;
 * this is not allowed, and ultimately handled in the Handler() method. [SEC]
 */
if (preg_match("#^(.+?)/(.*)$#", $wakka, $matches)) list(, $page, $handler) = $matches;
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
$wakka = instantiate('Wakka',$wakkaConfig);

/**
 * Check for database access.
 */
if (!$wakka->dblink)
{
	echo '<em class="error">'.ERROR_NO_DB_ACCESS.'</em>';
	exit;
}

/**
 * Save session ID
 */
$user = $wakka->GetUser();
// Only store sessions for real users!
if(NULL != $user)
{
	$res = $wakka->LoadSingle("SELECT * FROM ".$wakka->config['table_prefix']."sessions WHERE sessionid='".session_id()."' AND userid='".$user['name']."'");
	if(isset($res))
	{
		// Just update the session_start time
		$wakka->Query("UPDATE ".$wakka->config['table_prefix']."sessions SET session_start=FROM_UNIXTIME(".$wakka->GetMicroTime().") WHERE sessionid='".session_id()."' AND userid='".$user['name']."'");
	}
	else
	{
		// Create new session record
		$wakka->Query("INSERT INTO ".$wakka->config['table_prefix']."sessions (sessionid, userid, session_start) VALUES('".session_id()."', '".$user['name']."', FROM_UNIXTIME(".$wakka->GetMicroTime()."))");
	}
}

/**
 * Run the engine.
 */
if (!isset($handler)) $handler='';
$wakka->Run($page, $handler);
$content =  ob_get_contents();
/**
 * Use gzip compression if possible.
 */
/*
if ( isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strstr ($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') && function_exists('gzencode') ) #38
{
	// Tell the browser the content is compressed with gzip
	header ("Content-Encoding: gzip");
	$page_output = gzencode($content);
	$page_length = strlen($page_output);
} else {
 */
	$page_output = $content;
	$page_length = strlen($page_output);
//}

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
