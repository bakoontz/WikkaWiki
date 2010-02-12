<?php
/**
 * Generate a RSS 2.0 feed of the revisions of the current page.
 *
 * @package		Handlers
 * @subpackage	XML
 * @version		$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses		Config::$wakka_name
 * @uses		Config::$base_url
 * @uses		Wakka::GetConfigValue()
 * @uses		Wakka::HasAccess()
 * @uses		Wakka::LoadRevisions()
 * @uses		Wakka::Href()
 * @uses		Wakka::htmlspecialchars_ent()
 */

header('Content-type: text/xml');

$xml = '<?xml version="1.0" encoding="'.I18N_ENCODING_UTF8.'"?>'."\n";
$xml .= '<rss version="'.RSS_REVISIONS_VERSION.'">'."\n";
$xml .= "<channel>\n";
$xml .= '<title>'.$this->GetConfigValue('wakka_name').' - '.$this->tag."</title>\n";
$xml .= '<link>'.$this->Href()."</link>\n";
$xml .= '<description>'.sprintf(HISTORY_REVISIONS_OF, $this->GetConfigValue('wakka_name').'/'.$this->tag)."</description>\n";
$xml .= '<language>'.I18N_LANG."</language>\n";

if ($this->HasAccess('read'))
{
	// load revisions for this page
	if ($pages = $this->LoadRevisions($this->tag))
	{
		$max = 20;

		$c = 0;
		foreach ($pages as $page)
		{
			$c++;
			if (($c <= $max) || !$max)
			{
				$xml .= "<item>\n";
				$xml .= '<title>'.$page['time']."</title>\n";
				$xml .= '<link>'.$this->Href('show', '', 'time='.urlencode($page['time'])).'</link>'."\n";
				$xml .= '<description>'.sprintf(REVISIONS_EDITED_BY, $this->htmlspecialchars_ent($page['user'],ENT_COMPAT,'XML')).($page['note'] ? ' - '.$this->htmlspecialchars_ent($page['note'],ENT_COMPAT,'XML') : '')."</description>\n";
				$xml .= "\t".'<pubDate>'.date('r',strtotime($page['time']))."</pubDate>\n";
				$xml .= "</item>\n";
			}
		}
	}
}
else
{
	$xml .= "<item>\n";
	$xml .= '<title>'.WIKKA_ERROR_CAPTION.'</title>'."\n";
	$xml .= '<link>'.$this->Href()."</link>\n";
	$xml .= '<description>'.WIKKA_ERROR_ACL_READ_INFO."</description>\n";
	$xml .= "</item>\n";
}

$xml .= "</channel>\n";
$xml .= "</rss>\n";

print($xml);

?>
