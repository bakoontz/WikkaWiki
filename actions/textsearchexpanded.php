<?php echo $this->FormOpen("", "", "get") ?>
<table border="0" cellspacing="0" cellpadding="0">
        <tr>
                <td>Search for:&nbsp;</td>
                <td><input name="phrase" size="40" value="<?php if (isset($_REQUEST["phrase"])) echo $this->htmlspecialchars_ent(stripslashes($_REQUEST["phrase"])); ?>" /> <input type="submit" value="Search"/></td>
        </tr>
</table><br />
<?php echo $this->FormClose(); ?>

<?php
if (isset($_REQUEST["phrase"]) && $phrase = $_REQUEST["phrase"])
{
	$phrase = stripslashes($phrase); 
	$results = $this->FullTextSearch($phrase);
	$match_str = count($results) <> 1 ? " matches" : " match";
	print("Search results: <strong>".count($results).$match_str."</strong> for <strong>".$this->htmlspecialchars_ent($phrase)."</strong><br />\n");
	$phrase = str_replace("\"", "", $phrase);
	$phrase = preg_quote($phrase, "/");
	if ($results)
	{
		foreach ($results as $i => $page)
		{
			/* display portion of the matching body and highlight the search term */
			preg_match("/(.{0,120}$phrase.{0,120})/is",$page['body'],$matchString);
			$text = $this->htmlspecialchars_ent($matchString[0]);
			$highlightMatch = preg_replace("/($phrase)/i","<font color=\"green\"><b>$1</b></font>",$text,-1);
			$matchText = "&hellip;".$highlightMatch."&hellip;";
			$output .= "\n<p>".($i+1)." ".$this->Link($page["tag"])." &mdash; ".$page[time]."</p>";
			$output .= "\n<blockquote>".$matchText."</blockquote>\n";
		}
	}
}
$output = $this->ReturnSafeHtml($output);
echo $output;
?>