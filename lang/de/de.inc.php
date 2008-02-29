<?php
/**
 * Wikka language file.
 *
 * This file holds all interface language strings for Wikka (in german).
 * 
 * Based on rev 916 of the en.inc.php.
 *
 * @package		Language
 *
 * @version		$Id$
 * @license 	http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author 		{@link http://wikkawiki.org/NilsLindenberg Nils Lindenberg}
 *
 * @copyright 	Copyright 2007, {@link http://wikkawiki.org/CreditsPage Wikka Development Team}
 *
 * @todo		make sure that punctuation is always part of defined phrase
 *				(check where these constants are used)
 * @todo		use single quotes whenever possible
 * 				(unless	that leads to more than one escaped single quote)
 * @todo		move the rest of the hardcoded texts in here (double-check)
 *
 * @todo		document the use of phpdoc group docblocks to append automatically descriptions to multiple constants.
 *
 * @todo		backlink to constants adding the <tt>uses</tt> tag in the corresponding components
 */

/* ------------------ COMMON ------------------ */

/**#@+
 * Language constant shared among several Wikka components
 */
// NOTE: all common names (used in multiple files) should start with WIKKA_ !
define('WIKKA_ERROR_SETUP_FILE_MISSING', 'Eine für die Installation / das Upgrade notwendige Datei wurde nicht gefunden. Bitte installieren Sie Wikka erneut!');
define('WIKKA_ERROR_MYSQL_ERROR', 'MySQL Fehler: %d - %s');	// %d - error number; %s - error text
define('WIKKA_ERROR_CAPTION', 'Fehler');
define('WIKKA_ERROR_ACL_READ', 'Sie dürfen diese Seite nicht lesen.');
define('WIKKA_ERROR_ACL_READ_SOURCE', 'Sie dürfen den Quellcode dieser Seite nicht betrachten.');
define('WIKKA_ERROR_ACL_READ_INFO', 'Sie dürfen auf diese Information nicht zugreifen.');
define('WIKKA_ERROR_LABEL', 'Fehler');
define('WIKKA_ERROR_PAGE_NOT_EXIST', 'Die Seite %s gibt es leider nicht.'); // %s (source) page name
define('WIKKA_ERROR_EMPTY_USERNAME', 'Bitte wählen Sie einen Benutzernamen!');
define('WIKKA_ERROR_INVALID_PAGE_NAME', '%s ist kein gültiger Seitenname. Gültige Namen müssen mit einem Großbuchstaben beginnen, dürfen nur Buchstaben und Nummern enthalten und müssen CamelCase formatiert sein.'); // %s - page name
define('WIKKA_ERROR_PAGE_ALREADY_EXIST', 'Die Zielseite existiert bereits');
define('WIKKA_LOGIN_LINK_DESC', 'Login');
define('WIKKA_MAINPAGE_LINK_DESC', 'Hauptseite');
define('WIKKA_NO_OWNER', 'Niemand');
define('WIKKA_NOT_AVAILABLE', 'n/a');
define('WIKKA_NOT_INSTALLED', 'nicht installiert');
define('WIKKA_ANONYMOUS_USER', 'Anonymus'); // 'name' of non-registered user
define('WIKKA_UNREGISTERED_USER', 'unregistrierter Benutzer'); // alternative for 'anonymous' @@@ make one string only?
define('WIKKA_ANONYMOUS_AUTHOR_CAPTION', '('.WIKKA_UNREGISTERED_USER.')'); // @@@ or WIKKA_ANONYMOUS_USER
define('WIKKA_SAMPLE_WIKINAME', 'MaxMustermann'); // must be a CamelCase name
define('WIKKA_HISTORY', 'Geschichte');
define('WIKKA_REVISIONS', 'Versionen');
define('WIKKA_REV_WHEN_BY_WHO', '%1$s von %2$s'); // %1$s - timestamp; %2$s - user name
define('WIKKA_NO_PAGES_FOUND', 'Keine Seiten gefunden.');
define('WIKKA_PAGE_OWNER', 'Besitzer: %s'); // %s - page owner name or link
define('WIKKA_COMMENT_AUTHOR_DIVIDER', ', kommentiert von '); //TODo check if we can construct a single phrase here
define('WIKKA_PAGE_EDIT_LINK_DESC', 'Bearbeiten');
define('WIKKA_PAGE_CREATE_LINK_DESC', 'Erstellen');
define('WIKKA_PAGE_EDIT_LINK_TITLE', 'Hier klicken, um %s zu bearbeiten'); // %s page name @@@ 'Edit %s'
define('WIKKA_BACKLINKS_LINK_TITLE', 'Zeigt eine Liste der Seiten an, die auf %s linken.'); // %s page name
define('WIKKA_JRE_LINK_DESC', 'Java Runtime Environment');
define('WIKKA_NOTE', 'NOTE:');
define('WIKKA_JAVA_PLUGIN_NEEDED', 'Das Java 1.4.1 Plug-in (oder eine neuere Version) ist notwendig, um dieses Applett zu starten,');
/**#@-*/


/*  ------------------ CORE ------------------  */

/**#@+
 * Language constant for the core {@link wikka.php wikka} program
 */
// wikka
define('ERROR_WAKKA_LIBRARY_MISSING', 'Die notwendige Datei "%s" konnte nicht gefunden werden. Damit Wikka läuft, vergewissern Sie sich bitte, dass die Datei existiert und im richtigen Verzeichnis platziert ist!');	// %s - configured path to core class
define('ERROR_NO_DB_ACCESS', 'Fehler: Es konnte keine Verbindung zur Datenbank hergestellt werden.');
define('ERROR_RETRIEVAL_MYSQL_VERSION', 'Konnte MySQL-Version nicht ermitteln');
define('ERROR_WRONG_MYSQL_VERSION', 'Wikka setzt MySQL %s oder besser vorraus!');	// %s - version number
define('STATUS_WIKI_UPGRADE_NOTICE', 'Diese Seite wird gerade upgedated. Bitte versuchen Sie es später noch einmal.');
define('STATUS_WIKI_UNAVAILABLE', 'Das Wiki ist zur Zeit nicht erreichbar.');
define('PAGE_GENERATION_TIME', 'Die Seite wurde in %.4f Sekunden erstellt.'); // %.4f - page generation time
define('ERROR_HEADER_MISSING', 'Es wurde kein header-Template gefunden. Bitte stellen Sie sicher, dass sich eine Datei namens <code>header.php</code> im templates Verzeichnis befindet.'); //TODO Make sure this message matches any filename/folder change
define('ERROR_FOOTER_MISSING', 'Es wurde kein footer-Template gefunden. Bitte stellen Sie sicher, dass sich eine Datei namens <code>footer.php</code> im templates Verzeichnis befindet.'); //TODO Make sure this message matches any filename/folder change

#define('ERROR_WRONG_PHP_VERSION', '$_REQUEST[] not found. Wakka requires PHP 4.1.0 or higher!'); //TODO remove referral to PHP internals; refer only to required version
#define('ERROR_SETUP_HEADER_MISSING', 'The file "setup/header.php" was not found. Please install Wikka again!');
#define('ERROR_SETUP_FOOTER_MISSING', 'The file "setup/footer.php" was not found. Please install Wikka again!');
/**#@-*/

