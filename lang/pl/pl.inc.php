<?php
/**
 * Wikka language file.
 *
 * This file holds all interface language strings for Wikka.
 *
 * @package 		Language
 *
 * @version		$Id:pl.inc.php 003 2009-11-01 00:16:56Z KrzysztofTrybowski $
 * @license 		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author 		{@link http://wikkawiki.org/KrzysztofTrybowski Krzysztof Trybowski}
 *
 * @copyright 	Copyright 2007, {@link http://wikkawiki.org/CreditsPage Wikka Development Team}
 *
 * @todo		FOR THIS TO WORK, YOU NEED TO CHANGE YOUR CHARSET TO UTF-8!
 *  
 * @todo		review places marked with KJT
 *  
 * @todo		review translation for referrers
 *  
 * @todo		search for and embed some mindmap to test translation of that part of UI
 * 
 * @todo		yes/no: should names of default pages be translated?
 * 
 */

/* ------------------ COMMON ------------------ */

/**#@+
 * Language constant shared among several Wikka components
 */
// NOTE: all common names (used in multiple files) should start with WIKKA_ !
define('WIKKA_ADMIN_ONLY_TITLE', 'Przepraszamy, tylko administratorzy mają dostęp do tej informacji'); //title for elements that are only displayed to admins
define('WIKKA_ERROR_SETUP_FILE_MISSING', 'Plik instalatora nie został znaleziony. Proszę ponownie zainstalować WikkaWiki!');
define('WIKKA_ERROR_MYSQL_ERROR', 'Błąd MySQL: %d %s');	// %d error number; %s error text
define('WIKKA_ERROR_CAPTION', 'Błąd');
define('WIKKA_ERROR_ACL_READ', 'Nie masz prawa odczytu tej strony.');
define('WIKKA_ERROR_ACL_READ_SOURCE', 'Nie masz prawa odczytu kodu źródłowego tej strony.');
define('WIKKA_ERROR_ACL_READ_INFO', 'Nie masz prawa dostępu do tej informacji.');
define('WIKKA_ERROR_LABEL', 'Błąd');
define('WIKKA_ERROR_PAGE_NOT_EXIST', 'Przepraszamy, strona %s nie istnieje.'); // %s (source) page name
define('WIKKA_ERROR_EMPTY_USERNAME', 'Proszę podać nazwę użytkownika!');
define('WIKKA_DIFF_ADDITIONS_HEADER', 'Dodano:'); // KJT
define('WIKKA_DIFF_DELETIONS_HEADER', 'Usunięto:');
define('WIKKA_DIFF_NO_DIFFERENCES', 'Brak różnic');
define('ERROR_USERNAME_UNAVAILABLE', "Przepraszamy, podana nazwa użytkownika jest już zajęta lub z innych względów nie może zostać użyta.");
define('ERROR_USER_INVITED', "Złe hasło lub nazwa użytkownika."); // KJT Added for my customization // @@@ this acts as if user didn't exist at all, so it says "Bad password or username"
define('ERROR_USER_SIGNEDUP', "Przepraszamy, to konto nie zostało jeszcze zaktywowane przez email."); // KJT Added for my customization
define('ERROR_USER_PENDING', "Przepraszamy, to konto nie zostało jeszcze zaktywowane przez administratora."); // KJT Added for my customization
define('ERROR_USER_SUSPENDED', "Przepraszamy, to konto zostało zablokowane. Proszę skontaktować się z administratorem.");
define('ERROR_USER_BANNED', "To konto zastało zablokowane ze względu na złamanie zasad serwisu.");  // KJT Added for my customization
define('ERROR_USER_DELETED', "To konto zastało usunięte.");  // KJT Added for my customization
define('ERROR_USER_OTHER', "Wystąpił błąd podczas zakładania konta użytkownika. Skontaktuj się z administratorem."); //KJT // This means that "status" field in a DB is NULL. According to my customization, this is WRONG!
define('WIKKA_ACTIVATION_REQUIRED_ADMIN', "Konto zostało utworzone, ale jest nieaktywne. Zostanie zaktywowane przez administratora."); //KJT // Admin has to activate the account first (status = "pending") 
define('WIKKA_ACTIVATION_REQUIRED_EMAIL', "Konto zostało utworzone, ale jest nieaktywne. Aby zaktywować konto należy kliknąć odnośnik w otrzymanym emailu."); //KJT // User has to activate the account by email (status = "signed-up") -- NOT IMPLEMENTED
define('WIKKA_ERROR_INVALID_PAGE_NAME', 'Nazwa %s jest nieprawidłowa. Prawidłowa nazwa musi zaczynać się wielką literą, zawierać tyko litery i cyfry oraz być w formacie CamelCase.'); // %s page name TODO KJT not nice
define('WIKKA_ERROR_PAGE_ALREADY_EXIST', 'Przepraszamy, taka strona już istnieje');
define('WIKKA_LOGIN_LINK_DESC', 'logowania');
define('WIKKA_MAINPAGE_LINK_DESC', 'strona główna'); // TODO KJT this is not used!
define('WIKKA_NO_OWNER', 'Nikt');
define('WIKKA_NOT_AVAILABLE', 'b/d');
define('WIKKA_NOT_INSTALLED', 'nie zainstalowano');
define('WIKKA_ANONYMOUS_USER', 'użytkownik anonimowy'); // 'name' of non-registered user
define('WIKKA_UNREGISTERED_USER', 'użytkownik niezarejestrowany'); // alternative for 'anonymous' @@@ make one string only?
define('WIKKA_ANONYMOUS_AUTHOR_CAPTION', '('.WIKKA_UNREGISTERED_USER.')'); // @@@ or WIKKA_ANONYMOUS_USER
define('WIKKA_SAMPLE_WIKINAME', 'JanKowalski'); // must be a CamelCase name
define('WIKKA_HISTORY', 'historia');
define('WIKKA_REVISIONS', 'wersji');
define('WIKKA_REVISION_NUMBER', 'Wersja nr %s');
define('WIKKA_REV_WHEN_BY_WHO', '%1$s napisana przez %2$s'); // %1$s timestamp; %2$s user name
define('WIKKA_NO_PAGES_FOUND', 'Nie znaleziono stron.');
define('WIKKA_PAGE_OWNER', 'Właściciel: %s'); // %s page owner name or link
define('WIKKA_COMMENT_AUTHOR_DIVIDER', ', napisany przez użytkownika: '); //TODo check if we can construct a single phrase here
define('WIKKA_PAGE_EDIT_LINK_DESC', 'edytuj');
define('WIKKA_PAGE_CREATE_LINK_DESC', 'utworzyć');
define('WIKKA_PAGE_EDIT_LINK_TITLE', 'Edytuj stronę %s'); // %s page name @@@ 'Edit %s'
define('WIKKA_BACKLINKS_LINK_TITLE', 'Wyświetl listę stron zawierających odnośniki do strony %s'); // %s page name
define('WIKKA_JRE_LINK_DESC', 'Java Runtime Environment');
define('WIKKA_NOTE', 'UWAGA,');
define('WIKKA_JAVA_PLUGIN_NEEDED', 'do uruchomienia tego programu wymagana jest Java 1.4.1 (lub nowsza). ');
/**#@-*/


/*  ------------------ CORE ------------------  */

/**#@+
 * Language constant for the core {@link wikka.php wikka} program
 */
// wikka
define('ERROR_WAKKA_LIBRARY_MISSING', 'Nie znaleziono pliku „%s”. Aby uruchomić WikkaWiki, upewnij się, że plik istnieje i jest umieszczony we właściwym katalogu!');	// %s configured path to core class
define('ERROR_NO_DB_ACCESS', 'Błąd: Nie można połączyć się z bazą danych.');
define('ERROR_RETRIEVAL_MYSQL_VERSION', 'Nie można określić wersji MySQL.');
define('ERROR_WRONG_MYSQL_VERSION', 'WikkaWiki wymaga MySQL w wersji %s lub wyższej!');	// %s version number
define('STATUS_WIKI_UPGRADE_NOTICE', 'Witryna w trakcie aktualizacji. Spróbuj poźniej.');
define('STATUS_WIKI_UNAVAILABLE', 'Witryna jest chwilowo niedostępna.');
define('PAGE_GENERATION_TIME', 'Czas generowania strony w sekundach: %.4f.'); // %.4f page generation time
define('ERROR_HEADER_MISSING', 'Nie znaleziono szablonu nagłówka. Upewnij się, że plik <code>header.php</code> istnieje w katalogu <code>templates</code>.'); //TODO Make sure this message matches any filename/folder change
define('ERROR_FOOTER_MISSING', 'Nie znaleziono szablonu stopki. Upewnij się, że plik <code>footer.php</code> istnieje w katalogu <code>templates</code>.'); //TODO Make sure this message matches any filename/folder change

#define('ERROR_WRONG_PHP_VERSION', '$_REQUEST[] not found. Wakka requires PHP 4.1.0 or higher!'); //TODO remove referral to PHP internals; refer only to required version
#define('ERROR_SETUP_HEADER_MISSING', 'The file "setup/header.php" was not found. Please install Wikka again!');
#define('ERROR_SETUP_FOOTER_MISSING', 'The file "setup/footer.php" was not found. Please install Wikka again!');
/**#@-*/

/*  ------------------ TEMPLATE ------------------  */

/**#@+
 * Language constant used by the {@link header.php header} template
 */
// header
define('GENERIC_DOCTITLE', '%1$s: %2$s');	// %1$s wiki name; %2$s page title
define('RSS_REVISIONS_TITLE', '%1$s: wersje strony %2$s');	// %1$s wiki name; %2$s current page name
define('RSS_RECENTCHANGES_TITLE', '%s: strony zmieniane ostatnio');	// %s wiki name
define('YOU_ARE', 'Zalogowany jako: %s'); // %s name / ip of the user.
/**#@-*/

/**#@+
 * Language constant used by the {@link footer.php footer} template
 */
