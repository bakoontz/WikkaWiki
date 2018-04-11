<?php
/**
 * Wikka language file.
 *
 * This file holds all interface language strings for Wikka.
 *
 * @package 		Language
 *
 * @version		$Id$
 * @license 		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author 		{@link http://wikkawiki.org/MichielHoltkamp Michiel Holtkamp} (Dutch translation)
 *
 * @copyright 	Copyright 2008 {@link http://wikkawiki.org/MichielHoltkamp Michiel Holtkamp}
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
if(!defined('WIKKA_ERROR_SETUP_FILE_MISSING')) define('WIKKA_ERROR_SETUP_FILE_MISSING', 'Een bestand van het installatie- / upgradeprogramma is niet gevonden. Installeer Wikka opnieuw!');
if(!defined('WIKKA_ERROR_MYSQL_ERROR')) define('WIKKA_ERROR_MYSQL_ERROR', 'MySQL fout: %d - %s');	// %d - error number; %s - error text
if(!defined('WIKKA_ERROR_CAPTION')) define('WIKKA_ERROR_CAPTION', 'Fout');
if(!defined('WIKKA_ERROR_ACL_READ')) define('WIKKA_ERROR_ACL_READ', 'Je bent niet bevoegd om deze pagina te bekijken.');
if(!defined('WIKKA_ERROR_ACL_READ_SOURCE')) define('WIKKA_ERROR_ACL_READ_SOURCE', 'Je bent niet bevoegd om de broncode van deze pagina te bekijken.');
if(!defined('WIKKA_ERROR_ACL_READ_INFO')) define('WIKKA_ERROR_ACL_READ_INFO', 'Je bent niet bevoegd om deze informatie te bekijken.');
if(!defined('WIKKA_ERROR_LABEL')) define('WIKKA_ERROR_LABEL', 'Fout');
if(!defined('WIKKA_ERROR_PAGE_NOT_EXIST')) define('WIKKA_ERROR_PAGE_NOT_EXIST', 'Sorry, pagina %s bestaat niet.'); // %s (source) page name
if(!defined('WIKKA_ERROR_EMPTY_USERNAME')) define('WIKKA_ERROR_EMPTY_USERNAME', 'Vul je gebruikersnaam in!');
if(!defined('WIKKA_ERROR_PAGE_ALREADY_EXIST')) define('WIKKA_ERROR_PAGE_ALREADY_EXIST', 'Sorry, deze pagina bestaat al.');
if(!defined('WIKKA_LOGIN_LINK_DESC')) define('WIKKA_LOGIN_LINK_DESC', 'login');
if(!defined('WIKKA_MAINPAGE_LINK_DESC')) define('WIKKA_MAINPAGE_LINK_DESC', 'hoofdpagina');
if(!defined('WIKKA_NO_OWNER')) define('WIKKA_NO_OWNER', 'Niemand');
if(!defined('WIKKA_NOT_AVAILABLE')) define('WIKKA_NOT_AVAILABLE', 'nvt');
if(!defined('WIKKA_NOT_INSTALLED')) define('WIKKA_NOT_INSTALLED', 'niet geinstalleerd');
if(!defined('WIKKA_ANONYMOUS_USER')) define('WIKKA_ANONYMOUS_USER', 'anoniem'); // 'name' of non-registered user
if(!defined('WIKKA_UNREGISTERED_USER')) define('WIKKA_UNREGISTERED_USER', 'ongeregisteerde gebruiker'); // alternative for 'anonymous' @@@ make one string only?
if(!defined('WIKKA_ANONYMOUS_AUTHOR_CAPTION')) define('WIKKA_ANONYMOUS_AUTHOR_CAPTION', '('.WIKKA_UNREGISTERED_USER.')'); // @@@ or WIKKA_ANONYMOUS_USER
if(!defined('WIKKA_SAMPLE_WIKINAME')) define('WIKKA_SAMPLE_WIKINAME', 'JanSmit'); // must be a CamelCase name
if(!defined('WIKKA_HISTORY')) define('WIKKA_HISTORY', 'geschiedenis');
if(!defined('WIKKA_REVISIONS')) define('WIKKA_REVISIONS', 'revisies');
if(!defined('WIKKA_REV_WHEN_BY_WHO')) define('WIKKA_REV_WHEN_BY_WHO', '%1$s door %2$s'); // %1$s - timestamp; %2$s - user name
if(!defined('WIKKA_NO_PAGES_FOUND')) define('WIKKA_NO_PAGES_FOUND', 'Geen pagina\'s gevonden.');
if(!defined('WIKKA_PAGE_OWNER')) define('WIKKA_PAGE_OWNER', 'Eigenaar: %s'); // %s - page owner name or link
if(!defined('WIKKA_COMMENT_AUTHOR_DIVIDER')) define('WIKKA_COMMENT_AUTHOR_DIVIDER', ', opmerking door '); //TODo check if we can construct a single phrase here
if(!defined('WIKKA_PAGE_EDIT_LINK_DESC')) define('WIKKA_PAGE_EDIT_LINK_DESC', 'wijzig');
if(!defined('WIKKA_PAGE_CREATE_LINK_DESC')) define('WIKKA_PAGE_CREATE_LINK_DESC', 'maak');
if(!defined('WIKKA_PAGE_EDIT_LINK_TITLE')) define('WIKKA_PAGE_EDIT_LINK_TITLE', 'Klik om %s te wijzigen'); // %s page name @@@ 'Edit %s'
if(!defined('WIKKA_BACKLINKS_LINK_TITLE')) define('WIKKA_BACKLINKS_LINK_TITLE', 'Laat een lijst van pagina\'s zien die linken naar %s'); // %s page name
if(!defined('WIKKA_JRE_LINK_DESC')) define('WIKKA_JRE_LINK_DESC', 'Java Runtime Environment'); // @@@ TODO MJH: translate this?
if(!defined('WIKKA_NOTE')) define('WIKKA_NOTE', 'NOOT:');
if(!defined('WIKKA_JAVA_PLUGIN_NEEDED')) define('WIKKA_JAVA_PLUGIN_NEEDED', 'Java 1.4.1 (of later) Plug-in is nodig om deze applet te draaien,');
/**#@-*/


/*  ------------------ CORE ------------------  */

/**#@+
 * Language constant for the core {@link wikka.php wikka} program
 */
// wikka
if(!defined('ERROR_NO_DB_ACCESS')) define('ERROR_NO_DB_ACCESS', 'Fout: Kon niet verbinden met de database.');
if(!defined('ERROR_RETRIEVAL_MYSQL_VERSION')) define('ERROR_RETRIEVAL_MYSQL_VERSION', 'Kon MySQL versie niet bepalen');
if(!defined('ERROR_WRONG_MYSQL_VERSION')) define('ERROR_WRONG_MYSQL_VERSION', 'Wikka heeft MySQL %s of hoger nodig!');	// %s - version number
if(!defined('STATUS_WIKI_UPGRADE_NOTICE')) define('STATUS_WIKI_UPGRADE_NOTICE', 'Deze site wordt op dit moment ge-upgrade. Probeer het later opnieuw.');
if(!defined('STATUS_WIKI_UNAVAILABLE')) define('STATUS_WIKI_UNAVAILABLE', 'De wiki is op dit moment niet beschikbaar.');
if(!defined('PAGE_GENERATION_TIME')) define('PAGE_GENERATION_TIME', 'Pagina is gegenereerd in %.4f seconden'); // %.4f - page generation time
if(!defined('ERROR_HEADER_MISSING')) define('ERROR_HEADER_MISSING', 'Een header template kon niet gevonden worden. Zorg dat een bestand genaamd <code>header.php</code> in de templates directory staat.'); //TODO Make sure this message matches any filename/folder change
if(!defined('ERROR_FOOTER_MISSING')) define('ERROR_FOOTER_MISSING', 'Een footer template kon niet gevonden worden. Zorg dat een bestand genaamd <code>footer.php</code> in the templates directory staat.'); //TODO Make sure this message matches any filename/folder change

#if(!defined('ERROR_SETUP_HEADER_MISSING')) define('ERROR_SETUP_HEADER_MISSING', 'The file "setup/header.php" was not found. Please install Wikka again!');
#if(!defined('ERROR_SETUP_FOOTER_MISSING')) define('ERROR_SETUP_FOOTER_MISSING', 'The file "setup/footer.php" was not found. Please install Wikka again!');
/**#@-*/

/*  ------------------ TEMPLATE ------------------  */

/**#@+
 * Language constant used by the {@link header.php header} template
 */
// header
if(!defined('GENERIC_DOCTITLE')) define('GENERIC_DOCTITLE', '%1$s: %2$s');	// %1$s - wiki name; %2$s - page title
if(!defined('RSS_REVISIONS_TITLE')) define('RSS_REVISIONS_TITLE', '%1$s: revisies voor %2$s');	// %1$s - wiki name; %2$s - current page name
if(!defined('RSS_RECENTCHANGES_TITLE')) define('RSS_RECENTCHANGES_TITLE', '%s: recentelijk gewijzigde pagina\'s');	// %s - wiki name
if(!defined('YOU_ARE')) define('YOU_ARE', 'Jij bent %s'); // %s - name / ip of the user.
/**#@-*/

/**#@+
 * Language constant used by the {@link footer.php footer} template
 */
