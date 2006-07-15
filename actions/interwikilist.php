<?php
/**
 * Echos the list of InterWiki shortcuts.
 * 
 * @package		Actions
 * @version		$Id$
 * 
 * @uses		Wakka::Format
 * @filesource
 */

$file = implode("", file("interwiki.conf", 1));
print($this->Format("%%".$file."%%"));

?>