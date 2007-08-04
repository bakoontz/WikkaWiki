<?php
/**
 * Displays a list of users, ranked after their ownership of pages in the wiki.
 * 
 * @package		Actions
 * @version		$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author	Chris Tessmer (19 Dec 2002)
 * 
 * @uses	Wakka::Query()
 * @uses	Wakka::getCount()
 * @uses	Wakka::Format()
 */

// @@@ reformat query
$str = 'SELECT COUNT(*) AS cnt, `name`  FROM ';
$str .= $this->GetConfigValue('table_prefix').'users, ' ;
$str .= $this->GetConfigValue('table_prefix').'pages ';
$str .= "WHERE `name` = `owner` AND `latest` = 'Y' GROUP BY name ORDER BY cnt DESC;";
$rankQuery = $this->Query($str);

$where = "`latest` = 'Y'";
$total = $this->getCount('pages', $where);

// @@@ replace blockqoute by a style !!!
print("<blockquote><table>");

$i = 0;
while($row = mysql_fetch_array($rankQuery))
{ 
    $i++;
    $str = '<tr>';
    $str .= "<td>$i.&nbsp;</td>";
    $str .= '<td>'.$this->Link($row["name"]).'</td>';
    $str .= '<td> </td>';
    $str .= '<td> &nbsp;&nbsp;&nbsp;</td>';
    $str .= '<td>'.$row["cnt"].'</td>';
    $str .= '<td> &nbsp;&nbsp;&nbsp;</td>';
    $str .= '<td>'.round(($row["cnt"]/$total)*100, 2).'% </td>';
    $str .= '</tr>';
    print($str);
}
print("</table></blockquote>");
?>