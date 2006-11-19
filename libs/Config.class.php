<?php
/**
 * This file is part of Wikka, a PHP wiki engine.
 * 
 * It includes a pseudo-class, which provides the config entries used by Wikka core. 
 *
 * @package Config
 */
class Config 
{
	/**#@+
	 * @access public   
	 * @var string   
	 */
	/**
	 * Mysql hostname.
	 * The name of the mysql server. By default, and in general, this is localhost.
	 */
	var $mysql_host = 'localhost';
	/**
	 * Mysql database name.
	 * The name of the database where wikka tables are stored. This database must exist before installing Wikka.
	 * When upgrading, the handling of the database is automatic, but you could perform a backup if you wish (and you are encouraged to do so!)
	 */
	var $mysql_database = 'wikka';
	/**
	 * Mysql user.
	 * A username to which SELECT/UPDATE/INSERT/DELETE operations to the database are granted.
	 */
	var $mysql_user = 'wikka';
	/**
	 * Mysql table prefix.
	 * A prefix for all tables used by Wikka.
	 */
	var $table_prefix = 'wikka_';
	/**
	 * Wikkasite's Homepage.
	 * The root page (or home page) of your Wikka site. This should be a CamelCased word.
	 */
	var $root_page = 'HomePage';
	/**
	 * Wikkasite's name.
	 * The name of your wikka site. This should be CamelCase. This name will be included in the title of all pages in your wiki as a prefix.
	 */
	var $wakka_name = 'MyWikkaSite';
	/**
	 * Wikkasite's base url.
	 * The base url of your site. This should <b>end with a slash</b> if you enable rewrite_mode! This is in the form of http://wikkawiki.org/ or
	 * http://css.openformats.org/wikka.php?wakka=
	 */
	var $base_url;
	/**
	 * Cookies' suffix.
	 * <p>Suffix of the cookies used by Wikka. If you run more than one instance of Wikka on the same server, you should provide two different
	 * values for this config entry to avoid a session leakage.</p>
	 * <p>Important note: Actually, Wikka's cookies don't include the path and may be shared to other applications on your webserver.
	 * Some other web application may require that cookies they receive don't include illegal characters like @.</p>
	 */
	var $wiki_suffix = '@wikka';
	/**
	 * Path to action files.
	 * Name of the directory under which your actions files are stored. Markup syntax for actions are double brackets 
	 * (<kbd>{{actionname param1="value1"...}}</kbd>), and when a such markup is processed, Wikka includes the file named 'actionname.php'
	 * located at the value of this config entry. See {@link Wakka::Action()}.
	 */
	var $action_path = 'actions';
	/**
	 * Path to handler files.
	 * Name of the directory under which your handler files are stored. Handlers answer the questions: "How to show you the content of the page?"
	 * or "What should wikka do with this page?". The default handler is show. To specify a handler to the Wikka site, you suffix the pagename
	 * with one forward slash and the name of the handler, like http://wikkawiki.org/HomePage/acls
	 */
	var $handler_path = 'handlers';
	/**
	 * Edit buttons position.
	 * Valid values for this config entry are bottom, top and both. Actually, any invalid value is considered as if it was both.
	 * This entry lets you customize the placement of the Store/Preview/Cancel buttons when editing a page : whether at the top of the page body
	 * (before the wikiedit toolbar), or at the bottom (below the wikiedit toolbar, this is the default value) or both.
	 */
	var $edit_buttons_position = 'bottom';
	/**
	 * Stylesheet name.
	 * Name of the stylesheet used by your Wikka site. This entry must not be blanks, and it must point to an existing (<em>and valid</em>) css
	 * file located under the css folder. Dafault value is wikka.css.
	 */
	var $stylesheet = 'wikka.css';

