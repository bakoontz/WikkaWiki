<?php
$page = $this->LoadSingle("SELECT * FROM ".$this->config["table_prefix"]."pages WHERE tag='".$this->GetPageTag()."' AND latest = 'Y'");
$user = ($this->LoadUser($page["user"]))? $this->Link($page["user"]) : "anonymous";
echo $user;
?>
