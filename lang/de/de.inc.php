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
if(!defined('WIKKA_ERROR_SETUP_FILE_MISSING')) define('WIKKA_ERROR_SETUP_FILE_MISSING', 'Eine für die Installation / das Upgrade notwendige Datei wurde nicht gefunden. Bitte installieren Sie Wikka erneut!');
if(!defined('WIKKA_ERROR_MYSQL_ERROR')) define('WIKKA_ERROR_MYSQL_ERROR', 'MySQL Fehler: %d - %s');	// %d - error number; %s - error text
if(!defined('WIKKA_ERROR_CAPTION')) define('WIKKA_ERROR_CAPTION', 'Fehler');
if(!defined('WIKKA_ERROR_ACL_READ')) define('WIKKA_ERROR_ACL_READ', 'Sie dürfen diese Seite nicht lesen.');
if(!defined('WIKKA_ERROR_ACL_READ_SOURCE')) define('WIKKA_ERROR_ACL_READ_SOURCE', 'Sie dürfen den Quellcode dieser Seite nicht betrachten.');
if(!defined('WIKKA_ERROR_ACL_READ_INFO')) define('WIKKA_ERROR_ACL_READ_INFO', 'Sie dürfen auf diese Information nicht zugreifen.');
if(!defined('WIKKA_ERROR_LABEL')) define('WIKKA_ERROR_LABEL', 'Fehler');
if(!defined('WIKKA_ERROR_PAGE_NOT_EXIST')) define('WIKKA_ERROR_PAGE_NOT_EXIST', 'Die Seite %s gibt es leider nicht.'); // %s (source) page name
if(!defined('WIKKA_ERROR_EMPTY_USERNAME')) define('WIKKA_ERROR_EMPTY_USERNAME', 'Bitte wählen Sie einen Benutzernamen!');
if(!defined('WIKKA_ERROR_INVALID_PAGE_NAME')) define('WIKKA_ERROR_INVALID_PAGE_NAME', '%s ist kein gültiger Seitenname. Gültige Namen müssen mit einem Großbuchstaben beginnen, dürfen nur Buchstaben und Nummern enthalten und müssen CamelCase formatiert sein.'); // %s - page name
if(!defined('WIKKA_ERROR_PAGE_ALREADY_EXIST')) define('WIKKA_ERROR_PAGE_ALREADY_EXIST', 'Die Zielseite existiert bereits');
if(!defined('WIKKA_LOGIN_LINK_DESC')) define('WIKKA_LOGIN_LINK_DESC', 'Login');
if(!defined('WIKKA_MAINPAGE_LINK_DESC')) define('WIKKA_MAINPAGE_LINK_DESC', 'Hauptseite');
if(!defined('WIKKA_NO_OWNER')) define('WIKKA_NO_OWNER', 'Niemand');
if(!defined('WIKKA_NOT_AVAILABLE')) define('WIKKA_NOT_AVAILABLE', 'n/a');
if(!defined('WIKKA_NOT_INSTALLED')) define('WIKKA_NOT_INSTALLED', 'nicht installiert');
if(!defined('WIKKA_ANONYMOUS_USER')) define('WIKKA_ANONYMOUS_USER', 'Anonymus'); // 'name' of non-registered user
if(!defined('WIKKA_UNREGISTERED_USER')) define('WIKKA_UNREGISTERED_USER', 'unregistrierter Benutzer'); // alternative for 'anonymous' @@@ make one string only?
if(!defined('WIKKA_ANONYMOUS_AUTHOR_CAPTION')) define('WIKKA_ANONYMOUS_AUTHOR_CAPTION', '('.WIKKA_UNREGISTERED_USER.')'); // @@@ or WIKKA_ANONYMOUS_USER
if(!defined('WIKKA_SAMPLE_WIKINAME')) define('WIKKA_SAMPLE_WIKINAME', 'MaxMustermann'); // must be a CamelCase name
if(!defined('WIKKA_HISTORY')) define('WIKKA_HISTORY', 'Geschichte');
if(!defined('WIKKA_REVISIONS')) define('WIKKA_REVISIONS', 'Versionen');
if(!defined('WIKKA_REV_WHEN_BY_WHO')) define('WIKKA_REV_WHEN_BY_WHO', '%1$s von %2$s'); // %1$s - timestamp; %2$s - user name
if(!defined('WIKKA_NO_PAGES_FOUND')) define('WIKKA_NO_PAGES_FOUND', 'Keine Seiten gefunden.');
if(!defined('WIKKA_PAGE_OWNER')) define('WIKKA_PAGE_OWNER', 'Besitzer: %s'); // %s - page owner name or link
if(!defined('WIKKA_COMMENT_AUTHOR_DIVIDER')) define('WIKKA_COMMENT_AUTHOR_DIVIDER', ', kommentiert von '); //TODo check if we can construct a single phrase here
if(!defined('WIKKA_PAGE_EDIT_LINK_DESC')) define('WIKKA_PAGE_EDIT_LINK_DESC', 'Bearbeiten');
if(!defined('WIKKA_PAGE_CREATE_LINK_DESC')) define('WIKKA_PAGE_CREATE_LINK_DESC', 'Erstellen');
if(!defined('WIKKA_PAGE_EDIT_LINK_TITLE')) define('WIKKA_PAGE_EDIT_LINK_TITLE', 'Hier klicken, um %s zu bearbeiten'); // %s page name @@@ 'Edit %s'
if(!defined('WIKKA_BACKLINKS_LINK_TITLE')) define('WIKKA_BACKLINKS_LINK_TITLE', 'Zeigt eine Liste der Seiten an, die auf %s linken.'); // %s page name
if(!defined('WIKKA_JRE_LINK_DESC')) define('WIKKA_JRE_LINK_DESC', 'Java Runtime Environment');
if(!defined('WIKKA_NOTE')) define('WIKKA_NOTE', 'NOTE:');
if(!defined('WIKKA_JAVA_PLUGIN_NEEDED')) define('WIKKA_JAVA_PLUGIN_NEEDED', 'Das Java 1.4.1 Plug-in (oder eine neuere Version) ist notwendig, um dieses Applett zu starten,');
/**#@-*/


/*  ------------------ CORE ------------------  */

/**#@+
 * Language constant for the core {@link wikka.php wikka} program
 */
// wikka
if(!defined('ERROR_NO_DB_ACCESS')) define('ERROR_NO_DB_ACCESS', 'Fehler: Es konnte keine Verbindung zur Datenbank hergestellt werden.');
if(!defined('ERROR_RETRIEVAL_MYSQL_VERSION')) define('ERROR_RETRIEVAL_MYSQL_VERSION', 'Konnte MySQL-Version nicht ermitteln');
if(!defined('ERROR_WRONG_MYSQL_VERSION')) define('ERROR_WRONG_MYSQL_VERSION', 'Wikka setzt MySQL %s oder besser vorraus!');	// %s - version number
if(!defined('STATUS_WIKI_UPGRADE_NOTICE')) define('STATUS_WIKI_UPGRADE_NOTICE', 'Diese Seite wird gerade upgedated. Bitte versuchen Sie es später noch einmal.');
if(!defined('STATUS_WIKI_UNAVAILABLE')) define('STATUS_WIKI_UNAVAILABLE', 'Das Wiki ist zur Zeit nicht erreichbar.');
if(!defined('PAGE_GENERATION_TIME')) define('PAGE_GENERATION_TIME', 'Die Seite wurde in %.4f Sekunden erstellt.'); // %.4f - page generation time
if(!defined('ERROR_HEADER_MISSING')) define('ERROR_HEADER_MISSING', 'Es wurde kein header-Template gefunden. Bitte stellen Sie sicher, dass sich eine Datei namens <code>header.php</code> im templates Verzeichnis befindet.'); //TODO Make sure this message matches any filename/folder change
if(!defined('ERROR_FOOTER_MISSING')) define('ERROR_FOOTER_MISSING', 'Es wurde kein footer-Template gefunden. Bitte stellen Sie sicher, dass sich eine Datei namens <code>footer.php</code> im templates Verzeichnis befindet.'); //TODO Make sure this message matches any filename/folder change

#if(!defined('ERROR_SETUP_HEADER_MISSING')) define('ERROR_SETUP_HEADER_MISSING', 'The file "setup/header.php" was not found. Please install Wikka again!');
#if(!defined('ERROR_SETUP_FOOTER_MISSING')) define('ERROR_SETUP_FOOTER_MISSING', 'The file "setup/footer.php" was not found. Please install Wikka again!');
/**#@-*/

/*  ------------------ TEMPLATE ------------------  */

/**#@+
 * Language constant used by the {@link header.php header} template
 */
// header
if(!defined('GENERIC_DOCTITLE')) define('GENERIC_DOCTITLE', '%1$s: %2$s');	// %1$s - wiki name; %2$s - page title
if(!defined('RSS_REVISIONS_TITLE')) define('RSS_REVISIONS_TITLE', '%1$s: Versionen von %2$s');	// %1$s - wiki name; %2$s - current page name
if(!defined('RSS_RECENTCHANGES_TITLE')) define('RSS_RECENTCHANGES_TITLE', '%s: zuletzt geänderte Seiten');	// %s - wiki name
if(!defined('YOU_ARE')) define('YOU_ARE', 'Sie sind %s'); // %s - name / ip of the user.
/**#@-*/

/**#@+
 * Language constant used by the {@link footer.php footer} template
 */
