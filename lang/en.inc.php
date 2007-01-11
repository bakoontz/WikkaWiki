<?php
/*
 * This file will hold all language strings for wikka.
 * 
 * @package		language
 * @version		$Id$
 * @todo		unify names
 * @todo		move the rest of the hardoced texts in here.
 */

if (!defined('FREEMIND_PROJECT_URL')) define('FREEMIND_PROJECT_URL', 'http://freemind.sourceforge.net/');
/**
 * Main.
 */
// wikka
define('ERROR_WAKKA_LIBRARY_MISSING','The necessary file "libs/Wakka.class.php" could not be found. To run Wikka, please make sure the file exists and is placed in the right directory!');
define('ERROR_WRONG_PHP_VERSION', '$_REQUEST[] not found. Wakka requires PHP 4.1.0 or higher!');
define('ERROR_SETUP_FILE_MISSING', 'A file of the installer/ upgrader was not found. Please install Wikka again!');
define('ERROR_SETUP_HEADER_MISSING', 'The file "setup/header.php" was not found. Please install Wikka again!');
define('ERROR_SETUP_FOOTER_MISSING', 'The file "setup/footer.php" was not found. Please install Wikka again!');
define('ERROR_NO_DB_ACCESS', 'The wiki is currently unavailable. <br /><br />Error: Unable to connect to the MySQL database.');
/**
 * Display page generation time in seconds with 4 decimals (%.4f)
 */
define('PAGE_GENERATION_TIME', 'Page was generated in %.4f seconds');
define('WIKI_UPGRADE_NOTICE', 'This site is currently being upgraded. Please try again later.');
 

/**
 * Actions.
 */
// calendar
define('FMT_SUMMARY', "Calendar for %s");					
define('TODAY', "today");

// category
define('ERR_NO_PAGES', 'Sorry, No items found for %s');
define('PAGES_BELONGING_TO', 'The following %d page(s) belong to %s');

// color
define('ERROR_NO_TEXT_GIVEN','There is no text to highlight!');
define('ERROR_NO_COLOR_SPECIFIED', 'Sorry, but you did not specify a color for highlighting!');

// contact
define('SEND_FEEDBACK_LINK_TITLE', 'Send us your feedback');
define('SEND_FEEDBACK_LINK_TEXT', 'Contact');

// countowned
define('DISPLAY_MYPAGES_LINK_TITLE', 'Display a list of the pages you currently own');

// countpages
define('INDEX_LINK_TITLE', 'Display an alphabetical page index'); 

// emailpassword

define('PW_FORGOTTEN_HEADING', '==== Password reminder ==== ');
define('PW_CHK_SENT', "A password reminder has been sent to %s's registered email address."); // %s - username
define('PW_FORGOTTEN_MAIL', "Hello, %1\$\n\n\nSomeone requested that we send to this email address a password reminder to login at %2\$s. If you did not request this reminder, disregard this email. -- No action is necessary. -- Your password will stay the same.\n\nYour wikiname: %1\$s \nPassword reminder: %3\$s \nURL: %4\$s \n\nDo not forget to change the password immediately after logging in."); // %1\$ - username; %2\$s - wiki name; %3\$s - md5 sum of pw; %4\$s - login url of the wiki  
define('PW_FORGOTTEN_MAIL_REF', 'Password reminder for %s'); // %s - wiki name
define('PW_FORM_TEXT', 'Enter your WikiName and a password reminder will be sent to your registered email address.');
define('ERROR_EMPTY_USER', 'Please fill in your username!');
define('ERROR_UNKNOWN_USER', 'You have entered a non-existent user!');
define('ERROR_MAIL_NOT_SENT', 'An error occured while trying to send the password. Outgoing mail might be disabled. Please contact your server administrator.');
define('BUTTON_SEND_PW_LABEL', 'Send reminder');
define('USERSETTINGS_LINK', 'Return to the [[UserSettings login]] screen.');

// feedback
define('FEEDBACK_FORM_LABEL', 'Fill in the form below to send us your comments:');
define('FEEDBACK_NAME_LABEL', 'Name:');
define('FEEDBACK_EMAIL_LABEL', 'Email:');
define('FEEDBACK_COMMENT_LABEL', 'Comments:');
define('ERROR_NO_NAME', 'Please enter your name');
define('ERROR_NO_EMAIL', 'Please enter a valid email address');
define('ERROR_NO_TXT', 'Please enter some text');
define('FEEDBACK_SENT', 'Thanks for your interest! Your feedback has been sent to [[%s]] ---'); // %s - Admin name
define('MAIN_PAGE_LINK', 'Return to the [[%s main page]]'); // %s - Wikiname of main page
define('BUTTON_SEND', 'Send');

