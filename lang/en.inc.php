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

/**#@+
 * Language constant shared among several Wikka files
 */
define('WIKKA_ERROR_CAPTION','Error');
define('WIKKA_ERROR_ACL_READ','You aren\'t allowed to read this page.');
define('WIKKA_ERROR_ACL_READ_SOURCE','You aren\'t allowed to read the source of this page.');
define('WIKKA_ERROR_ACL_READ_INFO','You aren\'t allowed to access this information.');
define('WIKKA_ERROR_LABEL','Error');
define('WIKKA_ERROR_PAGE_NOT_EXIST','Sorry, page %s does not exist.'); // %s (source) page name
define('WIKKA_ERROR_EMPTY_USERNAME','Please fill in your username!');
define('WIKKA_LOGIN_LINK_DESC','login');
define('WIKKA_MAINPAGE_LINK_DESC','main page');
define('WIKKA_NOT_AVAILABLE','n/a');
define('WIKKA_NOT_INSTALLED','not installed');
define('WIKKA_ANONYMOUS_USER','anonymous'); // 'name' of non-registered user
define('WIKKA_UNREGISTERED_USER','unregistered user'); // alternative for 'anonymous' @@@ make one string only?
define('WIKKA_ANONYMOUS_AUTHOR_CAPTION','('.WIKKA_UNREGISTERED_USER.')'); // @@@ or WIKKA_ANONYMOUS_USER
define('WIKKA_SAMPLE_WIKINAME','JohnDoe'); // must be a CamelCase name
define('WIKKA_HISTORY','history');
define('WIKKA_REVISIONS','revisions');
define('WIKKA_REV_WHEN_BY_WHO','%1$s by %2$s'); // %1$s - timestamp; %2$s - user name
define('WIKKA_NO_PAGES_FOUND','No pages found.');
define('WIKKA_PAGE_OWNER','Owner: %s'); // %s - page owner name or link
define('WIKKA_COMMENT_AUTHOR_DIVIDER',', comment by '); //TODo check if we can construct a single phrase here
define('WIKKA_PAGE_EDIT_LINK_DESC','edit');
define('WIKKA_PAGE_CREATE_LINK_DESC','create');
define('WIKKA_PAGE_EDIT_LINK_TITLE','Click to edit %s'); // %s page name @@@ 'Edit %s'
define('LINKING_PAGES_LINK_TITLE','Display a list of pages linking to %s'); // %s page name
define('WIKKA_JRE_LINK_DESC','Java Runtime Environment');
define('WIKKA_NOTE','NOTE:');
define('WIKKA_JAVA_PLUGIN_NEEDED','Java 1.4.1 (or later) Plug-in is needed to run this applet,');
/**#@-*/


/*  ------------------ CORE ------------------  */

/**#@+
 * Language constant for the core {@link wikka.php wikka} program 
 */
// wikka
define('ERROR_WAKKA_LIBRARY_MISSING','The necessary file "libs/Wakka.class.php" could not be found. To run Wikka, please make sure the file exists and is placed in the right directory!');
define('ERROR_WRONG_PHP_VERSION','$_REQUEST[] not found. Wakka requires PHP 4.1.0 or higher!'); //TODO remove referral to PHP internals; refer only to required version
define('ERROR_SETUP_FILE_MISSING','A file of the installer / upgrader was not found. Please install Wikka again!');
define('ERROR_SETUP_HEADER_MISSING','The file "setup/header.php" was not found. Please install Wikka again!');
define('ERROR_SETUP_FOOTER_MISSING','The file "setup/footer.php" was not found. Please install Wikka again!');
define('ERROR_NO_DB_ACCESS','Error: Unable to connect to the MySQL database.'); //TODO Don't mention DB engine JW
define('STATUS_WIKI_UNAVAILABLE','The wiki is currently unavailable.');
define('STATUS_WIKI_UPGRADE_NOTICE','This site is currently being upgraded. Please try again later.');
define('PAGE_GENERATION_TIME','Page was generated in %.4f seconds'); // %.4f - page generation time
/**#@-*/
 

/*  ------------------ ACTIONS  ------------------  */

/**#@+
 * Language constant used by the {@link calendar.php calendar} action
 */
// calendar
define('FMT_SUMMARY','Calendar for %s');					
define('TODAY','today');
/**#@-*/

/**#@+
 * Language constant used by the {@link category.php category} action
 */
// category
define('ERR_NO_PAGES','Sorry, No items found for %s');
define('PAGES_BELONGING_TO','The following %1$d page(s) belong to %2$s'); // %1$d number found; %2$s category 
/**#@-*/

/**#@+
 * Language constant used by the {@link color.php color} action
 */
// color
define('ERROR_NO_TEXT_GIVEN','There is no text to highlight!');
define('ERROR_NO_COLOR_SPECIFIED','Sorry, but you did not specify a color for highlighting!');
/**#@-*/

/**#@+
 * Language constant used by the {@link contact.php contact} action
 */
// contact
define('SEND_FEEDBACK_LINK_TITLE','Send us your feedback');
define('SEND_FEEDBACK_LINK_TEXT','Contact');
/**#@-*/

/**#@+
 * Language constant used by the {@link countowned.php countowned} action
 */
// countowned
define('DISPLAY_MYPAGES_LINK_TITLE','Display a list of the pages you currently own');
/**#@-*/

/**#@+
 * Language constant used by the {@link countpages.php countpages} action
 */
