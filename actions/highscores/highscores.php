<?php
/**
 * Displays a list of top contributors, ranked by number of pages owned, edits or comments.
 *
 * @package		Actions
 * @version		$Id: highscores.php 820 2007-11-23 09:21:08Z DotMG $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author	Chris Tessmer (original code)
 * @author	{@link http://wikkawiki.org/DarTar Dario Taraborelli} (adding action parmeters, styling, accessibility)
 * 
 * @since Wikka 1.1.6.4
 * 
 * @input		integer  $top  optional: number of rows to be displayed;
 *				default: 10
 * @input		string  $rank  optional (edits|pages|comments): select the metric used to rank users
 * 				default: "pages"
 * @input		integer  $style  optional (simple|complex): displays a simple table or a table with caption and headers;
 *				default: "complex"
 * @output		a table with top wiki contributors ranked by the specified metric
 * 
 * @uses	Wakka::Query()
 * @uses	Wakka::Format()
 * @uses	Wakka::Link()
 * @uses	Wakka::GetConfigValue()
 * @uses	Wakka::getCount()
 * @uses	Wakka::FormatUser()
 * 
 * @todo translation strings for 1.1.7
 * @todo add paging functionality #679
 */

if(!defined('HIGHSCORES_DISPLAY_TOP')) define('HIGHSCORES_DISPLAY_TOP', 10); //limit output to top n users

//Initialisation (avoid notices)
$table = '';

//valid options
$valid_styles = array('complex','simple');
$valid_rank = array('edits','pages', 'comments');

//process action parameters

if (isset($top) && is_numeric($top))
{
	$limit = intval($top);
}
else
{
	$limit = HIGHSCORES_DISPLAY_TOP;
}

if (!isset($style) || !in_array($style, $valid_styles))
{
	$style = T_("complex");
}

if (!isset($rank) || !in_array($rank, $valid_rank))
{
	$rank = T_("pages");
}

switch($rank)
{
	case 'edits':	
	$label= T_("edits");
	$query = 'SELECT COUNT(*) AS cnt, `name`  FROM '.$this->GetConfigValue('table_prefix').'users, '.$this->GetConfigValue('table_prefix').'pages WHERE `name` = `user` GROUP BY name ORDER BY cnt DESC LIMIT '.$limit;
	$total = $this->getCount('pages');
	break;
		
	case 'comments':
	$label= T_("comments");
	$query = 'SELECT COUNT(*) AS cnt, `name`  FROM '.$this->GetConfigValue('table_prefix').'users, '.$this->GetConfigValue('table_prefix').'comments WHERE `name` = `user` GROUP BY name ORDER BY cnt DESC LIMIT '.$limit;	
	$total = $this->getCount('comments');
	break;	

	default:
	case 'pages': 
	$label= T_("pages owned");
	$query = 'SELECT COUNT(*) AS cnt, `name`  FROM '.$this->GetConfigValue('table_prefix').'users, '.$this->GetConfigValue('table_prefix').'pages WHERE `name` = `owner` AND `latest` = "Y" GROUP BY name ORDER BY cnt DESC LIMIT '.$limit;	
	$total = $this->getCount('pages', "`latest` = 'Y'");
	break;
}

//fetch data
$rank_query = $this->Query($query)->fetchAll();

$i = 0;
$str = '';
foreach($rank_query as $row)
{
	$i++;
	$str .= '	<tr '.(($i % 2)? '' : 'class="alt"').'>'."\n";
	$str .= '		<td>'.$i.'.&nbsp;</td>'."\n";
	$str .= '		<td>'.$this->FormatUser($row['name']).'</td>'."\n";
	$str .= '		<td class="number">'.$row['cnt'].'</td>'."\n";
	$str .= '		<td class="number">'.round(($row['cnt']/$total)*100, 1).'% </td>'."\n";
	$str .= '	</tr>'."\n";
}

$display_items = ($i > 1)? $i : '';

//output table
$table .= '<table class="data highscores">'."\n";

//display caption and headers for complex style
if ($style == 'complex')
{
	$table .= '	<caption>'.sprintf(T_("Top %s contributor(s)"), $display_items, $label).'</caption>'."\n";
	$table .= '	<thead>'."\n";
	$table .= '	<tr>'."\n";
	$table .= '		<th scope="col">'.T_("rank").'</th>'."\n";
	$table .= '		<th scope="col">'.T_("user").'</th>'."\n";
	$table .= '		<th scope="col">'.$label.'</th>'."\n";
	$table .= '		<th scope="col">'.T_("percentage").'</th>'."\n";
	$table .= '	</tr>'."\n";
	$table .= '	</thead>'."\n";
	$table .= '	<tbody>'."\n";
}
$table .= $str;
if ($style == 'complex')
{
	$table .= '	</tbody>'."\n";
}

$table .= '</table>'."\n";

echo $table;
?>
