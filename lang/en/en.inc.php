<?php
/**
 * Wikka language file.
 *
 * This file holds all interface language strings for Wikka.
 *
 * @package 		Language
 *
 * @version		$Id:en.inc.php 481 2007-05-17 16:34:24Z DarTar $
 * @license 		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author 		{@link http://wikkawiki.org/NilsLindenberg Nils Lindenberg}
 * @author 		{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 * @author 		{@link http://wikkawiki.org/JavaWoman Marjolein Katsma}
 * @author 		{@link http://wikkawiki.org/DotMG Mahefa Randimbisoa}
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

//if(!defined('')) define('', ''); //
/**#@+
 * Language constant shared among several Wikka components
 */
// NOTE: all common names (used in multiple files) should start with WIKKA_ !
if(!defined('WIKKA_ADMIN_ONLY_TITLE')) define('WIKKA_ADMIN_ONLY_TITLE', 'Sorry, only wiki administrators can display this information'); //title for elements that are only displayed to admins
if(!defined('WIKKA_ERROR_SETUP_FILE_MISSING')) define('WIKKA_ERROR_SETUP_FILE_MISSING', 'A file of the installer / upgrader was not found. Please install Wikka again!');
if(!defined('WIKKA_ERROR_MYSQL_ERROR')) define('WIKKA_ERROR_MYSQL_ERROR', 'MySQL error: %d - %s');	// %d - error number; %s - error text
if(!defined('WIKKA_ERROR_CAPTION')) define('WIKKA_ERROR_CAPTION', 'Error');
if(!defined('WIKKA_ERROR_ACL_READ')) define('WIKKA_ERROR_ACL_READ', 'You are not allowed to read this page.');
if(!defined('WIKKA_ERROR_ACL_READ_SOURCE')) define('WIKKA_ERROR_ACL_READ_SOURCE', 'You are not allowed to read the source of this page.');
if(!defined('WIKKA_ERROR_ACL_READ_INFO')) define('WIKKA_ERROR_ACL_READ_INFO', 'You are not allowed to access this information.');
if(!defined('WIKKA_ERROR_LABEL')) define('WIKKA_ERROR_LABEL', 'Error');
if(!defined('WIKKA_ERROR_PAGE_NOT_EXIST')) define('WIKKA_ERROR_PAGE_NOT_EXIST', 'Sorry, page %s does not exist.'); // %s (source) page name
if(!defined('WIKKA_ERROR_EMPTY_USERNAME')) define('WIKKA_ERROR_EMPTY_USERNAME', 'Please fill in your username!');
if(!defined('WIKKA_DIFF_ADDITIONS_HEADER')) define('WIKKA_DIFF_ADDITIONS_HEADER', 'Additions:');
if(!defined('WIKKA_DIFF_DELETIONS_HEADER')) define('WIKKA_DIFF_DELETIONS_HEADER', 'Deletions:');
if(!defined('WIKKA_DIFF_NO_DIFFERENCES')) define('WIKKA_DIFF_NO_DIFFERENCES', 'No Differences');
if(!defined('ERROR_USERNAME_UNAVAILABLE')) define('ERROR_USERNAME_UNAVAILABLE', 'Sorry, this user name is unavailable.');
if(!defined('ERROR_USER_SUSPENDED')) define('ERROR_USER_SUSPENDED', 'Sorry, this account has been suspended. Please contact an administrator for further details.');
if(!defined('WIKKA_ERROR_INVALID_PAGE_NAME')) define('WIKKA_ERROR_INVALID_PAGE_NAME', 'The page name %s is invalid. Valid page names must start with a capital letter, contain only letters and numbers, and be in CamelCase format.'); // %s - page name
if(!defined('WIKKA_ERROR_PAGE_ALREADY_EXIST')) define('WIKKA_ERROR_PAGE_ALREADY_EXIST', 'Sorry, the target page already exists');
if(!defined('WIKKA_LOGIN_LINK_DESC')) define('WIKKA_LOGIN_LINK_DESC', 'login');
if(!defined('WIKKA_MAINPAGE_LINK_DESC')) define('WIKKA_MAINPAGE_LINK_DESC', 'main page');
if(!defined('WIKKA_NO_OWNER')) define('WIKKA_NO_OWNER', 'Nobody');
if(!defined('WIKKA_NOT_AVAILABLE')) define('WIKKA_NOT_AVAILABLE', 'n/a');
if(!defined('WIKKA_NOT_INSTALLED')) define('WIKKA_NOT_INSTALLED', 'not installed');
if(!defined('WIKKA_ANONYMOUS_USER')) define('WIKKA_ANONYMOUS_USER', 'anonymous'); // 'name' of non-registered user
if(!defined('WIKKA_UNREGISTERED_USER')) define('WIKKA_UNREGISTERED_USER', 'unregistered user'); // alternative for 'anonymous' @@@ make one string only?
if(!defined('WIKKA_ANONYMOUS_AUTHOR_CAPTION')) define('WIKKA_ANONYMOUS_AUTHOR_CAPTION', '('.WIKKA_UNREGISTERED_USER.')'); // @@@ or WIKKA_ANONYMOUS_USER
if(!defined('WIKKA_SAMPLE_WIKINAME')) define('WIKKA_SAMPLE_WIKINAME', 'JohnDoe'); // must be a CamelCase name
if(!defined('WIKKA_HISTORY')) define('WIKKA_HISTORY', 'history');
if(!defined('WIKKA_REVISIONS')) define('WIKKA_REVISIONS', 'revisions');
if(!defined('WIKKA_REVISION_NUMBER')) define('WIKKA_REVISION_NUMBER', 'Revision %s');
if(!defined('WIKKA_REV_WHEN_BY_WHO')) define('WIKKA_REV_WHEN_BY_WHO', '%1$s by %2$s'); // %1$s - timestamp; %2$s - user name
if(!defined('WIKKA_NO_PAGES_FOUND')) define('WIKKA_NO_PAGES_FOUND', 'No pages found.');
if(!defined('WIKKA_NO_PAGES_FOUND_FOR')) define('WIKKA_NO_PAGES_FOUND_FOR', 'No pages found for %s.');
if(!defined('WIKKA_PAGE_OWNER')) define('WIKKA_PAGE_OWNER', 'Owner: %s'); // %s - page owner name or link
if(!defined('WIKKA_COMMENT_AUTHOR_DIVIDER')) define('WIKKA_COMMENT_AUTHOR_DIVIDER', ', comment by '); //TODo check if we can construct a single phrase here
if(!defined('WIKKA_PAGE_EDIT_LINK_DESC')) define('WIKKA_PAGE_EDIT_LINK_DESC', 'edit');
if(!defined('WIKKA_PAGE_CREATE_LINK_DESC')) define('WIKKA_PAGE_CREATE_LINK_DESC', 'create');
if(!defined('WIKKA_PAGE_EDIT_LINK_TITLE')) define('WIKKA_PAGE_EDIT_LINK_TITLE', 'Click to edit %s'); // %s page name @@@ 'Edit %s'
if(!defined('WIKKA_BACKLINKS_LINK_TITLE')) define('WIKKA_BACKLINKS_LINK_TITLE', 'Display a list of pages linking to %s'); // %s page name
if(!defined('WIKKA_JRE_LINK_DESC')) define('WIKKA_JRE_LINK_DESC', 'Java Runtime Environment');
if(!defined('WIKKA_NOTE')) define('WIKKA_NOTE', 'NOTE:');
if(!defined('WIKKA_JAVA_PLUGIN_NEEDED')) define('WIKKA_JAVA_PLUGIN_NEEDED', 'Java 1.4.1 (or later) Plug-in is needed to run this applet,');
if(!defined('REVISION_DATE_FORMAT')) define('REVISION_DATE_FORMAT', 'D, d M Y'); // @TODO
if(!defined('REVISION_TIME_FORMAT')) define('REVISION_TIME_FORMAT', 'H:i T'); // @TODO
if(!defined('TITLE_REVISION_LINK')) define('TITLE_REVISION_LINK', 'View recent revisions list for %s');  // @TODO
if(!defined('INPUT_ERROR_STYLE')) define('INPUT_ERROR_STYLE', 'class="highlight"'); // @TODO
if(!defined('CANCEL_ACL_LABEL')) define('CANCEL_ACL_LABEL', 'Cancel'); // @TODO
if(!defined('UNREGISTERED_USER')) define('UNREGISTERED_USER', 'unregistered user');  // @TODO
if(!defined('NOT_AVAILABLE')) define('NOT_AVAILABLE', 'n/a'); // @TODO replace inside the actions with WIKKA_NOT_AVAILABLE
if(!defined('WHEN_BY_WHO')) define('WHEN_BY_WHO', '%1$s by %2$s'); // @TODO
if(!defined('ERROR_ACL_READ_INFO')) define('ERROR_ACL_READ_INFO', 'You\'re not allowed to access this information.'); // @TODO
if(!defined('I18N_LANG')) define('I18N_LANG', 'en-US'); // @TODO
/**#@-*/

/*  ------------------ CORE ------------------  */

/**#@+
 * Language constant for the core {@link wikka.php wikka} program
 */
// wikka
if(!defined('ERROR_WAKKA_LIBRARY_MISSING')) define('ERROR_WAKKA_LIBRARY_MISSING','The necessary file "libs/Wakka.class.php" could not be found. To run Wikka, please make sure the file exists and is placed in the right directory!');
if(!defined('ERROR_WRONG_PHP_VERSION')) define('ERROR_WRONG_PHP_VERSION', 'Wikka requires PHP %s or higher!');  // %s - version number
if(!defined('ERROR_MYSQL_SUPPORT_MISSING')) define('ERROR_MYSQL_SUPPORT_MISSING', 'PHP can\'t find MySQL support but Wikka requires MySQL. Please check the output of <tt>phpinfo()</tt> in a php document for MySQL support: it needs to be compiled into PHP, the module itself needs to be present in the expected location, <strong>and</strong> php.ini needs to have it enabled.<br />Also note that you cannot have <tt>mysqli</tt> and <tt>mysql</tt> support both enabled at the same time.<br />Please double-check all of these things, restart your webserver after any fixes, and then try again!');
if(!defined('ERROR_SETUP_FILE_MISSING')) define('ERROR_SETUP_FILE_MISSING', 'A file of the installer/ upgrader was not found. Please install Wikka again!');
if(!defined('ERROR_SETUP_HEADER_MISSING')) define('ERROR_SETUP_HEADER_MISSING', 'The file "setup/header.php" was not found. Please install Wikka again!');
if(!defined('ERROR_SETUP_FOOTER_MISSING')) define('ERROR_SETUP_FOOTER_MISSING', 'The file "setup/footer.php" was not found. Please install Wikka again!');
if(!defined('ERROR_HEADER_MISSING')) define('ERROR_HEADER_MISSING', 'A header template could not be found. Please make sure that a file called <code>header.php</code> exists in the templates directory.'); //TODO Make sure this message matches any filename/folder change
if(!defined('ERROR_FOOTER_MISSING')) define('ERROR_FOOTER_MISSING', 'A footer template could not be found. Please make sure that a file called <code>footer.php</code> exists in the templates directory.'); //TODO Make sure this message matches any filename/folder change
if(!defined('ERROR_NO_DB_ACCESS')) define('ERROR_NO_DB_ACCESS', 'The wiki is currently unavailable. <br /><br />Error: Unable to connect to the MySQL database.');
if(!defined('PAGE_GENERATION_TIME')) define('PAGE_GENERATION_TIME', 'Page was generated in %.4f seconds'); // %.4f - generation time in seconds with 4 digits after the dot
if(!defined('WIKI_UPGRADE_NOTICE')) define('WIKI_UPGRADE_NOTICE', 'This site is currently being upgraded. Please try again later.');
/*

NOTE: These defines are the "new" defines ported from trunk to 1.2.
They will eventually need to be reconciled with updates to wikka.php.
For now, I've commented them out and have simply copied over the 1.2
versions.

if(!defined('ERROR_WAKKA_LIBRARY_MISSING')) define('ERROR_WAKKA_LIBRARY_MISSING', 'The necessary file "%s" could not be found. To run Wikka, please make sure the file exists and is placed in the right directory!');	// %s - configured path to core class
if(!defined('ERROR_NO_DB_ACCESS')) define('ERROR_NO_DB_ACCESS', 'Error: Unable to connect to the database.');
if(!defined('ERROR_RETRIEVAL_MYSQL_VERSION')) define('ERROR_RETRIEVAL_MYSQL_VERSION', 'Could not determine MySQL version');
if(!defined('ERROR_WRONG_MYSQL_VERSION')) define('ERROR_WRONG_MYSQL_VERSION', 'Wikka requires MySQL %s or higher!');	// %s - version number
if(!defined('STATUS_WIKI_UPGRADE_NOTICE')) define('STATUS_WIKI_UPGRADE_NOTICE', 'This site is currently being upgraded. Please try again later.');
if(!defined('STATUS_WIKI_UNAVAILABLE')) define('STATUS_WIKI_UNAVAILABLE', 'The wiki is currently unavailable.');
if(!defined('PAGE_GENERATION_TIME')) define('PAGE_GENERATION_TIME', 'Page was generated in %.4f seconds'); // %.4f - page generation time
if(!defined('ERROR_HEADER_MISSING')) define('ERROR_HEADER_MISSING', 'A header template could not be found. Please make sure that a file called <code>header.php</code> exists in the templates directory.'); //TODO Make sure this message matches any filename/folder change
if(!defined('ERROR_FOOTER_MISSING')) define('ERROR_FOOTER_MISSING', 'A footer template could not be found. Please make sure that a file called <code>footer.php</code> exists in the templates directory.'); //TODO Make sure this message matches any filename/folder change

#if(!defined('ERROR_WRONG_PHP_VERSION')) define('ERROR_WRONG_PHP_VERSION', '$_REQUEST[] not found. Wakka requires PHP 4.1.0 or higher!'); //TODO remove referral to PHP internals; refer only to required version
#if(!defined('ERROR_SETUP_HEADER_MISSING')) define('ERROR_SETUP_HEADER_MISSING', 'The file "setup/header.php" was not found. Please install Wikka again!');
#if(!defined('ERROR_SETUP_FOOTER_MISSING')) define('ERROR_SETUP_FOOTER_MISSING', 'The file "setup/footer.php" was not found. Please install Wikka again!');
*/
/**#@-*/

/*  ------------------ TEMPLATE ------------------  */

/**#@+
 * Language constant used by the {@link header.php header} template
 */
// header
if(!defined('GENERIC_DOCTITLE')) define('GENERIC_DOCTITLE', '%1$s: %2$s');	// %1$s - wiki name; %2$s - page title
if(!defined('RSS_REVISIONS_TITLE')) define('RSS_REVISIONS_TITLE', '%1$s: revisions for %2$s');	// %1$s - wiki name; %2$s - current page name
if(!defined('RSS_RECENTCHANGES_TITLE')) define('RSS_RECENTCHANGES_TITLE', '%s: recently edited pages');	// %s - wiki name
if(!defined('YOU_ARE')) define('YOU_ARE', 'You are %s'); // %s - name / ip of the user.
/**#@-*/

/**#@+
 * Language constant used by the {@link footer.php footer} template
 */
