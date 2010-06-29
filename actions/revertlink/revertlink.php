<?php
/**
 * Revert link menulet (admin-only)
 *
 * Displays a link allowing admins to revert the current page to the previous
 * version.
 *
 * Syntax: {{revertlink}}
 *
 * @package		Actions
 * @subpackage	Menulets
 * @name		Revert link
 *
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 *
 * @todo	Use a new HasRevisions() method instead of loading admin class
 */
if ($this->IsAdmin())
{
	include_once($this->BuildFullpathFromMultipath('..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'admin.lib.php', $this->GetConfigValue('action_path')));
	$res = LoadLastTwoPagesByTag($this, $this->tag);
	if(null !== $res)
	{
		echo '<a href="'.$this->Href('revert').'" title="Click to revert this page to the previous revision">'.REVERTLINK_TEXT.'</a>';
	}
	else
	{
		echo '<span class="disabled" title="'.REVERTLINK_OLDEST_TITLE.'">'.REVERTLINK_TEXT.'</span>';
	}
}
?>
