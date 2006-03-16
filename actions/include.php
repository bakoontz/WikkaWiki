<?php

define('CIRCULAR_REFERENCE_DETECTED', 'Circular reference detected');

if (!$page) $page = $wikka_vars;
$page = strtolower($page);
if (!$this->config["includes"]) $this->config["includes"][] = strtolower($this->tag);

if (!in_array($page, $this->config["includes"]) && $page != $this->tag) {
	if ($this->HasAccess("read", $page)) {
      	$this->config["includes"][] = $page;
        	$page = $this->LoadPage($page);
		print $this->Format($page["body"]);
	}
} else print '<span class="error">'.CIRCULAR_REFERENCE_DETECTED.'</span>';

?>