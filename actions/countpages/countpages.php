<?php
/**
 * Print the total number of pages in this wiki.
 *
 * @package		Actions
 * @version		$Id: countpages.php 820 2007-11-23 09:21:08Z DotMG $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	Wakka::getCount()
 * @uses	Wakka::Link()
 * 
 * @todo	Add parameter to specify date range #955
 */
$where = "`latest` = 'Y'";
$count = $this->getCount('pages', $where);
echo $this->Link('PageIndex', '', $count, '', '', INDEX_LINK_TITLE);
?>