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

class WikkaAction_interwikilist extends WikkaAction
{
	function WikkaAction_interwikilist($wakka)
	{
		parent::WikkaAction($wakka, __FILE__);
	}	

	static function getInfo()
	{
		return array(
			'author' => 'DotMG',
			'email' => 'dotmg@wikkawiki.org',
			'date' => '2007-11-23',
			'name' => 'Interwiki List',
			'desc' => 'Displays a list of interwiki tokens',
			'url' => 'http://www.wikkawiki.org',
			'since' => '1.3'
		);
	}

	function process($vars = null)
	{
		if($this->_isDisabled())
			return;

		$file = implode("", file("interwiki.conf", 1));
		print($this->wakka->Format("%%".$file."%%"));
	}
}
?>
