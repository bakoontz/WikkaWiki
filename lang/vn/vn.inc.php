<?php
/**
 * Wikka language file.
 *
 * This file holds all interface language strings for Wikka.
 *
 * @package 		Language
 *
 * @version		$Id: vn.inc.php.txt 24 2008-09-06 15:53:43Z kyanh $
 * @license 		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author 		{@link http://wikkawiki.org/KyAnh KyAnh}
 *
 * @copyright 	Copyright 2008, {@link http://wikkawiki.org/CreditsPage Wikka Development Team}
 *
 * @todo		translate -- of course :)
 *
 * @note	This Vietnamese pack is translated from
 * @note		http://wush.net/trac/wikka/browser/trunk/lang/en/en.inc.php
 * @note	svn revision: Id:en.inc.php 481 2007-05-17 16:34:24Z DarTar
 *
 */

/* ------------------ COMMON ------------------ */

/**#@+
 * Language constant shared among several Wikka components
 */
// NOTE: all common names (used in multiple files) should start with WIKKA_ !
if(!defined('WIKKA_ADMIN_ONLY_TITLE')) define('WIKKA_ADMIN_ONLY_TITLE', 'Xin lỗi! Chỉ người điều hành mới xem được thông tin này.'); //title for elements that are only displayed to admins
if(!defined('WIKKA_ERROR_SETUP_FILE_MISSING')) define('WIKKA_ERROR_SETUP_FILE_MISSING', 'Không tìm thấy tập tin dùng cho việc cài đặt hoặc nâng cấp! Hãy cài lại WikkaWiki');
if(!defined('WIKKA_ERROR_MYSQL_ERROR')) define('WIKKA_ERROR_MYSQL_ERROR', 'Lỗi MySQL: %d - %s');	// %d - error number; %s - error text
if(!defined('WIKKA_ERROR_CAPTION')) define('WIKKA_ERROR_CAPTION', 'Lỗi');
if(!defined('WIKKA_ERROR_ACL_READ')) define('WIKKA_ERROR_ACL_READ', 'Bạn chưa được cấp quyền xem trang này');
if(!defined('WIKKA_ERROR_ACL_READ_SOURCE')) define('WIKKA_ERROR_ACL_READ_SOURCE', 'Bạn chưa có quyền xem mã nguồn của trang này.');
if(!defined('WIKKA_ERROR_ACL_READ_INFO')) define('WIKKA_ERROR_ACL_READ_INFO', 'Bạn chưa có quyền xem thông tin này.');
if(!defined('WIKKA_ERROR_LABEL')) define('WIKKA_ERROR_LABEL', 'Error');
if(!defined('WIKKA_ERROR_PAGE_NOT_EXIST')) define('WIKKA_ERROR_PAGE_NOT_EXIST', 'Trang %s chưa được tạo ra.'); // %s (source) page name
if(!defined('WIKKA_ERROR_EMPTY_USERNAME')) define('WIKKA_ERROR_EMPTY_USERNAME', 'Vui lòng cho biết tên người dùng!');
if(!defined('WIKKA_DIFF_ADDITIONS_HEADER')) define('WIKKA_DIFF_ADDITIONS_HEADER', 'Thêm:');
if(!defined('WIKKA_DIFF_DELETIONS_HEADER')) define('WIKKA_DIFF_DELETIONS_HEADER', 'Bớt:');
if(!defined('WIKKA_DIFF_NO_DIFFERENCES')) define('WIKKA_DIFF_NO_DIFFERENCES', 'Không khác gì');
if(!defined('ERROR_USERNAME_UNAVAILABLE')) define('ERROR_USERNAME_UNAVAILABLE', "Tên người dùng này đã được chọn.");
if(!defined('ERROR_USER_SUSPENDED')) define('ERROR_USER_SUSPENDED', "Tài khoản tạm thời bị khóa. Vui lòng liên lạc người quản trị.");
if(!defined('WIKKA_ERROR_INVALID_PAGE_NAME')) define('WIKKA_ERROR_INVALID_PAGE_NAME', 'Trang %s không hợp lệ. Tên của trang phải bắt đầu bằng chữ cái hoa, chỉ chứa các chữ cái hoặc số, và ở dạng CamelCas.'); // %s - page name
if(!defined('WIKKA_ERROR_PAGE_ALREADY_EXIST')) define('WIKKA_ERROR_PAGE_ALREADY_EXIST', 'Đã có trang như vậy được tạo ra rồi!');
if(!defined('WIKKA_LOGIN_LINK_DESC')) define('WIKKA_LOGIN_LINK_DESC', 'mở khóa');
if(!defined('WIKKA_MAINPAGE_LINK_DESC')) define('WIKKA_MAINPAGE_LINK_DESC', 'trang chính');
if(!defined('WIKKA_NO_OWNER')) define('WIKKA_NO_OWNER', 'nobody');
if(!defined('WIKKA_NOT_AVAILABLE')) define('WIKKA_NOT_AVAILABLE', 'n/a');
if(!defined('WIKKA_NOT_INSTALLED')) define('WIKKA_NOT_INSTALLED', 'chưa được cài đặt');
if(!defined('WIKKA_ANONYMOUS_USER')) define('WIKKA_ANONYMOUS_USER', 'anonymous'); // 'name' of non-registered user
if(!defined('WIKKA_UNREGISTERED_USER')) define('WIKKA_UNREGISTERED_USER', 'khách'); // alternative for 'anonymous' @@@ make one string only?
if(!defined('WIKKA_ANONYMOUS_AUTHOR_CAPTION')) define('WIKKA_ANONYMOUS_AUTHOR_CAPTION', '('.WIKKA_UNREGISTERED_USER.')'); // @@@ or WIKKA_ANONYMOUS_USER
if(!defined('WIKKA_SAMPLE_WIKINAME')) define('WIKKA_SAMPLE_WIKINAME', 'JohnDoe'); // must be a CamelCase name
if(!defined('WIKKA_HISTORY')) define('WIKKA_HISTORY', 'history');
if(!defined('WIKKA_REVISIONS')) define('WIKKA_REVISIONS', 'phiên bản');
if(!defined('WIKKA_REVISION_NUMBER')) define('WIKKA_REVISION_NUMBER', 'Phiên bản %s');
if(!defined('WIKKA_REV_WHEN_BY_WHO')) define('WIKKA_REV_WHEN_BY_WHO', '%1$s by %2$s'); // %1$s - timestamp; %2$s - user name
if(!defined('WIKKA_NO_PAGES_FOUND')) define('WIKKA_NO_PAGES_FOUND', 'Không tìm thấy trang như vậy.');
if(!defined('WIKKA_PAGE_OWNER')) define('WIKKA_PAGE_OWNER', 'Sở hữu: %s'); // %s - page owner name or link
if(!defined('WIKKA_COMMENT_AUTHOR_DIVIDER')) define('WIKKA_COMMENT_AUTHOR_DIVIDER', ', comment by '); //TODo check if we can construct a single phrase here
if(!defined('WIKKA_PAGE_EDIT_LINK_DESC')) define('WIKKA_PAGE_EDIT_LINK_DESC', 'sửa');
if(!defined('WIKKA_PAGE_CREATE_LINK_DESC')) define('WIKKA_PAGE_CREATE_LINK_DESC', 'tạo mới');
if(!defined('WIKKA_PAGE_EDIT_LINK_TITLE')) define('WIKKA_PAGE_EDIT_LINK_TITLE', 'click chuột để sửa %s'); // %s page name @@@ 'Edit %s'
if(!defined('WIKKA_BACKLINKS_LINK_TITLE')) define('WIKKA_BACKLINKS_LINK_TITLE', 'Xem danh sách các trang liên kết đến %s'); // %s page name
if(!defined('WIKKA_JRE_LINK_DESC')) define('WIKKA_JRE_LINK_DESC', 'Môi trường thực thi Java');
if(!defined('WIKKA_NOTE')) define('WIKKA_NOTE', 'NOTE:');
if(!defined('WIKKA_JAVA_PLUGIN_NEEDED')) define('WIKKA_JAVA_PLUGIN_NEEDED', 'Để dùng applet này bạn cần Java 1.4.1 hoặc mới hơn');
/**#@-*/


/*  ------------------ CORE ------------------  */

/**#@+
 * Language constant for the core {@link wikka.php wikka} program
 */
// wikka
if(!defined('ERROR_NO_DB_ACCESS')) define('ERROR_NO_DB_ACCESS', 'Lỗi: không thể kết nối vào hệ thống dữ liệu.');
if(!defined('ERROR_RETRIEVAL_MYSQL_VERSION')) define('ERROR_RETRIEVAL_MYSQL_VERSION', 'Không thể xác định được phiên bản của MySQL');
if(!defined('ERROR_WRONG_MYSQL_VERSION')) define('ERROR_WRONG_MYSQL_VERSION', 'Wikka cần MySQL phiên bản %s hoặc cao hơn!');	// %s - version number
if(!defined('STATUS_WIKI_UPGRADE_NOTICE')) define('STATUS_WIKI_UPGRADE_NOTICE', 'Trang này đang được nâng cấp. Vui lòng ghé thăm sau!');
if(!defined('STATUS_WIKI_UNAVAILABLE')) define('STATUS_WIKI_UNAVAILABLE', 'Trang wiki này đang tạm ngưng hoạt động.');
if(!defined('PAGE_GENERATION_TIME')) define('PAGE_GENERATION_TIME', 'Trang được tạo ra trong %.4f giây'); // %.4f - page generation time
if(!defined('ERROR_HEADER_MISSING')) define('ERROR_HEADER_MISSING', 'Không tìm ra mẫu cho đầu trang. Hãy chắc rằng tập tin <code>header.php</code> có trong thư mục các mẫu.'); //TODO Make sure this message matches any filename/folder change
if(!defined('ERROR_FOOTER_MISSING')) define('ERROR_FOOTER_MISSING', 'Không tìm thấy mẫu cho chân trang. Hãy chắc rằng tập tin <code>footer.php</code> có trong thư mục các mẫu.'); //TODO Make sure this message matches any filename/folder change

