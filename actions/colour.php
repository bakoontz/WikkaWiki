<?php

if (is_array($vars)) {

    foreach ($vars as $param => $value) {
        if ($param == 'text')
        {
        $mytext= $value;
        }
        if ($param == 'c')
        {
        	$colourcode=$value;
        }
        elseif ($param == 'hex')
        {        	
        	$colourcode=$value;        	
        }

    }
    echo "<span style=\"color: $colourcode\">".$mytext."</span>";
}
?>
