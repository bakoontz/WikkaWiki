<?php
/**
 * Display a configuration form to install or upgrade Wikka.
 * 
 * @package	Setup
 * @version	$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 */

$wikiname = sprintf('<abbr title="%1$s">%2$s</abbr>', _p('A WikiName is formed by two or more capitalized words without space, e.g. JohnDoe'), __('WikiName'));

if (!$wakkaConfig["wakka_version"])
{
?>
<script type="text/javascript">
function check() {
 var f = document.forms.form1;
 var re;
 var err = '';
 re = new RegExp("^[A-Z][a-z]+[A-Z0-9][A-Za-z0-9]*$");
 if (f.elements["config[admin_users]"].value.search(re)==-1) 
 {
  err += "Admin name must be a WikiName. This means it must start with a capital letter, then have some lowercase letters, then have an uppercase letter.\nExamples: JohnSmith or JsnX\n\n";
 }
	if (5 > f.elements["password"].value.length) 
 {
  err += "Password is too short.  It must be at least five (5) characters long.\n\n";
 }
 if (f.elements["password"].value!=f.elements["password2"].value) 
 {
  err += "Passwords don't match.\n\n";
 }
 re = new RegExp("[a-z]+@[a-z]+\.[a-z]+", "i");
 if (f.elements["config[admin_email]"].value.search(re)==-1) 
 {
  err += "Email address appears incorrect.\n\n";
 }
 if (err)
 {
 	alert(err);
 	return (false);
 }
 return true;
}
</script>
<?php 
} 
?>
<form action="<?php echo $action_target; ?>" name="form1" method="post"<?php if (!$wakkaConfig['wakka_version']) echo ' onsubmit="return check();"';?>>
<table>
<?php
if ($wakkaConfig["wakka_version"])
{
	echo '	<tr><td>&nbsp;</td><td><h1>'.__('Wikka Upgrade').' (3/5)</h1></td></tr>'."\n";
	echo ' <tr><td>&nbsp;</td><td>'.__('Please review your configuration settings below').'.</td></tr>'."\n";
}
else
{
	echo '	<tr><td>&nbsp;</td><td><h1>'.__('Wikka Installation').' (3/5)</h1></td></tr>'."\n";
	echo ' <tr><td>&nbsp;</td><td>'.__('To start the installation, please fill in the form below').'.</td></tr>'."\n";
}
// Try to create .htaccess and wikka.config.php
if (!file_exists($wakkaConfigLocation))
{
	touch($wakkaConfigLocation);
}

if (!file_exists($wakkaConfigLocation) || !is_writeable($wakkaConfigLocation))
{
	echo ' <tr><td>&nbsp;</td><td><span class="note">'.sprintf(__('NOTE: This installer will try to write the configuration data to a file called %1$s, located in %2$s'), '<tt>'.basename($wakkaConfigLocation).'</tt>', '<tt>['.dirname($wakkaConfigLocation).']</tt>').'. ';
	echo __('In order for this to work, you must make sure the web server has write access to that file! If you can\'t do this, you will have to edit the file manually (the installer will tell you how)').'.</span></td></tr>'."\n";
}

//@@@DEBUG
//echo 'WAKKA: '.$_GET['wakka'].'<br />';

#$wakkaConfig['base_url'] = $url;

//@@@DEBUG
//echo 'BASE_URL: <tt>'.$wakkaConfig['base_url'].'</tt>';