/*  ------------------ TEMPLATE ------------------  */

/**#@+
 * Language constant used by the {@link header.php header} template
 */
// header
define('GENERIC_DOCTITLE', '%1$s: %2$s');	// %1$s - wiki name; %2$s - page title
define('RSS_REVISIONS_TITLE', '%1$s: Versionen von %2$s');	// %1$s - wiki name; %2$s - current page name
define('RSS_RECENTCHANGES_TITLE', '%s: zuletzt geänderte Seiten');	// %s - wiki name
define('YOU_ARE', 'Sie sind %s'); // %s - name / ip of the user.
/**#@-*/

/**#@+
 * Language constant used by the {@link footer.php footer} template
 */
// footer
define('FOOTER_PAGE_EDIT_LINK_DESC', 'Seite bearbeiten');
define('PAGE_HISTORY_LINK_TITLE', 'Hier klicken, um die letzten Änderungen an dieser Seite zu sehen'); // @@@ TODO 'View recent edits to this page'
define('PAGE_HISTORY_LINK_DESC', 'Seitengeschichte');
define('PAGE_REVISION_LINK_TITLE', 'Hier klicken um die Liste der letzten Versionen für diese Seite zu sehenC'); // @@@ TODO 'View recent revisions list for this page'
define('PAGE_REVISION_XML_LINK_TITLE', 'Hier klicken um die Liste der letzten Versionen für diese Seite zu sehen'); // @@@ TODO 'View recent revisions list for this page'
define('PAGE_ACLS_EDIT_LINK_DESC', 'Rechte bearbeiten');
define('PAGE_ACLS_EDIT_ADMIN_LINK_DESC', '('.PAGE_ACLS_EDIT_LINK_DESC.')');
define('PUBLIC_PAGE', 'Öffentliche Seite');
define('USER_IS_OWNER', 'Diese Seite gehört Ihnen.');
define('TAKE_OWNERSHIP', 'Seite in Besitz nehmen');
define('REFERRERS_LINK_TITLE', 'Hier klicken, um eine Liste der Referrer für diese Seite zu sehen'); // @@@ TODO 'View a list of URLs referring to this page'
define('REFERRERS_LINK_DESC', 'Referrer');
define('QUERY_LOG', 'Query-Log:');
define('SEARCH_LABEL', 'Suche:');
/**#@-*/


/*  ------------------ ACTIONS  ------------------  */

/**#@+
 * Language constant used by the {@link calendar.php calendar} action
 */
// calendar
define('FMT_SUMMARY', 'Kalender für %s');	// %s - ???@@@
define('TODAY', 'heute');
/**#@-*/

/**#@+
 * Language constant used by the {@link category.php category} action
 */
// category
define('ERROR_NO_PAGES', 'Leider keine Treffer für %s');	// %s - ???@@@
define('PAGES_BELONGING_TO', 'Die folgenden %1$d Seite(n) gehört/gehören zu %2$s'); // %1$d number found; %2$s category
/**#@-*/

/**#@+
 * Language constant used by the {@link color.php color} action
 */
// color
define('ERROR_NO_TEXT_GIVEN', 'Es wurde kein Text eingegeben!');
define('ERROR_NO_COLOR_SPECIFIED', 'Es wurde keine Farbe gewählt!');
/**#@-*/

/**#@+
 * Language constant used by the {@link contact.php contact} action
 */
// contact
define('SEND_FEEDBACK_LINK_TITLE', 'Senden Sie uns Feedback');
define('SEND_FEEDBACK_LINK_TEXT', 'Kontakt');
/**#@-*/

/**#@+
 * Language constant used by the {@link countowned.php countowned} action
 */
// countowned
define('DISPLAY_MYPAGES_LINK_TITLE', 'Zeigt eine Liste der Seiten an, die Ihnen gehören');
/**#@-*/

/**#@+
 * Language constant used by the {@link countpages.php countpages} action
 */
// countpages
define('INDEX_LINK_TITLE', 'Zeigt einen alphabetischen Indx der Seiten');
/**#@-*/

/**#@+
 * Language constant used by the {@link dbinfo.php dbinfo} action
 */
// dbinfo
define('HD_DBINFO','Datenbank-Information');
define('HD_DBINFO_DB','Datenbank');
define('HD_DBINFO_TABLES','Tabellen');
define('HD_DB_CREATE_DDL','DDL to create database %s:');				# %s will hold database name
define('HD_TABLE_CREATE_DDL','DDL to create table %s:');				# %s will hold table name
define('TXT_INFO_1','This utility provides some information about the database(s) and tables in your system.');
define('TXT_INFO_2',' Depending on permissions for the Wikka database user, not all databases or tables may be visible.');
define('TXT_INFO_3',' Where creation DDL is given, this reflects everything that would be needed to exactly recreate the same database and table definitions,');
define('TXT_INFO_4',' including defaults that may not have been specified explicitly.');
define('FORM_SELDB_LEGEND','Datenbanken');
define('FORM_SELTABLE_LEGEND','Tabellen');
define('FORM_SELDB_OPT_LABEL','Wählen Sie eine Datenbank:');
define('FORM_SELTABLE_OPT_LABEL','Wählen Sie eine Tabelle:');
define('FORM_SUBMIT_SELDB','Auswählen');
define('FORM_SUBMIT_SELTABLE','Auswählen');
define('MSG_ONLY_ADMIN','Sorry, only administrators can view database information.');
define('MSG_SINGLE_DB','Information for the <tt>%s</tt> database.');			# %s will hold database name
define('MSG_NO_TABLES','No tables found in the <tt>%s</tt> database. Your MySQL user may not have sufficient privileges to access this database.');		# %s will hold database name
define('MSG_NO_DB_DDL','Creation DDL for <tt>%s</tt> could not be retrieved.');	# %s will hold database name
define('MSG_NO_TABLE_DDL','Creation DDL for <tt>%s</tt> could not be retrieved.');# %s will hold table name
/**#@-*/

/**#@+
 * Language constant used by the {@link emailpassword.php emailpassword} action
 */
// emailpassword
define('PW_FORGOTTEN_HEADING', 'Passworterinnerung');
define('PW_CHK_SENT', 'Ein temporäres Passwort wurde an die von %s\'s angegebene Emailaddresse verschickt.'); // %s - username
define('PW_FORGOTTEN_MAIL', 'Hallo, %1$s\n\n\nSomeone requested that we send to this email address a password reminder to login at %2$s. If you did not request this reminder, disregard this email. -- No action is necessary. -- Your password will stay the same.\n\nYour wikiname: %1$s \nPassword reminder: %3$s \nURL: %4$s \n\nDo not forget to change the password immediately after logging in.'); // %1$s - username; %2$s - wiki name; %3$s - md5 sum of pw; %4$s - login url of the wiki
define('PW_FORGOTTEN_MAIL_REF', 'Temporäres Passwort für %s'); // %s - wiki name
define('PW_FORM_TEXT', 'Geben Sie Ihren WikiNamen ein um ein temporäres Paswort an Ihre registrierte Emailadresse geschickt zu bekommen.');
define('PW_FORM_FIELDSET_LEGEND', 'Ihr WikiName:');
define('ERROR_UNKNOWN_USER', 'Der angegebene Benutzername existiert nicht!');
#define('ERROR_MAIL_NOT_SENT', 'An error occurred while trying to send the password. Outgoing mail might be disabled. Please contact your server administrator.');
define('ERROR_MAIL_NOT_SENT', 'Bei dem Versuch, das Passwort zu versenden ist ein Fehler aufgetreten. Möglicherweise ist das Versenden von Mails deaktiviert. Versuchen Sie, dem Administrator des Wikis zu erreichen, beispielsweise über einen Kommentar auf einer Seite.');
define('BUTTON_SEND_PW', 'Temporäres Passwort senden');
define('USERSETTINGS_REF', 'Zurück zu %s.'); // %s - UserSettings link
/**#@-*/

