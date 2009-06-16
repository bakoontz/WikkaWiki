<?php
/**
 * Revision link menulet
 */
echo $this->GetPageTime() ? '<a class="datetime" href="'.$this->Href('revisions').'" title="Click to view recent revisions list for this page">'.$this->GetPageTime().'</a> <a href="'.$this->Href('revisions.xml').'" title="Click to display a feed with the latest revisions to this page."><img src="images/feed.png" class="icon" width="14" height="14" alt="feed icon" /></a>' : '';
?>