#if(!defined('ERROR_SETUP_HEADER_MISSING')) define('ERROR_SETUP_HEADER_MISSING', 'Tập tin "setup/header.php" ở đâu rồi! Hãy cài Wikka lại nhé!');
#if(!defined('ERROR_SETUP_FOOTER_MISSING')) define('ERROR_SETUP_FOOTER_MISSING', 'Tập tin "setup/footer.php" ở đâu rồi! Hãy cài lại Wikka nhé!');
/**#@-*/

/*  ------------------ TEMPLATE ------------------  */

/**#@+
 * Language constant used by the {@link header.php header} template
 */
// header
if(!defined('GENERIC_DOCTITLE')) define('GENERIC_DOCTITLE', '%1$s: %2$s');	// %1$s - wiki name; %2$s - page title
if(!defined('RSS_REVISIONS_TITLE')) define('RSS_REVISIONS_TITLE', '%1$s: các phiên bản cho %2$s');	// %1$s - wiki name; %2$s - current page name
if(!defined('RSS_RECENTCHANGES_TITLE')) define('RSS_RECENTCHANGES_TITLE', '%s: các trang được chỉnh sửa gần đây');	// %s - wiki name
if(!defined('YOU_ARE')) define('YOU_ARE', 'Bạn là %s'); // %s - name / ip of the user.
/**#@-*/

/**#@+
 * Language constant used by the {@link footer.php footer} template
 */
// footer
if(!defined('FOOTER_PAGE_EDIT_LINK_DESC')) define('FOOTER_PAGE_EDIT_LINK_DESC', 'Sửa trang');
if(!defined('PAGE_HISTORY_LINK_TITLE')) define('PAGE_HISTORY_LINK_TITLE', 'Xem các sửa đổi gần đây của trang'); // @@@ TODO 'View recent edits to this page'
if(!defined('PAGE_HISTORY_LINK_DESC')) define('PAGE_HISTORY_LINK_DESC', 'Lịch sử trang');
if(!defined('PAGE_REVISION_LINK_TITLE')) define('PAGE_REVISION_LINK_TITLE', 'Xem các phiên bản gần đây của trang'); // @@@ TODO 'View recent revisions list for this page'
if(!defined('PAGE_REVISION_XML_LINK_TITLE')) define('PAGE_REVISION_XML_LINK_TITLE', 'Xem các phiên bản gần đây của trang'); // @@@ TODO 'View recent revisions list for this page'
if(!defined('PAGE_ACLS_EDIT_LINK_DESC')) define('PAGE_ACLS_EDIT_LINK_DESC', 'Sửa quyền truy cập');
if(!defined('PAGE_ACLS_EDIT_ADMIN_LINK_DESC')) define('PAGE_ACLS_EDIT_ADMIN_LINK_DESC', '('.PAGE_ACLS_EDIT_LINK_DESC.')');
if(!defined('PUBLIC_PAGE')) define('PUBLIC_PAGE', 'Trang công cộng');
if(!defined('USER_IS_OWNER')) define('USER_IS_OWNER', 'Bạn sỡ hữu trang này.');
if(!defined('TAKE_OWNERSHIP')) define('TAKE_OWNERSHIP', 'Lấy quyền sở hữu');
if(!defined('REFERRERS_LINK_TITLE')) define('REFERRERS_LINK_TITLE', 'Xem cách liên kết đến trang này'); // @@@ TODO 'View a list of URLs referring to this page'
if(!defined('REFERRERS_LINK_DESC')) define('REFERRERS_LINK_DESC', 'Tham chiếu');
if(!defined('QUERY_LOG')) define('QUERY_LOG', 'Nhật ký truy vấn:');
if(!defined('SEARCH_LABEL')) define('SEARCH_LABEL', 'Tìm:');
/**#@-*/


/*  ------------------ ACTIONS  ------------------  */

/**#@+
 * Language constant used by the {@link calendar.php calendar} action
 */
// calendar
if(!defined('FMT_SUMMARY')) define('FMT_SUMMARY', 'Lịch cho %s');	// %s - ???@@@
if(!defined('TODAY')) define('TODAY', 'hôm nay');
/**#@-*/

/**#@+
 * Language constant used by the {@link category.php category} action
 */
// category
if(!defined('ERROR_NO_PAGES')) define('ERROR_NO_PAGES', 'Xin lỗi! Không tìm thấy thành phần nào cho trang %s');	// %s - ???@@@
if(!defined('PAGES_BELONGING_TO')) define('PAGES_BELONGING_TO', 'Có %1$d trang sau thuộc về phạm trù %2$s'); // %1$d number found; %2$s category
/**#@-*/

/**#@+
 * Language constant used by the {@link color.php color} action
 */
// color
if(!defined('ERROR_NO_TEXT_GIVEN')) define('ERROR_NO_TEXT_GIVEN', 'Không có đoạn văn nào để tô màu!');
if(!defined('ERROR_NO_COLOR_SPECIFIED')) define('ERROR_NO_COLOR_SPECIFIED', 'Bạn chưa chỉ ra màu để tô!');
/**#@-*/

/**#@+
 * Language constant used by the {@link contact.php contact} action
 */
// contact
if(!defined('SEND_FEEDBACK_LINK_TITLE')) define('SEND_FEEDBACK_LINK_TITLE', 'Gửi phản hồi');
if(!defined('SEND_FEEDBACK_LINK_TEXT')) define('SEND_FEEDBACK_LINK_TEXT', 'Liên hệ');
/**#@-*/

/**#@+
 * Language constant used by the {@link countowned.php countowned} action
 */
// countowned
if(!defined('DISPLAY_MYPAGES_LINK_TITLE')) define('DISPLAY_MYPAGES_LINK_TITLE', 'Danh sách các trang của bạn');
/**#@-*/

/**#@+
 * Language constant used by the {@link countpages.php countpages} action
 */
// countpages
if(!defined('INDEX_LINK_TITLE')) define('INDEX_LINK_TITLE', 'Danh sách ABC các trang');
/**#@-*/

/**#@+
 * Language constant used by the {@link dbinfo.php dbinfo} action
 */
// dbinfo
if(!defined('HD_DBINFO')) define('HD_DBINFO','Thông tin về cơ sỡ dữ liệu');
if(!defined('HD_DBINFO_DB')) define('HD_DBINFO_DB','Cơ sở dữ liệu');
if(!defined('HD_DBINFO_TABLES')) define('HD_DBINFO_TABLES','Các bảng');
if(!defined('HD_DB_CREATE_DDL')) define('HD_DB_CREATE_DDL','DDL tạo cơ sở dữ liệu %s:');				# %s will hold database name
if(!defined('HD_TABLE_CREATE_DDL')) define('HD_TABLE_CREATE_DDL','DDL tạo bảng %s:');				# %s will hold table name
if(!defined('TXT_INFO_1')) define('TXT_INFO_1','This utility provides some information about the database(s) and tables in your system.');
if(!defined('TXT_INFO_2')) define('TXT_INFO_2',' Depending on permissions for the Wikka database user, not all databases or tables may be visible.');
if(!defined('TXT_INFO_3')) define('TXT_INFO_3',' Where creation DDL is given, this reflects everything that would be needed to exactly recreate the same database and table definitions,');
if(!defined('TXT_INFO_4')) define('TXT_INFO_4',' including defaults that may not have been specified explicitly.');
if(!defined('FORM_SELDB_LEGEND')) define('FORM_SELDB_LEGEND','Các cơ sở dữ liệu');
if(!defined('FORM_SELTABLE_LEGEND')) define('FORM_SELTABLE_LEGEND','Các bảng');
if(!defined('FORM_SELDB_OPT_LABEL')) define('FORM_SELDB_OPT_LABEL','Chọn một cơ sở dữ liệu:');
if(!defined('FORM_SELTABLE_OPT_LABEL')) define('FORM_SELTABLE_OPT_LABEL','Chọn một bảng:');
if(!defined('FORM_SUBMIT_SELDB')) define('FORM_SUBMIT_SELDB','Chọn');
if(!defined('FORM_SUBMIT_SELTABLE')) define('FORM_SUBMIT_SELTABLE','Chọn');
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
if(!defined('PW_FORGOTTEN_HEADING')) define('PW_FORGOTTEN_HEADING', 'Nhắc nhở mật mã');
if(!defined('PW_CHK_SENT')) define('PW_CHK_SENT', 'Mật mã nhắc nhở được gửi tới email của người dùng %s\'s.'); // %s - username
if(!defined('PW_FORGOTTEN_MAIL')) define('PW_FORGOTTEN_MAIL', 'Xin chào %1$s!\n\n\nCó ai đó yêu cầu chúng tôi gửi mật mã nhắc nhở đến email này để đăng nhập vào trang %2$s. Nếu người đó chẳng phải là bạn, hãy bỏ qua email này, vì chúng tôi sẽ không có thay đổi nào về mật mã dành cho bạn.\n\nTài khoản: %1$s \nMật mã nhắc nhở: %3$s \nURL: %4$s \n\nNhớ đổi mật mã ngay sau khi sử dụng thông tin vừa nêu để đăng nhập.'); // %1$s - username; %2$s - wiki name; %3$s - md5 sum of pw; %4$s - login url of the wiki
if(!defined('PW_FORGOTTEN_MAIL_REF')) define('PW_FORGOTTEN_MAIL_REF', 'Mật mã nhắc nhở cho %s'); // %s - wiki name
if(!defined('PW_FORM_TEXT')) define('PW_FORM_TEXT', 'Nhập tên tài khoản của bạn và mật mã nhắc nhở sẽ được chuyển đến email đã dùng để đăng ký.');
if(!defined('PW_FORM_FIELDSET_LEGEND')) define('PW_FORM_FIELDSET_LEGEND', 'Tài khoản (WikiName):');
if(!defined('ERROR_UNKNOWN_USER')) define('ERROR_UNKNOWN_USER', 'Tài khoản đã chỉ ra không tồn tại trên hệ thống!');
#if(!defined('ERROR_MAIL_NOT_SENT')) define('ERROR_MAIL_NOT_SENT', 'An error occurred while trying to send the password. Outgoing mail might be disabled. Please contact your server administrator.');
if(!defined('ERROR_MAIL_NOT_SENT')) define('ERROR_MAIL_NOT_SENT', 'Lỗi xảy ra khi cố gửi password qua email. Hệ thống gửi mail không hoạt động. Vui lòng liên hệ người quản trị hệ thống để được hướng dẫn thêm.');
if(!defined('BUTTON_SEND_PW')) define('BUTTON_SEND_PW', 'Gửi mật mã nhắc nhở');
if(!defined('USERSETTINGS_REF')) define('USERSETTINGS_REF', 'Trở về trang %s.'); // %s - UserSettings link
/**#@-*/

