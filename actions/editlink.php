<?php
/**
 * Edit link menulet
 */
if ($this->method == 'edit' || $this->method == 'showcode')
{
	echo '<a href="'.$this->Href().'" title="Displayed the formatted version of this page">[Show]</a>';
}
else
{
	if ($this->HasAccess('write'))
	{
		echo '<a href="'.$this->Href('edit').'" title="Click to edit this page">[Edit]</a>';
	}
	else
	{
		echo '<a href="'.$this->Href('showcode').'" title="Display the markup for this page">[Source]</a>';
	}
}
?>