// footer
if(!defined('FOOTER_PAGE_EDIT_LINK_DESC')) define('FOOTER_PAGE_EDIT_LINK_DESC', 'Edit page');
if(!defined('PAGE_HISTORY_LINK_TITLE')) define('PAGE_HISTORY_LINK_TITLE', 'Click to view recent edits to this page'); // @@@ TODO 'View recent edits to this page'
if(!defined('PAGE_HISTORY_LINK_DESC')) define('PAGE_HISTORY_LINK_DESC', 'Page History');
if(!defined('PAGE_REVISION_LINK_TITLE')) define('PAGE_REVISION_LINK_TITLE', 'Click to view recent revisions list for this page'); // @@@ TODO 'View recent revisions list for this page'
if(!defined('PAGE_REVISION_XML_LINK_TITLE')) define('PAGE_REVISION_XML_LINK_TITLE', 'Click to view recent revisions list for this page'); // @@@ TODO 'View recent revisions list for this page'
if(!defined('PAGE_ACLS_EDIT_LINK_DESC')) define('PAGE_ACLS_EDIT_LINK_DESC', 'Edit ACLs');
if(!defined('PAGE_ACLS_EDIT_ADMIN_LINK_DESC')) define('PAGE_ACLS_EDIT_ADMIN_LINK_DESC', '('.PAGE_ACLS_EDIT_LINK_DESC.')');
if(!defined('PUBLIC_PAGE')) define('PUBLIC_PAGE', 'Public page');
if(!defined('USER_IS_OWNER')) define('USER_IS_OWNER', 'You own this page.');
if(!defined('TAKE_OWNERSHIP')) define('TAKE_OWNERSHIP', 'Take Ownership');
if(!defined('REFERRERS_LINK_TITLE')) define('REFERRERS_LINK_TITLE', 'Click to view a list of URLs referring to this page'); // @@@ TODO 'View a list of URLs referring to this page'
if(!defined('REFERRERS_LINK_DESC')) define('REFERRERS_LINK_DESC', 'Referrers');
if(!defined('QUERY_LOG')) define('QUERY_LOG', 'Query log:');
if(!defined('SEARCH_LABEL')) define('SEARCH_LABEL', 'Search:');
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
if(!defined('ADMINPAGES_PAGE_TITLE')) define('ADMINPAGES_PAGE_TITLE','Page Administration');
if(!defined('ADMINPAGES_FORM_LEGEND')) define('ADMINPAGES_FORM_LEGEND','Filter view:');
if(!defined('ADMINPAGES_FORM_SEARCH_STRING_LABEL')) define('ADMINPAGES_FORM_SEARCH_STRING_LABEL','Search page:');
if(!defined('ADMINPAGES_FORM_SEARCH_STRING_TITLE')) define('ADMINPAGES_FORM_SEARCH_STRING_TITLE','Enter a search string');
if(!defined('ADMINPAGES_FORM_SEARCH_SUBMIT')) define('ADMINPAGES_FORM_SEARCH_SUBMIT','Submit');
if(!defined('ADMINPAGES_FORM_DATE_RANGE_STRING_LABEL')) define('ADMINPAGES_FORM_DATE_RANGE_STRING_LABEL','Last edit range: Between');
if(!defined('ADMINPAGES_FORM_DATE_RANGE_CONNECTOR_LABEL')) define('ADMINPAGES_FORM_DATE_RANGE_CONNECTOR_LABEL','and');
if(!defined('ADMINPAGES_FORM_PAGER_LABEL_BEFORE')) define('ADMINPAGES_FORM_PAGER_LABEL_BEFORE','Show');
if(!defined('ADMINPAGES_FORM_PAGER_TITLE')) define('ADMINPAGES_FORM_PAGER_TITLE','Select records-per-page limit');
if(!defined('ADMINPAGES_FORM_PAGER_LABEL_AFTER')) define('ADMINPAGES_FORM_PAGER_LABEL_AFTER','records per page');
if(!defined('ADMINPAGES_FORM_PAGER_SUBMIT')) define('ADMINPAGES_FORM_PAGER_SUBMIT','Apply');
if(!defined('ADMINPAGES_FORM_PAGER_LINK')) define('ADMINPAGES_FORM_PAGER_LINK','Show records from %d to %d');
if(!defined('ADMINPAGES_FORM_RESULT_INFO')) define('ADMINPAGES_FORM_RESULT_INFO','Records');
if(!defined('ADMINPAGES_FORM_RESULT_SORTED_BY')) define('ADMINPAGES_FORM_RESULT_SORTED_BY','Sorted by:');
if(!defined('ADMINPAGES_TABLE_HEADING_PAGENAME')) define('ADMINPAGES_TABLE_HEADING_PAGENAME','Page Name');
if(!defined('ADMINPAGES_TABLE_HEADING_PAGENAME_TITLE')) define('ADMINPAGES_TABLE_HEADING_PAGENAME_TITLE','Sort by page name');
if(!defined('ADMINPAGES_TABLE_HEADING_OWNER')) define('ADMINPAGES_TABLE_HEADING_OWNER','Owner');
if(!defined('ADMINPAGES_TABLE_HEADING_OWNER_TITLE')) define('ADMINPAGES_TABLE_HEADING_OWNER_TITLE','Sort by page owner');
if(!defined('ADMINPAGES_TABLE_HEADING_LASTAUTHOR')) define('ADMINPAGES_TABLE_HEADING_LASTAUTHOR','Last Author');
if(!defined('ADMINPAGES_TABLE_HEADING_LASTAUTHOR_TITLE')) define('ADMINPAGES_TABLE_HEADING_LASTAUTHOR_TITLE','Sort by last author');
if(!defined('ADMINPAGES_TABLE_HEADING_LASTEDIT')) define('ADMINPAGES_TABLE_HEADING_LASTEDIT','Last Edit');
if(!defined('ADMINPAGES_TABLE_HEADING_LASTEDIT_TITLE')) define('ADMINPAGES_TABLE_HEADING_LASTEDIT_TITLE','Sort by edit time');
if(!defined('ADMINPAGES_TABLE_SUMMARY')) define('ADMINPAGES_TABLE_SUMMARY','List of pages on this server');
if(!defined('ADMINPAGES_TABLE_HEADING_HITS_TITLE')) define('ADMINPAGES_TABLE_HEADING_HITS_TITLE','Hits');
if(!defined('ADMINPAGES_TABLE_HEADING_REVISIONS_TITLE')) define('ADMINPAGES_TABLE_HEADING_REVISIONS_TITLE','Revisions');
if(!defined('ADMINPAGES_TABLE_HEADING_COMMENTS_TITLE')) define('ADMINPAGES_TABLE_HEADING_COMMENTS_TITLE','Comments');
if(!defined('ADMINPAGES_TABLE_HEADING_BACKLINKS_TITLE')) define('ADMINPAGES_TABLE_HEADING_BACKLINKS_TITLE','Backlinks');
if(!defined('ADMINPAGES_TABLE_HEADING_REFERRERS_TITLE')) define('ADMINPAGES_TABLE_HEADING_REFERRERS_TITLE','Referrers');
if(!defined('ADMINPAGES_TABLE_HEADING_HITS_ALT')) define('ADMINPAGES_TABLE_HEADING_HITS_ALT','Hits');
if(!defined('ADMINPAGES_TABLE_HEADING_REVISIONS_ALT')) define('ADMINPAGES_TABLE_HEADING_REVISIONS_ALT','Revisions');
if(!defined('ADMINPAGES_TABLE_HEADING_COMMENTS_ALT')) define('ADMINPAGES_TABLE_HEADING_COMMENTS_ALT','Comments');
if(!defined('ADMINPAGES_TABLE_HEADING_BACKLINKS_ALT')) define('ADMINPAGES_TABLE_HEADING_BACKLINKS_ALT','Backlinks');
if(!defined('ADMINPAGES_TABLE_HEADING_REFERRERS_ALT')) define('ADMINPAGES_TABLE_HEADING_REFERRERS_ALT','Referrers');
if(!defined('ADMINPAGES_TABLE_HEADING_ACTIONS')) define('ADMINPAGES_TABLE_HEADING_ACTIONS','Actions');
if(!defined('ADMINPAGES_ACTION_EDIT_LINK_TITLE')) define('ADMINPAGES_ACTION_EDIT_LINK_TITLE','Edit %s');
if(!defined('ADMINPAGES_ACTION_DELETE_LINK_TITLE')) define('ADMINPAGES_ACTION_DELETE_LINK_TITLE','Delete %s');
if(!defined('ADMINPAGES_ACTION_CLONE_LINK_TITLE')) define('ADMINPAGES_ACTION_CLONE_LINK_TITLE','Clone %s');
if(!defined('ADMINPAGES_ACTION_RENAME_LINK_TITLE')) define('ADMINPAGES_ACTION_RENAME_LINK_TITLE','Rename %s (DISABLED)');
if(!defined('ADMINPAGES_ACTION_ACL_LINK_TITLE')) define('ADMINPAGES_ACTION_ACL_LINK_TITLE','Change Access Control List for %s');
if(!defined('ADMINPAGES_ACTION_REVERT_LINK_TITLE')) define('ADMINPAGES_ACTION_REVERT_LINK_TITLE','Revert %s to previous version');
if(!defined('ADMINPAGES_ACTION_EDIT_LINK')) define('ADMINPAGES_ACTION_EDIT_LINK','edit');
if(!defined('ADMINPAGES_ACTION_DELETE_LINK')) define('ADMINPAGES_ACTION_DELETE_LINK','delete');
if(!defined('ADMINPAGES_ACTION_CLONE_LINK')) define('ADMINPAGES_ACTION_CLONE_LINK','clone');
if(!defined('ADMINPAGES_ACTION_RENAME_LINK')) define('ADMINPAGES_ACTION_RENAME_LINK','rename');
if(!defined('ADMINPAGES_ACTION_ACL_LINK')) define('ADMINPAGES_ACTION_ACL_LINK','acl');
if(!defined('ADMINPAGES_ACTION_INFO_LINK')) define('ADMINPAGES_ACTION_INFO_LINK','info');
if(!defined('ADMINPAGES_ACTION_REVERT_LINK')) define('ADMINPAGES_ACTION_REVERT_LINK', 'revert');
if(!defined('ADMINPAGES_TAKE_OWNERSHIP_LINK')) define('ADMINPAGES_TAKE_OWNERSHIP_LINK','Take ownership of');
if(!defined('ADMINPAGES_NO_OWNER')) define('ADMINPAGES_NO_OWNER','(Nobody)');
if(!defined('ADMINPAGES_TABLE_CELL_HITS_TITLE')) define('ADMINPAGES_TABLE_CELL_HITS_TITLE','Hits for %s (%d)');
if(!defined('ADMINPAGES_TABLE_CELL_REVISIONS_TITLE')) define('ADMINPAGES_TABLE_CELL_REVISIONS_TITLE','Display revisions for %s (%d)');
if(!defined('ADMINPAGES_TABLE_CELL_COMMENTS_TITLE')) define('ADMINPAGES_TABLE_CELL_COMMENTS_TITLE','Display comments for %s (%d)');
if(!defined('ADMINPAGES_TABLE_CELL_BACKLINKS_TITLE')) define('ADMINPAGES_TABLE_CELL_BACKLINKS_TITLE','Display pages linking to %s (%d)');
if(!defined('ADMINPAGES_TABLE_CELL_REFERRERS_TITLE')) define('ADMINPAGES_TABLE_CELL_REFERRERS_TITLE','Display external sites linking to %s (%d)');
if(!defined('ADMINPAGES_SELECT_RECORD_TITLE')) define('ADMINPAGES_SELECT_RECORD_TITLE','Select %s');
if(!defined('ADMINPAGES_NO_EDIT_NOTE')) define('ADMINPAGES_NO_EDIT_NOTE','(No edit note)');
if(!defined('ADMINPAGES_CHECK_ALL_TITLE')) define('ADMINPAGES_CHECK_ALL_TITLE','Check all records');
if(!defined('ADMINPAGES_CHECK_ALL')) define('ADMINPAGES_CHECK_ALL','Check all');
if(!defined('ADMINPAGES_UNCHECK_ALL_TITLE')) define('ADMINPAGES_UNCHECK_ALL_TITLE','Uncheck all records');
if(!defined('ADMINPAGES_UNCHECK_ALL')) define('ADMINPAGES_UNCHECK_ALL','Uncheck all');
if(!defined('ADMINPAGES_FORM_MASSACTION_LEGEND')) define('ADMINPAGES_FORM_MASSACTION_LEGEND','Mass-action');
if(!defined('ADMINPAGES_FORM_MASSACTION_LABEL')) define('ADMINPAGES_FORM_MASSACTION_LABEL','With selected');
if(!defined('ADMINPAGES_FORM_MASSACTION_SELECT_TITLE')) define('ADMINPAGES_FORM_MASSACTION_SELECT_TITLE','Choose action to apply to selected records (DISABLED)');
if(!defined('ADMINPAGES_FORM_MASSACTION_OPT_DELETE')) define('ADMINPAGES_FORM_MASSACTION_OPT_DELETE','Delete all');
if(!defined('ADMINPAGES_FORM_MASSACTION_OPT_CLONE')) define('ADMINPAGES_FORM_MASSACTION_OPT_CLONE','Clone all');
if(!defined('ADMINPAGES_FORM_MASSACTION_OPT_RENAME')) define('ADMINPAGES_FORM_MASSACTION_OPT_RENAME','Rename all');
if(!defined('ADMINPAGES_FORM_MASSACTION_OPT_ACL')) define('ADMINPAGES_FORM_MASSACTION_OPT_ACL','Change Access Control List');
if(!defined('ADMINPAGES_FORM_MASSACTION_OPT_REVERT')) define('ADMINPAGES_FORM_MASSACTION_OPT_REVERT','Revert to previous page version');
if(!defined('ADMINPAGES_FORM_MASSACTION_REVERT_ERROR')) define('ADMINPAGES_FORM_MASSACTION_REVERT_ERROR','Cannot be reverted');
if(!defined('ADMINPAGES_FORM_MASSACTION_SUBMIT')) define('ADMINPAGES_FORM_MASSACTION_SUBMIT','Submit');
if(!defined('ADMINPAGES_ERROR_NO_MATCHES')) define('ADMINPAGES_ERROR_NO_MATCHES','Sorry, there are no pages matching "%s"');
if(!defined('ADMINPAGES_LABEL_EDIT_NOTE')) define('ADMINPAGES_LABEL_EDIT_NOTE','Please enter a comment, or leave blank for default');
if(!defined('ADMINPAGES_CANCEL_LABEL')) define('ADMINPAGES_CANCEL_LABEL', 'Cancel');
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
if(!defined('ADMINUSERS_PAGE_TITLE')) define('ADMINUSERS_PAGE_TITLE','User Administration');
if(!defined('ADMINUSERS_FORM_LEGEND')) define('ADMINUSERS_FORM_LEGEND','Filter view:');
if(!defined('ADMINUSERS_FORM_SEARCH_STRING_LABEL')) define('ADMINUSERS_FORM_SEARCH_STRING_LABEL','Search user:');
if(!defined('ADMINUSERS_FORM_SEARCH_STRING_TITLE')) define('ADMINUSERS_FORM_SEARCH_STRING_TITLE','Enter a search string');
if(!defined('ADMINUSERS_FORM_SEARCH_SUBMIT')) define('ADMINUSERS_FORM_SEARCH_SUBMIT','Submit');
if(!defined('ADMINUSERS_FORM_PAGER_LABEL_BEFORE')) define('ADMINUSERS_FORM_PAGER_LABEL_BEFORE','Show');
if(!defined('ADMINUSERS_FORM_PAGER_TITLE')) define('ADMINUSERS_FORM_PAGER_TITLE','Select records-per-page limit');
if(!defined('ADMINUSERS_FORM_PAGER_LABEL_AFTER')) define('ADMINUSERS_FORM_PAGER_LABEL_AFTER','records per page');
if(!defined('ADMINUSERS_FORM_PAGER_SUBMIT')) define('ADMINUSERS_FORM_PAGER_SUBMIT','Apply');
if(!defined('ADMINUSERS_FORM_PAGER_LINK')) define('ADMINUSERS_FORM_PAGER_LINK','Show records from %d to %d');
if(!defined('ADMINUSERS_FORM_RESULT_INFO')) define('ADMINUSERS_FORM_RESULT_INFO','Records');
if(!defined('ADMINUSERS_FORM_RESULT_SORTED_BY')) define('ADMINUSERS_FORM_RESULT_SORTED_BY','Sorted by:');
if(!defined('ADMINUSERS_TABLE_HEADING_USERNAME')) define('ADMINUSERS_TABLE_HEADING_USERNAME','User Name');
if(!defined('ADMINUSERS_TABLE_HEADING_USERNAME_TITLE')) define('ADMINUSERS_TABLE_HEADING_USERNAME_TITLE','Sort by user name');
if(!defined('ADMINUSERS_TABLE_HEADING_EMAIL')) define('ADMINUSERS_TABLE_HEADING_EMAIL','Email');
if(!defined('ADMINUSERS_TABLE_HEADING_EMAIL_TITLE')) define('ADMINUSERS_TABLE_HEADING_EMAIL_TITLE','Sort by email');
if(!defined('ADMINUSERS_TABLE_HEADING_SIGNUPTIME')) define('ADMINUSERS_TABLE_HEADING_SIGNUPTIME','Signup Time');
if(!defined('ADMINUSERS_TABLE_HEADING_SIGNUPTIME_TITLE')) define('ADMINUSERS_TABLE_HEADING_SIGNUPTIME_TITLE','Sort by signup time');
if(!defined('ADMINUSERS_TABLE_HEADING_SIGNUPIP')) define('ADMINUSERS_TABLE_HEADING_SIGNUPIP','Signup IP');
if(!defined('ADMINUSERS_TABLE_HEADING_SIGNUPIP_TITLE')) define('ADMINUSERS_TABLE_HEADING_SIGNUPIP_TITLE','Sort by signup IP');
if(!defined('ADMINUSERS_TABLE_SUMMARY')) define('ADMINUSERS_TABLE_SUMMARY','List of users registered on this server');
if(!defined('ADMINUSERS_TABLE_HEADING_OWNED_TITLE')) define('ADMINUSERS_TABLE_HEADING_OWNED_TITLE','Owned Pages');
if(!defined('ADMINUSERS_TABLE_HEADING_EDITS_TITLE')) define('ADMINUSERS_TABLE_HEADING_EDITS_TITLE','Edits');
if(!defined('ADMINUSERS_TABLE_HEADING_COMMENTS_TITLE')) define('ADMINUSERS_TABLE_HEADING_COMMENTS_TITLE','Comments');
if(!defined('ADMINUSERS_ACTION_DELETE_LINK_TITLE')) define('ADMINUSERS_ACTION_DELETE_LINK_TITLE','Remove user %s');
if(!defined('ADMINUSERS_ACTION_DELETE_LINK')) define('ADMINUSERS_ACTION_DELETE_LINK','delete');
if(!defined('ADMINUSERS_TABLE_CELL_OWNED_TITLE')) define('ADMINUSERS_TABLE_CELL_OWNED_TITLE','Display pages owned by %s (%d)');
if(!defined('ADMINUSERS_TABLE_CELL_EDITS_TITLE')) define('ADMINUSERS_TABLE_CELL_EDITS_TITLE','Display page edits by %s (%d)');
if(!defined('ADMINUSERS_TABLE_CELL_COMMENTS_TITLE')) define('ADMINUSERS_TABLE_CELL_COMMENTS_TITLE','Display comments by %s (%d)');
if(!defined('ADMINUSERS_SELECT_RECORD_TITLE')) define('ADMINUSERS_SELECT_RECORD_TITLE','Select %s');
if(!defined('ADMINUSERS_SELECT_ALL_TITLE')) define('ADMINUSERS_SELECT_ALL_TITLE','Select all records');
if(!defined('ADMINUSERS_SELECT_ALL')) define('ADMINUSERS_SELECT_ALL','Select all');
if(!defined('ADMINUSERS_DESELECT_ALL_TITLE')) define('ADMINUSERS_DESELECT_ALL_TITLE','Deselect all records');
if(!defined('ADMINUSERS_DESELECT_ALL')) define('ADMINUSERS_DESELECT_ALL','Deselect all');
if(!defined('ADMINUSERS_FORM_MASSACTION_LEGEND')) define('ADMINUSERS_FORM_MASSACTION_LEGEND','Mass-action');
if(!defined('ADMINUSERS_FORM_MASSACTION_LABEL')) define('ADMINUSERS_FORM_MASSACTION_LABEL','With selected');
if(!defined('ADMINUSERS_FORM_MASSACTION_SELECT_TITLE')) define('ADMINUSERS_FORM_MASSACTION_SELECT_TITLE','Choose an action to apply to the selected records');
if(!defined('ADMINUSERS_FORM_MASSACTION_OPT_DELETE')) define('ADMINUSERS_FORM_MASSACTION_OPT_DELETE','Delete selected');
if(!defined('ADMINUSERS_FORM_MASSACTION_DELETE_ERROR')) define('ADMINUSERS_FORM_MASSACTION_DELETE_ERROR', 'Cannot delete admins');
if(!defined('ADMINUSERS_FORM_MASSACTION_SUBMIT')) define('ADMINUSERS_FORM_MASSACTION_SUBMIT','Submit');
if(!defined('ADMINUSERS_ERROR_NO_MATCHES')) define('ADMINUSERS_ERROR_NO_MATCHES','Sorry, there are no users matching "%s"');
if(!defined('ADMINUSERS_DELETE_USERS_HEADING')) define('ADMINUSERS_DELETE_USERS_HEADING', 'Delete these users?');
if(!defined('ADMINUSERS_DELETE_USERS_BUTTON')) define('ADMINUSERS_DELETE_USERS_BUTTON', 'Delete Users');
if(!defined('ADMINUSERS_CANCEL_BUTTON')) define('ADMINUSERS_CANCEL_BUTTON', 'Cancel');
if(!defined('ADMINUSERS_USERDELETE_SUCCESS')) define('ADMINUSERS_USERDELETE_SUCCESS', 'User has been sucessfully deleted'); //
if(!defined('ADMINUSERS_USERDELETE_FAILURE')) define('ADMINUSERS_USERDELETE_FAILURE', 'Sorry, could not delete user. Please check your admin settings'); //
/**#@-*/

