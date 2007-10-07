<?php
/**
 * Display the web wizard to install or upgrade WikkaWiki.
 * 
 * @package	Setup
 * @version	$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @todo i18n;
 * @todo Make requirements version-dependent if needed;
 */

//defaults
#if (!defined('PHP_REQ')) define('PHP_REQ', '4.1');		// vv use MINIMUM_PHP_VERSION (already defined!)
#if (!defined('MYSQL_REQ')) define('MYSQL_REQ', '3.23');	// vv use MINIMUM_MYSQL_VERSION (already defined!)

$server_info = array();
// Try to create .htaccess
if (!file_exists($htaccessLocation))
{
	#@touch($htaccessLocation);			// check result from touch - error if not successful!
	test('Creating empty .htaccess file...',@touch($htaccessLocation),'Could not create '.$htaccessLocation,0);	// @@@ should stop on error! don't stop for debugging only
}
if (preg_match('/Apache\/?([0-9|\.]*)/i', $_SERVER['SERVER_SOFTWARE'], $webserver_version))
{
	// Apache
	$server_info['type'] = 'Apache';
	$server_info['version'] = !empty($webserver_version[1]) ? $webserver_version[1] : 'n/a';
	$server_info['php_interface'] = (FALSE !== stristr(php_sapi_name(), 'cgi')) ? 'CGI' : 'Module';

	// mod_rewrite module checker
	ob_start();
	phpinfo(INFO_MODULES);
	$s = ob_get_contents();
	ob_end_clean();	
}
elseif (preg_match('/IIS\/?([0-9|\.]*)/i', $_SERVER['SERVER_SOFTWARE'], $webserver_version))
{
	// Microsoft IIS
	$server_info['type'] = 'Microsoft IIS';
	$server_info['version'] = !empty($webserver_version[1]) ? $webserver_version[1] : 'n/a';
}
elseif (preg_match('/lighttpd\/?([0-9|\.]*)/i', $_SERVER['SERVER_SOFTWARE'], $webserver_version))
{
	// lighttpd
	$server_info['type'] = 'Lighttpd';
	$server_info['version'] = !empty($webserver_version[1]) ? $webserver_version[1] : 'n/a';
}
else
{
	// Unknown
	$server_info['type'] = 'Unknown';
	if (strlen($_SERVER['SERVER_SOFTWARE']) > 0)
	{
		$server_info['type'] .= '('.$_SERVER['SERVER_SOFTWARE'].')';
	}
}



$php_note = '';
$mysql_note = '';
$php_req_ok = FALSE;
$mysql_req_ok = FALSE;

$server_info['mod_rewrite'] = false;
$server_info['mod_rewrite'] = test_mod_rewrite();

?>
<form action="<?php echo $action_target; ?>" name="form1" method="post">
<?php
// Start UPGRADER
if ($wakkaConfig['wakka_version'])
{
	echo '<h1>'.__('WikkaWiki Upgrade').' (2/5)</h1>'."\n";
}
// Start INSTALLER
else
{
	echo '<h1>'.__('WikkaWiki Installation').' (2/5)</h1>'."\n";
}

// Check system requirements

$server_info['php'] = phpversion();
$dblink = @mysql_connect($config['mysql_host'], $config['mysql_user'], $config['mysql_password']);
$server_info['mysql'] = @mysql_get_server_info($dblink);
$db_exists = @mysql_select_db($config['mysql_database'], $dblink);

#if ((function_exists('version_compare')) && version_compare($server_info['php'], PHP_REQ, '>='))
if ((function_exists('version_compare')) && version_compare($server_info['php'], MINIMUM_PHP_VERSION, '>='))
{
	$php_note = '<em class="ok">OK</em>';
	$php_req_ok = TRUE;
}
else
{
	#$php_note = '<em class="failed">'.__('Not supported').'</em><br /><span class="small">('.sprintf(__('Min. %s version required:'), 'PHP').' <strong>'.PHP_REQ.'</strong>) '.__('Any other information provided by this installer may be false').'.</span>';
	$php_note = '<em class="failed">'.__('Not supported').'</em><br /><span class="small">('.sprintf(__('Min. %s version required:'), 'PHP').' <strong>'.MINIMUM_PHP_VERSION.'</strong>) '.__('Any other information provided by this installer may be false').'.</span>';
}