// footer
if(!defined('FOOTER_PAGE_EDIT_LINK_DESC')) define('FOOTER_PAGE_EDIT_LINK_DESC', 'Seite bearbeiten');
if(!defined('PAGE_HISTORY_LINK_TITLE')) define('PAGE_HISTORY_LINK_TITLE', 'Hier klicken, um die letzten Änderungen an dieser Seite zu sehen'); // @@@ TODO 'View recent edits to this page'
if(!defined('PAGE_HISTORY_LINK_DESC')) define('PAGE_HISTORY_LINK_DESC', 'Seitengeschichte');
if(!defined('PAGE_REVISION_LINK_TITLE')) define('PAGE_REVISION_LINK_TITLE', 'Hier klicken um die Liste der letzten Versionen für diese Seite zu sehenC'); // @@@ TODO 'View recent revisions list for this page'
if(!defined('PAGE_REVISION_XML_LINK_TITLE')) define('PAGE_REVISION_XML_LINK_TITLE', 'Hier klicken um die Liste der letzten Versionen für diese Seite zu sehen'); // @@@ TODO 'View recent revisions list for this page'
if(!defined('PAGE_ACLS_EDIT_LINK_DESC')) define('PAGE_ACLS_EDIT_LINK_DESC', 'Rechte bearbeiten');
if(!defined('PAGE_ACLS_EDIT_ADMIN_LINK_DESC')) define('PAGE_ACLS_EDIT_ADMIN_LINK_DESC', '('.PAGE_ACLS_EDIT_LINK_DESC.')');
if(!defined('PUBLIC_PAGE')) define('PUBLIC_PAGE', 'Öffentliche Seite');
if(!defined('USER_IS_OWNER')) define('USER_IS_OWNER', 'Diese Seite gehört Ihnen.');
if(!defined('TAKE_OWNERSHIP')) define('TAKE_OWNERSHIP', 'Seite in Besitz nehmen');
if(!defined('REFERRERS_LINK_TITLE')) define('REFERRERS_LINK_TITLE', 'Hier klicken, um eine Liste der Referrer für diese Seite zu sehen'); // @@@ TODO 'View a list of URLs referring to this page'
if(!defined('REFERRERS_LINK_DESC')) define('REFERRERS_LINK_DESC', 'Referrer');
if(!defined('QUERY_LOG')) define('QUERY_LOG', 'Query-Log:');
if(!defined('SEARCH_LABEL')) define('SEARCH_LABEL', 'Suche:');
/**#@-*/


/*  ------------------ ACTIONS  ------------------  */

/**#@+
 * Language constant used by the {@link calendar.php calendar} action
 */
// calendar
if(!defined('FMT_SUMMARY')) define('FMT_SUMMARY', 'Kalender für %s');	// %s - ???@@@
if(!defined('TODAY')) define('TODAY', 'heute');
/**#@-*/

/**#@+
 * Language constant used by the {@link category.php category} action
 */
// category
if(!defined('ERROR_NO_PAGES')) define('ERROR_NO_PAGES', 'Leider keine Treffer für %s');	// %s - ???@@@
if(!defined('PAGES_BELONGING_TO')) define('PAGES_BELONGING_TO', 'Die folgenden %1$d Seite(n) gehört/gehören zu %2$s'); // %1$d number found; %2$s category
/**#@-*/

/**#@+
 * Language constant used by the {@link color.php color} action
 */
// color
if(!defined('ERROR_NO_TEXT_GIVEN')) define('ERROR_NO_TEXT_GIVEN', 'Es wurde kein Text eingegeben!');
if(!defined('ERROR_NO_COLOR_SPECIFIED')) define('ERROR_NO_COLOR_SPECIFIED', 'Es wurde keine Farbe gewählt!');
/**#@-*/

/**#@+
 * Language constant used by the {@link contact.php contact} action
 */
// contact
if(!defined('SEND_FEEDBACK_LINK_TITLE')) define('SEND_FEEDBACK_LINK_TITLE', 'Senden Sie uns Feedback');
if(!defined('SEND_FEEDBACK_LINK_TEXT')) define('SEND_FEEDBACK_LINK_TEXT', 'Kontakt');
/**#@-*/

/**#@+
 * Language constant used by the {@link countowned.php countowned} action
 */
// countowned
if(!defined('DISPLAY_MYPAGES_LINK_TITLE')) define('DISPLAY_MYPAGES_LINK_TITLE', 'Zeigt eine Liste der Seiten an, die Ihnen gehören');
/**#@-*/

/**#@+
 * Language constant used by the {@link countpages.php countpages} action
 */
// countpages
if(!defined('INDEX_LINK_TITLE')) define('INDEX_LINK_TITLE', 'Zeigt einen alphabetischen Indx der Seiten');
/**#@-*/

/**#@+
 * Language constant used by the {@link dbinfo.php dbinfo} action
 */
// dbinfo
if(!defined('HD_DBINFO')) define('HD_DBINFO','Datenbank-Information');
if(!defined('HD_DBINFO_DB')) define('HD_DBINFO_DB','Datenbank');
if(!defined('HD_DBINFO_TABLES')) define('HD_DBINFO_TABLES','Tabellen');
if(!defined('HD_DB_CREATE_DDL')) define('HD_DB_CREATE_DDL','DDL to create database %s:');				# %s will hold database name
if(!defined('HD_TABLE_CREATE_DDL')) define('HD_TABLE_CREATE_DDL','DDL to create table %s:');				# %s will hold table name
if(!defined('TXT_INFO_1')) define('TXT_INFO_1','This utility provides some information about the database(s) and tables in your system.');
if(!defined('TXT_INFO_2')) define('TXT_INFO_2',' Depending on permissions for the Wikka database user, not all databases or tables may be visible.');
if(!defined('TXT_INFO_3')) define('TXT_INFO_3',' Where creation DDL is given, this reflects everything that would be needed to exactly recreate the same database and table definitions,');
if(!defined('TXT_INFO_4')) define('TXT_INFO_4',' including defaults that may not have been specified explicitly.');
if(!defined('FORM_SELDB_LEGEND')) define('FORM_SELDB_LEGEND','Datenbanken');
if(!defined('FORM_SELTABLE_LEGEND')) define('FORM_SELTABLE_LEGEND','Tabellen');
if(!defined('FORM_SELDB_OPT_LABEL')) define('FORM_SELDB_OPT_LABEL','Wählen Sie eine Datenbank:');
if(!defined('FORM_SELTABLE_OPT_LABEL')) define('FORM_SELTABLE_OPT_LABEL','Wählen Sie eine Tabelle:');
if(!defined('FORM_SUBMIT_SELDB')) define('FORM_SUBMIT_SELDB','Auswählen');
if(!defined('FORM_SUBMIT_SELTABLE')) define('FORM_SUBMIT_SELTABLE','Auswählen');
if(!defined('MSG_ONLY_ADMIN')) define('MSG_ONLY_ADMIN','Sorry, only administrators can view database information.');
if(!defined('MSG_SINGLE_DB')) define('MSG_SINGLE_DB','Information for the <tt>%s</tt> database.');			# %s will hold database name
if(!defined('MSG_NO_TABLES')) define('MSG_NO_TABLES','No tables found in the <tt>%s</tt> database. Your MySQL user may not have sufficient privileges to access this database.');		# %s will hold database name
if(!defined('MSG_NO_DB_DDL')) define('MSG_NO_DB_DDL','Creation DDL for <tt>%s</tt> could not be retrieved.');	# %s will hold database name
if(!defined('MSG_NO_TABLE_DDL')) define('MSG_NO_TABLE_DDL','Creation DDL for <tt>%s</tt> could not be retrieved.');# %s will hold table name
/**#@-*/

/**#@+
 * Language constant used by the {@link emailpassword.php emailpassword} action
 */
// emailpassword
if(!defined('PW_FORGOTTEN_HEADING')) define('PW_FORGOTTEN_HEADING', 'Passworterinnerung');
if(!defined('PW_CHK_SENT')) define('PW_CHK_SENT', 'Ein temporäres Passwort wurde an die von %s\'s angegebene Emailaddresse verschickt.'); // %s - username
if(!defined('PW_FORGOTTEN_MAIL')) define('PW_FORGOTTEN_MAIL', 'Hallo, %1$s\n\n\nSomeone requested that we send to this email address a password reminder to login at %2$s. If you did not request this reminder, disregard this email. -- No action is necessary. -- Your password will stay the same.\n\nYour wikiname: %1$s \nPassword reminder: %3$s \nURL: %4$s \n\nDo not forget to change the password immediately after logging in.'); // %1$s - username; %2$s - wiki name; %3$s - md5 sum of pw; %4$s - login url of the wiki
if(!defined('PW_FORGOTTEN_MAIL_REF')) define('PW_FORGOTTEN_MAIL_REF', 'Temporäres Passwort für %s'); // %s - wiki name
if(!defined('PW_FORM_TEXT')) define('PW_FORM_TEXT', 'Geben Sie Ihren WikiNamen ein um ein temporäres Paswort an Ihre registrierte Emailadresse geschickt zu bekommen.');
if(!defined('PW_FORM_FIELDSET_LEGEND')) define('PW_FORM_FIELDSET_LEGEND', 'Ihr WikiName:');
if(!defined('ERROR_UNKNOWN_USER')) define('ERROR_UNKNOWN_USER', 'Der angegebene Benutzername existiert nicht!');
#if(!defined('ERROR_MAIL_NOT_SENT')) define('ERROR_MAIL_NOT_SENT', 'An error occurred while trying to send the password. Outgoing mail might be disabled. Please contact your server administrator.');
if(!defined('ERROR_MAIL_NOT_SENT')) define('ERROR_MAIL_NOT_SENT', 'Bei dem Versuch, das Passwort zu versenden ist ein Fehler aufgetreten. Möglicherweise ist das Versenden von Mails deaktiviert. Versuchen Sie, dem Administrator des Wikis zu erreichen, beispielsweise über einen Kommentar auf einer Seite.');
if(!defined('BUTTON_SEND_PW')) define('BUTTON_SEND_PW', 'Temporäres Passwort senden');
if(!defined('USERSETTINGS_REF')) define('USERSETTINGS_REF', 'Zurück zu %s.'); // %s - UserSettings link
/**#@-*/

/**#@+
 * Language constant used by the {@link feedback.php feedback} action
 */
