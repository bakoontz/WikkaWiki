<?php
/**
 * Print number of pages owned by the current user.
 * 
 * @package		Actions
 * @name		countowned.php
 * @version		$Id$
 * 
 * @uses	Wakka::GetUserName()
 * @uses	Wakka::Query()
 * @uses	Wakka::Link()
 */
 
if(!defined('DISPLAY_MYPAGES_LINK_TITLE')) define('DISPLAY_MYPAGES_LINK_TITLE', 'Display a list of the pages you currently own');

$str = 'SELECT COUNT(*) FROM '.$this->config["table_prefix"].'pages WHERE `owner` ';
$str .= "= '" . $this->GetUserName() . "' AND `latest` = 'Y'";
$countquery = $this->Query($str);
$count  = mysql_result($countquery, 0);
echo $this->Link('MyPages', '', $count,'','', DISPLAY_MYPAGES_LINK_TITLE);

?>