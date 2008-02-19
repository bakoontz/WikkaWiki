<?php

if (!$q) { 
	if ($wakka_vars) $q = $wakka_vars;
	else $q = $this->tag();
}

?>

<form action='http://www.google.com/search' method='get' name='f' target='_blank'>
	<input type='text' value='<?=$q ?>' name='q' size='30' /> <input name='btnG' type='submit' value='Google' />
</form>