// countpages
define('INDEX_LINK_TITLE','Display an alphabetical page index'); 
/**#@-*/

/**#@+
 * Language constant used by the {@link emailpassword.php emailpassword} action
 */
// emailpassword
define('PW_FORGOTTEN_HEADING','Password reminder');
define('PW_CHK_SENT','A password reminder has been sent to %s\'s registered email address.'); // %s - username
define('PW_FORGOTTEN_MAIL','Hello, %1$s\n\n\nSomeone requested that we send to this email address a password reminder to login at %2$s. If you did not request this reminder, disregard this email. -- No action is necessary. -- Your password will stay the same.\n\nYour wikiname: %1$s \nPassword reminder: %3$s \nURL: %4$s \n\nDo not forget to change the password immediately after logging in.'); // %1$s - username; %2$s - wiki name; %3$s - md5 sum of pw; %4$s - login url of the wiki  
define('PW_FORGOTTEN_MAIL_REF','Password reminder for %s'); // %s - wiki name
define('PW_FORM_TEXT','Enter your WikiName and a password reminder will be sent to your registered email address.');
define('PW_FORM_FIELDSET_LEGEND','Your WikiName:');
define('ERROR_UNKNOWN_USER','You have entered a non-existent user!');
define('ERROR_MAIL_NOT_SENT','An error occurred while trying to send the password. Outgoing mail might be disabled. Please contact your server administrator.');
define('BUTTON_SEND_PW','Send reminder');
define('USERSETTINGS_REF','Return to the %s page.'); // %s - UserSettings link
/**#@-*/

/**#@+
 * Language constant used by the {@link feedback.php feedback} action
 */
// feedback
define('FEEDBACK_FORM_CAPTION','Fill in the form below to send us your comments:');
define('FEEDBACK_NAME_LABEL','Name:');
define('FEEDBACK_EMAIL_LABEL','Email:');
define('FEEDBACK_COMMENT_LABEL','Comments:');
define('ERROR_NO_NAME','Please enter your name');
define('ERROR_NO_EMAIL','Please enter a valid email address');
define('ERROR_NO_TXT','Please enter some text');
define('FEEDBACK_SUBJECT','Feedback from %s'); // %s name of the wiki
define('FEEDBACK_SENT','Thanks for your interest! Your feedback has been sent to %s'); // %s - Admin email link (wiki format)
define('MAIN_PAGE_REF','Return to the %s'); // %s - main page link (wiki format)
define('FEEDBACK_SEND_BUTTON','Send');
/**#@-*/

/**#@+
 * Language constant used by the {@link files.php files} action
 */
// files
define('ERROR_UPLOAD_DIRECTORY_NOT_WRITABLE','Please make sure that the server has write access to a folder named %s.'); // %s Upload folder ref #89
define('ERROR_UPLOAD_DIRECTORY_NOT_READABLE','Please make sure that the server has read access to a folder named %s.'); // %s Upload folder ref #89
define('ERROR_NONEXISTENT_FILE','Sorry, a file named %s does not exist.'); // %s - file name ref
define('ERROR_FILE_UPLOAD_INCOMPLETE','File upload incomplete! Please try again.');
define('ERROR_UPLOADING_FILE','There was an error uploading your file');
define('ERROR_FILE_ALREADY_EXISTS','Sorry, a file named %s already exists.'); // %s - file name ref
define('ERROR_EXTENSION_NOT_ALLOWED','Sorry, files with this extension are not allowed.');
define('ERROR_FILE_TOO_BIG','Attempted file upload was too big. Maximum allowed size is %s.'); // %s - allowed filesize 
define('ERROR_NO_FILE_SELECTED','No file selected.'); 
define('ERROR_FILE_UPLOAD_IMPOSSIBLE','File upload impossible due to misconfigured server.');
define('FILE_UPLOAD_SUCCESSFUL','File was successfully uploaded.');
define('FILE_TABLE_CAPTION','Attachments');
define('FILE_TABLE_HEADER_NAME','File');
define('FILE_TABLE_HEADER_SIZE','Size');
define('FILE_TABLE_HEADER_DATE','Last modified');
define('FILE_UPLOAD_FORM_LABEL','Add new attachment:');
define('DOWNLOAD_LINK_TITLE','Download %s'); // %s - file name
define('DELETE_LINK_TITLE','Remove %s'); // %s - file name
define('NO_ATTACHMENTS','This page contains no attachment.');
/**#@-*/

/**#@+
 * Language constant used by the {@link footer.php footer} action
 */
// footer
define('FOOTER_PAGE_EDIT_LINK_DESC','Edit page');
define('PAGE_HISTORY_LINK_TITLE','Click to view recent edits to this page'); // @@@ TODO 'View recent edits to this page'
define('PAGE_HISTORY_LINK_DESC','Page History');
define('PAGE_REVISION_LINK_TITLE','Click to view recent revisions list for this page'); // @@@ TODO 'View recent revisions list for this page' 
define('PAGE_REVISION_XML_LINK_TITLE','Click to view recent revisions list for this page'); // @@@ TODO 'View recent revisions list for this page'
define('PAGE_ACLS_EDIT_LINK_DESC','Edit ACLs');
define('PAGE_ACLS_EDIT_ADMIN_LINK_DESC','('.PAGE_ACLS_EDIT_LINK_DESC.')');
define('PUBLIC_PAGE','Public page');
define('USER_IS_OWNER','You own this page.');
define('NO_OWNER','Nobody');
define('TAKE_OWNERSHIP','Take Ownership');
define('REFERRERS_LINK_TITLE','Click to view a list of URLs referring to this page'); // @@@ TODO 'View a list of URLs referring to this page'
define('REFERRERS_LINK_DESC','Referrers');
define('QUERY_LOG','Query log:');
define('SEARCH_LABEL','Search:');
/**#@-*/

