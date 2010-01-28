<?php
/**
 * Clone link menulet
 *
 * Displays a link to clone the current page (depending on user privileges).
 *
 * Syntax: {{clonelink}}
 *
 * @package		Actions
 * @subpackage	Menulets
 * @name		Clone link
 *
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 */
echo '<a href="'.$this->Href('clone').'" title="'.CLONELINK_TITLE.'">'.CLONELINK_TEXT.'</a>'."\n";
?>
