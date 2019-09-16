<?php
// convert inline csv data into a table.
// by OnegWR, may 2005, license GPL
$csv_printheader=0;
foreach(split("\n", $text) as $csv_n => $csv_line){
        if(preg_match("/^#|^\s*$/",$csv_line)) continue;
        if( $csv_printheader == 0){ print "<table cellpadding=\"5\" cellspacing=\"1\"><tbody>\n"; $csv_printheader=1; }
        print ($csv_n%2) ? "<tr bgcolor=\"#ffffee\">" : "<tr bgcolor=\"#eeeeee\">";
        foreach(split(";", $csv_line) as $csv_nn => $csv_cell){
                if(preg_match("/^\s*$/",$csv_cell)){
                        print "<td>&nbsp;</td>";
                }elseif(preg_match("/^(\"?)\*\*\*(.*?)\*\*\*(\"?)$/",$csv_cell,$matches)){
                        print "<td bgcolor=\"#cccccc\">".
                                $this->htmlspecialchars_ent($matches[2])."</td>";
                }elseif(preg_match("/^(\"?)(.*?)(\"?)$/",$csv_cell,$matches)){
                        print "<td>".$this->htmlspecialchars_ent($matches[2])."</td>";
                }else{
                        print "<td>".$this->htmlspecialchars_ent($csv_cell)."</td>";
                }
        }
        print "</tr>\n";
}
if( $csv_printheader == 1) print "</tbody></table>\n";
?>