/**#@+
 * Language constant used by the {@link calendar.php calendar} action
 */
// calendar
if(!defined('FMT_SUMMARY')) define('FMT_SUMMARY', 'Calendar for %s');	// %s - ???@@@
if(!defined('TODAY')) define('TODAY', 'today');
if(!defined('MIN_DATETIME')) define('MIN_DATETIME', strtotime('1970-01-01 00:00:00 GMT')); # earliest timestamp PHP can handle (Windows and some others - to be safe)
if(!defined('MAX_DATETIME')) define('MAX_DATETIME', strtotime('2038-01-19 03:04:07 GMT')); # latest timestamp PHP can handle
if(!defined('MIN_YEAR')) define('MIN_YEAR', date('Y',MIN_DATETIME));
if(!defined('MAX_YEAR')) define('MAX_YEAR', date('Y',MAX_DATETIME)-1); # don't include partial January 2038
if(!defined('CUR_YEAR')) define('CUR_YEAR', date('Y',mktime()));
if(!defined('CUR_MONTH')) define('CUR_MONTH', date('n',mktime()));
if(!defined('LOC_MON_YEAR')) define('LOC_MON_YEAR', "%B %Y"); # i18n
/**#@-*/

/**#@+
 * Language constant used by the {@link category.php category} action
 */
// category
if(!defined('ERROR_NO_PAGES')) define('ERROR_NO_PAGES', 'Sorry, No items found for %s');	// %s - ???@@@
if(!defined('PAGES_IN_CATEGORY')) define('PAGES_IN_CATEGORY', 'The following %1$d page(s) belong to %2$s'); // %1$d number found; %2$s category
/**#@-*/

/**#@+
 * Language constant used by the {@link checkversion.php checkversion} action
 */
if(!defined('CHECKVERSION_HOST')) define('CHECKVERSION_HOST', 'wikkawiki.org');
if(!defined('CHECKVERSION_RELEASE_FILE')) define('CHECKVERSION_RELEASE_FILE', '/downloads/latest_wikka_version.txt');
if(!defined('CHECKVERSION_DOWNLOAD_URL')) define('CHECKVERSION_DOWNLOAD_URL', 'http://docs.wikkawiki.org/WhatsNew');
if(!defined('CHECKVERSION_CONNECTION_TIMEOUT')) define('CHECKVERSION_CONNECTION_TIMEOUT', 5);
if(!defined('DEBUG_TIME_ELAPSED')) define('DEBUG_TIME_ELAPSED', '[elapsed time: %d]');
if(!defined('DEBUG_PHP_VERSION_UNSUPPORTED')) define('DEBUG_PHP_VERSION_UNSUPPORTED', '[%s PHP %s does not support this feature]');
if(!defined('DEBUG_ALLOW_FURL_DISABLED')) define('DEBUG_ALLOW_FURL_DISABLED', '[allow_url_fopen disabled]');
if(!defined('DEBUG_CANNOT_RESOLVE_HOSTNAME')) define('DEBUG_CANNOT_RESOLVE_HOSTNAME', '[Cannot resolve %s]');
if(!defined('DEBUG_CANNOT_CONNECT')) define('DEBUG_CANNOT_CONNECT', '[Cannot initiate socket connection]');
if(!defined('DEBUG_NEW_VERSION_AVAILABLE')) define('DEBUG_NEW_VERSION_AVAILABLE', '[%s from host %s]');
if(!defined('CHECKVERSION_CANNOT_CONNECT')) define('CHECKVERSION_CANNOT_CONNECT', '<div title="Cannot initiate network connection" style="clear: both; text-align: center; float: left; width: 300px; border: 1px solid %s; background-color: %s; color: %s; margin: 10px 0">'."\n"
	.'<div style="padding: 0 3px 0 3px; background-color: %s; font-size: 85%; font-weight: bold">CHECKVERSION FAILED</div>'."\n"
	.'<div style="padding: 0 3px 2px 3px; font-size: 85%; line-height: 150%; border-top: 1px solid %s;">'."\n"
	.'The network connection with the WikkaWiki server could not be established. To prevent delays in loading this page, please set enable_version_check to 0 in your wikka.config.php file.'."\n"
	.'</div>'."\n"
	.'</div>'."\n"
	.'<div class="clear"></div>'."\n");
if(!defined('CHECKVERSION_NEW_VERSION_AVAILABLE')) define('CHECKVERSION_NEW_VERSION_AVAILABLE', '<div title="A new version of WikkaWiki is available. Please upgrade!" style="clear: both; text-align: center; float: left; width: 300px; border: 1px solid %s; background-color: %s; color: %s; margin: 10px 0">'."\n"
	.'<div style="padding: 0 3px 0 3px; background-color: %s; font-size: 85%; font-weight: bold">UPGRADE NOTE</div>'."\n"
	.'<div style="padding: 0 3px 2px 3px; font-size: 85%; line-height: 150%; border-top: 1px solid %s;">'."\n"
	.'<strong>WikkaWiki %s</strong> is available for <a href="%s">download</a>!'."\n"
	.'</div>'."\n"
	.'</div>'."\n"
	.'<div class="clear"></div>'."\n");
/**#@-*/

/**#@+
 * Language constant used by the {@link clonelink.php clonelink} action
 */
if(!defined('CLONELINK_TEXT')) define('CLONELINK_TEXT', '[Clone]');
if(!defined('CLONELINK_TITLE')) define('CLONELINK_TITLE', 'Duplicate this page');
/**#@-*/

/**#@+
 * Language constant used by the {@link color.php color} action
 */
// color
if(!defined('ERROR_NO_TEXT_GIVEN')) define('ERROR_NO_TEXT_GIVEN', 'There is no text to highlight!');
if(!defined('ERROR_NO_COLOR_SPECIFIED')) define('ERROR_NO_COLOR_SPECIFIED', 'Sorry, but you did not specify a color for highlighting!');
if(!defined('PATTERN_VALID_HEX_COLOR')) define('PATTERN_VALID_HEX_COLOR', '#(?>[\da-f]{3}){1,2}');
if(!defined('PATTERN_VALID_RGB_COLOR')) define('PATTERN_VALID_RGB_COLOR', 'rgb\(\s*\d+((?>\.\d*)?%)?\s*(?>,\s*\d+(?(1)(\.\d*)?%)\s*){2}\)');
/**#@-*/

/**#@+
 * Language constant used by the {@link contact.php contact} action
 */
// contact
if(!defined('CONTACTLINK_TITLE')) define('CONTACTLINK_TITLE', 'Send us your feedback');
if(!defined('CONTACTLINK_TEXT')) define('CONTACTLINK_TEXT', 'Contact');
/**#@-*/

/**#@+
 * Language constant used by the {@link countowned.php countowned} action
 */
// countowned
if(!defined('DISPLAY_MYPAGES_LINK_TITLE')) define('DISPLAY_MYPAGES_LINK_TITLE', 'Display a list of the pages you currently own');
/**#@-*/

/**#@+
 * Language constant used by the {@link countpages.php countpages} action
 */
// countpages
if(!defined('INDEX_LINK_TITLE')) define('INDEX_LINK_TITLE', 'Display an alphabetical page index');
/**#@-*/

/**#@+
 * Language constant used by the {@link dbinfo.php dbinfo} action
 */
// dbinfo
if(!defined('HD_DBINFO')) define('HD_DBINFO','Database Information');
if(!defined('HD_DBINFO_DB')) define('HD_DBINFO_DB','Database');
if(!defined('HD_DBINFO_TABLES')) define('HD_DBINFO_TABLES','Tables');
if(!defined('HD_DB_CREATE_DDL')) define('HD_DB_CREATE_DDL','DDL to create database %s:');				# %s will hold database name
if(!defined('HD_TABLE_CREATE_DDL')) define('HD_TABLE_CREATE_DDL','DDL to create table %s:');				# %s will hold table name
if(!defined('TXT_INFO_1')) define('TXT_INFO_1','This utility provides some information about the database(s) and tables in your system.');
if(!defined('TXT_INFO_2')) define('TXT_INFO_2',' Depending on permissions for the Wikka database user, not all databases or tables may be visible.');
if(!defined('TXT_INFO_3')) define('TXT_INFO_3',' Where creation DDL is given, this reflects everything that would be needed to exactly recreate the same database and table definitions,');
if(!defined('TXT_INFO_4')) define('TXT_INFO_4',' including defaults that may not have been specified explicitly.');
if(!defined('FORM_SELDB_LEGEND')) define('FORM_SELDB_LEGEND','Databases');
if(!defined('FORM_SELTABLE_LEGEND')) define('FORM_SELTABLE_LEGEND','Tables');
if(!defined('FORM_SELDB_OPT_LABEL')) define('FORM_SELDB_OPT_LABEL','Select a database:');
if(!defined('FORM_SELTABLE_OPT_LABEL')) define('FORM_SELTABLE_OPT_LABEL','Select a table:');
if(!defined('FORM_SUBMIT_SELDB')) define('FORM_SUBMIT_SELDB','Select');
if(!defined('FORM_SUBMIT_SELTABLE')) define('FORM_SUBMIT_SELTABLE','Select');
if(!defined('MSG_ONLY_ADMIN')) define('MSG_ONLY_ADMIN','Sorry, only administrators can view database information.');
if(!defined('MSG_SINGLE_DB')) define('MSG_SINGLE_DB','Information for the <tt>%s</tt> database.');			# %s will hold database name
if(!defined('MSG_NO_TABLES')) define('MSG_NO_TABLES','No tables found in the <tt>%s</tt> database. Your MySQL user may not have sufficient privileges to access this database.');		# %s will hold database name
if(!defined('MSG_NO_DB_DDL')) define('MSG_NO_DB_DDL','Creation DDL for <tt>%s</tt> could not be retrieved.');	# %s will hold database name
if(!defined('MSG_NO_TABLE_DDL')) define('MSG_NO_TABLE_DDL','Creation DDL for <tt>%s</tt> could not be retrieved.');# %s will hold table name
/**#@-*/

/**#@+
 * Language constant used by the {@link deletelink.php deletelink} action
 */
if(!defined('DELETELINK_TEXT')) define('DELETELINK_TEXT', '[Delete]');
if(!defined('DELETELINK_TITLE')) define('DELETELINK_TITLE', 'Delete this page (requires confirmation)');
/**#@-*/

/**#@+
 * Language constant used by the {@link editlink.php editlink} action
 */
if(!defined('EDITLINK_TEXT')) define('EDITLINK_TEXT', '[Edit]');
if(!defined('SHOWLINK_TEXT')) define('SHOWLINK_TEXT', '[Show]');
if(!defined('SHOWCODELINK_TEXT')) define('SHOWCODELINK_TEXT', '[Source]');
if(!defined('EDITLINK_TITLE')) define('EDITLINK_TITLE', 'Click to edit this page');
if(!defined('SHOWLINK_TITLE')) define('SHOWLINK_TITLE', 'Displayed the formatted version of this page');
if(!defined('SHOWCODELINK_TITLE')) define('SHOWCODELINK_TITLE', 'Display the markup for this page');
/**#@-*/

/**#@+
 * Language constant used by the {@link emailpassword.php emailpassword} action
 */
