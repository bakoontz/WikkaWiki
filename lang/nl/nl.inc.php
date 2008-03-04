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
define('WIKKA_ERROR_SETUP_FILE_MISSING', 'Een bestand van het installatie- / upgradeprogramma is niet gevonden. Installeer Wikka opnieuw!');
define('WIKKA_ERROR_MYSQL_ERROR', 'MySQL fout: %d - %s');	// %d - error number; %s - error text
define('WIKKA_ERROR_CAPTION', 'Fout');
define('WIKKA_ERROR_ACL_READ', 'Je bent niet bevoegd om deze pagina te bekijken.');
define('WIKKA_ERROR_ACL_READ_SOURCE', 'Je bent niet bevoegd om de broncode van deze pagina te bekijken.');
define('WIKKA_ERROR_ACL_READ_INFO', 'Je bent niet bevoegd om deze informatie te bekijken.');
define('WIKKA_ERROR_LABEL', 'Fout');
define('WIKKA_ERROR_PAGE_NOT_EXIST', 'Sorry, pagina %s bestaat niet.'); // %s (source) page name
define('WIKKA_ERROR_EMPTY_USERNAME', 'Vul je gebruikersnaam in!');
define('WIKKA_ERROR_INVALID_PAGE_NAME', 'De paginanaam %s is ongeldig. Geldige pagina namen moeten beginnen met een hoofdletter, mogen alleen letters and nummers bevatten en moeten in het CamelCase formaat zijn.'); // %s - page name
define('WIKKA_ERROR_PAGE_ALREADY_EXIST', 'Sorry, deze pagina bestaat al.');
define('WIKKA_LOGIN_LINK_DESC', 'login');
define('WIKKA_MAINPAGE_LINK_DESC', 'hoofdpagina');
define('WIKKA_NO_OWNER', 'Niemand');
define('WIKKA_NOT_AVAILABLE', 'nvt');
define('WIKKA_NOT_INSTALLED', 'niet geinstalleerd');
define('WIKKA_ANONYMOUS_USER', 'anoniem'); // 'name' of non-registered user
define('WIKKA_UNREGISTERED_USER', 'ongeregisteerde gebruiker'); // alternative for 'anonymous' @@@ make one string only?
define('WIKKA_ANONYMOUS_AUTHOR_CAPTION', '('.WIKKA_UNREGISTERED_USER.')'); // @@@ or WIKKA_ANONYMOUS_USER
define('WIKKA_SAMPLE_WIKINAME', 'JanSmit'); // must be a CamelCase name
define('WIKKA_HISTORY', 'geschiedenis');
define('WIKKA_REVISIONS', 'revisies');
define('WIKKA_REV_WHEN_BY_WHO', '%1$s door %2$s'); // %1$s - timestamp; %2$s - user name
define('WIKKA_NO_PAGES_FOUND', 'Geen pagina\'s gevonden.');
define('WIKKA_PAGE_OWNER', 'Eigenaar: %s'); // %s - page owner name or link
define('WIKKA_COMMENT_AUTHOR_DIVIDER', ', opmerking door '); //TODo check if we can construct a single phrase here
define('WIKKA_PAGE_EDIT_LINK_DESC', 'wijzig');
define('WIKKA_PAGE_CREATE_LINK_DESC', 'maak');
define('WIKKA_PAGE_EDIT_LINK_TITLE', 'Klik om %s te wijzigen'); // %s page name @@@ 'Edit %s'
define('WIKKA_BACKLINKS_LINK_TITLE', 'Laat een lijst van pagina\'s zien die linken naar %s'); // %s page name
define('WIKKA_JRE_LINK_DESC', 'Java Runtime Environment'); // @@@ TODO MJH: translate this?
define('WIKKA_NOTE', 'NOOT:');
define('WIKKA_JAVA_PLUGIN_NEEDED', 'Java 1.4.1 (of later) Plug-in is nodig om deze applet te draaien,');
/**#@-*/


/*  ------------------ CORE ------------------  */

/**#@+
 * Language constant for the core {@link wikka.php wikka} program
 */
// wikka
define('ERROR_WAKKA_LIBRARY_MISSING', 'Het benodigde bestand "%s" kon niet gevonden worden. Om Wikka te gebruiken, zorg dathet bestand bestaat en in de juiste directory staat!');	// %s - configured path to core class
define('ERROR_NO_DB_ACCESS', 'Fout: Kon niet verbinden met de database.');
define('ERROR_RETRIEVAL_MYSQL_VERSION', 'Kon MySQL versie niet bepalen');
define('ERROR_WRONG_MYSQL_VERSION', 'Wikka heeft MySQL %s of hoger nodig!');	// %s - version number
define('STATUS_WIKI_UPGRADE_NOTICE', 'Deze site wordt op dit moment ge-upgrade. Probeer het later opnieuw.');
define('STATUS_WIKI_UNAVAILABLE', 'De wiki is op dit moment niet beschikbaar.');
define('PAGE_GENERATION_TIME', 'Pagina is gegenereerd in %.4f seconden'); // %.4f - page generation time
define('ERROR_HEADER_MISSING', 'Een header template kon niet gevonden worden. Zorg dat een bestand genaamd <code>header.php</code> in de templates directory staat.'); //TODO Make sure this message matches any filename/folder change
define('ERROR_FOOTER_MISSING', 'Een footer template kon niet gevonden worden. Zorg dat een bestand genaamd <code>footer.php</code> in the templates directory staat.'); //TODO Make sure this message matches any filename/folder change

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
define('RSS_REVISIONS_TITLE', '%1$s: revisies voor %2$s');	// %1$s - wiki name; %2$s - current page name
define('RSS_RECENTCHANGES_TITLE', '%s: recentelijk gewijzigde pagina\'s');	// %s - wiki name
define('YOU_ARE', 'Jij bent %s'); // %s - name / ip of the user.
/**#@-*/

/**#@+
 * Language constant used by the {@link footer.php footer} template
 */
// footer
define('FOOTER_PAGE_EDIT_LINK_DESC', 'Wijzig pagina');
define('PAGE_HISTORY_LINK_TITLE', 'Klik om recentelijke wijzigingen van deze pagina te bekijken.'); // @@@ TODO 'View recent edits to this page'
define('PAGE_HISTORY_LINK_DESC', 'Pagina Geschiedenis');
define('PAGE_REVISION_LINK_TITLE', 'Klik om een lijst van recentelijke revisies van deze pagina te bekijken.'); // @@@ TODO 'View recent revisions list for this page'
define('PAGE_REVISION_XML_LINK_TITLE', 'Klik om een lijst van recentelijke revisies van deze pagina te bekijken.'); // @@@ TODO 'View recent revisions list for this page'
define('PAGE_ACLS_EDIT_LINK_DESC', 'Wijzig ACLs');
define('PAGE_ACLS_EDIT_ADMIN_LINK_DESC', '('.PAGE_ACLS_EDIT_LINK_DESC.')');
define('PUBLIC_PAGE', 'Publieke pagina');
define('USER_IS_OWNER', 'Dit is een pagina van jou.');
define('TAKE_OWNERSHIP', 'Word eigenaar'); // @@@ TODO MJH: need better translation of 'Take Ownership'
define('REFERRERS_LINK_TITLE', 'Klik om een lijst van URL\'s te bekijken die naar deze pagina verwijzen.'); // @@@ TODO 'View a list of URLs referring to this page'
define('REFERRERS_LINK_DESC', 'Verwijzers'); // @@@ TODO MJH: good translation of 'referrers'?
define('QUERY_LOG', 'Vraag logboek:'); // @@@ TODO MJH: good translation of 'Query log'?
define('SEARCH_LABEL', 'Zoek:');
/**#@-*/