	// formatter and code highlighting paths
	/**
	 * Path to the wikka formatter.
	 * Location of the Wikka formatter. This entry is required. Formatters are special files containing codes that translate a page written in
	 * Wikka Syntax into html. See {@link Wakka::Format()}.
	 */
	var $wikka_formatter_path = 'formatters';
	/**
	 * Path to the wikka highlighters formatter.
	 * Location of the Wikka code highlighters. This entry is required. Highlighters are special files containing codes that enhance the
	 * readability of some snippets written in a known language by using special colors for keywords, strings, variables, ... and/or 
	 * providing links to official documentation of functions. Wikka uses a 3rdparty highlighter named GeSHi (see {@link Config::$geshi_path}
	 * but when this highlighter cannot process a given language, the highlighting is handled by Wikka (if available).
	 */
	var $wikka_highlighters_path = 'formatters';
	/**
	 * Path to the GeSHi package.
	 * Location of the GeSHi package. GeSHi is a 3rdparty package used to highlight code snippets. See {@link Config::$wikka_highlighters_path}.
	 * GeSHi supports about 70 different programming languages, for each one of them a different file for highlighting is used. Those files are
	 * stored in a folder configured as {@link Config::$geshi_languages_path geshi_language_path}.
	 */
	var $geshi_path = '3rdparty/plugins/geshi';
	/**
	 * Path to GeSHi language files.
	 * Location of the GeSHi language highlighting files
	 */
	var $geshi_languages_path = '3rdparty/plugins/geshi/geshi';

	/**
	 * Name of header action.
	 * Name of the action used to generate the header of the output html file. By default, this action inserts everything needed at the 
	 * top of a valid XHTML file, from the doctype declaration, including the &lt;head> tag to the header part of the <body> tag that 
	 * includes {@link Config::$navigation_links navigation links}. See {@link Config::$action_path} , {@link Wakka::Header()} and 
	 * {@link Wakka::Action()}. The header and
	 * the footer actions are not used when generating filetype other than html.
	 */
	var $header_action = 'header';
	/**
	 * Name of the footer action.
	 * Name of the action used to generate the footer of the output html file. By default, this action inserts the bottom menu containing
	 * links to handlers, links to validating tools and the Wikka official website, the page generation time, and eventually a log of the 
	 * queries used for generating the page. The header and
	 * the footer actions are not used when generating filetype other than html.
	 */
	var $footer_action = 'footer';

	/**
	 * Default navigation links.
	 * Navigation links for not logged-in users. This value should be formatted as a well formed Wakka syntax that can be passed to 
	 * {@link Wakka::Format()}. 
	 */
	var $navigation_links = '[[CategoryCategory Categories]] :: PageIndex ::  RecentChanges :: RecentlyCommented :: [[UserSettings Login/Register]]';
	/**
	 * Logged-in navigation links.
	 * Navigation links for logged-in users. See {@link Config::$navigation_links}.
	 */
	var $logged_in_navigation_links = '[[CategoryCategory Categories]] :: PageIndex :: RecentChanges :: RecentlyCommented :: [[UserSettings Change settings/Logout]]';

