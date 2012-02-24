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
if ($this->GetHandler() == 'edit' || $this->GetHandler() == 'showcode')
{
	echo '<a href="'.$this->Href().'" title="'.T_("Displayed the formatted version of this page").'">'.T_("[Show]").'</a>';
}
else
{
	if ($this->HasAccess('write'))
	{
		echo '<a href="'.$this->Href('edit').'" title="'.T_("Click to edit this page").'">'.T_("[Edit]").'</a>';
	}
	else
	{
		echo '<a href="'.$this->Href('showcode').'" title="'.T_("Display the markup for this page").'">'.T_("[Source]").'</a>';
	}
}
?>
