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
echo '<a href="'.$this->Href('history').'" title="'.HISTORYLINK_TITLE.'">'.HISTORYLINK_TEXT.'</a>'."\n";
?>