// emailpassword
if(!defined('PW_FORGOTTEN_HEADING')) define('PW_FORGOTTEN_HEADING', 'Password reminder');
if(!defined('PW_CHK_SENT')) define('PW_CHK_SENT', 'A password reminder has been sent to %s\'s registered email address.'); // %s - username
if(!defined('PW_FORGOTTEN_MAIL')) define('PW_FORGOTTEN_MAIL', 'Hello, %1$s!

Someone requested that we send to this email address a password reminder to 
login at %2$s. If you did not request this reminder, disregard this 
email, no action is necessary. Your password will stay the same.

Your wikiname: %1$s
Password reminder: %3$s
URL: %4$s

Do not forget to change the password immediately after logging in.');
// %1$s - username; %2$s - wiki name; %3$s - md5 sum of pw; %4$s - login url of the wiki
if(!defined('PW_FORGOTTEN_MAIL_REF')) define('PW_FORGOTTEN_MAIL_REF', 'Password reminder for %s'); // %s - wiki name
if(!defined('PW_FORM_TEXT')) define('PW_FORM_TEXT', 'Enter your WikiName and a password reminder will be sent to your registered email address.');
if(!defined('PW_FORM_FIELDSET_LEGEND')) define('PW_FORM_FIELDSET_LEGEND', 'Your WikiName:');
if(!defined('ERROR_UNKNOWN_USER')) define('ERROR_UNKNOWN_USER', 'You have entered a non-existent user!');
#if(!defined('ERROR_MAIL_NOT_SENT')) define('ERROR_MAIL_NOT_SENT', 'An error occurred while trying to send the password. Outgoing mail might be disabled. Please contact your server administrator.');
if(!defined('ERROR_MAIL_NOT_SENT')) define('ERROR_MAIL_NOT_SENT', 'An error occurred while trying to send the password. Outgoing mail might be disabled. Please try to contact your wiki administrator by posting a page comment.');
if(!defined('BUTTON_SEND_PW')) define('BUTTON_SEND_PW', 'Send reminder');
if(!defined('USERSETTINGS_REF')) define('USERSETTINGS_REF', 'Return to the %s page.'); // %s - UserSettings link
if(!defined('ERROR_EMPTY_USER')) define('ERROR_EMPTY_USER', 'Please fill in your username!');
if(!defined('BUTTON_SEND_PW_LABEL')) define('BUTTON_SEND_PW_LABEL', 'Send reminder');
if(!defined('USERSETTINGS_LINK')) define('USERSETTINGS_LINK', 'Return to the [[UserSettings login]] screen.');
/**#@-*/

/**#@+
 * Language constant used by the {@link feedback.php feedback} action
 */
// feedback
if(!defined('FILL_FORM')) define('FILL_FORM', '<p>Fill in the form below to send us your comments:</p>'."\n");
if(!defined('FEEDBACK_NAME_LABEL')) define('FEEDBACK_NAME_LABEL', 'Name: ');
if(!defined('FEEDBACK_EMAIL_LABEL')) define('FEEDBACK_EMAIL_LABEL', 'Email: ');
if(!defined('FEEDBACK_COMMENTS_LABEL')) define('FEEDBACK_COMMENTS_LABEL', 'Comments:');
if(!defined('FEEDBACK_SEND_BUTTON')) define('FEEDBACK_SEND_BUTTON', 'Send');
if(!defined('ERROR_EMPTY_NAME')) define('ERROR_EMPTY_NAME', 'Please enter your name');
if(!defined('ERROR_INVALID_EMAIL')) define('ERROR_INVALID_EMAIL', 'Please enter a valid email address');
if(!defined('ERROR_EMPTY_MESSAGE')) define('ERROR_EMPTY_MESSAGE', 'Please enter some text');
if(!defined('FEEDBACK_SUBJECT')) define('FEEDBACK_SUBJECT', 'Feedback from %s'); // %s - name of the wiki
if(!defined('SUCCESS_FEEDBACK_SENT')) define('SUCCESS_FEEDBACK_SENT', 'Thanks for your interest! Your feedback has been sent to %1$s ---'
	.'Return to the [[%2$s main page]]');
// currently unused in feedback action:
if(!defined('ERROR_FEEDBACK_MAIL_NOT_SENT')) define('ERROR_FEEDBACK_MAIL_NOT_SENT', 'Sorry, An error occurred while trying to send your email. Outgoing mail might be disabled. Please try another method to contact %s, for instance by posting a page comment'); // %s - name of the recipient
if(!defined('FEEDBACK_FORM_LEGEND')) define('FEEDBACK_FORM_LEGEND', 'Contact %s'); //%s - wikiname of the recipient
/**#@-*/

/**#@+
 * Language constant used by the {@link files.php files action} and {@link handlers/files.xml/files.xml.php files.xml handler}
 */
// files
if(!defined('ERROR_UPLOAD_DIRECTORY_NOT_WRITABLE')) define('ERROR_UPLOAD_DIRECTORY_NOT_WRITABLE', 'Please make sure that the server has write access to a folder named %s.'); // %s Upload folder ref #89
if(!defined('ERROR_UPLOAD_DIRECTORY_NOT_READABLE')) define('ERROR_UPLOAD_DIRECTORY_NOT_READABLE', 'Please make sure that the server has read access to a folder named %s.'); // %s Upload folder ref #89
if(!defined('ERROR_NONEXISTENT_FILE')) define('ERROR_NONEXISTENT_FILE', 'Sorry, a file named %s does not exist.'); // %s - file name ref
if(!defined('ERROR_FILE_UPLOAD_INCOMPLETE')) define('ERROR_FILE_UPLOAD_INCOMPLETE', 'File upload incomplete! Please try again.');
if(!defined('ERROR_UPLOADING_FILE')) define('ERROR_UPLOADING_FILE', 'There was an error uploading your file');
if(!defined('ERROR_FILE_ALREADY_EXISTS')) define('ERROR_FILE_ALREADY_EXISTS', 'Sorry, a file named %s already exists.'); // %s - file name ref
if(!defined('ERROR_EXTENSION_NOT_ALLOWED')) define('ERROR_EXTENSION_NOT_ALLOWED', 'Sorry, files with this extension are not allowed.');
if(!defined('ERROR_FILETYPE_NOT_ALLOWED')) define('ERROR_FILETYPE_NOT_ALLOWED', 'Sorry, files of this type are not allowed.');
if(!defined('ERROR_FILE_NOT_DELETED')) define('ERROR_FILE_NOT_DELETED', 'Sorry, the file could not be deleted!');
if(!defined('ERROR_FILE_TOO_BIG')) define('ERROR_FILE_TOO_BIG', 'Attempted file upload was too big. Maximum allowed size is %s.'); // %s - allowed filesize
if(!defined('ERROR_NO_FILE_SELECTED')) define('ERROR_NO_FILE_SELECTED', 'No file selected.');
if(!defined('ERROR_FILE_UPLOAD_IMPOSSIBLE')) define('ERROR_FILE_UPLOAD_IMPOSSIBLE', 'File upload impossible due to misconfigured server.');
if(!defined('SUCCESS_FILE_UPLOADED')) define('SUCCESS_FILE_UPLOADED', 'File was successfully uploaded.');
if(!defined('FILE_TABLE_CAPTION')) define('FILE_TABLE_CAPTION', 'Attachments');
if(!defined('FILE_TABLE_HEADER_NAME')) define('FILE_TABLE_HEADER_NAME', 'File');
if(!defined('FILE_TABLE_HEADER_SIZE')) define('FILE_TABLE_HEADER_SIZE', 'Size');
if(!defined('FILE_TABLE_HEADER_DATE')) define('FILE_TABLE_HEADER_DATE', 'Last modified');
if(!defined('FILE_UPLOAD_FORM_LEGEND')) define('FILE_UPLOAD_FORM_LEGEND', 'Add new attachment:');
if(!defined('FILE_UPLOAD_FORM_LABEL')) define('FILE_UPLOAD_FORM_LABEL', 'File:');
if(!defined('FILE_UPLOAD_FORM_BUTTON')) define('FILE_UPLOAD_FORM_BUTTON', 'Upload');
if(!defined('DOWNLOAD_LINK_TITLE')) define('DOWNLOAD_LINK_TITLE', 'Download %s'); // %s - file name
if(!defined('DELETE_LINK_TITLE')) define('DELETE_LINK_TITLE', 'Remove %s'); // %s - file name
if(!defined('NO_ATTACHMENTS')) define('NO_ATTACHMENTS', 'This page contains no attachment.');
if(!defined('FILES_DELETE_FILE')) define('FILES_DELETE_FILE', 'Delete this file?');
if(!defined('FILES_DELETE_FILE_BUTTON')) define('FILES_DELETE_FILE_BUTTON', 'Delete File');
if(!defined('FILES_CANCEL_BUTTON')) define('FILES_CANCEL_BUTTON', 'Cancel');
if(!defined('FILE_DELETED')) define('FILE_DELETED', 'File deleted');
if(!defined('ERROR_NO_FILE_UPLOADS')) define('ERROR_NO_FILE_UPLOADS', 'File uploads are disallowed on this server');
if(!defined('ERROR_NO_FILE_UPLOADED')) define('ERROR_NO_FILE_UPLOADED', 'No file uploaded');
if(!defined('ERROR_DURING_FILE_UPLOAD')) define('ERROR_DURING_FILE_UPLOAD', 'There was an error uploading your file.  Please try again.');
if(!defined('ERROR_MAX_FILESIZE_EXCEEDED')) define('ERROR_MAX_FILESIZE_EXCEEDED', 'Attempted file upload was too big.  Maximum allowed size is %d MB.');
if(!defined('ERROR_FILE_EXISTS')) define('ERROR_FILE_EXISTS', 'There is already a file named <tt>%s</tt>. Please rename before uploading or delete the existing file first.');
/**#@-*/

/**#@+
 * Language constant used by the {@link geshiversion.php geshiversion} action
 */
if(!defined('NOT_INSTALLED')) define('NOT_INSTALLED', 'not installed');
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
if(!defined('HIGHSCORES_LABEL_EDITS')) define('HIGHSCORES_LABEL_EDITS', 'edits');
if(!defined('HIGHSCORES_LABEL_COMMENTS')) define('HIGHSCORES_LABEL_COMMENTS', 'comments');
if(!defined('HIGHSCORES_LABEL_PAGES')) define('HIGHSCORES_LABEL_PAGES', 'pages owned');
if(!defined('HIGHSCORES_CAPTION')) define('HIGHSCORES_CAPTION', 'Top %1$s contributor(s) by number of %2$s');
if(!defined('HIGHSCORES_HEADER_RANK')) define('HIGHSCORES_HEADER_RANK', 'rank');
if(!defined('HIGHSCORES_HEADER_USER')) define('HIGHSCORES_HEADER_USER', 'user');
if(!defined('HIGHSCORES_HEADER_PERCENTAGE')) define('HIGHSCORES_HEADER_PERCENTAGE', 'percentage');
if(!defined('HIGHSCORES_DISPLAY_TOP')) define('HIGHSCORES_DISPLAY_TOP', 10); //limit output to top n users
if(!defined('HIGHSCORES_DEFAULT_STYLE')) define('HIGHSCORES_DEFAULT_STYLE', 'complex'); //set default layout style
if(!defined('HIGHSCORES_DEFAULT_RANK')) define('HIGHSCORES_DEFAULT_RANK', 'pages'); //set default layout style
/**#@-*/

/**#@+
 * Language constants used by the {@link historylink.php historylink} action
 */
if(!defined('HISTORYLINK_TEXT')) define('HISTORYLINK_TEXT', '[History]');
if(!defined('HISTORYLINK_TITLE')) define('HISTORYLINK_TITLE', 'Click to view recent edits to this page');
/**#@-*/

/**#@+
 * Language constants used by the {@link include.php include} action
 */
// include
if(!defined('ERROR_CIRCULAR_REFERENCE')) define('ERROR_CIRCULAR_REFERENCE', 'Circular reference detected!');
if(!defined('ERROR_TARGET_ACL')) define('ERROR_TARGET_ACL', "You aren't allowed to read included page <tt>%s</tt>");

/**#@-*/

/**#@+
 * Language constant used by the {@link lastedit.php lastedit} action
 */
// lastedit
if(!defined('LASTEDIT_DESC')) define('LASTEDIT_DESC', 'Last edited by %s'); // %s user name
if(!defined('LASTEDIT_DIFF_LINK_TITLE')) define('LASTEDIT_DIFF_LINK_TITLE', 'Show differences from last revision');
if(!defined('DEFAULT_SHOW')) define('DEFAULT_SHOW', '3');
if(!defined('DATE_FORMAT')) define('DATE_FORMAT', 'D, d M Y'); #TODO make this system-configurable
if(!defined('TIME_FORMAT')) define('TIME_FORMAT', 'H:i T'); #TODO make this system-configurable
if(!defined('LASTEDIT_BOX')) define('LASTEDIT_BOX', 'lastedit');
if(!defined('LASTEDIT_NOTES')) define('LASTEDIT_NOTES', 'lastedit_notes');
if(!defined('ANONYMOUS_USER')) define('ANONYMOUS_USER', 'anonymous');
if(!defined('LASTEDIT_MESSAGE')) define('LASTEDIT_MESSAGE', 'Last edited by %s');
if(!defined('DIFF_LINK_TITLE')) define('DIFF_LINK_TITLE', 'Show differences from last revision');
/**#@-*/

/**#@+
 * Language constant used by the {@link lastusers.php lastusers} action
 */
// lastusers
if(!defined('LASTUSERS_CAPTION')) define('LASTUSERS_CAPTION', 'Recently registered users');
if(!defined('SIGNUP_DATE_TIME')) define('SIGNUP_DATE_TIME', 'Signup Date/Time');
if(!defined('NAME_TH')) define('NAME_TH', 'Username');
if(!defined('OWNED_PAGES_TH')) define('OWNED_PAGES_TH', 'Owned pages');
if(!defined('SIGNUP_DATE_TIME_TH')) define('SIGNUP_DATE_TIME_TH', 'Signup date/time');
if(!defined('LASTUSERS_DEFAULT_STYLE')) define('LASTUSERS_DEFAULT_STYLE', 'complex'); # consistent parameter naming with HighScores action
if(!defined('LASTUSERS_MAX_USERS_DISPLAY')) define('LASTUSERS_MAX_USERS_DISPLAY', 10);
/**#@-*/

/**#@+
 * Language constant used by the {@link mindmap.php mindmap} action
 */
// mindmap
if(!defined('MM_JRE_INSTALL_REQ')) define('MM_JRE_INSTALL_REQ', 'Please install a %s on your computer.'); // %s - JRE install link
if(!defined('MM_DOWNLOAD_LINK_DESC')) define('MM_DOWNLOAD_LINK_DESC', 'Download this mind map');
if(!defined('MM_EDIT')) define('MM_EDIT', 'Use %s to edit it'); // %s - link to freemind project
if(!defined('MM_FULLSCREEN_LINK_DESC')) define('MM_FULLSCREEN_LINK_DESC', 'Open fullscreen');
if(!defined('ERROR_INVALID_MM_SYNTAX')) define('ERROR_INVALID_MM_SYNTAX', 'Error: Invalid MindMap action syntax.');
if(!defined('PROPER_USAGE_MM_SYNTAX')) define('PROPER_USAGE_MM_SYNTAX', 'Proper usage: %1$s or %2$s'); // %1$s - syntax sample 1; %2$s - syntax sample 2
if(!defined('FREEMIND_PROJECT_URL')) define('FREEMIND_PROJECT_URL', 'http://freemind.sourceforge.net/');
/**#@-*/

/**#@+
 * Language constant used by the {@link mychanges.php mychanges} action
 */
// mychanges
if(!defined('NO_PAGES_EDITED')) define('NO_PAGES_EDITED', 'You have not edited any pages yet.');
if(!defined('MYCHANGES_ALPHA_LIST')) define('MYCHANGES_ALPHA_LIST', "This is a list of pages edited by %s, along with the time of the last change.");
if(!defined('MYCHANGES_DATE_LIST')) define('MYCHANGES_DATE_LIST', "This is a list of pages edited by %s, ordered by the time of the last change.");
if(!defined('ORDER_DATE_LINK_DESC')) define('ORDER_DATE_LINK_DESC', 'order by date');
if(!defined('ORDER_ALPHA_LINK_DESC')) define('ORDER_ALPHA_LINK_DESC', 'order alphabetically');
if(!defined('MYCHANGES_NOT_LOGGED_IN')) define('MYCHANGES_NOT_LOGGED_IN', "You're not logged in, thus the list of pages you've edited couldn't be retrieved.");
/**#@-*/

/**#@+
 * Language constant used by the {@link mypages.php mypages} action
 */
// mypages
if(!defined('MYPAGES_CAPTION')) define('MYPAGES_CAPTION', 'This is the list of pages owned by %s');
if(!defined('MYPAGES_NONE_OWNED')) define('MYPAGES_NONE_OWNED', '%s doesn\'t own any pages.');
if(!defined('MYPAGES_NOT_LOGGED_IN')) define('MYPAGES_NOT_LOGGED_IN', 'You\'re not logged in, thus the list of your pages couldn\'t be retrieved.');
/**#@-*/

/**#@+
 * Language constant used by the {@link newpage.php newpage} action
 */
// newpage
if(!defined('NEWPAGE_CREATE_LEGEND')) define('NEWPAGE_CREATE_LEGEND', 'Create a new page');
if(!defined('NEWPAGE_CREATE_BUTTON')) define('NEWPAGE_CREATE_BUTTON', 'Create');
/**#@-*/

/**#@+
 * Language constant used by the {@link orphanedpages.php orphanedpages} action
 */
// orphanedpages
if(!defined('NO_ORPHANED_PAGES')) define('NO_ORPHANED_PAGES', 'No orphaned pages. Good!');

/**#@+
 * Language constant used by the {@link ownedpages.php ownedpages} action
 */
// ownedpages
if(!defined('OWNEDPAGES_COUNTS')) define('OWNEDPAGES_COUNTS', 'You own %1$s pages out of the %2$s pages on this Wiki.'); // %1$s - number of pages owned; %2$s - total number of pages
if(!defined('OWNEDPAGES_PERCENTAGE')) define('OWNEDPAGES_PERCENTAGE', 'That means you own %s of the total.'); // %s percentage of pages owned
/**#@-*/

/**#@+
 * Language constant used by the {@link ownerlink.php ownerlink} action
 */
if(!defined('OWNERLINK_PUBLIC_PAGE')) define('OWNERLINK_PUBLIC_PAGE', 'Public page');
if(!defined('OWNERLINK_NOBODY')) define('OWNERLINK_NOBODY', 'Nobody');
if(!defined('OWNERLINK_OWNER')) define('OWNERLINK_OWNER', 'Owner:');
if(!defined('OWNERLINK_SELF')) define('OWNERLINK_SELF', 'You own this page');
if(!defined('EDITACLLINK_TEXT')) define('EDITACLLINK_TEXT', '[Edit ACLs]');
if(!defined('EDITACLLINK_TITLE')) define('EDITACLLINK_TITLE', 'Change the Access Control List for this page');
if(!defined('CLAIMLINK_TEXT')) define('CLAIMLINK_TEXT', '[Take Ownership]');
if(!defined('CLAIMLINK_TITLE')) define('CLAIMLINK_TITLE', 'Click to become the owner of this page');
/**#@-*/

/**#@+
 * Language constant used by the {@link pageindex.php pageindex} action
 */
// pageindex
if(!defined('PAGEINDEX_HEADING')) define('PAGEINDEX_HEADING', 'Page Index');
if(!defined('PAGEINDEX_CAPTION')) define('PAGEINDEX_CAPTION', 'This is an alphabetical list of pages you can read on this server.');
if(!defined('PAGEINDEX_OWNED_PAGES_CAPTION')) define('PAGEINDEX_OWNED_PAGES_CAPTION', 'Items marked with a * indicate pages that you own.');
if(!defined('PAGEINDEX_ALL_PAGES')) define('PAGEINDEX_ALL_PAGES', 'All');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges.php recentchanges} action
 */
