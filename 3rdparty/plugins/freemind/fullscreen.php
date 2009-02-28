<?php
/**
 * Open a fullscreen window with an embedded Freemind map
 * 
 * @package  3rdParty
 * @subpackage Freemind
 * @author	{@link http://wikkawiki.org/JsnX Jason Tourtelotte} (first draft)
 * @author	{@link http://wikkawiki.org/JavaWoman Marjolein Katsma} (fixed notices, secured parameters, XHTML compliancy)
 * @license  http://gnu.org/copyleft/gpl.html GNU GPL
 * @version  $Id$
 * @filesource
 */

/**
 * Secure replacement for PHP built-in function htmlspecialchars().
 * 
 * Copy of Wakka::hsc_secure() - See Wakka.class.php for complete comments.
 */
function hsc_secure($string, $quote_style=ENT_COMPAT)
{
	// init
	$aTransSpecchar = array('&' => '&amp;',
							'"' => '&quot;',
							'<' => '&lt;',
							'>' => '&gt;'
							);			// ENT_COMPAT set
	if (ENT_NOQUOTES == $quote_style)	// don't convert double quotes
	{
		unset($aTransSpecchar['"']);
	}
	elseif (ENT_QUOTES == $quote_style)	// convert single quotes as well
	{
		$aTransSpecchar["'"] = '&#39;';	// (apos) htmlspecialchars() uses '&#039;'
	}
	// return translated string
	return strtr($string,$aTransSpecchar);
}

?>
<?php header("Content-Type: text/html; charset=ISO-8859-1");  ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
   "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
   <head>
      <title>mind map fullscreen</title>
      <style type="text/css">
	   html, body {height: 100%}
	   .floatright {float: right; width: 48%; margin: 0.5%; padding: 0.5%; background: #EEE;}
      </style>
   </head>
   <body>

<?php
$mindmap_url = hsc_secure(preg_replace('/&amp;/','&',(trim($_GET['url'])))); #312 // duplicates Wakka::cleanUrl()
if (isset($_GET['height'])) $height = hsc_secure(trim($_GET['height'])); #312 // more or less equivalent to Wakka::GetSafeVar()

if ($mindmap_url) {

	if (!isset($height)) $height = "100%";

	$output =
	"<span class=\"floatright\"> <a href=\"#\" onclick=\"window.close('fullmindmap')\">Close Window</a> </span> <br /> <div class=\"mindmap\" style=\"height: 100%; clear:both;\"><script type=\"text/javascript\">\n".
	"<!--\n".
	"    if(!navigator.javaEnabled()) {\n".
	"        document.write('Please install a <a href=\"http://www.java.com\">Java Runtime Environment<\/a> on your computer.');\n".
	"    }\n".
	"//-->\n".
	"</script>\n".
	"<applet code=\"freemind.main.FreeMindApplet.class\" archive=\"freemindbrowser.jar\" width=\"100%\" height=\"$height\">\n".
	"  <param name=\"type\" value=\"application/x-java-applet;version=1.4\" />\n".
	"  <param name=\"scriptable\" value=\"false\" />\n".
	"  <param name=\"modes\" value=\"freemind.modes.browsemode.BrowseMode\" />\n".
	"  <param name=\"browsemode_initial_map\" value=\"$mindmap_url\" />\n".
	"  <param name=\"initial_mode\" value=\"Browse\" />\n".
	"  <param name=\"selection_method\" value=\"selection_method_direct\" />\n".
	"</applet>\n".
	"<br />\n".
	"<span class=\"floatright\"> <a href=\"$mindmap_url\">Download this mind map</a> :: Use <a href=\"http://freemind.sourceforge.net/\">Freemind</a> to edit it :: <A HREF=\"#\" onclick=\"window.close('fullmindmap')\">Close Window</A></span>\n".
	"<br /><strong>NOTE:</strong>Java 1.4.1 (or later) Plug-in is needed to run this applet,<br /> so if it does not work,\n".
      "<a href=\"http://java.com/\"> get the latest Java Plug-in here.</a>\n</div>";

	print($output);

} else {
	echo "<em class='error'>Error: Invalid MindMap action syntax. <br /> Proper usage: {{mindmap http://domain.com/MapName/mindmap.mm}} or {{mindmap url=\"http://domain.com/MapName/mindmap.mm\"}}</em>";
}

?>

   </body>
</html>
