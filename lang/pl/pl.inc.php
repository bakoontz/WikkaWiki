<?php
/**
 * Wikka language file.
 *
 * This file holds all interface language strings for Wikka.
 *
 * @package 		Language
 *
 * @version		$Id:pl.inc.php 004 2010-08-18 15:05:12Z KrzysztofTrybowski $
 * @license 		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author 		{@link http://wikkawiki.org/KrzysztofTrybowski Krzysztof Trybowski}
 *
 * @copyright 	Copyright 2010, {@link http://wikkawiki.org/CreditsPage Wikka Development Team}
 *
 * @todo	review places marked with KJT
 *
 * @todo	review translation for referrers
 *
 * @todo	search for and embed some mindmap to test translation of that part of UI
 *
 * @todo	yes/no: should names of default pages be translated?
 *
 * @todo	Aren't we apologizing too much? -> "Przepraszamy"
 *
 * @todo	Clone: duplikuj vs skopiuj
 */

/* ------------------ COMMON ------------------ */

//if(!defined('')) define('', ''); //
/**#@+
 * Language constant shared among several Wikka components
 */
// NOTE: all common names (used in multiple files) should start with WIKKA_ !
if(!defined('WIKKA_ADMIN_ONLY_TITLE')) define('WIKKA_ADMIN_ONLY_TITLE', 'Przepraszamy, tylko administratorzy mają dostęp do tej informacji'); //title for elements that are only displayed to admins
if(!defined('WIKKA_ERROR_SETUP_FILE_MISSING')) define('WIKKA_ERROR_SETUP_FILE_MISSING', 'Plik instalatora nie został znaleziony. Zainstaluj Wikkę ponownie!');
if(!defined('WIKKA_ERROR_MYSQL_ERROR')) define('WIKKA_ERROR_MYSQL_ERROR', 'Błąd MySQL: %d %s');	// %d error number; %s error text
if(!defined('WIKKA_ERROR_CAPTION')) define('WIKKA_ERROR_CAPTION', 'Błąd');
if(!defined('WIKKA_ERROR_ACL_READ')) define('WIKKA_ERROR_ACL_READ', 'Nie masz prawa odczytu tej strony.');
if(!defined('WIKKA_ERROR_ACL_READ_SOURCE')) define('WIKKA_ERROR_ACL_READ_SOURCE', 'Nie masz prawa odczytu kodu źródłowego tej strony.');
if(!defined('WIKKA_ERROR_ACL_READ_INFO')) define('WIKKA_ERROR_ACL_READ_INFO', 'Nie masz prawa dostępu do tej informacji.');
if(!defined('WIKKA_ERROR_LABEL')) define('WIKKA_ERROR_LABEL', 'Błąd');
if(!defined('WIKKA_ERROR_PAGE_NOT_EXIST')) define('WIKKA_ERROR_PAGE_NOT_EXIST', 'Przepraszamy, strona %s nie istnieje.'); // %s (source) page name
if(!defined('WIKKA_ERROR_EMPTY_USERNAME')) define('WIKKA_ERROR_EMPTY_USERNAME', 'Proszę podać nazwę użytkownika!');
if(!defined('WIKKA_DIFF_ADDITIONS_HEADER')) define('WIKKA_DIFF_ADDITIONS_HEADER', 'Dodano:');
if(!defined('WIKKA_DIFF_DELETIONS_HEADER')) define('WIKKA_DIFF_DELETIONS_HEADER', 'Usunięto:');
if(!defined('WIKKA_DIFF_NO_DIFFERENCES')) define('WIKKA_DIFF_NO_DIFFERENCES', 'Brak różnic');
if(!defined('ERROR_USERNAME_UNAVAILABLE')) define('ERROR_USERNAME_UNAVAILABLE', "Przepraszamy, podana nazwa użytkownika jest już zajęta."); // @todo Usunięto fragment "lub z innych względów nie może zostać użyta."
if(!defined('ERROR_USER_SUSPENDED')) define('ERROR_USER_SUSPENDED', "Przepraszamy, to konto zostało zawieszone. Skontaktuj się z administratorem.");
if(!defined('WIKKA_ERROR_PAGE_ALREADY_EXIST')) define('WIKKA_ERROR_PAGE_ALREADY_EXIST', 'Przepraszamy, taka strona już istnieje');
if(!defined('WIKKA_LOGIN_LINK_DESC')) define('WIKKA_LOGIN_LINK_DESC', 'logowania'); // @todo Is this used?
if(!defined('WIKKA_MAINPAGE_LINK_DESC')) define('WIKKA_MAINPAGE_LINK_DESC', 'strona główna'); // @todo Is this used?
if(!defined('WIKKA_NO_OWNER')) define('WIKKA_NO_OWNER', 'Nikt');
if(!defined('WIKKA_NOT_AVAILABLE')) define('WIKKA_NOT_AVAILABLE', 'b/d');
if(!defined('WIKKA_NOT_INSTALLED')) define('WIKKA_NOT_INSTALLED', 'nie zainstalowano');
if(!defined('WIKKA_ANONYMOUS_USER')) define('WIKKA_ANONYMOUS_USER', 'użytkownik anonimowy'); // 'name' of non-registered user
if(!defined('WIKKA_UNREGISTERED_USER')) define('WIKKA_UNREGISTERED_USER', 'użytkownik niezarejestrowany'); // alternative for 'anonymous' @todo make one string only?
if(!defined('WIKKA_ANONYMOUS_AUTHOR_CAPTION')) define('WIKKA_ANONYMOUS_AUTHOR_CAPTION', '('.WIKKA_UNREGISTERED_USER.')'); // @todo Decide this or or WIKKA_ANONYMOUS_USER
if(!defined('WIKKA_SAMPLE_WIKINAME')) define('WIKKA_SAMPLE_WIKINAME', 'JanKowalski'); // must be a CamelCase name @todo Is this still true?
if(!defined('WIKKA_HISTORY')) define('WIKKA_HISTORY', 'historia');
if(!defined('WIKKA_REVISIONS')) define('WIKKA_REVISIONS', 'wersji');
if(!defined('WIKKA_REVISION_NUMBER')) define('WIKKA_REVISION_NUMBER', 'Wersja nr %s');
if(!defined('WIKKA_REV_WHEN_BY_WHO')) define('WIKKA_REV_WHEN_BY_WHO', '%1$s napisana przez użytkownika: %2$s'); // %1$s timestamp; %2$s user name
if(!defined('WIKKA_NO_PAGES_FOUND')) define('WIKKA_NO_PAGES_FOUND', 'Nie znaleziono stron.');
if(!defined('WIKKA_PAGE_OWNER')) define('WIKKA_PAGE_OWNER', 'Właściciel: %s'); // %s page owner name or link
if(!defined('WIKKA_COMMENT_AUTHOR_DIVIDER')) define('WIKKA_COMMENT_AUTHOR_DIVIDER', ', napisany przez: ');
if(!defined('WIKKA_PAGE_EDIT_LINK_DESC')) define('WIKKA_PAGE_EDIT_LINK_DESC', 'edytuj');
if(!defined('WIKKA_PAGE_CREATE_LINK_DESC')) define('WIKKA_PAGE_CREATE_LINK_DESC', 'utworzyć');
if(!defined('WIKKA_PAGE_EDIT_LINK_TITLE')) define('WIKKA_PAGE_EDIT_LINK_TITLE', 'Edytuj stronę %s'); // %s page name KJT: phrase different than in English: 'Edit page %s'
if(!defined('WIKKA_BACKLINKS_LINK_TITLE')) define('WIKKA_BACKLINKS_LINK_TITLE', 'Wyświetl strony zawierające odnośniki do „%s”'); // %s page name
if(!defined('WIKKA_JRE_LINK_DESC')) define('WIKKA_JRE_LINK_DESC', 'Java Runtime Environment');
if(!defined('WIKKA_NOTE')) define('WIKKA_NOTE', 'UWAGA,');
if(!defined('WIKKA_JAVA_PLUGIN_NEEDED')) define('WIKKA_JAVA_PLUGIN_NEEDED', 'Do uruchomienia tego programu wymagana jest Java 1.4.1 (lub nowsza). ');
if(!defined('REVISION_DATE_FORMAT')) define('REVISION_DATE_FORMAT', 'D, d M Y'); // @TODO
if(!defined('REVISION_TIME_FORMAT')) define('REVISION_TIME_FORMAT', 'H:i T'); // @TODO
if(!defined('INPUT_ERROR_STYLE')) define('INPUT_ERROR_STYLE', 'class="highlight"'); // @TODO
if(!defined('CANCEL_ACL_LABEL')) define('CANCEL_ACL_LABEL', 'Anuluj'); // @TODO
if(!defined('UNREGISTERED_USER')) define('UNREGISTERED_USER', 'użytkownik niezarejestrowany');  // @TODO
if(!defined('WHEN_BY_WHO')) define('WHEN_BY_WHO', '%1$s napisana przez: %2$s');
if(!defined('I18N_LANG')) define('I18N_LANG', 'pl-PL'); // @TODO
/**#@-*/

/*  ------------------ CORE ------------------  */

/**#@+
 * Language constant for the core {@link wikka.php wikka} program
 */
// wikka
if(!defined('ERROR_SETUP_FILE_MISSING')) define('ERROR_SETUP_FILE_MISSING', 'Plik instalatora nie został znaleziony. Zainstaluj Wikkę ponownie!');
if(!defined('ERROR_SETUP_HEADER_MISSING')) define('ERROR_SETUP_HEADER_MISSING', 'Plik "setup/header.php" nie został znaleziony. Zainstaluj Wikkę ponownie!');
if(!defined('ERROR_SETUP_FOOTER_MISSING')) define('ERROR_SETUP_FOOTER_MISSING', 'Plik "setup/footer.php" nie został znaleziony. Zainstaluj Wikkę ponownie!');
if(!defined('ERROR_HEADER_MISSING')) define('ERROR_HEADER_MISSING', 'Nie znaleziono szablonu nagłówka. Upewnij się, że plik <code>header.php</code> istnieje w katalogu szablonów.'); //TODO Make sure this message matches any filename/folder change
if(!defined('ERROR_FOOTER_MISSING')) define('ERROR_FOOTER_MISSING', 'Nie znaleziono szablonu stopki. Upewnij się, że plik <code>footer.php</code> istnieje w katalogu szablonów.'); //TODO Make sure this message matches any filename/folder change
if(!defined('ERROR_NO_DB_ACCESS')) define('ERROR_NO_DB_ACCESS', 'Błąd: Nie można połączyć się z bazą danych.');
if(!defined('PAGE_GENERATION_TIME')) define('PAGE_GENERATION_TIME', 'Czas generowania strony w sekundach: %.4f.'); // %.4f - generation time in seconds with 4 digits after the dot
if(!defined('WIKI_UPGRADE_NOTICE')) define('WIKI_UPGRADE_NOTICE', 'Witryna w trakcie aktualizacji. Spróbuj poźniej.');
/*

NOTE: These defines are the "new" defines ported from trunk to 1.2.
They will eventually need to be reconciled with updates to wikka.php.
For now, I've commented them out and have simply copied over the 1.2
versions.

if(!defined('ERROR_NO_DB_ACCESS')) define('ERROR_NO_DB_ACCESS', 'Błąd: Nie można połączyć się z bazą danych.');
if(!defined('ERROR_RETRIEVAL_MYSQL_VERSION')) define('ERROR_RETRIEVAL_MYSQL_VERSION', 'Nie można określić wersji MySQL.');
if(!defined('ERROR_WRONG_MYSQL_VERSION')) define('ERROR_WRONG_MYSQL_VERSION', 'WikkaWiki wymaga MySQL w wersji %s lub wyższej!');	// %s - version number
if(!defined('STATUS_WIKI_UPGRADE_NOTICE')) define('STATUS_WIKI_UPGRADE_NOTICE', 'Witryna w trakcie aktualizacji. Spróbuj poźniej.');
if(!defined('STATUS_WIKI_UNAVAILABLE')) define('STATUS_WIKI_UNAVAILABLE', 'Witryna jest chwilowo niedostępna.');
if(!defined('PAGE_GENERATION_TIME')) define('PAGE_GENERATION_TIME', 'Czas generowania strony w sekundach: %.4f.'); // %.4f - page generation time
if(!defined('ERROR_HEADER_MISSING')) define('ERROR_HEADER_MISSING', 'Nie znaleziono szablonu nagłówka. Upewnij się, że plik <code>header.php</code> istnieje w katalogu szablonów.'); //TODO Make sure this message matches any filename/folder change
if(!defined('ERROR_FOOTER_MISSING')) define('ERROR_FOOTER_MISSING', 'Nie znaleziono szablonu stopki. Upewnij się, że plik <code>footer.php</code> istnieje w katalogu szablonów.'); //TODO Make sure this message matches any filename/folder change

#if(!defined('ERROR_SETUP_HEADER_MISSING')) define('ERROR_SETUP_HEADER_MISSING', 'Plik "setup/header.php" nie został znaleziony. Zainstaluj Wikkę ponownie!');
#if(!defined('ERROR_SETUP_FOOTER_MISSING')) define('ERROR_SETUP_FOOTER_MISSING', 'Plik "setup/footer.php" nie został znaleziony. Zainstaluj Wikkę ponownie!');
*/
/**#@-*/

/*  ------------------ TEMPLATE ------------------  */

/**#@+
 * Language constant used by the {@link header.php header} template
 */
// header
if(!defined('GENERIC_DOCTITLE')) define('GENERIC_DOCTITLE', '%1$s: %2$s');	// %1$s - wiki name; %2$s - page title
if(!defined('RSS_REVISIONS_TITLE')) define('RSS_REVISIONS_TITLE', '%1$s: wersje strony %2$s');	// %1$s - wiki name; %2$s - current page name
if(!defined('RSS_RECENTCHANGES_TITLE')) define('RSS_RECENTCHANGES_TITLE', '%s: strony ostatnio zmieniane');	// %s - wiki name
if(!defined('YOU_ARE')) define('YOU_ARE', 'Zalogowany jako: %s'); // %s - name / ip of the user.
/**#@-*/

/**#@+
 * Language constant used by the {@link footer.php footer} template
 */
// footer
if(!defined('FOOTER_PAGE_EDIT_LINK_DESC')) define('FOOTER_PAGE_EDIT_LINK_DESC', 'Edytuj stronę');
if(!defined('PAGE_HISTORY_LINK_TITLE')) define('PAGE_HISTORY_LINK_TITLE', 'Zobacz ostatnie zmiany na stronie'); // KJT: phrase different than in English: 'View recent edits to this page'
if(!defined('PAGE_HISTORY_LINK_DESC')) define('PAGE_HISTORY_LINK_DESC', 'Historia strony');
if(!defined('PAGE_REVISION_LINK_TITLE')) define('PAGE_REVISION_LINK_TITLE', 'Zobacz listę ostatnich zmian tej strony'); // KJT: phrase different than in English: 'View recent revisions list for this page'
if(!defined('PAGE_REVISION_XML_LINK_TITLE')) define('PAGE_REVISION_XML_LINK_TITLE', 'Zobacz listę ostatnich zmian tej strony'); // KJT: phrase different than in English: 'View recent revisions list for this page'
if(!defined('PAGE_ACLS_EDIT_LINK_DESC')) define('PAGE_ACLS_EDIT_LINK_DESC', 'Edytuj uprawnienia');
if(!defined('PAGE_ACLS_EDIT_ADMIN_LINK_DESC')) define('PAGE_ACLS_EDIT_ADMIN_LINK_DESC', '('.PAGE_ACLS_EDIT_LINK_DESC.')');
if(!defined('PUBLIC_PAGE')) define('PUBLIC_PAGE', 'Strona publiczna');
if(!defined('USER_IS_OWNER')) define('USER_IS_OWNER', 'Jesteś właścicielem tej strony.');
if(!defined('TAKE_OWNERSHIP')) define('TAKE_OWNERSHIP', 'Przejmij na własność');
if(!defined('REFERRERS_LINK_TITLE')) define('REFERRERS_LINK_TITLE', 'Zobacz listę adresów zawierających odnośnik do tej strony'); // KJT: phrase different than in English: 'View a list of URLs referring to this page'
if(!defined('REFERRERS_LINK_DESC')) define('REFERRERS_LINK_DESC', 'Źródła odwiedzających'); // @todo KJT Write it better! Odnośniki do tej strony, Źródła gości, Kierunki odwiedzin, Pochodzenie gości, 
if(!defined('QUERY_LOG')) define('QUERY_LOG', 'Dziennik zapytań:'); // @todo KJT Write it better!
if(!defined('SEARCH_LABEL')) define('SEARCH_LABEL', 'Szukaj:');
if(!defined('VALID_XHTML_LINK_DESC')) define('VALID_XHTML_LINK_DESC', 'Valid XHTML');
if(!defined('VALID_CSS_LINK_DESC')) define('VALID_CSS_LINK_DESC', 'Valid CSS:');
if(!defined('POWERED_BY_WIKKA_LINK_DESC')) define('POWERED_BY_WIKKA_LINK_DESC', 'Powered by %s'); // %s - engine and version
/**#@-*/


/*  ------------------ ACTIONS  ------------------  */

/**#@+
 * Language constants used by the {@link adminpages.php adminpages} action
 */
