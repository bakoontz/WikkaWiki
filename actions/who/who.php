<?php
if ($this->GetUser()) {
	echo "You are ".$this->Format($this->GetUserName());
}
?>