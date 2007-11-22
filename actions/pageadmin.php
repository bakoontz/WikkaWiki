<?php

/**
 * Display a module for page management.
 *
 * This action allows admins to display information and perform operations
 * on wiki pages. Pages can be sorted, searched, paged, filtered. Page-related 
 * statistics are given, displaying the number of comments, revisions, backlinks
 * and referrers. Several handlers allow admins to perform specific operation on 
 * single pages. If the current user is not an administrator, the pageindex action
 * is displayed instead.
 *
 * @package		Actions
 * @name		PageAdmin
 *
 * @author		{@link http://wikka.jsnx.com/DarTar Dario Taraborelli}
 * @author		{@link http://wikka.jsnx.com/JavaWoman JavaWoman} (using getCount(); minor tweaks)
 * @author      {@link http://www.wikkawiki.org/BrianKoontz Brian Koontz} (mass page reversion)
 * @version		0.4
 * @since		Wikka 1.1.6.4
 *
 * @input		integer $colcolor  optional: enables color for statistics columns
 *				1: enables colored columns;
 *				0: disables colored columns;
 *				default: 1;
 * @input		integer $rowcolor  optional: enables alternate row colors
 *				1: enables colored rows;
 *				0: disables colored rows;
 *				default: 1;
 *
 * @output		A list of pages available on the current server.
 *
 * @todo	
 *			- mass-operations;
 *			- handlers: rename handler;
 *			- statistics: page hits;
 *			- full-text page search;
 *			- integrate with other admin modules.
 */

//utilities

/**
 * Build an array of numbers consisting of 'ranges' with increasing step size in each 'range'.
 *
 * A list of numbers like this is useful for instance for a dropdown to choose
 * a period expressed in number of days: a difference between 2 and 5 days may
 * be significant while that between 92 and 95 may not be.
 *
 * @author		{@link http://wikka.jsnx.com/JavaWoman JavaWoman}
 * @copyright	Copyright (c) 2005, Marjolein Katsma
 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @version		1.0
 *
 * @param	mixed	$limits	required: single integer or array of integers;
 *					defines the upper limits of the ranges as well as the next step size
 * @param	int		$max	required: upper limit for the whole list
 *					(will be included if smaller than the largest limit)
 * @param	int		$firstinc optional: increment for the first range; default 1
 * @return	array	resulting list of numbers
 */
function optionRanges($limits, $max, $firstinc = 1)
{
	// initializations
	if (is_int($limits)) $limits = array($limits);
	if ($firstinc < 1) $firstinc = 1;
	$opts = array();
	$inc = $firstinc;

	// first element is the first increment
	$opts[] = $inc;
	// each $limit is the upper limit of a 'range'
	foreach ($limits as $limit)
	{
		for ($i = $inc + $inc; $i <= $limit && $i < $max; $i += $inc)
		{
			$opts[] = $i;
		}
		// we quit at $max, even if there are more $limit elements
		if ($limit >= $max)
		{
			// add $max to the list; then break out of the loop
			$opts[] = $max;
			break;
		}
		// when $limit is reached, it becomes the new start and increment for the next 'range'
		$inc = $limit;
	}

	return $opts;
}

