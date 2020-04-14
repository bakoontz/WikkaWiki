<?php
/*
   Copyright (c) 2003,2004,2005,2009 Danilo Segan <danilo@kvota.net>.
   Copyright (c) 2005,2006 Steven Armstrong <sa@c-area.ch>

   This file is part of PHP-gettext.

   PHP-gettext is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   PHP-gettext is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with PHP-gettext; if not, write to the Free Software
   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

error_reporting(E_ALL | E_STRICT);

// define constants
define('PROJECT_DIR', realpath('./'));
define('LOCALE_DIR', PROJECT_DIR .'/locale');
define('DEFAULT_LOCALE', 'en_US');

require_once('../gettext.inc');

$supported_locales = array('en_US', 'sr_CS', 'de_CH');
$encoding = 'UTF-8';

$locale = (isset($_GET['lang']))? $_GET['lang'] : DEFAULT_LOCALE;

// gettext setup
T_setlocale(LC_MESSAGES, $locale);
// Set the text domain as 'messages'
$domain = 'messages';
T_bindtextdomain($domain, LOCALE_DIR);
T_bind_textdomain_codeset($domain, $encoding);
T_textdomain($domain);

header("Content-type: text/html; charset=$encoding");
?>
<html>
<head>
<title>PHP-gettext fallback example</title>
</head>
<body>
<h1>PHP-gettext as a fallback solution</h1>
<p>Example showing how to use PHP-gettext as a fallback solution if the native gettext library is not available or the system does not support the requested locale.</p>

<?php
print "<p>";
foreach($supported_locales as $l) {
	print "[<a href=\"?lang=$l\">$l</a>] ";
}
print "</p>\n";

if (!locale_emulation()) {
	print "<p>locale '$locale' is supported by your system, using native gettext implementation.</p>\n";
}
else {
	print "<p>locale '$locale' is <strong>not</strong> supported on your system, using custom gettext implementation.</p>\n";
}
?>

<hr />

<?php
// using PHP-gettext
print "<pre>";
print T_("This is how the story goes.\n\n");
for ($number=6; $number>=0; $number--) {
  print sprintf( T_ngettext("%d pig went to the market\n", 
			  "%d pigs went to the market\n", $number), 
		 $number );
}
print "</pre>\n";
?>

<hr />
<p>&laquo; <a href="./">back</a></p>
</body>
</html>
