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
 * @version		$Id$
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 * @author		{@link http://wikkawiki.org/JavaWoman JavaWoman} (using getCount(); minor tweaks)
 * @author      {@link http://wikkawiki.org/BrianKoontz Brian Koontz} (mass page reversion)
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
 * @output		A module to manage wiki pages.
 *
 * @todo	
 *			- sanitize URL parameters;
 *			- apply FormatUser();
 *			- port all the dependencies (CSS, icons, handlers);
 * 			- mass-operations;
 *			- handlers: rename handler;
 *			- statistics: page hits;
 *			- full-text page search;
 *			- integrate with other admin modules.
 * 			- tie to a new default page (AdminPages);
 * 			- move i18n strings to lang in 1.1.7;
 * 			- move icons to buddy file or action folder in 1.1.7;
 */


include_once('libs/admin.lib.php');

//utilities

/**
 * Build an array of numbers consisting of 'ranges' with increasing step size in each 'range'.
 *
 * A list of numbers like this is useful for instance for a dropdown to choose
 * a period expressed in number of days: a difference between 2 and 5 days may
 * be significant while that between 92 and 95 may not be.
 *
 * @author		{@link http://wikkawiki.org/JavaWoman JavaWoman}
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
 * 
 * @todo	find a better name
 * @todo	move to core
 */
if(!function_exists('optionRanges'))
{
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
}

// restrict access to admins
if ($this->IsAdmin($this->GetUser()))
{

	// -------------------------------------
	// set default values as constants
	define('ADMINPAGES_DEFAULT_RECORDS_LIMIT', '20'); # number of records per page
	define('ADMINPAGES_DEFAULT_MIN_RECORDS_DISPLAY', '5'); # min number of records
	define('ADMINPAGES_DEFAULT_RECORDS_RANGE',serialize(array('10','50','100','500','1000'))); #range array for records pager
	define('ADMINPAGES_DEFAULT_SORT_FIELD', 'time'); # sort field
	define('ADMINPAGES_DEFAULT_SORT_ORDER', 'desc'); # sort order, ascendant or descendant
	define('ADMINPAGES_DEFAULT_START', '0'); # start record
	define('ADMINPAGES_DEFAULT_SEARCH', ''); # keyword to restrict page search
	define('ADMINPAGES_DEFAULT_TAG_LENGTH', '12'); # max. length of displayed pagename
	define('ADMINPAGES_DEFAULT_URL_LENGTH', '15'); # max. length of displayed user host
	define('ADMINPAGES_DEFAULT_TERMINATOR', '&#8230;'); # standard symbol replacing truncated text (ellipsis) JW 2005-07-19
	define('ADMINPAGES_ALTERNATE_ROW_COLOR', '1'); # switch alternate row color
	define('ADMINPAGES_STAT_COLUMN_COLOR', '1'); # switch color for statistics columns
	// last edit range defaults
	define('ADMINPAGES_DEFAULT_START_YEAR', 'YYYY');
	define('ADMINPAGES_DEFAULT_START_MONTH', 'MM');
	define('ADMINPAGES_DEFAULT_START_DAY', 'DD');
	define('ADMINPAGES_DEFAULT_START_HOUR', 'hh');
	define('ADMINPAGES_DEFAULT_START_MINUTE', 'mm');
	define('ADMINPAGES_DEFAULT_START_SECOND', 'ss');
	define('ADMINPAGES_DEFAULT_END_YEAR', 'YYYY');
	define('ADMINPAGES_DEFAULT_END_MONTH', 'MM');
	define('ADMINPAGES_DEFAULT_END_DAY', 'DD');
	define('ADMINPAGES_DEFAULT_END_HOUR', 'hh');
	define('ADMINPAGES_DEFAULT_END_MINUTE', 'mm');
	define('ADMINPAGES_DEFAULT_END_SECOND', 'ss');
	define('ADMINPAGES_MAX_EDIT_NOTE_LENGTH', '50');
	
	// -------------------------------------
	// User-interface: icons

	define('ADMINPAGES_REVISIONS_ICON', 'images/icons/edit.png'); 
	define('ADMINPAGES_COMMENTS_ICON', 'images/icons/comment.png');
	define('ADMINPAGES_HITS_ICON', 'images/icons/star.png'); 
	define('ADMINPAGES_BACKLINKS_ICON', 'images/icons/link.png'); 
	define('ADMINPAGES_REFERRERS_ICON', 'images/icons/world.png'); 
	
	// -------------------------------------
	// User-interface: strings
	
	define('ADMINPAGES_PAGE_TITLE','Page Administration');
	define('ADMINPAGES_FORM_LEGEND','Filter view:');
	define('ADMINPAGES_FORM_SEARCH_STRING_LABEL','Search page:');
	define('ADMINPAGES_FORM_SEARCH_STRING_TITLE','Enter a search string');
	define('ADMINPAGES_FORM_SEARCH_SUBMIT','Submit');
	define('ADMINPAGES_FORM_DATE_RANGE_STRING_LABEL','Last edit range: Between');
	define('ADMINPAGES_FORM_DATE_RANGE_CONNECTOR_LABEL','and');
	define('ADMINPAGES_FORM_PAGER_LABEL_BEFORE','Show');
	define('ADMINPAGES_FORM_PAGER_TITLE','Select records-per-page limit');
	define('ADMINPAGES_FORM_PAGER_LABEL_AFTER','records per page');
	define('ADMINPAGES_FORM_PAGER_SUBMIT','Apply');
	define('ADMINPAGES_FORM_PAGER_LINK','Show records from %d to %d');
	define('ADMINPAGES_FORM_RESULT_INFO','Records');
	define('ADMINPAGES_FORM_RESULT_SORTED_BY','Sorted by:');
	define('ADMINPAGES_TABLE_HEADING_PAGENAME','Page Name');
	define('ADMINPAGES_TABLE_HEADING_PAGENAME_TITLE','Sort by page name');
	define('ADMINPAGES_TABLE_HEADING_OWNER','Owner');
	define('ADMINPAGES_TABLE_HEADING_OWNER_TITLE','Sort by page owner');
	define('ADMINPAGES_TABLE_HEADING_LASTAUTHOR','Last Author');
	define('ADMINPAGES_TABLE_HEADING_LASTAUTHOR_TITLE','Sort by last author');
	define('ADMINPAGES_TABLE_HEADING_LASTEDIT','Last Edit');
	define('ADMINPAGES_TABLE_HEADING_LASTEDIT_TITLE','Sort by edit time');
	define('ADMINPAGES_TABLE_SUMMARY','List of pages on this server');
	define('ADMINPAGES_TABLE_HEADING_HITS_TITLE','Hits');
	define('ADMINPAGES_TABLE_HEADING_REVISIONS_TITLE','Revisions');
	define('ADMINPAGES_TABLE_HEADING_COMMENTS_TITLE','Comments');
	define('ADMINPAGES_TABLE_HEADING_BACKLINKS_TITLE','Backlinks');
	define('ADMINPAGES_TABLE_HEADING_REFERRERS_TITLE','Referrers');
	define('ADMINPAGES_TABLE_HEADING_HITS_ALT','Hits');
	define('ADMINPAGES_TABLE_HEADING_REVISIONS_ALT','Revisions');
	define('ADMINPAGES_TABLE_HEADING_COMMENTS_ALT','Comments');
	define('ADMINPAGES_TABLE_HEADING_BACKLINKS_ALT','Backlinks');
	define('ADMINPAGES_TABLE_HEADING_REFERRERS_ALT','Referrers');
	define('ADMINPAGES_TABLE_HEADING_ACTIONS','Actions');
	define('ADMINPAGES_ACTION_EDIT_LINK_TITLE','Edit %s');
	define('ADMINPAGES_ACTION_DELETE_LINK_TITLE','Delete %s');
	define('ADMINPAGES_ACTION_CLONE_LINK_TITLE','Clone %s');
	define('ADMINPAGES_ACTION_RENAME_LINK_TITLE','Rename %s (DISABLED)');
	define('ADMINPAGES_ACTION_ACL_LINK_TITLE','Change Access Control List for %s');
	/* define('ADMINPAGES_ACTION_INFO_LINK_TITLE','Display information and statistics for %s'); */ #not implemented yet
	define('ADMINPAGES_ACTION_REVERT_LINK_TITLE','Revert %s to previous version');
	define('ADMINPAGES_ACTION_EDIT_LINK','edit');
	define('ADMINPAGES_ACTION_DELETE_LINK','delete');
	define('ADMINPAGES_ACTION_CLONE_LINK','clone');
	define('ADMINPAGES_ACTION_RENAME_LINK','rename');
	define('ADMINPAGES_ACTION_ACL_LINK','acl');
	define('ADMINPAGES_ACTION_INFO_LINK','info');
	define('ADMINPAGES_ACTION_REVERT_LINK', 'revert');
	define('ADMINPAGES_TAKE_OWNERSHIP_LINK','Take ownership of');
	define('ADMINPAGES_NO_OWNER','(Nobody)');
	define('ADMINPAGES_TABLE_CELL_HITS_TITLE','Hits for %s (%d)');
	define('ADMINPAGES_TABLE_CELL_REVISIONS_TITLE','Display revisions for %s (%d)');
	define('ADMINPAGES_TABLE_CELL_COMMENTS_TITLE','Display comments for %s (%d)');
	define('ADMINPAGES_TABLE_CELL_BACKLINKS_TITLE','Display pages linking to %s (%d)');
	define('ADMINPAGES_TABLE_CELL_REFERRERS_TITLE','Display external sites linking to %s (%d)');
	define('ADMINPAGES_SELECT_RECORD_TITLE','Select %s');
	define('ADMINPAGES_NO_EDIT_NOTE','(No edit note)');
	define('ADMINPAGES_CHECK_ALL_TITLE','Check all records');
	define('ADMINPAGES_CHECK_ALL','Check all');
	define('ADMINPAGES_UNCHECK_ALL_TITLE','Uncheck all records');
	define('ADMINPAGES_UNCHECK_ALL','Uncheck all');
	define('ADMINPAGES_FORM_MASSACTION_LEGEND','Mass-action');
	define('ADMINPAGES_FORM_MASSACTION_LABEL','With selected');
	define('ADMINPAGES_FORM_MASSACTION_SELECT_TITLE','Choose action to apply to selected records (DISABLED)');
	define('ADMINPAGES_FORM_MASSACTION_OPT_DELETE','Delete all');
	define('ADMINPAGES_FORM_MASSACTION_OPT_CLONE','Clone all');
	define('ADMINPAGES_FORM_MASSACTION_OPT_RENAME','Rename all');
	define('ADMINPAGES_FORM_MASSACTION_OPT_ACL','Change Access Control List');
	define('ADMINPAGES_FORM_MASSACTION_OPT_REVERT','Revert to previous page version');
	define('ADMINPAGES_FORM_MASSACTION_REVERT_ERROR','Cannot be reverted');
	define('ADMINPAGES_FORM_MASSACTION_SUBMIT','Submit');
	define('ADMINPAGES_ERROR_NO_MATCHES','Sorry, there are no pages matching "%s"');
	define('ADMINPAGES_LABEL_EDIT_NOTE','Please enter a comment, or leave blank for default');
	if (!defined('WHEN_BY_WHO')) define('WHEN_BY_WHO', '%1$s by %2$s');
	if (!defined('ADMINPAGES_CANCEL_LABEL')) define('ADMINPAGES_CANCEL_LABEL', 'Cancel');

	if(isset($_POST['cancel']) && ($_POST['cancel'] == ADMINPAGES_CANCEL_LABEL))
	{
		$this->Redirect($this->Href());
	}

	// -------------------------------------
	// Initialize variables
	
	$r = 1; #initialize row counter
	$r_color = ADMINPAGES_ALTERNATE_ROW_COLOR; #get alternate row color option
	$c_color = ADMINPAGES_STAT_COLUMN_COLOR; #get column color option
	// record dropdown
	$page_limits = unserialize(ADMINPAGES_DEFAULT_RECORDS_RANGE);
	// pager
	$prev = '';		
	$next = '';		
	
	//override defaults with action parameters
	if (is_array($vars))
	{
		foreach ($vars as $param => $value)
		{
			switch ($param)
			{
				case 'colcolor':
					$c_color = (preg_match('/[01]/',$value))? $value : ADMINPAGES_STAT_COLUMN_COLOR;
					break;
				case 'rowcolor':
					$r_color = (preg_match('/[01]/',$value))? $value : ADMINPAGES_ALTERNATE_ROW_COLOR;
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
			$id_params = array();
			$tags = array();
			foreach($_GET as $key=>$val)
			{
				if(FALSE !== strpos($key, "id_"))
				{
					array_push($id_params, $key);
					$id = substr($key, strpos($key,'_')+1);
					$res = $this->LoadPageById($id);
				    array_push($tags, $res['tag']);
				}
			}
			if(count($id_params) > 0)
			{
				?>
				<h3>Revert these pages?</h3><br/>
				<ul>
				<?php
				$errors = 0;
			    foreach($tags as $tag)
				{
					$res = LoadLastTwoPagesByTag($this, $tag);
					if(null===$res)
					{
						++$errors;
						echo "<li><span class='disabled'>".$tag."</span><ul><li><em class='error'>(".ADMINPAGES_FORM_MASSACTION_REVERT_ERROR.")</em></li></ul></li>\n";
						continue;
					}
					$params = "fastdiff=1&amp;a=".$res[0]['id']."&amp;b=".$res[1]['id'];
					echo '<li>'.$this->Link($tag)."\n";
					echo '<ul>'."\n";
					echo '<li>'."\n";
					echo '<a href="'.$this->Href('show', '', 'time='.urlencode($res[0]['time'])).'">['.$res[0]['id'].']</a> '.sprintf(WHEN_BY_WHO, '<a class="datetime" href="'.$this->Href('show','','time='.urlencode($res[0]['time'])).'">'.$res[0]['time'].'</a>', $res[0]['user'])."\n";
					echo ' &#8594; '."\n";
					echo '<a href="'.$this->Href('show', '', 'time='.urlencode($res[1]['time'])).'">['.$res[1]['id'].']</a> '.sprintf(WHEN_BY_WHO, '<a class="datetime" href="'.$this->Href('show','','time='.urlencode($res[1]['time'])).'">'.$res[1]['time'].'</a>', $res[1]['user'])."\n";
					echo ' (<a href="'.$this->Href('diff', $tag, $params).'">diff</a>)'."\n";
					echo '</li>'."\n";
					echo '</ul>'."\n";
					echo '</li>'."\n";
				}
				?></ul><?php
				echo "<br/>\n";
				echo $this->FormOpen() 
				?>
				<table border="0" cellspacing="0" cellpadding="0">
				<?php if($errors < count($tags)) { ?>
					<tr>
						<td>
							<input type="text" name="comment" value="" size="<?php echo ADMINPAGES_MAX_EDIT_NOTE_LENGTH; ?>" /> <?php echo ADMINPAGES_LABEL_EDIT_NOTE; ?> <br/><br/>
						</td>
					</tr>
				<?php } ?>
					<tr>
						<td> 
							<!-- nonsense input so form submission works with rewrite mode -->
							<input type="hidden" value="" name="null"/>
							<?php
							foreach($id_params as $id_param)
							{
								?>
								<input type="hidden" name="<?php echo $id_param ?>" value="1"/>
								<?php
							}
							?>
							<input type="hidden" name="massaction" value="massrevert"/>
							<?php if($errors < count($tags)) { ?>
							<input type="submit" value="Revert Pages"  style="width: 120px"   />
							<?php } ?>
							<input type="submit" value="<?php echo ADMINPAGES_CANCEL_LABEL?>" name="cancel" style="width: 120px" />
						</td>
					</tr>
				</table>
				<?php
				print($this->FormClose());
			}
			else
			{
				$this->Redirect($this->Href());
			}
		}
		else
		{
			$this->Redirect($this->Href());
		}
	}
	else if(isset($_POST['massaction']) && $_POST['massaction'] == 'massrevert')
	{
		$ids = array();
		foreach($_POST as $key=>$val)
		{
			if(FALSE !== strpos($key, "id_"))
			{
				$id = substr($key, strpos($key,'_')+1);
				array_push($ids, $id);
			}
		}
		if(count($ids) > 0)
		{
			$comment = '';
			if(isset($_POST['comment']))
			{
				$comment = $_POST['comment'];
			}
			foreach($ids as $id)
			{
				RevertPageToPreviousById($this, $id, $comment);
			}
		}
		$this->Redirect($this->Href());
	}
	else
	{
		// process URL variables

		// number of records per page
		$l = ADMINPAGES_DEFAULT_RECORDS_LIMIT;
		if (isset($_POST['l']) && (int)$_POST['l'] > 0)
		{
			$l = (int)$_POST['l'];
		}
		elseif (isset($_GET['l']) && (int)$_GET['l'] > 0)
		{
			$l = (int)$_GET['l'];
		}

		// last edit date range
		$start_YY = (isset($_POST['start_YY'])) ? $this->htmlspecialchars_ent($_POST['start_YY']) : ADMINPAGES_DEFAULT_START_YEAR;
		$start_MM = (isset($_POST['start_MM'])) ? $this->htmlspecialchars_ent($_POST['start_MM']) : ADMINPAGES_DEFAULT_START_MONTH;
		$start_DD = (isset($_POST['start_DD'])) ? $this->htmlspecialchars_ent($_POST['start_DD']) : ADMINPAGES_DEFAULT_START_DAY;
		$start_hh = (isset($_POST['start_hh'])) ? $this->htmlspecialchars_ent($_POST['start_hh']) : ADMINPAGES_DEFAULT_START_HOUR;
		$start_mm = (isset($_POST['start_mm'])) ? $this->htmlspecialchars_ent($_POST['start_mm']) : ADMINPAGES_DEFAULT_START_MINUTE;
		$start_ss = (isset($_POST['start_ss'])) ? $this->htmlspecialchars_ent($_POST['start_ss']) : ADMINPAGES_DEFAULT_START_SECOND;
		$end_YY = (isset($_POST['end_YY'])) ? $this->htmlspecialchars_ent($_POST['end_YY']) : ADMINPAGES_DEFAULT_END_YEAR;
		$end_MM = (isset($_POST['end_MM'])) ? $this->htmlspecialchars_ent($_POST['end_MM']) : ADMINPAGES_DEFAULT_END_MONTH;
		$end_DD = (isset($_POST['end_DD'])) ? $this->htmlspecialchars_ent($_POST['end_DD']) : ADMINPAGES_DEFAULT_END_DAY;
		$end_hh = (isset($_POST['end_hh'])) ? $this->htmlspecialchars_ent($_POST['end_hh']) : ADMINPAGES_DEFAULT_END_HOUR;
		$end_mm = (isset($_POST['end_mm'])) ? $this->htmlspecialchars_ent($_POST['end_mm']) : ADMINPAGES_DEFAULT_END_MINUTE;
		$end_ss = (isset($_POST['end_ss'])) ? $this->htmlspecialchars_ent($_POST['end_ss']) : ADMINPAGES_DEFAULT_END_SECOND;

		// sort field
		$sort = ADMINPAGES_DEFAULT_SORT_FIELD;
		$sort_fields = array('owner', 'tag', 'user', 'time');
		if(isset($_GET['sort']) && in_array($_GET['sort'], $sort_fields)) $sort = $_GET['sort'];

		// sort order
		$d = ADMINPAGES_DEFAULT_SORT_ORDER;
		$sort_order = array('asc', 'desc');
		if(isset($_GET['d']) && in_array($_GET['d'], $sort_order)) $d = $_GET['d'];
		// start record
		$s = ADMINPAGES_DEFAULT_START;
		if (isset($_GET['s']) && (int)$_GET['s'] >=0) $s = (int)$_GET['s']; 


		// search string
		$search = ADMINPAGES_DEFAULT_SEARCH;
		$search_disp = ADMINPAGES_DEFAULT_SEARCH;
		if (isset($_POST['search']))
		{
			$search = mysql_real_escape_string($_POST['search']);
			$search_disp = $this->htmlspecialchars_ent($_POST['search']);
		}
		elseif (isset($_GET['search']))
		{
			$search = mysql_real_escape_string($_GET['search']);
			$search_disp = $this->htmlspecialchars_ent($_GET['search']);
		}

		// select all	added JW 2005-07-19
		$checked = '';
		if (isset($_GET['selectall']))
		{
			$checked = (1 == $_GET['selectall']) ? ' checked="checked"' : '';
		}

		// print page header
		echo '<h3>'.ADMINPAGES_PAGE_TITLE.'</h3>'."\n";

		// build pager form	
		$form_filter = $this->FormOpen('','','post','page_admin_panel');
		$form_filter .= '<fieldset><legend>'.ADMINPAGES_FORM_LEGEND.'</legend>'."\n";
		$form_filter .= '<label for="search">'.ADMINPAGES_FORM_SEARCH_STRING_LABEL.'</label> <input type ="text" id="search" name="search" title="'.ADMINPAGES_FORM_SEARCH_STRING_TITLE.'" size="20" maxlength="50" value="'.$search_disp.'"/> <input type="submit" value="'.ADMINPAGES_FORM_SEARCH_SUBMIT.'" /><br />'."\n";
		// build date range fields
		$form_filter .= '<label>'.ADMINPAGES_FORM_DATE_RANGE_STRING_LABEL.'</label>&nbsp;<input class="datetime" type="text" name="start_YY" size="4" maxlength="4" value="'.$start_YY.'"/>-<input class="datetime" type="text" name="start_MM" size="2" maxlength="2" value="'.$start_MM.'"/>-<input class="datetime" type="text" name="start_DD" size="2" maxlength="2" value="'.$start_DD.'"/>&nbsp;<input class="datetime" type="text" name="start_hh" size="2" maxlength="2" value="'.$start_hh.'"/>:<input class="datetime" type="text" name="start_mm" size="2" maxlength="2" value="'.$start_mm.'"/>:<input class="datetime" type="text" name="start_ss" size="2" maxlength="2" value="'.$start_ss.'"/>&nbsp;'.ADMINPAGES_FORM_DATE_RANGE_CONNECTOR_LABEL.'&nbsp;<input class="datetime" type="text" name="end_YY" size="4" maxlength="4" value="'.$end_YY.'"/>-<input class="datetime" type="text" name="end_MM" size="2" maxlength="2" value="'.$end_MM.'"/>-<input class="datetime" type="text" name="end_DD" size="2" maxlength="2" value="'.$end_DD.'"/>&nbsp;<input class="datetime" type="text" name="end_hh" size="2" maxlength="2" value="'.$end_hh.'"/>:<input class="datetime" type="text" name="end_mm" size="2" maxlength="2" value="'.$end_mm.'"/>:<input class="datetime" type="text" name="end_ss" size="2" maxlength="2" value="'.$end_ss.'"/><br />'."\n";

		// check for/validate last date edit range
		$start_ts = '';
		$end_ts = '';
		if (!empty($_GET['start_ts']) && !empty($_GET['end_ts']))
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
			if (is_numeric($start_hh) && $start_hh >= 0 && $start_hh <=24)
			{
				$start_ts .= ' '.mysql_real_escape_string($start_hh).':';
				if (is_numeric($start_mm) && $start_mm >= 0 && $start_mm <= 59)
				{
					$start_ts .= mysql_real_escape_string($start_mm).':';
					if (is_numeric($start_ss) && $start_ss >= 0 && $start_ss <= 59)
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
			if (is_numeric($end_YY) && $end_YY > 1000 && $end_YY < 9999 &&
			   is_numeric($end_MM) && $end_MM > 0 && $end_MM < 13 &&
			   is_numeric($end_DD) && $end_DD > 0 && $end_DD < 32)
			{
				$end_ts = mysql_real_escape_string($end_YY);
				$end_ts .= '-';
				$end_ts .= mysql_real_escape_string($end_MM);
				$end_ts .= '-';
				$end_ts .= mysql_real_escape_string($end_DD);
				if (is_numeric($end_hh) && $end_hh >= 0 && $end_hh <=24)
				{
					$end_ts .= ' '.mysql_real_escape_string($end_hh).':';
					if (is_numeric($end_mm) && $end_mm >= 0 && $end_mm <= 59)
					{
						$end_ts .= mysql_real_escape_string($end_mm).':';
						if (is_numeric($end_ss) && $end_ss >= 0 && $end_ss <= 59)
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
		$where = ('' == $search) ? "`latest` = 'Y'" : "`tag` LIKE '%".$search."%' AND `latest` = 'Y'";
		if (!empty($start_ts) && !empty($end_ts))
		{
			$where .= " AND time > '".$start_ts."' AND time < '".$end_ts."'"; 
		}
		// get total number of pages
		$numpages = $this->getCount('pages', $where);

		// ranged drop-down
		$pages_opts = optionRanges($page_limits,$numpages, ADMINPAGES_DEFAULT_MIN_RECORDS_DISPLAY);
		$form_filter .= '<label for="l">'.ADMINPAGES_FORM_PAGER_LABEL_BEFORE.'</label> '."\n";
		$form_filter .= '<select name="l" id="l" title="'.ADMINPAGES_FORM_PAGER_TITLE.'">'."\n";
		// build drop-down
		foreach ($pages_opts as $opt)
		{
			$selected = ($opt == $l) ? ' selected="selected"' : '';
			$form_filter .= '<option value="'.$opt.'"'.$selected.'>'.$opt.'</option>'."\n";
		}
		$form_filter .=  '</select> <label for="l">'.ADMINPAGES_FORM_PAGER_LABEL_AFTER.'</label> <input type="submit" value="'.ADMINPAGES_FORM_PAGER_SUBMIT.'" /><br />'."\n";

		// build pager links
		$ll = $s+$l+1;
		$ul = ($s+2*$l) > $numpages ? $numpages : ($s+2*$l);
		if ($s > 0)
		{
			$prev = '<a href="'.$this->Href('','','l='.$l.'&amp;sort='.$sort.'&amp;d='.$d.'&amp;s='.($s-$l)).  '&amp;search='.urlencode($search).  '&amp;start_ts='.urlencode($start_ts).  '&amp;end_ts='.urlencode($end_ts).  '" title="'.sprintf(ADMINPAGES_FORM_PAGER_LINK, ($s-$l+1), $s).'">'.($s-$l+1).'-'.$s.'</a> |  '."\n";
		}
		if ($numpages > ($s + $l))
		{
			$next = ' | <a href="'.$this->Href('','','l='.$l.'&amp;sort='.$sort.'&amp;d='.$d.'&amp;s='.($s+$l)).'&amp;search='.urlencode($search).'&amp;start_ts='.urlencode($start_ts).'&amp;end_ts='.urlencode($end_ts).'" title="'.sprintf(ADMINPAGES_FORM_PAGER_LINK, $ll, $ul).'">'.$ll.(($ll==$ul)?'':('-'.$ul)).'</a>'."\n";
		}
		$form_filter .= ADMINPAGES_FORM_RESULT_INFO.' ('.$numpages.'): '.$prev.(($s+$l)>$numpages?($s+1).'-'.$numpages:($s+1).'-'.($s+$l)).$next.'<br />'."\n";
		$form_filter .= '<span class="sortorder">'.ADMINPAGES_FORM_RESULT_SORTED_BY.' <tt>'.$sort.', '.$d.'</tt></span>'."\n";
		$form_filter .= '</fieldset>'.$this->FormClose()."\n";

		// sort by counted values
		$count = '';
		$group = '';
		switch($sort) 
		{
			case 'edits': #alpha --- 'latest' needs to be disabled
				//sample query:
				//SELECT *, COUNT(*) as edits FROM `wikka_pages` GROUP BY tag ORDER BY edits DESC
				$count = ', COUNT(*) as edits';
				$group = 'GROUP BY tag';	
				$where .= 'AND 1';
				//$where = ('' == $search) ? "1" : "`tag` LIKE '%".$search."%'";
				$table = 'pages';	
				break;
			case 'comments': #to implement
			/*
				// SELECT wikka1160_pages.tag, COUNT(  *  )  AS comments FROM wikka_pages, wikka_comments WHERE wikka1160_pages.tag = wikka1160_comments.page_tag GROUP  BY wikka1160_pages.tag ORDER  BY comments DESC 
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
			$tagheader = '<a href="'.$this->Href('','', (($sort == 'tag' && $d == 'asc')? 'l='.$l.'&amp;sort=tag&amp;d=desc&amp;search='.urlencode($search).'&amp;start_ts='.urlencode($start_ts).'&amp;end_ts='.urlencode($end_ts) : 'l='.$l.'&amp;sort=tag&amp;d=asc&amp;search='.urlencode($search).'&amp;start_ts='.urlencode($start_ts).'&amp;end_ts='.urlencode($end_ts))).'" title="'.ADMINPAGES_TABLE_HEADING_PAGENAME_TITLE.'">'.ADMINPAGES_TABLE_HEADING_PAGENAME.'</a>';
			$ownerheader = '<a href="'.$this->Href('','', (($sort == 'owner' && $d == 'asc')? 'l='.$l.'&amp;sort=owner&amp;d=desc&amp;search='.urlencode($search).'&amp;start_ts='.urlencode($start_ts).'&amp;end_ts='.urlencode($end_ts) : 'l='.$l.'&amp;sort=owner&amp;d=asc&amp;search='.urlencode($search).'&amp;start_ts='.urlencode($start_ts).'&amp;end_ts='.urlencode($end_ts))).'" title="'.ADMINPAGES_TABLE_HEADING_OWNER_TITLE.'">'.ADMINPAGES_TABLE_HEADING_OWNER.'</a>';
			$userheader = '<a href="'.$this->Href('','', (($sort == 'user' && $d == 'asc')? 'l='.$l.'&amp;sort=user&amp;d=desc&amp;search='.urlencode($search).'&amp;start_ts='.urlencode($start_ts).'&amp;end_ts='.urlencode($end_ts) : 'l='.$l.'&amp;sort=user&amp;d=asc&amp;search='.urlencode($search).'&amp;start_ts='.urlencode($start_ts).'&amp;end_ts='.urlencode($end_ts))).'" title="'.ADMINPAGES_TABLE_HEADING_LASTAUTHOR_TITLE.'">'.ADMINPAGES_TABLE_HEADING_LASTAUTHOR.'</a>';
			$lasteditheader = '<a href="'.$this->Href('','', (($sort == 'time' && $d == 'desc')? 'l='.$l.'&amp;sort=time&amp;d=asc&amp;search='.urlencode($search).'&amp;start_ts='.urlencode($start_ts).'&amp;end_ts='.urlencode($end_ts) : 'l='.$l.'&amp;sort=time&amp;d=desc&amp;search='.urlencode($search).'&amp;start_ts='.urlencode($start_ts).'&amp;end_ts='.urlencode($end_ts))).'" title="'.ADMINPAGES_TABLE_HEADING_LASTEDIT_TITLE.'">'.ADMINPAGES_TABLE_HEADING_LASTEDIT.'</a>';
			/* $revisionsheader = '<a href="'.$this->Href('','', (($sort == 'edits' && $d == 'desc')? 'l='.$l.'&amp;sort=edits&amp;d=asc&amp;search='.urlencode($search).'&amp;start_ts='.urlencode($start_ts).'&amp;end_ts='.urlencode($end_ts) : 'l='.$l.'&amp;sort=edits&amp;d=desc&amp;search='.urlencode($search).'&amp;start_ts='.urlencode($start_ts).'&amp;end_ts='.urlencode($end_ts))).'" title="'.ADMINPAGES_TABLE_HEADING_REVISIONS_TITLE.'"><img src="'.ADMINPAGES_REVISIONS_ICON.'" alt="'.ADMINPAGES_TABLE_HEADING_REVISIONS_ALT.'"/></a>'; */ #not implemented

			$data_table = '<table id="adminpages" summary="'.ADMINPAGES_TABLE_SUMMARY.'" border="1px" class="data">'."\n".
			'<thead>'."\n".
			'	<tr>'."\n".
			'		<th> </th>'."\n".
			'		<th>'.$tagheader.'</th>'."\n".
			'		<th>'.$ownerheader.'</th>'."\n".
			'		<th>'.$userheader.'</th>'."\n".
			'		<th>'.$lasteditheader.'</th>'."\n".
			'		<th'.(($c_color == 1)? ' class="c1"' : '').' title="'.ADMINPAGES_TABLE_HEADING_HITS_TITLE.'"><img src="'.ADMINPAGES_HITS_ICON.'" alt="'.ADMINPAGES_TABLE_HEADING_HITS_ALT.'" /></th>'."\n".
			'		<th'.(($c_color == 1)? ' class="c2"' : '').' title="'.ADMINPAGES_TABLE_HEADING_REVISIONS_TITLE.'"><img src="'.ADMINPAGES_REVISIONS_ICON.'" alt="'.ADMINPAGES_TABLE_HEADING_REVISIONS_ALT.'" /></th>'."\n".
			'		<th'.(($c_color == 1)? ' class="c3"' : '').' title="'.ADMINPAGES_TABLE_HEADING_COMMENTS_TITLE.'"><img src="'.ADMINPAGES_COMMENTS_ICON.'" alt="'.ADMINPAGES_TABLE_HEADING_COMMENTS_ALT.'" /></th>'."\n".
			'		<th'.(($c_color == 1)? ' class="c4"' : '').' title="'.ADMINPAGES_TABLE_HEADING_BACKLINKS_TITLE.'"><img src="'.ADMINPAGES_BACKLINKS_ICON.'" alt="'.ADMINPAGES_TABLE_HEADING_BACKLINKS_ALT.'" /></th>'."\n".
			'		<th'.(($c_color == 1)? ' class="c5"' : '').' title="'.ADMINPAGES_TABLE_HEADING_REFERRERS_TITLE.'"><img src="'.ADMINPAGES_REFERRERS_ICON.'" alt="'.ADMINPAGES_TABLE_HEADING_REFERRERS_ALT.'" /></th>'."\n".
			'		<th>'.ADMINPAGES_TABLE_HEADING_ACTIONS.'</th>'."\n".
			'	</tr>'."\n".
			'</thead>'."\n";

			// feed table with data
			foreach($pagedata as $page)
			{
				// truncate long page names
				$pagename = (strlen($page['tag']) > ADMINPAGES_DEFAULT_TAG_LENGTH) ? substr($page['tag'], 0, ADMINPAGES_DEFAULT_TAG_LENGTH).ADMINPAGES_DEFAULT_TERMINATOR : $page['tag'];

				// build handler links
				$lastedit = '<a class="datetime" href="'.$this->Href('revisions', $page['tag'], '').'">'.$page['time'].'</a>';
				
				if ($pagename != $page['tag'])
				{
					$showpage = '<a href="'.$this->Href('',$page['tag'], '').'" title="'.$page['tag'].'">'.$pagename.'</a>';
				}
				else
				{
					$showpage = '<a href="'.$this->Href('',$page['tag'], '').'">'.$pagename.'</a>';
				}
				// Disable revert link if only one page revision exists
				$revertpage = '';
				$res = LoadLastTwoPagesByTag($this, $page['tag']);
				if(null===$res)
				{
					$revertpage = "<span class='disabled'>".ADMINPAGES_ACTION_REVERT_LINK."</span>";
				}
				else
				{
					$revertpage = '<a href="'.$this->Href('revert',$page['tag'], '').'" title="'.sprintf(ADMINPAGES_ACTION_REVERT_LINK_TITLE, $page['tag']).'">'.ADMINPAGES_ACTION_REVERT_LINK.'</a>';
				}
				$editpage = '<a href="'.$this->Href('edit',$page['tag'], '').'" title="'.sprintf(ADMINPAGES_ACTION_EDIT_LINK_TITLE, $page['tag']).'">'.ADMINPAGES_ACTION_EDIT_LINK.'</a>';
				$deletepage = '<a href="'.$this->Href('delete',$page['tag'], '').'" title="'.sprintf(ADMINPAGES_ACTION_DELETE_LINK_TITLE, $page['tag']).'">'.ADMINPAGES_ACTION_DELETE_LINK.'</a>';
				$clonepage = '<a href="'.$this->Href('clone',$page['tag'], '').'" title="'.sprintf(ADMINPAGES_ACTION_CLONE_LINK_TITLE, $page['tag']).'">'.ADMINPAGES_ACTION_CLONE_LINK.'</a>';
				/* $renamepage = '<a href="'.$this->Href('rename',$page['tag'], '').'" title="'.sprintf(ADMINPAGES_ACTION_RENAME_LINK_TITLE, $page['tag']).'">'.ADMINPAGES_ACTION_RENAME_LINK.'</a>'; */ #to be implemented
				$aclpage = '<a href="'.$this->Href('acls',$page['tag'], '').'" title="'.sprintf(ADMINPAGES_ACTION_ACL_LINK_TITLE, $page['tag']).'">'.ADMINPAGES_ACTION_ACL_LINK.'</a>';
				/* $infopage = '<a href="'.$this->Href('info',$page['tag'], '').'" title="'.sprintf(ADMINPAGES_ACTION_INFO_LINK_TITLE, $page['tag']).'">'.ADMINPAGES_ACTION_INFO_LINK.'</a>'; */ #to be implemented

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
					$owner = $this->Link($page['tag'], 'claim','(Nobody)','','',ADMINPAGES_TAKE_OWNERSHIP_LINK.' '.$page['tag']);
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
						$user = (strlen($page['user']) > ADMINPAGES_DEFAULT_URL_LENGTH) ? substr($page['user'], 0, ADMINPAGES_DEFAULT_URL_LENGTH).ADMINPAGES_DEFAULT_TERMINATOR : $page['user'];
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
					$user = ADMINPAGES_NO_OWNER;
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
				$hitspage = ($hn > 0) ? '<a href="'.$this->Href('hits',$page['tag'], '').'" title="'.sprintf(ADMINPAGES_TABLE_CELL_HITS_TITLE, $page['tag'], $hn).'">'.$hn.'</a>' : '0';

				// get page revisions and create revision link if needed
				$revpage = ($rv > 0) ? '<a href="'.$this->Href('revisions',$page['tag'], '').'" title="'.sprintf(ADMINPAGES_TABLE_CELL_REVISIONS_TITLE, $page['tag'], $rv).'">'.$rv.'</a>' : '0';

				// get page comments and create comments link if needed
				$commentspage = ($cn > 0) ? '<a href="'.$this->Href('',$page['tag'], 'show_comments=1#comments').'" title="'.sprintf(ADMINPAGES_TABLE_CELL_COMMENTS_TITLE, $page['tag'], $cn).'">'.$cn.'</a>' : '0';

				// get page backlinks and create backlinks link
				$backlinkpage = ($bn > 0) ? '<a href="'.$this->Href('backlinks',$page['tag'], '').'" title="'.sprintf(ADMINPAGES_TABLE_CELL_BACKLINKS_TITLE, $page['tag'], $bn).'">'.$bn.'</a>' : '0';

				// get page referrers and create referrer link
				$refpage = ($rn > 0) ? '<a href="'.$this->Href('referrers',$page['tag'], '').'" title="'.sprintf(ADMINPAGES_TABLE_CELL_REFERRERS_TITLE, $page['tag'], $rn).'">'.$rn.'</a>' : '0';

				// build table body
				$data_table .= '<tbody>'."\n";
				if ($r_color == 1)
				{
					$data_table .= '<tr '.(($r%2)? '' : 'class="alt"').'>'."\n"; #enable alternate row color
				}
				else
				{
					$data_table .= '<tr>'."\n"; #disable alternate row color
				}
				$data_table .= '		<td><input type="checkbox" name="id_'.$page['id'].'"'.$checked.' title="'.sprintf(ADMINPAGES_SELECT_RECORD_TITLE, $page['tag']).'" /></td>'."\n".	# modified JW 2005-07-19
				'		<td>'.$showpage.'</td>'."\n".
				'		<td>'.$owner.'</td>'."\n".
				'		<td>'.$user.'</td>'."\n".
				'		<td '.((strlen($page['note'])>0)? 'class="help" title="('.$page['note'].')"' : 'title="'.ADMINPAGES_NO_EDIT_NOTE.'"').'>'.$lastedit.'</td>'."\n".
				'		<td class="number'.(($c_color == 1)? ' c1' : '').'">'.$hitspage.'</td>'."\n".
				'		<td class="number'.(($c_color == 1)? ' c2' : '').'">'.$revpage.'</td>'."\n".
				'		<td class="number'.(($c_color == 1)? ' c3' : '').'">'.$commentspage.'</td>'."\n".
				'		<td class="number'.(($c_color == 1)? ' c4' : '').'">'.$backlinkpage.'</td>'."\n".
				'		<td class="number'.(($c_color == 1)? ' c5' : '').'">'.$refpage.'</td>'."\n".
				'		<td class="actions">'.$revertpage.' :: '.$editpage.' :: '.$deletepage.' :: '.$clonepage.' :: '.$aclpage.'</td>'."\n".
				'	</tr>'."\n".
				'</tbody>'."\n";

				//increase row counter    ----- alternate row colors
				if ($r_color == 1)
				{
					$r++;
				}
			}
			$data_table .= '</table>'."\n";

			$form_mass = $this->FormOpen('','','get');
			$form_mass .= $data_table;

			// multiple-page operations (forthcoming)		JW 2005-07-19 accesskey removed (causes more problems than it solves)
			$form_mass .= '<fieldset><legend>'.ADMINPAGES_FORM_MASSACTION_LEGEND.'</legend>';
			$form_mass .= '[<a href="'.$this->Href('','','l='.$l.'&amp;sort='.$sort.'&amp;d='.$d.'&amp;s='.$s.'&amp;search='.urlencode($search).'&amp;start_ts='.urlencode($start_ts).'&amp;end_ts='.urlencode($end_ts).'&amp;selectall=1').'" title="'.ADMINPAGES_CHECK_ALL_TITLE.'">'.ADMINPAGES_CHECK_ALL.'</a> | <a href="'.$this->Href('','','l='.$l.'&amp;sort='.$sort.'&amp;d='.$d.'&amp;s='.$s.'&amp;search='.urlencode($search).'&amp;start_ts='.urlencode($start_ts).'&amp;end_ts='.urlencode($end_ts).'&amp;selectall=0').'" title="'.ADMINPAGES_UNCHECK_ALL_TITLE.'">'.ADMINPAGES_UNCHECK_ALL.'</a>]<br />';
			$form_mass .= '<label for="action" >'.ADMINPAGES_FORM_MASSACTION_LABEL.'</label> <select title="'.ADMINPAGES_FORM_MASSACTION_SELECT_TITLE.'" id="action" name="action">';
			$form_mass .= '<option value="" selected="selected">---</option>';
			// Temporarily disabling all but massrevert
			/*
			echo '<option value="massdelete">'.FORM_MASSACTION_OPT_DELETE.'</option>';
			echo '<option value="massclone">'.FORM_MASSACTION_OPT_CLONE.'</option>';
			echo '<option value="massrename">'.FORM_MASSACTION_OPT_RENAME.'</option>';
			echo '<option value="massacls">'.FORM_MASSACTION_OPT_ACL.'</option>';
			*/
			$form_mass .= '<option value="massrevert">'.ADMINPAGES_FORM_MASSACTION_OPT_REVERT.'</option>';
			$form_mass .= '</select> <input type="submit" value="'.ADMINPAGES_FORM_MASSACTION_SUBMIT.'" />';
			$form_mass .= '</fieldset>';
			$form_mass .= $this->FormClose();
			
			// output
			echo $form_filter;
			echo $form_mass;
			
		}
		else
		{
			// no records matching the search string: print error message
			echo '<p><em class="error">'.sprintf(ADMINPAGES_ERROR_NO_MATCHES, $search_disp).'</em></p>';
		}
	}
}
else
{
	// current user is not admin: show plain page index
	echo $this->Action('pageindex');
}
?>