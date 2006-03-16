<?php
/**
 * Print the total number of pages in this wiki.
 */

//constant section
define ('DISPLAY_ALPHABETICAL_PAGE_INDEX', 'Display an alphabetical page index');

$pagedata = $this->LoadSingle("SELECT count(*) as num FROM ".$this->config["table_prefix"]."pages WHERE latest = 'Y'");
echo $this->Link('PageIndex', '', $pagedata['num'],'','', DISPLAY_ALPHABETICAL_PAGE_INDEX);

?>