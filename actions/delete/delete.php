<?php
echo  ($this->IsAdmin())? "<a href=\"".$this->href("delete")."\" title=\"Click to delete this page\">Delete this page</a>\n" : "";  #i18n
?>
