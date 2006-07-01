<?php
/**
 * Print name of this WikkaWiki.
 * 
 * @package	Actions
 * @name	wikkaname.php
 * @version $Id$
 * 
 * @uses	Wakka::GetConfigValue()
 */
echo $this->GetConfigValue("wakka_name");
?>