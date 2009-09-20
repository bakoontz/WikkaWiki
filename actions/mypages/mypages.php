<?php
/**
 * Display the list of pages an user owns.
 * 
 * @author	Carlo Zottmann
 * 
 * @uses	Wakka::GetUser()
 * @uses	Wakka::GetUserName()
 * @uses	Wakka::htmlspecialchars_ent()
 * @uses	Wakka::IsAdmin()
 * @uses	Wakka::Link()
 * @uses	Wakka::LoadAllPages()
 * 
 */

$username = '';
if(isset($_GET['user']))
{
	$username = $this->htmlspecialchars_ent($_GET['user']);
}

if (($this->IsAdmin() && !empty($username)) ||
	($this->GetUser() && $username = $this->GetUserName()))
{
	printf("<div class='floatl'>".MYPAGES_HEADER."</div><br/><br/>\n", $username);

	$my_pages_count = 0;

	if ($pages = $this->LoadAllPages())
	{
		$curChar = '';
		foreach ($pages as $page)
		{
			if ($username == $page["owner"]) {
				$firstChar = strtoupper($page["tag"][0]);
				if (!preg_match("/[A-Z,a-z]/", $firstChar)) {
					$firstChar = "#";
				}
	
				if ($firstChar != $curChar) {
					if ($curChar != '') echo "<br />\n";
					echo '<strong>'.$firstChar."</strong><br />\n";
					$curChar = $firstChar;
				}
	
				echo $this->Link($page["tag"])."<br />\n";
				
				$my_pages_count++;
			}
		}
		
		if ($my_pages_count == 0)
		{
			echo '<em class="error">'.MYPAGES_NONE_OWNED.'</em>';
		}
	}
	else
	{
		echo '<em class="error">'.MYPAGES_NONE_FOUND.'</em>';
	}
}
else
{
	echo '<em class="error">'.MYPAGES_NOT_LOGGED_IN.'</em>';
}

?>