// recentchanges
if(!defined('RECENTCHANGES_HEADING')) define('RECENTCHANGES_HEADING', 'Recently changed pages');
if(!defined('REVISIONS_LINK_TITLE')) define('REVISIONS_LINK_TITLE', 'View recent revisions list for %s'); // %s - page name
if(!defined('HISTORY_LINK_TITLE')) define('HISTORY_LINK_TITLE', 'View edit history of %s'); // %s - page name
if(!defined('WIKIPING_ENABLED')) define('WIKIPING_ENABLED', 'WikiPing enabled: Changes on this wiki are broadcast to %s'); // %s - link to wikiping server
if(!defined('RECENTCHANGES_NONE_FOUND')) define('RECENTCHANGES_NONE_FOUND', 'There are no recently changed pages.');
if(!defined('RECENTCHANGES_NONE_ACCESSIBLE')) define('RECENTCHANGES_NONE_ACCESSIBLE', 'There are no recently changed pages you have access to.');
if(!defined('PAGE_EDITOR_DIVIDER')) define('PAGE_EDITOR_DIVIDER', '&#8594;');
if(!defined('MAX_REVISION_NUMBER')) define('MAX_REVISION_NUMBER', '50');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentcomments.php recentcomments} action
 */
// recentcomments
if(!defined('RECENTCOMMENTS_TIMESTAMP_CAPTION')) define('RECENTCOMMENTS_TIMESTAMP_CAPTION', '%s'); // %s - timestamp
if(!defined('RECENTCOMMENTS_NONE_FOUND')) define('RECENTCOMMENTS_NONE_FOUND', 'There are no recent comments.');
if(!defined('RECENTCOMMENTS_NONE_FOUND_BY')) define('RECENTCOMMENTS_NONE_FOUND_BY', 'There are no recent comments by %s.');
if(!defined('RECENTCOMMENTS_NONE_ACCESSIBLE')) define('RECENTCOMMENTS_NONE_ACCESSIBLE', 'There are no recent comments you have access to.');
if(!defined('RECENT_COMMENTS_HEADING')) define('RECENT_COMMENTS_HEADING', '=====Recent comments=====');
if(!defined('COMMENT_AUTHOR_DIVIDER')) define('COMMENT_AUTHOR_DIVIDER', ', comment by ');
if(!defined('COMMENT_DATE_FORMAT')) define('COMMENT_DATE_FORMAT', 'D, d M Y');
if(!defined('COMMENT_TIME_FORMAT')) define('COMMENT_TIME_FORMAT', 'H:i T');
if(!defined('COMMENT_SNIPPET_LENGTH')) define('COMMENT_SNIPPET_LENGTH', 120);

/**#@-*/

/**#@+
 * Language constant used by the {@link recentlycommented.php recentlycommented} action
 */
// recentlycommented
if(!defined('RECENTLYCOMMENTED_HEADING')) define('RECENTLYCOMMENTED_HEADING', 'Recently commented pages');
if(!defined('RECENTLYCOMMENTED_NONE_FOUND')) define('RECENTLYCOMMENTED_NONE_FOUND', 'There are no recently commented pages.');
if(!defined('RECENTLYCOMMENTED_NONE_FOUND_BY')) define('RECENTLYCOMMENTED_NONE_FOUND_BY', 'There are no recently by %s commented pages.');
if(!defined('RECENTLYCOMMENTED_NONE_ACCESSIBLE')) define('RECENTLYCOMMENTED_NONE_ACCESSIBLE', 'There are no recently commented pages you have access to.');
/**#@-*/

/**#@+
 * Language constants used by the {@link redirect.php redirect} action
 */
if (!defined('PAGE_MOVED_TO')) if(!defined('PAGE_MOVED_TO')) define('PAGE_MOVED_TO', 'This page has been moved to %s.'); # %s - targe page
if (!defined('REDIRECTED_FROM')) if(!defined('REDIRECTED_FROM')) define('REDIRECTED_FROM', 'Redirected from %s.'); # %s - redirecting page
if(!defined('INVALID_REDIRECT')) if(!defined('INVALID_REDIRECT')) define('INVALID_REDIRECT', 'Invalid redirect. Target must be an existing wiki page.');

/**#@+
 * Language constant used by the {@link revert.php revert} action
 */
if(!defined('ERROR_NO_REVERT_PRIVS')) define('ERROR_NO_REVERT_PRIVS', "Sorry, you don't have privileges to revert this page");
/**#@-*/

/**#@+
 * Language constant used by the {@link revertlink.php revertlink} action
 */
if(!defined('REVERTLINK_TEXT')) define('REVERTLINK_TEXT', '[Revert]');
if(!defined('REVERTLINK_TITLE')) define('REVERTLINK_TITLE', 'Click to revert this page to the previous revision');
if(!defined('REVERTLINK_OLDEST_TITLE')) define('REVERTLINK_OLDEST_TITLE', 'This is the oldest known version for this page');
/**#@-*/

/**#@+
 * Language constant used by the {@link revisionlink.php revisionlink} action
 */
if(!defined('REVISIONLINK_TITLE')) define('REVISIONLINK_TITLE', 'Click to view recent revisions list for this page');
if(!defined('REVISIONFEEDLINK_TITLE')) define('REVISIONFEEDLINK_TITLE', 'Click to display a feed with the latest revisions to this page');
/**#@-*/

/**#@+
 * Language constant used by the {@link rss.php rss} action
 */
if(!defined('ERROR_INVALID_RSS_SYNTAX')) define('ERROR_INVALID_RSS_SYNTAX', 'Error: Invalid RSS action syntax. <br /> Proper usage: {{rss http://domain.com/feed.xml}} or {{rss url="http://domain.com/feed.xml"}}');
/**#@-*/

/**#@+
 * Language constant used by the {@link searchform.php searchform} action
 */
if(!defined('SEARCHFORM_LABEL')) define('SEARCHFORM_LABEL', 'Search: ');
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
if(!defined('SEARCH_FOR')) define('SEARCH_FOR', 'Search for');
if(!defined('SEARCH_ZERO_MATCH')) define('SEARCH_ZERO_MATCH', 'No matches');
if(!defined('SEARCH_ONE_MATCH')) define('SEARCH_ONE_MATCH', 'One match found');
if(!defined('SEARCH_N_MATCH')) define('SEARCH_N_MATCH', '%d matches found'); // %d - number of hits
if(!defined('SEARCH_RESULTS')) define('SEARCH_RESULTS', 'Search results: <strong>%1$s</strong> for <strong>%2$s</strong>'); // %1$s: n matches for | %2$s: search term
if(!defined('SEARCH_NOT_SURE_CHOICE')) define('SEARCH_NOT_SURE_CHOICE', 'Not sure which page to choose?');
if(!defined('SEARCH_EXPANDED_LINK_DESC')) define('SEARCH_EXPANDED_LINK_DESC', 'Expanded Text Search'); // search link description
if(!defined('SEARCH_TRY_EXPANDED')) define('SEARCH_TRY_EXPANDED', 'Try the %s which shows surrounding text.'); // %s expanded search link
/*
if(!defined('SEARCH_TIPS')) define('SEARCH_TIPS', "<br /><br /><hr /><br /><strong>Search Tips:</strong><br /><br />"
	."<div class=\"indent\"><tt>apple banana</tt></div>"
	."Find pages that contain at least one of the two words. <br />"
	."<br />"
	."<div class=\"indent\"><tt>+apple +juice</tt></div>"
	."Find pages that contain both words. <br />"
	."<br />"
	."<div class=\"indent\"><tt>+apple -macintosh</tt></div>"
	."Find pages that contain the word 'apple' but not 'macintosh'. <br />"
	."<br />"
	."<div class=\"indent\"><tt>apple*</tt></div>"
	."Find pages that contain words such as apple, apples, applesauce, or applet. <br />"
	."<br />"
	."<div class=\"indent\"><tt>\"some words\"</tt></div>"
	."Find pages that contain the exact phrase 'some words' (for example, pages that contain 'some words of wisdom' <br />"
	."but not 'some noise words'). <br />");
*/
if(!defined('SEARCH_MYSQL_IDENTICAL_CHARS')) define('SEARCH_MYSQL_IDENTICAL_CHARS', 'aàáâã,eèéêë,iìîï,oòóôõ,uùúû,cç,nñ,yý');
if(!defined('SEARCH_TIPS_TITLE')) define('SEARCH_TIPS_TITLE', 'Search Tips:');
if(!defined('SEARCH_WORD_1')) define('SEARCH_WORD_1', 'apple');
if(!defined('SEARCH_WORD_2')) define('SEARCH_WORD_2', 'banana');
if(!defined('SEARCH_WORD_3')) define('SEARCH_WORD_3', 'juice');
if(!defined('SEARCH_WORD_4')) define('SEARCH_WORD_4', 'macintosh');
if(!defined('SEARCH_WORD_5')) define('SEARCH_WORD_5', 'some');
if(!defined('SEARCH_WORD_6')) define('SEARCH_WORD_6', 'words');
if(!defined('SEARCH_PHRASE')) define('SEARCH_PHRASE',sprintf('%s %s',SEARCH_WORD_5,SEARCH_WORD_6));
if(!defined('SEARCH_TARGET_1')) define('SEARCH_TARGET_1', 'Find pages that contain at least one of the two words.');
if(!defined('SEARCH_TARGET_2')) define('SEARCH_TARGET_2', 'Find pages that contain both words.');
if(!defined('SEARCH_TARGET_3')) define('SEARCH_TARGET_3',sprintf("Find pages that contain the word '%1\$s' but not '%2\$s'.",SEARCH_WORD_1,SEARCH_WORD_4));
if(!defined('SEARCH_TARGET_4')) define('SEARCH_TARGET_4',sprintf('Find pages that contain words starting with "%s"', SEARCH_WORD_1));
if(!defined('SEARCH_TARGET_5')) define('SEARCH_TARGET_5',sprintf("Find pages that contain the exact phrase '%1\$s' (for example, pages that contain '%1\$s of wisdom' but not '%2\$s noise %3\$s').",SEARCH_PHRASE,SEARCH_WORD_5,SEARCH_WORD_6));
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
/**#@-*/

/**#@+
 * Language constant used by the {@link usersettings.php usersettings} action
 */
