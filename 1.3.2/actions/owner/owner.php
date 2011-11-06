<?php
if ($this->page) {
	if ($owner = $this->GetPageOwner()){
		if ($owner == "(Public)"){
			print "Public page";
		}
		elseif ($this->UserIsOwner()) {
			if ($this->IsAdmin()) {
				print "Owner: ".$this->Link($owner, "", "", 0)."\n";
			} else {
				print"You own this page.\n";
			}
		} else {
			print "Owner: ".$this->Link($owner, "", "", 0)."\n";
		}
	} else {
		print "Nobody\n";
	}
}
?>
