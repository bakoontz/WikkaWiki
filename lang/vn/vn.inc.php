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
define('WIKKA_ADMIN_ONLY_TITLE', 'Xin lỗi! Chỉ người điều hành mới xem được thông tin này.'); //title for elements that are only displayed to admins
define('WIKKA_ERROR_SETUP_FILE_MISSING', 'Không tìm thấy tập tin dùng cho việc cài đặt hoặc nâng cấp! Hãy cài lại WikkaWiki');
define('WIKKA_ERROR_MYSQL_ERROR', 'Lỗi MySQL: %d - %s');	// %d - error number; %s - error text
define('WIKKA_ERROR_CAPTION', 'Lỗi');
define('WIKKA_ERROR_ACL_READ', 'Bạn chưa được cấp quyền xem trang này');
define('WIKKA_ERROR_ACL_READ_SOURCE', 'Bạn chưa có quyền xem mã nguồn của trang này.');
define('WIKKA_ERROR_ACL_READ_INFO', 'Bạn chưa có quyền xem thông tin này.');
define('WIKKA_ERROR_LABEL', 'Error');
define('WIKKA_ERROR_PAGE_NOT_EXIST', 'Trang %s chưa được tạo ra.'); // %s (source) page name
define('WIKKA_ERROR_EMPTY_USERNAME', 'Vui lòng cho biết tên người dùng!');
define('WIKKA_DIFF_ADDITIONS_HEADER', 'Thêm:');
define('WIKKA_DIFF_DELETIONS_HEADER', 'Bớt:');
define('WIKKA_DIFF_NO_DIFFERENCES', 'Không khác gì');
define('ERROR_USERNAME_UNAVAILABLE', "Tên người dùng này đã được chọn.");
define('ERROR_USER_SUSPENDED', "Tài khoản tạm thời bị khóa. Vui lòng liên lạc người quản trị.");
define('WIKKA_ERROR_INVALID_PAGE_NAME', 'Trang %s không hợp lệ. Tên của trang phải bắt đầu bằng chữ cái hoa, chỉ chứa các chữ cái hoặc số, và ở dạng CamelCas.'); // %s - page name
define('WIKKA_ERROR_PAGE_ALREADY_EXIST', 'Đã có trang như vậy được tạo ra rồi!');
define('WIKKA_LOGIN_LINK_DESC', 'mở khóa');
define('WIKKA_MAINPAGE_LINK_DESC', 'trang chính');
define('WIKKA_NO_OWNER', 'nobody');
define('WIKKA_NOT_AVAILABLE', 'n/a');
define('WIKKA_NOT_INSTALLED', 'chưa được cài đặt');
define('WIKKA_ANONYMOUS_USER', 'anonymous'); // 'name' of non-registered user
define('WIKKA_UNREGISTERED_USER', 'khách'); // alternative for 'anonymous' @@@ make one string only?
define('WIKKA_ANONYMOUS_AUTHOR_CAPTION', '('.WIKKA_UNREGISTERED_USER.')'); // @@@ or WIKKA_ANONYMOUS_USER
define('WIKKA_SAMPLE_WIKINAME', 'JohnDoe'); // must be a CamelCase name
define('WIKKA_HISTORY', 'history');
define('WIKKA_REVISIONS', 'phiên bản');
define('WIKKA_REVISION_NUMBER', 'Phiên bản %s');
define('WIKKA_REV_WHEN_BY_WHO', '%1$s by %2$s'); // %1$s - timestamp; %2$s - user name
define('WIKKA_NO_PAGES_FOUND', 'Không tìm thấy trang như vậy.');
define('WIKKA_PAGE_OWNER', 'Sở hữu: %s'); // %s - page owner name or link
define('WIKKA_COMMENT_AUTHOR_DIVIDER', ', comment by '); //TODo check if we can construct a single phrase here
define('WIKKA_PAGE_EDIT_LINK_DESC', 'sửa');
define('WIKKA_PAGE_CREATE_LINK_DESC', 'tạo mới');
define('WIKKA_PAGE_EDIT_LINK_TITLE', 'click chuột để sửa %s'); // %s page name @@@ 'Edit %s'
define('WIKKA_BACKLINKS_LINK_TITLE', 'Xem danh sách các trang liên kết đến %s'); // %s page name
define('WIKKA_JRE_LINK_DESC', 'Môi trường thực thi Java');
define('WIKKA_NOTE', 'NOTE:');
define('WIKKA_JAVA_PLUGIN_NEEDED', 'Để dùng applet này bạn cần Java 1.4.1 hoặc mới hơn');
/**#@-*/


/*  ------------------ CORE ------------------  */

/**#@+
 * Language constant for the core {@link wikka.php wikka} program
 */
// wikka
define('ERROR_WAKKA_LIBRARY_MISSING', 'Không tìm thấy tập tin cần thiết "%s". Để dùng Wikka, phải chắc rằng tập tin này tồn tại và ở đúng thư mục!');	// %s - configured path to core class
define('ERROR_NO_DB_ACCESS', 'Lỗi: không thể kết nối vào hệ thống dữ liệu.');
define('ERROR_RETRIEVAL_MYSQL_VERSION', 'Không thể xác định được phiên bản của MySQL');
define('ERROR_WRONG_MYSQL_VERSION', 'Wikka cần MySQL phiên bản %s hoặc cao hơn!');	// %s - version number
define('STATUS_WIKI_UPGRADE_NOTICE', 'Trang này đang được nâng cấp. Vui lòng ghé thăm sau!');
define('STATUS_WIKI_UNAVAILABLE', 'Trang wiki này đang tạm ngưng hoạt động.');
define('PAGE_GENERATION_TIME', 'Trang được tạo ra trong %.4f giây'); // %.4f - page generation time
define('ERROR_HEADER_MISSING', 'Không tìm ra mẫu cho đầu trang. Hãy chắc rằng tập tin <code>header.php</code> có trong thư mục các mẫu.'); //TODO Make sure this message matches any filename/folder change
define('ERROR_FOOTER_MISSING', 'Không tìm thấy mẫu cho chân trang. Hãy chắc rằng tập tin <code>footer.php</code> có trong thư mục các mẫu.'); //TODO Make sure this message matches any filename/folder change

#define('ERROR_WRONG_PHP_VERSION', 'Không tìm thấy biến $_REQUEST[]. Wakka cần PHP phiên bản 4.1.0 hoặc cao hơn!'); //TODO remove referral to PHP internals; refer only to required version
#define('ERROR_SETUP_HEADER_MISSING', 'Tập tin "setup/header.php" ở đâu rồi! Hãy cài Wikka lại nhé!');
#define('ERROR_SETUP_FOOTER_MISSING', 'Tập tin "setup/footer.php" ở đâu rồi! Hãy cài lại Wikka nhé!');
/**#@-*/

/*  ------------------ TEMPLATE ------------------  */

/**#@+
 * Language constant used by the {@link header.php header} template
 */
