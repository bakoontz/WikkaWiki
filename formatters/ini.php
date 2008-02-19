<?php

/*$text = "; Menu specification file for Opera 7.0

[Version]
File Version=2

[Info]  #background info
Name=Munin++ Menu
Description=Munin++ Menu
Author=NonTroppo (originally by Rijk van Geijtenbeek)
Version=1.9";*/

$text = htmlspecialchars($text, ENT_QUOTES);

$text = preg_replace("/([=,\|]+)/m","<span style=\"color:#4400DD\">\\1</span>",$text);
$text = preg_replace("/^([;#].+)$/m","<span style=\"color:#226622\">\\1</span>",$text);
$text = preg_replace("/([^\d\w#;:>])([;#].+)$/m","<span style=\"color:#226622\">\\2</span>",$text);
$text = preg_replace("/^(\[.*\])/m","<strong style=\"color:#AA0000;background:#EEE0CC\">\\1</strong>",$text);
print "<pre>".$text."</pre>";

?>
