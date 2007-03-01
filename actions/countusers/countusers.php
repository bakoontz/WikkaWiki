<?php
/**
 * Print number of registered users.
 * 
 * @package		Actions
 * @version		$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses	Wakka::LoadSingle()
 */ 

$userdata = $this->LoadSingle("SELECT count(*) as num FROM ".$this->config["table_prefix"]."users ");
echo $userdata["num"];
?>