/**#@+
 * Language constant used by the {@link googleform.php googleform} action
 */
// googleform
define('GOOGLE_BUTTON','Google');
/**#@-*/

/**#@+
 * Language constant used by the {@link header.php header} action
 */
// header
define('GENERIC_DOCTITLE','%1$s: %2$s');	// %1$s - wiki name; %2$s - page title
define('RSS_REVISIONS_TITLE','%1$s: revisions for %2$s');	// %1$s - wiki name; %2$s - current page name
define('RSS_RECENTCHANGES_TITLE','%s: recently edited pages');	// %s - wiki name
define('YOU_ARE','You are %s'); // %s - name / ip of the user.
/**#@-*/

/**#@+
 * Language constant used by the {@link include.php include} action
 */
// include
define('ERROR_CIRCULAR_REFERENCE','Circular reference detected!');
/**#@-*/

/**#@+
 * Language constant used by the {@link lastedit.php lastedit} action
 */
// lastedit
define('LASTEDIT_DESC','Last edited by %s'); // %s user name
define('LASTEDIT_DIFF_LINK_TITLE','Show differences from last revision');
/**#@-*/

/**#@+
 * Language constant used by the {@link lastusers.php lastusers} action
 */
// lastusers
define('NAME','Name');
define('OWNED_PAGES','Owned Pages');
define('SIGNUP_DATE_TIME','Signup Date/Time');
/**#@-*/

/**#@+
 * Language constant used by the {@link mindmap.php mindmap} action
 */
// mindmap
define('MM_JRE_INSTALL_REQ','Please install a %s on your computer.'); // %s - JRE install link
define('MM_DOWNLOAD_LINK_DESC','Download this mind map');
define('MM_EDIT','Use %s to edit it'); // %s - link to freemind project
define('MM_FULLSCREEN_LINK_DESC','Open fullscreen');
define('ERROR_INVALID_MM_SYNTAX','Error: Invalid MindMap action syntax.');
define('PROPER_USAGE_MM_SYNTAX','Proper usage: %1$s or %2$s'); // %1$s - syntax sample 1; %2$s - syntax sample 2
/**#@-*/

/**#@+
 * Language constant used by the {@link mychanges.php mychanges} action
 */
// mychanges
define('NO_PAGES_EDITED','You have not edited any pages yet.');
define('MYCHANGES_ALPHA_LIST', "This is a list of pages you've edited, along with the time of your last change.");
define('MYCHANGES_DATE_LIST', "This is a list of pages you've edited, ordered by the time of your last change.");
define('ORDER_DATE','order by date');
define('ORDER_ALPHA','order alphabetically');
define('MYCHANGES_NOT_LOGGED_IN', "You're not logged in, thus the list of pages you've edited couldn't be retrieved.");
/**#@-*/

/**#@+
 * Language constant used by the {@link mypages.php mypages} action
 */
// mypages
define('OWNED_PAGES_TXT','This is the list of pages you own.'); 
define('OWNED_NO_PAGES','You don\'t own any pages.');
define('OWNED_NOT_LOGGED_IN', "You're not logged in, thus the list of your pages couldn't be retrieved.");
/**#@-*/

/**#@+
 * Language constant used by the {@link newpage.php newpage} action
 */
// newpage
define('ERROR_INVALID_PAGE_NAME','The page name %s is invalid. Valid page names must start with a capital letter, contain only letters and numbers, and be in CamelCase format.'); // %s - page name
define('NEWPAGE_CREATE_BUTTON','Create and Edit');
/**#@-*/

/**#@+
 * Language constant used by the {@link orphanedpages.php orphanedpages} action
 */
// orphanedpages
define('NO_ORPHANED_PAGES','No orphaned pages. Good!');

/**#@+
 * Language constant used by the {@link ownedpages.php ownedpages} action
 */
// ownedpages
define('OWNEDPAGES_COUNTS','You own %1$s pages out of the %2$s pages on this Wiki.'); // %1$s - number of pages owned; %2$s - total number of pages 
define('OWNEDPAGES_PERCENTAGE','That means you own %s of the total.'); // %s percentage of pages owned
/**#@-*/

/**#@+
 * Language constant used by the {@link pageindex.php pageindex} action
 */
// pageindex
define('PAGEINDEX_HEADING','Page Index');
define('PAGEINDEX_CAPTION','This is an alphabetical list of pages you can read on this server.');
define('PAGEINDEX_OWNED_PAGES_CAPTION','Items marked with a * indicate pages that you own.');
define('PAGEINDEX_ALL_PAGES','All');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges.php recentchanges} action
 */
