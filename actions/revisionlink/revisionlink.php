<?php
/**
 * Revision link menulet
 *
 * Displays the date and time of the latest revision and a link to the list of
 * recent revisions of the current page.
 *
 * Syntax: {{clonelink}}
 *
 * @package		Actions
 * @subpackage	Menulets
 * @name		Clone link
 *
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 */
echo $this->GetPageTime() ? '<a class="datetime" href="'.$this->Href('revisions').'" title="'.T_("Click to view recent revisions list for this page").'">'.$this->GetPageTime().'</a> <a href="'.$this->Href('revisions.xml').'" title="'.T_("Click to display a feed with the latest revisions to this page").'"><img src="images/feed.png" class="icon" width="14" height="14" alt="feed icon" /></a>' : '';
?>
