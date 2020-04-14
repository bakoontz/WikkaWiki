<?php
/**
 * Display the pages linking to the current page.
 *
 * If there is at least one other page in the wiki, which links to
 * the current page, the name(s) are shown as a simple list,
 * ordered alphabetically.
 *
 * @package		Actions
 * @version		$Id: backlinks.php 820 2007-11-23 09:21:08Z DotMG $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @output	a list of the pages linking to the page
 * @uses	Wakka::Link()
 * @uses	Wakka::LoadPagesLinkingTo()
 */

if ($pages = $this->LoadPagesLinkingTo($this->getPageTag())) {
	foreach ($pages as $page) {
		$links[] = $this->Link($page["page_tag"]);
	}
	print(implode("<br />\n", $links));
}

?>
