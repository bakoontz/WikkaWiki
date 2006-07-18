<?php
/**
 * Handle new comments.
 * 
 * @package	Handlers
 * @subpackage	Comments
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses	Wakka::HasAccess()
 * @uses	Wakka::Href()
 * @uses	Wakka::htmlspecialchars_ent()
 * @uses	Wakka::redirect()
 * @uses	Wakka::SaveComment()
 */
/**
 * i18n
 */
if (!defined('ERROR_EMPTY_COMMENT')) define ('ERROR_EMPTY_COMMENT', 'Comment body was empty -- not saved!');
if (!defined('ERROR_NO_RIGHT_TO_COMMENT')) define ('ERROR_NO_RIGHT_TO_COMMENT', "Sorry, you're not allowed to post comments to this page");

if ($this->HasAccess("comment") || $this->IsAdmin())
{
	$redirectmessage = "";

	$body = nl2br($this->htmlspecialchars_ent(trim($_POST["body"])));

	if (!$body)
	{
		$redirectmessage = ERROR_EMPTY_COMMENT;
	}
	else
	{
		// store new comment
		$this->SaveComment($this->tag, $body);
	}
	
	// redirect to page
	$this->redirect($this->Href(), $redirectmessage);
}
else
{
	print('<div class="page"><em class="error">'.ERROR_NO_RIGHT_TO_COMMENT.'</em></div>'."\n");
}

?>