// footer
if(!defined('FOOTER_PAGE_EDIT_LINK_DESC')) define('FOOTER_PAGE_EDIT_LINK_DESC', 'Wijzig pagina');
if(!defined('PAGE_HISTORY_LINK_TITLE')) define('PAGE_HISTORY_LINK_TITLE', 'Klik om recentelijke wijzigingen van deze pagina te bekijken.'); // @@@ TODO 'View recent edits to this page'
if(!defined('PAGE_HISTORY_LINK_DESC')) define('PAGE_HISTORY_LINK_DESC', 'Pagina Geschiedenis');
if(!defined('PAGE_REVISION_LINK_TITLE')) define('PAGE_REVISION_LINK_TITLE', 'Klik om een lijst van recentelijke revisies van deze pagina te bekijken.'); // @@@ TODO 'View recent revisions list for this page'
if(!defined('PAGE_REVISION_XML_LINK_TITLE')) define('PAGE_REVISION_XML_LINK_TITLE', 'Klik om een lijst van recentelijke revisies van deze pagina te bekijken.'); // @@@ TODO 'View recent revisions list for this page'
if(!defined('PAGE_ACLS_EDIT_LINK_DESC')) define('PAGE_ACLS_EDIT_LINK_DESC', 'Wijzig ACLs');
if(!defined('PAGE_ACLS_EDIT_ADMIN_LINK_DESC')) define('PAGE_ACLS_EDIT_ADMIN_LINK_DESC', '('.PAGE_ACLS_EDIT_LINK_DESC.')');
if(!defined('PUBLIC_PAGE')) define('PUBLIC_PAGE', 'Publieke pagina');
if(!defined('USER_IS_OWNER')) define('USER_IS_OWNER', 'Dit is een pagina van jou.');
if(!defined('TAKE_OWNERSHIP')) define('TAKE_OWNERSHIP', 'Word eigenaar'); // @@@ TODO MJH: need better translation of 'Take Ownership'
if(!defined('REFERRERS_LINK_TITLE')) define('REFERRERS_LINK_TITLE', 'Klik om een lijst van URL\'s te bekijken die naar deze pagina verwijzen.'); // @@@ TODO 'View a list of URLs referring to this page'
if(!defined('REFERRERS_LINK_DESC')) define('REFERRERS_LINK_DESC', 'Verwijzers'); // @@@ TODO MJH: good translation of 'referrers'?
if(!defined('QUERY_LOG')) define('QUERY_LOG', 'Vraag logboek:'); // @@@ TODO MJH: good translation of 'Query log'?
if(!defined('SEARCH_LABEL')) define('SEARCH_LABEL', 'Zoek:');
/**#@-*/


/*  ------------------ ACTIONS  ------------------  */

/**#@+
 * Language constant used by the {@link calendar.php calendar} action
 */
// calendar
if(!defined('FMT_SUMMARY')) define('FMT_SUMMARY', 'Kalender voor %s');	// %s - ???@@@
if(!defined('TODAY')) define('TODAY', 'vandaag');
/**#@-*/

/**#@+
 * Language constant used by the {@link category.php category} action
 */
// category
if(!defined('ERROR_NO_PAGES')) define('ERROR_NO_PAGES', 'Sorry, geen items gevonden voor %s');	// %s - ???@@@
if(!defined('PAGES_BELONGING_TO')) define('PAGES_BELONGING_TO', 'De volgende %1$d pagina(\'s) zijn van %2$s'); // %1$d number found; %2$s category
/**#@-*/

/**#@+
 * Language constant used by the {@link color.php color} action
 */
// color
if(!defined('ERROR_NO_TEXT_GIVEN')) define('ERROR_NO_TEXT_GIVEN', 'Er is geen tekst om te highlighten!'); // @@@ TODO MJH: translate 'highlight'?
if(!defined('ERROR_NO_COLOR_SPECIFIED')) define('ERROR_NO_COLOR_SPECIFIED', 'Sorry, maar je hebt geen highlightkleur gekozen!'); // @@@ TODO MJH: translate 'highlight'?
/**#@-*/

/**#@+
 * Language constant used by the {@link contact.php contact} action
 */
// contact
if(!defined('SEND_FEEDBACK_LINK_TITLE')) define('SEND_FEEDBACK_LINK_TITLE', 'Stuur ons je feedback');
if(!defined('SEND_FEEDBACK_LINK_TEXT')) define('SEND_FEEDBACK_LINK_TEXT', 'Contact');
/**#@-*/

/**#@+
 * Language constant used by the {@link countowned.php countowned} action
 */
// countowned
if(!defined('DISPLAY_MYPAGES_LINK_TITLE')) define('DISPLAY_MYPAGES_LINK_TITLE', 'Laat een lijst zien van jouw pagina\'s.');
/**#@-*/

/**#@+
 * Language constant used by the {@link countpages.php countpages} action
 */
// countpages
if(!defined('INDEX_LINK_TITLE')) define('INDEX_LINK_TITLE', 'Laat een alfabetische pagina index zien.');
/**#@-*/

/**#@+
 * Language constant used by the {@link dbinfo.php dbinfo} action
 */
// dbinfo
if(!defined('HD_DBINFO')) define('HD_DBINFO','Database Informatie');
if(!defined('HD_DBINFO_DB')) define('HD_DBINFO_DB','Database');
if(!defined('HD_DBINFO_TABLES')) define('HD_DBINFO_TABLES','Tabellen');
if(!defined('HD_DB_CREATE_DDL')) define('HD_DB_CREATE_DDL','DDL om database %s te maken:');				# %s will hold database name
if(!defined('HD_TABLE_CREATE_DDL')) define('HD_TABLE_CREATE_DDL','DDL om tabel %s te maken:');				# %s will hold table name
if(!defined('TXT_INFO_1')) define('TXT_INFO_1','Dit programma levert informatie over de database(s) en tabellen in je systeem.');
if(!defined('TXT_INFO_2')) define('TXT_INFO_2',' Afhankelijke van permissies van de Wikka database gebruiker, zijn niet alle databases of tabellen zichtbaar.');
if(!defined('TXT_INFO_3')) define('TXT_INFO_3',' Waar een DDL is opgegeven, omvat dit alles wat nodig is om exact dezelfde database en tabel definities opnieuw te maken,');
if(!defined('TXT_INFO_4')) define('TXT_INFO_4',' inclusief defaults die niet expliciet zijn opgegeven.');
if(!defined('FORM_SELDB_LEGEND')) define('FORM_SELDB_LEGEND','Databases');
if(!defined('FORM_SELTABLE_LEGEND')) define('FORM_SELTABLE_LEGEND','Tabellen');
if(!defined('FORM_SELDB_OPT_LABEL')) define('FORM_SELDB_OPT_LABEL','Selecteer een database:');
if(!defined('FORM_SELTABLE_OPT_LABEL')) define('FORM_SELTABLE_OPT_LABEL','Selecteer een tabel:');
if(!defined('FORM_SUBMIT_SELDB')) define('FORM_SUBMIT_SELDB','Selecteer');
if(!defined('FORM_SUBMIT_SELTABLE')) define('FORM_SUBMIT_SELTABLE','Selecteer');
if(!defined('MSG_ONLY_ADMIN')) define('MSG_ONLY_ADMIN','Sorry, alleen beheerders kunnen database informatie bekijken.');
if(!defined('MSG_SINGLE_DB')) define('MSG_SINGLE_DB','Informatie over de <tt>%s</tt> database.');			# %s will hold database name
if(!defined('MSG_NO_TABLES')) define('MSG_NO_TABLES','Geen tabellen gevonden in de <tt>%s</tt> database. Je MySQL gebruiker  heeft misschien niet genoeg  privileges om de database te benaderen.');		# %s will hold database name
if(!defined('MSG_NO_DB_DDL')) define('MSG_NO_DB_DDL','DDL om <tt>%s</tt> te maken kon niet opgehaald worden.');	# %s will hold database name
if(!defined('MSG_NO_TABLE_DDL')) define('MSG_NO_TABLE_DDL','DDL om <tt>%s</tt> te maken kon niet opgehaald worden.');# %s will hold table name
/**#@-*/

/**#@+
 * Language constant used by the {@link emailpassword.php emailpassword} action
 */
// emailpassword
if(!defined('PW_FORGOTTEN_HEADING')) define('PW_FORGOTTEN_HEADING', 'Wachtwoordherinnering');
if(!defined('PW_CHK_SENT')) define('PW_CHK_SENT', 'Een wachtwoordherinnering is gestuurd naar %s\'s opgeslagen email address.'); // %s - username
if(!defined('PW_FORGOTTEN_MAIL')) define('PW_FORGOTTEN_MAIL', 'Hallo, %1$s\n\n\nIemand vroeg ons om een wachtwoord herinneren te sturen naar dit emailadresom in te loggen op %2$s. Als je deze herinnering niet aangevraagd hebt, negeer dan deze email. -- Je hoeft geen actie te ondernemen. -- Je wachtwoord zal onveranderd blijven.\n\nJe WikiNaam: %1$s \nWachtwoordherinnering: %3$s \nURL: %4$s \n\nVergeet niet om je wachtwoord te veranderen direct nadat je ingelogd bent.'); // %1$s - username; %2$s - wiki name; %3$s - md5 sum of pw; %4$s - login url of the wiki
if(!defined('PW_FORGOTTEN_MAIL_REF')) define('PW_FORGOTTEN_MAIL_REF', 'Wachtwoordherinnering voor %s'); // %s - wiki name
if(!defined('PW_FORM_TEXT')) define('PW_FORM_TEXT', 'Geef je WikiNaam en een wachtwoordherinnering zal gestuurd worden naar je opgeslagen emailadres.');
if(!defined('PW_FORM_FIELDSET_LEGEND')) define('PW_FORM_FIELDSET_LEGEND', 'Je WikiNaam:');
if(!defined('ERROR_UNKNOWN_USER')) define('ERROR_UNKNOWN_USER', 'Je hebt een niet-bestaande gebruiker opgegeven!');
#if(!defined('ERROR_MAIL_NOT_SENT')) define('ERROR_MAIL_NOT_SENT', 'An error occurred while trying to send the password. Outgoing mail might be disabled. Please contact your server administrator.');
if(!defined('ERROR_MAIL_NOT_SENT')) define('ERROR_MAIL_NOT_SENT', 'Een fout is opgetreden tijdens het versturen van het wachtwoord. Uitgaande mail is misschien niet geactiveerd. Probeer je wikibeheerder te bereiken door een pagina opmerking te plaatsen.');
if(!defined('BUTTON_SEND_PW')) define('BUTTON_SEND_PW', 'Verstuur herinnering');
if(!defined('USERSETTINGS_REF')) define('USERSETTINGS_REF', 'Ga terug naar de %s pagina.'); // %s - UserSettings link
/**#@-*/

/**#@+
 * Language constant used by the {@link feedback.php feedback} action
 */