// header
define('GENERIC_DOCTITLE', '%1$s: %2$s');	// %1$s - wiki name; %2$s - page title
define('RSS_REVISIONS_TITLE', '%1$s: các phiên bản cho %2$s');	// %1$s - wiki name; %2$s - current page name
define('RSS_RECENTCHANGES_TITLE', '%s: các trang được chỉnh sửa gần đây');	// %s - wiki name
define('YOU_ARE', 'Bạn là %s'); // %s - name / ip of the user.
/**#@-*/

/**#@+
 * Language constant used by the {@link footer.php footer} template
 */
// footer
define('FOOTER_PAGE_EDIT_LINK_DESC', 'Sửa trang');
define('PAGE_HISTORY_LINK_TITLE', 'Xem các sửa đổi gần đây của trang'); // @@@ TODO 'View recent edits to this page'
define('PAGE_HISTORY_LINK_DESC', 'Lịch sử trang');
define('PAGE_REVISION_LINK_TITLE', 'Xem các phiên bản gần đây của trang'); // @@@ TODO 'View recent revisions list for this page'
define('PAGE_REVISION_XML_LINK_TITLE', 'Xem các phiên bản gần đây của trang'); // @@@ TODO 'View recent revisions list for this page'
define('PAGE_ACLS_EDIT_LINK_DESC', 'Sửa quyền truy cập');
define('PAGE_ACLS_EDIT_ADMIN_LINK_DESC', '('.PAGE_ACLS_EDIT_LINK_DESC.')');
define('PUBLIC_PAGE', 'Trang công cộng');
define('USER_IS_OWNER', 'Bạn sỡ hữu trang này.');
define('TAKE_OWNERSHIP', 'Lấy quyền sở hữu');
define('REFERRERS_LINK_TITLE', 'Xem cách liên kết đến trang này'); // @@@ TODO 'View a list of URLs referring to this page'
define('REFERRERS_LINK_DESC', 'Tham chiếu');
define('QUERY_LOG', 'Nhật ký truy vấn:');
define('SEARCH_LABEL', 'Tìm:');
/**#@-*/


/*  ------------------ ACTIONS  ------------------  */

/**#@+
 * Language constant used by the {@link calendar.php calendar} action
 */
// calendar
define('FMT_SUMMARY', 'Lịch cho %s');	// %s - ???@@@
define('TODAY', 'hôm nay');
/**#@-*/

/**#@+
 * Language constant used by the {@link category.php category} action
 */
// category
define('ERROR_NO_PAGES', 'Xin lỗi! Không tìm thấy thành phần nào cho trang %s');	// %s - ???@@@
define('PAGES_BELONGING_TO', 'Có %1$d trang sau thuộc về phạm trù %2$s'); // %1$d number found; %2$s category
/**#@-*/

/**#@+
 * Language constant used by the {@link color.php color} action
 */
// color
define('ERROR_NO_TEXT_GIVEN', 'Không có đoạn văn nào để tô màu!');
define('ERROR_NO_COLOR_SPECIFIED', 'Bạn chưa chỉ ra màu để tô!');
/**#@-*/

/**#@+
 * Language constant used by the {@link contact.php contact} action
 */
// contact
define('SEND_FEEDBACK_LINK_TITLE', 'Gửi phản hồi');
define('SEND_FEEDBACK_LINK_TEXT', 'Liên hệ');
/**#@-*/

/**#@+
 * Language constant used by the {@link countowned.php countowned} action
 */
// countowned
define('DISPLAY_MYPAGES_LINK_TITLE', 'Danh sách các trang của bạn');
/**#@-*/

/**#@+
 * Language constant used by the {@link countpages.php countpages} action
 */
// countpages
define('INDEX_LINK_TITLE', 'Danh sách ABC các trang');
/**#@-*/

/**#@+
 * Language constant used by the {@link dbinfo.php dbinfo} action
 */
// dbinfo
define('HD_DBINFO','Thông tin về cơ sỡ dữ liệu');
define('HD_DBINFO_DB','Cơ sở dữ liệu');
define('HD_DBINFO_TABLES','Các bảng');
define('HD_DB_CREATE_DDL','DDL tạo cơ sở dữ liệu %s:');				# %s will hold database name
define('HD_TABLE_CREATE_DDL','DDL tạo bảng %s:');				# %s will hold table name
define('TXT_INFO_1','This utility provides some information about the database(s) and tables in your system.');
define('TXT_INFO_2',' Depending on permissions for the Wikka database user, not all databases or tables may be visible.');
define('TXT_INFO_3',' Where creation DDL is given, this reflects everything that would be needed to exactly recreate the same database and table definitions,');
define('TXT_INFO_4',' including defaults that may not have been specified explicitly.');
define('FORM_SELDB_LEGEND','Các cơ sở dữ liệu');
define('FORM_SELTABLE_LEGEND','Các bảng');
define('FORM_SELDB_OPT_LABEL','Chọn một cơ sở dữ liệu:');
define('FORM_SELTABLE_OPT_LABEL','Chọn một bảng:');
define('FORM_SUBMIT_SELDB','Chọn');
define('FORM_SUBMIT_SELTABLE','Chọn');
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
define('PW_FORGOTTEN_HEADING', 'Nhắc nhở mật mã');
define('PW_CHK_SENT', 'Mật mã nhắc nhở được gửi tới email của người dùng %s\'s.'); // %s - username
define('PW_FORGOTTEN_MAIL', 'Xin chào %1$s!\n\n\nCó ai đó yêu cầu chúng tôi gửi mật mã nhắc nhở đến email này để đăng nhập vào trang %2$s. Nếu người đó chẳng phải là bạn, hãy bỏ qua email này, vì chúng tôi sẽ không có thay đổi nào về mật mã dành cho bạn.\n\nTài khoản: %1$s \nMật mã nhắc nhở: %3$s \nURL: %4$s \n\nNhớ đổi mật mã ngay sau khi sử dụng thông tin vừa nêu để đăng nhập.'); // %1$s - username; %2$s - wiki name; %3$s - md5 sum of pw; %4$s - login url of the wiki
define('PW_FORGOTTEN_MAIL_REF', 'Mật mã nhắc nhở cho %s'); // %s - wiki name
define('PW_FORM_TEXT', 'Nhập tên tài khoản của bạn và mật mã nhắc nhở sẽ được chuyển đến email đã dùng để đăng ký.');
define('PW_FORM_FIELDSET_LEGEND', 'Tài khoản (WikiName):');
define('ERROR_UNKNOWN_USER', 'Tài khoản đã chỉ ra không tồn tại trên hệ thống!');
#define('ERROR_MAIL_NOT_SENT', 'An error occurred while trying to send the password. Outgoing mail might be disabled. Please contact your server administrator.');
define('ERROR_MAIL_NOT_SENT', 'Lỗi xảy ra khi cố gửi password qua email. Hệ thống gửi mail không hoạt động. Vui lòng liên hệ người quản trị hệ thống để được hướng dẫn thêm.');
define('BUTTON_SEND_PW', 'Gửi mật mã nhắc nhở');
define('USERSETTINGS_REF', 'Trở về trang %s.'); // %s - UserSettings link
/**#@-*/