if(!defined('ADMINPAGES_DEFAULT_RECORDS_LIMIT')) define('ADMINPAGES_DEFAULT_RECORDS_LIMIT', '20'); # number of records per page
if(!defined('ADMINPAGES_DEFAULT_MIN_RECORDS_DISPLAY')) define('ADMINPAGES_DEFAULT_MIN_RECORDS_DISPLAY', '5'); # min number of records
if(!defined('ADMINPAGES_DEFAULT_RECORDS_RANGE')) define('ADMINPAGES_DEFAULT_RECORDS_RANGE',serialize(array('10','50','100','500','1000'))); #range array for records pager
if(!defined('ADMINPAGES_DEFAULT_SORT_FIELD')) define('ADMINPAGES_DEFAULT_SORT_FIELD', 'time'); # sort field
if(!defined('ADMINPAGES_DEFAULT_SORT_ORDER')) define('ADMINPAGES_DEFAULT_SORT_ORDER', 'desc'); # sort order, ascendant or descendant
if(!defined('ADMINPAGES_DEFAULT_START')) define('ADMINPAGES_DEFAULT_START', '0'); # start record
if(!defined('ADMINPAGES_DEFAULT_SEARCH')) define('ADMINPAGES_DEFAULT_SEARCH', ''); # keyword to restrict page search
if(!defined('ADMINPAGES_DEFAULT_TAG_LENGTH')) define('ADMINPAGES_DEFAULT_TAG_LENGTH', '12'); # max. length of displayed pagename
if(!defined('ADMINPAGES_DEFAULT_URL_LENGTH')) define('ADMINPAGES_DEFAULT_URL_LENGTH', '15'); # max. length of displayed user host
if(!defined('ADMINPAGES_DEFAULT_TERMINATOR')) define('ADMINPAGES_DEFAULT_TERMINATOR', '&#8230;'); # standard symbol replacing truncated text (ellipsis) JW 2005-07-19
if(!defined('ADMINPAGES_ALTERNATE_ROW_COLOR')) define('ADMINPAGES_ALTERNATE_ROW_COLOR', '1'); # switch alternate row color
if(!defined('ADMINPAGES_STAT_COLUMN_COLOR')) define('ADMINPAGES_STAT_COLUMN_COLOR', '1'); # switch color for statistics columns
if(!defined('ADMINPAGES_DEFAULT_START_YEAR')) define('ADMINPAGES_DEFAULT_START_YEAR', 'YYYY');
if(!defined('ADMINPAGES_DEFAULT_START_MONTH')) define('ADMINPAGES_DEFAULT_START_MONTH', 'MM');
if(!defined('ADMINPAGES_DEFAULT_START_DAY')) define('ADMINPAGES_DEFAULT_START_DAY', 'DD');
if(!defined('ADMINPAGES_DEFAULT_START_HOUR')) define('ADMINPAGES_DEFAULT_START_HOUR', 'hh');
if(!defined('ADMINPAGES_DEFAULT_START_MINUTE')) define('ADMINPAGES_DEFAULT_START_MINUTE', 'mm');
if(!defined('ADMINPAGES_DEFAULT_START_SECOND')) define('ADMINPAGES_DEFAULT_START_SECOND', 'ss');
if(!defined('ADMINPAGES_DEFAULT_END_YEAR')) define('ADMINPAGES_DEFAULT_END_YEAR', 'YYYY');
if(!defined('ADMINPAGES_DEFAULT_END_MONTH')) define('ADMINPAGES_DEFAULT_END_MONTH', 'MM');
if(!defined('ADMINPAGES_DEFAULT_END_DAY')) define('ADMINPAGES_DEFAULT_END_DAY', 'DD');
if(!defined('ADMINPAGES_DEFAULT_END_HOUR')) define('ADMINPAGES_DEFAULT_END_HOUR', 'hh');
if(!defined('ADMINPAGES_DEFAULT_END_MINUTE')) define('ADMINPAGES_DEFAULT_END_MINUTE', 'mm');
if(!defined('ADMINPAGES_DEFAULT_END_SECOND')) define('ADMINPAGES_DEFAULT_END_SECOND', 'ss');
if(!defined('ADMINPAGES_MAX_EDIT_NOTE_LENGTH')) define('ADMINPAGES_MAX_EDIT_NOTE_LENGTH', '50');
if(!defined('ADMINPAGES_REVISIONS_ICON')) define('ADMINPAGES_REVISIONS_ICON', 'images/icons/edit.png');
if(!defined('ADMINPAGES_COMMENTS_ICON')) define('ADMINPAGES_COMMENTS_ICON', 'images/icons/comment.png');
if(!defined('ADMINPAGES_HITS_ICON')) define('ADMINPAGES_HITS_ICON', 'images/icons/star.png');
if(!defined('ADMINPAGES_BACKLINKS_ICON')) define('ADMINPAGES_BACKLINKS_ICON', 'images/icons/link.png');
if(!defined('ADMINPAGES_REFERRERS_ICON')) define('ADMINPAGES_REFERRERS_ICON', 'images/icons/world.png');
if(!defined('ADMINPAGES_PAGE_TITLE')) define('ADMINPAGES_PAGE_TITLE','Administracja stronami');
if(!defined('ADMINPAGES_FORM_LEGEND')) define('ADMINPAGES_FORM_LEGEND','Filtr widoku:');
if(!defined('ADMINPAGES_FORM_SEARCH_STRING_LABEL')) define('ADMINPAGES_FORM_SEARCH_STRING_LABEL','Szukaj strony:');
if(!defined('ADMINPAGES_FORM_SEARCH_STRING_TITLE')) define('ADMINPAGES_FORM_SEARCH_STRING_TITLE','Kryteria wyszukiwania');
if(!defined('ADMINPAGES_FORM_SEARCH_SUBMIT')) define('ADMINPAGES_FORM_SEARCH_SUBMIT','Szukaj');
if(!defined('ADMINPAGES_FORM_DATE_RANGE_STRING_LABEL')) define('ADMINPAGES_FORM_DATE_RANGE_STRING_LABEL','Czas ostatniej edycji: pomiędzy');
if(!defined('ADMINPAGES_FORM_DATE_RANGE_CONNECTOR_LABEL')) define('ADMINPAGES_FORM_DATE_RANGE_CONNECTOR_LABEL','i');
if(!defined('ADMINPAGES_FORM_PAGER_LABEL_BEFORE')) define('ADMINPAGES_FORM_PAGER_LABEL_BEFORE','Wyświetl');
if(!defined('ADMINPAGES_FORM_PAGER_TITLE')) define('ADMINPAGES_FORM_PAGER_TITLE','Określ ilość wpisów na stronie');
if(!defined('ADMINPAGES_FORM_PAGER_LABEL_AFTER')) define('ADMINPAGES_FORM_PAGER_LABEL_AFTER','wpisów na stronie');
if(!defined('ADMINPAGES_FORM_PAGER_SUBMIT')) define('ADMINPAGES_FORM_PAGER_SUBMIT','Wyświetl');
if(!defined('ADMINPAGES_FORM_PAGER_LINK')) define('ADMINPAGES_FORM_PAGER_LINK','Wyświetl wpisy od %d do %d');
if(!defined('ADMINPAGES_FORM_RESULT_INFO')) define('ADMINPAGES_FORM_RESULT_INFO','Wpisy');
if(!defined('ADMINPAGES_FORM_RESULT_SORTED_BY')) define('ADMINPAGES_FORM_RESULT_SORTED_BY','Kryterium sortowania:');
if(!defined('ADMINPAGES_TABLE_HEADING_PAGENAME')) define('ADMINPAGES_TABLE_HEADING_PAGENAME','Nazwa strony');
if(!defined('ADMINPAGES_TABLE_HEADING_PAGENAME_TITLE')) define('ADMINPAGES_TABLE_HEADING_PAGENAME_TITLE','Sortuj wg nazwy strony');
if(!defined('ADMINPAGES_TABLE_HEADING_OWNER')) define('ADMINPAGES_TABLE_HEADING_OWNER','Właściciel');
if(!defined('ADMINPAGES_TABLE_HEADING_OWNER_TITLE')) define('ADMINPAGES_TABLE_HEADING_OWNER_TITLE','Sortuj wg właściciela');
if(!defined('ADMINPAGES_TABLE_HEADING_LASTAUTHOR')) define('ADMINPAGES_TABLE_HEADING_LASTAUTHOR','Ostatni autor');
if(!defined('ADMINPAGES_TABLE_HEADING_LASTAUTHOR_TITLE')) define('ADMINPAGES_TABLE_HEADING_LASTAUTHOR_TITLE','Sortuj wg ostatniego autora');
if(!defined('ADMINPAGES_TABLE_HEADING_LASTEDIT')) define('ADMINPAGES_TABLE_HEADING_LASTEDIT','Ostatnia edycja');
if(!defined('ADMINPAGES_TABLE_HEADING_LASTEDIT_TITLE')) define('ADMINPAGES_TABLE_HEADING_LASTEDIT_TITLE','Sortuj wg czasu ostatniej edycji');
if(!defined('ADMINPAGES_TABLE_SUMMARY')) define('ADMINPAGES_TABLE_SUMMARY','Lista stron w tym serwisie');
if(!defined('ADMINPAGES_TABLE_HEADING_HITS_TITLE')) define('ADMINPAGES_TABLE_HEADING_HITS_TITLE','Ilość wyświetleń');
if(!defined('ADMINPAGES_TABLE_HEADING_REVISIONS_TITLE')) define('ADMINPAGES_TABLE_HEADING_REVISIONS_TITLE','Wersje');
if(!defined('ADMINPAGES_TABLE_HEADING_COMMENTS_TITLE')) define('ADMINPAGES_TABLE_HEADING_COMMENTS_TITLE','Komentarze');
if(!defined('ADMINPAGES_TABLE_HEADING_BACKLINKS_TITLE')) define('ADMINPAGES_TABLE_HEADING_BACKLINKS_TITLE','Strony kierujące tutaj');
if(!defined('ADMINPAGES_TABLE_HEADING_REFERRERS_TITLE')) define('ADMINPAGES_TABLE_HEADING_REFERRERS_TITLE','Witryny kierujące tutaj');
if(!defined('ADMINPAGES_TABLE_HEADING_HITS_ALT')) define('ADMINPAGES_TABLE_HEADING_HITS_ALT','Wyświetleń');
if(!defined('ADMINPAGES_TABLE_HEADING_REVISIONS_ALT')) define('ADMINPAGES_TABLE_HEADING_REVISIONS_ALT','Wersje');
if(!defined('ADMINPAGES_TABLE_HEADING_COMMENTS_ALT')) define('ADMINPAGES_TABLE_HEADING_COMMENTS_ALT','Komentarze');
if(!defined('ADMINPAGES_TABLE_HEADING_BACKLINKS_ALT')) define('ADMINPAGES_TABLE_HEADING_BACKLINKS_ALT','Strony kierujące tutaj');
if(!defined('ADMINPAGES_TABLE_HEADING_REFERRERS_ALT')) define('ADMINPAGES_TABLE_HEADING_REFERRERS_ALT','Witryny kierujące tutaj');
if(!defined('ADMINPAGES_TABLE_HEADING_ACTIONS')) define('ADMINPAGES_TABLE_HEADING_ACTIONS','Działania');
if(!defined('ADMINPAGES_ACTION_EDIT_LINK_TITLE')) define('ADMINPAGES_ACTION_EDIT_LINK_TITLE','Edytuj stronę: %s');
if(!defined('ADMINPAGES_ACTION_DELETE_LINK_TITLE')) define('ADMINPAGES_ACTION_DELETE_LINK_TITLE','Usuń stronę: %s');
if(!defined('ADMINPAGES_ACTION_CLONE_LINK_TITLE')) define('ADMINPAGES_ACTION_CLONE_LINK_TITLE','Skopiuj stronę: %s');
if(!defined('ADMINPAGES_ACTION_RENAME_LINK_TITLE')) define('ADMINPAGES_ACTION_RENAME_LINK_TITLE','Zmień nazwę strony: %s');
if(!defined('ADMINPAGES_ACTION_ACL_LINK_TITLE')) define('ADMINPAGES_ACTION_ACL_LINK_TITLE','Zmień prawa dostępu do strony: %s');
if(!defined('ADMINPAGES_ACTION_REVERT_LINK_TITLE')) define('ADMINPAGES_ACTION_REVERT_LINK_TITLE','Cofnij stronę %s do poprzedniej wersji');
if(!defined('ADMINPAGES_ACTION_EDIT_LINK')) define('ADMINPAGES_ACTION_EDIT_LINK','edytuj');
if(!defined('ADMINPAGES_ACTION_DELETE_LINK')) define('ADMINPAGES_ACTION_DELETE_LINK','usuń');
if(!defined('ADMINPAGES_ACTION_CLONE_LINK')) define('ADMINPAGES_ACTION_CLONE_LINK','skopiuj');
if(!defined('ADMINPAGES_ACTION_RENAME_LINK')) define('ADMINPAGES_ACTION_RENAME_LINK','zmień nazwę');
if(!defined('ADMINPAGES_ACTION_ACL_LINK')) define('ADMINPAGES_ACTION_ACL_LINK','prawa dostępu');
if(!defined('ADMINPAGES_ACTION_INFO_LINK')) define('ADMINPAGES_ACTION_INFO_LINK','informacje');
if(!defined('ADMINPAGES_ACTION_REVERT_LINK')) define('ADMINPAGES_ACTION_REVERT_LINK', 'cofnij');
if(!defined('ADMINPAGES_TAKE_OWNERSHIP_LINK')) define('ADMINPAGES_TAKE_OWNERSHIP_LINK','Przejmij na własność stronę');
if(!defined('ADMINPAGES_NO_OWNER')) define('ADMINPAGES_NO_OWNER','(Nikt)');
if(!defined('ADMINPAGES_TABLE_CELL_HITS_TITLE')) define('ADMINPAGES_TABLE_CELL_HITS_TITLE','Wyświetlenia strony: %s (%d)');
if(!defined('ADMINPAGES_TABLE_CELL_REVISIONS_TITLE')) define('ADMINPAGES_TABLE_CELL_REVISIONS_TITLE','Wyświetl wersje strony: %s (%d)');
if(!defined('ADMINPAGES_TABLE_CELL_COMMENTS_TITLE')) define('ADMINPAGES_TABLE_CELL_COMMENTS_TITLE','Wyświetl komentarze strony: %s (%d)');
if(!defined('ADMINPAGES_TABLE_CELL_BACKLINKS_TITLE')) define('ADMINPAGES_TABLE_CELL_BACKLINKS_TITLE','Wyświetl strony kierujące do: %s (%d)');
if(!defined('ADMINPAGES_TABLE_CELL_REFERRERS_TITLE')) define('ADMINPAGES_TABLE_CELL_REFERRERS_TITLE','Wyświetl zewnętrzne witryny kierujące do: %s (%d)');
if(!defined('ADMINPAGES_SELECT_RECORD_TITLE')) define('ADMINPAGES_SELECT_RECORD_TITLE','Wybierz stronę %s');
if(!defined('ADMINPAGES_NO_EDIT_NOTE')) define('ADMINPAGES_NO_EDIT_NOTE','(Brak opisu zmian)');
if(!defined('ADMINPAGES_CHECK_ALL_TITLE')) define('ADMINPAGES_CHECK_ALL_TITLE','Wybierz wszystkie strony');
if(!defined('ADMINPAGES_CHECK_ALL')) define('ADMINPAGES_CHECK_ALL','Wybierz wszystkie');
if(!defined('ADMINPAGES_UNCHECK_ALL_TITLE')) define('ADMINPAGES_UNCHECK_ALL_TITLE','Anuluj wybór');
if(!defined('ADMINPAGES_UNCHECK_ALL')) define('ADMINPAGES_UNCHECK_ALL','Anuluj wybór');
if(!defined('ADMINPAGES_FORM_MASSACTION_LEGEND')) define('ADMINPAGES_FORM_MASSACTION_LEGEND','Działanie na wielu stronach');
if(!defined('ADMINPAGES_FORM_MASSACTION_LABEL')) define('ADMINPAGES_FORM_MASSACTION_LABEL','Działanie: ');
if(!defined('ADMINPAGES_FORM_MASSACTION_SELECT_TITLE')) define('ADMINPAGES_FORM_MASSACTION_SELECT_TITLE','Wybierz działanie do wykonania na zaznaczonych stronach');
if(!defined('ADMINPAGES_FORM_MASSACTION_OPT_DELETE')) define('ADMINPAGES_FORM_MASSACTION_OPT_DELETE','Usuń zaznaczone');
if(!defined('ADMINPAGES_FORM_MASSACTION_OPT_CLONE')) define('ADMINPAGES_FORM_MASSACTION_OPT_CLONE','Skopiuj zaznaczone');
if(!defined('ADMINPAGES_FORM_MASSACTION_OPT_RENAME')) define('ADMINPAGES_FORM_MASSACTION_OPT_RENAME','Zmień nazwę zaznaczonych');
if(!defined('ADMINPAGES_FORM_MASSACTION_OPT_ACL')) define('ADMINPAGES_FORM_MASSACTION_OPT_ACL','Zmień prawa dostępu do zaznaczonych stron');
if(!defined('ADMINPAGES_FORM_MASSACTION_OPT_REVERT')) define('ADMINPAGES_FORM_MASSACTION_OPT_REVERT','Cofnij zaznaczone do wcześniejszej wersji');
if(!defined('ADMINPAGES_FORM_MASSACTION_REVERT_ERROR')) define('ADMINPAGES_FORM_MASSACTION_REVERT_ERROR','Nie można cofnąć');
if(!defined('ADMINPAGES_FORM_MASSACTION_SUBMIT')) define('ADMINPAGES_FORM_MASSACTION_SUBMIT','Rozpocznij');
if(!defined('ADMINPAGES_ERROR_NO_MATCHES')) define('ADMINPAGES_ERROR_NO_MATCHES','Przepraszamy, znaleziono stron pasujących do wzorca: "%s"');
if(!defined('ADMINPAGES_LABEL_EDIT_NOTE')) define('ADMINPAGES_LABEL_EDIT_NOTE','Wprowadź opis zmian lub pozostaw puste, aby zastosować wartość domyślną');
if(!defined('ADMINPAGES_CANCEL_LABEL')) define('ADMINPAGES_CANCEL_LABEL', 'Anuluj');
/**#@-*/

/**#@+
 * Language constants used by the {@link adminusers.php adminusers} action
 */
