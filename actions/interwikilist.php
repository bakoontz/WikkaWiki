<?php
/**
 * Echos a list as shown in InterWiki.
 * 
 * @package		Actions
 * @name		Interwikilist
 * @version		$Id$
 * 
 */

$file = implode("", file("interwiki.conf", 1));
print($this->Format("%%".$file."%%"));

?>