/**#@+
 * Language constant used by the {@link feedback.php feedback} action
 */
// feedback
define('ERROR_EMPTY_NAME', 'Vui lòng cho biết tên');
define('ERROR_INVALID_EMAIL', 'Vui lòng cho biết email hợp lệ');
define('ERROR_EMPTY_MESSAGE', 'Vui lòng gõ vài dòng!');
define('ERROR_FEEDBACK_MAIL_NOT_SENT', 'Sorry, An error occurred while trying to send your email. Outgoing mail might be disabled. Please try another method to contact %s, for instance by posting a page comment'); // %s - name of the recipient
define('FEEDBACK_FORM_LEGEND', 'Contact %s'); //%s - wikiname of the recipient
define('FEEDBACK_NAME_LABEL', 'Tên:');
define('FEEDBACK_EMAIL_LABEL', 'Email:');
define('FEEDBACK_MESSAGE_LABEL', 'Thông điệp:');
define('FEEDBACK_SEND_BUTTON', 'Gửi');
define('FEEDBACK_SUBJECT', 'Phản hồi từ %s'); // %s - name of the wiki
define('SUCCESS_FEEDBACK_SENT', 'Thông điệp đã được gửi. Cảm ơn bạn %s đã phản hồi!'); //%s - name of the sender
/**#@-*/

/**#@+
 * Language constant used by the {@link files.php files action} and {@link handlers/files.xml/files.xml.php files.xml handler}
 */
// files
define('ERROR_UPLOAD_DIRECTORY_NOT_WRITABLE', 'Hãy chắc rằng server có quyền ghi vào thư mục %s.'); // %s Upload folder ref #89
define('ERROR_UPLOAD_DIRECTORY_NOT_READABLE', 'Hãy chắc rằng server có quyền đọc từ thư mục %s.'); // %s Upload folder ref #89
define('ERROR_NONEXISTENT_FILE', 'Xin lỗi! Tập tin % không tồn tại trên server.'); // %s - file name ref
define('ERROR_FILE_UPLOAD_INCOMPLETE', 'Việc tải tập tin lên không hoàn thành 100%. Vui lòng thử lại');
define('ERROR_UPLOADING_FILE', 'Có lỗi xảy ra trong quá trình tải tập tin len');
define('ERROR_FILE_ALREADY_EXISTS', 'Lỗi: tập tin %s đã có trên server.'); // %s - file name ref
define('ERROR_EXTENSION_NOT_ALLOWED', 'Xin lỗi. Các tập tin với phần mở rộng này không được phép tải lên');
define('ERROR_FILETYPE_NOT_ALLOWED', 'Xin lỗi. Các tập tin thuộc loại này không được phép tải lên!');
define('ERROR_FILE_NOT_DELETED', 'Xin lỗi! Không thể xóa tập tin!');
define('ERROR_FILE_TOO_BIG', 'Bạn đang cố tải lên tập tin quá lớn. Kích thước tối đa cho phép là %s.'); // %s - allowed filesize
define('ERROR_NO_FILE_SELECTED', 'Không có tập tin nào được chọn.');
define('ERROR_FILE_UPLOAD_IMPOSSIBLE', 'Không thể tải tập tin vì thiếu cấu hình cho server.');
define('SUCCESS_FILE_UPLOADED', 'Tập tin đã được tải lên thành công.');
define('FILE_TABLE_CAPTION', 'Đính kèm');
define('FILE_TABLE_HEADER_NAME', 'Tập tin');
define('FILE_TABLE_HEADER_SIZE', 'Cỡ');
define('FILE_TABLE_HEADER_DATE', 'Cập nhật lần cuối');
define('FILE_UPLOAD_FORM_LEGEND', 'Đính kèm tập tin khác:');
define('FILE_UPLOAD_FORM_LABEL', 'Tập tin:');
define('FILE_UPLOAD_FORM_BUTTON', 'Tải lên');
define('DOWNLOAD_LINK_TITLE', 'Tải xuống %s'); // %s - file name
define('DELETE_LINK_TITLE', 'Xóa tập tin %s'); // %s - file name
define('NO_ATTACHMENTS', 'Trang này không đính kèm tập tin.');
define('FILES_DELETE_FILE', 'Xóa tập tin này?');
define('FILES_DELETE_FILE_BUTTON', 'Xóa tập tin');
define('FILES_CANCEL_BUTTON', 'Bỏ qua');
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
define('HIGHSCORES_LABEL_EDITS', 'edits');
define('HIGHSCORES_LABEL_COMMENTS', 'bình luận');
define('HIGHSCORES_LABEL_PAGES', 'pages owned');
define('HIGHSCORES_CAPTION', 'Top %1$s contributor(s) by number of %2$s');
define('HIGHSCORES_HEADER_RANK', 'rank');
define('HIGHSCORES_HEADER_USER', 'user');
define('HIGHSCORES_HEADER_PERCENTAGE', 'percentage');
/**#@-*/

/**#@+
 * Language constants used by the {@link include.php include} action
 */
// include
define('ERROR_CIRCULAR_REFERENCE', 'Circular reference detected!');
define('ERROR_TARGET_ACL', "You aren't allowed to read included page <tt>%s</tt>");

/**#@-*/

/**#@+
 * Language constant used by the {@link lastedit.php lastedit} action
 */
// lastedit
define('LASTEDIT_DESC', 'Last edited by %s'); // %s user name
define('LASTEDIT_DIFF_LINK_TITLE', 'Show differences from last revision');
/**#@-*/

/**#@+
 * Language constant used by the {@link lastusers.php lastusers} action
 */
// lastusers
define('LASTUSERS_CAPTION', 'Các thành viên mới đăng ký');
define('SIGNUP_DATE_TIME', 'Ngày giờ đăng ký');
define('NAME_TH', 'Tên tài khoảng');
define('OWNED_PAGES_TH', 'Trang sở hữu');
define('SIGNUP_DATE_TIME_TH', 'Ngày giờ đăng ký');
/**#@-*/

/**#@+
 * Language constant used by the {@link mindmap.php mindmap} action
 */
// mindmap
define('MM_JRE_INSTALL_REQ', 'Vui lòng cài đặt %s (JRE) trên máy của bạn.'); // %s - JRE install link
define('MM_DOWNLOAD_LINK_DESC', 'Tải về ánh xạ tư duy này');
define('MM_EDIT', 'Use %s to edit it'); // %s - link to freemind project
define('MM_FULLSCREEN_LINK_DESC', 'Mở ở chế độ toàn màn hình');
define('ERROR_INVALID_MM_SYNTAX', 'Lỗi: cú pháp ánh xạ tư duy không hợp lệ.');
define('PROPER_USAGE_MM_SYNTAX', 'Cách dùng đúng: %1$s hoặc %2$s'); // %1$s - syntax sample 1; %2$s - syntax sample 2
/**#@-*/

/**#@+
 * Language constant used by the {@link mychanges.php mychanges} action
 */