/**#@+
 * Language constant used by the {@link feedback.php feedback} action
 */
// feedback
if(!defined('ERROR_EMPTY_NAME')) define('ERROR_EMPTY_NAME', 'Vui lòng cho biết tên');
if(!defined('ERROR_INVALID_EMAIL')) define('ERROR_INVALID_EMAIL', 'Vui lòng cho biết email hợp lệ');
if(!defined('ERROR_EMPTY_MESSAGE')) define('ERROR_EMPTY_MESSAGE', 'Vui lòng gõ vài dòng!');
if(!defined('ERROR_FEEDBACK_MAIL_NOT_SENT')) define('ERROR_FEEDBACK_MAIL_NOT_SENT', 'Sorry, An error occurred while trying to send your email. Outgoing mail might be disabled. Please try another method to contact %s, for instance by posting a page comment'); // %s - name of the recipient
if(!defined('FEEDBACK_FORM_LEGEND')) define('FEEDBACK_FORM_LEGEND', 'Contact %s'); //%s - wikiname of the recipient
if(!defined('FEEDBACK_NAME_LABEL')) define('FEEDBACK_NAME_LABEL', 'Tên:');
if(!defined('FEEDBACK_EMAIL_LABEL')) define('FEEDBACK_EMAIL_LABEL', 'Email:');
if(!defined('FEEDBACK_MESSAGE_LABEL')) define('FEEDBACK_MESSAGE_LABEL', 'Thông điệp:');
if(!defined('FEEDBACK_SEND_BUTTON')) define('FEEDBACK_SEND_BUTTON', 'Gửi');
if(!defined('FEEDBACK_SUBJECT')) define('FEEDBACK_SUBJECT', 'Phản hồi từ %s'); // %s - name of the wiki
if(!defined('SUCCESS_FEEDBACK_SENT')) define('SUCCESS_FEEDBACK_SENT', 'Thông điệp đã được gửi. Cảm ơn bạn %s đã phản hồi!'); //%s - name of the sender
/**#@-*/

/**#@+
 * Language constant used by the {@link files.php files action} and {@link handlers/files.xml/files.xml.php files.xml handler}
 */
// files
if(!defined('ERROR_UPLOAD_DIRECTORY_NOT_WRITABLE')) define('ERROR_UPLOAD_DIRECTORY_NOT_WRITABLE', 'Hãy chắc rằng server có quyền ghi vào thư mục %s.'); // %s Upload folder ref #89
if(!defined('ERROR_UPLOAD_DIRECTORY_NOT_READABLE')) define('ERROR_UPLOAD_DIRECTORY_NOT_READABLE', 'Hãy chắc rằng server có quyền đọc từ thư mục %s.'); // %s Upload folder ref #89
if(!defined('ERROR_NONEXISTENT_FILE')) define('ERROR_NONEXISTENT_FILE', 'Xin lỗi! Tập tin % không tồn tại trên server.'); // %s - file name ref
if(!defined('ERROR_FILE_UPLOAD_INCOMPLETE')) define('ERROR_FILE_UPLOAD_INCOMPLETE', 'Việc tải tập tin lên không hoàn thành 100%. Vui lòng thử lại');
if(!defined('ERROR_UPLOADING_FILE')) define('ERROR_UPLOADING_FILE', 'Có lỗi xảy ra trong quá trình tải tập tin len');
if(!defined('ERROR_FILE_ALREADY_EXISTS')) define('ERROR_FILE_ALREADY_EXISTS', 'Lỗi: tập tin %s đã có trên server.'); // %s - file name ref
if(!defined('ERROR_EXTENSION_NOT_ALLOWED')) define('ERROR_EXTENSION_NOT_ALLOWED', 'Xin lỗi. Các tập tin với phần mở rộng này không được phép tải lên');
if(!defined('ERROR_FILETYPE_NOT_ALLOWED')) define('ERROR_FILETYPE_NOT_ALLOWED', 'Xin lỗi. Các tập tin thuộc loại này không được phép tải lên!');
if(!defined('ERROR_FILE_NOT_DELETED')) define('ERROR_FILE_NOT_DELETED', 'Xin lỗi! Không thể xóa tập tin!');
if(!defined('ERROR_FILE_TOO_BIG')) define('ERROR_FILE_TOO_BIG', 'Bạn đang cố tải lên tập tin quá lớn. Kích thước tối đa cho phép là %s.'); // %s - allowed filesize
if(!defined('ERROR_NO_FILE_SELECTED')) define('ERROR_NO_FILE_SELECTED', 'Không có tập tin nào được chọn.');
if(!defined('ERROR_FILE_UPLOAD_IMPOSSIBLE')) define('ERROR_FILE_UPLOAD_IMPOSSIBLE', 'Không thể tải tập tin vì thiếu cấu hình cho server.');
if(!defined('SUCCESS_FILE_UPLOADED')) define('SUCCESS_FILE_UPLOADED', 'Tập tin đã được tải lên thành công.');
if(!defined('FILE_TABLE_CAPTION')) define('FILE_TABLE_CAPTION', 'Đính kèm');
if(!defined('FILE_TABLE_HEADER_NAME')) define('FILE_TABLE_HEADER_NAME', 'Tập tin');
if(!defined('FILE_TABLE_HEADER_SIZE')) define('FILE_TABLE_HEADER_SIZE', 'Cỡ');
if(!defined('FILE_TABLE_HEADER_DATE')) define('FILE_TABLE_HEADER_DATE', 'Cập nhật lần cuối');
if(!defined('FILE_UPLOAD_FORM_LEGEND')) define('FILE_UPLOAD_FORM_LEGEND', 'Đính kèm tập tin khác:');
if(!defined('FILE_UPLOAD_FORM_LABEL')) define('FILE_UPLOAD_FORM_LABEL', 'Tập tin:');
if(!defined('FILE_UPLOAD_FORM_BUTTON')) define('FILE_UPLOAD_FORM_BUTTON', 'Tải lên');
if(!defined('DOWNLOAD_LINK_TITLE')) define('DOWNLOAD_LINK_TITLE', 'Tải xuống %s'); // %s - file name
if(!defined('DELETE_LINK_TITLE')) define('DELETE_LINK_TITLE', 'Xóa tập tin %s'); // %s - file name
if(!defined('NO_ATTACHMENTS')) define('NO_ATTACHMENTS', 'Trang này không đính kèm tập tin.');
if(!defined('FILES_DELETE_FILE')) define('FILES_DELETE_FILE', 'Xóa tập tin này?');
if(!defined('FILES_DELETE_FILE_BUTTON')) define('FILES_DELETE_FILE_BUTTON', 'Xóa tập tin');
if(!defined('FILES_CANCEL_BUTTON')) define('FILES_CANCEL_BUTTON', 'Bỏ qua');
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
if(!defined('HIGHSCORES_LABEL_COMMENTS')) define('HIGHSCORES_LABEL_COMMENTS', 'bình luận');
if(!defined('HIGHSCORES_LABEL_PAGES')) define('HIGHSCORES_LABEL_PAGES', 'pages owned');
if(!defined('HIGHSCORES_CAPTION')) define('HIGHSCORES_CAPTION', 'Top %1$s contributor(s) by number of %2$s');
if(!defined('HIGHSCORES_HEADER_RANK')) define('HIGHSCORES_HEADER_RANK', 'rank');
if(!defined('HIGHSCORES_HEADER_USER')) define('HIGHSCORES_HEADER_USER', 'user');
if(!defined('HIGHSCORES_HEADER_PERCENTAGE')) define('HIGHSCORES_HEADER_PERCENTAGE', 'percentage');
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
/**#@-*/

