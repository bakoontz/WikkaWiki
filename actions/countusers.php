<?php
/**
 * Print number of registered users.
 * 
 * @package		Actions
 * @version		$Id$
 * 
 * @uses	Wakka::LoadSingle()
 * @filesource
 */ 

$userdata = $this->LoadSingle("SELECT count(*) as num FROM ".$this->config["table_prefix"]."users ");
echo $userdata["num"];
?>