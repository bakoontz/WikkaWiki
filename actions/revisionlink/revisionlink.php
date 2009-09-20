<?php
/**
 * Revision link menulet
 */
echo $this->GetPageTime() ? '<a class="datetime" href="'.$this->Href('revisions').'" title="'.REVISIONLINK_TITLE.'">'.$this->GetPageTime().'</a> <a href="'.$this->Href('revisions.xml').'" title="'.REVISIONFEEDLINK_TITLE.'"><img src="images/feed.png" class="icon" width="14" height="14" alt="feed icon" /></a>' : '';
?>