/**#@+
 * Language constant used by the {@link feedback.php feedback} action
 */
// feedback
define('ERROR_EMPTY_NAME', 'Bitte geben Sie ihren Namen an');
define('ERROR_INVALID_EMAIL', 'Bitte geben Sie eine gültige Emailadresse an');
define('ERROR_EMPTY_MESSAGE', 'Sie haben keinen Text eingegeben');
define('ERROR_FEEDBACK_MAIL_NOT_SENT', 'Bei dem Versuch, die Email zu versenden ist ein Fehler aufgetreten. Möglicherweise ist das Versenden von Mails deaktiviert. Versuchen Sie, %s auf anderem Wege zu erreichen, beispielsweise über einen Kommentar auf einer Seite.'); // %s - name of the recipient
define('FEEDBACK_FORM_LEGEND', '%s kontaktieren'); //%s - wikiname of the recipient
define('FEEDBACK_NAME_LABEL', 'Ihr Name:');
define('FEEDBACK_EMAIL_LABEL', 'Ihre Email:');
define('FEEDBACK_MESSAGE_LABEL', 'Ihre Nachricht:');
define('FEEDBACK_SEND_BUTTON', 'Senden');
define('FEEDBACK_SUBJECT', 'Feedback von %s'); // %s - name of the wiki
define('SUCCESS_FEEDBACK_SENT', 'Vielen Dank für Ihr Feedback, %s! Ihre Nachricht wurde verschickt.'); //%s - name of the sender
/**#@-*/

/**#@+
 * Language constant used by the {@link files.php files} action
 */
// files
define('ERROR_UPLOAD_DIRECTORY_NOT_WRITABLE', 'Bitte stellen Sie sicher dass der Server Schreibrechte für das Verzeichnis %s besitzt.'); // %s Upload folder ref #89
define('ERROR_UPLOAD_DIRECTORY_NOT_READABLE', 'Bitte stellen Sie sicher dass der Server Leserechte für das Verzeichnis %s besitzt.'); // %s Upload folder ref #89
define('ERROR_NONEXISTENT_FILE', 'Eine Datei mit dem Namen %s existiert nicht.'); // %s - file name ref
define('ERROR_FILE_UPLOAD_INCOMPLETE', 'Die Datei wurde leider nur unvollständig hochgeladen! Bitte versuchen Sie es erneut.');
define('ERROR_UPLOADING_FILE', 'Beim Hochladen der Datei ist ein Fehler aufgetreten');
define('ERROR_FILE_ALREADY_EXISTS', 'Eine Datei namens %s ist bereits vorhanden.'); // %s - file name ref
define('ERROR_EXTENSION_NOT_ALLOWED', 'Dateien mit dieser Endung sind leider nicht erlaubt.');
define('ERROR_FILE_TOO_BIG', 'Die gewählte Datei ist zu groß. Maximale Größe für Dateien: %s.'); // %s - allowed filesize
define('ERROR_NO_FILE_SELECTED', 'Keine Datei ausgewählt.');
define('ERROR_FILE_UPLOAD_IMPOSSIBLE', 'Auf Grund einer fehlerhaften Serverkonfiguration ist das Hochladen von Dateien nicht möglich.');
define('SUCCESS_FILE_UPLOADED', 'Die Datei wurde erfolgreich hochgeladen.');
define('FILE_TABLE_CAPTION', 'Dateien');
define('FILE_TABLE_HEADER_NAME', 'Datei');
define('FILE_TABLE_HEADER_SIZE', 'Größe');
define('FILE_TABLE_HEADER_DATE', 'Zuletzt geändert am');
define('FILE_UPLOAD_FORM_LEGEND', 'Neue Datei hochladen:');
define('FILE_UPLOAD_FORM_LABEL', 'Datei:');
define('FILE_UPLOAD_FORM_BUTTON', 'hochladen');
define('DOWNLOAD_LINK_TITLE', '%s herunterladen'); // %s - file name
define('DELETE_LINK_TITLE', '%s löschen'); // %s - file name
define('NO_ATTACHMENTS', 'Auf dieser Seite gibt es keine Dateien.');
/**#@-*/

/**#@+
 * Language constant used by the {@link googleform.php googleform} action
 */
// googleform
define('GOOGLE_BUTTON', 'Google');
/**#@-*/

/**#@+
 * Language constant used by the {@link include.php include} action
 */
// include
define('ERROR_CIRCULAR_REFERENCE', 'Zirkulare Referenz entdeckt!');
/**#@-*/

/**#@+
 * Language constant used by the {@link lastedit.php lastedit} action
 */
// lastedit
define('LASTEDIT_DESC', 'Zuletzt geändert von %s'); // %s user name
define('LASTEDIT_DIFF_LINK_TITLE', 'Unterschiede zur letzten Version zeigen');
/**#@-*/

/**#@+
 * Language constant used by the {@link lastusers.php lastusers} action
 */
// lastusers
define('LASTUSERS_CAPTION', 'Zuletzt registrierte Benutzer');
define('SIGNUP_DATE_TIME', 'Registrierungdatum/-zeit');
define('NAME_TH', 'Benutzername');
define('OWNED_PAGES_TH', 'Anzahl an Seiten');
define('SIGNUP_DATE_TIME_TH', 'Registrierungsdatum/-zeit');
/**#@-*/

/**#@+
 * Language constant used by the {@link mindmap.php mindmap} action
 */
// mindmap
define('MM_JRE_INSTALL_REQ', 'Bitte installieren sie %s auf ihrem Computer.'); // %s - JRE install link
define('MM_DOWNLOAD_LINK_DESC', 'Diese Mindmap herunterladen');
define('MM_EDIT', 'Benutzen Sie %s zum Editieren'); // %s - link to freemind project
define('MM_FULLSCREEN_LINK_DESC', 'Als Vollbild öffnen');
define('ERROR_INVALID_MM_SYNTAX', 'Fehler: falsche MindMap-Action Syntax.');
define('PROPER_USAGE_MM_SYNTAX', 'Richtige Syntax: %1$s oder %2$s'); // %1$s - syntax sample 1; %2$s - syntax sample 2
/**#@-*/

/**#@+
 * Language constant used by the {@link mychanges.php mychanges} action
 */
