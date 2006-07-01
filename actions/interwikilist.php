<?php
/**
 * Echos the lidt of InterWiki urls.
 * 
 * @package		Actions
 * @name		interwikilist.php
 * @version		$Id$
 * 
 * @uses		Wakka::Format
 */

$file = implode("", file("interwiki.conf", 1));
print($this->Format("%%".$file."%%"));

?>