// footer
define('FOOTER_PAGE_EDIT_LINK_DESC', 'Edytuj stronę');
define('PAGE_HISTORY_LINK_TITLE', 'Zobacz ostatnie zmiany na stronie'); // @@@ TODO 'View recent edits to this page'
define('PAGE_HISTORY_LINK_DESC', 'Historia strony');
define('PAGE_REVISION_LINK_TITLE', 'Zobacz listę wersji tej strony'); // @@@ TODO 'View recent revisions list for this page'
define('PAGE_REVISION_XML_LINK_TITLE', 'Zobacz listę wersji tej strony'); // @@@ TODO 'View recent revisions list for this page'
define('PAGE_ACLS_EDIT_LINK_DESC', 'Edytuj uprawnienia');
define('PAGE_ACLS_EDIT_ADMIN_LINK_DESC', '('.PAGE_ACLS_EDIT_LINK_DESC.')');
define('PUBLIC_PAGE', 'Strona publiczna');
define('USER_IS_OWNER', 'Jesteś właścicielem tej strony.');
define('TAKE_OWNERSHIP', 'Przejmij na własność');
define('REFERRERS_LINK_TITLE', 'Zobacz listę adresów zawierających odnośnik do tej strony'); // @@@ TODO 'View a list of URLs referring to this page'
define('REFERRERS_LINK_DESC', 'Źródła odwiedzających'); // @@@ TODO KJT Write it better! Odnośniki do tej strony, Źródła gości, Kierunki odwiedzin, Pochodzenie gości, 
define('QUERY_LOG', 'Dziennik zapytań:'); // @@@ TODO KJT Write it better!
define('SEARCH_LABEL', 'Szukaj:');
/**#@-*/


/*  ------------------ ACTIONS  ------------------  */

/**#@+
 * Language constant used by the {@link adminpages.php adminpages} action
 */
// adminpages
define('ADMINPAGES_PAGE_TITLE','Administracja stronami');
define('ADMINPAGES_FORM_LEGEND','Filtr widoku:');
define('ADMINPAGES_FORM_SEARCH_STRING_LABEL','Szukaj strony:');
define('ADMINPAGES_FORM_SEARCH_STRING_TITLE','Kryteria wyszukiwania');
define('ADMINPAGES_FORM_SEARCH_SUBMIT','Szukaj');
define('ADMINPAGES_FORM_DATE_RANGE_STRING_LABEL','Czas ostatniej edycji: pomiędzy');
define('ADMINPAGES_FORM_DATE_RANGE_CONNECTOR_LABEL','i');
define('ADMINPAGES_FORM_PAGER_LABEL_BEFORE','Wyświetl');
define('ADMINPAGES_FORM_PAGER_TITLE','Określ ilość wpisów na stronę');
define('ADMINPAGES_FORM_PAGER_LABEL_AFTER','wpisów na stronę');
define('ADMINPAGES_FORM_PAGER_SUBMIT','Wyświetl');
define('ADMINPAGES_FORM_PAGER_LINK','Wyświetl wpisy od %d do %d');
define('ADMINPAGES_FORM_RESULT_INFO','Wpisy');
define('ADMINPAGES_FORM_RESULT_SORTED_BY','Kryterium sortowania:');
define('ADMINPAGES_TABLE_HEADING_PAGENAME','Nazwa<br/>strony');
define('ADMINPAGES_TABLE_HEADING_PAGENAME_TITLE','Sortuj wg nazwy strony');
define('ADMINPAGES_TABLE_HEADING_OWNER','Właściciel');
define('ADMINPAGES_TABLE_HEADING_OWNER_TITLE','Sortuj wg właściciela');
define('ADMINPAGES_TABLE_HEADING_LASTAUTHOR','Ostatni<br/>autor');
define('ADMINPAGES_TABLE_HEADING_LASTAUTHOR_TITLE','Sortuj wg ostatniego autora');
define('ADMINPAGES_TABLE_HEADING_LASTEDIT','Ostatnia<br/>edycja');
define('ADMINPAGES_TABLE_HEADING_LASTEDIT_TITLE','Sortuj wg czasu ostatniej edycji');
define('ADMINPAGES_TABLE_SUMMARY','Lista stron w tym serwisie');
define('ADMINPAGES_TABLE_HEADING_HITS_TITLE','Ilość wyświetleń');
define('ADMINPAGES_TABLE_HEADING_REVISIONS_TITLE','Wersje');
define('ADMINPAGES_TABLE_HEADING_COMMENTS_TITLE','Komentarze');
define('ADMINPAGES_TABLE_HEADING_BACKLINKS_TITLE','Strony kierujące do tej');
define('ADMINPAGES_TABLE_HEADING_REFERRERS_TITLE','Witryny kierujące do tej');
define('ADMINPAGES_TABLE_HEADING_HITS_ALT','Wyświetleń');
define('ADMINPAGES_TABLE_HEADING_REVISIONS_ALT','Wersje');
define('ADMINPAGES_TABLE_HEADING_COMMENTS_ALT','Komentarze');
define('ADMINPAGES_TABLE_HEADING_BACKLINKS_ALT','Strony kierujące do tej');
define('ADMINPAGES_TABLE_HEADING_REFERRERS_ALT','Witryny kierujące do tej');
define('ADMINPAGES_TABLE_HEADING_ACTIONS','Działania');
define('ADMINPAGES_ACTION_EDIT_LINK_TITLE','Edytuj stronę: %s');
define('ADMINPAGES_ACTION_DELETE_LINK_TITLE','Usuń stronę: %s');
define('ADMINPAGES_ACTION_CLONE_LINK_TITLE','Skopiuj stronę: %s');
define('ADMINPAGES_ACTION_RENAME_LINK_TITLE','Zmień nazwę strony: %s');
define('ADMINPAGES_ACTION_ACL_LINK_TITLE','Zmień prawa dostępu do strony: %s');
define('ADMINPAGES_ACTION_INFO_LINK_TITLE','Wyświetl informacje i statystyki o stronie: %s'); #not implemented yet
define('ADMINPAGES_ACTION_REVERT_LINK_TITLE','Cofnij stronę %s do poprzedniej wersji');
define('ADMINPAGES_ACTION_EDIT_LINK','edytuj');
define('ADMINPAGES_ACTION_DELETE_LINK','usuń');
define('ADMINPAGES_ACTION_CLONE_LINK','skopiuj');
define('ADMINPAGES_ACTION_RENAME_LINK','zmień nazwę');
define('ADMINPAGES_ACTION_ACL_LINK','prawa dostępu');
define('ADMINPAGES_ACTION_INFO_LINK','informacje');
define('ADMINPAGES_ACTION_REVERT_LINK', 'cofnij');
define('ADMINPAGES_TAKE_OWNERSHIP_LINK','Przejmij na własność stronę');
define('ADMINPAGES_NO_OWNER','(Nikt)');
define('ADMINPAGES_TABLE_CELL_HITS_TITLE','Wyświetlenia strony: %s (%d)');
define('ADMINPAGES_TABLE_CELL_REVISIONS_TITLE','Wyświetl wersje strony: %s (%d)');
define('ADMINPAGES_TABLE_CELL_COMMENTS_TITLE','Wyświetl komentarze strony: %s (%d)');
define('ADMINPAGES_TABLE_CELL_BACKLINKS_TITLE','Wyświetl strony kierujące do: %s (%d)');
define('ADMINPAGES_TABLE_CELL_REFERRERS_TITLE','Wyświetl zewnętrzne witryny kierujące do: %s (%d)');
define('ADMINPAGES_SELECT_RECORD_TITLE','Wybierz stronę %s');
define('ADMINPAGES_NO_EDIT_NOTE','(Brak opisu zmian)');
define('ADMINPAGES_CHECK_ALL_TITLE','Wybierz wszystkie strony');
define('ADMINPAGES_CHECK_ALL','Wybierz wszystkie');
define('ADMINPAGES_UNCHECK_ALL_TITLE','Anuluj wybór');
define('ADMINPAGES_UNCHECK_ALL','Anuluj wybór');
define('ADMINPAGES_FORM_MASSACTION_LEGEND','Wykonanie działania na wielu stronach');
define('ADMINPAGES_FORM_MASSACTION_LABEL','Działanie:');
define('ADMINPAGES_FORM_MASSACTION_SELECT_TITLE','Wybierz działanie do wykonania na zaznaczonych stronach');
define('ADMINPAGES_FORM_MASSACTION_OPT_DELETE','Usuń zaznaczone');
define('ADMINPAGES_FORM_MASSACTION_OPT_CLONE','Skopiuj zaznaczone');
define('ADMINPAGES_FORM_MASSACTION_OPT_RENAME','Zmień nazwę zaznaczonych');
define('ADMINPAGES_FORM_MASSACTION_OPT_ACL','Zmień prawa dostępu do zaznaczonych stron');
define('ADMINPAGES_FORM_MASSACTION_OPT_REVERT','Cofnij zaznaczone do wcześniejszej wersji');
define('ADMINPAGES_FORM_MASSACTION_REVERT_ERROR','Nie można cofnąć');
define('ADMINPAGES_FORM_MASSACTION_SUBMIT','Rozpocznij');
define('ADMINPAGES_ERROR_NO_MATCHES','Przepraszamy, znaleziono stron pasujących do wzorca: "%s"');
define('ADMINPAGES_LABEL_EDIT_NOTE','Wprowadź opis zmian lub pozostaw puste, aby zastosować wartość domyślną');
define('WHEN_BY_WHO', '%1$s napisana przez: %2$s');
define('ADMINPAGES_CANCEL_LABEL', 'Anuluj');

/**#@-*/

/**#@+
 * Language constant used by the {@link adminusers.php adminusers} action
 */
