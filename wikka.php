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
 * @copyright Copyright 2006-2009, {@link http://wikkawiki.org/CreditsPage Wikka Development Team}
 *
 * @todo use templating class for page generation;
 * @todo add phpdoc documentation for configuration array elements;
 */

//error_reporting(E_ALL);
error_reporting (E_ALL ^ E_NOTICE);

// If you need to use this installation with a configuration file outside the
// installation directory uncomment the following line and adapt it to reflect
// the (filesystem) path to where your configuration file is located.
// This would make it possible to store the configuration file outside of the
// webroot, or to share one configuration file between several Wikka Wiki
// installations.
// This replaces the use of the environment variable WAKKA_CONFIG for security
// reasons. [SEC]
#if (!defined('WAKKA_CONFIG')) define('WAKKA_CONFIG','path/to/your/wikka.config.php');

/**#@+
 * Internationalization constant.
 */
if (!defined('ERROR_WAKKA_LIBRARY_MISSING')) define('ERROR_WAKKA_LIBRARY_MISSING','The necessary file "libs/Wakka.class.php" could not be found. To run Wikka, please make sure the file exists and is placed in the right directory!');
define('ERROR_WRONG_PHP_VERSION', 'Wikka requires PHP %s or higher!');	// %s - version number
define('MINIMUM_PHP_VERSION', '4.1');
if (!defined('ERROR_MYSQL_SUPPORT_MISSING')) define('ERROR_MYSQL_SUPPORT_MISSING', 'PHP can\'t find MySQL support but Wikka requires MySQL. Please check the output of <tt>phpinfo()</tt> in a php document for MySQL support: it needs to be compiled into PHP, the module itself needs to be present in the expected location, <strong>and</strong> php.ini needs to have it enabled.<br />Also note that you cannot have <tt>mysqli</tt> and <tt>mysql</tt> support both enabled at the same time.<br />Please double-check all of these things, restart your webserver after any fixes, and then try again!');
if (!defined('ERROR_SETUP_FILE_MISSING')) define('ERROR_SETUP_FILE_MISSING', 'A file of the installer/ upgrader was not found. Please install Wikka again!');
if (!defined('ERROR_SETUP_HEADER_MISSING')) define('ERROR_SETUP_HEADER_MISSING', 'The file "setup/header.php" was not found. Please install Wikka again!');
if (!defined('ERROR_SETUP_FOOTER_MISSING')) define('ERROR_SETUP_FOOTER_MISSING', 'The file "setup/footer.php" was not found. Please install Wikka again!');
if (!defined('ERROR_HEADER_MISSING')) define('ERROR_HEADER_MISSING', 'A header template could not be found. Please make sure that a file called <code>header.php</code> exists in the templates directory.'); //TODO Make sure this message matches any filename/folder change 
if (!defined('ERROR_FOOTER_MISSING')) define('ERROR_FOOTER_MISSING', 'A footer template could not be found. Please make sure that a file called <code>footer.php</code> exists in the templates directory.'); //TODO Make sure this message matches any filename/folder change 
if (!defined('ERROR_NO_DB_ACCESS')) define('ERROR_NO_DB_ACCESS', 'The wiki is currently unavailable. <br /><br />Error: Unable to connect to the MySQL database.');
if (!defined('PAGE_GENERATION_TIME')) define('PAGE_GENERATION_TIME', 'Page was generated in %.4f seconds'); // %.4f - generation time in seconds with 4 digits after the dot
if (!defined('WIKI_UPGRADE_NOTICE')) define('WIKI_UPGRADE_NOTICE', 'This site is currently being upgraded. Please try again later.');
/**#@-*/
/**
 * Defines the current Wikka version. Do not change the version number or you will have problems upgrading.
 */
if (!defined('WAKKA_VERSION')) define('WAKKA_VERSION', '1.2');

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

// Sanity checks - we die if these conditions aren't met

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

// Sanity checks OK - start rolling....

ob_start();
global $tstart;
$tstart = getmicrotime();
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
	$t_request .= '/';
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
	'gui_editor'				=> '1',
	'theme'						=> 'light',

	// formatter and code highlighting paths
	'wikka_formatter_path' 		=> 'plugins/formatters'.PATH_DIVIDER.'formatters',		# (location of Wikka formatter - REQUIRED)
	'wikka_highlighters_path'	=> 'formatters',		# (location of Wikka code highlighters - REQUIRED)
	'geshi_path' 				=> '3rdparty/plugins/geshi',				# (location of GeSHi package)
	'geshi_languages_path' 		=> '3rdparty/plugins/geshi/geshi',		# (location of GeSHi language highlighting files)

	// template
	'wikka_template_path' 		=> 'plugins/templates'.PATH_DIVIDER.'templates',		# (location of Wikka template files - REQUIRED)
	'safehtml_path'				=> '3rdparty/core/safehtml',
	'referrers_purge_time'		=> '30',
	'pages_purge_time'			=> '0',
	'xml_recent_changes'		=> '10',
	'hide_comments'				=> '0',
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
	'default_comment_acl'		=> '*',
	'allow_user_registration'	=> '1',
	'enable_version_check'      => '1',
	'version_check_interval'	=> '1h'
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

$wakkaConfig = array_merge($wakkaDefaultConfig, $wakkaConfig);	// merge defaults with config from file

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
if (!isset($method)) $method='';
$wakka->Run($page, $method);
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
