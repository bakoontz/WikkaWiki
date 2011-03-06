<?php
/**
 * Print information on the current release.
 *
 * @package		Actions
 * @version		$Id: wikkachanges.php 736 2007-10-03 10:56:11Z JavaWoman $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	Wakka::Format()
 * @todo	should be a default page, not an action
 */

$output = "=====Wikka Release Notes=====\n\n".
"This server is running [[http://wikkawiki.org/ Wikka Wiki]] version ##{{wikkaversion}}##.\n".
"For detailed release notes please check the [[http://docs.wikkawiki.org/WikkaReleaseNotes Wikka website]].";

print $this->Format($output);

?>
