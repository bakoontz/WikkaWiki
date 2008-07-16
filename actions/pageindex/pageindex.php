<?php
/**
 * Display an alphabetical list of pages of the wiki.
 *
 * This action checks user read privileges and displays an index of read-accessible pages.
 *
 * Optionally: {{pageindex showpagetitle="1"}}
 *
 * where showpagetitle="1" displays page titles (or page tag by
 *	default if no page title can be generated)
 *
 * @package		Actions
 * @version		$Id:pageindex.php 369 2007-03-01 14:38:59Z DarTar $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author	{@link http://wikkawiki.org/GiorgosKontopoulos GiorgosKontopoulos} (added ACL check, first code cleanup)
 * @author	{@link http://wikkawiki.org/DarTar DarTar} (adding doc header, minor code and layout refinements, i18n)
 * @author	{@link http://wikkawiki.org/BrianKoontz BrianKoontz} (added showpagetitle option)
 *
 * @uses		Wakka::LoadPageTitles()
 * @uses		Wakka::GetUserName()
 * @uses		Wakka::HasAccess()
 * @uses		Wakka::Link()
 * @uses		Wakka::Format()
 * @uses		Wakka::FormatUser()
 *
 * @output		a list of pages accessible to the current user
 * @todo		add filtering options
 * @todo		fix RE (#104 etc.)
 * @todo		action parameter validation
 */

$showpagetitle = FALSE;
if (isset($vars['showpagetitle']) && 1 == (int) $vars['showpagetitle'])
{
	$showpagetitle = TRUE;
}

if ($pages = $this->LoadPageTitles())
{
	// filter by letter
	$requested_letter = (isset($_GET['letter'])) ?  $this->GetSafeVar('letter', 'get') : ''; # #312
	if (!$requested_letter && isset($letter))
	{
		$requested_letter = strtoupper($letter); // TODO action parameter (letter) needs to be validated and sanitized (make sure it's a single character)
	}

	// get things started
	$cached_username = $this->GetUserName();
	$user_owns_pages = FALSE;
	$link = $this->href('', '', 'letter=');
	$alpha_bar = '<a href="'.$link.'">'.PAGEINDEX_ALL_PAGES.'</a>&nbsp;'."\n";
	$index_caption = PAGEINDEX_CAPTION;
	$index_output = '';
	$current_character = '';
	$character_changed = FALSE;

	// get page list
	foreach ($pages as $page)
	{
		// check user read privileges
		if (!$this->HasAccess('read', $page['tag']))
		{
			continue;
		}

		$page_owner = $page['owner'];
		// $this->CachePage($page);

		$firstChar = strtoupper($page['tag'][0]);
		if (!preg_match('/[A-Za-z]/', $firstChar))//TODO: (#104 #340, #34) Internationalization (allow other starting chars, make consistent with Formatter REs)
		{
			$firstChar = '#';
		}
		if ($firstChar != $current_character)
		{
			$alpha_bar .= '<a href="'.$link.$firstChar.'">'.$firstChar.'</a>&nbsp;'."\n";
			$current_character = $firstChar;
			$character_changed = TRUE;
		}
		if ($requested_letter == '' || $firstChar == $requested_letter)
		{
			if ($character_changed)
			{
				$index_output .= "<br />\n<strong>$firstChar</strong><br />\n";
				$character_changed = FALSE;
			}
			$index_output .= $this->Link($page['tag']);
			// Output page title if $showpagetitle set to 1
			if (TRUE === $showpagetitle)
			{
				$index_output .= "<span class=\"pagetitle\">[".$this->PageTitle($page['tag'])."]</span>";
			}
			if ($cached_username == $page_owner)
			{
				$index_output .= '*';
				$user_owns_pages = TRUE;
			}
			elseif ($page_owner != '(Public)' && $page_owner != '')
			{
				$index_output .= sprintf(' . . . . '.WIKKA_PAGE_OWNER, $this->FormatUser($page_owner));
			}
			$index_output .= "<br />\n";
		}
	}
	// generate page
	// @@@ don't use Format() - generate HTML!
	if ($user_owns_pages)
	{
		$index_caption .= '---'.PAGEINDEX_OWNED_PAGES_CAPTION;
	}
	echo $this->Format('===='.PAGEINDEX_HEADING.'==== --- <<'.$index_caption.'<< ::c:: ---');
	echo "\n<strong>".$alpha_bar."</strong><br />\n";
	echo $index_output;
}
else
{
	echo WIKKA_NO_PAGES_FOUND;
}
?>
