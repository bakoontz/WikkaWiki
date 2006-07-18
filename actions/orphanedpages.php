<?php
/**
 * Display a list of orphaned pages, i.e. pages with no links from other pages.
 * 
 * @package Actions
 * @version	$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses	Wakka::LoadOrphanedPages()
 * @uses	Wakka::Link()
 */
/**
 * i18n
 */
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