// feedback
if(!defined('ERROR_EMPTY_NAME')) define('ERROR_EMPTY_NAME', 'Vul je naam in');
if(!defined('ERROR_INVALID_EMAIL')) define('ERROR_INVALID_EMAIL', 'Vul een geldig emailadres in');
if(!defined('ERROR_EMPTY_MESSAGE')) define('ERROR_EMPTY_MESSAGE', 'Vul tekst in');
if(!defined('ERROR_FEEDBACK_MAIL_NOT_SENT')) define('ERROR_FEEDBACK_MAIL_NOT_SENT', 'Sorry, Een fout is opgetreden tijdens het versturen van je email. Uitgaande mail is misschien niet geactiveerd. Probeer een andere manier om %s te bereiken, bijvoorbeeld door een pagina opmerking te plaatsen.'); // %s - name of the recipient
if(!defined('FEEDBACK_FORM_LEGEND')) define('FEEDBACK_FORM_LEGEND', 'Neem contact op met %s'); //%s - wikiname of the recipient
if(!defined('FEEDBACK_NAME_LABEL')) define('FEEDBACK_NAME_LABEL', 'Je naam:');
if(!defined('FEEDBACK_EMAIL_LABEL')) define('FEEDBACK_EMAIL_LABEL', 'Je email:');
if(!defined('FEEDBACK_MESSAGE_LABEL')) define('FEEDBACK_MESSAGE_LABEL', 'Je bericht:');
if(!defined('FEEDBACK_SEND_BUTTON')) define('FEEDBACK_SEND_BUTTON', 'Verstuur');
if(!defined('FEEDBACK_SUBJECT')) define('FEEDBACK_SUBJECT', 'Feedback van %s'); // %s - name of the wiki
if(!defined('SUCCESS_FEEDBACK_SENT')) define('SUCCESS_FEEDBACK_SENT', 'Bedankt voor je feedback, %s! Je bericht is gestuurd.'); //%s - name of the sender
/**#@-*/

/**#@+
 * Language constant used by the {@link files.php files} action
 */
// files
if(!defined('ERROR_UPLOAD_DIRECTORY_NOT_WRITABLE')) define('ERROR_UPLOAD_DIRECTORY_NOT_WRITABLE', 'Zorg dat de server schrijftoegang heeft op de directory genaamd %s.'); // %s Upload folder ref #89
if(!defined('ERROR_UPLOAD_DIRECTORY_NOT_READABLE')) define('ERROR_UPLOAD_DIRECTORY_NOT_READABLE', 'Zorg dat de server leestoegang heeft op de dirctory genaamd %s.'); // %s Upload folder ref #89
if(!defined('ERROR_NONEXISTENT_FILE')) define('ERROR_NONEXISTENT_FILE', 'Sorry, een bestand genaamd %s bestaat niet.'); // %s - file name ref
if(!defined('ERROR_FILE_UPLOAD_INCOMPLETE')) define('ERROR_FILE_UPLOAD_INCOMPLETE', 'Bestandsupload incompleet! Probeer het opnieuw.');
if(!defined('ERROR_UPLOADING_FILE')) define('ERROR_UPLOADING_FILE', 'Er is een fout opgetreden tijdens het uploaden van je bestand.');
if(!defined('ERROR_FILE_ALREADY_EXISTS')) define('ERROR_FILE_ALREADY_EXISTS', 'Sorry, een bestand genaamd %s bestaat al.'); // %s - file name ref
if(!defined('ERROR_EXTENSION_NOT_ALLOWED')) define('ERROR_EXTENSION_NOT_ALLOWED', 'Sorry, bestanden met deze extensies zijn niet toegestaan.');
if(!defined('ERROR_FILE_TOO_BIG')) define('ERROR_FILE_TOO_BIG', 'Te uploaden bestand is te groot. Maximaal toegestane grootte is %s.'); // %s - allowed filesize
if(!defined('ERROR_NO_FILE_SELECTED')) define('ERROR_NO_FILE_SELECTED', 'Geen bestand geselecteerd.');
if(!defined('ERROR_FILE_UPLOAD_IMPOSSIBLE')) define('ERROR_FILE_UPLOAD_IMPOSSIBLE', 'Bestandsupload onmogelijk door verkeerd geconfiguurde server.');
if(!defined('SUCCESS_FILE_UPLOADED')) define('SUCCESS_FILE_UPLOADED', 'Bestand is successvol geupload.');
if(!defined('FILE_TABLE_CAPTION')) define('FILE_TABLE_CAPTION', 'Bijlages');
if(!defined('FILE_TABLE_HEADER_NAME')) define('FILE_TABLE_HEADER_NAME', 'Bestand');
if(!defined('FILE_TABLE_HEADER_SIZE')) define('FILE_TABLE_HEADER_SIZE', 'Grootte');
if(!defined('FILE_TABLE_HEADER_DATE')) define('FILE_TABLE_HEADER_DATE', 'Laats veranderd');
if(!defined('FILE_UPLOAD_FORM_LEGEND')) define('FILE_UPLOAD_FORM_LEGEND', 'Voeg nieuwe bijlage toe:');
if(!defined('FILE_UPLOAD_FORM_LABEL')) define('FILE_UPLOAD_FORM_LABEL', 'Bestand:');
if(!defined('FILE_UPLOAD_FORM_BUTTON')) define('FILE_UPLOAD_FORM_BUTTON', 'Upload');
if(!defined('DOWNLOAD_LINK_TITLE')) define('DOWNLOAD_LINK_TITLE', 'Download %s'); // %s - file name
if(!defined('DELETE_LINK_TITLE')) define('DELETE_LINK_TITLE', 'Verwijder %s'); // %s - file name
if(!defined('NO_ATTACHMENTS')) define('NO_ATTACHMENTS', 'Deze pagina heeft geen bijlage.');
/**#@-*/

/**#@+
 * Language constant used by the {@link googleform.php googleform} action
 */
// googleform
if(!defined('GOOGLE_BUTTON')) define('GOOGLE_BUTTON', 'Google');
/**#@-*/

/**#@+
 * Language constant used by the {@link highscores.php highscores} action
 */
// include
if(!defined('HIGHSCORES_LABEL_EDITS')) define('HIGHSCORES_LABEL_EDITS', 'wijzigingen');
if(!defined('HIGHSCORES_LABEL_COMMENTS')) define('HIGHSCORES_LABEL_COMMENTS', 'opmerkingen');
if(!defined('HIGHSCORES_LABEL_PAGES')) define('HIGHSCORES_LABEL_PAGES', 'eigen pagina\'s');
if(!defined('HIGHSCORES_CAPTION')) define('HIGHSCORES_CAPTION', 'Top %1$s bijdrager(s) op aantal %2$s'); 
if(!defined('HIGHSCORES_HEADER_RANK')) define('HIGHSCORES_HEADER_RANK', 'rank');
if(!defined('HIGHSCORES_HEADER_USER')) define('HIGHSCORES_HEADER_USER', 'gebruiker');
if(!defined('HIGHSCORES_HEADER_PERCENTAGE')) define('HIGHSCORES_HEADER_PERCENTAGE', 'percentage');
/**#@-*/

/**#@+
 * Language constant used by the {@link include.php include} action
 */
// include
if(!defined('ERROR_CIRCULAR_REFERENCE')) define('ERROR_CIRCULAR_REFERENCE', 'Circulaire referentie ontdekt!');
/**#@-*/

/**#@+
 * Language constant used by the {@link lastedit.php lastedit} action
 */
// lastedit
if(!defined('LASTEDIT_DESC')) define('LASTEDIT_DESC', 'Laatst gewijzigd door %s'); // %s user name
if(!defined('LASTEDIT_DIFF_LINK_TITLE')) define('LASTEDIT_DIFF_LINK_TITLE', 'Laat verschillen met laatste revisie zien.');
/**#@-*/

/**#@+
 * Language constant used by the {@link lastusers.php lastusers} action
 */
// lastusers
if(!defined('LASTUSERS_CAPTION')) define('LASTUSERS_CAPTION', 'Recentlijk geregistreerde gebruikers');
if(!defined('SIGNUP_DATE_TIME')) define('SIGNUP_DATE_TIME', 'Aanmeldingsdatum/-tijd');
if(!defined('NAME_TH')) define('NAME_TH', 'Gebruikersnaam');
if(!defined('OWNED_PAGES_TH')) define('OWNED_PAGES_TH', 'Eigen pages');
if(!defined('SIGNUP_DATE_TIME_TH')) define('SIGNUP_DATE_TIME_TH', 'Aanmeldingsdatum/-tijd');
/**#@-*/

/**#@+
 * Language constant used by the {@link mindmap.php mindmap} action
 */
// mindmap
if(!defined('MM_JRE_INSTALL_REQ')) define('MM_JRE_INSTALL_REQ', 'Installeer een %s op je computer.'); // %s - JRE install link
if(!defined('MM_DOWNLOAD_LINK_DESC')) define('MM_DOWNLOAD_LINK_DESC', 'Download deze MindMap');
if(!defined('MM_EDIT')) define('MM_EDIT', 'Use %s to edit it'); // %s - link to freemind project
if(!defined('MM_FULLSCREEN_LINK_DESC')) define('MM_FULLSCREEN_LINK_DESC', 'Open fullscreen');
if(!defined('ERROR_INVALID_MM_SYNTAX')) define('ERROR_INVALID_MM_SYNTAX', 'Fout: ongeldige MindMap actie syntax.');
if(!defined('PROPER_USAGE_MM_SYNTAX')) define('PROPER_USAGE_MM_SYNTAX', 'Geldig gebruik: %1$s of %2$s'); // %1$s - syntax sample 1; %2$s - syntax sample 2
/**#@-*/

/**#@+
 * Language constant used by the {@link mychanges.php mychanges} action
 */
// mychanges
if(!defined('NO_PAGES_EDITED')) define('NO_PAGES_EDITED', 'Je hebt nog geen pagina\'s gewijzigd.');
if(!defined('MYCHANGES_ALPHA_LIST')) define('MYCHANGES_ALPHA_LIST', "Dit is een lijst van pagina's die je gewijzigd hebt, met de tijden van je laatste wijziging.");
if(!defined('MYCHANGES_DATE_LIST')) define('MYCHANGES_DATE_LIST', "Dit is een lijst van pagina's die je gewijzigd hebt, gesorteerd op de tijd van je laatste wijziging.");
if(!defined('ORDER_DATE_LINK_DESC')) define('ORDER_DATE_LINK_DESC', 'sorteer op datum');
if(!defined('ORDER_ALPHA_LINK_DESC')) define('ORDER_ALPHA_LINK_DESC', 'sorteer alfabetisch');
if(!defined('MYCHANGES_NOT_LOGGED_IN')) define('MYCHANGES_NOT_LOGGED_IN', "Je bent niet ingelogd, daarom kon de lijst van pagina\'s die je gewijzigd hebt niet opgehaald worden.");
/**#@-*/

/**#@+
 * Language constant used by the {@link mypages.php mypages} action
 */
