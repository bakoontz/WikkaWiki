<?php
/**
 * Delete link menulet
 *
 * Displays a link to delete the current page (depending on user privileges).
 *
 * Syntax: {{deletelink}}
 *
 * @package		Actions
 * @subpackage	Menulets
 * @name		Delete link
 *
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 */
echo '<a href="'.$this->Href('delete').'" title="'.T_("Delete this page (requires confirmation)").'">'.T_("[Delete]").'</a>'."\n";
?>
