<?php
/**
 * Homepage link menulet
 *
 * Displays a link to the wiki homepage.
 *
 * Syntax: {{homepagelink}}
 *
 * @package		Actions
 * @subpackage	Menulets
 * @name		Homepage link
 *
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 */
echo '<a href="'.$this->href('', $this->GetConfigValue('root_page'), '').'">'.$this->GetConfigValue('root_page').'</a>'."\n";
?>
