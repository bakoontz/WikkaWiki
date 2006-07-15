<?php
/**
 * Print total number of comments in this wiki.
 * 
 * @package		Actions
 * @version 	$Id$
 * 
 * @uses	Wakka::LoadSingle()
 * @filesource
 */

$commentsdata = $this->LoadSingle("SELECT count(*) as num FROM ".$this->config["table_prefix"]."comments");
echo $commentsdata["num"];
?>