// feedback
if(!defined('ERROR_EMPTY_NAME')) define('ERROR_EMPTY_NAME', 'Bitte geben Sie ihren Namen an');
if(!defined('ERROR_INVALID_EMAIL')) define('ERROR_INVALID_EMAIL', 'Bitte geben Sie eine gültige Emailadresse an');
if(!defined('ERROR_EMPTY_MESSAGE')) define('ERROR_EMPTY_MESSAGE', 'Sie haben keinen Text eingegeben');
if(!defined('ERROR_FEEDBACK_MAIL_NOT_SENT')) define('ERROR_FEEDBACK_MAIL_NOT_SENT', 'Bei dem Versuch, die Email zu versenden ist ein Fehler aufgetreten. Möglicherweise ist das Versenden von Mails deaktiviert. Versuchen Sie, %s auf anderem Wege zu erreichen, beispielsweise über einen Kommentar auf einer Seite.'); // %s - name of the recipient
if(!defined('FEEDBACK_FORM_LEGEND')) define('FEEDBACK_FORM_LEGEND', '%s kontaktieren'); //%s - wikiname of the recipient
if(!defined('FEEDBACK_NAME_LABEL')) define('FEEDBACK_NAME_LABEL', 'Ihr Name:');
if(!defined('FEEDBACK_EMAIL_LABEL')) define('FEEDBACK_EMAIL_LABEL', 'Ihre Email:');
if(!defined('FEEDBACK_MESSAGE_LABEL')) define('FEEDBACK_MESSAGE_LABEL', 'Ihre Nachricht:');
if(!defined('FEEDBACK_SEND_BUTTON')) define('FEEDBACK_SEND_BUTTON', 'Senden');
if(!defined('FEEDBACK_SUBJECT')) define('FEEDBACK_SUBJECT', 'Feedback von %s'); // %s - name of the wiki
if(!defined('SUCCESS_FEEDBACK_SENT')) define('SUCCESS_FEEDBACK_SENT', 'Vielen Dank für Ihr Feedback, %s! Ihre Nachricht wurde verschickt.'); //%s - name of the sender
/**#@-*/

/**#@+
 * Language constant used by the {@link files.php files} action
 */
// files
if(!defined('ERROR_UPLOAD_DIRECTORY_NOT_WRITABLE')) define('ERROR_UPLOAD_DIRECTORY_NOT_WRITABLE', 'Bitte stellen Sie sicher dass der Server Schreibrechte für das Verzeichnis %s besitzt.'); // %s Upload folder ref #89
if(!defined('ERROR_UPLOAD_DIRECTORY_NOT_READABLE')) define('ERROR_UPLOAD_DIRECTORY_NOT_READABLE', 'Bitte stellen Sie sicher dass der Server Leserechte für das Verzeichnis %s besitzt.'); // %s Upload folder ref #89
if(!defined('ERROR_NONEXISTENT_FILE')) define('ERROR_NONEXISTENT_FILE', 'Eine Datei mit dem Namen %s existiert nicht.'); // %s - file name ref
if(!defined('ERROR_FILE_UPLOAD_INCOMPLETE')) define('ERROR_FILE_UPLOAD_INCOMPLETE', 'Die Datei wurde leider nur unvollständig hochgeladen! Bitte versuchen Sie es erneut.');
if(!defined('ERROR_UPLOADING_FILE')) define('ERROR_UPLOADING_FILE', 'Beim Hochladen der Datei ist ein Fehler aufgetreten');
if(!defined('ERROR_FILE_ALREADY_EXISTS')) define('ERROR_FILE_ALREADY_EXISTS', 'Eine Datei namens %s ist bereits vorhanden.'); // %s - file name ref
if(!defined('ERROR_EXTENSION_NOT_ALLOWED')) define('ERROR_EXTENSION_NOT_ALLOWED', 'Dateien mit dieser Endung sind leider nicht erlaubt.');
if(!defined('ERROR_FILE_TOO_BIG')) define('ERROR_FILE_TOO_BIG', 'Die gewählte Datei ist zu groß. Maximale Größe für Dateien: %s.'); // %s - allowed filesize
if(!defined('ERROR_NO_FILE_SELECTED')) define('ERROR_NO_FILE_SELECTED', 'Keine Datei ausgewählt.');
if(!defined('ERROR_FILE_UPLOAD_IMPOSSIBLE')) define('ERROR_FILE_UPLOAD_IMPOSSIBLE', 'Auf Grund einer fehlerhaften Serverkonfiguration ist das Hochladen von Dateien nicht möglich.');
if(!defined('SUCCESS_FILE_UPLOADED')) define('SUCCESS_FILE_UPLOADED', 'Die Datei wurde erfolgreich hochgeladen.');
if(!defined('FILE_TABLE_CAPTION')) define('FILE_TABLE_CAPTION', 'Dateien');
if(!defined('FILE_TABLE_HEADER_NAME')) define('FILE_TABLE_HEADER_NAME', 'Datei');
if(!defined('FILE_TABLE_HEADER_SIZE')) define('FILE_TABLE_HEADER_SIZE', 'Größe');
if(!defined('FILE_TABLE_HEADER_DATE')) define('FILE_TABLE_HEADER_DATE', 'Zuletzt geändert am');
if(!defined('FILE_UPLOAD_FORM_LEGEND')) define('FILE_UPLOAD_FORM_LEGEND', 'Neue Datei hochladen:');
if(!defined('FILE_UPLOAD_FORM_LABEL')) define('FILE_UPLOAD_FORM_LABEL', 'Datei:');
if(!defined('FILE_UPLOAD_FORM_BUTTON')) define('FILE_UPLOAD_FORM_BUTTON', 'hochladen');
if(!defined('DOWNLOAD_LINK_TITLE')) define('DOWNLOAD_LINK_TITLE', '%s herunterladen'); // %s - file name
if(!defined('DELETE_LINK_TITLE')) define('DELETE_LINK_TITLE', '%s löschen'); // %s - file name
if(!defined('NO_ATTACHMENTS')) define('NO_ATTACHMENTS', 'Auf dieser Seite gibt es keine Dateien.');
/**#@-*/

/**#@+
 * Language constant used by the {@link googleform.php googleform} action
 */
// googleform
if(!defined('GOOGLE_BUTTON')) define('GOOGLE_BUTTON', 'Google');
/**#@-*/

/**#@+
 * Language constant used by the {@link include.php include} action
 */
// include
if(!defined('ERROR_CIRCULAR_REFERENCE')) define('ERROR_CIRCULAR_REFERENCE', 'Zirkulare Referenz entdeckt!');
/**#@-*/

/**#@+
 * Language constant used by the {@link lastedit.php lastedit} action
 */
// lastedit
if(!defined('LASTEDIT_DESC')) define('LASTEDIT_DESC', 'Zuletzt geändert von %s'); // %s user name
if(!defined('LASTEDIT_DIFF_LINK_TITLE')) define('LASTEDIT_DIFF_LINK_TITLE', 'Unterschiede zur letzten Version zeigen');
/**#@-*/

/**#@+
 * Language constant used by the {@link lastusers.php lastusers} action
 */
// lastusers
if(!defined('LASTUSERS_CAPTION')) define('LASTUSERS_CAPTION', 'Zuletzt registrierte Benutzer');
if(!defined('SIGNUP_DATE_TIME')) define('SIGNUP_DATE_TIME', 'Registrierungdatum/-zeit');
if(!defined('NAME_TH')) define('NAME_TH', 'Benutzername');
if(!defined('OWNED_PAGES_TH')) define('OWNED_PAGES_TH', 'Anzahl an Seiten');
if(!defined('SIGNUP_DATE_TIME_TH')) define('SIGNUP_DATE_TIME_TH', 'Registrierungsdatum/-zeit');
/**#@-*/

/**#@+
 * Language constant used by the {@link mindmap.php mindmap} action
 */
// mindmap
if(!defined('MM_JRE_INSTALL_REQ')) define('MM_JRE_INSTALL_REQ', 'Bitte installieren sie %s auf ihrem Computer.'); // %s - JRE install link
if(!defined('MM_DOWNLOAD_LINK_DESC')) define('MM_DOWNLOAD_LINK_DESC', 'Diese Mindmap herunterladen');
if(!defined('MM_EDIT')) define('MM_EDIT', 'Benutzen Sie %s zum Editieren'); // %s - link to freemind project
if(!defined('MM_FULLSCREEN_LINK_DESC')) define('MM_FULLSCREEN_LINK_DESC', 'Als Vollbild öffnen');
if(!defined('ERROR_INVALID_MM_SYNTAX')) define('ERROR_INVALID_MM_SYNTAX', 'Fehler: falsche MindMap-Action Syntax.');
if(!defined('PROPER_USAGE_MM_SYNTAX')) define('PROPER_USAGE_MM_SYNTAX', 'Richtige Syntax: %1$s oder %2$s'); // %1$s - syntax sample 1; %2$s - syntax sample 2
/**#@-*/

/**#@+
 * Language constant used by the {@link mychanges.php mychanges} action
 */
// mychanges
if(!defined('NO_PAGES_EDITED')) define('NO_PAGES_EDITED', 'Sie haben noch keine Seiten geändert.');
if(!defined('MYCHANGES_ALPHA_LIST')) define('MYCHANGES_ALPHA_LIST', 'Dies ist die Liste der Seiten die Sie geändert haben zusammen mit dem Datum der letzten Änderung.');
if(!defined('MYCHANGES_DATE_LIST')) define('MYCHANGES_DATE_LIST', 'Dies ist die Liste der Seiten die Sie geändert haben, geordnet nach dem Datum der letzten Änderung.');
if(!defined('ORDER_DATE_LINK_DESC')) define('ORDER_DATE_LINK_DESC', 'nach Datum sortieren');
if(!defined('ORDER_ALPHA_LINK_DESC')) define('ORDER_ALPHA_LINK_DESC', 'alphabetisch sortieren');
if(!defined('MYCHANGES_NOT_LOGGED_IN')) define('MYCHANGES_NOT_LOGGED_IN', 'Sie sind nicht angemeldet, daher konnte die Liste mit den von Ihnen geänderten Seiten nicht erstellt werden.');
/**#@-*/

/**#@+
 * Language constant used by the {@link mypages.php mypages} action
 */
