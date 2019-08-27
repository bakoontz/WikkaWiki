<?php
// convert inline csv data into a table.
// by OnegWR, may 2005, license GPL
// http://wikkawiki.org/OnegWRCsv

// Copy the code below into a file named formatters/csv.php
// And give it the same file permissions as the other files in that directory.

print "<table><tbody>\n";
foreach (split("\n", $text) as $csv_n => $csv_line) 
{
	if (preg_match("/^#|^\s*$/",$csv_line)) 
		continue;

	print ($csv_n%2) ? "<tr bgcolor=\"#ffffee\">" : "<tr bgcolor=\"#eeeeee\">";

	foreach (split(";", $csv_line) as $csv_nn => $csv_cell) 
	{
#		https://www.phpliveregex.com

		if (preg_match("/^\"?[\s]*==.*==\"?$/", $csv_cell)) 
		{
			$style[$csv_nn]= "padding: 1px 10px 1px 10px; ";

			if (preg_match("/^\"?[\s]*==\/(.*)\/==\"?$/", $csv_cell, $title))
				$style[$csv_nn].= "text-align:right; ";
			elseif (preg_match("/^\"?[\s]*==\\\\(.*)\\\\==\"?$/", $csv_cell, $title))
				$style[$csv_nn].= "text-align:left; ";
			elseif (preg_match("/^\"?[\s]*==\|(.*)\|==\"?$/", $csv_cell, $title))
				$style[$csv_nn].= "text-align:center; ";

			if (preg_match("/^\"?[\s]*==.@@TOTAL@@.==\"?$/", $csv_cell))
				print "<th style=\"background-color:#ccc;". $style[$csv_nn] ."\">". ($total_i[$csv_nn] + ($total_d[$csv_nn]/100)) ."</th>";
			else {
				$total_i[$csv_nn]= 0;
				$total_d[$csv_nn]= 0;
				print "<th style=\"background-color:#ccc;". $style[$csv_nn] ."\">". $this->htmlspecialchars_ent($title[1]) ."</th>";
			}

			continue;
		}

		if (preg_match("/^\s*$/",$csv_cell))
			print "<td>&nbsp;</td>";
		elseif (preg_match("/^\"?[\s\d+\-,.]+\"?$/", $csv_cell))
		{
			$csv_cell_nows= preg_replace('/\s+/', '', $csv_cell);

			if (preg_match("/^\"?([+-]?)(\d{1,3}(\.\d{3})*|(\d+)),(\d{2})$\"?$/", $csv_cell_nows, $swe))
			{
				$format= "SE";
				$i= $swe[1] . preg_replace('/\./', '', $swe[2]);
				$d= $swe[1] . $swe[5];
			}
			elseif (preg_match("/^\"?([+-]?)(\d{1,3}(\,\d{3})*|(\d+))(\.(\d{2}))?$\"?$/", $csv_cell_nows, $usa))
			{
				$format= "US";
				$i= $usa[1] . preg_replace('/,/', '', $usa[2]);
				$d= $usa[1] . $usa[5];
			}
			else
			{
				$format= "??";
				$i= "0";
				$d= "0";
			}

			$total_i[$csv_nn]+= intval($i);
			$total_d[$csv_nn]+= intval($d);
			$nr= $i + ($d/100);
			print "<td style=\"". (($nr <= 0) ? "background-color:#d20; " : "" ) . $style[$csv_nn] ."\">". sprintf("%.2f", $nr) ."</td>";
			print "<td style=\"text-align:right;\">". $csv_cell ."(". $format .") &nbsp;". $total_i[$csv_nn] ." ". $total_d[$csv_nn] ."</td>";
		}
		elseif (preg_match("/^\"?(.*)\"?$/", $csv_cell, $matches))
			print "<td style=\"". $style[$csv_nn] ."\">". $this->htmlspecialchars_ent($matches[1]) ."</td>";
		
		else
			print "<td>".$this->htmlspecialchars_ent($csv_cell)."</td>";
	}
	print "</tr>\n";
}
print "</tbody></table>\n";
?>

