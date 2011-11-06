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
 * @uses		Wakka::GetConfigValue()
 * @uses		Wakka::HasAccess()
 * @uses		Wakka::LoadRevisions()
 * @uses		Wakka::Href()
 * @uses		Wakka::htmlspecialchars_ent()
 */

header('Content-type: text/xml');

$xml = '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
$xml .= '<rss version="2.0">'."\n";
$xml .= "<channel>\n";
$xml .= '<title>'.$this->GetConfigValue('wakka_name').' - '.$this->tag."</title>\n";
$xml .= '<link>'.$this->Href()."</link>\n";
$xml .= '<description>'.sprintf(T_("History/revisions of %s"), $this->GetConfigValue('wakka_name').'/'.$this->tag)."</description>\n";
$xml .= '<language>'.T_("en-US")."</language>\n";

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
				$xml .= '<description>'.sprintf(T_("Edited by %s"), $this->htmlspecialchars_ent($page['user'],ENT_COMPAT,'XML')).($page['note'] ? ' - '.$this->htmlspecialchars_ent($page['note'],ENT_COMPAT,'XML') : '')."</description>\n";
				$xml .= "\t".'<pubDate>'.date('r',strtotime($page['time']))."</pubDate>\n";
				$xml .= "</item>\n";
			}
		}
	}
}
else
{
	$xml .= "<item>\n";
	$xml .= '<title>'.T_("Error").'</title>'."\n";
	$xml .= '<link>'.$this->Href()."</link>\n";
	$xml .= '<description>'.T_("You are not allowed to access this information.")."</description>\n";
	$xml .= "</item>\n";
}

$xml .= "</channel>\n";
$xml .= "</rss>\n";

print($xml);

?>
