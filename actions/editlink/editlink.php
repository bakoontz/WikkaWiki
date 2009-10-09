<?php
/**
 * Edit link menulet
 */
if ($this->handler == 'edit' || $this->handler == 'showcode')
{
	echo '<a href="'.$this->Href().'" title="'.SHOWLINK_TITLE.'">'.SHOWLINK_TEXT.'</a>';
}
else
{
	if ($this->HasAccess('write'))
	{
		echo '<a href="'.$this->Href('edit').'" title="'.EDITLINK_TITLE.'">'.EDITLINK_TEXT.'</a>';
	}
	else
	{
		echo '<a href="'.$this->Href('showcode').'" title="'.SHOWCODELINK_TITLE.'">'.SHOWCODELINK_TEXT.'</a>';
	}
}
?>
