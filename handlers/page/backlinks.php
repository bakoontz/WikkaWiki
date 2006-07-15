<?php
/**
* Display a list of internal pages linking to the current page.
*
* Usage: append /backlinks to the URL of the page
* 
* This handler retrieves a list of internal pages linking to the current page.
* It first checks if they exist and prints them on the screen. 
*
* @package Handlers
* @subpackage Page
* @version $Id$
*
* @author {@link http://wakkawiki.de/MartinBurger Martin Burger} - original idea and code.
* @author {@link http://wikkawiki.org/DarTar Dario Taraborelli} - code rewritten, ExistsPage check added, removed links array. 
* @since Wikka 1.1.6.2
* 
* @uses	Wakka::ExistsPage()
* @uses	Wakka::Format()
* @uses	Wakka::LoadPagesLinkingTo
* @todo	move <div> to templating class
* @filesource
*/
echo '<div class="page">'."\n"; //TODO: move to templating class

/**
 * i18n
 */
define('PAGE_TITLE','Pages linking to %s');
define('ERROR_NO_BACKLINKS','There are no backlinks to this page.');

// build backlinks list
echo $this->Format('=== '.sprintf(PAGE_TITLE,'[['.$this->tag.']]').' === --- ---');
if ($pages = $this->LoadPagesLinkingTo($this->tag)) {
	foreach ($pages as $page) {
		if ($this->ExistsPage($page['tag'])) {
			print $this->Link($page['tag']).'<br />';
		}
	}
} else {
	print ERROR_NO_BACKLINKS;
}
echo '</div>'."\n" //TODO: move to templating class
?>