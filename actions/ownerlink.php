<?php
if ($this->page)
{
	if ($owner = $this->GetPageOwner())
	{
		if ($owner == '(Public)')
		{
			echo "Public page ".($this->IsAdmin() ? '<a href="'.$this->Href('acls').'">[Edit ACLs]</a>'."\n" : "\n");
		}
		// if owner is current user
		elseif ($this->UserIsOwner())
		{
       		if ($this->IsAdmin())
       		{
				echo 'Owner: '.$this->Link($owner, '', '', 0).' <a href="'.$this->Href('acls').'">[Edit ACLs]</a>'."\n";
			} 
			else
 				{
				echo 'You own this page <a href="'.$this->Href('acls').'">[Edit ACLs]</a>'."\n";
			}
		}
		else
		{
			echo 'Owner: '.$this->Link($owner, '', '', 0)."\n";
		}
	}
	else
	{
		echo 'Nobody'.($this->GetUser()? ' <a href="'.$this->Href('claim').'">[Take Ownership]</a>'."\n" : "\n");
	}
}
?>