/*  ------------------ ACTIONS  ------------------  */

/**#@+
 * Language constant used by the {@link calendar.php calendar} action
 */
// calendar
define('FMT_SUMMARY', 'Kalender voor %s');	// %s - ???@@@
define('TODAY', 'vandaag');
/**#@-*/

/**#@+
 * Language constant used by the {@link category.php category} action
 */
// category
define('ERROR_NO_PAGES', 'Sorry, geen items gevonden voor %s');	// %s - ???@@@
define('PAGES_BELONGING_TO', 'De volgende %1$d pagina(\'s) zijn van %2$s'); // %1$d number found; %2$s category
/**#@-*/

/**#@+
 * Language constant used by the {@link color.php color} action
 */
// color
define('ERROR_NO_TEXT_GIVEN', 'Er is geen tekst om te highlighten!'); // @@@ TODO MJH: translate 'highlight'?
define('ERROR_NO_COLOR_SPECIFIED', 'Sorry, maar je hebt geen highlightkleur gekozen!'); // @@@ TODO MJH: translate 'highlight'?
/**#@-*/

/**#@+
 * Language constant used by the {@link contact.php contact} action
 */
// contact
define('SEND_FEEDBACK_LINK_TITLE', 'Stuur ons je feedback');
define('SEND_FEEDBACK_LINK_TEXT', 'Contact');
/**#@-*/

/**#@+
 * Language constant used by the {@link countowned.php countowned} action
 */
// countowned
define('DISPLAY_MYPAGES_LINK_TITLE', 'Laat een lijst zien van jouw pagina\'s.');
/**#@-*/

/**#@+
 * Language constant used by the {@link countpages.php countpages} action
 */
// countpages
define('INDEX_LINK_TITLE', 'Laat een alfabetische pagina index zien.');
/**#@-*/

/**#@+
 * Language constant used by the {@link dbinfo.php dbinfo} action
 */
// dbinfo
define('HD_DBINFO','Database Informatie');
define('HD_DBINFO_DB','Database');
define('HD_DBINFO_TABLES','Tabellen');
define('HD_DB_CREATE_DDL','DDL om database %s te maken:');				# %s will hold database name
define('HD_TABLE_CREATE_DDL','DDL om tabel %s te maken:');				# %s will hold table name
define('TXT_INFO_1','Dit programma levert informatie over de database(s) en tabellen in je systeem.');
define('TXT_INFO_2',' Afhankelijke van permissies van de Wikka database gebruiker, zijn niet alle databases of tabellen zichtbaar.');
define('TXT_INFO_3',' Waar een DDL is opgegeven, omvat dit alles wat nodig is om exact dezelfde database en tabel definities opnieuw te maken,');
define('TXT_INFO_4',' inclusief defaults die niet expliciet zijn opgegeven.');
define('FORM_SELDB_LEGEND','Databases');
define('FORM_SELTABLE_LEGEND','Tabellen');
define('FORM_SELDB_OPT_LABEL','Selecteer een database:');
define('FORM_SELTABLE_OPT_LABEL','Selecteer een tabel:');
define('FORM_SUBMIT_SELDB','Selecteer');
define('FORM_SUBMIT_SELTABLE','Selecteer');
define('MSG_ONLY_ADMIN','Sorry, alleen beheerders kunnen database informatie bekijken.');
define('MSG_SINGLE_DB','Informatie over de <tt>%s</tt> database.');			# %s will hold database name
define('MSG_NO_TABLES','Geen tabellen gevonden in de <tt>%s</tt> database. Je MySQL gebruiker  heeft misschien niet genoeg  privileges om de database te benaderen.');		# %s will hold database name
define('MSG_NO_DB_DDL','DDL om <tt>%s</tt> te maken kon niet opgehaald worden.');	# %s will hold database name
define('MSG_NO_TABLE_DDL','DDL om <tt>%s</tt> te maken kon niet opgehaald worden.');# %s will hold table name
/**#@-*/

/**#@+
 * Language constant used by the {@link emailpassword.php emailpassword} action
 */
// emailpassword
define('PW_FORGOTTEN_HEADING', 'Wachtwoordherinnering');
define('PW_CHK_SENT', 'Een wachtwoordherinnering is gestuurd naar %s\'s opgeslagen email address.'); // %s - username
define('PW_FORGOTTEN_MAIL', 'Hallo, %1$s\n\n\nIemand vroeg ons om een wachtwoord herinneren te sturen naar dit emailadresom in te loggen op %2$s. Als je deze herinnering niet aangevraagd hebt, negeer dan deze email. -- Je hoeft geen actie te ondernemen. -- Je wachtwoord zal onveranderd blijven.\n\nJe WikiNaam: %1$s \nWachtwoordherinnering: %3$s \nURL: %4$s \n\nVergeet niet om je wachtwoord te veranderen direct nadat je ingelogd bent.'); // %1$s - username; %2$s - wiki name; %3$s - md5 sum of pw; %4$s - login url of the wiki
define('PW_FORGOTTEN_MAIL_REF', 'Wachtwoordherinnering voor %s'); // %s - wiki name
define('PW_FORM_TEXT', 'Geef je WikiNaam en een wachtwoordherinnering zal gestuurd worden naar je opgeslagen emailadres.');
define('PW_FORM_FIELDSET_LEGEND', 'Je WikiNaam:');
define('ERROR_UNKNOWN_USER', 'Je hebt een niet-bestaande gebruiker opgegeven!');
#define('ERROR_MAIL_NOT_SENT', 'An error occurred while trying to send the password. Outgoing mail might be disabled. Please contact your server administrator.');
define('ERROR_MAIL_NOT_SENT', 'Een fout is opgetreden tijdens het versturen van het wachtwoord. Uitgaande mail is misschien niet geactiveerd. Probeer je wikibeheerder te bereiken door een pagina opmerking te plaatsen.');
define('BUTTON_SEND_PW', 'Verstuur herinnering');
define('USERSETTINGS_REF', 'Ga terug naar de %s pagina.'); // %s - UserSettings link
/**#@-*/

/**#@+
 * Language constant used by the {@link feedback.php feedback} action
 */
