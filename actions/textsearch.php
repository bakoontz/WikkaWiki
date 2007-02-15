<?php
/**  
 * Search wiki pages for a phrase.
 * 
 * @package	Actions
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses	Wakka::FormClose()
 * @uses	Wakka::FormOpen()
 * @uses	Wakka::FullTextSearch()
 * @uses	Wakka::Href()
 * @uses	Wakka::HasAccess()
 * @uses	Wakka::CheckMySQLVersion()
 * @uses	Wakka::htmlspecialchars_ent()
 * @todo	JW: cleanup temporary comments i18n
 */

$result_page_list = '';
?>		
<?php echo $this->FormOpen('', '', 'get'); ?>
<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td><?php echo SEARCH_FOR; ?>:&nbsp;</td>
		<td><input name="phrase" size="40" value="<?php if (isset($_REQUEST['phrase'])) echo $this->htmlspecialchars_ent(stripslashes($_REQUEST['phrase'])); ?>" /> <input type="submit" value="Search"/></td>
	</tr>
</table><br />
<?php echo $this->FormClose(); ?>

<?php
if (isset($_REQUEST['phrase']) && ($phrase = $_REQUEST['phrase']))
{
	$phrase_re = stripslashes(trim($phrase)); 
	if (!$phrase_re) return;
	$results = $this->FullTextSearch($phrase_re);
	$total_results = 0;
	if ($results)
	{
		foreach ($results as $i => $page)
		{
			if ($this->HasAccess('read',$page['tag']))
			{
				$total_results++;
				$result_page_list .= '<li>'.$this->Link($page['tag']).'</li>'."\n";
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
	printf(SEARCH_RESULTS.' <strong>'.$match_str.'</strong> for <strong>'.$this->htmlspecialchars_ent($phrase).'</strong><br />'."\n", $total_results);
	if ($total_results > 0)
	{
		$expsearchurl  = $this->Href('', 'TextSearchExpanded', 'phrase='.urlencode($phrase));
		$expsearchlink = '<a href="'.$expsearchurl.'">'.SEARCH_EXPANDED_LINK_DESC.'</a>';

		echo '<ol>'.$result_page_list.'</ol>'."\n";
		#echo str_replace('$1', $this->Href('', 'TextSearchExpanded', 'phrase='.urlencode($phrase)), SEARCH_TRY_EXPANDED);
		printf('<br />'.SEARCH_NOT_SURE_CHOICE.'<br />'.SEARCH_TRY_EXPANDED,$expsearchlink);
	}
}
if ($this->CheckMySQLVersion(4,00,01))
{	
	#print SEARCH_TIPS;
	// define variables for template
	$search_tips     = SEARCH_TIPS;
	$search_word_1   = SEARCH_WORD_1;
	$search_word_2   = SEARCH_WORD_2;
	$search_word_3   = SEARCH_WORD_3;
	$search_word_4   = SEARCH_WORD_4;
	$search_phrase   = SEARCH_PHRASE;
	$search_target_1 = SEARCH_TARGET_1;
	$search_target_2 = SEARCH_TARGET_2;
	$search_target_3 = SEARCH_TARGET_3;
	$search_target_4 = SEARCH_TARGET_4;
	$search_target_5 = SEARCH_TARGET_5;
	// define template @@@ TODO make more structural markup
	$tpl_search_tips = <<<TPLSEARCHTIPS
	<br /><br /><hr /><br />
	<strong>$search_tips</strong><br /><br />
	<div class="indent">$search_word_1 $search_word_2</div>
	$search_target_1<br /><br />
	<div class="indent">+$search_word_1 +$search_word_3</div>
	$search_target_2<br /><br />
	<div class="indent">+$search_word_1 -$search_word_4</div>
	$search_target_3<br /><br />
	<div class="indent">$search_word_1*</div>
	$search_target_4<br /><br />
	<div class="indent">"$search_phrase"</div>
	$search_target_5 <br />
TPLSEARCHTIPS;
	// print template
	echo $tpl_search_tips;
}
?>
