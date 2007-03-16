<?php
/**
 * Display a configuration form to set default ACL.
 * 
 * @package	Setup
 * @version	$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 */
?>
<div style='display:none;'>
<script type='text/javascript'>
</script>
</div>
<form action="<?php echo $action_target; ?>" name="form1" method="post">
<table>
	<?php
	if (isset($wakkaConfig['wakka_version']) && ($wakkaConfig['wakka_version']))
	{
		echo '	<tr><td>&nbsp;</td><td><h1>'.__('WikkaWiki Upgrade').' (1/5)</h1></td></tr>'."\n";
		echo '<tr><td>&nbsp;</td><td><p>'.sprintf(__('Welcome to the WikkaWiki Setup Wizard. Your installed WikkaWiki is reporting itself as %s'), '<tt>'.$wakkaConfig['wakka_version'].'</tt>').'</p><p>'.sprintf(__('You are about to %1$s WikkaWiki to version %2$s'), '<em>'.__('upgrade').'</em>', '<strong><tt>'.WAKKA_VERSION.'</tt></strong>').'. '.sprintf(__('Please refer to the %1$s for further instructions'), '<a href="http://docs.wikkawiki.org/UpgradeNotes" target="_blank">'.__('documentation').'</a>').'.</p></td></tr>'."\n";
	}
	else
	{
		echo '	<tr><td>&nbsp;</td><td><h1>'.__('WikkaWiki Installation').' (1/5)</h1></td></tr>'."\n";
		echo '<tr><td>&nbsp;</td><td><p>'.__('Welcome to the WikkaWiki Setup Wizard. Since there is no existing WikkaWiki configuration, this probably is a <em>fresh install</em>').'.</p><p>'.sprintf(__('You are about to install WikkaWiki (version %s). This wizard will guide you through the installation, which should take only a few minutes'), '<strong><tt>'.WAKKA_VERSION.'</tt></strong>').'. '.sprintf(__('Please refer to the %1$s for further instructions'), '<a href="http://docs.wikkawiki.org/WikkaInstallation" target="_blank">'.__('documentation').'</a>').'.</p></td></tr>'."\n";
		echo '<tr><td>&nbsp;</td><td>'.__('To start the installation, please fill in the form below').'.</td></tr>'."\n";
	}

	?>
<?php 
	if (!isset($wakkaConfig['wakka_version']) || (!$wakkaConfig['wakka_version']))
	{
	?>
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td><td><h2><?php echo __('Database settings'); ?></h2></td></tr>
	<tr><td>&nbsp;</td><td><?php echo __('WikkaWiki uses a MySQL database to store datas. Please fill in MySQL settings below');?> :</td></tr>
	<tr><td>&nbsp;</td><td><?php echo __('The host your MySQL server is running on. Usually "localhost" (ie, the same machine your WikkaWiki site is on)'); ?>.</td></tr>
	<tr><td align="right" nowrap><?php echo __('MySQL host');?>:</td><td><input type="text" size="50" name="config[mysql_host]" value="<?php echo $wakkaConfig["mysql_host"] ?>" /></td></tr>
	<tr><td>&nbsp;</td><td><?php echo __('The MySQL database WikkaWiki should use. This database needs to exist already before you continue'); ?>!</td></tr>
	<tr><td align="right" nowrap><?php echo __('MySQL database'); ?>:</td><td><input type="text" size="50" name="config[mysql_database]" value="<?php echo $wakkaConfig["mysql_database"] ?>" /></td></tr>
	<tr><td>&nbsp;</td><td><?php printf(__('Name and password of the MySQL user used to connect to your database. This user/pass needs also to exist and to be valid. It should be granted access for %s operations to the database you\'ve created for WikkaWiki'), '&laquo;select/insert/update/delete/alter table&raquo;'); ?>.</td></tr>
	<tr><td align="right" nowrap><?php echo __('MySQL user name'); ?>:</td><td><input type="text" size="50" name="config[mysql_user]" value="<?php echo $wakkaConfig["mysql_user"] ?>" /></td></tr>
	<tr><td align="right" nowrap><?php echo __('MySQL password'); ?>:</td><td><input type="password" size="50" name="config[mysql_password]" value="<?php echo $wakkaConfig["mysql_password"] ?>" /></td></tr>
<?php
	}
?>
	<tr><td>&nbsp;</td><td>
	<input type="hidden" name="installAction" value="check" />
	<input type="submit" value="<?php echo _p('Continue');?>" />
	</td></tr>
</table>
</form>
