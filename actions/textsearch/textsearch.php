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
$utf8Compatible = 0;
if(1 == $this->config['utf8_compat_search'])
	$utf8Compatible = 1;

// get input
$phrase = stripslashes(trim($this->GetSafeVar('phrase', 'get'))); #312
$case = stripslashes(trim($this->GetSafeVar('case', 'get'))); #312

// display form
// TODO i18n
?>
<?php echo $this->FormOpen('', '', 'get'); ?>
<fieldset><legend><?php echo T_("Search for"); ?></legend>
<input name="phrase" size="40" value="<?php echo $phrase ?>" /> 
<?php if(0==$utf8Compatible) { ?>
<input id="case_sensitive" name="case" type="checkbox" value="1" <?php echo (1==$case?'checked="checked"':'') ?> />
<label for="case_sensitive">Case sensitive</label> 
<?php } ?>
<input type="submit" value="Search"/>
</fieldset>
<?php echo $this->FormClose(); ?>

<?php
// strange construct here
// also inconsistent behavior:
// if 'phrase' is empty, search tips would be displayed
// if 'phrase' is empty after trimming and removing slashes, search tips NOT displayed

// process search request
$results = $this->FullTextSearch($phrase, $case, $utf8Compatible);
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
if ($total_results > 0)
{
	$expsearchurl  = $this->Href('', 'TextSearchExpanded', 'phrase='.urlencode($phrase));
	$expsearchlink = '<a href="'.$expsearchurl.'">'.T_("Expanded Text Search").'</a>';

	echo '<ol>'.$result_page_list.'</ol>'."\n";
	printf('<br />'.T_("Not sure which page to choose?").'<br />'.T_("Try the %s which shows surrounding text."),$expsearchlink);
}

// display search tips
if(0==$utf8Compatible)
	print(SEARCH_TIPS);
else
	print(SEARCH_TIPS_UTF8_COMPAT);
?>
