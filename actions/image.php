<?php
/*
	"image" action

	Parameters:
		url		- URL of image to be embedded
		link		- target link for image (optional). Supports URL, WikiName links, InterWiki links etc.
		title		- title text displayed when mouse hovers above image
		class		- a class for the image
		alt         - an alt text

	$Id: image.php,v 1.1.1.1 2004/09/28 01:32:34 jsnx Exp $
*/

$title="WikiImage";
$class="";
$alt="image";

if (is_array($vars))
{
    foreach ($vars as $param => $value)
    {
    	if ($param == 'src' and $vars['url'] == '') {$vars['url']=$value;}
    	if ($param == 'title') {$title=htmlspecialchars($vars['title']);}
    	if ($param == 'class') {$class=htmlspecialchars($vars['class']);}
    	if ($param == 'alt') {$alt=htmlspecialchars($vars['alt']);}
	}
}


$output = "<img class=\"".$class."\" src=\"".$vars['url']."\" alt=\"".$alt."\" title=\"".$title."\" />";


// link?
if ($link = $vars['link'])
{
	$output = $this->Link($link, "", $output, 1, 0, 0);
}

$output = $this->ReturnSafeHTML($output);
print($output);

?>
