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
 * <li>page_tag : the name of the wanted page, alphabetically.</li></ul></p>
 * 
 * @package	Actions
 * @version $Id:wantedpages.php 369 2007-03-01 14:38:59Z DarTar $
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses	 Wakka::Link()
 * @uses	 Wakka::LoadWantedPages()
 * @uses	 Wakka::LoadWantedPages2()
 * @uses	 Wakka::FormOpen()
 * @uses	 Wakka::FormClose()
 */

$sorting_fields = array('count', 'time', 'page_tag');
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
			print($this->Link($page['page_tag']));
			if ($page['count'] > 1)
			{ #Use LINKING_PAGES_LINK_TITLE instead of WIKKA_BACKLINKS_LINK_TITLE ?
				print(' (<a href="'.$this->Href('backlinks', $page['page_tag']).'" title="'.sprintf(WIKKA_BACKLINKS_LINK_TITLE, $page['page_tag']).'">'.$page['count']."</a>)<br />\n");
			}
			else
			{
				preg_match('#/(.*)$#', $page['time'], $match);
				$pagetime = $match[1];
				print(' (1 : '.$this->Link($pagetime).' <small>['.$this->Link($pagetime, 'edit', WIKKA_PAGE_EDIT_LINK_DESC, false, true, sprintf(WIKKA_PAGE_EDIT_LINK_TITLE, $pagetime))."]</small>)<br />\n");
			}
		}
	}
}
elseif ($pages = $this->LoadWantedPages())
{
	foreach ($pages as $page)
	{
		print($this->Link($page['page_tag']).' (<a href="'.$this->Href('backlinks', $page['page_tag']).'" title="'.sprintf(WIKKA_BACKLINKS_LINK_TITLE, $page['page_tag']).'">'.$page['count']."</a>)<br />\n");
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
	echo '<fieldset id="wantedpages_sorting"><legend>'.SORTING_LEGEND.'</legend>';
	for ($i=1; $i<=3; $i++)
	{
		echo '<label for="ob'.$i.'">'.sprintf(SORTING_NUMBER_LABEL,$i).'</label> <select id="ob'.$i.'" name="ob'.$i.'">'.$options.'</select>';
		echo ' <input id="de'.$i.'" type="checkbox" name="de'.$i.'" /><label for="de'.$i.'">'.SORTING_DESC_LABEL.'</label><br />'."\n";
	}
	echo '<input type="submit" value="'.OK_BUTTON.'" />';
	echo '</fieldset>';
	echo $this->FormClose();
}
else
{
	print '<em>'.NO_WANTED_PAGES.'</em>';
}

?>