if(!defined('ADMINUSERS_DEFAULT_RECORDS_LIMIT')) define('ADMINUSERS_DEFAULT_RECORDS_LIMIT', '10'); # number of records per page
if(!defined('ADMINUSERS_DEFAULT_MIN_RECORDS_DISPLAY')) define('ADMINUSERS_DEFAULT_MIN_RECORDS_DISPLAY', '5'); # min number of records
if(!defined('ADMINUSERS_DEFAULT_RECORDS_RANGE')) define('ADMINUSERS_DEFAULT_RECORDS_RANGE',serialize(array('10','50','100','500','1000'))); #range array for records pager
if(!defined('ADMINUSERS_DEFAULT_SORT_FIELD')) define('ADMINUSERS_DEFAULT_SORT_FIELD', 'signuptime'); # sort field
if(!defined('ADMINUSERS_DEFAULT_SORT_ORDER')) define('ADMINUSERS_DEFAULT_SORT_ORDER', 'desc'); # sort order, ascendant or descendant
if(!defined('ADMINUSERS_DEFAULT_START')) define('ADMINUSERS_DEFAULT_START', '0'); # start record
if(!defined('ADMINUSERS_DEFAULT_SEARCH')) define('ADMINUSERS_DEFAULT_SEARCH', ''); # keyword to restrict search
if(!defined('ADMINUSERS_ALTERNATE_ROW_COLOR')) define('ADMINUSERS_ALTERNATE_ROW_COLOR', '1'); # switch alternate row color
if(!defined('ADMINUSERS_STAT_COLUMN_COLOR')) define('ADMINUSERS_STAT_COLUMN_COLOR', '1'); # switch color for statistics columns
if(!defined('ADMINUSERS_OWNED_ICON')) define('ADMINUSERS_OWNED_ICON', 'images/icons/keyring.png');
if(!defined('ADMINUSERS_EDITS_ICON')) define('ADMINUSERS_EDITS_ICON', 'images/icons/edit.png');
if(!defined('ADMINUSERS_COMMENTS_ICON')) define('ADMINUSERS_COMMENTS_ICON', 'images/icons/comment.png');
if(!defined('ADMINUSERS_PAGE_TITLE')) define('ADMINUSERS_PAGE_TITLE','Administracja użytkownikami');
if(!defined('ADMINUSERS_FORM_LEGEND')) define('ADMINUSERS_FORM_LEGEND','Filtr widoku:');
if(!defined('ADMINUSERS_FORM_SEARCH_STRING_LABEL')) define('ADMINUSERS_FORM_SEARCH_STRING_LABEL','Szukaj użytkownika:');
if(!defined('ADMINUSERS_FORM_SEARCH_STRING_TITLE')) define('ADMINUSERS_FORM_SEARCH_STRING_TITLE','Kryteria wyszukiwania');
if(!defined('ADMINUSERS_FORM_SEARCH_SUBMIT')) define('ADMINUSERS_FORM_SEARCH_SUBMIT','Szukaj');
if(!defined('ADMINUSERS_FORM_PAGER_LABEL_BEFORE')) define('ADMINUSERS_FORM_PAGER_LABEL_BEFORE','Wyświetl');
if(!defined('ADMINUSERS_FORM_PAGER_TITLE')) define('ADMINUSERS_FORM_PAGER_TITLE','Określ ilość wpisów na stronie');
if(!defined('ADMINUSERS_FORM_PAGER_LABEL_AFTER')) define('ADMINUSERS_FORM_PAGER_LABEL_AFTER','wpisów na stronie');
if(!defined('ADMINUSERS_FORM_PAGER_SUBMIT')) define('ADMINUSERS_FORM_PAGER_SUBMIT','Wyświetl');
if(!defined('ADMINUSERS_FORM_PAGER_LINK')) define('ADMINUSERS_FORM_PAGER_LINK','Wyświetl wpisy od %d do %d');
if(!defined('ADMINUSERS_FORM_RESULT_INFO')) define('ADMINUSERS_FORM_RESULT_INFO','Wpisy');
if(!defined('ADMINUSERS_FORM_RESULT_SORTED_BY')) define('ADMINUSERS_FORM_RESULT_SORTED_BY','Kryterium sortowania:');
if(!defined('ADMINUSERS_TABLE_HEADING_USERNAME')) define('ADMINUSERS_TABLE_HEADING_USERNAME','Nazwa użytkownika');
if(!defined('ADMINUSERS_TABLE_HEADING_USERNAME_TITLE')) define('ADMINUSERS_TABLE_HEADING_USERNAME_TITLE','Sortuj wg nazwy użytkownika');
if(!defined('ADMINUSERS_TABLE_HEADING_EMAIL')) define('ADMINUSERS_TABLE_HEADING_EMAIL','Email');
if(!defined('ADMINUSERS_TABLE_HEADING_EMAIL_TITLE')) define('ADMINUSERS_TABLE_HEADING_EMAIL_TITLE','Sortuj wg adresu email');
if(!defined('ADMINUSERS_TABLE_HEADING_SIGNUPTIME')) define('ADMINUSERS_TABLE_HEADING_SIGNUPTIME','Utworzenie konta');
if(!defined('ADMINUSERS_TABLE_HEADING_SIGNUPTIME_TITLE')) define('ADMINUSERS_TABLE_HEADING_SIGNUPTIME_TITLE','Sortuj wg czasu utworzenia konta');
if(!defined('ADMINUSERS_TABLE_HEADING_SIGNUPIP')) define('ADMINUSERS_TABLE_HEADING_SIGNUPIP','IP utworzenia konta');
if(!defined('ADMINUSERS_TABLE_HEADING_SIGNUPIP_TITLE')) define('ADMINUSERS_TABLE_HEADING_SIGNUPIP_TITLE','Sortuj wg numeru IP z którego utworzono konto');
if(!defined('ADMINUSERS_TABLE_SUMMARY')) define('ADMINUSERS_TABLE_SUMMARY','Lista zarejestrowanych użytkowników');
if(!defined('ADMINUSERS_TABLE_HEADING_ACTIONS')) define('ADMINUSERS_TABLE_HEADING_ACTIONS','Działania');
if(!defined('ADMINUSERS_TABLE_HEADING_OWNED_TITLE')) define('ADMINUSERS_TABLE_HEADING_OWNED_TITLE','Posiadane strony');
if(!defined('ADMINUSERS_TABLE_HEADING_EDITS_TITLE')) define('ADMINUSERS_TABLE_HEADING_EDITS_TITLE','Edycje');
if(!defined('ADMINUSERS_TABLE_HEADING_COMMENTS_TITLE')) define('ADMINUSERS_TABLE_HEADING_COMMENTS_TITLE','Komentarze');
if(!defined('ADMINUSERS_ACTION_DELETE_LINK_TITLE')) define('ADMINUSERS_ACTION_DELETE_LINK_TITLE','Usuń użytkownika: %s');
if(!defined('ADMINUSERS_ACTION_DELETE_LINK')) define('ADMINUSERS_ACTION_DELETE_LINK','usuń');
if(!defined('ADMINUSERS_TABLE_CELL_OWNED_TITLE')) define('ADMINUSERS_TABLE_CELL_OWNED_TITLE','Wyświetl strony posiadane przez użytkownika: %s (%d)');
if(!defined('ADMINUSERS_TABLE_CELL_EDITS_TITLE')) define('ADMINUSERS_TABLE_CELL_EDITS_TITLE','Wyświetl edycje użytkownika: %s (%d)');
if(!defined('ADMINUSERS_TABLE_CELL_COMMENTS_TITLE')) define('ADMINUSERS_TABLE_CELL_COMMENTS_TITLE','Wyświetl komentarze użytkownika: %s (%d)');
if(!defined('ADMINUSERS_SELECT_RECORD_TITLE')) define('ADMINUSERS_SELECT_RECORD_TITLE','Wybierz użytkownika: %s');
if(!defined('ADMINUSERS_SELECT_ALL_TITLE')) define('ADMINUSERS_SELECT_ALL_TITLE','Wybierz wszystkich użytkowników');
if(!defined('ADMINUSERS_SELECT_ALL')) define('ADMINUSERS_SELECT_ALL','Wybierz wszystkich');
if(!defined('ADMINUSERS_DESELECT_ALL_TITLE')) define('ADMINUSERS_DESELECT_ALL_TITLE','Anuluj wybór');
if(!defined('ADMINUSERS_DESELECT_ALL')) define('ADMINUSERS_DESELECT_ALL','Anuluj wybór');
if(!defined('ADMINUSERS_FORM_MASSACTION_LEGEND')) define('ADMINUSERS_FORM_MASSACTION_LEGEND','Działanie na wielu użytkownikach');
if(!defined('ADMINUSERS_FORM_MASSACTION_LABEL')) define('ADMINUSERS_FORM_MASSACTION_LABEL','Działanie: ');
if(!defined('ADMINUSERS_FORM_MASSACTION_SELECT_TITLE')) define('ADMINUSERS_FORM_MASSACTION_SELECT_TITLE','Wybierz działanie do wykonania na zaznaczonych użytkownikach');
if(!defined('ADMINUSERS_FORM_MASSACTION_OPT_DELETE')) define('ADMINUSERS_FORM_MASSACTION_OPT_DELETE','Usuń zaznaczonych');
if(!defined('ADMINUSERS_FORM_MASSACTION_DELETE_ERROR')) define('ADMINUSERS_FORM_MASSACTION_DELETE_ERROR', 'Nie można usunąć administratorów');
if(!defined('ADMINUSERS_FORM_MASSACTION_SUBMIT')) define('ADMINUSERS_FORM_MASSACTION_SUBMIT','Rozpocznij');
if(!defined('ADMINUSERS_ERROR_NO_MATCHES')) define('ADMINUSERS_ERROR_NO_MATCHES','Przepraszamy, nie znaleziono użytkowników pasujących do wzorca: "%s"');
if(!defined('ADMINUSERS_DELETE_USERS_HEADING')) define('ADMINUSERS_DELETE_USERS_HEADING', 'Usunąć wskazanych użytkowników?');
if(!defined('ADMINUSERS_DELETE_USERS_BUTTON')) define('ADMINUSERS_DELETE_USERS_BUTTON', 'Usuń użytkowników');
if(!defined('ADMINUSERS_CANCEL_BUTTON')) define('ADMINUSERS_CANCEL_BUTTON', 'Anuluj');
if(!defined('ADMINUSERS_USERDELETE_SUCCESS')) define('ADMINUSERS_USERDELETE_SUCCESS', 'Usunięto użytkownika');
if(!defined('ADMINUSERS_USERDELETE_FAILURE')) define('ADMINUSERS_USERDELETE_FAILURE', 'Przepraszamy, usunięcie użytkownika nie powiodło się. Sprawdź ustawienia administratora');
/**#@-*/

/**#@+
 * Language constant used by the {@link calendar.php calendar} action
 */
// calendar
if(!defined('FMT_SUMMARY')) define('FMT_SUMMARY', 'Kalendarz dla %s');	// %s - ???@@@ TODO KJT What is it?
if(!defined('MIN_DATETIME')) define('MIN_DATETIME', strtotime('1970-01-01 00:00:00 GMT')); # earliest timestamp PHP can handle (Windows and some others - to be safe)
if(!defined('MAX_DATETIME')) define('MAX_DATETIME', strtotime('2038-01-19 03:04:07 GMT')); # latest timestamp PHP can handle
if(!defined('MIN_YEAR')) define('MIN_YEAR', date('Y',MIN_DATETIME));
if(!defined('MAX_YEAR')) define('MAX_YEAR', date('Y',MAX_DATETIME)-1); # don't include partial January 2038
if(!defined('CUR_YEAR')) define('CUR_YEAR', date('Y',time()));
if(!defined('CUR_MONTH')) define('CUR_MONTH', date('n',time()));
if(!defined('LOC_MON_YEAR')) define('LOC_MON_YEAR', "%B %Y"); # i18n
/**#@-*/

/**#@+
 * Language constant used by the {@link category.php category} action
 */
// category
if(!defined('ERROR_NO_PAGES')) define('ERROR_NO_PAGES', 'Przepraszamy, do kategorii „%s” nie należą żadne strony');	// %s - ???@@@
if(!defined('PAGES_IN_CATEGORY')) define('PAGES_IN_CATEGORY', 'Do kategorii „%2$s” należą następujące strony (w ilości %1$d):'); // %1$d number found; %2$s category
/**#@-*/

/**#@+
 * Language constant used by the {@link checkversion.php checkversion} action
 */
if(!defined('CHECKVERSION_HOST')) define('CHECKVERSION_HOST', 'wikkawiki.org');
if(!defined('CHECKVERSION_RELEASE_FILE')) define('CHECKVERSION_RELEASE_FILE', '/downloads/latest_wikka_version.txt');
if(!defined('CHECKVERSION_DOWNLOAD_URL')) define('CHECKVERSION_DOWNLOAD_URL', 'http://docs.wikkawiki.org/WhatsNew');
if(!defined('CHECKVERSION_CONNECTION_TIMEOUT')) define('CHECKVERSION_CONNECTION_TIMEOUT', 5);
if(!defined('DEBUG_TIME_ELAPSED')) define('DEBUG_TIME_ELAPSED', '[czas pracy: %d]');
if(!defined('DEBUG_PHP_VERSION_UNSUPPORTED')) define('DEBUG_PHP_VERSION_UNSUPPORTED', '[PHP %2Ss w systemie %1$s nie obsługuje tej funkcji]');
if(!defined('DEBUG_ALLOW_FURL_DISABLED')) define('DEBUG_ALLOW_FURL_DISABLED', '[funkcja allow_url_fopen jest wyłączona]');
if(!defined('DEBUG_CANNOT_RESOLVE_HOSTNAME')) define('DEBUG_CANNOT_RESOLVE_HOSTNAME', '[Nie można pobrać nazwy hosta %s]');
if(!defined('DEBUG_CANNOT_CONNECT')) define('DEBUG_CANNOT_CONNECT', '[Nie można się połączyć]');
if(!defined('DEBUG_NEW_VERSION_AVAILABLE')) define('DEBUG_NEW_VERSION_AVAILABLE', '[%s z hosta %s]');
if(!defined('CHECKVERSION_CANNOT_CONNECT')) define('CHECKVERSION_CANNOT_CONNECT', '<div title="Nie można nawiązać połączenia sieciowego" style="clear: both; text-align: center; float: left; width: 300px; border: 1px solid %s; background-color: %s; color: %s; margin: 10px 0">'."\n"
	.'<div style="padding: 0 3px 0 3px; background-color: %s; font-size: 85%%; font-weight: bold">BŁĄD WYSZUKIWANIA AKTUALIZACJI</div>'."\n"
	.'<div style="padding: 0 3px 2px 3px; font-size: 85%%; line-height: 150%%; border-top: 1px solid %s;">'."\n"
	.'Nie można nawiązać połączenia do serwera WikkaWiki. Aby zapobiec opóźnieniom podczas ładowania tej strony, rozważ wyłączenie tej funkcji przez ustawienie w pliku wikka.config.php zmiennej enable_version_check na 0.'."\n"
	.'</div>'."\n"
	.'</div>'."\n"
	.'<div class="clear"></div>'."\n");
if(!defined('CHECKVERSION_NEW_VERSION_AVAILABLE')) define('CHECKVERSION_NEW_VERSION_AVAILABLE', '<div title="Nowa wersja WikkaWiki jest dostępna. Dokonaj aktualizacji!" style="clear: both; text-align: center; float: left; width: 300px; border: 1px solid %s; background-color: %s; color: %s; margin: 10px 0">'."\n"
	.'<div style="padding: 0 3px 0 3px; background-color: %s; font-size: 85%%; font-weight: bold">AKTUALIZACJA JEST DOSTĘPNA</div>'."\n"
	.'<div style="padding: 0 3px 2px 3px; font-size: 85%%; line-height: 150%%; border-top: 1px solid %s;">'."\n"
	.'<strong>WikkaWiki %s</strong> jest dostępna. <a href="%s">Pobierz</a>!'."\n"
	.'</div>'."\n"
	.'</div>'."\n"
	.'<div class="clear"></div>'."\n");
/**#@-*/

/**#@+
 * Language constant used by the {@link clonelink.php clonelink} action
 */
if(!defined('CLONELINK_TEXT')) define('CLONELINK_TEXT', '[Duplikuj]');
if(!defined('CLONELINK_TITLE')) define('CLONELINK_TITLE', 'Utwórz kopię tej strony');
/**#@-*/

/**#@+
 * Language constant used by the {@link color.php color} action
 */
// color
if(!defined('ERROR_NO_TEXT_GIVEN')) define('ERROR_NO_TEXT_GIVEN', 'Nie wpisano tekstu!');
if(!defined('ERROR_NO_COLOR_SPECIFIED')) define('ERROR_NO_COLOR_SPECIFIED', 'Nie określono koloru!');
if(!defined('PATTERN_VALID_HEX_COLOR')) define('PATTERN_VALID_HEX_COLOR', '#(?>[\da-f]{3}){1,2}');
if(!defined('PATTERN_VALID_RGB_COLOR')) define('PATTERN_VALID_RGB_COLOR', 'rgb\(\s*\d+((?>\.\d*)?%)?\s*(?>,\s*\d+(?(1)(\.\d*)?%)\s*){2}\)');
/**#@-*/

/**#@+
 * Language constant used by the {@link contact.php contact} action
 */
// contact
if(!defined('CONTACTLINK_TITLE')) define('CONTACTLINK_TITLE', 'Prześlij nam swoje uwagi');
if(!defined('CONTACTLINK_TEXT')) define('CONTACTLINK_TEXT', 'Kontakt');
/**#@-*/

/**#@+
 * Language constant used by the {@link countowned.php countowned} action
 */
// countowned
if(!defined('DISPLAY_MYPAGES_LINK_TITLE')) define('DISPLAY_MYPAGES_LINK_TITLE', 'Zobacz listę stron, które posiadasz');
/**#@-*/

/**#@+
 * Language constant used by the {@link countpages.php countpages} action
 */
// countpages
if(!defined('INDEX_LINK_TITLE')) define('INDEX_LINK_TITLE', 'Wyświetl alfabetyczny indeks stron');
/**#@-*/

/**#@+
 * Language constant used by the {@link dbinfo.php dbinfo} action
 */