// feedback
define('ERROR_EMPTY_NAME', 'Vul je naam in');
define('ERROR_INVALID_EMAIL', 'Vul een geldig emailadres in');
define('ERROR_EMPTY_MESSAGE', 'Vul tekst in');
define('ERROR_FEEDBACK_MAIL_NOT_SENT', 'Sorry, Een fout is opgetreden tijdens het versturen van je email. Uitgaande mail is misschien niet geactiveerd. Probeer een andere manier om %s te bereiken, bijvoorbeeld door een pagina opmerking te plaatsen.'); // %s - name of the recipient
define('FEEDBACK_FORM_LEGEND', 'Neem contact op met %s'); //%s - wikiname of the recipient
define('FEEDBACK_NAME_LABEL', 'Je naam:');
define('FEEDBACK_EMAIL_LABEL', 'Je email:');
define('FEEDBACK_MESSAGE_LABEL', 'Je bericht:');
define('FEEDBACK_SEND_BUTTON', 'Verstuur');
define('FEEDBACK_SUBJECT', 'Feedback van %s'); // %s - name of the wiki
define('SUCCESS_FEEDBACK_SENT', 'Bedankt voor je feedback, %s! Je bericht is gestuurd.'); //%s - name of the sender
/**#@-*/

/**#@+
 * Language constant used by the {@link files.php files} action
 */
// files
define('ERROR_UPLOAD_DIRECTORY_NOT_WRITABLE', 'Zorg dat de server schrijftoegang heeft op de directory genaamd %s.'); // %s Upload folder ref #89
define('ERROR_UPLOAD_DIRECTORY_NOT_READABLE', 'Zorg dat de server leestoegang heeft op de dirctory genaamd %s.'); // %s Upload folder ref #89
define('ERROR_NONEXISTENT_FILE', 'Sorry, een bestand genaamd %s bestaat niet.'); // %s - file name ref
define('ERROR_FILE_UPLOAD_INCOMPLETE', 'Bestandsupload incompleet! Probeer het opnieuw.');
define('ERROR_UPLOADING_FILE', 'Er is een fout opgetreden tijdens het uploaden van je bestand.');
define('ERROR_FILE_ALREADY_EXISTS', 'Sorry, een bestand genaamd %s bestaat al.'); // %s - file name ref
define('ERROR_EXTENSION_NOT_ALLOWED', 'Sorry, bestanden met deze extensies zijn niet toegestaan.');
define('ERROR_FILE_TOO_BIG', 'Te uploaden bestand is te groot. Maximaal toegestane grootte is %s.'); // %s - allowed filesize
define('ERROR_NO_FILE_SELECTED', 'Geen bestand geselecteerd.');
define('ERROR_FILE_UPLOAD_IMPOSSIBLE', 'Bestandsupload onmogelijk door verkeerd geconfiguurde server.');
define('SUCCESS_FILE_UPLOADED', 'Bestand is successvol geupload.');
define('FILE_TABLE_CAPTION', 'Bijlages');
define('FILE_TABLE_HEADER_NAME', 'Bestand');
define('FILE_TABLE_HEADER_SIZE', 'Grootte');
define('FILE_TABLE_HEADER_DATE', 'Laats veranderd');
define('FILE_UPLOAD_FORM_LEGEND', 'Voeg nieuwe bijlage toe:');
define('FILE_UPLOAD_FORM_LABEL', 'Bestand:');
define('FILE_UPLOAD_FORM_BUTTON', 'Upload');
define('DOWNLOAD_LINK_TITLE', 'Download %s'); // %s - file name
define('DELETE_LINK_TITLE', 'Verwijder %s'); // %s - file name
define('NO_ATTACHMENTS', 'Deze pagina heeft geen bijlage.');
/**#@-*/

/**#@+
 * Language constant used by the {@link googleform.php googleform} action
 */
// googleform
define('GOOGLE_BUTTON', 'Google');
/**#@-*/

/**#@+
 * Language constant used by the {@link highscores.php highscores} action
 */
// include
define('HIGHSCORES_LABEL_EDITS', 'wijzigingen');
define('HIGHSCORES_LABEL_COMMENTS', 'opmerkingen');
define('HIGHSCORES_LABEL_PAGES', 'eigen pagina\'s');
define('HIGHSCORES_CAPTION', 'Top %1$s bijdrager(s) op aantal %2$s'); 
define('HIGHSCORES_HEADER_RANK', 'rank');
define('HIGHSCORES_HEADER_USER', 'gebruiker');
define('HIGHSCORES_HEADER_PERCENTAGE', 'percentage');
/**#@-*/

/**#@+
 * Language constant used by the {@link include.php include} action
 */
// include
define('ERROR_CIRCULAR_REFERENCE', 'Circulaire referentie ontdekt!');
/**#@-*/

/**#@+
 * Language constant used by the {@link lastedit.php lastedit} action
 */
// lastedit
define('LASTEDIT_DESC', 'Laatst gewijzigd door %s'); // %s user name
define('LASTEDIT_DIFF_LINK_TITLE', 'Laat verschillen met laatste revisie zien.');
/**#@-*/

/**#@+
 * Language constant used by the {@link lastusers.php lastusers} action
 */
// lastusers
define('LASTUSERS_CAPTION', 'Recentlijk geregistreerde gebruikers');
define('SIGNUP_DATE_TIME', 'Aanmeldingsdatum/-tijd');
define('NAME_TH', 'Gebruikersnaam');
define('OWNED_PAGES_TH', 'Eigen pages');
define('SIGNUP_DATE_TIME_TH', 'Aanmeldingsdatum/-tijd');
/**#@-*/

/**#@+
 * Language constant used by the {@link mindmap.php mindmap} action
 */
// mindmap
define('MM_JRE_INSTALL_REQ', 'Installeer een %s op je computer.'); // %s - JRE install link
define('MM_DOWNLOAD_LINK_DESC', 'Download deze MindMap');
define('MM_EDIT', 'Use %s to edit it'); // %s - link to freemind project
define('MM_FULLSCREEN_LINK_DESC', 'Open fullscreen');
define('ERROR_INVALID_MM_SYNTAX', 'Fout: ongeldige MindMap actie syntax.');
define('PROPER_USAGE_MM_SYNTAX', 'Geldig gebruik: %1$s of %2$s'); // %1$s - syntax sample 1; %2$s - syntax sample 2
/**#@-*/

/**#@+
 * Language constant used by the {@link mychanges.php mychanges} action
 */
// mychanges
define('NO_PAGES_EDITED', 'Je hebt nog geen pagina\'s gewijzigd.');
define('MYCHANGES_ALPHA_LIST', "Dit is een lijst van pagina's die je gewijzigd hebt, met de tijden van je laatste wijziging.");
define('MYCHANGES_DATE_LIST', "Dit is een lijst van pagina's die je gewijzigd hebt, gesorteerd op de tijd van je laatste wijziging.");
define('ORDER_DATE_LINK_DESC', 'sorteer op datum');
define('ORDER_ALPHA_LINK_DESC', 'sorteer alfabetisch');
define('MYCHANGES_NOT_LOGGED_IN', "Je bent niet ingelogd, daarom kon de lijst van pagina\'s die je gewijzigd hebt niet opgehaald worden.");
/**#@-*/

/**#@+
 * Language constant used by the {@link mypages.php mypages} action
 */
// mypages
define('OWNED_PAGES_TXT', 'Dit is de lijst van jouw pagina\'s.');
define('OWNED_NO_PAGES', 'Je hebt geen eigen pagina\'s.');
define('OWNED_NOT_LOGGED_IN', "Je bent niet ingelogd, daarom kon de lijst van jouw pagina\'s niet opgehaald worden.");
/**#@-*/

