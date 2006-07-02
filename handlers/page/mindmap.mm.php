<?php
/**
 * Display a mindmap as XML. 
 * 
 * @package		Handlers
 * @subpackage	Mindmap
 * @name		mindmap.mm.php
 * @version		$Id$
 * 
 * @uses		Wakka::HasAccess()
 */
if ($this->HasAccess("read"))
{
	if (!$this->page)
	{
		return;
	}
	else
	{
		// display mind map xml
		$pagebody = $this->page["body"];
		if (preg_match("/(<map.*<\/map>)/s", $pagebody, $matches))
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