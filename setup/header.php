<?php

// stuff
function test($text, $condition, $errorText = "", $stopOnError = 1) {
	print("$text ");
	if ($condition)
	{
		print("<span class=\"ok\">OK</span><br />\n");
	}
	else
	{
		print("<span class=\"failed\">FAILED</span>");
		if ($errorText) print(": ".$errorText);
		print("<br />\n");
		if ($stopOnError)
		{
			include('setup/footer.php');
			exit;
		}
	}
}

function myLocation()
{
	list($url, ) = explode("?", $_SERVER["REQUEST_URI"]);
	return $url;
}

/**
 * Delete a file, or a folder and its contents
 *
 * @author      Aidan Lister <aidan@php.net>
 * @version     1.0.2
 * @param       string   $dirname    Directory to delete
 * @return      bool     Returns TRUE on success, FALSE on failure
 */
function rmdirr($dirname)
{
    // Sanity check
    if (!file_exists($dirname)) {
        return false;
    }
 
    // Simple delete for a file
    if (is_file($dirname)) {
        return unlink($dirname);
    }
 
    // Loop through the folder
    $dir = dir($dirname);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }
 
        // Recurse
        rmdirr("$dirname/$entry");
    }
 
    // Clean up
    $dir->close();
    return rmdir($dirname);
}

function DeleteCookie($name) { SetCookie($name, "", 1, "/"); $_COOKIE[$name] = ""; }


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title>Wikka Installation</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta name="keywords" content="Wikka Wakka Wiki" />
	<meta name="description" content="A WakkaWiki clone" />
	<link rel="stylesheet" type="text/css" href="css/setup.css" media="screen" />
	<link rel="icon" href="images/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
</head>
<body>
<div class="header">
	<img src="images/wikka_logo.jpg" alt="wikka logo" title="Welcome to Wikka" />
</div>
<div class="page"><!--START page body -->