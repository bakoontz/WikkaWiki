<?php
/**
 * Display the page source, slightly formatted for viewing.
 *
 * @package		Handlers
 * @subpackage	Page
 * @name		Showcode
 *
 * @author		{@link http://wikkawiki.org/JsnX JsnX} (first draft)
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli} (code cleanup, i18n strings and DB check)
 * @version		0.2
 * @since		Wikka 1.1.6.0
 *
 * @output		Wiki source of current page (if it exists).
 * @todo			- move structural elements to templating class;
 * 				- use error handler to display error messages;
 */

// i18n strings
define('SOURCE_HEADING', "=== Formatting code for [[%s]] ==");//TODO: check for consistency with other handlers (formatting code vs. source vs. markup)
define('ERROR_NOT_EXISTING_PAGE', "Sorry, this page doesn't exist.");
define('ERROR_NO_READ_ACCESS', "Sorry, you aren't allowed to read this page.");

echo '<div class="page">'."\n";//TODO: move to templating class

//check if page exists
if ($this->ExistsPage($this->tag))
{
	//check if user has read access
	if ($this->HasAccess('read'))
	{
		// display raw page, slightly formatted for viewing
		echo $this->Format(sprintf(SOURCE_HEADING, $this->tag));
		echo nl2br($this->htmlspecialchars_ent($this->page["body"], ENT_QUOTES));
	}
	else
	{
		echo ERROR_NO_READ_ACCESS;//TODO: use error handler
	}
}
else
{
	echo ERROR_NOT_EXISTING_PAGE;//TODO: use error handler
}

echo '</div>';//TODO: move to templating class
?>