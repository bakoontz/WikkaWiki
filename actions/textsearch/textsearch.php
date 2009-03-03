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
 *
 * @todo	[accesibility] make form accessible
 * @todo	i18n search button text
 */

// init
$result_page_list = '';

// get input
$phrase = (isset($_GET['phrase'])) ? stripslashes(trim($_GET['phrase'])) : ''; #312
$phrase_disp = $this->htmlspecialchars_ent($phrase);

// display form
// TODO i18n
?>
<?php echo $this->FormOpen('', '', 'get'); ?>
<fieldset><legend><?php echo SEARCH_FOR; ?></legend>
<input name="phrase" size="40" value="<?php echo $phrase_disp ?>" /> <input type="submit" value="Search"/>
</fieldset>
<?php echo $this->FormClose(); ?>

<?php
// strange construct here
// also inconsistent behavior:
// if 'phrase' is empty, search tips would be displayed
// if 'phrase' is empty after trimming and removing slashes, search tips NOT displayed

// process search request
if ('' !== $phrase)
{
	#$phrase_re = stripslashes(trim($phrase));
	#if (!$phrase_re) return;
	#$results = $this->FullTextSearch($phrase_re);
	$results = $this->FullTextSearch($phrase);
	$total_results = 0;
	if ($results)
	{
		foreach ($results as $i => $page)
		{
			if ($this->HasAccess('read',$page['tag']))
			{
				$total_results++;
				$result_page_list .= '<li>'.$this->Link($page['tag']).'</li>'."\n";	// @@@ make new array and let new array2list methods do the formatting
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
			$match_str = sprintf(SEARCH_N_MATCH, $total_results);
			break;
	}
	printf(SEARCH_RESULTS, $match_str, $this->htmlspecialchars_ent($phrase));
	if ($total_results > 0)
	{
		$expsearchurl  = $this->Href('', 'TextSearchExpanded', 'phrase='.urlencode($phrase));
		$expsearchlink = '<a href="'.$expsearchurl.'">'.SEARCH_EXPANDED_LINK_DESC.'</a>';

		echo '<ol>'.$result_page_list.'</ol>'."\n";
		printf('<br />'.SEARCH_NOT_SURE_CHOICE.'<br />'.SEARCH_TRY_EXPANDED,$expsearchlink);
	}
}

// display search tips
#if ($this->CheckMySQLVersion(4,00,01))	// DONE replace with version_compare
if ($this->CheckMySQLVersion('4.00.01'))
{
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
