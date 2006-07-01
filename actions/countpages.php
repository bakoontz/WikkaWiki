<?php
/** 
 * Print the total number of pages in this wiki.
 * 
 * @package		Actions
 * @name		countpages.php
 * @version		$Id$
 * 
 * @uses	Wakka::LoadSingle()
 * @uses	Wakka::Link()
 */

$pagedata = $this->LoadSingle("SELECT count(*) as num FROM ".$this->config["table_prefix"]."pages WHERE latest = 'Y'");
echo $this->Link('PageIndex', '', $pagedata['num'],'','','Display an alphabetical page index'); #i18n

?>