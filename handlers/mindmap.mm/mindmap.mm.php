<?php
/**
 * Display a mindmap as XML.
 *
 * @package		Handlers
 * @subpackage	Mindmaps
 * @version		$Id: mindmap.mm.php 738 2007-10-03 11:48:41Z JavaWoman $
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses		Wakka::HasAccess()
 */
if ($this->HasAccess('read'))
{
	if (!$this->page)
	{
		return;
	}
	else
	{
		// display mind map xml
		$pagebody = $this->page['body'];
		if (preg_match('/(<map.*<\/map>)/s', $pagebody, $matches))
		{
			echo $matches[1];
		}
	}
}
else
{
	return;
}
?>