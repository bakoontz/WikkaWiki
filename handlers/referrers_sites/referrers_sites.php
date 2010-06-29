<?php
/**
 * Show domains linking to the page/ the wiki.
 * 
 * @package		Handlers
 * @subpackage	Referrers	
 * @version		$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses		Wakka::IsAdmin()
 * @uses		Wakka::LoadReferrers()
 * @uses		Wakka::GetConfigValue()
 * @uses		Wakka::GetHandler()
 * @uses		Wakka::GetUser()
 * @uses		Wakka::Href()
 * @uses		Wakka::htmlspecialchars_ent()
 * 
 * @todo		move <div> to template
 */

echo '<div id="content">'."\n"; //TODO: move to templating class

$global = '';
$IsAdmin = $this->IsAdmin();
if (isset($_GET["global"])) #312
{
	// referrers to this wiki
	// prepare UI
	$global = $this->GetSafeVar('global', 'get'); #312
	$title = 'Domains/sites linking to this wiki (<a href="'.$this->Href('referrers', '', 'global=1').'">see list of different URLs</a>):'."\n";
	$referrers = $this->LoadReferrers();
}
else
{
	$title = 'Domains/sites pages linking to '.$this->Link($this->GetPageTag()).
		($this->GetConfigValue('referrers_purge_time') ? ' (last '.($this->GetConfigValue('referrers_purge_time') == 1 ? '24 hours' : $this->GetConfigValue('referrers_purge_time').' days').')' : '').' (<a href="'.$this->Href('referrers').'">see list of different URLs</a>):'."\n";
	$referrers = $this->LoadReferrers($this->GetPageTag());
}

echo '<strong>'.$title.'</strong><br />'."\n";
echo '<em class="error">Note to spammers: This page is not indexed by search engines, so don\'t waste your time.</em><br /><br />'."\n";

if ($this->GetUser())
{
	if ($referrers)
	{
		for ($a = 0; $a < count($referrers); $a++)
		{
			$temp_parse_url = parse_url($referrers[$a]['referrer']);
			$temp_parse_url = ($temp_parse_url['host'] != '') ? strtolower(preg_replace("/^www\./Ui", '', $temp_parse_url['host'])) : 'unknown';

			if (isset($referrer_sites["$temp_parse_url"]))
			{
				$referrer_sites["$temp_parse_url"] += $referrers[$a]['num'];
			}
			else
			{
				$referrer_sites["$temp_parse_url"] = $referrers[$a]['num'];
			}
		}

		array_multisort($referrer_sites, SORT_DESC, SORT_NUMERIC);
		reset($referrer_sites);

		echo '<table border="0" cellspacing="0" cellpadding="0">'."\n";
		foreach ($referrer_sites as $site => $site_count)
		{
			$site_esc = $this->htmlspecialchars_ent($site);
			echo '<tr>'."\n";
			echo '<td width="30" align="right" valign="top" style="padding-right: 10px">'.$site_count.'</td>'."\n";
			echo '<td valign="top">'.(($site != 'unknown') ? '<a href="http://'.$site_esc.'">'.$site_esc.'</a>' : $site).'</a> '.($IsAdmin ? '[<a href="'.$this->href('delete_referrer', '', 'spam_site=').$site_esc.'&amp;redirect='.$this->GetHandler().'">Blacklist</a>]' : '').'</td>'."\n";
			echo '</tr>'."\n";
		}
		echo '</table>'."\n";
	}
	else
	{
		echo '<em class="error">None</em><br />'."\n";
	}
} else {
	echo '<em class="error">You need to login to see referring sites</em><br />'."\n";
}

if ($global !== '')
{
	echo '<br />[<a href="'.$this->Href('referrers_sites').'">View referring sites for '.$this->GetPageTag().' only</a> | <a href="'.$this->Href('referrers').'">View referrers for '.$this->GetPageTag().' only</a> | <a href="'.$this->Href('review_blacklist').'">View referrer blacklist</a>]'."\n";
}
else
{
	echo '<br />[<a href="'.$this->Href('referrers_sites', '', 'global=1').'">View global referring sites</a> | <a href="'.$this->Href('referrers', '', 'global=1').'">View global referrers</a> | <a href="'.$this->Href('review_blacklist').'">View referrer blacklist</a>]'."\n";
}

echo '</div>'."\n" //TODO: move to templating class
?>
