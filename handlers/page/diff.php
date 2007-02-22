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
 * @author David Delon
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
 * @todo	replace $_REQUEST with either $_GET or $_POST (or both if really
 * 			necessary) - #312  
 */

echo '<div class="page">'."\n"; //TODO: move to templating class //TODO move _after_ redirect

// If javascript is disabled, user may get here after pressing button Next... on the /revisions handler. 
if ((isset($_GET['more_revisions'])) && (isset($_GET['a'])) && (isset($_GET['start'])))
{
	$this->Redirect($this->Href('revisions', '', 'a='.$_GET['a'].'&start='.$_GET['start']));
}

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
	if (!$pageA || !$pageB)
	{
		echo '<em class="error">'.ERROR_BAD_PARAMETERS.'</em><br />';
		return;	// @@@ ERROR: end div won't be produced here
	}
	// set up heading variables
	$linkPageA = '<a href="'.$this->Href('', '', 'time='.urlencode($pageA['time'])).'">'.$pageA['time'].'</a>';
	$linkPageB = '<a href="'.$this->Href('', '', 'time='.urlencode($pageB['time'])).'">'.$pageB['time'].'</a>';

	// If asked, call original diff 
	#if (isset($_REQUEST['fastdiff']) && $_REQUEST['fastdiff'])
	if (isset($_GET['fastdiff']) && $_GET['fastdiff'])	#312
	{

		// prepare bodies
		$bodyA = explode("\n", $pageA['body']);
		$bodyB = explode("\n", $pageB['body']);

		$added   = array_diff($bodyA, $bodyB);
		$deleted = array_diff($bodyB, $bodyA);

		//$output = sprintf('<h5>'.DIFF_FAST_COMPARISON_HEADER.'</h5><br />'."\n", $this->Href('', '', 'time='.urlencode($pageA['time'])), $pageA['time'], $this->Href('', '', 'time='.urlencode($pageB['time'])), $pageB['time']);
		$head = '<h5>'.sprintf(DIFF_FAST_COMPARISON_HEADER,$linkPageA,$linkPageB).'</h5>'."\n";
		// TODO add legend

		$output = '';

		if ($added)
		{
			// remove blank lines
			$output .= '<br />'."\n".'<strong>'.DIFF_ADDITIONS_HEADER.'</strong><br />'."\n"; //TODO make real heading
			$output .= '<ins>'.$this->Format(implode("\n", $added)).'</ins>';
		}

		if ($deleted)
		{
			$output .= '<br />'."\n".'<strong>'.DIFF_DELETIONS_HEADER.'</strong><br />'."\n"; //TODO make real heading
			$output .= '<del>'.$this->Format(implode("\n", $deleted)).'</del>';
		}
	
		if (!$added && !$deleted)
		{
			$output .= "<br />\n".DIFF_NO_DIFFERENCES;
		}
		echo $head.$output;
	}
	else
	{
		// extract text from bodies
		$textA = $pageB['body'];
		$textB = $pageA['body'];

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

		$sample_addition = '<ins>'.DIFF_SAMPLE_ADDITION.'</ins>';
		$sample_deletion = '<del>'.DIFF_SAMPLE_DELETION.'</del>';
		$head  = '<h5>'.sprintf(DIFF_COMPARISON_HEADER,$linkPageA,$linkPageB).'</h5>'."\n";
		$head .= sprintf(HIGHLIGHTING_LEGEND,$sample_addition,$sample_deletion);

		$output = '';

		while (1)
		{
			$sideO->skip_line();
			if ($sideO->isend())
			{
				break;
			}
			if ($sideO->decode_directive_line()) 
			{
				$argument=$sideO->getargument();
				$letter=$sideO->getdirective();
				switch ($letter)
				{
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
				if (($letter=='d') || ($letter=='c'))
				{
					$sideA->copy_whitespace($output);
					$output .='&yen;&yen;';
					$sideA->copy_word($output);
					$sideA->copy_until_ordinal($argument[1],$output);
					$output .=' &yen;&yen;';
				}

				// inserted word
				if ($letter == 'a' || $letter == 'c')
				{
					$sideB->copy_whitespace($output);
					$output .='&pound;&pound;';
					$sideB->copy_word($output);
					$sideB->copy_until_ordinal($argument[3],$output);
					$output .=' &pound;&pound;';
				}
			}
		}
		$sideB->copy_until_ordinal($count_total_right,$output);
		$sideB->copy_whitespace($output);

		$out = $this->Format($output);
		echo $head.$out;

	}
}
else
{
	echo '<em class="error">'.WIKKA_ERROR_ACL_READ.'</em>';
}
echo '</div>'."\n" //TODO: move to templating class
?>