/**#@+
 * Language constant used by the {@link newpage.php newpage} action
 */
// newpage
define('NEWPAGE_CREATE_LEGEND', 'Maak een nieuwe pagina');
define('NEWPAGE_CREATE_BUTTON', 'Maak');
/**#@-*/

/**#@+
 * Language constant used by the {@link orphanedpages.php orphanedpages} action
 */
// orphanedpages
define('NO_ORPHANED_PAGES', 'Geen ongerefereerde pagina\'s. Goed!'); // @@@ TODO MJH: orphaned as in no parents or no references?

/**#@+
 * Language constant used by the {@link ownedpages.php ownedpages} action
 */
// ownedpages
define('OWNEDPAGES_COUNTS', 'Op deze Wiki zijn %1$s van de %2$s pagina\'s van jou.'); // %1$s - number of pages owned; %2$s - total number of pages
define('OWNEDPAGES_PERCENTAGE', 'Dat betekent dat %s van jou is.'); // %s percentage of pages owned
/**#@-*/

/**#@+
 * Language constant used by the {@link pageindex.php pageindex} action
 */
// pageindex
define('PAGEINDEX_HEADING', 'Pagina Index');
define('PAGEINDEX_CAPTION', 'Dit is an alfabetische lijst van pages die je op deze server kunt lezen.');
define('PAGEINDEX_OWNED_PAGES_CAPTION', 'Items gemarkeerd met een * zijn je eigen pagina\'s.');
define('PAGEINDEX_ALL_PAGES', 'Alle');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges.php recentchanges} action
 */
// recentchanges
define('RECENTCHANGES_HEADING', 'Recentelijk gewijzigde pagina\'s');
define('REVISIONS_LINK_TITLE', 'Bekijk de lijst van recente revisies voor %s'); // %s - page name
define('HISTORY_LINK_TITLE', 'Bekijk de wijzigingsgeschiedenis van %s'); // %s - page name
define('WIKIPING_ENABLED', 'WikiPing actief: Veranderingen op deze wiki worden verzonden naar %s'); // %s - link to wikiping server
define('RECENTCHANGES_NONE_FOUND', 'Er zijn geen recentelijk gewijzigde pagina\'s.');
define('RECENTCHANGES_NONE_ACCESSIBLE', 'Er zijn geen recentelijk gewijzigde pagina\'s waar je toegang tot hebt.');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentcomments.php recentcomments} action
 */
// recentcomments
define('RECENTCOMMENTS_HEADING', 'Recentelijke opmerkingen.');
define('RECENTCOMMENTS_TIMESTAMP_CAPTION', '%s'); // %s - timestamp
define('RECENTCOMMENTS_NONE_FOUND', 'Er zijn geen recentelijke opmerkingen.');
define('RECENTCOMMENTS_NONE_ACCESSIBLE', 'Er zijn geen recentelijke opmerkingen waar je toegang tot hebt.');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentlycommented.php recentlycommented} action
 */
// recentlycommented
define('RECENTLYCOMMENTED_HEADING', 'Pagina\'s met recentelijke opmerkingen.');
define('RECENTLYCOMMENTED_NONE_FOUND', 'Er zijn geen pagina\'s met recentelijke opmerkingen.');
define('RECENTLYCOMMENTED_NONE_ACCESSIBLE', 'Er zijn geen pagina\'s met recentelijke opmerkingen waar je toegang tot hebt.');
/**#@-*/

/**#@+
 * Language constant used by the {@link system.php system} action
 */
// system
define('SYSTEM_HOST_CAPTION', '(%s)'); // %s - host name
define('WIKKA_STATUS_NOT_AVAILABLE', 'nvt');
/**#@-*/

/**#@+
 * Language constant shared by the {@link textsearch.php textsearch} and {@link textsearchexpanded.php textsearchexpanded} actions
 */
// textsearch & textsearchexpanded
define('SEARCH_FOR', 'Zoek naar');
define('SEARCH_ZERO_MATCH', 'Geen matches gevonden');
define('SEARCH_ONE_MATCH', 'Een match gevonden');
define('SEARCH_N_MATCH', '%d matches gevonden'); // %d - number of hits
define('SEARCH_RESULTS', 'Zoekresultaten: <strong>%1$s</strong> voor <strong>%2$s</strong>'); # %1$s: n matches for | %2$s: search term
define('SEARCH_NOT_SURE_CHOICE', 'Niet zeker welke pagina te kiezen?');
define('SEARCH_EXPANDED_LINK_DESC', 'Zoek Met Uitgebreide Tekst'); // search link description
define('SEARCH_TRY_EXPANDED', 'Probeer %s welke omringende tekst weergeeft.'); // %s expanded search link
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
define('SEARCH_TIPS', 'Zoek Tips:');
define('SEARCH_WORD_1', 'appel');
define('SEARCH_WORD_2', 'banaan');
define('SEARCH_WORD_3', 'sap');
define('SEARCH_WORD_4', 'macintosh');
define('SEARCH_WORD_5', 'enkele');
define('SEARCH_WORD_6', 'woorden');
define('SEARCH_PHRASE',sprintf('%s %s',SEARCH_WORD_5,SEARCH_WORD_6));
define('SEARCH_TARGET_1', 'Vind pagina\'s die minstens een van de twee woorden bevatten.');
define('SEARCH_TARGET_2', 'Vind pagina\'s die beide woorden bevatten.');
define('SEARCH_TARGET_3',sprintf("Vind pagina\'s die het woord '%1\$s' bevatten, maar niet '%2\$s'.",SEARCH_WORD_1,SEARCH_WORD_4));
define('SEARCH_TARGET_4',"Vind pagina\'s die woorden zoals 'appel', 'appels', 'appelmoes', of 'appelscha' bevatten."); // make sure target words all *start* with SEARCH_WORD_1
define('SEARCH_TARGET_5',sprintf("Vind pagina\'s die de exacte zinsnede '%1\$s' bevatten (bijvoorbeeld, pagina\'s die '%1\$s van wijsheid' maar niet '%2\$s irrelevante %3\$s').",SEARCH_PHRASE,SEARCH_WORD_5,SEARCH_WORD_6));
/**#@-*/

/**#@+
 * Language constant used by the {@link usersettings.php usersettings} action
 */
