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

		if (FALSE != ($aKey = $this->getSessionKey($this->GetSafeVar('form_id', 'post'))))     # check if page key was stored in session 
		{ 
			if (TRUE == ($rc = $this->hasValidSessionKey($aKey)))   # check if correct name,key pair was passed 
			{ 
				$keep_post_data  = TRUE; 
			} 
        } 
        if(!$keep_post_data) unset($_POST); 
		
		// cancel action and return to the page
		if (isset($_POST['cancel']) && ($_POST['cancel'] == T_("Cancel")))
		{
			$this->Redirect($this->Href());
		}
		
		// change ACL(s) and/or username
		if ($this->GetSafeVar('store', 'post') == T_("Store ACLs"))
		{
			$default_read_acl = $this->GetConfigValue('default_read_acl');
			$default_write_acl = $this->GetConfigValue('default_write_acl');
			$default_comment_read_acl = $this->GetConfigValue('default_comment_read_acl');
			$default_comment_post_acl = $this->GetConfigValue('default_comment_post_acl');
			$posted_read_acl = $this->GetSafeVar('read_acl', 'post');
			$posted_write_acl = $this->GetSafeVar('write_acl', 'post');
			$posted_comment_read_acl = $this->GetSafeVar('comment_read_acl', 'post');
			$posted_comment_post_acl = $this->GetSafeVar('comment_post_acl', 'post');
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
				$message = T_("Access control lists updated.");
			}
	
			// change owner?
			$newowner = $this->GetSafeVar('newowner', 'post');
	
			if (($newowner != 'same') &&
			    ($this->GetPageOwner($this->GetPageTag()) != $newowner))
			{
				if ($newowner == '')
				{
					$newowner = T_("'(Nobody");
				}
	
				$this->SetPageOwner($this->GetPageTag(), $newowner);
				$message .= sprintf(T_("Ownership changed to %s"), $newowner);
			}
	
			// redirect back to page
			$this->Redirect($this->Href(), $message);
		}
	}
	else	// show form
	{
	echo $this->Format(sprintf(T_("====Access Control Lists for %s===="), '[['.$this->tag.']]').' --- ');
?>
<?php echo $this->FormOpen('acls') ?>
<table class="acls">
<tr>
	<td>
	<strong><?php echo T_("Read ACL:"); ?></strong><br />
	<textarea id="acls" name="read_acl" rows="4" cols="20"><?php echo $this->ACLs['read_acl'] ?></textarea>
	</td>

	<td>
	<strong><?php echo T_("Write ACL:"); ?></strong><br />
	<textarea id="acls" name="write_acl" rows="4" cols="20"><?php echo $this->ACLs['write_acl'] ?></textarea>
	</td>

	<td>
	<strong><?php echo T_("Comment Read ACL:"); ?></strong><br />
	<textarea id="acls" name="comment_read_acl" rows="4" cols="20"><?php echo $this->ACLs['comment_read_acl'] ?></textarea>
	</td>

	<td>
	<strong><?php echo T_("Comment Post ACL:"); ?></strong><br />
	<textarea id="acls" name="comment_post_acl" rows="4" cols="20"><?php echo $this->ACLs['comment_post_acl'] ?></textarea>
	</td>

</tr>

<tr>
	<td colspan="2">
	<br />
	<input type="submit" value="<?php echo T_("Store ACLs")?>" name="store" />
	<input type="submit" value="<?php echo T_("Cancel")?>" name="cancel" />
	</td>

	<td>
	<strong><?php echo T_("Set Page Owner:"); ?></strong><br />
	<select name="newowner">
	<option value="same"><?php echo $this->GetPageOwner().' '.T_("(Current Owner)") ?></option>
	<option value="(Public)"><?php echo T_("(Public)"); ?></option>
	<option value=""><?php echo T_("(Nobody - Set free)"); ?></option>
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
<?php echo $this->Format(T_("'===Syntax:=== ---##*## = Everyone ---##+## = Registered users ---##\"JohnDoe\"## = the user called \"JohnDoe\", enter as many users as you want, one per line --- --- Any of these items can be negated with a ##!##: ---##!*## = No one (except admins")); ?>
<?php
		print($this->FormClose());
	}
}
else
{
	echo '<em class="error">'.T_("You are not the owner of this page.").'</em>'."\n";
}
echo '</div>'."\n" //TODO: move to templating class
?>
