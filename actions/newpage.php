<?php
// author: costal martignier
// beschreibung: erstellt eine seite
// parameter: keine
// lizenz: GPL
// email: wakkaactions@martignier.net
// url: http://knowledge.martignier.net

if ($_POST['submitted'] == true)
{
   $pagename = $_POST['pagename'];
   $url = $this->config['base_url'];
   $this->redirect($url.$pagename."/edit");
}
else
{   
   echo '<br />';
   echo '<form action="" method="post">
        <input type="hidden" name="submitted" value="true" />
        <input type="text" name="pagename" size="50"/>
        <input type="submit" value="Create and Edit" />
     </form>';   
} 
?>