// mypages
if(!defined('OWNED_PAGES_TXT')) define('OWNED_PAGES_TXT', 'Dit is de lijst van jouw pagina\'s.');
if(!defined('OWNED_NO_PAGES')) define('OWNED_NO_PAGES', 'Je hebt geen eigen pagina\'s.');
if(!defined('OWNED_NOT_LOGGED_IN')) define('OWNED_NOT_LOGGED_IN', "Je bent niet ingelogd, daarom kon de lijst van jouw pagina\'s niet opgehaald worden.");
/**#@-*/

/**#@+
 * Language constant used by the {@link newpage.php newpage} action
 */
// newpage
if(!defined('NEWPAGE_CREATE_LEGEND')) define('NEWPAGE_CREATE_LEGEND', 'Maak een nieuwe pagina');
if(!defined('NEWPAGE_CREATE_BUTTON')) define('NEWPAGE_CREATE_BUTTON', 'Maak');
/**#@-*/

/**#@+
 * Language constant used by the {@link orphanedpages.php orphanedpages} action
 */
// orphanedpages
if(!defined('NO_ORPHANED_PAGES')) define('NO_ORPHANED_PAGES', 'Geen ongerefereerde pagina\'s. Goed!'); // @@@ TODO MJH: orphaned as in no parents or no references?

/**#@+
 * Language constant used by the {@link ownedpages.php ownedpages} action
 */
// ownedpages
if(!defined('OWNEDPAGES_COUNTS')) define('OWNEDPAGES_COUNTS', 'Op deze Wiki zijn %1$s van de %2$s pagina\'s van jou.'); // %1$s - number of pages owned; %2$s - total number of pages
if(!defined('OWNEDPAGES_PERCENTAGE')) define('OWNEDPAGES_PERCENTAGE', 'Dat betekent dat %s van jou is.'); // %s percentage of pages owned
/**#@-*/

/**#@+
 * Language constant used by the {@link pageindex.php pageindex} action
 */
// pageindex
if(!defined('PAGEINDEX_HEADING')) define('PAGEINDEX_HEADING', 'Pagina Index');
if(!defined('PAGEINDEX_CAPTION')) define('PAGEINDEX_CAPTION', 'Dit is an alfabetische lijst van pages die je op deze server kunt lezen.');
if(!defined('PAGEINDEX_OWNED_PAGES_CAPTION')) define('PAGEINDEX_OWNED_PAGES_CAPTION', 'Items gemarkeerd met een * zijn je eigen pagina\'s.');
if(!defined('PAGEINDEX_ALL_PAGES')) define('PAGEINDEX_ALL_PAGES', 'Alle');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges.php recentchanges} action
 */
// recentchanges
if(!defined('RECENTCHANGES_HEADING')) define('RECENTCHANGES_HEADING', 'Recentelijk gewijzigde pagina\'s');
if(!defined('REVISIONS_LINK_TITLE')) define('REVISIONS_LINK_TITLE', 'Bekijk de lijst van recente revisies voor %s'); // %s - page name
if(!defined('HISTORY_LINK_TITLE')) define('HISTORY_LINK_TITLE', 'Bekijk de wijzigingsgeschiedenis van %s'); // %s - page name
if(!defined('WIKIPING_ENABLED')) define('WIKIPING_ENABLED', 'WikiPing actief: Veranderingen op deze wiki worden verzonden naar %s'); // %s - link to wikiping server
if(!defined('RECENTCHANGES_NONE_FOUND')) define('RECENTCHANGES_NONE_FOUND', 'Er zijn geen recentelijk gewijzigde pagina\'s.');
if(!defined('RECENTCHANGES_NONE_ACCESSIBLE')) define('RECENTCHANGES_NONE_ACCESSIBLE', 'Er zijn geen recentelijk gewijzigde pagina\'s waar je toegang tot hebt.');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentcomments.php recentcomments} action
 */
// recentcomments
if(!defined('RECENTCOMMENTS_HEADING')) define('RECENTCOMMENTS_HEADING', 'Recentelijke opmerkingen.');
if(!defined('RECENTCOMMENTS_TIMESTAMP_CAPTION')) define('RECENTCOMMENTS_TIMESTAMP_CAPTION', '%s'); // %s - timestamp
if(!defined('RECENTCOMMENTS_NONE_FOUND')) define('RECENTCOMMENTS_NONE_FOUND', 'Er zijn geen recentelijke opmerkingen.');
if(!defined('RECENTCOMMENTS_NONE_ACCESSIBLE')) define('RECENTCOMMENTS_NONE_ACCESSIBLE', 'Er zijn geen recentelijke opmerkingen waar je toegang tot hebt.');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentlycommented.php recentlycommented} action
 */
// recentlycommented
if(!defined('RECENTLYCOMMENTED_HEADING')) define('RECENTLYCOMMENTED_HEADING', 'Pagina\'s met recentelijke opmerkingen.');
if(!defined('RECENTLYCOMMENTED_NONE_FOUND')) define('RECENTLYCOMMENTED_NONE_FOUND', 'Er zijn geen pagina\'s met recentelijke opmerkingen.');
if(!defined('RECENTLYCOMMENTED_NONE_ACCESSIBLE')) define('RECENTLYCOMMENTED_NONE_ACCESSIBLE', 'Er zijn geen pagina\'s met recentelijke opmerkingen waar je toegang tot hebt.');
/**#@-*/

/**#@+
 * Language constant used by the {@link system.php system} action
 */
// system
if(!defined('SYSTEM_HOST_CAPTION')) define('SYSTEM_HOST_CAPTION', '(%s)'); // %s - host name
if(!defined('WIKKA_STATUS_NOT_AVAILABLE')) define('WIKKA_STATUS_NOT_AVAILABLE', 'nvt');
/**#@-*/

/**#@+
 * Language constant shared by the {@link textsearch.php textsearch} and {@link textsearchexpanded.php textsearchexpanded} actions
 */
// textsearch & textsearchexpanded
if(!defined('SEARCH_FOR')) define('SEARCH_FOR', 'Zoek naar');
if(!defined('SEARCH_ZERO_MATCH')) define('SEARCH_ZERO_MATCH', 'Geen matches gevonden');
if(!defined('SEARCH_ONE_MATCH')) define('SEARCH_ONE_MATCH', 'Een match gevonden');
if(!defined('SEARCH_N_MATCH')) define('SEARCH_N_MATCH', '%d matches gevonden'); // %d - number of hits
if(!defined('SEARCH_RESULTS')) define('SEARCH_RESULTS', 'Zoekresultaten: <strong>%1$s</strong> voor <strong>%2$s</strong>'); # %1$s: n matches for | %2$s: search term
if(!defined('SEARCH_NOT_SURE_CHOICE')) define('SEARCH_NOT_SURE_CHOICE', 'Niet zeker welke pagina te kiezen?');
if(!defined('SEARCH_EXPANDED_LINK_DESC')) define('SEARCH_EXPANDED_LINK_DESC', 'Zoek Met Uitgebreide Tekst'); // search link description
if(!defined('SEARCH_TRY_EXPANDED')) define('SEARCH_TRY_EXPANDED', 'Probeer %s welke omringende tekst weergeeft.'); // %s expanded search link
if(!defined('SEARCH_TIPS')) define('SEARCH_TIPS', 'Zoek Tips:');
if(!defined('SEARCH_WORD_1')) define('SEARCH_WORD_1', 'appel');
if(!defined('SEARCH_WORD_2')) define('SEARCH_WORD_2', 'banaan');
if(!defined('SEARCH_WORD_3')) define('SEARCH_WORD_3', 'sap');
if(!defined('SEARCH_WORD_4')) define('SEARCH_WORD_4', 'macintosh');
if(!defined('SEARCH_WORD_5')) define('SEARCH_WORD_5', 'enkele');
if(!defined('SEARCH_WORD_6')) define('SEARCH_WORD_6', 'woorden');
if(!defined('SEARCH_PHRASE')) define('SEARCH_PHRASE',sprintf('%s %s',SEARCH_WORD_5,SEARCH_WORD_6));
if(!defined('SEARCH_TARGET_1')) define('SEARCH_TARGET_1', 'Vind pagina\'s die minstens een van de twee woorden bevatten.');
if(!defined('SEARCH_TARGET_2')) define('SEARCH_TARGET_2', 'Vind pagina\'s die beide woorden bevatten.');
if(!defined('SEARCH_TARGET_3')) define('SEARCH_TARGET_3',sprintf("Vind pagina\'s die het woord '%1\$s' bevatten, maar niet '%2\$s'.",SEARCH_WORD_1,SEARCH_WORD_4));
if(!defined('SEARCH_TARGET_4')) define('SEARCH_TARGET_4',"Vind pagina\'s die woorden zoals 'appel', 'appels', 'appelmoes', of 'appelscha' bevatten."); // make sure target words all *start* with SEARCH_WORD_1
if(!defined('SEARCH_TARGET_5')) define('SEARCH_TARGET_5',sprintf("Vind pagina\'s die de exacte zinsnede '%1\$s' bevatten (bijvoorbeeld, pagina\'s die '%1\$s van wijsheid' maar niet '%2\$s irrelevante %3\$s').",SEARCH_PHRASE,SEARCH_WORD_5,SEARCH_WORD_6));
/**#@-*/

/**#@+
 * Language constant used by the {@link usersettings.php usersettings} action
 */
