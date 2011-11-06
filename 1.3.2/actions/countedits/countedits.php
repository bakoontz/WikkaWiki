<?php
/**
 * Print total number of edits in this wiki.
 *
 * @package		Actions
 * @version 	$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	Wakka::getCount()
 * 
 * @todo	Add parameter to specify date range #955
 */
echo $this->getCount('pages');
?>