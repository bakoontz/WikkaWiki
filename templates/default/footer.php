<!-- BEGIN PAGE FOOTER -->
<div id="footer">
<?php
	//page generation start
	global $tstart;
	echo $this->MakeMenu('options_menu');
	$wikka_patch_level = ($this->GetWikkaPatchLevel() == '0') ? '' : '-p'.$this->GetWikkaPatchLevel();
?>
</div>
<!-- END PAGE FOOTER -->
<!-- BEGIN SYSTEM INFO -->
<div id="smallprint">
<?php
echo $this->Link('http://validator.w3.org/check/referer', '', T_("Valid XHTML"));
?> ::
<?php
echo $this->Link('http://jigsaw.w3.org/css-validator/check/referer', '', T_("Valid CSS:"));
?> ::
<?php
echo $this->Link('http://wikkawiki.org/', '', sprintf(T_("Powered by %s"), 'WikkaWiki' .($this->IsAdmin() ? ' '.$this->GetWakkaVersion() . $wikka_patch_level : '')));
?>
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
echo '<!--'.sprintf(T_("Page was generated in %.4f seconds"), $this->microTimeDiff($tstart)).'-->'."\n";
?>
</div>
<!-- END PAGE WRAPPER -->
</body>
</html>
