<?php
/**
 * Display an image, optionally linked to an URL.
 *
 * @package		Actions
 * @version		$Id: image.php 1342 2009-03-03 02:29:51Z BrianKoontz $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	Wakka::Link()
 * @uses	Wakka::ReturnSafeHTML()
 * @uses	Wakka::htmlspecialchars_ent()
 * @uses	Wakka::cleanUrl()
 * @uses	Wakka::StaticHref()
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
$title = 'WikiImage';	// @@@ don't use title attribute if no title specified
$class = $link = '';
$alt = 'image';			// @@@ don't generate a meaningless alt text: invalid!
$url = '';
$width = 0;
$height = 0;
$attr = '';

// params
if (is_array($vars))
{
	foreach ($vars as $param => $value)
	{
		$value = $this->htmlspecialchars_ent($value);
		if ($param == 'src' && $vars['url'] == '') {$vars['url']=$value;}
		if ($param == 'title') {$title = $this->htmlspecialchars_ent($vars['title']);}
		if ($param == 'class') {$class = $this->htmlspecialchars_ent($vars['class']);}
		if ($param == 'alt') {$alt = $this->htmlspecialchars_ent($vars['alt']);}
		if ($param == 'link') {$link = $this->htmlspecialchars_ent($vars['link']);}
		if ($param == 'width' && (int)$vars['width'] > 0) {$width = (int)$vars['width'];}
		if ($param == 'height' && (int)$vars['height'] > 0) {$height = (int)$vars['height'];}
	}
}
if (isset($vars['url'])) $url = $this->StaticHref($this->cleanUrl(trim($this->htmlspecialchars_ent($vars['url']))));

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