	/**
	 * Referrers purge time in days.
	 * Number of days to keep referrers list in the database. The {@link Wakka::Maintenance() maintenance} is done automatically, but it may
	 * be a little late sometimes. 0 means no purging. Default value is 30.
	 */
	var $referrers_purge_time = '30';
	/**
	 * Pages purge time in days.
	 * Number of days to keep older revisions of page in database. The {@link Wakka::Maintenance() maintenance} is done automatically, but it may
	 * be a little late sometimes. Default value is 0, which means no purging.
	 */
	var $pages_purge_time = '0';
	/**
	 * Number of entries in XML RecentChanges.
	 * Maximum number of {@link recentchanges.xml.php RecentChanges} to show in XML format. 0 means no max. Default value is 10.
	 */
	var $xml_recent_changes = '10';
	/**
	 * Hide comments.
	 * If this value equals 1, the comments are disable.
	 */
	var $hide_comments = '0';
	/**
	 * Allow anonymous users to delete comments.
	 * If this value equals 1, anonymous users are allowed to delete their own comments. Anonymous users are identified by
	 * the IP address they use, or if Config::$enable_user_host_lookup is enabled, by their hostname. So, be aware that users
	 * having a machine with a dynamically attributed adress IP may be unable to delete their comments later even if this 
	 * config entry is set to 1. Also, it may happen that someone else (anonymous) can delete anonymous comments if his
	 * connection uses the same IP address used to post the comment.
	 */
	var $anony_delete_own_comments = '1';
	/**
	 * System Information visibility.
	 * This config entry enables or disables display of system information in SysInfo. If this value is not set to 1 (by
	 * default, its value is 0), only administrators can view system information. Note that revealing such information to
	 * untrusted user may be a security risk to your webserver.
	 */
	var $public_sysinfo = '0';
	/**
	 * RSS autodiscovery.
	 * Enable (1, default) or disable (0) RSS autodiscovery by adding <link rel="alternate" tags in your html output.
	 * Currently, RSS feed for the revisions of a page or global RSS for any recently changed pages (RecentChanges) of the
	 * whole Wikka site are supported and their existence are indicated by this config entry.
	 */
	var $enable_rss_autodiscovery = '1';
	/**
	 * Require edit note.
	 * If 0 (default), edit note is optional. If 1, edit note is required and if 2, edit note is disabled. As its name suggests,
	 * edit note is a short phrase indicating how the content of the page has changed. It is stored in database, and it appears
	 * on some actions like {@link recentchanges.php recentchanges}, {@link pageindex.php pageindex}, 
	 * {@link lastedit.php lastedit}, ...
	 */
	var $require_edit_note = '0';
	/**
	 * User registration.
	 * Control availability of user registration: If 0, user registration is disabled; use this value if your wiki is private,
	 * or if you have a user-defined process to control registration. The default value 1 enables it: anyone can register to 
	 * the wiki and is immediately counted as registered user after that. The advanced value 2 enables it but requires a
	 * {@link Config::$invitation_code register code}.
	 */
	var $allow_user_registration = '1';
	/**
	 * Invitation code.
	 * This value is used if Config::$require_edit_note is set to 2. This is a string value containing a secret code the
	 * user who wants to register should know.
	 */
	var $invitation_code = '';
	/**
	 * User host lookup.
	 * If this value is set to 1, the hostname are looked up and used instead of the numeric IP address for anonymous users.
	 * Other value disables this. Note that with the default value 1, the time needed to generate a page can increase considerably
	 * when the server has some network problem to a DNS.
	 */
	var $enable_user_host_lookup = '1';
	/**
	 * Double doublequote.
	 * <p>Control behaviour of wikka when handling raw HTML. Markup for raw HTML is to enclose them in double doublequote. Possible
	 * values for this option are : <ul>
	 * <li>safe: the content is to be sanitized through the Wakka::ReturnSafeHTML() method. This is the default value.
	 * Wikka uses a 3rdparty program named SafeHTML to suppress dangerous tags or attributes. For more information, see
	 * {@link safehtml.php}.</li>
	 * <li>raw: the content of double doublequote is considered sure and will be pasted as is. This is extremely dangerous to
	 * an open wiki, but it may be useful if you use Wikka inside an intranet where all users having access to Wikka can be
	 * trusted. Everything can be included in the html output, even <script> tags.</li>
	 * <li>disabled: All other values disable raw HTML. All dangerous characters (<, > and &) will be escaped.</li></ul></p>
	 * <p>Note that using another value than raw or safe for double_doublequote_html does not disable other behaviour of the
	 * double doublequote markup, such as unwikifying a word or a syntax.</p>
	 */
	var $double_doublequote_html = 'safe';
	/**
	 * External link tail.
	 * Tail appended to external link on output HTML. You can enter here any valid XHTML code.
	 */
	var $external_link_tail = '<span class="exttail">&#8734;</span>';
	/**
	 * SQL debugging.
	 * A value of 1 turns on sql debugging: instructions passed to the database server are listed at the bottom of the page. Any other value
	 * turns it off, but you should use only values 0 or 1. The sql debugging is visible only by logged-in administrators, so you can safely
	 * enable it without compromising the security of the Wikka site.
	 */
	var $sql_debugging = '0';
	/**
	 * Administrators' usernames.
	 * A comma separated value of administator usernames. Spaces are allowed in the string. You enter here usernames as they were typed when
	 * administrator registered, the string is <b>case sensitive</b>.
	 */
	var $admin_users = '';
	/**
	 * Administrator email.
	 * A valid email address through which the site administrator (a single person or a group) can be reached. Be aware that this address may
	 * be communicated automatically by the system to users.
	 */
	var $admin_email = '';
	/**
	 * Path to uploaded files.
	 * Folder under which are stored files uploaded by users. This folder will take place at the root of your Wikka Installation, if you don't
	 * specify a full path. If it doesn't exist, it will be created. The webserver must have write access to this folder.
	 */
	var $upload_path = 'uploads';
	/**
	 * Mime types.
	 * Config entry not used! Documentation not available!
	 */
	var $mime_types = 'mime_types.txt';

