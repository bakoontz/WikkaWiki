<!-- BEGIN PAGE FOOTER -->
<div id="footer">
<?php
	//page generation start
	global $tstart;
	echo $this->FormOpen('', 'TextSearch', 'get'); 
	echo $this->HasAccess('write') ? '<a href="'.$this->Href('edit').'" title="Click to edit this page">Edit</a> ::'."\n" : '';
	echo '<a href="'.$this->Href('history').'" title="Click to view recent edits to this page">Page History</a> ::'."\n";
	echo $this->GetPageTime() ? '<a class="datetime" href="'.$this->Href('revisions').'" title="Click to view recent revisions list for this page">'.$this->GetPageTime().'</a> <a href="'.$this->Href('revisions.xml').'" title="Click to display a feed with the latest revisions to this page."><img src="images/feed.png" class="icon" width="14" height="14" alt="feed icon" /></a> ::'."\n" : '';

	// if this page exists
	if ($this->page)
	{
		if ($owner = $this->GetPageOwner())
		{
			if ($owner == '(Public)')
			{
				echo "Public page ".($this->IsAdmin() ? '<a href="'.$this->Href('acls').'">(Edit ACLs)</a> ::'."\n" : '::'."\n");
			}
			// if owner is current user
			elseif ($this->UserIsOwner())
			{
           		if ($this->IsAdmin())
           		{
					echo 'Owner: '.$this->Link($owner, '', '', 0).' :: <a href="'.$this->Href('acls').'">Edit ACLs</a> ::'."\n";
				} 
				else
 				{
					echo 'You own this page. :: <a href="'.$this->Href('acls').'">Edit ACLs</a> ::'."\n";
				}
			}
			else
			{
				echo 'Owner: '.$this->Link($owner, '', '', 0).' ::'."\n";
			}
		}
		else
		{
			echo 'Nobody'.($this->GetUser()? ' (<a href="'.$this->Href('claim').'">Take Ownership</a>) ::'."\n" : ' ::'."\n");
		}
	}
?>
<?php
echo ($this->GetUser() ? '<a href="'.$this->Href('referrers').'" title="Click to view a list of URLs referring to this page.">Referrers</a> :: '."\n" : '');
?>
Search: <input name="phrase" size="15" class="searchbox" />
<?php
echo $this->FormClose();
?>
</div>
<!-- END PAGE FOOTER -->
<!-- BEGIN SYSTEM INFO -->
<div class="smallprint">
<?php
echo $this->Link('http://validator.w3.org/check/referer', '', 'Valid XHTML 1.0 Transitional');
?> ::
<?php
echo $this->Link('http://jigsaw.w3.org/css-validator/check/referer', '', 'Valid CSS');
?> ::
Powered by <?php echo $this->Link('http://wikkawiki.org/', '', 'WikkaWiki ' . ($this->IsAdmin() ? $this->GetWakkaVersion() : "")); ?>
</div>
<?php
if ($this->GetConfigValue('sql_debugging'))
{
	echo '<div class="smallprint"><strong>Query log:</strong><br />'."\n";
	foreach ($this->queryLog as $query)
	{
		echo $query['query'].' ('.$query['time'].')<br />'."\n";
	}
	echo '</div>'."\n";
}
echo '<!--'.sprintf(PAGE_GENERATION_TIME, $this->microTimeDiff($tstart)).'-->'."\n";
?>
<!-- END SYSTEM INFO -->
	</div>
<!-- END PAGE WRAPPER -->
</body>
</html>