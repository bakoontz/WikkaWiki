<?php

$output = "=====Wikka Release Notes=====\n\n".
"This server is running [[http://wikkawiki.org/ Wikka Wiki]] version **{{wikkaversion}}**.\n".
"The release notes are described on the [[http://wikkawiki.org/WikkaReleaseNotes main Wikka server]].";

print $this->Format($output);

?>