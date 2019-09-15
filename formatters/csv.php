<?php
// convert inline csv data into a table.
// by OnegWR, may 2005, license GPL
// http://wikkawiki.org/OnegWRCsv

// Copy the code below into a file named formatters/csv.php
// And give it the same file permissions as the other files in that directory.

$csv_printheader=0;
foreach (split("\n", $text) as $csv_n => $csv_line) {
        if(preg_match("/^#|^\s*$/",$csv_line)) 
			continue;

        if( $csv_printheader == 0) {
			print "<table cellpadding=\"5\" cellspacing=\"1\"><tbody>\n"; $csv_printheader=1; 
		}
        print ($csv_n%2) ? "<tr bgcolor=\"#ffffee\">" : "<tr bgcolor=\"#eeeeee\">";

        foreach(split(",", $csv_line) as $csv_nn => $csv_cell) {
                if(preg_match("/^\s*$/",$csv_cell)) {
                        print "<td>&nbsp;</td>";
                } elseif(preg_match("/^\"?[\s]*==\/(.*?)\/==\"?$/",$csv_cell,$matches)) {
                        print "<td style=\"background-color:#ccc; text-align:right;\">".$this->htmlspecialchars_ent($matches[1])."</td>";
						$style[$csv_nn]= "text-align:right";
                } elseif(preg_match("/^\"?[\s]*==\\\\(.*?)\\\\==\"?$/",$csv_cell,$matches)) {
                        print "<td style=\"background-color:#ccc; text-align:left;\">".$this->htmlspecialchars_ent($matches[1])."</td>";
						$style[$csv_nn]= "text-align:left";
                } elseif(preg_match("/^\"?[\s]*==(.*?)==\"?$/",$csv_cell,$matches)) {
                        print "<td style=\"background-color:#ccc; text-align:center;\">".$this->htmlspecialchars_ent($matches[1])."</td>";
						$style[$csv_nn]= "text-align:center";
                } elseif(preg_match("/^\"?(.*?)\"?$/",$csv_cell,$matches)) {
                        print "<td style=\"".$style[$csv_nn]."\">".$this->htmlspecialchars_ent($matches[1])."</td>";
                } else {
                        print "<td>".$this->htmlspecialchars_ent($csv_cell)."</td>";
                }
        }
        print "</tr>\n";
}
if ($csv_printheader == 1) print "</tbody></table>\n";
?>

