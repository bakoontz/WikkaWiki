<?php
/**
 * This file is part of Wikka, a PHP wiki engine.
 *
 * It includes the Wakka class, which provides the core functions
 * to run Wikka.
 *
 * @package Wikka
 * @subpackage Libs
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author Hendrik Mans <hendrik@mans.de>
 * @author Jason Tourtelotte <wikka-admin@jsnx.com>
 * @author {@link http://wikkawiki.org/JavaWoman Marjolein Katsma}
 * @author {@link http://wikkawiki.org/NilsLindenberg Nils Lindenberg}
 * @author {@link http://wikkawiki.org/DotMG Mahefa Randimbisoa}
 * @author {@link http://wikkawiki.org/DarTar Dario Taraborelli}
 * @author {@link http://wikkawiki.org/BrianKoontz Brian Koontz}
 *
 * @copyright Copyright 2002-2003, Hendrik Mans <hendrik@mans.de>
 * @copyright Copyright 2004-2005, Jason Tourtelotte <wikka-admin@jsnx.com>
 * @copyright Copyright 2006, {@link http://wikkawiki.org/CreditsPage Wikka Development Team}
 */

// Comments
if (!defined('COMMENT_NO_DISPLAY')) define('COMMENT_NO_DISPLAY', 0);
if (!defined('COMMENT_ORDER_DATE_ASC')) define('COMMENT_ORDER_DATE_ASC', 1);
if (!defined('COMMENT_ORDER_DATE_DESC')) define('COMMENT_ORDER_DATE_DESC', 2);
if (!defined('COMMENT_ORDER_THREADED')) define('COMMENT_ORDER_THREADED', 3);
if (!defined('COMMENT_MAX_TRAVERSAL_DEPTH')) define('COMMENT_MAX_TRAVERSAL_DEPTH', 10);

/**
 * The Wikka core.
 *
 * This class contains all the core methods used to run Wikka.
 * @name Wakka
 * @package Wikka
 * @subpackage Libs
 *
 */
class Wakka
{
	/**
	 * Hold the wikka config.
	 * @access private
	 */
	var $config = array();
	/**
	 * Hold the connection-link to the database.
	 * @access private
	 */
	var $dblink;
	var $page;
	/**
	 * Hold the name of the current page.
	 *
	 * @access	private
	 */
	var $tag;
	var $queryLog = array();
	/**
	 * Hold the interWiki List.
	 */
	var $interWiki = array();
	/**
	 * Hold the Wikka version.
	 */
	var $VERSION;
	var $cookies_sent = false;
	/**
	 * $pageCache. 
	 * This array stores cached pages. Keys are page names (tag) or page id (prepended with /#) and values are the 
	 * page structure. See {@link Wakka::CachePage()}
	 * @var array
	 * @access public
	 */
	var $pageCache;
	/**
	 * $do_not_send_anticaching_headers. 
	 * If this value is set to true, Anti-caching HTTP headers won't be added.
	 * @var boolean
	 * @access public
	 */
	var $do_not_send_anticaching_headers = false;

	/**
	 * Constructor
	 */
	function Wakka($config)
	{
		$this->config = $config;
		$this->dblink = @mysql_connect($this->config["mysql_host"], $this->config["mysql_user"], $this->config["mysql_password"]);
		if ($this->dblink)
		{
			if (!@mysql_select_db($this->config["mysql_database"], $this->dblink))
			{
				@mysql_close($this->dblink);
				$this->dblink = false;
			}
		}
		$this->VERSION = WAKKA_VERSION;
	}

	/**
	 * Database methods
	 */
	/**
	 * Send a query to the database.
	 *
	 * If the query will fail, the function will simply die(). If SQL-
	 * Debugging is enabled, the query and the time it took to execute
	 * will be added to the Query-Log.
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::GetMicroTime()
	 * @param	string $query mandatory: the query to be executed.
	 * @return	array the result of the query.
	 * @todo	i18n
	 * @todo	move into a database class.
	 */
	function Query($query)
	{
		$start = $this->GetMicroTime();
		if (!$result = mysql_query($query, $this->dblink))
		{
			ob_end_clean();
			die(QUERY_FAILED.' '.$query.' ('.mysql_error($this->dblink).')'); #376
		}
		if ($this->GetConfigValue("sql_debugging"))
		{
			$time = $this->GetMicroTime() - $start;
			$this->queryLog[] = array(
				"query"		=> $query,
				"time"		=> $time);
		}
		return $result;
	}
	/**
	 * Return the first result of a query executed on the database.
	 *
	 * @uses	Wakka::LoadAll()
	 * @param	string $query mandatory: the query to be executed
	 * @return	string? the first result of the query.
	 * @todo	perhaps adding 'LIMIT 1' here instead depending on it beeing in every $query
	 * @todo	move into a database class.
	 */
	function LoadSingle($query) { if ($data = $this->LoadAll($query)) return $data[0]; return (false);}
	/**
	 * Return all results of a query executed on the database.
	 *
	 * @uses	Wakka::Query()
	 * @param	string $query mandatory: the query to be executed
	 * @return	array the result of the query.
	 * @todo	move into a database class.
	 */   
	function LoadAll($query)
	{
		$data = array();
		if ($r = $this->Query($query))
		{
			while ($row = mysql_fetch_assoc($r)) $data[] = $row;
			mysql_free_result($r);
		}
		return $data;
	}
	/**
	 * Check if the MySQL-Version is higher or equal to a given one.
	 *
	 * @param	integer $major mandatory:
	 * @param	integer $minor mandatory:
	 * @param	integer $subminor mandatory:
	 * @return	1 - if higher or equal; 0 if not or n/a
	 * @todo	move into a database class.
	 * @todo	use PHP version_compare() for comparison
	 */
	function CheckMySQLVersion($major, $minor, $subminor)
	{
		$result = @mysql_query('SELECT VERSION() AS version');
		if ($result != FALSE && @mysql_num_rows($result) > 0)
		{
			$row   = mysql_fetch_array($result);
			$match = explode('.', $row['version']);
		}
		else
		{
			$result = @mysql_query('SHOW VARIABLES LIKE \'version\'');
			if ($result != FALSE && @mysql_num_rows($result) > 0) {
				$row   = mysql_fetch_row($result);
				$match = explode('.', $row[1]);
			} else {
				return 0;
			}
		}

		$mysql_major = $match[0];
		$mysql_minor = $match[1];
		$mysql_subminor = $match[2][0].$match[2][1];

		if ($mysql_major > $major) {
			return 1;
		} else {
			if (($mysql_major == $major) && ($mysql_minor >= $minor) && ($mysql_subminor >= $subminor)) {
				return 1;
			} else {
				return 0;
			}
		}
	}

	/**
	 * Misc methods
	 */
	/**
	 * Generate a timestamp.
	 */
	function GetMicroTime() { list($usec, $sec) = explode(" ",microtime()); return ((float)$usec + (float)$sec); }
	/**
	 * Buffer the output from an included file.
	 *
	 * @param	string $filename mandatory: name of the file to be included
	 * @param	string $notfoundText optional: optional text to be returned if the file was not found. default: ""
	 * @param	string $vars optional: vars to be passed to the file. default: ""
	 * @param	string $path optional: path to the file. default: ""
	 * @return	string in case the file has some output or there was a notfoundText, boolean FALSE otherwise
	 * @todo	make the function return only one type of variable
	 */
	function IncludeBuffered($filename, $notfoundText = "", $vars = "", $path = "")
	{
		if ($path) $dirs = explode(":", $path);
		else $dirs = array("");

		foreach($dirs as $dir)
		{
			if ($dir) $dir .= "/";
			$fullfilename = $dir.$filename;
			if (file_exists($fullfilename))
			{
				if (is_array($vars)) extract($vars);

				ob_start();
				include($fullfilename);
				$output = ob_get_contents();
				ob_end_clean();
				return $output;
			}
		}
		if ($notfoundText) return $notfoundText;
		else return false;
	}
	
	/**
	 * Create a unique id for an HTML element.
	 *
	 * Although - given Wikka accepts can use embedded HTML - it cannot be
	 * guaranteed that an id generated by this method is unique it tries its
	 * best to make it unique:
	 * - ids are organized into groups, with the group name used as a prefix
	 * - if an id is specified it is compared with other ids in the same group;
	 *   if an identical id exists within the same group, a sequence suffix is
	 *   added, otherwise the specified id is accepted and recorded as a member
	 *   of the group
	 * - if no id is specified (or an invalid one) an id will be generated, and
	 *   given a sequence suffix if needed
	 *
	 * For headings, it is possible to derive an id from the heading content;
	 * to support this, any embedded whitespace is replaced with underscores
	 * to generate a recognizable id that will remain (mostly) constant even if
	 * new headings are inserted in a page. (This is not done for embedded
	 * HTML.)
	 *
	 * The method supports embedded HTML as well: as long as the formatter
	 * passes each id found in embedded HTML through this method it can take
	 * care that the id is valid and unique.
	 * This works as follows:
	 * - indicate an 'embedded' id with group 'embed'
	 * - NO prefix will be added for this reserved group
	 * - ids will be recorded and checked for uniqueness and validity
	 * - invalid ids are replaced
	 * - already-existing ids in the group are given a sequence suffix
	 * The result is that as long as the already-defined id is valid and
	 * unique, it will be remain unchanged (but recorded to ensure uniqueness
	 * overall).
	 *
	 * @author		{@link http://wikka.jsnx.com/JavaWoman JavaWoman}
	 * @copyright	Copyright © 2005, Marjolein Katsma
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 *
	 * @access	public
	 * @uses	ID_LENGTH
	 *
	 * @param	string	$group	required: id group (e.g. form, head); will be
	 *							used as prefix (except for the reserved group
	 *							'embed' to be used for embedded HTML only)
	 * @param	string	$id		optional: id to use; if not specified or
	 *							invalid, an id will be generated; if not
	 *							unique, a sequence number will be appended
	 * @return	string	resulting id
	 */
	function makeId($group,$id='')
	{
		// initializations
		static $aSeq = array();		# group sequences
		static $aIds = array();		# used ids

		// preparation for group
		if (!preg_match('/^[A-Z-a-z]/',$group))		# make sure group starts with a letter
		{
			$group = 'g'.$group;
		}
		if (!isset($aSeq[$group]))
		{
			$aSeq[$group] = 0;
		}
		if (!isset($aIds[$group]))
		{
			$aIds[$group] = array();
		}
		if ('embed' != $group)
		{
			$id = preg_replace('/\s+/','_',trim($id));	# replace any whitespace sequence in $id with a single underscore
		}

		// validation (full for 'embed', characters only for other groups since we'll add a prefix)
		if ('embed' == $group)
		{
			$validId = preg_match('/^[A-Za-z][A-Za-z0-9_:.-]*$/',$id);	# #34 ref: http://www.w3.org/TR/html4/types.html#type-id
		}
		else
		{
			$validId = preg_match('/^[A-Za-z0-9_:.-]*$/',$id);
		}

		// build or generate id
		if ('' == $id || !$validId || in_array($id,$aIds)) 			# ignore specified id if it is invalid or exists already
		{
			$id = substr(md5($group.$id),0,ID_LENGTH);				# use group and id as basis for generated id
		}
		$idOut = ('embed' == $group) ? $id : $group.'_'.$id;		# add group prefix (unless embedded HTML)
		if (in_array($id,$aIds[$group]))
		{
			$idOut .= '_'.++$aSeq[$group];							# add suffiX to make ID unique
		}

		// result
		$aIds[$group][] = $id;										# keep track of both specified and generated ids (without suffix)
		return $idOut;
	}
	