// mychanges
define('NO_PAGES_EDITED', 'Sie haben noch keine Seiten geändert.');
define('MYCHANGES_ALPHA_LIST', 'Dies ist die Liste der Seiten die Sie geändert haben zusammen mit dem Datum der letzten Änderung.');
define('MYCHANGES_DATE_LIST', 'Dies ist die Liste der Seiten die Sie geändert haben, geordnet nach dem Datum der letzten Änderung.');
define('ORDER_DATE_LINK_DESC', 'nach Datum sortieren');
define('ORDER_ALPHA_LINK_DESC', 'alphabetisch sortieren');
define('MYCHANGES_NOT_LOGGED_IN', 'Sie sind nicht angemeldet, daher konnte die Liste mit den von Ihnen geänderten Seiten nicht erstellt werden.');
/**#@-*/

/**#@+
 * Language constant used by the {@link mypages.php mypages} action
 */
// mypages
define('OWNED_PAGES_TXT', 'Dies ist die Liste der Seiten, die Ihnen gehören.');
define('OWNED_NO_PAGES', 'Ihnen gehören keine Seiten.');
define('OWNED_NOT_LOGGED_IN', 'Sie sind nicht eingeloggt, daher konnte die Liste Ihrer Seiten nicht erstellt werden.');
/**#@-*/

/**#@+
 * Language constant used by the {@link newpage.php newpage} action
 */
// newpage
define('NEWPAGE_CREATE_LEGEND', 'Neue Seite erstellen');
define('NEWPAGE_CREATE_BUTTON', 'Erstellen');
/**#@-*/

/**#@+
 * Language constant used by the {@link orphanedpages.php orphanedpages} action
 */
// orphanedpages
define('NO_ORPHANED_PAGES', 'Keine verwaisten Seiten. Gut!');

/**#@+
 * Language constant used by the {@link ownedpages.php ownedpages} action
 */
// ownedpages
define('OWNEDPAGES_COUNTS', 'Ihnen gehören %1$s Seiten von insgesamt %2$s Seiten in diesem Wiki.'); // %1$s - number of pages owned; %2$s - total number of pages
define('OWNEDPAGES_PERCENTAGE', 'Das heißt, Ihnen gehören %s Prozent.'); // %s percentage of pages owned
/**#@-*/

/**#@+
 * Language constant used by the {@link pageindex.php pageindex} action
 */
// pageindex
define('PAGEINDEX_HEADING', 'Seitenindex');
define('PAGEINDEX_CAPTION', 'Dies ist ein alphabetische Liste aller Seite die Sie in diesem Wiki lesen dürfen.');
define('PAGEINDEX_OWNED_PAGES_CAPTION', 'Seiten mit einem * gehören Ihnen.');
define('PAGEINDEX_ALL_PAGES', 'Alle');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges.php recentchanges} action
 */
// recentchanges
define('RECENTCHANGES_HEADING', 'Zuletzt geänderte Seiten');
define('REVISIONS_LINK_TITLE', 'Liste der letzten Versionen für %s ansehen'); // %s - page name
define('HISTORY_LINK_TITLE', 'Seitengeschichte von %s ansehen'); // %s - page name
define('WIKIPING_ENABLED', 'WikiPing aktiviert: Änderungen in diesem Wiki werden auch auf %s angezeigt.'); // %s - link to wikiping server
define('RECENTCHANGES_NONE_FOUND', 'Es gibt keine zuletzt geänderten Seiten.');
define('RECENTCHANGES_NONE_ACCESSIBLE', 'Es gibt keine zuletzt geänderten Seiten die Sie lesen dürfen.');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentcomments.php recentcomments} action
 */
// recentcomments
define('RECENTCOMMENTS_HEADING', 'Letzte Kommentare');
define('RECENTCOMMENTS_TIMESTAMP_CAPTION', '%s'); // %s - timestamp
define('RECENTCOMMENTS_NONE_FOUND', 'Es gibt keine letzten Kommentare.');
define('RECENTCOMMENTS_NONE_ACCESSIBLE', 'Es gibt keine letzten Kommentare die Sie lesen dürfen.');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentlycommented.php recentlycommented} action
 */
// recentlycommented
define('RECENTLYCOMMENTED_HEADING', 'Zuletzt kommentierte Seiten');
define('RECENTLYCOMMENTED_NONE_FOUND', 'Es gibt keine zuletzt kommentierten Seiten.');
define('RECENTLYCOMMENTED_NONE_ACCESSIBLE', 'Es gibt keine zuletzt kommentierten Seiten die Sie lesen dürfen.');
/**#@-*/

/**#@+
 * Language constant used by the {@link system.php system} action
 */
// system
define('SYSTEM_HOST_CAPTION', '(%s)'); // %s - host name
define('WIKKA_STATUS_NOT_AVAILABLE', 'n/a');
/**#@-*/

/**#@+
 * Language constant shared by the {@link textsearch.php textsearch} and {@link textsearchexpanded.php textsearchexpanded} actions
 */
// textsearch & textsearchexpanded
define('SEARCH_FOR', 'Suche nach');
define('SEARCH_ZERO_MATCH', 'Keine Treffer');
define('SEARCH_ONE_MATCH', 'Ein Treffer gefunden');
define('SEARCH_N_MATCH', '%d Treffer gefunden'); // %d - number of hits
define('SEARCH_RESULTS', 'Ergebnisse der Suche: <strong>%1$s</strong> für <strong>%2$s</strong>'); # %1$s: n matches for | %2$s: search term 
define('SEARCH_NOT_SURE_CHOICE', 'Unsicher welche Seite es ist?');
define('SEARCH_EXPANDED_LINK_DESC', 'erweiterte Volltextsuche'); // search link description
define('SEARCH_TRY_EXPANDED', 'Probieren Sie die %s um auch die Fundstellen anzuzeigen.'); // %s expanded search link
/*
define('SEARCH_TIPS', "<br /><br /><hr /><br /><strong>Search Tips:</strong><br /><br />"
	."<div class=\"indent\">apple banana</div>"
	."Find pages that contain at least one of the two words. <br />"
	."<br />"
	."<div class=\"indent\">+apple +juice</div>"
	."Find pages that contain both words. <br />"
	."<br />"
	."<div class=\"indent\">+apple -macintosh</div>"
	."Find pages that contain the word 'apple' but not 'macintosh'. <br />"
	."<br />"
	."<div class=\"indent\">apple*</div>"
	."Find pages that contain words such as apple, apples, applesauce, or applet. <br />"
	."<br />"
	."<div class=\"indent\">\"some words\"</div>"
	."Find pages that contain the exact phrase 'some words' (for example, pages that contain 'some words of wisdom' <br />"
	."but not 'some noise words'). <br />");
*/
define('SEARCH_TIPS', 'Suchtipps:');
define('SEARCH_WORD_1', 'apple');
define('SEARCH_WORD_2', 'banana');
define('SEARCH_WORD_3', 'juice');
define('SEARCH_WORD_4', 'macintosh');
define('SEARCH_WORD_5', 'some');
define('SEARCH_WORD_6', 'words');
define('SEARCH_PHRASE',sprintf('%s %s',SEARCH_WORD_5,SEARCH_WORD_6));
define('SEARCH_TARGET_1', 'Seiten finden, die zumindest eines der beiden Wörter enthalten.');
define('SEARCH_TARGET_2', 'Nur Seiten finden, die beide Wörter enthalten.');
define('SEARCH_TARGET_3',sprintf("Seiten finden, die zwar '%1\$s' aber nicht '%2\$s' enthalten.",SEARCH_WORD_1,SEARCH_WORD_4));
define('SEARCH_TARGET_4',"Find pages that contain words such as 'apple', 'apples', 'applesauce', or 'applet'."); // make sure target words all *start* with SEARCH_WORD_1
define('SEARCH_TARGET_5',sprintf("Find pages that contain the exact phrase '%1\$s' (for example, pages that contain '%1\$s of wisdom' but not '%2\$s noise %3\$s').",SEARCH_PHRASE,SEARCH_WORD_5,SEARCH_WORD_6));
/**#@-*/

