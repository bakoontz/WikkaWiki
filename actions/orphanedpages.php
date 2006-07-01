<?php
/**
 * Displays all pages with no links from any other page.
 * 
 * @package Actions
 * @name	orphanedpages.php
 * 
 * @version	$Id$
 * 
 * @uses	Wakka::LoadOrphanedPages()
 * @uses	Wakka::Link()
 */

//i18n 
if(!defined('NO_ORPHANED_PAGES')) define('NO_ORPHANED_PAGES', 'No orphaned pages. Good!');

if ($pages = $this->LoadOrphanedPages())
{
	foreach ($pages as $page)
	{
		print($this->Link($page["tag"], "", "", 0)."<br />\n");
	}
}
else
{
	print('<em>'.NO_ORPHANED_PAGES.'</em>');
}

?>