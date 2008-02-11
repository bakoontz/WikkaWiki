<?php
/**
 * This file is part of Wikka, a PHP wiki engine.
 *
 * This file saves input from user into session, and redirects to 
 * the normal process of the installation. If mod rewrite was
 * available, this file would not be called directly, but included
 * from rewrite-ok.php. The difference between the 2 files is that
 * the other one sets the value of $_SESSION['mod_rewrite'] to 'ok'.
 * @todo Case https or setup files in different directory.
 *
 * @package		Setup
 * @version		$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author	{@link http://wikkawiki.org/DotMG Mahefa Randimbisoa}
 *
 * @copyright	Copyright 2007, {@link http://wikkawiki.org/CreditsPage Wikka Development Team}
 */

// duplicate copy of wikka.php - to be moved to a function.
$scheme = ((isset($_SERVER['HTTPS'])) && !empty($_SERVER['HTTPS']) && 'off' != $_SERVER['HTTPS']) ? 'https://' : 'http://';
$server_port = ':'.$_SERVER['SERVER_PORT'];
if ((('http://' == $scheme) && (':80' == $server_port)) || (('https://' == $scheme) && (':443' == $server_port)))
{
	$server_port = '';
}
if (!defined('WIKKA_BASE_DOMAIN_URL')) define('WIKKA_BASE_DOMAIN_URL', $scheme.$_SERVER['SERVER_NAME'].$server_port);
define('WIKKA_BASE_URL_PATH', preg_replace('!setup/test/[^.]+.php!', '', $_SERVER['SCRIPT_NAME']));
define('WIKKA_BASE_URL', WIKKA_BASE_DOMAIN_URL.WIKKA_BASE_URL_PATH);

@session_start();
$config = isset($_POST['pconfig']) ? $_POST['pconfig'] : array();
unset ($_POST['pconfig']);
$_SESSION['wikka'][$_POST['installAction']] = $_POST;
$_SESSION['sconfig'] = array_merge( $_SESSION['sconfig'], $config);
session_write_close(); 
header('Location: '.WIKKA_BASE_URL_PATH.'wikka.php?installAction='.$_POST['installAction'].'&nonce='.dechex(crc32(rand())));
die();
?>
