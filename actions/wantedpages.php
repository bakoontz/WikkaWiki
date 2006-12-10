<?php
/**
 * Display a list of nonexisting pages to which other pages are linking to.
 * 
 * <p>This action lists all pagenames that don't exist but are referred to by other pages on the wiki. By default, the 
 * WikkaInstaller creates a page named WantedPages that uses this action.</p>
 * <p>Those non-existing pages are listed as one line per wanted pages. Each line is composed of 2 parts : The name of 
 * the wanted page in a form of a link: Clicking on this link will let you create the page and start editing its content.
 * Then in brackets, you see the number of pages linking to the wanted page. This number is also in a form of a link:
 * clicking on it will let you see all the pages linking to the wanted page, using the {@link backlinks.php backlinks}
 * handler.</p>
 * <p>Since version 1.1.7, if there is only one page linking to the wanted page, that pagename is shown in brackets after
 * the number 1, you can click on the page name to see its content. Another link labelled <tt>edit<tt> is also provided
 * to let you edit the page. Such link is useful if, for example, you have a page named MySQL in the WantedPages list
 * but you certainly don't want to create such page, so you can start editing the page linking to it and unwikify
 * the word MySQL.</p>
 * <p>Another enhancement available since version 1.1.7 is the ability to sort the list by combination of the 3 parameters
 * below : <ul>
 * <li>count : number of distinct pages linking to the wanted page,</li>
 * <li>time : date last modified of any page linking to the wanted page,</li>
 * <li>tag : the name of the wanted page, alphabetically.</li></ul></p>
 * 
 * @package	Actions
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses	 Wakka::Link()
 * @uses	 Wakka::LoadWantedPages()
 * @uses	 Wakka::LoadWantedPages2()
 * @uses	 Wakka::FormOpen()
 * @uses	 Wakka::FormClose()
 */
// i18n
if (!defined('BACKLINKS_TITLE')) define('BACKLINKS_TITLE', 'Click to view all pages linking to %s');
if (!defined('LABEL_EDIT')) define('LABEL_EDIT', 'edit');
if (!defined('LISTPAGES_EDIT_TITLE')) define('LISTPAGES_EDIT_TITLE', 'Click to edit %s');
if (!defined('LEGEND_SORTING')) define('LEGEND_SORTING', 'Sorting ...');
if (!defined('LABEL_SORTING_NUMBER')) define('LABEL_SORTING_NUMBER', 'Sorting #');
if (!defined('LABEL_SORTING_DESC')) define('LABEL_SORTING_DESC', 'desc');
if (!defined('LABEL_OK')) define('LABEL_OK', '   OK   ');
if (!defined('NO_WANTED_PAGES')) define('NO_WANTED_PAGES', 'No wanted pages. Good!');

$sorting_fields = array('count', 'time', 'tag');
if ((isset($vars) && is_array($vars) && isset($vars['option']) && $vars['option'] == 'v2') || (isset($_GET['ob1'])))
{
	$sort = '';
	for ($i = 1; $i <= 3; $i ++)
	{
		if (isset($_GET['ob'.$i]))
		{
			if (in_array($_GET['ob'.$i], $sorting_fields))
			{
				if ($sort) 
				{
					$sort .= ',';
				}
				$sort .= $_GET['ob'.$i].' ';
				if (isset($_GET['de'.$i]))
				{
					$sort .= 'desc';
				}
			}
		}
	}
	if ($pages = $this->LoadWantedPages2($sort))
	{
		foreach ($pages as $page)
		{
			print($this->Link($page["tag"]));
			if ($page['count'] > 1)
			{
				print(' (<a href="'.$this->href('backlinks', $page['tag']).'" title="'.sprintf(BACKLINKS_TITLE, $page['tag']).'">'.$page['count']."</a>)<br />\n");
			}
			else
			{
				preg_match('#/(.*)$#', $page['time'], $match);
				print(' (1 : '.$this->Link($match[1]).' <small>['.$this->Link($match[1], 'edit', 'edit', false, true, sprintf(LISTPAGES_EDIT_TITLE, $match[1]))."]</small>)<br />\n");
			}
		}
	}
}
elseif ($pages = $this->LoadWantedPages())
{
	foreach ($pages as $page)
	{
		print($this->Link($page['tag']).' (<a href="'.$this->href('backlinks', $page['tag']).'" title="'.sprintf(BACKLINKS_TITLE, $page['tag']).'">'.$page['count']."</a>)<br />\n");
	}
}
if ($pages)
{
	// adding form to control sorting
	$options = '<option value="">&nbsp;</option>';
	foreach ($sorting_fields as $i)
	{
		$options .= '<option value="'.$i.'">'.$i.'</option>';
	}
	echo $this->FormOpen('', '', 'get');
	echo '<fieldset id="wantedpages_sorting"><legend>'.LEGEND_SORTING.'</legend>';
	for ($i=1; $i<=3; $i++)
	{
		echo '<label>'.LABEL_SORTING_NUMBER.$i.' : <select name="ob'.$i.'">'.$options.'</select></label> <label><input type="checkbox" name="de'.$i.'" /> '.LABEL_SORTING_DESC.'</label><br />'."\n";
	}
	echo '<input type="submit" value="'.LABEL_OK.'" />';
	echo '</fieldset>';
	echo $this->FormClose();
}
else
{
	print('<em>'.NO_WANTED_PAGES.'</em>');
}

?>
