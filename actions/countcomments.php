<?php
/**
 * Print total number of comments in this wiki.
 * 
 * @package		Actions
 * @name		Countcomments
 * @version 	$Id$
 * 
 * @uses	wakka::LoadSingle()
 */

$commentsdata = $this->LoadSingle("SELECT count(*) as num FROM ".$this->config["table_prefix"]."comments");
echo $commentsdata["num"];
?>