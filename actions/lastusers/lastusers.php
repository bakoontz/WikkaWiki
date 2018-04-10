<?php
/**
 * Display a table of recently registered users.
 *
 * @package		Actions
 * @version		$Id: lastusers.php 1232 2008-09-17 20:46:30Z DarTar $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	Wakka::LoadAll()
 * @uses	Wakka::getCount()
 * @uses	Wakka::GetConfigValue()
 * @uses	Wakka::FormatUser()
 *
 * @input		integer  $max  optional: number of rows to be displayed;
 *				default: 10
 * @input		string  $style  optional (simple|complex): displays a simple table or a table with caption and headers and statistics on the number of pages owned;
 *				default: "complex"
 * @output		a table with the last registered users
 * 
 * @todo	document usage and parameters
 */
 
//defaults
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
	$style = T_("complex");	
}
if (isset($vars['max']) && is_numeric($vars['max']) && $vars['max'] > 0)
{
	$max = (int) $vars['max'];
}
else
{
	$max = LASTUSERS_MAX_USERS_DISPLAY;	
}

// @@@TODO reformat query
$last_users = $this->LoadAll("SELECT name, signuptime FROM ".$this->GetConfigValue('table_prefix')."users ORDER BY signuptime DESC LIMIT :max", array(':max' => $max));

$htmlout .= '<table class="data lastusers">'."\n";
if ($style == 'complex')
{
	$htmlout .= '<caption>'.T_("Recently registered users").'</caption>'."\n";
	$htmlout .= '  <tr>'."\n";
	$htmlout .= '    <th>'.T_("Username").'</th>'."\n";
	$htmlout .= '    <th>'.T_("Owned pages").'</th>'."\n";
	$htmlout .= '    <th>'.T_("Signup date/time").'</th>'."\n";
	$htmlout .= '  </tr>'."\n";
}
foreach($last_users as $user)
{
	$htmlout .= '  <tr>'."\n";
	if ($style == 'complex')
	{
		$where = "`owner` = :name AND `latest` = 'Y'";
		$htmlout .= '    <td>'.$this->FormatUser($user['name']).'</td>'."\n";
		$htmlout .= '    <td class="number">'.$this->getCount('pages', $where, array(':name' => $user['name'])).'</td>'."\n";
		$htmlout .= '    <td class="datetime">('.$user['signuptime'].')</td>'."\n";
	}
	else
	{
		$htmlout .= '    <td>'.$this->FormatUser($user['name']).'</td>'."\n";
		$htmlout .= '    <td class="datetime">'.$user['signuptime'].'</td>'."\n";
	}
	$htmlout .= "  </tr>\n";
}

$htmlout .= '</table>'."\n";
echo $htmlout;
?>
