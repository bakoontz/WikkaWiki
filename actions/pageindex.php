<?php /*dotmg modifications : contact m.randimbisoa@dotmg.net*/ ?>
<?php

if ($pages = $this->LoadAllPages())
{
	$curChar = "";
	foreach ($pages as $page)
	{
		//#dotmg [1 line modified]: added [0-9]+$ to regular expression
		if (!preg_match("/^Comment[0-9]+$/", $page["tag"])) {
			$firstChar = strtoupper($page["tag"][0]);
			if (!preg_match("/[A-Z,a-z]/", $firstChar)) {
				$firstChar = "#";
			}

			if ($firstChar != $curChar) {
				if ($curChar) print("<br />\n");
				print("<strong>$firstChar</strong><br />\n");
				$curChar = $firstChar;
			}

			if ($this->HasAccess("read", $page["tag"])) {
				print($this->Link($page["tag"]));
     			}
			else {
				print($page["tag"]);
			}
			if ($page['owner'] != '')
     			{                       
       			if ($this->UserName() == $page['owner']) 
      				print(" . . . . one of your pages.");
     				else {
					if ($this->HasAccess("read", $page["owner"])) {
	         				print(" . . . . Owner: ".$this->Format($page['owner']));
		     			}
					else {
   						print(" . . . . Owner: ".$page['owner']);
					}
				}
     			}

     			print("<br />\n");    
		}
	}
}
else
{
	print("<em>No pages found.</em>");
}

?>