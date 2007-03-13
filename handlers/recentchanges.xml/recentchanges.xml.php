<?php
/**
 * Generate a feed with recent changes in the wiki.
 * 
 * @package		Handlers
 * @subpackage	XML	
 * @version		$Id: recentchanges.xml.php 325 2007-02-21 11:42:05Z JavaWoman $
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

/**
 * Defaults
 */
if (!defined('I18N_LANG')) define('I18N_LANG', 'en-US');
if (!defined('I18N_ENCODING_88591')) define('I18N_ENCODING_88591', 'ISO-8859-1');
if (!defined('RSS_REVISIONS_VERSION')) define('RSS_REVISIONS_VERSION','2.0');
if (!defined('RSS_RECENTCHANGES_VERSION')) define('RSS_RECENTCHANGES_VERSION','0.92');

header("Content-type: text/xml");

$xml  = '<?xml version="1.0" encoding="'.I18N_ENCODING_88591.'"?>'."\n";
$xml .= '<?xml-stylesheet href="' . $this->GetConfigValue("base_url") .'/css/xml.css" type="text/css"?' .">\n";
$xml .= '<rss version="'.RSS_RECENTCHANGES_VERSION.'">'."\n";
$xml .= "<channel>\n";
$xml .= "<title>".$this->GetConfigValue("wakka_name")." - ".$this->tag."</title>\n";
$xml .= "<link>".$this->GetConfigValue("base_url")."</link>\n";
$xml .= sprintf('<description>'.RECENTCHANGES_DESC."</description>\n", $this->GetConfigValue("wakka_name"));
$xml .= '<language>'.I18N_LANG."</language>\n";

if ($pages = $this->LoadRecentlyChanged())
{
	$max = $this->GetConfigValue("xml_recent_changes");

	$c = 0;
	foreach ($pages as $page)
	{
		$c++;
		if (($c <= $max) || !$max)
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
	$xml .= '<title>'.WIKKA_ERROR_CAPTION."</title>\n";
	$xml .= "<link>".$this->Href("show")."</link>\n";
	$xml .= '<description>'.WIKKA_ERROR_ACL_READ_INFO."</description>\n";
	$xml .= "</item>\n";
}

$xml .= "</channel>\n";
$xml .= "</rss>\n";

print($xml);

?> 
