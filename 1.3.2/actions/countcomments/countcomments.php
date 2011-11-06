<?php
/**
 * Print total number of comments in this wiki.
 *
 * @package		Actions
 * @version 	$Id: countcomments.php 820 2007-11-23 09:21:08Z DotMG $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	Wakka::getCount()
 * 
 * @todo	Add parameter to specify date range #955
 */
echo $this->getCount('comments');
?>