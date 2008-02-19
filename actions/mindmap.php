<?php

$mindmap_url = $vars['url'];
$height = $vars['height'];
if ((!$mindmap_url && !$height) && $wikka_vars) $mindmap_url = $wikka_vars;

if ($mindmap_url) {

	if (!$height) $height = "550";
	$mindmap_url_fullscreen = "freemind/fullscreen.php?url=$mindmap_url"; 

	$output = 
	"<script type=\"text/javascript\" language=\"JavaScript\">\n".
	"<!--\n".
	"    if(!navigator.javaEnabled()) {\n".
	"        document.write('Please install a <a href=\"http://www.java.com/\">Java Runtime Environment</a> on your computer.');\n".
	"    }\n".
	"function popup(mylink, windowname)\n".
	"{\n".
	"if (! window.focus)return true;\n".
	"var href;\n".
	"if (typeof(mylink) == 'string')\n".
	"   href=mylink;\n".
	"else\n".
	"   href=mylink.href;\n".
	"window.open(href, windowname, ',type=fullWindow,fullscreen,scrollbars=yes');\n".
	"return false;\n".
	"}\n".
	"//-->\n".
	"</script>\n".
	"<!-- MS IE (Microsoft Internet Explorer) will use outer object -->\n".
	"<object classid=\"clsid:8AD9C840-044E-11D1-B3E9-00805F499D93\" \n".
	"	codebase=\"http://java.sun.com/products/plugin/autodl/jinstall-1_4_2_05-windows-i586.cab#Version=1,4,1,0\" \n".
	"	height=\"$height\" width=\"100%\" > \n".
	"	<param name=\"scriptable\" value=\"false\" />\n".
	"	<param name=\"code\" value=\"freemind.main.FreeMindApplet.class\" />\n".
	"	<param name=\"archive\" value=\"freemind/freemindbrowser.jar\" />\n".
	"	<param name=\"modes\" value=\"freemind.modes.browsemode.BrowseMode\" />\n".
	"	<param name=\"browsemode_initial_map\" value=\"$mindmap_url\" />\n".
	"	<param name=\"initial_mode\" value=\"Browse\" />\n".
	"	<!--[if !IE]> Mozilla/Netscape and others will use inner object -->\n".
	"	<object classid=\"java:freemind.main.FreeMindApplet.class\" \n".
	"		archive=\"freemind/freemindbrowser.jar\" \n".
	"		height=\"$height\" width=\"100%\"> \n".
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
	"<span class=\"floatr\"><a href=\"$mindmap_url\">Download this mind map</a> :: Use <a href=\"http://freemind.sourceforge.net/\">Freemind</a> to edit it :: <a href=\"$mindmap_url_fullscreen\" onclick=\"return popup(this,'fullmindmap')\">Open fullscreen</a></span><div style=\"clear:both;\"></div>\n";

	print($output);

} else {
	echo "<span class='error'><em>Error: Invalid MindMap action syntax. <br /> Proper usage: {{mindmap http://domain.com/MapName/mindmap.mm}} or {{mindmap url=\"http://domain.com/MapName/mindmap.mm\"}}</em></span>";
}

?>