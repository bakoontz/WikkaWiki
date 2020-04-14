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
	include_once($this->BuildFullpathFromMultipath('../../libs/admin.lib.php', $this->GetConfigValue('action_path')));
	$res = LoadLastTwoPagesByTag($this, $this->GetPageTag());
	if(null !== $res)
	{
		echo '<a href="'.$this->Href('revert').'" title="Click to revert this page to the previous revision">'.T_("[Revert]").'</a>';
	}
	else
	{
		echo '<span class="disabled" title="'.T_("This is the oldest known version for this page").'">'.T_("[Revert]").'</span>';
	}
}
?>