/**#@+
 * Language constant used by the {@link usersettings.php usersettings} action
 */
// usersettings
// - error messages
define('ERROR_EMPTY_USERNAME', 'Bitte wählen Sie einen Benutzernamen.');
define('ERROR_NONEXISTENT_USERNAME', 'Dieser Benutzername existiert nicht.'); // @@@ too specific
define('ERROR_RESERVED_PAGENAME', 'Dieser Benutzername ist schon für eine Seite reserviert. Bitte wählen Sie einen anderen Namen.');
define('ERROR_WIKINAME', 'Ein Benutzername muss wie ein %1$s formatiert sein, beispielsweise %2$s.'); // %1$s - identifier WikiName; %2$s - sample WikiName
define('ERROR_EMPTY_EMAIL_ADDRESS', 'Bitten geben Sie eine Emailadresse an.');
define('ERROR_INVALID_EMAIL_ADDRESS', 'Die Emailadresse scheint ungültig zu sein.');
define('ERROR_INVALID_PASSWORD', 'Sie haben das falsche Passwort eingegeben.');	// @@@ too specific
define('ERROR_INVALID_HASH', 'Das temporäre Passwort stimmt leider nicht.');
define('ERROR_INVALID_OLD_PASSWORD', 'Das alte Passwort stimmt nicht.');
define('ERROR_EMPTY_PASSWORD', 'Bitte wählen Sie ein Passwort.');
define('ERROR_EMPTY_PASSWORD_OR_HASH', 'Bitten geben Sie ihr Passwort oder ihre Passworterinnerung ein.');
define('ERROR_EMPTY_CONFIRMATION_PASSWORD', 'Bitte bestätigen Sie ihr Passwort, um einen neuen Account zu registrieren.');
define('ERROR_EMPTY_NEW_CONFIRMATION_PASSWORD', 'Please confirm your new password in order to update your account.');
define('ERROR_EMPTY_NEW_PASSWORD', 'Dass neue Passwort darf nicht leer sein.');
define('ERROR_PASSWORD_MATCH', 'Die Passwörter stimmen nicht überein.');
define('ERROR_PASSWORD_NO_BLANK', 'Leerzeichen sind in einem Passwort leider nicht erlaubt.');
define('ERROR_PASSWORD_TOO_SHORT', 'Das Passwort muss mindestens %d Zeichen lang sein.'); // %d - minimum password length
define('ERROR_INVALID_INVITATION_CODE', 'This is a private wiki, only invited members can register an account! Please contact the administrator of this website for an invitation code.');
define('ERROR_INVALID_REVISION_DISPLAY_LIMIT', 'The number of page revisions should not exceed %d.'); // %d - maximum revisions to view
define('ERROR_INVALID_RECENTCHANGES_DISPLAY_LIMIT', 'The number of recently changed pages should not exceed %d.'); // %d - maximum changed pages to view
// - success messages
define('SUCCESS_USER_LOGGED_OUT', 'Sie haben sich erfolgreich abgemeldet.');
define('SUCCESS_USER_REGISTERED', 'Sie haben sich erfolgreich registriert!');
define('SUCCESS_USER_SETTINGS_STORED', 'Einstellungen gespeichert!');
define('SUCCESS_USER_PASSWORD_CHANGED', 'Passwort erfolgreich geändert!');
// - captions
define('NEW_USER_REGISTER_CAPTION', 'If you are signing up as a new user:');
define('REGISTERED_USER_LOGIN_CAPTION', 'If you already have a login, sign in here:');
define('RETRIEVE_PASSWORD_CAPTION', 'Log in with your [[%s password reminder]]:'); //%s PasswordForgotten link
define('USER_LOGGED_IN_AS_CAPTION', 'Sie sind angemeldet als %s'); // %s user name
// - form legends
define('USER_ACCOUNT_LEGEND', 'Ihr Account');
define('USER_SETTINGS_LEGEND', 'Einstellungen');
define('LOGIN_REGISTER_LEGEND', 'Anmelden/Registrieren');
define('LOGIN_LEGEND', 'Anmelden');
#define('REGISTER_LEGEND', 'Register'); // @@@ TODO to be used later for register-action
define('CHANGE_PASSWORD_LEGEND', 'Ändern Sie ihr Passwort');
define('RETRIEVE_PASSWORD_LEGEND', 'Passworterinnerung');
// - form field labels (should end in ':' _unless_ it's a checkbox or radio button option)
define('USERSETTINGS_REDIRECT_AFTER_LOGIN_LABEL', 'Redirect to %s after login');	// %s page to redirect to
define('USER_EMAIL_LABEL', 'Ihre Emailadresse:');
define('DOUBLECLICK_LABEL', 'Editieren mit Doubleclick:');
define('SHOW_COMMENTS_LABEL', 'Kommentare standardmäßig anzeigen:');
define('DEFAULT_COMMENT_STYLE_LABEL', 'Default comment style');
define('COMMENT_ASC_LABEL', 'Flach (älteste zuerst)');
define('COMMENT_DEC_LABEL', 'Flach (neue zuerst)');
define('COMMENT_THREADED_LABEL', 'Hierarchisch');
define('COMMENT_DELETED_LABEL', '[Kommentar gelöscht]');
define('COMMENT_BY_LABEL', 'Kommentar von ');
define('RECENTCHANGES_DISPLAY_LIMIT_LABEL', 'RecentChanges display limit:');
define('PAGEREVISION_LIST_LIMIT_LABEL', 'Page revisions list limit:');
define('NEW_PASSWORD_LABEL', 'Ihr neues Passwort:');
define('NEW_PASSWORD_CONFIRM_LABEL', 'Neues Passwort bestätigen:');
define('NO_REGISTRATION', 'Registration on this wiki is disabled.');
define('PASSWORD_LABEL', 'Password (%s+ Zeichen):'); // %s minimum number of characters
define('CONFIRM_PASSWORD_LABEL', 'Passwort bestätigen:');
define('TEMP_PASSWORD_LABEL', 'temporäres Passwort:');
define('INVITATION_CODE_SHORT', 'Einladungscode');
define('INVITATION_CODE_LONG', 'In order to register, you must fill in the invitation code sent by this website\'s administrator.');
define('INVITATION_CODE_LABEL', 'Ihr %s:'); // %s - expanded short invitation code prompt
define('WIKINAME_SHORT', 'WikiName');
define('WIKINAME_LONG',sprintf('A WikiName is formed by two or more capitalized words without space, e.g. %s',WIKKA_SAMPLE_WIKINAME));
define('WIKINAME_LABEL', 'Ihr %s:'); // %s - expanded short wiki name prompt
// - form options
define('CURRENT_PASSWORD_OPTION', 'Ihr derzeitiges Passwort');
define('PASSWORD_REMINDER_OPTION', 'Passworterinnerung');
// - form buttons
define('UPDATE_SETTINGS_BUTTON', 'Einstellungen speichern');
define('LOGIN_BUTTON', 'anmelden');
define('LOGOUT_BUTTON', 'abmelden');
define('CHANGE_PASSWORD_BUTTON', 'Passwort ändern');
define('REGISTER_BUTTON', 'Registrieren');
/**#@-*/

