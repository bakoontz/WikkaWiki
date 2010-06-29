<?php
/**
 * Search wiki pages for a phrase.
 *
 * @package	Actions
 * @version $Id: textsearch.php 1346 2009-03-03 03:38:17Z BrianKoontz $
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
 * @uses	Wakka::Link()
 *
 * @todo	[accessibility] make form accessible
 * @todo	i18n search button text
 */

// init
$result_page_list = '';

// get input
$phrase = stripslashes(trim($this->GetSafeVar('phrase', 'get'))); #312
$case = stripslashes(trim($this->GetSafeVar('case', 'get'))); #312

// display form
// TODO i18n
?>
<?php echo $this->FormOpen('', '', 'get'); ?>
<fieldset><legend><?php echo SEARCH_FOR; ?></legend>
<input name="phrase" size="40" value="<?php echo $phrase ?>" /> <input id="case_sensitive" name="case" type="checkbox" value="1" <?php echo (1==$case?'checked="checked"':'') ?> /><label for="case_sensitive">Case sensitive</label> <input type="submit" value="Search"/>
</fieldset>
<?php echo $this->FormClose(); ?>

<?php
// strange construct here
// also inconsistent behavior:
// if 'phrase' is empty, search tips would be displayed
// if 'phrase' is empty after trimming and removing slashes, search tips NOT displayed

// process search request
$results = $this->FullTextSearch($phrase, $case);
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

// display search tips
print(SEARCH_TIPS);
?>