// mypages
if(!defined('OWNED_PAGES_TXT')) define('OWNED_PAGES_TXT', 'Dies ist die Liste der Seiten, die Ihnen gehören.');
if(!defined('OWNED_NO_PAGES')) define('OWNED_NO_PAGES', 'Ihnen gehören keine Seiten.');
if(!defined('OWNED_NOT_LOGGED_IN')) define('OWNED_NOT_LOGGED_IN', 'Sie sind nicht eingeloggt, daher konnte die Liste Ihrer Seiten nicht erstellt werden.');
/**#@-*/

/**#@+
 * Language constant used by the {@link newpage.php newpage} action
 */
// newpage
if(!defined('NEWPAGE_CREATE_LEGEND')) define('NEWPAGE_CREATE_LEGEND', 'Neue Seite erstellen');
if(!defined('NEWPAGE_CREATE_BUTTON')) define('NEWPAGE_CREATE_BUTTON', 'Erstellen');
/**#@-*/

/**#@+
 * Language constant used by the {@link orphanedpages.php orphanedpages} action
 */
// orphanedpages
if(!defined('NO_ORPHANED_PAGES')) define('NO_ORPHANED_PAGES', 'Keine verwaisten Seiten. Gut!');

/**#@+
 * Language constant used by the {@link ownedpages.php ownedpages} action
 */
// ownedpages
if(!defined('OWNEDPAGES_COUNTS')) define('OWNEDPAGES_COUNTS', 'Ihnen gehören %1$s Seiten von insgesamt %2$s Seiten in diesem Wiki.'); // %1$s - number of pages owned; %2$s - total number of pages
if(!defined('OWNEDPAGES_PERCENTAGE')) define('OWNEDPAGES_PERCENTAGE', 'Das heißt, Ihnen gehören %s Prozent.'); // %s percentage of pages owned
/**#@-*/

/**#@+
 * Language constant used by the {@link pageindex.php pageindex} action
 */
// pageindex
if(!defined('PAGEINDEX_HEADING')) define('PAGEINDEX_HEADING', 'Seitenindex');
if(!defined('PAGEINDEX_CAPTION')) define('PAGEINDEX_CAPTION', 'Dies ist ein alphabetische Liste aller Seite die Sie in diesem Wiki lesen dürfen.');
if(!defined('PAGEINDEX_OWNED_PAGES_CAPTION')) define('PAGEINDEX_OWNED_PAGES_CAPTION', 'Seiten mit einem * gehören Ihnen.');
if(!defined('PAGEINDEX_ALL_PAGES')) define('PAGEINDEX_ALL_PAGES', 'Alle');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges.php recentchanges} action
 */
// recentchanges
if(!defined('RECENTCHANGES_HEADING')) define('RECENTCHANGES_HEADING', 'Zuletzt geänderte Seiten');
if(!defined('REVISIONS_LINK_TITLE')) define('REVISIONS_LINK_TITLE', 'Liste der letzten Versionen für %s ansehen'); // %s - page name
if(!defined('HISTORY_LINK_TITLE')) define('HISTORY_LINK_TITLE', 'Seitengeschichte von %s ansehen'); // %s - page name
if(!defined('WIKIPING_ENABLED')) define('WIKIPING_ENABLED', 'WikiPing aktiviert: Änderungen in diesem Wiki werden auch auf %s angezeigt.'); // %s - link to wikiping server
if(!defined('RECENTCHANGES_NONE_FOUND')) define('RECENTCHANGES_NONE_FOUND', 'Es gibt keine zuletzt geänderten Seiten.');
if(!defined('RECENTCHANGES_NONE_ACCESSIBLE')) define('RECENTCHANGES_NONE_ACCESSIBLE', 'Es gibt keine zuletzt geänderten Seiten die Sie lesen dürfen.');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentcomments.php recentcomments} action
 */
// recentcomments
if(!defined('RECENTCOMMENTS_HEADING')) define('RECENTCOMMENTS_HEADING', 'Letzte Kommentare');
if(!defined('RECENTCOMMENTS_TIMESTAMP_CAPTION')) define('RECENTCOMMENTS_TIMESTAMP_CAPTION', '%s'); // %s - timestamp
if(!defined('RECENTCOMMENTS_NONE_FOUND')) define('RECENTCOMMENTS_NONE_FOUND', 'Es gibt keine letzten Kommentare.');
if(!defined('RECENTCOMMENTS_NONE_ACCESSIBLE')) define('RECENTCOMMENTS_NONE_ACCESSIBLE', 'Es gibt keine letzten Kommentare die Sie lesen dürfen.');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentlycommented.php recentlycommented} action
 */
// recentlycommented
if(!defined('RECENTLYCOMMENTED_HEADING')) define('RECENTLYCOMMENTED_HEADING', 'Zuletzt kommentierte Seiten');
if(!defined('RECENTLYCOMMENTED_NONE_FOUND')) define('RECENTLYCOMMENTED_NONE_FOUND', 'Es gibt keine zuletzt kommentierten Seiten.');
if(!defined('RECENTLYCOMMENTED_NONE_ACCESSIBLE')) define('RECENTLYCOMMENTED_NONE_ACCESSIBLE', 'Es gibt keine zuletzt kommentierten Seiten die Sie lesen dürfen.');
/**#@-*/

/**#@+
 * Language constant used by the {@link system.php system} action
 */
// system
if(!defined('SYSTEM_HOST_CAPTION')) define('SYSTEM_HOST_CAPTION', '(%s)'); // %s - host name
if(!defined('WIKKA_STATUS_NOT_AVAILABLE')) define('WIKKA_STATUS_NOT_AVAILABLE', 'n/a');
/**#@-*/

/**#@+
 * Language constant shared by the {@link textsearch.php textsearch} and {@link textsearchexpanded.php textsearchexpanded} actions
 */
// textsearch & textsearchexpanded
if(!defined('SEARCH_FOR')) define('SEARCH_FOR', 'Suche nach');
if(!defined('SEARCH_ZERO_MATCH')) define('SEARCH_ZERO_MATCH', 'Keine Treffer');
if(!defined('SEARCH_ONE_MATCH')) define('SEARCH_ONE_MATCH', 'Ein Treffer gefunden');
if(!defined('SEARCH_N_MATCH')) define('SEARCH_N_MATCH', '%d Treffer gefunden'); // %d - number of hits
if(!defined('SEARCH_RESULTS')) define('SEARCH_RESULTS', 'Ergebnisse der Suche: <strong>%1$s</strong> für <strong>%2$s</strong>'); # %1$s: n matches for | %2$s: search term 
if(!defined('SEARCH_NOT_SURE_CHOICE')) define('SEARCH_NOT_SURE_CHOICE', 'Unsicher welche Seite es ist?');
if(!defined('SEARCH_EXPANDED_LINK_DESC')) define('SEARCH_EXPANDED_LINK_DESC', 'erweiterte Volltextsuche'); // search link description
if(!defined('SEARCH_TRY_EXPANDED')) define('SEARCH_TRY_EXPANDED', 'Probieren Sie die %s um auch die Fundstellen anzuzeigen.'); // %s expanded search link
if(!defined('SEARCH_TIPS')) define('SEARCH_TIPS', 'Suchtipps:');
if(!defined('SEARCH_WORD_1')) define('SEARCH_WORD_1', 'apple');
if(!defined('SEARCH_WORD_2')) define('SEARCH_WORD_2', 'banana');
if(!defined('SEARCH_WORD_3')) define('SEARCH_WORD_3', 'juice');
if(!defined('SEARCH_WORD_4')) define('SEARCH_WORD_4', 'macintosh');
if(!defined('SEARCH_WORD_5')) define('SEARCH_WORD_5', 'some');
if(!defined('SEARCH_WORD_6')) define('SEARCH_WORD_6', 'words');
if(!defined('SEARCH_PHRASE')) define('SEARCH_PHRASE',sprintf('%s %s',SEARCH_WORD_5,SEARCH_WORD_6));
if(!defined('SEARCH_TARGET_1')) define('SEARCH_TARGET_1', 'Seiten finden, die zumindest eines der beiden Wörter enthalten.');
if(!defined('SEARCH_TARGET_2')) define('SEARCH_TARGET_2', 'Nur Seiten finden, die beide Wörter enthalten.');
if(!defined('SEARCH_TARGET_3')) define('SEARCH_TARGET_3',sprintf("Seiten finden, die zwar '%1\$s' aber nicht '%2\$s' enthalten.",SEARCH_WORD_1,SEARCH_WORD_4));
if(!defined('SEARCH_TARGET_4')) define('SEARCH_TARGET_4',"Find pages that contain words such as 'apple', 'apples', 'applesauce', or 'applet'."); // make sure target words all *start* with SEARCH_WORD_1
if(!defined('SEARCH_TARGET_5')) define('SEARCH_TARGET_5',sprintf("Find pages that contain the exact phrase '%1\$s' (for example, pages that contain '%1\$s of wisdom' but not '%2\$s noise %3\$s').",SEARCH_PHRASE,SEARCH_WORD_5,SEARCH_WORD_6));
/**#@-*/

/**#@+
 * Language constant used by the {@link usersettings.php usersettings} action
 */
