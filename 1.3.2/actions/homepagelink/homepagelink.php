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
echo '<a href="'.$this->href('', $this->config['root_page'], '').'">'.$this->config['root_page'].'</a>'."\n";
?>
