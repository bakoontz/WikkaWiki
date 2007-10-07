<?php
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
