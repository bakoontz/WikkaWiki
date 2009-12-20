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
 * @uses	Wakka::Link()
 * 
 * @todo	[accesibility] make form accessible 
 * @todo	i18n search button text  
 */

// init
$result_page_list = '';

// get input
$phrase = (isset($_GET['phrase'])) ? stripslashes(trim($_GET['phrase'])) : ''; #312
$case = (isset($_GET['case'])) ? stripslashes(trim($_GET['case'])) : 0; #312
$phrase_disp = $this->htmlspecialchars_ent($phrase);
$case_disp = $this->htmlspecialchars_ent($case);

// display form
?>		
<?php echo $this->FormOpen('', '', 'get'); ?>
<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td><?php echo SEARCH_FOR; ?>:&nbsp;</td>
		<td><input name="phrase" size="40" value="<?php echo $phrase_disp ?>" /> <input name="case" type="checkbox" value="1" <?php echo (1==$case?'checked="checked"':'') ?> /><label for="case">Case sensitive</label> <input type="submit" value="Search"/></td><!--i18n-->
	</tr>
</table><br />
<?php echo $this->FormClose(); ?>

<?php
// strange construct here 
// also inconsistent behavior:
// if 'phrase' is empty, search tips would be displayed
// if 'phrase' is empty after trimming and removing slashes, search tips NOT displayed

// process search request  
if ('' !== $phrase)
{
	$results = $this->FullTextSearch($phrase, $case);
	$total_results = 0;
	if ($results)
	{
		foreach ($results as $i => $page)
		{
			if ($this->HasAccess("read",$page["tag"]))
			{
				$total_results ++;
				$result_page_list .= ($i+1).". ".$this->Link($page["tag"])."<br />\n";
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
printf(SEARCH_RESULTS.'<br />', $match_str, $phrase_disp);
	if ($total_results > 0)
	{
		print($result_page_list);
		print(str_replace('$1', $this->href("", "TextSearchExpanded", 'phrase='.urlencode($phrase)), SEARCH_TRY_EXPANDED));
	}
}

// display search tips
if ($this->CheckMySQLVersion(4,00,01))	//TODO replace with version_compare
{	
	print(SEARCH_TIPS);
}
?>
