<?php
/**
 * Display the raw version of a wiki page, i.e. the source with no formatting.
 *
 * @package		Handlers
 * @subpackage	Page
 * @version		$Id: raw.php 738 2007-10-03 11:48:41Z JavaWoman $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses		Wakka::HasAccess()
 */

if ($this->HasAccess('read') && $this->page)
{
	// display raw page.
	// Send page as UTF-8 in cases where webserver is set up for different char set.
	header('Content-Type: text/plain; charset=utf-8');
	print($this->page['body']);
}
?>
