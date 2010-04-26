<?php
/**
 * Display a list of nonexisting pages to which other pages are linking to.
 *
 * <p>This action lists all pagenames that don't exist but are referred to by other pages on the wiki. By default, the
 * WikkaInstaller creates a page named WantedPages that uses this action.</p>
 * <p>Those non-existing pages are listed as one line per wanted pages. Each line is composed of 2 parts : The name of
 * the wanted page in a form of a link: Clicking on this link will let you create the page and start editing its content.
 * Then in brackets, you see the number of pages linking to the wanted page. This number is also in a form of a link:
 * clicking on it will let you see all the pages linking to the wanted page, using the {@link backlinks.php backlinks}
 * handler.</p>
 *
 * @package	Actions
 * @version	$Id:wantedpages.php 369 2007-03-01 14:38:59Z DarTar $
 * @license	http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	Wakka::Link()
 * @uses	Wakka::LoadWantedPages()
 *
 * @todo	use new array2list methods to build output
 */

if (isset($_GET["linking_to"]))
{
	$linking_to = $this->GetSafeVar('linking_to');
	if ($pages = $this->LoadPagesLinkingTo($linking_to))
	{
		print(sprintf(WANTEDPAGES_PAGES_LINKING_TO,$this->Link($linking_to)).":<br />\n");
		foreach ($pages as $page)
		{
			print($this->Link($page["page_tag"])."<br />\n");
		}
	}
	else
	{
		print('<p class="error">No page is linking to '.$this->Link($linking_to).".</p>");
	}
}
else
{
	if ($pages = $this->LoadWantedPages())
	{
		foreach ($pages as $page)
		{
			print($this->Link($page["tag"])." (<a href=\"".$this->href("", "", "linking_to=".$page["tag"])."\">".$page["count"]."</a>)<br />\n");
		}
	}
	else
	{
		print('<p class="error">No wanted pages. Good!</p>');
	}
}
?>
