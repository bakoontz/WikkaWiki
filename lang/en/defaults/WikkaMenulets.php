<?php
	$filename = 'lang/'.$config['default_lang'].'/defaults/_WikkaMenulets.php';
	$fp = fopen($filename, 'r');
	$contents = fread($fp, filesize($filename));
	echo $contents;
?>
