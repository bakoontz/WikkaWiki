<?php
/**
 * History link menulet
 */
//i18n
if (!defined('HISTORYLINK_TEXT')) define('HISTORYLINK_TEXT', '[History]');
if (!defined('HISTORYLINK_TITLE')) define('HISTORYLINK_TITLE', 'Click to view recent edits to this page');

echo '<a href="'.$this->Href('history').'" title="'.HISTORYLINK_TITLE.'">'.HISTORYLINK_TEXT.'</a>'."\n";
?>