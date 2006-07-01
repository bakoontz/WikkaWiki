<?php
/**
 * Print total number of comments in this wiki.
 * 
 * @package		Actions
 * @name		countcomments.php
 * @version 	$Id$
 * 
 * @uses	Wakka::LoadSingle()
 */

$commentsdata = $this->LoadSingle("SELECT count(*) as num FROM ".$this->config["table_prefix"]."comments");
echo $commentsdata["num"];
?>