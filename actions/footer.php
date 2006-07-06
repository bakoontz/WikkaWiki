<div class="footer">
<?php
/**
 * Echos the footer for an xhtml 1.0 page.
 * 
 * @package		Template
 * @subpackage	xHtml
 * @version		$Id$
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

// i18n
if (!defined('PAGE_EDIT_LINK_TITLE')) define ('PAGE_EDIT_LINK_TITLE', 'Click to edit this page');
if (!defined('PAGE_EDIT_LINK_TEXT')) define ('PAGE_EDIT_LINK_TEXT', 'Edit page');
if (!defined('PAGE_HISTORY_LINK_TITLE')) define ('PAGE_HISTORY_LINK_TITLE', 'Click to view recent edits to this page');
if (!defined('PAGE_HISTORY_LINK_TEXT')) define ('PAGE_HISTORY_LINK_TEXT', 'Page History');
if (!defined('PAGE_REVISION_LINK_TITLE')) define ('PAGE_REVISION_LINK_TITLE', 'Click to view recent revisions list for this page');
if (!defined('PAGE_REVISION_LINK_XML_TITLE')) define ('PAGE_REVISION_LINK_XML_TITLE', 'Click to view recent revisions list for this page');
if (!defined('PAGE_ACLS_EDIT_LINK_TEXT')) define ('PAGE_ACLS_EDIT_LINK_TEXT', 'Edit ACLs');
if (!defined('PAGE_ACLS_EDIT_LINK_TEXT_ADMIN')) define ('PAGE_ACLS_EDIT_LINK_TEXT_ADMIN', '(Edit ACLs)');

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
				print("Public page ".($this->IsAdmin() ? '<a href="'.$this->href("acls").'">'.PAGE_ACLS_EDIT_LINK_TEXT_ADMIN.'</a> ::'."\n" : "::\n"));
			}
			// if owner is current user
			elseif ($this->UserIsOwner())
			{
           		if ($this->IsAdmin())
           		{
					print("Owner: ".$this->Link($owner, "", "", 0).' :: <a href="'.$this->href("acls").'">'.PAGE_ACLS_EDIT_LINK_TEXT_ADMIN.'</a> ::'."\n"); #i18n
            	} 
            	else 
            	{
					print("You own this page. :: <a href=\"".$this->href("acls").'">'.PAGE_ACLS_EDIT_LINK_TEXT.'</a> ::'."\n"); #i18n
				}
			}
			else
			{
				print("Owner: ".$this->Link($owner, "", "", 0)." ::\n"); #i18n
			}
		}
		else
		{
			print("Nobody".($this->GetUser() ? " (<a href=\"".$this->href("claim")."\">Take Ownership</a>) ::\n" : " ::\n")); #i18n
		}
	}
?>
<?php echo ($this->GetUser() ? "<a href='".$this->href("referrers")."' title='Click to view a list of URLs referring to this page.'>Referrers</a> :: " : "") #i18n ?> 
Search: <input name="phrase" size="15" class="searchbox" />
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