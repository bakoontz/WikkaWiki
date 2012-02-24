<div id="content">
<?php
/**
 * Display the page source, slightly formatted for viewing.
 *
 * @package		Handlers
 * @subpackage	Page
 * @name		Showcode
 * @version		$Id:showcode.php 407 2007-03-13 05:59:51Z DarTar $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author		{@link http://wikkawiki.org/JsnX JsnX} (first draft)
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli} (code cleanup, i18n strings and DB check)
 * @version		0.31
 * @since		Wikka 1.1.6.0
 *
 * @uses		Wakka::existsPage()
 * @uses		Wakka::HasAccess()
 * @uses		Wakka::Href()
 * @uses		Wakka::Link()
 * @uses		Wakka::htmlspecialchars_ent()
 *
 * @output		Wiki source of current page (if it exists).
 * @todo		move structural elements to template;
 * @todo		create GeSHi highlighter for Wikka markup; #144
 */

//check if page exists
if ($this->ExistsPage($this->GetPageTag()))
{
	//check if user has read access
	if ($this->HasAccess('read'))
	{
		// display raw page, slightly formatted for viewing
		$pagelink = $this->Link($this->GetPageTag(), '', $this->GetPageTag());
		printf('<h4>'.T_("Wiki source for %s").'</h4><br />', $pagelink);
		echo '<p><a class="keys" href="'.$this->Href('raw').'">'.T_("Show raw source").'</a></p>';
		echo '<div class="wikisource">'.nl2br($this->htmlspecialchars_ent($this->page["body"], ENT_QUOTES)).'</div>';
	}
	else
	{
		echo '<em class="error">'.T_("You are not allowed to read the source of this page.").'</em>';
	}
}
else
{
	echo '<em class="error">'.sprintf(T_("Sorry, page %s does not exist."),$this->GetPageTag()).'</em>';
}

?>
</div>
