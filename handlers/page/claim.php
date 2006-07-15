<?php
/**
 * Claim the ownership of a page if has no owner, and if user is logged in.
 * 
 * @package		Handlers
 * @subpackage	Page 
 * @version		$Id$
 * 
 * @uses	Wakka::GetPageOwner()
 * @uses	Wakka::GetPageTag()
 * @uses	Wakka::GetUser()
 * @uses	Wakka::GetUserName()
 * @uses	Wakka::Href()
 * @uses	Wakka::SetRedirectMessager()
 * @uses	Wakka::Redirect()
 * @filesource
 */

/**
 * i18n
 */
if(!defined('USER_IS_NOW_OWNER')) define ('USER_IS_NOW_OWNER',"You are now the owner of this page.");

if ($this->page && !$this->GetPageOwner() && $this->GetUser())
{
	$this->SetPageOwner($this->GetPageTag(), $this->GetUserName());
	$this->SetRedirectMessage(USER_IS_NOW_OWNER);
}

$this->Redirect($this->Href());

?>