// usersettings
// - error messages
if(!defined('ERROR_EMPTY_USERNAME')) define('ERROR_EMPTY_USERNAME', 'Vul je gebruikersnaam in.');
if(!defined('ERROR_NONEXISTENT_USERNAME')) define('ERROR_NONEXISTENT_USERNAME', 'Sorry, deze gebruikersnaam bestaat niet.'); // @@@ too specific
if(!defined('ERROR_RESERVED_PAGENAME')) define('ERROR_RESERVED_PAGENAME', 'Sorry, deze is naam is gereserveerd voor een pagina. Kies een andere naam.');
if(!defined('ERROR_WIKINAME')) define('ERROR_WIKINAME', 'Gebruikersnaam er uit zien als een %1$s, bijv. %2$s.'); // %1$s - identifier WikiName; %2$s - sample WikiName
if(!defined('ERROR_EMPTY_EMAIL_ADDRESS')) define('ERROR_EMPTY_EMAIL_ADDRESS', 'Vul een emailadres in.');
if(!defined('ERROR_INVALID_EMAIL_ADDRESS')) define('ERROR_INVALID_EMAIL_ADDRESS', 'Dat ziet er niet echt uit als een emailadres.');
if(!defined('ERROR_INVALID_PASSWORD')) define('ERROR_INVALID_PASSWORD', 'Sorry, je hebt het verkeerde wachtwoord ingevuld.');	// @@@ too specific
if(!defined('ERROR_INVALID_HASH')) define('ERROR_INVALID_HASH', 'Sorry, je hebt een verkeerde wachtwoordherinnering ingevuld.');
if(!defined('ERROR_INVALID_OLD_PASSWORD')) define('ERROR_INVALID_OLD_PASSWORD', 'Het oude wachtwoord wat je ingevuld hebt is verkeerd.');
if(!defined('ERROR_EMPTY_PASSWORD')) define('ERROR_EMPTY_PASSWORD', 'Vul een wachtwoord in.');
if(!defined('ERROR_EMPTY_PASSWORD_OR_HASH')) define('ERROR_EMPTY_PASSWORD_OR_HASH', 'Vul je wachtwoord of wachtwoordherinnering in.');
if(!defined('ERROR_EMPTY_CONFIRMATION_PASSWORD')) define('ERROR_EMPTY_CONFIRMATION_PASSWORD', 'Bevesting je wachtwoord om een nieuw account te registreren.');
if(!defined('ERROR_EMPTY_NEW_CONFIRMATION_PASSWORD')) define('ERROR_EMPTY_NEW_CONFIRMATION_PASSWORD', 'Bevesting je wachtwoord om een nieuw account te wijzigen.');
if(!defined('ERROR_EMPTY_NEW_PASSWORD')) define('ERROR_EMPTY_NEW_PASSWORD', 'Je moet ook een nieuw wachtwoord invullen.');
if(!defined('ERROR_PASSWORD_MATCH')) define('ERROR_PASSWORD_MATCH', 'Wachtwoorden zijn niet gelijk.');
if(!defined('ERROR_PASSWORD_NO_BLANK')) define('ERROR_PASSWORD_NO_BLANK', 'Sorry, spaties (en soortgelijken) zijn niet toegestaand in het wachtwoord.');
if(!defined('ERROR_PASSWORD_TOO_SHORT')) define('ERROR_PASSWORD_TOO_SHORT', 'Sorry, het wachtwoord moet minstens %d karakters bevatten.'); // %d - minimum password length
if(!defined('ERROR_INVALID_INVITATION_CODE')) define('ERROR_INVALID_INVITATION_CODE', 'Dit is a prive wiki, alleen uitgenodigden kunnen een account registreren! Neem contact op met de beheerder van deze website voor een uitnodigindscode.');
if(!defined('ERROR_INVALID_REVISION_DISPLAY_LIMIT')) define('ERROR_INVALID_REVISION_DISPLAY_LIMIT', 'Het aantal paginarevisies mag niet groter zijn dan %d.'); // %d - maximum revisions to view
if(!defined('ERROR_INVALID_RECENTCHANGES_DISPLAY_LIMIT')) define('ERROR_INVALID_RECENTCHANGES_DISPLAY_LIMIT', 'Het aantal recentlelijk gewijzigde pagina\'s mag niet groter zijn dan %d.'); // %d - maximum changed pages to view
// - success messages
if(!defined('SUCCESS_USER_LOGGED_OUT')) define('SUCCESS_USER_LOGGED_OUT', 'Je hebt jezelf successvol uitgelogd.');
if(!defined('SUCCESS_USER_REGISTERED')) define('SUCCESS_USER_REGISTERED', 'Je hebt jezelf successvol geregistreerd!');
if(!defined('SUCCESS_USER_SETTINGS_STORED')) define('SUCCESS_USER_SETTINGS_STORED', 'Gebruikersinstellingen opgeslagen!');
if(!defined('SUCCESS_USER_PASSWORD_CHANGED')) define('SUCCESS_USER_PASSWORD_CHANGED', 'Wachtwoord successvol gewijzigd!');
// - captions
if(!defined('NEW_USER_REGISTER_CAPTION')) define('NEW_USER_REGISTER_CAPTION', 'Als je je registreerd als een nieuwe gebruiker:');
if(!defined('REGISTERED_USER_LOGIN_CAPTION')) define('REGISTERED_USER_LOGIN_CAPTION', 'Als je al een account hebt, log dan hier in:');
if(!defined('RETRIEVE_PASSWORD_CAPTION')) define('RETRIEVE_PASSWORD_CAPTION', 'Login met je [[%s wachtwoordherinnering]]:'); //%s PasswordForgotten link
if(!defined('USER_LOGGED_IN_AS_CAPTION')) define('USER_LOGGED_IN_AS_CAPTION', 'Je bent ingelogd als %s'); // %s user name
// - form legends
if(!defined('USER_ACCOUNT_LEGEND')) define('USER_ACCOUNT_LEGEND', 'Je account');
if(!defined('USER_SETTINGS_LEGEND')) define('USER_SETTINGS_LEGEND', 'Instellingen');
if(!defined('LOGIN_REGISTER_LEGEND')) define('LOGIN_REGISTER_LEGEND', 'Login/Registreer');
if(!defined('LOGIN_LEGEND')) define('LOGIN_LEGEND', 'Login');
#if(!defined('REGISTER_LEGEND')) define('REGISTER_LEGEND', 'Register'); // @@@ TODO to be used later for register-action
if(!defined('CHANGE_PASSWORD_LEGEND')) define('CHANGE_PASSWORD_LEGEND', 'Wijzig je wachtwoord');
if(!defined('RETRIEVE_PASSWORD_LEGEND')) define('RETRIEVE_PASSWORD_LEGEND', 'Wachtwoord vergeten');
// - form field labels (should end in ':' _unless_ it's a checkbox or radio button option)
if(!defined('USERSETTINGS_REDIRECT_AFTER_LOGIN_LABEL')) define('USERSETTINGS_REDIRECT_AFTER_LOGIN_LABEL', 'Verwijs naar %s na login');	// %s page to redirect to
if(!defined('USER_EMAIL_LABEL')) define('USER_EMAIL_LABEL', 'Je emailadres:');
if(!defined('DOUBLECLICK_LABEL')) define('DOUBLECLICK_LABEL', 'Dubbelklik wijzigen:');
if(!defined('SHOW_COMMENTS_LABEL')) define('SHOW_COMMENTS_LABEL', 'Laat opmerkingen standaard zien:');
if(!defined('COMMENT_STYLE_LABEL')) define('COMMENT_STYLE_LABEL', 'Standaard opmerkingen stijl');
if(!defined('COMMENT_ASC_LABEL')) define('COMMENT_ASC_LABEL', 'Plat (oudste eerst)'); // @@@ TODO MJH: Plat?
if(!defined('COMMENT_DEC_LABEL')) define('COMMENT_DEC_LABEL', 'Plat (newest first)'); // @@@ TODO MJH: Plat?
if(!defined('COMMENT_THREADED_LABEL')) define('COMMENT_THREADED_LABEL', 'Threaded'); // @@@ TODO MJH: gethread? vertakt? boomweergave? boomstructuur?
if(!defined('COMMENT_DELETED_LABEL')) define('COMMENT_DELETED_LABEL', '[Opmerking verwijderd]');
if(!defined('COMMENT_BY_LABEL')) define('COMMENT_BY_LABEL', 'Opmerking van ');
if(!defined('RECENTCHANGES_DISPLAY_LIMIT_LABEL')) define('RECENTCHANGES_DISPLAY_LIMIT_LABEL', 'RecentChanges weergave limiet:');
if(!defined('PAGEREVISION_LIST_LIMIT_LABEL')) define('PAGEREVISION_LIST_LIMIT_LABEL', 'Pagina revisies weergave limiet:');
if(!defined('NEW_PASSWORD_LABEL')) define('NEW_PASSWORD_LABEL', 'Je nieuwe wachtwoord:');
if(!defined('NEW_PASSWORD_CONFIRM_LABEL')) define('NEW_PASSWORD_CONFIRM_LABEL', 'Bevestig nieuw wachtwoord:');
if(!defined('NO_REGISTRATION')) define('NO_REGISTRATION', 'Registratie op deze wiki is uitgeschakeld.');
if(!defined('PASSWORD_LABEL')) define('PASSWORD_LABEL', 'Wachtwoord (%s+ karakters):'); // %s minimum number of characters
if(!defined('CONFIRM_PASSWORD_LABEL')) define('CONFIRM_PASSWORD_LABEL', 'Bevestig wachtwoord:');
if(!defined('TEMP_PASSWORD_LABEL')) define('TEMP_PASSWORD_LABEL', 'Wachtwoordherinnering:');
if(!defined('INVITATION_CODE_SHORT')) define('INVITATION_CODE_SHORT', 'Uitnodigingscode');
if(!defined('INVITATION_CODE_LONG')) define('INVITATION_CODE_LONG', 'Om te registreren, moet je de uitnodiginscode invullen die gestuurd is door de beheerder van deze website.');
if(!defined('INVITATION_CODE_LABEL')) define('INVITATION_CODE_LABEL', 'Je %s:'); // %s - expanded short invitation code prompt
if(!defined('WIKINAME_SHORT')) define('WIKINAME_SHORT', 'WikiNaam');
if(!defined('WIKINAME_LONG')) define('WIKINAME_LONG',sprintf('Een WikiNaam wordt gevormd door twee of meer woorden die beginnen met een hoofdletter, zonder spaties, bv. %s',WIKKA_SAMPLE_WIKINAME));
if(!defined('WIKINAME_LABEL')) define('WIKINAME_LABEL', 'Je %s:'); // %s - expanded short wiki name prompt
// - form options
if(!defined('CURRENT_PASSWORD_OPTION')) define('CURRENT_PASSWORD_OPTION', 'Je huidige wachtwoord');
if(!defined('PASSWORD_REMINDER_OPTION')) define('PASSWORD_REMINDER_OPTION', 'Wachtwoordherinnering');
// - form buttons
if(!defined('UPDATE_SETTINGS_BUTTON')) define('UPDATE_SETTINGS_BUTTON', 'Vernieuw Instellingen');
if(!defined('LOGIN_BUTTON')) define('LOGIN_BUTTON', 'Login');
if(!defined('LOGOUT_BUTTON')) define('LOGOUT_BUTTON', 'Log uit');
if(!defined('CHANGE_PASSWORD_BUTTON')) define('CHANGE_PASSWORD_BUTTON', 'Wijzig wachtwoord');
if(!defined('REGISTER_BUTTON')) define('REGISTER_BUTTON', 'Registreer');
/**#@-*/

/**#@+
 * Language constant used by the {@link wantedpages.php wantedpages} action
 */
