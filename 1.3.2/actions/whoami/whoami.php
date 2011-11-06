<?php
/**
 * 'Who am I?' menulet
 *
 * Display the current username if signed in or the hostname for anonymous visitors
 * This menulet can be toggled from the menu configuration files under /config.
 *
 * Syntax: {{whoami}}
 *
 * @package		Actions
 * @subpackage	Menulets
 * @name		Who am I?
 *
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 */
$currentuser = $this->GetUserName();

if (strlen($currentuser)>0)
{
	//user is registered
	if ($this->GetUser())
	{
		echo $this->FormatUser($currentuser);
	}
	//user is anonymous
	else
	{
		echo "<tt>".$currentuser."</tt>";
	}
}
?>
