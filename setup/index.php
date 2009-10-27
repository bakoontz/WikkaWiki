<?php
/**
 * The installer mainscript.
 * 
 * This file is called anytime a valid or up-to-date config file cannot be found in
 * the main Wikka folder. It calls the different subroutines needed to install or upgrade.
 *
 * @package	Setup
 * @version	$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * @todo	remove meta tags - this page is not going to be indexed by search engines (or at least shouldn't be!)
 */

/**#@+
 * Default value.
 */
define('DEFAULT_SETUP_ACTION', 'default');
/**#@-*/

// define paths
$action_target = $_SERVER['SCRIPT_NAME'];
if (isset($_GET['nonce']))
{
	session_cache_limiter('public');
}
@session_start();
	if (!isset($_SESSION['sconfig'])) $_SESSION['sconfig'] = $wakkaConfig;
	if (isset($_POST) && !empty($_POST))
	{
		$config = isset($_POST['pconfig']) ? $_POST['pconfig'] : array();
		unset ($_POST['pconfig']);
		$_SESSION['wikka'][$_POST['installAction']] = $_POST;
		$_SESSION['sconfig'] = array_merge( $_SESSION['sconfig'], $config);
		session_write_close(); 
		header('Location: '.WIKKA_BASE_URL.'wikka.php?installAction='.$_POST['installAction'].'&nonce='.dechex(crc32(rand())));
		die();
	}
	else
	{
		$config = isset($_SESSION['sconfig']) ? $_SESSION['sconfig'] : array();
		if (isset($config['mysql_host']) && isset($config['mysql_user']) && isset($config['mysql_password']))
		{
			$dblink = @mysql_connect($config['mysql_host'], $config['mysql_user'], $config['mysql_password']);
		}
		if (isset($dblink) && $dblink && isset($config['mysql_database']))
		{
			@mysql_select_db($config['mysql_database'], $dblink);
		}
	}

// get utilities
/**
 * Setup function library
 *
 * @todo	rename file to functions.lib.php (or setup.lib.php to be more specific!) according to coding guidelines
 */
require_once WIKKA_SETUP_PATH.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'functions.inc.php';

// get subpage request
if (isset($_GET['installAction']))
{
	$installAction = trim($_GET['installAction']);
}
else
{
	$installAction = DEFAULT_SETUP_ACTION;
}

if (('grabinfo' == $installAction) || ('install' == $installAction))
{
	ob_start();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<!--START HTML head -->
<head>
	<title>Wikka Setup Wizard</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta name="keywords" content="WikkaWiki" />
	<meta name="description" content="<?php echo _p('A WakkaWiki fork');?>" />
<?php // @@@ use StaticHref() for href attributes? ?>
	<link rel="stylesheet" type="text/css" href="css/setup.css" media="screen" />
	<link rel="icon" href="images/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
</head>
<!--END HTML head -->
<!--START HTML body -->
<body>
<div class="header">
<?php // @@@ use StaticHref() for src attribute? ?>
	<img src="images/wikka_logo.jpg" alt="<?php echo _p('wikka logo');?>" title="<?php echo _p('Welcome to Wikka');?>" />
</div>
<!--START page body -->
<div class="page">
<?php
// load subpage
// use path constant
if (file_exists(WIKKA_SETUP_PATH.DIRECTORY_SEPARATOR.$installAction.'.php'))
	/**
	 * Installation phase script.
	 */
	include(WIKKA_SETUP_PATH.DIRECTORY_SEPARATOR.$installAction.'.php');
else
	print '<em>'.WIKKA_ERROR_SETUP_FILE_MISSING.'</em>';
?>
</div>
<!--END page body -->
</body>
<!--END HTML body -->
</html>