/**#@+
 * Language constant used by the {@link lastusers.php lastusers} action
 */
// lastusers
if(!defined('LASTUSERS_CAPTION')) define('LASTUSERS_CAPTION', 'Các thành viên mới đăng ký');
if(!defined('SIGNUP_DATE_TIME')) define('SIGNUP_DATE_TIME', 'Ngày giờ đăng ký');
if(!defined('NAME_TH')) define('NAME_TH', 'Tên tài khoảng');
if(!defined('OWNED_PAGES_TH')) define('OWNED_PAGES_TH', 'Trang sở hữu');
if(!defined('SIGNUP_DATE_TIME_TH')) define('SIGNUP_DATE_TIME_TH', 'Ngày giờ đăng ký');
/**#@-*/

/**#@+
 * Language constant used by the {@link mindmap.php mindmap} action
 */
// mindmap
if(!defined('MM_JRE_INSTALL_REQ')) define('MM_JRE_INSTALL_REQ', 'Vui lòng cài đặt %s (JRE) trên máy của bạn.'); // %s - JRE install link
if(!defined('MM_DOWNLOAD_LINK_DESC')) define('MM_DOWNLOAD_LINK_DESC', 'Tải về ánh xạ tư duy này');
if(!defined('MM_EDIT')) define('MM_EDIT', 'Use %s to edit it'); // %s - link to freemind project
if(!defined('MM_FULLSCREEN_LINK_DESC')) define('MM_FULLSCREEN_LINK_DESC', 'Mở ở chế độ toàn màn hình');
if(!defined('ERROR_INVALID_MM_SYNTAX')) define('ERROR_INVALID_MM_SYNTAX', 'Lỗi: cú pháp ánh xạ tư duy không hợp lệ.');
if(!defined('PROPER_USAGE_MM_SYNTAX')) define('PROPER_USAGE_MM_SYNTAX', 'Cách dùng đúng: %1$s hoặc %2$s'); // %1$s - syntax sample 1; %2$s - syntax sample 2
/**#@-*/

/**#@+
 * Language constant used by the {@link mychanges.php mychanges} action
 */
// mychanges
if(!defined('NO_PAGES_EDITED')) define('NO_PAGES_EDITED', 'Bạn chưa sửa trang này xong.');
if(!defined('MYCHANGES_ALPHA_LIST')) define('MYCHANGES_ALPHA_LIST', "Đây là danh sách các trang soạn bởi %s cùng với thời gian của lần cập nhật cuối.");
if(!defined('MYCHANGES_DATE_LIST')) define('MYCHANGES_DATE_LIST', "Đây là danh sách các trang soạn bởi %s, sắp xếp theo thời gian cập nhật cuối.");
if(!defined('ORDER_DATE_LINK_DESC')) define('ORDER_DATE_LINK_DESC', 'sắp xếp theo ngày');
if(!defined('ORDER_ALPHA_LINK_DESC')) define('ORDER_ALPHA_LINK_DESC', 'sắp xếp theo thứ tự ABC');
if(!defined('MYCHANGES_NOT_LOGGED_IN')) define('MYCHANGES_NOT_LOGGED_IN', "Bạn chưa đăng nhập, vì thế danh sách các trang bạn soạn không thể xem được.");
/**#@-*/

/**#@+
 * Language constant used by the {@link mypages.php mypages} action
 */
// mypages
if(!defined('OWNED_PAGES_TXT')) define('OWNED_PAGES_TXT', "Danh sách các trang sở hữu bởi %s.");
if(!defined('OWNED_NO_PAGES')) define('OWNED_NO_PAGES', 'You don\'t own any pages.');
if(!defined('OWNED_NONE_FOUND')) define('OWNED_NONE_FOUND', 'Không tìm thấy trang nào.');
if(!defined('OWNED_NOT_LOGGED_IN')) define('OWNED_NOT_LOGGED_IN', "You're not logged in, thus the list of your pages couldn't be retrieved.");
/**#@-*/

/**#@+
 * Language constant used by the {@link newpage.php newpage} action
 */
// newpage
if(!defined('NEWPAGE_CREATE_LEGEND')) define('NEWPAGE_CREATE_LEGEND', 'Tạo trang mới');
if(!defined('NEWPAGE_CREATE_BUTTON')) define('NEWPAGE_CREATE_BUTTON', 'Tạo');
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
if(!defined('RECENTCHANGES_HEADING')) define('RECENTCHANGES_HEADING', 'Những trang thay đổi gần đây');
if(!defined('REVISIONS_LINK_TITLE')) define('REVISIONS_LINK_TITLE', 'Xem danh sách các phiên bản mới nhất của %s'); // %s - page name
if(!defined('HISTORY_LINK_TITLE')) define('HISTORY_LINK_TITLE', 'Xem lịch sử của trang %s'); // %s - page name
if(!defined('WIKIPING_ENABLED')) define('WIKIPING_ENABLED', 'WikiPing enabled: Changes on this wiki are broadcast to %s'); // %s - link to wikiping server
if(!defined('RECENTCHANGES_NONE_FOUND')) define('RECENTCHANGES_NONE_FOUND', 'Không có các trang nào thay đổi gần đây.');
if(!defined('RECENTCHANGES_NONE_ACCESSIBLE')) define('RECENTCHANGES_NONE_ACCESSIBLE', 'Bạn chưa có quyền xem những trang thay đổi gần đây.');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentcomments.php recentcomments} action
 */
// recentcomments
if(!defined('RECENTCOMMENTS_HEADING')) define('RECENTCOMMENTS_HEADING', 'Bình luận gần đây');
if(!defined('RECENTCOMMENTS_TIMESTAMP_CAPTION')) define('RECENTCOMMENTS_TIMESTAMP_CAPTION', '%s'); // %s - timestamp
if(!defined('RECENTCOMMENTS_NONE_FOUND')) define('RECENTCOMMENTS_NONE_FOUND', 'Không có bình luận.');
if(!defined('RECENTCOMMENTS_NONE_ACCESSIBLE')) define('RECENTCOMMENTS_NONE_ACCESSIBLE', 'Không có bình luận nào bạn có quyền xem.');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentlycommented.php recentlycommented} action
 */
// recentlycommented
if(!defined('RECENTLYCOMMENTED_HEADING')) define('RECENTLYCOMMENTED_HEADING', 'Recently commented pages');
if(!defined('RECENTLYCOMMENTED_NONE_FOUND')) define('RECENTLYCOMMENTED_NONE_FOUND', 'There are no recently commented pages.');
if(!defined('RECENTLYCOMMENTED_NONE_ACCESSIBLE')) define('RECENTLYCOMMENTED_NONE_ACCESSIBLE', 'There are no recently commented pages you have access to.');
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
if(!defined('SEARCH_FOR')) define('SEARCH_FOR', 'Tìm');
if(!defined('SEARCH_ZERO_MATCH')) define('SEARCH_ZERO_MATCH', 'No matches');
if(!defined('SEARCH_ONE_MATCH')) define('SEARCH_ONE_MATCH', 'One match found');
if(!defined('SEARCH_N_MATCH')) define('SEARCH_N_MATCH', '%d matches found'); // %d - number of hits
if(!defined('SEARCH_RESULTS')) define('SEARCH_RESULTS', 'Search results: <strong>%1$s</strong> for <strong>%2$s</strong>'); # %1$s: n matches for | %2$s: search term
if(!defined('SEARCH_NOT_SURE_CHOICE')) define('SEARCH_NOT_SURE_CHOICE', 'Not sure which page to choose?');
if(!defined('SEARCH_EXPANDED_LINK_DESC')) define('SEARCH_EXPANDED_LINK_DESC', 'Expanded Text Search'); // search link description
if(!defined('SEARCH_TRY_EXPANDED')) define('SEARCH_TRY_EXPANDED', 'Try the %s which shows surrounding text.'); // %s expanded search link
if(!defined('SEARCH_TIPS')) define('SEARCH_TIPS', 'Mẹo tìm kiếm:');
if(!defined('SEARCH_WORD_1')) define('SEARCH_WORD_1', 'táo');
if(!defined('SEARCH_WORD_2')) define('SEARCH_WORD_2', 'chuối');
if(!defined('SEARCH_WORD_3')) define('SEARCH_WORD_3', 'trái cây');
if(!defined('SEARCH_WORD_4')) define('SEARCH_WORD_4', 'macintosh');
if(!defined('SEARCH_WORD_5')) define('SEARCH_WORD_5', 'vài');
if(!defined('SEARCH_WORD_6')) define('SEARCH_WORD_6', 'từ');
if(!defined('SEARCH_PHRASE')) define('SEARCH_PHRASE',sprintf('%s %s',SEARCH_WORD_5,SEARCH_WORD_6));
if(!defined('SEARCH_TARGET_1')) define('SEARCH_TARGET_1', 'Find pages that contain at least one of the two words.');
if(!defined('SEARCH_TARGET_2')) define('SEARCH_TARGET_2', 'Find pages that contain both words.');
if(!defined('SEARCH_TARGET_3')) define('SEARCH_TARGET_3',sprintf("Find pages that contain the word '%1\$s' but not '%2\$s'.",SEARCH_WORD_1,SEARCH_WORD_4));
if(!defined('SEARCH_TARGET_4')) define('SEARCH_TARGET_4',"Find pages that contain words such as 'apple', 'apples', 'applesauce', or 'applet'."); // make sure target words all *start* with SEARCH_WORD_1
if(!defined('SEARCH_TARGET_5')) define('SEARCH_TARGET_5',sprintf("Find pages that contain the exact phrase '%1\$s' (for example, pages that contain '%1\$s of wisdom' but not '%2\$s noise %3\$s').",SEARCH_PHRASE,SEARCH_WORD_5,SEARCH_WORD_6));
/**#@-*/

