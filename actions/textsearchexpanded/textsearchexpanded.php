<?php
/**
 * Search wiki pages for a phrase and display the results with a snippet of surrounding text.
 * 
 * @package	Actions
 * @version $Id: textsearchexpanded.php 1346 2009-03-03 03:38:17Z BrianKoontz $
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses	Wakka::FormClose()
 * @uses	Wakka::FormOpen()
 * @uses	Wakka::FullTextSearch()
 * @uses	Wakka::HasAccess()
 * @uses	Wakka::htmlspecialchars_ent()
 * @uses	Wakka::Link()
 * @uses	Wakka::ReturnSafeHtml()
 *
 * @todo	[accessibility] make form accessible 
 * @todo	i18n search button text  
 */

/**#@+
 * Default value.
 */
if (!defined('SEARCH_MAX_SNIPPETS')) define('SEARCH_MAX_SNIPPETS', 3);
if(!defined('SEARCH_MYSQL_IDENTICAL_CHARS')) define('SEARCH_MYSQL_IDENTICAL_CHARS', 'aàáâã,eèéêë,iìîï,oòóôõ,uùúû,cç,nñ,yý');
/**#@-*/

// init
$result_page_list = '';
$utf8Compatible = 0;
if(1==$this->GetConfigValue('utf8_compat_search'))
	$utf8Compatible = 1;

// get input
$phrase = stripslashes(trim($this->GetSafeVar('phrase', 'get'))); #312
$case = stripslashes(trim($this->GetSafeVar('case', 'get')));

// display form
// TODO i18n
?>
<?php echo $this->FormOpen("", "", "get"); ?>
<fieldset><legend><?php echo T_("Search for"); ?></legend>
<input name="phrase" size="40" value="<?php echo $phrase ?>" /> 
<?php if(0==$utf8Compatible) { ?>
<input name="case" type="checkbox" value="1" <?php echo (1==$case?'checked="checked"':'') ?> />
<label for="case">Case sensitive</label> 
<?php } ?>
<input type="submit" value="Search"/>
</fieldset>
<?php echo $this->FormClose(); ?>

<?php
// TODO see remarks in textsearch.php

// process search request  
$results = $this->FullTextSearch($phrase, $case, $utf8Compatible);
$total_results = 0;
if ($results)
{
	// build RE from $phrase for highlighting
	// init
	$phrase_re = $phrase;
	$additional_re = ''; #38
	
	// Extract Exact Phrases: terms inside double quotes
	$phrase_re = html_entity_decode($phrase_re);
	if (preg_match_all('/"([^"]+?)"/', $phrase_re, $match1))
	{
		$phrase_re = preg_replace('/"[^"]+?"/', '', $phrase_re);
		foreach($match1[1] as $match1_v)
		{
			$additional_re .= '|'.preg_quote($match1_v);
		}
	}
	// Following is preg_quote, but -, * and + are not quoted
	$phrase_re = preg_replace('/(\\.|\\\\|\\?|\\[|\\^|\\]|\\$|\\(|\\)|\\{|\\}|\\=|\\!|\\<|\\>|\\||\\:|\\/|~)/', "\\\\$1", $phrase_re);	#34
	// Suppress from regexp words beginning with -, replace any suit of whitespace characters by a single space
	$phrase_re = preg_replace('/\\-\\S*|\\s\\+/', ' ', " $phrase_re ");	#34
	$phrase_re = preg_replace('/\\\\[<>\\(\\)~]/', '.?', $phrase_re);	#34
	// trimming, suppressing *, replace spaces by |. 
	// BEWARE: $phrase_re must not contain .*, that could replace replacement terms like </span>
	// We ensure we never replace the < and > characters
	$phrase_re = preg_replace(array('/^ +| +$/', '/ \*/', '/\*/', '/ +/'), array('', '|', '[^\s<>]*', '|'), $phrase_re);	#34
	// Let preg_match find rêve when searching for reve
	$pattern     = '/['.str_replace(',', ']/i,/[', SEARCH_MYSQL_IDENTICAL_CHARS).']/i';	#34
	$replacement = '['.str_replace(',', '],[', SEARCH_MYSQL_IDENTICAL_CHARS).']';	#34
	$phrase_re = preg_replace(explode(',', $pattern), explode(',', $replacement) , $phrase_re);
	// add extracted exact phrases back
	if ($phrase_re) 
	{
		$phrase_re .= $additional_re;
	}
	else
	{
		$phrase_re = substr($additional_re, 1); // Suppress leading |
	}
	
	// highlight search terms in page fragment
	foreach ($results as $i => $page)
	{
		if ($this->HasAccess("read",$page["tag"]))
		{
			$total_results++;
			// display portion of the matching body and highlight the search term */ 
			// Note that FullTextSearch finds a phrase even if it only matches a tag (name or attribute), and not its content
			if (1==$case) {
				preg_match_all("/(.{0,120})($phrase_re)(.{0,120})/s",$page['tag'].' :: '.$page['body'],$matchString);
			} else {
				preg_match_all("/(.{0,120})($phrase_re)(.{0,120})/is",$page['tag'].' :: '.$page['body'],$matchString);
			}
			if (count($matchString[0]) > SEARCH_MAX_SNIPPETS)
			{
				$matchString[0] = array_splice($matchString[0], SEARCH_MAX_SNIPPETS, count($matchString));
			}
			$text = $this->htmlspecialchars_ent(implode('<br />', $matchString[0]));	//TODO could be done in a single step
			$text = str_replace('&lt;br /&gt;', '&hellip;<br />&hellip;', $text);		//TODO could be done in a single step
			//TODO the single step won't work until htmlspecialchars_ent() accepts array as parameter...
			#$text = implode('&hellip;<br />&hellip;',$this->htmlspecialchars_ent($matchString[0])); // $matchString[0] has an array of matching snippets
			// CSS-driven highlighting, tse stands for textsearchexpanded. We highlight $text in 2 steps, 
			// We do not use <span>..</span> with preg_replace to ensure that the tag `span' won't be replaced if
			// $phrase contains `span'.
			if (1==$case) {
				$highlightMatch = preg_replace('/('.$this->htmlspecialchars_ent($phrase_re).')/','<<$1>>',$text,-1); // -1 = no limit (default!)
			} else {
				$highlightMatch = preg_replace('/('.$this->htmlspecialchars_ent($phrase_re).')/i','<<$1>>',$text,-1); // -1 = no limit (default!)
			}
			$matchText = "&hellip;".str_replace(array('<<', '>>'), array('<span class="tse_keywords">', '</span>'), $highlightMatch)."&hellip;";
			$result_page_list .= "\n<li>".$this->Link($page["tag"])." &mdash; ".$page['time'];
			$result_page_list .= "\n<blockquote>".$matchText."</blockquote>\n";
			$result_page_list .= "\n</li>\n";
		}
	}
}
switch ($total_results)
{
	case 0:
		$match_str = T_("No matches");
		break;
	case 1:
		$match_str = T_("One match found");
		break;
	default:
		$match_str = sprintf(T_("%d matches found"), $total_results);
		break;
}
printf(T_("Search results: <strong>%s</strong> for <strong>%s</strong>"), $match_str, $this->htmlspecialchars_ent($phrase));
$result_page_list = $this->ReturnSafeHtml($result_page_list);
if ($total_results) 
{
	echo '<ol>'.$result_page_list.'</ol>'."\n";
}
?>
