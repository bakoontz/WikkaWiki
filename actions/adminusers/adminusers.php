<?php
/**
 * Display a module for user management.
 *
 * This action allows admins to display information on registered users.
 * Users can be searched, paged, filtered. User-related statistics are given,
 * showing the number of pages commented, created and modified pages by each user.
 * A feedback handler allows admins to send an email to single users. If the current
 * user is not an administrator, then the lastuser action is displayed instead.
 *
 * @package    	Actions
 * @version		$Id$
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 * @since		Wikka 1.1.6.4
 *
 * @uses Wakka::Action()
 * @uses Wakka::ExistsPage()
 * @uses Wakka::FormClose()
 * @uses Wakka::FormOpen()
 * @uses Wakka::getCount()
 * @uses Wakka::GetUser()
 * @uses Wakka::Href()
 * @uses Wakka::htmlspecialchars_ent()
 * @uses Wakka::IsAdmin()
 * @uses Wakka::Link()
 * @uses Wakka::LoadAll()
 * @uses Wakka::Redirect()
 *
 * @uses Config::$action_path
 * 
 * @input		integer $colcolor  optional: enables color for statistics columns
 *				1: enable colored columns;
 *				0: disable colored columns;
 *				default: 1;
 * @input		integer $rowcolor  optional: enables alternate row colors
 *				1: enable colored rows;
 *				0: disable colored rows;
 *				default: 1;
 *
 * @output		A module to manage registered users.
 *
 * @todo
 *			- sanitize URL parameters;
 *			- apply FormatUser();
 * 			- port all the dependencies (CSS, icons, handlers);
 * 			- mass-operations;
 *			- deleting/banning users;
 * 			- single/mass user feedback via new feedback action; #608
 *			- integrate with other admin modules;
 * 			- tie to a new default page (AdminUsers);
 * 			- move i18n strings to lang in 1.1.7;
 * 			- move icons to buddy file or action folder in 1.1.7;
 */

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
		if($firstinc > $max) $firstinc = $max;
		$opts = array();
		$inc = $firstinc;

		// first element is the first increment
		$opts[] = $inc;
		if($inc >= $max) return $opts;
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
	if($this->GetSafeVar('cancel', 'post') == ADMINUSERS_CANCEL_BUTTON)
	{
		$this->Redirect($this->Href());
	}

	//initialize row & column colors variables
	$r = 1; #initialize row counter
	$r_color = ADMINUSERS_ALTERNATE_ROW_COLOR; #get alternate row color option
	$c_color = ADMINUSERS_STAT_COLUMN_COLOR; #get column color option
	// record dropdown
	$user_limits = unserialize(ADMINUSERS_DEFAULT_RECORDS_RANGE);
	// pager
	$prev = '';		
	$next = '';		
	
	//override defaults with action parameters
	if (is_array($vars))
	{
		foreach ($vars as $param => $value)
		{
			$value = $this->htmlspecialchars_ent($value);
			switch ($param)
			{
				case 'colcolor':
					$c_color = (preg_match('/[01]/',$value))? $value : ADMINUSERS_STAT_COLUMN_COLOR;
					break;
				case 'rowcolor':
					$r_color = (preg_match('/[01]/',$value))? $value : ADMINUSERS_ALTERNATE_ROW_COLOR;
					break;
			}
		}
	}
	
	//perform actions if required
	$g_action = '';
	if(isset($_GET['action']))
	{
		$g_action = $this->GetSafeVar('action', 'get');
	}
	if($g_action == 'owned') 
	{
		echo $this->Action('mypages');
	} 
	elseif($g_action == 'changes') 
	{
		echo $this->Action('mychanges');
	} 
	elseif($g_action == 'comments') 
	{
		echo $this->Action('recentcomments');
	} 
	/*
	elseif($g_action == 'feedback' || $_POST['mail']) 
	{
		echo $this->Action('userfeedback'); #to be added in 1.1.7, see #608
	}
	*/
	elseif($g_action == 'delete')
	{
		if(isset($_GET['user']))
		{
			include_once($this->BuildFullpathFromMultipath('..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'admin.lib.php', $this->config['action_path']));
			$status = DeleteUser($this, $this->GetSafeVar('user', 'get'));
			if(false===$status)
			{
				$this->Redirect($this->Href(), ADMINUSERS_USERDELETE_FAILURE);
			}
			else
			{
				$this->Redirect($this->Href(), ADMINUSERS_USERDELETE_SUCCESS);
			}
		}
	}
	elseif($g_action == 'massdelete')
	{
		$usernames = array();
		foreach($_GET as $key => $val)
		{
			$val = $this->GetSafeVar($key, 'get');
			if($val == "on")
			{
				array_push($usernames, $this->htmlspecialchars_ent($key));
			}
		}
		if(count($usernames) > 0)
		{
			echo '<h3>'.ADMINUSERS_DELETE_USERS_HEADING.'</h3><br />'."\n".'<ul>';
			$errors = 0;
			foreach($usernames as $username)
			{
				if($this->IsAdmin($username))
				{
					++$errors;
					echo '<li><span class="disabled">'.$username.'&nbsp;</span><em class="error">('.ADMINUSERS_FORM_MASSACTION_DELETE_ERROR.")</em></li>\n";
					continue;
				}
				echo "<li>".$username."</li>\n";
			}
			echo "</ul><br/>\n";
			echo $this->FormOpen() 
			?>
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td> 
						<!-- nonsense input so form submission works with rewrite mode -->
						<input type="hidden" value="" name="null"/>
						<?php
						foreach($usernames as $username)
						{
							if(true!==$this->IsAdmin($username))
							{
							?>
							<input type="hidden" name="<?php echo $username ?>" value="username"/>
							<?php
							}
						}
						?>
						<input type="hidden" name="massaction" value="massdelete"/>
						<?php if($errors < count($usernames)) { ?>
						<input type="submit" value="<?php echo ADMINUSERS_DELETE_USERS_BUTTON;?>"  style="width: 120px"   />
						<?php } ?>
						<input type="submit" value="<?php echo ADMINUSERS_CANCEL_BUTTON;?>" name="cancel" style="width: 120px" />
					</td>
				</tr>
			</table>
			<?php
			print($this->FormClose());
		}
	}	
	else if($this->GetSafeVar('massaction', 'post') == 'massdelete')
	{
		$usernames = array();
		foreach($_POST as $key => $val)
		{
			$val = $this->GetSafeVar($key, 'post');
			if($val == "username")
			{
				array_push($usernames, $this->htmlspecialchars_ent($key));
			}
		}
		if(count($usernames) > 0)
		{
			include_once($this->BuildFullpathFromMultipath('..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'admin.lib.php', $this->config['action_path']));
			$status = true;
			foreach($usernames as $username)
			{
				$status = $status && DeleteUser($this, $username);
			}
		}
		if(false === $status)
		{
			$this->Redirect($this->Href(), ADMINUSERS_USERDELETE_FAILURE);
		}
		else
		{
			$this->Redirect($this->Href(), ADMINUSERS_USERDELETE_SUCCESS);
		}
	}
	else 
	{
		// process URL variables
	
		// number of records per page
		$l = $this->GetSafeVar('l', 'post');
		if (!$l)
		{
			$l = $this->GetSafeVar('l', 'get');
		}
		if (!$l)
		{
			$l = ADMINUSERS_DEFAULT_RECORDS_LIMIT;
		}

		// sort field
		$sort_fields = array('name', 'email', 'signuptime');
		$sort = (isset($_GET['sort'])) ? $this->GetSafeVar('sort', 'get') : ADMINUSERS_DEFAULT_SORT_FIELD;
		if(!in_array($sort, $sort_fields)) $sort = ADMINUSERS_DEFAULT_SORT_FIELD;
		// sort order
		$sort_order = array('asc', 'desc');
		$d = (isset($_GET['d'])) ? $this->GetSafeVar('d', 'get') : ADMINUSERS_DEFAULT_SORT_ORDER;
		if(!in_array($d, $sort_order)) $d = ADMINUSERS_DEFAULT_SORT_ORDER;
		// start record
		$s = (isset($_GET['s'])) ? $this->GetSafeVar('s', 'get') : ADMINUSERS_DEFAULT_START;
		if (!(int)$s >=0) $s = ADMINUSERS_DEFAULT_START;
	
		// search string
		$search = ADMINUSERS_DEFAULT_SEARCH;
		$search_disp = ADMINUSERS_DEFAULT_SEARCH;
		if (isset($_POST['search']))
		{
			$search = mysql_real_escape_string($this->GetSafeVar('search', 'post'));
			$search_disp = $this->GetSafeVar('search', 'post');
		}
		elseif (isset($_GET['search']))
		{
			$search = mysql_real_escape_string($this->GetSafeVar('search', 'get'));
			$search_disp = $this->GetSafeVar('search', 'get');
		}
		elseif($this->GetSafeVar('submit', 'post') == ADMINUSERS_FORM_SEARCH_SUBMIT)
		{
			// Reset num recs per page for empty (reset) search
			$l = ADMINUSERS_DEFAULT_RECORDS_LIMIT;
		}
	
		// select all
		$checked = '';
		if (isset($_GET['selectall']))
		{
			$checked = (1 == $_GET['selectall']) ? ' checked="checked"' : '';
		}
	
		// restrict MySQL query by search string
		$where = "(status IS NULL OR status != 'deleted') AND ";
		$where .= ('' == $search) ? '1' : "name LIKE '%".$search."%'";
		// get total number of users
		$numusers = $this->getCount('users', $where);
		// If the user doesn't specifically want to change the records
		// per page, then use the default.  The problem here is that one
		// form is being used to process two post requests, so things
		// (search string, num recs per page) get out of sync, requiring
		// multiple submissions.
		if(!isset($_GET['l']) && $this->GetSafeVar('submit', 'post') != ADMINUSERS_FORM_PAGER_SUBMIT)
		{
			$l = ADMINUSERS_DEFAULT_RECORDS_LIMIT;
		}
	
		// print page header
		echo '<h3>'.ADMINUSERS_PAGE_TITLE.'</h3>'."\n";

		//non-working message retrieval removed, see #753
	
		// build pager form	
		$form_filter = $this->FormOpen('','','post','user_admin_panel');
		$form_filter .= '<fieldset><legend>'.ADMINUSERS_FORM_LEGEND.'</legend>'."\n";
		$form_filter .= '<label for="search">'.ADMINUSERS_FORM_SEARCH_STRING_LABEL.'</label> <input type ="text" id="search" name="search" title="'.ADMINUSERS_FORM_SEARCH_STRING_TITLE.'" size="20" maxlength="50" value="'.$search_disp.'"/> <input name="submit" type="submit" value="'.ADMINUSERS_FORM_SEARCH_SUBMIT.'" /><br />'."\n";
		// get values range for drop-down
		$users_opts = optionRanges($user_limits,$numusers, ADMINUSERS_DEFAULT_MIN_RECORDS_DISPLAY);
		$form_filter .= '<label for="l">'.ADMINUSERS_FORM_PAGER_LABEL_BEFORE.'</label> '."\n";
		$form_filter .= '<select name="l" id="l" title="'.ADMINUSERS_FORM_PAGER_TITLE.'">'."\n";
		// build drop-down
		foreach ($users_opts as $opt)
		{
			$selected = ($opt == $l) ? ' selected="selected"' : '';
			$form_filter .= '<option value="'.$opt.'"'.$selected.'>'.$opt.'</option>'."\n";
		}
		$form_filter .=  '</select> <label for="l">'.ADMINUSERS_FORM_PAGER_LABEL_AFTER.'</label> <input name="submit" type="submit" value="'.ADMINUSERS_FORM_PAGER_SUBMIT.'" /><br />'."\n";
	
		// build pager links
		$ll = $s+$l+1;
		$ul = ($s+2*$l) > $numusers ? $numusers : ($s+2*$l);
		if ($s > 0)
		{
			$prev = '<a href="'.$this->Href('','','l='.$l.'&amp;sort='.$sort.'&amp;d='.$d.'&amp;s='.($s-$l)).  '&amp;search='.urlencode($search).  '&amp;start_ts='.urlencode($start_ts).  '&amp;end_ts='.urlencode($end_ts).  '" title="'.sprintf(ADMINUSERS_FORM_PAGER_LINK, ($s-$l+1), $s).'">'.($s-$l+1).'-'.$s.'</a> |  '."\n";
		}
		if ($numusers > ($s + $l))
		{
			$next = ' | <a href="'.$this->Href('','','l='.$l.'&amp;sort='.$sort.'&amp;d='.$d.'&amp;s='.($s+$l)).'&amp;search='.urlencode($search).'&amp;start_ts='.urlencode($start_ts).'&amp;end_ts='.urlencode($end_ts).'" title="'.sprintf(ADMINUSERS_FORM_PAGER_LINK, $ll, $ul).'">'.$ll.(($ll==$ul)?'':('-'.$ul)).'</a>'."\n";
		}
		$form_filter .= ADMINUSERS_FORM_RESULT_INFO.' ('.$numusers.'): '.$prev.(($s+$l)>$numusers?($s+1).'-'.$numusers:($s+1).'-'.($s+$l)).$next.'<br />'."\n";
		$form_filter .= '<span class="sortorder">'.ADMINUSERS_FORM_RESULT_SORTED_BY.' <tt>'.$sort.', '.$d.'</tt></span>'."\n";
		$form_filter .= '</fieldset>'.$this->FormClose()."\n";

		// get user list
		$userdata = $this->LoadAll("SELECT * FROM ".$this->config["table_prefix"]."users WHERE ".$where." ORDER BY ".$sort." ".$d." limit ".$s.", ".$l);
	
		if ($userdata)
		{
			// build header links
			$nameheader = '<a href="'.$this->Href('','', (($sort == 'name' && $d == 'asc')? 'l='.$l.'&amp;sort=name&amp;d=desc' : 'l='.$l.'&amp;sort=name&amp;d=asc')).'" title="'.ADMINUSERS_TABLE_HEADING_USERNAME_TITLE.'">'.ADMINUSERS_TABLE_HEADING_USERNAME.'</a>';
			$emailheader = '<a href="'.$this->Href('','', (($sort == 'email' && $d == 'asc')? 'l='.$l.'&amp;sort=email&amp;d=desc' : 'l='.$l.'&amp;sort=email&amp;d=asc')).'" title="'.ADMINUSERS_TABLE_HEADING_EMAIL_TITLE.'">'.ADMINUSERS_TABLE_HEADING_EMAIL.'</a>';
			$timeheader = '<a href="'.$this->Href('','', (($sort == 'signuptime' && $d == 'desc')? 'l='.$l.'&amp;sort=signuptime&amp;d=asc' : 'l='.$l.'')).'" title="'.ADMINUSERS_TABLE_HEADING_SIGNUPTIME_TITLE.'">'.ADMINUSERS_TABLE_HEADING_SIGNUPTIME.'</a>';

			/*$ipheader = '<a href="'.$this->Href('','', (($sort == 'ipaddress' && $d == 'desc')? 'l='.$l.'&amp;sort=ipaddress&amp;d=asc' : 'l='.$l.'&amp;sort=ipaddress&amp;d=desc')).'" title="'.TABLE_HEADING_SIGNUPIP_TITLE.'">'.TABLE_HEADING_SIGNUPIP.'</a>'; # installed as beta feature at wikka.jsnx.com  */
	
			// build table headers
			$data_table = '<table id="adminusers" summary="'.ADMINUSERS_TABLE_SUMMARY.'" border="1px" class="data">'."\n".
				'<thead>'."\n".
	  			'	<tr>'."\n".
				'		<th></th>'."\n".
				'		<th>'.$nameheader.'</th>'."\n".
				'		<th>'.$emailheader.'</th>'."\n".
				'		<th>'.$timeheader.'</th>'."\n".
				/* '		<th>'.$ipheader.'</th>'."\n". # installed as beta feature at wikkawiki.org */
				'		<th'.(($c_color == 1)? ' class="c1"' : '').' title="'.ADMINUSERS_TABLE_HEADING_OWNED_TITLE.'"><img src="'.ADMINUSERS_OWNED_ICON.'" class="icon" alt="O"/></th>'."\n".
				'		<th'.(($c_color == 1)? ' class="c2"' : '').' title="'.ADMINUSERS_TABLE_HEADING_EDITS_TITLE.'"><img src="'.ADMINUSERS_EDITS_ICON.'" class="icon" alt="E"/></th>'."\n".
				'		<th'.(($c_color == 1)? ' class="c3"' : '').' title="'.ADMINUSERS_TABLE_HEADING_COMMENTS_TITLE.'"><img src="'.ADMINUSERS_COMMENTS_ICON.'" class="icon" alt="C"/></th>'."\n".
				'		<th>'.ADMINUSERS_TABLE_HEADING_ACTIONS.'</th>'."\n".
	 		 	'	</tr>'."\n".
	 		 	'</thead>'."\n";
	
			// print user table
			foreach($userdata as $user)
			{
				// get counts	
				$where_owned	= "`owner` = '".$user['name']."' AND latest = 'Y'";
				$where_changes	= "`user` = '".$user['name']."'";
				$where_comments	= "`user` = '".$user['name']."'";
				$numowned = $this->getCount('pages', $where_owned);
				$numchanges = $this->getCount('pages', $where_changes);
				$numcomments = $this->getCount('comments', $where_comments);
		
				// build statistics links if needed
				$ownedlink = ($numowned > 0)? '<a title="'.sprintf(ADMINUSERS_TABLE_CELL_OWNED_TITLE,$user['name'],$numowned).'" href="'.$this->Href('','','user='.$user['name'].'&amp;action=owned').'">'.$numowned.'</a>' : '0';
				$changeslink = ($numchanges > 0)? '<a title="'.sprintf(ADMINUSERS_TABLE_CELL_EDITS_TITLE,$user['name'],$numchanges).'" href="'.$this->Href('','','user='.$user['name'].'&amp;action=changes').'">'.$numchanges.'</a>' : '0';
				$commentslink = ($numcomments > 0)? '<a title="'.sprintf(ADMINUSERS_TABLE_CELL_COMMENTS_TITLE,$user['name'],$numcomments).'" href="'.$this->Href('','','user='.$user['name'].'&amp;action=comments').'">'.$numcomments.'</a>' : '0';

				// build handler links
				// Disable delete link if user is admin
				$deleteuser = '';
				if($this->IsAdmin($user['name']))
				{
					$deleteuser = "<span class='disabled'>".ADMINUSERS_ACTION_DELETE_LINK."</span>";
				}
				else
				{
					$deleteuser = '<a title="'.sprintf(ADMINUSERS_ACTION_DELETE_LINK_TITLE, $user['name']).'" href="'.$this->Href('','','user='.$user['name'].'&amp;action=delete').'">'.ADMINUSERS_ACTION_DELETE_LINK.'</a>';
				}
				//$feedbackuser = '<a title="'.sprintf(ADMINUSERS_ACTION_FEEDBACK_LINK_TITLE, $user['name']).'" href="'.$this->Href('','','user='.$user['name'].'&amp;action=feedback').'">'.ADMINUSERS_ACTION_FEEDBACK_LINK.'</a>'; #to be added in 1.1.7, see #608
	
				// build table body
				$data_table .= '<tbody>'."\n";
				if ($r_color == 1)
				{
					$data_table .= '	<tr '.(($r%2)? '' : 'class="alt"').'>'."\n"; #enable alternate row color
				}
				else
				{
					$data_table .= '	<tr>'."\n"; #disable alternate row color
				}
				$data_table .= '		<td><input type="checkbox" name="'.$user['name'].'"'.$checked.' title="'.sprintf(ADMINUSERS_SELECT_RECORD_TITLE,$user['name']).'"/></td>'."\n".	
	 				'		<td>'.(($this->ExistsPage($user['name']))? $this->Link($user['name']) : $user['name']).'</td>'."\n". #check if userpage exists
			 		'		<td>'.$user['email'].'</td>'."\n".
					'		<td class="datetime">'.$user['signuptime'].'</td>'."\n".
					/* '		<td>'.$user['ipaddress'].'</td>'."\n". # installed as beta feature at wikkawiki.org */
					'		<td class="number'.(($c_color == 1)? ' c1' : '').'">'.$ownedlink.'</td>'."\n".  #set column color
					'		<td class="number'.(($c_color == 1)? ' c2' : '').'">'.$changeslink.'</td>'."\n". #set column color
					'		<td class="number'.(($c_color == 1)? ' c3' : '').'">'.$commentslink.'</td>'."\n".   #set column color
					'		<td class="actions">'.$deleteuser.'</td>'."\n". 
	 				'	</tr>'."\n".
	 				'</tbody>'."\n";
	
				//increase row counter    ----- alternate row colors
				if ($r_color == 1)
				{
					$r++;
				}
			}
			$data_table .= "</table>\n";

			// mass operations		JW 2005-07-19 accesskey removed (causes more problems than it solves)
			$form_mass = $this->FormOpen('','','get');
			$form_mass .= $data_table;			
			$form_mass .= '<fieldset>'."\n".
				'	<legend>'.ADMINUSERS_FORM_MASSACTION_LEGEND.'</legend>'."\n".
				'	[<a href="'.$this->Href('','','l='.$l.'&amp;sort='.$sort.'&amp;d='.$d.'&amp;s='.$s.'&amp;search='.urlencode($search).'&amp;selectall=1').'" title="'.ADMINUSERS_SELECT_ALL_TITLE.'">'.ADMINUSERS_SELECT_ALL.'</a> | <a href="'.$this->Href('','','l='.$l.'&amp;sort='.$sort.'&amp;d='.$d.'&amp;s='.$s.'&amp;search='.urlencode($search).'&amp;selectall=0').'" title="'.ADMINUSERS_DESELECT_ALL_TITLE.'">'.ADMINUSERS_DESELECT_ALL.'</a>]<br />'."\n".
				'	<label for="action" >'.ADMINUSERS_FORM_MASSACTION_LABEL.'</label>'."\n".
				'	<select title="'.ADMINUSERS_FORM_MASSACTION_SELECT_TITLE.'" id="action" name="action">'."\n".
				'		<option value="" selected="selected">---</option>'."\n".
				'		<option value="massdelete">'.ADMINUSERS_FORM_MASSACTION_OPT_DELETE.'</option>'."\n".
				//'		<option value="massfeedback">'.ADMINUSERS_FORM_MASSACTION_OPT_FEEDBACK.'</option>'."\n". #to be added in 1.1.7, see #608
				'	</select> <input type="submit" value="'.ADMINUSERS_FORM_MASSACTION_SUBMIT.'" />'."\n".
				'</fieldset>';
			$form_mass .= $this->FormClose();

			// output
			echo $form_filter;
			echo $form_mass;
		}
		else
		{
			// no records matching the search string: print error message
			echo '<p><em class="error">'.sprintf(ADMINUSERS_ERROR_NO_MATCHES, $search_disp).'</em></p>';
		}
	}
}
else
{
	// user is not admin
	echo $this->Action('lastusers');
}
?>
