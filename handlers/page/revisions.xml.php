<?php
// i18n strings
define('EDITED_BY', 'Edited by %s');
define('ERROR_ACL_READ_INFO', 'You\'re not allowed to access this information.');
define('HISTORY_REVISIONS_OF', 'History/revisions of %s');
if (!defined('I18N_LANG')) define('I18N_LANG', 'en-us');
if (!defined('I18N_CHARSET')) define('I18N_CHARSET', 'utf-8');

header("Content-type: text/xml");

$xml = '<?xml version="1.0" encoding="'.I18N_CHARSET.'"?>'."\n";
$xml .= "<rss version=\"2.0\">\n";
$xml .= "<channel>\n";
$xml .= "<title>".$this->GetConfigValue("wakka_name")." - ".$this->tag."</title>\n";
$xml .= "<link>".$this->GetConfigValue("base_url").$this->tag."</link>\n";
$xml .= '<description>'.sprintf(HISTORY_REVISIONS_OF, $this->GetConfigValue('wakka_name').'/'.$this->tag)."</description>\n";
$xml .= '<language>'.I18N_LANG."</language>\n";

if ($this->HasAccess("read"))
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
				$xml .= "<title>".$page["time"]."</title>\n";
				$xml .= '<link>'.$this->Href('show', '', 'time=.'.urlencode($page['time'])).'</link>'."\n";
				$xml .= '<description>'.sprintf(EDITED_BY, $this->htmlspecialchars_ent($page["user"]))." - ".$this->htmlspecialchars_ent($page["note"], '', '', 'XML')."</description>\n";
				$xml .= "\t<pubDate>".date("r",strtotime($page["time"]))."</pubDate>\n";
				$xml .= "</item>\n";
			}
		}
		$output .= "</table>".$this->FormClose()."\n";
	}
}
else
{
	$xml .= "<item>\n";
	$xml .= "<title>Error</title>\n";
	$xml .= "<link>".$this->Href("show")."</link>\n";
	$xml .= '<description>'.ERROR_ACL_READ_INFO."</description>\n";
	$xml .= "</item>\n";
}

$xml .= "</channel>\n";
$xml .= "</rss>\n";

print($xml);

?>
