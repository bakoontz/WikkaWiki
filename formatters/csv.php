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
		if (preg_match("/^\"?[\s]*==.*==\"?$/", $csv_cell)) 
		{
			$style[$csv_nn]= "padding: 1px 10px 1px 10px; ";

			if (preg_match("/^\"?[\s]*==\/(.*)\/==\"?$/", $csv_cell, $title))
				$style[$csv_nn].= "text-align:right; ";

			elseif (preg_match("/^\"?[\s]*==\\\\(.*)\\\\==\"?$/", $csv_cell, $title))
				$style[$csv_nn].= "text-align:left; ";

			elseif (preg_match("/^\"?[\s]*==\|(.*)\|==\"?$/", $csv_cell, $title))
				$style[$csv_nn].= "text-align:center; ";

			print "<th style=\"background-color:#ccc;". $style[$csv_nn] ."\">". $this->htmlspecialchars_ent($title[1]) ."</th>";
			continue;
		}

		if (preg_match("/^\s*$/",$csv_cell))
			print "<td>&nbsp;</td>";
		elseif (preg_match("/^\"?(.*)\"?$/", $csv_cell, $matches))
			print "<td style=\"". $style[$csv_nn] ."\">".$this->htmlspecialchars_ent($matches[1])."</td>";
		else
			print "<td>".$this->htmlspecialchars_ent($csv_cell)."</td>";
	}
	print "</tr>\n";
}
print "</tbody></table>\n";
?>

