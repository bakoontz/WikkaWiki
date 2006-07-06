<?php
/**
 * Includes an (external) page into the current page.
 *  
 * @package		Actions
 * @version		$Id$
 * 
 * @uses	Wakka::cleanUrl()
 * @uses	Wakka::htmlspecialchars_ent()
 */
 
$width = $this->htmlspecialchars_ent(trim($vars['width']));
$height = $this->htmlspecialchars_ent(trim($vars['height']));
$url = $this->cleanUrl(trim($vars['url']));

echo '<iframe width="'.$width.'" height="'.$height.'" src="'.$url.'"></iframe>';
?>