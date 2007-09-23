<?php
// ***** CONSTANTS section *****
define('NAME', "Name");
define('OWNED_PAGES', "Owned Pages");
define('SIGNUP_DATE_TIME', "Signup Date/Time");
// ***** END CONSTANTS section *****

if ($stat===0) $limit = 1000;
else $limit = 100;

if (!$max || $limit<$max)
  $max = $limit;

$last_users = $this->LoadAll("select name, signuptime from ".$this->config["table_prefix"]."users order by signuptime desc limit ".(int)$max);

$htmlout = "<table width=\"50%\" border=\"0\" cellpadding=\"3%\">\n".
  "  <tr>\n".
    "    <th><u>".NAME."</u></th>\n".
    "    <th><u>".OWNED_PAGES."</u></th>\n".
    "    <th><u>".SIGNUP_DATE_TIME."</u></th>\n".
  "  </tr>\n";

foreach($last_users as $user)
{
 $htmlout .= "  <tr>\n";
 if ($stat!=="0") $num = $this->LoadSingle("select count(*) as n from ".$this->config["table_prefix"]."pages where owner='".$user["name"]."' AND latest = 'Y'");
 $htmlout .= "    <td>".$this->Link($user["name"])."</td>\n    <td>".($stat!=="0"?" . . . . . (".$num["n"].")":"")."</td>\n    <td>(".$user["signuptime"].")</td>\n";
 $htmlout .= "  </tr>\n";
}

$htmlout .= "</table>\n";

print($htmlout);

?>