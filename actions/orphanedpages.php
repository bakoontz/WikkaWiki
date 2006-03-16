<?php

define('NO_ORPHANED_PAGES', 'No orphaned pages. Good!');

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