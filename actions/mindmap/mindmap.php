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
 */

if (!defined('FREEMIND_PROJECT_URL')) define('FREEMIND_PROJECT_URL', 'http://freemind.sourceforge.net/');
if (!defined('WIKKA_JRE_DOWNLOAD_URL')) define('WIKKA_JRE_DOWNLOAD_URL','http://www.java.com/getjava');
if (!defined('SAMPLE_SYNTAX_URL')) define('SAMPLE_SYNTAX_URL','http://example.com/MapName/mindmap.mm');
if (!defined('SAMPLE_SYNTAX1')) define('SAMPLE_SYNTAX1','{{mindmap '.SAMPLE_SYNTAX_URL.'}}');
if (!defined('SAMPLE_SYNTAX2')) define('SAMPLE_SYNTAX2','{{mindmap url="'.SAMPLE_SYNTAX_URL.'"}}');


// get action input
$mindmap_url = $vars['url'];
if ((!$mindmap_url && !$height) && $wikka_vars)
{
	$mindmap_url = $wikka_vars;
}
$mindmap_url = $this->cleanUrl(trim($mindmap_url));
$height = $this->htmlspecialchars_ent(trim($vars['height']));
if (!$height) $height = '550';

// output
if ($mindmap_url) 
{
	// set up template variables
	$jre_download_link = '<a href="'.WIKKA_JRE_DOWNLOAD_URL.'">'.WIKKA_JRE_LINK_DESC.'</a>';
	$jre_install_req = sprintf(MM_JRE_INSTALL_REQ,$jre_download_link);
	$jre_install_req_js = str_replace('/','\/',$jre_install_req);	// escape slashes for JavaScript 
	$freemind_link = '<a href="'.FREEMIND_PROJECT_URL.'">Freemind</a>';

	$mm_download_link = '<a href="'.$mindmap_url.'">'.MM_DOWNLOAD_LINK_DESC.'</a>';
	$mm_edit = sprintf(MM_EDIT,$freemind_link);
	$mm_url_fullscreen = "3rdparty/plugins/freemind/fullscreen.php?url=$mindmap_url";
	$mm_fullscreen_link = '<a href="'.$mm_url_fullscreen.'" onclick="return popup(this,\'fullmindmap\')">'.MM_FULLSCREEN_LINK_DESC.'</a>';

	// define template
	$mm_template = <<<TPLMINDMAP
	<script type="text/javascript" language="JavaScript">
	<!--
	    if (!navigator.javaEnabled()) {
	        document.write('{$jre_install_req_js}');
	    }
		function popup(mylink, windowname)
		{
			if (! window.focus) return true;
			var href;
			if (typeof(mylink) == 'string')
				href=mylink;
			else
				href=mylink.href;
			window.open(href, windowname, ',type=fullWindow,fullscreen,scrollbars=yes');
			return false;
		}
	//-->
	</script>
	<applet code="freemind.main.FreeMindApplet.class" archive="3rdparty/plugins/freemind/freemindbrowser.jar" width="100%" height="$height">
		<param name="type" value="application/x-java-applet;version=1.4" />
		<param name="scriptable" value="false" />
		<param name="modes" value="freemind.modes.browsemode.BrowseMode" />
		<param name="browsemode_initial_map" value="$mindmap_url" />
		<param name="initial_mode" value="Browse" />
		<param name="selection_method" value="selection_method_direct" />
	</applet>
	<br />
	<span class="floatr">$mm_download_link :: $mm_edit :: $mm_fullscreen_link</span>
	<div style="clear:both;"></div>
TPLMINDMAP;

	// display template
	echo $mm_template;
} 
else 
{
	echo '<span class="error"><em>'.ERROR_INVALID_MM_SYNTAX.'<br />'.sprintf(PROPER_USAGE_MM_SYNTAX,SAMPLE_SYNTAX1,SAMPLE_SYNTAX2).'</em></span>';
}
?>