/**#@+
 * Language constant used by the {@link usersettings.php usersettings} action
 */
// usersettings
// - error messages
if(!defined('ERROR_EMPTY_USERNAME')) define('ERROR_EMPTY_USERNAME', 'Vui lòng cho biết tên của bạn.');
if(!defined('ERROR_NONEXISTENT_USERNAME')) define('ERROR_NONEXISTENT_USERNAME', 'Xin lỗi. Tên này không tồn tại.'); // @@@ too specific
if(!defined('ERROR_RESERVED_PAGENAME')) define('ERROR_RESERVED_PAGENAME', 'Xin lỗi. Tên này dành riêng cho việc đặt tên trang. Vui lòng chọn tên khác.');
if(!defined('ERROR_WIKINAME')) define('ERROR_WIKINAME', 'Tên người dùng phải ở dạng %1$s, ví dụ %2$s.'); // %1$s - identifier WikiName; %2$s - sample WikiName
if(!defined('ERROR_EMPTY_EMAIL_ADDRESS')) define('ERROR_EMPTY_EMAIL_ADDRESS', 'Vui lòng cho biết email.');
if(!defined('ERROR_INVALID_EMAIL_ADDRESS')) define('ERROR_INVALID_EMAIL_ADDRESS', 'Dường như bạn chưa chỉ ra một email thật sự.');
if(!defined('ERROR_INVALID_PASSWORD')) define('ERROR_INVALID_PASSWORD', 'Xin lỗi! Bạn đã chỉ ra mật mã chưa hợp lệ.');	// @@@ too specific
if(!defined('ERROR_INVALID_HASH')) define('ERROR_INVALID_HASH', 'Mật mã nhắc nhở không đúng.');
if(!defined('ERROR_INVALID_OLD_PASSWORD')) define('ERROR_INVALID_OLD_PASSWORD', 'Bạn đã gõ sai mật mã cũ.');
if(!defined('ERROR_EMPTY_PASSWORD')) define('ERROR_EMPTY_PASSWORD', 'Vui lòng cho biết mật mã.');
if(!defined('ERROR_EMPTY_PASSWORD_OR_HASH')) define('ERROR_EMPTY_PASSWORD_OR_HASH', 'Vui lòng cho biết mật mã hoặc mật mã nhắc nhở.');
if(!defined('ERROR_EMPTY_CONFIRMATION_PASSWORD')) define('ERROR_EMPTY_CONFIRMATION_PASSWORD', 'Vui lòng xác nhận lại mật mã để đăng ký tài khoản mới.');
if(!defined('ERROR_EMPTY_NEW_CONFIRMATION_PASSWORD')) define('ERROR_EMPTY_NEW_CONFIRMATION_PASSWORD', 'Vui lòng xác nhận lại mật mã để cập nhận thông tin cá nhân.');
if(!defined('ERROR_EMPTY_NEW_PASSWORD')) define('ERROR_EMPTY_NEW_PASSWORD', 'Vui lòng cho biết mậ mã mới.');
if(!defined('ERROR_PASSWORD_MATCH')) define('ERROR_PASSWORD_MATCH', 'Hai mật mã không khớp nhau.');
if(!defined('ERROR_PASSWORD_NO_BLANK')) define('ERROR_PASSWORD_NO_BLANK', 'Ồ không! Mật mã không thể trống trơn như vậy!');
if(!defined('ERROR_PASSWORD_TOO_SHORT')) define('ERROR_PASSWORD_TOO_SHORT', 'Mật mã phải chứa ít nhất %d ký tự.'); // %d - minimum password length
if(!defined('ERROR_INVALID_REVISION_DISPLAY_LIMIT')) define('ERROR_INVALID_REVISION_DISPLAY_LIMIT', 'The number of page revisions should not exceed %d.'); // %d - maximum revisions to view
if(!defined('ERROR_INVALID_RECENTCHANGES_DISPLAY_LIMIT')) define('ERROR_INVALID_RECENTCHANGES_DISPLAY_LIMIT', 'The number of recently changed pages should not exceed %d.'); // %d - maximum changed pages to view
if(!defined('ERROR_VALIDATION_FAILED')) if(!defined('ERROR_VALIDATION_FAILED')) define('ERROR_VALIDATION_FAILED', "Chứng thực đăng ký bị lỗi. Vui lòng thử lại!");
// - success messages
if(!defined('SUCCESS_USER_LOGGED_OUT')) define('SUCCESS_USER_LOGGED_OUT', 'Đã thoát thành công khỏi hệ thống.');
if(!defined('SUCCESS_USER_REGISTERED')) define('SUCCESS_USER_REGISTERED', 'Đã đăng ký thành công!');
if(!defined('SUCCESS_USER_SETTINGS_STORED')) define('SUCCESS_USER_SETTINGS_STORED', 'Thiết lập cá nhân đã được lưu!');
if(!defined('SUCCESS_USER_PASSWORD_CHANGED')) define('SUCCESS_USER_PASSWORD_CHANGED', 'Đã cập nhật chìa khóa!');
// - captions
if(!defined('NEW_USER_REGISTER_CAPTION')) define('NEW_USER_REGISTER_CAPTION', 'Nếu bạn đăng ký tài khoản mới:');
if(!defined('REGISTERED_USER_LOGIN_CAPTION')) define('REGISTERED_USER_LOGIN_CAPTION', 'Nếu bạn đã có tài khoản, đăng nhập ở đây:');
if(!defined('RETRIEVE_PASSWORD_CAPTION')) define('RETRIEVE_PASSWORD_CAPTION', 'Log in with your [[%s password reminder]]:'); //%s PasswordForgotten link
if(!defined('USER_LOGGED_IN_AS_CAPTION')) define('USER_LOGGED_IN_AS_CAPTION', 'You are logged in as %s'); // %s user name
// - form legends
if(!defined('USER_ACCOUNT_LEGEND')) define('USER_ACCOUNT_LEGEND', 'Tài khoản của bạn');
if(!defined('USER_SETTINGS_LEGEND')) define('USER_SETTINGS_LEGEND', 'Thiết lập');
if(!defined('LOGIN_REGISTER_LEGEND')) define('LOGIN_REGISTER_LEGEND', 'Đăng nhập/Đăng ký');
if(!defined('LOGIN_LEGEND')) define('LOGIN_LEGEND', 'Đăng nhập');
#if(!defined('REGISTER_LEGEND')) define('REGISTER_LEGEND', 'Register'); // @@@ TODO to be used later for register-action
if(!defined('CHANGE_PASSWORD_LEGEND')) define('CHANGE_PASSWORD_LEGEND', 'Change your password');
if(!defined('RETRIEVE_PASSWORD_LEGEND')) define('RETRIEVE_PASSWORD_LEGEND', 'Password forgotten');
// - form field labels (should end in ':' _unless_ it's a checkbox or radio button option)
if(!defined('USERSETTINGS_REDIRECT_AFTER_LOGIN_LABEL')) define('USERSETTINGS_REDIRECT_AFTER_LOGIN_LABEL', 'Redirect to %s after login');	// %s page to redirect to
if(!defined('USER_EMAIL_LABEL')) define('USER_EMAIL_LABEL', 'Your email address:');
if(!defined('DOUBLECLICK_LABEL')) define('DOUBLECLICK_LABEL', 'Doubleclick editing:');
if(!defined('SHOW_COMMENTS_LABEL')) define('SHOW_COMMENTS_LABEL', 'Show comments by default:');
if(!defined('COMMENT_STYLE_LABEL')) define('COMMENT_STYLE_LABEL', 'Comment style');
if(!defined('COMMENT_ASC_LABEL')) define('COMMENT_ASC_LABEL', 'Phẳng (cũ trước)');
if(!defined('COMMENT_DEC_LABEL')) define('COMMENT_DEC_LABEL', 'Phẳng (mới trước)');
if(!defined('COMMENT_THREADED_LABEL')) define('COMMENT_THREADED_LABEL', 'Luồng');
if(!defined('COMMENT_DELETED_LABEL')) define('COMMENT_DELETED_LABEL', '[Comment deleted]');
if(!defined('COMMENT_BY_LABEL')) define('COMMENT_BY_LABEL', 'Bình luận bởi ');
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
if(!defined('CURRENT_PASSWORD_OPTION')) define('CURRENT_PASSWORD_OPTION', 'Mật mã hiện tại');
if(!defined('PASSWORD_REMINDER_OPTION')) define('PASSWORD_REMINDER_OPTION', 'Mật mã nhắc nhở');
// - form buttons
if(!defined('UPDATE_SETTINGS_BUTTON')) define('UPDATE_SETTINGS_BUTTON', 'Cập nhật thông tin');
if(!defined('LOGIN_BUTTON')) define('LOGIN_BUTTON', 'Đăng nhập');
if(!defined('LOGOUT_BUTTON')) define('LOGOUT_BUTTON', 'Thoát');
if(!defined('CHANGE_PASSWORD_BUTTON')) define('CHANGE_PASSWORD_BUTTON', 'Đổi mật mã');
if(!defined('REGISTER_BUTTON')) define('REGISTER_BUTTON', 'Đăng ký');
/**#@-*/

