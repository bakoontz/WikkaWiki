<?php
/**
 * Display a form to manage ACL for the current page.
 *
 * @package		Handlers
 * @name		ACL
 *
 * @uses		Config::$default_comment_post_acl
 * @uses		Config::$default_comment_read_acl
 * @uses		Config::$default_read_acl
 * @uses		Config::$default_write_acl
 * @uses		Config::$read_acl
 * @uses		Config::$write_acl
 * @uses		Wakka::Format()
 * @uses		Wakka::FormClose()
 * @uses		Wakka::FormOpen()
 * @uses		Wakka::GetPageOwner()
 * @uses		Wakka::GetPageTag()
 * @uses		Wakka::getSessionKey()
 * @uses		Wakka::hasValidSessionKey()
 * @uses		Wakka::htmlspecialchars_ent()
 * @uses		Wakka::Href()
 * @uses		Wakka::LoadSingle()
 * @uses		Wakka::LoadUsers()
 * @uses		Wakka::Redirect()
 * @uses		Wakka::SaveACL()
 * @uses		Wakka::SetPageOwner()
 * @uses		Wakka::TrimACLs()
 * @uses		Wakka::UserIsOwner()
 * @author		{@link http://wikkawiki.org/MinusF MinusF} (preliminary code cleanup, css selectors)
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli} (further cleanup)
 * @author		{@link http://wikkawiki.org/NilsLindenberg Nils Lindenberg} (i18n)
 * @since		Wikka 1.1.6.2
 *
 * @todo		- move main <div> to templating class
 */

echo '<div id="content">'."\n"; //TODO: move to templating class

// validate data source 
$keep_post_data = FALSE; 

if ($this->UserIsOwner())
{
	if (isset($_POST['form_id']))
	{

		if (FALSE != ($aKey = $this->getSessionKey($_POST['form_id'])))     # check if page key was stored in session 
		{ 
			if (TRUE == ($rc = $this->hasValidSessionKey($aKey)))   # check if correct name,key pair was passed 
			{ 
				$keep_post_data  = TRUE; 
			} 
        } 
        if(!$keep_post_data) unset($_POST); 
		
		// cancel action and return to the page
		if (isset($_POST['cancel']) && ($_POST['cancel'] == CANCEL_ACL_LABEL))
		{
			$this->Redirect($this->Href());
		}
		
		// change ACL(s) and/or username
		if (isset($_POST['store']) && ($_POST['store'] == STORE_ACL_LABEL))
		{
			$default_read_acl = $this->GetConfigValue('default_read_acl');
			$default_write_acl = $this->GetConfigValue('default_write_acl');
			$default_comment_read_acl = $this->GetConfigValue('default_comment_read_acl');
			$default_comment_post_acl = $this->GetConfigValue('default_comment_post_acl');
			$posted_read_acl = $_POST['read_acl'];
			$posted_write_acl = $_POST['write_acl'];
			$posted_comment_read_acl = $_POST['comment_read_acl'];
			$posted_comment_post_acl = $_POST['comment_post_acl'];
			$message = '';
	
			// store lists only if ACLs have previously been defined,
			// or if the posted values are different than the defaults
	
			$page = $this->LoadSingle('SELECT * FROM '.$this->config['table_prefix'].
			    "acls WHERE page_tag = '".mysql_real_escape_string($this->GetPageTag()).
			    "' LIMIT 1");
	
			if ($page ||
			    ($posted_read_acl	 != $default_read_acl	||
			     $posted_write_acl	 != $default_write_acl	||
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
	}
	else	// show form
	{
	echo $this->Format(sprintf(ACL_HEADING, '[['.$this->tag.']]').' --- ');
?>
<?php echo $this->FormOpen('acls') ?>
<table class="acls">
<tr>
	<td>
	<strong><?php echo READ_ACL_LABEL; ?></strong><br />
	<textarea name="read_acl" rows="4" cols="20"><?php echo $this->ACLs['read_acl'] ?></textarea>
	</td>

	<td>
	<strong><?php echo WRITE_ACL_LABEL; ?></strong><br />
	<textarea name="write_acl" rows="4" cols="20"><?php echo $this->ACLs['write_acl'] ?></textarea>
	</td>

	<td>
	<strong><?php echo ACLS_COMMENT_READ_LABEL; ?></strong><br />
	<textarea name="comment_read_acl" rows="4" cols="20"><?php echo $this->ACLs['comment_read_acl'] ?></textarea>
	</td>

	<td>
	<strong><?php echo ACLS_COMMENT_POST_LABEL; ?></strong><br />
	<textarea name="comment_post_acl" rows="4" cols="20"><?php echo $this->ACLs['comment_post_acl'] ?></textarea>
	</td>

</tr>

<tr>
	<td colspan="2">
	<br />
	<input type="submit" value="<?php echo STORE_ACL_LABEL?>" name="store" />
	<input type="submit" value="<?php echo CANCEL_ACL_LABEL?>" name="cancel" />
	</td>

	<td>
	<strong><?php echo SET_OWNER_LABEL; ?></strong><br />
	<select name="newowner">
	<option value="same"><?php echo $this->GetPageOwner().' '.SET_OWNER_CURRENT_LABEL ?></option>
	<option value="(Public)"><?php echo SET_OWNER_PUBLIC_LABEL; ?></option>
	<option value=""><?php echo SET_NO_OWNER_LABEL; ?></option>
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

<br />
<?php echo $this->Format(ACL_SYNTAX_HELP); ?>
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