// adminusers
define('ADMINUSERS_PAGE_TITLE','Administracja użytkownikami');
define('ADMINUSERS_FORM_LEGEND','Filtr widoku:');
define('ADMINUSERS_FORM_SEARCH_STRING_LABEL','Szukaj użytkownika:');
define('ADMINUSERS_FORM_SEARCH_STRING_TITLE','Kryteria wyszukiwania');
define('ADMINUSERS_FORM_SEARCH_SUBMIT','Szukaj');
define('ADMINUSERS_FORM_PAGER_LABEL_BEFORE','Wyświetl');
define('ADMINUSERS_FORM_PAGER_TITLE','Określ ilość wpisów na stronę');
define('ADMINUSERS_FORM_PAGER_LABEL_AFTER','wpisów na stronę');
define('ADMINUSERS_FORM_PAGER_SUBMIT','Wyświetl');
define('ADMINUSERS_FORM_PAGER_LINK','Wyświetl wpisy od %d do %d');
define('ADMINUSERS_FORM_RESULT_INFO','Wpisy');
define('ADMINUSERS_FORM_RESULT_SORTED_BY','Kryterium sortowania:');
define('ADMINUSERS_TABLE_HEADING_USERNAME','Nazwa<br/>użytkownika');
define('ADMINUSERS_TABLE_HEADING_USERNAME_TITLE','Sortuj wg nazwy użytkownika');
define('ADMINUSERS_TABLE_HEADING_EMAIL','Email');
define('ADMINUSERS_TABLE_HEADING_EMAIL_TITLE','Sortuj wg adresu email');
define('ADMINUSERS_TABLE_HEADING_STATUS','Status'); //KJT
define('ADMINUSERS_TABLE_HEADING_STATUS_TITLE','Sortuj wg statusu'); //KJT
define('ADMINUSERS_TABLE_HEADING_SIGNUPTIME','Utworzenie<br/>konta');
define('ADMINUSERS_TABLE_HEADING_SIGNUPTIME_TITLE','Sortuj wg czasu utworzenia konta');
define('ADMINUSERS_TABLE_HEADING_LASTLOGINTIME','Ostatnie<br/>logowanie'); //KJT
define('ADMINUSERS_TABLE_HEADING_LASTLOGINTIME_TITLE','Sortuj wg czasu ostatniego logowania'); //KJT
define('ADMINUSERS_TABLE_HEADING_SIGNUPIP','IP utworzenia<br/>konta');
define('ADMINUSERS_TABLE_HEADING_SIGNUPIP_TITLE','Sortuj wg numeru IP z którego utworzono konto');
define('ADMINUSERS_TABLE_SUMMARY','Lista zarejestrowanych użytkowników');
define('ADMINUSERS_TABLE_HEADING_ACTIONS','Działania');
define('ADMINUSERS_TABLE_HEADING_OWNED_TITLE','Posiadane strony');
define('ADMINUSERS_TABLE_HEADING_EDITS_TITLE','Edycje');
define('ADMINUSERS_TABLE_HEADING_COMMENTS_TITLE','Komentarze');
define('ADMINUSERS_ACTION_DELETE_LINK_TITLE','Usuń użytkownika: %s');
define('ADMINUSERS_ACTION_FEEDBACK_LINK_TITLE','Wyślij wiadomość do użytkownika: %s'); #to be added in 1.1.7, see #608
define('ADMINUSERS_ACTION_SUSPEND_LINK_TITLE','Zawieś użytkownika: %s'); //KJT
define('ADMINUSERS_ACTION_BAN_LINK_TITLE','Zablokuj użytkownika: %s (za złamanie zasad serwisu)'); //KJT
define('ADMINUSERS_ACTION_ACTIVATE_LINK_TITLE','Uaktywnij użytkownika: %s'); //KJT
define('ADMINUSERS_ACTION_DELETE_LINK','usuń');
define('ADMINUSERS_ACTION_FEEDBACK_LINK','wiadomość'); #to be added in 1.1.7, see #608
define('ADMINUSERS_ACTION_SUSPEND_LINK','zawieś'); //KJT
define('ADMINUSERS_ACTION_BAN_LINK','zablokuj'); //KJT
define('ADMINUSERS_ACTION_ACTIVATE_LINK','zaktywuj'); //KJT
define('ADMINUSERS_TABLE_CELL_OWNED_TITLE','Wyświetl strony posiadane przez użytkownika: %s (%d)');
define('ADMINUSERS_TABLE_CELL_EDITS_TITLE','Wyświetl edycje użytkownika: %s (%d)');
define('ADMINUSERS_TABLE_CELL_COMMENTS_TITLE','Wyświetl komentarze użytkownika: %s (%d)');
define('ADMINUSERS_SELECT_RECORD_TITLE','Wybierz użytkownika: %s');
define('ADMINUSERS_SELECT_ALL_TITLE','Wybierz wszystkich użytkowników');
define('ADMINUSERS_SELECT_ALL','Wybierz wszystkich');
define('ADMINUSERS_DESELECT_ALL_TITLE','Anuluj wybór');
define('ADMINUSERS_DESELECT_ALL','Anuluj wybór');
define('ADMINUSERS_FORM_MASSACTION_LEGEND','Wykonanie działania na wielu użytkownikach');
define('ADMINUSERS_FORM_MASSACTION_LABEL','Działanie: ');
define('ADMINUSERS_FORM_MASSACTION_SELECT_TITLE','Wybierz działanie do wykonania na zaznaczonych użytkownikach');
define('ADMINUSERS_FORM_MASSACTION_OPT_DELETE','Usuń zaznaczonych');
define('ADMINUSERS_FORM_MASSACTION_OPT_SUSPEND','Zawieś zaznaczonych'); // KJT
define('ADMINUSERS_FORM_MASSACTION_OPT_BAN','Zablokuj zaznaczonych'); // KJT
define('ADMINUSERS_FORM_MASSACTION_OPT_ACTIVATE','Zaktywuj zaznaczonych'); // KJT
define('ADMINUSERS_FORM_MASSACTION_OPT_FEEDBACK','Wyślij wiadomość do zaznaczonych'); #to be added in 1.1.7, see #608
define('ADMINUSERS_FORM_MASSACTION_DELETE_ERROR', 'Nie można usunąć administratorów');
define('ADMINUSERS_FORM_MASSACTION_SUSPEND_ERROR', 'Nie można zawiesić administratorów'); // KJT
define('ADMINUSERS_FORM_MASSACTION_BAN_ERROR', 'Nie można zablokować administratorów'); // KJT
define('ADMINUSERS_FORM_MASSACTION_SUBMIT','Rozpocznij');
define('ADMINUSERS_ERROR_NO_MATCHES','Przepraszamy, nie znaleziono użytkowników pasujących do wzorca: "%s"');
define('ADMINUSERS_DELETE_USERS_HEADING', 'Usunąć wskazanych użytkowników?');
define('ADMINUSERS_DELETE_USERS_BUTTON', 'Usuń użytkowników');
define('ADMINUSERS_SUSPEND_USERS_HEADING', 'Zawiesić wskazanych użytkowników?'); //KJT
define('ADMINUSERS_SUSPEND_USERS_BUTTON', 'Zawieś użytkowników'); //KJT
define('ADMINUSERS_BAN_USERS_HEADING', 'Zablokować wskazanych użytkowników?'); //KJT
define('ADMINUSERS_BAN_USERS_BUTTON', 'Zablokuj użytkowników'); //KJT
define('ADMINUSERS_ACTIVATE_USERS_HEADING', 'Zaktywować wskazanych użytkowników?'); //KJT
define('ADMINUSERS_ACTIVATE_USERS_BUTTON', 'Zaktywuj użytkowników'); //KJT	
define('ADMINUSERS_CANCEL_BUTTON', 'Anuluj');
define('ADMINUSERS_ADD_USER_LEGEND', 'Dodaj użytkownika'); //KJT
define('ADMINUSERS_USER_EMAIL_LABEL', 'Adres email:'); //KJT
define('USERDELETE_MESSAGE_SUCCESS', 'Usunięto użytkowników'); //KJT
define('USERDELETE_MESSAGE_FAILURE', 'Usunięcie użytkowników nie powiodło się!'); //KJT
define('USERSUSPEND_MESSAGE_SUCCESS', 'Zawieszono użytkowników'); //KJT
define('USERSUSPEND_MESSAGE_FAILURE', 'Zawieszenie użytkowników nie powiodło się!'); //KJT
define('USERBAN_MESSAGE_SUCCESS', 'Zablokowano użytkowników'); //KJT
define('USERBAN_MESSAGE_FAILURE', 'Zablokowanie użytkowników nie powiodło się!'); //KJT
define('USERACTIVATE_MESSAGE_SUCCESS', 'Zaktywowano użytkowników'); //KJT
define('USERACTIVATE_MESSAGE_FAILURE', 'Aktywacja użytkowników nie powiodła się!'); //KJT
define('ADMINUSERS_STATUS_INVITED','Zaproszony'); //KJT
define('ADMINUSERS_STATUS_SIGNEDUP','Nie potwierdzony'); //KJT
define('ADMINUSERS_STATUS_PENDING','Oczekujący'); //KJT
define('ADMINUSERS_STATUS_ACTIVE','Aktywny'); //KJT
define('ADMINUSERS_STATUS_SUSPENDED','Zwieszony'); //KJT
define('ADMINUSERS_STATUS_BANNED','Zablokowany'); //KJT
define('ADMINUSERS_STATUS_DELETED','Usunięty'); //KJT
define('ADMINUSERS_STATUS_INVITED_TITLE','Użytkownik został zaproszony, ale nie założył jeszcze konta.'); //KJT
define('ADMINUSERS_STATUS_SIGNEDUP_TITLE','Użytkownik zapisał się, ale nie potwierdził konta przez email.'); //KJT
define('ADMINUSERS_STATUS_PENDING_TITLE','Użytkownik zapisał się i oczekuje na akceptację przez administratora.'); //KJT
define('ADMINUSERS_STATUS_ACTIVE_TITLE','Użytkownik jest aktywny i może się zalogować.'); //KJT
define('ADMINUSERS_STATUS_SUSPENDED_TITLE','Użytkownik został zawieszony i nie może się zalogować.'); //KJT
define('ADMINUSERS_STATUS_BANNED_TITLE','Użytkownik został zablokowany za złamanie zasad serwisu i nie może się zalogować.'); //KJT
define('ADMINUSERS_STATUS_DELETED_TITLE','Użytkownik został usunięty wraz z informacjami o haśle i nie może się zalogować.'); //KJT
define('ADMINUSERS_LASTLOGINTIME_NEVER','Nigdy'); //KJT
/**#@-*/

/**#@+
 * Language constant used by the {@link calendar.php calendar} action
 */
// calendar
define('FMT_SUMMARY', 'Kalendarz dla %s');	// %s ???@@@ TODO KJT What is it?
define('TODAY', 'dzisiaj');
/**#@-*/

/**#@+
 * Language constant used by the {@link category.php category} action
 */
// category
define('ERROR_NO_PAGES', 'Przepraszamy, do kategorii <em>%s</em> nie należą żadne strony');	// %s ???@@@
define('PAGES_BELONGING_TO', 'Do kategorii <em>%2$s</em> należą następujące strony (w ilości %1$d): '); // %1$d number found; %2$s category
/**#@-*/

/**#@+
 * Language constant used by the {@link color.php color} action
 */
// color
define('ERROR_NO_TEXT_GIVEN', 'Nie wpisano tekstu!');
define('ERROR_NO_COLOR_SPECIFIED', 'Nie określono koloru!');
/**#@-*/

/**#@+
 * Language constant used by the {@link contact.php contact} action
 */
