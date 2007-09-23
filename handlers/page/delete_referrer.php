<?php
/**
 * Delete a referrer and add it to the blacklist.
 *
 * @package		Handlers
 * @subpackage	Referrers
 * @version		$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses	Wakka::Href()
 * @uses	Wakka::Query()
 * @uses	Wakka::Redirect()
 */

#if (isset($_REQUEST['spam_link']))
if (isset($_GET['spam_link']))			// coming from referrers handler #312
{
	#$parsed_url = parse_url($_REQUEST['spam_link']);
	$parsed_url = parse_url($_GET['spam_link']); #312
	$spammer = isset($parsed_url['host']) ? $parsed_url['host'] : '';
}
#elseif (isset($_REQUEST['spam_site']))
elseif (isset($_GET['spam_site']))		// coming from referrers_sites handler #312
{
	#$spammer = $_REQUEST['spam_site'];
	$spammer = $_GET['spam_site']; #312
}

if (isset($spammer) && $spammer)
{
	$this->Query("DELETE FROM ".$this->config["table_prefix"]."referrers WHERE referrer like '%".mysql_real_escape_string($spammer)."%'");
	if (!$already_blacklisted = $this->LoadSingle("select * from ".$this->config["table_prefix"]."referrer_blacklist WHERE spammer = '".mysql_real_escape_string($spammer)."'"))
	{
		$this->Query("INSERT INTO ".$this->config["table_prefix"]."referrer_blacklist SET spammer = '".mysql_real_escape_string($spammer)."'");
	}
}

// Redirect back to original page/handler
#$redirect = isset($_REQUEST['redirect']) ? $_REQUEST['redirect'] : '';
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : ''; #312
$this->Redirect($this->Href($redirect));
?>