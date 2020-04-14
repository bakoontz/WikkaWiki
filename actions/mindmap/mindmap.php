<?php
/**
 * Display a mindmap using the Java-Apllet bundeled in 3rdparty/plugins/freemind/.
 * 
 * @uses	Wakka::cleanUrl()
 * @uses	Wakka::htmlspecialchars_ent()
 * 
 * @see		3rdparty/plugins/freemind/
 * 
 * @todo	New version of the freemind-browser (#691)
 */

// height
$height = "550";
if(isset($vars['height'])) $height = $this->htmlspecialchars_ent(trim($vars['height']));

// URL to the map
$mindmap_url = '';
if (isset($vars['url'])) $mindmap_url = $vars['url'];
else if (isset($wikka_vars)) $mindmap_url = $wikka_vars; // backwards compatibility for {{mindmap http://domain.com/MapName/mindmap.mm}}
$mindmap_url = $this->cleanUrl(trim($mindmap_url));

if ('' != $mindmap_url) 
{
	$mindmap_url_fullscreen = '3rdparty/plugins/freemind/fullscreen.php?url='.$mindmap_url;

	$output =
	'<script type="text/javascript" language="JavaScript">'."\n".
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
	'<applet code="freemind.main.FreeMindApplet.class" archive="3rdparty/plugins/freemind/freemindbrowser.jar" width="100%" height="'.$height.'">'."\n".
	'	<param name="type" value="application/x-java-applet;version=1.4" />'."\n".
	' 	<param name="scriptable" value="false" />'."\n".
	'	<param name="modes" value="freemind.modes.browsemode.BrowseMode" />'."\n".
	'	<param name="browsemode_initial_map" value="'.$mindmap_url.'" />'."\n".
	'	<param name="initial_mode" value="Browse" />'."\n".
	'	<param name="selection_method" value="selection_method_direct" />'."\n".
	"</applet>\n".
	"<br />\n".
	'<span class="floatr"><a href="'.$mindmap_url.'">Download this mind map</a> :: Use <a href="http://freemind.sourceforge.net/">Freemind</a> to edit it :: <a href="'.$mindmap_url_fullscreen."\" onclick=\"return popup(this,'fullmindmap')\">Open fullscreen</a></span><div style=\"clear:both;\"></div>\n";

	echo $output;

} else 
{
	echo '<em class="error">Error: Invalid MindMap action syntax. <br /> Proper usage: {{mindmap http://domain.com/MapName/mindmap.mm}} or {{mindmap url="http://domain.com/MapName/mindmap.mm"}}</em>';
}

?>
