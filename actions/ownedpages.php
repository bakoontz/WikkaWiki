<?php
/**
 * Number and percentage of the pages the current user owns.
 * 
 * @package Actions
 * @name	ownedpages.php
 * 
 * @author Chris Tessmer
 * @version	$Id$
 * @license	GPL
 * 
 * @uses	Wakka::GetUserName()
 * @uses	Wakka::Query()
 */

$str = 'SELECT COUNT(*) FROM '.$this->config["table_prefix"].'pages WHERE `owner` ';
$str .= "= '" . $this->GetUserName() . "' AND `latest` = 'Y'";
$countQuery = $this->Query( $str );

# get the total # of pages
$str = 'SELECT COUNT(*) FROM '.$this->config["table_prefix"].'pages WHERE `latest` = \'Y\' ';
$totalQuery = $this->Query( $str );    

$count  = mysql_result($countQuery, 0); 
$total  = mysql_result($totalQuery, 0); 

$percent = round( ($count/$total )*100, 2 ) ;

print( "You own <strong>$count</strong> pages out of the <strong>$total</strong> pages on this Wiki."); #i18n
print( "<br />That means you own <strong>$percent%</strong> of the total." ); #i18n
?>