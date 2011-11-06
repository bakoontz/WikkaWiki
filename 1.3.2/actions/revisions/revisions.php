<?php
if ($this->GetPageTime()) {
	echo "<a href=\"".$this->href("revisions")."\" title=\"Click to view recent revisions list for this page\">".$this->GetPageTime()."</a>\n";
}
?>
