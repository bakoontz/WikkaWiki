<?php
/**
* Creates a <meta> tag in the header of the page output. 
*
* Specify a name for the meta tag, and the content:
*
*     {{meta name="somename" content="blah blah blah"}}
*
* @package		Actions
* @name		    meta	
* @version		$Id$
* @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
* @filesource
*
* @author	{@link http://wikkawiki.org/BrianKoontz Brian Koontz}
*
* @version    1.3
* @uses		Wakka::htmlspecialchars_ent()
* @uses     Wakka::AddCustomHeader()
* @input	string	$name	    mandatory: value of the name attribute of the
* meta tag 
* @input	string	$content    mandatory: value of the content attribute
* of the meta tag 
* @output	HTML meta tag 
*
* @documentation  {@link http://docs.wikkawiki.org/MetaActionInfo}
*
*/

    $name = htmlspecialchars_ent($vars['name']);
    $content = htmlspecialchars_ent($vars['content']);
    $this->AddCustomHeader("<meta name=\"".$name."\" content=\"".$content."\" />");
?>
