<?php
/**
 * Shows the pages and subcategories belonging to a category.
 *
 * See WikiCategory to understand how the system works.
 *
 * @package		Actions
 * @version		$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	Wakka::GetPageTag();
 * @uses	Wakka::ListPages()
 * @uses	Wakka::LoadPagesLinkingTo()
 *
 * @input	string	$page		optional: the category for which you want to display the pages and categories. Default: current page
 * @input	integer	$compact	optional: produces a columnar layout with a layout table; 1 produces output in the form of an unordered list. Default: 0
 * @input	integer	$col		optional: number of columns (for compact=0). Default: 1
 * @input	integer	$class		optional: class to be applied to HTML output structure
 * @output	string	A html table or unordered list with pages
 * @todo	replace with advanced category action (which not only produces
 *			better output but also solves bugs)
 */
if ($tag = $_GET['wakka'])	#312 (only files action uses POST for wakka)
{
	// init
	$str ='';
	$thispage = (isset($this->_included_page)) ? $this->_included_page : $this->GetPageTag();
	// @@@	BUT see #232 comment 4 - this is actually a bug!
	//		The "current" page is not necessarily a category page.

	// get parameters and set defaults
	// @@@ parameters are not sanitized!!
	if (!isset($page))
	{
		$page = $thispage;
	}
	if ($page == '/')
	{
		$page = "CategoryCategory";	// top level category
	}
	if (!isset($col))
	{
		$col = 1;
	}
	if (!isset($compact))
	{
		$compact = 0;
	}
	if (!isset($class))
	{
		$class = '';
	}

	#if (!isset($page)) $page=$this->GetPageTag();
	// next line: #232 - partial fix only!
	#if (isset($this->_included_page)) $page = $this->_included_page;

	// default to current page as (assumed) category
	#if (!$page) {$page=$cattag;}	// this duplicates the $this->GetPageTag() above!

	$results = $this->LoadPagesLinkingTo($page);
	$errmsg = '<em class="error">'.sprintf(ERROR_NO_PAGES, $page).'</em>';
	$str = $this->ListPages($results, $errmsg, $class, $col, $compact);

	if ($str != $errmsg)
	{
		echo '<p>'.sprintf(PAGES_BELONGING_TO, count($results), $page).'</p>'."\n";
	}
	echo $str;
}
?>