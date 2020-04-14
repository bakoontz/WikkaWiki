<?php 
if ($this->GetUser()) {
	echo "<a href='".$this->href("referrers")."' title='Click to view a list of URLs referring to this page.'>Referrers</a>\n"; 
}
?> 
