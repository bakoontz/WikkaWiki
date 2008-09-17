<?php
/**
 * Display an infobox with the Wikka version in which a specific feature was added.
 */

//defaults
define('INFOBOX_UNTIL_LATEST_VERSION', '1.1.6.5');
define('INFOBOX_UNTIL_DEFAULT_ALIGN', 'float: right');
define('INFOBOX_UNTIL_DEFAULT_MARGIN', 'margin: 0 0 10px 10px');
$valid_versions = array('1.1.6.0', '1.1.6.1', '1.1.6.2', '1.1.6.3', '1.1.6.4', '1.1.6.5', '1.1.6.6', '1.1.7');

//color scheme array
$c = array(
			'A' => array('#699', '#BFFFFF', '#303030', '#A0E0E0', '#90B0B0'),
			'B' => array('#996', '#FFFFBF', '#303030', '#E0E0A0', '#B0B090'),
			'C' => array('#969', '#FFBFFF', '#303030', '#E0A0E0', '#B090B0'),
			'D' => array('#966', '#FFBFBF', '#303030', '#E0A0A0', '#B09090'),
			'E' => array('#669', '#BFBFFF', '#303030', '#A0A0E0', '#9090B0'),
			'F' => array('#696', '#BFFFBF', '#303030', '#A0E0A0', '#90B090')
);

//validate action parameter
if (isset($version) && in_array($version, $valid_versions))
{
	$display_version = $version;
}
else
{
	$display_version = INFOBOX_UNTIL_LATEST_VERSION;
}

//assign color scheme
switch ($display_version)
{
	case "1.1.7":
	$s = 'B';
	break;

	case "1.1.6.6":
	$s = 'A';
	break;

	case "1.1.6.5":
	$s = 'F';
	break;
		
	case "1.1.6.4":
	$s = 'E';
	break;

	case "1.1.6.3":
	$s = 'D';
	break;

	case "1.1.6.2":
	$s = 'C';
	break;

	case "1.1.6.1":
	$s = 'B';
	break;

	case "1.1.6.0":
	$s = 'A';
	break;

	default:
	$s = 'C';
}


//set alignment
if (isset($align))
{
	switch($align)
	{
		case 'left':
		$float = 'left';
		$margin = 'margin: 10px 10px 10px 0';
		break;

		case 'right':
		$float = 'float:right';
		$margin = 'margin: 10px 0 10px 10px';
		break;
	}
}
else
{
		$float = INFOBOX_DEFAULT_ALIGN;
		$margin = INFOBOX_DEFAULT_MARGIN;
}

//toggle mode
if ($display == "inline")
{
	//display inline tag 
	echo '<div title="This feature was supported until WikkaWiki '.$display_version.' and has been discontinued in more recent versions" style="cursor: help; margin: 2px; display: inline; padding: 2px 3px; font-size: 85%; line-height: 150%; background-color: '.$c[$s][1].'; border: 1px solid '.$c[$s][4].';">'."\n";
	echo '<strong>until '.$display_version.'</strong>'."\n";
	echo '</div>'."\n";
}
else
{	
	//display full infobox
	echo '<div title="This feature was supported until WikkaWiki '.$display_version.' and has been discontinued in more recent versions" style="cursor: help; '.$float.'; width: 200px; border: 1px solid '.$c[$s][0].'; background-color: '.$c[$s][1].'; color: '.$c[$s][2].'; '.$margin.'">'."\n";
	echo '<div style="padding: 0 3px 0 3px; background-color: '.$c[$s][3].'; font-size: 85%; font-weight: bold">NOTE</div>'."\n";
	echo '<div style="padding: 0 3px 2px 3px; font-size: 85%; line-height: 150%; border-top: 1px solid '.$c[$s][4].';">'."\n";
	echo 'This feature was supported until:<br /> <strong>WikkaWiki '.$display_version.'</strong>'."\n";
	echo '</div>'."\n";
	echo '</div>'."\n";
}
?>