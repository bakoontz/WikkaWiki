<?php
/**
 * Display the full text, a link, a button or metadata for a specified license.
 *
 * This action displays the full text or link for a number of licenses. The license type
 * can be specified via a $type action parameter. The display mode is selected via
 * the $display parameter. All links support the rel=license microformat. A custom copyright
 * page link can be specified via a $link parameter if type 'C' (All rights reserved) is used.
 *
 * Usage:
 * {{license [display="fulltext"][type="CC-BY-SA"]}}
 * 
 * @package		Actions
 * @version		$Id$
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 * @since		Wikka 1.1.7
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses		Wakka::Format()
 * @input		string $type optional: selects the license to be displayed;
 *					'C' 	=> 	'All rights reserved' (custom license),
 * 					'PD' 	=> 	'Public Domain',
 *					'GPL' 	=> 	'GNU General Public License',
 *					'GPL2' 	=> 	'GNU General Public License 2.0',
 *					'LGPL' 	=> 	'GNU Lesser General Public License',
 *					'GFDL' 	=> 	'GNU Free Documentation License',
 *					'AGPL' 	=> 	'GNU Affero General Public License',
 *					'CC-BY' =>	'Creative Common Attribution License',
 *					'CC-BY-SA'	=>	'Creative Common Attribution-ShareAlike License',
 *					'CC-BY-ND' 	=>	'Creative Common Attribution-NoDerivs License',
 *					'CC-BY-NC' 	=>	'Creative Common Attribution-NonCommercial  License',
 *					'CC-BY-NC-SA' 	=>	'Creative Common Attribution-NonCommercial-ShareAlike License',
 *					'CC-BY-NC-ND' 	=>	'Creative Common Attribution-NonCommercial-NoDerivs License'
 *					default: 'GPL'
 * 
 * @input		string $display optional: defines output option;
 *					'fulltext' 	=> 	displays the full text of the license
 *					'link' 	=> displays a link to the license with the license full name
 *					'abbr' 	=> 	displays a link to the license with the license acronym
 *					'notice' 	=> 	displays a link to the license with text "Some rights reserved"
 *					'button' 	=> 	displays a button with a link to the license
 *					'badge' 	=> 	displays a 80x15 badge with a link to the license
 * 					default: 'link'
 * 
 * @input		string $link optional: the absolute URL for a custom license (only used with type 'C');
 * 
 * @output		fulltext or link or button for the specified license
 * 
 * @todo		add option to embed license metadata
 * @todo		replace hardcoded paths and URI #718 
 * @todo		CSS selectors
 */

//valid types
$license_valid_types = array('C', 'PD', 'GPL', 'GPL2', 'LGPL', 'GFDL', 'AGPL', 'CC-BY', 'CC-BY-SA', 'CC-BY-ND', 'CC-BY-NC', 'CC-BY-NC-SA', 'CC-BY-NC-ND');

//valid display modes
$valid_display_modes = array('fulltext', 'link', 'abbr', 'notice', 'button', 'badge');

// License full names
$license_fullname = array(
	'C' 	=> 	'All rights reserved',
	'PD' 	=> 	'Public Domain',
	'GPL' 	=> 	'GNU General Public License',
	'GPL2' 	=> 	'GNU General Public License 2.0',
	'LGPL' 	=> 	'GNU Lesser General Public License',
	'GFDL' 	=> 	'GNU Free Documentation License',
	'AGPL' 	=> 	'GNU Affero General Public License',
	'CC-BY' =>	'Creative Common Attribution License',
	'CC-BY-SA'	=>	'Creative Common Attribution-ShareAlike License',
	'CC-BY-ND' 	=>	'Creative Common Attribution-NoDerivs License',
	'CC-BY-NC' 	=>	'Creative Common Attribution-NonCommercial  License',
	'CC-BY-NC-SA' 	=>	'Creative Common Attribution-NonCommercial-ShareAlike License',
	'CC-BY-NC-ND' 	=>	'Creative Common Attribution-NonCommercial-NoDerivs License'
);

