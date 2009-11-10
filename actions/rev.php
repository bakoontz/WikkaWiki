<?php
/**
 * Display revision number.
 *
 * This action simply displays the latest revision number for the current page.
 *
 * @package Actions
 * @version		$Id$
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 * @since Wikka 1.1.6.6
 *
 * @todo	add parameter to display latest global revision number (for the whole wiki)
 * @todo	add parameter to specify revision to be displayed along with a link to the source of the page at that revision 
 */
echo '<tt>['.$this->page['id'].']</tt>';
?>