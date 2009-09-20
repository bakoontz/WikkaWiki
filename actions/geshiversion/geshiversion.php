<?php
/**
 * Display current GeSHi version.
 *
 * By default this only displays for Wikka admins. This option can be
 * changed by setting 'public_sysinfo' to '1' in the Wikka
 * configuration file.
 * 
 * Syntax: {{geshiversion}}
 *
 * @package		Actions
 * @name		GeSHiVersion
 *
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 * @author		{@link http://wikkawiki.org/JavaWoman JavaWoman}
 * @author		{@link http://wikkawiki.org/BrianKoontz Brian Koontz}
 * @since		Wikka 1.1.6.2
 *
 */

// defaults
$out = '<abbr title="'.WIKKA_ADMIN_ONLY_TITLE.'">'.NOT_AVAILABLE.'</abbr>'."\n";

//check privs
if ($this->config['public_sysinfo'] == '1' || $this->IsAdmin())
{
	if (file_exists($this->config['geshi_path'].'/geshi.php'))
	{
		include_once($this->config['geshi_path'].'/geshi.php');
		if (defined('GESHI_VERSION'))
		{
			$out = GESHI_VERSION;
		}
		else
		{
			$out = NOT_AVAILABLE;
		}
	}
	else
	{
			$out = NOT_INSTALLED;
	}
}
echo $out;
?>