// usersettings
// - error messages
if(!defined('ERROR_EMPTY_USERNAME')) define('ERROR_EMPTY_USERNAME', 'Bitte wählen Sie einen Benutzernamen.');
if(!defined('ERROR_NONEXISTENT_USERNAME')) define('ERROR_NONEXISTENT_USERNAME', 'Dieser Benutzername existiert nicht.'); // @@@ too specific
if(!defined('ERROR_RESERVED_PAGENAME')) define('ERROR_RESERVED_PAGENAME', 'Dieser Benutzername ist schon für eine Seite reserviert. Bitte wählen Sie einen anderen Namen.');
if(!defined('ERROR_WIKINAME')) define('ERROR_WIKINAME', 'Ein Benutzername muss wie ein %1$s formatiert sein, beispielsweise %2$s.'); // %1$s - identifier WikiName; %2$s - sample WikiName
if(!defined('ERROR_EMPTY_EMAIL_ADDRESS')) define('ERROR_EMPTY_EMAIL_ADDRESS', 'Bitten geben Sie eine Emailadresse an.');
if(!defined('ERROR_INVALID_EMAIL_ADDRESS')) define('ERROR_INVALID_EMAIL_ADDRESS', 'Die Emailadresse scheint ungültig zu sein.');
if(!defined('ERROR_INVALID_PASSWORD')) define('ERROR_INVALID_PASSWORD', 'Sie haben das falsche Passwort eingegeben.');	// @@@ too specific
if(!defined('ERROR_INVALID_HASH')) define('ERROR_INVALID_HASH', 'Das temporäre Passwort stimmt leider nicht.');
if(!defined('ERROR_INVALID_OLD_PASSWORD')) define('ERROR_INVALID_OLD_PASSWORD', 'Das alte Passwort stimmt nicht.');
if(!defined('ERROR_EMPTY_PASSWORD')) define('ERROR_EMPTY_PASSWORD', 'Bitte wählen Sie ein Passwort.');
if(!defined('ERROR_EMPTY_PASSWORD_OR_HASH')) define('ERROR_EMPTY_PASSWORD_OR_HASH', 'Bitten geben Sie ihr Passwort oder ihre Passworterinnerung ein.');
if(!defined('ERROR_EMPTY_CONFIRMATION_PASSWORD')) define('ERROR_EMPTY_CONFIRMATION_PASSWORD', 'Bitte bestätigen Sie ihr Passwort, um einen neuen Account zu registrieren.');
if(!defined('ERROR_EMPTY_NEW_CONFIRMATION_PASSWORD')) define('ERROR_EMPTY_NEW_CONFIRMATION_PASSWORD', 'Please confirm your new password in order to update your account.');
if(!defined('ERROR_EMPTY_NEW_PASSWORD')) define('ERROR_EMPTY_NEW_PASSWORD', 'Dass neue Passwort darf nicht leer sein.');
if(!defined('ERROR_PASSWORD_MATCH')) define('ERROR_PASSWORD_MATCH', 'Die Passwörter stimmen nicht überein.');
if(!defined('ERROR_PASSWORD_NO_BLANK')) define('ERROR_PASSWORD_NO_BLANK', 'Leerzeichen sind in einem Passwort leider nicht erlaubt.');
if(!defined('ERROR_PASSWORD_TOO_SHORT')) define('ERROR_PASSWORD_TOO_SHORT', 'Das Passwort muss mindestens %d Zeichen lang sein.'); // %d - minimum password length
if(!defined('ERROR_INVALID_INVITATION_CODE')) define('ERROR_INVALID_INVITATION_CODE', 'This is a private wiki, only invited members can register an account! Please contact the administrator of this website for an invitation code.');
if(!defined('ERROR_INVALID_REVISION_DISPLAY_LIMIT')) define('ERROR_INVALID_REVISION_DISPLAY_LIMIT', 'The number of page revisions should not exceed %d.'); // %d - maximum revisions to view
if(!defined('ERROR_INVALID_RECENTCHANGES_DISPLAY_LIMIT')) define('ERROR_INVALID_RECENTCHANGES_DISPLAY_LIMIT', 'The number of recently changed pages should not exceed %d.'); // %d - maximum changed pages to view
// - success messages
if(!defined('SUCCESS_USER_LOGGED_OUT')) define('SUCCESS_USER_LOGGED_OUT', 'Sie haben sich erfolgreich abgemeldet.');
if(!defined('SUCCESS_USER_REGISTERED')) define('SUCCESS_USER_REGISTERED', 'Sie haben sich erfolgreich registriert!');
if(!defined('SUCCESS_USER_SETTINGS_STORED')) define('SUCCESS_USER_SETTINGS_STORED', 'Einstellungen gespeichert!');
if(!defined('SUCCESS_USER_PASSWORD_CHANGED')) define('SUCCESS_USER_PASSWORD_CHANGED', 'Passwort erfolgreich geändert!');
// - captions
if(!defined('NEW_USER_REGISTER_CAPTION')) define('NEW_USER_REGISTER_CAPTION', 'If you are signing up as a new user:');
if(!defined('REGISTERED_USER_LOGIN_CAPTION')) define('REGISTERED_USER_LOGIN_CAPTION', 'If you already have a login, sign in here:');
if(!defined('RETRIEVE_PASSWORD_CAPTION')) define('RETRIEVE_PASSWORD_CAPTION', 'Log in with your [[%s password reminder]]:'); //%s PasswordForgotten link
if(!defined('USER_LOGGED_IN_AS_CAPTION')) define('USER_LOGGED_IN_AS_CAPTION', 'Sie sind angemeldet als %s'); // %s user name
// - form legends
if(!defined('USER_ACCOUNT_LEGEND')) define('USER_ACCOUNT_LEGEND', 'Ihr Account');
if(!defined('USER_SETTINGS_LEGEND')) define('USER_SETTINGS_LEGEND', 'Einstellungen');
if(!defined('LOGIN_REGISTER_LEGEND')) define('LOGIN_REGISTER_LEGEND', 'Anmelden/Registrieren');
if(!defined('LOGIN_LEGEND')) define('LOGIN_LEGEND', 'Anmelden');
#if(!defined('REGISTER_LEGEND')) define('REGISTER_LEGEND', 'Register'); // @@@ TODO to be used later for register-action
if(!defined('CHANGE_PASSWORD_LEGEND')) define('CHANGE_PASSWORD_LEGEND', 'Ändern Sie ihr Passwort');
if(!defined('RETRIEVE_PASSWORD_LEGEND')) define('RETRIEVE_PASSWORD_LEGEND', 'Passworterinnerung');
// - form field labels (should end in ':' _unless_ it's a checkbox or radio button option)
if(!defined('USERSETTINGS_REDIRECT_AFTER_LOGIN_LABEL')) define('USERSETTINGS_REDIRECT_AFTER_LOGIN_LABEL', 'Redirect to %s after login');	// %s page to redirect to
if(!defined('USER_EMAIL_LABEL')) define('USER_EMAIL_LABEL', 'Ihre Emailadresse:');
if(!defined('DOUBLECLICK_LABEL')) define('DOUBLECLICK_LABEL', 'Editieren mit Doubleclick:');
if(!defined('SHOW_COMMENTS_LABEL')) define('SHOW_COMMENTS_LABEL', 'Kommentare standardmäßig anzeigen:');
if(!defined('COMMENT_STYLE_LABEL')) define('COMMENT_STYLE_LABEL', 'Comment style');
if(!defined('COMMENT_ASC_LABEL')) define('COMMENT_ASC_LABEL', 'Flach (älteste zuerst)');
if(!defined('COMMENT_DEC_LABEL')) define('COMMENT_DEC_LABEL', 'Flach (neue zuerst)');
if(!defined('COMMENT_THREADED_LABEL')) define('COMMENT_THREADED_LABEL', 'Hierarchisch');
if(!defined('COMMENT_DELETED_LABEL')) define('COMMENT_DELETED_LABEL', '[Kommentar gelöscht]');
if(!defined('COMMENT_BY_LABEL')) define('COMMENT_BY_LABEL', 'Kommentar von ');
if(!defined('RECENTCHANGES_DISPLAY_LIMIT_LABEL')) define('RECENTCHANGES_DISPLAY_LIMIT_LABEL', 'RecentChanges display limit:');
if(!defined('PAGEREVISION_LIST_LIMIT_LABEL')) define('PAGEREVISION_LIST_LIMIT_LABEL', 'Page revisions list limit:');
if(!defined('NEW_PASSWORD_LABEL')) define('NEW_PASSWORD_LABEL', 'Ihr neues Passwort:');
if(!defined('NEW_PASSWORD_CONFIRM_LABEL')) define('NEW_PASSWORD_CONFIRM_LABEL', 'Neues Passwort bestätigen:');
if(!defined('NO_REGISTRATION')) define('NO_REGISTRATION', 'Registration on this wiki is disabled.');
if(!defined('PASSWORD_LABEL')) define('PASSWORD_LABEL', 'Password (%s+ Zeichen):'); // %s minimum number of characters
if(!defined('CONFIRM_PASSWORD_LABEL')) define('CONFIRM_PASSWORD_LABEL', 'Passwort bestätigen:');
if(!defined('TEMP_PASSWORD_LABEL')) define('TEMP_PASSWORD_LABEL', 'temporäres Passwort:');
if(!defined('INVITATION_CODE_SHORT')) define('INVITATION_CODE_SHORT', 'Einladungscode');
if(!defined('INVITATION_CODE_LONG')) define('INVITATION_CODE_LONG', 'In order to register, you must fill in the invitation code sent by this website\'s administrator.');
if(!defined('INVITATION_CODE_LABEL')) define('INVITATION_CODE_LABEL', 'Ihr %s:'); // %s - expanded short invitation code prompt
if(!defined('WIKINAME_SHORT')) define('WIKINAME_SHORT', 'WikiName');
if(!defined('WIKINAME_LONG')) define('WIKINAME_LONG',sprintf('A WikiName is formed by two or more capitalized words without space, e.g. %s',WIKKA_SAMPLE_WIKINAME));
if(!defined('WIKINAME_LABEL')) define('WIKINAME_LABEL', 'Ihr %s:'); // %s - expanded short wiki name prompt
// - form options
if(!defined('CURRENT_PASSWORD_OPTION')) define('CURRENT_PASSWORD_OPTION', 'Ihr derzeitiges Passwort');
if(!defined('PASSWORD_REMINDER_OPTION')) define('PASSWORD_REMINDER_OPTION', 'Passworterinnerung');
// - form buttons
if(!defined('UPDATE_SETTINGS_BUTTON')) define('UPDATE_SETTINGS_BUTTON', 'Einstellungen speichern');
if(!defined('LOGIN_BUTTON')) define('LOGIN_BUTTON', 'anmelden');
if(!defined('LOGOUT_BUTTON')) define('LOGOUT_BUTTON', 'abmelden');
if(!defined('CHANGE_PASSWORD_BUTTON')) define('CHANGE_PASSWORD_BUTTON', 'Passwort ändern');
if(!defined('REGISTER_BUTTON')) define('REGISTER_BUTTON', 'Registrieren');
/**#@-*/

