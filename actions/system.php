<?php
/**
 * Display information about the system Wikka is running on.
 *
 * Depending on the 'show' parameter, this action displays different types of system information.
 * By default it only displays this information to Wikka Admins, this option can be changed by 
 * setting 'public_sysinfo' to '1' in the Wikka configuration file.
 * 
 *  Syntax:
 *	{{system [show="OS|machine|host"]}}
 *
 * @package		Actions
 * @name		System
 *
 * @author		{@link http://wikkawiki.org/JavaWoman Marjolein Katsma} (first version)
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli} (admin check)
 * @since		Wikka 1.1.6.2
 *
 * @input		string	$show	optional: what type of system info to show (OS, machine or host);
 *				default: empty, shows all information
 */

// i18n
if (!defined('NOT_AVAILABLE')) define('NOT_AVAILABLE', 'n/a');

// defaults
$show = '';
$out = NOT_AVAILABLE;

//check privs
if ($this->config['public_sysinfo'] == '1' || $this->IsAdmin())
{

	// get param and validation
	$valid_show = array('os','machine','host');
	if (is_array($vars))
	{
		foreach ($vars as $param => $value)
		{
			switch ($param)
			{
				case 'show':
					if (in_array($value, $valid_show)) $show = strtolower($value);
					break;
			}
		}
	}
	
	// get data
	$host		= php_uname('n');
	$os			= php_uname('s');
	$release	= php_uname('r');
	$version	= php_uname('v');
	$machine	= php_uname('m');
	
	// build output
	$out = '';
	switch ($show)
	{
		case '':
			if (isset($os)) $out .= $os.' ';
			if (isset($release)) $out .= $release.' ';
			if (isset($version)) $out .= $version.' ';
			if (isset($machine)) $out .= $machine.' ';
			if (isset($host)) $out .= '('.$host.')';
			break;
		case 'os':
			if (isset($os)) $out .= $os.' ';
			if (isset($release)) $out .= $release.' ';
			if (isset($version)) $out .= $version.' ';
			break;
		case 'machine':
			if (isset($machine)) $out .= $machine.' ';
			break;
		case 'host':
			if (isset($host)) $out .= $host.' ';
			break;
	}
}	

// show result
echo trim($out);
?>