/**#@+
 * Language constant used by the {@link wantedpages.php wantedpages} action
 */
// wantedpages
if(!defined('SORTING_LEGEND')) define('SORTING_LEGEND', 'Sắp xếp ...');
if(!defined('SORTING_NUMBER_LABEL')) define('SORTING_NUMBER_LABEL', 'Sắp xếp #%d:');
if(!defined('SORTING_DESC_LABEL')) define('SORTING_DESC_LABEL', 'giảm');
if(!defined('OK_BUTTON')) define('OK_BUTTON', '   OK   ');
if(!defined('NO_WANTED_PAGES')) define('NO_WANTED_PAGES', 'Không có trang chờ. Tốt lắm!');
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
if(!defined('CLOSE_WINDOW')) define('CLOSE_WINDOW', 'Đóng cửa sổ');
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
if(!defined('ACLS_STORE_BUTTON')) define('ACLS_STORE_BUTTON', 'Lưu ACL');
if(!defined('CANCEL_BUTTON')) define('CANCEL_BUTTON', 'Bỏ qua');
// - syntax
if(!defined('ACLS_SYNTAX_HEADING')) define('ACLS_SYNTAX_HEADING', 'Cú pháp:');
if(!defined('ACLS_EVERYONE')) define('ACLS_EVERYONE', 'Mọi người');
if(!defined('ACLS_REGISTERED_USERS')) define('ACLS_REGISTERED_USERS', 'Số người dùng có đăng ký');
if(!defined('ACLS_NONE_BUT_ADMINS')) define('ACLS_NONE_BUT_ADMINS', 'Không ai (trừ admin)');
if(!defined('ACLS_ANON_ONLY')) define('ACLS_ANON_ONLY', 'Chỉ dành cho khách');
if(!defined('ACLS_LIST_USERNAMES')) define('ACLS_LIST_USERNAMES', 'dành cho người dùng %s; liệt kê mỗi người dùng trên một dòng'); // %s - sample user name
if(!defined('ACLS_NEGATION')) define('ACLS_NEGATION', 'Any of these items can be negated with a %s:'); // %s - 'negation' mark
if(!defined('ACLS_DENY_USER_ACCESS')) define('ACLS_DENY_USER_ACCESS', '%s sẽ bị ngăn cản truy cập'); // %s - sample user name
if(!defined('ACLS_AFTER')) define('ACLS_AFTER', 'sau');
if(!defined('ACLS_TESTING_ORDER1')) define('ACLS_TESTING_ORDER1', 'ACLs được kiểm tra theo thứ tự đã chỉ ra:');
if(!defined('ACLS_TESTING_ORDER2')) define('ACLS_TESTING_ORDER2', 'So be sure to specify %1$s on a separate line %2$s negating any users, not before.'); // %1$s - 'all' mark; %2$s - emphasised 'after'
if(!defined('ACLS_DEFAULT_ACLS')) define('ACLS_DEFAULT_ACLS', 'Danh sách rỗng sẽ được thiết lập giá trị mặc định như chỉ ra trong %s.');
/**#@-*/

/**#@+
 * Language constant used by the {@link backlinks.php backlinks} (page) handler
 */
// backlinks
if(!defined('BACKLINKS_HEADING')) define('BACKLINKS_HEADING', 'Các trang liên kết tới %s');
if(!defined('BACKLINKS_NO_PAGES')) define('BACKLINKS_NO_PAGES', 'Không có liên kết ngược cho trang này.');
/**#@-*/

/**#@+
 * Language constant used by the {@link claim.php claim} (page) handler
 */
// claim
if(!defined('USER_IS_NOW_OWNER')) define('USER_IS_NOW_OWNER', 'Bây giờ bạn là chủ sở hữu trang này.');
/**#@-*/

/**#@+
 * Language constant used by the {@link clone.php clone} (page) handler
 */
// clone
if(!defined('ERROR_ACL_WRITE')) define('ERROR_ACL_WRITE', 'Bạn chưa có quyền ghi vào trang  %s');
if(!defined('CLONE_VALID_TARGET')) define('CLONE_VALID_TARGET', 'Vui lòng cho tên thích hợp để nhân bản và vài ghi chú (tùy chọn):');
if(!defined('CLONE_LEGEND')) define('CLONE_LEGEND', 'Nhân bản trang %s'); // %s source page name
if(!defined('CLONED_FROM')) define('CLONED_FROM', 'Nhân bản từ trang %s'); // %s source page name
if(!defined('SUCCESS_CLONE_CREATED')) define('SUCCESS_CLONE_CREATED', 'trang %s đã được tạo ra!'); // %s new page name
if(!defined('CLONE_X_TO_LABEL')) define('CLONE_X_TO_LABEL', 'Nhân bản với tên:');
if(!defined('CLONE_EDIT_NOTE_LABEL')) define('CLONE_EDIT_NOTE_LABEL', 'Ghi chú:');
if(!defined('CLONE_EDIT_OPTION_LABEL')) define('CLONE_EDIT_OPTION_LABEL', ' Sửa sau khi nhân bản');
if(!defined('CLONE_ACL_OPTION_LABEL')) define('CLONE_ACL_OPTION_LABEL', ' Nhân bản ACL');
if(!defined('CLONE_BUTTON')) define('CLONE_BUTTON', 'Nhân bản');
if(!defined('ERROR_INVALID_PAGENAME')) define('ERROR_INVALID_PAGENAME', 'This page name is invalid. Valid page names must not contain the characters | ? = &lt; &gt; / \' " % or &amp;.');
/**#@-*/

/**#@+
 * Language constant used by the {@link delete.php delete} (page) handler
 */
// delete
if(!defined('ERROR_NO_PAGE_DEL_ACCESS')) define('ERROR_NO_PAGE_DEL_ACCESS', 'Bạn chưa có quyền xóa trang này.');
if(!defined('PAGE_DELETION_HEADER')) define('PAGE_DELETION_HEADER', 'Xóa trang %s'); // %s - name of the page
if(!defined('SUCCESS_PAGE_DELETED')) define('SUCCESS_PAGE_DELETED', 'Trang vừa được xóa!');
if(!defined('PAGE_DELETION_CAPTION')) define('PAGE_DELETION_CAPTION', 'Xóa trang này, cùng toàn bộ các bình luận của nó?');
if(!defined('PAGE_DELETION_DELETE_BUTTON')) define('PAGE_DELETION_DELETE_BUTTON', 'Xóa trang');
if(!defined('PAGE_DELETION_CANCEL_BUTTON')) define('PAGE_DELETION_CANCEL_BUTTON', 'Bỏ qua');
/**#@-*/

/**#@+
 * Language constant used by the {@link diff.php diff} (page) handler
 */
// diff
if(!defined('ERROR_DIFF_LIBRARY_MISSING')) define('ERROR_DIFF_LIBRARY_MISSING', 'Tập tin <tt>'.WIKKA_LIBRARY_PATH.'/diff.lib.php</tt> không được tìm thấy. Vui lòng liên hệ người quản trị hệ thống.');
if(!defined('ERROR_BAD_PARAMETERS')) define('ERROR_BAD_PARAMETERS', 'Tham số bạn chỉ ra không hợp lệ: một trong hai phiên bản đã bị xóa.');
if(!defined('DIFF_COMPARISON_HEADER')) define('DIFF_COMPARISON_HEADER', 'So sánh %1$s cho %2$s'); // %1$s - link to revision list; %2$s - link to page
if(!defined('DIFF_REVISION_LINK_TITLE')) define('DIFF_REVISION_LINK_TITLE', 'Danh sách các phiên bản của trang %s'); // %s page name
if(!defined('DIFF_PAGE_LINK_TITLE')) define('DIFF_PAGE_LINK_TITLE', 'Xem bản cuối cùng của trang này');
if(!defined('DIFF_SAMPLE_ADDITION')) define('DIFF_SAMPLE_ADDITION', 'thêm');
if(!defined('DIFF_SAMPLE_DELETION')) define('DIFF_SAMPLE_DELETION', 'xóa');
if(!defined('DIFF_SIMPLE_BUTTON')) define('DIFF_SIMPLE_BUTTON', 'So sánh đơn giản');
if(!defined('DIFF_FULL_BUTTON')) define('DIFF_FULL_BUTTON', 'So sánh đầy đủ');
if(!defined('HIGHLIGHTING_LEGEND')) define('HIGHLIGHTING_LEGEND', 'Hướng dẫn về tô màu:');

/**#@-*/

/**#@+
 * Language constant used by the {@link edit.php edit} (page) handler
 */