// dbinfo
if(!defined('HD_DBINFO')) define('HD_DBINFO', 'Informacja o bazie danych');
if(!defined('HD_DBINFO_DB')) define('HD_DBINFO_DB', 'Baza danych');
if(!defined('HD_DBINFO_TABLES')) define('HD_DBINFO_TABLES', 'Tabele');
if(!defined('HD_DB_CREATE_DDL')) define('HD_DB_CREATE_DDL', 'Kod SQL opisujący strukturę bazy %s:');	# %s will hold database name
if(!defined('HD_TABLE_CREATE_DDL')) define('HD_TABLE_CREATE_DDL', 'Kod SQL opisujący strukturę tabeli %s:');	# %s will hold table name
if(!defined('TXT_INFO_1')) define('TXT_INFO_1','Niniejsze narzędzie dostarcza niektórych informacji o bazie (lub bazach) danych i tabelach istniejących w tym systemie.');
if(!defined('TXT_INFO_2')) define('TXT_INFO_2',' Jest możliwe, że nie wszystkie istniejące bazy danych lub tabele zostały wyświetlone poniżej &mdash; zależy to od poziomu praw dostępu posiadanych przez WikkaWiki.');
if(!defined('TXT_INFO_3')) define('TXT_INFO_3',' W przypadku, gdy podano kod SQL służący do utworzenia baz danych lub tabel, zawiera on wszystkie informacje potrzebne do kompletnego odtworzenia ich struktury, ');
if(!defined('TXT_INFO_4')) define('TXT_INFO_4',' łącznie z wartościami domyślnymi (które mogły nie zostać podane jawnie, lecz wynikają z ustawień serwera).');
if(!defined('FORM_SELDB_LEGEND')) define('FORM_SELDB_LEGEND', 'Bazy danych');
if(!defined('FORM_SELTABLE_LEGEND')) define('FORM_SELTABLE_LEGEND', 'Tabele');
if(!defined('FORM_SELDB_OPT_LABEL')) define('FORM_SELDB_OPT_LABEL', 'Wybierz bazę danych:');
if(!defined('FORM_SELTABLE_OPT_LABEL')) define('FORM_SELTABLE_OPT_LABEL', 'Wybierz tabelę:');
if(!defined('FORM_SUBMIT_SELDB')) define('FORM_SUBMIT_SELDB', 'Wybierz');
if(!defined('FORM_SUBMIT_SELTABLE')) define('FORM_SUBMIT_SELTABLE', 'Wybierz');
if(!defined('MSG_ONLY_ADMIN')) define('MSG_ONLY_ADMIN', 'Przepraszamy, tylko administratorzy mają dostęp do tych informacji.');
if(!defined('MSG_SINGLE_DB')) define('MSG_SINGLE_DB', 'Informacje o bazie dancych <tt>%s</tt>.');	# %s will hold database name
if(!defined('MSG_NO_TABLES')) define('MSG_NO_TABLES', 'W bazie <tt>%s</tt> nie znaleziono żadnych tabel. Możliwe, że aktualny użytkownik MySQL nie ma odpowiednich praw dostępu do tej bazy.');	# %s will hold database name
if(!defined('MSG_NO_DB_DDL')) define('MSG_NO_DB_DDL', 'Nie udało się pozyskać kodu SQL tworzącego bazę <tt>%s</tt>.');	# %s will hold database name
if(!defined('MSG_NO_TABLE_DDL')) define('MSG_NO_TABLE_DDL', 'Nie udało się pozyskać kodu SQL tworzącego tabelę <tt>%s</tt>.');# %s will hold table name
/**#@-*/

/**#@+
 * Language constant used by the {@link deletelink.php deletelink} action
 */
if(!defined('DELETELINK_TEXT')) define('DELETELINK_TEXT', '[Usuń]');
if(!defined('DELETELINK_TITLE')) define('DELETELINK_TITLE', 'Usuń tę stronę (wymaga potwierdzenia)');
/**#@-*/

/**#@+
 * Language constant used by the {@link editlink.php editlink} action
 */
if(!defined('EDITLINK_TEXT')) define('EDITLINK_TEXT', '[Edytuj]');
if(!defined('SHOWLINK_TEXT')) define('SHOWLINK_TEXT', '[Pokaż]');
if(!defined('SHOWCODELINK_TEXT')) define('SHOWCODELINK_TEXT', '[Źródło]');
if(!defined('EDITLINK_TITLE')) define('EDITLINK_TITLE', 'Edytuj tę stronę'); //KJT: phrase different than in English: 'Edit this page'
if(!defined('SHOWLINK_TITLE')) define('SHOWLINK_TITLE', 'Wyświetl treść strony');
if(!defined('SHOWCODELINK_TITLE')) define('SHOWCODELINK_TITLE', 'Wyświetl kod źródłowy strony');
/**#@-*/

/**#@+
 * Language constant used by the {@link emailpassword.php emailpassword} action
 */
// emailpassword
if(!defined('PW_FORGOTTEN_HEADING')) define('PW_FORGOTTEN_HEADING', 'Przypomnienie hasła');
if(!defined('PW_CHK_SENT')) define('PW_CHK_SENT', 'Przypomnienie hasła zostało wysłane na adres email użytkownika „%s”.'); // %s username
if(!defined('PW_FORGOTTEN_MAIL')) define('PW_FORGOTTEN_MAIL', 'Witaj %1$s,

ktoś zażądał, aby na ten adres email zostało wysłane przypomnienie hasła 
do strony "%2$s". Jeśli to nie Ty żądałeś przypomnienia hasła, po prostu 
zignoruj tę wiadomość, a Twoje hasło nie zostanie zmienione.

Nazwa użytkownika: %1$s
Przypomnienie hasła: %3$s
Adres strony: %4$s

Nie zapomnij zmienić hasła zaraz po zalogowaniu!');
// %1$s - username; %2$s - wiki name; %3$s - md5 sum of pw; %4$s - login url of the wiki
if(!defined('PW_FORGOTTEN_MAIL_REF')) define('PW_FORGOTTEN_MAIL_REF', 'Przypomnienie hasła użytkownika %s'); // %s - wiki name
if(!defined('PW_FORM_TEXT')) define('PW_FORM_TEXT', 'Podaj swoją nazwę użytkownika. Przypomnienie hasła zostanie wysłane na adres email podany podczas rejestracji.');
if(!defined('PW_FORM_FIELDSET_LEGEND')) define('PW_FORM_FIELDSET_LEGEND', 'Nazwa użytkownika:');
if(!defined('ERROR_UNKNOWN_USER')) define('ERROR_UNKNOWN_USER', 'Taki użytkownik nie istnieje!');
#if(!defined('ERROR_MAIL_NOT_SENT')) define('ERROR_MAIL_NOT_SENT', 'Wystąpił błąd podczas wysyłania przypomnienia hasła. Być może wysyłanie wiadomości jest zablokowane. Skontaktuj się z administratorem serwera.');
if(!defined('ERROR_MAIL_NOT_SENT')) define('ERROR_MAIL_NOT_SENT', 'Wystąpił błąd podczas wysyłania przypomnienia hasła. Być może wysyłanie wiadomości jest zablokowane. Skontaktuj się z administratorem, umieszczając komentarz do tej strony.');
if(!defined('BUTTON_SEND_PW')) define('BUTTON_SEND_PW', 'Wyślij przypomnienie');
if(!defined('USERSETTINGS_REF')) define('USERSETTINGS_REF', 'Wróć do strony %s.'); // %s - UserSettings link
if(!defined('USERSETTINGS_LINK')) define('USERSETTINGS_LINK', 'Wróć do [[UserSettings strony logowania]].');
/**#@-*/

/**#@+
 * Language constant used by the {@link feedback.php feedback} action
 */
// feedback
if(!defined('FILL_FORM')) define('FILL_FORM', '<p>Aby wysłać nam swoje uwagi, wypełnij poniższy formularz:</p>'."\n");
if(!defined('FEEDBACK_NAME_LABEL')) define('FEEDBACK_NAME_LABEL', 'Twoje imię:');
if(!defined('FEEDBACK_EMAIL_LABEL')) define('FEEDBACK_EMAIL_LABEL', 'Twój adres email:');
if(!defined('FEEDBACK_COMMENTS_LABEL')) define('FEEDBACK_COMMENTS_LABEL', 'Komentarze:');
if(!defined('FEEDBACK_SEND_BUTTON')) define('FEEDBACK_SEND_BUTTON', 'Wyślij');
if(!defined('ERROR_EMPTY_NAME')) define('ERROR_EMPTY_NAME', 'Podaj imię');
if(!defined('ERROR_INVALID_EMAIL')) define('ERROR_INVALID_EMAIL', 'Podaj właściwy adres email');
if(!defined('ERROR_EMPTY_MESSAGE')) define('ERROR_EMPTY_MESSAGE', 'Wpisz wiadomość');
if(!defined('FEEDBACK_SUBJECT')) define('FEEDBACK_SUBJECT', 'Wiadomość wysłana z %s'); // %s - name of the wiki
if(!defined('SUCCESS_FEEDBACK_SENT')) define('SUCCESS_FEEDBACK_SENT', 'Dziękujemy, twoja wiadomość została wysłana do %1$s ---'
	.'Wróć do [[%2$s strony głównej]].');
// currently unused in feedback action:
if(!defined('ERROR_FEEDBACK_MAIL_NOT_SENT')) define('ERROR_FEEDBACK_MAIL_NOT_SENT', 'Przepraszamy, wystąpił błąd podczas wysyłania. Być może wysyłanie wiadomości jest zablokowane. Spróbuj innej metody skontaktowania się z użytkownikiem „%s”, na przykład poprzez umieszczenie komentarza do tej strony.'); // %s - name of the recipient
if(!defined('FEEDBACK_FORM_LEGEND')) define('FEEDBACK_FORM_LEGEND', 'Napisz do „%s”'); //%s - wikiname of the recipient
/**#@-*/

/**#@+
 * Language constant used by the {@link files.php files action} and {@link handlers/files.xml/files.xml.php files.xml handler}
 */
// files
if(!defined('ERROR_UPLOAD_DIRECTORY_NOT_WRITABLE')) define('ERROR_UPLOAD_DIRECTORY_NOT_WRITABLE', 'Upewnij się, że serwer WWW ma prawa zapisu do katalogu %s.'); // %s Upload folder ref #89
if(!defined('ERROR_UPLOAD_DIRECTORY_NOT_READABLE')) define('ERROR_UPLOAD_DIRECTORY_NOT_READABLE', 'Upewnij sie, że serwer WWW ma prawa odczytu katalogu %s.'); // %s Upload folder ref #89
if(!defined('ERROR_NONEXISTENT_FILE')) define('ERROR_NONEXISTENT_FILE', 'Przepraszamy, plik %s nie istnieje.'); // %s file name ref
if(!defined('ERROR_FILE_UPLOAD_INCOMPLETE')) define('ERROR_FILE_UPLOAD_INCOMPLETE', 'Transfer pliku został przerwany. Spróbuj ponownie.');
if(!defined('ERROR_UPLOADING_FILE')) define('ERROR_UPLOADING_FILE', 'Wystąpił błąd podczas transferu pliku.');
if(!defined('ERROR_FILE_ALREADY_EXISTS')) define('ERROR_FILE_ALREADY_EXISTS', 'Przepraszamy, plik %s już istnieje.'); // %s - file name ref
if(!defined('ERROR_EXTENSION_NOT_ALLOWED')) define('ERROR_EXTENSION_NOT_ALLOWED', 'Przepraszamy, pliki o tym rozszerzeniu nie są akceptowane.');
if(!defined('ERROR_FILETYPE_NOT_ALLOWED')) define('ERROR_FILETYPE_NOT_ALLOWED', 'Przepraszamy, pliki tego typu nie są akceptowane.');
if(!defined('ERROR_FILE_NOT_DELETED')) define('ERROR_FILE_NOT_DELETED', 'Przepraszamy, nie można skasować tego pliku!');
if(!defined('ERROR_FILE_TOO_BIG')) define('ERROR_FILE_TOO_BIG', 'Plik jest zbyt duży. Maksymalna dopuszczalna wielkość to %s.'); // %s - allowed filesize
if(!defined('ERROR_NO_FILE_SELECTED')) define('ERROR_NO_FILE_SELECTED', 'Nie wybrano pliku.');
if(!defined('ERROR_FILE_UPLOAD_IMPOSSIBLE')) define('ERROR_FILE_UPLOAD_IMPOSSIBLE', 'Transfer pliku nie jest możliwy, ze względu na błąd w konfiguracji serwera.');
if(!defined('SUCCESS_FILE_UPLOADED')) define('SUCCESS_FILE_UPLOADED', 'Transfer pliku zakończony pomyślnie.');
if(!defined('FILE_TABLE_CAPTION')) define('FILE_TABLE_CAPTION', 'Załączniki');
if(!defined('FILE_TABLE_HEADER_NAME')) define('FILE_TABLE_HEADER_NAME', 'Nazwa pliku');
if(!defined('FILE_TABLE_HEADER_SIZE')) define('FILE_TABLE_HEADER_SIZE', 'Rozmiar');
if(!defined('FILE_TABLE_HEADER_DATE')) define('FILE_TABLE_HEADER_DATE', 'Data ostatniej modyfikacji');
if(!defined('FILE_UPLOAD_FORM_LEGEND')) define('FILE_UPLOAD_FORM_LEGEND', 'Dodaj załącznik:');
if(!defined('FILE_UPLOAD_FORM_LABEL')) define('FILE_UPLOAD_FORM_LABEL', 'Plik:');
if(!defined('FILE_UPLOAD_FORM_BUTTON')) define('FILE_UPLOAD_FORM_BUTTON', 'Dodaj');
if(!defined('DOWNLOAD_LINK_TITLE')) define('DOWNLOAD_LINK_TITLE', 'Pobierz %s'); // %s - file name
if(!defined('DELETE_LINK_TITLE')) define('DELETE_LINK_TITLE', 'Usuń %s'); // %s - file name
if(!defined('NO_ATTACHMENTS')) define('NO_ATTACHMENTS', 'Ta strona nie zawiera załączników.');
if(!defined('FILES_DELETE_FILE')) define('FILES_DELETE_FILE', 'Usunąć plik?');
if(!defined('FILES_DELETE_FILE_BUTTON')) define('FILES_DELETE_FILE_BUTTON', 'Usuń plik');
if(!defined('FILES_CANCEL_BUTTON')) define('FILES_CANCEL_BUTTON', 'Anuluj');
if(!defined('FILE_DELETED')) define('FILE_DELETED', 'Plik usunięto');
if(!defined('UPLOAD_DATE_FORMAT')) define('UPLOAD_DATE_FORMAT', 'Y-m-d H:i'); //TODO use general config settings for date format
/**#@-*/

/**#@+
 * Language constant used by the {@link googleform.php googleform} action
 */
// googleform
if(!defined('GOOGLE_BUTTON')) define('GOOGLE_BUTTON', 'Szukaj w Google');
/**#@-*/

/**#@+
 * Language constant used by the {@link highscores.php highscores} action
 */
// include
if(!defined('HIGHSCORES_LABEL_EDITS')) define('HIGHSCORES_LABEL_EDITS', 'edycji');
if(!defined('HIGHSCORES_LABEL_COMMENTS')) define('HIGHSCORES_LABEL_COMMENTS', 'komentarzy');
if(!defined('HIGHSCORES_LABEL_PAGES')) define('HIGHSCORES_LABEL_PAGES', 'posiadanych stron');
if(!defined('HIGHSCORES_CAPTION')) define('HIGHSCORES_CAPTION', 'Najaktywniejsi redaktorzy wg ilości %2$s');
if(!defined('HIGHSCORES_HEADER_RANK')) define('HIGHSCORES_HEADER_RANK', 'pozycja');
if(!defined('HIGHSCORES_HEADER_USER')) define('HIGHSCORES_HEADER_USER', 'użytkownik');
if(!defined('HIGHSCORES_HEADER_PERCENTAGE')) define('HIGHSCORES_HEADER_PERCENTAGE', 'procent');
if(!defined('HIGHSCORES_DISPLAY_TOP')) define('HIGHSCORES_DISPLAY_TOP', 10); //limit output to top n users
if(!defined('HIGHSCORES_DEFAULT_STYLE')) define('HIGHSCORES_DEFAULT_STYLE', 'complex'); //set default layout style
if(!defined('HIGHSCORES_DEFAULT_RANK')) define('HIGHSCORES_DEFAULT_RANK', 'pages'); //set default layout style
/**#@-*/

/**#@+
 * Language constants used by the {@link historylink.php historylink} action
 */
if(!defined('HISTORYLINK_TEXT')) define('HISTORYLINK_TEXT', '[Historia]');
if(!defined('HISTORYLINK_TITLE')) define('HISTORYLINK_TITLE', 'Zobacz historię zmian tej strony'); //KJT: phrase different than in English: 'View recent edits to this page'
/**#@-*/

/**#@+
 * Language constants used by the {@link include.php include} action
 */
// include
if(!defined('ERROR_CIRCULAR_REFERENCE')) define('ERROR_CIRCULAR_REFERENCE', 'Wykryto zapętlenie odnośników!');
if(!defined('ERROR_TARGET_ACL')) define('ERROR_TARGET_ACL', "Nie masz prawa odczytu strony wstawionej <tt>%s</tt>");
/**#@-*/

/**#@+
 * Language constant used by the {@link lastedit.php lastedit} action
 */
// lastedit
if(!defined('LASTEDIT_DESC')) define('LASTEDIT_DESC', 'Ostatnio edytowany przez: „%s”'); // %s user name
if(!defined('LASTEDIT_DIFF_LINK_TITLE')) define('LASTEDIT_DIFF_LINK_TITLE', 'Porównaj z poprzednią wersją');
if(!defined('DEFAULT_SHOW')) define('DEFAULT_SHOW', '3');
if(!defined('DATE_FORMAT')) define('DATE_FORMAT', 'D, d M Y'); #TODO make this system-configurable
if(!defined('TIME_FORMAT')) define('TIME_FORMAT', 'H:i T'); #TODO make this system-configurable
if(!defined('LASTEDIT_BOX')) define('LASTEDIT_BOX', 'lastedit');
if(!defined('LASTEDIT_NOTES')) define('LASTEDIT_NOTES', 'lastedit_notes');
/**#@-*/

/**#@+
 * Language constant used by the {@link lastusers.php lastusers} action
 */
// lastusers
if(!defined('LASTUSERS_CAPTION')) define('LASTUSERS_CAPTION', 'Ostatnio zarejestrowani użytkownicy');
if(!defined('SIGNUP_DATE_TIME')) define('SIGNUP_DATE_TIME', 'Data rejestracji');
if(!defined('NAME_TH')) define('NAME_TH', 'Nazwa użytkownika');
if(!defined('OWNED_PAGES_TH')) define('OWNED_PAGES_TH', 'Posiadane strony');
if(!defined('SIGNUP_DATE_TIME_TH')) define('SIGNUP_DATE_TIME_TH', 'Data rejestracji');
if(!defined('LASTUSERS_DEFAULT_STYLE')) define('LASTUSERS_DEFAULT_STYLE', 'complex'); # consistent parameter naming with HighScores action
if(!defined('LASTUSERS_MAX_USERS_DISPLAY')) define('LASTUSERS_MAX_USERS_DISPLAY', 10);
/**#@-*/

/**#@+
 * Language constant used by the {@link mindmap.php mindmap} action
 */
// mindmap
if(!defined('MM_JRE_INSTALL_REQ')) define('MM_JRE_INSTALL_REQ', 'Proszę zainstalować %s.'); // %s - JRE install link
if(!defined('MM_DOWNLOAD_LINK_DESC')) define('MM_DOWNLOAD_LINK_DESC', 'Pobierz tę mindmapę');
if(!defined('MM_EDIT')) define('MM_EDIT', 'Użyj %s, aby edytować'); // %s - link to freemind project @todo KJT Test it!
if(!defined('MM_FULLSCREEN_LINK_DESC')) define('MM_FULLSCREEN_LINK_DESC', 'Otwórz na pełnym ekranie');
if(!defined('ERROR_INVALID_MM_SYNTAX')) define('ERROR_INVALID_MM_SYNTAX', 'Błąd: niewłaściwa składnia akcji MindMap.');
if(!defined('PROPER_USAGE_MM_SYNTAX')) define('PROPER_USAGE_MM_SYNTAX', 'Sposób użycia: %1$s lub %2$s'); // %1$s syntax sample 1; %2$s syntax sample 2
/**#@-*/

/**#@+
 * Language constant used by the {@link mychanges.php mychanges} action
 */
// mychanges
if(!defined('NO_PAGES_EDITED')) define('NO_PAGES_EDITED', 'Nie edytowałeś jeszcze żadnych stron.');
if(!defined('MYCHANGES_ALPHA_LIST')) define('MYCHANGES_ALPHA_LIST', "Lista stron edytowanych przez użytkownika „%s”, wraz z datą ostatniej edycji.");
if(!defined('MYCHANGES_DATE_LIST')) define('MYCHANGES_DATE_LIST', "Lista stron edytowanych przez użytkownika „%s”, uporządkowana wg daty ostatniej edycji.");
if(!defined('ORDER_DATE_LINK_DESC')) define('ORDER_DATE_LINK_DESC', 'uporządkuj wg daty');
if(!defined('ORDER_ALPHA_LINK_DESC')) define('ORDER_ALPHA_LINK_DESC', 'uporządkuj alfabetycznie');
if(!defined('MYCHANGES_NOT_LOGGED_IN')) define('MYCHANGES_NOT_LOGGED_IN', "Nie jesteś zalogowany, więc nie można wyświetlić listy stron, które edytowałeś.");
/**#@-*/

/**#@+
 * Language constant used by the {@link mypages.php mypages} action
 */
// mypages
if(!defined('MYPAGES_CAPTION')) define('MYPAGES_CAPTION', "Lista stron, których właścicielem jest %s.");
if(!defined('MYPAGES_NONE_OWNED')) define('MYPAGES_NONE_OWNED', "%s nie posiada żadnych stron.");
if(!defined('MYPAGES_NOT_LOGGED_IN')) define('MYPAGES_NOT_LOGGED_IN', 'Nie jesteś zalogowany, więc nie można wyświetlić listy stron, które posiadasz.');
/**#@-*/

/**#@+
 * Language constant used by the {@link newpage.php newpage} action
 */
// newpage
if(!defined('NEWPAGE_CREATE_LEGEND')) define('NEWPAGE_CREATE_LEGEND', 'Utwórz nową stronę');
if(!defined('NEWPAGE_CREATE_BUTTON')) define('NEWPAGE_CREATE_BUTTON', 'Utwórz');
/**#@-*/

/**#@+
 * Language constant used by the {@link orphanedpages.php orphanedpages} action
 */
// orphanedpages
if(!defined('NO_ORPHANED_PAGES')) define('NO_ORPHANED_PAGES', 'Wszystkie strony posiadają powiązania z innymi. Znakomicie!');

/**#@+
 * Language constant used by the {@link ownedpages.php ownedpages} action
 */
// ownedpages
if(!defined('OWNEDPAGES_COUNTS')) define('OWNEDPAGES_COUNTS', 'Posiadasz %1$s stron z %2$s stron utworzonych w tej witrynie.'); // %1$s - number of pages owned; %2$s - total number of pages
if(!defined('OWNEDPAGES_PERCENTAGE')) define('OWNEDPAGES_PERCENTAGE', 'Oznacza to, że posiadasz %s całości.'); // %s percentage of pages owned
/**#@-*/

/**#@+
 * Language constant used by the {@link ownerlink.php ownerlink} action
 */
if(!defined('OWNERLINK_PUBLIC_PAGE')) define('OWNERLINK_PUBLIC_PAGE', 'Strona publiczna');
if(!defined('OWNERLINK_NOBODY')) define('OWNERLINK_NOBODY', 'Nikt');
if(!defined('OWNERLINK_OWNER')) define('OWNERLINK_OWNER', 'Właściciel:');
if(!defined('OWNERLINK_SELF')) define('OWNERLINK_SELF', 'Jesteś właścicielem tej strony');
if(!defined('EDITACLLINK_TEXT')) define('EDITACLLINK_TEXT', '[Edytuj uprawnienia]');
if(!defined('EDITACLLINK_TITLE')) define('EDITACLLINK_TITLE', 'Zmień prawa dostępu do tej strony');
if(!defined('CLAIMLINK_TEXT')) define('CLAIMLINK_TEXT', '[Przejmij]');
if(!defined('CLAIMLINK_TITLE')) define('CLAIMLINK_TITLE', 'Przejmij stronę na własność'); //KJT: phrase different than in English: 'Take ownership of the page'
/**#@-*/

/**#@+
 * Language constant used by the {@link pageindex.php pageindex} action
 */
// pageindex
if(!defined('PAGEINDEX_HEADING')) define('PAGEINDEX_HEADING', 'Indeks stron');
if(!defined('PAGEINDEX_CAPTION')) define('PAGEINDEX_CAPTION', 'Alfabetyczna lista stron utworzonych w tej witrynie.');
if(!defined('PAGEINDEX_OWNED_PAGES_CAPTION')) define('PAGEINDEX_OWNED_PAGES_CAPTION', 'Gwiazdką oznaczono strony, które posiadasz.');
if(!defined('PAGEINDEX_ALL_PAGES')) define('PAGEINDEX_ALL_PAGES', 'Wszystkie');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges.php recentchanges} action
 */
