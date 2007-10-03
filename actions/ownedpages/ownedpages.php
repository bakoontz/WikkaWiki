<?php
/**
 * Display the number and percentage of pages owned by the current user.
 *
 * @package	Actions
 * @version	$Id$
 * @license	http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author	{@link http://web.archive.org/web/20040820215257/http://www.wakkawiki.com/ChrisTessmer Chris Tessmer}
 *
 * @uses	Wakka::reg_username
 * @uses	Wakka::existsUser()
 * @uses	Wakka::getCount()
 */

$count = 0;
#if ($user = $this->GetUser())		// check if user is logged in and get data
if ($this->existsUser())
{
	#$whereOwner = "`owner` = '" . $this->GetUserName() . "' AND `latest` = 'Y'";
	#$whereOwner = "`owner` = '" . $user['name'] . "' AND `latest` = 'Y'";
	$whereOwner = "`owner` = '" . $this->reg_username . "' AND `latest` = 'Y'";
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