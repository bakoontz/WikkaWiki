<?php
printf('{{image url="images/wikka_logo.jpg" alt="wikka logo" title="%s"}}---', T_("Welcome to your Wikka site"));
printf('{{checkversion}}---');
printf(T_('Thanks for installing Wikka! This wiki runs on version %s, patch level %s. You may want to read the %s to learn what\'s new in this release.---'), '##{{wikkaversion}}##', '##{{wikkapatchlevel}}##', '[[WikkaReleaseNotes '.T_("release notes").']]');
printf('---');
printf('>>==%s==', T_('Keep up-to-date'));
printf(T_('To receive the latest news from the Wikka Development Team, you can sign up to one of our %s, subscribe to our %s or join us for a chat on %s.---'), '[[http://wikkawiki.org/WikkaMailingLists '.T_('mailing lists').']]', '[[http://blog.wikkawiki.org Blog]]', '[[http://wikkawiki.org/TheLounge IRC]]');
printf('>>====%s====', T_('Getting started'));
printf(T_('Double-click on this page or click on the %s link in the page footer to get started. If you are not sure how a wiki works, you can check out the %s and play in the SandBox.---'), '**'.T_('Edit').'**', '[[FormattingRules '.T_("Wikka formatting guide").']]');
printf('---');
printf('>>==%s==', T_('Need more help?'));
printf(T_('Don\'t forget to visit the %s!'), '[[http://wikkawiki.org '.T_('WikkaWiki website').']]');
printf('>>====%s====', T_('Some useful pages'));
printf('
~-[[FormattingRules %s]]
~-[[WikkaDocumentation %s]]
~-[[RecentChanges %s]]
~-[[SysInfo %s]]
', T_('Wikka formatting guide'), T_('Documentation'), T_('Recently modified pages'), T_('System Information'));
printf('---');
printf(T_('You will find more useful pages in the %s or in the %s.---'), '[[CategoryWiki '.T_("Wiki category").']]', 'PageIndex');
?>
