<?php
// convert inline csv data into a table.
// by OnegWR, May 2005, license GPL http://wikkawiki.org/OnegWRCsv
// by ThePLG, Mar 2020, license GPL http://wikkawiki.org/PLG-Csv

// Copy the code below into a file named formatters/csv.php
// And give it the same file permissions as the other files in that directory.

$DEBUG= 0;
$comments= 0;

$style_header="background-color:#ccc; ";
$style_even="background-color:#ffe; ";
$style_odd="background-color:#eee; ";
$style_error="background-color:#d30; ";

print "<table><tbody>\n";
foreach ($array_csv_lines= preg_split("/[\n]/", $text) as $csv_n => $csv_line) 
{
	if (preg_match("/^#|^\s*$/",$csv_line)) 
	{
		if (preg_match("/^#!\s*th\s*{\s*background-color:\s*([^\s;]*)\s*;\s*}$/", $csv_line, $color)) {
			$style_header= "background-color:". $color[1] ."; ";
		}
		else
		{
			if (preg_match("/^#!\s*td\s*{.*background-color-even\s*:\s*([^\s;]*)\s*;.*}$/", $csv_line, $color))
				$style_even= "background-color:". $color[1] ."; ";

			if (preg_match("/^#!\s*td\s*{.*background-color-odd\s*:\s*([^\s;]*)\s*;.*}$/", $csv_line, $color))
				$style_odd= "background-color:". $color[1] ."; ";

			if (preg_match("/^#!\s*td\s*{.*background-color-error\s*:\s*([^\s;]*)\s*;.*}$/", $csv_line, $color))
				$style_error= "background-color:". $color[1] ."; ";
		}

		$comments++;
		continue;
	}

	print (($csv_n+$comments)%2) ? "<tr style=\"". $style_even ."\">" : "<tr style=\"". $style_odd ."\">";

	// https://www.rexegg.com/regex-lookarounds.html
	// asserts what precedes the ; is not a backslash \\\\, doesn't account for \\; (escaped backslash semicolon)
	// OMFG! https://stackoverflow.com/questions/40479546/how-to-split-on-white-spaces-not-between-quotes
	//
	foreach (preg_split("/(?<!\\\\);(?=(?:[^\"]*([\"])[^\"]*\\1)*[^\"]*$)/", $csv_line) as $csv_nn => $csv_cell)
	{
		// https://www.phpliveregex.com
		// https://www.regular-expressions.info/quickstart.html

		if ($csv_n == $comments)
		{
			$style[$csv_nn]= "padding: 1px 10px 1px 10px; ";
			$total[$csv_nn]= 0;
		}

		if (preg_match("/^\"?\s*==(.*)==\s*\"?$/", $csv_cell, $header)) 
		{
			$title[$csv_nn]= $header[1];

			if (preg_match("/([\/\\\\|])([^\/\\\\|]*)\\1$/", $title[$csv_nn], $align)) 
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
					print "<th style=\"". $style_header . $style[$csv_nn] ."\">". sprintf("%0.2f", $total_i[$csv_nn] + ($total_d[$csv_nn]/100)) ."</th>";
				else
					print "<th style=\"". $style_error . $style[$csv_nn] ."\">ERROR!</th>";

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
	
			print "<th style=\"". $style_header . $style[$csv_nn] ."\">". $this->htmlspecialchars_ent($title[$csv_nn]) ."</th>";
			continue;
		}

		// if a cell is blank, print &nbsp;
		//
		if (preg_match("/^\s*$/",$csv_cell))
			print "<td style=\"". $style[$csv_nn] ."\">&nbsp;</td>";
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
		// extract the cell out of it's quotes
		//
        elseif (preg_match("/^\s*(\"?)(.*)\\1\s*$/", $csv_cell, $matches))
		{
			if ($matches[1] == "\"")
			{
				$style[$csv_nn]= "white-space:pre; ". $style[$csv_nn];
				$cell= $matches[2];
			}
			else
				$cell= preg_replace('/\\\\;/', ';', $matches[2]);

			// test for CamelLink
			//
			if (preg_match_all("/\[\[([[:alnum:]-]+)\]\]/", $cell, $all_links))
			{
				$linked= $cell;
				
				foreach ($all_links[1] as $n => $camel_link) 
					$linked = preg_replace("/\[\[". $camel_link ."\]\]/", $this->Link($camel_link), $linked);

				print "<td style=\"". $style[$csv_nn] ."\">". $linked ."</td>"; // no htmlspecialchars_ent()
			}		
			else
				print "<td style=\"". $style[$csv_nn] ."\">". $this->htmlspecialchars_ent($cell) ."</td>";
		}
		else
			print "<td style=\"". $style_error . $style[$csv_nn] ."\">ERROR!</td>"; // $this->htmlspecialchars_ent($csv_cell)

	}
	print "</tr>\n";

}
print "</tbody></table>\n";

?>