// contact
define('SEND_FEEDBACK_LINK_TITLE', 'Prześlij nam swoje uwagi');
define('SEND_FEEDBACK_LINK_TEXT', 'Prześlij nam swoje uwagi'); // TODO KJT 'Kontakt'
/**#@-*/

/**#@+
 * Language constant used by the {@link countowned.php countowned} action
 */
// countowned
define('DISPLAY_MYPAGES_LINK_TITLE', 'Zobacz listę stron, które posiadasz');
/**#@-*/

/**#@+
 * Language constant used by the {@link countpages.php countpages} action
 */
// countpages
define('INDEX_LINK_TITLE', 'Wyświetl alfabetyczny indeks stron');
/**#@-*/

/**#@+
 * Language constant used by the {@link dbinfo.php dbinfo} action
 */
// dbinfo
define('HD_DBINFO', 'Informacja o bazie danych');
define('HD_DBINFO_DB', 'Baza danych');
define('HD_DBINFO_TABLES', 'Tabele');
define('HD_DB_CREATE_DDL', 'Kod SQL tworzący bazę %s:');				# %s will hold database name
define('HD_TABLE_CREATE_DDL', 'Kod SQL tworzący tabelę %s:');				# %s will hold table name
define('TXT_INFO_1', 'Niniejsze narzędzie dostarcza niektórych informacji o bazie (lub bazach) danych i tabelach istniejących w tym systemie.');
define('TXT_INFO_2', ' Jest możliwe, że nie wszystkie istniejące bazy danych lub tabele zostały wyświetlone poniżej &mdash; zależy to od poziomu praw dostępu posiadanych przez skrypt WikkaWiki.');
define('TXT_INFO_3', ' W przypadku, gdy podano kod SQL służący do utworzenia baz danych lub tabel, zawiera on wszystkie informacje potrzebne do kompletnego odtworzenia ich struktury, ');
define('TXT_INFO_4', ' łącznie z wartościami domyślnymi (które wcale nie musiały zostać podane, lecz wynikają z ustawień tego serwera).');
define('FORM_SELDB_LEGEND', 'Bazy danych');
define('FORM_SELTABLE_LEGEND', 'Tabele');
define('FORM_SELDB_OPT_LABEL', 'Wybierz bazę danych:');
define('FORM_SELTABLE_OPT_LABEL', 'Wybierz tabelę:');
define('FORM_SUBMIT_SELDB', 'Wybierz');
define('FORM_SUBMIT_SELTABLE', 'Wybierz');
define('MSG_ONLY_ADMIN', 'Przepraszamy, tylko administratorzy mają dostęp do tych informacji.');
define('MSG_SINGLE_DB', 'Informacje o bazie dancych <tt>%s</tt>.');			# %s will hold database name
define('MSG_NO_TABLES', 'W bazie <tt>%s</tt> nie znaleziono żadnych tabel. Możliwe, że aktualny użytkownik systemu MySQL nie ma odpowiednich praw dostępu do tej bazy.');		# %s will hold database name
define('MSG_NO_DB_DDL', 'Nie udało się pozyskać kodu SQL tworzącego bazę <tt>%s</tt>.');	# %s will hold database name
define('MSG_NO_TABLE_DDL', 'Nie udało się pozyskać kodu SQL tworzącego tabelę <tt>%s</tt>.');# %s will hold table name
/**#@-*/

/**#@+
 * Language constant used by the {@link emailpassword.php emailpassword} action
 */
// emailpassword
define('PW_FORGOTTEN_HEADING', 'Przypomnienie hasła');
define('PW_CHK_SENT', 'Przypomnienie hasła zostało wysłane na adres email użytkownika <em>%s</em>.'); // %s username
define('PW_FORGOTTEN_MAIL', 'Witaj %1$s,'."\n".'ktoś zażądał, aby na ten adres email zostało wysłane przypomnienie hasła do strony "%2$s".'."\n".'Jeśli to nie Ty żądałeś przypomnienia hasła, po prostu zignoruj tę wiadomość, a Twoje hasło nie zostanie zmienione.'."\n\n\n".'** Dane konta'."\n".'--------------------'."\n".'   Nazwa użytkownika: %1$s '."\n".' Przypomnienie hasła: %3$s '."\n".'        Adres strony: %4$s '."\n\n".'Nie zapomnij zmienić hasła zaraz po zalogowaniu!'); // %1$s username; %2$s wiki name; %3$s md5 sum of pw; %4$s login url of the wiki
//define('PW_FORGOTTEN_MAIL', '<p>Witaj %1$s,<br>ktoś zażądał, aby na ten adres email zostało wysłane przypomnienie hasła do strony <em>%2$s</em>.</p><p>Jeśli to nie Ty żądałeś przypomnienia hasła, po prostu zignoruj tę wiadomość, a Twoje hasło nie zostanie zmienione.</p><h4>Dane konta</h4><table><tr><td>Nazwa użytkownika:</td><td><strong>%1$s</strong></td></tr><tr><td>Przypomnienie hasła:</td><td><strong><big>%3$s</big></strong></td></tr><tr><td>Adres strony:</td><td><strong><a href="%4$s">%4$s</a></strong></td></tr></table><p>Nie zapomnij zmienić hasła zaraz po zalogowaniu!</p> '); // %1$s username; %2$s wiki name; %3$s md5 sum of pw; %4$s login url of the wiki
define('PW_FORGOTTEN_MAIL_REF', 'Przypomnienie hasła użytkownika %s'); // %s wiki name
define('PW_FORM_TEXT', 'Podaj swoją nazwę użytkownika. Przypomnienie hasła zostanie wysłane na adres email podany podczas rejestracji.');
define('PW_FORM_FIELDSET_LEGEND', 'Nazwa użytkownika:');
define('ERROR_UNKNOWN_USER', 'Taki użytkownik nie istnieje!');

define('ERROR_MAIL_NOT_SENT', 'Przepraszamy, wystąpił błąd podczas wysyłania przypomnienia hasła. Być może wysyłanie wiadomości jest zablokowane. Skontaktuj się z administratorem, umieszczając komentarz do tej strony.');
define('BUTTON_SEND_PW', 'Wyślij przypomnienie');
define('USERSETTINGS_REF', 'Wróć do strony %s.'); // %s UserSettings link
/**#@-*/

/**#@+
 * Language constant used by the {@link feedback.php feedback} action
 */
// feedback
define('ERROR_EMPTY_NAME', 'Podaj imię');
define('ERROR_INVALID_EMAIL', 'Podaj właściwy adres email');
define('ERROR_EMPTY_MESSAGE', 'Wpisz wiadomość');
define('ERROR_FEEDBACK_MAIL_NOT_SENT', 'Przepraszamy, wystąpił błąd podczas wysyłania. Być może wysyłanie wiadomości jest zablokowane. Spróbuj innej metody skontaktowania się z użytkownikiem <em>%s</em>, na przykład poprzez umieszczenie komentarza do tej strony.'); // %s name of the recipient
define('FEEDBACK_FORM_LEGEND', 'Wiadomość do <em>%s</em>'); //%s wikiname of the recipient
define('FEEDBACK_NAME_LABEL', 'Twoje imię:');
define('FEEDBACK_EMAIL_LABEL', 'Twój adres email:');
define('FEEDBACK_MESSAGE_LABEL', 'Wiadomość:');
define('FEEDBACK_SEND_BUTTON', 'Wyślij');
define('FEEDBACK_SUBJECT', 'Wiadomość wysłana z %s'); // %s name of the wiki
define('SUCCESS_FEEDBACK_SENT', 'Dziękujemy, twoja wiadomość została wysłana.'); //%s name of the sender
/**#@-*/

/**#@+
 * Language constant used by the {@link files.php files action} and {@link handlers/files.xml/files.xml.php files.xml handler}
 */
// files
define('ERROR_UPLOAD_DIRECTORY_NOT_WRITABLE', 'Upewnij się, że serwer WWW ma prawa zapisu do katalogu %s.'); // %s Upload folder ref #89
define('ERROR_UPLOAD_DIRECTORY_NOT_READABLE', 'Upewnij sie, że serwer WWW ma prawa odczytu katalogu %s.'); // %s Upload folder ref #89
define('ERROR_NONEXISTENT_FILE', 'Przepraszamy, plik %s nie istnieje.'); // %s file name ref
define('ERROR_FILE_UPLOAD_INCOMPLETE', 'Transfer pliku został przerwany. Spróbuj ponownie.');
define('ERROR_UPLOADING_FILE', 'Wystąpił błąd podczas transferu pliku.');
define('ERROR_FILE_ALREADY_EXISTS', 'Przepraszamy, plik %s już istnieje.'); // %s file name ref
define('ERROR_EXTENSION_NOT_ALLOWED', 'Przepraszamy, pliki o tym rozszerzeniu nie są akceptowane.');
define('ERROR_FILETYPE_NOT_ALLOWED', 'Przepraszamy, pliki tego typu nie są akceptowane.');
define('ERROR_FILE_NOT_DELETED', 'Przepraszamy, nie można skasować tego pliku!');
define('ERROR_FILE_TOO_BIG', 'Plik jest zbyt duży. Maksymalna dopuszczalna wielkość to %s.'); // %s allowed filesize
define('ERROR_NO_FILE_SELECTED', 'Nie wybrano pliku.');
define('ERROR_FILE_UPLOAD_IMPOSSIBLE', 'Transfer pliku nie jest możliwy, ze względu na błąd w konfiguracji serwera.');
define('SUCCESS_FILE_UPLOADED', 'Transfer pliku zakończony pomyślnie.');
define('FILE_TABLE_CAPTION', 'Załączniki');
define('FILE_TABLE_HEADER_NAME', 'Nazwa pliku');
define('FILE_TABLE_HEADER_SIZE', 'Rozmiar');
define('FILE_TABLE_HEADER_DATE', 'Data ostatniej modyfikacji');
define('FILE_UPLOAD_FORM_LEGEND', 'Dodaj załącznik:');
define('FILE_UPLOAD_FORM_LABEL', 'Plik:');
define('FILE_UPLOAD_FORM_BUTTON', 'Dodaj');
define('DOWNLOAD_LINK_TITLE', 'Pobierz %s'); // %s file name
define('DELETE_LINK_TITLE', 'Usuń %s'); // %s file name
define('NO_ATTACHMENTS', 'Ta strona nie zawiera załączników.');
define('FILES_DELETE_FILE', 'Usunąć plik?');
define('FILES_DELETE_FILE_BUTTON', 'Usuń plik');
define('FILES_CANCEL_BUTTON', 'Anuluj');
define('FILE_DELETED', 'Plik usunięto');
/**#@-*/

