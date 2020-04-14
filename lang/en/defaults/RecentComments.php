<?php
$str = <<<EOD
=====Recent Comments=====

Recently made comments - a more complete overview than RecentlyCommented which lists only **pages** with the //last// comment on them for each date.

{{recentcomments}}

----
CategoryAdmin
EOD;
echo($str);
?>
