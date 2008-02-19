<?php echo $this->FormOpen("", "", "get") ?>
		Search for:&nbsp;
		<input name="phrase" size="35" value="<?php echo htmlspecialchars($_REQUEST["phrase"]) ?>" class="searchbox" /> <input type="submit" value="Search" />
<?php echo $this->FormClose(); ?>

<?php
if ($phrase = $_REQUEST["phrase"])
{
	print("<br />");
	$results = $this->FullTextSearch($phrase);
	if ($results) 
	{
		print("<strong>Search results for pages containing \"$phrase\":</strong><br /><br />\n");
		foreach ($results as $i => $page)
		{
			print(($i+1).". ".$this->Link($page["tag"])."<br />\n");
		}
		print("<br />Not sure which page to choose?<br />Try the <a href=\"".$this->href("", "TextSearchExpanded", "phrase=$phrase")."\">Expanded Text Search</a> which shows surrounding text.");
	}
	else
	{
		print("No results for \"$phrase\". :-(");
	}
}

?>