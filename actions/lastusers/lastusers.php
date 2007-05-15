<?php
/**
 * Display a list of newly registered users.
 * 
 * @package		Actions
 * @version		$Id:lastusers.php 369 2007-03-01 14:38:59Z DarTar $
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses	Wakka::LoadAll()
 * @uses	Wakka::LoadSingle()
 * @uses	Wakka::Link()
 * 
 * @todo	needs to be rewritten following coding guidelines;
 */

if ($stat===0) $limit = 1000;
else $limit = 100;

if (!$max || $limit<$max)
  $max = $limit;

$last_users = $this->LoadAll("SELECT name, signuptime FROM ".$this->config["table_prefix"]."users ORDER BY signuptime DESC LIMIT ".(int)$max);

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
 if ($stat!=="0") $num = $this->LoadSingle("SELECT COUNT(*) AS n FROM ".$this->config["table_prefix"]."pages WHERE owner='".$user["name"]."' AND latest = 'Y'");
 $htmlout .= "    <td>".$this->Link($user["name"])."</td>\n    <td style=\"text-align: right;\">".(($stat !== "0")? $num['n'] : '')."</td>\n    <td><tt>".$user["signuptime"]."</tt></td>\n";
 $htmlout .= "  </tr>\n";
}

$htmlout .= "</table>\n";

print($htmlout);

?>