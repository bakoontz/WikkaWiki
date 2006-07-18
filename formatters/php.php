<?php
/**
 * PHP language file for Wikka highlighting (uses PHP built-in highlighting).
 * 
 * @package	Formatters
 * @version	$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses		Wakka::method()
 */

if ($this->method == "diff") {
	// save output buffer and restart with clean buffer
	$dummy = ob_get_clean(); ob_start();
	// replace diff-tags to prevent highlighting these html-entities!
	$text = str_replace(array("&pound;&pound;", "&yen;&yen;"), array("££", "¥¥"), $text);
}

highlight_string($text);

if ($this->method == "diff") {
	// get highlighting output
	$listing = ob_get_clean(); ob_start();
	// render diff tags
	$listing = preg_replace("/££<\/font>/", "</font>££", $listing);
	$listing = preg_replace("/££(.*?)££/", "<ins>\\1</ins>", $listing);
	$listing = preg_replace("/¥¥<\/font>/", "</font>¥¥", $listing);
	$listing = preg_replace("/¥¥(.*?)¥¥/", "<del>\\1</del>", $listing);
	// write original output and revised highlighting back to fresh buffer
	print $dummy.$listing;
}

?>