// recentchanges
define('RECENTCHANGES_HEADING','Recently changed pages');
define('REVISIONS_LINK_TITLE','View recent revisions list for %s'); // %s - page name
define('HISTORY_LINK_TITLE','View edit history of %s'); // %s - page name
define('WIKIPING_ENABLED','WikiPing enabled: Changes on this wiki are broadcast to %s'); // %s - link to wikiping server
define('RECENTCHANGES_NONE_FOUND','There are no recently changed pages.');
define('RECENTCHANGES_NONE_ACCESSIBLE','There are no recently changed pages you have access to.');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentcomments.php recentcomments} action
 */
// recentcomments
define('RECENTCOMMENTS_HEADING','Recent comments');
define('RECENTCOMMENTS_TIMESTAMP_CAPTION','(%s)'); // %s - timestamp
define('RECENTCOMMENTS_NONE_FOUND','There are no recent comments.');
define('RECENTCOMMENTS_NONE_ACCESSIBLE','There are no recent comments you have access to.');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentlycommented.php recentlycommented} action
 */
// recentlycommented 
define('RECENTLYCOMMENTED_HEADING','Recently commented pages');
define('RECENTLYCOMMENTED_NONE_FOUND','There are no recently commented pages.');
define('RECENTLYCOMMENTED_NONE_ACCESSIBLE','There are no recently commented pages you have access to.');
/**#@-*/

/**#@+
 * Language constant used by the {@link system.php system} action
 */
// system
define('SYSTEM_HOST_CAPTION','(%s)'); // %s - host name
define('WIKKA_STATUS_NOT_AVAILABLE', 'n/a'); 
/**#@-*/

/**#@+
 * Language constant shared by the {@link textsearch.php textsearch} and {@link textsearchexpanded.php textsearchexpanded} actions
 */
// textsearch & textsearchexpanded
define('SEARCH_FOR','Search for');
define('SEARCH_ZERO_MATCH','No matches');
define('SEARCH_ONE_MATCH','One match found');
define('SEARCH_N_MATCH','%d matches found'); // %d - number of hits
define('SEARCH_RESULTS','Search results:');
define('SEARCH_NOT_SURE_CHOICE','Not sure which page to choose?');
define('SEARCH_EXPANDED_LINK_DESC','Expanded Text Search'); // search link description
define('SEARCH_TRY_EXPANDED','Try the %s which shows surrounding text.'); // %s expanded search link
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
define('SEARCH_TIPS','Search Tips:');
define('SEARCH_WORD_1','apple');
define('SEARCH_WORD_2','banana');
define('SEARCH_WORD_3','juice');
define('SEARCH_WORD_4','macintosh');
define('SEARCH_WORD_5','some');
define('SEARCH_WORD_6','words');
define('SEARCH_PHRASE',sprintf('%s %s',SEARCH_WORD_5,SEARCH_WORD_6));
define('SEARCH_TARGET_1','Find pages that contain at least one of the two words.');
define('SEARCH_TARGET_2','Find pages that contain both words.');
define('SEARCH_TARGET_3',sprintf("Find pages that contain the word '%1\$s' but not '%2\$s'.",SEARCH_WORD_1,SEARCH_WORD_4));
define('SEARCH_TARGET_4',"Find pages that contain words such as 'apple','apples','applesauce', or 'applet'."); // make sure target words all *start* with SEARCH_WORD_1
define('SEARCH_TARGET_5',sprintf("Find pages that contain the exact phrase '%1\$s' (for example, pages that contain '%1\$s of wisdom' but not '%2\$s noise %3\$s').",SEARCH_PHRASE,SEARCH_WORD_5,SEARCH_WORD_6));
/**#@-*/

/**#@+
 * Language constant used by the {@link usersettings.php usersettings} action
 */