// usersettings
// - error messages
if(!defined('ERROR_EMPTY_USERNAME')) define('ERROR_EMPTY_USERNAME', 'Please fill in your user name.');
if(!defined('ERROR_NONEXISTENT_USERNAME')) define('ERROR_NONEXISTENT_USERNAME', 'Sorry, this user name doesn\'t exist.'); // @@@ too specific
if(!defined('ERROR_RESERVED_PAGENAME')) define('ERROR_RESERVED_PAGENAME', 'Sorry, this name is reserved for a page. Please choose a different name.');
if(!defined('ERROR_WIKINAME')) define('ERROR_WIKINAME', 'Username must be formatted as a %1$s, e.g. %2$s.'); // %1$s - identifier WikiName; %2$s - sample WikiName
if(!defined('ERROR_EMPTY_EMAIL_ADDRESS')) define('ERROR_EMPTY_EMAIL_ADDRESS', 'Please specify an email address.');
if(!defined('ERROR_INVALID_EMAIL_ADDRESS')) define('ERROR_INVALID_EMAIL_ADDRESS', 'That doesn\'t quite look like an email address.');
if(!defined('ERROR_INVALID_PASSWORD')) define('ERROR_INVALID_PASSWORD', 'Sorry, you entered the wrong password.');	// @@@ too specific
if(!defined('ERROR_INVALID_HASH')) define('ERROR_INVALID_HASH', 'Sorry, you entered a wrong password reminder.');
if(!defined('ERROR_INVALID_OLD_PASSWORD')) define('ERROR_INVALID_OLD_PASSWORD', 'The old password you entered is wrong.');
if(!defined('ERROR_EMPTY_PASSWORD')) define('ERROR_EMPTY_PASSWORD', 'Please fill in a password.');
if(!defined('ERROR_EMPTY_PASSWORD_OR_HASH')) define('ERROR_EMPTY_PASSWORD_OR_HASH', 'Please fill your password or password reminder.');
if(!defined('ERROR_EMPTY_CONFIRMATION_PASSWORD')) define('ERROR_EMPTY_CONFIRMATION_PASSWORD', 'Please confirm your password in order to register a new account.');
if(!defined('ERROR_EMPTY_NEW_CONFIRMATION_PASSWORD')) define('ERROR_EMPTY_NEW_CONFIRMATION_PASSWORD', 'Please confirm your new password in order to update your account.');
if(!defined('ERROR_EMPTY_NEW_PASSWORD')) define('ERROR_EMPTY_NEW_PASSWORD', 'You must also fill in a new password.');
if(!defined('ERROR_PASSWORD_MATCH')) define('ERROR_PASSWORD_MATCH', 'Passwords don\'t match.');
if(!defined('ERROR_PASSWORD_NO_BLANK')) define('ERROR_PASSWORD_NO_BLANK', 'Sorry, blanks are not permitted in the password.');
if(!defined('ERROR_PASSWORD_TOO_SHORT')) define('ERROR_PASSWORD_TOO_SHORT', 'Sorry, the password must contain at least %d characters.'); // %d - minimum password length
if(!defined('ERROR_INVALID_REVISION_DISPLAY_LIMIT')) define('ERROR_INVALID_REVISION_DISPLAY_LIMIT', 'The number of page revisions should not exceed %d.'); // %d - maximum revisions to view
if(!defined('ERROR_INVALID_RECENTCHANGES_DISPLAY_LIMIT')) define('ERROR_INVALID_RECENTCHANGES_DISPLAY_LIMIT', 'The number of recently changed pages should not exceed %d.'); // %d - maximum changed pages to view
if(!defined('ERROR_VALIDATION_FAILED')) if(!defined('ERROR_VALIDATION_FAILED')) define('ERROR_VALIDATION_FAILED', "Registration validation failed, please try again!");
// - success messages
if(!defined('SUCCESS_USER_LOGGED_OUT')) define('SUCCESS_USER_LOGGED_OUT', 'You have successfully logged out.');
if(!defined('SUCCESS_USER_REGISTERED')) define('SUCCESS_USER_REGISTERED', 'You have successfully registered!');
if(!defined('SUCCESS_USER_SETTINGS_STORED')) define('SUCCESS_USER_SETTINGS_STORED', 'User settings stored!');
if(!defined('SUCCESS_USER_PASSWORD_CHANGED')) define('SUCCESS_USER_PASSWORD_CHANGED', 'Password successfully changed!');
// - captions
if(!defined('NEW_USER_REGISTER_CAPTION')) define('NEW_USER_REGISTER_CAPTION', 'If you are signing up as a new user:');
if(!defined('REGISTERED_USER_LOGIN_CAPTION')) define('REGISTERED_USER_LOGIN_CAPTION', 'If you already have a login, sign in here:');
if(!defined('RETRIEVE_PASSWORD_CAPTION')) define('RETRIEVE_PASSWORD_CAPTION', 'Log in with your [[%s password reminder]]:'); //%s PasswordForgotten link
if(!defined('USER_LOGGED_IN_AS_CAPTION')) define('USER_LOGGED_IN_AS_CAPTION', 'You are logged in as %s'); // %s user name
// - form legends
if(!defined('USER_ACCOUNT_LEGEND')) define('USER_ACCOUNT_LEGEND', 'Your account');
if(!defined('USER_SETTINGS_LEGEND')) define('USER_SETTINGS_LEGEND', 'Settings');
if(!defined('LOGIN_REGISTER_LEGEND')) define('LOGIN_REGISTER_LEGEND', 'Login/Register');
if(!defined('LOGIN_LEGEND')) define('LOGIN_LEGEND', 'Login');
#if(!defined('REGISTER_LEGEND')) define('REGISTER_LEGEND', 'Register'); // @@@ TODO to be used later for register-action
if(!defined('CHANGE_PASSWORD_LEGEND')) define('CHANGE_PASSWORD_LEGEND', 'Change your password');
if(!defined('RETRIEVE_PASSWORD_LEGEND')) define('RETRIEVE_PASSWORD_LEGEND', 'Password forgotten');
// - form field labels (should end in ':' _unless_ it's a checkbox or radio button option)
if(!defined('USERSETTINGS_REDIRECT_AFTER_LOGIN_LABEL')) define('USERSETTINGS_REDIRECT_AFTER_LOGIN_LABEL', 'Redirect to %s after login');	// %s page to redirect to
if(!defined('USER_EMAIL_LABEL')) define('USER_EMAIL_LABEL', 'Your email address:');
if(!defined('DOUBLECLICK_LABEL')) define('DOUBLECLICK_LABEL', 'Doubleclick editing:');
if(!defined('SHOW_COMMENTS_LABEL')) define('SHOW_COMMENTS_LABEL', 'Show comments by default:');
if(!defined('DEFAULT_COMMENT_STYLE_LABEL')) define('DEFAULT_COMMENT_STYLE_LABEL', 'Default comment style');
if(!defined('COMMENT_ASC_LABEL')) define('COMMENT_ASC_LABEL', 'Flat (oldest first)');
if(!defined('COMMENT_DEC_LABEL')) define('COMMENT_DEC_LABEL', 'Flat (newest first)');
if(!defined('COMMENT_THREADED_LABEL')) define('COMMENT_THREADED_LABEL', 'Threaded');
if(!defined('COMMENT_DELETED_LABEL')) define('COMMENT_DELETED_LABEL', '[Comment deleted]');
if(!defined('COMMENT_BY_LABEL')) define('COMMENT_BY_LABEL', 'Comment by ');
if(!defined('RECENTCHANGES_DISPLAY_LIMIT_LABEL')) define('RECENTCHANGES_DISPLAY_LIMIT_LABEL', 'RecentChanges display limit:');
if(!defined('PAGEREVISION_LIST_LIMIT_LABEL')) define('PAGEREVISION_LIST_LIMIT_LABEL', 'Page revisions list limit:');
if(!defined('NEW_PASSWORD_LABEL')) define('NEW_PASSWORD_LABEL', 'Your new password:');
if(!defined('NEW_PASSWORD_CONFIRM_LABEL')) define('NEW_PASSWORD_CONFIRM_LABEL', 'Confirm new password:');
if(!defined('NO_REGISTRATION')) define('NO_REGISTRATION', 'Registration on this wiki is disabled.');
if(!defined('PASSWORD_LABEL')) define('PASSWORD_LABEL', 'Password (%s+ chars):'); // %s minimum number of characters
if(!defined('CONFIRM_PASSWORD_LABEL')) define('CONFIRM_PASSWORD_LABEL', 'Confirm password:');
if(!defined('TEMP_PASSWORD_LABEL')) define('TEMP_PASSWORD_LABEL', 'Password reminder:');
if(!defined('INVITATION_CODE_SHORT')) define('INVITATION_CODE_SHORT', 'Invitation Code');
if(!defined('INVITATION_CODE_LONG')) define('INVITATION_CODE_LONG', 'In order to register, you must fill in the invitation code sent by this website\'s administrator.');
if(!defined('INVITATION_CODE_LABEL')) define('INVITATION_CODE_LABEL', 'Your %s:'); // %s - expanded short invitation code prompt
if(!defined('WIKINAME_SHORT')) define('WIKINAME_SHORT', 'WikiName');
if(!defined('WIKINAME_LONG')) define('WIKINAME_LONG',sprintf('A WikiName is formed by two or more capitalized words without space, e.g. %s',WIKKA_SAMPLE_WIKINAME));
if(!defined('WIKINAME_LABEL')) define('WIKINAME_LABEL', 'Your %s:'); // %s - expanded short wiki name prompt
// - form options
if(!defined('CURRENT_PASSWORD_OPTION')) define('CURRENT_PASSWORD_OPTION', 'Your current password');
if(!defined('PASSWORD_REMINDER_OPTION')) define('PASSWORD_REMINDER_OPTION', 'Password reminder');
// - form buttons
if(!defined('UPDATE_SETTINGS_BUTTON')) define('UPDATE_SETTINGS_BUTTON', 'Update Settings');
if(!defined('LOGIN_BUTTON')) define('LOGIN_BUTTON', 'Login');
if(!defined('LOGOUT_BUTTON')) define('LOGOUT_BUTTON', 'Logout');
if(!defined('CHANGE_PASSWORD_BUTTON')) define('CHANGE_PASSWORD_BUTTON', 'Change password');
if(!defined('REGISTER_BUTTON')) define('REGISTER_BUTTON', 'Register');
if(!defined('PASSWORD_MIN_LENGTH')) define('PASSWORD_MIN_LENGTH', '5');
if(!defined('VALID_EMAIL_PATTERN')) define('VALID_EMAIL_PATTERN', '/^.+?\@.+?\..+$/'); //TODO: Use central regex library
if(!defined('REVISION_DISPLAY_LIMIT_MIN')) define('REVISION_DISPLAY_LIMIT_MIN', '0'); // 0 means no limit, 1 is the minimum number of revisions
if(!defined('REVISION_DISPLAY_LIMIT_MAX')) define('REVISION_DISPLAY_LIMIT_MAX', '20'); // keep this value within a reasonable limit to avoid an unnecessary long lists
if(!defined('RECENTCHANGES_DISPLAY_LIMIT_MIN')) define('RECENTCHANGES_DISPLAY_LIMIT_MIN', '0'); // 0 means no limit, 1 is the minimum number of changes
if(!defined('RECENTCHANGES_DISPLAY_LIMIT_MAX')) define('RECENTCHANGES_DISPLAY_LIMIT_MAX', '50'); // keep this value within a reasonable limit to avoid an unnecessary long list
if(!defined('USER_SETTINGS_HEADING')) define('USER_SETTINGS_HEADING', 'User settings');
if(!defined('USER_LOGGED_OUT')) define('USER_LOGGED_OUT', 'You have successfully logged out.');
if(!defined('USER_SETTINGS_STORED')) define('USER_SETTINGS_STORED', 'User settings stored!');
if(!defined('ERROR_NO_BLANK')) define('ERROR_NO_BLANK', 'Sorry, blanks are not permitted in the password.');
if(!defined('PASSWORD_CHANGED')) define('PASSWORD_CHANGED', 'Password successfully changed!');
if(!defined('ERROR_OLD_PASSWORD_WRONG')) define('ERROR_OLD_PASSWORD_WRONG', 'The old password you entered is wrong.');
if(!defined('UPDATE_SETTINGS_INPUT')) define('UPDATE_SETTINGS_INPUT', 'Update Settings');
if(!defined('CHANGE_PASSWORD_HEADING')) define('CHANGE_PASSWORD_HEADING', 'Change your password:');
if(!defined('CURRENT_PASSWORD_LABEL')) define('CURRENT_PASSWORD_LABEL', 'Your current password:');
if(!defined('PASSWORD_REMINDER_LABEL')) define('PASSWORD_REMINDER_LABEL', 'Password reminder:');
if(!defined('CHANGE_BUTTON_LABEL')) define('CHANGE_BUTTON_LABEL', 'Change password');
if(!defined('REGISTER_BUTTON_LABEL')) define('REGISTER_BUTTON_LABEL', 'Register');
if(!defined('QUICK_LINKS_HEADING')) define('QUICK_LINKS_HEADING', 'Quick links');
if(!defined('QUICK_LINKS')) define('QUICK_LINKS', 'See a list of pages you own (MyPages) and pages you\'ve edited (MyChanges).');
if(!defined('ERROR_WRONG_PASSWORD')) define('ERROR_WRONG_PASSWORD', 'Sorry, you entered the wrong password.');
if(!defined('ERROR_WRONG_HASH')) define('ERROR_WRONG_HASH', 'Sorry, you entered a wrong password reminder.');
if(!defined('ERROR_NON_EXISTENT_USERNAME')) define('ERROR_NON_EXISTENT_USERNAME', 'Sorry, this user name doesn\'t exist.');
if(!defined('ERROR_USERNAME_EXISTS')) define('ERROR_USERNAME_EXISTS', 'Sorry, this user name already exists.');
if(!defined('ERROR_EMAIL_ADDRESS_REQUIRED')) define('ERROR_EMAIL_ADDRESS_REQUIRED', 'Please specify an email address.');
if(!defined('REGISTRATION_SUCCEEDED')) define('REGISTRATION_SUCCEEDED', 'You have successfully registered!');
if(!defined('REGISTERED_USER_LOGIN_LABEL')) define('REGISTERED_USER_LOGIN_LABEL', 'If you\'re already a registered user, log in here!');
if(!defined('LOGIN_HEADING')) define('LOGIN_HEADING', '===Login===');
if(!defined('LOGIN_REGISTER_HEADING')) define('LOGIN_REGISTER_HEADING', '===Login/Register===');
if(!defined('LOGIN_BUTTON_LABEL')) define('LOGIN_BUTTON_LABEL', 'Login');
if(!defined('LOGOUT_BUTTON_LABEL')) define('LOGOUT_BUTTON_LABEL', 'Logout');
if(!defined('NEW_USER_REGISTER_LABEL')) define('NEW_USER_REGISTER_LABEL', 'Fields you only need to fill in when you\'re logging in for the first time (and thus signing up as a new user on this site).');
if(!defined('RETRIEVE_PASSWORD_HEADING')) define('RETRIEVE_PASSWORD_HEADING', '===Forgot your password?===');
if(!defined('RETRIEVE_PASSWORD_MESSAGE')) define('RETRIEVE_PASSWORD_MESSAGE', 'If you need a password reminder, click [[PasswordForgotten here]]. --- You can login here using your password reminder.');
if(!defined('THEME_LABEL')) define('THEME_LABEL', 'Theme:');
/**#@-*/

/**#@+
 * Language constant used by the {@link wantedpages.php wantedpages} action
 */
// wantedpages
if(!defined('SORTING_LEGEND')) define('SORTING_LEGEND', 'Sorting ...');
if(!defined('SORTING_NUMBER_LABEL')) define('SORTING_NUMBER_LABEL', 'Sorting #%d:');
if(!defined('SORTING_DESC_LABEL')) define('SORTING_DESC_LABEL', 'desc');
if(!defined('OK_BUTTON')) define('OK_BUTTON', '   OK   ');
if(!defined('NO_WANTED_PAGES')) define('NO_WANTED_PAGES', 'No wanted pages. Good!');
if(!defined('WANTEDPAGES_PAGES_LINKING_TO')) define('WANTEDPAGES_PAGES_LINKING_TO', 'Pages linking to %s');
/**#@-*/

/**#@+
 * Language constant used by the {@link wikkaconfig.php wikkaconfig} action
 */
//wikkaconfig
if(!defined('WIKKACONFIG_CAPTION')) define('WIKKACONFIG_CAPTION', "Wikka Configuration Settings [%s]"); // %s link to Wikka Config options documentation
if(!defined('WIKKACONFIG_DOCS_URL')) define('WIKKACONFIG_DOCS_URL', "http://docs.wikkawiki.org/ConfigurationOptions");
if(!defined('WIKKACONFIG_DOCS_TITLE')) define('WIKKACONFIG_DOCS_TITLE', "Read the documentation on Wikka Configuration Settings");
if(!defined('WIKKACONFIG_TH_OPTION')) define('WIKKACONFIG_TH_OPTION', "Option");
if(!defined('WIKKACONFIG_TH_VALUE')) define('WIKKACONFIG_TH_VALUE', "Value");

/* ------------------ 3RD PARTY ------------------ */

/**#@+
 * Language constant used by the {@link fullscreen.php fullscreen} 3rd party MindMap display utility
 */
// fullscreen
if(!defined('CLOSE_WINDOW')) define('CLOSE_WINDOW', 'Close Window');
if(!defined('MM_GET_JAVA_PLUGIN_LINK_DESC')) define('MM_GET_JAVA_PLUGIN_LINK_DESC', 'get the latest Java Plug-in here'); // used in MM_GET_JAVA_PLUGIN
if(!defined('MM_GET_JAVA_PLUGIN')) define('MM_GET_JAVA_PLUGIN', 'so if it does not work, %s.'); // %s - plugin download link
/**#@-*/


/* ------------------ FORMATTERS ------------------ */

/**#@+
 * Language constant used by the {@link wakka.php wakka} formatter
 */
// wakka
if(!defined('GRABCODE_BUTTON')) define('GRABCODE_BUTTON', 'Grab');
if(!defined('GRABCODE_BUTTON_TITLE')) define('GRABCODE_BUTTON_TITLE', 'Download %s'); // %s download filename
/**#@-*/


/* ------------------ HANDLERS (PAGE) ------------------ */

/**#@+
 * Language constant used by the {@link acls.php acls} (page) handler
 */
// acls
// TODO: 'translate' DB value '(Public)' when displaying it!
if(!defined('ACLS_UPDATED')) define('ACLS_UPDATED', 'Access control lists updated.');
if(!defined('NO_PAGE_OWNER')) define('NO_PAGE_OWNER', '(Nobody)');
if(!defined('NOT_PAGE_OWNER')) define('NOT_PAGE_OWNER', 'You are not the owner of this page.');
if(!defined('PAGE_OWNERSHIP_CHANGED')) define('PAGE_OWNERSHIP_CHANGED', 'Ownership changed to %s'); // %s - name of new owner
if(!defined('ACLS_LEGEND')) define('ACLS_LEGEND', 'Access Control Lists for %s'); // %s - name of current page
if(!defined('ACLS_READ_LABEL')) define('ACLS_READ_LABEL', 'Read ACL:');
if(!defined('ACLS_WRITE_LABEL')) define('ACLS_WRITE_LABEL', 'Write ACL:');
if(!defined('ACLS_COMMENT_READ_LABEL')) define('ACLS_COMMENT_READ_LABEL', 'Comment Read ACL:');
if(!defined('ACLS_COMMENT_POST_LABEL')) define('ACLS_COMMENT_POST_LABEL', 'Comment Post ACL:');
if(!defined('SET_OWNER_LABEL')) define('SET_OWNER_LABEL', 'Set Page Owner:');
if(!defined('SET_OWNER_CURRENT_OPTION')) define('SET_OWNER_CURRENT_OPTION', '(Current Owner)');
if(!defined('SET_OWNER_PUBLIC_OPTION')) define('SET_OWNER_PUBLIC_OPTION', '(Public)'); // actual DB value will remain '(Public)' even if this option text is translated!
if(!defined('SET_NO_OWNER_OPTION')) define('SET_NO_OWNER_OPTION', '(Nobody - Set free)');
if(!defined('ACLS_STORE_BUTTON')) define('ACLS_STORE_BUTTON', 'Store ACLs');
if(!defined('CANCEL_BUTTON')) define('CANCEL_BUTTON', 'Cancel');
// - syntax
if(!defined('ACLS_SYNTAX_HEADING')) define('ACLS_SYNTAX_HEADING', 'Syntax:');
if(!defined('ACLS_EVERYONE')) define('ACLS_EVERYONE', 'Everyone');
if(!defined('ACLS_REGISTERED_USERS')) define('ACLS_REGISTERED_USERS', 'Registered users');
if(!defined('ACLS_NONE_BUT_ADMINS')) define('ACLS_NONE_BUT_ADMINS', 'No one (except admins)');
if(!defined('ACLS_ANON_ONLY')) define('ACLS_ANON_ONLY', 'Anonymous users only');
if(!defined('ACLS_LIST_USERNAMES')) define('ACLS_LIST_USERNAMES', 'the user called %s; enter as many users as you want, one per line'); // %s - sample user name
if(!defined('ACLS_NEGATION')) define('ACLS_NEGATION', 'Any of these items can be negated with a %s:'); // %s - 'negation' mark
if(!defined('ACLS_DENY_USER_ACCESS')) define('ACLS_DENY_USER_ACCESS', '%s will be denied access'); // %s - sample user name
if(!defined('ACLS_AFTER')) define('ACLS_AFTER', 'after');
if(!defined('ACLS_TESTING_ORDER1')) define('ACLS_TESTING_ORDER1', 'ACLs are tested in the order they are specified:');
if(!defined('ACLS_TESTING_ORDER2')) define('ACLS_TESTING_ORDER2', 'So be sure to specify %1$s on a separate line %2$s negating any users, not before.'); // %1$s - 'all' mark; %2$s - emphasised 'after'
if(!defined('ACLS_DEFAULT_ACLS')) define('ACLS_DEFAULT_ACLS', 'Any lists that are left empty will be set to the defaults as specified in %s.');
if(!defined('ACL_HEADING')) define('ACL_HEADING', '====Access Control Lists for %s===='); // %s - name of current page
if(!defined('READ_ACL_LABEL')) define('READ_ACL_LABEL', 'Read ACL:');
if(!defined('WRITE_ACL_LABEL')) define('WRITE_ACL_LABEL', 'Write ACL:');
if(!defined('COMMENT_ACL_LABEL')) define('COMMENT_ACL_LABEL', 'Comment ACL:');
if(!defined('STORE_ACL_LABEL')) define('STORE_ACL_LABEL', 'Store ACLs');
if(!defined('SET_OWNER_CURRENT_LABEL')) define('SET_OWNER_CURRENT_LABEL', '(Current Owner)');
if(!defined('SET_OWNER_PUBLIC_LABEL')) define('SET_OWNER_PUBLIC_LABEL','(Public)');
if(!defined('SET_NO_OWNER_LABEL')) define('SET_NO_OWNER_LABEL', '(Nobody - Set free)');
if(!defined('ACL_SYNTAX_HELP')) define('ACL_SYNTAX_HELP', '===Syntax:=== ---##*## = Everyone ---##+## = Registered users ---##""JohnDoe""## = the user called ""JohnDoe"", enter as many users as you want, one per line --- --- Any of these items can be negated with a ##!##: ---##!*## = No one (except admins) ---##!+## = Anonymous users only ---##""!JohnDoe""## = ""JohnDoe"" will be denied access --- --- //ACLs are tested in the order they are specified:// --- So be sure to specify ##*## on a separate line //after// negating any users, not before.');
/**#@-*/

