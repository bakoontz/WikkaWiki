<?php
$id = htmlspecialchars($vars['id']);
if (is_numeric($id)) {
	print($this->Link("http://bugs.wakkawiki.com/mantis/bug_view_page.php?bug_id=$id", "", "Bug $id"));
} else {
	print($this->Link("http://bugs.wakkawiki.com/mantis/", "", "Bugtracker"));
}
?>
