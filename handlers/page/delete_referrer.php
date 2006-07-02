<?php
/**
 * Delete a referrer and add it to the blacklist.
 *
 * @package         Handlers
 * @subpackage        Referrers
 * @name              delete_referrer.php
 * @version $Id$
 * 
 * @uses	Wakka::Href()
 * @uses	Wakka::Query()
 * @uses	Wakka::redirect()
 */

if ($spam_link = $_REQUEST["spam_link"])
{
	$parsed_url = parse_url($spam_link);
	$spammer = $parsed_url["host"];

} else {
	$spammer = $_REQUEST["spam_site"];
}

if ($spammer) {
	$this->Query("DELETE FROM ".$this->config["table_prefix"]."referrers WHERE referrer like '%".mysql_real_escape_string($spammer)."%'");
	if (!$already_blacklisted = $this->LoadSingle("select * from ".$this->config["table_prefix"]."referrer_blacklist WHERE spammer = '".mysql_real_escape_string($spammer)."'"))
		$this->Query("INSERT INTO ".$this->config["table_prefix"]."referrer_blacklist SET spammer = '".mysql_real_escape_string($spammer)."'");
}

// Redirect to last page
$redirect = $_REQUEST["redirect"];
$this->redirect($this->Href($redirect));

?>