/**#@+
 * Language constant used by the {@link googleform.php googleform} action
 */
// googleform
define('GOOGLE_BUTTON', 'Szukaj w Google');
/**#@-*/

/**#@+
 * Language constant used by the {@link highscores.php highscores} action
 */
// include
define('HIGHSCORES_LABEL_EDITS', 'edycji');
define('HIGHSCORES_LABEL_COMMENTS', 'komentarzy');
define('HIGHSCORES_LABEL_PAGES', 'posiadanych stron');
define('HIGHSCORES_CAPTION', 'Najaktywniejsi redaktorzy wg ilości %2$s'); 
define('HIGHSCORES_HEADER_RANK', 'pozycja');
define('HIGHSCORES_HEADER_USER', 'użytkownik');
define('HIGHSCORES_HEADER_PERCENTAGE', 'procent');
/**#@-*/

/**#@+
 * Language constants used by the {@link include.php include} action
 */
// include
define('ERROR_CIRCULAR_REFERENCE', 'Wykryto zapętlenie odnośników!');
define('ERROR_TARGET_ACL', "Nie masz prawa odczytu do wstawionej tu strony <tt>%s</tt>");

/**#@-*/

/**#@+
 * Language constant used by the {@link lastedit.php lastedit} action
 */
// lastedit
define('LASTEDIT_DESC', 'Ostatnio edytowany przez: <em>%s</em>'); // %s user name
define('LASTEDIT_DIFF_LINK_TITLE', 'Porównaj z poprzednią wersją');
/**#@-*/

/**#@+
 * Language constant used by the {@link lastusers.php lastusers} action
 */
// lastusers
define('LASTUSERS_CAPTION', 'Ostatnio zarejestrowani użytkownicy');
define('SIGNUP_DATE_TIME', 'Data rejestracji');
define('NAME_TH', 'Nazwa użytkownika');
define('OWNED_PAGES_TH', 'Posiadane strony');
define('SIGNUP_DATE_TIME_TH', 'Data rejestracji');
/**#@-*/

/**#@+
 * Language constant used by the {@link mindmap.php mindmap} action
 */
// mindmap
define('MM_JRE_INSTALL_REQ', 'Proszę zainstalować %s.'); // %s JRE install link
define('MM_DOWNLOAD_LINK_DESC', 'Pobierz tę mindmapę');
define('MM_EDIT', 'Użyj %s, aby edytować'); // %s link to freemind project TODO KJT 'Test it!'
define('MM_FULLSCREEN_LINK_DESC', 'Otwórz na pełnym ekranie');
define('ERROR_INVALID_MM_SYNTAX', 'Błąd: niewłaściwa składnia akcji MindMap.');
define('PROPER_USAGE_MM_SYNTAX', 'Sposób użycia: %1$s lub %2$s'); // %1$s syntax sample 1; %2$s syntax sample 2
/**#@-*/

/**#@+
 * Language constant used by the {@link mychanges.php mychanges} action
 */
// mychanges
define('NO_PAGES_EDITED', 'Nie edytowałeś jeszcze żadnych stron.');
define('MYCHANGES_ALPHA_LIST', "Lista stron edytowanych przez użytkownika <em>%s</em>, wraz z datą ostatniej edycji.");
define('MYCHANGES_DATE_LIST', "Lista stron edytowanych przez użytkownika <em>%s</em>, uporządkowana wg daty ostatniej edycji.");
define('ORDER_DATE_LINK_DESC', 'uporządkuj wg daty');
define('ORDER_ALPHA_LINK_DESC', 'uporządkuj alfabetycznie');
define('MYCHANGES_NOT_LOGGED_IN', "Nie jesteś zalogowany, więc nie można wyświetlić listy stron, które edytowałeś.");
/**#@-*/

/**#@+
 * Language constant used by the {@link mypages.php mypages} action
 */
// mypages
define('MYPAGES_NONE_OWNED', "Nie posiadasz żadnych stron.");
define('OWNED_PAGES_TXT', "Lista stron posiadanych przez użytkownika <em>%s</em>.");
define('OWNED_NO_PAGES', 'Nie posiadasz żadnych stron.');
define('OWNED_NONE_FOUND', 'Nie znaleziono żadnych stron.');
define('OWNED_NOT_LOGGED_IN', "Nie jesteś zalogowany, więc nie można wyświetlić listy stron, które posiadasz.");
/**#@-*/

/**#@+
 * Language constant used by the {@link newpage.php newpage} action
 */
// newpage
define('NEWPAGE_CREATE_LEGEND', 'Utwórz nową stronę');
define('NEWPAGE_CREATE_BUTTON', 'Utwórz');
/**#@-*/

/**#@+
 * Language constant used by the {@link orphanedpages.php orphanedpages} action
 */
// orphanedpages
define('NO_ORPHANED_PAGES', 'Wszystkie strony posiadają powiązania z innymi. Znakomicie!');

/**#@+
 * Language constant used by the {@link ownedpages.php ownedpages} action
 */
// ownedpages
define('OWNEDPAGES_COUNTS', 'Posiadasz %1$s stron z %2$s stron utworzonych w tej witrynie.'); // %1$s number of pages owned; %2$s total number of pages
define('OWNEDPAGES_PERCENTAGE', 'Oznacza to, że posiadasz %s całości.'); // %s percentage of pages owned
/**#@-*/

/**#@+
 * Language constant used by the {@link pageindex.php pageindex} action
 */
// pageindex
define('PAGEINDEX_HEADING', 'Indeks stron');
define('PAGEINDEX_CAPTION', 'Alfabetyczna lista stron utworzonych w tej witrynie.');
define('PAGEINDEX_OWNED_PAGES_CAPTION', 'Gwiazdką oznaczono strony, które posiadasz.');
define('PAGEINDEX_ALL_PAGES', 'Wszystkie');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges.php recentchanges} action
 */
// recentchanges
define('RECENTCHANGES_HEADING', 'Strony ostatnio zmienione');
define('REVISIONS_LINK_TITLE', 'Zobacz listę wersji strony %s'); // %s page name
define('HISTORY_LINK_TITLE', 'Zobacz ostatnie zmiany na stronie %s'); // %s page name
define('WIKIPING_ENABLED', 'Usługa WikiPing włączona: zmiany wprowadzane w tej witrynie są ogłaszane na %s'); // %s link to wikiping server
define('RECENTCHANGES_NONE_FOUND', 'Żadne strony nie zostały ostatnio zmienione.');
define('RECENTCHANGES_NONE_ACCESSIBLE', 'Nie masz prawa dostępu do żadnych stron, które zostały ostatnio zmienione.');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentcomments.php recentcomments} action
 */
// recentcomments
define('RECENTCOMMENTS_HEADING', 'Ostatnie komentarze');
define('RECENTCOMMENTS_TIMESTAMP_CAPTION', '%s'); // %s timestamp
define('RECENTCOMMENTS_NONE_FOUND', 'Nie znaleziono nowych komentarzy.');
define('RECENTCOMMENTS_NONE_ACCESSIBLE', 'Nie masz prawa dostępu do żadnych nowych komentarzy.');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentlycommented.php recentlycommented} action
 */
// recentlycommented
define('RECENTLYCOMMENTED_HEADING', 'Strony ostatnio skomentowane');
define('RECENTLYCOMMENTED_NONE_FOUND', 'Żadne strony nie zostały ostatnio skomentowane.');
define('RECENTLYCOMMENTED_NONE_ACCESSIBLE', 'Nie masz prawa dostępu do żadnych stron, które zostały ostatnio skomentowane.');
/**#@-*/

/**#@+
 * Language constant used by the {@link system.php system} action
 */
// system
define('SYSTEM_HOST_CAPTION', '(%s)'); // %s host name
define('WIKKA_STATUS_NOT_AVAILABLE', 'b/d');
/**#@-*/

/**#@+
 * Language constant shared by the {@link textsearch.php textsearch} and {@link textsearchexpanded.php textsearchexpanded} actions
 */
// textsearch & textsearchexpanded
define('SEARCH_FOR', 'Szukaj');
define('SEARCH_ZERO_MATCH', 'nic nie znaleziono');
define('SEARCH_ONE_MATCH', 'znaleziono <strong>jedną</strong> stronę');
define('SEARCH_N_MATCH', 'znaleziono stron: <strong>%d</strong>'); // %d number of hits
define('SEARCH_RESULTS', 'Wyniki wyszukiwania hasła <strong>%2$s</strong> &mdash; %1$s.'); # %1$s: n matches for | %2$s: search term
define('SEARCH_NOT_SURE_CHOICE', 'Nie wiesz, którą stronę wybrać?');
define('SEARCH_EXPANDED_LINK_DESC', 'wyszukiwania rozszerzonego'); // search link description
define('SEARCH_TRY_EXPANDED', 'Użyj %s, które pokazuje fragment treści stron.'); // %s expanded search link
define('SEARCH_TIPS', 'Pomoc dotycząca wyszukiwania:');
define('SEARCH_WORD_1', 'granat');
define('SEARCH_WORD_2', 'jabłko');
define('SEARCH_WORD_3', 'sok');
define('SEARCH_WORD_4', 'kolor');
define('SEARCH_WORD_5', 'słoik');
define('SEARCH_WORD_6', 'miodu');
define('SEARCH_PHRASE', sprintf('%s %s',SEARCH_WORD_5,SEARCH_WORD_6));
define('SEARCH_TARGET_1', 'Wyszukuje strony, które zawierają przynajmniej jedno z podanych słów.');
define('SEARCH_TARGET_2', 'Wyszukuje strony, które zawierają oba słowa.');
define('SEARCH_TARGET_3', sprintf("Wyszukuje strony, które zawierają słowo „%1\$s”, ale nie zawierają słowa „%2\$s”.",SEARCH_WORD_1,SEARCH_WORD_4));
define('SEARCH_TARGET_4', "Wyszukuje strony, kótre zawierają takie słowa jak: „granat”, „granatowy”, „granatnik” czy „granaty”."); // make sure target words all *start* with SEARCH_WORD_1
define('SEARCH_TARGET_5', sprintf("Wyszukuje strony, które zawierają frazę „%1\$s” (przykładowo pasuje tu „%1\$s Kubusia Puchatka” ale nie „%2\$s pełen %3\$s”).",SEARCH_PHRASE,SEARCH_WORD_5,SEARCH_WORD_6));
/**#@-*/

