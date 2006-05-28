<?php
/**
 * Display current GeSHi version.
 *
 * @package		Actions
 * @name		GeSHiVersion
 *
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 * @since		Wikka 1.1.6.2
 *
 */

// i18n
if (!defined('NOT_AVAILABLE')) define('NOT_AVAILABLE', 'n/a');
if (!defined('NOT_INSTALLED')) define('NOT_INSTALLED', 'not installed');

if (file_exists($this->config['geshi_path'].'/geshi.php'))
{
	include_once($this->config['geshi_path'].'/geshi.php');
	if (defined('GESHI_VERSION'))
	{
		echo GESHI_VERSION;
	}
	else
	{
		echo NOT_AVAILABLE;
	}
}
else
{
		echo NOT_INSTALLED;
}
?>