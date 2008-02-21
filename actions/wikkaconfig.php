<?php
/**
 * Display a table with the configuration settings for the current wiki.
 * 
 * This admin-only action reads the configuration settings from the config file and displays them in a table for ease of reference.
 * It is accessible by default via the SysInfo page.
 * 
 * @package	Actions
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 * 
 * @uses	Wakka::IsAdmin()
 * 
 * @todo	Use core method to generate tables with alternate rows
 * @todo	Move translation strings to lang file
 */ 

//i18n strings
if (!defined('WIKKACONFIG_CAPTION')) define('WIKKACONFIG_CAPTION', "Wikka Configuration Settings [%s]"); // %s link to Wikka Config options documentation
if (!defined('WIKKACONFIG_DOCS_URL')) define('WIKKACONFIG_DOCS_URL', "http://docs.wikkawiki.org/ConfigurationOptions");
if (!defined('WIKKACONFIG_DOCS_TITLE')) define('WIKKACONFIG_DOCS_TITLE', "Read the documentation on Wikka Configuration Settings");
if (!defined('WIKKACONFIG_TH_OPTION')) define('WIKKACONFIG_TH_OPTION', "Option");
if (!defined('WIKKACONFIG_TH_VALUE')) define('WIKKACONFIG_TH_VALUE', "Value");

if ($this->IsAdmin())
{
	$odd_row = TRUE;
	$settings = $this->config;
	//array of sensitive config options to exclude from the output 
	$hide_options = array('mysql_host', 'mysql_user', 'mysql_password');

	$wc_output = '<table class="data wikkaconfig">'."\n";
	$wc_output .= '	<caption>'.sprintf(WIKKACONFIG_CAPTION, '<a href="'.WIKKACONFIG_DOCS_URL.'" target="_blank" title="'.WIKKACONFIG_DOCS_TITLE.'">?</a>').'</caption>'."\n";
	$wc_output .= '	<thead>'."\n";
	$wc_output .= '		<tr>'."\n";
	$wc_output .= '			<th scope="col">'.WIKKACONFIG_TH_OPTION.'</th><th scope="col">'.WIKKACONFIG_TH_VALUE.'</th>'."\n";
	$wc_output .= '		</tr>'."\n";
	$wc_output .= '	</thead>'."\n";
	$wc_output .= '	<tbody>'."\n";
	
	//go through config array
	foreach ($settings as $key => $value)
	{
		//only display safe and non-empty settings
		if (!in_array($key, $hide_options) && strlen($value)>0)
		{
			//alternate row coloring
			if ($odd_row)
			{
				$alt = ' class="alt"';
				$odd_row = FALSE;
			}
		 	else
			{
				$alt =  '';
				$odd_row = TRUE;
			}
			$wc_output .= '		<tr'.$alt.'>'."\n";
			$wc_output .= '			<th scope="row">'.$key.'</th><td><tt>'.$value.'</tt></td>'."\n";
			$wc_output .= '		</tr>'."\n";
		}
	}
	$wc_output .= '	</tbody>'."\n";
	$wc_output .= '</table>'."\n";	
	echo $wc_output;
}
?>