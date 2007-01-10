<div class="footer">
<?php
/**
 * Generates the page footer.
 * 
 * @package		Template
 * @version		$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses	Wakka::FormOpen()
 * @uses	Wakka::HasAccess()
 * @uses	Wakka::Href()
 * @uses	Wakka::GetPageTime()
 * @uses	Wakka::GetPageOwner()
 * @uses	Wakka::IsAdmin()
 * @uses	Wakka::UserIsOwner()
 * @uses	Wakka::Link()
 * @uses	Wakka::FormClose()
 * @uses	Wakka::GetWakkaVersion()
 * 
 * @todo	move <div class="footer"> to template
 */

	echo $this->FormOpen("", "TextSearch", "get"); 
	echo $this->HasAccess("write") ? '<a href="'.$this->href("edit").'" title="'.PAGE_EDIT_LINK_TITLE.'">'.PAGE_EDIT_LINK_TEXT.'</a> ::'."\n" : "";
	echo '<a href="'.$this->href("history").'" title="'.PAGE_HISTORY_LINK_TITLE.'">'.PAGE_HISTORY_LINK_TEXT.'</a> ::'."\n";
	echo $this->GetPageTime() ? '<a href="'.$this->href("revisions").'" title="'.PAGE_REVISION_LINK_TITLE.'">'.$this->GetPageTime().'</a> <a href="'.$this->href("revisions.xml").'" title="'.PAGE_REVISION_LINK_XML_TITLE.'"><img src="images/xml.png" width="36" height="14" align="middle" style="border : 0px;" alt="XML" /></a> ::'."\n" : "";

	// if this page exists
	if ($this->page)
	{
		if ($owner = $this->GetPageOwner())
		{
			if ($owner == "(Public)")
			{
				print(PUBLIC_PAGE.' '.($this->IsAdmin() ? '<a href="'.$this->href("acls").'">'.PAGE_ACLS_EDIT_LINK_TEXT_ADMIN.'</a> ::'."\n" : "::\n"));
			}
			// if owner is current user
			elseif ($this->UserIsOwner())
			{
           		if ($this->IsAdmin())
           		{
					print(OWNER_LABEL.' '.$this->Link($owner, "", "", 0).' :: <a href="'.$this->href("acls").'">'.PAGE_ACLS_EDIT_LINK_TEXT_ADMIN.'</a> ::'."\n");
            	} 
            	else 
            	{
					print(USER_IS_OWNER.' :: <a href="'.$this->href("acls").'">'.PAGE_ACLS_EDIT_LINK_TEXT.'</a> ::'."\n");
				}
			}
			else
			{
				print(OWNER_LABEL.' '.$this->Link($owner, "", "", 0)." ::\n");
			}
		}
		else
		{
			print(NO_OWNER.($this->GetUser() ? ' (<a href="'.$this->href("claim").'">'.TAKE_OWNERSHIP."</a>) ::\n" : " ::\n"));
		}
	}
?>
<?php echo ($this->GetUser() ? '<a href="'.$this->href("referrers").'" title="'.REFERRER_LINK_TITLE.'">'.REFERRER_LINK_TEXT.'</a> :: ' : "") ?> 
<?php echo SEARCH_LABEL;?> <input name="phrase" size="15" class="searchbox" />
<?php echo $this->FormClose(); ?>
</div>

<div class="smallprint">
<?php echo $this->Link("http://validator.w3.org/check/referer", "", "Valid XHTML 1.0 Transitional") ?> ::
<?php echo $this->Link("http://jigsaw.w3.org/css-validator/check/referer", "", "Valid CSS") ?> ::
Powered by <?php echo $this->Link("http://wikkawiki.org/", "", "Wikka Wakka Wiki ".$this->GetWakkaVersion()) ?>
</div>

<?php
	// display SQL debug information to admins
	if ($this->config['sql_debugging'] == 1 && $this->IsAdmin())
	{
		echo '<div class="smallprint"><strong>Query log:</strong><br />'."\n";
		foreach ($this->queryLog as $query)
		{
			echo $query['query'].' ('.$query['time'].')<br />'."\n";
		}
		echo '</div>';
	}
?>