/**#@+
 * Language constant used by the {@link usersettings.php usersettings} action
 */
// usersettings
// - error messages
define('ERROR_EMPTY_USERNAME', 'Podaj swoją nazwę użytkownika.');
define('ERROR_NONEXISTENT_USERNAME', 'Złe hasło lub nazwa użytkownika.'); // @@@ too specific
define('ERROR_RESERVED_PAGENAME', 'Przepraszamy, ta nazwa jest zarezerwowana. Użyj innej.');
define('ERROR_WIKINAME', 'Nazwa użytkownika musi być w formacie CamelCase, np. JanKowalski.'); // %1$s identifier WikiName; %2$s sample WikiName/*%2$s*/
define('ERROR_EMPTY_EMAIL_ADDRESS', 'Proszę podać adres email.');
define('ERROR_INVALID_EMAIL_ADDRESS', 'Nieprawidłowy format adresu email.');
define('ERROR_INVALID_PASSWORD', 'Złe hasło lub nazwa użytkownika.');	// @@@ too specific
//define('ERROR_WRONG_PASSWORD', 'Złe hasło lub nazwa użytkownika.');	// @@@ too specific
define('ERROR_INVALID_HASH', 'Złe przypomnienie hasła.');
define('ERROR_INVALID_OLD_PASSWORD', 'Niewłaściwe dotychczasowe hasło.');
define('ERROR_EMPTY_PASSWORD', 'Proszę podać hasło.'); // TODO KJT 'not nice'
define('ERROR_EMPTY_PASSWORD_OR_HASH', 'Proszę podać hasło lub przypomnienie hasła.'); // TODO KJT 'not nice'
define('ERROR_EMPTY_CONFIRMATION_PASSWORD', 'Aby zarejestrować nowego użytkownika, należy potwierdzić hasło.');
define('ERROR_EMPTY_NEW_CONFIRMATION_PASSWORD', 'Aby zmienić hasło, należy wpisać je dwukrotnie.');
define('ERROR_EMPTY_NEW_PASSWORD', 'Należy także podać nowe hasło.');
define('ERROR_PASSWORD_MATCH', 'Podane hasła różnią się.');
define('ERROR_PASSWORD_NO_BLANK', 'Przepraszamy, w hasłach nie można używać spacji.');
define('ERROR_PASSWORD_TOO_SHORT', 'Przepraszamy, hasła muszą składać się co najmniej z %d znaków.'); // %d minimum password length
//define('ERROR_INVALID_INVITATION_CODE', 'Niniejsza witryna ma charakter prywatny &mdash; wstęp tylko z zaproszeniami. Aby się zarejestrować, należy podać specjalny kod zaproszenia, który można uzyskać kontaktując się z administratorem.'); // KJT -- dlaczego to usunięto?!
define('ERROR_INVALID_REVISION_DISPLAY_LIMIT', 'Liczba wyświetlanych wersji stron nie może przekroczyć %d.'); // %d maximum revisions to view
define('ERROR_INVALID_RECENTCHANGES_DISPLAY_LIMIT', 'Liczba wyświetlanych zmian stron nie może przekroczyć %d.'); // %d maximum changed pages to view
if(!defined('ERROR_VALIDATION_FAILED')) define('ERROR_VALIDATION_FAILED', 'Weryfikacja rejestracji nie powiodła się. Spróbuj ponownie.'); // TODO KJT 'is this right?'
// - success messages
define('SUCCESS_USER_LOGGED_OUT', 'Pomyślne wylogowanie.');
define('SUCCESS_USER_REGISTERED', 'Rejestracja powiodła się!');
define('SUCCESS_USER_SETTINGS_STORED', 'Zapisano ustawienia użytkownika!');
define('SUCCESS_USER_PASSWORD_CHANGED', 'Zmiana hasła powiodła się!');
// - captions
define('NEW_USER_REGISTER_CAPTION', 'Jeśli chcesz się zarejestrować, wypełnij poniższe pola:');
define('REGISTERED_USER_LOGIN_CAPTION', 'Jeśli posiadasz już konto, zaloguj się:');
define('RETRIEVE_PASSWORD_CAPTION', 'Zaloguj się używając [[%s przypomnienia hasła]]:'); //%s PasswordForgotten link
define('USER_LOGGED_IN_AS_CAPTION', 'Jesteś zalogowany jako %s'); // %s user name
// - form legends
define('USER_ACCOUNT_LEGEND', 'Twoje konto');
define('USER_SETTINGS_LEGEND', 'Ustawienia');
define('LOGIN_REGISTER_LEGEND', 'Logowanie/Rejestracja');
define('LOGIN_LEGEND', 'Zaloguj');
#define('REGISTER_LEGEND', 'Zarejestruj'); // @@@ TODO to be used later for register-action
define('CHANGE_PASSWORD_LEGEND', 'Zmień hasło');
define('RETRIEVE_PASSWORD_LEGEND', 'Przypomnienie hasła');
// - form field labels (should end in ':' _unless_ it's a checkbox or radio button option)
define('USERSETTINGS_REDIRECT_AFTER_LOGIN_LABEL', 'Po zalogowaniu przekieruj do strony %s ');	// %s page to redirect to
define('USER_EMAIL_LABEL', 'Twój adres email:');
define('DOUBLECLICK_LABEL', 'Włącz edytowanie przez podwójne kliknięcie:');
define('SHOW_COMMENTS_LABEL', 'Domyślnie pokazuj komentarze:');
define('DEFAULT_COMMENT_STYLE_LABEL', 'Domyślny styl komentarzy');
define('COMMENT_ASC_LABEL', 'Płaskie (najstarsze jako pierwsze)');
define('COMMENT_DEC_LABEL', 'Płaskie (najnowsze jako pierwsze)');
define('COMMENT_THREADED_LABEL', 'Ułożone w drzewo');
define('COMMENT_DELETED_LABEL', '[Komentarz usunięty]');
define('COMMENT_BY_LABEL', 'Napisany przez: '); // TODO KJT 'change to use %s'
define('RECENTCHANGES_DISPLAY_LIMIT_LABEL', 'Liczba wyświetlanych zmian stron:');
define('PAGEREVISION_LIST_LIMIT_LABEL', 'Liczba wyświetlanych wersji stron:');
define('NEW_PASSWORD_LABEL', 'Nowe hasło:');
define('NEW_PASSWORD_CONFIRM_LABEL', 'Potwierdź nowe hasło:');
define('NO_REGISTRATION', 'Rejestracja nowych kont w tej witrynie została wyłączona.');
define('PASSWORD_LABEL', 'Hasło (min. %s znaków):'); // %s minimum number of characters
define('CONFIRM_PASSWORD_LABEL', 'Potwierdź hasło:');
define('TEMP_PASSWORD_LABEL', 'Przypomnienie hasła:');
define('INVITATION_CODE_SHORT', 'kod zaproszenia');
define('INVITATION_CODE_LONG', 'Aby się zarejestrować, należy podać specjalny kod zaproszenia, który można uzyskać kontaktując się z administratorem.');
define('INVITATION_CODE_LABEL', 'Twój %s:'); // %s expanded short invitation code prompt
define('WIKINAME_SHORT', 'NazwaUzytkownika');
define('WIKINAME_LONG', sprintf('Nazwa użytkownika musi składać się z co najmniej dwóch wyrazów napisanych z wielkiej litery, bez spacji ani polskich znaków, np. %s',WIKKA_SAMPLE_WIKINAME));
define('WIKINAME_LABEL', '%s:'); // %s expanded short wiki name prompt
// - form options
define('CURRENT_PASSWORD_OPTION', 'Aktualne hasło');
define('PASSWORD_REMINDER_OPTION', 'Przypomnienie hasła');
// - form buttons
define('UPDATE_SETTINGS_BUTTON', 'Zapisz zmiany');
define('LOGIN_BUTTON', 'Zaloguj');
define('LOGOUT_BUTTON', 'Wyloguj');
define('CHANGE_PASSWORD_BUTTON', 'Zmień hasło');
define('REGISTER_BUTTON', 'Zarejestruj');
/**#@-*/

/**#@+
 * Language constant used by the {@link wantedpages.php wantedpages} action
 */
// wantedpages
define('SORTING_LEGEND', 'Sortowanie');
define('SORTING_NUMBER_LABEL', '%d. kryterium:');
define('SORTING_DESC_LABEL', 'malejąco');
define('OK_BUTTON', '   OK   ');
define('NO_WANTED_PAGES', 'Żadne strony nie oczekują na utworzenie. Znakomicie!');
/**#@-*/

/**#@+
 * Language constant used by the {@link wikkaconfig.php wikkaconfig} action
 */
//wikkaconfig
define('WIKKACONFIG_CAPTION', "Ustawienia systemu Wikka [%s]"); // %s link to Wikka Config options documentation
define('WIKKACONFIG_DOCS_URL', "http://docs.wikkawiki.org/ConfigurationOptions");
define('WIKKACONFIG_DOCS_TITLE', "Przeczytaj dokumentację ustawień systemu Wikka"); //KJT
define('WIKKACONFIG_TH_OPTION', "Opcja");
define('WIKKACONFIG_TH_VALUE', "Wartość");

/* ------------------ 3RD PARTY ------------------ */

/**#@+
 * Language constant used by the {@link fullscreen.php fullscreen} 3rd party MindMap display utility
 */
// fullscreen
define('CLOSE_WINDOW', 'Zamknij okno');
define('MM_GET_JAVA_PLUGIN_LINK_DESC', 'Pobierz najnowszą wersję Java'); // used in MM_GET_JAVA_PLUGIN
define('MM_GET_JAVA_PLUGIN', '%s.'); // %s - plugin download link
/**#@-*/


/* ------------------ FORMATTERS ------------------ */

/**#@+
 * Language constant used by the {@link wakka.php wakka} formatter
 */
// wakka
define('GRABCODE_BUTTON', 'Pobierz');
define('GRABCODE_BUTTON_TITLE', 'Pobierz %s'); // %s download filename
/**#@-*/


/* ------------------ HANDLERS (PAGE) ------------------ */

/**#@+
 * Language constant used by the {@link acls.php acls} (page) handler
 */
