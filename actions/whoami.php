<?php
/**
 * Display the current username if signed in or the hostname for anonymous visitors
 * This menulet can be toggled from the menu configuration files under /config.
 * 
 * @todo	Use FormatUser() method when available
 */
$currentuser = $this->GetUserName();

if (strlen($currentuser)>0)
{
	//user is registered
	if ($this->GetUser())
	{
		echo $this->Link($currentuser);
	}
	//user is anonymous
	else
	{
		echo "<tt>".$currentuser."</tt>";
	}
}
?>