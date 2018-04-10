<?php
/**
 * Print number of pages owned by the current user.
 *
 * @package		Actions
 * @version		$Id: countowned.php 920 2008-02-18 22:42:38Z BrianKoontz $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	Wakka::GetUserName()
 * @uses	Wakka::getCount()
 * @uses	Wakka::Link()
 * @todo	print different text if user is not logged in
 * @todo	Add parameter to specify date range #955
 */
$count = 0;
if ($username = $this->GetUserName())		// no param: get name of logged in user only (#543)
{
	$where = "owner = :username AND latest = 'Y'";
	$count = $this->getCount('pages', $where, array(':username' => $username));
}

echo $this->Link('MyPages', '', $count,'','', T_("Display a list of the pages you currently own"));

?>
