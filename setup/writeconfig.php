<?php
/**
 * Store the configuration in a file called wikka.config.php
 * 
 * @package	Setup
 * @version	$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 */

// remove config values for whatever reason
unset($config['allow_doublequote_html']);
$config['base_url'] = $url;

// set version to current version, yay!
$config['wakka_version'] = WAKKA_VERSION;
// initialize! Suppose rewrite_mode unavailable.
if (!isset($config['rewrite_mode']))
{
	$config['rewrite_mode'] = 0;
	if (($_SESSION['server_info']['mod_rewrite']) && (file_exists($htaccessLocation)) && (is_writeable($htaccessLocation)))
	{
		$config['rewrite_mode'] = 1;
	}
}

if ($wakkaConfig['wakka_version'])
{
	echo '<h1>'.__('Wikka Upgrade').' (5/5)</h1>'."\n";
}
else
{
	echo '<h1>'.__('Wikka Installation').' (5/5)</h1>'."\n";
}

// Writing .htaccess
// Flag registering status of attempt to update .htaccess
$htaccess_updated = 0;
if ($config['rewrite_mode'] == 1)
{
	$rewrite_base = preg_replace('+^\\S*://[^/]*(?=/)+', '', $config['base_url']);
	$htaccess_content = file($htaccessLocation);
	$new_htaccess_content = '';
	$on_rewrite_section = 0;
	foreach ($htaccess_content as $line)
	{
		if ((preg_match("/^(\\s*Rewrite(Engine|Base)|#---WIKKA-REWRITING-BEGIN)/i", $line)) && ($on_rewrite_section == 1))
		{
			$line = '';
		}
		if (preg_match("/^(\\s*)<IfModule\\s+mod_rewrite.c>/i", $line , $m))
		{
			$line .= '#---WIKKA-REWRITING-BEGIN'."\n";
			$line .= $m[1].' RewriteEngine On'."\n";
			$line .= $m[1].' RewriteBase '.$rewrite_base."\n";
			$on_rewrite_section = 1;
		}
		if (($on_rewrite_section == 1) && (preg_match("/^(\\s*)<\\/IfModule>/i", $line)))
		{
			$on_rewrite_section = 2;
		}
		$new_htaccess_content .= $line;
	}
	print('<h2>'.__('Updating .htaccess').'</h2>'."\n");
	test(__('Updating rewriting rules').'...', $on_rewrite_section, '', 0);
	if ($on_rewrite_section)
	{
		test(sprintf(__('Writing .htaccess file (%s)'), '<tt>'.$htaccessLocation.'</tt>').'...', $f1 = @fopen($htaccessLocation, "w"), "", 0);
		if ($f1)
		{
			fwrite($f1, $new_htaccess_content);
			fclose($f1);
			$htaccess_updated = 1;
		}
	}
}
$config['rewrite_mode'] = $htaccess_updated;
// convert config array into PHP code
$double_backslash = '\\\\';
$single_quote = '\'';
$configCode = "<?php\n// wikka.config.php written at ".strftime("%c")."\n// do not change wikka_version manually!\n\n\$wakkaConfig = array(\n";
foreach ($config as $k => $v)
{
	if ($k !== 0)
	{
		$entries[] = "\t'".$k."' => '".preg_replace('/['.$double_backslash.$single_quote.']/', $double_backslash.'$0', $v)."'"; // #5
	}
}
$server_info = "\t'server_info' => array(\n";
$i = 0;
foreach ($_SESSION['server_info'] as $k => $v)
{
	if ($i) $server_info .= ",\n";
	else $i++;
	$server_info .= "\t\t'$k' => '".preg_replace('/['.$double_backslash.$single_quote.']/', $double_backslash.'$0', $v)."'";
}
$server_info .= ")";
$configCode .= implode(",\n", $entries).");\n?>";

// try to write configuration file
print('<h2>'.__('Writing configuration').'</h2>'."\n");
test(sprintf(__('Writing configuration file %s'), '<tt>'.$wakkaConfigLocation.'</tt>...'), $fp = @fopen($wakkaConfigLocation, "w"), "", 0);

if ($fp)
{
	fwrite($fp, $configCode);
	// write
	fclose($fp);
	printf('<p>'.__('That\'s all! You can now %1$sreturn to your WikkaWiki site%2$s'), '<a href="'.$config['base_url'].'">', '</a>');
	echo '. '."\n";
	printf(__('However, you are advised to remove write access to %s again now that it\'s been written. Leaving the file writable can be a security risk'), '<tt>wikka.config.php</tt>');
	echo '!</p>';
}
else
{
	// complain
	print('<p><span class="failed">'.__('WARNING').':</span> ');
	printf(__('The file %1$s could not be written. You will need to give your web server temporary write access to either your wikka directory, or a blank file called %1$s'), '<tt>'.$wakkaConfigLocation.'</tt>');
	echo ' (<tt>touch wikka.config.php ; chmod 666 wikka.config.php</tt>) ; ';
	printf(__('don\'t forget to remove write access again later, ie %s'), '<tt>chmod 644 '.$wakkaConfigLocation.'</tt>');
	echo ".\n";
	printf(__('If, for any reason, you can\'t do this, you\'ll have to copy the text below into a new file and save/upload it as %1$s'), '<tt>'.$wakkaConfigLocation.'</tt>');
	printf(__('Once you\'ve done this, your Wikka site should work. If not, please visit %s'), '<a href="http://docs.wikkawiki.org/WikkaInstallation">Wikka:WikkaInstallation</a>');
	echo '.</p>'."\n";
	?>
	<form action="<?php echo $action_target; ?>" method="post">
	<input type="hidden" name="installAction" value="writeconfig" />
	<input type="submit" value="<?php echo _p('Try again'); ?>" />
	</form>	
	<?php
	print("<div><textarea readonly='readonly' style='width: 80%; height: 600px;'>".str_replace(array('&', '<'), array('&amp;', '&lt;'), $configCode)."</textarea></div>\n"); //TODO: make code block downloadable
}

?>
