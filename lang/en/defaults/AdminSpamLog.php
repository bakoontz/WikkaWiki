<?php
$str = <<<EOD
=====Spam Log Management=====
>>**see also:**
~-RecentComments (with an interface to mass-delete spam and add them to the log)
~-AdminBadWords (to maintain the list of words for the content filter)
**documentation and development**
~-WikkaBetaFeatures
~-[[SpamLogAction | SpamLog Action]]

''While this action itself is still in beta, the 'spam log' this action allows to view, maintain and manage is very much "live" and **actually used for logging rejected "spammy submissions"**, which can be comments, pages or feedback.'' 
>>This is a test page for the (beta) [[SpamLogAction | SpamLog]] action.

Current data are a little bit "lacking" since during development the format changed somewhat. (The edit functionality was used to add placeholders for missing data; from now on new data should be writting in this format.)

While it's already possible to "clear" the file it's probably not a good idea to do this yet - it provides good data to test upcoming features with, such as selectable and sortable columns in the "summary" view and statistics. Apart from that, the user interface gives some hints about planned but not yet implemented  features. Some columns in the Summary view are truncated; you can hover your mouse or otherwise retrieve a title attribute to get the full string; the plan is to make the length(s) configurable to suit your own font and window size.
~&That said, I've just cleared out a lot of bytes resulting from DarTar trying to save a page with a **lot** of links; these log items were not useful at all (though the page is!) - and URL throttling for admins isn't either. //The edit handler has now been patched to apply URL throttling only for non-admins.// --JavaWoman

Please note that the development and documentation pages still need to be created or updated!::c::
{{spamlog}}

----
CategoryAdmin
EOD;
echo($str);
?>
