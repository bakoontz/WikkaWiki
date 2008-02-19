<?php echo $this->FormOpen("", "", "GET") ?>
		<td>Search for:&nbsp;</td>
		<td><input name="phrase" size="35" value="<?php echo htmlspecialchars($_REQUEST["phrase"]) ?>" class="searchbox" /> <input type="submit" value="Search" class="btn" /></td>
<?php echo $this->FormClose(); ?>

<?php
if ($phrase = $_REQUEST["phrase"])
{
	print("<br />");
	// if ($results = $this->FullTextSearch($phrase)) // Uncomment to stop partial tag searching.
	if ($results = $this->FullTextSearchAndLikeTags($phrase)) // Partial tag search.
	{
		print("<strong>Search results for pages containing \"$phrase\":</strong><br /><br />\n");
		foreach ($results as $i => $page)
		{
			print(($i+1).". ".$this->Link($page["tag"])."<br />\n");
		}
		print("<BR>Not sure which page to choose?<BR>Try the <a href=\"".$this->href("", "TextSearchExpanded", "phrase=$phrase")."\">Expanded Text Search</a> which shows surrounding text.");
	}
	else
	{
		print("No results for \"$phrase\". :-(");
	}
}

?>