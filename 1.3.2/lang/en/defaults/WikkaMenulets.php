<?php
	$filename = 'lang'.DIRECTORY_SEPARATOR.$config['default_lang'].DIRECTORY_SEPARATOR.'defaults'.DIRECTORY_SEPARATOR.'_WikkaMenulets.php';
	$fp = fopen($filename, 'r');
	$contents = fread($fp, filesize($filename));
	echo $contents;
?>
