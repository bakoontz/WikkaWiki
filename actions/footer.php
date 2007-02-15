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
	echo $this->HasAccess("write") ? '<a href="'.$this->Href("edit").'" title="'.PAGE_EDIT_LINK_TITLE.'">'.PAGE_EDIT_LINK_DESC.'</a> ::'."\n" : "";
	echo '<a href="'.$this->Href("history").'" title="'.PAGE_HISTORY_LINK_TITLE.'">'.PAGE_HISTORY_LINK_DESC.'</a> ::'."\n";
	echo $this->GetPageTime() ? '<a href="'.$this->Href("revisions").'" title="'.PAGE_REVISION_LINK_TITLE.'">'.$this->GetPageTime().'</a> <a href="'.$this->href("revisions.xml").'" title="'.PAGE_REVISION_XML_LINK_TITLE.'"><img src="images/xml.png" width="36" height="14" align="middle" style="border: 0px;" alt="XML" /></a> ::'."\n" : "";

	// if this page exists
	if ($this->page)
	{
		if ($owner = $this->GetPageOwner())
		{
			$page_owner_link = $this->Link($owner,'','',0);
			if ($owner == "(Public)")
			{
				print(PUBLIC_PAGE.' '.($this->IsAdmin() ? '<a href="'.$this->Href("acls").'">'.PAGE_ACLS_EDIT_LINK_TEXT_ADMIN.'</a> ::'."\n" : "::\n"));
			}
			// if owner is current user
			elseif ($this->UserIsOwner())
			{
           		if ($this->IsAdmin())
           		{
					print(sprintf(WIKKA_PAGE_OWNER,$page_owner_link).' :: <a href="'.$this->href("acls").'">'.PAGE_ACLS_EDIT_ADMIN_LINK_TEXT.'</a> ::'."\n");
            	} 
            	else 
            	{
					print(USER_IS_OWNER.' :: <a href="'.$this->href("acls").'">'.PAGE_ACLS_EDIT_ADMIN_LINK_TEXT.'</a> ::'."\n");
				}
			}
			else
			{
				print sprintf(WIKKA_PAGE_OWNER,$page_owner_link)." ::\n";
			}
		}
		else
		{
			print(WIKKA_NO_OWNER.($this->GetUser() ? ' (<a href="'.$this->href("claim").'">'.TAKE_OWNERSHIP."</a>) ::\n" : " ::\n"));
		}
	}
?>
<?php echo ($this->GetUser() ? '<a href="'.$this->href("referrers").'" title="'.REFERRERS_LINK_TITLE.'">'.REFERRERS_LINK_DESC.'</a> :: ' : "") ?> 
<label for="src_phrase"><?php echo SEARCH_LABEL;?></label> <input id="src_phrase" name="phrase" size="15" class="searchbox" />
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