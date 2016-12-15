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

if (isset($_GET['spam_link']))			// coming from referrers handler #312
{
	$parsed_url = parse_url($this->GetSafeVar('spam_link', 'get')); # #312
	$spammer = isset($parsed_url['host']) ? $parsed_url['host'] : '';
}
elseif (isset($_GET['spam_site']))		// coming from referrers_sites handler #312
{
	$spammer = $this->GetSafeVar('spam_site', 'get'); # #312
}

if (isset($spammer) && $spammer)
{
	$like_spammer = '%'.$spammer.'%';
	$this->Query("DELETE FROM ".$this->GetConfigValue('table_prefix')."referrers WHERE referrer LIKE :spammer", array(':spammer' => $like_spammer));
	if (!$already_blacklisted = $this->LoadSingle("SELECT * from ".$this->GetConfigValue('table_prefix')."referrer_blacklist WHERE spammer = :spammer", array(':spammer' => $spammer)))
	{
		$this->Query("INSERT INTO ".$this->GetConfigValue('table_prefix')."referrer_blacklist SET spammer = :spammer", array(':spammer' => $spammer));
	}
}

// Redirect back to original page/handler
$redirect = $this->GetSafeVar('redirect', 'get'); # #312
$this->Redirect($this->Href($redirect));
?>