// usersettings
define('ERROR_EMPTY_USERNAME','Please fill in your user name.');
define('ERROR_NONEXISTENT_USERNAME','Sorry, this user name doesn\'t exist.'); // @@@ too specific
define('ERROR_RESERVED_PAGENAME','Sorry, this name is reserved for a page. Please choose a different name.');
define('ERROR_WIKINAME','Username must be formatted as a %1$s, e.g. %2$s.'); // %1$s - identifier WikiName; %2$s - sample WikiName
define('ERROR_EMPTY_EMAIL_ADDRESS','Please specify an email address.');
define('ERROR_INVALID_EMAIL_ADDRESS','That doesn\'t quite look like an email address.');
define('ERROR_INVALID_PASSWORD','Sorry, you entered the wrong password.');	// @@@ too specific
define('ERROR_INVALID_HASH','Sorry, you entered a wrong password reminder.');
define('ERROR_INVALID_OLD_PASSWORD','The old password you entered is wrong.');
define('ERROR_EMPTY_PASSWORD','Please fill in a password.');
define('ERROR_EMPTY_PASSWORD_OR_HASH','Please fill your password or password reminder.');
define('ERROR_EMPTY_CONFIRMATION_PASSWORD','Please confirm your password in order to register a new account.');
define('ERROR_EMPTY_NEW_CONFIRMATION_PASSWORD','Please confirm your new password in order to update your account.');
define('ERROR_EMPTY_NEW_PASSWORD','You must also fill in a new password.');
define('ERROR_PASSWORD_MATCH','Passwords don\'t match.');
define('ERROR_PASSWORD_NO_BLANK','Sorry, blanks are not permitted in the password.');
define('ERROR_PASSWORD_TOO_SHORT','Sorry, the password must contain at least %d characters.'); // %d - minimum password length
define('ERROR_INVALID_INVITATION_CODE','This is a private wiki, only invited members can register an account! Please contact the administrator of this website for an invitation code.');
define('ERROR_INVALID_REVISION_DISPLAY_LIMIT','The number of page revisions should not exceed %d.'); // %d - maximum revisions to view
define('ERROR_INVALID_RECENTCHANGES_DISPLAY_LIMIT','The number of recently changed pages should not exceed %d.'); // %d - maximum changed pages to view
// - success messages
define('USER_LOGGED_OUT_SUCCESS','You have successfully logged out.');
define('USER_REGISTERED_SUCCESS','You have successfully registered!');
define('USER_SETTINGS_STORED_SUCCESS','User settings stored!');
define('USER_PASSWORD_CHANGED_SUCCESS','Password successfully changed!');
// - captions
define('NEW_USER_REGISTER_CAPTION','Fields required if you are signing up as a new user:');
define('REGISTERED_USER_LOGIN_CAPTION','If you\'re already a registered user, log in here:');
define('RETRIEVE_PASSWORD_LINK_DESC','here'); // TODO rephrase with functional name and avoid 'here'
define('RETRIEVE_PASSWORD_CAPTION1','If you need a password reminder, click %s.'); // %s PasswordForgotten link 
define('RETRIEVE_PASSWORD_CAPTION2','You can login here using your password reminder.');
define('USER_LOGGED_IN_AS_CAPTION','You are logged in as %s'); // %s user name
// - form legends
define('USER_ACCOUNT_LEGEND','Your account');
define('USER_SETTINGS_LEGEND','Settings');
define('LOGIN_REGISTER_LEGEND','Login/Register');
define('LOGIN_LEGEND','Login');
#define('REGISTER_LEGEND','Register'); // @@@ TODO to be used later for register-action
define('CHANGE_PASSWORD_LEGEND','Change your password');
define('RETRIEVE_PASSWORD_LEGEND','Password forgotten');
// - form field labels (should end in ':' _unless_ it's a checkbox or radio button option)
define('DONT_GO_BACK_LABEL','Don\'t go back to %s'); // %s page user came from
define('USER_EMAIL_LABEL','Your email address:');
define('DOUBLECLICK_LABEL','Doubleclick editing:');
define('SHOW_COMMENTS_LABEL','Show comments by default:');
define('RECENTCHANGES_DISPLAY_LIMIT_LABEL','RecentChanges display limit:');
define('PAGEREVISION_LIST_LIMIT_LABEL','Page revisions list limit:');
define('NEW_PASSWORD_LABEL','Your new password:');
define('NEW_PASSWORD_CONFIRM_LABEL','Confirm new password:');
define('NO_REGISTRATION','Registration on this wiki is disabled.');
define('PASSWORD_LABEL','Password (%s+ chars):'); //
define('CONFIRM_PASSWORD_LABEL','Confirm password:');
define('TEMP_PASSWORD_LABEL','Password reminder:');
define('INVITATION_CODE_SHORT','Invitation Code');
define('INVITATION_CODE_LONG','In order to register, you must fill in the invitation code sent by this website\'s administrator.');
define('INVITATION_CODE_LABEL','Your %s:'); // %s - expanded short invitation code prompt
define('WIKINAME_SHORT','WikiName');
define('WIKINAME_LONG',sprintf('A WikiName is formed by two or more capitalized words without space, e.g. %s',WIKKA_SAMPLE_WIKINAME));
define('WIKINAME_LABEL','Your %s:'); // %s - expanded short wiki name prompt
// - form options
define('CURRENT_PASSWORD_OPTION','Your current password');
define('PASSWORD_REMINDER_OPTION','Password reminder');
// - form buttons
define('UPDATE_SETTINGS_BUTTON','Update Settings');
define('LOGIN_BUTTON','Login');
define('LOGOUT_BUTTON','Logout');
define('CHANGE_PASSWORD_BUTTON','Change password');
define('REGISTER_BUTTON','Register');
/**#@-*/

/**#@+
 * Language constant used by the {@link wantedpages.php wantedpages} action
 */
// wantedpages
define('SORTING_LEGEND','Sorting ...');
define('SORTING_NUMBER_LABEL','Sorting #%d:');
define('SORTING_DESC_LABEL','desc');
define('OK_BUTTON','   OK   ');
define('NO_WANTED_PAGES','No wanted pages. Good!');
/**#@-*/


/* ------------------ 3RD PARTY ------------------ */

/**#@+
 * Language constant used by the {@link fullscreen.php fullscreen} 3rd party MindMap display utility
 */
// fullscreen
define('CLOSE_WINDOW','Close Window');
define('MM_GET_JAVA_PLUGIN_LINK_DESC','get the latest Java Plug-in here'); // used in MM_GET_JAVA_PLUGIN
define('MM_GET_JAVA_PLUGIN','so if it does not work, %s.'); // %s - plugin download link
/**#@-*/


/* ------------------ FORMATTERS ------------------ */

/**#@+
 * Language constant used by the {@link wakka.php wakka} formatter
 */
// wakka
define('GRABCODE_BUTTON','Grab');
define('GRABCODE_BUTTON_TITLE','Download %s'); // %s download filename
/**#@-*/


/* ------------------ HANDLERS (PAGE) ------------------ */

/**#@+
 * Language constant used by the {@link acls.php acls} (page) handler
 */