// files
define('ERROR_UPLOAD_DIRECTORY_NOT_WRITABLE', 'Please make sure that the server has write access to a folder named <tt>./%s</tt>.');
define('ERROR_UPLOAD_DIRECTORY_NOT_READABLE', 'Please make sure that the server has read access to a folder named <tt>./%s</tt>.');
define('ERROR_INEXISTENT_FILE', 'Sorry, a file named <tt>%s</tt> does not exist.'); // %s - name of the file
define('ERROR_FILE_UPLOAD_INCOMPLETE', 'File upload incomplete! Please try again.');
define('ERROR_UPLOADING_FILE', 'There was an error uploading your file');
define('FILE_UPLOAD_SUCCESSFUL','File was successfully uploaded.');
define('ERROR_FILE_ALREADY_EXISTS', 'Sorry, a file named <tt>%s</tt> already exists.'); // %s - name of the file
define('ERROR_EXTENSION_NOT_ALLOWED', 'Sorry, files with this extension are not allowed.');
define('ERROR_FILE_TOO_BIG','Attempted file upload was too big. Maximum allowed size is %s.'); // %s - allowed filesize 
define('ERROR_NO_FILE_SELECTED','No file selected.'); 
define('ERROR_FILE_UPLOAD_IMPOSSIBLE', 'File upload impossible due to misconfigured server.');
define('FILE_TABLE_CAPTION', 'Attachments');
define('FILE_TABLE_HEADER_NAME', 'File');
define('FILE_TABLE_HEADER_SIZE', 'Size');
define('FILE_TABLE_HEADER_DATE', 'Last modified');
define('FILE_UPLOAD_FORM_LABEL', 'Add new attachment:');
define('DOWNLOAD_LINK_TITLE', 'Download %s'); // %s - file name
define('DELETE_LINK_TITLE', 'Remove %s'); // %s - file name
define('NO_ATTACHMENTS', 'This page contains no attachment.');

// footer
define('PAGE_EDIT_LINK_TITLE', 'Click to edit this page');
define('PAGE_EDIT_LINK_TEXT', 'Edit page');
define('PAGE_HISTORY_LINK_TITLE', 'Click to view recent edits to this page');
define('PAGE_HISTORY_LINK_TEXT', 'Page History');
define('PAGE_REVISION_LINK_TITLE', 'Click to view recent revisions list for this page');
define('PAGE_REVISION_LINK_XML_TITLE', 'Click to view recent revisions list for this page');
define('PAGE_ACLS_EDIT_LINK_TEXT', 'Edit ACLs');
define('PAGE_ACLS_EDIT_LINK_TEXT_ADMIN', '(Edit ACLs)');
define('PUBLIC_PAGE', 'Public page');
define('OWNER_LABEL', 'Owner:');
define('USER_IS_OWNER', 'You own this page.');
define('NO_OWNER', 'Nobody');
define('TAKE_OWNERSHIP', 'Take Ownership');
define('REFERRER_LINK_TITLE', 'Click to view a list of URLs referring to this page');
define('REFERRER_LINK_TEXT', 'Referrers');
define('QUERY_LOG', 'Query log:');
define('SEARCH_LABEL', 'Search:');

// geshiversion
define('NOT_AVAILABLE', 'n/a');
define('NOT_INSTALLED', 'not installed');

// googleform
define('BUTTON_GOOGLE', 'Google');

// header
define('YOU_ARE', 'You are %s'); // %s - name/ ip of the user.
define('LINKING_PAGES_LINK_TITLE', 'Display a list of pages linking to %s'); // %s - page name

// include
define('ERROR_CIRCULAR_REFERENCE', 'Circular reference detected!');

// lastedit
define('ANONYMOUS_USER', 'anonymous');
define('LASTEDIT_MESSAGE', 'Last edited by %s');
define('DIFF_LINK_TITLE', 'Show differences from last revision');

// lastusers
define('NAME', "Name");
define('OWNED_PAGES', "Owned Pages");
define('SIGNUP_DATE_TIME', "Signup Date/Time");

// mindmap
define('ERROR_INVALID_MM_SYNTAX', 'Error: Invalid MindMap action syntax. <br /> Proper usage: {{mindmap http://domain.com/MapName/mindmap.mm}} or {{mindmap url="http://domain.com/MapName/mindmap.mm"}}');
define('DOWNLOAD_MM', 'Download this mind map');
define('EDIT_MM', 'Use <a href="'.FREEMIND_PROJECT_URL.'">Freemind</a> to edit it');
define('MM_FULLSCREEN_LINK_TITLE', 'Open fullscreen');