// recentchanges
if(!defined('RECENTCHANGES_HEADING')) define('RECENTCHANGES_HEADING', 'Strony ostatnio zmienione');
if(!defined('REVISIONS_LINK_TITLE')) define('REVISIONS_LINK_TITLE', 'Zobacz listę wersji strony %s'); // %s - page name
if(!defined('HISTORY_LINK_TITLE')) define('HISTORY_LINK_TITLE', 'Zobacz ostatnie zmiany na stronie %s'); // %s - page name
if(!defined('WIKIPING_ENABLED')) define('WIKIPING_ENABLED', 'Usługa WikiPing włączona: zmiany wprowadzane w tej witrynie są ogłaszane na %s'); // %s - link to wikiping server
if(!defined('RECENTCHANGES_NONE_FOUND')) define('RECENTCHANGES_NONE_FOUND', 'Żadne strony nie były ostatnio zmieniane.');
if(!defined('RECENTCHANGES_NONE_ACCESSIBLE')) define('RECENTCHANGES_NONE_ACCESSIBLE', 'Nie masz prawa dostępu do żadnej ze stron, które były ostatnio zmieniane.');
if(!defined('PAGE_EDITOR_DIVIDER')) define('PAGE_EDITOR_DIVIDER', '&#8594;');
if(!defined('MAX_REVISION_NUMBER')) define('MAX_REVISION_NUMBER', '50');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentcomments.php recentcomments} action
 */
// recentcomments
if(!defined('RECENTCOMMENTS_TIMESTAMP_CAPTION')) define('RECENTCOMMENTS_TIMESTAMP_CAPTION', '%s'); // %s - timestamp
if(!defined('RECENTCOMMENTS_NONE_FOUND')) define('RECENTCOMMENTS_NONE_FOUND', 'Nie znaleziono nowych komentarzy.');
if(!defined('RECENTCOMMENTS_NONE_FOUND_BY')) define('RECENTCOMMENTS_NONE_FOUND_BY', 'Użytkownik %s nie napisał jeszcze komentarzy.');
if(!defined('RECENTCOMMENTS_NONE_ACCESSIBLE')) define('RECENTCOMMENTS_NONE_ACCESSIBLE', 'Nie masz prawa dostępu do żadnych nowych komentarzy.');
if(!defined('RECENT_COMMENTS_HEADING')) define('RECENT_COMMENTS_HEADING', '=====Ostatnie komentarze=====');
if(!defined('COMMENT_DATE_FORMAT')) define('COMMENT_DATE_FORMAT', 'D, d M Y');
if(!defined('COMMENT_TIME_FORMAT')) define('COMMENT_TIME_FORMAT', 'H:i T');
if(!defined('COMMENT_SNIPPET_LENGTH')) define('COMMENT_SNIPPET_LENGTH', 120);
/**#@-*/

/**#@+
 * Language constant used by the {@link recentlycommented.php recentlycommented} action
 */
// recentlycommented
if(!defined('RECENTLYCOMMENTED_HEADING')) define('RECENTLYCOMMENTED_HEADING', 'Strony ostatnio skomentowane');
if(!defined('RECENTLYCOMMENTED_NONE_FOUND')) define('RECENTLYCOMMENTED_NONE_FOUND', 'Żadne strona nie została ostatnio skomentowana.');
if(!defined('RECENTLYCOMMENTED_NONE_FOUND_BY')) define('RECENTLYCOMMENTED_NONE_FOUND_BY', 'Użytkownik „%s” nie skomentował ostatnio żadnej strony.');
if(!defined('RECENTLYCOMMENTED_NONE_ACCESSIBLE')) define('RECENTLYCOMMENTED_NONE_ACCESSIBLE', 'Nie masz prawa dostępu do żadnej ze stron, które ostatnio skomentowano.');
/**#@-*/

/**#@+
 * Language constants used by the {@link redirect.php redirect} action
 */
if(!defined('PAGE_MOVED_TO')) define('PAGE_MOVED_TO', 'Strona została przemianowana na „%s”.'); # %s - targe page
if(!defined('REDIRECTED_FROM')) define('REDIRECTED_FROM', 'Przekierowano z „%s”.'); # %s - redirecting page
if(!defined('INVALID_REDIRECT')) define('INVALID_REDIRECT', 'Niewłaściwe przekierowanie. Strona docelowa musi istnieć.');
/**#@-*/

/**#@+
 * Language constant used by the {@link referrerslink.php referrerslink} action
 */
if(!defined('REFERRERSLINK_TEXT')) define('REFERRERSLINK_TEXT', '[Źródła odwiedzających]');
if(!defined('REFERRERSLINK_TITLE')) define('REFERRERSLINK_TITLE', 'Zobacz listę adresów zawierających odnośnik do tej strony.');
/**#@-*/

/**#@+
 * Language constant used by the {@link revert.php revert} action
 */
if(!defined('ERROR_NO_REVERT_PRIVS')) define('ERROR_NO_REVERT_PRIVS', "Przepraszamy, nie masz prawa cofać edycji tej strony");
/**#@-*/

/**#@+
 * Language constant used by the {@link revertlink.php revertlink} action
 */
if(!defined('REVERTLINK_TEXT')) define('REVERTLINK_TEXT', '[Cofnij edycję]');
if(!defined('REVERTLINK_OLDEST_TITLE')) define('REVERTLINK_OLDEST_TITLE', 'To jest najstarsza znana wersja tej strony');
/**#@-*/

/**#@+
 * Language constant used by the {@link revisionlink.php revisionlink} action
 */
if(!defined('REVISIONLINK_TITLE')) define('REVISIONLINK_TITLE', 'Zobacz listę ostatnich zmian tej strony');
if(!defined('REVISIONFEEDLINK_TITLE')) define('REVISIONFEEDLINK_TITLE', 'Zobacz listę ostatnich zmian tej strony');
/**#@-*/

/**#@+
 * Language constant used by the {@link rss.php rss} action
 */
if(!defined('ERROR_INVALID_RSS_SYNTAX')) define('ERROR_INVALID_RSS_SYNTAX', 'Błąd: Niewłaściwa składnia akcji RSS. <br /> Właściwe użycie: {{rss http://domain.com/feed.xml}} lub {{rss url="http://domain.com/feed.xml"}}');
/**#@-*/

/**#@+
 * Language constant used by the {@link searchform.php searchform} action
 */
if(!defined('SEARCHFORM_LABEL')) define('SEARCHFORM_LABEL', 'Szukaj: ');
/**#@-*/

/**#@+
 * Language constant used by the {@link system.php system} action
 */
// system
if(!defined('SYSTEM_HOST_CAPTION')) define('SYSTEM_HOST_CAPTION', '(%s)'); // %s - host name
/**#@-*/

/**#@+
 * Language constant shared by the {@link textsearch.php textsearch} and {@link textsearchexpanded.php textsearchexpanded} actions
 */
// textsearch & textsearchexpanded
if(!defined('SEARCH_FOR')) define('SEARCH_FOR', 'Szukaj');
if(!defined('SEARCH_ZERO_MATCH')) define('SEARCH_ZERO_MATCH', 'nic nie znaleziono');
if(!defined('SEARCH_ONE_MATCH')) define('SEARCH_ONE_MATCH', 'znaleziono <strong>jedną</strong> stronę');
if(!defined('SEARCH_N_MATCH')) define('SEARCH_N_MATCH', 'znaleziono stron: <strong>%d</strong>'); // %d number of hits
if(!defined('SEARCH_RESULTS')) define('SEARCH_RESULTS', 'Wyniki wyszukiwania hasła <strong>%2$s</strong> &mdash; %1$s.'); # %1$s: n matches for | %2$s: search term
if(!defined('SEARCH_NOT_SURE_CHOICE')) define('SEARCH_NOT_SURE_CHOICE', 'Nie wiesz, którą stronę wybrać?');
if(!defined('SEARCH_EXPANDED_LINK_DESC')) define('SEARCH_EXPANDED_LINK_DESC', 'wyszukiwania rozszerzonego'); // search link description
if(!defined('SEARCH_TRY_EXPANDED')) define('SEARCH_TRY_EXPANDED', 'Użyj %s, które pokazuje fragment treści stron.'); // %s expanded search link
if(!defined('SEARCH_MYSQL_IDENTICAL_CHARS')) define('SEARCH_MYSQL_IDENTICAL_CHARS', 'aàáâãą,eèéêëę,iìîï,oòóôõ,uùúû,cçć,nñń,yý,sś,lł,zżź');
if(!defined('SEARCH_WORD_1')) define('SEARCH_WORD_1', 'granat');
if(!defined('SEARCH_WORD_2')) define('SEARCH_WORD_2', 'jabłko');
if(!defined('SEARCH_WORD_3')) define('SEARCH_WORD_3', 'sok');
if(!defined('SEARCH_WORD_4')) define('SEARCH_WORD_4', 'kolor');
if(!defined('SEARCH_WORD_5')) define('SEARCH_WORD_5', 'słoik');
if(!defined('SEARCH_WORD_6')) define('SEARCH_WORD_6', 'miodu');
if(!defined('SEARCH_PHRASE')) define('SEARCH_PHRASE',sprintf('%s %s',SEARCH_WORD_5,SEARCH_WORD_6));
if(!defined('SEARCH_TARGET_1')) define('SEARCH_TARGET_1', 'Wyszukuje strony, które zawierają przynajmniej jedno z podanych słów.');
if(!defined('SEARCH_TARGET_2')) define('SEARCH_TARGET_2', 'Wyszukuje strony, które zawierają oba słowa.');
if(!defined('SEARCH_TARGET_3')) define('SEARCH_TARGET_3', sprintf("Wyszukuje strony, które zawierają słowo „%1\$s”, ale nie zawierają słowa „%2\$s”.",SEARCH_WORD_1,SEARCH_WORD_4));
if(!defined('SEARCH_TARGET_4')) define('SEARCH_TARGET_4', "Wyszukuje strony, kótre zawierają takie słowa jak: „granat”, „granatowy”, „granatnik” czy „granaty”."); // make sure target words all *start* with SEARCH_WORD_1
if(!defined('SEARCH_TARGET_5')) define('SEARCH_TARGET_5', sprintf("Wyszukuje strony, które zawierają frazę „%1\$s” (przykładowo pasuje tu „%1\$s Kubusia Puchatka” ale nie „%2\$s pełen %3\$s”).",SEARCH_PHRASE,SEARCH_WORD_5,SEARCH_WORD_6));
if(!defined('SEARCH_TIPS_TITLE')) define('SEARCH_TIPS_TITLE', 'Porady dotyczące wyszukiwania');
if(!defined('SEARCH_TIPS_UTF8_COMPAT_TITLE')) define('SEARCH_TIPS_UTF8_COMPAT_TITLE', 'Porady dotyczące wyszukiwania');
if(!defined('SEARCH_TIPS')) define('SEARCH_TIPS', '<br /><br /><hr /><br /><strong>'.SEARCH_TIPS_TITLE.':</strong><br /><br />'
	.'<div class="indent"><tt>'.SEARCH_WORD_1.' '.SEARCH_WORD_2.'</tt></div>'
	.SEARCH_TARGET_1.'<br /><br />'
	.'<div class="indent"><tt>'.'+'.SEARCH_WORD_1.' '.'+'.SEARCH_WORD_3.'</tt></div>'
	.SEARCH_TARGET_2.'<br /><br />'
	.'<div class="indent"><tt>'.'+'.SEARCH_WORD_1.' '.'-'.SEARCH_WORD_4.'</tt></div>'
	.SEARCH_TARGET_3.'<br /><br />'
	.'<div class="indent"><tt>'.SEARCH_WORD_1.'*'.'</tt></div>'
	.SEARCH_TARGET_4.'<br /><br />'
	.'<div class="indent"><tt>'.'"'.SEARCH_WORD_5.' '.SEARCH_WORD_6.'"'.'</tt></div>'
	.SEARCH_TARGET_5.'<br />');
if(!defined('SEARCH_TIPS_UTF8_COMPAT')) define('SEARCH_TIPS_UTF8_COMPAT', '<br /><br /><hr /><br /><strong>'.SEARCH_TIPS_UTF8_COMPAT_TITLE.':</strong><br /><br />'
	.'<div class="indent"><tt>'.SEARCH_WORD_1.' '.SEARCH_WORD_2.'</tt></div>'
	.SEARCH_TARGET_1.'<br /><br />');
/**#@-*/

/**#@+
 * Language constant used by the {@link usersettings.php usersettings} action
 */
