<?php
	@session_start();
	$_SESSION['mod_rewrite'] = 'ok';
	include('test-mod-rewrite.php');
	die();
?>
