<?php
/**
 * Ownership menulet
 */
//i18n
if (!defined('OWNERLINK_PUBLIC_PAGE')) define('OWNERLINK_PUBLIC_PAGE', 'Public page');
if (!defined('OWNERLINK_NOBODY')) define('OWNERLINK_NOBODY', 'Nobody');
if (!defined('OWNERLINK_OWNER')) define('OWNERLINK_OWNER', 'Owner:');
if (!defined('OWNERLINK_SELF')) define('OWNERLINK_SELF', 'You own this page');
if (!defined('EDITACLLINK_TEXT')) define('EDITACLLINK_TEXT', '[Edit ACLs]');
if (!defined('EDITACLLINK_TITLE')) define('EDITACLLINK_TITLE', 'Change the Access Control List for this page');
if (!defined('CLAIMLINK_TEXT')) define('CLAIMLINK_TEXT', '[Take Ownership]');
if (!defined('CLAIMLINK_TITLE')) define('CLAIMLINK_TITLE', 'Click to become the owner of this page');

if ($this->page)
{
	if ($owner = $this->GetPageOwner())
	{
		if ($owner == '(Public)')
		{
			echo OWNERLINK_PUBLIC_PAGE." ".($this->IsAdmin() ? '<a title="'.EDITACLLINK_TITLE.'" href="'.$this->Href('acls').'">'.EDITACLLINK_TEXT.'</a>'."\n" : "\n");
		}
		// if owner is current user
		elseif ($this->UserIsOwner())
		{
       		if ($this->IsAdmin())
       		{
				echo OWNERLINK_OWNER.' '.$this->Link($owner, '', '', 0).' <a title="'.EDITACLLINK_TITLE.'" href="'.$this->Href('acls').'">'.EDITACLLINK_TEXT.'</a>'."\n";
			} 
			else
 				{
				echo OWNERLINK_SELF.' <a title="'.EDITACLLINK_TITLE.'" href="'.$this->Href('acls').'">'.EDITACLLINK_TEXT.'</a>'."\n";
			}
		}
		else
		{
			echo OWNERLINK_OWNER.' '.$this->Link($owner, '', '', 0)."\n";
		}
	}
	else
	{
		echo OWNERLINK_NOBODY.($this->GetUser()? ' <a title="'.CLAIMLINK_TITLE.'" href="'.$this->Href('claim').'">'.CLAIMLINK_TEXT.'</a>'."\n" : "\n");
	}
}
?>