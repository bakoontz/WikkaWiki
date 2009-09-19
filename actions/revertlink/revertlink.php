<?php
/**
 * Revert link menulet (admin-only)
 *
 * @todo	Use a new HasRevisions() method instead of loading admin class
 */
//i18n
if (!defined('REVERTLINK_TEXT')) define('REVERTLINK_TEXT', '[Revert]');
if (!defined('REVERTLINK_TITLE')) define('REVERTLINK_TITLE', 'Click to revert this page to the previous revision');
if (!defined('REVERTLINK_OLDEST_TITLE')) define('REVERTLINK_OLDEST_TITLE', 'This is the oldest known version for this page');

if ($this->IsAdmin())
{
	include_once('libs/admin.lib.php');
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
