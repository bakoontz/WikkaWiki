<?php
/**
 * Display a list of newly registered users.
 *
 * @package		Actions
 * @version		$Id:lastusers.php 369 2007-03-01 14:38:59Z DarTar $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	Wakka::LoadAll()
 * @uses	Wakka::getCount()
 * @uses	Wakka::Link()
 *
 * @todo	document usage and parameters
 * @todo	needs to be rewritten following coding guidelines;
 * @todo	use constants instead of "magic numbers"
 * @todo	use standard way to fetch and validate action parameters
 */
if (!isset($stat)) $stat = 0;
if (!isset($max)) $max = 0;

if ($stat===0) $limit = 1000;
else $limit = 100;

if (!$max || $limit<$max)
  $max = $limit;

// @@@ reformat query
$last_users = $this->LoadAll("SELECT name, signuptime FROM ".$this->GetConfigValue('table_prefix')."users ORDER BY signuptime DESC LIMIT ".(int) $max);

$htmlout = '<table class="wikka">'."\n".
  "<caption>".LASTUSERS_CAPTION."</caption>"."\n".
  "  <tr>\n".
	"    <th>".NAME_TH."</th>\n".
	"    <th>".OWNED_PAGES_TH."</th>\n".
	"    <th>".SIGNUP_DATE_TIME_TH."</th>\n".
  "  </tr>\n";

foreach($last_users as $user)
{
	$htmlout .= "  <tr>\n";
	if (0 !== $stat)
	{
		$where = "`owner` = '".mysql_real_escape_string($user['name'])."' AND `latest` = 'Y'";
		$htmlout .= "    <td>".$this->Link($user['name'])."</td>\n    <td>"." . . . . . (".$this->getCount('pages', $where).")"."</td>\n    <td>(".$user['signuptime'].")</td>\n";
	}
	else
	{
		$htmlout .= "    <td>".$this->Link($user['name'])."</td>\n    <td></td>\n    <td>(".$user['signuptime'].")</td>\n";
	}
	$htmlout .= "  </tr>\n";
}

$htmlout .= "</table>\n";

print($htmlout);

?>
