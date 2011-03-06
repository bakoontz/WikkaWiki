<?php
/**
 * Display a data table.
 *
 * @package		Actions
 * @version		$Id: table.php 1196 2008-07-16 04:25:09Z BrianKoontz $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @input	string $cells mandatory: string with all cells, separated by ';' and '###' for empty cells
 * @input	integer $cellpadding optional: padding within cells in pixels; default: 1
 * @input	string $style optional: in-line style for the table.
 * @input	integer $columns optional: 	number of columns for the table; default: 1
 * @uses	Wakka::ReturnSafeHTML()
 * @todo		will be replaced after Wikka 1.1.6.2 by the table formatter
 */

// Init:
$delimiter=';';
$empty_cell='###';
$row=1;
$cellpadding=1;
$cellspacing=1;
$border=1;
$columns=1;
$style='';

if (is_array($vars))
{
	foreach ($vars as $param => $value)
	{
		$value = $this->htmlspecialchars_ent($value);
		if ($param == 'style') {$style=$value;}
		if ($param == 'columns') {$columns=$value;}
		if ($param == 'cellpadding')
		{
			$cellpadding = $value;
			$border = $value;
		}
		if ($param == 'cells') $cells = split($delimiter, $value);
	}
	$cached_output = '<table class="data" cellpadding="'.$cellpadding.'" cellspacing="'.$cellspacing.'" border="'.$border.'" style="'.$style."\">\n";
	foreach ($cells as $cell_item)
	{
		if ($row == 1) $cached_output .= "   <tr>\n";
		if ($cell_item==$empty_cell) $cell_item='<br />';
		$cached_output .= "       <td>".$cell_item."</td>\n";
		$row ++;
		if ($row > $columns)
		{
			$row = "1";
			$cached_output .= "   </tr>\n";
		}
	}
	$cached_output .= "</table>";
	echo $this->ReturnSafeHTML($cached_output);
}

?>