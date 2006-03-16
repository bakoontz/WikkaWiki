<?php
/**
 * ownedpages.php  {{OwnedPages}}
 * author: Chris Tessmer
 * date: 19 Dec 2002
 * license: [[GPL]]
 */

// constant section
define('PAGES_YOU_OWN', 'You own %1\s pages out of the %2\s pages on this Wiki'); // %1\s - html-formatted number of own pages, %2\s - html-formatted number of total pages
define('PAGES_TOTAL_PERC','That means you own %s of the total.'); // %s - percent of pages owned

$str = 'SELECT COUNT(*) FROM '.$this->config["table_prefix"].'pages WHERE `owner` ';
$str .= "= '" . $this->GetUserName() . "' AND `latest` = 'Y'";
$countQuery = $this->Query( $str );

// get the total # of pages
$str = 'SELECT COUNT(*) FROM '.$this->config["table_prefix"].'pages WHERE `latest` = \'Y\' ';
$totalQuery = $this->Query( $str );

$count  = mysql_result($countQuery, 0);
$total  = mysql_result($totalQuery, 0);

$percent = round( ($count/$total )*100, 2 ) ;

printf(PAGES_YOU_OWN, "<strong>$count</strong>", "<strong>$total</strong>");
print('<br />'.sprintf(PAGES_TOTAL_PERC,"<strong>$percent%</strong>"));
?>