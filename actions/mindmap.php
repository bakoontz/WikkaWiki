<?php
/**
 * Embed a mindmap in the current page.
 * 
 * @package		Actions
 * @version		$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses	Wakka::cleanUrl()
 * @uses	Wakka::htmlspecialchars_ent()
 * @todo should we keep an action for mindmaps on top of the handler/formatter?
 */

/**
 * Used by EDIT_MM so url needs to be changed only once.
 */
if (!defined('FREEMIND_PROJECT_URL')) define('FREEMIND_PROJECT_URL', 'http://freemind.sourceforge.net/');

$mindmap_url = $vars['url'];
if ((!$mindmap_url && !$height) && $wikka_vars) $mindmap_url = $wikka_vars;
$mindmap_url = $this->cleanUrl(trim($mindmap_url));
$height = $this->htmlspecialchars_ent(trim($vars['height']));

if ($mindmap_url) 
{
	if (!$height) $height = "550";
	$mindmap_url_fullscreen = "3rdparty/plugins/freemind/fullscreen.php?url=$mindmap_url";

	$output =
	"<script type=\"text/javascript\" language=\"JavaScript\">\n".
	"<!--\n".
	"    if(!navigator.javaEnabled()) {\n".
	"        document.write('Please install a <a href=\"http://www.java.com/\">Java Runtime Environment</a> on your computer.');\n". #i18n
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
	'	<param name="scriptable" value="false" />'."\n".
	'	<param name="modes" value="freemind.modes.browsemode.BrowseMode" />'."\n".
	'	<param name="browsemode_initial_map" value="'.$mindmap_url.'" />'."\n".
	'	<param name="initial_mode" value="Browse" />'."\n".
	'	<param name="selection_method" value="selection_method_direct" />'."\n".
	"</applet>\n".
	"<br />\n".
	'<span class="floatr"><a href="'.$mindmap_url.'">'.DOWNLOAD_MM.'</a> :: '.EDIT_MM.' :: <a href="'.$mindmap_url_fullscreen."\" onclick=\"return popup(this,'fullmindmap')\">".MM_FULLSCREEN_LINK_TITLE."</a></span><div style=\"clear:both;\"></div>\n";

	print($output);
} 
else 
{
	echo '<span class="error"><em>'.ERROR_INVALID_MM_SYNTAX.'</em></span>';
}
?>