// usersettings
// - error messages
if(!defined('ERROR_EMPTY_USERNAME')) define('ERROR_EMPTY_USERNAME', 'Podaj swoją nazwę użytkownika.');
if(!defined('ERROR_NONEXISTENT_USERNAME')) define('ERROR_NONEXISTENT_USERNAME', 'Złe hasło lub nazwa użytkownika.'); // @@@ too specific
if(!defined('ERROR_RESERVED_PAGENAME')) define('ERROR_RESERVED_PAGENAME', 'Przepraszamy, ta nazwa jest zarezerwowana. Użyj innej.');
if(!defined('ERROR_WIKINAME')) define('ERROR_WIKINAME', 'Nazwa użytkownika musi być w formacie CamelCase, np. JanKowalski.'); // %1$s identifier WikiName; %2$s sample WikiName/*%2$s*/
if(!defined('ERROR_EMPTY_EMAIL_ADDRESS')) define('ERROR_EMPTY_EMAIL_ADDRESS', 'Proszę podać adres email.');
if(!defined('ERROR_INVALID_EMAIL_ADDRESS')) define('ERROR_INVALID_EMAIL_ADDRESS', 'Nieprawidłowy format adresu email.');
if(!defined('ERROR_INVALID_PASSWORD')) define('ERROR_INVALID_PASSWORD', 'Złe hasło lub nazwa użytkownika.');	// @@@ too specific
if(!defined('ERROR_INVALID_HASH')) define('ERROR_INVALID_HASH', 'Złe przypomnienie hasła.');
if(!defined('ERROR_INVALID_OLD_PASSWORD')) define('ERROR_INVALID_OLD_PASSWORD', 'Niewłaściwe dotychczasowe hasło.');
if(!defined('ERROR_EMPTY_PASSWORD')) define('ERROR_EMPTY_PASSWORD', 'Proszę podać hasło.'); // TODO KJT 'not nice'
if(!defined('ERROR_EMPTY_PASSWORD_OR_HASH')) define('ERROR_EMPTY_PASSWORD_OR_HASH', 'Proszę podać hasło lub przypomnienie hasła.'); // TODO KJT 'not nice'
if(!defined('ERROR_EMPTY_CONFIRMATION_PASSWORD')) define('ERROR_EMPTY_CONFIRMATION_PASSWORD', 'Aby zarejestrować nowego użytkownika, należy potwierdzić hasło.');
if(!defined('ERROR_EMPTY_NEW_CONFIRMATION_PASSWORD')) define('ERROR_EMPTY_NEW_CONFIRMATION_PASSWORD', 'Aby zmienić hasło, należy wpisać je dwukrotnie.');
if(!defined('ERROR_EMPTY_NEW_PASSWORD')) define('ERROR_EMPTY_NEW_PASSWORD', 'Należy także podać nowe hasło.');
if(!defined('ERROR_PASSWORD_MATCH')) define('ERROR_PASSWORD_MATCH', 'Podane hasła różnią się.');
if(!defined('ERROR_PASSWORD_NO_BLANK')) define('ERROR_PASSWORD_NO_BLANK', 'Przepraszamy, w hasłach nie można używać spacji.');
if(!defined('ERROR_PASSWORD_TOO_SHORT')) define('ERROR_PASSWORD_TOO_SHORT', 'Przepraszamy, hasła muszą składać się co najmniej z %d znaków.'); // %d minimum password length
if(!defined('ERROR_INVALID_REVISION_DISPLAY_LIMIT')) define('ERROR_INVALID_REVISION_DISPLAY_LIMIT', 'Liczba wyświetlanych wersji stron nie może przekroczyć %d.'); // %d maximum revisions to view
if(!defined('ERROR_INVALID_RECENTCHANGES_DISPLAY_LIMIT')) define('ERROR_INVALID_RECENTCHANGES_DISPLAY_LIMIT', 'Liczba wyświetlanych zmian stron nie może przekroczyć %d.'); // %d maximum changed pages to view
if(!defined('ERROR_VALIDATION_FAILED')) define('ERROR_VALIDATION_FAILED', 'Weryfikacja rejestracji nie powiodła się. Spróbuj ponownie.'); // TODO KJT 'is this right?'
// - success messages
if(!defined('SUCCESS_USER_LOGGED_OUT')) define('SUCCESS_USER_LOGGED_OUT', 'Pomyślne wylogowanie.');
if(!defined('SUCCESS_USER_REGISTERED')) define('SUCCESS_USER_REGISTERED', 'Rejestracja powiodła się!');
if(!defined('SUCCESS_USER_SETTINGS_STORED')) define('SUCCESS_USER_SETTINGS_STORED', 'Zapisano ustawienia użytkownika!');
if(!defined('SUCCESS_USER_PASSWORD_CHANGED')) define('SUCCESS_USER_PASSWORD_CHANGED', 'Zmiana hasła powiodła się!');
// - captions
if(!defined('NEW_USER_REGISTER_CAPTION')) define('NEW_USER_REGISTER_CAPTION', 'Jeśli chcesz się zarejestrować, wypełnij poniższe pola:');
if(!defined('REGISTERED_USER_LOGIN_CAPTION')) define('REGISTERED_USER_LOGIN_CAPTION', 'Jeśli posiadasz już konto, zaloguj się:');
if(!defined('RETRIEVE_PASSWORD_CAPTION')) define('RETRIEVE_PASSWORD_CAPTION', 'Zaloguj się używając [[%s przypomnienia hasła]]:'); //%s PasswordForgotten link
if(!defined('USER_LOGGED_IN_AS_CAPTION')) define('USER_LOGGED_IN_AS_CAPTION', 'Jesteś zalogowany jako %s'); // %s user name
// - form legends
if(!defined('USER_ACCOUNT_LEGEND')) define('USER_ACCOUNT_LEGEND', 'Twoje konto');
if(!defined('USER_SETTINGS_LEGEND')) define('USER_SETTINGS_LEGEND', 'Ustawienia');
if(!defined('LOGIN_REGISTER_LEGEND')) define('LOGIN_REGISTER_LEGEND', 'Logowanie/Rejestracja');
if(!defined('LOGIN_LEGEND')) define('LOGIN_LEGEND', 'Zaloguj');
#if(!defined('REGISTER_LEGEND')) define('REGISTER_LEGEND', 'Zarejestruj'); // @@@ TODO to be used later for register-action
if(!defined('CHANGE_PASSWORD_LEGEND')) define('CHANGE_PASSWORD_LEGEND', 'Zmień hasło');
if(!defined('RETRIEVE_PASSWORD_LEGEND')) define('RETRIEVE_PASSWORD_LEGEND', 'Przypomnienie hasła');
// - form field labels (should end in ':' _unless_ it's a checkbox or radio button option)
if(!defined('USERSETTINGS_REDIRECT_AFTER_LOGIN_LABEL')) define('USERSETTINGS_REDIRECT_AFTER_LOGIN_LABEL', 'Po zalogowaniu przekieruj do strony %s ');	// %s page to redirect to
if(!defined('USER_EMAIL_LABEL')) define('USER_EMAIL_LABEL', 'Twój adres email:');
if(!defined('DOUBLECLICK_LABEL')) define('DOUBLECLICK_LABEL', 'Włącz edytowanie przez podwójne kliknięcie:');
if(!defined('SHOW_COMMENTS_LABEL')) define('SHOW_COMMENTS_LABEL', 'Domyślnie pokazuj komentarze:');
if(!defined('COMMENT_STYLE_LABEL')) define('COMMENT_STYLE_LABEL', 'Styl komentarzy');
if(!defined('COMMENT_ASC_LABEL')) define('COMMENT_ASC_LABEL', 'Płaskie (najstarsze jako pierwsze)');
if(!defined('COMMENT_DEC_LABEL')) define('COMMENT_DEC_LABEL', 'Płaskie (najnowsze jako pierwsze)');
if(!defined('COMMENT_THREADED_LABEL')) define('COMMENT_THREADED_LABEL', 'Ułożone w drzewo');
if(!defined('COMMENT_DELETED_LABEL')) define('COMMENT_DELETED_LABEL', '[Komentarz usunięty]');
if(!defined('COMMENT_BY_LABEL')) define('COMMENT_BY_LABEL', 'Napisany przez: '); // TODO KJT 'change to use %s'
if(!defined('RECENTCHANGES_DISPLAY_LIMIT_LABEL')) define('RECENTCHANGES_DISPLAY_LIMIT_LABEL', 'Liczba wyświetlanych zmian stron:');
if(!defined('PAGEREVISION_LIST_LIMIT_LABEL')) define('PAGEREVISION_LIST_LIMIT_LABEL', 'Liczba wyświetlanych wersji stron:');
if(!defined('NEW_PASSWORD_LABEL')) define('NEW_PASSWORD_LABEL', 'Nowe hasło:');
if(!defined('NEW_PASSWORD_CONFIRM_LABEL')) define('NEW_PASSWORD_CONFIRM_LABEL', 'Potwierdź nowe hasło:');
if(!defined('NO_REGISTRATION')) define('NO_REGISTRATION', 'Rejestracja nowych kont w tej witrynie została wyłączona.');
if(!defined('PASSWORD_LABEL')) define('PASSWORD_LABEL', 'Hasło (min. %s znaków):'); // %s minimum number of characters
if(!defined('CONFIRM_PASSWORD_LABEL')) define('CONFIRM_PASSWORD_LABEL', 'Potwierdź hasło:');
if(!defined('TEMP_PASSWORD_LABEL')) define('TEMP_PASSWORD_LABEL', 'Przypomnienie hasła:');
if(!defined('INVITATION_CODE_SHORT')) define('INVITATION_CODE_SHORT', 'kod zaproszenia');
if(!defined('INVITATION_CODE_LONG')) define('INVITATION_CODE_LONG', 'Aby się zarejestrować, należy podać specjalny kod zaproszenia, który można uzyskać kontaktując się z administratorem.');
if(!defined('INVITATION_CODE_LABEL')) define('INVITATION_CODE_LABEL', 'Twój %s:'); // %s - expanded short invitation code prompt
if(!defined('WIKINAME_SHORT')) define('WIKINAME_SHORT', 'NazwaUzytkownika');
if(!defined('WIKINAME_LONG')) define('WIKINAME_LONG', sprintf('Nazwa użytkownika musi składać się z co najmniej dwóch wyrazów napisanych z wielkiej litery, bez spacji ani polskich znaków, np. %s',WIKKA_SAMPLE_WIKINAME));
if(!defined('WIKINAME_LABEL')) define('WIKINAME_LABEL', '%s:'); // %s - expanded short wiki name prompt
// - form options
if(!defined('CURRENT_PASSWORD_OPTION')) define('CURRENT_PASSWORD_OPTION', 'Aktualne hasło');
if(!defined('PASSWORD_REMINDER_OPTION')) define('PASSWORD_REMINDER_OPTION', 'Przypomnienie hasła');
// - form buttons
if(!defined('UPDATE_SETTINGS_BUTTON')) define('UPDATE_SETTINGS_BUTTON', 'Zapisz zmiany');
if(!defined('LOGIN_BUTTON')) define('LOGIN_BUTTON', 'Zaloguj');
if(!defined('LOGOUT_BUTTON')) define('LOGOUT_BUTTON', 'Wyloguj');
if(!defined('CHANGE_PASSWORD_BUTTON')) define('CHANGE_PASSWORD_BUTTON', 'Zmień hasło');
if(!defined('REGISTER_BUTTON')) define('REGISTER_BUTTON', 'Zarejestruj');
if(!defined('PASSWORD_MIN_LENGTH')) define('PASSWORD_MIN_LENGTH', '5');
if(!defined('VALID_EMAIL_PATTERN')) define('VALID_EMAIL_PATTERN', '/^.+?\@.+?\..+$/'); //TODO: Use central regex library
if(!defined('REVISION_DISPLAY_LIMIT_MIN')) define('REVISION_DISPLAY_LIMIT_MIN', '0'); // 0 means no limit, 1 is the minimum number of revisions
if(!defined('REVISION_DISPLAY_LIMIT_MAX')) define('REVISION_DISPLAY_LIMIT_MAX', '20'); // keep this value within a reasonable limit to avoid an unnecessary long lists
if(!defined('RECENTCHANGES_DISPLAY_LIMIT_MIN')) define('RECENTCHANGES_DISPLAY_LIMIT_MIN', '0'); // 0 means no limit, 1 is the minimum number of changes
if(!defined('RECENTCHANGES_DISPLAY_LIMIT_MAX')) define('RECENTCHANGES_DISPLAY_LIMIT_MAX', '50'); // keep this value within a reasonable limit to avoid an unnecessary long list
if(!defined('ERROR_NO_BLANK')) define('ERROR_NO_BLANK', 'Hasło nie może zawierać spacji.');
if(!defined('ERROR_WRONG_PASSWORD')) define('ERROR_WRONG_PASSWORD', 'Podałeś złe hasło.');
if(!defined('ERROR_EMAIL_ADDRESS_REQUIRED')) define('ERROR_EMAIL_ADDRESS_REQUIRED', 'Podaj adres email.');
if(!defined('THEME_LABEL')) define('THEME_LABEL', 'Styl:');
/**#@-*/

/**#@+
 * Language constant used by the {@link wantedpages.php wantedpages} action
 */
// wantedpages
if(!defined('SORTING_LEGEND')) define('SORTING_LEGEND', 'Sortowanie');
if(!defined('SORTING_NUMBER_LABEL')) define('SORTING_NUMBER_LABEL', '%d. kryterium:');
if(!defined('SORTING_DESC_LABEL')) define('SORTING_DESC_LABEL', 'malejąco');
if(!defined('OK_BUTTON')) define('OK_BUTTON', '   OK   ');
if(!defined('NO_WANTED_PAGES')) define('NO_WANTED_PAGES', 'Nie ma stron oczekujących na utworzenie. Znakomicie!');
if(!defined('WANTEDPAGES_PAGES_LINKING_TO')) define('WANTEDPAGES_PAGES_LINKING_TO', 'Strony kierujące do %s'); // @todo Test this
/**#@-*/

/**#@+
 * Language constant used by the {@link wikkaconfig.php wikkaconfig} action
 */
//wikkaconfig
if(!defined('WIKKACONFIG_CAPTION')) define('WIKKACONFIG_CAPTION', "Ustawienia programu Wikka [%s]"); // %s link to Wikka Config options documentation
if(!defined('WIKKACONFIG_DOCS_URL')) define('WIKKACONFIG_DOCS_URL', "http://docs.wikkawiki.org/ConfigurationOptions");
if(!defined('WIKKACONFIG_DOCS_TITLE')) define('WIKKACONFIG_DOCS_TITLE', "Przeczytaj dokumentację ustawień systemu Wikka"); //KJT
if(!defined('WIKKACONFIG_TH_OPTION')) define('WIKKACONFIG_TH_OPTION', "Opcja");
if(!defined('WIKKACONFIG_TH_VALUE')) define('WIKKACONFIG_TH_VALUE', "Wartość");

/* ------------------ 3RD PARTY ------------------ */

/**#@+
 * Language constant used by the {@link fullscreen.php fullscreen} 3rd party MindMap display utility
 */
// fullscreen
if(!defined('CLOSE_WINDOW')) define('CLOSE_WINDOW', 'Zamknij okno');
if(!defined('MM_GET_JAVA_PLUGIN_LINK_DESC')) define('MM_GET_JAVA_PLUGIN_LINK_DESC', 'pobierz najnowszą wersję Javy'); // used in MM_GET_JAVA_PLUGIN
if(!defined('MM_GET_JAVA_PLUGIN')) define('MM_GET_JAVA_PLUGIN', '%s.'); // %s - plugin download link
/**#@-*/


/* ------------------ FORMATTERS ------------------ */

/**#@+
 * Language constant used by the {@link wakka.php wakka} formatter
 */
// wakka
if(!defined('GRABCODE_BUTTON')) define('GRABCODE_BUTTON', 'Pobierz');
if(!defined('GRABCODE_BUTTON_TITLE')) define('GRABCODE_BUTTON_TITLE', 'Pobierz %s'); // %s download filename
/**#@-*/


/* ------------------ HANDLERS (PAGE) ------------------ */

/**#@+
 * Language constant used by the {@link acls.php acls} (page) handler
 */