// mychanges
define('NO_PAGES_EDITED', 'Bạn chưa sửa trang này xong.');
define('MYCHANGES_ALPHA_LIST', "Đây là danh sách các trang soạn bởi %s cùng với thời gian của lần cập nhật cuối.");
define('MYCHANGES_DATE_LIST', "Đây là danh sách các trang soạn bởi %s, sắp xếp theo thời gian cập nhật cuối.");
define('ORDER_DATE_LINK_DESC', 'sắp xếp theo ngày');
define('ORDER_ALPHA_LINK_DESC', 'sắp xếp theo thứ tự ABC');
define('MYCHANGES_NOT_LOGGED_IN', "Bạn chưa đăng nhập, vì thế danh sách các trang bạn soạn không thể xem được.");
/**#@-*/

/**#@+
 * Language constant used by the {@link mypages.php mypages} action
 */
// mypages
define('OWNED_PAGES_TXT', "Danh sách các trang sở hữu bởi %s.");
define('OWNED_NO_PAGES', 'You don\'t own any pages.');
define('OWNED_NONE_FOUND', 'Không tìm thấy trang nào.');
define('OWNED_NOT_LOGGED_IN', "You're not logged in, thus the list of your pages couldn't be retrieved.");
/**#@-*/

/**#@+
 * Language constant used by the {@link newpage.php newpage} action
 */
// newpage
define('NEWPAGE_CREATE_LEGEND', 'Tạo trang mới');
define('NEWPAGE_CREATE_BUTTON', 'Tạo');
/**#@-*/

/**#@+
 * Language constant used by the {@link orphanedpages.php orphanedpages} action
 */
// orphanedpages
define('NO_ORPHANED_PAGES', 'No orphaned pages. Good!');

/**#@+
 * Language constant used by the {@link ownedpages.php ownedpages} action
 */
// ownedpages
define('OWNEDPAGES_COUNTS', 'You own %1$s pages out of the %2$s pages on this Wiki.'); // %1$s - number of pages owned; %2$s - total number of pages
define('OWNEDPAGES_PERCENTAGE', 'That means you own %s of the total.'); // %s percentage of pages owned
/**#@-*/

/**#@+
 * Language constant used by the {@link pageindex.php pageindex} action
 */
// pageindex
define('PAGEINDEX_HEADING', 'Page Index');
define('PAGEINDEX_CAPTION', 'This is an alphabetical list of pages you can read on this server.');
define('PAGEINDEX_OWNED_PAGES_CAPTION', 'Items marked with a * indicate pages that you own.');
define('PAGEINDEX_ALL_PAGES', 'All');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges.php recentchanges} action
 */
// recentchanges
define('RECENTCHANGES_HEADING', 'Những trang thay đổi gần đây');
define('REVISIONS_LINK_TITLE', 'Xem danh sách các phiên bản mới nhất của %s'); // %s - page name
define('HISTORY_LINK_TITLE', 'Xem lịch sử của trang %s'); // %s - page name
define('WIKIPING_ENABLED', 'WikiPing enabled: Changes on this wiki are broadcast to %s'); // %s - link to wikiping server
define('RECENTCHANGES_NONE_FOUND', 'Không có các trang nào thay đổi gần đây.');
define('RECENTCHANGES_NONE_ACCESSIBLE', 'Bạn chưa có quyền xem những trang thay đổi gần đây.');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentcomments.php recentcomments} action
 */
// recentcomments
define('RECENTCOMMENTS_HEADING', 'Bình luận gần đây');
define('RECENTCOMMENTS_TIMESTAMP_CAPTION', '%s'); // %s - timestamp
define('RECENTCOMMENTS_NONE_FOUND', 'Không có bình luận.');
define('RECENTCOMMENTS_NONE_ACCESSIBLE', 'Không có bình luận nào bạn có quyền xem.');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentlycommented.php recentlycommented} action
 */
// recentlycommented
define('RECENTLYCOMMENTED_HEADING', 'Recently commented pages');
define('RECENTLYCOMMENTED_NONE_FOUND', 'There are no recently commented pages.');
define('RECENTLYCOMMENTED_NONE_ACCESSIBLE', 'There are no recently commented pages you have access to.');
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
define('SEARCH_FOR', 'Tìm');
define('SEARCH_ZERO_MATCH', 'No matches');
define('SEARCH_ONE_MATCH', 'One match found');
define('SEARCH_N_MATCH', '%d matches found'); // %d - number of hits
define('SEARCH_RESULTS', 'Search results: <strong>%1$s</strong> for <strong>%2$s</strong>'); # %1$s: n matches for | %2$s: search term
define('SEARCH_NOT_SURE_CHOICE', 'Not sure which page to choose?');
define('SEARCH_EXPANDED_LINK_DESC', 'Expanded Text Search'); // search link description
define('SEARCH_TRY_EXPANDED', 'Try the %s which shows surrounding text.'); // %s expanded search link
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
define('SEARCH_TIPS', 'Mẹo tìm kiếm:');
define('SEARCH_WORD_1', 'táo');
define('SEARCH_WORD_2', 'chuối');
define('SEARCH_WORD_3', 'trái cây');
define('SEARCH_WORD_4', 'macintosh');
define('SEARCH_WORD_5', 'vài');
define('SEARCH_WORD_6', 'từ');
define('SEARCH_PHRASE',sprintf('%s %s',SEARCH_WORD_5,SEARCH_WORD_6));
define('SEARCH_TARGET_1', 'Find pages that contain at least one of the two words.');
define('SEARCH_TARGET_2', 'Find pages that contain both words.');
define('SEARCH_TARGET_3',sprintf("Find pages that contain the word '%1\$s' but not '%2\$s'.",SEARCH_WORD_1,SEARCH_WORD_4));
define('SEARCH_TARGET_4',"Find pages that contain words such as 'apple', 'apples', 'applesauce', or 'applet'."); // make sure target words all *start* with SEARCH_WORD_1
define('SEARCH_TARGET_5',sprintf("Find pages that contain the exact phrase '%1\$s' (for example, pages that contain '%1\$s of wisdom' but not '%2\$s noise %3\$s').",SEARCH_PHRASE,SEARCH_WORD_5,SEARCH_WORD_6));
/**#@-*/

/**#@+
 * Language constant used by the {@link usersettings.php usersettings} action
 */
