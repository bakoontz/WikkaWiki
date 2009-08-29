<?php
/**
 * Delete link menulet
 */
//i18n
if (!defined('DELETELINK_TEXT')) define('DELETELINK_TEXT', '[Delete]');
if (!defined('DELETELINK_TITLE')) define('DELETELINK_TITLE', 'Delete this page (requires confirmation)');

echo '<a href="'.$this->Href('delete').'" title="'.DELETELINK_TITLE.'">'.DELETELINK_TEXT.'</a>'."\n";
?>