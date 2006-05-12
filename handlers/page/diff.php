<div class="page">
<?php

define ('ERROR_DIV_LIBRARY_MISSING', 'The necessary file "libs/diff.lib.php" could not be found. Please make sure the file exists and is placed in the right directory!');
define ('ERROR_NO_PAGE_ACCESS', 'You are not authorized to view this page.');
define ('CONTENT_ADDITIONS_HEADER', 'Additions:');
define ('CONTENT_DELETIONS_HEADER', 'Deletions:');
define ('CONTENT_NO_DIFFERENCES', 'No Differences');

if ($this->HasAccess("read")) 
{

/* A php wdiff  (word diff) for wakka, adapted by David Delon
   based on wdiff and phpwiki diff (copyright in libs/diff.inc.php).
   TODO : Since wdiff use only directive lines, all stuff in diff class 
   related to line and context display should be removed.

   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2, or (at your option)
   any later version. */

// looking for the diff-classes
if (file_exists('libs/diff.lib.php')) require_once('libs/diff.lib.php');
else die(ERROR_DIV_LIBRARY_MISSING);

// If asked, call original diff 

	if ($_REQUEST["fastdiff"]) {
	   
		/* NOTE: This is a really cheap way to do it. I think it may be more intelligent to write the two pages to temporary files and run /usr/bin/diff over them. Then again, maybe not.        */ 
		// load pages
		  $pageA = $this->LoadPageById($_REQUEST["a"]);
		  $pageB = $this->LoadPageById($_REQUEST["b"]);
	
		// prepare bodies
		  $bodyA = explode("\n", $pageA["body"]);
		  $bodyB = explode("\n", $pageB["body"]);
	
		  $added = array_diff($bodyA, $bodyB);
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
	
	else {
	
	// load pages
	
		$pageA = $this->LoadPageById($_REQUEST["b"]);
		$pageB = $this->LoadPageById($_REQUEST["a"]);
	
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
