<?php
/**
 * Display a form to manage ACL for the current page.
 *
 * @package		Handlers
 * @subpackage	Page
 * @name		ACLs
 * @version		$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	Config::$default_comment_read_acl
 * @uses	Config::$default_comment_write_acl
 * @uses	Config::$default_read_acl
 * @uses	Config::$default_write_acl
 * @uses	Wakka::htmlspecialchars_ent()
 *
 * @author	{@link http://wikkawiki.org/MinusF MinusF} (preliminary code cleanup, css selectors)
 * @author	{@link http://wikkawiki.org/DarTar Dario Taraborelli} (further cleanup)
 * @author	{@link http://wikkawiki.org/NilsLindenberg Nils Lindenberg} (i18n)
 * @since	Wikka 1.0.0
 *
 * @todo	show Cancel button only if JavaScript is available	#35
 * @todo	do NOT use whitespace as ACL delimiter see #226 comment:8
 * @todo	produce ACLs syntax help directly as HTML, outside the form, using
 *			constants for the the ACL symbols (and implement these symbols as
 *			constants elsewhere! #539)
 * @todo	generate ACLs reading/loading/handling from list of known ACLs
 *			(see @todos on ALC-related methods); note that current table layout
 *			for the ACLs form may have to be dropped as we add more ACLs.
 */

if ($this->UserIsOwner())
{
	if ($_POST)
	{

        if(isset($_POST['cancel']) && ($_POST['cancel'] == CANCEL_BUTTON))
        {
            $this->Redirect($this->Href());
        }

		$default_read_acl = $this->GetConfigValue('default_read_acl');
		$default_write_acl = $this->GetConfigValue('default_write_acl');
		$default_comment_read_acl = $this->GetConfigValue('default_comment_read_acl');
		$default_comment_post_acl = $this->GetConfigValue('default_comment_post_acl');
		$posted_read_acl = $_POST['read_acl'];
		$posted_write_acl = $_POST['write_acl'];
		$posted_comment_read_acl = $_POST['comment_read_acl'];
		$posted_comment_post_acl = $_POST['comment_post_acl'];
		$message = '';

		if(empty($posted_read_acl))
		{
			$posted_read_acl = $default_read_acl;
		}
		if(empty($posted_write_acl))
		{
			$posted_write_acl = $default_write_acl;
		}
		if(empty($posted_comment_read_acl))
		{
			$posted_comment_read_acl = $default_comment_read_acl;
		}
		if(empty($posted_comment_post_acl))
		{
			$posted_comment_post_acl = $default_comment_post_acl;
		}

		// store lists only if ACLs have previously been defined,
		// or if the posted values are different than the defaults

		$page = $this->LoadSingle("
			SELECT *
			FROM ".$this->GetConfigValue('table_prefix')."acls
			WHERE page_tag = '".mysql_real_escape_string($this->GetPageTag())."'
			LIMIT 1"
			);

		if ($page ||
			($posted_read_acl != $default_read_acl ||
			 $posted_write_acl != $default_write_acl ||
			 $posted_comment_read_acl != $default_comment_read_acl ||
			 $posted_comment_post_acl != $default_comment_post_acl))
		{
			$this->SaveACL($this->GetPageTag(), 'read', $this->TrimACLs($posted_read_acl));
			$this->SaveACL($this->GetPageTag(), 'write', $this->TrimACLs($posted_write_acl));
			$this->SaveACL($this->GetPageTag(), 'comment_read', $this->TrimACLs($posted_comment_read_acl));
			$this->SaveACL($this->GetPageTag(), 'comment_post', $this->TrimACLs($posted_comment_post_acl));
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
		echo '<div class="page">'."\n";
		echo $this->FormOpen('acls')
?>
<fieldset><legend><?php echo $this->Format(sprintf(ACLS_LEGEND, '[['.$this->tag.']]').' --- ');?></legend>
<table class="acls">
<tr>
	<td>
	<label for="read_acl"><strong><?php echo ACLS_READ_LABEL ?></strong></label><br />
	<textarea id="read_acl" name="read_acl" rows="4" cols="20"><?php echo preg_replace("/[|,]+/", "\n", $this->ACLs['read_acl']) ?></textarea>
	</td>

	<td>
	<label for="write_acl"><strong><?php echo ACLS_WRITE_LABEL ?></strong></label><br />
	<textarea id="write_acl" name="write_acl" rows="4" cols="20"><?php echo preg_replace("/[|,]+/", "\n", $this->ACLs['write_acl']) ?></textarea>
	</td>

	<td>
	<label for="comment_read_acl"><strong><?php echo ACLS_COMMENT_READ_LABEL ?></strong></label><br />
	<textarea id="comment_read_acl" name="comment_read_acl" rows="4" cols="20"><?php echo preg_replace("/[|,]+/", "\n", $this->ACLs['comment_read_acl']) ?></textarea>
	</td>

	<td>
	<label for="comment_post_acl"><strong><?php echo ACLS_COMMENT_POST_LABEL ?></strong></label><br />
	<textarea id="comment_post_acl" name="comment_post_acl" rows="4" cols="20"><?php echo preg_replace("/[|,]+/", "\n", $this->ACLs['comment_post_acl']) ?></textarea>
	</td>

</tr>
<tr>
	<td colspan="2">
	<br />
	<input type="submit" value="<?php echo ACLS_STORE_BUTTON ?>" />
	<input type="submit" value="<?php echo CANCEL_BUTTON ?>" name="cancel"/>
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
				echo '	<option value="'.$this->htmlspecialchars_ent($user['name']).'">'.$user['name'].'</option>'."\n";
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
		$acls_syntax_help .= ' --- --- '.sprintf(ACLS_DEFAULT_ACLS, '##wikka.config.php##');

		echo $this->Format($acls_syntax_help);
		echo $this->FormClose();
		echo '</div>'."\n";
	}
}
else
{
	echo '<div class="page">'."\n";
	echo '	<em class="error">'.NOT_PAGE_OWNER.'</em>'."\n";
	echo '</div>'."\n";
}
?>
