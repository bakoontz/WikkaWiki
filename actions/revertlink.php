<?php
/**
 * Revert link menulet (admin-only)
 *
 * @todo	Use a new HasRevisions() method instead of loading admin class
 */
if ($this->IsAdmin())
{
	include_once('libs/admin.lib.php');
	$res = LoadLastTwoPagesByTag($this, $this->tag);
	if(null !== $res)
	{
		echo '<a href="'.$this->Href('revert').'" title="Click to revert this page to the previous revision">[Revert]</a>';
	}
	else
	{
		echo '<span class="disabled" title="There are no older revisions for this page">[Revert]</span>';
	}
}
?>
