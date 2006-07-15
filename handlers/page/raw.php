<?php
/**
 * Display the raw version of a wiki page, i.e. the source with no formatting.
 * 
 * @package		Handlers
 * @subpackage	Page
 * @version		$Id$
 * 
 * @uses		Wakka::HasAccess()
 * @filesource
 */

if ($this->HasAccess("read") && $this->page)
{
	// display raw page
	print($this->page["body"]);
}
?>