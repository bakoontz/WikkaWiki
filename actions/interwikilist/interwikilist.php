<?php
/**
 * Echos the list of InterWiki shortcuts.
 *
 * @package		Actions
 * @version		$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses		Wakka::Format
 */

$file = implode("", file("interwiki.conf", 1));
print($this->Format("%%".$file."%%"));

?>