<?php
$page = $this->LoadSingle("SELECT * FROM ".$this->GetConfigValue('table_prefix')."pages WHERE tag='".$this->GetPageTag()."' AND latest = 'Y'");
echo ($page["note"])? $this->Format("//".$page["note"]."//") : $this->Format("//No edit notes available//");
?>
