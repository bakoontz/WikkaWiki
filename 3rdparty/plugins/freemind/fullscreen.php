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
 * @todo	check if we should (copy and) use Wakka::GetSafeVar() and 
 * 			Wakka::Wakka::cleanUrl() to secure our input parameters 
 * 			(cf. mindmap action!)
 * @todo	we probably should be using $_GET instead of $_REQUEST as well
 * @todo	fix JRE download URL
 */

if (!defined('FREEMIND_PROJECT_URL')) define('FREEMIND_PROJECT_URL', 'http://freemind.sourceforge.net/');
if (!defined('WIKKA_JRE_DOWNLOAD_URL')) define('WIKKA_JRE_DOWNLOAD_URL','http://www.java.com/');	// TODO @@@ I don't think that's the correct link now! JW
if (!defined('SAMPLE_SYNTAX_URL')) define('SAMPLE_SYNTAX_URL','http://example.com/MapName/mindmap.mm');
if (!defined('SAMPLE_SYNTAX1')) define('SAMPLE_SYNTAX1','{{mindmap '.SAMPLE_SYNTAX_URL.'}}');
if (!defined('SAMPLE_SYNTAX2')) define('SAMPLE_SYNTAX2','{{mindmap url="'.SAMPLE_SYNTAX_URL.'"}}');

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
#$mindmap_url = htmlspecialchars(preg_replace('/&amp;/','&',(trim($_REQUEST["url"]))));
#if (isset($_REQUEST["height"])) $height = htmlspecialchars(trim($_REQUEST["height"]));
$mindmap_url = hsc_secure(preg_replace('/&amp;/','&',(trim($_REQUEST['url'])))); // duplicates Wakka::cleanUrl()
if (isset($_REQUEST['height'])) $height = hsc_secure(trim($_REQUEST['height'])); // more or less equivalent to Wakka::GetSafeVar()

if ($mindmap_url) {

	// set up template variables
	$close_window = CLOSE_WINDOW;
	$height = (isset($height)) ? $height : '80%';	// try to avoid vertical scrollbar on window (NOTE: mindmap action uses '550' as default)
	$jre_plugin_link = '<a href="'.WIKKA_JRE_DOWNLOAD_URL.'">'.MM_GET_JAVA_PLUGIN_LINK_DESC.'</a>';
	$jre_download_link = '<a href="'.WIKKA_JRE_DOWNLOAD_URL.'">'.WIKKA_JRE_LINK_DESC.'</a>';
	$jre_install_req = sprintf(MM_JRE_INSTALL_REQ,$jre_download_link);
	$jre_install_req_sub = $jre_install_req; 
	$jre_install_req_sub[0] = strtolower($jre_install_req_sub[0]);	// lower case first char for use in subphrase
	$freemind_link = '<a href="'.FREEMIND_PROJECT_URL.'">Freemind</a>';

	$mm_download_link = '<a href="'.$mindmap_url.'">'.MM_DOWNLOAD_LINK_DESC.'</a>';
	$mm_edit = sprintf(MM_EDIT,$freemind_link);
	$mm_note = WIKKA_NOTE;
	$mm_plugin_needed = WIKKA_JAVA_PLUGIN_NEEDED;
	$mm_get_plugin = sprintf(MM_GET_JAVA_PLUGIN,$jre_install_req_sub);

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