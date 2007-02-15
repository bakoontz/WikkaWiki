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

if (!defined('FREEMIND_PROJECT_URL')) define('FREEMIND_PROJECT_URL', 'http://freemind.sourceforge.net/');
if (!defined('WIKKA_JRE_DOWNLOAD_URL')) define('WIKKA_JRE_DOWNLOAD_URL','http://www.java.com/');	# @@@ I don't think that's the correct link now! JW
if (!defined('SAMPLE_SYNTAX_URL')) define('SAMPLE_SYNTAX_URL','http://example.com/MapName/mindmap.mm');
if (!defined('SAMPLE_SYNTAX1')) define('SAMPLE_SYNTAX1','{{mindmap '.SAMPLE_SYNTAX_URL.'}}');
if (!defined('SAMPLE_SYNTAX2')) define('SAMPLE_SYNTAX2','{{mindmap url="'.SAMPLE_SYNTAX_URL.'"}}');

/**
 * Include language file if it exists.
 * @see /lang/en.inc.php
 */
if (file_exists('../../../lang/en.inc.php')) require_once('../../../lang/en.inc.php'); // todo add base dir
else die('Language File (/lang/en.inc.php) not found! Please add the file.');
?>
<?php header("Content-Type: text/html; charset=ISO-8859-1");  ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
   "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
   <head>
      <title>mind map fullscreen</title>
      <style type="text/css">
	   html, body {height: 100%}
	   .floatr {float: right; width: 48%; margin: 0.5%; padding: 0.5%; background: #EEE;}
      </style>
   </head>
   <body>

<?php
$mindmap_url = htmlspecialchars(preg_replace('/&amp;/','&',(trim($_REQUEST["url"]))));
if (isset($_REQUEST["height"])) $height = htmlspecialchars(trim($_REQUEST["height"]));

if ($mindmap_url) {

	// set up template variables
	$height = (isset($height)) ? $height : '100%';
	$close_window = CLOSE_WINDOW;
	$jre_plugin_link = '<a href="'.WIKKA_JRE_DOWNLOAD_URL.'">'.MM_GET_JAVA_PLUGIN_LINK_DESC.'</a>';
	$jre_download_link = '<a href="'.WIKKA_JRE_DOWNLOAD_URL.'">'.WIKKA_JRE_LINK_DESC.'</a>';
	$jre_install_req = str_replace('/','\/',sprintf(MM_JRE_INSTALL_REQ,$jre_download_link));
	$mm_note = WIKKA_NOTE;
	$mm_plugin_needed = WIKKA_JAVA_PLUGIN_NEEDED;
	$mm_get_plugin = MM_GET_JAVA_PLUGIN;
	$mm_download_link = '<a href="'.$mindmap_url.'">'.MM_DOWNLOAD_LINK_DESC.'</a>';
	$freemind_link = '<a href="'.FREEMIND_PROJECT_URL.'">Freemind</a>';
	$mm_edit = sprintf(MM_EDIT,$freemind_link);

	// define template
	$mm_template = <<<TPLMMTEMPLATE
	<span class="floatr"><a href="#" onclick="window.close('fullmindmap')">$close_window</a></span><br />
	<div class="mindmap" style="height: 100%; clear:both;"><script type="text/javascript">
	<!--
	    if(!navigator.javaEnabled()) {
	        document.write('{$jre_install_req}');
	    }
	//-->
	</script>
	<applet code="freemind.main.FreeMindApplet.class" archive="freemindbrowser.jar" width="100%" height="$height">
	  <param name="type" value="application/x-java-applet;version=1.4" />
	  <param name="scriptable" value="false" />
	  <param name="modes" value="freemind.modes.browsemode.BrowseMode" />
	  <param name="browsemode_initial_map" value="$mindmap_url" />
	  <param name="initial_mode" value="Browse" />
	  <param name="selection_method" value="selection_method_direct" />
	</applet>
	<br />
	<span class="floatr">$mm_download_link :: $mm_edit :: <a href="#" onclick="window.close('fullmindmap')">$close_window</a></span>
	<br /><strong>$jre_note</strong> $mm_plugin_needed<br />$mm_get_plugin
	</div>
TPLMMTEMPLATE;

	// display template
	echo $mm_template;

} else {
	echo '<span class="error"><em>'.ERROR_INVALID_MM_SYNTAX.'<br />'.sprintf(PROPER_USAGE_MM_SYNTAX,SAMPLE_SYNTAX1,SAMPLE_SYNTAX2).'</em></span>';
}

?>

   </body>
</html>