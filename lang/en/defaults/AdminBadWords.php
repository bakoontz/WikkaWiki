<?php
$str = <<<EOD
=====""BadWords"" list maintenance=====

>>**See also:**
~-WikkaBetaFeatures
~-[[BadWordsAction | BadWords Action]]
**Note:**
While this action itself is still in beta, the 'badwords' list this action allows to maintain is very much "live" and **actually used for content filtering of comments**. Words were taken from the referrers blacklist (for now) but the list is not "complete" - so in that sense it's also still "beta". In other words, while you can "play" adding or removing words, be careful not to add innocent words!
>>This is a test page for the ([[WikkaBetaFeatures | beta]]) ""BadWords"" action.::c::
""<!--"Doesn't look right with your skin? See <a href="DbInfoAction#hn_Styling">Styling</a> on DbInfoAction."-->""
{{badwords}}

~& Sounds interesting, but how does this work? -- DarTar
~~&Patience... - I've been **very** busy spam fighting (still am, in fact - see your mail :)); documentation will follow. --JavaWoman

----
CategoryAdmin
EOD;
echo($str);
?>
