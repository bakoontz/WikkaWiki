<?php
/**
 * Display a list of internal pages linking to the current page.
 *
 * Usage: append /backlinks to the URL of the page
 * 
 * This handler retrieves and show a list of internal pages linking to the current page.
 * It uses {@link Wakka::ListPages()} to list them, parameters passed to this method make
 * it to display one entry per line, each entry followed by an edit link to allow direct
 * editing of the referring page.
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
 * @todo	Don't use Format() just to create a simple heading; generate HTML!
 */

// define variables
$page = $this->tag;
$pages = $this->LoadPagesLinkingTo($page);
// produce output
echo '<div class="page">'."\n";
echo $this->Format('=== '.sprintf(BACKLINKS_HEADING,'[['.$page.']]').' === --- ---');
echo $this->ListPages($pages, sprintf('<em class="error">'.BACKLINKS_NO_PAGES.'</em>', $page), '', 0, 1, true);
echo '</div>'."\n"
?>
