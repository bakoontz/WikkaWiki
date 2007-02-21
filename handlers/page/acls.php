<?php
/**
 * Display a form to manage ACL for the current page.
 *
 * @package		Handlers
 * @subpackage 	Page
 * @version		$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses Config::$default_comment_acl
 * @uses Config::$default_read_acl
 * @uses Config::$default_write_acl
 * @uses Wakka::htmlspecialchars_ent()
 *
 * @author		{@link http://wikkawiki.org/MinusF MinusF} (preliminary code cleanup, css selectors)
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli} (further cleanup)
 * @author		{@link http://wikkawiki.org/NilsLindenberg Nils Lindenberg} (i18n)
 *
 * @todo		move main <div> to templating class
 * @todo		show Cancel button only if JavaScript is available
 */

echo '<div class="page">'."\n"; //TODO: move to templating class

if ($this->UserIsOwner())
{
	if ($_POST)
	{
		$default_read_acl = $this->GetConfigValue('default_read_acl');
		$default_write_acl = $this->GetConfigValue('default_write_acl');
		$default_comment_acl = $this->GetConfigValue('default_comment_acl');
		$posted_read_acl = $_POST['read_acl'];
		$posted_write_acl = $_POST['write_acl'];
		$posted_comment_acl = $_POST['comment_acl'];
		$message = '';

		// store lists only if ACLs have previously been defined,
		// or if the posted values are different than the defaults

		$page = $this->LoadSingle('SELECT * FROM '.$this->config['table_prefix'].
		    "acls WHERE page_tag = '".mysql_real_escape_string($this->GetPageTag()).
		    "' LIMIT 1");

		if ($page ||
		    ($posted_read_acl != $default_read_acl||
		     $posted_write_acl != $default_write_acl||
		     $posted_comment_acl != $default_comment_acl))
		{
			$this->SaveACL($this->GetPageTag(), 'read', $this->TrimACLs($posted_read_acl));
			$this->SaveACL($this->GetPageTag(), 'write', $this->TrimACLs($posted_write_acl));
			$this->SaveACL($this->GetPageTag(), 'comment', $this->TrimACLs($posted_comment_acl));
			$message = ACLS_UPDATED;
		}

		// change owner?
		$newowner = $_POST['newowner'];

		if (($newowner != 'same') &&
		    ($this->GetPageOwner($this->GetPageTag()) != $newowner))
		{
			if ($newowner == '')
			{
				$newowner = NO_PAGE_OWNER;
			}

			$this->SetPageOwner($this->GetPageTag(), $newowner);
			$message .= sprintf(PAGE_OWNERSHIP_CHANGED, $newowner);
		}

		// redirect back to page
		$this->Redirect($this->Href(), $message);
	}
	else // show form
	{
?>
<?php echo $this->FormOpen('acls') ?>
<fieldset><legend><?php echo $this->Format(sprintf(ACLS_LEGEND, '[['.$this->tag.']]').' --- ');?></legend>
<table class="acls">
<tr>
	<td>
	<label for="read_acl"><strong><?php echo ACLS_READ_LABEL ?></strong></label><br />
	<textarea id="read_acl" name="read_acl" rows="4" cols="20"><?php echo preg_replace("/[\s,]+/", "\n", $this->ACLs['read_acl']) ?></textarea>
	</td>

	<td>
	<label for="write_acl"><strong><?php echo ACLS_WRITE_LABEL ?></strong></label><br />
	<textarea id="write_acl" name="write_acl" rows="4" cols="20"><?php echo preg_replace("/[\s,]+/", "\n", $this->ACLs['write_acl']) ?></textarea>
	</td>

	<td>
	<label for="comment_acl"><strong><?php echo ACLS_COMMENT_LABEL ?></strong></label><br />
	<textarea id="comment_acl" name="comment_acl" rows="4" cols="20"><?php echo preg_replace("/[\s,]+/", "\n", $this->ACLs['comment_acl']) ?></textarea>
	</td>
</tr>

<tr>
	<td colspan="2">
	<br />
	<input type="submit" value="<?php echo ACLS_STORE_BUTTON ?>" />
	<input type="button" value="<?php echo CANCEL_BUTTON ?>" onclick="history.back();" />
	</td>

	<td>
	<label for="newowner"><strong><?php echo SET_OWNER_LABEL ?></strong></label><br />
	<select id="newowner" name="newowner">
	<option value="same"><?php echo $this->GetPageOwner().' '.SET_OWNER_CURRENT_OPTION ?></option>
	<option value="(Public)"><?php echo SET_OWNER_PUBLIC_OPTION ?></option>
	<option value=""><?php echo SET_NO_OWNER_OPTION ?></option>
<?php
		if ($users = $this->LoadUsers())
		{
			foreach($users as $user)
			{
				echo "\t".'<option value="'.$this->htmlspecialchars_ent($user['name']).'">'.$user['name'].'</option>'."\n";
			}
		}
?>
	</select>
	</td>
</tr>
</table>
</fieldset>
<br />
<?php
	$acls_sample_wiki_name_escaped = '""'.WIKKA_SAMPLE_WIKINAME.'""';

	$acls_syntax_help  = '==='.ACLS_SYNTAX_HEADING.'===';
	$acls_syntax_help .= ' ---##*## = '.ACLS_EVERYONE;
	$acls_syntax_help .= ' ---##+## = '.ACLS_REGISTERED_USERS;
	$acls_syntax_help .= ' ---##'.$acls_sample_wiki_name_escaped.'## = '.sprintf(ACLS_LIST_USERNAMES,$acls_sample_wiki_name_escaped);
	$acls_syntax_help .= ' --- --- '.sprintf(ACLS_NEGATION,'##!##');
	$acls_syntax_help .= ' ---##!*## = '.ACLS_NONE_BUT_ADMINS;
	$acls_syntax_help .= ' ---##!+## = '.ACLS_ANON_ONLY;
	$acls_syntax_help .= ' ---##!'.$acls_sample_wiki_name_escaped.'## = '.sprintf(ACLS_DENY_USER_ACCESS,$acls_sample_wiki_name_escaped);
	$acls_syntax_help .= ' --- --- //'.ACLS_TESTING_ORDER1.'//';
	$acls_syntax_help .= ' --- '.sprintf(ACLS_TESTING_ORDER2,'##*##','//'.ACLS_AFTER.'//');

	echo $this->Format($acls_syntax_help);
?>
<?php
		print($this->FormClose());
	}
}
else
{
	echo '<em class="error">'.NOT_PAGE_OWNER.'</em>'."\n";
}
echo '</div>'."\n" //TODO: move to templating class
?>