/**#@+
 * Language constant used by the {@link wantedpages.php wantedpages} action
 */
// wantedpages
if(!defined('SORTING_LEGEND')) define('SORTING_LEGEND', 'Sortiert nach ...');
if(!defined('SORTING_NUMBER_LABEL')) define('SORTING_NUMBER_LABEL', 'Sortierung #%d:');
if(!defined('SORTING_DESC_LABEL')) define('SORTING_DESC_LABEL', 'absteigend');
if(!defined('OK_BUTTON')) define('OK_BUTTON', 'Sortieren');
if(!defined('NO_WANTED_PAGES')) define('NO_WANTED_PAGES', 'Keine gewünschten Seiten. Gut!');
/**#@-*/


/* ------------------ 3RD PARTY ------------------ */

/**#@+
 * Language constant used by the {@link fullscreen.php fullscreen} 3rd party MindMap display utility
 */
// fullscreen
if(!defined('CLOSE_WINDOW')) define('CLOSE_WINDOW', 'Fenster schliessen');
if(!defined('MM_GET_JAVA_PLUGIN_LINK_DESC')) define('MM_GET_JAVA_PLUGIN_LINK_DESC', 'get the latest Java Plug-in here'); // used in MM_GET_JAVA_PLUGIN
if(!defined('MM_GET_JAVA_PLUGIN')) define('MM_GET_JAVA_PLUGIN', 'so if it does not work, %s.'); // %s - plugin download link
/**#@-*/


/* ------------------ FORMATTERS ------------------ */

/**#@+
 * Language constant used by the {@link wakka.php wakka} formatter
 */
// wakka
if(!defined('GRABCODE_BUTTON')) define('GRABCODE_BUTTON', 'Grab');
if(!defined('GRABCODE_BUTTON_TITLE')) define('GRABCODE_BUTTON_TITLE', '%s herunterladen'); // %s download filename
/**#@-*/


/* ------------------ HANDLERS (PAGE) ------------------ */

/**#@+
 * Language constant used by the {@link acls.php acls} (page) handler
 */
// acls
// TODO: 'translate' DB value '(Public)' when displaying it!
if(!defined('ACLS_UPDATED')) define('ACLS_UPDATED', 'Rechtelisten aktualisiert.');
if(!defined('NO_PAGE_OWNER')) define('NO_PAGE_OWNER', '(Niemand)');
if(!defined('NOT_PAGE_OWNER')) define('NOT_PAGE_OWNER', 'Sie sind nicht der Besitzer dieser Seite.');
if(!defined('PAGE_OWNERSHIP_CHANGED')) define('PAGE_OWNERSHIP_CHANGED', '%s ist nun Besitzer der Seite.'); // %s - name of new owner
if(!defined('ACLS_LEGEND')) define('ACLS_LEGEND', 'Rechtelisten für %s'); // %s - name of current page
if(!defined('ACLS_READ_LABEL')) define('ACLS_READ_LABEL', 'Leserechte:');
if(!defined('ACLS_WRITE_LABEL')) define('ACLS_WRITE_LABEL', 'Schreibrechte:');
if(!defined('ACLS_COMMENT_READ_LABEL')) define('ACLS_COMMENT_READ_LABEL', 'Kommentar-Leserechte:');
if(!defined('ACLS_COMMENT_POST_LABEL')) define('ACLS_COMMENT_POST_LABEL', 'Kommentar-Schreibrechte:');
if(!defined('SET_OWNER_LABEL')) define('SET_OWNER_LABEL', 'Besitzer der Seite bestimmen:');
if(!defined('SET_OWNER_CURRENT_OPTION')) define('SET_OWNER_CURRENT_OPTION', '(derzeitiger Besitzer)');
if(!defined('SET_OWNER_PUBLIC_OPTION')) define('SET_OWNER_PUBLIC_OPTION', '(Öffentlichkeit)'); // actual DB value will remain '(Public)' even if this option text is translated!
if(!defined('SET_NO_OWNER_OPTION')) define('SET_NO_OWNER_OPTION', '(Niemand - kein Besitzer)');
if(!defined('ACLS_STORE_BUTTON')) define('ACLS_STORE_BUTTON', 'Rechte speichern');
if(!defined('CANCEL_BUTTON')) define('CANCEL_BUTTON', 'abbrechen');
// - syntax
if(!defined('ACLS_SYNTAX_HEADING')) define('ACLS_SYNTAX_HEADING', 'Syntax:');
if(!defined('ACLS_EVERYONE')) define('ACLS_EVERYONE', 'Jeder');
if(!defined('ACLS_REGISTERED_USERS')) define('ACLS_REGISTERED_USERS', 'Registrierte Benutzer');
if(!defined('ACLS_NONE_BUT_ADMINS')) define('ACLS_NONE_BUT_ADMINS', 'Niemand (außer Admins)');
if(!defined('ACLS_ANON_ONLY')) define('ACLS_ANON_ONLY', 'nur anonyme Benutzer');
if(!defined('ACLS_LIST_USERNAMES')) define('ACLS_LIST_USERNAMES', 'der Benutzer mit dem Namen %s; Sie können so viele Benutzer hinzufügen, wie Sie wollen, einen pro Zeile'); // %s - sample user name
if(!defined('ACLS_NEGATION')) define('ACLS_NEGATION', 'Any of these items can be negated with a %s:'); // %s - 'negation' mark
if(!defined('ACLS_DENY_USER_ACCESS')) define('ACLS_DENY_USER_ACCESS', '%s das Recht verweigern'); // %s - sample user name
if(!defined('ACLS_AFTER')) define('ACLS_AFTER', 'nach');
if(!defined('ACLS_TESTING_ORDER1')) define('ACLS_TESTING_ORDER1', 'Die Rechte werden in der Reihenfolge ausgewertet, in der sie angegeben sind:');
if(!defined('ACLS_TESTING_ORDER2')) define('ACLS_TESTING_ORDER2', 'So be sure to specify %1$s on a separate line %2$s negating any users, not before.'); // %1$s - 'all' mark; %2$s - emphasised 'after'
/**#@-*/

/**#@+
 * Language constant used by the {@link backlinks.php backlinks} (page) handler
 */
// backlinks
if(!defined('BACKLINKS_HEADING')) define('BACKLINKS_HEADING', 'Seiten die auf %s verweisen');
if(!defined('BACKLINKS_NO_PAGES')) define('BACKLINKS_NO_PAGES', 'Keine Seite verweist auf diese Seite.');
/**#@-*/

/**#@+
 * Language constant used by the {@link claim.php claim} (page) handler
 */
// claim
if(!defined('USER_IS_NOW_OWNER')) define('USER_IS_NOW_OWNER', 'Sie sind jetzt Besitzer dieser Seite.');
/**#@-*/

/**#@+
 * Language constant used by the {@link clone.php clone} (page) handler
 */
// clone
if(!defined('ERROR_ACL_WRITE')) define('ERROR_ACL_WRITE', 'Sie haben leider keine Schreibrechte für %s');
if(!defined('CLONE_VALID_TARGET')) define('CLONE_VALID_TARGET', 'Bitte geben sie einen validen Namen für die Zeilseite und eine (optionale) Bearbeitungsnotiz an.');
if(!defined('CLONE_LEGEND')) define('CLONE_LEGEND', '%s klonen'); // %s source page name
if(!defined('CLONED_FROM')) define('CLONED_FROM', 'Geklont von %s'); // %s source page name
if(!defined('SUCCESS_CLONE_CREATED')) define('SUCCESS_CLONE_CREATED', '%s wurde erfolgreich erstellt!'); // %s new page name
if(!defined('CLONE_X_TO_LABEL')) define('CLONE_X_TO_LABEL', 'Klonen als:');
if(!defined('CLONE_EDIT_NOTE_LABEL')) define('CLONE_EDIT_NOTE_LABEL', 'Bearbeitungsnotiz:');
if(!defined('CLONE_EDIT_OPTION_LABEL')) define('CLONE_EDIT_OPTION_LABEL', ' Nach dem Klonen bearbeiten');
if(!defined('CLONE_ACL_OPTION_LABEL')) define('CLONE_ACL_OPTION_LABEL', ' Rechte klonen');
if(!defined('CLONE_BUTTON')) define('CLONE_BUTTON', 'Klonen');
if(!defined('ERROR_INVALID_PAGENAME')) define('ERROR_INVALID_PAGENAME', 'This page name is invalid. Valid page names must not contain the characters | ? = &lt; &gt; / \' " % or &amp;.');
/**#@-*/

/**#@+
 * Language constant used by the {@link delete.php delete} (page) handler
 */
// delete
if(!defined('ERROR_NO_PAGE_DEL_ACCESS')) define('ERROR_NO_PAGE_DEL_ACCESS', 'Sie dürfen diese Seite nicht löschen.');
if(!defined('PAGE_DELETION_HEADER')) define('PAGE_DELETION_HEADER', '%s löschen'); // %s - name of the page
if(!defined('SUCCESS_PAGE_DELETED')) define('SUCCESS_PAGE_DELETED', 'Seite wurde gelöscht!');
if(!defined('PAGE_DELETION_CAPTION')) define('PAGE_DELETION_CAPTION', 'Diese Seite vollständig löschen, inklusive aller Kommentare?');
if(!defined('PAGE_DELETION_DELETE_BUTTON')) define('PAGE_DELETION_DELETE_BUTTON', 'Seite löschen');
if(!defined('PAGE_DELETION_CANCEL_BUTTON')) define('PAGE_DELETION_CANCEL_BUTTON', 'abbrechen');
/**#@-*/

/**#@+
 * Language constant used by the {@link diff.php diff} (page) handler
 */
