<?php
/**
 * Search form menulet
 */
echo $this->FormOpen('', 'TextSearch', 'get');
echo '<label for="searchbox">'.T_("Search: ").'</label><input id="searchbox" name="phrase" size="15" class="searchbox" />';
echo $this->FormClose();
?>