// usersettings
// - error messages
define('ERROR_EMPTY_USERNAME', 'Vul je gebruikersnaam in.');
define('ERROR_NONEXISTENT_USERNAME', 'Sorry, deze gebruikersnaam bestaat niet.'); // @@@ too specific
define('ERROR_RESERVED_PAGENAME', 'Sorry, deze is naam is gereserveerd voor een pagina. Kies een andere naam.');
define('ERROR_WIKINAME', 'Gebruikersnaam er uit zien als een %1$s, bijv. %2$s.'); // %1$s - identifier WikiName; %2$s - sample WikiName
define('ERROR_EMPTY_EMAIL_ADDRESS', 'Vul een emailadres in.');
define('ERROR_INVALID_EMAIL_ADDRESS', 'Dat ziet er niet echt uit als een emailadres.');
define('ERROR_INVALID_PASSWORD', 'Sorry, je hebt het verkeerde wachtwoord ingevuld.');	// @@@ too specific
define('ERROR_INVALID_HASH', 'Sorry, je hebt een verkeerde wachtwoordherinnering ingevuld.');
define('ERROR_INVALID_OLD_PASSWORD', 'Het oude wachtwoord wat je ingevuld hebt is verkeerd.');
define('ERROR_EMPTY_PASSWORD', 'Vul een wachtwoord in.');
define('ERROR_EMPTY_PASSWORD_OR_HASH', 'Vul je wachtwoord of wachtwoordherinnering in.');
define('ERROR_EMPTY_CONFIRMATION_PASSWORD', 'Bevesting je wachtwoord om een nieuw account te registreren.');
define('ERROR_EMPTY_NEW_CONFIRMATION_PASSWORD', 'Bevesting je wachtwoord om een nieuw account te wijzigen.');
define('ERROR_EMPTY_NEW_PASSWORD', 'Je moet ook een nieuw wachtwoord invullen.');
define('ERROR_PASSWORD_MATCH', 'Wachtwoorden zijn niet gelijk.');
define('ERROR_PASSWORD_NO_BLANK', 'Sorry, spaties (en soortgelijken) zijn niet toegestaand in het wachtwoord.');
define('ERROR_PASSWORD_TOO_SHORT', 'Sorry, het wachtwoord moet minstens %d karakters bevatten.'); // %d - minimum password length
define('ERROR_INVALID_INVITATION_CODE', 'Dit is a prive wiki, alleen uitgenodigden kunnen een account registreren! Neem contact op met de beheerder van deze website voor een uitnodigindscode.');
define('ERROR_INVALID_REVISION_DISPLAY_LIMIT', 'Het aantal paginarevisies mag niet groter zijn dan %d.'); // %d - maximum revisions to view
define('ERROR_INVALID_RECENTCHANGES_DISPLAY_LIMIT', 'Het aantal recentlelijk gewijzigde pagina\'s mag niet groter zijn dan %d.'); // %d - maximum changed pages to view
// - success messages
define('SUCCESS_USER_LOGGED_OUT', 'Je hebt jezelf successvol uitgelogd.');
define('SUCCESS_USER_REGISTERED', 'Je hebt jezelf successvol geregistreerd!');
define('SUCCESS_USER_SETTINGS_STORED', 'Gebruikersinstellingen opgeslagen!');
define('SUCCESS_USER_PASSWORD_CHANGED', 'Wachtwoord successvol gewijzigd!');
// - captions
define('NEW_USER_REGISTER_CAPTION', 'Als je je registreerd als een nieuwe gebruiker:');
define('REGISTERED_USER_LOGIN_CAPTION', 'Als je al een account hebt, log dan hier in:');
define('RETRIEVE_PASSWORD_CAPTION', 'Login met je [[%s wachtwoordherinnering]]:'); //%s PasswordForgotten link
define('USER_LOGGED_IN_AS_CAPTION', 'Je bent ingelogd als %s'); // %s user name
// - form legends
define('USER_ACCOUNT_LEGEND', 'Je account');
define('USER_SETTINGS_LEGEND', 'Instellingen');
define('LOGIN_REGISTER_LEGEND', 'Login/Registreer');
define('LOGIN_LEGEND', 'Login');
#define('REGISTER_LEGEND', 'Register'); // @@@ TODO to be used later for register-action
define('CHANGE_PASSWORD_LEGEND', 'Wijzig je wachtwoord');
define('RETRIEVE_PASSWORD_LEGEND', 'Wachtwoord vergeten');
// - form field labels (should end in ':' _unless_ it's a checkbox or radio button option)
define('USERSETTINGS_REDIRECT_AFTER_LOGIN_LABEL', 'Verwijs naar %s na login');	// %s page to redirect to
define('USER_EMAIL_LABEL', 'Je emailadres:');
define('DOUBLECLICK_LABEL', 'Dubbelklik wijzigen:');
define('SHOW_COMMENTS_LABEL', 'Laat opmerkingen standaard zien:');
define('DEFAULT_COMMENT_STYLE_LABEL', 'Standaard opmerkingen stijl');
define('COMMENT_ASC_LABEL', 'Plat (oudste eerst)'); // @@@ TODO MJH: Plat?
define('COMMENT_DEC_LABEL', 'Plat (newest first)'); // @@@ TODO MJH: Plat?
define('COMMENT_THREADED_LABEL', 'Threaded'); // @@@ TODO MJH: gethread? vertakt? boomweergave? boomstructuur?
define('COMMENT_DELETED_LABEL', '[Opmerking verwijderd]');
define('COMMENT_BY_LABEL', 'Opmerking van ');
define('RECENTCHANGES_DISPLAY_LIMIT_LABEL', 'RecentChanges weergave limiet:');
define('PAGEREVISION_LIST_LIMIT_LABEL', 'Pagina revisies weergave limiet:');
define('NEW_PASSWORD_LABEL', 'Je nieuwe wachtwoord:');
define('NEW_PASSWORD_CONFIRM_LABEL', 'Bevestig nieuw wachtwoord:');
define('NO_REGISTRATION', 'Registratie op deze wiki is uitgeschakeld.');
define('PASSWORD_LABEL', 'Wachtwoord (%s+ karakters):'); // %s minimum number of characters
define('CONFIRM_PASSWORD_LABEL', 'Bevestig wachtwoord:');
define('TEMP_PASSWORD_LABEL', 'Wachtwoordherinnering:');
define('INVITATION_CODE_SHORT', 'Uitnodigingscode');
define('INVITATION_CODE_LONG', 'Om te registreren, moet je de uitnodiginscode invullen die gestuurd is door de beheerder van deze website.');
define('INVITATION_CODE_LABEL', 'Je %s:'); // %s - expanded short invitation code prompt
define('WIKINAME_SHORT', 'WikiNaam');
define('WIKINAME_LONG',sprintf('Een WikiNaam wordt gevormd door twee of meer woorden die beginnen met een hoofdletter, zonder spaties, bv. %s',WIKKA_SAMPLE_WIKINAME));
define('WIKINAME_LABEL', 'Je %s:'); // %s - expanded short wiki name prompt
// - form options
define('CURRENT_PASSWORD_OPTION', 'Je huidige wachtwoord');
define('PASSWORD_REMINDER_OPTION', 'Wachtwoordherinnering');
// - form buttons
define('UPDATE_SETTINGS_BUTTON', 'Vernieuw Instellingen');
define('LOGIN_BUTTON', 'Login');
define('LOGOUT_BUTTON', 'Log uit');
define('CHANGE_PASSWORD_BUTTON', 'Wijzig wachtwoord');
define('REGISTER_BUTTON', 'Registreer');
/**#@-*/

/**#@+
 * Language constant used by the {@link wantedpages.php wantedpages} action
 */
// wantedpages
define('SORTING_LEGEND', 'Bezig met sorteren ...');
define('SORTING_NUMBER_LABEL', 'Bezig met sorteren van #%d:');
define('SORTING_DESC_LABEL', 'aflopend');
define('OK_BUTTON', '   OK   ');
define('NO_WANTED_PAGES', 'Geen `wanted\' pagina\'s. Mooi!'); // @@@ TODO MJH: translate 'wanted'?
/**#@-*/


