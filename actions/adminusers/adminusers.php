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

if(!defined('ADMINUSERS_DEFAULT_RECORDS_LIMIT')) define('ADMINUSERS_DEFAULT_RECORDS_LIMIT', '10'); # number of records per page
if(!defined('ADMINUSERS_DEFAULT_MIN_RECORDS_DISPLAY')) define('ADMINUSERS_DEFAULT_MIN_RECORDS_DISPLAY', '5'); # min number of records
if(!defined('ADMINUSERS_DEFAULT_RECORDS_RANGE')) define('ADMINUSERS_DEFAULT_RECORDS_RANGE',serialize(array('10','50','100','500','1000'))); #range array for records pager
if(!defined('ADMINUSERS_DEFAULT_START')) define('ADMINUSERS_DEFAULT_START', '0'); # start record
if(!defined('ADMINUSERS_DEFAULT_SEARCH')) define('ADMINUSERS_DEFAULT_SEARCH', ''); # keyword to restrict search
if(!defined('ADMINUSERS_ALTERNATE_ROW_COLOR')) define('ADMINUSERS_ALTERNATE_ROW_COLOR', '1'); # switch alternate row color
if(!defined('ADMINUSERS_STAT_COLUMN_COLOR')) define('ADMINUSERS_STAT_COLUMN_COLOR', '1'); # switch color for statistics columns
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
	if($this->GetSafeVar('cancel', 'post') == T_("Cancel"))
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
	elseif($this->GetSafeVar('submit', 'post') == T_("Delete"))
	{
		if($this->GetSafeVar('user', 'post') != null)
		{
			include_once($this->BuildFullpathFromMultipath('../../libs/admin.lib.php', $this->GetConfigValue('action_path')));
			#$status = DeleteUser($this, $this->GetSafeVar('user', 'post'));
            $this->Redirect($this->Href(), $this->GetSafeVar('user','post'));
			if(false===$status)
			{
				$this->Redirect($this->Href(), T_("Sorry, could not delete user. Please check your admin settings"));
			}
			else
			{
				$this->Redirect($this->Href(), T_("User has been successfully deleted"));
			}
		}
	}
	elseif($this->GetSafeVar('submit', 'post') == T_("Execute"))
	{
        if($this->GetSafeVar('force_password_reset', 'post') == 'true') {
            $users = $this->LoadUsers();
            $admins = explode(",", $this->GetConfigValue('admin_users'));
            array_push($admins, 'WikkaInstaller');
            foreach($users as $user) {
                if(array_search($user['name'], $admins) !== FALSE) {
                    continue;
                }
                $sql = "UPDATE ".$this->GetConfigValue('table_prefix')."users
                        SET force_password_reset=true 
                        WHERE name='".$user['name']."'";
                $this->Query($sql);
            }
            $this->Redirect();
        }
        else {
            $usernames = array();
            foreach($_POST as $key => $val)
            {
                $val = $this->GetSafeVar($key, 'post');
                if($val == "on")
                {
                    array_push($usernames, $this->htmlspecialchars_ent($key));
                }
            }
            if(count($usernames) > 0)
            {
                echo $this->FormOpen();
                echo '<h3>'.T_("Delete these users?").'</h3><br />'."\n".'<ul>';
                $errors = 0;
                foreach($usernames as $username)
                {
                    if($this->IsAdmin($username))
                    {
                        ++$errors;
                        echo '<li><span class="disabled">'.$username.'&nbsp;</span><em class="error">('.T_("Cannot delete admins").")</em></li>\n";
                        continue;
                    }
                    echo "<li>".$username."</li>\n";
                }
                echo "</ul><br/>\n";
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
                            <input type="submit" value="<?php echo T_("Delete Users");?>"  style="width: 120px"   />
                            <?php } ?>
                            <input type="submit" value="<?php echo T_("Cancel");?>" name="cancel" style="width: 120px" />
                        </td>
                    </tr>
                </table>
                <?php
                echo $this->FormClose();
            }
            else
            {
                $this->Redirect();
            }
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
			include_once($this->BuildFullpathFromMultipath('../../libs/admin.lib.php', $this->GetConfigValue('action_path')));
			$status = true;
			foreach($usernames as $username)
			{
				$status = $status && DeleteUser($this, $username);
			}
		}
		if(false === $status)
		{
			$this->Redirect($this->Href(), T_("Sorry, could not delete user. Please check your admin settings"));
		}
		else
		{
			$this->Redirect($this->Href(), T_("Users have been sucessfully deleted"));
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
		$sort = (isset($_GET['sort'])) ? $this->GetSafeVar('sort', 'get') : "signuptime";
		if(!in_array($sort, $sort_fields)) $sort = "signuptime";
		$d = 'desc';
		if(isset($_GET['d'])) {
			if($this->GetSafeVar('d', 'get') == 'asc') {
				$d = 'asc';
			}
		}
		// start record
		$s = (isset($_GET['s'])) ? $this->GetSafeVar('s', 'get') : ADMINUSERS_DEFAULT_START;
		if ((int)$s < 0) $s = ADMINUSERS_DEFAULT_START;
	
		// search string
		$search = ADMINUSERS_DEFAULT_SEARCH; 
		$search = ADMINUSERS_DEFAULT_SEARCH;
		if (isset($_POST['search']))
		{
			$search = $this->GetSafeVar('search', 'post');
		}
		elseif (isset($_GET['search']))
		{
			$search = $this->GetSafeVar('search', 'get');
		}
		elseif($this->GetSafeVar('submit', 'post') == T_("Submit"))
		{
			// Reset num recs per page for empty (reset) search
			$l = ADMINUSERS_DEFAULT_RECORDS_LIMIT;
		}
	
		// select all
		$checked = '';
		$params = array();
		if (isset($_GET['selectall']))
		{
			$checked = (1 == $_GET['selectall']) ? ' checked="checked"' : '';
		}
	
		// restrict MySQL query by search string
		$where = "(status IS NULL OR status != 'deleted') AND ";
		if('' == $search) {
			$where .= '1';
		} else {
			$where .= "name LIKE :search";
			$params = array(':search' => '%'.$search.'%');
		}
		// get total number of users

		$numusers = $this->getCount('users', $where, $params);
		// If the user doesn't specifically want to change the records
		// per page, then use the default.  The problem here is that one
		// form is being used to process two post requests, so things
		// (search string, num recs per page) get out of sync, requiring
		// multiple submissions.
		if(!isset($_GET['l']) && $this->GetSafeVar('submit', 'post') != T_("Apply"))
		{
			$l = ADMINUSERS_DEFAULT_RECORDS_LIMIT;
		}
	
		// print page header
		echo '<h3>'.T_("User Administration").'</h3>'."\n";

		//non-working message retrieval removed, see #753
	
		// build pager form	
		$form_filter = $this->FormOpen('','','post','user_admin_panel');
		$form_filter .= '<fieldset><legend>'.T_("Filter view:").'</legend>'."\n";
		$form_filter .= '<label for="search">'.T_("Search user:").'</label> <input type ="text" id="search" name="search" title="'.T_("Enter a search string").'" size="20" maxlength="50" value="'.$search.'"/> <input name="submit" type="submit" value="'.T_("Submit").'" /><br />'."\n";
		// get values range for drop-down
		$users_opts = optionRanges($user_limits,$numusers, ADMINUSERS_DEFAULT_MIN_RECORDS_DISPLAY);
		$form_filter .= '<label for="l">'.T_("Show").'</label> '."\n";
		$form_filter .= '<select name="l" id="l" title="'.T_("Select records-per-page limit").'">'."\n";
		// build drop-down
		foreach ($users_opts as $opt)
		{
			$selected = ($opt == $l) ? ' selected="selected"' : '';
			$form_filter .= '<option value="'.$opt.'"'.$selected.'>'.$opt.'</option>'."\n";
		}
		$form_filter .=  '</select> <label for="l">'.T_("records per page").'</label> <input name="submit" type="submit" value="'.T_("Apply").'" /><br />'."\n";
	
		// build pager links
		$ll = $s+$l+1;
		$ul = ($s+2*$l) > $numusers ? $numusers : ($s+2*$l);
		if ($s > 0)
		{
			$prev = '<a href="'.$this->Href('','','l='.$l.'&amp;sort='.$sort.'&amp;d='.$d.'&amp;s='.($s-$l)).  '&amp;search='.urlencode($search).'" title="'.sprintf(T_("Show records from %d to %d"), ($s-$l+1), $s).'">'.($s-$l+1).'-'.$s.'</a> |  '."\n";
		}
		if ($numusers > ($s + $l))
		{
			$next = ' | <a href="'.$this->Href('','','l='.$l.'&amp;sort='.$sort.'&amp;d='.$d.'&amp;s='.($s+$l)).'&amp;search='.urlencode($search).'" title="'.sprintf(T_("Show records from %d to %d"), $ll, $ul).'">'.$ll.(($ll==$ul)?'':('-'.$ul)).'</a>'."\n";
		}
		$form_filter .= T_("Records").' ('.$numusers.'): '.$prev.(($s+$l)>$numusers?($s+1).'-'.$numusers:($s+1).'-'.($s+$l)).$next.'<br />'."\n";
		$form_filter .= '<span class="sortorder">'.T_("Sorted by:").' <tt>'.$sort.', '.$d.'</tt></span>'."\n";
		$form_filter .= '</fieldset>'.$this->FormClose()."\n";

		// get user list
		$params[':s'] = (int)$s;
		$params[':l'] = (int)$l;
		$userdata = $this->LoadAll("SELECT ".implode(",", $sort_fields)." FROM ".$this->GetConfigValue('table_prefix')."users WHERE ".$where." ORDER BY ".$sort." ".$d." limit :s, :l", $params);
		if ($userdata)
		{
			// build header links
			$nameheader = '<a href="'.$this->Href('','', (($sort == 'name' && $d == 'asc')? 'l='.$l.'&amp;sort=name&amp;d=desc' : 'l='.$l.'&amp;sort=name&amp;d=asc')).'" title="'.T_("Sort by user name").'">'.T_("User Name").'</a>';
			$emailheader = '<a href="'.$this->Href('','', (($sort == 'email' && $d == 'asc')? 'l='.$l.'&amp;sort=email&amp;d=desc' : 'l='.$l.'&amp;sort=email&amp;d=asc')).'" title="'.T_("Sort by email").'">'.T_("Email").'</a>';
			$timeheader = '<a href="'.$this->Href('','', (($sort == 'signuptime' && $d == 'desc')? 'l='.$l.'&amp;sort=signuptime&amp;d=asc' : 'l='.$l.'')).'" title="'.T_("Sort by signup time").'">'.T_("Signup Time").'</a>';

			/*$ipheader = '<a href="'.$this->Href('','', (($sort == 'ipaddress' && $d == 'desc')? 'l='.$l.'&amp;sort=ipaddress&amp;d=asc' : 'l='.$l.'&amp;sort=ipaddress&amp;d=desc')).'" title="'.TABLE_HEADING_SIGNUPIP_TITLE.'">'.TABLE_HEADING_SIGNUPIP.'</a>'; # installed as beta feature at wikka.jsnx.com  */
	
			// build table headers
			$data_table = '<table id="adminusers" summary="'.T_("List of users registered on this server").'" border="1px" class="data">'."\n".
				'<thead>'."\n".
	  			'	<tr>'."\n".
				'		<th></th>'."\n".
				'		<th>'.$nameheader.'</th>'."\n".
				'		<th>'.$emailheader.'</th>'."\n".
				'		<th>'.$timeheader.'</th>'."\n".
				/* '		<th>'.$ipheader.'</th>'."\n". # installed as beta feature at wikkawiki.org */
				'		<th'.(($c_color == 1)? ' class="c1"' : '').' title="'.T_("Owned Pages").'"><img src="images/icons/keyring.png" class="icon" alt="O"/></th>'."\n".
				'		<th'.(($c_color == 1)? ' class="c2"' : '').' title="'.T_("Edits").'"><img src="images/icons/edit.png" class="icon" alt="E"/></th>'."\n".
				'		<th'.(($c_color == 1)? ' class="c3"' : '').' title="'.T_("Comments").'"><img src="images/icons/comment.png" class="icon" alt="C"/></th>'."\n".
	 		 	'	</tr>'."\n".
	 		 	'</thead>'."\n";
	
			// print user table
			foreach($userdata as $user)
			{
				// get counts	
				$where_owned	= "`owner` = :owner AND latest = 'Y'";
				$where_changes	= "`user` = :user";
				$where_comments	= "`user` = :user";
				$numowned = $this->getCount('pages', 
				                            $where_owned,
											array(':owner' => $user['name']));
				$numchanges = $this->getCount('pages', 
											  $where_changes,
											  array(':user' => $user['name']));
				$numcomments = $this->getCount('comments', 
				                               $where_comments,
											   array(':user' => $user['name']));
		
				// build statistics links if needed
				$ownedlink = ($numowned > 0)? '<a title="'.sprintf(T_("Display pages owned by %s (%d)"),$user['name'],$numowned).'" href="'.$this->Href('','','user='.$user['name'].'&amp;action=owned').'">'.$numowned.'</a>' : '0';
				$changeslink = ($numchanges > 0)? '<a title="'.sprintf(T_("Display page edits by %s (%d)"),$user['name'],$numchanges).'" href="'.$this->Href('','','user='.$user['name'].'&amp;action=changes').'">'.$numchanges.'</a>' : '0';
				$commentslink = ($numcomments > 0)? '<a title="'.sprintf(T_("Display comments by %s (%d)"),$user['name'],$numcomments).'" href="'.$this->Href('','','user='.$user['name'].'&amp;action=comments').'">'.$numcomments.'</a>' : '0';

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
				$data_table .= '		<td><input type="checkbox" name="'.$user['name'].'"'.$checked.' title="'.sprintf(T_("Select %s"),$user['name']).'"/></td>'."\n".	
	 				'		<td>'.(($this->ExistsPage($user['name']))? $this->Link($user['name']) : $user['name']).'</td>'."\n". #check if userpage exists
			 		'		<td>'.$user['email'].'</td>'."\n".
					'		<td class="datetime">'.$user['signuptime'].'</td>'."\n".
					/* '		<td>'.$user['ipaddress'].'</td>'."\n". # installed as beta feature at wikkawiki.org */
					'		<td class="number'.(($c_color == 1)? ' c1' : '').'">'.$ownedlink.'</td>'."\n".  #set column color
					'		<td class="number'.(($c_color == 1)? ' c2' : '').'">'.$changeslink.'</td>'."\n". #set column color
					'		<td class="number'.(($c_color == 1)? ' c3' : '').'">'.$commentslink.'</td>'."\n".   #set column color
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
			$form_mass = $this->FormOpen();
			$form_mass .= $data_table;			
			$form_mass .= '<fieldset>'."\n".
				'	<legend>'.T_("Mass-action").'</legend>'."\n".
				'	[<a href="'.$this->Href('','','l='.$l.'&amp;sort='.$sort.'&amp;d='.$d.'&amp;s='.$s.'&amp;search='.urlencode($search).'&amp;selectall=1').'" title="'.T_("Select all records").'">'.T_("Select all").'</a> | <a href="'.$this->Href('','','l='.$l.'&amp;sort='.$sort.'&amp;d='.$d.'&amp;s='.$s.'&amp;search='.urlencode($search).'&amp;selectall=0').'" title="'.T_("Deselect all records").'">'.T_("Deselect all").'</a>]<br />'."\n".
				'	<label for="action" >'.T_("With selected").'</label>'."\n".
				'	<select title="'.T_("Choose an action to apply to the selected records").'" id="action" name="action">'."\n".
				'		<option value="" selected="selected">---</option>'."\n".
				'		<option value="massdelete">'.T_("Delete selected").'</option><br/>'."\n".
        				//'		<option value="massfeedback">'.ADMINUSERS_FORM_MASSACTION_OPT_FEEDBACK.'</option>'."\n". #to be added in 1.1.7, see #608
               '    </select><br/>'."\n".
               # Force password reset checkbox
               '<input type="checkbox" name="force_password_reset" value="true"/>'.
               '<label for="force_password_reset">'.T_("Force password reset for all non-admin users?").'</label><br/>'."\n".	
               '<input type="submit" name="submit" value="'.T_("Execute").'" />'."\n".
			'</fieldset>';
			$form_mass .= $this->FormClose();

			// output
			echo $form_filter;
			echo $form_mass;
		}
		else
		{
			// no records matching the search string: print error message
			echo '<p><em class="error">'.sprintf(T_("Sorry, there are no users matching \"%s\""), $search).'</em></p>';
		}
	}
}
else
{
	// user is not admin
	echo $this->Action('lastusers');
}
?>
