<?php
/**
 * Search form menulet
 */
echo $this->FormOpen('', 'TextSearch', 'get');
echo '<label for="searchbox">'.SEARCHFORM_LABEL.'</label><input id="searchbox" name="phrase" size="15" class="searchbox" />';
echo $this->FormClose();
?>
