<?php

//interface strings
if (!defined('ERROR_EMPTY_COMMENT')) define('ERROR_EMPTY_COMMENT', "Sorry, empty comments cannot be saved.");
if (!defined('ERROR_NO_COMMENT_WRITE_ACCESS')) define('ERROR_NO_COMMENT_WRITE_ACCESS', "Sorry, you are not allowed to post comments to this page.");
if (!defined('ERROR_COMMENT_NO_KEY')) define('ERROR_COMMENT_NO_KEY', "Your comment cannot be saved. Please contact the wiki administrator(1).");
if (!defined('ERROR_COMMENT_INVALID_KEY')) define('ERROR_COMMENT_INVALID_KEY', "Your comment cannot be saved. Please contact the wiki administrator(2).");

$redirectmessage = '';

if (($this->HasAccess('comment') || $this->IsAdmin()) && $this->existsPage($this->tag))
{
	$body = (isset($_POST['body'])) ? trim($_POST['body']) : '';

	if ('' == $body) #check if comment is non-empty
	{
		$redirectmessage = ERROR_EMPTY_COMMENT;
	}
	elseif (FALSE === ($aKey = $this->getSessionKey($_POST['form_id'])))	# check if page key was stored in session
	{
		$redirectmessage = ERROR_COMMENT_NO_KEY;
	}
	elseif (TRUE !== ($rc = $this->hasValidSessionKey($aKey)))	# check if correct name,key pair was passed
	{
		$redirectmessage = ERROR_COMMENT_INVALID_KEY;
	}
	// all is kosher: store new comment
	else
	{
		$body = nl2br($this->htmlspecialchars_ent($body));
		$this->SaveComment($this->tag, $body);
	}
	
	// redirect to parent page
	$this->Redirect($this->Href(), $redirectmessage);
}
else
{
	echo '<div id="content"><em class="error">'.ERROR_NO_COMMENT_WRITE_ACCESS.'</em></div>'."\n";
}
?>
