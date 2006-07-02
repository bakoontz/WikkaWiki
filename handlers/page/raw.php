<?php
/**
 * Display the raw version of a wiki page, i.e. without wiki formatting.
 * 
 * @package		Handlers
 * @subpackage	Page
 * @name		raw.php
 * @version		$Id$
 * 
 * @uses		HasAccess()
 */

if ($this->HasAccess("read") && $this->page)
{
	// display raw page
	print($this->page["body"]);
}
?>