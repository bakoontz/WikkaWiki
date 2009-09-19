<?php
/**
 * Print Wikka patch level. 
 *
 * By default this only displays for Wikka admins. This option can be
 * changed by setting 'public_sysinfo' to '1' in the Wikka
 * configuration file.
 *
 * Syntax: {{wikkapatchlevel}}
 *
 * @package		Actions
 * @name		Wikka Patch Level 
 *
 * @author		{@link http://wikkawiki.org/BrianKoontz Brian Koontz}
 *
 * Also see author list for {{wikkaversion}}
 */

// i18n
if (!defined('NOT_AVAILABLE')) define('NOT_AVAILABLE', 'n/a');
if (!defined('WIKKA_ADMIN_ONLY_TITLE')) define('WIKKA_ADMIN_ONLY_TITLE', 'Sorry, only wiki administrators can display this information');

// defaults
$out = '<abbr title="'.WIKKA_ADMIN_ONLY_TITLE.'">'.NOT_AVAILABLE.'</abbr>';

//check privs
if ($this->config['public_sysinfo'] == '1' || $this->IsAdmin())
{
	$out = $this->PATCH_LEVEL;
}
echo $out;
?>
