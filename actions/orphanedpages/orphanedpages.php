<?php
/**
 * Display a list of orphaned pages, i.e. pages with no links from other pages.
 *
 * @package	Actions
 * @version	$Id: orphanedpages.php 1132 2008-06-05 10:59:39Z DotMG $
 * @license	http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	Wakka::LoadOrphanedPages()
 * @uses	Wakka::Link()
 * @todo	use new array lister method(s) to generate unordered list or columns
 */

if ($pages = $this->LoadOrphanedPages())
{
	foreach ($pages as $page)
	{
		print $this->Link($page['tag'], '', '', 0)."<br />\n";
	}
}
else
{
	print '<em class="error">'.NO_ORPHANED_PAGES.'</em>';
}
?>