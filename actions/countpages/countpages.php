<?php
/**
 * Print the total number of pages in this wiki.
 *
 * @package		Actions
 * @version		$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	Wakka::getCount()
 * @uses	Wakka::Link()
 */

$where = "`latest` = 'Y'";
$count = $this->getCount('pages', $where);
echo $this->Link('PageIndex', '', $count, '', '', INDEX_LINK_TITLE);

?>