// diff
if(!defined('ERROR_DIFF_LIBRARY_MISSING')) define('ERROR_DIFF_LIBRARY_MISSING', 'Die notwendige Datei "'.WIKKA_LIBRARY_PATH.'/diff.lib.php" wurde nicht gefunden. Please make sure the file exists and is placed in the right directory!'); //TODO 'Please make sure' should be 'please inform WikiAdmin' - end user can't "make sure"
if(!defined('ERROR_BAD_PARAMETERS')) define('ERROR_BAD_PARAMETERS', 'There is something wrong with parameters you supplied, it\'s very likely that one of the versions you want to compare has been deleted.');
if(!defined('DIFF_ADDITIONS_HEADER')) define('DIFF_ADDITIONS_HEADER', 'Additions:');
if(!defined('DIFF_DELETIONS_HEADER')) define('DIFF_DELETIONS_HEADER', 'Deletions:');
if(!defined('DIFF_NO_DIFFERENCES')) define('DIFF_NO_DIFFERENCES', 'Keine Unterschiede');
if(!defined('DIFF_FAST_COMPARISON_HEADER')) define('DIFF_FAST_COMPARISON_HEADER', 'Vergleich von %1$s &amp; %2$s'); // %1$s - link to page A; %2$s - link to page B
if(!defined('DIFF_COMPARISON_HEADER')) define('DIFF_COMPARISON_HEADER', 'Comparing %2$s to %1$s'); // %1$s - link to page A; %2$s - link to page B (yes, they're swapped!)
if(!defined('DIFF_SAMPLE_ADDITION')) define('DIFF_SAMPLE_ADDITION', 'hinzugefügt');
if(!defined('DIFF_SAMPLE_DELETION')) define('DIFF_SAMPLE_DELETION', 'gelöscht');
if(!defined('HIGHLIGHTING_LEGEND')) define('HIGHLIGHTING_LEGEND', 'Highlighting Guide: %1$s %2$s'); // %1$s - sample added text; %2$s - sample deleted text
/**#@-*/

/**#@+
 * Language constant used by the {@link edit.php edit} (page) handler
 */
// edit
if(!defined('ERROR_OVERWRITE_ALERT1')) define('ERROR_OVERWRITE_ALERT1', 'OVERWRITE ALERT: This page was modified by someone else while you were editing it.');
if(!defined('ERROR_OVERWRITE_ALERT2')) define('ERROR_OVERWRITE_ALERT2', 'Bitte kopieren Sie ihre Änderungen und bearbeiten Sie die Seite erneut.');
if(!defined('ERROR_MISSING_EDIT_NOTE')) define('ERROR_MISSING_EDIT_NOTE', 'Fehlender Bearbeitungskommentar: Bitte geben Sie einen Bearbeitungskommentar ein!');
if(!defined('ERROR_TAG_TOO_LONG')) define('ERROR_TAG_TOO_LONG', 'Page name too long! %d characters max.'); // %d - maximum page name length
if(!defined('ERROR_NO_WRITE_ACCESS')) define('ERROR_NO_WRITE_ACCESS', 'You don\'t have write access to this page. You might need to [[UserSettings login]] or [[UserSettings register an account]] to be able to edit this page.'); //TODO Distinct links for login and register actions
if(!defined('EDIT_STORE_PAGE_LEGEND')) define('EDIT_STORE_PAGE_LEGEND', 'Seite speichern');
if(!defined('EDIT_PREVIEW_HEADER')) define('EDIT_PREVIEW_HEADER', 'Vorschau');
if(!defined('EDIT_NOTE_LABEL')) define('EDIT_NOTE_LABEL', 'Bitte geben Sie einen Kommentar zu ihrer Bearbeitung an.'); // label after field, so no colon!
if(!defined('MESSAGE_AUTO_RESIZE')) define('MESSAGE_AUTO_RESIZE', 'Clicking on %s will automatically truncate the page name to the correct size'); // %s - rename button text
if(!defined('EDIT_PREVIEW_BUTTON')) define('EDIT_PREVIEW_BUTTON', 'Vorschau');
if(!defined('EDIT_STORE_BUTTON')) define('EDIT_STORE_BUTTON', 'Speichern');
if(!defined('EDIT_REEDIT_BUTTON')) define('EDIT_REEDIT_BUTTON', 'Erneut bearbeiten');
if(!defined('EDIT_CANCEL_BUTTON')) define('EDIT_CANCEL_BUTTON', 'abbrechen');
if(!defined('EDIT_RENAME_BUTTON')) define('EDIT_RENAME_BUTTON', 'umbenennen');
if(!defined('ACCESSKEY_PREVIEW')) define('ACCESSKEY_PREVIEW', 'p'); // ideally, should match EDIT_PREVIEW_BUTTON
if(!defined('ACCESSKEY_STORE')) define('ACCESSKEY_STORE', 's'); // ideally, should match EDIT_STORE_BUTTON
if(!defined('ACCESSKEY_REEDIT')) define('ACCESSKEY_REEDIT', 'r'); // ideally, should match EDIT_REEDIT_BUTTON
if(!defined('SHOWCODE_LINK')) define('SHOWCODE_LINK', 'View formatting code for this page');
if(!defined('SHOWCODE_LINK_TITLE')) define('SHOWCODE_LINK_TITLE', 'Click to view page formatting code'); // @@@ TODO 'View page formatting code'
if(!defined('EDIT_COMMENT_TIMESTAMP_CAPTION')) define('EDIT_COMMENT_TIMESTAMP_CAPTION', '(%s)'); // %s timestamp
/**#@-*/

/**#@+
 * Language constant used by the {@link grabcode.php grabcode} (page) handler
 */
// grabcode
if(!defined('ERROR_NO_CODE')) define('ERROR_NO_CODE', 'Es gibt leider keinen Code auf dieser Seite der heruntergeladen werden könnte.');
/**#@-*/

/**#@+
 * Language constant used by the {@link history.php history} (page) handler
 */
// history
if(!defined('EDITED_ON')) define('EDITED_ON', 'Bearbeitet am %1$s von %2$s'); // %1$s - time; %2$s - user name
if(!defined('HISTORY_PAGE_VIEW')) define('HISTORY_PAGE_VIEW', 'Geschichte der letzten Änderungen von %s'); // %s pagename
if(!defined('OLDEST_VERSION_EDITED_ON_BY')) define('OLDEST_VERSION_EDITED_ON_BY', 'Die älteste bekannte Version dieser Seite wurde von %2$s am %1$s erstellt.'); // %1$s - time; %2$s - user name
if(!defined('MOST_RECENT_EDIT')) define('MOST_RECENT_EDIT', 'Letzte Änderung am %1$s durch %2$s'); // %1$s - time; %2$s - user name
if(!defined('HISTORY_MORE_LINK_DESC')) define('HISTORY_MORE_LINK_DESC', 'hier'); // used for alternative history link in HISTORY_MORE
if(!defined('HISTORY_MORE')) define('HISTORY_MORE', 'Full history for this page cannot be displayed within a single page, click %s to view more.'); // %s alternative history link # @@@ TODO avoid using 'here' ^
/**#@-*/

/**#@+
 * Language constant shared by the {@link processcomment.php processcomment} and {@link show.php show} (page) handlers
 */
// processcomment & show
// - comment buttons
if(!defined('COMMENT_DELETE_BUTTON')) define('COMMENT_DELETE_BUTTON', 'Löschen');
if(!defined('COMMENT_REPLY_BUTTON')) define('COMMENT_REPLY_BUTTON', 'Antworten');
if(!defined('COMMENT_ADD_BUTTON')) define('COMMENT_ADD_BUTTON', 'Kommentar hinzufügen');
if(!defined('COMMENT_NEW_BUTTON')) define('COMMENT_NEW_BUTTON', 'Kommentar verfassen');
/**#@-*/

/**#@+
 * Language constant used by the {@link processcomment.php processcomment} (page) handler
 */
// processcomment
if(!defined('ERROR_NO_COMMENT_DEL_ACCESS')) define('ERROR_NO_COMMENT_DEL_ACCESS', 'Sie sind nicht berechtigt, diesen Kommentar zu löschen!');
if(!defined('ERROR_NO_COMMENT_WRITE_ACCESS')) define('ERROR_NO_COMMENT_WRITE_ACCESS', 'Sie sind nicht berechtigt, Kommentare zu dieser Seite hinzuzufügen');
if(!defined('ERROR_EMPTY_COMMENT')) define('ERROR_EMPTY_COMMENT', 'Der Kommentar enthielt keinen Text -- er wurde nicht gespeichert!');
if(!defined('ADD_COMMENT_LABEL')) define('ADD_COMMENT_LABEL', 'Antwort auf %s:');
if(!defined('NEW_COMMENT_LABEL')) define('NEW_COMMENT_LABEL', 'Einen neuen Kommentar verfassen:');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges_simple.xml.mm.php recentchanges_simple.xml.mm} (page) handler
 */
// recentchanges_simple.xml.mm
if(!defined('FIRST_NODE_LABEL')) define('FIRST_NODE_LABEL', 'Letzte Änderungen');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges.xml.php recentchanges.xml} (page) handler
 */
// recentchanges.xml
if(!defined('RECENTCHANGES_DESC')) define('RECENTCHANGES_DESC', 'Letzte Änderungen an %s'); // %s - page name
/**#@-*/

/**#@+
 * Language constant shared by the {@link referrers_sites.php referrers_sites}, {@link referrers.php referrers} and {@link review_blacklist.php review_blacklist} (page) handlers
 */