#if (version_compare($server_info['mysql'], MYSQL_REQ, '>='))
if (version_compare($server_info['mysql'], MINIMUM_MYSQL_VERSION, '>='))
{
	$mysql_note = '<em class="ok">OK</em>';
	$mysql_req_ok = TRUE;
}
else
{
	#$mysql_note = '<em class="failed">'.__('Not supported').'</em><br /><span class="small">('.sprintf(__('Min. %s version required:'), 'MySQL').' <strong>'.MYSQL_REQ.'</strong>)</span>';
	$mysql_note = '<em class="failed">'.__('Not supported').'</em><br /><span class="small">('.sprintf(__('Min. %s version required:'), 'MySQL').' <strong>'.MINIMUM_MYSQL_VERSION.'</strong>)</span>';
}
if (!$server_info['mysql'])
{
	#$mysql_note = '<em class="failed">'.__('Unable to find MySQL version').'</em><br /><span class="small">('.sprintf(__('Min. %s version required:'), 'MySQL').' <strong>'.MYSQL_REQ.'</strong>, '.__('if you are sure you have the same or a newer version, you can go ahead').'.)</span>';
	$mysql_note = '<em class="failed">'.__('Unable to find MySQL version').'</em><br /><span class="small">('.sprintf(__('Min. %s version required:'), 'MySQL').' <strong>'.MINIMUM_MYSQL_VERSION.'</strong>, '.__('if you are sure you have the same or a newer version, you can go ahead').'.)</span>';
	$server_info['mysql'] = 'n/a';
	$mysql_retry = "\n<input type='hidden' name='retry_mysql' value='1' />";
}

if ($mysql_req_ok && $php_req_ok && $db_exists)
{
	$req = '<h2 class="ok">'.__('Congratulations').'!</h2><p>'.sprintf(__('Your server matches the minimal system requirements to install WikkaWiki %s. You can now proceed to the installation'), '<tt>'.WAKKA_VERSION.'</tt>').'.</p>';
}
elseif ($db_exists)
{
	$req = '<h2 class="failed">'.__('Warning').'</h2><p>'.sprintf(__('Your server does not match some of the minimal system requirements to install WikkaWiki %s. If you decide to install WikkaWiki, some functionality might not correctly work'), '<tt>'.WAKKA_VERSION.'</tt>').'.</p>';
}
else
{
	$req = '<h2 class="failed">'.__('Error').'</h2><p>'.__('The supplied database settings are invalid! Remember: The database should exist before you try to install WikkaWiki, and the mysql_user should have full access to it').'.</p>';
}


// output result
echo '<h2>'.__('System Configuration Check').'</h2>'."\n";
echo '<p>'.__('Your server is running with the following configuration:').'</p>'."\n";
echo '<div class="note"><ul>'."\n";
echo '<li><strong>PHP '.$server_info['php'].'</strong>: '.$php_note.'</li>'."\n";
echo '<li><strong>MySQL '.$server_info['mysql'].'</strong>: '.$mysql_note.'</li>'."\n";
echo '<li><strong>'.__('Server information:').'</strong>'."\n";
if (isset($server_info['type']))
{
	echo '<ul>'."\n";
	echo '<li><strong>'.__('Type').':</strong> '.$server_info['type'].'</li>'."\n";
	if (isset($server_info['version']))
	{
		echo '<li><strong>'.__('Version').':</strong> '.$server_info['version'].'</li>'."\n";
	}
	if (isset($server_info['php_interface']))
	{
		echo '<li><strong>'.__('PHP Interface').':</strong> '.$server_info['php_interface'].'</li>'."\n";
	}
	echo '</ul>'."\n";
}
echo '</li>'."\n";
if (isset($server_info['mod_rewrite']))
{
	echo '<li><strong>'.__('URL rewriting').' ('.__('optional').'):</strong>'."\n";
	echo '<ul>'."\n";	
	echo '<li><strong>'.__('Rewrite mode').':</strong> '.($server_info['mod_rewrite'] ? '<span class="ok">OK</span>' : '<span class="failed">n/a</span> <a href="http://docs.wikkawiki.org/ModRewrite">'.__('learn more').'...</a>');
	echo '</li>'."\n".'</ul></li>';
}
echo '</ul></div>'."\n";
echo '<h3>'.__('Download this').'</h3>';
echo '<p>';
printf(__('You can optionally %1$sdownload%2$s your system configuration settings. This may be useful should you have any problem when upgrading or installing some add-ons'), '<a href="wikka.php?installAction=grabinfo">', '</a>');
echo '.</p>';

echo $req."\n";
if (($server_info['mod_rewrite']) && (!file_exists($htaccessLocation) || !is_writeable($htaccessLocation)))
{
	echo '<p class="failed"> ... '.sprintf(__('but you should give the web server write access to file %s'), '<tt>'.$htaccessLocation.'</tt>').'</p>';
}

//Next action
$_SESSION['server_info'] = $server_info;
?>
<br />

<?php
	if ($db_exists)
	{
?>
<input type="hidden" name="installAction" value="setup" />
<input type="submit" value="<?php echo _p('Start'); ?>" />
<?php
	}
	else
	{
?>
<input type='hidden' name='installAction' value='default' />
<input type='submit' value="<?php echo _p('Go back'); ?>" />
<?php
	}
?>
</form>