	/**
	 * Strip potentiall dangerous tags from given html.
	 *
	 * @param	string $html mandatory: the html-text to be secured
	 * @return	string the filtered html-text
	 */
	function ReturnSafeHTML($html)
	{
		require_once('3rdparty/core/safehtml/classes/safehtml.php');

		// Instantiate the handler
		$safehtml =& new safehtml();

		$filtered_output = $safehtml->parse($html);

		return $filtered_output;
	}

	/**
	 * Make sure a (user-provided) URL does use &amp; instead of & and is protected from attacks.
	 *
	 * Any already-present '&amp;' is first turned into '&'; then htmlspecialchars() is applied so
	 * all ampersands are "escaped" while characters that could be used to create a script attack
	 * (< > or ") are "neutralized by escaping them.
	 *
	 * This method should be applied on any user-provided url in actions, handlers etc.
	 *
	 * @author		{@link http://wikkawiki.org/JavaWoman JavaWoman}
	 * @copyright	Copyright © 2004, Marjolein Katsma
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 * @version		0.7
	 *
	 * @access		public
	 * @todo		refine (maybe)
	 *
	 * @param		string	$url  required: URL to sanitize
	 * @return		string	sanitzied URL
	 */
	function cleanUrl($url)
	{
		return htmlspecialchars(preg_replace('/&amp;/','&',$url));
	}

	/**
	 * Wrapper around PHP's htmlspecialchars() which preserves (repairs) entity references.
	 *
	 * The function accepts the same parameters as htmlspecialchars() in PHP and passes them on
	 * to that function.
	 *
	 * One defaults here is different here from that in htmlspecialchars() in PHP:
	 * charset is set to UTF-8 so we're ready for UTF-8 support (and as long as we don't support
	 * that there should be no difference with Latin-1); on systems where the charset parameter
	 * is not available or UTF-8 is not supported this will revert to Latin-1 (ISO-8859-1).
	 *
	 * The function first applies htmlspecialchars() to the input string and then "unescapes"
	 * character entity references and numeric character references (both decimal and hexadecimal).
	 * Entities are recognized also if the ending semicolon is omitted at the end or before a
	 * newline or tag but for consistency the semicolon is always added in the output where it was
	 * omitted.
	 *
	 * NOTE:
	 * Where code should be rendered _as_code_ the original PHP function should be used so that
	 * entity references are also rendered as such instead of as their corresponding characters.
	 *
	 * @access	public
	 * @since	Wikka 1.1.6.0
	 * @version	1.0
	 * @todo	(later) support full range of situations where (in SGML) a terminating ; may legally
	 *			be omitted (end, newline and tag are merely the most common ones).
	 * @todo (maybe) recognize valid html entities, thus transform &error; to &amp;error;
	 *
	 * @param	string	$text required: text to be converted
	 * @param	integer	$quote_style optional: quoting style - can be ENT_COMPAT (default, escape
	 *			only double quotes), ENT_QUOTES (escape both double and single quotes) or
	 *			ENT_NOQUOTES (don't escape any quotes)
	 * @param	string	$charset optional: charset to use while converting; default UTF-8
	 *			(overriding PHP's default ISO-8859-1)
	 * @return	string	converted string with escaped special characted but entity references intact
	 */
	function htmlspecialchars_ent($text,$quote_style=ENT_COMPAT,$charset='UTF-8',$doctype='HTML')
	{
		// define patterns
		$alpha  = '[a-z]+';							# character entity reference todo: $alpha='eacute|egrave|ccirc|...'
		$ignore_case = 'i';
		if ($doctype == 'XML')
		{
			$alpha = 'lt|gt|quot|amp';
			$ignore_case = '';
			if ($quote_style === '') $quote_style = ENT_COMPAT;
			if ($charset === '') $charset = 'UTF-8';
		}
		$numdec = '#[0-9]+';						# numeric character reference (decimal)
		$numhex = '#x[0-9a-f]+';					# numeric character reference (hexadecimal)
		$terminator = ';|(?=($|[\n<]|&lt;))';		# semicolon; or end-of-string, newline or tag
		$entitystring = $alpha.'|'.$numdec.'|'.$numhex;
		$escaped_entity = '&amp;('.$entitystring.')('.$terminator.')';

		// execute PHP built-in function, passing on optional parameters
		$output = htmlspecialchars($text,$quote_style,$charset);
		// "repair" escaped entities
		// modifiers: s = across lines, i = case-insensitive
		$output = preg_replace('/'.$escaped_entity.'/s'.$ignore_case,"&$1;",$output);
		// return output
		return $output;
	}

	/**
	 * Get a value provided by user (by get, post or cookie) and sanitize it.
	 * The method is also helpful to disable warning when the value was absent.
	 *
	 * @access	public
	 * @since	Wikka 1.1.7.0
	 * @version	1.0
	 *
	 * @param	string	$varname required: field name on get or post or cookie name
	 * @param	string	$gpc one of get, post, request and cookie. Optional, defaults to request.
	 * @return	string	sanitized value of $_REQUEST[$varname] (or $_GET, $_POST, $_COOKIE, depending on $gpc)
	 */
	function GetSafeVar($varname, $gpc='request')
	{
		$safe_var = null;
		if ($gpc == 'post')
		{
			$safe_var = isset($_POST[$varname]) ?$_POST[$varname] : null;
		}
		elseif ($gpc == 'request')
		{
			$safe_var = isset($_REQUEST[$varname]) ? $_REQUEST[$varname] : null;
		}
		elseif ($gpc == 'get')
		{
			$safe_var = isset($_GET[$varname]) ? $_GET[$varname] : null;
		}
		elseif ($gpc == 'cookie')
		{
			$safe_var = isset($_COOKIE[$varname]) ? $_COOKIE[$varname] : null;
		}
		return ($this->htmlspecialchars_ent($safe_var));
	}
	/**
	 * Highlight a code block with GeSHi.
	 *
	 * The path to GeSHi and the GeSHi language files must be defined in the configuration.
	 *
	 * This implementation fits in with general Wikka behavior; e.g., we use classes and an external
	 * stylesheet to render hilighting.
	 *
	 * Apart from this fixed general behavior, WikiAdmin can configure a few behaviors via the
	 * configuration file:
	 * geshi_header			- wrap code in div (default) or pre
	 * geshi_line_numbers	- disable line numbering, or enable normal or fancy line numbering
	 * geshi_tab_width		- override tab width (default is 8 but 4 is more commonly used in code)
	 *
	 * Limitation: while line numbering is supported, extra GeSHi styling for line numbers is not.
	 * When line numbering is enabled, the end user can "turn it on" by specifying a starting line
	 * number together with the language code in a code block, e.g., (php;260); this number is then
	 * passed as the $start parameter for this method.
	 *
	 * @access	public
	 * @since	wikka 1.1.6.0
	 * @uses	Wakka::config
	 * @uses	GeShi
	 * @todo		support for GeSHi line number styles
	 * @todo		enable error handling
	 *
	 * @param	string	$sourcecode	required: source code to be highlighted
	 * @param	string	$language	required: language spec to select highlighter
	 * @param	integer	$start		optional: start line number; if supplied and >= 1 line numbering
	 *			 will be turned on if it is enabled in the configuration.
	 * @return	string	code block with syntax highlighting classes applied
	 */
	function GeSHi_Highlight($sourcecode, $language, $start=0)
	{
		// create GeSHi object
		include_once($this->config['geshi_path'].'/geshi.php');
		$geshi =& new GeSHi($sourcecode, $language, $this->config['geshi_languages_path']);				# create object by reference

		$geshi->enable_classes();								# use classes for hilighting (must be first after creating object)
		$geshi->set_overall_class('code');						# enables using a single stylesheet for multiple code fragments

		// configure user-defined behavior
		$geshi->set_header_type(GESHI_HEADER_DIV);				# set default
		if (isset($this->config['geshi_header']))				# config override
		{
			if ('pre' == $this->config['geshi_header'])
			{
				$geshi->set_header_type(GESHI_HEADER_PRE);
			}
		}
		$geshi->enable_line_numbers(GESHI_NO_LINE_NUMBERS);		# set default
		if ($start > 0)											# line number > 0 _enables_ numbering
		{
			if (isset($this->config['geshi_line_numbers']))		# effect only if enabled in configuration
			{
				if ('1' == $this->config['geshi_line_numbers'])
				{
					$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
				}
				elseif ('2' == $this->config['geshi_line_numbers'])
				{
					$geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS);
				}
				if ($start > 1)
				{
					$geshi->start_line_numbers_at($start);
				}
			}
		}
		if (isset($this->config['geshi_tab_width']))			# GeSHi override (default is 8)
		{
			$geshi->set_tab_width($this->config['geshi_tab_width']);
		}

