<?php
/**
 * Generate a feed with recent changes in the wiki.
 * 
 * @package		Handlers
 * @subpackage	XML	
 * @version		$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses	Config::$base_url
 * @uses	Config::$wakka_name
 * @uses	Config::$xml_recent_changes
 * @uses	Wakka::GetConfigValue()
 * @uses	Wakka::Href()
 * @uses	Wakka::htmlspecialchars_ent()
 * @uses	Wakka::LoadRecentlyChanged()
 */

// i18n strings
define('WIKKA_REV_WHEN_BY_WHO', '%1$s by %2$s');
define('ERROR_ACL_READ_INFO', 'You\'re not allowed to access this information.');
define('LABEL_ERROR', 'Error');
if (!defined('I18N_LANG')) define('I18N_LANG', 'en-us');

header("Content-type: text/xml");

$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
$xml .= '<?xml-stylesheet href="' . $this->GetConfigValue("base_url") .'/css/xml.css" type="text/css"?' .">\n";
$xml .= "<rss version=\"0.92\">\n";
$xml .= "<channel>\n";
$xml .= "<title>".$this->GetConfigValue("wakka_name")." - ".$this->tag."</title>\n";
$xml .= "<link>".$this->GetConfigValue("base_url")."</link>\n";
$xml .= "<description>Recent changes of ".$this->GetConfigValue("wakka_name")."</description>\n";
$xml .= '<language>'.I18N_LANG."</language>\n";

if ($pages = $this->LoadRecentlyChanged())
{
	$max = $this->GetConfigValue("xml_recent_changes");

	$c = 0;
	foreach ($pages as $page)
	{
		$c++;
		if (($this->HasAccess('read', $page['tag'])) && (($c <= $max) || !$max))
		{
			$xml .= "<item>\n";
			$xml .= "<title>".$this->htmlspecialchars_ent($page["tag"])."</title>\n";
			$xml .= "<link>".$this->Href("show", $page["tag"], "time=".urlencode($page["time"]))."</link>\n";
			$xml .= "\t<description>".sprintf(WIKKA_REV_WHEN_BY_WHO, $page['time'], $this->htmlspecialchars_ent($page['user'],ENT_COMPAT,'XML')).($page['note'] ? ' - '.$this->htmlspecialchars_ent($page['note'],ENT_COMPAT,'XML') : '')."</description>\n";
			//$xml .= "\t<guid>".$page["id"]."</guid>";
			$xml .= "\t<pubDate>".date("r",strtotime($page["time"]))."</pubDate>\n";
			$xml .= "</item>\n";
		}
	}
}
else
{
	$xml .= "<item>\n";
	$xml .= '<title>'.LABEL_ERROR."</title>\n";
	$xml .= "<link>".$this->Href("show")."</link>\n";
	$xml .= '<description>'.ERROR_ACL_READ_INFO."</description>\n";
	$xml .= "</item>\n";
}

$xml .= "</channel>\n";
$xml .= "</rss>\n";

print($xml);

?> 
