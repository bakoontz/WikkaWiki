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
 * @version		0.3
 * @since		Wikka 1.1.6.0
 *
 * @uses		Wakka::existsPage()
 * @uses		Wakka::HasAccess()
 * @uses		Wakka::Link()
 * @uses		Wakka::htmlspecialchars_ent()
 *
 * @output		Wiki source of current page (if it exists).
 * @todo		- move structural elements to template;
 *				- create GeSHi highlighter for Wikka markup; #144
 */

// i18n strings
#if(!defined('SOURCE_HEADING')) define('SOURCE_HEADING', '=== Formatting code for [[%s]] ==='); //TODO: check for consistency with other handlers (formatting code vs. source vs. markup)
if (!defined('SOURCE_HEADING')) define('SOURCE_HEADING', 'Formatting code for %s'); //TODO: check for consistency with other handlers (formatting code vs. source vs. markup)
if (!defined('RAW_LINK_LABEL')) define('RAW_LINK_LABEL', 'show source only');
if (!defined('ERROR_NOT_EXISTING_PAGE')) define('ERROR_NOT_EXISTING_PAGE', 'Sorry, this page doesn\'t exist.');
if (!defined('ERROR_NO_READ_ACCESS')) define('ERROR_NO_READ_ACCESS', 'Sorry, you aren\'t allowed to read this page.');

echo '<div class="page">'."\n";

//check if page exists
if ($this->existsPage($this->tag))		// name change, interface change (allow active page only)
{
	//check if user has read access
	if ($this->HasAccess('read'))
	{
		// display raw page, slightly formatted for viewing
		#echo $this->Format(sprintf(SOURCE_HEADING.' --- ', $this->tag));
		echo '<h3>'.sprintf(SOURCE_HEADING, $this->Link($this->tag)).'<h3><br />'."\n";;
		echo '(<a href="'.$this->href('raw').'">'.RAW_LINK_LABEL.'</a>)<br /><br />';
		echo '<tt>'.nl2br($this->htmlspecialchars_ent($this->page['body'], ENT_QUOTES)).'</tt>';
	}
	else
	{
		echo '<em class="error">'.ERROR_NO_READ_ACCESS.'</em>';
	}
}
else
{
	echo '<em class="error">'.ERROR_NOT_EXISTING_PAGE.'</em>';
}

echo '</div>';
?>