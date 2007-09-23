<?php
/**
 * Compare two versions of a page and output the differences.
 * 
 * Parameters to this handler are passed through $_GET. <ul>
 * <li>$_GET['a'] is the id of the base revision of the page</li>
 * <li>$_GET['b'] is the id of the revision to compare</li>
 * <li>$_GET['fastdiff'], if provided, enables the normal diff, and if absent, the simple diff is used.</li>
 * <li><em>If $_GET['more_revisions'] is also present, this means that JavaScript is disabled and the user
 * was on the {@link revisions.php revision handler}, so the page is redirected to that handler, with the
 * parameters $a and $start.</em></li></ul>
 *
 * @package     Handlers
 * @subpackage  Page
 * @version 	$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @author	David Delon
 * 
 * @uses	Wakka::HasAccess()
 * @uses	Wakka::LoadPageById()
 * @uses	Wakka::Href()
 * @uses	Wakka::Format()
 * @uses	Wakka::Redirect()
 * @uses	Diff
 * 
 * @todo	move main <div> to templating class;
 * @todo 	This is a really cheap way to do it. I think it may be more intelligent to write the two pages to temporary files and run /usr/bin/diff over them. Then again, maybe not.
 * 			JW: that may be nice but won't work on a Windows system ;) 
 */

define ('ERROR_DIV_LIBRARY_MISSING', 'The necessary file "libs/diff.lib.php" could not be found. Please make sure the file exists and is placed in the right directory!');
define ('ERROR_NO_PAGE_ACCESS', 'You are not authorized to view this page.');
define ('CONTENT_ADDITIONS_HEADER', 'Additions:');
define ('CONTENT_DELETIONS_HEADER', 'Deletions:');
define ('CONTENT_NO_DIFFERENCES', 'No Differences');

echo '<div class="page">'."\n"; //TODO: move to templating class //TODO move _after_ redirect

if ($this->HasAccess("read")) 
{
	// looking for the diff-classes
	// @@@ TODO needed only if we're NOT doing a 'fastdiff'
	// instead of copping out we could use fastdiff as fallback if the library is missing:
	// first determine diff method based on params AND presense of library; then do it
	if (file_exists('libs'.DIRECTORY_SEPARATOR.'diff.lib.php')) require_once('libs'.DIRECTORY_SEPARATOR.'diff.lib.php');	#89
	else die(ERROR_DIFF_LIBRARY_MISSING);	// @@@ ERROR: end div won't be produced here

	// load pages
	#$pageA = $this->LoadPageById($_REQUEST['a']);
	#$pageB = $this->LoadPageById($_REQUEST['b']);
	$pageA = $this->LoadPageById($_GET['a']);	#312
	$pageB = $this->LoadPageById($_GET['b']);	#312

	// If asked, call original diff 
	#if (isset($_REQUEST['fastdiff']) && $_REQUEST['fastdiff'])
	if (isset($_GET['fastdiff']) && $_GET['fastdiff'])	#312
	{
		// prepare bodies
		$bodyA = explode("\n", $pageA['body']);
		$bodyB = explode("\n", $pageB['body']);

		$added   = array_diff($bodyA, $bodyB);
		$deleted = array_diff($bodyB, $bodyA);
	
		  $output .= "<b>Comparison of  <a href=\"".$this->Href("", "", "time=".urlencode($pageA["time"]))."\">".$pageA["time"]."</a> &amp; <a href=\"".$this->Href("", "", "time=".urlencode($pageB["time"]))."\">".$pageB["time"]."</a></b><br />\n"; #i18n
	
		  if ($added)
		  {
			// remove blank lines
			$output .= "<br />\n<b>".CONTENT_ADDITIONS_HEADER."</b><br />\n";
			$output .= "<span class=\"additions\">".$this->Format(implode("\n", $added))."</span>";
		  }
	
		  if ($deleted)
		  {
			$output .= "<br />\n<b>".CONTENT_DELETIONS_HEADER."</b><br />\n";
			$output .= "<span class=\"deletions\">".$this->Format(implode("\n", $deleted))."</span>";
		  }
	
		  if (!$added && !$deleted)
		  {
			$output .= "<br />\n".CONTENT_NO_DIFFERENCES;
		  }
		  echo $output;
	
	}
	else
	{
		// extract text from bodies
		$textA = $pageA["body"];
		$textB = $pageB["body"];
	
		$sideA = new Side($textA);
		$sideB = new Side($textB);
	
		$bodyA='';
		$sideA->split_file_into_words($bodyA);
	
		$bodyB='';
		$sideB->split_file_into_words($bodyB);
	
		// diff on these two file
		$diff = new Diff(split("\n",$bodyA),split("\n",$bodyB));
	
		// format output
		$fmt = new DiffFormatter();
	
		$sideO = new Side($fmt->format($diff));
	
		$resync_left=0;
		$resync_right=0;
	
		$count_total_right=$sideB->getposition() ;
	
		$sideA->init();
		$sideB->init();
	
		echo "<b>Comparing <a href=\"".$this->Href("", "", "time=".urlencode($pageA["time"]))."\">".$pageA["time"]."</a> to <a href=\"".$this->Href("", "", "time=".urlencode($pageB["time"]))."\">".$pageB["time"]."</a></b> "; #i18n
		echo "-- Highlighting Guide: <span class=\"additions\">addition</span> <span class=\"deletions\">deletion</span><p>"; #i18n
		$output='';

		  while (1) {
		       
		      $sideO->skip_line();
		      if ($sideO->isend()) {
			  break;
		      }
	
		      if ($sideO->decode_directive_line()) {
			$argument=$sideO->getargument();
			$letter=$sideO->getdirective();
		      switch ($letter) {
			    case 'a':
			      $resync_left = $argument[0];
			      $resync_right = $argument[2] - 1;
			      break;
	
			    case 'd':
			      $resync_left = $argument[0] - 1;
			      $resync_right = $argument[2];
			      break;
	
			    case 'c':
			      $resync_left = $argument[0] - 1;
			      $resync_right = $argument[2] - 1;
			      break;
	
			    }
	
			    $sideA->skip_until_ordinal($resync_left);
			    $sideB->copy_until_ordinal($resync_right,$output);
	  
	// deleted word
	
			if (($letter=='d') || ($letter=='c')) {
				$sideA->copy_whitespace($output);
				$output .="&yen;&yen;";
				$sideA->copy_word($output);
				$sideA->copy_until_ordinal($argument[1],$output);
				$output .=" &yen;&yen;";
			}
	
	// inserted word
			    if ($letter == 'a' || $letter == 'c') {
				$sideB->copy_whitespace($output);
				$output .="&pound;&pound;";
				$sideB->copy_word($output);
				$sideB->copy_until_ordinal($argument[3],$output);
				$output .=" &pound;&pound;";
			    }
	
		  }
	
		}
	
		  $sideB->copy_until_ordinal($count_total_right,$output);
		  $sideB->copy_whitespace($output);
		  $out=$this->Format($output);
		  echo $out;
	
	}

}
else{
	echo '<em>'.ERROR_NO_PAGE_ACCESS.'</em>';
}
?>
</div>
