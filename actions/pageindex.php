<?php
if ($pages = $this->LoadAllPages())
{
	$curChar = "";
	$cached_username = $this->GetUserName();
	$owns_pages = false;

	foreach ($pages as $page)
	{
		$page_owner = $page["owner"];
		$this->CachePage($page);

		$firstChar = strtoupper($page["tag"][0]);
		if (!preg_match("/[A-Z,a-z]/", $firstChar)) {
			$firstChar = "#";
		}
		if ($firstChar != $curChar) {
			if ($curChar) print("<br />\n");
			print("<strong>$firstChar</strong><br />\n");
			$curChar = $firstChar;
		}

			print($this->Link($page["tag"]));

		if ($page_owner != '' && $page_owner != '(Public)')
     		{                       
       		if ($cached_username == $page_owner) 
     		{                       
      			print("*");
				$owns_pages = true;
				}
				else {
				print(" . . . . Owner: ".$page_owner);
				}
			}
     		print("<br />\n");    
	}
	if ($owns_pages) print("<br />\n* Indicates a page that you own.<br />\n");    
}
else
{
	print("<em>No pages found.</em>");
}

?>