// acls
// TODO: 'translate' DB value '(Public)' when displaying it!
if(!defined('ACLS_UPDATED')) define('ACLS_UPDATED', 'Prawa dostępu zostały zaktualizowane.');
if(!defined('NO_PAGE_OWNER')) define('NO_PAGE_OWNER', '(Nikt)');
if(!defined('NOT_PAGE_OWNER')) define('NOT_PAGE_OWNER', 'Nie jesteś właścicielem tej strony.');
if(!defined('PAGE_OWNERSHIP_CHANGED')) define('PAGE_OWNERSHIP_CHANGED', 'Nowy właściciel strony: %s'); // %s name of new owner
if(!defined('ACLS_LEGEND')) define('ACLS_LEGEND', 'Prawa dostępu do strony %s'); // %s name of current page
if(!defined('ACLS_READ_LABEL')) define('ACLS_READ_LABEL', 'Prawo odczytu:');
if(!defined('ACLS_WRITE_LABEL')) define('ACLS_WRITE_LABEL', 'Prawo zapisu:');
if(!defined('ACLS_COMMENT_READ_LABEL')) define('ACLS_COMMENT_READ_LABEL', 'Prawo odczytu komentarza:');
if(!defined('ACLS_COMMENT_POST_LABEL')) define('ACLS_COMMENT_POST_LABEL', 'Prawo dodania komentarza:');
if(!defined('SET_OWNER_LABEL')) define('SET_OWNER_LABEL', 'Zmień właściciela:');
if(!defined('SET_OWNER_CURRENT_OPTION')) define('SET_OWNER_CURRENT_OPTION', '(aktualny właściciel)');
if(!defined('SET_OWNER_PUBLIC_OPTION')) define('SET_OWNER_PUBLIC_OPTION', '(Publiczna)'); // actual DB value will remain '(Public)' even if this option text is translated!
if(!defined('SET_NO_OWNER_OPTION')) define('SET_NO_OWNER_OPTION', '(Nikt — uwolnij stronę)');
if(!defined('ACLS_STORE_BUTTON')) define('ACLS_STORE_BUTTON', 'Zapisz uprawnienia');
if(!defined('CANCEL_BUTTON')) define('CANCEL_BUTTON', 'Anuluj');
// - syntax
if(!defined('ACLS_SYNTAX_HEADING')) define('ACLS_SYNTAX_HEADING', 'Składnia:');
if(!defined('ACLS_EVERYONE')) define('ACLS_EVERYONE', 'Wszyscy');
if(!defined('ACLS_REGISTERED_USERS')) define('ACLS_REGISTERED_USERS', 'Użytkownicy zarejestrowani');
if(!defined('ACLS_NONE_BUT_ADMINS')) define('ACLS_NONE_BUT_ADMINS', 'Nikt (poza administratorami)');
if(!defined('ACLS_ANON_ONLY')) define('ACLS_ANON_ONLY', 'Tylko użytkownicy anonimowi');
if(!defined('ACLS_LIST_USERNAMES')) define('ACLS_LIST_USERNAMES', 'użytkownik %s; możesz podać dowolną liczbę użytkowników, po jednym w każdej linii'); // %s sample user name
if(!defined('ACLS_NEGATION')) define('ACLS_NEGATION', 'Każdy z powyższych wpisów może zostać zanegowany przy użyciu znaku %s:'); // %s 'negation' mark
if(!defined('ACLS_DENY_USER_ACCESS')) define('ACLS_DENY_USER_ACCESS', 'zabrania dostępu użytkownikowi %s'); // %s sample user name
if(!defined('ACLS_AFTER')) define('ACLS_AFTER', 'po');
if(!defined('ACLS_TESTING_ORDER1')) define('ACLS_TESTING_ORDER1', 'Prawa dostępu są stosowane w kolejności wpisania.');
if(!defined('ACLS_TESTING_ORDER2')) define('ACLS_TESTING_ORDER2', 'Dlatego jeżeli chcesz użyć znaku %1$s, należy go wpisać w osobnej linijce %2$s wpisach zabraniających dostępu wybranym użytkownikom.'); // %1$s 'all' mark; %2$s emphasised 'after'
if(!defined('ACLS_DEFAULT_ACLS')) define('ACLS_DEFAULT_ACLS', 'Usunięcie wszystkich wartości z którejś z powyższych czterech list, spowoduje ustawienie dla niej wartości domyślnych, zdefiniowanych w pliku %s.');
if(!defined('ACL_HEADING')) define('ACL_HEADING', '====Prawa dostępu do strony „%s”===='); // %s - name of current page
if(!defined('READ_ACL_LABEL')) define('READ_ACL_LABEL', 'Prawo odczytu:');
if(!defined('WRITE_ACL_LABEL')) define('WRITE_ACL_LABEL', 'Prawo zapisu:');
if(!defined('STORE_ACL_LABEL')) define('STORE_ACL_LABEL', 'Zapisz uprawnienia');
if(!defined('SET_OWNER_CURRENT_LABEL')) define('SET_OWNER_CURRENT_LABEL', '(aktualny właściciel)');
if(!defined('SET_OWNER_PUBLIC_LABEL')) define('SET_OWNER_PUBLIC_LABEL','(Publiczna)');
if(!defined('SET_NO_OWNER_LABEL')) define('SET_NO_OWNER_LABEL', '(Nikt — uwolnij stronę)');
if(!defined('ACL_SYNTAX_HELP')) define('ACL_SYNTAX_HELP', '===Składnia:=== ---##*## = Wszyscy ---##+## = Użytkownicy zarejestrowani ---##""JanKowalski""## = użytkownik ""JanKowalski""; możesz podać dowolną liczbę użytkowników, po jednym w każdej linii --- --- Każdy z powyższych wpisów może zostać zanegowany przy użyciu znaku ##!##: ---##!*## = Nikt (poza administratorami) ---##!+## = Tylko użytkownicy anonimowi ---##""!JanKowalski""## = zabrania dostępu użytkownikowi ""JanKowalski"" --- --- //Prawa dostępu są stosowane w kolejności wpisania:// --- Dlatego jeżeli chcesz użyć znaku ##*##, należy go wpisać w osobnej linijce //po// wpisach zabraniających dostępu wybranym użytkownikom.');
/**#@-*/

/**#@+
 * Language constant used by the {@link backlinks.php backlinks} (page) handler
 */
// backlinks
if(!defined('PAGE_TITLE')) define('PAGE_TITLE','Strony zawierające odnośniki do %s');
if(!defined('MESSAGE_NO_BACKLINKS')) define('MESSAGE_NO_BACKLINKS','Żadne strony nie zawierają odnośników do tej strony.');
if(!defined('MESSAGE_MISSING_PAGE')) define('MESSAGE_MISSING_PAGE','Niestety, strona %s nie istnieje.');
if(!defined('MESSAGE_PAGE_INACCESSIBLE')) define('MESSAGE_PAGE_INACCESSIBLE', 'Nie masz praw odczytu tej strony');
/**#@-*/

/**#@+
 * Language constant used by the {@link claim.php claim} (page) handler
 */
// claim
if(!defined('USER_IS_NOW_OWNER')) define('USER_IS_NOW_OWNER', 'Jesteś teraz właścicielem tej strony.');
/**#@-*/

/**#@+
 * Language constant used by the {@link clone.php clone} (page) handler
 */
// clone
if(!defined('ERROR_ACL_WRITE')) define('ERROR_ACL_WRITE', 'Nie masz prawa zapisu strony „%s”');
if(!defined('CLONE_VALID_TARGET')) define('CLONE_VALID_TARGET', 'Podaj prawidłową nazwę nowej strony oraz opcjonalnie opis zmian.');
if(!defined('CLONE_LEGEND')) define('CLONE_LEGEND', 'Skopiuj %s'); // %s source page name
if(!defined('CLONED_FROM')) define('CLONED_FROM', 'Skopiowano z %s'); // %s source page name
if(!defined('SUCCESS_CLONE_CREATED')) define('SUCCESS_CLONE_CREATED', 'Strona „%s” została pomyślnie utworzona!'); // %s new page name
if(!defined('CLONE_X_TO_LABEL')) define('CLONE_X_TO_LABEL', 'Nazwa kopii:');
if(!defined('CLONE_EDIT_NOTE_LABEL')) define('CLONE_EDIT_NOTE_LABEL', 'Opis zmian:');
if(!defined('CLONE_EDIT_OPTION_LABEL')) define('CLONE_EDIT_OPTION_LABEL', ' Po skopiowaniu przejdź do edycji &nbsp;');
if(!defined('CLONE_ACL_OPTION_LABEL')) define('CLONE_ACL_OPTION_LABEL', ' Skopiuj także prawa dostępu');
if(!defined('CLONE_BUTTON')) define('CLONE_BUTTON', 'Duplikuj');
if(!defined('CLONE_HEADER')) define('CLONE_HEADER', 'Utwórz kopię tej strony');
if(!defined('CLONE_SUCCESSFUL')) define('CLONE_SUCCESSFUL', 'Strona „%s” została pomyślnie utworzona!');
if(!defined('CLONE_X_TO')) define('CLONE_X_TO', 'Skopiuj „%s” jako:');
if(!defined('EDIT_NOTE')) define('EDIT_NOTE', 'Opis zmian:');
if(!defined('ERROR_ACL_READ')) define('ERROR_ACL_READ', 'Nie masz uprawnień do odczytu źródła tej strony.');
if(!defined('ERROR_INVALID_PAGENAME')) define('ERROR_INVALID_PAGENAME', 'Nazwa strony jest nieprawidłowa. Prawidłowa nazwa nie może zawierać znaków: | ? = &lt; &gt; / \' " % &amp;.');
if(!defined('ERROR_PAGE_ALREADY_EXIST')) define('ERROR_PAGE_ALREADY_EXIST', 'Przepraszamy, taka strona już istnieje');
if(!defined('ERROR_PAGE_NOT_EXIST')) define('ERROR_PAGE_NOT_EXIST', ' Przepraszamy, strona „%s” nie istnieje.');
if(!defined('LABEL_CLONE')) define('LABEL_CLONE', 'Skopiuj');
if(!defined('LABEL_EDIT_OPTION')) define('LABEL_EDIT_OPTION', ' Po skopiowaniu przejdź do edycji ');
if(!defined('PLEASE_FILL_VALID_TARGET')) define('PLEASE_FILL_VALID_TARGET', 'Podaj prawidłową nazwę nowej strony oraz opcjonalnie opis zmian.');
/**#@-*/

/**#@+
 * Language constant used by the {@link delete.php delete} (page) handler
 */
// delete
if(!defined('ERROR_NO_PAGE_DEL_ACCESS')) define('ERROR_NO_PAGE_DEL_ACCESS', 'Nie masz prawa usunięcia tej strony.');
if(!defined('PAGE_DELETION_HEADER')) define('PAGE_DELETION_HEADER', 'Usuń stronę „%s”'); // %s - name of the page
if(!defined('SUCCESS_PAGE_DELETED')) define('SUCCESS_PAGE_DELETED', 'Strona została usunięta!');
if(!defined('PAGE_DELETION_CAPTION')) define('PAGE_DELETION_CAPTION', 'Czy usunąć stronę oraz wszystkie jej komentarze?');
if(!defined('PAGE_DELETION_DELETE_BUTTON')) define('PAGE_DELETION_DELETE_BUTTON', 'Usuń stronę');
if(!defined('PAGE_DELETION_CANCEL_BUTTON')) define('PAGE_DELETION_CANCEL_BUTTON', 'Anuluj');
/**#@-*/

/**#@+
 * Language constant used by the {@link diff.php diff} (page) handler
 */
// diff
if(!defined('ERROR_DIFF_LIBRARY_MISSING')) define('ERROR_DIFF_LIBRARY_MISSING', 'Nie znaleziono pliku <tt>"libs/diff.lib.php"</tt>. Powiadom administratora witryny.');
if(!defined('ERROR_BAD_PARAMETERS')) define('ERROR_BAD_PARAMETERS', 'Podano nieprawidłowe parametry. Prawdopodobnie jedna z wersji wybranych do porównania została już usunięta.');
if(!defined('DIFF_COMPARISON_HEADER')) define('DIFF_COMPARISON_HEADER', 'Porównanie %1$s strony %2$s'); // %1$s - link to revision list; %2$s - link to page
if(!defined('DIFF_REVISION_LINK_TITLE')) define('DIFF_REVISION_LINK_TITLE', 'Wyświetl listę wersji strony %s'); // %s page name
if(!defined('DIFF_PAGE_LINK_TITLE')) define('DIFF_PAGE_LINK_TITLE', 'Przejdź do najnowszej wersji tej strony');
if(!defined('DIFF_SAMPLE_ADDITION')) define('DIFF_SAMPLE_ADDITION', '&nbsp;teksty dodane&nbsp;');
if(!defined('DIFF_SAMPLE_DELETION')) define('DIFF_SAMPLE_DELETION', '&nbsp;teksty usunięte&nbsp;');
if(!defined('DIFF_SIMPLE_BUTTON')) define('DIFF_SIMPLE_BUTTON', 'Porównanie uproszczone');
if(!defined('DIFF_FULL_BUTTON')) define('DIFF_FULL_BUTTON', 'Porównanie szczegółowe');
if(!defined('HIGHLIGHTING_LEGEND')) define('HIGHLIGHTING_LEGEND', 'Legenda:');
if(!defined('ERROR_NO_PAGE_ACCESS')) define('ERROR_NO_PAGE_ACCESS', 'Nie masz uprawnień by wyświetlić tę stronę.');
if(!defined('CONTENT_ADDITIONS_HEADER')) define('CONTENT_ADDITIONS_HEADER', 'Wstawiono:');
if(!defined('CONTENT_DELETIONS_HEADER')) define('CONTENT_DELETIONS_HEADER', 'Usunięto:');
if(!defined('CONTENT_NO_DIFFERENCES')) define('CONTENT_NO_DIFFERENCES', 'Brak różnic');
/**#@-*/

/**#@+
 * Language constant used by the {@link edit.php edit} (page) handler
 */
// edit
if(!defined('ERROR_OVERWRITE_ALERT1')) define('ERROR_OVERWRITE_ALERT1', 'KONFLIKT EDYCJI: Ktoś inny zmodyfikował tę stronę, podczas gdy ją edytowałeś.');
if(!defined('ERROR_OVERWRITE_ALERT2')) define('ERROR_OVERWRITE_ALERT2', 'Kopiuj swoje zmiany i rozpocznij edycję od początku.');
if(!defined('ERROR_MISSING_EDIT_NOTE')) define('ERROR_MISSING_EDIT_NOTE', 'BRAK OPISU ZMIAN: proszę podać opis zmian!');
if(!defined('ERROR_TAG_TOO_LONG')) define('ERROR_TAG_TOO_LONG', 'Nazwa strony jest zbyt długa! Maksymalna liczba znaków: %d.'); // %d maximum page name length
if(!defined('ERROR_NO_WRITE_ACCESS')) define('ERROR_NO_WRITE_ACCESS', 'Nie masz prawa zapisu tej strony. Aby móc wprowadzić zmiany, powinieneś się [[UserSettings zalogować lub zarejestrować]].'); //TODO Distinct links for login and register actions
if(!defined('EDIT_STORE_PAGE_LEGEND')) define('EDIT_STORE_PAGE_LEGEND', 'Zapisz stronę');
if(!defined('EDIT_PREVIEW_HEADER')) define('EDIT_PREVIEW_HEADER', 'Podgląd');
if(!defined('EDIT_NOTE_LABEL')) define('EDIT_NOTE_LABEL', 'dodaj opis wprowadzonych zmian'); // label after field, so no colon!
if(!defined('MESSAGE_AUTO_RESIZE')) define('MESSAGE_AUTO_RESIZE', 'Kliknij %s aby automatycznie obciąć nazwę do wymaganego rozmiaru.'); // %s rename button text
if(!defined('EDIT_PREVIEW_BUTTON')) define('EDIT_PREVIEW_BUTTON', 'Podgląd');
if(!defined('EDIT_STORE_BUTTON')) define('EDIT_STORE_BUTTON', 'Zapisz');
if(!defined('EDIT_REEDIT_BUTTON')) define('EDIT_REEDIT_BUTTON', 'Wróć do edycji');
if(!defined('EDIT_CANCEL_BUTTON')) define('EDIT_CANCEL_BUTTON', 'Anuluj');
if(!defined('EDIT_RENAME_BUTTON')) define('EDIT_RENAME_BUTTON', 'Zmień nazwę');
if(!defined('ACCESSKEY_PREVIEW')) define('ACCESSKEY_PREVIEW', 'p'); // ideally, should match EDIT_PREVIEW_BUTTON
if(!defined('ACCESSKEY_STORE')) define('ACCESSKEY_STORE', 'z'); // ideally, should match EDIT_STORE_BUTTON
if(!defined('ACCESSKEY_REEDIT')) define('ACCESSKEY_REEDIT', 'w'); // ideally, should match EDIT_REEDIT_BUTTON
if(!defined('SHOWCODE_LINK')) define('SHOWCODE_LINK', 'Zobacz źródło tej strony'); // TODO KJT not sure...
if(!defined('SHOWCODE_LINK_TITLE')) define('SHOWCODE_LINK_TITLE', 'Zobacz źródło tej strony'); // @@@ TODO 'View page formatting code' TODO KJT not sure here either.,,
if(!defined('EDIT_COMMENT_TIMESTAMP_CAPTION')) define('EDIT_COMMENT_TIMESTAMP_CAPTION', '(%s)'); // %s timestamp
if(!defined('ERROR_INVALID_PAGEID')) define('ERROR_INVALID_PAGEID', 'Dla żądanej strony nie istnieje wskazany numer wersji');
if(!defined('MAX_TAG_LENGTH')) define('MAX_TAG_LENGTH', 75);
if(!defined('MAX_EDIT_NOTE_LENGTH')) define('MAX_EDIT_NOTE_LENGTH', 50);
if(!defined('INPUT_SUBMIT_PREVIEW')) define('INPUT_SUBMIT_PREVIEW', 'Preview');
if(!defined('INPUT_SUBMIT_RENAME')) define('INPUT_SUBMIT_RENAME', 'Rename');
/**#@-*/

/**#@+
 * Language constant used by the {@link grabcode.php grabcode} (page) handler
 */
// grabcode
if(!defined('ERROR_NO_CODE')) define('ERROR_NO_CODE', 'Przepraszamy, nie ma kodu do pobrania.');
if(!defined('DEFAULT_FILENAME')) define('DEFAULT_FILENAME', 'codeblock.txt'); # default name for code blocks
if(!defined('FILE_EXTENSION')) define('FILE_EXTENSION', '.txt'); # extension appended to code block name
/**#@-*/

/**#@+
 * Language constant used by the {@link history.php history} (page) handler
 */
// history
if(!defined('EDITED_ON')) define('EDITED_ON', 'Zmodyfikowana %1$s przez użytkownika: %2$s'); // %1$s - time; %2$s - user name
if(!defined('HISTORY_PAGE_VIEW')) define('HISTORY_PAGE_VIEW', 'Historia zmian strony %s'); // %s pagename
if(!defined('OLDEST_VERSION_EDITED_ON_BY')) define('OLDEST_VERSION_EDITED_ON_BY', 'Najstarsza znana wersja tej strony. Została utworzona %1$s przez użytkownika: %2$s'); // %1$s - time; %2$s - user name
if(!defined('MOST_RECENT_EDIT')) define('MOST_RECENT_EDIT', 'Aktualna wersja. Zmodyfikowana %1$s przez użytkownika: %2$s'); // %1$s time; %2$s user name
if(!defined('HISTORY_MORE_LINK_DESC')) define('HISTORY_MORE_LINK_DESC', 'Zobacz dalszą część historii zmian'); // used for alternative history link in HISTORY_MORE
if(!defined('HISTORY_MORE')) define('HISTORY_MORE', 'Nie można wyświetlić całej historii zmian na jednej stronie. %s.'); // %s alternative history link # @@@ TODO avoid using 'here' ^
if(!defined('DIFF_NO_DIFFERENCES')) define('DIFF_NO_DIFFERENCES', 'Brak różnic');
/**#@-*/

/**#@+
 * Language constant shared by the {@link processcomment.php processcomment} and {@link show.php show} (page) handlers
 */
// processcomment & show
if(!defined('COMMENT_NO_DISPLAY')) define('COMMENT_NO_DISPLAY', 0);
if(!defined('COMMENT_ORDER_DATE_ASC')) define('COMMENT_ORDER_DATE_ASC', 1);
if(!defined('COMMENT_ORDER_DATE_DESC')) define('COMMENT_ORDER_DATE_DESC', 2);
if(!defined('COMMENT_ORDER_THREADED')) define('COMMENT_ORDER_THREADED', 3);
if(!defined('COMMENT_MAX_TRAVERSAL_DEPTH')) define('COMMENT_MAX_TRAVERSAL_DEPTH', 10);