// wantedpages
if(!defined('SORTING_LEGEND')) define('SORTING_LEGEND', 'Bezig met sorteren ...');
if(!defined('SORTING_NUMBER_LABEL')) define('SORTING_NUMBER_LABEL', 'Bezig met sorteren van #%d:');
if(!defined('SORTING_DESC_LABEL')) define('SORTING_DESC_LABEL', 'aflopend');
if(!defined('OK_BUTTON')) define('OK_BUTTON', '   OK   ');
if(!defined('NO_WANTED_PAGES')) define('NO_WANTED_PAGES', 'Geen `wanted\' pagina\'s. Mooi!'); // @@@ TODO MJH: translate 'wanted'?
/**#@-*/


/* ------------------ 3RD PARTY ------------------ */

/**#@+
 * Language constant used by the {@link fullscreen.php fullscreen} 3rd party MindMap display utility
 */
// fullscreen
if(!defined('CLOSE_WINDOW')) define('CLOSE_WINDOW', 'Sluit Window');
if(!defined('MM_GET_JAVA_PLUGIN_LINK_DESC')) define('MM_GET_JAVA_PLUGIN_LINK_DESC', 'download de nieuwste Java Plug-in hier'); // used in MM_GET_JAVA_PLUGIN
if(!defined('MM_GET_JAVA_PLUGIN')) define('MM_GET_JAVA_PLUGIN', 'dus als het niet werkt, %s.'); // %s - plugin download link
/**#@-*/


/* ------------------ FORMATTERS ------------------ */

/**#@+
 * Language constant used by the {@link wakka.php wakka} formatter
 */
// wakka
if(!defined('GRABCODE_BUTTON')) define('GRABCODE_BUTTON', 'Download');
if(!defined('GRABCODE_BUTTON_TITLE')) define('GRABCODE_BUTTON_TITLE', 'Download %s'); // %s download filename
/**#@-*/


/* ------------------ HANDLERS (PAGE) ------------------ */

/**#@+
 * Language constant used by the {@link acls.php acls} (page) handler
 */
// acls
// TODO: 'translate' DB value '(Public)' when displaying it!
if(!defined('ACLS_UPDATED')) define('ACLS_UPDATED', 'Access control lists aangepast.'); // @@@ TODO MJH: translate ACL?
if(!defined('NO_PAGE_OWNER')) define('NO_PAGE_OWNER', '(Niemand)');
if(!defined('NOT_PAGE_OWNER')) define('NOT_PAGE_OWNER', 'Je bent geen eigenaar van deze pagina.');
if(!defined('PAGE_OWNERSHIP_CHANGED')) define('PAGE_OWNERSHIP_CHANGED', 'Eigenaarschap veranderd naar %s'); // %s - name of new owner
if(!defined('ACLS_LEGEND')) define('ACLS_LEGEND', 'Access Control Lists voor %s'); // %s - name of current page
if(!defined('ACLS_READ_LABEL')) define('ACLS_READ_LABEL', 'Lees ACL:');
if(!defined('ACLS_WRITE_LABEL')) define('ACLS_WRITE_LABEL', 'Schrijf ACL:');
if(!defined('ACLS_COMMENT_READ_LABEL')) define('ACLS_COMMENT_READ_LABEL', 'Opmerkingen lees ACL:');
if(!defined('ACLS_COMMENT_POST_LABEL')) define('ACLS_COMMENT_POST_LABEL', 'Opmerkingen plaats ACL:');
if(!defined('SET_OWNER_LABEL')) define('SET_OWNER_LABEL', 'Zet pagina eigenaar:');
if(!defined('SET_OWNER_CURRENT_OPTION')) define('SET_OWNER_CURRENT_OPTION', '(Huidige Eigenaar)');
if(!defined('SET_OWNER_PUBLIC_OPTION')) define('SET_OWNER_PUBLIC_OPTION', '(Publiekelijk)'); // actual DB value will remain '(Public)' even if this option text is translated!
if(!defined('SET_NO_OWNER_OPTION')) define('SET_NO_OWNER_OPTION', '(Niemand - Bevrijd)'); // @@@ TODO MJH: how to translate 'set free'? what is the context?
if(!defined('ACLS_STORE_BUTTON')) define('ACLS_STORE_BUTTON', 'ACL\'s opslaan');
if(!defined('CANCEL_BUTTON')) define('CANCEL_BUTTON', 'Annuleer');
// - syntax
if(!defined('ACLS_SYNTAX_HEADING')) define('ACLS_SYNTAX_HEADING', 'Syntax:');
if(!defined('ACLS_EVERYONE')) define('ACLS_EVERYONE', 'Iedereen');
if(!defined('ACLS_REGISTERED_USERS')) define('ACLS_REGISTERED_USERS', 'Geregistreerde gebruikers');
if(!defined('ACLS_NONE_BUT_ADMINS')) define('ACLS_NONE_BUT_ADMINS', 'Niemand (behalve beheerders)');
if(!defined('ACLS_ANON_ONLY')) define('ACLS_ANON_ONLY', 'Alleen anonieme gebruikers');
if(!defined('ACLS_LIST_USERNAMES')) define('ACLS_LIST_USERNAMES', 'de gebruiker genaamd %s; vul zoveel gebruiks in als je wilt, een per regel.'); // %s - sample user name
if(!defined('ACLS_NEGATION')) define('ACLS_NEGATION', 'Elk van deze items kan toegang geweigerd worden door er een %s voor te zetten:'); // %s - 'negation' mark
if(!defined('ACLS_DENY_USER_ACCESS')) define('ACLS_DENY_USER_ACCESS', '%s zal geen toegang verleend worden'); // %s - sample user name
if(!defined('ACLS_AFTER')) define('ACLS_AFTER', 'nadat');
if(!defined('ACLS_TESTING_ORDER1')) define('ACLS_TESTING_ORDER1', 'ACLs worden getest in de volgorde die ze zijn opgegeven:');
if(!defined('ACLS_TESTING_ORDER2')) define('ACLS_TESTING_ORDER2', 'Dus zorg ervoor dat %1$s op een aparte regel is opgegeven %2$s gebruikers opgegeven zijn die geweigerd moeten worden, niet ervoor.'); // %1$s - 'all' mark; %2$s - emphasised 'after'
/**#@-*/

/**#@+
 * Language constant used by the {@link backlinks.php backlinks} (page) handler
 */
// backlinks
if(!defined('BACKLINKS_HEADING')) define('BACKLINKS_HEADING', 'Pagina\'s die naar %s verwijzen');
if(!defined('BACKLINKS_NO_PAGES')) define('BACKLINKS_NO_PAGES', 'Er zijn geen backlinks naar deze pagina.');
/**#@-*/

/**#@+
 * Language constant used by the {@link claim.php claim} (page) handler
 */
// claim
if(!defined('USER_IS_NOW_OWNER')) define('USER_IS_NOW_OWNER', 'Je bent nu de eigenaar van deze pagina.');
/**#@-*/

/**#@+
 * Language constant used by the {@link clone.php clone} (page) handler
 */
// clone
if(!defined('ERROR_ACL_WRITE')) define('ERROR_ACL_WRITE', 'Sorry! Je hebt geen schrijftoegang tot %s');
if(!defined('CLONE_VALID_TARGET')) define('CLONE_VALID_TARGET', 'Vul een geldige pagina naam in en een (optionele) wijzigingsnotitie.');
if(!defined('CLONE_LEGEND')) define('CLONE_LEGEND', 'Kopieer %s'); // %s source page name
if(!defined('CLONED_FROM')) define('CLONED_FROM', 'Gekopieerd van %s'); // %s source page name
if(!defined('SUCCESS_CLONE_CREATED')) define('SUCCESS_CLONE_CREATED', '%s is succesvol gemaakt!'); // %s new page name
if(!defined('CLONE_X_TO_LABEL')) define('CLONE_X_TO_LABEL', 'Kopieer als:');
if(!defined('CLONE_EDIT_NOTE_LABEL')) define('CLONE_EDIT_NOTE_LABEL', 'Wijzig notitie:');
if(!defined('CLONE_EDIT_OPTION_LABEL')) define('CLONE_EDIT_OPTION_LABEL', ' Wijzig na het maken');
if(!defined('CLONE_ACL_OPTION_LABEL')) define('CLONE_ACL_OPTION_LABEL', ' Kopieer ACL');
if(!defined('CLONE_BUTTON')) define('CLONE_BUTTON', 'Kopieer');
if(!defined('ERROR_INVALID_PAGENAME')) define('ERROR_INVALID_PAGENAME', 'This page name is invalid. Valid page names must not contain the characters | ? = &lt; &gt; / \' " % or &amp;.');
/**#@-*/

/**#@+
 * Language constant used by the {@link delete.php delete} (page) handler
 */
// delete
if(!defined('ERROR_NO_PAGE_DEL_ACCESS')) define('ERROR_NO_PAGE_DEL_ACCESS', 'Je bent niet bevoegd om deze pagina te verwijderen.');
if(!defined('PAGE_DELETION_HEADER')) define('PAGE_DELETION_HEADER', 'Verwijder %s'); // %s - name of the page
if(!defined('SUCCESS_PAGE_DELETED')) define('SUCCESS_PAGE_DELETED', 'Pagina is verwijderd!');
if(!defined('PAGE_DELETION_CAPTION')) define('PAGE_DELETION_CAPTION', 'Verwijder deze pagina volledig, inclusief alle opmerkingen?');
if(!defined('PAGE_DELETION_DELETE_BUTTON')) define('PAGE_DELETION_DELETE_BUTTON', 'Verwijder Pagina');
if(!defined('PAGE_DELETION_CANCEL_BUTTON')) define('PAGE_DELETION_CANCEL_BUTTON', 'Annuleer');
/**#@-*/

/**#@+
 * Language constant used by the {@link diff.php diff} (page) handler
 */
