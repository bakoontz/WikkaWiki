<?php
/**
 * Display an image, optionally linked to an URL.
 * 
 *     @changeset: According to previous released version, we don't need to check width and height of the image.
 *                 so, reference to getimagesize() is removed
 *     @changeset: According to Wikka:EnhancedImageAction, we can configure this action to check if the image
 *                 referenced is really a valid image. See $check_image on the source code of this action.
 *
 * @package		Actions
 * @version		$Id: image.php 1342 2009-03-03 02:29:51Z BrianKoontz $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	Wakka::cleanUrl()
 * @uses	Wakka::htmlspecialchars_ent()
 * @uses	Wakka::Link()
 * @uses	Wakka::StaticHref()
 * @uses  T_()
 *
 * @input string $url mandatory: URL of image to be embedded [$src is also allowed]
 * @input string $alt mandatory: Alternative text. $title or $url will be used as $alt if not provided.
 * @input string $title optional: title text displayed on mouseover (tooltip)
 * @input string $class optional: a CSS className for the image
 * @input string $link optional: target link for image. Supports URL, WikiName links, InterWiki links, etc...
 * @input int $width optional: width used to strech or shrink the image along its width
 * @input int $height optional: height used to strech or shrink the image along its height
 * @input boolean $forceLinkTracking optional: use to create backlinks for images linking to other wiki pages default: FALSE 
 *
 * @output string img element or image link
 */

/**
 * @param string $check_image: hard-coded config value to enable or disable image checking.
 *       By default: no check is performed (default value is 'never'). Checking image could be resource wasting.
 *              If a page hotlinks to images hosted on a remote server, the remote image will be downloaded 
 *              each time our Wiki page will be loaded. The time needed to generate our page will be too big if
 *              many remote images are linked to, or if any of these images takes time to load.
 *              Secondly, it is not efficient to check image each time our page needs to be generated. Ideally,
 *              checking would be performed once, when the page is being saved, and once in a while to ensure 
 *              referenced images are still available.
 *       Values: never, always. (@todo: onsave, integer value speciefying a number of days interval to perform checking)
 */
$check_image = 'never';
$link_title = '';

/* 1) $vars must be an array and either url or src parameter is provided */
if (!is_array($vars) || (empty($vars['url']) && empty($vars['src'])))
{
	return;
}
/* 2) standardize src and url + Url sanitization */
if (empty($vars['url']))
{
	$vars['url'] = $vars['src'];
}
$vars['src'] = $this->StaticHref($this->cleanUrl($vars['url']));
/* 2.5) Check if image is valid */
if ('always' == $check_image)
{
	$attr = @getimagesize($vars['src']);
	if (!is_array($attr))
	{
		echo sprintf(T_("Image file %s could not be loaded! Check if it's a valid image."), $this->htmlspecialchars_ent($vars['src']));
		return;
	}
}
/* 3) if mandatory alt param is not provided, we use the filename */
if (empty($vars['alt'])) 
{
	if (!empty($vars['title']))
	{
		$vars['alt'] = $vars['title'];
	}
	else
	{
		$vars['alt'] = ' ('.T_('image: ').$vars['src'].') ';
	}
}
/* 4) Sanitizing parameters and building output*/
$allowed_params = array('src', 'alt', 'title', 'id', 'class', 'width', 'height');
$output = '<img';
foreach ($allowed_params as $param)
{
	if (empty($vars[$param])) 
	{
		continue;
	}
	$vars[$param] = $this->htmlspecialchars_ent($vars[$param]);
	if ('width' == $param || 'height' == $param)
	{
		$vars[$param] = intval($vars[$param]);
		if ($vars[$param] <= 0) //ignore if negative or null value
		{
			continue;
		}
	}
	// If both title and link are provided, we put the title on the enclosing <a> element
	if (('title' == $param) && !empty($vars['link']))
	{
		$link_title = $vars['title'];
		continue;
	}
	$output .= ' '.$param.'="'.$vars[$param].'"';
}
$output .= ' />';
/* 5) Should the image link somewhere? */
if (!empty($vars['link']))
{
	$output = $this->Link($vars['link'], '', $output, 1, false, $link_title);
}
/* 6) Return the output. We don't need ReturnSafeHTML anymore. */
echo $output;
