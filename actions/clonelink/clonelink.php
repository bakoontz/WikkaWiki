<?php
/**
 * Clone link menulet
 */
//i18n
if (!defined('CLONELINK_TEXT')) define('CLONELINK_TEXT', '[Clone]');
if (!defined('CLONELINK_TITLE')) define('CLONELINK_TITLE', 'Duplicate this page');

echo '<a href="'.$this->Href('clone').'" title="'.CLONELINK_TITLE.'">'.CLONELINK_TEXT.'</a>'."\n";
?>