// diff
if(!defined('ERROR_DIFF_LIBRARY_MISSING')) define('ERROR_DIFF_LIBRARY_MISSING', 'Het benodigde bestand "'.WIKKA_LIBRARY_PATH.'/diff.lib.php" kon niet gevonden worden. Zorg dat het bestand bestaat en in de juiste directory staat!'); //TODO 'Please make sure' should be 'please inform WikiAdmin' - end user can't "make sure"
if(!defined('ERROR_BAD_PARAMETERS')) define('ERROR_BAD_PARAMETERS', 'Er is iets mis met de opties die je hebt opgegeven, het is waarschijnlijk dat een van de versies die je wilt vergelijken verwijderd is.');
if(!defined('DIFF_ADDITIONS_HEADER')) define('DIFF_ADDITIONS_HEADER', 'Toevoegingen:');
if(!defined('DIFF_DELETIONS_HEADER')) define('DIFF_DELETIONS_HEADER', 'Verwijderingen:');
if(!defined('DIFF_NO_DIFFERENCES')) define('DIFF_NO_DIFFERENCES', 'Geen verschillen');
if(!defined('DIFF_FAST_COMPARISON_HEADER')) define('DIFF_FAST_COMPARISON_HEADER', 'Vergelijking van %1$s &amp; %2$s'); // %1$s - link to page A; %2$s - link to page B
if(!defined('DIFF_COMPARISON_HEADER')) define('DIFF_COMPARISON_HEADER', 'Bezig met vergelijken van %2$s met %1$s'); // %1$s - link to page A; %2$s - link to page B (yes, they're swapped!)
if(!defined('DIFF_SAMPLE_ADDITION')) define('DIFF_SAMPLE_ADDITION', 'toevoeging');
if(!defined('DIFF_SAMPLE_DELETION')) define('DIFF_SAMPLE_DELETION', 'verwijdering');
if(!defined('HIGHLIGHTING_LEGEND')) define('HIGHLIGHTING_LEGEND', 'Highlighting Legenda: %1$s %2$s'); // %1$s - sample added text; %2$s - sample deleted text
/**#@-*/

/**#@+
 * Language constant used by the {@link edit.php edit} (page) handler
 */
// edit
if(!defined('ERROR_OVERWRITE_ALERT1')) define('ERROR_OVERWRITE_ALERT1', 'OVERSCHRIJVINGSALARM: Deze pagina is aangepast door iemand anders terwijl je het aan het wijzigen was.');
if(!defined('ERROR_OVERWRITE_ALERT2')) define('ERROR_OVERWRITE_ALERT2', 'Kopieer je veranderingen en wijzig deze pagina opnieuw.');
if(!defined('ERROR_MISSING_EDIT_NOTE')) define('ERROR_MISSING_EDIT_NOTE', 'MISSENDE WIJZIGINGSNOTITIE: Vul een wijzigingsnotitie in!');
if(!defined('ERROR_TAG_TOO_LONG')) define('ERROR_TAG_TOO_LONG', 'Paginanaam te lang! Maximaal %d karakters.'); // %d - maximum page name length
if(!defined('ERROR_NO_WRITE_ACCESS')) define('ERROR_NO_WRITE_ACCESS', 'Je hebt geen schrijftoegang tot deze pagina. Mogelijk moet je [[UserSettings inloggen]] of [[UserSettings een account registreren]] om deze pagina te wijzigen.'); //TODO Distinct links for login and register actions
if(!defined('EDIT_STORE_PAGE_LEGEND')) define('EDIT_STORE_PAGE_LEGEND', 'Pagina opslaan');
if(!defined('EDIT_PREVIEW_HEADER')) define('EDIT_PREVIEW_HEADER', 'Preview');
if(!defined('EDIT_NOTE_LABEL')) define('EDIT_NOTE_LABEL', 'Vul een notitie over je wijziging in'); // label after field, so no colon!
if(!defined('MESSAGE_AUTO_RESIZE')) define('MESSAGE_AUTO_RESIZE', 'Op %s klikken kapt automatisch de paginanaam af naar de juiste grootte'); // %s - rename button text
if(!defined('EDIT_PREVIEW_BUTTON')) define('EDIT_PREVIEW_BUTTON', 'Preview');
if(!defined('EDIT_STORE_BUTTON')) define('EDIT_STORE_BUTTON', 'Opslaan');
if(!defined('EDIT_REEDIT_BUTTON')) define('EDIT_REEDIT_BUTTON', 'Wijzig opnieuw');
if(!defined('EDIT_CANCEL_BUTTON')) define('EDIT_CANCEL_BUTTON', 'Annuleer');
if(!defined('EDIT_RENAME_BUTTON')) define('EDIT_RENAME_BUTTON', 'Hernoem');
if(!defined('ACCESSKEY_PREVIEW')) define('ACCESSKEY_PREVIEW', 'p'); // ideally, should match EDIT_PREVIEW_BUTTON
if(!defined('ACCESSKEY_STORE')) define('ACCESSKEY_STORE', 'o'); // ideally, should match EDIT_STORE_BUTTON
if(!defined('ACCESSKEY_REEDIT')) define('ACCESSKEY_REEDIT', 'w'); // ideally, should match EDIT_REEDIT_BUTTON
if(!defined('SHOWCODE_LINK')) define('SHOWCODE_LINK', 'Bekijk formatting code voor deze pagina'); // @@@ TODO MJH: how to translate 'formatting code'?
if(!defined('SHOWCODE_LINK_TITLE')) define('SHOWCODE_LINK_TITLE', 'Klik om de pagina formatting code te bekijken'); // @@@ TODO 'View page formatting code'
if(!defined('EDIT_COMMENT_TIMESTAMP_CAPTION')) define('EDIT_COMMENT_TIMESTAMP_CAPTION', '(%s)'); // %s timestamp
/**#@-*/

/**#@+
 * Language constant used by the {@link grabcode.php grabcode} (page) handler
 */
// grabcode
if(!defined('ERROR_NO_CODE')) define('ERROR_NO_CODE', 'Sorry, er is geen code om te downloaden.');
/**#@-*/

/**#@+
 * Language constant used by the {@link history.php history} (page) handler
 */
// history
if(!defined('EDITED_ON')) define('EDITED_ON', 'Gewijzigd op %1$s door %2$s'); // %1$s - time; %2$s - user name
if(!defined('HISTORY_PAGE_VIEW')) define('HISTORY_PAGE_VIEW', 'Geschiedenis van recente veranderingen voor %s'); // %s pagename
if(!defined('OLDEST_VERSION_EDITED_ON_BY')) define('OLDEST_VERSION_EDITED_ON_BY', 'De oudste bekende versie van deze pagina is gemaakt op %1$s door %2$s'); // %1$s - time; %2$s - user name
if(!defined('MOST_RECENT_EDIT')) define('MOST_RECENT_EDIT', 'Nieuwste wijziging op %1$s door %2$s'); // %1$s - time; %2$s - user name
if(!defined('HISTORY_MORE_LINK_DESC')) define('HISTORY_MORE_LINK_DESC', 'hier'); // used for alternative history link in HISTORY_MORE
if(!defined('HISTORY_MORE')) define('HISTORY_MORE', 'Volledige geschiedenis van deze pagina kan niet weergegeven worden op een enkele pagina, klik %s om meer te zien.'); // %s alternative history link # @@@ TODO avoid using 'here' ^
/**#@-*/

/**#@+
 * Language constant shared by the {@link processcomment.php processcomment} and {@link show.php show} (page) handlers
 */
// processcomment & show
// - comment buttons
if(!defined('COMMENT_DELETE_BUTTON')) define('COMMENT_DELETE_BUTTON', 'Verwijder');
if(!defined('COMMENT_REPLY_BUTTON')) define('COMMENT_REPLY_BUTTON', 'Reageer');
if(!defined('COMMENT_ADD_BUTTON')) define('COMMENT_ADD_BUTTON', 'Voeg opmerking toe');
if(!defined('COMMENT_NEW_BUTTON')) define('COMMENT_NEW_BUTTON', 'Nieuwe opmerking');
/**#@-*/

/**#@+
 * Language constant used by the {@link processcomment.php processcomment} (page) handler
 */
// processcomment
if(!defined('ERROR_NO_COMMENT_DEL_ACCESS')) define('ERROR_NO_COMMENT_DEL_ACCESS', 'Sorry, je bent niet bevoegd om deze opmerking te verwijderen!');
if(!defined('ERROR_NO_COMMENT_WRITE_ACCESS')) define('ERROR_NO_COMMENT_WRITE_ACCESS', 'Sorry, je bent niet bevoegd om opmerkingen te plaatsen op deze pagina');
if(!defined('ERROR_EMPTY_COMMENT')) define('ERROR_EMPTY_COMMENT', 'Opmerking was leeg -- niet opgeslagen!');
if(!defined('ADD_COMMENT_LABEL')) define('ADD_COMMENT_LABEL', 'Reactie op %s:');
if(!defined('NEW_COMMENT_LABEL')) define('NEW_COMMENT_LABEL', 'Plaats een nieuwe opmerking:');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges_simple.xml.mm.php recentchanges_simple.xml.mm} (page) handler
 */
// recentchanges_simple.xml.mm
if(!defined('FIRST_NODE_LABEL')) define('FIRST_NODE_LABEL', 'Recente Wijzigingen');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges.xml.php recentchanges.xml} (page) handler
 */
// recentchanges.xml
if(!defined('RECENTCHANGES_DESC')) define('RECENTCHANGES_DESC', 'Recente wijzigingen van %s'); // %s - page name
/**#@-*/

/**#@+
 * Language constant shared by the {@link referrers_sites.php referrers_sites}, {@link referrers.php referrers} and {@link review_blacklist.php review_blacklist} (page) handlers
 */
// referrers_sites + referrers + review_blacklist
if(!defined('REFERRERS_PURGE_24_HOURS')) define('REFERRERS_PURGE_24_HOURS', 'laatste 24 uur');
if(!defined('REFERRERS_PURGE_N_DAYS')) define('REFERRERS_PURGE_N_DAYS', 'laatste %d dagen'); // %d number of days
if(!defined('REFERRERS_NO_SPAM')) define('REFERRERS_NO_SPAM', 'Noot aan spammers: Deze pagina is niet geindexeerd door zoekmachines, dus verspil je tijd niet.'); // @@@ TODO MJH: maybe leave untranslated, because spammers less likely to read Dutch :)
if(!defined('REFERRERS_DOMAINS_TO_WIKI_LINK_DESC')) define('REFERRERS_DOMAINS_TO_WIKI_LINK_DESC', 'Bekijk verwijzende domeinen voor de hele Wiki'); // @@@ TODO MJH: Not sure about 'referrer' translation.
if(!defined('REFERRERS_DOMAINS_TO_PAGE_LINK_DESC')) define('REFERRERS_DOMAINS_TO_PAGE_LINK_DESC', 'Bekijk verwijzende domeinen alleen voor %s'); // %s - page name
if(!defined('REFERRERS_URLS_TO_WIKI_LINK_DESC')) define('REFERRERS_URLS_TO_WIKI_LINK_DESC', 'Bekijk verwijzende URL\'s voor de hele Wiki');
if(!defined('REFERRERS_URLS_TO_PAGE_LINK_DESC')) define('REFERRERS_URLS_TO_PAGE_LINK_DESC', 'Bekijk verwijzende URL\'s alleen voor %s'); // %s - page name
if(!defined('REFERRER_BLACKLIST_LINK_DESC')) define('REFERRER_BLACKLIST_LINK_DESC', 'Bekijk de zwarte lijst voor verwijzers');
if(!defined('BLACKLIST_LINK_DESC')) define('BLACKLIST_LINK_DESC', 'Zwarte lijst');
if(!defined('NONE_CAPTION')) define('NONE_CAPTION', 'Geen');
if(!defined('PLEASE_LOGIN_CAPTION')) define('PLEASE_LOGIN_CAPTION', 'Je moet inloggen om verwijzende sites te bekijken.');
/**#@-*/

