<?php
// JavaWoman 2005-07-23
//	- use ellipsis to truncate content
//	- (more) standardized day and time format
//	- use user's recentchanges count as view limit
//	- display data in one table per day
//	- for admin each day is form with checkboxes; checked comments can be deleted (and logged) as spam
// JavaWoman 2005-07-23
//	- refinement of log data
// JavaWoman 2005-07-26
//	- add log user/origin to log data
//  TODO
//	- prepare strings for internationalization
//	- phpDoc comment block and better code structure
//	- find a way to reposition on the form that was updated with '(un)check all'
define('DEFAULT_TERMINATOR', '&#8230;'); # standard symbol replacing truncated text (ellipsis)

$longdayformat  = 'Y-m-d (l)';		# with day name
$longtimeformat = 'H:i:s T';		# with time zone
$dayformat		= 'Y-m-d';
$timeformat     = 'H:i:s';

if ($user = $this->GetUser()) {
	$max = $user['changescount'];
} else {
	$max = 50;
}
$isAdmin = $this->IsAdmin();

// handle any removal requests
if ($isAdmin && isset($_POST))
{
	foreach ($_POST as $key => $value)
	{
		// chceck if a remove button was pressed
		if (preg_match('/^remove_(.+)$/',$key,$aMatches))	# a remove button was pressed
		{
			$day = $aMatches[1];							# deduce for which day
			$aComments = 'd_'.$day;							# handle comments checkboxes for that day
			foreach ($_POST[$aComments] as $idComment => $checkComment)
			{
				if ($checkComment == 1)
				{
					// get comment data
					$comment = $this->loadCommentId($idComment);
					if (isset($comment['id']))				# if comment was retrieved (i.e. not already removed by someone!)
					{
						// delete the comment
						$rc = $this->deleteComment($idComment);
						// log if deletion successful
						if ($rc)
						{
							$body		= str_replace('<br />','',$comment['comment']);
							$user		= $comment['user'];
							$reason		= 'mass delete by '.$user['name'].', '.date($dayformat.' '.$timeformat);
							$urlcount	= preg_match_all('/\b[a-z]+:\/\/\S+/',$body,$dummy);	# matches is a required parameter but we're interesting in the count only
							$time		= $comment['time'];
							$this->logSpamComment($comment['page_tag'],$body,$reason,$urlcount,$user,$time);
						}
					}
				}
			}
		}
	}
}

// show overview (with form for admin)
if ($comments = $this->LoadRecentComments($max))	# JW 2005-07-23 use RecentChanges limit (default is a fixed 50)
{
	$curday = NULL;
	foreach ($comments as $comment)
	{
		// day header
		$time = $comment['time'];
		$day = substr($time,0,10);
		if ($day != $curday)
		{
			// finish previous day
			if (isset($curday))
			{
				if ($isAdmin)
				{
					echo "</tbody>\n";
					echo '<tbody>'."\n";
					echo '<tr>'."\n";
					echo '	<td colspan="4" class="buttonrow">'.'<input type="submit" name="remove_'.$curday.'" value="Remove spam" title="Will be written to spam log" />'."</td>\n";
					echo "</tr>\n";
				}
				echo "</tbody>\n";
				echo "</table>\n";
				if ($isAdmin)
				{
					echo $this->FormClose();
				}
			}
			$curday = $day;

			// start new day
			// handle check/clear buttons
			if ($isAdmin)
			{
				$checked = '';
				if (isset($_POST['checkall_'.$day]))
				{
					$checked = ' checked="checked"';
				}
				elseif (isset($_POST['clearall_'.$day]))
				{
					$checked = '';
				}
			}

			$dayformatted = date($longdayformat,strtotime($time));
			#$action = $this->GetPageTag().'#form_'.$day;	@@@ fragments not supported yet in Href, MiniHref or FormOpen

			if ($isAdmin)
			{
				echo $this->FormOpen('','','post',$day);				# use day as form id
			}
			echo '<table summary="Comments for '.$day.'" class="commentday">'."\n";
			echo '<caption>'.$dayformatted."</caption>\n";
			echo "<thead>\n";
			echo "<tr>\n";
			if ($isAdmin)
			{
				echo '	<th scope="col" class="check">Spam?</th>'."\n";
			}
			echo '	<th scope="col" rowspan="2" class="time">Time</th>'."\n";
			echo '	<th scope="col" class="tag">Page</th>'."\n";
			echo '	<th scope="col" class="user">User</th>'."\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo '	<th scope="col" class="check">';
			if ($isAdmin)
			{
				if ('' == $checked)
				{
					echo '<input type="submit" name="checkall_'.$curday.'" value="Check all" class="checkall" />';
				}
				else
				{
					echo '<input type="submit" name="clearall_'.$curday.'" value="Uncheck all" class="checkall" />';
				}
			}
			echo "	</th>\n";
			echo '	<th scope="colspan" colspan="2">Content (summary)</th>'."\n";
			echo "</tr>\n";
			echo "</thead>\n";
			echo '<tbody>'."\n";
		}

		// two rows for one comment
		$timeformatted = date($timeformat,strtotime($time));		# leave out timezone
		$cid = $comment['id'];
		$pagecommentlink = '<a href="'.$this->Href('',$comment['page_tag'],'show_comments=1').'#comment_'.$cid.'">'.$comment['page_tag'].'</a>';
		$user = ($this->IsWikiName($comment['user'])) ? $this->Format($comment['user']) : $comment['user'];		# as in show handler: user URL should not be formatted
		$comment_preview = str_replace('<br />','',$comment['comment']);
		if (strlen($comment_preview) > 125)
		{
			$comment_preview = substr($comment_preview, 0, 125).DEFAULT_TERMINATOR;
		}

		echo "<tr>\n";
		if ($isAdmin)
		{
			echo '	<td rowspan="2" class="check"> <input type="checkbox" name="d_'.$day.'['.$cid.']" value="1"'.$checked.' /> </td>'."\n";
		}
		echo '	<td rowspan="2" class="time">'.$timeformatted."</td>\n";
		echo '	<td>'.$pagecommentlink."</td>\n";
		echo '	<td>'.$user."</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo '	<td colspan="2">'.$comment_preview.'</td>'."\n";
		echo "</tr>\n";
	}

	// finish last day
	if ($isAdmin)
	{
		echo "</tbody>\n";
		echo '<tbody>'."\n";
		echo '<tr>'."\n";
		echo '	<td colspan="4" class="buttonrow">'.'<input type="submit" name="remove_'.$curday.'" value="Remove spam" title="Will be written to spam log" />'."</td>\n";
		echo "</tr>\n";
	}
	echo "</tbody>\n";
	echo "</table>\n";
	if ($isAdmin)
	{
		echo $this->FormClose();
	}
}
else
{
	print("<em>No recent comments.</em>");
}
?>