// usersettings
// - error messages
define('ERROR_EMPTY_USERNAME', 'Vui lòng cho biết tên của bạn.');
define('ERROR_NONEXISTENT_USERNAME', 'Xin lỗi. Tên này không tồn tại.'); // @@@ too specific
define('ERROR_RESERVED_PAGENAME', 'Xin lỗi. Tên này dành riêng cho việc đặt tên trang. Vui lòng chọn tên khác.');
define('ERROR_WIKINAME', 'Tên người dùng phải ở dạng %1$s, ví dụ %2$s.'); // %1$s - identifier WikiName; %2$s - sample WikiName
define('ERROR_EMPTY_EMAIL_ADDRESS', 'Vui lòng cho biết email.');
define('ERROR_INVALID_EMAIL_ADDRESS', 'Dường như bạn chưa chỉ ra một email thật sự.');
define('ERROR_INVALID_PASSWORD', 'Xin lỗi! Bạn đã chỉ ra mật mã chưa hợp lệ.');	// @@@ too specific
define('ERROR_INVALID_HASH', 'Mật mã nhắc nhở không đúng.');
define('ERROR_INVALID_OLD_PASSWORD', 'Bạn đã gõ sai mật mã cũ.');
define('ERROR_EMPTY_PASSWORD', 'Vui lòng cho biết mật mã.');
define('ERROR_EMPTY_PASSWORD_OR_HASH', 'Vui lòng cho biết mật mã hoặc mật mã nhắc nhở.');
define('ERROR_EMPTY_CONFIRMATION_PASSWORD', 'Vui lòng xác nhận lại mật mã để đăng ký tài khoản mới.');
define('ERROR_EMPTY_NEW_CONFIRMATION_PASSWORD', 'Vui lòng xác nhận lại mật mã để cập nhận thông tin cá nhân.');
define('ERROR_EMPTY_NEW_PASSWORD', 'Vui lòng cho biết mậ mã mới.');
define('ERROR_PASSWORD_MATCH', 'Hai mật mã không khớp nhau.');
define('ERROR_PASSWORD_NO_BLANK', 'Ồ không! Mật mã không thể trống trơn như vậy!');
define('ERROR_PASSWORD_TOO_SHORT', 'Mật mã phải chứa ít nhất %d ký tự.'); // %d - minimum password length
define('ERROR_INVALID_REVISION_DISPLAY_LIMIT', 'The number of page revisions should not exceed %d.'); // %d - maximum revisions to view
define('ERROR_INVALID_RECENTCHANGES_DISPLAY_LIMIT', 'The number of recently changed pages should not exceed %d.'); // %d - maximum changed pages to view
if(!defined('ERROR_VALIDATION_FAILED')) define('ERROR_VALIDATION_FAILED', "Chứng thực đăng ký bị lỗi. Vui lòng thử lại!");
// - success messages
define('SUCCESS_USER_LOGGED_OUT', 'Đã thoát thành công khỏi hệ thống.');
define('SUCCESS_USER_REGISTERED', 'Đã đăng ký thành công!');
define('SUCCESS_USER_SETTINGS_STORED', 'Thiết lập cá nhân đã được lưu!');
define('SUCCESS_USER_PASSWORD_CHANGED', 'Đã cập nhật chìa khóa!');
// - captions
define('NEW_USER_REGISTER_CAPTION', 'Nếu bạn đăng ký tài khoản mới:');
define('REGISTERED_USER_LOGIN_CAPTION', 'Nếu bạn đã có tài khoản, đăng nhập ở đây:');
define('RETRIEVE_PASSWORD_CAPTION', 'Log in with your [[%s password reminder]]:'); //%s PasswordForgotten link
define('USER_LOGGED_IN_AS_CAPTION', 'You are logged in as %s'); // %s user name
// - form legends
define('USER_ACCOUNT_LEGEND', 'Tài khoản của bạn');
define('USER_SETTINGS_LEGEND', 'Thiết lập');
define('LOGIN_REGISTER_LEGEND', 'Đăng nhập/Đăng ký');
define('LOGIN_LEGEND', 'Đăng nhập');
#define('REGISTER_LEGEND', 'Register'); // @@@ TODO to be used later for register-action
define('CHANGE_PASSWORD_LEGEND', 'Change your password');
define('RETRIEVE_PASSWORD_LEGEND', 'Password forgotten');
// - form field labels (should end in ':' _unless_ it's a checkbox or radio button option)
define('USERSETTINGS_REDIRECT_AFTER_LOGIN_LABEL', 'Redirect to %s after login');	// %s page to redirect to
define('USER_EMAIL_LABEL', 'Your email address:');
define('DOUBLECLICK_LABEL', 'Doubleclick editing:');
define('SHOW_COMMENTS_LABEL', 'Show comments by default:');
define('DEFAULT_COMMENT_STYLE_LABEL', 'Default comment style');
define('COMMENT_ASC_LABEL', 'Phẳng (cũ trước)');
define('COMMENT_DEC_LABEL', 'Phẳng (mới trước)');
define('COMMENT_THREADED_LABEL', 'Luồng');
define('COMMENT_DELETED_LABEL', '[Comment deleted]');
define('COMMENT_BY_LABEL', 'Bình luận bởi ');
define('RECENTCHANGES_DISPLAY_LIMIT_LABEL', 'RecentChanges display limit:');
define('PAGEREVISION_LIST_LIMIT_LABEL', 'Page revisions list limit:');
define('NEW_PASSWORD_LABEL', 'Your new password:');
define('NEW_PASSWORD_CONFIRM_LABEL', 'Confirm new password:');
define('NO_REGISTRATION', 'Registration on this wiki is disabled.');
define('PASSWORD_LABEL', 'Password (%s+ chars):'); // %s minimum number of characters
define('CONFIRM_PASSWORD_LABEL', 'Confirm password:');
define('TEMP_PASSWORD_LABEL', 'Password reminder:');
define('INVITATION_CODE_SHORT', 'Invitation Code');
define('INVITATION_CODE_LONG', 'In order to register, you must fill in the invitation code sent by this website\'s administrator.');
define('INVITATION_CODE_LABEL', 'Your %s:'); // %s - expanded short invitation code prompt
define('WIKINAME_SHORT', 'WikiName');
define('WIKINAME_LONG',sprintf('A WikiName is formed by two or more capitalized words without space, e.g. %s',WIKKA_SAMPLE_WIKINAME));
define('WIKINAME_LABEL', 'Your %s:'); // %s - expanded short wiki name prompt
// - form options
define('CURRENT_PASSWORD_OPTION', 'Mật mã hiện tại');
define('PASSWORD_REMINDER_OPTION', 'Mật mã nhắc nhở');
// - form buttons
define('UPDATE_SETTINGS_BUTTON', 'Cập nhật thông tin');
define('LOGIN_BUTTON', 'Đăng nhập');
define('LOGOUT_BUTTON', 'Thoát');
define('CHANGE_PASSWORD_BUTTON', 'Đổi mật mã');
define('REGISTER_BUTTON', 'Đăng ký');
/**#@-*/

/**#@+
 * Language constant used by the {@link wantedpages.php wantedpages} action
 */
// wantedpages
define('SORTING_LEGEND', 'Sắp xếp ...');
define('SORTING_NUMBER_LABEL', 'Sắp xếp #%d:');
define('SORTING_DESC_LABEL', 'giảm');
define('OK_BUTTON', '   OK   ');
define('NO_WANTED_PAGES', 'Không có trang chờ. Tốt lắm!');
/**#@-*/

/**#@+
 * Language constant used by the {@link wikkaconfig.php wikkaconfig} action
 */