// acls
// TODO: 'translate' DB value '(Public)' when displaying it!
define('ACLS_UPDATED','Access control lists updated.');
define('NO_PAGE_OWNER','(Nobody)');
define('NOT_PAGE_OWNER','You are not the owner of this page.');
define('PAGE_OWNERSHIP_CHANGED','Ownership changed to %s'); // %s - name of new owner
define('ACLS_LEGEND','Access Control Lists for %s'); // %s - name of current page
define('ACLS_READ_LABEL','Read ACL:');
define('ACLS_WRITE_LABEL','Write ACL:');
define('ACLS_COMMENT_LABEL','Comment ACL:');
define('SET_OWNER_LABEL','Set Page Owner:');
define('SET_OWNER_CURRENT_OPTION','(Current Owner)');
define('SET_OWNER_PUBLIC_OPTION','(Public)'); // actual DB value will remain '(Public)' even if this option text is translated!
define('SET_NO_OWNER_OPTION','(Nobody - Set free)');
define('ACLS_STORE_BUTTON','Store ACLs');
define('CANCEL_BUTTON','Cancel');
// - syntax
define('ACLS_SYNTAX_HEADING','Syntax:');
define('ACLS_EVERYONE','Everyone');
define('ACLS_REGISTERED_USERS','Registered users');
define('ACLS_NONE_BUT_ADMINS','No one (except admins)');
define('ACLS_ANON_ONLY','Anonymous users only');
define('ACLS_LIST_USERNAMES','the user called %s; enter as many users as you want, one per line'); // %s - sample user name
define('ACLS_NEGATION','Any of these items can be negated with a %s:'); // %s - 'negation' mark
define('ACLS_DENY_USER_ACCESS','%s will be denied access'); // %s - sample user name
define('ACLS_AFTER','after');
define('ACLS_TESTING_ORDER1','ACLs are tested in the order they are specified:');
define('ACLS_TESTING_ORDER2','So be sure to specify %1$s on a separate line %2$s negating any users, not before.'); // %1$s - 'all' mark; %2$s - emphasised 'after' 
/**#@-*/

/**#@+
 * Language constant used by the {@link backlinks.php backlinks} (page) handler
 */
// backlinks
define('BACKLINKS_HEADING','Pages linking to %s');
define('BACKLINKS_NO_PAGES','There are no backlinks to this page.');
/**#@-*/

/**#@+
 * Language constant used by the {@link claim.php claim} (page) handler
 */
// claim
define('USER_IS_NOW_OWNER','You are now the owner of this page.');
/**#@-*/

/**#@+
 * Language constant used by the {@link clone.php clone} (page) handler
 */
// clone
define('ERROR_ACL_WRITE','Sorry! You don\'t have write-access to %s');
define('ERROR_PAGE_ALREADY_EXIST','Sorry, the destination page already exists');
define('CLONE_VALID_TARGET','Please fill in a valid target page name and an (optional) edit note.');
define('CLONE_LEGEND','Clone %s'); // %s source page name
define('CLONED_FROM','Cloned from %s'); // %s source page name
define('CLONE_SUCCESS','%s was succesfully created!'); // %s new page name
define('CLONE_X_TO_LABEL','Clone as:');
define('CLONE_EDIT_NOTE_LABEL','Edit note:');
define('CLONE_EDIT_OPTION_LABEL',' Edit after creation');
define('CLONE_ACL_OPTION_LABEL',' Clone ACL');
define('CLONE_BUTTON','Clone');
/**#@-*/

/**#@+
 * Language constant used by the {@link delete.php delete} (page) handler
 */
// delete
define('ERROR_NO_PAGE_DEL_ACCESS','You are not allowed to delete this page.');
define('PAGE_DELETION_HEADER','Delete %s'); // %s - name of the page
define('PAGE_DELETION_SUCCESS','Page has been deleted!');
define('PAGE_DELETION_CAPTION','Completely delete this page, including all comments?');
define('PAGE_DELETION_DELETE_BUTTON','Delete Page');
define('PAGE_DELETION_CANCEL_BUTTON','Cancel');
/**#@-*/

/**#@+
 * Language constant used by the {@link diff.php diff} (page) handler
 */
// diff
define('ERROR_DIFF_LIBRARY_MISSING','The necessary file "libs'.DIRECTORY_SEPARATOR.'diff.lib.php" could not be found. Please make sure the file exists and is placed in the right directory!'); //TODO 'Please make sure' should be 'please inform WikiAdmin' - end user can't "make sure"
define('ERROR_BAD_PARAMETERS','There is something wrong with parameters you supplied, it\'s very likely that one of the versions you want to compare has been deleted.');
define('DIFF_ADDITIONS_HEADER','Additions:');
define('DIFF_DELETIONS_HEADER','Deletions:');
define('DIFF_NO_DIFFERENCES','No Differences');
define('DIFF_FAST_COMPARISON_HEADER','Comparison of %1$s &amp; %2$s'); // %1$s - link to page A; %2$s - link to page B
define('DIFF_COMPARISON_HEADER','Comparing %2$s to %1$s'); // %1$s - link to page A; %2$s - link to page B (yes, they're swapped!)
define('DIFF_SAMPLE_ADDITION','addition');
define('DIFF_SAMPLE_DELETION','deletion');
define('HIGHLIGHTING_LEGEND','Highlighting Guide: %1$s %2$s'); // %1$s - sample added text; %2$s - sample deleted text
/**#@-*/

/**#@+
 * Language constant used by the {@link edit.php edit} (page) handler
 */