// - comment buttons
if(!defined('COMMENT_DELETE_BUTTON')) define('COMMENT_DELETE_BUTTON', 'Usuń');
if(!defined('COMMENT_REPLY_BUTTON')) define('COMMENT_REPLY_BUTTON', 'Odpowiedz');
if(!defined('COMMENT_ADD_BUTTON')) define('COMMENT_ADD_BUTTON', 'Dodaj komentarz');
if(!defined('COMMENT_NEW_BUTTON')) define('COMMENT_NEW_BUTTON', 'Nowy komentarz');
/**#@-*/

/**#@+
 * Language constant used by the {@link processcomment.php processcomment} (page) handler
 */
// processcomment
if(!defined('ERROR_NO_COMMENT_DEL_ACCESS')) define('ERROR_NO_COMMENT_DEL_ACCESS', 'Przepraszamy, nie możesz usunąć tego komentarza!');
if(!defined('ERROR_NO_COMMENT_WRITE_ACCESS')) define('ERROR_NO_COMMENT_WRITE_ACCESS', 'Przepraszamy, nie możesz komentować tej strony.');
if(!defined('ERROR_EMPTY_COMMENT')) define('ERROR_EMPTY_COMMENT', 'Treść komentarza jest pusta &mdash; nie zapisano!');
if(!defined('ERROR_COMMENT_NO_KEY')) define('ERROR_COMMENT_NO_KEY', "Nie można zapisać komentarza. Skontaktuj się z administratorem wiki.");
if(!defined('ERROR_COMMENT_INVALID_KEY')) define('ERROR_COMMENT_INVALID_KEY', "Nie można zapisać komentarza. Skontaktuj się z administratorem wiki.");
if(!defined('ADD_COMMENT_LABEL')) define('ADD_COMMENT_LABEL', 'W odpowiedzi na %s:');
if(!defined('NEW_COMMENT_LABEL')) define('NEW_COMMENT_LABEL', 'Napisz komentarz:');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges_simple.xml.mm.php recentchanges_simple.xml.mm} (page) handler
 */
// recentchanges_simple.xml.mm
if(!defined('FIRST_NODE_LABEL')) define('FIRST_NODE_LABEL', 'Ostatnie zmiany');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges.xml.php recentchanges.xml} (page) handler
 */
// recentchanges.xml
if(!defined('RECENTCHANGES_DESC')) define('RECENTCHANGES_DESC', 'Ostatnie zmiany strony %s'); // %s - page name
if(!defined('RECENTCHANGES_FEED_TITLE')) define('RECENTCHANGES_FEED_TITLE',"%s — strony ostatnio zmieniane");	// %s - name of the wiki
if(!defined('RECENTCHANGES_FEED_DESCRIPTION')) define('RECENTCHANGES_FEED_DESCRIPTION',"Nowe i niedawno zmieniane strony witryny „%s”");	// %s - name of the wiki
if(!defined('RECENTCHANGES_FEED_IMAGE_TITLE')) define('RECENTCHANGES_FEED_IMAGE_TITLE',"logo Wikka");
if(!defined('RECENTCHANGES_FEED_IMAGE_DESCRIPTION')) define('RECENTCHANGES_FEED_IMAGE_DESCRIPTION',"Kanał RSS dostarczony przez: Wikka");
if(!defined('RECENTCHANGES_FEED_ITEM_DESCRIPTION')) define('RECENTCHANGES_FEED_ITEM_DESCRIPTION',"Napisane przez: „%s”");	// %s - user name
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges.xml.mm.php recentchanges.mm.xml} (page) handler
 */
// recentchanges.mm.xml
if(!defined('RECENTCHANGES_REV_TIME_CAPTION')) define('RECENTCHANGES_REV_TIME_CAPTION', 'Czas edycji: %s'); // %s timestamp // @todo Check this
if(!defined('RECENTCHANGES_VIEW_HISTORY_TITLE')) define('RECENTCHANGES_VIEW_HISTORY_TITLE', 'Wyświetl historię');
if(!defined('RECENTCHANGES_AUTHOR')) define('RECENTCHANGES_AUTHOR', 'Autor: %s'); // %s author
/**#@-*/

/**#@+
 * Language constant shared by the {@link referrers_sites.php referrers_sites}, {@link referrers.php referrers} and {@link review_blacklist.php review_blacklist} (page) handlers
 */
// referrers_sites + referrers + review_blacklist
if(!defined('REFERRERS_PURGE_24_HOURS')) define('REFERRERS_PURGE_24_HOURS', 'w ciągu ostatnich 24 godzin');
if(!defined('REFERRERS_PURGE_N_DAYS')) define('REFERRERS_PURGE_N_DAYS', 'w ciągu ostatnich %d dni'); // %d number of days
if(!defined('REFERRERS_NO_SPAM')) define('REFERRERS_NO_SPAM', 'Do spamerów: Ta strona nie jest indeksowana przez wyszukiwarki. Szkoda waszego czasu.');
if(!defined('REFERRERS_DOMAINS_TO_WIKI_LINK_DESC')) define('REFERRERS_DOMAINS_TO_WIKI_LINK_DESC', 'Domeny, z których przychodzili odwiedzający tę witrynę');
if(!defined('REFERRERS_DOMAINS_TO_PAGE_LINK_DESC')) define('REFERRERS_DOMAINS_TO_PAGE_LINK_DESC', 'Domeny, z których przychodzili odwiedzający stronę %s'); // %s page name
if(!defined('REFERRERS_URLS_TO_WIKI_LINK_DESC')) define('REFERRERS_URLS_TO_WIKI_LINK_DESC', 'Adresy, z których przychodzili odwiedzający tę witrynę');
if(!defined('REFERRERS_URLS_TO_PAGE_LINK_DESC')) define('REFERRERS_URLS_TO_PAGE_LINK_DESC', 'Adresy, z których przychodzili odwiedzający stronę %s'); // %s page name
if(!defined('REFERRER_BLACKLIST_LINK_DESC')) define('REFERRER_BLACKLIST_LINK_DESC', 'Zobacz listę stron wykluczonych z tego spisu');
if(!defined('BLACKLIST_LINK_DESC')) define('BLACKLIST_LINK_DESC', 'Czarna lista');
if(!defined('NONE_CAPTION')) define('NONE_CAPTION', 'Brak');
if(!defined('PLEASE_LOGIN_CAPTION')) define('PLEASE_LOGIN_CAPTION', 'Aby zobaczyć listę adresów spod których przychodzą odwiedzający tę stronę, musisz się zalogować.');
/**#@-*/

/**#@+
 * Language constant used by the {@link referrers_sites.php referrers_sites} (page) handler
 */
// referrers_sites
if(!defined('REFERRERS_URLS_LINK_DESC')) define('REFERRERS_URLS_LINK_DESC', 'Wyświetl wg adresów');
if(!defined('REFERRERS_DOMAINS_TO_WIKI')) define('REFERRERS_DOMAINS_TO_WIKI', 'Domeny, z których przychodzili odwiedzający tę witrynę. [%s]'); // %s link to referrers handler
if(!defined('REFERRERS_DOMAINS_TO_PAGE')) define('REFERRERS_DOMAINS_TO_PAGE', 'Domeny, z których przychodzili odwiedzający tę stronę (%1$s) %2$s. [%3$s]'); // %1$s page link; %2$s purge time; %3$s link to referrers handler
/**#@-*/

/**#@+
 * Language constant used by the {@link referrers.php referrers} (page) handler
 */
// referrers
if(!defined('REFERRERS_DOMAINS_LINK_DESC')) define('REFERRERS_DOMAINS_LINK_DESC', 'Wyświetl wg domen');
if(!defined('REFERRERS_URLS_TO_WIKI')) define('REFERRERS_URLS_TO_WIKI', 'Adresy, z których przychodzili odwiedzający tę witrynę. [%s]'); // %s link to referrers_sites handler
if(!defined('REFERRERS_URLS_TO_PAGE')) define('REFERRERS_URLS_TO_PAGE', 'Adresy, z których przychodzili odwiedzający tę stronę (%1$s) %2$s. [%3$s]'); // %1$s page link; %2$s purge time; %3$s link to referrers_sites handler
/**#@-*/

/**#@+
 * Language constant used by the {@link revert.php revert} (page) handler
 */
// revert
if(!defined('REVERT_DEFAULT_COMMENT')) define('REVERT_DEFAULT_COMMENT', 'Reverted to previous revision');
/**#@-*/

/**#@+
 * Language constant used by the {@link review_blacklist.php review_blacklist} (page) handler
 */
// review_blacklist
if(!defined('BLACKLIST_HEADING')) define('BLACKLIST_HEADING', 'Czarna lista &mdash; strony nie uwzględniane w spisach miejsc pochodzenia odwiedzających');
if(!defined('BLACKLIST_REMOVE_LINK_DESC')) define('BLACKLIST_REMOVE_LINK_DESC', 'Usuń');
if(!defined('STATUS_BLACKLIST_EMPTY')) define('STATUS_BLACKLIST_EMPTY', 'Czarna lista jest pusta.');
if(!defined('BLACKLIST_VIEW_GLOBAL_SITES')) define('BLACKLIST_VIEW_GLOBAL_SITES', 'Wyświetl adresy, z których przychodzili odwiedzający tę witrynę');
if(!defined('BLACKLIST_VIEW_GLOBAL')) define('BLACKLIST_VIEW_GLOBAL', 'wyświetl źródła odwiedzających');
/**#@-*/

/**#@+
 * Language constant used by the {@link revisions.php revisions} (page) handler
 */
// revisions
if(!defined('REVISIONS_CAPTION')) define('REVISIONS_CAPTION', 'Wersje strony %s'); // %s pagename
if(!defined('REVISIONS_NO_REVISIONS_YET')) define('REVISIONS_NO_REVISIONS_YET', 'Nie ma jeszcze historii');
if(!defined('REVISIONS_SIMPLE_DIFF')) define('REVISIONS_SIMPLE_DIFF', 'proste porównanie');
if(!defined('REVISIONS_MORE_CAPTION')) define('REVISIONS_MORE_CAPTION', 'Istnieje więcej wersji tej strony. Kliknij poniżej, aby je zobaczyć.'); // %s text of REVISIONS_MORE_BUTTON
if(!defined('REVISIONS_RETURN_TO_NODE_BUTTON')) define('REVISIONS_RETURN_TO_NODE_BUTTON', 'Powrót do treści strony');
if(!defined('REVISIONS_SHOW_DIFFERENCES_BUTTON')) define('REVISIONS_SHOW_DIFFERENCES_BUTTON', 'Pokaż różnice');
if(!defined('REVISIONS_MORE_BUTTON')) define('REVISIONS_MORE_BUTTON', 'Zobacz wcześniejsze wersje');
/**#@-*/

/**#@+
 * Language constant used by the {@link revisions.xml.php revisions.xml} (page) handler
 */
// revisions.xml
if(!defined('REVISIONS_EDITED_BY')) define('REVISIONS_EDITED_BY', 'Napisana przez %s'); // %s user name
if(!defined('HISTORY_REVISIONS_OF')) define('HISTORY_REVISIONS_OF', 'Historia wersji strony %s'); // %s page name
if(!defined('I18N_ENCODING_UTF8')) define('I18N_ENCODING_UTF8', 'UTF-8');
if(!defined('RSS_REVISIONS_VERSION')) define('RSS_REVISIONS_VERSION','2.0');
/**#@-*/

/**#@+
 * Language constant used by the {@link show.php show} (page) handler
 */
// show
if(!defined('SHOW_RE_EDIT_BUTTON')) define('SHOW_RE_EDIT_BUTTON', 'Edytuj tę wersję');
if(!defined('SHOW_FORMATTED_BUTTON')) define('SHOW_FORMATTED_BUTTON', 'Pokaż wersję sformatowaną');
if(!defined('SHOW_SOURCE_BUTTON')) define('SHOW_SOURCE_BUTTON', 'Pokaż źródło');
if(!defined('SHOW_ASK_CREATE_PAGE_CAPTION')) define('SHOW_ASK_CREATE_PAGE_CAPTION', 'Nie ma jeszcze strony o tej nazwie. Czy chcesz ją %s?'); // %s page create link
if(!defined('SHOW_OLD_REVISION_CAPTION')) define('SHOW_OLD_REVISION_CAPTION', 'To jest stara wersja strony %1$s, utworzona przez użytkownika: %2$s, datowana: %3$s.'); // %1$s - page link; %2$s - username; %3$s - timestamp;
if(!defined('COMMENTS_CAPTION')) define('COMMENTS_CAPTION', 'Komentarze');
if(!defined('DISPLAY_COMMENTS_LABEL')) define('DISPLAY_COMMENTS_LABEL', 'Pokaż komentarze.');
if(!defined('DISPLAY_COMMENT_LINK_DESC')) define('DISPLAY_COMMENT_LINK_DESC', 'Pokaż komentarz.');
if(!defined('DISPLAY_COMMENTS_EARLIEST_LINK_DESC')) define('DISPLAY_COMMENTS_EARLIEST_LINK_DESC', 'Starsze jako pierwsze'); // TODO KJT 'not used!'
if(!defined('DISPLAY_COMMENTS_LATEST_LINK_DESC')) define('DISPLAY_COMMENTS_LATEST_LINK_DESC', 'Niedawne jako pierwsze'); // TODO KJT 'not used!'
if(!defined('DISPLAY_COMMENTS_THREADED_LINK_DESC')) define('DISPLAY_COMMENTS_THREADED_LINK_DESC', 'W drzewku'); // TODO KJT 'not used!'
if(!defined('HIDE_COMMENTS_LINK_DESC')) define('HIDE_COMMENTS_LINK_DESC', 'Ukryj');
if(!defined('STATUS_NO_COMMENTS')) define('STATUS_NO_COMMENTS', 'Nie ma jeszcze komentarzy.');
if(!defined('STATUS_ONE_COMMENT')) define('STATUS_ONE_COMMENT', 'Tę stronę skomentowano jeden raz.');
if(!defined('STATUS_SOME_COMMENTS')) define('STATUS_SOME_COMMENTS', 'Tę stronę skomentowano %d razy.'); // %d number of comments
if(!defined('COMMENT_TIME_CAPTION')) define('COMMENT_TIME_CAPTION', '%s'); // %s comment time
if(!defined('SHOW_OLD_REVISION_SOURCE')) define('SHOW_OLD_REVISION_SOURCE', 0); # if set to 1 shows by default the source of an old revision instead of the rendered version
/**#@-*/

/**#@+
 * Language constant used by the {@link showcode.php showcode} (page) handler
 */
// showcode
if(!defined('SOURCE_HEADING')) define('SOURCE_HEADING', 'Kod źródłowy strony %s'); // %s - page link
if(!defined('SHOW_RAW_LINK_DESC')) define('SHOW_RAW_LINK_DESC', 'Wyświetl sam kod źródłowy');
/**#@-*/

/* ------------------ LIBS ------------------*/

/**#@+
 * Language constant used by the {@link Wakka.class.php Wakka class} (the Wikka core containing most methods)
 */
// Wakka.class
if(!defined('QUERY_FAILED')) define('QUERY_FAILED', 'Błąd zapytania.');
if(!defined('REDIR_DOCTITLE')) define('REDIR_DOCTITLE', 'Przekierowano do %s'); // %s - target page
if(!defined('REDIR_LINK_DESC')) define('REDIR_LINK_DESC', 'użyj tego linku'); // used in REDIR_MANUAL_CAPTION
if(!defined('REDIR_MANUAL_CAPTION')) define('REDIR_MANUAL_CAPTION', 'Jeżeli przekierowanie nie nastąpi automatycznie, %s'); // %s target page link
if(!defined('CREATE_THIS_PAGE_LINK_TITLE')) define('CREATE_THIS_PAGE_LINK_TITLE', 'Utwórz tę stronę');
if(!defined('ACTION_UNKNOWN_SPECCHARS')) define('ACTION_UNKNOWN_SPECCHARS', 'Nie znaleziono akcji; jej nazwa nie może zawierać znaków specjalnych.');
if(!defined('ACTION_UNKNOWN')) define('ACTION_UNKNOWN', 'Nie znaleziono akcji „%s”.'); // %s action name
if(!defined('HANDLER_UNKNOWN_SPECCHARS')) define('HANDLER_UNKNOWN_SPECCHARS', 'Nie znaleziono obiektu obsługującego; jego nazwa nie może zawierać znaków specjalnych.');
if(!defined('HANDLER_UNKNOWN')) define('HANDLER_UNKNOWN', 'Nie znaleziono obiektu obsługującego „%s”.'); // %s handler name
if(!defined('FORMATTER_UNKNOWN_SPECCHARS')) define('FORMATTER_UNKNOWN_SPECCHARS', 'Nie znaleziono obiektu formatującego; jego nazwa nie może zawierać znaków specjalnych.');
if(!defined('FORMATTER_UNKNOWN')) define('FORMATTER_UNKNOWN', 'Nie znaleziono obiektu formatującego „%s”.'); // %s formatter name
if(!defined('DEFAULT_THEMES_TITLE')) define('DEFAULT_THEMES_TITLE', 'Domyślne style (%s)'); //%s: number of available themes @todo check this
if(!defined('CUSTOM_THEMES_TITLE')) define('CUSTOM_THEMES_TITLE', 'Style użytkownika (%s)'); //%s: number of available themes @todo check this
/**#@-*/

/**#@+
 * Language constant used by the {@link admin.lib.php admin class}
 * (the admin core containing most admin-related methods)
 */
// admin.lib
// Reversion routine strings
if(!defined('REVERT_DEFAULT_COMMENT')) define('REVERT_DEFAULT_COMMENT', 'Wycofywanie ostatniej edycji użytkownika „%s” [%d] do wcześniejszej wersji [%d]'); // @ todo Check this, check %d
if(!defined('REVERT_MESSAGE_SUCCESS')) define('REVERT_MESSAGE_SUCCESS', 'Cofnij do poprzedniej wersji');
if(!defined('REVERT_MESSAGE_FAILURE')) define('REVERT_MESSAGE_FAILURE', 'Wycofywanie do poprzeniej wersji NIE POWIODŁO SIĘ!');

// User deletion strings
if(!defined('USERDELETE_MESSAGE_SUCCESS')) define('USERDELETE_MESSAGE_SUCCESS', 'Usunięto użytkownika');
if(!defined('USERDELETE_MESSAGE_FAILURE')) define('USERDELETE_MESSAGE_FAILURE', 'Usunięcie użytkownika nie powiodło się');
/**#@-*/

/* ------------------ SETUP ------------------ */
/**#@+
 * Language constant used by the {@link index.php setup} program (and several included files)
 */
// @@@ later....
/**#@-*/

?>
