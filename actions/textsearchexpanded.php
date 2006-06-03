<?php
 //constants
	if (!defined('SEARCH_FOR')) define('SEARCH_FOR', 'Search for');
	if (!defined('SEARCH_ZERO_MATCH')) define('SEARCH_ZERO_MATCH', 'No matches');
	if (!defined('SEARCH_ONE_MATCH')) define('SEARCH_ONE_MATCH', 'One match found');
	if (!defined('SEARCH_N_MATCH')) define('SEARCH_N_MATCH', 'There was %d matches found');
	if (!defined('SEARCH_RESULTS')) define('SEARCH_RESULTS', 'Search results');
	if (!defined('SEARCH_MAX_SNIPPETS')) define('SEARCH_MAX_SNIPPETS', 3);
	if (!defined('SEARCH_MYSQL_IDENTICAL_CHARS')) define('SEARCH_MYSQL_IDENTICAL_CHARS', 'aàáâã,eèéêë,iìîï,oòóôõ,uùúû,cç,nñ,yý');
	$result_page_list = '';
?>
<?php echo $this->FormOpen("", "", "get"); ?>
<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td><?php echo SEARCH_FOR; ?>:&nbsp;</td>
		<td><input name="phrase" size="40" value="<?php if (isset($_REQUEST["phrase"])) echo $this->htmlspecialchars_ent(stripslashes($_REQUEST["phrase"])); ?>" /> <input type="submit" value="Search"/></td>
	</tr>
</table><br />
<?php echo $this->FormClose(); ?>

<?php
if (isset($_REQUEST['phrase']) && ($phrase = $_REQUEST["phrase"]))
{
	$phrase_re = stripslashes(trim($phrase));
	if (!$phrase_re) return;
	$results = $this->FullTextSearch($phrase_re);
	if ($results)
	{
		$total_results = 0;
		#Suppress Exact Phrases: terms inside double quotes
		if (preg_match_all('/"([^"]+?)"/', $phrase_re, $match1))
		{
			$phrase_re = preg_replace('/"[^"]+?"/', '', $phrase_re);
			$additional_re = '';
			foreach($match1[1] as $match1_v)
			{
				$additional_re .= '|'.preg_quote($match1_v);
			}
		}
		#Following is preg_quote, but -, * and + are not quoted
		$phrase_re = preg_replace('/(\\.|\\\\|\\?|\\[|\\^|\\]|\\$|\\(|\\)|\\{|\\}|\\=|\\!|\\<|\\>|\\||\\:|\\/|~)/', "\\\\$1", $phrase_re);
		#Suppress from regexp words beginning with -, replace any suit of whitespace characters by a single space
		$phrase_re = preg_replace('/\\-\\S*|\\s\\+/', ' ', " $phrase_re ");
		$phrase_re = preg_replace('/\\\\[<>\\(\\)~]/', '.?', $phrase_re);
		#trimming, suppressing *, replace spaces by |. 
		#BEWARE: $phrase_re must not contain .*, that could replace a replacement terms like </span>
		#We ensure we never replace the < and > characters
		$phrase_re = preg_replace(array('/^ +| +$/', '/ \*/', '/\*/', '/ +/'), array('', '|', '[^\s<>]*', '|'), $phrase_re);
		#Let preg_match find rêve when searching for reve
		$pattern = '/['.str_replace(',', ']/i,/[', SEARCH_MYSQL_IDENTICAL_CHARS).']/i';
		$replacement = '['.str_replace(',', '],[', SEARCH_MYSQL_IDENTICAL_CHARS).']';
		$phrase_re = preg_replace(explode(',', $pattern), explode(',', $replacement) , $phrase_re);
		if ($phrase_re) 
		{
			$phrase_re .= $additional_re;
		}
		else
		{
			$phrase_re = substr($additional_re, 1); #Suppress leading |
		}
		foreach ($results as $i => $page)
		{
			if ($this->HasAccess("read",$page["tag"]))
			{
				$total_results++;
			 /* display portion of the matching body and highlight the search term */ 
			 #Note that FullTextSearch finds phrase even if only tag contains phrase, and not body
				preg_match_all("/(.{0,120})($phrase_re)(.{0,120})/is",$page['tag'].' :: '.$page['body'],$matchString);
				if (count($matchString[0]) > SEARCH_MAX_SNIPPETS)
				{
					$matchString[0] = array_splice($matchString[0], SEARCH_MAX_SNIPPETS, count($matchString));
				}
				$text = $this->htmlspecialchars_ent(implode('<br />', $matchString[0]));
				$text = str_replace('&lt;br /&gt;', '&hellip;<br />&hellip;', $text);
			 # CSS-driven highlighting, tse stands for textsearchexpanded. We highlight $text in 2 steps, 
			 #  We do not use <span>..</span> with preg_replace to ensure that the tag `span' won't be replaced if
			 #  $phrase contains `span'.
				$highlightMatch = preg_replace('/('.$this->htmlspecialchars_ent($phrase_re).')/i','<<$1>>',$text,-1);
				$matchText = "&hellip;".str_replace(array('<<', '>>'), array('<span class="tse_keywords">', '</span>'), $highlightMatch)."&hellip;";
				$result_page_list .= "\n<p>".($i+1)." ".$this->Link($page["tag"])." &mdash; ".$page['time']."</p>";
				$result_page_list .= "\n<blockquote>".$matchText."</blockquote>\n";
			}
		}
	}
}
switch ($total_results)
{
	case 0:
		$match_str = SEARCH_ZERO_MATCH;
		break;
	case 1:
		$match_str = SEARCH_ONE_MATCH;
		break;
	default:
		$match_str = SEARCH_N_MATCH;
		break;
}
printf(SEARCH_RESULTS.": <strong>".$match_str."</strong> for <strong>".$this->htmlspecialchars_ent($phrase)."</strong><br />\n", $total_results);
$result_page_list = $this->ReturnSafeHtml($result_page_list);
echo $result_page_list;
?>
