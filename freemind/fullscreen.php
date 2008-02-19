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

$mindmap_url = $_REQUEST["url"];
$height = $_REQUEST["height"];

if ($mindmap_url) {

	if (!$height) $height = "100%";

	$output = 
	"<span class=\"floatright\"> <A HREF=\"#\" onclick=\"window.close('fullmindmap')\">Close Window</A> </span> <br /> <div class=\"mindmap\" style=\"height: 100%; clear:both;\"><script type=\"text/javascript\">\n".
	"<!--\n".
	"    if(!navigator.javaEnabled()) {\n".
	"        document.write('Please install a <a href=\"http://www.java.com\">Java Runtime Environment<\/a> on your computer.');\n".
	"    }\n".
	"//-->\n".
	"</script>\n".
	"<!-- MS IE (Microsoft Internet Explorer) will use outer object -->\n".
	"<object classid=\"clsid:8AD9C840-044E-11D1-B3E9-00805F499D93\" \n".
	"	codebase=\"http://java.sun.com/products/plugin/autodl/jinstall-1_4_2_05-windows-i586.cab#Version=1,4,1,0\" \n".
	"	height=\"100%\" width=\"100%\" > \n".
	"	<param name=\"scriptable\" value=\"false\" />\n".
	"	<param name=\"code\" value=\"freemind.main.FreeMindApplet.class\" />\n".
	"	<param name=\"archive\" value=\"freemindbrowser.jar\" />\n".
	"	<param name=\"modes\" value=\"freemind.modes.browsemode.BrowseMode\" />\n".
	"	<param name=\"browsemode_initial_map\" value=\"$mindmap_url\" />\n".
	"	<param name=\"initial_mode\" value=\"Browse\" />\n".
	"	<!--[if !IE]> Mozilla/Netscape and others will use inner object -->\n".
	"	<object classid=\"java:freemind.main.FreeMindApplet.class\" \n".
	"		archive=\"freemindbrowser.jar\" \n".
	"		height=\"100%\" width=\"100%\"> \n".
	"		<param name=\"scriptable\" value=\"false\" />\n".
	"		<param name=\"modes=\" value=\"freemind.modes.browsemode.BrowseMode\" />\n".
	"		<param name=\"browsemode_initial_map\" value=\"$mindmap_url\" />\n".
	"		<param name=\"initial_mode\" value=\"Browse\" />\n".
	"	   <strong>This browser does not have a Java Plug-in.\n".
	"	   <br />\n".
	"	   <a href=\"http://java.sun.com/products/plugin/downloads/index.html\">\n".
	"	   Get the latest Java Plug-in here.</a>\n".
	"	   </strong>\n".
	"	</object> \n".
	"	<!-- <![endif]-->\n".
	"</object>\n".
	"<br />\n".
	"<span class=\"floatright\"> <a href=\"$mindmap_url\">Download this mind map</a> :: Use <a href=\"http://freemind.sourceforge.net/\">Freemind</a> to edit it :: <A HREF=\"#\" onclick=\"window.close('fullmindmap')\">Close Window</A></span>\n".
	"<br /><strong>NOTE:</strong>Java 1.4.1 (or later) Plug-in is needed to run this applet,<br /> so if it does not work,\n".             
      "<a href=\"http://java.com/\"> get the latest Java Plug-in here.</a>\n</div>";

	print($output);

} else {
	echo "<span class='error'><em>Error: Invalid MindMap action syntax. <br /> Proper usage: {{mindmap http://domain.com/MapName/mindmap.mm}} or {{mindmap url=\"http://domain.com/MapName/mindmap.mm\"}}</em></span>";
}

?>

   </body>
</html>