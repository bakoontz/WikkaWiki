<?php
/**
 * Shows the pages and subcategories belonging to a category.
 * 
 * See WikiCategory to understand how the system works.
 * 
 * @package		Actions
 * @version 	$Id$
 * 
 * @input	string $page optional: the category for which you want to display the pages and categories. Default: current page
 * @input	integer $compact optional: 0 produces a columnar layout with a layout table; 1 produces output in the form of an unordered list. Default: 0
 * @input	integer $col optional: number of columns (for compact=0). Default: 1
 * @output	A html table with pages
 * @uses	Wakka::GetPageTag();
 * @uses	Wakka::ListPages()
 * @uses	Wakka::LoadPagesLinkingTo()
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 */

	$str = "";

	# 'col' option
	$col = 1;
	if(isset($vars['col']))
		$col = $this->htmlspecialchars_ent($vars['col']);

	# 'compact' option
	$compact = 0;
	if(isset($vars['compact']))
		$compact = $this->htmlspecialchars_ent($vars['compact']);

	# 'class' option
	$class = 0;
	if(isset($vars['class']))
		$class = $this->htmlspecialchars_ent($vars['class']);

	# 'page' option
	if(isset($vars['page']))
		$page = $this->htmlspecialchars_ent($vars['page']);
	else
	{
		#232 : if page is not given, and if this action is called from an included page, use the name of the page that is included
		#  and not the caller's PageTag.
		if (isset($this->config['includes']) && is_array($this->GetConfigValue('includes')))
		{
			$count = count($this->GetConfigValue('includes')) - 1;
			$page = $this->config['includes'][$count];
		}
		else
		{
			$page=$this->GetPageTag(); 
		}
	}
	if ($page=="/") $page="CategoryCategory"; 

	$results = $this->LoadPagesLinkingTo($page);

	$errmsg = '<em class="error">'.sprintf(T_("Sorry, No items found for %s"), $page).'</em>';
	$list_pages_option = array(
			'nopagesText' => $errmsg,
			'class' => $class,
			'columns' => $col,
			'sort' => 'no',
			'compact' => $compact
			);
	$str = $this->ListPages($results, $list_pages_option);
	if ($str != $errmsg)
	{
		printf(T_("The following %d page(s) belong to %s").'<br /><br />', count($results), $page);
	}
	print($str);
?>
