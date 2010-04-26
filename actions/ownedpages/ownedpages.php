<?php
/**
 * Display the number and percentage of pages owned by the current user.
 *
 * @package	Actions
 * @version	$Id: ownedpages.php 736 2007-10-03 10:56:11Z JavaWoman $
 * @license	http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author	{@link http://web.archive.org/web/20040820215257/http://www.wakkawiki.com/ChrisTessmer Chris Tessmer}
 *
 * @uses	Wakka::GetUserName()
 * @uses	Wakka::existsUser()
 * @uses	Wakka::getCount()
 */

$count = 0;
if ($this->existsUser())
{
	$whereOwner = "`owner` = '" . $this->GetUserName() . "' AND `latest` = 'Y'";
	$count = $this->getCount('pages',$whereOwner);
}
$whereTotal = "`latest` = 'Y'";
#$count = $this->getCount('pages', $whereOwner);
$total = $this->getCount('pages',$whereTotal);

$percent = round( ($count/$total )*100, 2 ) ;

$disp_count = '<strong>'.$count.'</strong>';
$disp_total = '<strong>'.$total.'</strong>';
$disp_percent = '<strong>'.$percent.'%</strong>';
echo sprintf(OWNEDPAGES_COUNTS,$disp_count,$disp_total);
echo '<br />';
echo sprintf(OWNEDPAGES_PERCENTAGE,$disp_percent);
?>
