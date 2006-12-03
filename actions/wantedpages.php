<?php
/**
 * Display a list of nonexisting pages to which other pages are linking to.
 * 
	* <p>This action lists all pagenames that don't exist but are referred to by other pages on the wiki. By default, the 
	* WikkaInstaller creates a page named WantedPages that uses this action.</p>
	* <p>Those non-existing pages are listed as one line per wanted pages. Each line is composed of 2 parts : The name of 
	* the wanted page in a form of a link: Clicking on this link will let you create the page and start editing its content.
	* Then in brackets, you see the number of pages linking to the wanted page. This number is also in a form of a link:
	* clicking on it will let you see all the pages linking to the wanted page.</p>
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
	* <p>If a parameter linking_to, passed by GET method is identified, the behaviour of this action is quite different: It 
	* acts like the {@link backlinks.php backlinks handler} on the value of this parameter.</p>
	* 
 * @package	Actions
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses	 Wakka::LoadPagesLinkingTo()
 * @uses	 Wakka::Link()
 * @uses	 Wakka::LoadWantedPages()
 * @uses	 Wakka::LoadWantedPages2()
 * @uses	 Wakka::FormOpen()
 * @uses	 Wakka::FormClose()
 */
$linking_to = '';
$sorting_fields = array('count', 'time', 'tag');
if (isset($_REQUEST["linking_to"]))
{
	$linking_to = $_REQUEST["linking_to"];
	if ($pages = $this->LoadPagesLinkingTo($linking_to))
	{
		print("Pages linking to ".$this->Link($linking_to).":<br />\n");
		foreach ($pages as $page)
		{
			print($this->Link($page["tag"]). ' <small>['.$this->Link($page["tag"], 'edit', 'edit')."]</small><br />\n"); #i18n : 3rd param of 2nd Link()
		}
	}
	else
	{
		print("<em>No page is linking to ".$this->Link($linking_to).".</em>"); # i18n
	}
}
else
{
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
					print(" (<a href=\"".$this->href("", "", "linking_to=".$page["tag"])."\">".$page["count"]."</a>)<br />\n");
				}
				else
				{
					preg_match('#/(.*)$#', $page['time'], $match);
					print(' (1 : '.$this->Link($match[1]).' <small>['.$this->Link($match[1], 'edit', 'edit')."]</small>)<br />\n"); #i18n : 3rd param of 2nd Link()
				}
			}
		}
	}
	elseif ($pages = $this->LoadWantedPages())
	{
		foreach ($pages as $page)
		{
			print($this->Link($page["tag"])." (<a href=\"".$this->href("", "", "linking_to=".$page["tag"])."\">".$page["count"]."</a>)<br />\n");
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
		echo '<fieldset id="wantedpages_sorting"><legend>Sorting...</legend>'; #i18n
		for ($i=1; $i<=3; $i++)
		{
			echo '<label>Sorting #'.$i.' : <select name="ob'.$i.'">'.$options.'</select></label> <label><input type="checkbox" name="de'.$i.'" /> desc</label><br />'."\n"; #i18n
		}
		echo '<input type="submit" value="  OK  " />'; #i18n
		echo '</fieldset>';
		echo $this->FormClose();
	}
	else
	{
		print("<em>No wanted pages. Good!</em>"); # i18n
	}
}

?>
