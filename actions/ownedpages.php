<?php
/**
 * Display the number and percentage of pages owned by the current user.
 * 
 * @package Actions
 * @version	$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @author Chris Tessmer
 * 
 * @uses	Wakka::GetUserName()
 * @uses	Wakka::Query()
 */

$str  = 'SELECT COUNT(*) FROM '.$this->config["table_prefix"].'pages WHERE `owner` ';
$str .= "= '" . $this->GetUserName() . "' AND `latest` = 'Y'";
$countQuery = $this->Query( $str );

// get the total # of pages
$str = 'SELECT COUNT(*) FROM '.$this->config["table_prefix"].'pages WHERE `latest` = \'Y\' ';
$totalQuery = $this->Query( $str );    

$count   = mysql_result($countQuery, 0); 
$total   = mysql_result($totalQuery, 0); 
$percent = round( ($count/$total )*100, 2 ) ;

$disp_count = '<strong>'.$count.'</strong>';
$disp_total = '<strong>'.$total.'</strong>';
$disp_percent = '<strong>'.$percent.'%</strong>';
echo sprintf(OWNEDPAGES_COUNTS,$disp_count,$disp_total);
echo '<br />';
echo sprintf(OWNEDPAGES_PERCENTAGE,$disp_percent);
?>