// referrers_sites + referrers + review_blacklist
if(!defined('REFERRERS_PURGE_24_HOURS')) define('REFERRERS_PURGE_24_HOURS', 'in den letzten 24 Stunden');
if(!defined('REFERRERS_PURGE_N_DAYS')) define('REFERRERS_PURGE_N_DAYS', 'in den letzten %d Tagen'); // %d number of days
if(!defined('REFERRERS_NO_SPAM')) define('REFERRERS_NO_SPAM', 'Nachricht an Spammer: Diese Seite wird nicht von Suchmaschinen indiziert, verschwenden sie also ihre Zeit nicht.');
if(!defined('REFERRERS_DOMAINS_TO_WIKI_LINK_DESC')) define('REFERRERS_DOMAINS_TO_WIKI_LINK_DESC', 'View global referring sites');
if(!defined('REFERRERS_DOMAINS_TO_PAGE_LINK_DESC')) define('REFERRERS_DOMAINS_TO_PAGE_LINK_DESC', 'View referring sites for %s only'); // %s - page name
if(!defined('REFERRERS_URLS_TO_WIKI_LINK_DESC')) define('REFERRERS_URLS_TO_WIKI_LINK_DESC', 'View global referrers');
if(!defined('REFERRERS_URLS_TO_PAGE_LINK_DESC')) define('REFERRERS_URLS_TO_PAGE_LINK_DESC', 'View referrers for %s only'); // %s - page name
if(!defined('REFERRER_BLACKLIST_LINK_DESC')) define('REFERRER_BLACKLIST_LINK_DESC', 'View referrer blacklist');
if(!defined('BLACKLIST_LINK_DESC')) define('BLACKLIST_LINK_DESC', 'Blacklist');
if(!defined('NONE_CAPTION')) define('NONE_CAPTION', 'keine');
if(!defined('PLEASE_LOGIN_CAPTION')) define('PLEASE_LOGIN_CAPTION', 'Sie müssen eingeloggt sein, um die Liste der Referrer zu betrachten');
/**#@-*/

/**#@+
 * Language constant used by the {@link referrers_sites.php referrers_sites} (page) handler
 */
// referrers_sites
if(!defined('REFERRERS_URLS_LINK_DESC')) define('REFERRERS_URLS_LINK_DESC', 'vergleiche die Liste der verschiedenen URLs');
if(!defined('REFERRERS_DOMAINS_TO_WIKI')) define('REFERRERS_DOMAINS_TO_WIKI', 'Domains/sites linking to this wiki (%s)'); // %s - link to referrers handler
if(!defined('REFERRERS_DOMAINS_TO_PAGE')) define('REFERRERS_DOMAINS_TO_PAGE', 'Domains/sites linking to %1$s %2$s (%3$s)'); // %1$s - page link; %2$s - purge time; %3$s - link to referrers handler
/**#@-*/

/**#@+
 * Language constant used by the {@link referrers.php referrers} (page) handler
 */
// referrers
if(!defined('REFERRERS_DOMAINS_LINK_DESC')) define('REFERRERS_DOMAINS_LINK_DESC', 'vergleiche die Liste der Domains');
if(!defined('REFERRERS_URLS_TO_WIKI')) define('REFERRERS_URLS_TO_WIKI', 'Externe Seiten die auf dieses Wiki verweisen (%s)'); // %s - link to referrers_sites handler
if(!defined('REFERRERS_URLS_TO_PAGE')) define('REFERRERS_URLS_TO_PAGE', 'External pages linking to %1$s %2$s (%3$s)'); // %1$s - page link; %2$s - purge time; %3$s - link to referrers_sites handler
/**#@-*/

/**#@+
 * Language constant used by the {@link review_blacklist.php review_blacklist} (page) handler
 */
// review_blacklist
if(!defined('BLACKLIST_HEADING')) define('BLACKLIST_HEADING', 'Referrer Blacklist');
if(!defined('BLACKLIST_REMOVE_LINK_DESC')) define('BLACKLIST_REMOVE_LINK_DESC', 'entfernen');
if(!defined('STATUS_BLACKLIST_EMPTY')) define('STATUS_BLACKLIST_EMPTY', 'Die Blacklist ist leer.');
/**#@-*/

/**#@+
 * Language constant used by the {@link revisions.php revisions} (page) handler
 */
// revisions
if(!defined('REVISIONS_CAPTION')) define('REVISIONS_CAPTION', 'Versionen von %s'); // %s pagename
if(!defined('REVISIONS_NO_REVISIONS_YET')) define('REVISIONS_NO_REVISIONS_YET', 'Es gibt noch keine Versionen für diese Seite');
if(!defined('REVISIONS_SIMPLE_DIFF')) define('REVISIONS_SIMPLE_DIFF', 'Simple Diff');
if(!defined('REVISIONS_MORE_CAPTION')) define('REVISIONS_MORE_CAPTION', 'There are more revisions that were not shown here, click the button labelled %s below to view these entries'); // %S - text of REVISIONS_MORE_BUTTON
if(!defined('REVISIONS_RETURN_TO_NODE_BUTTON')) define('REVISIONS_RETURN_TO_NODE_BUTTON', 'Return To Node / Cancel');
if(!defined('REVISIONS_SHOW_DIFFERENCES_BUTTON')) define('REVISIONS_SHOW_DIFFERENCES_BUTTON', 'Zeige Änderungen');
if(!defined('REVISIONS_MORE_BUTTON')) define('REVISIONS_MORE_BUTTON', 'Nächste...');
/**#@-*/

/**#@+
 * Language constant used by the {@link revisions.xml.php revisions.xml} (page) handler
 */
// revisions.xml
if(!defined('REVISIONS_EDITED_BY')) define('REVISIONS_EDITED_BY', 'Geändert durch %s'); // %s user name
if(!defined('HISTORY_REVISIONS_OF')) define('HISTORY_REVISIONS_OF', 'Geschichte/Versionen von %s'); // %s - page name
/**#@-*/

/**#@+
 * Language constant used by the {@link show.php show} (page) handler
 */
// show
if(!defined('SHOW_RE_EDIT_BUTTON')) define('SHOW_RE_EDIT_BUTTON', 'Diese alte Version erneut bearbeiten');
if(!defined('SHOW_ASK_CREATE_PAGE_CAPTION')) define('SHOW_ASK_CREATE_PAGE_CAPTION', 'Diese Seite existiert noch nicht. Vielleicht wollen Sie sie %s?'); // %s - page create link
if(!defined('SHOW_OLD_REVISION_CAPTION')) define('SHOW_OLD_REVISION_CAPTION', 'Dies ist eine alte Version von %1$s vom %2$s.'); // %1$s - page link; %2$s - timestamp
if(!defined('COMMENTS_CAPTION')) define('COMMENTS_CAPTION', 'Kommentare');
if(!defined('DISPLAY_COMMENTS_LABEL')) define('DISPLAY_COMMENTS_LABEL', 'Kommentare zeigen');
if(!defined('DISPLAY_COMMENT_LINK_DESC')) define('DISPLAY_COMMENT_LINK_DESC', 'Kommentare anzeigen');
if(!defined('DISPLAY_COMMENTS_EARLIEST_LINK_DESC')) define('DISPLAY_COMMENTS_EARLIEST_LINK_DESC', 'Älteste zuerst');
if(!defined('DISPLAY_COMMENTS_LATEST_LINK_DESC')) define('DISPLAY_COMMENTS_LATEST_LINK_DESC', 'Neue zuerst');
if(!defined('DISPLAY_COMMENTS_THREADED_LINK_DESC')) define('DISPLAY_COMMENTS_THREADED_LINK_DESC', 'Hierarchisch');
if(!defined('HIDE_COMMENTS_LINK_DESC')) define('HIDE_COMMENTS_LINK_DESC', 'Kommentare verbergen');
if(!defined('STATUS_NO_COMMENTS')) define('STATUS_NO_COMMENTS', 'Diese Seite wurde noch nicht kommentiert.');
if(!defined('STATUS_ONE_COMMENT')) define('STATUS_ONE_COMMENT', 'Diese Seite wurde einmal kommentiert.');
if(!defined('STATUS_SOME_COMMENTS')) define('STATUS_SOME_COMMENTS', 'Diese Seite wurde %d mal kommentiert.'); // %d - number of comments
if(!defined('COMMENT_TIME_CAPTION')) define('COMMENT_TIME_CAPTION', '%s'); // %s comment time
/**#@-*/

/**#@+
 * Language constant used by the {@link showcode.php showcode} (page) handler
 */
// showcode
if(!defined('SOURCE_HEADING')) define('SOURCE_HEADING', 'Formatting code for %s'); // %s - page link
if(!defined('SHOW_RAW_LINK_DESC')) define('SHOW_RAW_LINK_DESC', 'show source only');
/**#@-*/

/* ------------------ LIBS ------------------*/

/**#@+
 * Language constant used by the {@link Wakka.class.php Wakka class} (the Wikka core containing most methods)
 */
// Wakka.class
if(!defined('QUERY_FAILED')) define('QUERY_FAILED', 'Abfrage gescheitert:');
if(!defined('REDIR_DOCTITLE')) define('REDIR_DOCTITLE', 'Weitergeleitet zu %s'); // %s - target page
if(!defined('REDIR_LINK_DESC')) define('REDIR_LINK_DESC', 'diesem Link'); // used in REDIR_MANUAL_CAPTION
if(!defined('REDIR_MANUAL_CAPTION')) define('REDIR_MANUAL_CAPTION', 'If your browser does not redirect you, please follow %s'); // %s target page link
if(!defined('CREATE_THIS_PAGE_LINK_TITLE')) define('CREATE_THIS_PAGE_LINK_TITLE', 'Diese Seite erstellen');
if(!defined('ACTION_UNKNOWN_SPECCHARS')) define('ACTION_UNKNOWN_SPECCHARS', 'Unknown action; the action name must not contain special characters.');
if(!defined('ACTION_UNKNOWN')) define('ACTION_UNKNOWN', 'Unknown action "%s"'); // %s - action name
if(!defined('HANDLER_UNKNOWN_SPECCHARS')) define('HANDLER_UNKNOWN_SPECCHARS', 'Unknown handler; the handler name must not contain special characters.');
if(!defined('HANDLER_UNKNOWN')) define('HANDLER_UNKNOWN', 'Sorry, %s is an unknown handler.'); // %s handler name
if(!defined('FORMATTER_UNKNOWN_SPECCHARS')) define('FORMATTER_UNKNOWN_SPECCHARS', 'Unknown formatter; the formatter name must not contain special characters.');
if(!defined('FORMATTER_UNKNOWN')) define('FORMATTER_UNKNOWN', 'Formatter "%s" not found'); // %s formatter name
/**#@-*/

/* ------------------ SETUP ------------------ */
/**#@+
 * Language constant used by the {@link index.php setup} program (and several included files)
 */
// @@@ later....
/**#@-*/

?>