/**#@+
 * Language constant used by the {@link backlinks.php backlinks} (page) handler
 */
// backlinks
if(!defined('PAGE_TITLE')) define('PAGE_TITLE','Pages linking to %s');
if(!defined('MESSAGE_NO_BACKLINKS')) define('MESSAGE_NO_BACKLINKS','There are no backlinks to this page.');
if(!defined('MESSAGE_MISSING_PAGE')) define('MESSAGE_MISSING_PAGE','Sorry, page %s does not exist.');
if(!defined('MESSAGE_PAGE_INACCESSIBLE')) define('MESSAGE_PAGE_INACCESSIBLE', 'You are not allowed to read this page');
/**#@-*/

/**#@+
 * Language constant used by the {@link claim.php claim} (page) handler
 */
// claim
if(!defined('USER_IS_NOW_OWNER')) define('USER_IS_NOW_OWNER', 'You are now the owner of this page.');
/**#@-*/

/**#@+
 * Language constant used by the {@link clone.php clone} (page) handler
 */
// clone
define ('VALID_PAGENAME_PATTERN', '/^[A-Za-zÄÖÜßäöü]+[A-Za-z0-9ÄÖÜßäöü]*$/s');
if(!defined('ERROR_ACL_WRITE')) define('ERROR_ACL_WRITE', 'Sorry! You don\'t have write-access to %s');
if(!defined('CLONE_VALID_TARGET')) define('CLONE_VALID_TARGET', 'Please fill in a valid target page name and an (optional) edit note.');
if(!defined('CLONE_LEGEND')) define('CLONE_LEGEND', 'Clone %s'); // %s source page name
if(!defined('CLONED_FROM')) define('CLONED_FROM', 'Cloned from %s'); // %s source page name
if(!defined('SUCCESS_CLONE_CREATED')) define('SUCCESS_CLONE_CREATED', '%s was succesfully created!'); // %s new page name
if(!defined('CLONE_X_TO_LABEL')) define('CLONE_X_TO_LABEL', 'Clone as:');
if(!defined('CLONE_EDIT_NOTE_LABEL')) define('CLONE_EDIT_NOTE_LABEL', 'Edit note:');
if(!defined('CLONE_EDIT_OPTION_LABEL')) define('CLONE_EDIT_OPTION_LABEL', ' Edit after creation');
if(!defined('CLONE_ACL_OPTION_LABEL')) define('CLONE_ACL_OPTION_LABEL', ' Clone ACL');
if(!defined('CLONE_BUTTON')) define('CLONE_BUTTON', 'Clone');
if(!defined('CLONE_HEADER')) define('CLONE_HEADER', 'Clone current page');
if(!defined('CLONE_SUCCESSFUL')) define('CLONE_SUCCESSFUL', '%s was succesfully created!');
if(!defined('CLONE_X_TO')) define('CLONE_X_TO', 'Clone %s to:');
if(!defined('EDIT_NOTE')) define('EDIT_NOTE', 'Edit note:');
if(!defined('ERROR_ACL_READ')) define('ERROR_ACL_READ', 'You are not allowed to read the source of this page.');
if(!defined('ERROR_INVALID_PAGENAME')) define('ERROR_INVALID_PAGENAME', 'This page name is invalid. Valid page names must start with a letter and contain only letters and numbers.');
if(!defined('ERROR_PAGE_ALREADY_EXIST')) define('ERROR_PAGE_ALREADY_EXIST', 'Sorry, the destination page already exists');
if(!defined('ERROR_PAGE_NOT_EXIST')) define('ERROR_PAGE_NOT_EXIST', ' Sorry, page %s does not exist.');
if(!defined('LABEL_CLONE')) define('LABEL_CLONE', 'Clone');
if(!defined('LABEL_EDIT_OPTION')) define('LABEL_EDIT_OPTION', ' Edit after creation ');
if(!defined('PLEASE_FILL_VALID_TARGET')) define('PLEASE_FILL_VALID_TARGET', 'Please fill in a valid target <tt>PageName</tt> and an (optional) edit note.');
/**#@-*/

/**#@+
 * Language constant used by the {@link delete.php delete} (page) handler
 */
// delete
if(!defined('ERROR_NO_PAGE_DEL_ACCESS')) define('ERROR_NO_PAGE_DEL_ACCESS', 'You are not allowed to delete this page.');
if(!defined('PAGE_DELETION_HEADER')) define('PAGE_DELETION_HEADER', 'Delete %s'); // %s - name of the page
if(!defined('SUCCESS_PAGE_DELETED')) define('SUCCESS_PAGE_DELETED', 'Page has been deleted!');
if(!defined('PAGE_DELETION_CAPTION')) define('PAGE_DELETION_CAPTION', 'Completely delete this page, including all comments?');
if(!defined('PAGE_DELETION_DELETE_BUTTON')) define('PAGE_DELETION_DELETE_BUTTON', 'Delete Page');
if(!defined('PAGE_DELETION_CANCEL_BUTTON')) define('PAGE_DELETION_CANCEL_BUTTON', 'Cancel');
/**#@-*/

/**#@+
 * Language constant used by the {@link diff.php diff} (page) handler
 */
// diff
if(!defined('ERROR_DIFF_LIBRARY_MISSING')) define('ERROR_DIFF_LIBRARY_MISSING', 'The file <tt>"libs/diff.lib.php"</tt> could not be found. You may want to notify the wiki administrator');
if(!defined('ERROR_BAD_PARAMETERS')) define('ERROR_BAD_PARAMETERS', 'The parameters you supplied are incorrect, one of the two revisions may have been removed.');
if(!defined('DIFF_COMPARISON_HEADER')) define('DIFF_COMPARISON_HEADER', 'Comparing %1$s for %2$s'); // %1$s - link to revision list; %2$s - link to page
if(!defined('DIFF_REVISION_LINK_TITLE')) define('DIFF_REVISION_LINK_TITLE', 'Display the revision list for %s'); // %s page name
if(!defined('DIFF_PAGE_LINK_TITLE')) define('DIFF_PAGE_LINK_TITLE', 'Return to the latest version of this page');
if(!defined('DIFF_SAMPLE_ADDITION')) define('DIFF_SAMPLE_ADDITION', 'addition');
if(!defined('DIFF_SAMPLE_DELETION')) define('DIFF_SAMPLE_DELETION', 'deletion');
if(!defined('DIFF_SIMPLE_BUTTON')) define('DIFF_SIMPLE_BUTTON', 'Simple Diff');
if(!defined('DIFF_FULL_BUTTON')) define('DIFF_FULL_BUTTON', 'Full Diff');
if(!defined('HIGHLIGHTING_LEGEND')) define('HIGHLIGHTING_LEGEND', 'Highlighting Guide:');
define ('ERROR_NO_PAGE_ACCESS', 'You are not authorized to view this page.');
define ('CONTENT_ADDITIONS_HEADER', 'Additions:');
define ('CONTENT_DELETIONS_HEADER', 'Deletions:');
define ('CONTENT_NO_DIFFERENCES', 'No Differences');
/**#@-*/

/**#@+
 * Language constant used by the {@link edit.php edit} (page) handler
 */
// edit
if(!defined('ERROR_OVERWRITE_ALERT1')) define('ERROR_OVERWRITE_ALERT1', 'OVERWRITE ALERT: This page was modified by someone else while you were editing it.');
if(!defined('ERROR_OVERWRITE_ALERT2')) define('ERROR_OVERWRITE_ALERT2', 'Please copy your changes and re-edit this page.');
if(!defined('ERROR_MISSING_EDIT_NOTE')) define('ERROR_MISSING_EDIT_NOTE', 'MISSING EDIT NOTE: Please fill in an edit note!');
if(!defined('ERROR_TAG_TOO_LONG')) define('ERROR_TAG_TOO_LONG', 'Page name too long! %d characters max.'); // %d - maximum page name length
if(!defined('ERROR_NO_WRITE_ACCESS')) define('ERROR_NO_WRITE_ACCESS', 'You don\'t have write access to this page. You might need to [[UserSettings login]] or [[UserSettings register an account]] to be able to edit this page.'); //TODO Distinct links for login and register actions
if(!defined('EDIT_STORE_PAGE_LEGEND')) define('EDIT_STORE_PAGE_LEGEND', 'Store page');
if(!defined('EDIT_PREVIEW_HEADER')) define('EDIT_PREVIEW_HEADER', 'Preview');
if(!defined('EDIT_NOTE_LABEL')) define('EDIT_NOTE_LABEL', 'Please add a note on your edit'); // label after field, so no colon!
if(!defined('MESSAGE_AUTO_RESIZE')) define('MESSAGE_AUTO_RESIZE', 'Clicking on %s will automatically truncate the page name to the correct size'); // %s - rename button text
if(!defined('EDIT_PREVIEW_BUTTON')) define('EDIT_PREVIEW_BUTTON', 'Preview');
if(!defined('EDIT_STORE_BUTTON')) define('EDIT_STORE_BUTTON', 'Store');
if(!defined('EDIT_REEDIT_BUTTON')) define('EDIT_REEDIT_BUTTON', 'Re-edit');
if(!defined('EDIT_CANCEL_BUTTON')) define('EDIT_CANCEL_BUTTON', 'Cancel');
if(!defined('EDIT_RENAME_BUTTON')) define('EDIT_RENAME_BUTTON', 'Rename');
if(!defined('ACCESSKEY_PREVIEW')) define('ACCESSKEY_PREVIEW', 'p'); // ideally, should match EDIT_PREVIEW_BUTTON
if(!defined('ACCESSKEY_STORE')) define('ACCESSKEY_STORE', 's'); // ideally, should match EDIT_STORE_BUTTON
if(!defined('ACCESSKEY_REEDIT')) define('ACCESSKEY_REEDIT', 'r'); // ideally, should match EDIT_REEDIT_BUTTON
if(!defined('SHOWCODE_LINK')) define('SHOWCODE_LINK', 'View formatting code for this page');
if(!defined('SHOWCODE_LINK_TITLE')) define('SHOWCODE_LINK_TITLE', 'Click to view page formatting code'); // @@@ TODO 'View page formatting code'
if(!defined('EDIT_COMMENT_TIMESTAMP_CAPTION')) define('EDIT_COMMENT_TIMESTAMP_CAPTION', '(%s)'); // %s timestamp
if(!defined('ERROR_INVALID_PAGEID')) define('ERROR_INVALID_PAGEID', 'The revision id does not exist for the requested page');
define ('MAX_TAG_LENGTH', 75);
define ('MAX_EDIT_NOTE_LENGTH', 50);
if(!defined('PREVIEW_HEADER')) define('PREVIEW_HEADER', 'Preview');
if(!defined('LABEL_EDIT_NOTE')) define('LABEL_EDIT_NOTE', 'Please add a note on your edit');
if(!defined('ERROR_OVERWRITE_ALERT')) define('ERROR_OVERWRITE_ALERT', 'OVERWRITE ALERT: This page was modified by someone else while you were editing it.<br /> Please copy your changes and re-edit this page.');
if(!defined('INPUT_SUBMIT_PREVIEW')) define('INPUT_SUBMIT_PREVIEW', 'Preview');
if(!defined('INPUT_SUBMIT_STORE')) define('INPUT_SUBMIT_STORE', 'Store');
if(!defined('INPUT_SUBMIT_REEDIT')) define('INPUT_SUBMIT_REEDIT', 'Re-edit');
if(!defined('INPUT_BUTTON_CANCEL')) define('INPUT_BUTTON_CANCEL', 'Cancel');
if(!defined('INPUT_SUBMIT_RENAME')) define('INPUT_SUBMIT_RENAME', 'Rename');
/**#@-*/

/**#@+
 * Language constant used by the {@link grabcode.php grabcode} (page) handler
 */
// grabcode
if(!defined('ERROR_NO_CODE')) define('ERROR_NO_CODE', 'Sorry, there is no code to download.');
if(!defined('DEFAULT_FILENAME')) define('DEFAULT_FILENAME', 'codeblock.txt'); # default name for code blocks
if(!defined('FILE_EXTENSION')) define('FILE_EXTENSION', '.txt'); # extension appended to code block name
/**#@-*/

/**#@+
 * Language constant used by the {@link history.php history} (page) handler
 */
// history
if(!defined('EDITED_ON')) define('EDITED_ON', 'Edited on %1$s by %2$s'); // %1$s - time; %2$s - user name
if(!defined('HISTORY_PAGE_VIEW')) define('HISTORY_PAGE_VIEW', 'History of recent changes for %s'); // %s pagename
if(!defined('OLDEST_VERSION_EDITED_ON_BY')) define('OLDEST_VERSION_EDITED_ON_BY', 'The oldest known version of this page was created on %1$s by %2$s'); // %1$s - time; %2$s - user name
if(!defined('MOST_RECENT_EDIT')) define('MOST_RECENT_EDIT', 'Last edited on %1$s by %2$s');
if(!defined('HISTORY_MORE_LINK_DESC')) define('HISTORY_MORE_LINK_DESC', 'here'); // used for alternative history link in HISTORY_MORE
if(!defined('HISTORY_MORE')) define('HISTORY_MORE', 'Full history for this page cannot be displayed within a single page, click %s to view more.'); // %s alternative history link # @@@ TODO avoid using 'here' ^
if(!defined('DIFF_ADDITIONS')) define('DIFF_ADDITIONS', 'Additions:');
if(!defined('DIFF_DELETIONS')) define('DIFF_DELETIONS', 'Deletions:');
if(!defined('DIFF_NO_DIFFERENCES')) define('DIFF_NO_DIFFERENCES', 'No differences.');
if(!defined('REVISION_NUMBER')) define('REVISION_NUMBER', 'Revision %s');
/**#@-*/

/**#@+
 * Language constant shared by the {@link processcomment.php processcomment} and {@link show.php show} (page) handlers
 */
