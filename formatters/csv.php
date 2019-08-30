<?php
// convert inline csv data into a table.
// by OnegWR, may 2005, license GPL
// by ThePLG, aug 2019, license GPL
// http://wikkawiki.org/OnegWRCsv

// Copy the code below into a file named formatters/csv.php
// And give it the same file permissions as the other files in that directory.

$DEBUG= 0;

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

		if ($csv_n == 0)
		{
			$style[$csv_nn]= "padding: 1px 10px 1px 10px; ";
			$total[$csv_nn]= 0;
		}

		if (preg_match("/\"?\s*==(.*)==\"?$/", $csv_cell, $header)) 
		{
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

			if (!strcmp($title[$csv_nn], "++TOTAL++"))
			{
				if ($total[$csv_nn] > 0)
					print "<th style=\"background-color:#ccc;". $style[$csv_nn] ."\">". sprintf("%0.2f", $total_i[$csv_nn] + ($total_d[$csv_nn]/100)) ."</th>";
				else
					print "<th style=\"background-color:#d30;". $style[$csv_nn] ."\">ERROR!</th>";

				continue;
			}

			if (preg_match("/^(.*)([+#])\\2$/", $title[$csv_nn], $accum)) 
			{
				switch ($accum[2]) {
					case "#" :
						$DEBUG= 1; // drop through ...
					case "+" :
						$total[$csv_nn]= 1;
						$total_i[$csv_nn]= 0;
						$total_d[$csv_nn]= 0;
						break;
				}

				$title[$csv_nn]= $accum[1];
			}
	
			print "<th style=\"background-color:#ccc;". $style[$csv_nn] ."\">". $this->htmlspecialchars_ent($title[$csv_nn]) ."</th>";
			continue;
		}

		if (preg_match("/^\s*$/",$csv_cell))
			print "<td>&nbsp;</td>";
		elseif ($total[$csv_nn] && preg_match("/^\"?([\s\d+\-,.]+)\"?$/", $csv_cell, $matches))
		{
			$matches_nows= preg_replace('/\s+/', '', $matches[1]);

			if (preg_match("/^([+-]?)(\d{1,3}(\.\d{3})*|(\d+)),(\d{2})$/", $matches_nows, $swe))
			{
				$format= "SE";
				$i= $swe[1] . preg_replace('/\./', '', $swe[2]);
				$d= $swe[1] . $swe[5];
			}
			elseif (preg_match("/^([+-]?)(\d{1,3}(\,\d{3})*|(\d+))(\.(\d{2}))?$/", $matches_nows, $usa))
			{
				$format= "US";
				$i= $usa[1] . preg_replace('/,/', '', $usa[2]);
				$d= $usa[1] . $usa[5];
			}
			else
			{
				$total[$csv_nn]= -1;
				print "<td style=\"". $style[$csv_nn] ."\">".$this->htmlspecialchars_ent($matches_nows)."</td>";
				continue;
			}

			$total_i[$csv_nn]+= intval($i);
			$total_d[$csv_nn]+= intval($d);
			$nr= $i + ($d/100);

			if ($DEBUG == 1)
				print "<td style=\"". $style[$csv_nn] ."\">". $csv_cell ."(". $format .")= " . sprintf("%.2f", $nr) ."+= ". $total_i[$csv_nn] ." ". $total_d[$csv_nn] ."</td>";
			else
				print "<td title=\"". $csv_cell ."(". $format .")\" style=\"". (($nr <= 0) ? "background-color:#d30; " : "" ) . $style[$csv_nn] ."\">". sprintf("%.2f", $nr) ."</td>";
		}
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