//wikkaconfig
define('WIKKACONFIG_CAPTION', "Wikka Configuration Settings [%s]"); // %s link to Wikka Config options documentation
define('WIKKACONFIG_DOCS_URL', "http://docs.wikkawiki.org/ConfigurationOptions");
define('WIKKACONFIG_DOCS_TITLE', "Read the documentation on Wikka Configuration Settings");
define('WIKKACONFIG_TH_OPTION', "Option");
define('WIKKACONFIG_TH_VALUE', "Value");

/* ------------------ 3RD PARTY ------------------ */

/**#@+
 * Language constant used by the {@link fullscreen.php fullscreen} 3rd party MindMap display utility
 */
// fullscreen
define('CLOSE_WINDOW', 'Đóng cửa sổ');
define('MM_GET_JAVA_PLUGIN_LINK_DESC', 'get the latest Java Plug-in here'); // used in MM_GET_JAVA_PLUGIN
define('MM_GET_JAVA_PLUGIN', 'so if it does not work, %s.'); // %s - plugin download link
/**#@-*/


/* ------------------ FORMATTERS ------------------ */

/**#@+
 * Language constant used by the {@link wakka.php wakka} formatter
 */
// wakka
define('GRABCODE_BUTTON', 'Grab');
define('GRABCODE_BUTTON_TITLE', 'Download %s'); // %s download filename
/**#@-*/


/* ------------------ HANDLERS (PAGE) ------------------ */

/**#@+
 * Language constant used by the {@link acls.php acls} (page) handler
 */
// acls
// TODO: 'translate' DB value '(Public)' when displaying it!
define('ACLS_UPDATED', 'Access control lists updated.');
define('NO_PAGE_OWNER', '(Nobody)');
define('NOT_PAGE_OWNER', 'You are not the owner of this page.');
define('PAGE_OWNERSHIP_CHANGED', 'Ownership changed to %s'); // %s - name of new owner
define('ACLS_LEGEND', 'Access Control Lists for %s'); // %s - name of current page
define('ACLS_READ_LABEL', 'Read ACL:');
define('ACLS_WRITE_LABEL', 'Write ACL:');
define('ACLS_COMMENT_READ_LABEL', 'Comment Read ACL:');
define('ACLS_COMMENT_POST_LABEL', 'Comment Post ACL:');
define('SET_OWNER_LABEL', 'Set Page Owner:');
define('SET_OWNER_CURRENT_OPTION', '(Current Owner)');
define('SET_OWNER_PUBLIC_OPTION', '(Public)'); // actual DB value will remain '(Public)' even if this option text is translated!
define('SET_NO_OWNER_OPTION', '(Nobody - Set free)');
define('ACLS_STORE_BUTTON', 'Lưu ACL');
define('CANCEL_BUTTON', 'Bỏ qua');
// - syntax
define('ACLS_SYNTAX_HEADING', 'Cú pháp:');
define('ACLS_EVERYONE', 'Mọi người');
define('ACLS_REGISTERED_USERS', 'Số người dùng có đăng ký');
define('ACLS_NONE_BUT_ADMINS', 'Không ai (trừ admin)');
define('ACLS_ANON_ONLY', 'Chỉ dành cho khách');
define('ACLS_LIST_USERNAMES', 'dành cho người dùng %s; liệt kê mỗi người dùng trên một dòng'); // %s - sample user name
define('ACLS_NEGATION', 'Any of these items can be negated with a %s:'); // %s - 'negation' mark
define('ACLS_DENY_USER_ACCESS', '%s sẽ bị ngăn cản truy cập'); // %s - sample user name
define('ACLS_AFTER', 'sau');
define('ACLS_TESTING_ORDER1', 'ACLs được kiểm tra theo thứ tự đã chỉ ra:');
define('ACLS_TESTING_ORDER2', 'So be sure to specify %1$s on a separate line %2$s negating any users, not before.'); // %1$s - 'all' mark; %2$s - emphasised 'after'
define('ACLS_DEFAULT_ACLS', 'Danh sách rỗng sẽ được thiết lập giá trị mặc định như chỉ ra trong %s.');
/**#@-*/

/**#@+
 * Language constant used by the {@link backlinks.php backlinks} (page) handler
 */
// backlinks
define('BACKLINKS_HEADING', 'Các trang liên kết tới %s');
define('BACKLINKS_NO_PAGES', 'Không có liên kết ngược cho trang này.');
/**#@-*/

/**#@+
 * Language constant used by the {@link claim.php claim} (page) handler
 */
// claim
define('USER_IS_NOW_OWNER', 'Bây giờ bạn là chủ sở hữu trang này.');
/**#@-*/

/**#@+
 * Language constant used by the {@link clone.php clone} (page) handler
 */
// clone
define('ERROR_ACL_WRITE', 'Bạn chưa có quyền ghi vào trang  %s');
define('CLONE_VALID_TARGET', 'Vui lòng cho tên thích hợp để nhân bản và vài ghi chú (tùy chọn):');
define('CLONE_LEGEND', 'Nhân bản trang %s'); // %s source page name
define('CLONED_FROM', 'Nhân bản từ trang %s'); // %s source page name
define('SUCCESS_CLONE_CREATED', 'trang %s đã được tạo ra!'); // %s new page name
define('CLONE_X_TO_LABEL', 'Nhân bản với tên:');
define('CLONE_EDIT_NOTE_LABEL', 'Ghi chú:');
define('CLONE_EDIT_OPTION_LABEL', ' Sửa sau khi nhân bản');
define('CLONE_ACL_OPTION_LABEL', ' Nhân bản ACL');
define('CLONE_BUTTON', 'Nhân bản');
/**#@-*/

/**#@+
 * Language constant used by the {@link delete.php delete} (page) handler
 */
// delete
define('ERROR_NO_PAGE_DEL_ACCESS', 'Bạn chưa có quyền xóa trang này.');
define('PAGE_DELETION_HEADER', 'Xóa trang %s'); // %s - name of the page
define('SUCCESS_PAGE_DELETED', 'Trang vừa được xóa!');
define('PAGE_DELETION_CAPTION', 'Xóa trang này, cùng toàn bộ các bình luận của nó?');
define('PAGE_DELETION_DELETE_BUTTON', 'Xóa trang');
define('PAGE_DELETION_CANCEL_BUTTON', 'Bỏ qua');
/**#@-*/

/**#@+
 * Language constant used by the {@link diff.php diff} (page) handler
 */
// diff
define('ERROR_DIFF_LIBRARY_MISSING', 'Tập tin <tt>'.WIKKA_LIBRARY_PATH.DIRECTORY_SEPARATOR.'diff.lib.php</tt> không được tìm thấy. Vui lòng liên hệ người quản trị hệ thống.');
define('ERROR_BAD_PARAMETERS', 'Tham số bạn chỉ ra không hợp lệ: một trong hai phiên bản đã bị xóa.');
define('DIFF_COMPARISON_HEADER', 'So sánh %1$s cho %2$s'); // %1$s - link to revision list; %2$s - link to page
define('DIFF_REVISION_LINK_TITLE', 'Danh sách các phiên bản của trang %s'); // %s page name
define('DIFF_PAGE_LINK_TITLE', 'Xem bản cuối cùng của trang này');
define('DIFF_SAMPLE_ADDITION', 'thêm');
define('DIFF_SAMPLE_DELETION', 'xóa');
define('DIFF_SIMPLE_BUTTON', 'So sánh đơn giản');
define('DIFF_FULL_BUTTON', 'So sánh đầy đủ');
define('HIGHLIGHTING_LEGEND', 'Hướng dẫn về tô màu:');