// mychanges
define('NO_PAGES_FOUND', 'No pages found.');
define('NO_PAGES_EDITED', 'You have not edited any pages yet.');
define('ALPHA_PAGES_CHANGE_LIST', "This is a list of pages you've edited, along with the time of your last change.");
define('TIME_PAGES_CHANGE_LIST', "This is a list of pages you've edited, ordered by the time of your last change.");
define('ORDER_DATE', 'order by date');
define('ORDER_ALPHA', 'order alphabetically');
define('NOT_LOGGED_IN', "You're not logged in, thus the list of pages you've edited couldn't be retrieved."); #duplicate

// mypages
define('OWNED_PAGES_TXT', "This is the list of pages you own."); 
define('NO_OWNED_PAGES', "You don't own any pages.");
define('USER_NOT_LOGGED_IN', "You're not logged in, thus the list of your pages couldn't be retrieved."); #duplicate

// newpage
define('ERROR_INVALID_PAGE_NAME', 'The page name %s is invalid. Valid page names must start with a capital letter, contain only letters and numbers, and be in CamelCase format.'); // %s - page name
define('NEW_PAGE_FORM_LABEL', 'Create and Edit'); // %s - page name

// orphanedpages
define('NO_ORPHANED_PAGES', 'No orphaned pages. Good!');

// pageindex
define('PAGE_HEADING',"Page Index");
define('INDEX_CAPTION',"This is an alphabetical list of pages you can read on this server.");
define('ALL_PAGES',"All");
define('PAGE_OWNER'," . . . . Owner: %s");
define('OWNED_PAGES_CAPTION',"Items marked with a * indicate pages that you own.");

// recentchanges
define('RECENT_CHANGES_HEADING', '=====Recently changed pages=====');
define('UNREGISTERED_USER', 'unregistered user');
define('LABEL_HISTORY', 'history');
define('TITLE_REVISION_LINK', 'View recent revisions list for %s');
define('TITLE_HISTORY_LINK', 'View edit history of %s');
define('WIKIPING_ENABLED', 'WikiPing enabled: Changes on this wiki are broadcast to <a href="http://%1$s">http://%1$s</a>');
define('NO_RECENTLY_CHANGED_PAGES', 'There are no recently changed pages.');
define('NO_READABLE_RECENTLY_CHANGED_PAGES', 'There are no recently changed pages you have access to.');

// recentcomments
define('RECENT_COMMENTS_HEADING', '=====Recent comments=====');
define('COMMENT_AUTHOR_DIVIDER', ', comment by '); // recentlycommented
define('NO_RECENT_COMMENTS', 'There are no recent comments.');
define('NO_READABLE_RECENT_COMMENTS', 'There are no recent comments you can read.');

// recentlycommented 
define('RECENTLY_COMMENTED_HEADING', '=====Recently commented pages=====');
define('ANONYMOUS_COMMENT_AUTHOR', '(unregistered user)');
define('NO_RECENTLY_COMMENTED', 'There are no recently commented pages.');
define('NO_READABLE_RECENTLY_COMMENTED', 'There are no recently commented pages you can read.');

// system - see geshiversion

// textsearch & textsearchexpanded
define('SEARCH_FOR', 'Search for');
define('SEARCH_ZERO_MATCH', 'No matches');
define('SEARCH_ONE_MATCH', 'One match found');
define('SEARCH_N_MATCH', '%d matches found');
define('SEARCH_RESULTS', 'Search results');
define('SEARCH_TRY_EXPANDED', '<br />Not sure which page to choose?<br />Try the <a href="$1">Expanded Text Search</a> which shows surrounding text.');
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