/*
	<tr><td>&nbsp;</td><td><br /><h2>Default ACL Configuration</h2></td></tr>
	<tr><td>&nbsp;</td><td>You can select one of the following preselected configurations</td></tr>
	<tr><td>&nbsp;</td><td>
	<label><input type="radio" name="config[acl]" checked="checked" value='0'/>private wiki for personal use</label><br />
	<label><input type="radio" name="config[acl]" value='1' />personal wiki with no comments</label><br />
	<label><input type="radio" name="config[acl]" value='2' />personal wiki with comments from registered users only</label><br />
	<label><input type="radio" name="config[acl]" value='3' />personal wiki with comments from any user</label><br />
	<label><input type="radio" name="config[acl]" value='4' />public wiki with contributions and comments from registered users only</label><br />
	<label><input type="radio" name="config[acl]" value='5' />public wiki with contributions from registered users only and </label><br />
	</td></tr>
	<tr><td>&nbsp;</td><td>&nbsp;</td></tr>	
	<tr><td>&nbsp;</td><td><label><input type="radio" name="config[acl]" value='6' />Alternatively, you can select any of the following configurations</label></td></tr>
*/
 // Showing select boxes for choosing ACL options
	ACL_show_selectbox('read');
	ACL_show_selectbox('write');
	ACL_show_selectbox('comment_read');
	ACL_show_selectbox('comment_post');
	
	if (!$wakkaConfig["wakka_version"])
 	{
	?>
	<tr><td>&nbsp;</td><td><br /><h2><?php echo __('Database Configuration'); ?></h2></td></tr>
	<tr><td>&nbsp;</td><td><?php echo __('Prefix of all tables used by Wikka. This allows you to run multiple Wikka installations using the same MySQL database by configuring them to use different table prefixes'); ?>.</td></tr>
	<tr><td align="right" nowrap="nowrap"><?php echo __('Table prefix'); ?>:</td><td><input type="text" size="50" name="config[table_prefix]" value="<?php echo $config["table_prefix"] ?>" /></td></tr>
	<?php
	 }
	?>
	<tr><td>&nbsp;</td><td><br /><h2><?php echo __('Wikka Site Configuration'); ?></h2></td></tr>
	<tr><td>&nbsp;</td><td><?php echo __('The name of your Wikka site, as it will be displayed in the title'); ?>.</td></tr>
	<tr><td align="right" nowrap="nowrap"><?php echo __('Your Wikka site\'s name'); ?>:</td><td><input type="text" size="50" name="config[wakka_name]" value="<?php echo $config["wakka_name"] ?>" /></td></tr>

	<tr><td>&nbsp;</td><td><?php echo __('Your Wikka site\'s home page').'. '.sprintf(__('Should be formatted as a %s'), $wikiname); ?>.</td></tr>
	<tr><td align="right" nowrap="nowrap"><?php echo __('Home page'); ?>:</td><td><input type="text" size="50" name="config[root_page]" value="<?php echo $config["root_page"] ?>" /></td></tr>

	<tr><td>&nbsp;</td><td><?php echo __('Suffix used for cookies and part of the session name. This allows you to run multiple Wikka installations on the same server by configuring them to use different wiki prefixes.'); ?></td></tr>
	<tr><td align="right" nowrap="nowrap"><?php echo __('Your Wiki suffix:'); ?></td><td><input type="text" size="50" name="config[wiki_suffix]" value="<?php echo $config["wiki_suffix"] ?>" /></td></tr>

	<tr><td>&nbsp;</td><td><?php echo __('META Keywords/Description that get inserted into the HTML headers'); ?>.</td></tr>
	<tr><td align="right" nowrap="nowrap"><?php echo __('Meta Keywords'); ?>:</td><td><input type="text" size="50" name="config[meta_keywords]" value="<?php echo $config["meta_keywords"] ?>" /></td></tr>
	<tr><td align="right" nowrap="nowrap"><?php echo __('Meta Description'); ?>:</td><td><input type="text" size="50" name="config[meta_description]" value="<?php echo $config["meta_description"] ?>" /></td></tr>

	<?php
	if (!$wakkaConfig["wakka_version"])
	{
	?>
	 <tr><td>&nbsp;</td><td><br /><h2><?php echo __('Admin Account Configuration'); ?></h2></td></tr>

		<tr><td>&nbsp;</td><td><?php printf(__('This is the username of the person running this wiki. Later you\'ll be able to add other admins. The admin username should be formatted as a %s'), $wikiname); ?>.</td></tr>
		<tr><td align="right" nowrap="nowrap"><?php echo __('Admin name'); ?>:</td><td><input type="text" size="50" name="config[admin_users]" value="<?php echo $config["admin_users"] ?>" /></td></tr>

		<tr><td>&nbsp;</td><td><?php echo __('Choose a password for administrator (5+ chars)'); ?></td></tr>
		<tr><td align="right" nowrap="nowrap"><?php echo __('Enter password'); ?>:</td><td><input type="password" size="50" name="password" value="" /></td></tr>
		<tr><td align="right" nowrap="nowrap"><?php echo __('Confirm password'); ?>:</td><td><input type="password" size="50" name="password2" value="" /></td></tr>
		<tr><td>&nbsp;</td><td><?php echo __('Administrator email'); ?>.</td></tr>
		<tr><td align="right" nowrap="nowrap"><?php echo __('Email'); ?>:</td><td><input type="text" size="50" name="config[admin_email]" value="<?php echo $config["admin_email"] ?>" /></td></tr>

<?php			
	}
?>
 <tr><td>&nbsp;</td><td>
 <input type="hidden" name="installAction" value="install" />
 <input type="submit" value="<?php echo _p('Continue'); ?>"/>
 </td></tr>
</table>
</form>
