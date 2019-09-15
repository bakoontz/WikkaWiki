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

	foreach (preg_split("/(?<!\\\\);/", $csv_line) as $csv_nn => $csv_cell) 
	{
		// https://www.phpliveregex.com
		// https://www.regular-expressions.info/quickstart.html

		if (preg_match("/\"?\s*==(.*)==\"?$/", $csv_cell, $header)) 
		{
			if ($csv_n == 0)
				$style[$csv_nn]= "padding: 1px 10px 1px 10px; ";

			$title[$csv_nn]= $header[1];

			if (preg_match("/([\/\\\\|])(.*)\\1$/", $title[$csv_nn], $align)) 
			{
				switch ($align[1]) {
					case "/" :	$style[$csv_nn].= "text-align:right; ";	break;
					case "\\" :	$style[$csv_nn].= "text-align:left; ";	break;
					case "|" :	$style[$csv_nn].= "text-align:center; "; break;
				}

				$title[$csv_nn]= $align[2];
			}

			print "<th style=\"background-color:#ccc;". $style[$csv_nn] ."\">". $this->htmlspecialchars_ent($title[$csv_nn]) ."</th>";
			continue;
		}

		if (preg_match("/^\s*$/",$csv_cell))
			print "<td>&nbsp;</td>";
		elseif (preg_match("/^\"?(.*)\"?$/", $csv_cell, $matches))
		{
			$esc_semicolon= preg_replace('/\\\\;/', ';', $matches[1]);

			if (preg_match_all("/\[\[([[:alnum:]-]+)\]\]/", $esc_semicolon, $all_links))
			{
				$linked= $matches[1];
				
				foreach ($all_links[1] as $n => $camel_link) 
					$linked = preg_replace("/\[\[". $camel_link ."\]\]/", $this->Link($camel_link), $linked);

				print "<td style=\"". $style[$csv_nn] ."\">". $linked ."</td>";
			}		
			else
				print "<td style=\"". $style[$csv_nn] ."\">". $this->htmlspecialchars_ent($esc_semicolon) ."</td>";
		}
		else
			print "<td style=\"background-color:#d30;". $style[$csv_nn] ."\">ERROR!</td>"; // $this->htmlspecialchars_ent($csv_cell)

	}
	print "</tr>\n";
}
print "</tbody></table>\n";
?>

