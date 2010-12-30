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
 */ 

if ($this->IsAdmin())
{
	$odd_row = TRUE;
	$settings = $this->config;
	//array of sensitive config options to exclude from the output 
	$hide_options = array('mysql_host', 'mysql_user', 'mysql_password');

	$wc_output = '<table class="data wikkaconfig">'."\n";
	$wc_output .= '	<caption>'.sprintf(T_("Wikka Configuration Settings [%s]"), '<a href="http://docs.wikkawiki.org/ConfigurationOptions" target="_blank" title="'.T_("Read the documentation on Wikka Configuration Settings").'">?</a>').'</caption>'."\n";
	$wc_output .= '	<thead>'."\n";
	$wc_output .= '		<tr>'."\n";
	$wc_output .= '			<th scope="col">'.T_("Option").'</th><th scope="col">'.T_("Value").'</th>'."\n";
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
