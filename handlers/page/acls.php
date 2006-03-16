<div class="page">
<?php

// constant section
define('ACLS_UPDATED', 'Access control lists updated.');
define('NO_PAGE_OWNER', '(Nobody)');
define('PAGE_OWNERSHIP_CHANGED', "Ownership changed to %s");  // %s - name of the new owner
define('NOT_PAGE_OWNER', "You're not the owner of this page.");
define('ACL_CHANGE_FORM_HEADER', "Access Control Lists for %s"); // %s - name of the page
define('READ_ACL_LABEL', 'Read ACL:');
define('WRITE_ACL_LABEL', 'Write ACL:');
define('COMMENT_ACL_LABEL', 'Comments ACL:');
define('SET_PAGE_OWNER_LABEL', 'Set Page Owner:');
define('SET_PAGE_OWNER_CURRENT_LABEL', '(Current Owner)');
define('SET_PAGE_OWNER_PUBLIC_LABEL','(Public)');
define('SET_NO_PAGE_OWNER_LABEL', '(Nobody - Set free)');
define('ACL_SYNTAX_HELP', '===Syntax:=== ---* = Everyone ---+ = Registered users ---Or enter individual user ""WikiNames"", one per line ---""--------------------------------------"" ---Note: Any of these items can be negated with a ! ---!* = No one ---!+ = Anonymous users ---!""JohnDoe"" = ""JohnDoe"" will be denied access. --- --- //Be aware that the ACLs are tested in the order specified.// ---So be sure to specify * on a separate line ---**after** negating any users--not before.--- Otherwise, the * everyone condition will always give access ---before the list of users that should not have access is tested.'); // gets wiki-formatted

if ($this->UserIsOwner())
{
	if ($_POST)
	{

		$default_read_acl = $this->GetConfigValue("default_read_acl");
		$default_write_acl = $this->GetConfigValue("default_write_acl");
		$default_comment_acl = $this->GetConfigValue("default_comment_acl");
		$posted_read_acl = $_POST["read_acl"];
		$posted_write_acl = $_POST["write_acl"];
		$posted_comment_acl = $_POST["comment_acl"];
		$message = "";

		// store lists only if ACLs have previously been defined,
		// or if the posted values are different than the defaults

		$page = $this->LoadSingle("select * from ".$this->config["table_prefix"]."acls where page_tag = '".mysql_real_escape_string($this->GetPageTag())."' limit 1");
		if ($page || ($posted_read_acl != $default_read_acl || $posted_write_acl != $default_write_acl || $posted_comment_acl != $default_comment_acl ))
		{
			$this->SaveACL($this->GetPageTag(), "read", $this->TrimACLs($posted_read_acl));
			$this->SaveACL($this->GetPageTag(), "write", $this->TrimACLs($posted_write_acl));
			$this->SaveACL($this->GetPageTag(), "comment", $this->TrimACLs($posted_comment_acl));
			$message = ACLS_UPDATED;
		}

		// change owner?
		$newowner = $_POST["newowner"];
		if (($newowner <> "same") and ($this->GetPageOwner($this->GetPageTag()) <> $newowner))
		{
			if ($newowner == "") $newowner = NO_PAGE_OWNER;
			$this->SetPageOwner($this->GetPageTag(), $newowner);
			$message .= sprintf(PAGE_OWNERSHIP_CHANGED, $newowner);
		}

		// redirect back to page
		$this->Redirect($this->Href(), $message);
	}
	else
	{
		// show form
		?>
		<h3> <?php printf(ACL_CHANGE_FORM_HEADER, $this->Link($this->GetPageTag())); ?></h3>
		<br />

		<?php echo $this->FormOpen("acls") ?>
		<table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td valign="top" style="padding-right: 20px">
					<strong><?php echo READ_ACL_LABEL ?></strong><br />
					<textarea name="read_acl" rows="4" cols="20"><?php echo $this->ACLs["read_acl"] ?></textarea>
				<td>
				<td valign="top" style="padding-right: 20px">
					<strong><?php echo WRITE_ACL_LABEL ?></strong><br />
					<textarea name="write_acl" rows="4" cols="20"><?php echo $this->ACLs["write_acl"] ?></textarea>
				<td>
				<td valign="top" style="padding-right: 20px">
					<strong><?php echo COMMENT_ACL_LABEL ?></strong><br />
					<textarea name="comment_acl" rows="4" cols="20"><?php echo $this->ACLs["comment_acl"] ?></textarea>
				<td>
			</tr>
			<tr>
				<td colspan="3">
					<br />
					<input type="submit" value="Store ACLs" style="width: 120px" accesskey="s" />
					<input type="button" value="Cancel" onclick="history.back();" style="width: 120px" />
				</td>
				<td colspan="3">
					<strong><?php echo SET_PAGE_OWNER_LABEL; ?></strong><br />
					<select name="newowner">
						<option value="same"><?php echo $this->GetPageOwner().' '.SET_PAGE_OWNER_CURRENT_LABEL; ?></option>
						<option value="(Public)"><?php echo SET_PAGE_OWNER_PUBLIC_LABEL; ?></option>
						<option value=""><?php echo SET_NO_PAGE_OWNER_LABEL; ?></option>
						<?php
						if ($users = $this->LoadUsers())
						{
							foreach($users as $user)
							{
								print("<option value=\"".$this->htmlspecialchars_ent($user["name"])."\">".$user["name"]."</option>\n");
							}
						}
						?>
					</select>
				<td>
			</tr>
			<tr>
				<td colspan="3">
				<br /><?php echo $this->Format(ACL_SYNTAX_HELP); ?>
				<td>
			</tr>
		</table>
		<?php
		print($this->FormClose());
	}
}
else
{
	print('<em>'.NOT_PAGE_OWNER.'</em>');
}

?>
</div>