/* ------------------ 3RD PARTY ------------------ */

/**#@+
 * Language constant used by the {@link fullscreen.php fullscreen} 3rd party MindMap display utility
 */
// fullscreen
define('CLOSE_WINDOW', 'Sluit Window');
define('MM_GET_JAVA_PLUGIN_LINK_DESC', 'download de nieuwste Java Plug-in hier'); // used in MM_GET_JAVA_PLUGIN
define('MM_GET_JAVA_PLUGIN', 'dus als het niet werkt, %s.'); // %s - plugin download link
/**#@-*/


/* ------------------ FORMATTERS ------------------ */

/**#@+
 * Language constant used by the {@link wakka.php wakka} formatter
 */
// wakka
define('GRABCODE_BUTTON', 'Download');
define('GRABCODE_BUTTON_TITLE', 'Download %s'); // %s download filename
/**#@-*/


/* ------------------ HANDLERS (PAGE) ------------------ */

/**#@+
 * Language constant used by the {@link acls.php acls} (page) handler
 */
// acls
// TODO: 'translate' DB value '(Public)' when displaying it!
define('ACLS_UPDATED', 'Access control lists aangepast.'); // @@@ TODO MJH: translate ACL?
define('NO_PAGE_OWNER', '(Niemand)');
define('NOT_PAGE_OWNER', 'Je bent geen eigenaar van deze pagina.');
define('PAGE_OWNERSHIP_CHANGED', 'Eigenaarschap veranderd naar %s'); // %s - name of new owner
define('ACLS_LEGEND', 'Access Control Lists voor %s'); // %s - name of current page
define('ACLS_READ_LABEL', 'Lees ACL:');
define('ACLS_WRITE_LABEL', 'Schrijf ACL:');
define('ACLS_COMMENT_READ_LABEL', 'Opmerkingen lees ACL:');
define('ACLS_COMMENT_POST_LABEL', 'Opmerkingen plaats ACL:');
define('SET_OWNER_LABEL', 'Zet pagina eigenaar:');
define('SET_OWNER_CURRENT_OPTION', '(Huidige Eigenaar)');
define('SET_OWNER_PUBLIC_OPTION', '(Publiekelijk)'); // actual DB value will remain '(Public)' even if this option text is translated!
define('SET_NO_OWNER_OPTION', '(Niemand - Bevrijd)'); // @@@ TODO MJH: how to translate 'set free'? what is the context?
define('ACLS_STORE_BUTTON', 'ACL\'s opslaan');
define('CANCEL_BUTTON', 'Annuleer');
// - syntax
define('ACLS_SYNTAX_HEADING', 'Syntax:');
define('ACLS_EVERYONE', 'Iedereen');
define('ACLS_REGISTERED_USERS', 'Geregistreerde gebruikers');
define('ACLS_NONE_BUT_ADMINS', 'Niemand (behalve beheerders)');
define('ACLS_ANON_ONLY', 'Alleen anonieme gebruikers');
define('ACLS_LIST_USERNAMES', 'de gebruiker genaamd %s; vul zoveel gebruiks in als je wilt, een per regel.'); // %s - sample user name
define('ACLS_NEGATION', 'Elk van deze items kan toegang geweigerd worden door er een %s voor te zetten:'); // %s - 'negation' mark
define('ACLS_DENY_USER_ACCESS', '%s zal geen toegang verleend worden'); // %s - sample user name
define('ACLS_AFTER', 'nadat');
define('ACLS_TESTING_ORDER1', 'ACLs worden getest in de volgorde die ze zijn opgegeven:');
define('ACLS_TESTING_ORDER2', 'Dus zorg ervoor dat %1$s op een aparte regel is opgegeven %2$s gebruikers opgegeven zijn die geweigerd moeten worden, niet ervoor.'); // %1$s - 'all' mark; %2$s - emphasised 'after'
/**#@-*/

/**#@+
 * Language constant used by the {@link backlinks.php backlinks} (page) handler
 */
// backlinks
define('BACKLINKS_HEADING', 'Pagina\'s die naar %s verwijzen');
define('BACKLINKS_NO_PAGES', 'Er zijn geen backlinks naar deze pagina.');
/**#@-*/

/**#@+
 * Language constant used by the {@link claim.php claim} (page) handler
 */
// claim
define('USER_IS_NOW_OWNER', 'Je bent nu de eigenaar van deze pagina.');
/**#@-*/

/**#@+
 * Language constant used by the {@link clone.php clone} (page) handler
 */
// clone
define('ERROR_ACL_WRITE', 'Sorry! Je hebt geen schrijftoegang tot %s');
define('CLONE_VALID_TARGET', 'Vul een geldige pagina naam in en een (optionele) wijzigingsnotitie.');
define('CLONE_LEGEND', 'Kopieer %s'); // %s source page name
define('CLONED_FROM', 'Gekopieerd van %s'); // %s source page name
define('SUCCESS_CLONE_CREATED', '%s is succesvol gemaakt!'); // %s new page name
define('CLONE_X_TO_LABEL', 'Kopieer als:');
define('CLONE_EDIT_NOTE_LABEL', 'Wijzig notitie:');
define('CLONE_EDIT_OPTION_LABEL', ' Wijzig na het maken');
define('CLONE_ACL_OPTION_LABEL', ' Kopieer ACL');
define('CLONE_BUTTON', 'Kopieer');
/**#@-*/

/**#@+
 * Language constant used by the {@link delete.php delete} (page) handler
 */
// delete
define('ERROR_NO_PAGE_DEL_ACCESS', 'Je bent niet bevoegd om deze pagina te verwijderen.');
define('PAGE_DELETION_HEADER', 'Verwijder %s'); // %s - name of the page
define('SUCCESS_PAGE_DELETED', 'Pagina is verwijderd!');
define('PAGE_DELETION_CAPTION', 'Verwijder deze pagina volledig, inclusief alle opmerkingen?');
define('PAGE_DELETION_DELETE_BUTTON', 'Verwijder Pagina');
define('PAGE_DELETION_CANCEL_BUTTON', 'Annuleer');
/**#@-*/

/**#@+
 * Language constant used by the {@link diff.php diff} (page) handler
 */
// diff
define('ERROR_DIFF_LIBRARY_MISSING', 'Het benodigde bestand "'.WIKKA_LIBRARY_PATH.DIRECTORY_SEPARATOR.'diff.lib.php" kon niet gevonden worden. Zorg dat het bestand bestaat en in de juiste directory staat!'); //TODO 'Please make sure' should be 'please inform WikiAdmin' - end user can't "make sure"
define('ERROR_BAD_PARAMETERS', 'Er is iets mis met de opties die je hebt opgegeven, het is waarschijnlijk dat een van de versies die je wilt vergelijken verwijderd is.');
define('DIFF_ADDITIONS_HEADER', 'Toevoegingen:');
define('DIFF_DELETIONS_HEADER', 'Verwijderingen:');
define('DIFF_NO_DIFFERENCES', 'Geen verschillen');
define('DIFF_FAST_COMPARISON_HEADER', 'Vergelijking van %1$s &amp; %2$s'); // %1$s - link to page A; %2$s - link to page B
define('DIFF_COMPARISON_HEADER', 'Bezig met vergelijken van %2$s met %1$s'); // %1$s - link to page A; %2$s - link to page B (yes, they're swapped!)
define('DIFF_SAMPLE_ADDITION', 'toevoeging');
define('DIFF_SAMPLE_DELETION', 'verwijdering');
define('HIGHLIGHTING_LEGEND', 'Highlighting Legenda: %1$s %2$s'); // %1$s - sample added text; %2$s - sample deleted text
/**#@-*/

