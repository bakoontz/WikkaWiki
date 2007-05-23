<?php
/**
 * Print number of pages owned by the current user.
 * 
 * @package		Actions
 * @version		$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses	Wakka::GetUserName()
 * @uses	Wakka::getCount()
 * @uses	Wakka::Link()
 */

$where = "`owner` = '".mysql_real_escape_string($this->GetUserName())."' AND `latest` = 'Y'";
$count = $this->getCount('pages', $where);
echo $this->Link('MyPages', '', $count,'','', DISPLAY_MYPAGES_LINK_TITLE);

?>