// restrict access to admins
if ($this->IsAdmin($this->GetUser())) {

	// -------------------------------------
	// set default values as constants
	define('DEFAULT_RECORDS_LIMIT', '20'); # number of records per page
	define('DEFAULT_MIN_RECORDS_DISPLAY', '5'); # min number of records
	define('DEFAULT_RECORDS_RANGE',serialize(array('10','50','100','500','1000'))); #range array for records pager
	define('DEFAULT_SORT_FIELD', 'time'); # sort field
	define('DEFAULT_SORT_ORDER', 'desc'); # sort order, ascendant or descendant
	define('DEFAULT_START', '0'); # start record
	define('DEFAULT_SEARCH', ''); # keyword to restrict page search
	define('DEFAULT_TAG_LENGTH', '12'); # max. length of displayed pagename
	define('DEFAULT_URL_LENGTH', '15'); # max. length of displayed user host
	define('DEFAULT_TERMINATOR', '&#8230;'); # standard symbol replacing truncated text (ellipsis) JW 2005-07-19
	define('ALTERNATE_ROW_COLOR', '1'); # switch alternate row color
	define('STAT_COLUMN_COLOR', '1'); # switch color for statistics columns
	// last edit range defaults
	define('DEFAULT_START_YEAR', 'YYYY');
	define('DEFAULT_START_MONTH', 'MM');
	define('DEFAULT_START_DAY', 'DD');
	define('DEFAULT_START_HOUR', 'hh');
	define('DEFAULT_START_MINUTE', 'mm');
	define('DEFAULT_START_SECOND', 'ss');
	define('DEFAULT_END_YEAR', 'YYYY');
	define('DEFAULT_END_MONTH', 'MM');
	define('DEFAULT_END_DAY', 'DD');
	define('DEFAULT_END_HOUR', 'hh');
	define('DEFAULT_END_MINUTE', 'mm');
	define('DEFAULT_END_SECOND', 'ss');

	
	// -------------------------------------
	// User-interface: icons
	
	define('HITS_ICON', 'images/icons/16x16/stock_about.png'); 
	define('REVISIONS_ICON', 'images/icons/16x16/stock_book_open.png'); 
	define('COMMENTS_ICON', 'images/icons/16x16/stock_help-agent.png'); 
	define('BACKLINKS_ICON', 'images/icons/16x16/stock_link.png'); 
	define('REFERRERS_ICON', 'images/icons/16x16/stock_internet.png'); 
	
	
	// -------------------------------------
	// User-interface: strings
	
	define('PAGE_TITLE','Page Administration');
	define('FORM_LEGEND','Filter view:');
	define('FORM_SEARCH_STRING_LABEL','Search page:');
	define('FORM_SEARCH_STRING_TITLE','Enter a search string');
	define('FORM_SEARCH_SUBMIT','Submit');
	define('FORM_DATE_RANGE_STRING_LABEL','Last edit range: Between');
	define('FORM_DATE_RANGE_CONNECTOR_LABEL','and');
	define('FORM_PAGER_LABEL_BEFORE','Show');
	define('FORM_PAGER_TITLE','Select records-per-page limit');
	define('FORM_PAGER_LABEL_AFTER','records per page');
	define('FORM_PAGER_SUBMIT','Apply');
	define('FORM_PAGER_LINK','Show records from %d to %d');
	define('FORM_RESULT_INFO','Records');
	define('FORM_RESULT_SORTED_BY','Sorted by:');
	define('TABLE_HEADING_PAGENAME','Page Name');
	define('TABLE_HEADING_PAGENAME_TITLE','Sort by page name');
	define('TABLE_HEADING_OWNER','Owner');
	define('TABLE_HEADING_OWNER_TITLE','Sort by page owner');
	define('TABLE_HEADING_LASTAUTHOR','Last Author');
	define('TABLE_HEADING_LASTAUTHOR_TITLE','Sort by last author');
	define('TABLE_HEADING_LASTEDIT','Last Edit');
	define('TABLE_HEADING_LASTEDIT_TITLE','Sort by edit time');
	define('TABLE_SUMMARY','List of pages on this server');
	define('TABLE_HEADING_HITS_TITLE','Hits');
	define('TABLE_HEADING_REVISIONS_TITLE','Sort by number of revisions (DEBUG ONLY)');
	define('TABLE_HEADING_COMMENTS_TITLE','Comments');
	define('TABLE_HEADING_BACKLINKS_TITLE','Backlinks');
	define('TABLE_HEADING_REFERRERS_TITLE','Referrers');
	define('TABLE_HEADING_HITS_ALT','Hits');
	define('TABLE_HEADING_REVISIONS_ALT','Revisions');
	define('TABLE_HEADING_COMMENTS_ALT','Comments');
	define('TABLE_HEADING_BACKLINKS_ALT','Backlinks');
	define('TABLE_HEADING_REFERRERS_ALT','Referrers');
	define('TABLE_HEADING_ACTIONS','Actions');
	define('ACTION_EDIT_LINK_TITLE','Edit %s');
	define('ACTION_DELETE_LINK_TITLE','Delete %s');
	define('ACTION_CLONE_LINK_TITLE','Clone %s');
	define('ACTION_RENAME_LINK_TITLE','Rename %s (DISABLED)');
	define('ACTION_ACL_LINK_TITLE','Change Access Control List for %s');
	define('ACTION_INFO_LINK_TITLE','Display information and statistics for %s');
	define('ACTION_REVERT_LINK_TITLE','Revert %s to previous version');
	define('ACTION_EDIT_LINK','edit');
	define('ACTION_DELETE_LINK','delete');
	define('ACTION_CLONE_LINK','clone');
	define('ACTION_RENAME_LINK','rename');
	define('ACTION_ACL_LINK','acl');
	define('ACTION_INFO_LINK','info');
	define('ACTION_REVERT_LINK', 'revert');
	define('TAKE_OWNERSHIP_LINK','Take ownership of');
	define('NO_OWNER','(Nobody)');
	define('TABLE_CELL_HITS_TITLE','Hits for %s (%d)');
	define('TABLE_CELL_REVISIONS_TITLE','Display revisions for %s (%d)');
	define('TABLE_CELL_COMMENTS_TITLE','Display comments for %s (%d)');
	define('TABLE_CELL_BACKLINKS_TITLE','Display pages linking to %s (%d)');
	define('TABLE_CELL_REFERRERS_TITLE','Display external sites linking to %s (%d)');
	define('SELECT_RECORD_TITLE','Select %s');
	define('NO_EDIT_NOTE','[No edit note]');
	define('CHECK_ALL_TITLE','Check all records');
	define('CHECK_ALL','Check all');
	define('UNCHECK_ALL_TITLE','Uncheck all records');
	define('UNCHECK_ALL','Uncheck all');
	define('FORM_MASSACTION_LEGEND','Mass-action');
	define('FORM_MASSACTION_LABEL','With selected');
	define('FORM_MASSACTION_SELECT_TITLE','Choose action to apply to selected records (DISABLED)');
	define('FORM_MASSACTION_OPT_DELETE','Delete all');
	define('FORM_MASSACTION_OPT_CLONE','Clone all');
	define('FORM_MASSACTION_OPT_RENAME','Rename all');
	define('FORM_MASSACTION_OPT_ACL','Change Access Control List');
	define('FORM_MASSACTION_OPT_REVERT','Revert to previous page version');
	define('FORM_MASSACTION_SUBMIT','Submit');
	define('ERROR_NO_MATCHES','Sorry, there are no pages matching "%s"');
	
	
	// -------------------------------------
	// Initialize variables
	
	$r = 1; #initialize row counter
	$r_color = ALTERNATE_ROW_COLOR; #get alternate row color option
	$c_color = STAT_COLUMN_COLOR; #get column color option
	// record dropdown
	$page_limits = unserialize(DEFAULT_RECORDS_RANGE);
	// pager
	$prev = '';		
	$next = '';		
	
	//override defaults with action parameters
	if (is_array($vars)) {
		foreach ($vars as $param => $value){
			switch ($param) {
				case 'colcolor':
					$c_color = (preg_match('/[01]/',$value))? $value : STAT_COLUMN_COLOR;
					break;
				case 'rowcolor':
					$r_color = (preg_match('/[01]/',$value))? $value : ALTERNATE_ROW_COLOR;
					break;
			}
		}
	}
	
	// Perform mass-operations if required (forthcoming)
	// Temporarily disabled actions other than massrevert
	if (isset($_GET['action']))
	{
		/*
		if ($_GET['action'] == 'massdelete')
		{
			echo $this->Action('massdelete');
		}
		elseif ($_GET['action'] == 'massrename')
		{
			echo $this->Action('massrename');
		}
		elseif ($_GET['action'] == 'massacls')
		{
			echo $this->Action('massacls');
		}
		*/
		if ($_GET['action'] == 'massrevert')
		{
			$this->IncludeBuffered("revert.php", '', '', "handlers/page");
			$this->Redirect($this->Href());
		}
		else
		{
			// No action selected!
			$this->Redirect($this->Href());
		}
	}
	else
	{
		// process URL variables
		# JW 2005-07-19 some modifications to avoid notices but these are still not actually secure

		// number of records per page
		if (isset($_POST['l']))
			$l = $_POST['l'];
		elseif (isset($_GET['l']))
			$l = $_GET['l'];
		else
			$l = DEFAULT_RECORDS_LIMIT;

		// last edit date range
		$start_YY = (isset($_POST['start_YY'])) ? $this->htmlspecialchars_ent($_POST['start_YY']) : DEFAULT_START_YEAR;
		$start_MM = (isset($_POST['start_MM'])) ? $this->htmlspecialchars_ent($_POST['start_MM']) : DEFAULT_START_MONTH;
		$start_DD = (isset($_POST['start_DD'])) ? $this->htmlspecialchars_ent($_POST['start_DD']) : DEFAULT_START_DAY;
		$start_hh = (isset($_POST['start_hh'])) ? $this->htmlspecialchars_ent($_POST['start_hh']) : DEFAULT_START_HOUR;
		$start_mm = (isset($_POST['start_mm'])) ? $this->htmlspecialchars_ent($_POST['start_mm']) : DEFAULT_START_MINUTE;
		$start_ss = (isset($_POST['start_ss'])) ? $this->htmlspecialchars_ent($_POST['start_ss']) : DEFAULT_START_SECOND;
		$end_YY = (isset($_POST['end_YY'])) ? $this->htmlspecialchars_ent($_POST['end_YY']) : DEFAULT_END_YEAR;
		$end_MM = (isset($_POST['end_MM'])) ? $this->htmlspecialchars_ent($_POST['end_MM']) : DEFAULT_END_MONTH;
		$end_DD = (isset($_POST['end_DD'])) ? $this->htmlspecialchars_ent($_POST['end_DD']) : DEFAULT_END_DAY;
		$end_hh = (isset($_POST['end_hh'])) ? $this->htmlspecialchars_ent($_POST['end_hh']) : DEFAULT_END_HOUR;
		$end_mm = (isset($_POST['end_mm'])) ? $this->htmlspecialchars_ent($_POST['end_mm']) : DEFAULT_END_MINUTE;
		$end_ss = (isset($_POST['end_ss'])) ? $this->htmlspecialchars_ent($_POST['end_ss']) : DEFAULT_END_SECOND;

		// sort field
		$sort = (isset($_GET['sort'])) ? $_GET['sort'] : DEFAULT_SORT_FIELD;
		// sort order
		$d = (isset($_GET['d'])) ? $_GET['d'] : DEFAULT_SORT_ORDER;
		// start record
		$s = (isset($_GET['s'])) ? $_GET['s'] : DEFAULT_START;

		// search string
		if (isset($_POST['q']))
			$q = $_POST['q'];
		elseif (isset($_GET['q']))
			$q = $_GET['q'];
		else
			$q = DEFAULT_SEARCH;

		// select all	added JW 2005-07-19
		$checked = '';
		if (isset($_GET['selectall']))
		{
			$checked = (1 == $_GET['selectall']) ? ' checked="checked"' : '';
		}

		// print page header
		echo $this->Format('==== '.PAGE_TITLE.' ==== --- ');

		// build pager form	
		$form1 = $this->FormOpen('','','post','page_admin_panel');
		$form1 .= '<fieldset><legend>'.FORM_LEGEND.'</legend>'."\n";
		$form1 .= '<label for="q">'.FORM_SEARCH_STRING_LABEL.'</label> <input type ="text" id="q" name="q" title="'.FORM_SEARCH_STRING_TITLE.'" size="20" maxlength="50" value="'.$q.'"/> <input type="submit" value="'.FORM_SEARCH_SUBMIT.'" /><br />'."\n";
		// build date range fields
		$form1 .= '<label for="d">'.FORM_DATE_RANGE_STRING_LABEL.'</label>&nbsp;<input type="text" name="start_YY" size="4" maxlength="4" value="'.$start_YY.'"/>-<input type="text" name="start_MM" size="2" maxlength="2" value="'.$start_MM.'"/>-<input type="text" name="start_DD" size="2" maxlength="2" value="'.$start_DD.'"/>&nbsp;<input type="text" name="start_hh" size="2" maxlength="2" value="'.$start_hh.'"/>:<input type="text" name="start_mm" size="2" maxlength="2" value="'.$start_mm.'"/>:<input type="text" name="start_ss" size="2" maxlength="2" value="'.$start_ss.'"/>&nbsp;'.FORM_DATE_RANGE_CONNECTOR_LABEL.'&nbsp;<input type="text" name="end_YY" size="4" maxlength="4" value="'.$end_YY.'"/>-<input type="text" name="end_MM" size="2" maxlength="2" value="'.$end_MM.'"/>-<input type="text" name="end_DD" size="2" maxlength="2" value="'.$end_DD.'"/>&nbsp;<input type="text" name="end_hh" size="2" maxlength="2" value="'.$end_hh.'"/>:<input type="text" name="end_mm" size="2" maxlength="2" value="'.$end_mm.'"/>:<input type="text" name="end_ss" size="2" maxlength="2" value="'.$end_ss.'"/><br />'."\n";

		// check for/validate last date edit range
		$start_ts = '';
		$end_ts = '';
		if(!empty($_GET['start_ts']) && !empty($_GET['end_ts']))
		{
			$start_ts = $this->htmlspecialchars_ent($_GET['start_ts']);
			$end_ts = $this->htmlspecialchars_ent($_GET['end_ts']); 
		}
		elseif (is_numeric($start_YY) && $start_YY > 1000 && $start_YY < 9999 &&
				   is_numeric($start_MM) && $start_MM > 0 && $start_MM < 13 &&
				   is_numeric($start_DD) && $start_DD > 0 && $start_DD < 32)
		{
			$start_ts = mysql_real_escape_string($start_YY);
			$start_ts .= '-';
			$start_ts .= mysql_real_escape_string($start_MM);
			$start_ts .= '-';
			$start_ts .= mysql_real_escape_string($start_DD);
			if(is_numeric($start_hh) && $start_hh >= 0 && $start_hh <=24)
			{
				$start_ts .= ' '.mysql_real_escape_string($start_hh).':';
				if(is_numeric($start_mm) && $start_mm >= 0 && $start_mm <= 59)
				{
					$start_ts .= mysql_real_escape_string($start_mm).':';
					if(is_numeric($start_ss) && $start_ss >= 0 && $start_ss <= 59)
					{
						$start_ts .= mysql_real_escape_string($start_ss);
					}
					else
					{
						$start_ts .= '00';
					}
				}
				else
				{
					$start_ts .= '00:00';
				}
			}
			if(is_numeric($end_YY) && $end_YY > 1000 && $end_YY < 9999 &&
			   is_numeric($end_MM) && $end_MM > 0 && $end_MM < 13 &&
			   is_numeric($end_DD) && $end_DD > 0 && $end_DD < 32)
			{
				$end_ts = mysql_real_escape_string($end_YY);
				$end_ts .= '-';
				$end_ts .= mysql_real_escape_string($end_MM);
				$end_ts .= '-';
				$end_ts .= mysql_real_escape_string($end_DD);
				if(is_numeric($end_hh) && $end_hh >= 0 && $end_hh <=24)
				{
					$end_ts .= ' '.mysql_real_escape_string($end_hh).':';
					if(is_numeric($end_mm) && $end_mm >= 0 && $end_mm <= 59)
					{
						$end_ts .= mysql_real_escape_string($end_mm).':';
						if(is_numeric($end_ss) && $end_ss >= 0 && $end_ss <= 59)
						{
							$end_ts .= mysql_real_escape_string($end_ss);
						}
						else
						{
							$end_ts .= '00';
						}
					}
					else
					{
						$end_ts .= '00:00';
					}
				}
			}
			else
			{
				$end_ts  = strftime("%Y-%m-%d %T");
			}
		}

		// restrict MySQL query by search string	 modified JW 2005-07-19
		$where = ('' == $q) ? "`latest` = 'Y'" : "`tag` LIKE '%".$q."%' AND `latest` = 'Y'";
		if(!empty($start_ts) && !empty($end_ts))
		{
			$where .= " AND time > '".$start_ts."' AND time < '".$end_ts."'"; 
		}
		// get total number of pages
		$numpages = $this->getCount('pages',$where);

		// ranged drop-down
		$pages_opts = optionRanges($page_limits,$numpages,DEFAULT_MIN_RECORDS_DISPLAY);
		$form1 .= '<label for="l">'.FORM_PAGER_LABEL_BEFORE.'</label> '."\n";
		$form1 .= '<select name="l" id="l" title="'.FORM_PAGER_TITLE.'">'."\n";
		// build drop-down
		foreach ($pages_opts as $opt) {
			$selected = ($opt == $l) ? ' selected="selected"' : '';
			$form1 .= '<option value="'.$opt.'"'.$selected.'>'.$opt.'</option>'."\n";
		}
		$form1 .=  '</select> <label for="l">'.FORM_PAGER_LABEL_AFTER.'</label> <input type="submit" value="'.FORM_PAGER_SUBMIT.'" /><br />'."\n";

		// build pager links
		if ($s > 0)
			$prev = '<a href="' .$this->Href('','','l='.$l.'&amp;sort='.$sort.'&amp;d='.$d.'&amp;s='.($s-$l)).'&amp;q='.$q.'&amp;start_ts='.urlencode($start_ts).'&amp;end_ts='.urlencode($end_ts).'" title="'.sprintf(FORM_PAGER_LINK, ($s-$l+1), $s).'">'.($s-$l+1).'-'.$s.'</a> |  '."\n";
		if ($numpages > ($s + $l))
			$next = ' | <a href="'.$this->Href('','','l='.$l.'&amp;sort='.$sort.'&amp;d='.$d.'&amp;s='.($s+$l)).'&amp;q='.$q.'&amp;start_ts='.urlencode($start_ts).'&amp;end_ts='.urlencode($end_ts).'" title="'.sprintf(FORM_PAGER_LINK, ($s+$l+1), ($s+2*$l)).'">'.($s+$l+1).'-'.($s+2*$l).'</a>'."\n";
		$form1 .= FORM_RESULT_INFO.' ('.$numpages.'): '.$prev.($s+1).'-'.($s+$l).$next.'<br />'."\n";
		$form1 .= '('.FORM_RESULT_SORTED_BY.'<em>'.$sort.', '.$d.'</em>)'."\n";
		$form1 .= '</fieldset>'.$this->FormClose()."\n";

		// print form
		echo $form1;

		// sort by counted values
		switch($sort) 
		{
			case 'edits': #alpha --- 'latest' needs to be disabled
				//sample query:
				//SELECT *, COUNT(*) as edits FROM `wikka1160_pages` GROUP BY tag ORDER BY edits DESC
				$count = ', COUNT(*) as edits';
				$group = 'GROUP BY tag';	
				$where .= 'AND 1';
				//$where = ('' == $q) ? "1" : "`tag` LIKE '%".$q."%'";
				$table = 'pages';	
				break;
			case 'comments': #to implement
			/*
				// SELECT wikka1160_pages.tag, COUNT(  *  )  AS comments FROM wikka1160_pages, wikka1160_comments WHERE wikka1160_pages.tag = wikka1160_comments.page_tag GROUP  BY wikka1160_pages.tag ORDER  BY comments DESC 
				$count = ', COUNT(*) as edits';
				$group = 'GROUP BY tag';	
				$where = '1';
			*/	
				break;
			default:
				$table = 'pages';	
		}

		$query = "SELECT *".$count." FROM ".$this->config['table_prefix'].$table." WHERE ".  $where." ".$group." ORDER BY ".$sort." ".$d." LIMIT ".$s.", ".$l;
		$pagedata = $this->LoadAll($query);

		if ($pagedata)
		{
			// build table headers
			$tagheader = '<a href="'.$this->Href('','', (($sort == 'tag' && $d == 'asc')? 'l='.$l.'&amp;sort=tag&amp;d=desc&amp;q='.$q.'&amp;start_ts='.urlencode($start_ts).'&amp;end_ts='.urlencode($end_ts) : 'l='.$l.'&amp;sort=tag&amp;d=asc&amp;q='.$q.'&amp;start_ts='.urlencode($start_ts).'&amp;end_ts='.urlencode($end_ts))).'" title="'.TABLE_HEADING_PAGENAME_TITLE.'">'.TABLE_HEADING_PAGENAME.'</a>';
			$ownerheader = '<a href="'.$this->Href('','', (($sort == 'owner' && $d == 'asc')? 'l='.$l.'&amp;sort=owner&amp;d=desc&amp;q='.$q.'&amp;start_ts='.urlencode($start_ts).'&amp;end_ts='.urlencode($end_ts) : 'l='.$l.'&amp;sort=owner&amp;d=asc&amp;q='.$q.'&amp;start_ts='.urlencode($start_ts).'&amp;end_ts='.urlencode($end_ts))).'" title="'.TABLE_HEADING_OWNER_TITLE.'">'.TABLE_HEADING_OWNER.'</a>';
			$userheader = '<a href="'.$this->Href('','', (($sort == 'user' && $d == 'asc')? 'l='.$l.'&amp;sort=user&amp;d=desc&amp;q='.$q.'&amp;start_ts='.urlencode($start_ts).'&amp;end_ts='.urlencode($end_ts) : 'l='.$l.'&amp;sort=user&amp;d=asc&amp;q='.$q.'&amp;start_ts='.urlencode($start_ts).'&amp;end_ts='.urlencode($end_ts))).'" title="'.TABLE_HEADING_LASTAUTHOR_TITLE.'">'.TABLE_HEADING_LASTAUTHOR.'</a>';
			$lasteditheader = '<a href="'.$this->Href('','', (($sort == 'time' && $d == 'desc')? 'l='.$l.'&amp;sort=time&amp;d=asc&amp;q='.$q.'&amp;start_ts='.urlencode($start_ts).'&amp;end_ts='.urlencode($end_ts) : 'l='.$l.'&amp;sort=time&amp;d=desc&amp;q='.$q.'&amp;start_ts='.urlencode($start_ts).'&amp;end_ts='.urlencode($end_ts))).'" title="'.TABLE_HEADING_LASTEDIT_TITLE.'">'.TABLE_HEADING_LASTEDIT.'</a>';
			$revisionsheader = '<a href="'.$this->Href('','', (($sort == 'edits' && $d == 'desc')? 'l='.$l.'&amp;sort=edits&amp;d=asc&amp;q='.$q.'&amp;start_ts='.urlencode($start_ts).'&amp;end_ts='.urlencode($end_ts) : 'l='.$l.'&amp;sort=edits&amp;d=desc&amp;q='.$q.'&amp;start_ts='.urlencode($start_ts).'&amp;end_ts='.urlencode($end_ts))).'" title="'.TABLE_HEADING_REVISIONS_TITLE.'"><img src="'.REVISIONS_ICON.'" alt="'.TABLE_HEADING_REVISIONS_ALT.'"/></a>';

			$htmlout = "<table summary=\"".TABLE_SUMMARY."\" border=\"1px\" id=\"admin_table\">\n".
			"<thead>\n<tr>\n".
			"    <th>&nbsp;</th>\n".
			"    <th>".$tagheader."</th>\n".
			"    <th>".$ownerheader."</th>\n".
			"    <th>".$userheader."</th>\n".
			"    <th>".$lasteditheader."</th>\n".
			"    <th class=\"number ".(($c_color == 1)? ' c1' : '')."\" title=\"".TABLE_HEADING_HITS_TITLE."\"><img src=\"".HITS_ICON."\" alt=\"".TABLE_HEADING_HITS_ALT."\"/></th>\n".
			"    <th class=\"number ".(($c_color == 1)? ' c2' : '')."\" title=\"".TABLE_HEADING_REVISIONS_TITLE."\">".$revisionsheader."</th>\n".
			"    <th class=\"number ".(($c_color == 1)? ' c3' : '')."\" title=\"".TABLE_HEADING_COMMENTS_TITLE."\"><img src=\"".COMMENTS_ICON."\" alt=\"".TABLE_HEADING_COMMENTS_ALT."\"/></th>\n".
			"    <th class=\"number ".(($c_color == 1)? ' c4' : '')."\" title=\"".TABLE_HEADING_BACKLINKS_TITLE."\"><img src=\"".BACKLINKS_ICON."\" alt=\"".TABLE_HEADING_BACKLINKS_ALT."\"/></th>\n".
			"    <th class=\"number ".(($c_color == 1)? ' c5' : '')."\" title=\"".TABLE_HEADING_REFERRERS_TITLE."\"><img src=\"".REFERRERS_ICON."\" alt=\"".TABLE_HEADING_REFERRERS_ALT."\"/></th>\n".
			"    <th class=\"center\">".TABLE_HEADING_ACTIONS."</th>\n".
			"  </tr>\n</thead>\n";

			// feed table with data
			foreach($pagedata as $page)
			{
				// truncate long page names
				$pagename = (strlen($page['tag']) > DEFAULT_TAG_LENGTH) ? substr($page['tag'], 0, DEFAULT_TAG_LENGTH).DEFAULT_TERMINATOR : $page['tag'];

				// build handler links
				$lastedit = $page['time'];
				if ($pagename != $page['tag'])
				{
					$showpage = '<a href="'.$this->Href('',$page['tag'], '').'" title="'.$page['tag'].'">'.$pagename.'</a>';
				}
				else
				{
					$showpage = '<a href="'.$this->Href('',$page['tag'], '').'">'.$pagename.'</a>';
				}
				$editpage = '<a href="'.$this->Href('edit',$page['tag'], '').'" title="'.sprintf(ACTION_EDIT_LINK_TITLE, $page['tag']).'">'.ACTION_EDIT_LINK.'</a>';
				$deletepage = '<a href="'.$this->Href('delete',$page['tag'], '').'" title="'.sprintf(ACTION_DELETE_LINK_TITLE, $page['tag']).'">'.ACTION_DELETE_LINK.'</a>';
				$clonepage = '<a href="'.$this->Href('clone',$page['tag'], '').'" title="'.sprintf(ACTION_CLONE_LINK_TITLE, $page['tag']).'">'.ACTION_CLONE_LINK.'</a>';
				// renaming disabled
				$renamepage = '<a href="'.$this->Href('rename',$page['tag'], '').'" title="'.sprintf(ACTION_RENAME_LINK_TITLE, $page['tag']).'">'.ACTION_RENAME_LINK.'</a>';
				$aclpage = '<a href="'.$this->Href('acls',$page['tag'], '').'" title="'.sprintf(ACTION_ACL_LINK_TITLE, $page['tag']).'">'.ACTION_ACL_LINK.'</a>';
				$infopage = '<a href="'.$this->Href('info',$page['tag'], '').'" title="'.sprintf(ACTION_INFO_LINK_TITLE, $page['tag']).'">'.ACTION_INFO_LINK.'</a>';
				$revertpage = '<a href="'.$this->Href('revert',$page['tag'], '').'" title="'.sprintf(ACTION_REVERT_LINK_TITLE, $page['tag']).'">'.ACTION_REVERT_LINK.'</a>';


				// get page owner
				if ($page['owner'])
				{
					// is the owner a registered user?
					if ($this->LoadUser($page['owner']))
					{
						// does user's homepage exist?
						if ($this->ExistsPage($page['owner']))
						{
							$owner = $this->Link($page['owner']);
						}
						else
						{
							$owner = $page['owner'];
						}
					}
					else
					{
						$owner = $page['owner'];
					}
				}
				else
				{
					// page has empty owner field: print claim link
					$owner = $this->Link($page['tag'], 'claim','(Nobody)','','',TAKE_OWNERSHIP_LINK.' '.$page['tag']);
				}
				// get last author
				if ($page['user'])
				{
					// is the author a registered user?
					if ($this->LoadUser($page['user']))
					{
						// does user's homepage exist?
						if ($this->ExistsPage($page['user']))
						{
							$user = $this->Link($page['user']);
						}
						else
						{
							$user = $page['user'];
						}
					}
					else
					{
						// truncate long host names
						$user = (strlen($page['user']) > DEFAULT_URL_LENGTH) ? substr($page['user'], 0, DEFAULT_URL_LENGTH).DEFAULT_TERMINATOR : $page['user'];
						# added  JW 2005-07-19
						if ($user != $page['user'])
						{
							$user = '<span title="'.$page['user'].'">'.$user.'</span>';
						}
					}
				}
				else
				{
					// page has empty user field
					$user = NO_OWNER;
				}

				// get counts	- JW 2005-07-19
				$whereTag		= "`tag` = '".$page['tag']."'";
				$wherePageTag	= "`page_tag` = '".$page['tag']."'";
				$whereToTag		= "`to_tag` = '".$page['tag']."'";
				$hn = 0;
				$rv = $this->getCount('pages',$whereTag);
				$cn = $this->getCount('comments',$wherePageTag);
				$bn = $this->getCount('links',$whereToTag);
				$rn = $this->getCount('referrers',$wherePageTag);

				// get page hits (forthcoming)
				$hitspage = ($hn > 0) ? '<a href="'.$this->Href('hits',$page['tag'], '').'" title="'.sprintf(TABLE_CELL_HITS_TITLE, $page['tag'], $hn).'">'.$hn.'</a>' : '0';

				// get page revisions and create revision link if needed
				$revpage = ($rv > 0) ? '<a href="'.$this->Href('revisions',$page['tag'], '').'" title="'.sprintf(TABLE_CELL_REVISIONS_TITLE, $page['tag'], $rv).'">'.$rv.'</a>' : '0';

				// get page comments and create comments link if needed
				$commentspage = ($cn > 0) ? '<a href="'.$this->Href('',$page['tag'], 'show_comments=1#comments').'" title="'.sprintf(TABLE_CELL_COMMENTS_TITLE, $page['tag'], $cn).'">'.$cn.'</a>' : '0';

				// get page backlinks and create backlinks link
				$backlinkpage = ($bn > 0) ? '<a href="'.$this->Href('backlinks',$page['tag'], '').'" title="'.sprintf(TABLE_CELL_BACKLINKS_TITLE, $page['tag'], $bn).'">'.$bn.'</a>' : '0';

				// get page referrers and create referrer link
				$refpage = ($rn > 0) ? '<a href="'.$this->Href('referrers',$page['tag'], '').'" title="'.sprintf(TABLE_CELL_REFERRERS_TITLE, $page['tag'], $rn).'">'.$rn.'</a>' : '0';

				// build table body
				$htmlout .= "<tbody>\n";
				if ($r_color == 1) { 
					$htmlout .= "<tr ".(($r%2)? '' : 'class="alt"').">\n"; #enable alternate row color
				} else {
					$htmlout .= "<tr>\n"; #disable alternate row color
				}
				$htmlout .="    <td><input type=\"checkbox\" name=\"id_".$page['id']."\"".$checked." title=\"".sprintf(SELECT_RECORD_TITLE, $page['tag'])."\"/></td>\n".	# modified JW 2005-07-19
				"    <td>".$showpage."</td>\n".
				"    <td>".$owner."</td>\n".
				"    <td>".$user."</td>\n".
				"    <td class=\"time\" ".((strlen($page['note'])>0)? 'title="['.$page['note'].']"' : 'title="'.NO_EDIT_NOTE.'"').">".$lastedit."</td>\n".
				"    <td class=\"number ".(($c_color == 1)? ' c1' : '')."\">".$hitspage."</td>\n".
				"    <td class=\"number ".(($c_color == 1)? ' c2' : '')."\">".$revpage."</td>\n".
				"    <td class=\"number ".(($c_color == 1)? ' c3' : '')."\">".$commentspage."</td>\n".
				"    <td class=\"number ".(($c_color == 1)? ' c4' : '')."\">".$backlinkpage."</td>\n".
				"    <td class=\"number ".(($c_color == 1)? ' c5' : '')."\">".$refpage."</td>\n".
				"    <td class=\"center \">".$editpage." :: ".$deletepage." :: ".$clonepage." :: "./*$renamepage*." :: ".*/$aclpage." :: ".$infopage." :: ".$revertpage."</td>\n".
				"  </tr>\n</tbody>\n";

				//increase row counter    ----- alternate row colors
				if ($r_color == 1) $r++;
				}
			$htmlout .= '</table>'."\n";
			// print the table
			echo $this->FormOpen('','','get');
			echo $htmlout;

			// multiple-page operations (forthcoming)		JW 2005-07-19 accesskey removed (causes more problems than it solves)
			echo '<fieldset><legend>'.FORM_MASSACTION_LEGEND.'</legend>';
			echo '[<a href="'.$this->Href('','','l='.$l.'&amp;sort='.$sort.'&amp;d='.$d.'&amp;s='.$s.'&amp;q='.$q.'&amp;start_ts='.urlencode($start_ts).'&amp;end_ts='.urlencode($end_ts).'&amp;selectall=1').'" title="'.CHECK_ALL_TITLE.'">'.CHECK_ALL.'</a> | <a href="'.$this->Href('','','l='.$l.'&amp;sort='.$sort.'&amp;d='.$d.'&amp;s='.$s.'&amp;q='.$q.'&amp;start_ts='.urlencode($start_ts).'&amp;end_ts='.urlencode($end_ts).'&amp;selectall=0').'" title="'.UNCHECK_ALL_TITLE.'">'.UNCHECK_ALL.'</a>]<br />';
			echo '<label for="action" >'.FORM_MASSACTION_LABEL.'</label> <select title="'.FORM_MASSACTION_SELECT_TITLE.'" id="action" name="action">';
			echo '<option value="" selected="selected">---</option>';
			// Temporarily disabling all but massrevert
			/*
			echo '<option value="massdelete">'.FORM_MASSACTION_OPT_DELETE.'</option>';
			echo '<option value="massclone">'.FORM_MASSACTION_OPT_CLONE.'</option>';
			echo '<option value="massrename">'.FORM_MASSACTION_OPT_RENAME.'</option>';
			echo '<option value="massacls">'.FORM_MASSACTION_OPT_ACL.'</option>';
			*/
			echo '<option value="massrevert">'.FORM_MASSACTION_OPT_REVERT.'</option>';
			echo '</select> <input type="submit" value="'.FORM_MASSACTION_SUBMIT.'" />';
			echo '</fieldset>';
			echo $this->FormClose();
		}
		else
		{
			// no records matching the search string: print error message
			echo '<p><span class="error">'.sprintf(ERROR_NO_MATCHES, $q).'</span></p>';
		}
	}
}
else
{
	// current user is not admin: show plain page index
	echo $this->Action('pageindex');
}
?>