/**#@-*/

/**#@+
 * Language constant used by the {@link edit.php edit} (page) handler
 */
// edit
define('ERROR_OVERWRITE_ALERT1', 'CẢNH BÁO: bạn và ai khác đang đồng thời soạn trang này.');
define('ERROR_OVERWRITE_ALERT2', 'Vui lòng chép lại các thay đổi của bạn và tiếp tục soan thảo trang.');
define('ERROR_MISSING_EDIT_NOTE', 'Vui lòng ghi chú về thay đổi của bạn!');
define('ERROR_TAG_TOO_LONG', 'Tên trang quá dài. Chỉ tối đa %d ký tự thôi!'); // %d - maximum page name length
define('ERROR_NO_WRITE_ACCESS', 'Bạn chưa có quyền truy cập trang này. Có khi bạn cần [[UserSettings đăng nhập]] hoặc [[UserSettings đăng ký tài khoản]] để tạo, sửa trang này.'); //TODO Distinct links for login and register actions
define('EDIT_STORE_PAGE_LEGEND', 'Lưu trang');
define('EDIT_PREVIEW_HEADER', 'Xem trước');
define('EDIT_NOTE_LABEL', 'Vui lòng thêm ghi chú về thay đổi của bạn'); // label after field, so no colon!
define('MESSAGE_AUTO_RESIZE', 'Click chuột vào %s sẽ tự động thu gọn tên trang để điều chỉnh kích cỡ'); // %s - rename button text
define('EDIT_PREVIEW_BUTTON', 'Xem trước');
define('EDIT_STORE_BUTTON', 'Lưu');
define('EDIT_REEDIT_BUTTON', 'Tiếp tục soạn');
define('EDIT_CANCEL_BUTTON', 'Bỏ qua');
define('EDIT_RENAME_BUTTON', 'Đổi tên');
define('ACCESSKEY_PREVIEW', 'p'); // ideally, should match EDIT_PREVIEW_BUTTON
define('ACCESSKEY_STORE', 's'); // ideally, should match EDIT_STORE_BUTTON
define('ACCESSKEY_REEDIT', 'r'); // ideally, should match EDIT_REEDIT_BUTTON
define('SHOWCODE_LINK', 'Xem mã nguồn wiki của trang này');
define('SHOWCODE_LINK_TITLE', 'Xem mã nguồn'); // @@@ TODO 'View page formatting code'
define('EDIT_COMMENT_TIMESTAMP_CAPTION', '(%s)'); // %s timestamp
if (!defined('ERROR_INVALID_PAGEID')) define('ERROR_INVALID_PAGEID', 'Không có phiên bản với id đã chỉ ra');
/**#@-*/

/**#@+
 * Language constant used by the {@link grabcode.php grabcode} (page) handler
 */
// grabcode
define('ERROR_NO_CODE', 'Xin lỗi: không có mã nào để tải về.');
/**#@-*/

/**#@+
 * Language constant used by the {@link history.php history} (page) handler
 */
// history
define('EDITED_ON', 'Sửa vào thởi điểm %1$s bởi %2$s'); // %1$s - time; %2$s - user name
define('HISTORY_PAGE_VIEW', 'Lịch sử các thay đổi gần đây của trang %s'); // %s pagename
define('OLDEST_VERSION_EDITED_ON_BY', 'Bản cũ nhất của trang này tạo vào thời điểm %1$s bởi %2$s'); // %1$s - time; %2$s - user name
define('MOST_RECENT_EDIT', 'Sửa lần cuối vào %1$s bởi %2$s');
define('HISTORY_MORE_LINK_DESC', 'here'); // used for alternative history link in HISTORY_MORE
define('HISTORY_MORE', 'Lịch sử đầy đủ của trang này không thể xem vừa vẹn trên chỉ một trang. Click chuột vào %s để xem thêm.'); // %s alternative history link # @@@ TODO avoid using 'here' ^
/**#@-*/

/**#@+
 * Language constant shared by the {@link processcomment.php processcomment} and {@link show.php show} (page) handlers
 */
// processcomment & show
// - comment buttons
define('COMMENT_DELETE_BUTTON', 'Xóa');
define('COMMENT_REPLY_BUTTON', 'Trả lời');
define('COMMENT_ADD_BUTTON', 'Thêm bình luận');
define('COMMENT_NEW_BUTTON', 'Bình luận mới');
/**#@-*/

/**#@+
 * Language constant used by the {@link processcomment.php processcomment} (page) handler
 */
// processcomment
define('ERROR_NO_COMMENT_DEL_ACCESS', 'Bạn chưa có quyền xóa bình luận!');
define('ERROR_NO_COMMENT_WRITE_ACCESS', 'Bạn chưa có quyền gửi bình luận ở trang này');
define('ERROR_EMPTY_COMMENT', 'Nội dung bình luận chưa có gì!');
define('ADD_COMMENT_LABEL', 'Trả lời cho %s:');
define('NEW_COMMENT_LABEL', 'Gửi bình luận:');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges_simple.xml.mm.php recentchanges_simple.xml.mm} (page) handler
 */
// recentchanges_simple.xml.mm
define('FIRST_NODE_LABEL', 'Thay đổi gần đây');
/**#@-*/

/**#@+
 * Language constant used by the {@link recentchanges.xml.php recentchanges.xml} (page) handler
 */
// recentchanges.xml
define('RECENTCHANGES_DESC', 'Thay đổi gần đây của trang %s'); // %s - page name
/**#@-*/

/**#@+
 * Language constant shared by the {@link referrers_sites.php referrers_sites}, {@link referrers.php referrers} and {@link review_blacklist.php review_blacklist} (page) handlers
 */
// referrers_sites + referrers + review_blacklist
define('REFERRERS_PURGE_24_HOURS', '24 giờ cuối');
define('REFERRERS_PURGE_N_DAYS', '%d ngày vừa qua'); // %d number of days
define('REFERRERS_NO_SPAM', 'Lời nhắn đến người người quấy rối: Trang này không được đánh dấu bởi các chương trình tìm kiếm; vì thế đừng mất thời gian của bạn với trang này!');
define('REFERRERS_DOMAINS_TO_WIKI_LINK_DESC', 'Xem tên miền tham chiếu đến wiki');
define('REFERRERS_DOMAINS_TO_PAGE_LINK_DESC', 'Xem tên miền tham chiếu đến %s'); // %s - page name
define('REFERRERS_URLS_TO_WIKI_LINK_DESC', 'Xem tham chiếu toàn cục');
define('REFERRERS_URLS_TO_PAGE_LINK_DESC', 'Xem địa chỉ tham chiếu cho trang %s'); // %s - page name
define('REFERRER_BLACKLIST_LINK_DESC', 'Xem danh sách địa chỉ tham chiếu đen');
define('BLACKLIST_LINK_DESC', 'Danh sách đen');
define('NONE_CAPTION', 'Không');
define('PLEASE_LOGIN_CAPTION', 'Bạn cần đăng nhập để xem danh sách các tham chiếu');
/**#@-*/