// edit
if(!defined('ERROR_OVERWRITE_ALERT1')) define('ERROR_OVERWRITE_ALERT1', 'CẢNH BÁO: bạn và ai khác đang đồng thời soạn trang này.');
if(!defined('ERROR_OVERWRITE_ALERT2')) define('ERROR_OVERWRITE_ALERT2', 'Vui lòng chép lại các thay đổi của bạn và tiếp tục soan thảo trang.');
if(!defined('ERROR_MISSING_EDIT_NOTE')) define('ERROR_MISSING_EDIT_NOTE', 'Vui lòng ghi chú về thay đổi của bạn!');
if(!defined('ERROR_TAG_TOO_LONG')) define('ERROR_TAG_TOO_LONG', 'Tên trang quá dài. Chỉ tối đa %d ký tự thôi!'); // %d - maximum page name length
if(!defined('ERROR_NO_WRITE_ACCESS')) define('ERROR_NO_WRITE_ACCESS', 'Bạn chưa có quyền truy cập trang này. Có khi bạn cần [[UserSettings đăng nhập]] hoặc [[UserSettings đăng ký tài khoản]] để tạo, sửa trang này.'); //TODO Distinct links for login and register actions
if(!defined('EDIT_STORE_PAGE_LEGEND')) define('EDIT_STORE_PAGE_LEGEND', 'Lưu trang');
if(!defined('EDIT_PREVIEW_HEADER')) define('EDIT_PREVIEW_HEADER', 'Xem trước');
if(!defined('EDIT_NOTE_LABEL')) define('EDIT_NOTE_LABEL', 'Vui lòng thêm ghi chú về thay đổi của bạn'); // label after field, so no colon!
if(!defined('MESSAGE_AUTO_RESIZE')) define('MESSAGE_AUTO_RESIZE', 'Click chuột vào %s sẽ tự động thu gọn tên trang để điều chỉnh kích cỡ'); // %s - rename button text
if(!defined('EDIT_PREVIEW_BUTTON')) define('EDIT_PREVIEW_BUTTON', 'Xem trước');
if(!defined('EDIT_STORE_BUTTON')) define('EDIT_STORE_BUTTON', 'Lưu');
if(!defined('EDIT_REEDIT_BUTTON')) define('EDIT_REEDIT_BUTTON', 'Tiếp tục soạn');
if(!defined('EDIT_CANCEL_BUTTON')) define('EDIT_CANCEL_BUTTON', 'Bỏ qua');
if(!defined('EDIT_RENAME_BUTTON')) define('EDIT_RENAME_BUTTON', 'Đổi tên');
if(!defined('ACCESSKEY_PREVIEW')) define('ACCESSKEY_PREVIEW', 'p'); // ideally, should match EDIT_PREVIEW_BUTTON
if(!defined('ACCESSKEY_STORE')) define('ACCESSKEY_STORE', 's'); // ideally, should match EDIT_STORE_BUTTON
if(!defined('ACCESSKEY_REEDIT')) define('ACCESSKEY_REEDIT', 'r'); // ideally, should match EDIT_REEDIT_BUTTON
if(!defined('SHOWCODE_LINK')) define('SHOWCODE_LINK', 'Xem mã nguồn wiki của trang này');
if(!defined('SHOWCODE_LINK_TITLE')) define('SHOWCODE_LINK_TITLE', 'Xem mã nguồn'); // @@@ TODO 'View page formatting code'
if(!defined('EDIT_COMMENT_TIMESTAMP_CAPTION')) define('EDIT_COMMENT_TIMESTAMP_CAPTION', '(%s)'); // %s timestamp
if (!defined('ERROR_INVALID_PAGEID')) if(!defined('ERROR_INVALID_PAGEID')) define('ERROR_INVALID_PAGEID', 'Không có phiên bản với id đã chỉ ra');
/**#@-*/

/**#@+
 * Language constant used by the {@link grabcode.php grabcode} (page) handler
 */
// grabcode
if(!defined('ERROR_NO_CODE')) define('ERROR_NO_CODE', 'Xin lỗi: không có mã nào để tải về.');
/**#@-*/

/**#@+
 * Language constant used by the {@link history.php history} (page) handler
 */
// history
if(!defined('EDITED_ON')) define('EDITED_ON', 'Sửa vào thởi điểm %1$s bởi %2$s'); // %1$s - time; %2$s - user name
if(!defined('HISTORY_PAGE_VIEW')) define('HISTORY_PAGE_VIEW', 'Lịch sử các thay đổi gần đây của trang %s'); // %s pagename
if(!defined('OLDEST_VERSION_EDITED_ON_BY')) define('OLDEST_VERSION_EDITED_ON_BY', 'Bản cũ nhất của trang này tạo vào thời điểm %1$s bởi %2$s'); // %1$s - time; %2$s - user name
if(!defined('MOST_RECENT_EDIT')) define('MOST_RECENT_EDIT', 'Sửa lần cuối vào %1$s bởi %2$s');
if(!defined('HISTORY_MORE_LINK_DESC')) define('HISTORY_MORE_LINK_DESC', 'here'); // used for alternative history link in HISTORY_MORE
if(!defined('HISTORY_MORE')) define('HISTORY_MORE', 'Lịch sử đầy đủ của trang này không thể xem vừa vẹn trên chỉ một trang. Click chuột vào %s để xem thêm.'); // %s alternative history link # @@@ TODO avoid using 'here' ^
/**#@-*/

/**#@+
 * Language constant shared by the {@link processcomment.php processcomment} and {@link show.php show} (page) handlers
 */
// processcomment & show
// - comment buttons
if(!defined('COMMENT_DELETE_BUTTON')) define('COMMENT_DELETE_BUTTON', 'Xóa');
if(!defined('COMMENT_REPLY_BUTTON')) define('COMMENT_REPLY_BUTTON', 'Trả lời');
if(!defined('COMMENT_ADD_BUTTON')) define('COMMENT_ADD_BUTTON', 'Thêm bình luận');
if(!defined('COMMENT_NEW_BUTTON')) define('COMMENT_NEW_BUTTON', 'Bình luận mới');
/**#@-*/

/**#@+
 * Language constant used by the {@link processcomment.php processcomment} (page) handler
 */
// processcomment
if(!defined('ERROR_NO_COMMENT_DEL_ACCESS')) define('ERROR_NO_COMMENT_DEL_ACCESS', 'Bạn chưa có quyền xóa bình luận!');
if(!defined('ERROR_NO_COMMENT_WRITE_ACCESS')) define('ERROR_NO_COMMENT_WRITE_ACCESS', 'Bạn chưa có quyền gửi bình luận ở trang này');
if(!defined('ERROR_EMPTY_COMMENT')) define('ERROR_EMPTY_COMMENT', 'Nội dung bình luận chưa có gì!');
if(!defined('ADD_COMMENT_LABEL')) define('ADD_COMMENT_LABEL', 'Trả lời cho %s:');
if(!defined('NEW_COMMENT_LABEL')) define('NEW_COMMENT_LABEL', 'Gửi bình luận:');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges_simple.xml.mm.php recentchanges_simple.xml.mm} (page) handler
 */
// recentchanges_simple.xml.mm
if(!defined('FIRST_NODE_LABEL')) define('FIRST_NODE_LABEL', 'Thay đổi gần đây');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges.xml.php recentchanges.xml} (page) handler
 */
// recentchanges.xml
if(!defined('RECENTCHANGES_DESC')) define('RECENTCHANGES_DESC', 'Thay đổi gần đây của trang %s'); // %s - page name
/**#@-*/

/**#@+
 * Language constant shared by the {@link referrers_sites.php referrers_sites}, {@link referrers.php referrers} and {@link review_blacklist.php review_blacklist} (page) handlers
 */
// referrers_sites + referrers + review_blacklist
if(!defined('REFERRERS_PURGE_24_HOURS')) define('REFERRERS_PURGE_24_HOURS', '24 giờ cuối');
if(!defined('REFERRERS_PURGE_N_DAYS')) define('REFERRERS_PURGE_N_DAYS', '%d ngày vừa qua'); // %d number of days
if(!defined('REFERRERS_NO_SPAM')) define('REFERRERS_NO_SPAM', 'Lời nhắn đến người người quấy rối: Trang này không được đánh dấu bởi các chương trình tìm kiếm; vì thế đừng mất thời gian của bạn với trang này!');
if(!defined('REFERRERS_DOMAINS_TO_WIKI_LINK_DESC')) define('REFERRERS_DOMAINS_TO_WIKI_LINK_DESC', 'Xem tên miền tham chiếu đến wiki');
if(!defined('REFERRERS_DOMAINS_TO_PAGE_LINK_DESC')) define('REFERRERS_DOMAINS_TO_PAGE_LINK_DESC', 'Xem tên miền tham chiếu đến %s'); // %s - page name
if(!defined('REFERRERS_URLS_TO_WIKI_LINK_DESC')) define('REFERRERS_URLS_TO_WIKI_LINK_DESC', 'Xem tham chiếu toàn cục');
if(!defined('REFERRERS_URLS_TO_PAGE_LINK_DESC')) define('REFERRERS_URLS_TO_PAGE_LINK_DESC', 'Xem địa chỉ tham chiếu cho trang %s'); // %s - page name
if(!defined('REFERRER_BLACKLIST_LINK_DESC')) define('REFERRER_BLACKLIST_LINK_DESC', 'Xem danh sách địa chỉ tham chiếu đen');
if(!defined('BLACKLIST_LINK_DESC')) define('BLACKLIST_LINK_DESC', 'Danh sách đen');
if(!defined('NONE_CAPTION')) define('NONE_CAPTION', 'Không');
if(!defined('PLEASE_LOGIN_CAPTION')) define('PLEASE_LOGIN_CAPTION', 'Bạn cần đăng nhập để xem danh sách các tham chiếu');
/**#@-*/

/**#@+
 * Language constant used by the {@link referrers_sites.php referrers_sites} (page) handler
 */
