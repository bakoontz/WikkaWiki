<?php

//constant section
define('USER_IS_NEW_OWNER_MESSAGE', 'You are now the owner of this page.');

// only claim ownership if this page has no owner, and if user is logged in.
if ($this->page && !$this->GetPageOwner() && $this->GetUser())
{
	$this->SetPageOwner($this->GetPageTag(), $this->GetUserName());
	$this->SetRedirectMessage(USER_IS_NEW_OWNER_MESSAGE);
}

$this->Redirect($this->Href());

?>