/**#@+
 * Language constant used by the {@link edit.php edit} (page) handler
 */
// edit
define('ERROR_OVERWRITE_ALERT1', 'OVERSCHRIJVINGSALARM: Deze pagina is aangepast door iemand anders terwijl je het aan het wijzigen was.');
define('ERROR_OVERWRITE_ALERT2', 'Kopieer je veranderingen en wijzig deze pagina opnieuw.');
define('ERROR_MISSING_EDIT_NOTE', 'MISSENDE WIJZIGINGSNOTITIE: Vul een wijzigingsnotitie in!');
define('ERROR_TAG_TOO_LONG', 'Paginanaam te lang! Maximaal %d karakters.'); // %d - maximum page name length
define('ERROR_NO_WRITE_ACCESS', 'Je hebt geen schrijftoegang tot deze pagina. Mogelijk moet je [[UserSettings inloggen]] of [[UserSettings een account registreren]] om deze pagina te wijzigen.'); //TODO Distinct links for login and register actions
define('EDIT_STORE_PAGE_LEGEND', 'Pagina opslaan');
define('EDIT_PREVIEW_HEADER', 'Preview');
define('EDIT_NOTE_LABEL', 'Vul een notitie over je wijziging in'); // label after field, so no colon!
define('MESSAGE_AUTO_RESIZE', 'Op %s klikken kapt automatisch de paginanaam af naar de juiste grootte'); // %s - rename button text
define('EDIT_PREVIEW_BUTTON', 'Preview');
define('EDIT_STORE_BUTTON', 'Opslaan');
define('EDIT_REEDIT_BUTTON', 'Wijzig opnieuw');
define('EDIT_CANCEL_BUTTON', 'Annuleer');
define('EDIT_RENAME_BUTTON', 'Hernoem');
define('ACCESSKEY_PREVIEW', 'p'); // ideally, should match EDIT_PREVIEW_BUTTON
define('ACCESSKEY_STORE', 'o'); // ideally, should match EDIT_STORE_BUTTON
define('ACCESSKEY_REEDIT', 'w'); // ideally, should match EDIT_REEDIT_BUTTON
define('SHOWCODE_LINK', 'Bekijk formatting code voor deze pagina'); // @@@ TODO MJH: how to translate 'formatting code'?
define('SHOWCODE_LINK_TITLE', 'Klik om de pagina formatting code te bekijken'); // @@@ TODO 'View page formatting code'
define('EDIT_COMMENT_TIMESTAMP_CAPTION', '(%s)'); // %s timestamp
/**#@-*/

/**#@+
 * Language constant used by the {@link grabcode.php grabcode} (page) handler
 */
// grabcode
define('ERROR_NO_CODE', 'Sorry, er is geen code om te downloaden.');
/**#@-*/

/**#@+
 * Language constant used by the {@link history.php history} (page) handler
 */
// history
define('EDITED_ON', 'Gewijzigd op %1$s door %2$s'); // %1$s - time; %2$s - user name
define('HISTORY_PAGE_VIEW', 'Geschiedenis van recente veranderingen voor %s'); // %s pagename
define('OLDEST_VERSION_EDITED_ON_BY', 'De oudste bekende versie van deze pagina is gemaakt op %1$s door %2$s'); // %1$s - time; %2$s - user name
define('MOST_RECENT_EDIT', 'Nieuwste wijziging op %1$s door %2$s'); // %1$s - time; %2$s - user name
define('HISTORY_MORE_LINK_DESC', 'hier'); // used for alternative history link in HISTORY_MORE
define('HISTORY_MORE', 'Volledige geschiedenis van deze pagina kan niet weergegeven worden op een enkele pagina, klik %s om meer te zien.'); // %s alternative history link # @@@ TODO avoid using 'here' ^
/**#@-*/

/**#@+
 * Language constant shared by the {@link processcomment.php processcomment} and {@link show.php show} (page) handlers
 */
// processcomment & show
// - comment buttons
define('COMMENT_DELETE_BUTTON', 'Verwijder');
define('COMMENT_REPLY_BUTTON', 'Reageer');
define('COMMENT_ADD_BUTTON', 'Voeg opmerking toe');
define('COMMENT_NEW_BUTTON', 'Nieuwe opmerking');
/**#@-*/

/**#@+
 * Language constant used by the {@link processcomment.php processcomment} (page) handler
 */
// processcomment
define('ERROR_NO_COMMENT_DEL_ACCESS', 'Sorry, je bent niet bevoegd om deze opmerking te verwijderen!');
define('ERROR_NO_COMMENT_WRITE_ACCESS', 'Sorry, je bent niet bevoegd om opmerkingen te plaatsen op deze pagina');
define('ERROR_EMPTY_COMMENT', 'Opmerking was leeg -- niet opgeslagen!');
define('ADD_COMMENT_LABEL', 'Reactie op %s:');
define('NEW_COMMENT_LABEL', 'Plaats een nieuwe opmerking:');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges_simple.xml.mm.php recentchanges_simple.xml.mm} (page) handler
 */
// recentchanges_simple.xml.mm
define('FIRST_NODE_LABEL', 'Recente Wijzigingen');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges.xml.php recentchanges.xml} (page) handler
 */
// recentchanges.xml
define('RECENTCHANGES_DESC', 'Recente wijzigingen van %s'); // %s - page name
/**#@-*/

/**#@+
 * Language constant shared by the {@link referrers_sites.php referrers_sites}, {@link referrers.php referrers} and {@link review_blacklist.php review_blacklist} (page) handlers
 */
// referrers_sites + referrers + review_blacklist
define('REFERRERS_PURGE_24_HOURS', 'laatste 24 uur');
define('REFERRERS_PURGE_N_DAYS', 'laatste %d dagen'); // %d number of days
define('REFERRERS_NO_SPAM', 'Noot aan spammers: Deze pagina is niet geindexeerd door zoekmachines, dus verspil je tijd niet.'); // @@@ TODO MJH: maybe leave untranslated, because spammers less likely to read Dutch :)
define('REFERRERS_DOMAINS_TO_WIKI_LINK_DESC', 'Bekijk verwijzende domeinen voor de hele Wiki'); // @@@ TODO MJH: Not sure about 'referrer' translation.
define('REFERRERS_DOMAINS_TO_PAGE_LINK_DESC', 'Bekijk verwijzende domeinen alleen voor %s'); // %s - page name
define('REFERRERS_URLS_TO_WIKI_LINK_DESC', 'Bekijk verwijzende URL\'s voor de hele Wiki');
define('REFERRERS_URLS_TO_PAGE_LINK_DESC', 'Bekijk verwijzende URL\'s alleen voor %s'); // %s - page name
define('REFERRER_BLACKLIST_LINK_DESC', 'Bekijk de zwarte lijst voor verwijzers');
define('BLACKLIST_LINK_DESC', 'Zwarte lijst');
define('NONE_CAPTION', 'Geen');
define('PLEASE_LOGIN_CAPTION', 'Je moet inloggen om verwijzende sites te bekijken.');
/**#@-*/

