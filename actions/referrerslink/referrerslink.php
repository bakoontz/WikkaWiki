<?php
/**
 * Referrers link menulet
 *
 * Displays a link to a handler displaying referrers for the current page.
 *
 * Syntax: {{referrerslink}}
 *
 * @package		Actions
 * @subpackage	Menulets
 * @name		Referrers link
 *
 * @author		{@link http://wikkawiki.org/EmeraldIsland EmeraldIsland}
 */
echo '<a href="'.$this->Href('referrers').'" title="'.T_("Click to view a list of URLs referring to this page.").'">'.T_("[Referrers]").'</a>';
?>
