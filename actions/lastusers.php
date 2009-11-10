<?php
/**
 * Display a table of recently registered users.
 *
 * @package		Actions
 * @version		$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	Wakka::LoadAll()
 * @uses	Wakka::getCount()
 * @uses	Wakka::GetConfigValue()
 *
 * @input		integer  $max  optional: number of rows to be displayed;
 *				default: 10
 * @input		string  $style  optional (simple|complex): displays a simple table or a table with caption and headers and statistics on the number of pages owned;
 *				default: "complex"
 * @output		a table with the last registered users
 * 
 * @todo	document usage and parameters
 */
 
//i18n
if (!defined('LASTUSERS_CAPTION')) define('LASTUSERS_CAPTION', 'Recently registered users');
if (!defined('SIGNUP_DATE_TIME')) define('SIGNUP_DATE_TIME', 'Signup Date/Time');
if (!defined('NAME_TH')) define('NAME_TH', 'Username');
if (!defined('OWNED_PAGES_TH')) define('OWNED_PAGES_TH', 'Owned pages');
if (!defined('SIGNUP_DATE_TIME_TH')) define('SIGNUP_DATE_TIME_TH', 'Signup date/time');

//defaults
if (!defined('LASTUSERS_DEFAULT_STYLE')) define('LASTUSERS_DEFAULT_STYLE', 'complex'); # consistent parameter naming with HighScores action
if (!defined('LASTUSERS_MAX_USERS_DISPLAY')) define('LASTUSERS_MAX_USERS_DISPLAY', 10);

//initialize
$htmlout = '';
$style = '';
$max = '';

//validate action parameters
if (isset($vars['style']) && in_array($vars['style'], array('complex','simple')))
{
	$style = $vars['style'];
}
else
{
	$style = LASTUSERS_DEFAULT_STYLE;	
}
if (isset($vars['max']) && $vars['max'] > 0)
{
	$max = (int) $vars['max'];
}
else
{
	$max = LASTUSERS_MAX_USERS_DISPLAY;	
}

// @@@TODO reformat query
$last_users = $this->LoadAll("SELECT name, signuptime FROM ".$this->GetConfigValue('table_prefix')."users ORDER BY signuptime DESC LIMIT ".$max);

$htmlout .= '<table class="data lastusers">'."\n";
if ($style == 'complex')
{
	$htmlout .= '<caption>'.LASTUSERS_CAPTION.'</caption>'."\n";
	$htmlout .= '  <tr>'."\n";
	$htmlout .= '    <th>'.NAME_TH.'</th>'."\n";
	$htmlout .= '    <th>'.OWNED_PAGES_TH.'</th>'."\n";
	$htmlout .= '    <th>'.SIGNUP_DATE_TIME_TH.'</th>'."\n";
	$htmlout .= '  </tr>'."\n";
}
foreach($last_users as $user)
{
	$htmlout .= '  <tr>'."\n";
	if ($style == 'complex')
	{
		$where = "`owner` = '".mysql_real_escape_string($user['name'])."' AND `latest` = 'Y'";
		$htmlout .= '    <td>'.$user['name'].'</td>'."\n";
		$htmlout .= '    <td class="number">'.$this->getCount('pages', $where).'</td>'."\n";
		$htmlout .= '    <td class="datetime">('.$user['signuptime'].')</td>'."\n";
	}
	else
	{
		$htmlout .= '    <td>'.$user['name'].'</td>'."\n";
		$htmlout .= '    <td class="datetime">'.$user['signuptime'].'</td>'."\n";
	}
	$htmlout .= "  </tr>\n";
}

$htmlout .= '</table>'."\n";
echo $htmlout;
?>