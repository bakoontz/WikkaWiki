<?php
/**
 * History link menulet
 *
 * Displays a link the revision history of the current page.
 *
 * Syntax: {{historylink}}
 *
 * @package		Actions
 * @subpackage	Menulets
 * @name		History link
 *
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 */
echo '<a href="'.$this->Href('history').'" title="'.T_("Click to view recent edits to this page").'">'.T_("[History]").'</a>'."\n";
?>
