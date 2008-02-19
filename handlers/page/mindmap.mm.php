<?php
if ($this->HasAccess("read"))
{
	if (!$this->page)
	{
		return;
	}
	else
	{
		// display raw page
		print($this->page["body"]);
	}
}
else
{
	return;
}
?>