/**#@+
 * Language constant used by the {@link referrers_sites.php referrers_sites} (page) handler
 */
// referrers_sites
if(!defined('REFERRERS_URLS_LINK_DESC')) define('REFERRERS_URLS_LINK_DESC', 'bekijk een lijst van verschillende URL\'s');
if(!defined('REFERRERS_DOMAINS_TO_WIKI')) define('REFERRERS_DOMAINS_TO_WIKI', 'Domeinen/websites die naar deze wiki linken (%s)'); // %s - link to referrers handler
if(!defined('REFERRERS_DOMAINS_TO_PAGE')) define('REFERRERS_DOMAINS_TO_PAGE', 'Domeinen/websites die linken naar %1$s %2$s (%3$s)'); // %1$s - page link; %2$s - purge time; %3$s - link to referrers handler
/**#@-*/

/**#@+
 * Language constant used by the {@link referrers.php referrers} (page) handler
 */
// referrers
if(!defined('REFERRERS_DOMAINS_LINK_DESC')) define('REFERRERS_DOMAINS_LINK_DESC', 'bekijk een lijst van domeinen');
if(!defined('REFERRERS_URLS_TO_WIKI')) define('REFERRERS_URLS_TO_WIKI', 'Externe pagina\'s die linken naar deze wiki (%s)'); // %s - link to referrers_sites handler
if(!defined('REFERRERS_URLS_TO_PAGE')) define('REFERRERS_URLS_TO_PAGE', 'Externe pagina\'s die linken naar %1$s %2$s (%3$s)'); // %1$s - page link; %2$s - purge time; %3$s - link to referrers_sites handler
/**#@-*/

/**#@+
 * Language constant used by the {@link review_blacklist.php review_blacklist} (page) handler
 */
// review_blacklist
if(!defined('BLACKLIST_HEADING')) define('BLACKLIST_HEADING', 'Zwarte lijst van verwijzingen');
if(!defined('BLACKLIST_REMOVE_LINK_DESC')) define('BLACKLIST_REMOVE_LINK_DESC', 'Verwijder');
if(!defined('STATUS_BLACKLIST_EMPTY')) define('STATUS_BLACKLIST_EMPTY', 'De zwarte lijst is leeg.');
/**#@-*/

/**#@+
 * Language constant used by the {@link revisions.php revisions} (page) handler
 */
// revisions
if(!defined('REVISIONS_CAPTION')) define('REVISIONS_CAPTION', 'Revisies voor %s'); // %s pagename
if(!defined('REVISIONS_NO_REVISIONS_YET')) define('REVISIONS_NO_REVISIONS_YET', 'Er zijn nog geen revisies voor deze pagina');
if(!defined('REVISIONS_SIMPLE_DIFF')) define('REVISIONS_SIMPLE_DIFF', 'Simpele Verschillen');
if(!defined('REVISIONS_MORE_CAPTION')) define('REVISIONS_MORE_CAPTION', 'Er zijn meer revisies die niet hier zijn laten zien, klik op de knop hieronder genaamd %s om deze te bekijken'); // %S - text of REVISIONS_MORE_BUTTON
if(!defined('REVISIONS_RETURN_TO_NODE_BUTTON')) define('REVISIONS_RETURN_TO_NODE_BUTTON', 'Ga terug naar Node / Annuleer'); // @@@ TODO MJH: translate 'Node'?
if(!defined('REVISIONS_SHOW_DIFFERENCES_BUTTON')) define('REVISIONS_SHOW_DIFFERENCES_BUTTON', 'Laat Verschillen Zien');
if(!defined('REVISIONS_MORE_BUTTON')) define('REVISIONS_MORE_BUTTON', 'Volgende...');
/**#@-*/

/**#@+
 * Language constant used by the {@link revisions.xml.php revisions.xml} (page) handler
 */
// revisions.xml
if(!defined('REVISIONS_EDITED_BY')) define('REVISIONS_EDITED_BY', 'Gewijzigd door %s'); // %s user name
if(!defined('HISTORY_REVISIONS_OF')) define('HISTORY_REVISIONS_OF', 'Geschiedenis/revisies van %s'); // %s - page name
/**#@-*/

/**#@+
 * Language constant used by the {@link show.php show} (page) handler
 */
// show
if(!defined('SHOW_RE_EDIT_BUTTON')) define('SHOW_RE_EDIT_BUTTON', 'Wijzig deze oude revisie opnieuw');
if(!defined('SHOW_ASK_CREATE_PAGE_CAPTION')) define('SHOW_ASK_CREATE_PAGE_CAPTION', 'Deze pagina bestaat nog niet. Als je het wilt maken, klik dan op %s'); // %s - page create link // @@@ TODO MJH: can't use page create link in sentence directly, because word form changes in this context (maak -> maken)
if(!defined('SHOW_OLD_REVISION_CAPTION')) define('SHOW_OLD_REVISION_CAPTION', 'Dit is een oude revisie van %1$s van %2$s.'); // %1$s - page link; %2$s - timestamp
if(!defined('COMMENTS_CAPTION')) define('COMMENTS_CAPTION', 'Opmerkingen');
if(!defined('DISPLAY_COMMENTS_LABEL')) define('DISPLAY_COMMENTS_LABEL', 'Laat opmerkingen zien');
if(!defined('DISPLAY_COMMENT_LINK_DESC')) define('DISPLAY_COMMENT_LINK_DESC', 'Laat opmerking zien');
if(!defined('DISPLAY_COMMENTS_EARLIEST_LINK_DESC')) define('DISPLAY_COMMENTS_EARLIEST_LINK_DESC', 'Vroegsten eerst');
if(!defined('DISPLAY_COMMENTS_LATEST_LINK_DESC')) define('DISPLAY_COMMENTS_LATEST_LINK_DESC', 'Nieuwsten eerst');
if(!defined('DISPLAY_COMMENTS_THREADED_LINK_DESC')) define('DISPLAY_COMMENTS_THREADED_LINK_DESC', 'Threaded'); // @@@ TODO MJH: how to translate 'Threaded' (also see above somewhere)
if(!defined('HIDE_COMMENTS_LINK_DESC')) define('HIDE_COMMENTS_LINK_DESC', 'Verberg opmerkingen');
if(!defined('STATUS_NO_COMMENTS')) define('STATUS_NO_COMMENTS', 'Er zijn geen opmerkingen op deze pagina.');
if(!defined('STATUS_ONE_COMMENT')) define('STATUS_ONE_COMMENT', 'Er is een opmerkingen op deze pagina.');
if(!defined('STATUS_SOME_COMMENTS')) define('STATUS_SOME_COMMENTS', 'Er zijn %d opmerkingen op deze pagina.'); // %d - number of comments
if(!defined('COMMENT_TIME_CAPTION')) define('COMMENT_TIME_CAPTION', '%s'); // %s comment time
/**#@-*/

/**#@+
 * Language constant used by the {@link showcode.php showcode} (page) handler
 */
// showcode
if(!defined('SOURCE_HEADING')) define('SOURCE_HEADING', 'Formatting code voor %s'); // %s - page link // @@@ TODO MJH: translate 'formatting code'?
if(!defined('SHOW_RAW_LINK_DESC')) define('SHOW_RAW_LINK_DESC', 'bekijk alleen broncode');
/**#@-*/

/* ------------------ LIBS ------------------*/

/**#@+
 * Language constant used by the {@link Wakka.class.php Wakka class} (the Wikka core containing most methods)
 */
// Wakka.class
if(!defined('QUERY_FAILED')) define('QUERY_FAILED', 'Vraag gefaald:'); // @@@ TODO MJH: correct translation of 'query'?
if(!defined('REDIR_DOCTITLE')) define('REDIR_DOCTITLE', 'Verwezen naar %s'); // %s - target page
if(!defined('REDIR_LINK_DESC')) define('REDIR_LINK_DESC', 'deze link'); // used in REDIR_MANUAL_CAPTION
if(!defined('REDIR_MANUAL_CAPTION')) define('REDIR_MANUAL_CAPTION', 'Als je browser je niet verwijst, volg dan %s'); // %s target page link
if(!defined('CREATE_THIS_PAGE_LINK_TITLE')) define('CREATE_THIS_PAGE_LINK_TITLE', 'Maak deze pagina');
if(!defined('ACTION_UNKNOWN_SPECCHARS')) define('ACTION_UNKNOWN_SPECCHARS', 'Onbekende actie; de actienaam mag geen speciale karakters bevatten.');
if(!defined('ACTION_UNKNOWN')) define('ACTION_UNKNOWN', 'Onbekende actie "%s"'); // %s - action name
if(!defined('HANDLER_UNKNOWN_SPECCHARS')) define('HANDLER_UNKNOWN_SPECCHARS', 'Onbekende handler; de handlernaam mag geen speciale karakters bevatten.');
if(!defined('HANDLER_UNKNOWN')) define('HANDLER_UNKNOWN', 'Sorry, %s is een onbekende handler.'); // %s handler name
if(!defined('FORMATTER_UNKNOWN_SPECCHARS')) define('FORMATTER_UNKNOWN_SPECCHARS', 'Onbekende formatter; de formatternaam mag geen speciale karakters bevatten.');
if(!defined('FORMATTER_UNKNOWN')) define('FORMATTER_UNKNOWN', 'Formatter "%s" niet gevonden'); // %s formatter name
/**#@-*/

/* ------------------ SETUP ------------------ */
/**#@+
 * Language constant used by the {@link index.php setup} program (and several included files)
 */
// @@@ later....
/**#@-*/

?>
