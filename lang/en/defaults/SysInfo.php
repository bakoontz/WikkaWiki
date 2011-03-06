<?php
printf(
'{{checkversion}}
=====%s=====

~-Wikka %s ##{{wikkaversion}}##
~-Wikka %s ##{{wikkapatchlevel}}##
~-PHP %s ##{{phpversion}}##
~-""MySQL"" %s ##{{mysqlversion}}##
~-""GeSHi"" %s ##{{geshiversion}}##
~-%s
~~-%s ##{{system show="host"}}##
~~-%s ##{{system show="os"}}##
~~-%s ##{{system show="machine"}}##

----
CategoryAdmin', T_('System Information'), T_('version:'), T_('patch level:'), T_('version:'), T_('version:'), T_('version:'), T_('Server:'), T_('Host:'), T_('Operating System:'), T_('Machine:'));
?>
