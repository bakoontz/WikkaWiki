<?php
/**
 * Embed a Flash object in a wiki page.
 *
 * Example: {{flash url="http://example.com/example.swf" [width="x"] [height="x"]}}
 *
 * @package 	Actions
 * @name		Flash
 * @version		$Id: flash.php 1196 2008-07-16 04:25:09Z BrianKoontz $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses		Wakka::cleanUrl()
 * @uses		Wakka::StaticHref()
 *
 * @input	string	$url	mandatory: URL of the Flash object (or a relative path)
 * @input	int		$width	optional: custom width (pixels)
 * @input	int		$height	optional: custom height (pixels)
 * @output	string	object element wich embeds the Flash object
 * @todo	use constants instead of "magic numbers"
 */

define('FLASH_DEFAULT_WIDTH',550);
define('FLASH_DEFAULT_HEIGHT',400);
define('FLASH_MAX_WIDTH',950);
define('FLASH_MAX_HEIGHT',950);

// setting defaults
$width = FLASH_DEFAULT_WIDTH;
$height = FLASH_DEFAULT_HEIGHT;
$url = '';

// getting params
if (is_array($vars))
{
    foreach ($vars as $param => $value)
    {
    	if ($param == 'width') 
    	{
    		$width = (int)$vars['width'];
    		if ($width>FLASH_MAX_WIDTH) $width = FLASH_MAX_WIDTH;
    	}
    	if ($param == 'height') 
    	{
    		$height = (int)$vars['height'];
    		if ($height>FLASH_MAX_HEIGHT) $height = FLASH_MAX_HEIGHT;
    	}
    	if ($param == 'url')
    	{
    		$url = $this->cleanUrl(trim($vars['url']));
    	}
    	
    }
}

if (!$url) $url = $vars[0];
$url = $this->StaticHref($this->cleanUrl(trim($this->htmlspecialchars_ent($url))));

// ouput, if any
if ($url)
  echo '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="'.$width.'" height="'.$height.'">
	<param name="movie" value="'.$url.'" />
	<param name="quality" value="high" />
	<embed src="'.$url.'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="'.$width.'" height="'.$height.'"></embed>
</object>';
?>