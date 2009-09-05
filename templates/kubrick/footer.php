<!-- BEGIN PAGE FOOTER -->
<div id="footer">
<?php
	$wikka_patch_level = ($this->GetWikkaPatchLevel() == '0') ? '' : '-p'.$this->GetWikkaPatchLevel();
?>
</div>
<!-- END PAGE FOOTER -->
<!-- BEGIN SYSTEM INFO -->
<div id="smallprint">
<?php
echo $this->Link('http://validator.w3.org/check/referer', '', 'Valid XHTML 1.0 Transitional');
?> ::
<?php
echo $this->Link('http://jigsaw.w3.org/css-validator/check/referer', '', 'Valid CSS');
?> ::
Powered by <?php echo $this->Link('http://wikkawiki.org/', '', 'WikkaWiki ' . ($this->IsAdmin() ? $this->GetWakkaVersion() . $wikka_patch_level : "")); ?>
</div>
<!-- END SYSTEM INFO -->
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
</div>
<!-- END PAGE WRAPPER -->
</body>
</html>