/**#@+
 * Language constant used by the {@link wantedpages.php wantedpages} action
 */
// wantedpages
define('SORTING_LEGEND', 'Sortiert nach ...');
define('SORTING_NUMBER_LABEL', 'Sortierung #%d:');
define('SORTING_DESC_LABEL', 'absteigend');
define('OK_BUTTON', 'Sortieren');
define('NO_WANTED_PAGES', 'Keine gewünschten Seiten. Gut!');
/**#@-*/


/* ------------------ 3RD PARTY ------------------ */

/**#@+
 * Language constant used by the {@link fullscreen.php fullscreen} 3rd party MindMap display utility
 */
// fullscreen
define('CLOSE_WINDOW', 'Fenster schliessen');
define('MM_GET_JAVA_PLUGIN_LINK_DESC', 'get the latest Java Plug-in here'); // used in MM_GET_JAVA_PLUGIN
define('MM_GET_JAVA_PLUGIN', 'so if it does not work, %s.'); // %s - plugin download link
/**#@-*/


/* ------------------ FORMATTERS ------------------ */

/**#@+
 * Language constant used by the {@link wakka.php wakka} formatter
 */
// wakka
define('GRABCODE_BUTTON', 'Grab');
define('GRABCODE_BUTTON_TITLE', '%s herunterladen'); // %s download filename
/**#@-*/


/* ------------------ HANDLERS (PAGE) ------------------ */

/**#@+
 * Language constant used by the {@link acls.php acls} (page) handler
 */
// acls
// TODO: 'translate' DB value '(Public)' when displaying it!
define('ACLS_UPDATED', 'Rechtelisten aktualisiert.');
define('NO_PAGE_OWNER', '(Niemand)');
define('NOT_PAGE_OWNER', 'Sie sind nicht der Besitzer dieser Seite.');
define('PAGE_OWNERSHIP_CHANGED', '%s ist nun Besitzer der Seite.'); // %s - name of new owner
define('ACLS_LEGEND', 'Rechtelisten für %s'); // %s - name of current page
define('ACLS_READ_LABEL', 'Leserechte:');
define('ACLS_WRITE_LABEL', 'Schreibrechte:');
define('ACLS_COMMENT_READ_LABEL', 'Kommentar-Leserechte:');
define('ACLS_COMMENT_POST_LABEL', 'Kommentar-Schreibrechte:');
define('SET_OWNER_LABEL', 'Besitzer der Seite bestimmen:');
define('SET_OWNER_CURRENT_OPTION', '(derzeitiger Besitzer)');
define('SET_OWNER_PUBLIC_OPTION', '(Öffentlichkeit)'); // actual DB value will remain '(Public)' even if this option text is translated!
define('SET_NO_OWNER_OPTION', '(Niemand - kein Besitzer)');
define('ACLS_STORE_BUTTON', 'Rechte speichern');
define('CANCEL_BUTTON', 'abbrechen');
// - syntax
define('ACLS_SYNTAX_HEADING', 'Syntax:');
define('ACLS_EVERYONE', 'Jeder');
define('ACLS_REGISTERED_USERS', 'Registrierte Benutzer');
define('ACLS_NONE_BUT_ADMINS', 'Niemand (außer Admins)');
define('ACLS_ANON_ONLY', 'nur anonyme Benutzer');
define('ACLS_LIST_USERNAMES', 'der Benutzer mit dem Namen %s; Sie können so viele Benutzer hinzufügen, wie Sie wollen, einen pro Zeile'); // %s - sample user name
define('ACLS_NEGATION', 'Any of these items can be negated with a %s:'); // %s - 'negation' mark
define('ACLS_DENY_USER_ACCESS', '%s das Recht verweigern'); // %s - sample user name
define('ACLS_AFTER', 'nach');
define('ACLS_TESTING_ORDER1', 'Die Rechte werden in der Reihenfolge ausgewertet, in der sie angegeben sind:');
define('ACLS_TESTING_ORDER2', 'So be sure to specify %1$s on a separate line %2$s negating any users, not before.'); // %1$s - 'all' mark; %2$s - emphasised 'after'
/**#@-*/

/**#@+
 * Language constant used by the {@link backlinks.php backlinks} (page) handler
 */
// backlinks
define('BACKLINKS_HEADING', 'Seiten die auf %s verweisen');
define('BACKLINKS_NO_PAGES', 'Keine Seite verweist auf diese Seite.');
/**#@-*/

/**#@+
 * Language constant used by the {@link claim.php claim} (page) handler
 */
// claim
define('USER_IS_NOW_OWNER', 'Sie sind jetzt Besitzer dieser Seite.');
/**#@-*/

/**#@+
 * Language constant used by the {@link clone.php clone} (page) handler
 */
// clone
define('ERROR_ACL_WRITE', 'Sie haben leider keine Schreibrechte für %s');
define('CLONE_VALID_TARGET', 'Bitte geben sie einen validen Namen für die Zeilseite und eine (optionale) Bearbeitungsnotiz an.');
define('CLONE_LEGEND', '%s klonen'); // %s source page name
define('CLONED_FROM', 'Geklont von %s'); // %s source page name
define('SUCCESS_CLONE_CREATED', '%s wurde erfolgreich erstellt!'); // %s new page name
define('CLONE_X_TO_LABEL', 'Klonen als:');
define('CLONE_EDIT_NOTE_LABEL', 'Bearbeitungsnotiz:');
define('CLONE_EDIT_OPTION_LABEL', ' Nach dem Klonen bearbeiten');
define('CLONE_ACL_OPTION_LABEL', ' Rechte klonen');
define('CLONE_BUTTON', 'Klonen');
/**#@-*/

/**#@+
 * Language constant used by the {@link delete.php delete} (page) handler
 */
// delete
define('ERROR_NO_PAGE_DEL_ACCESS', 'Sie dürfen diese Seite nicht löschen.');
define('PAGE_DELETION_HEADER', '%s löschen'); // %s - name of the page
define('SUCCESS_PAGE_DELETED', 'Seite wurde gelöscht!');
define('PAGE_DELETION_CAPTION', 'Diese Seite vollständig löschen, inklusive aller Kommentare?');
define('PAGE_DELETION_DELETE_BUTTON', 'Seite löschen');
define('PAGE_DELETION_CANCEL_BUTTON', 'abbrechen');
/**#@-*/

