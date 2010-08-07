<?php
/*
 Script: convertHTMLEntities.php

 This script will convert HTML entities (values stored in the format
 "&#12345") in your Wikka database to UTF-8 multibyte characters. This
 conversion is necessary if you are upgrading from version 1.2-p1 or
 earlier to version 1.3 or later and you have been using UTF-8
 character encodings.  If you have not been using HTML entities for
 language characters, it is not necessary to run this script. 

 Before running this script:

 ***BACKUP YOUR DATABASE!***BACKUP YOUR DATABASE!***BACKUP YOUR DATABASE!***

 We recommended this script be executed from the command line.  If you
 attempt to execute this script from within Wikka, the script may time
 out for large databases.

 Please note that your PHP version must be enabled with the mbstring
 extension for this script to work.  Details about this process can be
 found here: http://www.php.net/manual/en/mbstring.installation.php

 ===> To run this script from the command line <===
 Run this script from within your scripts directory/folder:

 php.exe -f convertHTMLEntities.php (Windows)
 php -f convertHTMLEntities.php (Unix/OSX)

 Note that you might need to specify the directory from which to run
 your php executable.

 ===> To run this script from within Wikka <===
 Create an .htaccess file in your scripts directory with the following
 line:

 RewriteEngine Off

 Point your browser to this script, specifying the scripts directory:

 http://your.wikka.url/scripts/convertHTMLEntities.php

 BE SURE TO DELETE THE .htaccess FILE AFTER RUNNING THE SCRIPT!  If
 you are not using mod_rewrite, you might also want to delete the
 script as well, as it will not be needed for future upgrades.
*/
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<?php

include_once('../wikka.config.php');

function replace_callback($matches)
{

	$new = mb_convert_encoding($matches[1], 'UTF-8', 'HTML-ENTITIES');
	return $new;
}

echo '<pre>', PHP_EOL;

$db = @mysql_connect($wakkaConfig['mysql_host'], $wakkaConfig['mysql_user'], $wakkaConfig['mysql_password'], true);
if(!@mysql_select_db($wakkaConfig['mysql_database'], $db))
	die("select db");
mysql_query("SET NAMES 'utf8'", $db);
$sql = "SELECT id FROM ".$wakkaConfig['table_prefix']."pages where latest='Y'";
$result = @mysql_query($sql, $db);
while($row = @mysql_fetch_assoc($result))
{
	$count = 0;
	$sql = "SELECT tag, time, body, owner, user, latest, note FROM ".$wakkaConfig['table_prefix']."pages WHERE id=".$row['id'];
	$result2 = mysql_fetch_assoc(@mysql_query($sql, $db));
	$body = $result2['body'];
	$body = preg_replace_callback('/(\&\#[0-9]+\;)/', 'replace_callback', $body, -1, $count);
	if($count > 0)
	{
		echo "Converting ".$result2['tag']." to UTF-8...";
		$note = "HTML entities converted to UTF-8"; 
		$sql = "INSERT INTO ".$wakkaConfig['table_prefix']."pages (tag, time, body, owner, user, latest, note) VALUES ('".$result2['tag']."', now(), '".mysql_escape_string($body)."', '".$result2['owner']."', '".$result2['user']."', 'Y', '".$note."')";
		mysql_query($sql, $db);
		$sql = "UPDATE ".$wakkaConfig['table_prefix']."pages SET latest = 'N' WHERE id = ".$row['id'];
		mysql_query($sql, $db);
		echo "done!", PHP_EOL;
	}
}
@mysql_close($db);

echo "Conversion complete.", PHP_EOL;
echo '</pre>', PHP_EOL;

?>
</body>
</html>
