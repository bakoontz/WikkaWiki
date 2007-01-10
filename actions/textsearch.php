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
 */

$result_page_list = '';
?>		
<?php echo $this->FormOpen('', '', 'get'); ?>
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
	$total_results = 0;
	if ($results)
	{
		foreach ($results as $i => $page)
		{
			if ($this->HasAccess("read",$page["tag"]))
			{
				$total_results ++;
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
	printf(SEARCH_RESULTS.': <strong>'.$match_str.'</strong> for <strong>'.$this->htmlspecialchars_ent($phrase).'</strong><br />'."\n", $total_results);
	if ($total_results > 0)
	{
		echo '<ol>'.$result_page_list.'</ol>'."\n";
		echo str_replace('$1', $this->Href('', 'TextSearchExpanded', 'phrase='.urlencode($phrase)), SEARCH_TRY_EXPANDED);
	}
}
if ($this->CheckMySQLVersion(4,00,01))
{	
	print SEARCH_TIPS;
}
?>