// usersettings
// i18n
define('USER_ACCOUNT_LEGEND', "Your account");
define('USER_SETTINGS_LEGEND', "Settings");
define('LOGIN_REGISTER_LEGEND', "Login/Register");
define('LOGIN_LEGEND', "Login");
define('RETRIEVE_PASSWORD_LEGEND', "Password forgotten");
// define('REGISTER_LABEL', "Register"); # to be used later for register-action
define('USER_LOGGED_OUT', "You have successfully logged out.");
define('USER_SETTINGS_STORED', "User settings stored!");
define('ERROR_NO_BLANK', "Sorry, blanks are not permitted in the password.");
define('ERROR_PASSWORD_TOO_SHORT', "Sorry, the password must contain at least %s characters.");
define('PASSWORD_CHANGED', "Password successfully changed!");
define('ERROR_OLD_PASSWORD_WRONG', "The old password you entered is wrong.");
define('USER_LOGGED_IN_AS_LABEL', "You are logged in as %s");
define('LABEL_NO_GO_BACK', 'Don\'t go back to %s');
define('USER_EMAIL_LABEL', "Your email address:");
define('DOUBLECLICK_LABEL', "Doubleclick editing:");
define('SHOW_COMMENTS_LABEL', "Show comments by default:");
define('RECENTCHANGES_DISPLAY_LIMIT_LABEL', "RecentChanges display limit:");
define('PAGEREVISION_LIST_LIMIT_LABEL', "Page revisions list limit:");
define('UPDATE_SETTINGS_INPUT', "Update Settings");
define('CHANGE_PASSWORD_HEADING', "Change your password:");
define('CURRENT_PASSWORD_LABEL', "Your current password:");
define('PASSWORD_REMINDER_LABEL', "Password reminder:");
define('NEW_PASSWORD_LABEL', "Your new password:");
define('NEW_PASSWORD_CONFIRM_LABEL', "Confirm new password:");
define('CHANGE_BUTTON_LABEL', "Change password");
define('REGISTER_BUTTON_LABEL', "Register");
define('INVITATION_CODE_LABEL', "<abbr title=\"In order to register, you must fill in the invitation code sent by this website's administrator.\">Invitation Code</abbr>:");
define('ERROR_WRONG_PASSWORD', "Sorry, you entered the wrong password.");
define('ERROR_WRONG_HASH', "Sorry, you entered a wrong password reminder.");
define('ERROR_EMPTY_USERNAME', "Please fill in your user name.");
define('ERROR_NON_EXISTENT_USERNAME', "Sorry, this user name doesn't exist.");
define('ERROR_RESERVED_PAGENAME', "Sorry, this name is reserved for a page. Please choose a different name.");
define('ERROR_WIKINAME', "Username must be formatted as a ##\"\"WikiName\"\"##, e.g. ##\"\"JohnDoe\"\"##.");
define('ERROR_EMPTY_PASSWORD', "Please fill in a password.");
define('ERROR_EMPTY_PASSWORD_OR_HASH', "Please fill your password or hash.");
define('ERROR_EMPTY_CONFIRMATION_PASSWORD', "Please confirm your password in order to register a new account.");
define('ERROR_EMPTY_NEW_CONFIRMATION_PASSWORD', "Please confirm your new password in order to update your account.");
define('ERROR_EMPTY_NEW_PASSWORD', "You must also fill in a new password.");
define('ERROR_PASSWORD_MATCH', "Passwords don't match.");
define('ERROR_EMAIL_ADDRESS_REQUIRED', "Please specify an email address.");
define('ERROR_INVALID_EMAIL_ADDRESS', "That doesn't quite look like an email address.");
define('ERROR_INVALID_REVISION_DISPLAY_LIMIT', "The number of page revisions should not exceed %d.");
define('ERROR_INVALID_RECENTCHANGES_DISPLAY_LIMIT', "The number of recently changed pages should not exceed %d.");
define('ERROR_INVITATION_CODE_INCORRECT', "This is a private wiki, only invited members can register an account! Please contact the administrator of this website for an invitation code.");
define('NO_REGISTRATION', "Registration on this wiki is disabled.");
define('REGISTRATION_SUCCEEDED', "You have successfully registered!");
define('REGISTERED_USER_LOGIN_LABEL', "If you're already a registered user, log in here:");
define('WIKINAME_LABEL', "Your <abbr title=\"A WikiName is formed by two or more capitalized words without space, e.g. JohnDoe\">WikiName</abbr>:");
define('PASSWORD_LABEL', "Password (%s+ chars):");
define('LOGIN_BUTTON_LABEL', "Login");
define('LOGOUT_BUTTON_LABEL', "Logout");
define('NEW_USER_REGISTER_LABEL', "Fields required if you are signing up as a new user:");
define('CONFIRM_PASSWORD_LABEL', "Confirm password:");
define('RETRIEVE_PASSWORD_MESSAGE', "If you need a password reminder, click [[PasswordForgotten here]]. --- You can login here using your password reminder.");
define('TEMP_PASSWORD_LABEL', "Password reminder:");


// wantedpages
define('BACKLINKS_TITLE', 'Click to view all pages linking to %s');
define('LABEL_EDIT', 'edit');
define('LISTPAGES_EDIT_TITLE', 'Click to edit %s');
define('LEGEND_SORTING', 'Sorting ...');
define('LABEL_SORTING_NUMBER', 'Sorting #');
define('LABEL_SORTING_DESC', 'desc');
define('LABEL_OK', '   OK   ');
define('NO_WANTED_PAGES', 'No wanted pages. Good!');
?>
