<?php
/**
 * Display a list of internal pages linking to the current page.
 *
 * Usage: append /backlinks to the URL of the page
 * 
 * This handler retrieves a list of internal pages linking to the current page.
 * There is no need to check existence of page here because when a page is deleted,
 * links table should be cleaned up accordingly.
 *
 * @package Handlers
 * @subpackage Page
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author {@link http://wakkawiki.de/MartinBurger Martin Burger} - original idea and code.
 * @author {@link http://wikkawiki.org/DarTar Dario Taraborelli} - code rewritten, ExistsPage check added, removed links array. 
 * @author {$link http://wikkawiki.org/DotMG DotMG} - ExistsPage check removed, added call to ListPages.
 * @since Wikka 1.1.6.2
 * 
 * @uses	Wakka::Format()
 * @uses	Wakka::LoadPagesLinkingTo()
 * @uses	Wakka::ListPages()
 * 
 * @todo	move <div> to templating class
 */
echo '<div class="page">'."\n"; //TODO: move to templating class

/**
 * i18n
 */
define('PAGE_TITLE','Pages linking to %s');
define('ERROR_NO_BACKLINKS','There are no backlinks to this page.');

$page = $this->tag;
echo $this->Format('=== '.sprintf(PAGE_TITLE,'[['.$page.']]').' === --- ---');
$pages = $this->LoadPagesLinkingTo($page);
$str = $this->ListPages($pages, sprintf('<em class="error">'.ERROR_NO_BACKLINKS.'</em>', $page), '', 0, 1);
echo $str.'</div>'."\n" //TODO: move to templating class
?>