/**#@+
 * Language constant used by the {@link diff.php diff} (page) handler
 */
// diff
define('ERROR_DIFF_LIBRARY_MISSING', 'Die notwendige Datei "'.WIKKA_LIBRARY_PATH.DIRECTORY_SEPARATOR.'diff.lib.php" wurde nicht gefunden. Please make sure the file exists and is placed in the right directory!'); //TODO 'Please make sure' should be 'please inform WikiAdmin' - end user can't "make sure"
define('ERROR_BAD_PARAMETERS', 'There is something wrong with parameters you supplied, it\'s very likely that one of the versions you want to compare has been deleted.');
define('DIFF_ADDITIONS_HEADER', 'Additions:');
define('DIFF_DELETIONS_HEADER', 'Deletions:');
define('DIFF_NO_DIFFERENCES', 'Keine Unterschiede');
define('DIFF_FAST_COMPARISON_HEADER', 'Vergleich von %1$s &amp; %2$s'); // %1$s - link to page A; %2$s - link to page B
define('DIFF_COMPARISON_HEADER', 'Comparing %2$s to %1$s'); // %1$s - link to page A; %2$s - link to page B (yes, they're swapped!)
define('DIFF_SAMPLE_ADDITION', 'hinzugefügt');
define('DIFF_SAMPLE_DELETION', 'gelöscht');
define('HIGHLIGHTING_LEGEND', 'Highlighting Guide: %1$s %2$s'); // %1$s - sample added text; %2$s - sample deleted text
/**#@-*/

/**#@+
 * Language constant used by the {@link edit.php edit} (page) handler
 */
// edit
define('ERROR_OVERWRITE_ALERT1', 'OVERWRITE ALERT: This page was modified by someone else while you were editing it.');
define('ERROR_OVERWRITE_ALERT2', 'Bitte kopieren Sie ihre Änderungen und bearbeiten Sie die Seite erneut.');
define('ERROR_MISSING_EDIT_NOTE', 'Fehlender Bearbeitungskommentar: Bitte geben Sie einen Bearbeitungskommentar ein!');
define('ERROR_TAG_TOO_LONG', 'Page name too long! %d characters max.'); // %d - maximum page name length
define('ERROR_NO_WRITE_ACCESS', 'You don\'t have write access to this page. You might need to [[UserSettings login]] or [[UserSettings register an account]] to be able to edit this page.'); //TODO Distinct links for login and register actions
define('EDIT_STORE_PAGE_LEGEND', 'Seite speichern');
define('EDIT_PREVIEW_HEADER', 'Vorschau');
define('EDIT_NOTE_LABEL', 'Bitte geben Sie einen Kommentar zu ihrer Bearbeitung an.'); // label after field, so no colon!
define('MESSAGE_AUTO_RESIZE', 'Clicking on %s will automatically truncate the page name to the correct size'); // %s - rename button text
define('EDIT_PREVIEW_BUTTON', 'Vorschau');
define('EDIT_STORE_BUTTON', 'Speichern');
define('EDIT_REEDIT_BUTTON', 'Erneut bearbeiten');
define('EDIT_CANCEL_BUTTON', 'abbrechen');
define('EDIT_RENAME_BUTTON', 'umbenennen');
define('ACCESSKEY_PREVIEW', 'p'); // ideally, should match EDIT_PREVIEW_BUTTON
define('ACCESSKEY_STORE', 's'); // ideally, should match EDIT_STORE_BUTTON
define('ACCESSKEY_REEDIT', 'r'); // ideally, should match EDIT_REEDIT_BUTTON
define('SHOWCODE_LINK', 'View formatting code for this page');
define('SHOWCODE_LINK_TITLE', 'Click to view page formatting code'); // @@@ TODO 'View page formatting code'
define('EDIT_COMMENT_TIMESTAMP_CAPTION', '(%s)'); // %s timestamp
/**#@-*/

/**#@+
 * Language constant used by the {@link grabcode.php grabcode} (page) handler
 */
// grabcode
define('ERROR_NO_CODE', 'Es gibt leider keinen Code auf dieser Seite der heruntergeladen werden könnte.');
/**#@-*/

/**#@+
 * Language constant used by the {@link history.php history} (page) handler
 */
// history
define('EDITED_ON', 'Bearbeitet am %1$s von %2$s'); // %1$s - time; %2$s - user name
define('HISTORY_PAGE_VIEW', 'Geschichte der letzten Änderungen von %s'); // %s pagename
define('OLDEST_VERSION_EDITED_ON_BY', 'Die älteste bekannte Version dieser Seite wurde von %2$s am %1$s erstellt.'); // %1$s - time; %2$s - user name
define('MOST_RECENT_EDIT', 'Letzte Änderung am %1$s durch %2$s'); // %1$s - time; %2$s - user name
define('HISTORY_MORE_LINK_DESC', 'hier'); // used for alternative history link in HISTORY_MORE
define('HISTORY_MORE', 'Full history for this page cannot be displayed within a single page, click %s to view more.'); // %s alternative history link # @@@ TODO avoid using 'here' ^
/**#@-*/

/**#@+
 * Language constant shared by the {@link processcomment.php processcomment} and {@link show.php show} (page) handlers
 */
// processcomment & show
// - comment buttons
define('COMMENT_DELETE_BUTTON', 'Löschen');
define('COMMENT_REPLY_BUTTON', 'Antworten');
define('COMMENT_ADD_BUTTON', 'Kommentar hinzufügen');
define('COMMENT_NEW_BUTTON', 'Kommentar verfassen');
/**#@-*/

/**#@+
 * Language constant used by the {@link processcomment.php processcomment} (page) handler
 */
// processcomment
define('ERROR_NO_COMMENT_DEL_ACCESS', 'Sie sind nicht berechtigt, diesen Kommentar zu löschen!');
define('ERROR_NO_COMMENT_WRITE_ACCESS', 'Sie sind nicht berechtigt, Kommentare zu dieser Seite hinzuzufügen');
define('ERROR_EMPTY_COMMENT', 'Der Kommentar enthielt keinen Text -- er wurde nicht gespeichert!');
define('ADD_COMMENT_LABEL', 'Antwort auf %s:');
define('NEW_COMMENT_LABEL', 'Einen neuen Kommentar verfassen:');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges_simple.xml.mm.php recentchanges_simple.xml.mm} (page) handler
 */
// recentchanges_simple.xml.mm
define('FIRST_NODE_LABEL', 'Letzte Änderungen');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges.xml.php recentchanges.xml} (page) handler
 */
// recentchanges.xml
define('RECENTCHANGES_DESC', 'Letzte Änderungen an %s'); // %s - page name
/**#@-*/

/**#@+
 * Language constant shared by the {@link referrers_sites.php referrers_sites}, {@link referrers.php referrers} and {@link review_blacklist.php review_blacklist} (page) handlers
 */
