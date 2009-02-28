<?php
/**
 * Display a searchbox for searching google.
 *
 * @filesource
 * @uses	Wakka::GetPageTag()
 * @uses	Wakka::ReturnSafeHTML()
 */

// defaults
$q = '';

// getting params
if (is_array($vars))
{
    foreach ($vars as $param => $value)
    {
    	if ($param == 'q') 
    	{
    		$q = $value;
    	}
    }
}

// compatibility for {{googleform query}}
if (('' == $q) && isset($wikka_vars)) $q = $wikka_vars;

// fallback: use the pagename
if('' == $q) $q = $this->GetPageTag();

// sec input
$q = $this->ReturnSafeHTML($q);

?>
<form action='http://www.google.com/search' method='get' name='f' target='_blank'>
	<input type='text' value='<?php echo $q; ?>' name='q' size='30' /> <input name='btnG' type='submit' value='Google' />
</form>