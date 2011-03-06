<?php
/**
 * Echos the list of InterWiki shortcuts.
 *
 * @package		Actions
 * @version		$Id: interwikilist.php 820 2007-11-23 09:21:08Z DotMG $
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses		Wakka::Format
 */

$file = implode("", file("interwiki.conf", 1));
print($this->Format("%%".$file."%%"));

?>