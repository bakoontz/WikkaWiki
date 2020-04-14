<?php
if ($this->HasAccess("write")) {
	echo  "<a href=\"".$this->href("edit")."\" title=\"Click to edit this page\">Edit page</a>\n"; #i18n
} else {
	echo  "<a href=\"".$this->href("showcode")."\" title=\"Click to display the page source\">Show code</a>\n"; #i18n
}
?>