/**#@+
 * Language constant used by the {@link referrers_sites.php referrers_sites} (page) handler
 */
// referrers_sites
define('REFERRERS_URLS_LINK_DESC', 'Danh các các địa chỉ khác nhau');
define('REFERRERS_DOMAINS_TO_WIKI', 'Các trang, tên miền tham khảo đến wiki (%s)'); // %s - link to referrers handler
define('REFERRERS_DOMAINS_TO_PAGE', 'Các trang, tên miền tham khảo đến trang %1$s %2$s (%3$s)'); // %1$s - page link; %2$s - purge time; %3$s - link to referrers handler
/**#@-*/

/**#@+
 * Language constant used by the {@link referrers.php referrers} (page) handler
 */
// referrers
define('REFERRERS_DOMAINS_LINK_DESC', 'Xem danh sách tên miền');
define('REFERRERS_URLS_TO_WIKI', 'Liên kết ngoài tham chiếu đến wiki (%s)'); // %s - link to referrers_sites handler
define('REFERRERS_URLS_TO_PAGE', 'Liên kết ngoài tham chiếu đến trang %1$s %2$s (%3$s)'); // %1$s - page link; %2$s - purge time; %3$s - link to referrers_sites handler
/**#@-*/

/**#@+
 * Language constant used by the {@link review_blacklist.php review_blacklist} (page) handler
 */
// review_blacklist
define('BLACKLIST_HEADING', 'Danh sách liên đen các tham chiếu');
define('BLACKLIST_REMOVE_LINK_DESC', 'Xóa');
define('STATUS_BLACKLIST_EMPTY', 'Không có gì trong danh sách đen.');
/**#@-*/

/**#@+
 * Language constant used by the {@link revisions.php revisions} (page) handler
 */
// revisions
define('REVISIONS_CAPTION', 'Phiên bản cho trang %s'); // %s pagename
define('REVISIONS_NO_REVISIONS_YET', 'Chưa có phiên bản nào của trang này');
define('REVISIONS_SIMPLE_DIFF', 'So sánh đơn giản');
define('REVISIONS_MORE_CAPTION', 'Có vài phiên bản khác không được trình bày ở đây. Click chuột vào nút %s để xem các phiên bản này'); // %S - text of REVISIONS_MORE_BUTTON
define('REVISIONS_RETURN_TO_NODE_BUTTON', 'Trở về nốt / Bỏ qua');
define('REVISIONS_SHOW_DIFFERENCES_BUTTON', 'Xem sự khác biệt');
define('REVISIONS_MORE_BUTTON', 'Tiếp...');
/**#@-*/

/**#@+
 * Language constant used by the {@link revisions.xml.php revisions.xml} (page) handler
 */
// revisions.xml
define('REVISIONS_EDITED_BY', 'Sửa bởi %s'); // %s user name
define('HISTORY_REVISIONS_OF', 'Lịch sử của trang %s'); // %s - page name
/**#@-*/

/**#@+
 * Language constant used by the {@link show.php show} (page) handler
 */
// show
define('SHOW_RE_EDIT_BUTTON', 'Sửa lại phiên bản cũ này');
define('SHOW_FORMATTED_BUTTON', 'Xem bản đã định dạng');
define('SHOW_SOURCE_BUTTON', 'Xem mã nguồn');
define('SHOW_ASK_CREATE_PAGE_CAPTION', 'Trang này chưa tồi tại. Bạn có muốn %s nó?'); // %s - page create link
define('SHOW_OLD_REVISION_CAPTION', 'Đây là phiên bản cũ của trang %1$s tạo bởi %2$s vào thời điểm %3$s.'); // %1$s - page link; %2$s - username; %3$s - timestamp;
define('COMMENTS_CAPTION', 'Bình luận');
define('DISPLAY_COMMENTS_LABEL', 'Xem bình luận');
define('DISPLAY_COMMENT_LINK_DESC', 'Xem bình luận');
define('DISPLAY_COMMENTS_EARLIEST_LINK_DESC', 'Bài cũ trước');
define('DISPLAY_COMMENTS_LATEST_LINK_DESC', 'Bài mới trước');
define('DISPLAY_COMMENTS_THREADED_LINK_DESC', 'Luồng');
define('HIDE_COMMENTS_LINK_DESC', 'Che bình luận');
define('STATUS_NO_COMMENTS', 'Chưa có bình luận nào.');
define('STATUS_ONE_COMMENT', 'Một bình luận.');
define('STATUS_SOME_COMMENTS', 'Có %d bình luận.'); // %d - number of comments
define('COMMENT_TIME_CAPTION', '%s'); // %s comment time
/**#@-*/

/**#@+
 * Language constant used by the {@link showcode.php showcode} (page) handler
 */
// showcode
define('SOURCE_HEADING', 'Mã nguồn wiki cho trang %s'); // %s - page link
define('SHOW_RAW_LINK_DESC', 'Xem mã nguồn nguyên thủy');
/**#@-*/

/* ------------------ LIBS ------------------*/

/**#@+
 * Language constant used by the {@link Wakka.class.php Wakka class} (the Wikka core containing most methods)
 */
// Wakka.class
define('QUERY_FAILED', 'Truy vẫn bị lỗi:');
define('REDIR_DOCTITLE', 'Chuyển hướng đến %s'); // %s - target page
define('REDIR_LINK_DESC', 'liên kết này'); // used in REDIR_MANUAL_CAPTION
define('REDIR_MANUAL_CAPTION', 'Trường hợp trình duyệt của bạn không chuyển trang, vui lòng dùng liên kết %s'); // %s target page link
define('CREATE_THIS_PAGE_LINK_TITLE', 'Tạo trang này');
define('ACTION_UNKNOWN_SPECCHARS', 'Không rõ action. Tên của action không được chứa ký tự đặc biệt.');
define('ACTION_UNKNOWN', 'Action không xác định: "%s"'); // %s - action name
define('HANDLER_UNKNOWN_SPECCHARS', 'Handler không xác định. Tên của handler không được chứa ký tự đặc biệt.');
define('HANDLER_UNKNOWN', 'Handler không xác định: %s. Xin lỗi bạn!'); // %s handler name
define('FORMATTER_UNKNOWN_SPECCHARS', 'Formatter không xác định. Tên của formatter không được chứa ký tực đặc biệt.');
define('FORMATTER_UNKNOWN', 'Không tìm thấy formatter "%s"'); // %s formatter name
/**#@-*/

/* ------------------ SETUP ------------------ */
/**#@+
 * Language constant used by the {@link index.php setup} program (and several included files)
 */
// @@@ later....
/**#@-*/

?>
