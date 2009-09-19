<?php
/**
 * Revision link menulet
 */
//i18n
if (!defined('REVISIONLINK_TITLE')) define('REVISIONLINK_TITLE', 'Click to view recent revisions list for this page');
if (!defined('REVISIONFEEDLINK_TITLE')) define('REVISIONFEEDLINK_TITLE', 'Click to display a feed with the latest revisions to this page');

echo $this->GetPageTime() ? '<a class="datetime" href="'.$this->Href('revisions').'" title="'.REVISIONLINK_TITLE.'">'.$this->GetPageTime().'</a> <a href="'.$this->Href('revisions.xml').'" title="'.REVISIONFEEDLINK_TITLE.'"><img src="images/feed.png" class="icon" width="14" height="14" alt="feed icon" /></a>' : '';
?>