<?php
/**
 * Display an image, optionally linked to an URL.
 *
 * @package		Actions
 * @version		$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	Wakka::Link()
 * @uses	Wakka::ReturnSafeHTML()
 * @uses	Wakka::htmlspecialchars_ent()
 * @uses	Wakka::cleanUrl()
 *
 * @input	string	$url	mandatory: URL of image to be embedded
 * @input	string	$link	optional: target link for image (optional). Supports URL, WikiName links, InterWiki links etc.
 * @input	string	$title	optional: title text displayed when mouse hovers above image
 * @input	string	$class	optional: a class for the image
 * @input	string	$alt	optional: an alt text
 * @input	int		$height optinal: height of the image
 * @input	int		$width optinal: width of the image
 * @output	string	img element or image link
 * @todo	make alt required (wasn't there a better version already?)
 */

// defaults
$title = 'WikiImage';
$class = $link = '';
$alt = 'image';
$url = '';
$width = 0;
$height = 0;
$attr = ''; #

// params
if (is_array($vars))
{
    foreach ($vars as $param => $value)
    {
    	if ($param == 'src' && $vars['url'] == '') {$vars['url']=$value;}
    	if ($param == 'title') {$title = $this->htmlspecialchars_ent($vars['title']);}
    	if ($param == 'class') {$class = $this->htmlspecialchars_ent($vars['class']);}
    	if ($param == 'alt') {$alt = $this->htmlspecialchars_ent($vars['alt']);}
    	if ($param == 'link') {$link = $this->htmlspecialchars_ent($vars['link']);}
    	if ($param == 'width' && (int)$vars['width'] > 0) {$width = (int)$vars['width'];}
    	if ($param == 'height' && (int)$vars['height'] > 0) {$height = (int)$vars['height'];}
	}
}
if(isset($vars['url'])) $url = $this->cleanUrl(trim($vars['url']));

// try to determine image size if given none
if (0 == $width && 0 == $height) #
{
	if (file_exists($url)) $attr = getimagesize($url);
	if (is_array($attr))
	{
		if (0 < $attr[1]) $height = $attr[1];
		if (0 < $attr[0]) $width = $attr[0];
	}
}

// building output
$output = '<img';
if ('' != $class) $output .= ' class="'.$class.'"';
if (0 < $width) $output .= ' width="'.$width.'"';
if (0 < $height) $output .= ' height="'.$height.'"';
$output .= ' src="'.$url.'" alt="'.$alt.'" title="'.$title.'" />';

// link?
if ($link !== '')
{
	$output = $this->Link($link, "", $output, 1, 0, 0);
}

$output = $this->ReturnSafeHTML($output);
echo $output;

?>