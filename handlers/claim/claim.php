<?php
/**
 * Claim the ownership of a page if has no owner, and if user is logged in.
 *
 * @package		Handlers
 * @subpackage	Page
 * @version		$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	Wakka::GetPageOwner()
 * @uses	Wakka::GetPageTag()
 * @uses	Wakka::GetUser()
 * @uses	Wakka::Href()
 * @uses	Wakka::SetRedirectMessager()
 * @uses	Wakka::Redirect()
 */

// only claim ownership if this page has no owner, and if user is logged in.
#if ($this->page && !$this->GetPageOwner() && $this->GetUser())
if ($this->page && !$this->GetPageOwner() && ($user = $this->GetUser()))
{
	#$this->SetPageOwner($this->GetPageTag(), $this->GetUserName());
	$this->SetPageOwner($this->GetPageTag(), $user['name']);
	$this->SetRedirectMessage(USER_IS_NOW_OWNER);
}

$this->Redirect($this->Href());
?>