// acls
// TODO: 'translate' DB value '(Public)' when displaying it!
define('ACLS_UPDATED', 'Prawa dostępu zostały zaktualizowane.');
define('NO_PAGE_OWNER', '(Nikt)');
define('NOT_PAGE_OWNER', 'Nie jesteś właścicielem tej strony.');
define('PAGE_OWNERSHIP_CHANGED', 'Nowy właściciel strony: %s'); // %s name of new owner
define('ACLS_LEGEND', 'Prawa dostępu do strony %s'); // %s name of current page
define('ACLS_READ_LABEL', 'Prawo odczytu:');
define('ACLS_WRITE_LABEL', 'Prawo zapisu:');
define('ACLS_COMMENT_READ_LABEL', 'Prawo odczytu komentarza:');
define('ACLS_COMMENT_POST_LABEL', 'Prawo dodania komentarza:');
define('SET_OWNER_LABEL', 'Zmień właściciela:');
define('SET_OWNER_CURRENT_OPTION', '(aktualny właściciel)');
define('SET_OWNER_PUBLIC_OPTION', '(Publiczna)'); // actual DB value will remain '(Public)' even if this option text is translated!
define('SET_NO_OWNER_OPTION', '(Nikt &mdash; uwolnij stronę)');
define('ACLS_STORE_BUTTON', 'Zapisz prawa dostępu');
define('CANCEL_BUTTON', 'Anuluj');
// - syntax
define('ACLS_SYNTAX_HEADING', 'Składnia:');
define('ACLS_EVERYONE', 'Wszyscy');
define('ACLS_REGISTERED_USERS', 'Użytkownicy zarejestrowani');
define('ACLS_NONE_BUT_ADMINS', 'Nikt (poza administratorami)');
define('ACLS_ANON_ONLY', 'Tylko użytkownicy anonimowi');
define('ACLS_LIST_USERNAMES', 'użytkownik %s; możesz podać dowolną liczbę użytkowników, po jednym w każdej linii'); // %s sample user name
define('ACLS_NEGATION', 'Każdy z powyższych wpisów może zostać zanegowany przy użyciu znaku %s:'); // %s 'negation' mark
define('ACLS_DENY_USER_ACCESS', 'zabroni dostępu użytkownikowi %s'); // %s sample user name
define('ACLS_AFTER', 'po');
define('ACLS_TESTING_ORDER1', 'Prawa dostępu są stosowane w kolejności wpisania.');
define('ACLS_TESTING_ORDER2', 'Dlatego jeżeli chcesz użyć znaku %1$s, należy go wpisać w osobnej linijce %2$s wpisach zabraniających dostępu wybranym użytkownikom.'); // %1$s 'all' mark; %2$s emphasised 'after'
define('ACLS_DEFAULT_ACLS', 'Usunięcie wszystkich wartości z którejś z powyższych czterech list, spowoduje ustawienie dla niej wartości domyślnych, zdefiniowanych w pliku %s.');
/**#@-*/

/**#@+
 * Language constant used by the {@link backlinks.php backlinks} (page) handler
 */
// backlinks
define('PAGE_TITLE','Strony zawierające odnośniki do %s');
define('MESSAGE_NO_BACKLINKS','Żadne strony nie zawierają odnośników do tej strony.');
define('MESSAGE_MISSING_PAGE','Niestety, strona %s nie istnieje.');
define('MESSAGE_PAGE_INACCESSIBLE', 'Nie masz praw odczytu tej strony');
/**#@-*/

/**#@+
 * Language constant used by the {@link claim.php claim} (page) handler
 */
// claim
define('USER_IS_NOW_OWNER', 'Jesteś teraz właścicielem tej strony.');
/**#@-*/

/**#@+
 * Language constant used by the {@link clone.php clone} (page) handler
 */
// clone
define('ERROR_ACL_WRITE', 'Nie masz prawa zapisu strony %s');
define('CLONE_VALID_TARGET', 'Podaj prawidłową nazwę nowej strony oraz opcjonalnie „opis zmian”.');
define('CLONE_LEGEND', 'Duplikuj %s'); // %s source page name
define('CLONED_FROM', 'Skopiowano z %s'); // %s source page name
define('SUCCESS_CLONE_CREATED', 'Strona %s została pomyślnie utworzona!'); // %s new page name
define('CLONE_X_TO_LABEL', 'Nazwa kopii:');
define('CLONE_EDIT_NOTE_LABEL', 'Opis zmian:');
define('CLONE_EDIT_OPTION_LABEL', ' Po skopiowaniu przejdź do edycji &nbsp;');
define('CLONE_ACL_OPTION_LABEL', ' Skopiuj także prawa dostępu');
define('CLONE_BUTTON', 'Duplikuj');
/**#@-*/

/**#@+
 * Language constant used by the {@link delete.php delete} (page) handler
 */
// delete
define('ERROR_NO_PAGE_DEL_ACCESS', 'Nie masz prawa usunięcia tej strony.');
define('PAGE_DELETION_HEADER', 'Usuń stronę %s'); // %s name of the page
define('SUCCESS_PAGE_DELETED', 'Strona została usunięta!');
define('PAGE_DELETION_CAPTION', 'Czy usunąć stronę oraz wszystkie jej komentarze?');
define('PAGE_DELETION_DELETE_BUTTON', 'Usuń stronę');
define('PAGE_DELETION_CANCEL_BUTTON', 'Anuluj');
/**#@-*/

/**#@+
 * Language constant used by the {@link diff.php diff} (page) handler
 */
// diff
define('ERROR_DIFF_LIBRARY_MISSING', 'Nie znaleziono pliku <tt>'.WIKKA_LIBRARY_PATH.DIRECTORY_SEPARATOR.'diff.lib.php</tt>. Powiadom administratora!');
define('ERROR_BAD_PARAMETERS', 'Podano nieprawidłowe parametry. Prawdopodobnie jedna z wersji wybranych do porównania została już usunięta.');
//define('DIFF_ADDITIONS_HEADER', 'Dodano:');
//define('DIFF_DELETIONS_HEADER', 'Usunięto:');
//define('DIFF_NO_DIFFERENCES', 'Brak różnic');
//define('DIFF_FAST_COMPARISON_HEADER', 'Porównanie wersji %1$s z wersją %2$s'); // %1$s link to page A; %2$s link to page B
define('DIFF_COMPARISON_HEADER', 'Porównanie %1$s strony %2$s'); // %1$s - link to revision list; %2$s - link to page
define('DIFF_REVISION_LINK_TITLE', 'Wyświetl listę wersji strony %s'); // %s page name
define('DIFF_PAGE_LINK_TITLE', 'Przejdź do najnowszej wersji tej strony');

define('DIFF_SAMPLE_ADDITION', '&nbsp;teksty dodane&nbsp;');
define('DIFF_SAMPLE_DELETION', '&nbsp;teksty usunięte&nbsp;');
define('DIFF_SIMPLE_BUTTON', 'Porównanie uproszczone');
define('DIFF_FULL_BUTTON', 'Porównanie szczegółowe');
define('HIGHLIGHTING_LEGEND', 'Legenda:');

/**#@-*/

/**#@+
 * Language constant used by the {@link edit.php edit} (page) handler
 */
// edit
define('ERROR_OVERWRITE_ALERT1', 'KONFLIKT EDYCJI: Ktoś inny zmodyfikował tę stronę, podczas gdy ją edytowałeś.');
define('ERROR_OVERWRITE_ALERT2', 'Kopiuj swoje zmiany i rozpocznij edycję od początku.');
define('ERROR_MISSING_EDIT_NOTE', 'BRAK OPISU ZMIAN: proszę podać opis zmian!');
define('ERROR_TAG_TOO_LONG', 'Nazwa strony jest zbyt długa! Maksymalna liczba znaków: %d.'); // %d maximum page name length
define('ERROR_NO_WRITE_ACCESS', 'Nie masz prawa zapisu tej strony. Aby móc wprowadzić zmiany, powinieneś się [[UserSettings zalogować lub zarejestrować]].'); //TODO Distinct links for login and register actions
define('EDIT_STORE_PAGE_LEGEND', 'Zapisz stronę');
define('EDIT_PREVIEW_HEADER', 'Podgląd');
define('EDIT_NOTE_LABEL', 'dodaj opis wprowadzonych zmian'); // label after field, so no colon!
define('MESSAGE_AUTO_RESIZE', 'Kliknij %s aby automatycznie obciąć nazwę do wymaganego rozmiaru.'); // %s rename button text
define('EDIT_PREVIEW_BUTTON', 'Podgląd');
define('EDIT_STORE_BUTTON', 'Zapisz');
define('EDIT_REEDIT_BUTTON', 'Wróć do edycji');
define('EDIT_CANCEL_BUTTON', 'Anuluj');
define('EDIT_RENAME_BUTTON', 'Zmień nazwę');
define('ACCESSKEY_PREVIEW', 'p'); // ideally, should match EDIT_PREVIEW_BUTTON
define('ACCESSKEY_STORE', 'z'); // ideally, should match EDIT_STORE_BUTTON
define('ACCESSKEY_REEDIT', 'w'); // ideally, should match EDIT_REEDIT_BUTTON
define('SHOWCODE_LINK', 'Zobacz źródło tej strony'); // TODO KJT not sure...
define('SHOWCODE_LINK_TITLE', 'Zobacz źródło tej strony'); // @@@ TODO 'View page formatting code' TODO KJT not sure here either.,,
define('EDIT_COMMENT_TIMESTAMP_CAPTION', '(%s)'); // %s timestamp
if (!defined('ERROR_INVALID_PAGEID')) define('ERROR_INVALID_PAGEID', 'Dla żądanej strony nie istnieje wskazany numer wersji');
/**#@-*/

/**#@+
 * Language constant used by the {@link grabcode.php grabcode} (page) handler
 */
// grabcode
define('ERROR_NO_CODE', 'Przepraszamy, nie ma kodu do pobrania.');
/**#@-*/

/**#@+
 * Language constant used by the {@link history.php history} (page) handler
 */
// history
define('EDITED_ON', 'Zmodyfikowana %1$s przez użytkownika: %2$s'); // %1$s time; %2$s user name
define('HISTORY_PAGE_VIEW', 'Historia zmian strony %s'); // %s pagename
define('OLDEST_VERSION_EDITED_ON_BY', 'Najstarsza znana wersja tej strony. Została utworzona %1$s przez użytkownika: %2$s'); // %1$s time; %2$s user name
define('MOST_RECENT_EDIT', 'Aktualna wersja. Zmodyfikowana %1$s przez użytkownika: %2$s'); // %1$s time; %2$s user name
define('HISTORY_MORE_LINK_DESC', 'Zobacz dalszą część historii zmian'); // used for alternative history link in HISTORY_MORE
define('HISTORY_MORE', 'Nie można wyświetlić całej historii zmian na jednej stronie. %s.'); // %s alternative history link # @@@ TODO avoid using 'here' ^
/**#@-*/