/**#@+
 * Language constant used by the {@link referrers_sites.php referrers_sites} (page) handler
 */
// referrers_sites
define('REFERRERS_URLS_LINK_DESC', 'bekijk een lijst van verschillende URL\'s');
define('REFERRERS_DOMAINS_TO_WIKI', 'Domeinen/websites die naar deze wiki linken (%s)'); // %s - link to referrers handler
define('REFERRERS_DOMAINS_TO_PAGE', 'Domeinen/websites die linken naar %1$s %2$s (%3$s)'); // %1$s - page link; %2$s - purge time; %3$s - link to referrers handler
/**#@-*/

/**#@+
 * Language constant used by the {@link referrers.php referrers} (page) handler
 */
// referrers
define('REFERRERS_DOMAINS_LINK_DESC', 'bekijk een lijst van domeinen');
define('REFERRERS_URLS_TO_WIKI', 'Externe pagina\'s die linken naar deze wiki (%s)'); // %s - link to referrers_sites handler
define('REFERRERS_URLS_TO_PAGE', 'Externe pagina\'s die linken naar %1$s %2$s (%3$s)'); // %1$s - page link; %2$s - purge time; %3$s - link to referrers_sites handler
/**#@-*/

/**#@+
 * Language constant used by the {@link review_blacklist.php review_blacklist} (page) handler
 */
// review_blacklist
define('BLACKLIST_HEADING', 'Zwarte lijst van verwijzingen');
define('BLACKLIST_REMOVE_LINK_DESC', 'Verwijder');
define('STATUS_BLACKLIST_EMPTY', 'De zwarte lijst is leeg.');
/**#@-*/

/**#@+
 * Language constant used by the {@link revisions.php revisions} (page) handler
 */
// revisions
define('REVISIONS_CAPTION', 'Revisies voor %s'); // %s pagename
define('REVISIONS_NO_REVISIONS_YET', 'Er zijn nog geen revisies voor deze pagina');
define('REVISIONS_SIMPLE_DIFF', 'Simpele Verschillen');
define('REVISIONS_MORE_CAPTION', 'Er zijn meer revisies die niet hier zijn laten zien, klik op de knop hieronder genaamd %s om deze te bekijken'); // %S - text of REVISIONS_MORE_BUTTON
define('REVISIONS_RETURN_TO_NODE_BUTTON', 'Ga terug naar Node / Annuleer'); // @@@ TODO MJH: translate 'Node'?
define('REVISIONS_SHOW_DIFFERENCES_BUTTON', 'Laat Verschillen Zien');
define('REVISIONS_MORE_BUTTON', 'Volgende...');
/**#@-*/

/**#@+
 * Language constant used by the {@link revisions.xml.php revisions.xml} (page) handler
 */
// revisions.xml
define('REVISIONS_EDITED_BY', 'Gewijzigd door %s'); // %s user name
define('HISTORY_REVISIONS_OF', 'Geschiedenis/revisies van %s'); // %s - page name
/**#@-*/

/**#@+
 * Language constant used by the {@link show.php show} (page) handler
 */
// show
define('SHOW_RE_EDIT_BUTTON', 'Wijzig deze oude revisie opnieuw');
define('SHOW_ASK_CREATE_PAGE_CAPTION', 'Deze pagina bestaat nog niet. Als je het wilt maken, klik dan op %s'); // %s - page create link // @@@ TODO MJH: can't use page create link in sentence directly, because word form changes in this context (maak -> maken)
define('SHOW_OLD_REVISION_CAPTION', 'Dit is een oude revisie van %1$s van %2$s.'); // %1$s - page link; %2$s - timestamp
define('COMMENTS_CAPTION', 'Opmerkingen');
define('DISPLAY_COMMENTS_LABEL', 'Laat opmerkingen zien');
define('DISPLAY_COMMENT_LINK_DESC', 'Laat opmerking zien');
define('DISPLAY_COMMENTS_EARLIEST_LINK_DESC', 'Vroegsten eerst');
define('DISPLAY_COMMENTS_LATEST_LINK_DESC', 'Nieuwsten eerst');
define('DISPLAY_COMMENTS_THREADED_LINK_DESC', 'Threaded'); // @@@ TODO MJH: how to translate 'Threaded' (also see above somewhere)
define('HIDE_COMMENTS_LINK_DESC', 'Verberg opmerkingen');
define('STATUS_NO_COMMENTS', 'Er zijn geen opmerkingen op deze pagina.');
define('STATUS_ONE_COMMENT', 'Er is een opmerkingen op deze pagina.');
define('STATUS_SOME_COMMENTS', 'Er zijn %d opmerkingen op deze pagina.'); // %d - number of comments
define('COMMENT_TIME_CAPTION', '%s'); // %s comment time
/**#@-*/

/**#@+
 * Language constant used by the {@link showcode.php showcode} (page) handler
 */
// showcode
define('SOURCE_HEADING', 'Formatting code voor %s'); // %s - page link // @@@ TODO MJH: translate 'formatting code'?
define('SHOW_RAW_LINK_DESC', 'bekijk alleen broncode');
/**#@-*/

/* ------------------ LIBS ------------------*/

/**#@+
 * Language constant used by the {@link Wakka.class.php Wakka class} (the Wikka core containing most methods)
 */
// Wakka.class
define('QUERY_FAILED', 'Vraag gefaald:'); // @@@ TODO MJH: correct translation of 'query'?
define('REDIR_DOCTITLE', 'Verwezen naar %s'); // %s - target page
define('REDIR_LINK_DESC', 'deze link'); // used in REDIR_MANUAL_CAPTION
define('REDIR_MANUAL_CAPTION', 'Als je browser je niet verwijst, volg dan %s'); // %s target page link
define('CREATE_THIS_PAGE_LINK_TITLE', 'Maak deze pagina');
define('ACTION_UNKNOWN_SPECCHARS', 'Onbekende actie; de actienaam mag geen speciale karakters bevatten.');
define('ACTION_UNKNOWN', 'Onbekende actie "%s"'); // %s - action name
define('HANDLER_UNKNOWN_SPECCHARS', 'Onbekende handler; de handlernaam mag geen speciale karakters bevatten.');
define('HANDLER_UNKNOWN', 'Sorry, %s is een onbekende handler.'); // %s handler name
define('FORMATTER_UNKNOWN_SPECCHARS', 'Onbekende formatter; de formatternaam mag geen speciale karakters bevatten.');
define('FORMATTER_UNKNOWN', 'Formatter "%s" niet gevonden'); // %s formatter name
/**#@-*/

/* ------------------ SETUP ------------------ */
/**#@+
 * Language constant used by the {@link index.php setup} program (and several included files)
 */
// @@@ later....
/**#@-*/

?>