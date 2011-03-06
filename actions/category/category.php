<?php
/**
 * Shows the pages and subcategories belonging to a category.
 * 
 * See WikiCategory to understand how the system works.
 * 
 * @package		Actions
 * @version 	$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @input	string $page optional: the category for which you want to display the pages and categories. Default: current page
 * @input	integer $compact optional: produces a columnar layout with a layout table; 1 produces output in the form of an unordered list. Default: 0
 * @input	integer $col optional: number of columns (for compact=0). Default: 1
 * @output	A html table with pages
 * @uses	Wakka::GetPageTag();
 * @uses	Wakka::FullCategoryTextSearch()
 * @uses	Wakka::FullTextSearch()
 */
 
if ($cattag = $_GET['wakka'])	#312 (only files action uses POST for wakka)
{
	$str ="";
	if (!isset($col)) $col=1;
	if (!isset($compact)) $compact=0;
	if (!isset($page)) $page=$this->GetPageTag(); 
	if ($page=="/") $page="CategoryCategory"; 

//	$page= preg_replace( "/(\w+)\s(\w+)/", "$1$2",$page);
	if (isset($class)) {
		$class="class=\"$class\"";
	} else {
		$class="";
	}
	if (!$page) {$page=$cattag;}

	if ($this->CheckMySQLVersion(4,0,1))
	{
    	$results = $this->FullCategoryTextSearch($page); 
	}
	else
	{
		$utf8Compatible = 0;
		if(1 == $this->config['utf8_compat_search'])
			$utf8Compatible = 1;
    	$results = $this->FullTextSearch($page, 0, $utf8Compatible); 
	}

	if ($results)
	{
		$str = ":\n";
		if (!$compact) $str .= '<br /><br /><table '.$class.' width="100%"><tr>';
		else $str .= '<div '.$class.'><ul>';
		
		$count = 0; 
		$pagecount = 0;
		$list = array();
		
		foreach ($results as $i => $cpage) if($cpage['tag'] != $page) { array_push($list,$cpage['tag']);}
		sort($list);
		while (list($key, $val) = each($list)) {
			if ($count == $col & !$compact)  { $str .= "</tr><tr>"; $count=0; }
			if (!$compact) $str .= '<td>'.$this->Format('[['.$val.']]').'</td>';
			else $str .= '<li>'.$this->Format('[['.$val.' '.preg_replace( "/Category/", "",$val).']]').'</li>';
			$count++;
			$pagecount++;
		}
		$str = sprintf(T_("The following %d page(s)"),$pagecount).$str;
		if (!$compact)  $str .= '</tr></table>'; else $str .= '</ul></div>';
	}
	else $str .= sprintf(T_("Sorry, No items found for %s"),$page);
	print($str);
}
?>
