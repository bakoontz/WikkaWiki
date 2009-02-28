<?php
/**
 * Include flash files into a page.
 * 
 * Syntax: {{flash url="http://example.com/example.swf" [width="x"] [height="x"]}}
 * 
 * Width and Height are optional arguments.
 * 
 * @uses	Wakka::cleanUrl()
 */

// setting defaults
$width = 550;
$height = 400;
$url = '';

// getting params
if (is_array($vars))
{
    foreach ($vars as $param => $value)
    {
    	if ($param == 'width') 
    	{
    		$width = (int)$vars['width'];
    		if ($width>950) $width = 950;
    	}
    	if ($param == 'height') 
    	{
    		$height = (int)$vars['height'];
    		if ($height>950) $height = 950;
    	}
    	if ($param == 'url')
    	{
    		$url = $this->cleanUrl(trim($vars['url']));
    	}
    	
    }
}

// compatibilty for {{flash http://example.com/example.swf}}
if ('' == $url && isset($wikka_vars)) $url = $this->cleanUrl(trim($wikka_vars));

// ouput, if any
if ('' != $url)
  echo '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="'.$width.'" height="'.$height.'">
	<param name="movie" value="'.$url.'" />
	<param name="quality" value="high" />
	<embed src="'.$url.'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="'.$width.'" height="'.$height.'"></embed>
</object>';
?>