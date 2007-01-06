<?php
/**
 * Delete a referrer and add it to the blacklist.
 *
 * @package         Handlers
 * @subpackage        Referrers
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses	Wakka::Href()
 * @uses	Wakka::Query()
 * @uses	Wakka::Redirect()
 */

if (isset($_REQUEST['spam_link']))
{
	$parsed_url = parse_url($_REQUEST['spam_link']);
	$spammer = isset($parsed_url['host']) ? $parsed_url['host'] : '';
}
elseif (isset($_REQUEST['spam_site']))
{
	$spammer = $_REQUEST['spam_site'];
}

if (isset($spammer) && $spammer)
{
	$this->Query("DELETE FROM ".$this->config["table_prefix"]."referrers WHERE referrer like '%".mysql_real_escape_string($spammer)."%'");
	if (!$already_blacklisted = $this->LoadSingle("select * from ".$this->config["table_prefix"]."referrer_blacklist WHERE spammer = '".mysql_real_escape_string($spammer)."'"))
		$this->Query("INSERT INTO ".$this->config["table_prefix"]."referrer_blacklist SET spammer = '".mysql_real_escape_string($spammer)."'");
}

// Redirect to last page
$redirect = isset($_REQUEST['redirect']) ? $_REQUEST['redirect'] : '';
$this->Redirect($this->Href($redirect));

?>
