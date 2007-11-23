<?php
/**
 * Print number of pages owned by the current user.
 *
 * @package		Actions
 * @version		$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	Wakka::GetUserName()
 * @uses	Wakka::getCount()
 * @uses	Wakka::Link()
 * @todo	print different text if user is not logged in
 */
/*
$where = "`owner` = '".mysql_real_escape_string($this->GetUserName())."' AND `latest` = 'Y'";
$count = $this->getCount('pages', $where);
*/
$count = 0;
if ($username = $this->GetUserName())		// no param: get name of logged in user only (#543)
{
	$where = "`owner` = ".mysql_real_escape_string($username)." AND `latest` = 'Y'";
	$count = $this->getCount('pages',$where);
}

echo $this->Link('MyPages', '', $count,'','', DISPLAY_MYPAGES_LINK_TITLE);

?>