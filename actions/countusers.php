<?php
/**
 * Print number of registered users.
 * 
 * @package		Actions
 * @name		Countusers
 * @version		$Id$
 * 
 * @uses	wakka::LoadSingle()
 */ 

$userdata = $this->LoadSingle("SELECT count(*) as num FROM ".$this->config["table_prefix"]."users ");
echo $userdata["num"];
?>