<?php
/**
 * Print number of registered users.
 * 
 * @package		Actions
 * @name		countusers.php
 * @version		$Id$
 * 
 * @uses	Wakka::LoadSingle()
 */ 

$userdata = $this->LoadSingle("SELECT count(*) as num FROM ".$this->config["table_prefix"]."users ");
echo $userdata["num"];
?>