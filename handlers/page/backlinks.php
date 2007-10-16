<?php
/**
* Displays a list of internal pages linking to the current page.
*
* Usage: append /backlinks to the URL of the page
*
* This handler retrieves a list of internal pages linking to the current page.
* It first checks if they exist and prints them on the screen.
*
* @package		Handlers
* @subpackage
* @name			backlinks
*
* @author	{@link http://wakkawiki.de/MartinBurger Martin Burger} - original idea and code.
* @author	{@link http://wikkawiki.org/DarTar Dario Taraborelli} - code rewritten, existsPage check added, removed links array.
* @version	0.4
* @since	Wikka 1.1.6.2
*
* @uses		Wakka::LoadPagesLinkingTo()
* @uses		Wakka::existsPage()
* @uses		Wakka::Link()
*
* @todo		optional (GET) parameter to list links from non-active (deleted, renamed)
*			pages as well
* @todo		build array and use core formatting routine to format as list or columns
*
*/

// User-interface: strings
define('PAGE_TITLE','Pages linking to %s');
define('MESSAGE_NO_BACKLINKS','There are no backlinks to this page.');

echo '<div class="page">'."\n";

// build backlinks list
#echo $this->Format('=== '.sprintf(PAGE_TITLE,'[['.$this->tag.']]').' === --- ---');
echo '<h3>'.sprintf(PAGE_TITLE,$this->tag).'</h3><br />'."\n";
if ($pages = $this->LoadPagesLinkingTo($this->tag)) {
	foreach ($pages as $page) {
		if ($this->existsPage($page['tag'])) {			// name change, interface change (active pages only)
			print $this->Link($page['tag']).'<br />';
		}
	}
} else {
	print MESSAGE_NO_BACKLINKS;
}
?>
</div>