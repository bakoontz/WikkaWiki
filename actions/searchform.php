<?php
/**
 * Search form menulet
 */
//i18n
if (!defined('SEARCHFORM_LABEL')) define('SEARCHFORM_LABEL', 'Search: ');

echo $this->FormOpen('', 'TextSearch', 'get');
echo '<label for="searchbox">'.SEARCHFORM_LABEL.'</label><input id="searchbox" name="phrase" size="15" class="searchbox" />';
echo $this->FormClose();
?>