// edit
define('ERROR_OVERWRITE_ALERT1','OVERWRITE ALERT: This page was modified by someone else while you were editing it.');
define('ERROR_OVERWRITE_ALERT2','Please copy your changes and re-edit this page.');
define('ERROR_MISSING_EDIT_NOTE','MISSING EDIT NOTE: Please fill in an edit note!');
define('ERROR_TAG_TOO_LONG','Tag too long! %d characters max.'); // %d - maximum page name length // TODO: use 'Page name' instead of 'Tag'
define('ERROR_NO_WRITE_ACCESS','You don\'t have write access to this page. You might need to register an account to be able to edit this page.');
define('EDIT_STORE_PAGE_LEGEND','Store page');
define('EDIT_PREVIEW_HEADER','Preview');
define('EDIT_NOTE_LABEL','Please add a note on your edit'); // label after field, so no colon!
define('MESSAGE_AUTO_RESIZE','Clicking on %s will automatically truncate the tag to the correct size'); // %s - rename button text // TODO: use 'page name' instead of 'tag'
define('EDIT_PREVIEW_BUTTON','Preview');
define('EDIT_STORE_BUTTON','Store');
define('EDIT_REEDIT_BUTTON','Re-edit');
define('EDIT_CANCEL_BUTTON','Cancel');
define('EDIT_RENAME_BUTTON','Rename');
define('ACCESSKEY_PREVIEW','p'); // ideally, should match EDIT_PREVIEW_BUTTON
define('ACCESSKEY_STORE','s'); // ideally, should match EDIT_STORE_BUTTON
define('ACCESSKEY_REEDIT','r'); // ideally, should match EDIT_REEDIT_BUTTON
define('SHOWCODE_LINK','View formatting code for this page');
define('SHOWCODE_LINK_TITLE','Click to view page formatting code'); // @@@ TODO 'View page formatting code'
define('EDIT_COMMENT_TIMESTAMP_CAPTION','(%s)'); // %s timestamp
/**#@-*/

/**#@+
 * Language constant used by the {@link grabcode.php grabcode} (page) handler
 */
// grabcode
define('ERROR_NO_CODE','Sorry, there is no code to download.');
/**#@-*/

/**#@+
 * Language constant used by the {@link history.php history} (page) handler
 */
// history
define('EDITED_ON','Edited on %1$s by %2$s'); // %1$s - time; %2$s - user name
define('HISTORY_PAGE_VIEW','Page view:');
define('OLDEST_VERSION_EDITED_ON_BY','Oldest known version of this page was edited on %1$s by %2$s'); // %1$s - time; %2$s - user name
define('MOST_RECENT_EDIT','Most recent edit on %1$s by %2$s'); // %1$s - time; %2$s - user name
define('HISTORY_MORE_LINK_DESC','here'); // used for alternative history link in HISTORY_MORE
define('HISTORY_MORE','Full history for this page cannot be displayed within a single page, click %s to view more.'); // %s alternative history link # @@@ TODO avoid using 'here'
/**#@-*/

/**#@+
 * Language constant shared by the {@link processcomment.php processcomment} and {@link show.php show} (page) handlers
 */
// processcomment & show
// - comment buttons
define('COMMENT_DELETE_BUTTON','Delete Comment');
define('COMMENT_REPLY_BUTTON','Reply to Comment');
define('COMMENT_ADD_BUTTON','Add Comment');
define('COMMENT_NEW_BUTTON','New Comment');
/**#@-*/

/**#@+
 * Language constant used by the {@link processcomment.php processcomment} (page) handler
 */
// processcomment
define('ERROR_NO_COMMENT_DEL_ACCESS','Sorry, you\'re not allowed to delete this comment!');
define('ERROR_NO_COMMENT_WRITE_ACCESS','Sorry, you\'re not allowed to post comments to this page');
define('ERROR_EMPTY_COMMENT','Comment body was empty -- not saved!');
define('ADD_COMMENT_LABEL','Add a comment to this page:');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges_simple.xml.mm.php recentchanges_simple.xml.mm} (page) handler
 */
// recentchanges_simple.xml.mm
define('FIRST_NODE_LABEL','Recent Changes');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges.xml.php recentchanges.xml} (page) handler
 */
// recentchanges.xml
define('RECENTCHANGES_DESC','Recent changes of %s'); // %s - page name
/**#@-*/

/**#@+
 * Language constant shared by the {@link referrers_sites.php referrers_sites}, {@link referrers.php referrers} and {@link review_blacklist.php review_blacklist} (page) handlers
 */
// referrers_sites + referrers + review_blacklist
define('REFERRERS_PURGE_24_HOURS','last 24 hours');
define('REFERRERS_PURGE_N_DAYS','last %d days'); // %d number of days
define('REFERRERS_NO_SPAM','Note to spammers: This page is not indexed by search engines, so don\'t waste your time.');
define('REFERRERS_DOMAINS_TO_WIKI_LINK_DESC','View global referring sites');
define('REFERRERS_DOMAINS_TO_PAGE_LINK_DESC','View referring sites for %s only'); // %s - page name
define('REFERRERS_URLS_TO_WIKI_LINK_DESC','View global referrers');
define('REFERRERS_URLS_TO_PAGE_LINK_DESC','View referrers for %s only'); // %s - page name
define('REFERRER_BLACKLIST_LINK_DESC','View referrer blacklist');
define('BLACKLIST_LINK_DESC','Blacklist');
define('NONE_CAPTION','None');
define('PLEASE_LOGIN_CAPTION','You need to login to see referring sites');
/**#@-*/

