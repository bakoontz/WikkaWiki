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
 * 
 * @uses Wakka::hsc_secure()
 * @uses Wakka::StaticHref()
 * @uses Config::$default_lang
 * @todo	check if we should (copy and) use Wakka::GetSafeVar() and 
 * 			Wakka::Wakka::cleanUrl() to secure our input parameters 
 * 			(cf. mindmap action!)
 * @todo	since this produces a whole page, turn into a normal handler
 */

if (!defined('FREEMIND_PROJECT_URL')) define('FREEMIND_PROJECT_URL', 'http://freemind.sourceforge.net/');
if (!defined('WIKKA_JRE_DOWNLOAD_URL')) define('WIKKA_JRE_DOWNLOAD_URL','http://www.java.com/getjava');
if (!defined('SAMPLE_SYNTAX_URL')) define('SAMPLE_SYNTAX_URL','http://example.com/MapName/mindmap.mm');
if (!defined('SAMPLE_SYNTAX1')) define('SAMPLE_SYNTAX1','{{mindmap '.SAMPLE_SYNTAX_URL.'}}');
if (!defined('SAMPLE_SYNTAX2')) define('SAMPLE_SYNTAX2','{{mindmap url="'.SAMPLE_SYNTAX_URL.'"}}');

if (!isset($_GET['url'])) return;
/**
 * Include language file if one exists.
 *
 * @see		en.inc.php
 * @todo	is this necessary? if so why?
 */
$default_lang	= $this->GetConfigValue('default_lang');
#$fallback_lang	= 'en';							// should always be available
$fallback_lang	= CONFIG_DEFAULT_LANGUAGE;
$default_language_file  = WIKKA_LANG_PATH.DIRECTORY_SEPARATOR.$default_lang.DIRECTORY_SEPARATOR.$default_lang.'.inc.php';
$fallback_language_file = WIKKA_LANG_PATH.DIRECTORY_SEPARATOR.$fallback_lang.DIRECTORY_SEPARATOR.$fallback_lang.'.inc.php';
$language_file_not_found = sprintf(ERROR_LANGUAGE_FILE_MISSING,$default_language_file);
if (file_exists($default_language_file))
{
	require_once $default_language_file;	// todo add base dir
}
elseif (file_exists($fallback_language_file))
{
	require_once $fallback_language_file;	// silent fallback
}
else
{
	// use global constant
	#die('Language file ('.$default_language_file.') not found! Please add the file.');
	die($language_file_not_found);
}
?>
<?php header('Content-Type: text/html; charset=ISO-8859-1'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
	"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<title>Mind map fullscreen</title>
	<style type="text/css">
html, body { height: 100%; }
.floatr { float: right; width: 48%; margin: 0.5%; padding: 0.5%; background: #EEE; }
	</style>
</head>
<body>

<?php
$mindmap_url = $this->hsc_secure(preg_replace('/&amp;/','&',(trim($_GET['url'])))); #312 // duplicates Wakka::cleanUrl()
if (isset($_GET['height'])) $height = $this->hsc_secure(trim($_GET['height'])); #312 // more or less equivalent to Wakka::GetSafeVar()

if ($mindmap_url) 
{
	// set up template variables
	$close_window = CLOSE_WINDOW;
	$height = (isset($height)) ? $height : '80%';	// try to avoid vertical scrollbar on window (NOTE: mindmap action uses '550' as default)
	$jre_plugin_link = '<a href="'.WIKKA_JRE_DOWNLOAD_URL.'">'.MM_GET_JAVA_PLUGIN_LINK_DESC.'</a>';
	$jre_download_link = '<a href="'.WIKKA_JRE_DOWNLOAD_URL.'">'.WIKKA_JRE_LINK_DESC.'</a>';
	$jre_install_req = sprintf(MM_JRE_INSTALL_REQ, $jre_download_link);
	$jre_install_req_js = str_replace(array('\\', '\'', "\r", "\n") ,array('\\\\', '\\\'', '', '\\n'), $jre_install_req);	// escape slashes for JavaScript 
	$jre_install_req_sub = $jre_install_req; 
	$jre_install_req_sub[0] = strtolower($jre_install_req_sub[0]);	// lower case first char for use in subphrase
	$freemind_link = '<a href="'.FREEMIND_PROJECT_URL.'">Freemind</a>';

	$mm_download_link = '<a href="'.$mindmap_url.'">'.MM_DOWNLOAD_LINK_DESC.'</a>';
	$mm_edit = sprintf(MM_EDIT,$freemind_link);
	$mm_note = WIKKA_NOTE;
	$mm_plugin_needed = WIKKA_JAVA_PLUGIN_NEEDED;
	$mm_get_plugin = sprintf(MM_GET_JAVA_PLUGIN,$jre_install_req_sub);

	#$mm_archive = $this->StaticHref('3rdparty/plugins/freemind/freemindbrowser.jar');
	$mm_archivepath = $this->StaticHref($this->GetConfigValue('freemind_uripath').'/freemindbrowser.jar');
	// define template
	$mm_template = <<<TPLMMTEMPLATE
	<span class="floatr"><a href="#" onclick="window.close('fullmindmap')">$close_window</a></span><br />
	<div class="mindmap" style="height: 100%; clear:both;">
	<script type="text/javascript">
	<!--
		if (!navigator.javaEnabled()) {
			document.write('{$jre_install_req_js}');
		}
	//-->
	</script>
	<applet code="freemind.main.FreeMindApplet.class" archive="$mm_archivepath" width="100%" height="$height">
		<param name="type" value="application/x-java-applet;version=1.4" />
		<param name="scriptable" value="false" />
		<param name="modes" value="freemind.modes.browsemode.BrowseMode" />
		<param name="browsemode_initial_map" value="$mindmap_url" />
		<param name="initial_mode" value="Browse" />
		<param name="selection_method" value="selection_method_direct" />
	</applet>
	<br />
	<span class="floatr">$mm_download_link :: $mm_edit :: <a href="#" onclick="window.close('fullmindmap')">$close_window</a></span>
	<br /><strong>$mm_note</strong> $mm_plugin_needed $mm_get_plugin
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