	// code hilighting with GeSHi
	/**
	 * GeSHi header.
	 * Tag type to use for code block highlighting using GeSHi. Possible values are div and pre
	 */
	var $geshi_header = 'div';
	/**
	 * GESHI_LINE_NUMBERS.
	 * Control the use of line numbering in GeSHi highlighting: 0 disables line numbers, 1 enables them and 2 uses fancy line numbers.
	 * These configurations are passed as GeSHi parameters GESHI_NO_LINE_NUMBERS, GESHI_NORMAL_LINE_NUMBERS and GESHI_FANCY_LINE_NUMBERS.
	 * See {@link GeSHi::enable_line_numbers()}
	 */
	var $geshi_line_numbers = '1';
	/**
	 * GeSHi tab width.
	 * Number of non-breaking spaces to replace a tabulation in code rendering. Default value is 8.
	 */
	var $geshi_tab_width = '4';
	/**
	 * Grabcode button use.
	 * Allow (1, default) or disallow (any other value) code block downloading.
	 */
	var $grabcode_button = '1';

	/**
	 * Wikiping server.
	 * Hostname or IP address of a wikiping server. If this value is not set or if it is set to an empty string, Wikiping functionnality
	 * is unavailable. See {@link Wakka::WikiPing()}.
	 */
	var $wikiping_server = '';

	/**
	 * Default write ACL.
	 * Default write ACL for new pages and for pages with no particular ACL specified. Write ACL concerns also creating a new page.
	 */
	var $default_write_acl = '+';
	/**
	 * Default read ACL.
	 * Default read ACL for new pages and for pages with no particular ACL specified.
	 */
	var $default_read_acl = '*';
	/**
	 * Default comment ACL.
	 * Default comment ACL for new pages and for pages with no particular ACL specified.
	 */
	var $default_comment_acl = '*';
	/**
	 * Maximum size of output HTML.
	 * This parameter configures the maximum size (in bytes) of dynamically generated output HTML one some kind of page handlers. Actually, it
	 * limits the size of {@link history.php the history handler} to prevent it to have too big size. 0 or a false value means no limit.
	 * Note that the comparison is not really accurate, so, if you have to limit the output to an exact size, consider specifying a lesser 
	 * value.
	 */
	var $pagesize_max;
	/**
	 * Default value for maximum revisioncount.
	 * Revision count is the number of revisions to show in {@link history.php history} and {@link revisions.php revisions} handlers.
	 */
	var $default_revisioncount;
	/**#@-*/

	/**#@+
	 * @var boolean (true or false)
	 */
	/**
	 * Pagename case sensitive.
	 * Checks if Database uses case sensitive or case insensitive collation for page names.
	 */
	var $pagename_case_sensitive = false;
	/**
	 * Rewrite_mode.
	 * Rewrite_mode is enabled if this config entry is true, and disabled if not.
	*/
	var $rewrite_mode;
	/**#@-*/
	/**
	 * GUI editor.
	 * This entry enables or disables the wikiedit toolbar. If its value is interpreted as false, visitors cannot use shortcuts
	 * or buttons when editing a page.
	 */
	var $gui_editor = '1';

	function Config()
	{
		$this->rewrite_mode = (preg_match('/'.preg_quote('wikka.php').'$/', $_SERVER['REQUEST_URI']) ? '0' : '1');
		$this->base_url = 'http://'.$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT'] != 80 ? ':'.$_SERVER['SERVER_PORT'] : '').$_SERVER['REQUEST_URI'].(preg_match('/'.preg_quote('wikka.php').'$/', $_SERVER['REQUEST_URI']) ? '?wakka=' : '');
	}
}
$buff = new Config;
$wakkaDefaultConfig = get_object_vars($buff);

?>
