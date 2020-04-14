<?php
if ($this->page){
	if ($owner = $this->GetPageOwner()){
		if ($owner == "(Public)"){
			print $this->IsAdmin() ? "<a href=\"".$this->href("acls")."\">(Edit ACLs)</a>\n" : "";
		} elseif ($this->UserIsOwner()){
			print "<a href=\"".$this->href("acls")."\">Edit ACLs</a>\n";
		} 
	} else {
		print ($this->GetUser()) ? " (<a href=\"".$this->href("claim")."\">Take Ownership</a>)\n" : "";
	}
}
?>

