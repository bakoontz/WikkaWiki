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
 * @uses	Wakka::getCount()
 */

$whereOwner = "`owner` = '" . $this->GetUserName() . "' AND `latest` = 'Y'";
$whereTotal   = "`latest` = 'Y'";
$count = $this->getCount('pages', $whereOwner);
$total = $this->getCount('pages',$whereTotal);

$percent = round( ($count/$total )*100, 2 ) ;

$disp_count = '<strong>'.$count.'</strong>';
$disp_total = '<strong>'.$total.'</strong>';
$disp_percent = '<strong>'.$percent.'%</strong>';
echo sprintf(OWNEDPAGES_COUNTS,$disp_count,$disp_total);
echo '<br />';
echo sprintf(OWNEDPAGES_PERCENTAGE,$disp_percent);
?>
