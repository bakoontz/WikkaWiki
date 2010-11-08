<?php
/**
 * Claim the ownership of a page if has no owner, and if user is logged in.
 *
 * @package		Handlers
 * @version		$Id: claim.php 738 2007-10-03 11:48:41Z JavaWoman $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	Wakka::GetPageOwner()
 * @uses	Wakka::GetPageTag()
 * @uses	Wakka::GetUser()
 * @uses	Wakka::GetUserName()
 * @uses	Wakka::Href()
 * @uses	Wakka::Redirect()
 * @uses	Wakka::SetPageOwner()
 * @uses	Wakka::SetRedirectMessage()
 */

// only claim ownership if current page has no owner, and if user is logged in.
if ($this->page && !$this->GetPageOwner() && $this->GetUser())
{
	$this->SetPageOwner($this->GetPageTag(), $this->GetUserName());
	$this->SetRedirectMessage(T_("You are now the owner of this page."));
}

$this->Redirect($this->Href());
?>
