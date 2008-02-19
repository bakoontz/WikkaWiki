<?php

if ($q) {
	$q = $this->ReturnSafeHTML($q);
}
else { 
	if ($wikka_vars) $q = $this->ReturnSafeHTML($wikka_vars);
	else $q = $this->tag();
}

?>

<form action='http://www.google.com/search' method='get' name='f' target='_blank'>
	<input type='text' value='<?=$q ?>' name='q' size='30' /> <input name='btnG' type='submit' value='Google' />
</form>