<?php

//interface strings
if (!defined('ERROR_EMPTY_COMMENT')) define('ERROR_EMPTY_COMMENT', "Sorry, empty comments cannot be saved.");
if (!defined('ERROR_NO_COMMENT_WRITE_ACCESS')) define('ERROR_NO_COMMENT_WRITE_ACCESS', "Sorry, you are not allowed to post comments to this page.");
if (!defined('ERROR_COMMENT_NO_KEY')) define('ERROR_COMMENT_NO_KEY', "Your comment cannot be saved. Please contact the wiki administrator(1).");
if (!defined('ERROR_COMMENT_INVALID_KEY')) define('ERROR_COMMENT_INVALID_KEY', "Your comment cannot be saved. Please contact the wiki administrator(2).");

$redirectmessage = '';

if (($this->HasAccess('comment') || $this->IsAdmin()) && $this->existsPage($this->tag))
{
	$body = trim($this->GetSafeVar('body', 'post'));

	// initializations
	$redirectmessage = '';
	$failed = FALSE;
	$reason = '';
	# matches is a required parameter but we're interesting in the count only
	$urlcount = preg_match_all('/\b[a-z]+:\/\/\S+/',$body,$dummy);
	# prevent problems when counting fails
	if (FALSE === $urlcount) $urlcount = 0;
	$maxurls  = $this->config['max_new_comment_urls'];
	$logging  = ($this->config['spam_logging'] == '1');

	if ('' == $body) #check if comment is non-empty
	{
		$redirectmessage = ERROR_EMPTY_COMMENT;
	}

	else if ($urlcount > $maxurls)
	{
		$redirectmessage = 'Too many URLs -- comment not saved!';
		if ($logging)
		{
			$failed = TRUE;
			$reason = 'urls > '.$maxurls;
		}
	}

	# Apply content filter if configured
	else if ($this->config['content_filtering'] == "1" && $this->hasBadWords($body))
	{
		$redirectmessage = 'Content not acceptable - please reformulate your comment!';
		if ($logging)
		{
			$failed = TRUE;
			$reason = 'filter';
		}
	}

	// all is kosher: store new comment
	else
	{
		$body = nl2br($this->htmlspecialchars_ent($body));
		$this->SaveComment($this->tag, $body);
	}

	// log failed attempt
	if ($failed && $logging)
	{
		// log failed attempt
		$this->logSpamComment($this->tag,$body,$reason,$urlcount);
	}
	
	// redirect to parent page
	$this->Redirect($this->Href(), $redirectmessage);
}
else
{
	echo '<div id="content"><em class="error">'.ERROR_NO_COMMENT_WRITE_ACCESS.'</em></div>'."\n";
}
?>
