<?php
#$skin = ($this->GetCookie("wikiskin"))? $this->GetCookie("wikiskin") : $this->GetConfigValue("stylesheet");
$defaultskin = $this->config['stylesheet'];
$skin = (!$this->GetCookie('wikiskin')) ? $defaultskin : $this->GetCookie('wikiskin'); # JW 2005-07-08 FIX possibly undefined cookie
echo '<a href="'.$this->config['base_url'].'css/'.$skin.'" title="Display stylesheet">'.$skin.'</a>';
?>