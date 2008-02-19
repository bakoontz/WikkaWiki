<div class="page">
<?php


if ($this->UserIsOwner())
{
	if ($_POST)
	{
		// store lists
		$this->SaveAcl($this->GetPageTag(), "read", $_POST["read_acl"]);
		$this->SaveAcl($this->GetPageTag(), "write", $_POST["write_acl"]);
		$this->SaveAcl($this->GetPageTag(), "comment", $_POST["comment_acl"]);
		$message = "Access control lists updated";
		
		// change owner?
		$newowner = $_POST["newowner"];
		if (($newowner <> "same") and ($this->GetPageOwner($this->GetPageTag()) <> $newowner))
		// if ($newowner = $_POST["newowner"])
		{
			$this->SetPageOwner($this->GetPageTag(), $newowner);
			if ($newowner == "") { $newowner = "Nobody"; }
			$message .= " and gave ownership to ".$newowner;
		}

		// redirect back to page
		$this->SetMessage($message."!");
		$this->Redirect($this->Href());
	}
	else
	{
		// load acls
		$readACL = $this->LoadAcl($this->GetPageTag(), "read");
		$writeACL = $this->LoadAcl($this->GetPageTag(), "write");
		$commentACL = $this->LoadAcl($this->GetPageTag(), "comment");

		// show form
		?>
		<h3>Access Control Lists for <?php echo $this->Link($this->GetPageTag()) ?></h3>
		<br />
		
		<?php echo $this->FormOpen("acls") ?>
		<table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td valign="top" style="padding-right: 20px">
					<strong>Read ACL:</strong><br />
					<textarea name="read_acl" rows="4" cols="20"><?php echo $readACL["list"] ?></textarea>
				<td>
				<td valign="top" style="padding-right: 20px">
					<strong>Write ACL:</strong><br />
					<textarea name="write_acl" rows="4" cols="20"><?php echo $writeACL["list"] ?></textarea>
				<td>
				<td valign="top" style="padding-right: 20px">
					<strong>Comments ACL:</strong><br />
					<textarea name="comment_acl" rows="4" cols="20"><?php echo $commentACL["list"] ?></textarea>
				<td>
			</tr>
			<tr>
				<td colspan="3">
				Syntax:<br />
					* = Everyone<br />
					+ = Only registered users<br />
					Or enter individual user WikiNames<br />
				<td>
				<td colspan="3">
					<strong>Set Owner:</strong><br />
					<select name="newowner">
						<option value="same">Don't change</option>
						<option value="">(Nobody)</option>
						<?php
						if ($users = $this->LoadUsers())
						{
							foreach($users as $user)
							{
								print("<option value=\"".htmlspecialchars($user["name"])."\">".$user["name"]."</option>\n");
							}
						}
						?>
					</select>
				<td>
			</tr>
			<tr>
				<td colspan="3">
					<br />
					<input type="submit" value="Store ACLs" style="width: 120px" accesskey="s" />
					<input type="button" value="Cancel" onClick="history.back();" style="width: 120px" />
				</td>
			</tr>
		</table>
		<?php
		print($this->FormClose());
	}
}
else
{
	print("<em>You're not the owner of this page.</em>");
}

?>
</div>