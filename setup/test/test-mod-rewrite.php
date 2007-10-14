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

$url = 'http://'.$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT'] != 80 ? ':'.$_SERVER['SERVER_PORT'] : '');
$url .= preg_replace('/setup\\/test\\/(test-mod-rewrite|rewrite-ok)\\.php/', '', $_SERVER['SCRIPT_NAME']);	
@session_start();
$config = isset($_POST['pconfig']) ? $_POST['pconfig'] : array();
unset ($_POST['pconfig']);
$_SESSION['wikka'][$_POST['installAction']] = $_POST;
$_SESSION['sconfig'] = array_merge( $_SESSION['sconfig'], $config);
session_write_close(); 
header('Location: '.$url.'wikka.php?installAction='.$_POST['installAction'].'&nonce='.dechex(crc32(rand())));
die();
?>
