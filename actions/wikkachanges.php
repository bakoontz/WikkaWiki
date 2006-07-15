<?php
/**
 * Print information on the current release.
 * 
 * @package	Actions
 * @version $Id$
 * 
 * @uses	Wakka::Format()
 * @todo	should be a default page, not an action
 * @filesource
 */

$output = "=====Wikka Release Notes=====\n\n".
"This server is running [[http://wikkawiki.org/ Wikka Wiki]] version ##{{wikkaversion}}##.\n".
"For detailed release notes please check the [[http://wikkawiki.org/WikkaReleaseNotes Wikka website]].";

print $this->Format($output);

?>