// referrers_sites
if(!defined('REFERRERS_URLS_LINK_DESC')) define('REFERRERS_URLS_LINK_DESC', 'Danh các các địa chỉ khác nhau');
if(!defined('REFERRERS_DOMAINS_TO_WIKI')) define('REFERRERS_DOMAINS_TO_WIKI', 'Các trang, tên miền tham khảo đến wiki (%s)'); // %s - link to referrers handler
if(!defined('REFERRERS_DOMAINS_TO_PAGE')) define('REFERRERS_DOMAINS_TO_PAGE', 'Các trang, tên miền tham khảo đến trang %1$s %2$s (%3$s)'); // %1$s - page link; %2$s - purge time; %3$s - link to referrers handler
/**#@-*/

/**#@+
 * Language constant used by the {@link referrers.php referrers} (page) handler
 */
// referrers
if(!defined('REFERRERS_DOMAINS_LINK_DESC')) define('REFERRERS_DOMAINS_LINK_DESC', 'Xem danh sách tên miền');
if(!defined('REFERRERS_URLS_TO_WIKI')) define('REFERRERS_URLS_TO_WIKI', 'Liên kết ngoài tham chiếu đến wiki (%s)'); // %s - link to referrers_sites handler
if(!defined('REFERRERS_URLS_TO_PAGE')) define('REFERRERS_URLS_TO_PAGE', 'Liên kết ngoài tham chiếu đến trang %1$s %2$s (%3$s)'); // %1$s - page link; %2$s - purge time; %3$s - link to referrers_sites handler
/**#@-*/

/**#@+
 * Language constant used by the {@link review_blacklist.php review_blacklist} (page) handler
 */
// review_blacklist
if(!defined('BLACKLIST_HEADING')) define('BLACKLIST_HEADING', 'Danh sách liên đen các tham chiếu');
if(!defined('BLACKLIST_REMOVE_LINK_DESC')) define('BLACKLIST_REMOVE_LINK_DESC', 'Xóa');
if(!defined('STATUS_BLACKLIST_EMPTY')) define('STATUS_BLACKLIST_EMPTY', 'Không có gì trong danh sách đen.');
/**#@-*/

/**#@+
 * Language constant used by the {@link revisions.php revisions} (page) handler
 */
// revisions
if(!defined('REVISIONS_CAPTION')) define('REVISIONS_CAPTION', 'Phiên bản cho trang %s'); // %s pagename
if(!defined('REVISIONS_NO_REVISIONS_YET')) define('REVISIONS_NO_REVISIONS_YET', 'Chưa có phiên bản nào của trang này');
if(!defined('REVISIONS_SIMPLE_DIFF')) define('REVISIONS_SIMPLE_DIFF', 'So sánh đơn giản');
if(!defined('REVISIONS_MORE_CAPTION')) define('REVISIONS_MORE_CAPTION', 'Có vài phiên bản khác không được trình bày ở đây. Click chuột vào nút %s để xem các phiên bản này'); // %S - text of REVISIONS_MORE_BUTTON
if(!defined('REVISIONS_RETURN_TO_NODE_BUTTON')) define('REVISIONS_RETURN_TO_NODE_BUTTON', 'Trở về nốt / Bỏ qua');
if(!defined('REVISIONS_SHOW_DIFFERENCES_BUTTON')) define('REVISIONS_SHOW_DIFFERENCES_BUTTON', 'Xem sự khác biệt');
if(!defined('REVISIONS_MORE_BUTTON')) define('REVISIONS_MORE_BUTTON', 'Tiếp...');
/**#@-*/

/**#@+
 * Language constant used by the {@link revisions.xml.php revisions.xml} (page) handler
 */
// revisions.xml
if(!defined('REVISIONS_EDITED_BY')) define('REVISIONS_EDITED_BY', 'Sửa bởi %s'); // %s user name
if(!defined('HISTORY_REVISIONS_OF')) define('HISTORY_REVISIONS_OF', 'Lịch sử của trang %s'); // %s - page name
/**#@-*/

/**#@+
 * Language constant used by the {@link show.php show} (page) handler
 */
// show
if(!defined('SHOW_RE_EDIT_BUTTON')) define('SHOW_RE_EDIT_BUTTON', 'Sửa lại phiên bản cũ này');
if(!defined('SHOW_FORMATTED_BUTTON')) define('SHOW_FORMATTED_BUTTON', 'Xem bản đã định dạng');
if(!defined('SHOW_SOURCE_BUTTON')) define('SHOW_SOURCE_BUTTON', 'Xem mã nguồn');
if(!defined('SHOW_ASK_CREATE_PAGE_CAPTION')) define('SHOW_ASK_CREATE_PAGE_CAPTION', 'Trang này chưa tồi tại. Bạn có muốn %s nó?'); // %s - page create link
if(!defined('SHOW_OLD_REVISION_CAPTION')) define('SHOW_OLD_REVISION_CAPTION', 'Đây là phiên bản cũ của trang %1$s tạo bởi %2$s vào thời điểm %3$s.'); // %1$s - page link; %2$s - username; %3$s - timestamp;
if(!defined('COMMENTS_CAPTION')) define('COMMENTS_CAPTION', 'Bình luận');
if(!defined('DISPLAY_COMMENTS_LABEL')) define('DISPLAY_COMMENTS_LABEL', 'Xem bình luận');
if(!defined('DISPLAY_COMMENT_LINK_DESC')) define('DISPLAY_COMMENT_LINK_DESC', 'Xem bình luận');
if(!defined('DISPLAY_COMMENTS_EARLIEST_LINK_DESC')) define('DISPLAY_COMMENTS_EARLIEST_LINK_DESC', 'Bài cũ trước');
if(!defined('DISPLAY_COMMENTS_LATEST_LINK_DESC')) define('DISPLAY_COMMENTS_LATEST_LINK_DESC', 'Bài mới trước');
if(!defined('DISPLAY_COMMENTS_THREADED_LINK_DESC')) define('DISPLAY_COMMENTS_THREADED_LINK_DESC', 'Luồng');
if(!defined('HIDE_COMMENTS_LINK_DESC')) define('HIDE_COMMENTS_LINK_DESC', 'Che bình luận');
if(!defined('STATUS_NO_COMMENTS')) define('STATUS_NO_COMMENTS', 'Chưa có bình luận nào.');
if(!defined('STATUS_ONE_COMMENT')) define('STATUS_ONE_COMMENT', 'Một bình luận.');
if(!defined('STATUS_SOME_COMMENTS')) define('STATUS_SOME_COMMENTS', 'Có %d bình luận.'); // %d - number of comments
if(!defined('COMMENT_TIME_CAPTION')) define('COMMENT_TIME_CAPTION', '%s'); // %s comment time
/**#@-*/

/**#@+
 * Language constant used by the {@link showcode.php showcode} (page) handler
 */
// showcode
if(!defined('SOURCE_HEADING')) define('SOURCE_HEADING', 'Mã nguồn wiki cho trang %s'); // %s - page link
if(!defined('SHOW_RAW_LINK_DESC')) define('SHOW_RAW_LINK_DESC', 'Xem mã nguồn nguyên thủy');
/**#@-*/

/* ------------------ LIBS ------------------*/

/**#@+
 * Language constant used by the {@link Wakka.class.php Wakka class} (the Wikka core containing most methods)
 */
// Wakka.class
if(!defined('QUERY_FAILED')) define('QUERY_FAILED', 'Truy vẫn bị lỗi:');
if(!defined('REDIR_DOCTITLE')) define('REDIR_DOCTITLE', 'Chuyển hướng đến %s'); // %s - target page
if(!defined('REDIR_LINK_DESC')) define('REDIR_LINK_DESC', 'liên kết này'); // used in REDIR_MANUAL_CAPTION
if(!defined('REDIR_MANUAL_CAPTION')) define('REDIR_MANUAL_CAPTION', 'Trường hợp trình duyệt của bạn không chuyển trang, vui lòng dùng liên kết %s'); // %s target page link
if(!defined('CREATE_THIS_PAGE_LINK_TITLE')) define('CREATE_THIS_PAGE_LINK_TITLE', 'Tạo trang này');
if(!defined('ACTION_UNKNOWN_SPECCHARS')) define('ACTION_UNKNOWN_SPECCHARS', 'Không rõ action. Tên của action không được chứa ký tự đặc biệt.');
if(!defined('ACTION_UNKNOWN')) define('ACTION_UNKNOWN', 'Action không xác định: "%s"'); // %s - action name
if(!defined('HANDLER_UNKNOWN_SPECCHARS')) define('HANDLER_UNKNOWN_SPECCHARS', 'Handler không xác định. Tên của handler không được chứa ký tự đặc biệt.');
if(!defined('HANDLER_UNKNOWN')) define('HANDLER_UNKNOWN', 'Handler không xác định: %s. Xin lỗi bạn!'); // %s handler name
if(!defined('FORMATTER_UNKNOWN_SPECCHARS')) define('FORMATTER_UNKNOWN_SPECCHARS', 'Formatter không xác định. Tên của formatter không được chứa ký tực đặc biệt.');
if(!defined('FORMATTER_UNKNOWN')) define('FORMATTER_UNKNOWN', 'Không tìm thấy formatter "%s"'); // %s formatter name
/**#@-*/

/* ------------------ SETUP ------------------ */
/**#@+
 * Language constant used by the {@link index.php setup} program (and several included files)
 */
// @@@ later....
/**#@-*/

?>
