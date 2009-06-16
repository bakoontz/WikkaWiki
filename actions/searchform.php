<?php
/**
 * Search form menulet
 */
echo $this->FormOpen('', 'TextSearch', 'get');
echo 'Search: <input name="phrase" size="15" class="searchbox" />';
echo $this->FormClose();
?>