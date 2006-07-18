<?php
/**
 * Display a list of nonexisting pages to which other pages are linking to.
 * 
 * @package	Actions
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses	 Wakka::LoadPagesLinkingTo()
 * @uses	 Wakka::Link()
 * @uses	 Wakka::LoadWantedPages()
 * @uses	 Wakka::Href()
 */
$linking_to = '';
if (isset($_REQUEST["linking_to"]))
{
	$linking_to = $_REQUEST["linking_to"];
	if ($pages = $this->LoadPagesLinkingTo($linking_to))
	{
		print("Pages linking to ".$this->Link($linking_to).":<br />\n");
		foreach ($pages as $page)
		{
			print($this->Link($page["tag"])."<br />\n");
		}
	}
	else
	{
		print("<em>No page is linking to ".$this->Link($linking_to).".</em>"); # i18n
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
		print("<em>No wanted pages. Good!</em>"); # i18n
	}
}

?>