/**#@+
 * Language constant used by the {@link referrers_sites.php referrers_sites} (page) handler
 */
// referrers_sites
define('REFERRERS_URLS_LINK_DESC','see list of different URLs');
define('REFERRERS_DOMAINS_TO_WIKI','Domains/sites linking to this wiki (%s)'); // %s - link to referrers handler
define('REFERRERS_DOMAINS_TO_PAGE','Domains/sites linking to %1$s %2$s (%3$s)'); // %1$s - page link; %2$s - purge time; %3$s - link to referrers handler
/**#@-*/

/**#@+
 * Language constant used by the {@link referrers.php referrers} (page) handler
 */
// referrers
define('REFERRERS_DOMAINS_LINK_DESC','see list of domains');
define('REFERRERS_URLS_TO_WIKI','External pages linking to this wiki (%s)'); // %s - link to referrers_sites handler
define('REFERRERS_URLS_TO_PAGE','External pages linking to %1$s %2$s (%3$s)'); // %1$s - page link; %2$s - purge time; %3$s - link to referrers_sites handler
/**#@-*/

/**#@+
 * Language constant used by the {@link review_blacklist.php review_blacklist} (page) handler
 */
// review_blacklist
define('BLACKLIST_HEADING','Referrer Blacklist');
define('BLACKLIST_REMOVE_LINK_DESC','Remove');
define('STATUS_BLACKLIST_EMPTY','Blacklist is empty.');
/**#@-*/

/**#@+
 * Language constant used by the {@link revisions.php revisions} (page) handler
 */
// revisions
define('REVISIONS_SIMPLE_DIFF','Simple Diff');
define('REVISIONS_MORE_CAPTION','There are more revisions that were not shown here, click the button labelled %s below to view these entries'); // %S - text of REVISIONS_MORE_BUTTON
define('REVISIONS_RETURN_TO_NODE_BUTTON','Return To Node / Cancel');
define('REVISIONS_SHOW_DIFFERENCES_BUTTON','Show Differences');
define('REVISIONS_MORE_BUTTON','Next...');
/**#@-*/

/**#@+
 * Language constant used by the {@link revisions.xml.php revisions.xml} (page) handler
 */
// revisions.xml
define('REVISIONS_EDITED_BY','Edited by %s'); // %s user name
define('HISTORY_REVISIONS_OF','History/revisions of %s'); // %s - page name
/**#@-*/

/**#@+
 * Language constant used by the {@link show.php show} (page) handler
 */
// show
define('SHOW_RE_EDIT_BUTTON','Re-edit this old revision');
define('SHOW_ASK_CREATE_PAGE_CAPTION','This page doesn\'t exist yet. Maybe you want to %s it?'); // %s - page create link
define('SHOW_OLD_REVISION_CAPTION','This is an old revision of %1$s from %2$s.'); // %1$s - page link; %2$s - timestamp
define('COMMENTS_CAPTION','Comments');
define('DISPLAY_COMMENTS_LABEL','Display comments: ');
define('DISPLAY_COMMENT_LINK_DESC','Display comment');
define('DISPLAY_COMMENTS_EARLIEST_LINK_DESC','Earliest first');
define('DISPLAY_COMMENTS_LATEST_LINK_DESC','Latest first');
define('DISPLAY_COMMENTS_THREADED_LINK_DESC','Threaded');
define('HIDE_COMMENTS_LINK_DESC','Hide comments/form');
define('STATUS_NO_COMMENTS','There are no comments on this page.');
define('STATUS_ONE_COMMENT','There is one comment on this page.');
define('STATUS_SOME_COMMENTS','There are %d comments on this page.'); // %d - number of comments
define('COMMENT_TIME_CAPTION','(%s)'); // %s comment time
/**#@-*/

/**#@+
 * Language constant used by the {@link showcode.php showcode} (page) handler
 */
// showcode
define('SOURCE_HEADING','Formatting code for %s'); // %s - page link
define('SHOW_RAW_LINK_DESC','show source only');
/**#@-*/

/* ------------------ LIBS ------------------*/

/**#@+
 * Language constant used by the {@link Wakka.class.php Wakka class} (the Wikka core containing most methods)
 */
// Wakka.class
define('QUERY_FAILED','Query failed:');
define('REDIR_DOCTITLE','Redirected to %s'); // %s - target page
define('REDIR_LINK_DESC','this link'); // used in REDIR_MANUAL_CAPTION
define('REDIR_MANUAL_CAPTION','If your browser does not redirect you, please follow %s'); // %s target page link
define('CREATE_THIS_PAGE_LINK_TITLE','Create this page');
define('ACTION_UNKNOWN_SPECCHARS','Unknown action; the action name must not contain special characters.');
define('ACTION_UNKNOWN','Unknown action "%s"'); // %s - action name
define('HANDLER_UNKNOWN','Sorry, %s is an unknown handler.'); // %s handler name
define('FORMATTER_UNKNOWN','Formatter "%s" not found'); // %s formatter name
/**#@-*/

/* ------------------ SETUP ------------------ */
/**#@+
 * Language constant used by the {@link default.php setup} program (several files)
 */
// @@@ later....
/**#@-*/

?>