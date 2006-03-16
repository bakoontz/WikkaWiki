<?php
// constant section
define(NO_WANTED_PAGES, 'No wanted pages. Good!');
define(NO_PAGES_LINKING_TO, "No page is linking to %s."); // %s - pagename
define(PAGES_LINKING_TO, "Pages linking to %s:"); // %s - pagename

if ($linking_to = $_REQUEST["linking_to"])
{
	if ($pages = $this->LoadPagesLinkingTo($linking_to))
	{
		print(sprintf(PAGES_LINKING_TO, $this->Link($linking_to))."<br />\n");
		foreach ($pages as $page)
		{
			print($this->Link($page["tag"])."<br />\n");
		}
	}
	else
	{
		print('<em>'.sprintf(NO_PAGES_LINKING_TO, $this->Link($linking_to)).'</em>');
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
		print('<em>'.NO_WANTED_PAGES.'</em>');
	}
}

?>