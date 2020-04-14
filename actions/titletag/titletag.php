<?php
/**
* Creates a <title> tag in the header of the page output. 
*
* Note: You will need to remove any existing <title>...</title> tags in
* your header.php files (located under the templates/ directory).
* Otherwise, you will have multiple <title> tags in your HTML output, which
* is not in compliance with the HTML recommendations.
*
* Specify a string for the title tag:
*
*     {{titletag string="My page title with important keywords"}}
*
* @package		Actions
* @name		    titletag
* @version		$Id$
* @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
* @filesource
*
* @author	{@link http://wikkawiki.org/BrianKoontz Brian Koontz}
*
* @version    1.3.7
* @uses		Wakka::htmlspecialchars_ent()
* @uses     Wakka::AddCustomHeader()
* @input	string	$string	    mandatory: value of the string enclosed by
* the title tag 
* @output	HTML title tag 
*
* @documentation  {@link http://docs.wikkawiki.org/TitletagActionInfo}
*
*/

    $string = htmlspecialchars_ent($vars['string']);
    $this->AddCustomHeader("<title>".$string."</title>");
?>
