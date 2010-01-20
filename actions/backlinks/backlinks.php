<?php
/**
 * Display the pages linking to the current page.
 *
 * If there is at least one other page in the wiki, which links to
 * the current page, the name(s) are shown as a simple list,
 * ordered alphabetically.
 *
 * @package		Actions
 * @version		$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @output	a list of the pages linking to the page
 * @uses	Wakka::Link()
 * @uses	Wakka::LoadPagesLinkingTo()
 * @uses	Wakka::ListPages()
 */

$pages = $this->LoadPagesLinkingTo($this->GetPageTag());
echo $this->ListPages($pages, '', '', 0, 1);
?>