		// parse and return highlighted code
		return $geshi->parse_code();
	}

	/**
	 * Variable-related methods
	 */
	/**
	 * Get the name tag of the current page.
	 *
	 * @return	string the name of the page
	 */
	function GetPageTag() { return $this->tag; }
	/**
	 * Get the time to current verion of the current page was saved.
	 *
	 * @return string?
	 */
	function GetPageTime() { return $this->page["time"]; }
	/**
	 * Get the handler used on the page.
	 *
	 * @return string name of the method.
	 */
	function GetMethod() { return $this->method; }
	/**
	 * Get the value of a given value from the wikka config.
	 *
	 * @param	$name mandatory: name of a key in the config array
	 */
	function GetConfigValue($name) { return (isset($this->config[$name])) ? $this->config[$name] : null; }
	/**
	 * Get the name of the Wiki.
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @return	string the name of the Wiki.
	 */
	function GetWakkaName() { return $this->GetConfigValue("wakka_name"); }
	/**
	 * Get the wikka version.
	 *
	 * @return	string the wikka version
	 */
	function GetWakkaVersion() { return $this->VERSION; }

	/**
	 * Page-related methods
	 */
	/**
	 * LoadPage loads the page whose name is $tag.
	 * 
	 * If parameter $time is provided, LoadPage returns the page as it was at that exact time.
	 * If parameter $time is not provided, it returns the page as its latest state.
	 * LoadPage and LoadPageById remember the page tag or page id they've queried by caching them,
	 * so, these methods try first to retrieve data from cache if available.
	 * @uses	Wakka:LoadSingle()
	 * @uses	Wakka:CachePage()
	 * @uses	Wakka:CacheNonExistentPage()
	 * @uses	Wakka:GetCachedPage()
	 * @param string $tag 
	 * @param string $time 
	 * @param int $cache 
	 * @access public
	 * @return mixed $page
	 */
	function LoadPage($tag, $time = "", $cache = 1) {
		$page = null;
		// retrieve from cache
		if (!$time && $cache) {
			$page = $this->GetCachedPage($tag);
			if ($page=="cached_nonexistent_page") return null;
		}
		// load page
		if (!$page) $page = $this->LoadSingle("select * from ".$this->config["table_prefix"]."pages where tag = '".mysql_real_escape_string($tag)."' ".($time ? "and time = '".mysql_real_escape_string($time)."'" : "and latest = 'Y'")." limit 1");
		// cache result
		if ($page) 
		{
			$this->CachePage($page);
		} 
		else 
		{
			$this->CacheNonExistentPage($tag);
		}
		return $page;
	}
	/**
	 * Determine if the current version of the page is the latest.
	 *
	 * @return boolean? TRUE if it is the latest, false otherwise.
	 */
	function IsLatestPage() {
		return $this->latest;
	}
	/**
	 * GetCachedPageById gets a page from cache whose id is $id.
	 * 
	 * @param mixed $id the id of the page to retrieve from cache
	 * @access public
	 * @return mixed an array as returned by LoadPage(), or null if absent from cache.
	 */
	function GetCachedPageById($id)
	{
		return $this->GetCachedPage('/#'.$id);
	}
	/**
	 * GetCachedPage gets a page from cache whose name is $tag.
	 * 
	 * @see Wakka::CachePage()
	 * @uses Wakka::GetConfigValue()
	 * @uses Config::$pagename_case_sensitive
	 * @uses Wakka::$pageCache
	 * @param mixed $tag the name of the page to retrieve from cache.
	 * @access public
	 * @return mixed an array as returned by LoadPage(), or null if absent from cache.
	 */
	function GetCachedPage($tag) 
	{
		if (!$this->GetConfigValue('pagename_case_sensitive')) $tag = strtolower($tag);
		$page = (isset($this->pageCache[$tag])) ? $this->pageCache[$tag] : null; 
		if ((is_string($page)) && ($page[0] == '/'))
		{
			$page = $this->pageCache[substr($page, 1)];
		}
		return ($page); 
	}
	/**
	 * CachePage caches a page to prevent reusing mysql operations when reloading it.
	 * <p>Cached pages are stored in the array $this->pageCache.</p>
	 * <p>If this is the latest version of the page, the page name is used as a key for the array. That page name
	 * may be lowercased if the database doesn't work with case sensitive collation. Lowercasing it enhances the
	 * power of caching by preventing reloading of a page (with mysql) under another case. But if the database
	 * needs to work with case sensitive collation (like cp1250_czech_cs), you must set a config value named
	 * `pagename_case_sensitive' to 1, and this lowercasing will be disabled.</p>
	 * <p>CachePage also stores the page under a key made of a special marker slash+sharp (/#) concatenated with 
	 * the page id. As example, a page having id=208 will be stored at $this->pageCache['/#208'].This ensures 
	 * that a page previously loaded by its name or by id will be retrieved from cache if the page id match.</p>
	 * <p>Normally, the type of the value of the array is an array containing the page data, as returned by
	 * LoadPage. However, If this is the latest version of the page, a link will be made between the page id and 
	 * the page tag. In such case, the value of an entry of $this->pageCache[] will be just a string beginning 
	 * with a slash (/), and to retrieve the data, you have to use this string as a key for the array
	 * $this->pageCache[] after suppressing the leading slash.</p>
	 * 
	 * @uses Wakka::GetConfigValue()
	 * @uses Config::$pagename_case_sensitive
	 * @uses Wakka::$pageCache
	 * @param mixed $page 
	 * @access public
	 * @return void
	 */
	function CachePage($page) 
	{ 
		$page_cache_key = $page ? $page['tag'] : '';
		$page_cache_key = $this->GetConfigValue('pagename_case_sensitive') ? $page_cache_key : strtolower($page_cache_key);
		if ($page['latest'] == 'Y')
		{
			$this->pageCache[$page_cache_key] = $page;
			$this->pageCache['/#'.$page['id']] = '/'.$page_cache_key;
		}
		else
		{
			$this->pageCache['/#'.$page['id']] = $page;
		}
	}
	/**
	 * CacheNonExistentPage marks a page name in cache as a non existent page.
	 * 
	 * @uses Wakka::GetConfigValue()
	 * @uses Config::$pagename_case_sensitive
	 * @uses Wakka::$pageCache
	 * @param string $tag the name of the page.
	 * @access public
	 * @return void
	 */
	function CacheNonExistentPage($tag)
	{
		if (!$this->GetConfigValue('pagename_case_sensitive')) $tag = strtolower($tag);
		$this->pageCache[$tag] = 'cached_nonexistent_page';
	}
	function SetPage($page) { $this->page = $page; if ($this->page["tag"]) $this->tag = $this->page["tag"]; }
	/**
	 * LoadPageById loads a page whose id is $id.
	 * 
	 * If the parameter $cache is true, it first tries to retrieve it from cache.
	 * If the page id was not retrieved from cache, then use sql and cache the page.
	 * @param int $id Id of the page to load.
	 * @param boolean $cache if true, an attempt to retrieve from cache will be made first.
	 * @access public
	 * @return mixed a page identified by $id
	 */
	function LoadPageById($id, $cache = true) 
	{ 
		// It first tries to retrieve from cache.
		if ($cache)
		{
			$page = $this->GetCachedPageById($id);
			if ((is_string($page)) && ($page == 'cached_nonexistent_page'))
			{
				return null;
			}
			if (is_array($page))
			{
				return ($page);
			}
		}
		// If the page id was not retrieved from cache, then use sql and cache the page.
		$page = $this->LoadSingle("select * from ".$this->config["table_prefix"]."pages where id = '".mysql_real_escape_string($id)."' limit 1"); 
		if ($page)
		{
			$this->CachePage($page);
		}
		else
		{
			$this->CacheNonExistentPage('/#'.$id);
		}
		return $page;
	}
	/**
	 * LoadRevisions: Load revisions of a page. 
	 * 
	 * <p>Returns up to $max latest revisions of $page.
	 * 0 or a negative value for $max means using a default value.
	 * The default value for $max is the `revisioncount' user's preference if user is logged in,
	 * or the (new) config value {@link Config::$default_revisioncount default_revisioncount}, if
	 * such config entry exists, or falls to a hard-coded value of 20.</p>
	 * <p>A revision structure consists of an edit note
	 * (`<b>note</b>' key), the `<b>id</b>' of the revision which permits to retrieve later the
	 * full edit data (especially the body field), the date of revision (`<b>time</b> key) and the
	 * `<b>user</b>' who did the modification. </p>
	 * <p>Since 1.1.7, we replaced `SELECT *' in the sql instruction by 
	 * `SELECT note, id, time, user' because only these fields are really needed. (Trac:#75)</p>
	 * <p>If param $start is supplied, LoadRevisions ignore the $start most recent revisions; this
	 * will allow browsing full history step by step if the pagesize or the number of total revision 
	 * are getting too big.</p>
	 *
	 * @uses Wakka::GetUser()
	 * @uses Wakka::LoadAll()
	 * @uses Config::$default_revisioncount
	 * @uses Config::$pagename_case_sensitive
	 * @param string $page Name of the page to view revisions of
	 * @param int $start 
	 * @param int $max Maximum number of revisions to load.
	 * @access public
	 * @return array This value contains fields note, id, time and user.
	 */
	function LoadRevisions($page, $start='', $max=0) 
	{
		$max = intval($max);
		if ($max <= 0)
		{
			if ($user = $this->GetUser())
			{
				$max = intval($user['revisioncount']);
			}
			elseif (($max = intval($this->GetConfigValue('default_revisioncount'))) <= 0)
			{
				$max = 20;
			}
		}
		if ($max <= 0)
		{ // 0 or a negative value means no max, so choose a huge number.
			$max = 1000;
		}
		if ($start = intval($start))
		{
			$start .= ', ';
		}
		else
		{
			$start = '';
		}
		$revisions = $this->LoadAll("select note, id, time, user from ".$this->config["table_prefix"]."pages where tag = '".mysql_real_escape_string($page)."' order by time desc LIMIT $start$max");
		if (is_array($revisions) && (count($revisions) < $max) && (count($revisions))) #38
		{
			if (!$this->GetConfigValue('pagename_case_sensitive')) $page_lowercase = strtolower($page);
			$this->specialCache['oldest_revision'][$page_lowercase] = $revisions[count($revisions) - 1];
		}
		return ($revisions);
	}
	/**
	 * LoadOldestRevision: Load the oldest known revision of a page.
	 * 
	 * @param string $page The name of the page to load oldest revision of.
	 * @uses Config::$pagename_case_sensitive
	 * @uses Wakka::$specialCache
	 * @uses Wakka::LoadSingle
	 * @access public
	 * @return array
	 */
	function LoadOldestRevision($page)
	{
		if (!$this->GetConfigValue('pagename_case_sensitive')) $page_lowercase = strtolower($page);
		if (isset($this->specialCache['oldest_revision'][$page_lowercase])) return ($this->specialCache['oldest_revision'][$page_lowercase]);
		$latest_revision = $this->LoadSingle("select note, id, time, user from ".$this->config['table_prefix']."pages where tag = '".mysql_real_escape_string($page)."' order by time LIMIT 1");
		$this->specialCache['oldest_revision'][$page_lowercase] = $latest_revision;
		return ($latest_revision);
	}
	function LoadPagesLinkingTo($tag) { return $this->LoadAll("select from_tag as tag from ".$this->config["table_prefix"]."links where to_tag = '".mysql_real_escape_string($tag)."' order by tag"); }
	function LoadRecentlyChanged()
	{
		if ($pages = $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where latest = 'Y' order by time desc"))
		{
			foreach ($pages as $page)
			{
				$this->CachePage($page);
			}
			return $pages;
		}
		return false;
	}
	/**
	 * Load pages that need to be created.
	 * This is an expanded version of {@link Wakka::LoadWantedPages()} by permitting sorting by
	 * number of pages referring to each wanted page, or by latest modified date of any page referring
	 * to wanted pages, or alphabetically.
	 * WARNING: The parameter $sort passed to this method is considered sanitized.
	 *
	 * @uses Wakka::LoadAll()
	 * @param string $sort Sorting needed: Legal SQL expression after ORDER BY clause. Field names are count, time and tag.
	 * @access public
	 * @return array
	 */
	function LoadWantedPages2($sort='')
	{
		if (!$sort)
		{
			$sort = 'count desc, time desc, tag';
		}
		return $this->LoadAll('
			select distinct _LINKS.to_tag as tag,
			count(_LINKS.from_tag) as count,
			max(CONCAT_WS("/", _PAGES2.time, _PAGES2.tag)) as time
			from '.$this->config['table_prefix'].'links _LINKS left join '.$this->config['table_prefix'].
			'pages _PAGES on _LINKS.to_tag = _PAGES.tag 
			INNER JOIN '.$this->config['table_prefix'].'pages _PAGES2 ON _LINKS.from_tag = _PAGES2.tag
			where _PAGES.tag is NULL 
			and _PAGES2.latest = \'Y\'
			group by tag order by '.$sort);
	}
	function LoadWantedPages() { return $this->LoadAll("select distinct ".$this->config["table_prefix"]."links.to_tag as tag,count(".$this->config["table_prefix"]."links.from_tag) as count from ".$this->config["table_prefix"]."links left join ".$this->config["table_prefix"]."pages on ".$this->config["table_prefix"]."links.to_tag = ".$this->config["table_prefix"]."pages.tag where ".$this->config["table_prefix"]."pages.tag is NULL group by tag order by count desc"); }
	function IsWantedPage($tag)
	{
		if ($pages = $this->LoadWantedPages())
		{
			foreach ($pages as $page)
			{
				if ($page["tag"] == $tag) return true;
			}
		}
		return false;
	}
	function LoadOrphanedPages() { return $this->LoadAll("select distinct tag from ".$this->config["table_prefix"]."pages left join ".$this->config["table_prefix"]."links on ".$this->config["table_prefix"]."pages.tag = ".$this->config["table_prefix"]."links.to_tag where ".$this->config["table_prefix"]."links.to_tag is NULL order by tag"); }
	function LoadPageTitles() { return $this->LoadAll("select tag, owner from ".$this->config["table_prefix"]."pages where latest = 'Y' order by tag"); }
	function LoadPagesByOwner($owner) { return $this->LoadAll('select tag from '.$this->config['table_prefix'].'pages where latest = \'Y\' and owner = \''.mysql_real_escape_string($owner).'\'');}
	function LoadAllPages() { return $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where latest = 'Y' order by tag"); }
	// function FullTextSearch($phrase) { return $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where latest = 'Y' and match(tag, body) against('".mysql_real_escape_string($phrase)."')"); }
	function FullTextSearch($phrase)
	{
		$data = "";
		if ($this->CheckMySQLVersion(4,00,01))
		{
			if (preg_match('/[A-Z]/', $phrase)) $phrase = "\"".$phrase."\"";
			$data = $this->LoadAll(" select * from "
			.$this->config["table_prefix"]
			."pages where latest = 'Y' and tag like('%".mysql_real_escape_string($phrase)."%') UNION select * from "
			.$this->config["table_prefix"]
			."pages where latest = 'Y' and match(tag, body) against('".mysql_real_escape_string($phrase)
			."' IN BOOLEAN MODE) order by time DESC");
		}

		// else if ($this->CheckMySQLVersion(3,23,23))
		// {
		//	$data = $this->LoadAll("select * from "
		//	.$this->config["table_prefix"]
		//	."pages where latest = 'Y' and
		//		  match(tag, body)
		//		  against('".mysql_real_escape_string($phrase)."')
		//		  order by time DESC");
		// }

		/* if no results perform a more general search */
		if (!$data)  {
				$data = $this->LoadAll("select * from "
				.$this->config["table_prefix"]
				."pages where latest = 'Y' and
				  (tag like '%".mysql_real_escape_string($phrase)."%' or
				   body like '%".mysql_real_escape_string($phrase)."%')
				   order by time DESC");
		}

		return($data);
	}
	/**
	 * Takes an array of pages returned by LoadAll() and presents it using table or unordered list.
	 *
	 * This method is called in Category action and Backlinks handler
	 *
	 * @author		{@link http://wikkawiki.org/DotMG DotMG}
	 * @access		public
	 *
	 * @uses	Wakka::Format()
	 * @param mixed $pages required: Array of pages returned by LoadAll
	 * @param string $nopagesText optional: Error message returned if $pages is void. Default: ''
	 * @param string $class optional: A classname to be attached to the table or unordered list. Default: ''
	 * @param int $columns optional: Number of columns of the table if compact = 0. Default: 3
	 * @param int $compact optional: If 0: use table, if 1: use unordered list. Default: 0
		* @param boolean $show_edit_link If true, each page is followed by an edit link. Default: false. 
	 * @return string
	 */
	function ListPages($pages, $nopagesText = '', $class = '', $columns = 3, $compact = 0, $show_edit_link=false)
	{
		$edit_link = '';
		if (!$pages) return ($nopagesText);
		if ($class) $class=" class='$class'";
		$str = $compact ? "<div$class><ul>" : "<table width='100%'$class><tr\n>";
		foreach ($pages as $page)
		{
			$list[] = $page['tag'];
		}
		sort ($list);
		$count = 0;
		foreach ($list as $val)
		{
			if ($show_edit_link) 
			{
				$edit_link = ' <small>['.$this->Link($val, 'edit', WIKKA_PAGE_EDIT_LINK_DESC, false, true, sprintf(WIKKA_PAGE_EDIT_LINK_TITLE, $val)).']</small>';
			}
			if ($compact)
			{
				$link = '[['.$val;
				if (eregi('^Category', $val)) $link.= ' '.eregi_replace('^Category', '', $val);
				$str .= "<li\n>".$this->Format($link.']]').$edit_link.'</li>';
			}
			else
			{
				if ($count == $columns)
				{
					$str .= "</tr><tr\n>";
					$count = 0;
				}
				$str .= '<td>'.$this->Format('[['.$val.']]').$edit_link.'</td>';
			}
			$count ++;
		}
		$str .= $compact ? '</ul></div>' : '</tr></table>';
		return ($str);
	}
	/**
	 * Save a page.
	 *
	 * @uses	Wakka::GetPingParams()
	 * @uses	Wakka::GetUser()
	 * @uses	Wakka::GetUserName()
	 * @uses	Wakka::HasAccess()
	 * @uses	Wakka::LoadPage()
	 * @uses	Wakka::Query()
	 * @uses	Wakka::WikiPing()
	 */
	function SavePage($tag, $body, $note)
	{
		// get current user
		$user = $this->GetUserName();

		// TODO: check write privilege	??? is this still a TODO??
		if ($this->HasAccess("write", $tag))
		{
			// is page new?
			if (!$oldPage = $this->LoadPage($tag))
			{
				// current user is owner if user is logged in, otherwise, no owner.
				if ($this->GetUser()) $owner = $user;
			}
			else
			{
				// aha! page isn't new. keep owner!
				$owner = $oldPage["owner"];
			}

			// set all other revisions to old
			$this->Query("update ".$this->config["table_prefix"]."pages set latest = 'N' where tag = '".mysql_real_escape_string($tag)."'");

			// add new revision
			$this->Query("insert into ".$this->config["table_prefix"]."pages set ".
				"tag = '".mysql_real_escape_string($tag)."', ".
				 "time = now(), ".
				  "owner = '".mysql_real_escape_string($owner)."', ".
				 "user = '".mysql_real_escape_string($user)."', ".
				"note = '".mysql_real_escape_string($note)."', ".
				 "latest = 'Y', ".
				 "body = '".mysql_real_escape_string($body)."'");

			if ($pingdata = $this->GetPingParams($this->config["wikiping_server"], $tag, $user, $note))
				$this->WikiPing($pingdata);
		}
	}
	/**
	 * Return the title of the current page.
	 *
	 * It is retrieved either from the first level 1-3 header in the page body
	 * or, if there is none such headline, from the page name.
	 *
	 * @uses	Wakka::Format()
	 * @uses	Wakka::GetPageTag()
	 * @return	string the title of the current page
	 */
	function PageTitle() {
		$title = "";
		$pagecontent = $this->page["body"];
		if (ereg( "(=){3,5}([^=\n]+)(=){3,5}", $pagecontent, $title)) {
			$formatting_tags = array("**", "//", "__", "##", "''", "++", "#%", "@@", "\"\"");
			$title = str_replace($formatting_tags, "", $title[2]);
		}
		if ($title) return strip_tags($this->Format($title));				# fix for forced links in heading
		else return $this->GetPageTag();
	}
	/**
	 * Check by name if a page exists.
	 *
	 * @author		{@link http://wikkawiki.org/JavaWoman JavaWoman}
	 * @copyright	Copyright © 2004, Marjolein Katsma
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 * @version		1.0
	 *
	 * @access		public
	 * @uses		Query()
	 *
	 * @param		string  $page  page name to check
	 * @return		boolean  TRUE if page exists, FALSE otherwise
	 */
	function ExistsPage($page)
	{
		$count = 0;
		$query =	 "SELECT COUNT(tag)
					FROM ".$this->config['table_prefix']."pages
					WHERE tag='".mysql_real_escape_string($page)."'";
		if ($r = $this->Query($query))
		{
			$count = mysql_result($r,0);
			mysql_free_result($r);
		}
		return ($count > 0) ? TRUE : FALSE;
	}

	/**
	 * WIKI PING  -- Coded by DreckFehler
	 */
	/**
	 * WikiPing an external server.
	 *
	 * @todo	move to an extra class
	 */
	function HTTPpost($host, $data, $contenttype="application/x-www-form-urlencoded", $maxAttempts = 5) {
		$attempt =0; $status = 300; $result = "";
		while ($status >= 300 && $status < 400 && $attempt++ <= $maxAttempts) {
			$url = parse_url($host);
			if (isset($url["path"]) == false) $url["path"] = "/";
			if (isset($url["port"]) == false) $url["port"] = 80;

			if ($socket = fsockopen ($url["host"], $url["port"], $errno, $errstr, 15)) {
				$strQuery = "POST ".$url["path"]." HTTP/1.1\n";
				$strQuery .= "Host: ".$url["host"]."\n";
				$strQuery .= "Content-Length: ".strlen($data)."\n";
				$strQuery .= "Content-Type: ".$contenttype."\n";
				$strQuery .= "Connection: close\n\n";
				$strQuery .= $data;

				// send request & get response
				fputs($socket, $strQuery);
				$bHeader = true;
				while (!feof($socket)) {
					$strLine = trim(fgets($socket, 512));
					if (strlen($strLine) == 0) $bHeader = false; // first empty line ends header-info
					if ($bHeader) {
						if (!$status) $status = $strLine;
						if (preg_match("/^Location:\s(.*)/", $strLine, $matches)) $location = $matches[1];
					} else $result .= trim($strLine)."\n";
				}
				fclose ($socket);
			} else $status = "999 timeout";

			if ($status) {
				if(preg_match("/(\d){3}/", $status, $matches)) $status = $matches[1];
			} else $status = 999;
			$host = $location;
		}
		if (preg_match("/^[\da-fA-F]+(.*)$/", $result, $matches)) $result = $matches[1];
		return $result;
	}
	/**
	 * Manage the WikiPing(s) of a change to (an) external sever(s).
	 *
	 * @uses	Wakka::HTTPpost()
	 * @todo	move to an extra class
	 */
	function WikiPing($ping, $debug = false) {
		if ($ping) {
			$rpcRequest .= "<methodCall>\n";
			$rpcRequest .= "<methodName>wiki.ping</methodName>\n";
			$rpcRequest .= "<params>\n";
			$rpcRequest .= "<param>\n<value>\n<struct>\n";
			$rpcRequest .= "<member>\n<name>tag</name>\n<value>".$ping["tag"]."</value>\n</member>\n";
			$rpcRequest .= "<member>\n<name>url</name>\n<value>".$ping["taglink"]."</value>\n</member>\n";
			$rpcRequest .= "<member>\n<name>wiki</name>\n<value>".$ping["wiki"]."</value>\n</member>\n";
			if ($ping["author"]) {
				$rpcRequest .= "<member>\n<name>author</name>\n<value>".$ping["author"]."</value>\n</member>\n";
				if ($ping["authorpage"]) $rpcRequest .= "<member>\n<name>authorpage</name>\n<value>".$ping["authorpage"]."</value>\n</member>\n";
			}
			if ($ping["history"]) $rpcRequest .= "<member>\n<name>history</name>\n<value>".$ping["history"]."</value>\n</member>\n";
			if ($ping["changelog"]) $rpcRequest .= "<member>\n<name>changelog</name>\n<value>".$this->htmlspecialchars_ent($ping["changelog"], '', '', 'XML')."</value>\n</member>\n";
			$rpcRequest .= "</struct>\n</value>\n</param>\n";
			$rpcRequest .= "</params>\n";
			$rpcRequest .= "</methodCall>\n";

			foreach (explode(" ", $ping["server"]) as $server) {
				$response = $this->HTTPpost($server, $rpcRequest, "text/xml");
				if ($debug) print $response;
			}
		}
	}
	/**
	 * Gather the necessary parameters for WikiPing.
	 *
	 * @uses	Wakka::Href()
	 * @uses	Wakka::LoadPage()
	 * @param	string $server mandatory:
	 * @param	string $tag mandatory:
	 * @param	string $user mandatory:
	 * @param	string $changelog optional:
	 * @return	array/boolean either an array with the WikiPing-params or false if
	 * @todo	move to an extra class
	 */
	function GetPingParams($server, $tag, $user, $changelog = "") {
		$ping = array();
		if ($server) {
			$ping["server"] = $server;
			if ($tag) $ping["tag"] = $tag; else return false; // set page-title
			if (!$ping["taglink"] = $this->Href("", $tag)) return false; // set page-url
				if (!$ping["wiki"] = $this->config["wakka_name"]) return false; // set site-name
			$ping["history"] = $this->Href("revisions", $tag); // set url to history

			if ($user) {
				$ping["author"] = $user; // set username
				if ($this->LoadPage($user)) $ping["authorpage"] = $this->Href("", $user); // set link to user page
			}
			if ($changelog) $ping["changelog"] = $changelog;
			return $ping;
		} else return false;
	}

	/**
	 * Cookie related functions.
	 */
	/**
	 * Set a temporary Cookie.
	 */
	function SetSessionCookie($name, $value) { SetCookie($name.$this->config['wiki_suffix'], $value, 0, "/"); $_COOKIE[$name.$this->config['wiki_suffix']] = $value; $this->cookies_sent = true; }
	/**
	 * Set a Cookie.
	 */
	function SetPersistentCookie($name, $value) { SetCookie($name.$this->config['wiki_suffix'], $value, time() + 90 * 24 * 60 * 60, "/"); $_COOKIE[$name.$this->config['wiki_suffix']] = $value; $this->cookies_sent = true; }
	/**
	 * Delete a Cookie.
	 */
	function DeleteCookie($name) { SetCookie($name.$this->config['wiki_suffix'], "", 1, "/"); $_COOKIE[$name.$this->config['wiki_suffix']] = ""; $this->cookies_sent = true; }
	/**
	 * Get the value of a Cookie.
	 */
	function GetCookie($name)
	{
		if (isset($_COOKIE[$name.$this->config['wiki_suffix']]))
		{
			return $_COOKIE[$name.$this->config['wiki_suffix']];
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * HTTP/REQUEST/LINK RELATED
	 */
	/**
	 * Store a message in the session to be displayed after redirection.
	 *
	 * @param	string $message text to be stored
	 */
	function SetRedirectMessage($message) { $_SESSION["redirectmessage"] = $message; }
	/**
	 * Get a message, if one was stored before redirection.
	 *
	 * @return string either the text of the message or an empty string.
	 */
	function GetRedirectMessage()
	{
		$message = '';
		if (isset($_SESSION["redirectmessage"]))
		{
			$message = $_SESSION["redirectmessage"];
			$_SESSION["redirectmessage"] = "";
		}
		return $message;
	}
	/**
	 * Perform a redirection to another page.
	 *
	 * On IIS server, and if the page had sent any cookies, the redirection must not be performed
	 * by using the 'Location:' header: We use meta http-equiv OR javascript OR link (Credits MarceloArmonas)
	 * @author {@link http://wikkawiki.org/DotMG Mahefa Randimbisoa} (added IIS support)
	 * @access	public
	 * @since	Wikka 1.1.6.2
	 *
	 * @param	string	$url optional: destination URL; if not specified redirect to the same page.
	 * @param	string	$message optional: message that will show as alert in the destination URL
	 */
	function Redirect($url='', $message='')
	{
		if ($message != '') $_SESSION["redirectmessage"] = $message;
		$url = ($url == '' ) ? $this->config['base_url'].$this->tag : $url;
		if ((eregi('IIS', $_SERVER["SERVER_SOFTWARE"])) && ($this->cookies_sent))
		{
			@ob_end_clean(); 
			$redirlink = '<a href="'.$this->Href($url).'">'.REDIR_LINK_DESC.'</a>';
			die('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en"><head><title>'.sprintf(REDIR_DOCTITLE,$this->Href($url)).'</title>'.
'<meta http-equiv="refresh" content="0; url=\''.$url.'\'" /></head><body><div><script type="text/javascript">window.location.href="'.$url.'";</script>'.
'</div><noscript>'.sprintf(REDIR_MANUAL_CAPTION,$redirlink).'</noscript></body></html>');
		}
		else
		{
			header("Location: ".$url);
		}
		exit;
	}
	/**
	 * Get the Pagename (and the method).
	 */
	function MiniHref($method = "", $tag = "") { if (!$tag = trim($tag)) $tag = $this->tag; return $tag.($method ? "/".$method : ""); }
	/**
	 * Get the full url to a page/method.
	 *
	 * @uses	Wakka::MiniHref()
	 */
	function Href($method = "", $tag = "", $params = "")
	{
		$href = $this->config["base_url"].$this->MiniHref($method, $tag);
		if ($params)
		{
			$href .= ($this->config["rewrite_mode"] ? "?" : "&amp;").$params;
		}
		return $href;
	}
	/**
	 * Turn text with certain types of markup into the appropriate link.
	 *
	 * Beware of the $title parameter: quotes and backslashes should be previously escaped before passed to
	 * this method.
	 *
	 * @access	public
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::GetInterWikiUrl()
	 * @uses	Wakka::Href()
	 * @uses	Wakka::htmlspecialchars_ent()
	 * @uses	Wakka::LoadPage()
	 *
	 * @param	mixed $tag mandatory:
	 * @param	string $method optional:
	 * @param	 string $text optional:
	 * @param	 boolean $track optional:
	 * @param	 boolean $escapeText optional:
	 * @param	 string $title optional:
	 * @return	string
	 * @todo	i18n
	 * @todo	move regexps to regexp-library
	 */
	function Link($tag, $method='', $text='', $track=TRUE, $escapeText=TRUE, $title='') {
		if (!$text) $text = $tag;
		// escape text?
		if ($escapeText) $text = $this->htmlspecialchars_ent($text);
		$tag = $this->htmlspecialchars_ent($tag); #142 & #148
		$method = $this->htmlspecialchars_ent($method);
		$title_attr = $title ? ' title="'.$this->htmlspecialchars_ent($title).'"' : '';
		$url = '';

		// is this an interwiki link?
		// before the : should be a WikiName; anything after can be (nearly) anything that's allowed in a URL
		if (preg_match("/^([A-ZÄÖÜ][A-Za-zÄÖÜßäöü]+)[:](\S*)$/", $tag, $matches))	// @@@ FIXME #34 (inconsistent with Formatter) 
		{
			$url = $this->GetInterWikiUrl($matches[1], $matches[2]);
		}
		elseif (preg_match("/^(http|https|ftp|news|irc|gopher):\/\/([^\\s\"<>]+)$/", $tag))
		{
			$url = $tag; // this is a valid external URL
		}
		// is this a full link? ie, does it contain something *else* than valid WikiName (alpha-numeric) characters?
		// FIXME just use (!IsWikiName($tag)) here (then fix the RE there!)
		elseif (preg_match("/[^[:alnum:],ÄÖÜ,ßäöü]/", $tag))	// @@@ FIXME #34 (inconsistent with Formatter! ) remove all ',' from RE (comma should not be allowed in WikiNames!)
		{
			// check for email addresses
			if (preg_match("/^.+\@.+$/", $tag))
			{
				$url = "mailto:".$tag;
			}
			// check for protocol-less URLs
			else if (!preg_match("/:/", $tag))
			{
				$url = "http://".$tag;
			}
		}
		else
		{
			// it's a wiki link
			if (isset($_SESSION['linktracking']) && $_SESSION["linktracking"] && $track) $this->TrackLinkTo($tag);
			$linkedPage = $this->LoadPage($tag);
			return ($linkedPage ? '<a href="'.$this->Href($method, $linkedPage['tag']).'"'.$title_attr.'>'.$text.'</a>' : '<a class="missingpage" href="'.$this->Href("edit", $tag).'" title="'.CREATE_THIS_PAGE_LINK_TITLE.'">'.$text.'</a>'); #i18n
		}
		$external_link_tail = $this->GetConfigValue("external_link_tail");
		return $url ? '<a class="ext" href="'.$url.'">'.$text.'</a>'.$external_link_tail : $text;
	}

	// function PregPageLink($matches) { return $this->Link($matches[1]); }
	/**
	 * Check if a given text is in the Camelcase format.
	 */
	function IsWikiName($text) { return preg_match("/^[A-Z,ÄÖÜ][a-z,ßäöü]+[A-Z,0-9,ÄÖÜ][A-Z,a-z,0-9,ÄÖÜ,ßäöü]*$/", $text); }	// @@@ FIXME #34 (inconsistent with Formatter!) remove all ',' from RE (comma should not be allowed in WikiNames!)
	function TrackLinkTo($tag) { $_SESSION["linktable"][] = $tag; }
	function GetLinkTable() { return $_SESSION["linktable"]; }
	function ClearLinkTable() { $_SESSION["linktable"] = array(); }
	function StartLinkTracking() { $_SESSION["linktracking"] = 1; }
	function StopLinkTracking() { $_SESSION["linktracking"] = 0; }
	function WriteLinkTable()
	{
		// delete old link table
		$this->Query("delete from ".$this->config["table_prefix"]."links where from_tag = '".mysql_real_escape_string($this->GetPageTag())."'");
		// build new link table
		if ($linktable = $this->GetLinkTable())
		{
			$from_tag = mysql_real_escape_string($this->GetPageTag());
			$sql = '';
			$written = array();
			foreach ($linktable as $to_tag)
			{
				$lower_to_tag = strtolower($to_tag);
				if ((!$written[$lower_to_tag]) && ($lower_to_tag != strtolower($from_tag)))
				{
					if ($sql) $sql .= ', ';
					$sql .= "('".$from_tag."', '".mysql_real_escape_string($to_tag)."')";
					$written[$lower_to_tag] = 1;
				}
			}
			if ($sql)
			{
				$this->Query("INSERT INTO {$this->config['table_prefix']}links VALUES $sql");
			}
		}
	}
	/**
	 * Output the Header for Wikka-pages.
	 *
	 * @uses	Wakka::Action()
	 */
	function Header() { return $this->Action($this->config['header_action'], 0); }
	/**
	 * Output the Footer for Wikka-pages.
	 *
	 * @uses	Wakka::Action()
	 */
	function Footer() { return $this->Action($this->config['footer_action'], 0); }

	/**
	 * FORMS
	 */
	function FormOpen($method = "", $tag = "", $formMethod = "post")
	{
		$result = '<form action="'.$this->Href($method, $tag).'" method="'.$formMethod."\">\n";
		if (!$this->config["rewrite_mode"]) $result .= '<input type="hidden" name="wakka" value="'.$this->MiniHref($method, $tag)."\" />\n";
		return $result;
	}
	/**
	 * Provide the html to close a form.
	 *
	 * @return string the html-tag to close a form and a newline.
	 */
	function FormClose()
	{
		return "</form>\n";
	}

	/**
	 * INTERWIKI STUFF
	 */
	/**
	 * Read the list of interWikis from interwiki.conf.
	 *
	 * interwiki.conf in the main dir of wikka holds a list of urls to other
	 * websites and a shortcut for them, making it possible to use shortcuts
	 * to their pages instead of the full URL.
	 *
	 * The file must have only one entry per line consisting of:
	 * shortcut full_URL
	 *
	 * @uses	Wakka::AddInterWiki()
	 */
	function ReadInterWikiConfig()
	{
		if ($lines = file("interwiki.conf"))
		{
			foreach ($lines as $line)
			{
				if ($line = trim($line))
				{
					list($wikiName, $wikiUrl) = explode(" ", trim($line));
					$this->AddInterWiki($wikiName, $wikiUrl);
				}
			}
		}
	}
	/**
	 * Add an interWiki to the interWiki list.
	 */
	function AddInterWiki($name, $url)
	{
		$this->interWiki[strtolower($name)] = $url;
	}
	/**
	 * Return the full URL of an interwiki for a given shortcut, if in the list.
	 *
	 * @param  string $name mandatory: the shortcut for the interWiki
	 * @param  string $tag	mandatory: name of a page in the other wiki
	 * @return string the full URL for $tag or VOID
	 */
	function GetInterWikiUrl($name, $tag) {
		if (isset($this->interWiki[strtolower($name)]))
		{
			return $this->interWiki[strtolower($name)].$tag;
		}
	}

	/**
	 * REFERRERS
	 */
	function LogReferrer($tag = "", $referrer = "")
	{
		// fill values
		if (!$tag = trim($tag)) $tag = $this->GetPageTag();
		if (!isset($_SERVER['HTTP_REFERER'])) return; #38
		if (!$referrer = trim($referrer) && isset($_SERVER["HTTP_REFERER"])) $referrer = $_SERVER["HTTP_REFERER"];
		$referrer = $this->cleanUrl($referrer);			# secured JW 2005-01-20

		// check if it's coming from another site
		if ($referrer && !preg_match("/^".preg_quote($this->GetConfigValue("base_url"), "/")."/", $referrer))
		{
			$parsed_url = parse_url($referrer);
			$spammer = $parsed_url["host"];
			$blacklist = $this->LoadSingle("select * from ".$this->config["table_prefix"]."referrer_blacklist WHERE spammer = '".mysql_real_escape_string($spammer)."'");
			if (!$blacklist) {
			$this->Query("insert into ".$this->config["table_prefix"]."referrers set ".
				"page_tag = '".mysql_real_escape_string($tag)."', ".
				"referrer = '".mysql_real_escape_string($referrer)."', ".
				"time = now()");
			}
		}
	}
	function LoadReferrers($tag = "")
	{
		return $this->LoadAll("select referrer, count(referrer) as num from ".$this->config["table_prefix"]."referrers ".($tag = trim($tag) ? "where page_tag = '".mysql_real_escape_string($tag)."'" : "")." group by referrer order by num desc");
	}

	/**
	 * ACTIONS / PLUGINS
	 */
	/**
	 * Handle the call to an action.
	 *
	 * @uses	Wakka::IncludeBuffered()
	 * @uses	Wakka::StartLinkTracking()
	 * @uses	Wakka::StopLinkTracking()
	 */
	function Action($action, $forceLinkTracking = 0)
	{
		$action = trim($action);
		$vars=array();

		// search for parameters separated by spaces or newlines - #371
		if (preg_match('/\s/', $action))
		{
			// parse input for action name and parameters
			preg_match('/^([A-Za-z0-9]*)\s+(.*)$/s', $action, $matches);
			// extract $action and $vars_temp ("raw" attributes)
			list(, $action, $vars_temp) = $matches;

			if ($action) {
				// match all attributes (key and value)
				preg_match_all('/([A-Za-z0-9]*)=("|\')(.*)\\2/U', $vars_temp, $matches);

				// prepare an array for extract() to work with (in $this->IncludeBuffered())
				if (is_array($matches)) {
					for ($a = 0; $a < count($matches[0]); $a++) {
						$vars[$matches[1][$a]] = $matches[3][$a];
					}
				}
				$vars['wikka_vars'] = trim($vars_temp); // <<< add the buffered parameter-string to the array
			} else {
				return '<em class="error">'.ACTION_UNKNOWN_SPECCHARS.'</em>'; // <<< the pattern ([A-Za-z0-9])\s+ didn't match!
			}
		}
		if (!preg_match('/^[a-zA-Z0-9]+$/', $action)) return '<em class="error">'.ACTION_UNKNOWN_SPECCHARS.'</em>';
		if (!$forceLinkTracking)
		{
			/**
			 * @var boolean holds previous state of LinkTracking before we StopLinkTracking(). It will then be used to test if we should StartLinkTracking() or not. 
			 */
			$link_tracking_state = isset($_SESSION['linktracking']) ? $_SESSION['linktracking'] : 0; #38
			$this->StopLinkTracking();
		}
		$result = $this->IncludeBuffered(strtolower($action).'.php', '<em class="error">'.sprintf(ACTION_UNKNOWN,$action).'</em>', $vars, $this->config['action_path']);
		if ($link_tracking_state)
		{
			$this->StartLinkTracking();
		}
		return $result;
	}
	/**
	 * Use a handler on the current page.
	 *
	 * @uses	Wakka::IncludeBuffered()   
	 * @todo	 use templating class
	 * @todo	 separate different classes of handlers (page, user, files etc.);
	 */
	function Method($method)
	{
		if (strstr($method, '/'))
		{
			$method = substr($method, strrpos($method, '/')+1);
		}
		if (!$handler = $this->page['handler']) $handler = 'page';
		$method_location = $handler.'/'.$method.'.php';
		$method_location_disp = '<tt>'.$method_location.'</tt>';
		return $this->IncludeBuffered($method_location, '<div class="page"><em class="error">'.sprintf(HANDLER_UNKNOWN,$method_location_disp).'</em></div>', '', $this->config['handler_path']);
	}
	/**
	 * Format a text using a given or the standard "wakka" formatter.
	 *
	 * @uses	Wakka::IncludeBuffered()
	 * @param	$text
	 */
	function Format($text, $formatter="wakka") { return $this->IncludeBuffered($formatter.'.php', '<em class="error">'.sprintf(FORMATTER_UNKNOWN,$formatter),'</em>', compact('text'), $this->config['wikka_formatter_path']); }

	/**
	 * USERS
	 */
	/**
	 * Load a (given) user.
	 *
	 * <p>If a second parameter $password is supplied, this method checks if this password is valid, thus a false return value would mean
	 * nonexistent user or invalid password. Note that this parameter is the <strong>hashed value</strong> of the password usually typed in 
	 * by user, and not the password itself.</p>
	 * <p>If this parameter is not supplied, it checks only for existence of the username, and returns an array containing all information
	 * about the given user if it exists, or a false value. In this latter case, result is cached in $this->specialCache in order to 
	 * improve performance.</p>
	 *
	 * @uses	Wakka::LoadSingle()
	 * @param	string $name mandatory: name of the user
	 * @param	string $password optional: password of the user. default: 0 (=none)
	 * @return	array the data of the user, or false if non-existing user or invalid password supplied.
	 */
	function LoadUser($name, $password = 0) 
	{
		if (($password === 0) && (isset($this->specialCache['user'][strtolower($name)])))
		{
			return ($this->specialCache['user'][strtolower($name)]);
		}
		$user = $this->LoadSingle("select * from ".$this->config['table_prefix']."users where name = '".mysql_real_escape_string($name)."' ".($password === 0 ? "" : "and password = '".mysql_real_escape_string($password)."'")." limit 1");
		if ($password === 0)
		{
			$this->specialCache['user'][strtolower($name)] = $user;
		}
		return ($user);
	}
	/**
	 * Load all users registered at the wiki.
	 *
	 * @uses	Wakka::LoadAll()
	 * @return	array contains all users data
	 */
	function LoadUsers() { return $this->LoadAll("select * from ".$this->config['table_prefix']."users order by name"); }
	/**
	 * Get the name or address of the current user.
	 *
	 * If the user is not logged-in, the host name is only looked up if enabled
	 * in the config (since it can lead to long page generation times).
	 * Set 'enable_user_host_lookup' in wikka.config.php to 1 to do the look-up.
	 * Otherwise the ip-address is used.
	 *
	 * @uses	Wakka::GetUser()
	 * @return	string name/ip-adress/host-name of the current user
	 */
	function GetUserName()
	{
		if ($user = $this->GetUser())
		{
			$name = $user['name'];
		}
		else
		{
			$ip = $_SERVER['REMOTE_ADDR'];
			if ($this->config['enable_user_host_lookup'] == 1)
			{
				$name = gethostbyaddr($ip) ? gethostbyaddr($ip) : $ip;
			}
			else
			{
				$name = $ip;
			}
		}
		return $name;
	}
	/**
	 * Get the name of the current user if he is logged in.
	 *
	 * @return string/NULL either a string with the user name or NULL
	 */
	function GetUser() { return (isset($_SESSION["user"])) ? $_SESSION["user"] : NULL; }
	/**
	 * Log-in a (given) user.
	 *
	 * The data of the user is stored in the session and name and password are stored in a Cookie.
	 * 
	 * @uses	Wakka::SetPersistentCookie()
	 * @param	array $user mandatory: must contain the userdata
	 * @todo	name should be made made persistent with opposite function LogOutUser()
	 */
	function SetUser($user) { $_SESSION["user"] = $user; $this->SetPersistentCookie("user_name", $user["name"]); $this->SetPersistentCookie("pass", $user["password"]); }
	/**
	 * Log-out the current user.
	 *
	 * The data of the user is deleted from the session and name and password Cookies are deleted, too.
	 * 
	 * @uses	Wakka::DeleteCookie()
	 * @todo	name should be made made persistent with opposite function SetUser()
	 */
	function LogoutUser() { $_SESSION["user"] = ""; $this->DeleteCookie("user_name"); $this->DeleteCookie("pass"); }
	/**
	 * Find out if the current user wants comments to be shown by default.
	 *
	 * If the user is not logged-in, it is assumed that he does not want
	 * the comments be shown by default.
	 *
	 * @uses	Wakka::GetUser()
	 * @return	boolean TRUE if the user wants Comments, FALSE otherwise
	 */
	function UserWantsComments() { if (!$user = $this->GetUser()) return false; return ($user["show_comments"] == "Y"); }

	/**
	 * COMMENTS
	 *
	/**

	 * Load the comments for a (given) page.
	 *
	 * @uses	Wakka::LoadAll()
	 * @uses	Wakka::TraverseComments()
	 * @param	string $tag mandatory: name of the page
	 * @param   integer $order optional: order of comments. Default: COMMENT_ORDER_DATE_ASC
	 * @return	array All the comments for this page ordered by $order
	 */
	function LoadComments($tag, $order=COMMENT_ORDER_DATE_ASC) {
		if($order==COMMENT_ORDER_DATE_ASC) { // Return ASC by date
			return $this->LoadAll("SELECT * FROM ".$this->config["table_prefix"]."comments WHERE page_tag = '".mysql_real_escape_string($tag)."' AND deleted IS NULL ORDER BY time"); 
		}
		if($order==COMMENT_ORDER_DATE_DESC) {
			return $this->LoadAll("SELECT * FROM ".$this->config["table_prefix"]."comments WHERE page_tag = '".mysql_real_escape_string($tag)."' AND deleted IS NULL ORDER BY time DESC"); 
		}
		if($order==COMMENT_ORDER_THREADED) {
			$record = array();
			$this->TraverseComments($tag, $record);
			return $record;
		}
	}

	/**
	 * Updates modified table fields in bulk. 
	 * WARNING: Do not add, delete, or reorder records or fields in
	 *          queries prior to calling this function!!
	 * @uses    Query()
	 * @param	string $tablename mandatory: Table to modify
	 * @param	string $keyfield mandatory: Field name of primary key
	 * @param	resource $old_res mandatory: Old (original) resource
	 *			as generated by mysql_query
	 * @param	resource $new_res mandatory: New (modified) resource
	 *			originally created as a copy of $old_res
	 * @todo    Does not currently handle deletions or insertions of
	 *			records or fields.
	 */
	 function Update($tablename, $keyfield, $old_res, $new_res) {
		 // Sanity checks!
		 if(count($old_res) != count($new_res)) return;
		 if(!$tablename || ! $keyfield) return;
		 // Reference:
		 // http://www.php.net/manual/en/function.mysql-query.php,
		 // annotation by babba@nurfuerspam.de
		 for($i=0; $i<count($old_res); $i++) {
			 // Sanity check
			 if($old_res[0][$keyfield] != $new_res[0][$keyfield]) return;
			 $changedvals = "";
			 foreach($old_res[$i] as $key=>$oldval) {
				 $newval = $new_res[$i][$key];
				 if($oldval != $newval) {
					 if($changedvals != "") {
						 $changedvals .= ", ";
					 }
					 $changedvals .= "`".$key."`=";
					 if(!is_numeric($newval)) {
						 $changedvals .= "'".$newval."'";
					 } else {
						 $changedvals .= $newval;
					 }
				 }
			 }
			 if($changedvals == "") return;
			 $this->Query("UPDATE ".$tablename." SET ".$changedvals." WHERE ".$keyfield."=".$old_res[$i][$keyfield]);
		 }
	}

	/**
	 * Traverse comments in threaded order 
	 *
	 * @uses	Wakka::LoadAll()
	 * @uses	Wakka::CountComments()
	 * @uses	Wakka::LoadSingle()
	 * @uses	Wakka::TraverseComments()
	 * @param	string $tag mandatory: name of the page
	 * @param   array &$graph mandatory: empty array 
	 * @return	array Ordered graph of comments and indent levels (values) for this page
	 */
	function TraverseComments($tag, &$graph) {
		static $level = -1;
		static $visited = array();
		static $transformed_map = array();
		if(!$transformed_map) {
			array_push($visited, 'NULL');
			$count = $this->CountComments($tag);
			$initial_map = $this->LoadAll("SELECT id, parent FROM ".$this->config["table_prefix"]."comments WHERE page_tag='".$tag."' ORDER BY id asc");
			// Create an array of arrays, with the (unique) key of
			// 'parent' pointing to an array of date-ordered
			// children.
			for($i=0; $i<$count; ++$i) {
				$id = $initial_map[$i]['id'];
				$parent = $initial_map[$i]['parent'];
				if(!$parent)
					$parent = 'NULL';
				if(!array_key_exists($parent, $transformed_map))
						$transformed_map[$parent] = array();
				array_push($transformed_map[$parent], $id);
			}
		}
		//DEBUG vv
/*
		foreach($transformed_map as $key=>$val) {
			echo "<br/>Key: ".$key." Vals: ";
			foreach($val as $key2=>$val2) {
				echo $val2." ";
			}
		}
		echo "<br/>Visited: ";
		foreach($visited as $val) {
			echo $val." ";
		}
*/
		//DEBUG ^^
		if(is_array($transformed_map[end($visited)]))
			$id = array_shift($transformed_map[end($visited)]);
		//DEBUG
		//echo "<br/>Visiting: ".$id;
		if(isset($id)) {
			// Limit recursions to COMMENT_MAX_TRAVERSAL_DEPTH
			if($level >= COMMENT_MAX_TRAVERSAL_DEPTH) {
				--$level;
				array_pop($visited);
				$this->TraverseComments($tag, $graph); 
			} else {
				// Traverse children
				++$level;
				array_push($visited, $id);
				$graph[] = $this->LoadSingle("SELECT * FROM ".$this->config["table_prefix"]."comments WHERE id = ".$id);
				end($graph);
				$graph[key($graph)]['level'] = $level;
				$this->TraverseComments($tag, $graph);
			}
		} else if($level < 0) { // End traversal
			//DEBUG
			//echo "<br/>Ending!";
			return;
		} else {
			// Step back to the parent to find next child
			--$level;
			array_pop($visited);
			//DEBUG
			//echo "<br/>Going back to ".$prev;
			$this->TraverseComments($tag, $graph); 
		}
	}

	/**
	 * Count the comments for a (given) page.
	 *
	 * @uses	Wakka::LoadSingle()
	 * @param	string $tag mandatory: name of the page
	 * @return	integer Count of comments 
	 */
	function CountComments($tag) {
			$data = $this->LoadSingle("SELECT count(*) FROM ".$this->config["table_prefix"]."comments WHERE page_tag = '".mysql_real_escape_string($tag)."'"); 
			return $data['count(*)'];
	}

	/**
	 * Load the last 50 comments on the wiki.
	 *
	 *
	 * @uses	Wakka::LoadAll()
	 * @param	integer $limit optional: number of last comments. default: 50
	 * @return	array the last x comments
	 */
	function LoadRecentComments($limit = 50) { return $this->LoadAll("SELECT * FROM ".$this->config["table_prefix"]."comments ORDER BY time DESC LIMIT ".intval($limit)); }
	/**
	 * Load the last 50 comments on different pages on the wiki.
	 *
	 * @uses	Wakka::LoadAll()
	 * @param	integer $limit optional: number of last comments on different pages. default: 50
	 * @return	array the last x comments on different pages
	 */
	function LoadRecentlyCommented($limit = 50)
	{
		$sql = "SELECT comments.id, comments.page_tag, comments.time, comments.comment, comments.user"
			. " FROM ".$this->config["table_prefix"]."comments AS comments"
			. " LEFT JOIN ".$this->config["table_prefix"]."comments AS c2 ON comments.page_tag = c2.page_tag AND comments.id < c2.id"
			. " WHERE c2.page_tag IS NULL "
			. " ORDER BY time DESC "
			. " LIMIT ".intval($limit);
		return $this->LoadAll($sql);
	}
	/**
	 * Save a (given) comment for a (given) page.
	 *
	 * @uses	Wakka::GetUserName()
	 * @uses	Wakka::Query()
	 * @param	string $page_tag mandatory: name of the page
	 * @param	string $comment mandatory: text of the comment
	 */
	function SaveComment($page_tag, $comment, $parent_id)
	{
		// get current user
		$user = $this->GetUserName();

		// add new comment
		$parent_id = mysql_real_escape_string($parent_id);
		if(!$parent_id) 
			$parent_id = "NULL";
		$this->Query("INSERT INTO ".$this->config["table_prefix"]."comments SET ".
			"page_tag = '".mysql_real_escape_string($page_tag)."', ".
			"time = now(), ".
			"comment = '".mysql_real_escape_string($comment)."', ".
			"parent = $parent_id, ".
			"user = '".mysql_real_escape_string($user)."'");
	}

	/**
	 * ACCESS CONTROL
	 */
	/**
	 * Check if current user is the owner of the current or a specified page.
	 * 
	 * @access	  public
	 * @uses		Wakka::GetPageOwner()
	 * @uses		Wakka::GetPageTag() 
	 * @uses		Wakka::GetUser()
	 * @uses		Wakka::GetUserName()
	 * @uses		Wakka::IsAdmin()
	 *
	 * @param	   string  $tag optional: page to be checked. Default: current page.
	 * @return	  boolean TRUE if the user is the owner, FALSE otherwise.
	 */
	function UserIsOwner($tag = "")
	{
		// if not logged in, user can't be owner!
		if (!$this->GetUser()) return false;

		// if user is admin, return true. Admin can do anything!
		if ($this->IsAdmin()) return true;

		// set default tag & check if user is owner
		if (!$tag = trim($tag)) $tag = $this->GetPageTag();
		if ($this->GetPageOwner($tag) == $this->GetUserName()) return true;
		return false;
	}
	/**
	 * Check if current user is listed in configuration list as admin.
	 * 
	 * @access	  public
	 * @uses		Wakka::GetUserName()
	 * @return	  boolean TRUE if the user is an admin, FALSE otherwise.
	 */
	function IsAdmin() {
		$adminstring = $this->config["admin_users"];
		$adminarray = explode(',' , $adminstring);

		foreach ($adminarray as $admin) {
			if (trim($admin) == $this->GetUserName()) return true;
		}
		return false;
	}
	/**
	 * Return the owner for a given/the current page at a given/the current version.
	 *
	 * @uses	Wakka::GetPageTag()
	 * @uses	Wakka::LoadPage()
	 * @param	string $tag optional: name of the page. default: current one
	 * @param	? $time optional: time of the page-revision. default: current one?
	 * @return	string the owner of the page
	 */
	function GetPageOwner($tag = "", $time = "") { if (!$tag = trim($tag)) $tag = $this->GetPageTag(); if ($page = $this->LoadPage($tag, $time)) return $page["owner"]; return (false);}
	/**
	 * Set an (given) owner for a (given) page.
	 *
	 * @uses	Wakka::LoadUser()
	 * @uses	Wakka::Query()
	 * @param	string $tag mandatory: name of the page
	 * @param	string $user mandatory: name of the user
	 * @todo	see if "(Public)" and "(Nobody)" have to be replaced by constants to allow i18n
	 * 			JW: could keep these constants in the database but 'translate' them in the UI
	 */
	function SetPageOwner($tag, $user)
	{
		// check if user exists
		if( $user <> '' && ($this->LoadUser($user) || $user == "(Public)" || $user == "(Nobody)"))
		{
			if ($user == "(Nobody)") $user = "";
			// update latest revision with new owner
			$this->Query("update ".$this->config["table_prefix"]."pages set owner = '".mysql_real_escape_string($user)."' where tag = '".mysql_real_escape_string($tag)."' and latest = 'Y' limit 1");
		}
	}
	/**
	 * Load the Access Control list for a given page and a given privilege.
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::LoadSingle()
	 * @param	string $tag mandatory:
	 * @param	string $privilege mandatory:
	 * @param	integer $useDefaults optional:
	 * @return	array the page name and the acl
	 */
	function LoadACL($tag, $privilege, $useDefaults = 1)
	{
		if ((!$acl = $this->LoadSingle("SELECT ".mysql_real_escape_string($privilege)."_acl FROM ".$this->config["table_prefix"]."acls WHERE page_tag = '".mysql_real_escape_string($tag)."' LIMIT 1")) && $useDefaults)
		{
			$acl = array("page_tag" => $tag, $privilege."_acl" => $this->GetConfigValue("default_".$privilege."_acl"));
		}
		return $acl;
	}
	/**
	 * Load all Access Control lists for a given page.
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::LoadSingle()
	 * @param	string $tag mandatory:
	 * @param	integer $useDefaults optional:
	 * @return	array the page name and all acls
	 */
	function LoadAllACLs($tag, $useDefaults = 1)
	{
		if ((!$acl = $this->LoadSingle("SELECT * FROM ".$this->config["table_prefix"]."acls WHERE page_tag = '".mysql_real_escape_string($tag)."' LIMIT 1")) && $useDefaults)
		{
			$acl = array("page_tag" => $tag, "read_acl" => $this->GetConfigValue("default_read_acl"), "write_acl" => $this->GetConfigValue("default_write_acl"), "comment_acl" => $this->GetConfigValue("default_comment_acl"));
		}
		return $acl;
	}
	/**
	 * Save an Access Control List for a given privilege on a given page to the database. 
	 * If the ACL record doesn't already exist, create it with the
	 * config defaults.
	 *
	 * @uses	Wakka::LoadACL()
	 * @uses	Wakka::Query()
	 * @uses	Wakka::SaveACL()
	 *
	 * @param	string $tag mandatory: name of the page
	 * @param	string $privilege mandatory: name of the privilege
	 * @param	string $list mandatory: a string containing the AC-Syntax   
	 */
	function SaveACL($tag, $privilege, $list) {
		$insert = 0;
		if(!$acls = $this->LoadAllACLs($tag, 0)) { # Load defaults
			$insert = 1;
			$acls['read_acl'] = $this->GetConfigValue('default_read_acl');
			$acls['write_acl'] = $this->GetConfigValue('default_write_acl');
			$acls['comment_acl'] = $this->GetConfigValue('default_comment_acl');
		}
		$priv = mysql_real_escape_string($privilege)."_acl";
		$acls[$priv] = 
			mysql_real_escape_string(trim(str_replace("\r", "", $list))); 
		if(!$insert) {
			$this->Query("UPDATE ".$this->config["table_prefix"]."acls SET ".$priv." = '".$acls[$priv]."' WHERE page_tag = '".mysql_real_escape_string($tag)."' LIMIT 1");
		} else {
			$acl_list = "";
			foreach($acls as $acl => $value) {
				$acl_list .= $acl." = '".$value."', ";
			}
			# Remove the trailing comma
			$acl_list = trim($acl_list, ", ");
			$this->Query("INSERT INTO ".$this->config["table_prefix"]."acls SET page_tag = '".mysql_real_escape_string($tag)."', ".$acl_list);
		}
	}

	/**
	 * Clone an Access Control List from one page to another. If ACL
	 * list isn't defined for the source page, use defaults from
	 * config for the destination page. 
	 *
	 * @uses	Wakka::LoadAllACLs()
	 * @uses	Wakka::Query()
	 *
	 * @param	string $from_tag mandatory: Source page for ACLs 
	 * @param	string $to_tag mandatory: Target page for ACLs 
	 */
	function CloneACLs($from_tag, $to_tag) {
		$acls = $this->LoadAllACLs($from_tag, 1); # Load defaults
		$acl_list = "";
		foreach($acls as $acl => $value) {
			if($acl === 'page_tag') continue;
			$acl_list .= $acl." = '".$value."', ";
		}
		# Remove the trailing comma
		$acl_list = trim($acl_list, ", ");

		if($this->LoadAllACLs($to_tag, 0)) {
			$this->Query("UPDATE ".$this->config["table_prefix"]."acls SET ".$acl_list." WHERE page_tag = '".mysql_real_escape_string($to_tag)."' LIMIT 1");
		} else {
			$this->Query("INSERT INTO ".$this->config["table_prefix"]."acls SET page_tag = '".mysql_real_escape_string($to_tag)."', ".$acl_list);
		}
	}

	/**
	 * Split ACL list on whitespace or commas, then trim any remaining
	 * whitespace.  Return a whitespace-delimited list.  Used mainly
	 * to remove carriage returns.
	 * @param string $list mandatory: List of ACLs to trim
	 **/
	function TrimACLs($list) {
		foreach (preg_split("/[\s,]+/", $list) as $line)
		{
			$line = trim($line);
			$trimmed_list .= $line." ";
		}
		return $trimmed_list;
	}

	/**
	 * Check if a given/ the current user has access to a given privilege on a/ the current page.
	 *
	 * @uses	Wakka::ACLs()
	 * @uses	 Wakka::GetPageTag()
	 * @uses	 Wakka::GetUser()
	 * @uses	 Wakka::GetUserName()
	 * @uses	 Wakka::LoadAllACLs
	 * @uses	 Wakka::UserIsOwner()
	 * @param   string $privilege mandatory: privilege which shall be checked
	 * @param	string $tag optional: name of the page default: current page
	 * @param	string $tag optional: name of the user default: current user
	 * @return	 FALSE/0 or TRUE/1
	 * @todo	make this function return only a boolean value
	 */
	function HasAccess($privilege, $tag = "", $user = "")
	{
		// set defaults
		if (!$tag) $tag = $this->GetPageTag();
		if (!$user) $user = $this->GetUserName();

		// if current user is owner, return true. owner can do anything!
		if ($this->UserIsOwner($tag)) return true;

		// see whether user is registered and logged in
		$registered = FALSE;
		if ($this->GetUser()) $registered = true;

		// load acl
		if ($tag == $this->GetPageTag())
		{
			$acl = $this->ACLs[$privilege."_acl"];
		}
		else
		{
			$tag_ACLs = $this->LoadAllACLs($tag);
			$acl = $tag_ACLs[$privilege."_acl"];
		}

		// fine fine... now go through acl
		foreach (preg_split("/[\s,]+/", $acl) as $line)
		{
			// check for inversion character "!"
			if (preg_match("/^[!](.*)$/", $line, $matches))
			{
				$negate = 1;
				$line = $matches[1];
			}
			else
			{
				$negate = 0;
			}

			// if there's still anything left... lines with just a "!" don't count!
			if ($line)
			{
				switch ($line[0])
				{
				// comments
				case "#":
					break;
				// everyone
				case "*":
					return !$negate;
				// only registered users
				case "+":
					// return ($registered) ? !$negate : false;
					return ($registered) ? !$negate : $negate;
				// aha! a user entry.
				default:
					if ($line == $user)
					{
						return !$negate;
					}
				}
			}
		}

		// tough luck.
		return false;
	}

	/**
	 * MAINTENANCE
	 */
	/**
	 * Purge referrers and old page revisions.
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::Query()
	 */
	function Maintenance()
	{
		// purge referrers
		if ($days = $this->GetConfigValue("referrers_purge_time")) {
			$this->Query("DELETE FROM ".$this->config["table_prefix"]."referrers WHERE time < date_sub(now(), interval '".mysql_real_escape_string($days)."' day)");
		}

		// purge old page revisions
		if ($days = $this->GetConfigValue("pages_purge_time")) {
			$this->Query("delete from ".$this->config["table_prefix"]."pages where time < date_sub(now(), interval '".mysql_real_escape_string($days)."' day) and latest = 'N'");
		}
	}
	/**
	 * THE BIG EVIL NASTY ONE!
	 *
	 * @uses	Wakka::Footer()
	 * @uses	Wakka::GetCookie()
	 * @uses	Wakka::GetMicrotime()
	 * @uses	Wakka::GetUser()
	 * @uses	Wakka::Header()
	 * @uses	Wakka::Href()
	 * @uses	Wakka::LoadAllACLs()
	 * @uses	Wakka::LoadUser()
	 * @uses	Wakka::LogReferrer()
	 * @uses	Wakka::Maintenance()
	 * @uses	Wakka::Method()
	 * @uses	Wakka::ReadInterWikiConfig()
	 * @uses	Wakka::Redirect()
	 * @uses	Wakka::SetCookie()
	 * @uses	Wakka::SetPage()
	 * @uses	Wakka::SetUser()
	 *
	 * @param	string $tag mandatory: name of the single page/image/file etc. to be used
	 * @param	string $method optional: the method which should be used. default: "show"
	 */
	function Run($tag, $method = "")
	{
		// do our stuff!
		if (!$this->method = trim($method)) $this->method = "show";
		if (!$this->tag = trim($tag)) $this->Redirect($this->Href("", $this->config["root_page"]));
		if (!$this->GetUser() && ($user = $this->LoadUser($this->GetCookie('user_name'), $this->GetCookie('pass')))) $this->SetUser($user);
		if ((!$this->GetUser() && isset($_COOKIE["wikka_user_name"])) && ($user = $this->LoadUser($_COOKIE["wikka_user_name"], $_COOKIE["wikka_pass"])))
		{
		 //Old cookies : delete them
			SetCookie('wikka_user_name', "", 1, "/");
			$_COOKIE['wikka_user_name'] = "";
			SetCookie('wikka_pass', '', 1, '/');
			$_COOKIE['wikka_pass'] = "";
			$this->SetUser($user);
		}
		$this->SetPage($this->LoadPage($tag, (isset($_REQUEST["time"]) ? $_REQUEST["time"] :'')));

		$this->LogReferrer();
		$this->ACLs = $this->LoadAllACLs($this->tag);
		$this->ReadInterWikiConfig();
		if(!($this->GetMicroTime()%3)) $this->Maintenance();

		if (preg_match('/\.(xml|mm)$/', $this->method))
		{
			header("Content-type: text/xml");
			print($this->Method($this->method));
		}
		// raw page handler
		elseif ($this->method == "raw")
		{
			header("Content-type: text/plain");
			print($this->Method($this->method));
		}
		// grabcode page handler
		elseif ($this->method == "grabcode")
		{
			print($this->Method($this->method));
		}
		elseif (preg_match('/\.(gif|jpg|png)$/', $this->method))
		{
			header('Location: images/' . $this->method);
		}
		elseif (preg_match('/\.css$/', $this->method))
		{
			header('Location: css/' . $this->method);
		}
		else
		{
			print($this->Header().$this->Method($this->method).$this->Footer());
		}
	}
}
?>