/**#@+
 * Language constant shared by the {@link processcomment.php processcomment} and {@link show.php show} (page) handlers
 */
// processcomment & show
// - comment buttons
define('COMMENT_DELETE_BUTTON', 'Usuń');
define('COMMENT_REPLY_BUTTON', 'Odpowiedz');
define('COMMENT_ADD_BUTTON', 'Dodaj komentarz');
define('COMMENT_NEW_BUTTON', 'Nowy komentarz');
/**#@-*/

/**#@+
 * Language constant used by the {@link processcomment.php processcomment} (page) handler
 */
// processcomment
define('ERROR_NO_COMMENT_DEL_ACCESS', 'Przepraszamy, nie możesz usunąć tego komentarza!');
define('ERROR_NO_COMMENT_WRITE_ACCESS', 'Przepraszamy, nie możesz komentować tej strony.');
define('ERROR_EMPTY_COMMENT', 'Treść komentarza jest pusta &mdash; nie zapisano!');
define('ERROR_COMMENT_NO_KEY', "Nie można zapisać komentarza. Skontaktuj się z administratorem wiki.");
define('ERROR_COMMENT_INVALID_KEY', "Nie można zapisać komentarza. Skontaktuj się z administratorem wiki.");
define('ADD_COMMENT_LABEL', 'W odpowiedzi na %s:');
define('NEW_COMMENT_LABEL', 'Napisz komentarz:');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges_simple.xml.mm.php recentchanges_simple.xml.mm} (page) handler
 */
// recentchanges_simple.xml.mm
define('FIRST_NODE_LABEL', 'Ostatnie zmiany');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges.xml.php recentchanges.xml} (page) handler
 */
// recentchanges.xml
define('RECENTCHANGES_DESC', 'Ostatnie zmiany strony %s'); // %s page name
/**#@-*/

/**#@+
 * Language constant shared by the {@link referrers_sites.php referrers_sites}, {@link referrers.php referrers} and {@link review_blacklist.php review_blacklist} (page) handlers
 */
// referrers_sites + referrers + review_blacklist
define('REFERRERS_PURGE_24_HOURS', 'w ciągu ostatnich 24 godzin');
define('REFERRERS_PURGE_N_DAYS', 'w ciągu ostatnich %d dni'); // %d number of days
define('REFERRERS_NO_SPAM', 'Do spamerów: Ta strona nie jest indeksowana przez wyszukiwarki. Szkoda waszego czasu.');
define('REFERRERS_DOMAINS_TO_WIKI_LINK_DESC', 'Domeny, z których przychodzili odwiedzający tę witrynę');
define('REFERRERS_DOMAINS_TO_PAGE_LINK_DESC', 'Domeny, z których przychodzili odwiedzający stronę %s'); // %s page name
define('REFERRERS_URLS_TO_WIKI_LINK_DESC', 'Adresy, z których przychodzili odwiedzający tę witrynę');
define('REFERRERS_URLS_TO_PAGE_LINK_DESC', 'Adresy, z których przychodzili odwiedzający stronę %s'); // %s page name
define('REFERRER_BLACKLIST_LINK_DESC', 'Zobacz czarną listę stron, nie umieszczanych w tym spisie');
define('BLACKLIST_LINK_DESC', 'Czarna lista');
define('NONE_CAPTION', 'Brak');
define('PLEASE_LOGIN_CAPTION', 'Aby zobaczyć listę adresów spod których przychodzą odwiedzający tę stronę, musisz się zalogować.');
/**#@-*/

/**#@+
 * Language constant used by the {@link referrers_sites.php referrers_sites} (page) handler
 */
// referrers_sites
define('REFERRERS_URLS_LINK_DESC', 'Wyświetl wg adresów');
define('REFERRERS_DOMAINS_TO_WIKI', 'Domeny, z których przychodzili odwiedzający tę witrynę. [%s]'); // %s link to referrers handler
define('REFERRERS_DOMAINS_TO_PAGE', 'Domeny, z których przychodzili odwiedzający tę stronę (%1$s) %2$s. [%3$s]'); // %1$s page link; %2$s purge time; %3$s link to referrers handler
/**#@-*/

/**#@+
 * Language constant used by the {@link referrers.php referrers} (page) handler
 */
// referrers
define('REFERRERS_DOMAINS_LINK_DESC', 'Wyświetl wg domen');
define('REFERRERS_URLS_TO_WIKI', 'Adresy, z których przychodzili odwiedzający tę witrynę. [%s]'); // %s link to referrers_sites handler
define('REFERRERS_URLS_TO_PAGE', 'Adresy, z których przychodzili odwiedzający tę stronę (%1$s) %2$s. [%3$s]'); // %1$s page link; %2$s purge time; %3$s link to referrers_sites handler
/**#@-*/

/**#@+
 * Language constant used by the {@link review_blacklist.php review_blacklist} (page) handler
 */
// review_blacklist
define('BLACKLIST_HEADING', 'Czarna lista &mdash; strony nie uwzględniane w spisach miejsc pochodzenia odwiedzających');
define('BLACKLIST_REMOVE_LINK_DESC', 'Usuń');
define('STATUS_BLACKLIST_EMPTY', 'Czarna lista jest pusta.');
/**#@-*/

/**#@+
 * Language constant used by the {@link revisions.php revisions} (page) handler
 */
// revisions
define('REVISIONS_CAPTION', 'Wersje strony %s'); // %s pagename
define('REVISIONS_NO_REVISIONS_YET', 'Nie ma jeszcze historii');
define('REVISIONS_SIMPLE_DIFF', 'proste porównanie');
define('REVISIONS_MORE_CAPTION', 'Istnieje więcej wersji tej strony. Kliknij poniżej, aby je zobaczyć.'); // %s text of REVISIONS_MORE_BUTTON
define('REVISIONS_RETURN_TO_NODE_BUTTON', 'Powrót do treści strony');
define('REVISIONS_SHOW_DIFFERENCES_BUTTON', 'Pokaż różnice');
define('REVISIONS_MORE_BUTTON', 'Zobacz wcześniejsze wersje');
/**#@-*/

/**#@+
 * Language constant used by the {@link revisions.xml.php revisions.xml} (page) handler
 */
// revisions.xml
define('REVISIONS_EDITED_BY', 'Napisana przez %s'); // %s user name
define('HISTORY_REVISIONS_OF', 'Historia wersji strony %s'); // %s page name
/**#@-*/

/**#@+
 * Language constant used by the {@link show.php show} (page) handler
 */
// show
define('SHOW_RE_EDIT_BUTTON', 'Edytuj tę wersję');
define('SHOW_FORMATTED_BUTTON', 'Pokaż wersję sformatowaną');
define('SHOW_SOURCE_BUTTON', 'Pokaż źródło');
define('SHOW_ASK_CREATE_PAGE_CAPTION', 'Nie ma jeszcze strony o tej nazwie. Czy chcesz ją %s?'); // %s page create link
define('SHOW_OLD_REVISION_CAPTION', 'To jest stara wersja strony %1$s, utworzona przez użytkownika: %2$s, datowana: %3$s.'); // %1$s - page link; %2$s - username; %3$s - timestamp; 
define('COMMENTS_CAPTION', 'Komentarze');
define('DISPLAY_COMMENTS_LABEL', 'Pokaż komentarze.');
define('DISPLAY_COMMENT_LINK_DESC', 'Pokaż komentarz.');
define('DISPLAY_COMMENTS_EARLIEST_LINK_DESC', 'Starsze jako pierwsze'); // TODO KJT 'not used!'
define('DISPLAY_COMMENTS_LATEST_LINK_DESC', 'Niedawne jako pierwsze'); // TODO KJT 'not used!'
define('DISPLAY_COMMENTS_THREADED_LINK_DESC', 'W drzewku'); // TODO KJT 'not used!'
define('HIDE_COMMENTS_LINK_DESC', 'Ukryj');
define('STATUS_NO_COMMENTS', 'Nie ma jeszcze komentarzy.');
define('STATUS_ONE_COMMENT', 'Tę stronę skomentowano jeden raz.');
define('STATUS_SOME_COMMENTS', 'Tę stronę skomentowano %d razy.'); // %d number of comments
define('COMMENT_TIME_CAPTION', '%s'); // %s comment time
/**#@-*/

/**#@+
 * Language constant used by the {@link showcode.php showcode} (page) handler
 */
// showcode
define('SOURCE_HEADING', 'Kod źródłowy strony %s'); // %s page link
define('SHOW_RAW_LINK_DESC', 'Wyświetl sam kod źródłowy');
/**#@-*/

/* ------------------ LIBS ------------------*/

/**#@+
 * Language constant used by the {@link Wakka.class.php Wakka class} (the Wikka core containing most methods)
 */
// Wakka.class
define('QUERY_FAILED', 'Błąd zapytania.');
define('REDIR_DOCTITLE', 'Przekierowano do %s'); // %s target page
define('REDIR_LINK_DESC', 'użyj tego linku'); // used in REDIR_MANUAL_CAPTION
define('REDIR_MANUAL_CAPTION', 'Jeżeli przekierowanie nie nastąpi automatycznie, %s'); // %s target page link
define('CREATE_THIS_PAGE_LINK_TITLE', 'Utwórz tę stronę');
define('ACTION_UNKNOWN_SPECCHARS', 'Nie znaleziono akcji; jej nazwa nie może zawierać znaków specjalnych.');
define('ACTION_UNKNOWN', 'Nie znaleziono akcji „%s”.'); // %s action name
define('HANDLER_UNKNOWN_SPECCHARS', 'Nie znaleziono obiektu obsługującego; jego nazwa nie może zawierać znaków specjalnych.');
define('HANDLER_UNKNOWN', 'Nie znaleziono obiektu obsługującego „%s”.'); // %s handler name
define('FORMATTER_UNKNOWN_SPECCHARS', 'Nie znaleziono obiektu formatującego; jego nazwa nie może zawierać znaków specjalnych.');
define('FORMATTER_UNKNOWN', 'Nie znaleziono obiektu formatującego „%s”.'); // %s formatter name
/**#@-*/

/* ------------------ SETUP ------------------ */
/**#@+
 * Language constant used by the {@link index.php setup} program (and several included files)
 */
// @@@ later....
/**#@-*/

?>
