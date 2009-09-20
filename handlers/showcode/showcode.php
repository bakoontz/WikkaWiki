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

echo '<div id="content">'."\n";

//check if page exists
if ($this->ExistsPage($this->tag))
{
	//check if user has read access
	if ($this->HasAccess('read'))
	{
		// display raw page, slightly formatted for viewing
		$pagelink = $this->Link($this->tag, '', $this->tag);
		printf('<h4>'.SOURCE_HEADING.'</h4><br />', $pagelink);
		echo '<p><a class="keys" href="'.$this->href('raw').'">'.RAW_LINK_DESC.'</a></p>';
		echo '<div class="wikisource">'.nl2br($this->htmlspecialchars_ent($this->page["body"], ENT_QUOTES)).'</div>';
	}
	else
	{
		echo '<em class="error">'.ERROR_NO_READ_ACCESS.'</em>';
	}
}
else
{
	echo '<em class="error">'.sprintf(ERROR_NOT_EXISTING_PAGE,$this->tag).'</em>';
}

echo '</div>';
?>
