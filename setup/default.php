<?php
/**
 * Display a configuration form to set language and database settings.
 * 
 * @package	Setup
 * @version	$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses init_test_mod_rewrite()
 * @todo	make form accessible!
 */
?>
<form action="setup/test/test-mod-rewrite.php" name="form1" method="post">
<table>
	<?php
	if (isset($config['wakka_version']) && ($config['wakka_version']))
	{
		echo '	<tr><td>&nbsp;</td><td><h1>'.__('WikkaWiki Upgrade').' (1/5)</h1></td></tr>'."\n";
		echo '<tr><td>&nbsp;</td><td><p>'.sprintf(__('Welcome to the WikkaWiki Setup Wizard. Your installed WikkaWiki is reporting itself as %s'), '<tt>'.$config['wakka_version'].'</tt>').'</p><p>'.sprintf(__('You are about to %1$s WikkaWiki to version %2$s'), '<em>'.__('upgrade').'</em>', '<strong><tt>'.WAKKA_VERSION.'</tt></strong>').'. '.sprintf(__('Please refer to the %1$s for further instructions'), '<a href="http://docs.wikkawiki.org/UpgradeNotes" target="_blank">'.__('documentation').'</a>').'.</p></td></tr>'."\n";
	}
	else
	{
		echo '	<tr><td>&nbsp;</td><td><h1>'.__('WikkaWiki Installation').' (1/5)</h1></td></tr>'."\n";
		echo '<tr><td>&nbsp;</td><td><p>'.__('Welcome to the WikkaWiki Setup Wizard. Since there is no existing WikkaWiki configuration, this probably is a <em>fresh install</em>').'.</p><p>'.sprintf(__('You are about to install WikkaWiki (version %s). This wizard will guide you through the installation, which should take only a few minutes'), '<strong><tt>'.WAKKA_VERSION.'</tt></strong>').'. '.sprintf(__('Please refer to the %1$s for further instructions'), '<a href="http://docs.wikkawiki.org/WikkaInstallation" target="_blank">'.__('documentation').'</a>').'.</p></td></tr>'."\n";
	}

	$setupfiles_to_update = array(
		'./.htaccess', './wikka.config.php', 'setup/test/.htaccess');
	$setup_files_not_writable = '';
	// Need to adjust setup/test/.htaccess if installed in a subdir
	include_once('./inc/functions.inc.php');
	$htaccess = 'setup/test/.htaccess';
	if(setupfile_is_writable($htaccess))
	{
		$rewrite_base = preg_replace('|^\\S+://[^/]*(?=/)|', '', $url);	// remove scheme and domain
		$htaccess_content = file($htaccess);
		$new_htaccess_content = '';
		foreach($htaccess as $line)
		{
			if(preg_match("/^(\\s*RewriteBase/i", $line))
			{
				$line = "RewriteBase $rewrite_base/setup/test"; 
			}
			$new_htaccess_content .= $line;
		}
		print('<tr><td>&nbsp;</td><td class="note">'."\n");
		print('<h2>'.__('Setting up mod_rewrite autodetection').'</h2>'."\n");
		test(sprintf(__('Writing .htaccess file (%s)'), '<tt>'.$htaccess.'</tt>').'...', $f1 = @fopen($htaccess, "w"), "", 0);
		print('</td></tr>'."\n");
		if ($f1)
		{
			fwrite($f1, $new_htaccess_content);
			fclose($f1);
		}
	}

	foreach ($setupfiles_to_update as $f1)
	{
		if (!setupfile_is_writable($f1))
		{
			$setup_files_not_writable .= '<li><tt>'.$f1.'</tt></li>';
		}
	}
	if ($setup_files_not_writable)
	{
		printf('<tr><td>&nbsp;</td><td class="note"><p>'.__('The following files need to be written/updated by the installer: %s'.
		 'Please ensure that the file is writable.  <b>When fixed, reload this page using the Reload button in your browser.</b> ').
		 '</p></td></tr>', '</p><ul>'.$setup_files_not_writable.'</ul><p>');
	}
	init_test_mod_rewrite();
	// Language select
	if (!isset($config['default_lang']))
	{
		// use constant CONFIG_DEFAULT_LANGUAGE
		#$config['default_lang'] = 'en';
		$config['default_lang'] = CONFIG_DEFAULT_LANGUAGE;
	}
	?>
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td><td><h2><?php echo __('Language settings'); ?></h2></td></tr>
	<tr><td>&nbsp;</td><td><?php echo __('Please select a language for the default pages that Wikka will create:'); ?></td></tr>
	<tr><td><?php echo __('Choose a default language'); ?>:</td><td><?php Language_selectbox($config['default_lang']); // @@@ does not actually display any language choice ?></td></tr>
<?php 
	if (!isset($config['wakka_version']) || (!$config['wakka_version']))
	{
	?>
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td><td><h2><?php echo __('Database settings'); ?></h2></td></tr>
	<tr><td>&nbsp;</td><td><?php echo __('WikkaWiki uses a MySQL database to store data. The wizard will start by checking the connection to the database.');?></td></tr>
	<tr><td>&nbsp;</td><td><?php echo __('1. The host your MySQL server is running on. <span class="note">Usually <tt>localhost</tt> (i.e. the same machine your WikkaWiki site is on).</span>'); ?></td></tr>
	<tr><td align="right" nowrap="nowrap"><?php echo __('MySQL host');?>:</td><td><input type="text" size="50" name="pconfig[mysql_host]" value="<?php echo $config["mysql_host"] ?>" /></td></tr>
	<tr><td>&nbsp;</td><td><?php echo __('2. The name of the database WikkaWiki will use. <span class="note">Note that this database must already exist before you continue.</span>'); ?></td></tr>
	<tr><td align="right" nowrap="nowrap"><?php echo __('MySQL database'); ?>:</td><td><input type="text" size="50" name="pconfig[mysql_database]" value="<?php echo $config["mysql_database"] ?>" /></td></tr>
	<tr><td>&nbsp;</td><td><?php printf(__('3. Username and password to connect to your database. <span class="note">This user must exist and be granted access for <tt>%s</tt> operations to the database where WikkaWiki will be installed.</span>'), 'SELECT, INSERT, UPDATE, DELETE, ALTER TABLE'); ?></td></tr>
	<tr><td align="right" nowrap="nowrap"><?php echo __('MySQL username'); ?>:</td><td><input type="text" size="50" name="pconfig[mysql_user]" value="<?php echo $config["mysql_user"] ?>" /></td></tr>
	<tr><td align="right" nowrap="nowrap"><?php echo __('MySQL password'); ?>:</td><td><input type="password" size="50" name="pconfig[mysql_password]" value="" /></td></tr>
<?php
	}
?>
	<tr><td>&nbsp;</td><td>
	<input type="hidden" name="installAction" value="check" />
	<input type="submit" value="<?php echo _p('Continue');?>" />
	<input type="hidden" name="redirect_to" value="<?php echo $action_target; ?>" />
	</td></tr>
</table>
</form>