// License full names
$license_url = array(
	'PD' 	=> 	'http://creativecommons.org/licenses/publicdomain/',
	'GPL' 	=> 	'http://www.gnu.org/copyleft/gpl.html',
	'GPL2' 	=> 	'http://www.gnu.org/licenses/old-licenses/gpl-2.0.html',
	'LGPL' 	=> 	'http://www.gnu.org/licenses/lgpl.html',
	'GFDL' 	=> 	'http://www.gnu.org/licenses/fdl.html',
	'AGPL' 	=> 	'http://www.gnu.org/licenses/agpl.html',
	'CC-BY' =>	'http://creativecommons.org/licenses/by/3.0/',
	'CC-BY-SA'	=>	'http://creativecommons.org/licenses/by-sa/3.0/',
	'CC-BY-ND' 	=>	'http://creativecommons.org/licenses/by-nd/3.0/',
	'CC-BY-NC' 	=>	'http://creativecommons.org/licenses/by-nc/3.0/',
	'CC-BY-NC-SA' 	=>	'http://creativecommons.org/licenses/by-nc-sa/3.0/',
	'CC-BY-NC-ND' 	=>	'http://creativecommons.org/licenses/by-nc-nd/3.0/'
);

// icons


// process action parameters

// is license type valid?
if (isset($vars['type']) && in_array($vars['type'], $license_valid_types))
{
	$type = $vars['type'];
}
else
{
	$type = LICENSE_DEFAULT_TYPE;
}

// is display mode valid?
if (isset($vars['display'])  && in_array($vars['display'], $valid_display_modes))
{
	$display = $vars['display'];
}
else
{
	$display = LICENSE_DEFAULT_DISPLAY;
}

// is a custom license link specified?
if (isset($vars['link']) && $type = 'C')
{
	$link = $vars['link'];
}
else
{
	$link = $license_url[$type];
}


//toggle display mode
$output = "";
switch($display)
{
	//display the full text of the license if it's not a custom one
	case 'fulltext':
		if ($type !== 'C')
		{
			$output .= '<div style="padding: 1em; font-family: Georgia, Times, serif; border: 1px solid #CCA; background-color: #FFD">'."\n";
			$fullpath = $this->BuildFullpathFromMultipath('license/inc/buttons/'.$type.'.png', $this->GetConfigValue('action_path'), '/');
			if (file_exists($fullpath))
			{
				$output .= '<p>'."\n";
				$output .= '<a rel="license" title="'.$license_fullname[$type].'" href="'.$link.'"><img src="'.$fullpath.'" alt="'.$license_fullname[$type].'" /></a>'."\n";
				$output .= '</p>'."\n";
			}
			$fullpath = $this->BuildFullpathFromMultipath('license/inc/fulltext/', $this->GetConfigValue('action_path'));
			$output .= $this->IncludeBuffered($type.'.htm', 'Error', '', $fullpath);
			$output .= '</div>'."\n";
		}
		break;

	//display a link to the license with a short notice text
	case 'notice':
		if ($type == 'PD')
		{
			$output .= '<a rel="license" title="'.$license_fullname[$type].'" href="'.$link.'">Released in the public domain</a>'."\n";
		}
		else if ($type == 'C')
		{
			$output .= '<a rel="license" title="'.$license_fullname[$type].'" href="'.$link.'">All rights reserved</a>'."\n";
		}
		else
		{
			$output .= '<a rel="license" title="'.$license_fullname[$type].'" href="'.$link.'">Some rights reserved</a>'."\n";
		}
		break;
		
	//display a button with a link to the license
	case 'button':
		$fullpath = $this->BuildFullpathFromMultipath('license/inc/buttons/'.$type.'.png', $this->GetConfigValue('action_path'), '/');
		if (file_exists($fullpath)) //TODO #718
		{
			$output .= '<a rel="license" title="'.$license_fullname[$type].'" href="'.$link.'"><img src="'.$this->StaticHref($fullpath).'" alt="'.$license_fullname[$type].'" /></a>'	."\n";
		}
		break;

	//display a 80x15 badge with a link to the license
	case 'badge':
		$fullpath = $this->BuildFullpathFromMultipath('license/inc/badges/'.$type.'.png', $this->GetConfigValue('action_path'), '/');
		if (file_exists($fullpath)) //TODO #718
		{
			$output .= '<a rel="license" title="'.$license_fullname[$type].'" href="'.$link.'"><img src="'.$this->StaticHref($fullpath).'" alt="'.$license_fullname[$type].'" /></a>'	."\n";
		}
		break;
	
	//display an acronym with a link to the license
	case 'abbr':
		$output = '<a rel="license" title="'.$license_fullname[$type].'" href="'.$link.'">'.$type.'</a>'."\n";
		break;	
	
	//display the full name and a link to the license
	default:
	case 'link':
		$output = '<a rel="license" title="'.$license_fullname[$type].'" href="'.$link.'">'.$license_fullname[$type].'</a>'."\n";
		break;	
}

echo $output;
?>
