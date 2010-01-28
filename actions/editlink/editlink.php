<?php
/**
 * Edit link menulet
 *
 * Displays a link to modify the current page (depending on user privileges).
 * If the current user has now user privileges, a link to display the page
 * source is displayed instead.
 *
 * Syntax: {{editlink}}
 *
 * @package		Actions
 * @subpackage	Menulets
 * @name		Edit link
 *
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli}
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
