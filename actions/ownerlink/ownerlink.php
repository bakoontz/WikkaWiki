<?php
/**
 * Ownership menulet
 *
 * Displays a link to the page owner's profile (as well as links to change ACL,
 * depending on user privileges).
 *
 * Syntax: {{ownerlink}}
 *
 * @package		Actions
 * @subpackage	Menulets
 * @name		Owner link
 *
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 */
if ($this->page)
{
	if ($owner = $this->GetPageOwner())
	{
		if ($owner == '(Public)')
		{
			echo T_("Public page")." ".($this->IsAdmin() ? '<a title="'.T_("Change the Access Control List for this page").'" href="'.$this->Href('acls').'">'.T_("[Edit ACLs]").'</a>'."\n" : "\n");
		}
		// if owner is current user
		elseif ($this->UserIsOwner())
		{
       		if ($this->IsAdmin())
       		{
				echo T_("Owner:").' '.$this->Link($owner, '', '', 0).' <a title="'.T_("Change the Access Control List for this page").'" href="'.$this->Href('acls').'">'.T_("[Edit ACLs]").'</a>'."\n";
			} 
			else
 				{
				echo T_("You own this page").' <a title="'.T_("Change the Access Control List for this page").'" href="'.$this->Href('acls').'">'.T_("[Edit ACLs]").'</a>'."\n";
			}
		}
		else
		{
			echo T_("Owner:").' '.$this->Link($owner, '', '', 0)."\n";
		}
	}
	else
	{
		echo T_("Nobody").($this->GetUser()? ' <a title="'.T_("Click to become the owner of this page").'" href="'.$this->Href('claim').'">'.T_("[Take Ownership]").'</a>'."\n" : "\n");
	}
}
?>
