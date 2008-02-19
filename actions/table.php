<?php

//$vars = array('columns' => '3', 'cellpadding' => '1', 'cells' => '**BIG**;**GREEN**;**FROGS**;yes;yes;no;no;yes;yes');

// Init:
$delimiter=';';
$empty_cell='###';
$row=1;
$cellpadding=1;
$cellspacing=1;
$border=1;
$columns=1;
$style='border-spacing: 2px;border:2px outset #876;width:auto;margin:0 auto;';

// Parameter aus Array holen
if (is_array($vars))
{
    foreach ($vars as $param => $value)
    {    	
    	if ($param == 'style') {$style=$value;}   
    	if ($param == 'columns') {$columns=$value;}	
        if ($param == 'cellpadding')
        {
        	$cellpadding=$value;
        	$border=$value;
        }             
        if ($param == 'cells') $cells = split($delimiter, $value);
    }
// Tabellen-Tag oeffnen:
	echo "<table cellpadding='".$cellpadding."' cellspacing='".$cellspacing."' border='".$border."' style='".$style."'>";
	foreach ($cells as $cell_item)
	 	{
	 		if ($row == 1) echo "<tr>";
	 		if ($cell_item==$empty_cell) $cell_item='<br />';
			echo "<td>".$cell_item."</td>";
			$row ++;
			if ($row > $columns)
				{
					$row = "1";
					echo "</tr>";
		    	}
	 	}

// Tabellen-Tag schliessen
	echo "</table>";
}
?>
