<?php
/**
 * Print total number of comments in this wiki.
 * 
 * @package		Actions
 * @version 	$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses	Wakka::LoadSingle()
 */

$commentsdata = $this->LoadSingle("SELECT count(*) as num FROM ".$this->config["table_prefix"]."comments");
echo $commentsdata["num"];
?>