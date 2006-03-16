<?php
/**
 * Print number of pages owned by the current user.
 */

//constant section
define('DISPLAY_PAGES_YOU_OWN','Display a list of the pages you currently own');

$str = 'SELECT COUNT(*) FROM '.$this->config["table_prefix"].'pages WHERE `owner` ';
$str .= "= '" . $this->GetUserName() . "' AND `latest` = 'Y'";
$countquery = $this->Query($str);
$count  = mysql_result($countquery, 0);
echo $this->Link('MyPages', '', $count,'','', DISPLAY_PAGES_YOU_OWN);

?>