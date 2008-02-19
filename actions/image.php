<?php
/*
	"image" action

	Parameters:
		url		- URL of image to be embedded
		link		- target link for image (optional). Supports URL, WikiName links, InterWiki links etc.
		title		- title text displayed when mouse hovers above image
		class		- a class for the image
		alt         - an alt text

	$Id$
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


require_once('safehtml/classes/HTMLSax.php');
require_once('safehtml/classes/safehtml.php');


// Save all "<" symbols
$output = preg_replace("/<(?=[^a-zA-Z\/\!\?\%])/", "&lt;", $output);

// Instantiate the handler
$handler=& new safehtml();

// Instantiate the parser
$parser=& new XML_HTMLSax();

// Register the handler with the parser
$parser->set_object($handler);

// Set the handlers
$parser->set_element_handler('openHandler','closeHandler');
$parser->set_data_handler('dataHandler');
$parser->set_escape_handler('escapeHandler');

$parser->parse($output);

$output = $handler->getXHTML(); 


print($output);
?>