// referrers_sites + referrers + review_blacklist
define('REFERRERS_PURGE_24_HOURS', 'in den letzten 24 Stunden');
define('REFERRERS_PURGE_N_DAYS', 'in den letzten %d Tagen'); // %d number of days
define('REFERRERS_NO_SPAM', 'Nachricht an Spammer: Diese Seite wird nicht von Suchmaschinen indiziert, verschwenden sie also ihre Zeit nicht.');
define('REFERRERS_DOMAINS_TO_WIKI_LINK_DESC', 'View global referring sites');
define('REFERRERS_DOMAINS_TO_PAGE_LINK_DESC', 'View referring sites for %s only'); // %s - page name
define('REFERRERS_URLS_TO_WIKI_LINK_DESC', 'View global referrers');
define('REFERRERS_URLS_TO_PAGE_LINK_DESC', 'View referrers for %s only'); // %s - page name
define('REFERRER_BLACKLIST_LINK_DESC', 'View referrer blacklist');
define('BLACKLIST_LINK_DESC', 'Blacklist');
define('NONE_CAPTION', 'keine');
define('PLEASE_LOGIN_CAPTION', 'Sie müssen eingeloggt sein, um die Liste der Referrer zu betrachten');
/**#@-*/

/**#@+
 * Language constant used by the {@link referrers_sites.php referrers_sites} (page) handler
 */
// referrers_sites
define('REFERRERS_URLS_LINK_DESC', 'vergleiche die Liste der verschiedenen URLs');
define('REFERRERS_DOMAINS_TO_WIKI', 'Domains/sites linking to this wiki (%s)'); // %s - link to referrers handler
define('REFERRERS_DOMAINS_TO_PAGE', 'Domains/sites linking to %1$s %2$s (%3$s)'); // %1$s - page link; %2$s - purge time; %3$s - link to referrers handler
/**#@-*/

/**#@+
 * Language constant used by the {@link referrers.php referrers} (page) handler
 */
// referrers
define('REFERRERS_DOMAINS_LINK_DESC', 'vergleiche die Liste der Domains');
define('REFERRERS_URLS_TO_WIKI', 'Externe Seiten die auf dieses Wiki verweisen (%s)'); // %s - link to referrers_sites handler
define('REFERRERS_URLS_TO_PAGE', 'External pages linking to %1$s %2$s (%3$s)'); // %1$s - page link; %2$s - purge time; %3$s - link to referrers_sites handler
/**#@-*/

/**#@+
 * Language constant used by the {@link review_blacklist.php review_blacklist} (page) handler
 */
// review_blacklist
define('BLACKLIST_HEADING', 'Referrer Blacklist');
define('BLACKLIST_REMOVE_LINK_DESC', 'entfernen');
define('STATUS_BLACKLIST_EMPTY', 'Die Blacklist ist leer.');
/**#@-*/

/**#@+
 * Language constant used by the {@link revisions.php revisions} (page) handler
 */
// revisions
define('REVISIONS_CAPTION', 'Versionen von %s'); // %s pagename
define('REVISIONS_NO_REVISIONS_YET', 'Es gibt noch keine Versionen für diese Seite');
define('REVISIONS_SIMPLE_DIFF', 'Simple Diff');
define('REVISIONS_MORE_CAPTION', 'There are more revisions that were not shown here, click the button labelled %s below to view these entries'); // %S - text of REVISIONS_MORE_BUTTON
define('REVISIONS_RETURN_TO_NODE_BUTTON', 'Return To Node / Cancel');
define('REVISIONS_SHOW_DIFFERENCES_BUTTON', 'Zeige Änderungen');
define('REVISIONS_MORE_BUTTON', 'Nächste...');
/**#@-*/

/**#@+
 * Language constant used by the {@link revisions.xml.php revisions.xml} (page) handler
 */
// revisions.xml
define('REVISIONS_EDITED_BY', 'Geändert durch %s'); // %s user name
define('HISTORY_REVISIONS_OF', 'Geschichte/Versionen von %s'); // %s - page name
/**#@-*/

/**#@+
 * Language constant used by the {@link show.php show} (page) handler
 */
// show
define('SHOW_RE_EDIT_BUTTON', 'Diese alte Version erneut bearbeiten');
define('SHOW_ASK_CREATE_PAGE_CAPTION', 'Diese Seite existiert noch nicht. Vielleicht wollen Sie sie %s?'); // %s - page create link
define('SHOW_OLD_REVISION_CAPTION', 'Dies ist eine alte Version von %1$s vom %2$s.'); // %1$s - page link; %2$s - timestamp
define('COMMENTS_CAPTION', 'Kommentare');
define('DISPLAY_COMMENTS_LABEL', 'Kommentare zeigen');
define('DISPLAY_COMMENT_LINK_DESC', 'Kommentare anzeigen');
define('DISPLAY_COMMENTS_EARLIEST_LINK_DESC', 'Älteste zuerst');
define('DISPLAY_COMMENTS_LATEST_LINK_DESC', 'Neue zuerst');
define('DISPLAY_COMMENTS_THREADED_LINK_DESC', 'Hierarchisch');
define('HIDE_COMMENTS_LINK_DESC', 'Kommentare verbergen');
define('STATUS_NO_COMMENTS', 'Diese Seite wurde noch nicht kommentiert.');
define('STATUS_ONE_COMMENT', 'Diese Seite wurde einmal kommentiert.');
define('STATUS_SOME_COMMENTS', 'Diese Seite wurde %d mal kommentiert.'); // %d - number of comments
define('COMMENT_TIME_CAPTION', '%s'); // %s comment time
/**#@-*/

/**#@+
 * Language constant used by the {@link showcode.php showcode} (page) handler
 */
// showcode
define('SOURCE_HEADING', 'Formatting code for %s'); // %s - page link
define('SHOW_RAW_LINK_DESC', 'show source only');
/**#@-*/

/* ------------------ LIBS ------------------*/

/**#@+
 * Language constant used by the {@link Wakka.class.php Wakka class} (the Wikka core containing most methods)
 */
// Wakka.class
define('QUERY_FAILED', 'Abfrage gescheitert:');
define('REDIR_DOCTITLE', 'Weitergeleitet zu %s'); // %s - target page
define('REDIR_LINK_DESC', 'diesem Link'); // used in REDIR_MANUAL_CAPTION
define('REDIR_MANUAL_CAPTION', 'If your browser does not redirect you, please follow %s'); // %s target page link
define('CREATE_THIS_PAGE_LINK_TITLE', 'Diese Seite erstellen');
define('ACTION_UNKNOWN_SPECCHARS', 'Unknown action; the action name must not contain special characters.');
define('ACTION_UNKNOWN', 'Unknown action "%s"'); // %s - action name
define('HANDLER_UNKNOWN_SPECCHARS', 'Unknown handler; the handler name must not contain special characters.');
define('HANDLER_UNKNOWN', 'Sorry, %s is an unknown handler.'); // %s handler name
define('FORMATTER_UNKNOWN_SPECCHARS', 'Unknown formatter; the formatter name must not contain special characters.');
define('FORMATTER_UNKNOWN', 'Formatter "%s" not found'); // %s formatter name
/**#@-*/

/* ------------------ SETUP ------------------ */
/**#@+
 * Language constant used by the {@link index.php setup} program (and several included files)
 */
// @@@ later....
/**#@-*/

?>