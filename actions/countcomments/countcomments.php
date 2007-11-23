<?php
/**
 * Print total number of comments in this wiki.
 *
 * @package		Actions
 * @version 	$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	Wakka::getCount()
 */

echo $this->getCount('comments');
?>
