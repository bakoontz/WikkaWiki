<?php
/**
 * Generate a RSS 2.0 feed of the revisions of the current page.
 * 
 * @package		Handlers
 * @subpackage	XML
 * @version		$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
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

/**
 * Defaults
 */
if (!defined('I18N_LANG')) define('I18N_LANG', 'en-US');
if (!defined('I18N_ENCODING_UTF8')) define('I18N_ENCODING_UTF8', 'UTF-8');
if (!defined('RSS_REVISIONS_VERSION')) define('RSS_REVISIONS_VERSION','2.0');
if (!defined('RSS_RECENTCHANGES_VERSION')) define('RSS_RECENTCHANGES_VERSION','0.92');
if (!defined('REVISIONS_EDITED_BY')) define('REVISIONS_EDITED_BY','Edited by %s');

/**
 * i18n
 */
define('EDITED_BY', 'Edited by %s');
define('ERROR_ACL_READ_INFO', 'You\'re not allowed to access this information.');
define('HISTORY_REVISIONS_OF', 'History/revisions of %s');

header("Content-type: text/xml");

$xml = '<?xml version="1.0" encoding="'.I18N_ENCODING_UTF8.'"?>'."\n";
$xml .= '<rss version="'.RSS_REVISIONS_VERSION.'">'."\n";
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
				$xml .= '<description>'.sprintf(REVISIONS_EDITED_BY, $this->htmlspecialchars_ent($page['user'],ENT_COMPAT,'XML')).($page['note'] ? ' - '.$this->htmlspecialchars_ent($page['note'],ENT_COMPAT,'XML') : '')."</description>\n";
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