// processcomment & show
// - comment buttons
if(!defined('COMMENT_DELETE_BUTTON')) define('COMMENT_DELETE_BUTTON', 'Delete');
if(!defined('COMMENT_REPLY_BUTTON')) define('COMMENT_REPLY_BUTTON', 'Reply');
if(!defined('COMMENT_ADD_BUTTON')) define('COMMENT_ADD_BUTTON', 'Add Comment');
if(!defined('COMMENT_NEW_BUTTON')) define('COMMENT_NEW_BUTTON', 'New Comment');
if(!defined('DISPLAY_COMMENTS_THREADED')) define('DISPLAY_COMMENTS_THREADED', 'Threaded');
if(!defined('BUTTON_NEW_COMMENT')) define('BUTTON_NEW_COMMENT', 'New Comment');
if(!defined('BUTTON_REPLY_COMMENT')) define('BUTTON_REPLY_COMMENT', 'Reply to Comment');
if(!defined('COMMENT_NO_DISPLAY')) define('COMMENT_NO_DISPLAY', 0);
if(!defined('COMMENT_ORDER_DATE_ASC')) define('COMMENT_ORDER_DATE_ASC', 1);
if(!defined('COMMENT_ORDER_DATE_DESC')) define('COMMENT_ORDER_DATE_DESC', 2);
if(!defined('COMMENT_ORDER_THREADED')) define('COMMENT_ORDER_THREADED', 3);
if(!defined('COMMENT_MAX_TRAVERSAL_DEPTH')) define('COMMENT_MAX_TRAVERSAL_DEPTH', 10);
/**#@-*/

/**#@+
 * Language constant used by the {@link processcomment.php processcomment} (page) handler
 */
// processcomment
if(!defined('ERROR_NO_COMMENT_DEL_ACCESS')) define('ERROR_NO_COMMENT_DEL_ACCESS', 'Sorry, you\'re not allowed to delete this comment!');
if(!defined('ERROR_NO_COMMENT_WRITE_ACCESS')) define('ERROR_NO_COMMENT_WRITE_ACCESS', 'Sorry, you\'re not allowed to post comments to this page');
if(!defined('ERROR_EMPTY_COMMENT')) define('ERROR_EMPTY_COMMENT', 'Comment body was empty -- not saved!');
if(!defined('ERROR_COMMENT_NO_KEY')) define('ERROR_COMMENT_NO_KEY', "Your comment cannot be saved. Please contact the wiki administrator.");
if(!defined('ERROR_COMMENT_INVALID_KEY')) define('ERROR_COMMENT_INVALID_KEY', "Your comment cannot be saved. Please contact the wiki administrator.");
if(!defined('ADD_COMMENT_LABEL')) define('ADD_COMMENT_LABEL', 'In reply to %s:');
if(!defined('NEW_COMMENT_LABEL')) define('NEW_COMMENT_LABEL', 'Post a new comment:');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges_simple.xml.mm.php recentchanges_simple.xml.mm} (page) handler
 */
// recentchanges_simple.xml.mm
if(!defined('FIRST_NODE_LABEL')) define('FIRST_NODE_LABEL', 'Recent Changes');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges.xml.php recentchanges.xml} (page) handler
 */
// recentchanges.xml
if(!defined('RECENTCHANGES_DESC')) define('RECENTCHANGES_DESC', 'Recent changes of %s'); // %s - page name
if(!defined('LABEL_ERROR')) define('LABEL_ERROR', 'Error');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges.xml.mm.php recentchanges.mm.xml} (page) handler
 */
// recentchanges.mm.xml
if(!defined('RECENTCHANGES_REV_TIME_CAPTION')) define('RECENTCHANGES_REV_TIME_CAPTION', 'Revision time: %s'); // %s timestamp
if(!defined('RECENTCHANGES_VIEW_HISTORY_TITLE')) define('RECENTCHANGES_VIEW_HISTORY_TITLE', 'View History');
if(!defined('RECENTCHANGES_AUTHOR')) define('RECENTCHANGES_AUTHOR', 'Author: %s'); // %s author
/**#@-*/

/**#@+
 * Language constant shared by the {@link referrers_sites.php referrers_sites}, {@link referrers.php referrers} and {@link review_blacklist.php review_blacklist} (page) handlers
 */
// referrers_sites + referrers + review_blacklist
if(!defined('REFERRERS_PURGE_24_HOURS')) define('REFERRERS_PURGE_24_HOURS', 'last 24 hours');
if(!defined('REFERRERS_PURGE_N_DAYS')) define('REFERRERS_PURGE_N_DAYS', 'last %d days'); // %d number of days
if(!defined('REFERRERS_NO_SPAM')) define('REFERRERS_NO_SPAM', 'Note to spammers: This page is not indexed by search engines, so don\'t waste your time.');
if(!defined('REFERRERS_DOMAINS_TO_WIKI_LINK_DESC')) define('REFERRERS_DOMAINS_TO_WIKI_LINK_DESC', 'View global referring sites');
if(!defined('REFERRERS_DOMAINS_TO_PAGE_LINK_DESC')) define('REFERRERS_DOMAINS_TO_PAGE_LINK_DESC', 'View referring sites for %s only'); // %s - page name
if(!defined('REFERRERS_URLS_TO_WIKI_LINK_DESC')) define('REFERRERS_URLS_TO_WIKI_LINK_DESC', 'View global referrers');
if(!defined('REFERRERS_URLS_TO_PAGE_LINK_DESC')) define('REFERRERS_URLS_TO_PAGE_LINK_DESC', 'View referrers for %s only'); // %s - page name
if(!defined('REFERRER_BLACKLIST_LINK_DESC')) define('REFERRER_BLACKLIST_LINK_DESC', 'View referrer blacklist');
if(!defined('BLACKLIST_LINK_DESC')) define('BLACKLIST_LINK_DESC', 'Blacklist');
if(!defined('NONE_CAPTION')) define('NONE_CAPTION', 'None');
if(!defined('PLEASE_LOGIN_CAPTION')) define('PLEASE_LOGIN_CAPTION', 'You need to login to see referring sites');
/**#@-*/

/**#@+
 * Language constant used by the {@link referrers_sites.php referrers_sites} (page) handler
 */
// referrers_sites
if(!defined('REFERRERS_URLS_LINK_DESC')) define('REFERRERS_URLS_LINK_DESC', 'see list of different URLs');
if(!defined('REFERRERS_DOMAINS_TO_WIKI')) define('REFERRERS_DOMAINS_TO_WIKI', 'Domains/sites linking to this wiki (%s)'); // %s - link to referrers handler
if(!defined('REFERRERS_DOMAINS_TO_PAGE')) define('REFERRERS_DOMAINS_TO_PAGE', 'Domains/sites linking to %1$s %2$s (%3$s)'); // %1$s - page link; %2$s - purge time; %3$s - link to referrers handler
/**#@-*/

/**#@+
 * Language constant used by the {@link referrers.php referrers} (page) handler
 */
// referrers
if(!defined('REFERRERS_DOMAINS_LINK_DESC')) define('REFERRERS_DOMAINS_LINK_DESC', 'see list of domains');
if(!defined('REFERRERS_URLS_TO_WIKI')) define('REFERRERS_URLS_TO_WIKI', 'External pages linking to this wiki (%s)'); // %s - link to referrers_sites handler
if(!defined('REFERRERS_URLS_TO_PAGE')) define('REFERRERS_URLS_TO_PAGE', 'External pages linking to %1$s %2$s (%3$s)'); // %1$s - page link; %2$s - purge time; %3$s - link to referrers_sites handler
/**#@-*/

/**#@+
 * Language constant used by the {@link revert.php revert} (page) handler
 */
// revert
define ('REVERT_DEFAULT_COMMENT', 'Reverted to previous revision');
/**#@-*/

/**#@+
 * Language constant used by the {@link review_blacklist.php review_blacklist} (page) handler
 */
// review_blacklist
if(!defined('BLACKLIST_HEADING')) define('BLACKLIST_HEADING', 'Referrer Blacklist');
if(!defined('BLACKLIST_REMOVE_LINK_DESC')) define('BLACKLIST_REMOVE_LINK_DESC', 'Remove');
if(!defined('STATUS_BLACKLIST_EMPTY')) define('STATUS_BLACKLIST_EMPTY', 'Blacklist is empty.');
if(!defined('BLACKLIST_VIEW_GLOBAL_SITES')) define('BLACKLIST_VIEW_GLOBAL_SITES', 'View global referring sites');
if(!defined('BLACKLIST_VIEW_GLOBAL')) define('BLACKLIST_VIEW_GLOBAL', 'view global referrers');
/**#@-*/

/**#@+
 * Language constant used by the {@link revisions.php revisions} (page) handler
 */
// revisions
if(!defined('REVISIONS_CAPTION')) define('REVISIONS_CAPTION', 'Revisions for %s'); // %s pagename
if(!defined('REVISIONS_NO_REVISIONS_YET')) define('REVISIONS_NO_REVISIONS_YET', 'There are no revisions for this page yet');
if(!defined('REVISIONS_SIMPLE_DIFF')) define('REVISIONS_SIMPLE_DIFF', 'Simple Diff');
if(!defined('REVISIONS_MORE_CAPTION')) define('REVISIONS_MORE_CAPTION', 'There are more revisions that were not shown here, click the button labelled %s below to view these entries'); // %S - text of REVISIONS_MORE_BUTTON
if(!defined('REVISIONS_RETURN_TO_NODE_BUTTON')) define('REVISIONS_RETURN_TO_NODE_BUTTON', 'Return To Node / Cancel');
if(!defined('REVISIONS_SHOW_DIFFERENCES_BUTTON')) define('REVISIONS_SHOW_DIFFERENCES_BUTTON', 'Show Differences');
if(!defined('REVISIONS_MORE_BUTTON')) define('REVISIONS_MORE_BUTTON', 'Next...');
/**#@-*/

/**#@+
 * Language constant used by the {@link revisions.xml.php revisions.xml} (page) handler
 */
// revisions.xml
if(!defined('REVISIONS_EDITED_BY')) define('REVISIONS_EDITED_BY', 'Edited by %s'); // %s user name
if(!defined('HISTORY_REVISIONS_OF')) define('HISTORY_REVISIONS_OF', 'History/revisions of %s'); // %s - page name
if(!defined('EDITED_BY')) define('EDITED_BY', 'Edited by %s');
if(!defined('I18N_ENCODING_UTF8')) define('I18N_ENCODING_UTF8', 'UTF-8');
if(!defined('RSS_REVISIONS_VERSION')) define('RSS_REVISIONS_VERSION','2.0');
if(!defined('RSS_RECENTCHANGES_VERSION')) define('RSS_RECENTCHANGES_VERSION','0.92');
/**#@-*/

/**#@+
 * Language constant used by the {@link revisions.php revisions} (page) handler
 */
if(!defined('BUTTON_RETURN_TO_NODE')) define('BUTTON_RETURN_TO_NODE', 'Return To Node / Cancel');
if(!defined('BUTTON_SHOW_DIFFERENCES')) define('BUTTON_SHOW_DIFFERENCES', 'Show Differences');
if(!defined('SIMPLE_DIFF')) define('SIMPLE_DIFF', 'Simple Diff');
/**#@-*/

/**#@+
 * Language constant used by the {@link show.php show} (page) handler
 */
// show
if(!defined('SHOW_RE_EDIT_BUTTON')) define('SHOW_RE_EDIT_BUTTON', 'Re-edit this old revision');
if(!defined('SHOW_FORMATTED_BUTTON')) define('SHOW_FORMATTED_BUTTON', 'Show formatted');
if(!defined('SHOW_SOURCE_BUTTON')) define('SHOW_SOURCE_BUTTON', 'Show source');
if(!defined('SHOW_ASK_CREATE_PAGE_CAPTION')) define('SHOW_ASK_CREATE_PAGE_CAPTION', 'This page doesn\'t exist yet. Maybe you want to %s it?'); // %s - page create link
if(!defined('SHOW_OLD_REVISION_CAPTION')) define('SHOW_OLD_REVISION_CAPTION', 'This is an old revision of %1$s made by %2$s on %3$s.'); // %1$s - page link; %2$s - username; %3$s - timestamp;
if(!defined('COMMENTS_CAPTION')) define('COMMENTS_CAPTION', 'Comments');
if(!defined('DISPLAY_COMMENTS_LABEL')) define('DISPLAY_COMMENTS_LABEL', 'Show comments');
if(!defined('DISPLAY_COMMENT_LINK_DESC')) define('DISPLAY_COMMENT_LINK_DESC', 'Display comment');
if(!defined('DISPLAY_COMMENTS_EARLIEST_LINK_DESC')) define('DISPLAY_COMMENTS_EARLIEST_LINK_DESC', 'Earliest first');
if(!defined('DISPLAY_COMMENTS_LATEST_LINK_DESC')) define('DISPLAY_COMMENTS_LATEST_LINK_DESC', 'Latest first');
if(!defined('DISPLAY_COMMENTS_THREADED_LINK_DESC')) define('DISPLAY_COMMENTS_THREADED_LINK_DESC', 'Threaded');
if(!defined('HIDE_COMMENTS_LINK_DESC')) define('HIDE_COMMENTS_LINK_DESC', 'Hide comments');
if(!defined('STATUS_NO_COMMENTS')) define('STATUS_NO_COMMENTS', 'There are no comments on this page.');
if(!defined('STATUS_ONE_COMMENT')) define('STATUS_ONE_COMMENT', 'There is one comment on this page.');
if(!defined('STATUS_SOME_COMMENTS')) define('STATUS_SOME_COMMENTS', 'There are %d comments on this page.'); // %d - number of comments
if(!defined('COMMENT_TIME_CAPTION')) define('COMMENT_TIME_CAPTION', '%s'); // %s comment time
if(!defined('SHOW_OLD_REVISION_SOURCE')) define('SHOW_OLD_REVISION_SOURCE', 0); # if set to 1 shows by default the source of an old revision instead of the rendered version
/**#@-*/

/**#@+
 * Language constant used by the {@link showcode.php showcode} (page) handler
 */
// showcode
if(!defined('SOURCE_HEADING')) define('SOURCE_HEADING', 'Wiki source for %s'); // %s - page link
if(!defined('SHOW_RAW_LINK_DESC')) define('SHOW_RAW_LINK_DESC', 'Show raw source');
if(!defined('RAW_LINK_DESC')) define('RAW_LINK_DESC', 'show source only');
if(!defined('ERROR_NOT_EXISTING_PAGE')) define('ERROR_NOT_EXISTING_PAGE', 'Sorry, this page doesn\'t exist.');
if(!defined('ERROR_NO_READ_ACCESS')) define('ERROR_NO_READ_ACCESS', 'Sorry, you aren\'t allowed to read this page.');
/**#@-*/

/* ------------------ LIBS ------------------*/

/**#@+
 * Language constant used by the {@link Wakka.class.php Wakka class} (the Wikka core containing most methods)
 */
// Wakka.class
if(!defined('QUERY_FAILED')) define('QUERY_FAILED', 'Query failed:');
if(!defined('REDIR_DOCTITLE')) define('REDIR_DOCTITLE', 'Redirected to %s'); // %s - target page
if(!defined('REDIR_LINK_DESC')) define('REDIR_LINK_DESC', 'this link'); // used in REDIR_MANUAL_CAPTION
if(!defined('REDIR_MANUAL_CAPTION')) define('REDIR_MANUAL_CAPTION', 'If your browser does not redirect you, please follow %s'); // %s target page link
if(!defined('CREATE_THIS_PAGE_LINK_TITLE')) define('CREATE_THIS_PAGE_LINK_TITLE', 'Create this page');
if(!defined('ACTION_UNKNOWN_SPECCHARS')) define('ACTION_UNKNOWN_SPECCHARS', 'Unknown action; the action name must not contain special characters.');
if(!defined('ACTION_UNKNOWN')) define('ACTION_UNKNOWN', 'Unknown action "%s"'); // %s - action name
if(!defined('HANDLER_UNKNOWN_SPECCHARS')) define('HANDLER_UNKNOWN_SPECCHARS', 'Unknown handler; the handler name must not contain special characters.');
if(!defined('HANDLER_UNKNOWN')) define('HANDLER_UNKNOWN', 'Sorry, %s is an unknown handler.'); // %s handler name
if(!defined('FORMATTER_UNKNOWN_SPECCHARS')) define('FORMATTER_UNKNOWN_SPECCHARS', 'Unknown formatter; the formatter name must not contain special characters.');
if(!defined('FORMATTER_UNKNOWN')) define('FORMATTER_UNKNOWN', 'Formatter "%s" not found'); // %s formatter name
if(!defined('DEFAULT_THEMES_TITLE')) define('DEFAULT_THEMES_TITLE', 'Default themes (%s)'); //%s: number of available themes 
if(!defined('CUSTOM_THEMES_TITLE')) define('CUSTOM_THEMES_TITLE', 'Custom themes (%s)'); //%s: number of available themes
/**#@-*/

/* ------------------ SETUP ------------------ */
/**#@+
 * Language constant used by the {@link index.php setup} program (and several included files)
 */
// @@@ later....
/**#@-*/

?>
