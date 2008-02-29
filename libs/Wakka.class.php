<?php
/**
 * This file is part of Wikka, a PHP wiki engine.
 *
 * It contains the Wakka class, which provides the core functions
 * to run Wikka.
 *
 * @package		Wikka
 * @subpackage	Libs
 * @version		$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author	{@link http://www.mornography.de/ Hendrik Mans}
 * @author	{@link http://wikkawiki.org/JsnX Jason Tourtelotte}
 * @author	{@link http://wikkawiki.org/JavaWoman Marjolein Katsma}
 * @author	{@link http://wikkawiki.org/NilsLindenberg Nils Lindenberg}
 * @author	{@link http://wikkawiki.org/DotMG Mahefa Randimbisoa}
 * @author	{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 * @author	{@link http://wikkawiki.org/BrianKoontz Brian Koontz}
 *
 * @copyright	Copyright 2002-2003, Hendrik Mans <hendrik@mans.de>
 * @copyright	Copyright 2004-2005, Jason Tourtelotte <wikka-admin@jsnx.com>
 * @copyright	Copyright 2006-2007, {@link http://wikkawiki.org/CreditsPage Wikka Development Team}
 */

/**#@+
 * Numeric constant used as default. May be made a configurable value.
 */
if (!defined('COMMENT_NO_DISPLAY')) define('COMMENT_NO_DISPLAY', 0);
if (!defined('COMMENT_ORDER_DATE_ASC')) define('COMMENT_ORDER_DATE_ASC', 1);
if (!defined('COMMENT_ORDER_DATE_DESC')) define('COMMENT_ORDER_DATE_DESC', 2);
if (!defined('COMMENT_ORDER_THREADED')) define('COMMENT_ORDER_THREADED', 3);
if (!defined('COMMENT_MAX_TRAVERSAL_DEPTH')) define('COMMENT_MAX_TRAVERSAL_DEPTH', 10);
if (!defined('MAX_HOSTNAME_LENGTH_DISPLAY')) define('MAX_HOSTNAME_LENGTH_DISPLAY', 50);
/**
 * Length to use for generated part of id attribute.
 */
if (!defined('ID_LENGTH')) define('ID_LENGTH',10);		// @@@ maybe make length configurable
/**#@-*/

/**#@+
 * String constant defining a regular expresion pattern.
 */
/**
 * To be used in replacing img tags having an alt attribute with the value of the alt attribute, trimmed.
 * - $result[0] : the entire img tag
 * - $result[1] : If the alt attribute exists, this holds the single character used to delimit the alt string.
 * - $result[2] : The content of the alt attribute, after it has been trimmed, if the attribute exists.
 */
if (!defined('PATTERN_REPLACE_IMG_WITH_ALTTEXT')) define('PATTERN_REPLACE_IMG_WITH_ALTTEXT', '/<img[^>]*(?<=\\s)alt=("|\')\s*(.*?)\s*\\1.*?>/');
/**
 * Defines characters that are not valid for an ID.
 * Defined as the negation of a character class comprising the characters that
 * <i>are</i> valid in an ID. All but valid characters will be stripped when deriving
 * an ID froma provided string.
 */
if (!defined('PATTERN_INVALID_ID_CHARS')) define ('PATTERN_INVALID_ID_CHARS', '/[^A-Za-z0-9_:.-\s]/');
/**#@-*/

/**#@+
 * String constant defining a regularly used bit of constant text.
 */
if (!defined('WIKKA_URL_EXTENSION')) define('WIKKA_URL_EXTENSION', 'wikka.php?wakka=');
/**#@-*/

/**
 * The Wikka core class.
 *
 * This class contains all the core methods used to run Wikka.
 * @name		Wakka
 * @package		Wikka
 * @subpackage	Libs
 *
 */
class Wakka
{
	/**
	 * Hold the Wikka version.
	 *
	 * @var		string
	 */
	var $VERSION;
	/**
	 * Hold the wikka config.
	 *
	 * @access	private
	 * @var		array
	 */
	var $config = array();
	/**
	 * Hold the connection-link to the database.
	 *
	 * @access	public
	 * @var		resource
	 */
	var $dblink;

	/**
	 * Hold a log of queries and the times used for them; used for debugging.
	 *
	 * @var		array
	 */
	var $queryLog = array();
	/**
	 * Hold the interWiki List.
	 *
	 * @var		array
	 */
	var $interWiki = array();

	/**#@+*
	 * Variable to store data about HTTP headers.
	 */
	/**
	 * Keep track of whether a (at least one) cookie has been sent to the browser.
	 *
	 * @var		boolean
	 */
	var $cookies_sent = FALSE;
	/**
	 * If this value is set to TRUE, Anti-caching HTTP headers won't be added.
	 *
	 * @access	public
	 * @var		boolean
	 */
	var $do_not_send_anticaching_headers = FALSE;
	/**
	 * Customized head elements to be added in the <head> section.
	 *
	 * Array one may use to gather customized elements to be added inside <head>
	 * section, like additional stylesheet links, customized javascript, ...
	 * Handlers and/or actions adding items to this variable are responsible for
	 * sanitizing values passed to it.
	 * Use {@link Wakka::AddCustomHeader()} to populate this array.
	 *
	 * @access	public
	 * @var		array
	 */
	var $additional_headers = array();
	/**#@-*/

	/**#@+*
	 * Variable to store data about pages.
	 */
	/**
	 * Hold record for the current page.
	 *
	 * @access	private
	 * @var		array
	 */
	var $page;
	/**
	 * Hold the name of the current page.
	 *
	 * @access	private
	 * @var		string
	 */
	var $tag;
	/**
	 * Title of the page to insert in the <title> element.
	 *
	 * @access	public
	 * @var		string
	 */
	var $page_title = '';
	/**
	 * This associative array stores cached pages.
	 *
	 * Keys are page names (tag) or page id (prepended with /#) and values are the
	 * page structure. See {@link Wakka::CachePage()}
	 *
	 * @access	public
	 * @var		array
	 */
	var $pageCache;
	/**
	 * Keep track of included pages to avoid circular references.
	 *
	 * @access	private
	 * @var		array
	 */
	var $included_pages = array();
	/**
	 * Cache for various stuff.
	 */
	var $specialCache = array();
	/**#@-*/

	/**#@+*
	 * Variable to store data about users.
	 */
	/**
	 * Tracks whether the <b>current</b> user is registered or not.
	 *
	 * @access	public
	 * @var		boolean
	 */
	var $registered = FALSE;
	/**
	 * Name of <b>current</b> user if registered.
	 *
	 * @access	public
	 * @var		string
	 */
	var $reg_username = '';
	/**
	 * Name of <b>current</b> user if anonymous (effectively either IP address or host name).
	 *
	 * @access	public
	 * @var		string
	 */
	var $anon_username = '';
	/**
	 * Cache for usernames that are known to be registered.
	 *
	 * @access	public
	 * @var		array()
	 */
	var $registered_users = array();
	/**
	 * Cache for usernames/IP addresses/hostnames that are known to be <b>not</b> registered.
	 *
	 * @access	public
	 * @var		array()
	 */
	var $anon_users = array();
	/**#@-*/

	/**#@+*
	 * URL or URL component, derived just once in {@link Wakka::Run()} for later usage.
	 */
	/**
	 * Complete Wikka URL ready to append a page name to.
	 * Derived from {@link WIKKA_BASE_URL} and (if rewrite mode is NOT on)
	 * {@link WIKKA_URL_EXTENSION} concatenated.
	 *
	 * @var string
	 */
	var $wikka_url = '';
	/**#@-*/

	/**
	 * Constructor.
	 *
	 * @uses	Wakka::GetConfigValue()
	 */
	function Wakka($config)
	{
		$this->config = $config;

		$this->dblink = @mysql_connect($this->GetConfigValue('mysql_host'), $this->GetConfigValue('mysql_user'), $this->GetConfigValue('mysql_password'));
		if ($this->dblink)
		{
			if (!@mysql_select_db($this->GetConfigValue('mysql_database'), $this->dblink))
			{
				@mysql_close($this->dblink);
				$this->dblink = FALSE;
			}
		}
		$this->VERSION = WAKKA_VERSION;
	}

	/**
	 * DATABASE methods
	 */

	/**
	 * Send a query to the database.
	 *
	 * If the query fails, the function will simply die(). If SQL-
	 * Debugging is enabled, the query and the time it took to execute
	 * are added to the Query-Log.
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @uses	getmicrotime()
	 *
	 * @param	string	$query	mandatory: the query to be executed.
	 * @return	array	the result of the query.
	 * @todo	move into a database class.
	 */
	function Query($query)
	{
		if ($this->GetConfigValue('sql_debugging'))	// @@@
		{
			#$start = $this->GetMicroTime();
			$start = getmicrotime(TRUE);
		}
		if (!$result = mysql_query($query, $this->dblink))
		{
			ob_end_clean();
			die(QUERY_FAILED.' <pre>'.$query.'</pre> ('.mysql_errno($this->dblink).' '.mysql_error($this->dblink).')'); #376
		}
		if ($this->GetConfigValue('sql_debugging'))	// @@@
		{
			#$time = $this->GetMicroTime() - $start;
			$time = getmicrotime(TRUE) - $start;
			$this->queryLog[] = array(
				'query'		=> $query,
				'time'		=> $time
				);
		}
		return $result;
	}
	/**
	 * Return the first row of a query executed on the database.
	 *
	 * @uses	Wakka::LoadAll()
	 *
	 * @param	string	$query	mandatory: the query to be executed
	 * @return	mixed	an array with the first result row of the query, or FALSE if nothing was returned.
	 * @todo	move into a database class.
	 */
	function LoadSingle($query)
	{
		/*
		if ($data = $this->LoadAll($query))
		{
			return $data[0];
		}
		return FALSE;
		*/
		$data = $this->LoadAll($query);
		$result = (count($data) == 0) ? FALSE : $data[0];
		return $result;
	}
	/**
	 * Return all results of a query executed on the database.
	 *
	 * @uses	Wakka::Query()
	 *
	 * @param	string $query mandatory: the query to be executed
	 * @return	array the result of the query.
	 * @todo	move into a database class.
	 */
	function LoadAll($query)
	{
		$data = array();
		if ($r = $this->Query($query))
		{
			while ($row = mysql_fetch_assoc($r))
			{
				$data[] = $row;
			}
			mysql_free_result($r);
		}
		return $data;
	}
	/**
	 * Generic 'count' query.
	 *
	 * Get a count of the number of records in a given table that would be matched
	 * by the given (optional) WHERE criteria. Only a single table can be queried.
	 *
	 * @author		{@link http://wikkawiki.org/JavaWoman JavaWoman}
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 * @version		1.1
	 *
	 * @access	public
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::Query()
	 *
	 * @param	string	$table	required: (logical) table name to query;
	 *							prefix will be automatically added
	 * @param	string	$where	optional: criteria to be specified for a WHERE clause;
	 *							do not include WHERE
	 * @return	integer	number of matches returned by MySQL
	 * @todo	move into a database class.
	 */
	function getCount($table,$where='')							# JW 2005-07-16
	{
		// build query
		$where = ('' != $where) ? ' WHERE '.$where : '';
		$query = "
			SELECT COUNT(*)
			FROM ".$this->GetConfigValue('table_prefix').$table.
			$where;

		// get and return the count as an integer
		$count = (int)mysql_result($this->Query($query),0);
		return $count;
	}
	/**
	 * Check if the MySQL-Version is higher or equal to a given (minimum) one.
	 *
	 * !param	integer $major mandatory: INTERFACE CHANGE
	 * !param	integer $minor mandatory: INTERFACE CHANGE
	 * !param	integer $subminor mandatory: INTERFACE CHANGE
	 * @param	string	$min_version	optional; default: 3.23, the minimum for Wikka to run
	 * @return	boolean TRUE if version is sufficient (higher or equal specified version); FALSE if not or n/a
	 * @todo	move into a database class
	 * DONE		use Compatibility function getMysqlVersion to get version,
	 *			then use PHP version_compare() for comparison - just like wikka.php does!
	 */
	#function CheckMySQLVersion($major, $minor, $subminor)
	function CheckMySQLVersion($min_version='3.23')
	{
		// init
		$sufficient = FALSE;
		// get version
		/*
		$result = @mysql_query("SELECT VERSION() AS version");
		if ($result !== FALSE && @mysql_num_rows($result) > 0)
		{
			$row   = mysql_fetch_array($result);
			$match = explode('.', $row['version']);
		}
		else
		{
			$result = @mysql_query("SHOW VARIABLES LIKE 'version'");
			if ($version !== FALSE && @mysql_num_rows($result) > 0)
			{
				$row   = mysql_fetch_row($result);
				$match = explode('.', $row[1]);
			}
		}

		if (FALSE !== $result)
		{
			$mysql_major = $match[0];
			$mysql_minor = $match[1];
			$mysql_subminor = $match[2][0].$match[2][1];

			if ($mysql_major > $major)
			{
				$sufficient = TRUE;
			}
			elseif (($mysql_major == $major) && ($mysql_minor >= $minor) && ($mysql_subminor >= $subminor))
			{
				$sufficient = TRUE;
			}
		}
		*/

		$errors = array();
		$mysql_version = getMysqlVersion($errors);

		// report any error encountered retrieving version (like in Query())
		if (($n = count($errors)) > 0)
		{
			for ($i=0; $i <= $n; $i++)
			{
				printf(WIKKA_ERROR_MYSQL_ERROR, $errors['no'][$i], $errors['txt'][$i]);
				echo "<br/>\n";
			}
			$mysql_version_retrieval_error = ERROR_RETRIEVAL_MYSQL_VERSION;
			ob_end_clean();
			die($mysql_version_retrieval_error);
		}

		// check version we retrieved against criteria
		if ($mysql_version !== FALSE &&
			version_compare($mysql_version, $min_version,'>=')	// >= MySQL minimum version??
		   )
		{
			$sufficient = TRUE;
		}

		return $sufficient;
	}
	/**
	 * Updates modified table fields in bulk.
	 *
	 * WARNING:	Do not add, delete, or reorder records or fields in
	 *			queries prior to calling this function!!
	 *			JW: why not? please explain...
	 *
	 * @uses	Wakka::Query()
	 *
	 * @param	string		$tablename	mandatory: Table to modify
	 * @param	string		$keyfield	mandatory: Field name of primary key
	 * @param	resource	$old_res	mandatory: Old (original) resource
	 *			as generated by mysql_query
	 * @param	resource	$new_res	mandatory: New (modified) resource
	 *			originally created as a copy of $old_res
	 * @todo	Does not currently handle deletions or insertions of
	 *			records or fields.
	 * @todo	use function to build value list BUT we need to know actual column type
	 * @todo	move into a database class
	 */
	function Update($tablename, $keyfield, $old_res, $new_res)
	{
		// init
		$count_old = count($old_res);
		// security checks!
		if ($count_old != count($new_res))
		{
			return;
		}
		if (!$tablename || !$keyfield)
		{
			return;
		}

		// Reference:
		// http://php.net/mysql-query - annotation by babba@nurfuerspam.de
		for ($i=0; $i<$count_old; $i++)
		{
			// security check
			if ($old_res[0][$keyfield] != $new_res[0][$keyfield])
			{
				return;
			}
			// @@@ make into function!	buildSQLValueList()
			$changedvals = '';
			foreach ($old_res[$i] as $key=>$oldval)
			{
				$newval = $new_res[$i][$key];
				if ($oldval != $newval)
				{
					if ($changedvals != '')
					{
						$changedvals .= ",\n";	// using newline rather than space for more readable query
					}
					$changedvals .= '`'.$key.'`=';
					if (!is_numeric($newval))	// not strictly safe; we need to know if column type is string or number
					{
						$changedvals .= '"'.mysql_real_escape_string($newval).'"';
					}
					else
					{
						$changedvals .= $newval;
					}
				}
			}
			if ($changedvals == '')
			{
				return;
			}
			$this->Query("
				UPDATE ".$tablename."
				SET ".$changedvals."
				WHERE ".$keyfield." = ".$old_res[$i][$keyfield]
				);
		}
	}

	/**
	 * MISCELLANEOUS methods
	 */

	/**
	 * Generate a timestamp - OBSOLETE.
	 *
	 * DISABLED: replaced by getmicrotime() in Compatibility library!
	 * Left in place but returning just zero for now.
	 */
	function GetMicroTime()
	{
		return 0;
	}
	/**
	 * Buffer the output from an included file.
	 *
	 * @param	string	$filename	mandatory: name of the file to be included;
	 *					note this may already contain a (partial) path!
	 *					(see {@link http://wush.net/trac/wikka/ticket/446 #446})
	 * @param	string	$path		mandatory: path to the file
	 * @param	string	$not_found_text	mandatory: text to be returned if the file was not found;
	 *					if the intention is to let this fail silently, just pass an empty string here
	 * @param	boolean	$makepage	optional: create a "page" div for error; default FALSE
	 * @param	string	$vars	optional: vars to be passed to the file to handle. default: ''
	 * @return	string	the included file's output or the $not_found_text if the file could not be found
	 */
	function IncludeBuffered($filename, $path, $not_found_text, $makepage=FALSE, $vars='')
	{
#echo 'IncludeBuffered - filename specified: '.$filename."<br/>\n";
#echo 'IncludeBuffered - path specified: '.$path."<br/>\n";
		$output = '';
		$not_found_text = trim($not_found_text);
		// build full (relative) path to requested plugin (method/action/formatter/...)
		$fullfilepath = trim($path).DIRECTORY_SEPARATOR.$filename;	#89
#echo 'IncludeBuffered - fullfilepath derived: '.$fullfilepath."<br/>\n";
		// check if requested file (method/action/formatter/...) actually exists
		if (file_exists($fullfilepath))
		{
			if (is_array($vars))
			{
				// make the parameters also available by name (apart from the array itself):
				// some callers (still) rely on these separate values, so we extract them, too
				// taking care not to overwrite any already-existing variable
				// NOTE: this usage is DEPRECATED
				extract($vars, EXTR_SKIP);	// [SEC] EXTR_SKIP avoids collision with existing filenames
			}
			ob_start();
			include $fullfilepath;			// this is where it all happens!
			$output = ob_get_contents();
			ob_end_clean();
		}
		else
		{
			// @@@ wrap in <em class="error"> here, not in callers
			$output = ($makepage) ? '<div class="page"><em class="error">'.$not_found_text.'</em></div>' : '<em class="error">'.$not_found_text.'</em>';
		}
		return $output;
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
	 * @author		{@link http://wikkawiki.org/JavaWoman JavaWoman}
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
		static $aSeq = array();										# group sequences
		static $aIds = array();										# used ids

		// preparation for group
		if (!preg_match('/^[A-Z-a-z]/',$group))						# make sure group starts with a letter
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
			$id = preg_replace('/\s+/','_',trim($id));				# replace any whitespace sequence in $id with a single underscore
		}

		// validation (full for 'embed', characters only for other groups since we'll add a prefix)
		if ('embed' == $group)
		{
			$validId = preg_match('/^[A-Za-z][A-Za-z0-9_:.-]*$/',$id);	# ref: http://www.w3.org/TR/html4/types.html#type-id
		}
		else
		{
			$validId = preg_match('/^[A-Za-z0-9_:.-]*$/',$id);
		}

		// build or generate id
		if ('' == $id || !$validId || in_array($id,$aIds))			# ignore specified id if it is invalid or exists already
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
	 * Strip potentially dangerous tags from embedded HTML.
	 *
	 * @param	string $html mandatory: HTML to be secured
	 * @return	string sanitized HTML
	 */
	function ReturnSafeHTML($html)
	{
		#require_once('3rdparty'.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'safehtml'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'safehtml.php');
		$safehtml_classpath = $this->GetConfigValue('safehtml_path').DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'safehtml.php';
		require_once $safehtml_classpath;

		// Instantiate the handler
		#$safehtml =& new safehtml();
		$safehtml = instantiate('safehtml');

		$filtered_output = $safehtml->parse($html);

		return $filtered_output;
	}

	/**
	 * ARRAYS: processing arrays into formatted output
	 */

	/**
	 * Takes an array of pages returned by LoadAll() and renders it as a table or unordered list.
	 *
	 * @author	{@link http://wikkawiki.org/DotMG DotMG}
	 *
	 * @access	public
	 * @uses	Wakka::Format()
	 *
	 * @param	mixed	$pages			mandatory: Array of pages returned by LoadAll
	 * @param	string	$nopagesText	optional: Error message returned if $pages is void. Default: ''
	 * @param	string	$class			optional: A classname to be attached to the table or unordered list. Default: ''
	 * @param	int		$columns		optional: Number of columns of the table if compact = 0. Default: 3
	 * @param	int		$compact		optional: If 0: use table, if 1: use unordered list. Default: 0
	 * @param	boolean	$show_edit_link	If TRUE, each page is followed by an edit link. Default: FALSE.
	 * @return	string	HTML: formatted array contents
	 * @todo	Use as a wrapper for the new array functions - avoiding table layout and enhancing scannability of the result!!!
	 */
	function ListPages($pages, $nopagesText = '', $class = '', $columns = 3, $compact = 0, $show_edit_link=FALSE)	// @@@ use boolean for $compact
	{
		$edit_link = '';
		if (!$pages)
		{
			return ($nopagesText);
		}
		if ($class)
		{
			$class = ' class="'.$class.'"';
		}
		$str = $compact ? '<div'.$class.'><ul>' : '<table width="100%"'.$class.'><tr>'."\n";
		foreach ($pages as $page)
		{
			#$list[] = $page['tag'];
			$list[] = $page['page_tag'];	#487 - was not handled in [520]!
		}
		sort($list);			// @@@ caller should ensure (via query!) the list is already sorted
								// this could break an already-sorted list!
		$count = 0;
		foreach ($list as $val)
		{
			if ($show_edit_link)
			{
				$edit_link = ' <small>['.$this->Link($val, 'edit', WIKKA_PAGE_EDIT_LINK_DESC, false, true, sprintf(WIKKA_PAGE_EDIT_LINK_TITLE, $val)).']</small>';
			}
			if ($compact)
			{
				#$link = '[['.$val;
				if (eregi('^Category', $val))
				{
					$val .= ' '.eregi_replace('^Category', '', $val);
				}
				// @@@ Format() should not be used to format a link
				$str .= '<li>'."\n".$this->Format('[['.$val.']]').$edit_link.'</li>';
			}
			else
			{
				if ($count == $columns)
				{
					$str .= '</tr><tr>'."\n";
					$count = 0;
				}
				// @@@ Format() should not be used to format a link
				$str .= '<td>'.$this->Format('[['.$val.']]').$edit_link.'</td>';
			}
			$count ++;
		}
		$str .= $compact ? '</ul></div>' : '</tr></table>';
		return $str;
	}

	/**
	 * SECURITY-related methods
	 */

	/**
	 * Make sure a (user-provided) URL does use &amp; instead of & and is protected from attacks.
	 *
	 * Any already-present '&amp;' is first turned into '&'; then hsc_secure()
	 * is applied so all ampersands are "escaped" while characters that could be
	 * used to create a script attack (< > or ") are "neutralized" by escaping
	 * them.
	 *
	 * This method should be applied on any user-provided url in actions,
	 * handlers etc.
	 *
	 * Note: hsc_secure() is the secure replacement for PHP's htmlspecialchars().
	 * See #427.
	 *
	 * @author		{@link http://wikkawiki.org/JavaWoman JavaWoman}
	 * @copyright	Copyright © 2004, Marjolein Katsma
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 * @version		1.0
	 *
	 * @access		public
	 * @uses		Wakka::hsc_secure()
	 *
	 * @param		string	$url  required: URL to sanitize
	 * @return		string	sanitzied URL
	 */
	function cleanUrl($url)
	{
		$url = $this->hsc_secure(preg_replace('/&amp;/','&',$url));
		return $url;
	}

	/**
	 * Wrapper around hsc_secure() which preserves entity references.
	 *
	 * The first two parameters for this function as the same as those for
	 * htmlspecialchars() in PHP: the text to be treated, and an optional
	 * parameter determining how to handle quotes; both these parameters are
	 * passed on to our hsc_secure() replacement for htmlspecialchars().
	 *
	 * Since hsc_secure() does not need a character set parameter, we don't
	 * have that here any more either.
	 *
	 * A third 'doctype' parameter is for local use only and determines how
	 * pre-existing entity references are treated after hsc_secure() has done
	 * its work: numeic entity references are always "unescaped' since they are
	 * valid for both HTML and XML doctypes; for XML the named entity references
	 * for the special characters are unescaped as well, while for for HTML any
	 * named entity reference is unescaped. This parameter is optional and
	 * defaults to HTML.
	 *
	 * The function first applies hsc_secure() to the input string and then
	 * "unescapes" character entity references and numeric character references
	 * (both decimal and hexadecimal).
	 * Entities are recognized also if the ending semicolon is omitted at the
	 * end or before a newline or tag but for consistency the semicolon is
	 * always added in the output where it was omitted.
	 *
	 * Usage note:
	 * Where code should be rendered <em>as code</em> hsc_secure() should be
	 * used directly so that entity references are also rendered as such instead
	 * of as their corresponding characters.
	 *
	 * Documentation note:
	 * It seems the $doctype parameter was added in 1.1.6.2; version should have
	 * been bumped up to 1.1, and the param documented. We'll assume the updated
	 * version was indeed 1.1, and put this one using hsc_secure() at 1.2 (at
	 * the same time updating the 'XML' doctype with apos as named entity).
	 *
	 * @since	Wikka 1.1.6.0
	 * @version	1.2
	 *
	 * @access	public
	 * @uses	Wakka::hsc_secure()
	 *
	 * @param	string	$text required: text to be converted
	 * @param	integer	$quote_style optional: quoting style - can be ENT_COMPAT
	 * 			(default, escape only double quotes), ENT_QUOTES (escape both
	 * 			double and single quotes) or ENT_NOQUOTES (don't escape any
	 * 			quotes)
	 * @param	string $doctype 'HTML' (default) or 'XML'; for XML only the XML
	 * 			standard entities are unescaped so we'll have valid XML content
	 * @return	string	converted string with escaped special characted but
	 * 			entity references intact
	 *
	 * @todo	(maybe) recognize valid html entities and only leave those
	 * 			alone, thus transform &error; to &amp;error;
	 * @todo	(later - maybe) support full range of situations where (in SGML)
	 * 			a terminating ; may legally be omitted (end, newline and tag are
	 * 			merely the most common ones); such usage is quite rare though
	 * 			and may not be worth the effort
	 */
	function htmlspecialchars_ent($text,$quote_style=ENT_COMPAT,$doctype='HTML')
	{
		// re-establish default if overwritten because of third parameter
		// [ENT_COMPAT] => 2
		// [ENT_QUOTES] => 3
		// [ENT_NOQUOTES] => 0
		if (!in_array($quote_style,array(ENT_COMPAT,ENT_QUOTES,ENT_NOQUOTES)))
		{
			$quote_style = ENT_COMPAT;
		}

		// define patterns
		$terminator = ';|(?=($|[\n<]|&lt;))';	// semicolon; or end-of-string, newline or tag
		$numdec = '#[0-9]+';					// numeric character reference (decimal)
		$numhex = '#x[0-9a-f]+';				// numeric character reference (hexadecimal)
		if ($doctype == 'XML')					// pure XML allows only named entities for special chars
		{
			// only valid named entities in XML (case-sensitive)
			$named = 'lt|gt|quot|apos|amp';
			$ignore_case = '';
			$entitystring = $named.'|'.$numdec.'|'.$numhex;
		}
		else									// (X)HTML
		{
			$alpha  = '[a-z]+';					// character entity reference TODO $named='eacute|egrave|ccirc|...'
			$ignore_case = 'i';					// names can consist of upper and lower case letters
			$entitystring = $alpha.'|'.$numdec.'|'.$numhex;
		}
		$escaped_entity = '&amp;('.$entitystring.')('.$terminator.')';

		// execute our replacement hsc_secure() function, passing on optional parameters
		$output = $this->hsc_secure($text,$quote_style);

		// "repair" escaped entities
		// modifiers: s = across lines, i = case-insensitive
		$output = preg_replace('/'.$escaped_entity.'/s'.$ignore_case,"&$1;",$output);

		// return output
		return $output;
	}

	/**
	 * Secure replacement for PHP built-in function htmlspecialchars().
	 *
	 * See ticket #427 (http://wush.net/trac/wikka/ticket/427) for the rationale
	 * for this replacement function.
	 *
	 * The INTERFACE for this function is almost the same as that for
	 * htmlspecialchars(), with the same default for quote style; however, there
	 * is no 'charset' parameter. The reason for this is as follows:
	 *
	 * The PHP docs say:
	 * 	"The third argument charset defines character set used in conversion."
	 *
	 * I suspect PHP's htmlspecialchars() is working at the byte-value level and
	 * thus _needs_ to know (or assume) a character set because the special
	 * characters to be replaced could exist at different code points in
	 * different character sets. (If indeed htmlspecialchars() works at
	 * byte-value level that goes some  way towards explaining why the
	 * vulnerability would exist in this function, too, and not only in
	 * htmlentities() which certainly is working at byte-value level.)
	 *
	 * This replacement function however works at character level and should
	 * therefore be "immune" to character set differences - so no charset
	 * parameter is needed or provided. If a third parameter is passed, it will
	 * be silently ignored.
	 *
	 * In the OUTPUT there is a minor difference in that we use '&#39;' instead
	 * of PHP's '&#039;' for a single quote: this provides compatibility with
	 * 	get_html_translation_table(HTML_SPECIALCHARS, ENT_QUOTES)
	 * (see comment by mikiwoz at yahoo dot co dot uk on
	 * http://php.net/htmlspecialchars); it also matches the entity definition
	 * for XML 1.0
	 * (http://www.w3.org/TR/xhtml1/dtds.html#a_dtd_Special_characters).
	 * Like PHP we use a numeric character reference instead of '&apos;' for the
	 * single quote. For the other special characters we use the named entity
	 * references, as PHP is doing.
	 *
	 * And finally:
	 * The name for this function was basically inspired by waawaamilk (GeSHi),
	 * kindly provided by BenBE (GeSHi), happily acknowledged by WikkaWiki Dev
	 * Team and finally used by JavaWoman. :)
	 *
	 * @author 		{@link http://wikkawiki.org/JavaWoman Marjolein Katsma}
	 * @since		Wikka 1.1.6.3
	 * @version		1.0
	 * @license		http://www.gnu.org/copyleft/lgpl.html
	 * 				GNU Lesser General Public License
	 * @copyright	Copyright 2007, {@link http://wikkawiki.org/CreditsPage
	 * 				Wikka Development Team}
	 *
	 * @access	public
	 *
	 * @param	string	$string	string to be converted
	 * @param	integer	$quote_style
	 * 			- ENT_COMPAT:   escapes &, <, > and double quote (default)
	 * 			- ENT_NOQUOTES: escapes only &, < and >
	 * 			- ENT_QUOTES:   escapes &, <, >, double and single quotes
	 * @return	string	converted string
	 */
	function hsc_secure($string, $quote_style=ENT_COMPAT)
	{
		// init
		$aTransSpecchar = array('&' => '&amp;',
								'"' => '&quot;',
								'<' => '&lt;',
								'>' => '&gt;'
								);			// ENT_COMPAT set
		if (ENT_NOQUOTES == $quote_style)	// don't convert double quotes
		{
			unset($aTransSpecchar['"']);
		}
		elseif (ENT_QUOTES == $quote_style)	// convert single quotes as well
		{
			$aTransSpecchar["'"] = '&#39;';	// (apos) htmlspecialchars() uses '&#039;'
		}

		// return translated string
		$result = strtr($string,$aTransSpecchar);
		return $result;
	}

	/**
	 * Get a value provided by user (by get, post or cookie) and sanitize it.
	 * The method is also helpful to disable warning when the value was absent.
	 *
	 * @since	Wikka 1.1.7.0
	 * @version	1.0
	 *
	 * @access	public
	 *
	 * @param	string	$varname required: field name on get or post or cookie name
	 * @param	string	$gpc one of get, post, request and cookie. Optional, defaults to request.
	 * @return	string	sanitized value of $_REQUEST[$varname] (or $_GET, $_POST, $_COOKIE, depending on $gpc)
	 */
	function GetSafeVar($varname, $gpc='request')
	{
		$safe_var = NULL;
		if ($gpc == 'post')
		{
			$safe_var = isset($_POST[$varname]) ? $_POST[$varname] : NULL;
		}
		elseif ($gpc == 'request')
		{
			$safe_var = isset($_REQUEST[$varname]) ? $_REQUEST[$varname] : NULL;
		}
		elseif ($gpc == 'get')
		{
			$safe_var = isset($_GET[$varname]) ? $_GET[$varname] : NULL;
		}
		elseif ($gpc == 'cookie')
		{
			$safe_var = isset($_COOKIE[$varname]) ? $_COOKIE[$varname] : NULL;
		}
		$var = $this->htmlspecialchars_ent($safe_var);
		return $var;
	}

	/**
	 * CODE presentation
	 */

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
	 * @since	wikka 1.1.6.0
	 *
	 * @access	public
	 * @uses	Wakka::GetConfigValue()
	 * @uses	GeShi
	 *
	 * @param	string	$sourcecode	required: source code to be highlighted
	 * @param	string	$language	required: language spec to select highlighter
	 * @param	integer	$start		optional: start line number; if supplied and >= 1 line numbering
	 *			will be turned on if it is enabled in the configuration.
	 * @return	string	code block with syntax highlighting classes applied
	 * @todo		support for GeSHi line number styles
	 * @todo		enable error handling
	 */
	function GeSHi_Highlight($sourcecode, $language, $start=0)
	{
		// create GeSHi object
		include_once($this->GetConfigValue('geshi_path').DIRECTORY_SEPARATOR.'geshi.php');
		$geshi =& new GeSHi($sourcecode, $language, $this->GetConfigValue('geshi_languages_path'));				# create object by reference

		$geshi->enable_classes();								# use classes for hilighting (must be first after creating object)
		$geshi->set_overall_class('code');						# enables using a single stylesheet for multiple code fragments

		// configure user-defined behavior
		$geshi->set_header_type(GESHI_HEADER_DIV);				# set default
		if (NULL !== $this->GetConfigValue('geshi_header'))				# config override
		{
			if ('pre' == $this->GetConfigValue('geshi_header'))
			{
				$geshi->set_header_type(GESHI_HEADER_PRE);
			}
		}
		$geshi->enable_line_numbers(GESHI_NO_LINE_NUMBERS);		# set default
		if ($start > 0)											# line number > 0 _enables_ numbering
		{
			if (NULL !== $this->GetConfigValue('geshi_line_numbers'))		# effect only if enabled in configuration
			{
				if ('1' == $this->GetConfigValue('geshi_line_numbers'))
				{
					$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
				}
				elseif ('2' == $this->GetConfigValue('geshi_line_numbers'))
				{
					$geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS);
				}
				if ($start > 1)
				{
					$geshi->start_line_numbers_at($start);
				}
			}
		}
		if (NULL !== $this->GetConfigValue('geshi_tab_width'))			# GeSHi override (default is 8)
		{
			$geshi->set_tab_width($this->GetConfigValue('geshi_tab_width'));
		}

		// parse and return highlighted code
		// comments added to make GeSHi-highlighted block visible in code JW/20070220
		$code = '<!--start GeSHi-->'."\n".$geshi->parse_code()."\n".'<!--end GeSHi-->'."\n";
		return $code;
	}

	/**
	 * VARIABLE-related methods ("getters" and "setters")
	 *
	 * @todo	decide if we need (all) these methods!
	 *			JW: my vote is NOT if all a getter does is return a variable directly;
	 *			but useful if there's some processing or checking involved -
	 *			in which case an accompanying "setter" method should be used
	 *			for creating/updating the variable - if only for consistency.
	 *
	 *			JW: GetConfigValue() is one such - so I created its sister
	 *			SetConfigValue as well.
	 */

	/**
	 * Get the name tag of the current page.
	 *
	 * @uses	Wakka::$tag
	 *
	 * @return	string the name of the page
	 */
	function GetPageTag()
	{
		return $this->tag;
	}
	/**
	 * Get the time the current verion of the current page was saved.
	 *
	 * @uses	Wakka::$page
	 *
	 * @return	string
	 */
	function GetPageTime()
	{
		return $this->page['time'];
	}
	/**
	 * Determine if the current version of the page is the latest.
	 *
	 * @return boolean TRUE if it is the latest, FALSE otherwise.
	 * @todo	Remove this method? Never called, and the variable does not seem to be set anywhere...
	 */
	function IsLatestPage()
	{
		return $this->latest;
	}
	/**
	 * Check whether the page is already assigned a title to set in the <title> tag.
	 *
	 * @access	public
	 * @uses	Wakka::$page_title
	 *
	 * @return	boolean
	 */
	function HasPageTitle()
	{
		return ('' != $this->page_title);
	}
	/**
	 * Store page data.
	 *
	 * @uses	Wakka::$page
	 * @uses	Wakka::$tag
	 */
	function SetPage($page)
	{
		global $debug;
if ($debug) echo 'SetPage: ';
		$this->page = $page;
		if ($this->page['tag'])
		{
			$this->tag = $this->page['tag'];
		}
if ($debug) echo 'tag is '.$this->tag."<br/>\n";
	}
	/**
	 * Store the title of a page (as derived by the formatter).
	 *
	 * Actually, the title of the page is chosen from the text inside headings
	 * h1 through h4, that is encountered first.
	 * (But that process isn't happening in this function! see wakka3callback().)
	 *
	 * @access	public
	 * @uses	Wakka::$page_title
	 *
	 * @param	string	$page_title	the new title of the page.
	 * @return	void
	 * @todo	probably better to use the already-existing Wakka::$page array to store this?
	 */
	function SetPageTitle($page_title)
	{
		if (trim($page_title))
		{
			$this->page_title = $page_title;
		}
	}
	/**
	 * Get the handler used on the page.
	 *
	 * @return string name of the handler.
	 */
	function GetHandler()
	{
		return $this->handler;
	}
	/**
	 * Get the value of a given item from the wikka config.
	 *
	 * @uses	Wakka::config
	 *
	 * @param	$name	mandatory: name of a key in the config array
	 * @return	mixed	the value of the configuration item, or NULL if not found
	 */
	function GetConfigValue($name)
	{
		$val = (isset($this->config[$name])) ? $this->config[$name] : NULL;
		return $val;
	}
	/**
	 * Set the value of a given item from the wikka config.
	 *
	 * @uses	Wakka::config
	 *
	 * @param	$name mandatory: name of a key in the config array
	 * @param	$value mandatory: the value to set the item at
	 * 	 */
	function SetConfigValue($name,$value)
	{
		$this->config[$name] = $value;
	}
	/**
	 * Get the name of the Wiki.
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @return	string the name of the Wiki.
	 */
	function GetWakkaName()
	{
		return $this->GetConfigValue('wakka_name');
	}
	/**
	 * Get the wikka version.
	 *
	 * @return	string the wikka version
	 */
	function GetWakkaVersion()
	{
		return $this->VERSION;
	}

	/**
	 * PAGE-related methods
	 */

	/**
	 * LoadPage loads the page whose name is $tag.
	 *
	 * If parameter $time is provided, LoadPage returns the page as it was at that exact time.
	 * If parameter $time is not provided, it returns the page as its latest state.
	 * LoadPage and LoadPageById remember the page tag or page id they've queried by caching them,
	 * so, these methods try first to retrieve data from cache if available.
	 *
	 * @access	public
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka:LoadSingle()
	 * @uses	Wakka:CachePage()
	 * @uses	Wakka:CacheNonExistentPage()
	 * @uses	Wakka:GetCachedPage()
	 *
	 * @param	string	$tag	mandatory: name of the page to load
	 * @param	string	$time	optional: timestamp if a specific revision should be loaded
	 * @param	boolean	$cache	optional: if TRUE and the latest version was requested,
	 *					an attempt to retrieve from cache will be made first.
	 *					default: TRUE
	 * @return	mixed	array with page structure, or FALSE if not retrieved
	 */
	function LoadPage($tag, $time='', $cache=TRUE)
	{
		global $debug;
if ($debug) echo 'LoadPage '.$tag;
		$page = FALSE;
		// retrieve from cache
		if (!$time && (bool) $cache)
		{
if ($debug) echo ' looking in cache...';
			$page = $this->GetCachedPage($tag);
			if ($page == 'cached_nonexistent_page')
			{
				$page = FALSE;
			}
		}
if ($debug) if (is_array($page)) echo ' in cache';
if ($debug) echo gettype($page);
		// load page if not yet retrieved
		if (FALSE === $page)		// Nothing from cache? then get from DB
		{
if ($debug) echo ' not found in cache...';
			$query ="
				SELECT *
				FROM ".$this->GetConfigValue('table_prefix')."pages
				WHERE tag = '".mysql_real_escape_string($tag)."' ".
					($time ? "AND time = '".mysql_real_escape_string($time)."'" : "AND latest = 'Y'")."
				LIMIT 1";
if ($debug) echo 'query: <pre>'.$query.'</pre>';
					$page = $this->LoadSingle($query);
		}
if ($debug) if (is_array($page)) echo ' in database';

// cache result
		if (is_array($page) && !$time)	// existing, current page only
		{
if ($debug) echo ' found';
			$this->CachePage($page);
		}
		elseif (FALSE === $page)
		{
if ($debug) echo ' not found';
			$this->CacheNonExistentPage($tag);
		}
if ($debug) echo "<br/>\n";
		return $page;
	}
	/**
	 * GetCachedPageById gets a page from cache whose id is $id.
	 *
	 * @access	public
	 * @param	mixed	$id	the id of the page to retrieve from cache
	 * @return	mixed	an array as returned by LoadPage(), or NULL if absent from cache.
	 * @todo	should probably return FALSE instead of NULL in case of failure, for consistency with LoadPage()
	 */
	function GetCachedPageById($id)
	{
		return $this->GetCachedPage('/#'.$id);
	}
	/**
	 * GetCachedPage gets a page from cache whose name is $tag.
	 *
	 * @access	public
	 * @see		Wakka::CachePage()
	 * @uses	Wakka::$pageCache
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Config::$pagename_case_sensitive
	 *
	 * @param	mixed	$tag	the name of the page to retrieve from cache.
	 * @return	mixed	an array as returned by LoadPage(), or FALSE if absent from cache.
	 */
	function GetCachedPage($tag)
	{
		if (!$this->GetConfigValue('pagename_case_sensitive'))
		{
			$tag = strtolower($tag);
		}
		$page = (isset($this->pageCache[$tag])) ? $this->pageCache[$tag] : FALSE;
		if ((is_string($page)) && ($page[0] == '/'))
		{
			$page = $this->pageCache[substr($page, 1)];
		}
		return $page;
	}
	/**
	 * CachePage caches a page to prevent reusing MySQL operations when reloading it.
	 *
	 * <p>Cached pages are stored in the array {@link Wakka::pageCache}.</p>
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
	 * @access	public
	 * @uses	Wakka::$pageCache
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Config::$pagename_case_sensitive
	 *
	 * @param	mixed	$page
	 * @return	void
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
	 * CacheNonExistentPage marks a page name in cache as a non-existent page.
	 *
	 * @access	public
	 * @uses	Wakka::$pageCache
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Config::$pagename_case_sensitive
	 *
	 * @param	string	$tag the name of the page.
	 * @return	void
	 */
	function CacheNonExistentPage($tag)
	{
		if (!$this->GetConfigValue('pagename_case_sensitive'))
		{
			$tag = strtolower($tag);
		}
		$this->pageCache[$tag] = 'cached_nonexistent_page';
	}
	/**
	 * LoadPageById loads a page whose id is $id.
	 *
	 * If the parameter $cache is true, it first tries to retrieve it from cache.
	 * If the page id was not retrieved from cache, then use sql and cache the page.
	 *
	 * @access	public
	 * @uses	Wakka::GetCachedPageById()
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::LoadSingle()
	 * @uses	Wakka::CachePage()
	 * @uses	Wakka::CacheNonExistentPage()
	 *
	 * @param	int		$id		mandatory: Id of the page to load.
	 * @param	boolean	$cache	optional: if TRUE, an attempt to retrieve from
	 *					cache will be made first.
	 *					default: TRUE
	 * @return	mixed	array with page structure identified by $id, or FALSE if no page could not be retrieved
	 */
	function LoadPageById($id, $cache=TRUE)
	{
		// It first tries to retrieve from cache.
		if ((bool) $cache)
		{
			$page = $this->GetCachedPageById($id);
			if ((is_string($page)) && ($page == 'cached_nonexistent_page'))
			{
				return FALSE;
			}
			if (is_array($page))
			{
				return $page;
			}
		}
		// If the page id was not retrieved from cache, then use sql and cache the page.
		$page = $this->LoadSingle("
			SELECT *
			FROM ".$this->GetConfigValue('table_prefix')."pages
			WHERE id = '".mysql_real_escape_string($id)."'
			LIMIT 1"
			);
		if (is_array($page))
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
	 * The default value for $max is the 'revisioncount' user's preference if user is logged in,
	 * or the (new) config value {@link Config::$default_revisioncount default_revisioncount}, if
	 * such config entry exists, or falls to a hard-coded value of 20.</p>
	 * <p>A revision structure consists of an edit note
	 * ('<b>note</b>' key), the '<b>id</b>' of the revision which permits to retrieve later the
	 * full edit data (especially the body field), the date of revision (`<b>time</b> key) and the
	 * '<b>user</b>' who did the modification. </p>
	 * <p>Since 1.1.7, we replaced 'SELECT *' in the sql instruction by
	 * 'SELECT note, id, time, user' because only these fields are really needed. (Trac:#75)</p>
	 * <p>If param $start is supplied, LoadRevisions ignores the $start most recent revisions; this
	 * will allow browsing full history step by step if the pagesize or the number of total revision
	 * are getting too big.</p>
	 *
	 * @access	public
	 * @uses	Wakka::GetUser()
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::LoadAll()
	 * @uses	Config::$default_revisioncount
	 * @uses	Config::$pagename_case_sensitive
	 *
	 * @param	string	$tag Name of the page to view revisions of
	 * @param	int	$start
	 * @param	int	$max	Maximum number of revisions to load; zero or a negative number signifies "no limit"
	 * @return	array	This value contains fields note, id, time and user.
	 * @todo	avoid "magic numbers"!
	 * @todo	do we really need a limit for "no limit"? why is the imposed limit
	 *			different for registered and anonymous users?
	 * @todo	review usage of cache - see NOTE & todo in {@link Wakka::LoadOldestRevision}!.
	 */
	function LoadRevisions($tag, $start=0, $max=0)
	{
		$max = (int) $max;
		if ($max <= 0)
		{
			if ($user = $this->GetUser())				// get registered user
			{
				$limitmax = (int) $user['revisioncount'];	// @@@ this typecast should not be necessary - make sure it always contains an integer!
				if ($limitmax <= 0)
				{
					// 0 or a negative value means no max, so choose a huge number.
					// @@@ why set a limit for 'no limit'? just don't use the LIMIT clause in the query
					$limitmax = 1000;	// limit for registered users @@@ should be a defined constant
				}
			}
			else										// anonymous user
			{
				$limitmax = (int) $this->GetConfigValue('default_revisioncount');
				if ($limitmax <= 0)
				{
					$limitmax = 20;		// limit for anonymous users @@@ should be a defined constant
				}
			}
		}
		else
		{
			$limitmax = $max;
		}
		$limitstart = '';					// default for query
		if (($start = (int) $start) > 0)	// do not accept negative values!
		{
			$limitstart = $start.', ';
		}
		$revisions = $this->LoadAll("
			SELECT note, id, time, user
			FROM ".$this->GetConfigValue('table_prefix')."pages
			WHERE tag = '".mysql_real_escape_string($tag)."'
			ORDER BY time DESC
			LIMIT ".$limitstart.$limitmax
			);
		// store oldest *requested* revision in the "special cache"
		// @@@ what is the (count($revisions) < $limitmax) doing here? (this will FAIL if exactly $limitmax results are returned, and it cannot be more!)
		if (is_array($revisions) && (count($revisions) < $limitmax) && (count($revisions) > 0)) #38
		{
			if (!$this->GetConfigValue('pagename_case_sensitive'))
			{
				$tag_lowercase = strtolower($tag);
			}
			// @@@ $tag_lowercase won't have a value if pagename_case_sensitive is TRUE!
			$this->specialCache['oldest_revision'][$tag_lowercase] = $revisions[count($revisions) - 1];
		}
		return $revisions;
	}
	/**
	 * LoadOldestRevision: Load the oldest known revision of a page.
	 *
	 * Attempts to retrieve from "special cache" before looking in the database.
	 * NOTE: LoadRevisions stores the oldest <b>requested</b> revison in the
	 * cache, so if this method finds something in the cache, it's not necessarily
	 * the oldest <b>known</b> revision.
	 *
	 * @access	public
	 * @uses	Config::$pagename_case_sensitive
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::$specialCache
	 * @uses	Wakka::LoadSingle
	 *
	 * @param	string	$tag	The name of the page to load oldest revision of.
	 * @return	array
	 * @todo	review usage of cache - see NOTES above. Also note that revisions
	 *			are (intentionally or not) stored only if config flag
	 *			'pagename_case_sensitive' is FALSE
	 */
	function LoadOldestRevision($tag)
	{
		if (!$this->GetConfigValue('pagename_case_sensitive'))
		{
			$tag_lowercase = strtolower($tag);
		}
		// @@@ $tag_lowercase won't have a value if pagename_case_sensitive is TRUE!
		if (isset($this->specialCache['oldest_revision'][$tag_lowercase]))	// @@@ use getter
		{
			$oldest_revision = $this->specialCache['oldest_revision'][$tag_lowercase];
		}
		else
		{
			$oldest_revision = $this->LoadSingle("
				SELECT note, id, time, user
				FROM ".$this->GetConfigValue('table_prefix')."pages
				WHERE tag = '".mysql_real_escape_string($tag)."'
				ORDER BY time
				LIMIT 1"
				);
			if (is_array($oldest_revision))
			{
				$this->specialCache['oldest_revision'][$tag_lowercase] = $oldest_revision;	// @@@ use setter
			}
		}
		return $oldest_revision;
	}
	/**
	 * Load pages linking to a given page.
	 *
	 * @param	string	$tag	mandatory: name of page to find referring links to
	 * @return	array	one record with a page name for each page found (empty array if none found).
	 */
	function LoadPagesLinkingTo($tag)	// #410
	{
		$pages = $this->LoadAll("
			SELECT from_tag AS page_tag
			FROM ".$this->GetConfigValue('table_prefix')."links
			WHERE to_tag = '".mysql_real_escape_string($tag)."'
			ORDER BY page_tag"
			);
		return $pages;
	}
	/**
	 * Load the last x edited pages on the wiki.
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::LoadAll()
	 *
	 * @param	integer	$limit optional: number of edited pages to show. default: 50
	 * @return	array	the last x pages that were changed (empty array if none found)
	 * @todo	use constant for default limit value (no "magic numbers!")
	 * @todo	do we need the whole page for each, or only specific fields?
	 */
	#function LoadRecentlyChanged()
	function LoadRecentlyChanged($limit=50)	// @@@
	{
		$limit = (int) $limit;
		if ($limit < 1)
		{
			$limit = 50;		// @@@
		}
		$pages = $this->LoadAll("
			SELECT *
			FROM ".$this->GetConfigValue('table_prefix')."pages
			WHERE latest = 'Y'
			ORDER BY time DESC
			LIMIT ".$limit
			);
		if ($pages)
		{
			foreach ($pages as $page)
			{
				$this->CachePage($page);
			}
		}
		return $pages;
	}
	/**
	 * Load pages that need to be created.
	 *
	 * This is an expanded version of {@link Wakka::LoadWantedPages()} allowing sorting by
	 * number of pages referring to each wanted page, or by latest modified date of any page referring
	 * to wanted pages, or alphabetically.
	 * WARNING: The parameter $sort passed to this method is considered sanitized.
	 *
	 * @access	public
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::LoadAll()
	 *
	 * @param	string	$sort	Sorting needed: Legal SQL expression after ORDER BY clause. Field names are count, time and tag.
	 * @return	array
	 * @todo	it would be useful to set a LIMIT ($max) here as well
	 */
	function LoadWantedPages2($sort='')
	{
#echo 'sort: '.$sort."<br/>\n";
		if (empty($sort))
		{
			$sort = 'count DESC, time DESC, page_tag';
		}
		$pre = $this->GetConfigValue('table_prefix');
		$pages = $this->LoadAll("
			SELECT DISTINCT
				_links.to_tag AS page_tag,
				COUNT(_links.from_tag) AS count,
				MAX(CONCAT_WS('/', _pages2.time, _pages2.tag)) AS time
			FROM ".$pre."links _links
			LEFT JOIN ".$pre."pages _pages
				ON _links.to_tag = _pages.tag
			INNER JOIN ".$pre."pages _pages2
				ON _links.from_tag = _pages2.tag
			WHERE _pages.tag IS NULL
				AND _pages2.latest = 'Y'
			GROUP BY page_tag
			ORDER BY ".$sort
			);
		return $pages;
	}
	/**
	 * Load pages that need to be created.
	 *
	 * @access	public
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::LoadAll()
	 *
	 * @param	string $sort Sorting needed: Legal SQL expression after ORDER BY clause. Field names are count, time and tag.
	 * @return	array
	 * @todo	it would be useful to set a LIMIT ($max) here as well
	 */
	function LoadWantedPages()		// #410
	{
		$pre = $this->GetConfigValue('table_prefix');
		$pages = $this->LoadAll("
			SELECT DISTINCT ".
				$pre."links.to_tag AS page_tag,
				COUNT(".$pre."links.from_tag) AS count
			FROM ".$pre."links
			LEFT JOIN ".$pre."pages
				ON ".$pre."links.to_tag = ".$pre."pages.tag
			WHERE ".$pre."pages.tag IS NULL
			GROUP BY page_tag
			ORDER BY count DESC"
			);
		return $pages;
	}
	function IsWantedPage($tag)		// #410 - but function not used in 1.1.6.3 -OR- trunk?
	{
		$wanted = FALSE;
		if ($pages = $this->LoadWantedPages())
		{
			foreach ($pages as $page)
			{
				if ($page['page_tag'] == $tag)
				{
					$wanted = TRUE;
					break;
				}
			}
		}
		return $wanted;
	}
	function LoadOrphanedPages()
	{
		$pre = $this->GetConfigValue('table_prefix');
		$pages = $this->LoadAll("
			SELECT DISTINCT tag
			FROM ".$pre."pages
			LEFT JOIN ".$pre."links
				ON ".$pre."pages.tag = ".$pre."links.to_tag
			WHERE ".$pre."links.to_tag IS NULL
			ORDER BY tag"
			);
		return $pages;
	}
	function LoadPageTitles()		// @@@ name no longer matches function
	{
		$tags = $this->LoadAll("
			SELECT DISTINCT tag, owner
			FROM ".$this->GetConfigValue('table_prefix')."pages
			WHERE latest = 'Y'
			ORDER BY tag"
			);
		return $tags;
	}
	/**
	 * Get names of pages (tags) owned by the specified user.
	 *
	 * @uses	Wakka::GetConfigValue()
	 *
	 * @param	string	$owner
	 * @return	array	one row for each page owned by $owner
	 */
	function LoadPagesByOwner($owner)
	{
		#return $this->LoadAll('SELECT tag FROM '.$this->GetConfigValue('table_prefix').'pages WHERE latest = "Y" and owner = "'.mysql_real_escape_string($owner).'"');
		$query = "
			SELECT DISTINCT tag
			FROM ".$this->GetConfigValue('table_prefix')."pages
			WHERE `latest` = 'Y'
				AND `owner` = '".mysql_real_escape_string($owner)."'
			ORDER BY `tag`";
		$pages = $this->LoadAll($query);
		return $pages;
	}
	// DEPRECATED
	function LoadAllPages()
	{
		$pages = $this->LoadAll("
			SELECT * FROM ".$this->GetConfigValue('table_prefix')."pages
			WHERE latest = 'Y'
			ORDER BY tag"
			);
		return $pages;
	}

	/**
	 * Save a page.
	 *
	 * @uses	Wakka::reg_username
	 * @uses	Wakka::GetPingParams()
	 * @uses	Wakka::existsUser()
	 * @uses	Wakka::GetUserName()
	 * @uses	Wakka::HasAccess()
	 * @uses	Wakka::LoadPage()
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::Query()
	 * @uses	Wakka::WikiPing()
	 * @uses	Wakka::ParsePageTitle()
	 *
	 * @todo	document params and return
	 */
	function SavePage($tag, $body, $note)
	{
		global $debug;
		// get name of current user
		$username = $this->GetUserName();

		if ($this->HasAccess('write', $tag))
		{
			// is page new?
			if (!$oldPage = $this->LoadPage($tag))
			{
				// current user is owner if user is logged in, otherwise, no owner.
				#if ($this->GetUser())
if ($debug) echo 'SavePage calling... ';
				if ($this->existsUser())
				{
					$owner = $this->reg_username;
				}
			}
			else
			{
				// aha! page isn't new. keep owner!
				$owner = $oldPage['owner'];
			}
			// Parse page title
			$page_title = $this->ParsePageTitle($body);

			// set all other revisions to old
			$this->Query("
				UPDATE ".$this->GetConfigValue('table_prefix')."pages
				SET latest = 'N'
				WHERE tag = '".mysql_real_escape_string($tag)."'"
				);

			// add new revision
			$this->Query("
				INSERT INTO ".$this->GetConfigValue('table_prefix')."pages
				SET	tag		= '".mysql_real_escape_string($tag)."',
					time	= now(),
					owner	= '".mysql_real_escape_string($owner)."',
					user	= '".mysql_real_escape_string($username)."',
					note	= '".mysql_real_escape_string($note)."',
					latest	= 'Y',
					title	= '".mysql_real_escape_string($page_title)."',
					body	= '".mysql_real_escape_string($body)."'"
				);

			if ($pingdata = $this->GetPingParams($this->GetConfigValue('wikiping_server'), $tag, $username, $note))
			{
				$this->WikiPing($pingdata);
			}
		}
	}

	/**
	 * SEARCH
	 */

	#function FullTextSearch($phrase) { return $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where latest = 'Y' and match(tag, body) against('".mysql_real_escape_string($phrase)."')"); }
	function FullTextSearch($phrase)
	{
		$data = '';
		#if ($this->CheckMySQLVersion(4,00,01))
		if ($this->CheckMySQLVersion('4.00.01'))
		{
			if (preg_match('/[A-Z]/', $phrase))
			{
				$phrase = '"'.$phrase.'"';
			}
			$data = $this->LoadAll("
				SELECT *
				FROM ".$this->GetConfigValue('table_prefix')."pages
				WHERE latest = 'Y'
					AND tag LIKE('%".mysql_real_escape_string($phrase)."%')
				UNION SELECT *
					FROM ".$this->GetConfigValue('table_prefix')."pages
					WHERE latest = 'Y'
						AND MATCH(tag, body) AGAINST('".mysql_real_escape_string($phrase)."' IN BOOLEAN MODE)
				ORDER BY time DESC"
				);
		}

		//#elseif ($this->CheckMySQLVersion(3,23,23))
		//elseif ($this->CheckMySQLVersion('3.23.23'))
		//{
		//	$data = $this->LoadAll("select * from "
		//	.$this->config["table_prefix"]
		//	."pages where latest = 'Y' and
		//		  match(tag, body)
		//		  against('".mysql_real_escape_string($phrase)."')
		//		  order by time DESC");
		//}

		/* if no results perform a more general search */
		if (!$data)
		{
			$data = $this->LoadAll("
				SELECT *
				FROM ".$this->GetConfigValue('table_prefix')."pages
				WHERE latest = 'Y'
					AND (tag LIKE '%".mysql_real_escape_string($phrase)."%'
						OR body LIKE '%".mysql_real_escape_string($phrase)."%')
				ORDER BY time DESC"
				);
		}

		return $data;
	}

	/**
	 * [Short description needed here].
	 *
	 * @access	public
	 *
	 * @param	string	$textvalue	the text to be cleaned
	 * @param	string	$pattern_prohibited_chars	optional: valid regular expression pattern.
	 * 			Characters that match this expression will be stripped.
	 * 			If this is set to an empty string, every character will be valid.
	 * @param	boolean	$decode_html_entities	should htmlentities be decoded?
	 * @return	string	The text after some characters stripped
	 * @todo	Better strategy:
	 *			pull out the nodeToTextOnly() bit (usable for TOC) and use this:
	 *			1) separately in wikka3callback in Formatter (so a TOC can be built!)
	 *			2) for turning heading into a <title> text (instead of this function!)
	 *			THEN: rename this to what it was intended to do: textToValidId()
	 *			(and use that in the Formatter, of course)
	 * @todo	move regexes to library #34
	 */
	function CleanTextNode($textvalue, $pattern_prohibited_chars = '/[^A-Za-z0-9_:.-\s]/', $decode_html_entities = TRUE)
	{
		// START -- nodeToTextOnly
		$textvalue = trim($textvalue);
		// First find and replace any image having an alt attribute with its (trimmed) alt text
		// Image tags missing an alt attribute are not replaced.
		$textvalue = preg_replace(PATTERN_REPLACE_IMG_WITH_ALTTEXT, '\\2', $textvalue);
		// @@@ JW/2005-05-27 now first replace linebreaks <br/> and other whitespace with single spaces!!
		// Remove all other tags, including img tags that missed an alt attribute
		$textvalue = strip_tags($textvalue);
		// @@@ this all-text result is usable for a TOC!!!
		// Use this if we have a condition set to generate a TOC
		// END -- nodeToTextOnly

		if ($decode_html_entities)
		{
			if (function_exists('html_entity_decode'))
			{
				// replace entities that can be interpreted
				// use default charset ISO-8859-1 because other chars won't be valid for an ID anyway
				$textvalue = html_entity_decode($textvalue, ENT_NOQUOTES);
			}
			// remove any remaining entities (so we don't end up with strange words and numbers in the ID text)
			$textvalue = preg_replace('/&[#]?.+?;/','',$textvalue);
		}
		// finally remove non-ID characters (except whitespace which is handled by makeId())
		if ($pattern_prohibited_chars)	// @@@ make this into a global constant instead of a parameter!
		{
			$textvalue = preg_replace($pattern_prohibited_chars, '', $textvalue);
		}
		return $textvalue;
	}
	/**
	 * Parses the body of a page for a page title
	 *
	 * Searches for first instance of header markup in page body and
	 * returns this string as the page title, or an empty string if none found.
	 *
	 * @param	string	$body	page body
	 * @return	string	the title of the current page, or empty string if none found
	 * @todo	move regexps to regexp-library		#34
	 * @todo	make consistent with how the Formatter derives a page title
	 */
	function ParsePageTitle($body)
	{
		$page_title = '';
		#if (preg_match('#(={1,6})([^=].*?)\\1#s', $body, $matches))	# note that we don't match headings that are not valid Wikka markup!
		// Wikka markup for headings uses 2 - 6 '=' characters for h5-h1 => ={2,6}
		// @@@ Also, the Formatter uses only h1-h4 to derive a title, which would correspond to ={3,6}!
		if (preg_match('#(={2,6})([^=].*?)\\1#s', $body, $matches))	# note that we don't match headings that are not valid Wikka markup!
		{
			list($h_fullmatch, $h_markup, $h_heading) = $matches;
			if (isset($h_markup))
			{
				$page_title = $h_heading;	// @@@ use nodeToTextOnly() here
			}
		}
		return $page_title;
	}
	/**
	 * Return the title of the current page or page as specified by a page tag.
	 *
	 * The page title is cleaned and trimmed. See {@link
	 *	Wakka::wakka3callback()} to find how the title is derived.
	 * If SetPageTitle() was unable to choose a title for the page,
	 *	the page name is used by default.
	 * Attempts to retrieve page title from DB if $tag is specified
	 *	and is not the current page that's loaded
	 *
	 * @uses	Wakka::$page_title
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::LoadSingle()
	 * @uses	Wakka::CleanTextNode()
	 * @uses	Wakka::GetPageTag()
	 * @uses	Wakka::HasPageTitle()
	 *
	 * @param	string	@tag	optional: page to get title for (default current page)
	 * @return	mixed	the title of the current page or NULL if none found
	 * @todo	it would be more appropriate if SetPageTitle() received only
	 *			already text-only titles, or did the conversion itself: derive once,
	 *			use many times. It should not be necessary to do any "cleaning" here!
	 */
	function PageTitle($tag=NULL)
	{
		$page_title = NULL;
		$result = '';
		if (!empty($tag) && ($tag != $this->GetPageTag()))	// $tag specified, and not current page
		{
			$query = "
				SELECT title
				FROM ".$this->GetConfigValue('table_prefix')."pages
				WHERE tag = '".mysql_real_escape_string($tag)."'
					AND latest = 'Y'
				LIMIT 1";
			$res = $this->LoadSingle($query);
			if (is_array($res) && !empty($res['title']))
			{
				$page_title = $res['title'];				// title as stored in database
			}
			else
			{
				$result = $tag;								// tag: always valid text
			}
		}
		// when we get here, either no tag was specified, or it was the current page
		elseif (!$this->HasPageTitle())						// current page, without title
		{
			$result = $this->GetPageTag();					// tag: always valid text
		}
		#if (empty($tag))									// current page
		else												// current page, with title
		{
			$page_title = $this->page_title;				// title as previously derived
		}

		// We clean the title, note that unlike makeId(), the characters " and ' are allowed here.
		// @@@	... but CleanTextNode removes them anyway! and...
		// @@@	JW: actually, only tags aren't allowed, but text in the full Unicode range is allowed for a title!
		//		the default behavior of CleanTextNode will mangle many Unicode strings that are
		//		perfectly valid for a document title!! See @todo at CleanTextNode().
		//		=> use headingToTextOnly() here (which will already trim!)
		if ('' != $page_title)		// do we have a title now?
		{
			$result = trim($this->CleanTextNode($page_title, ''));	# trim spaces #500
		}

		return $result;
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
	 * @uses		Wakka::getCount()
	 *
	 * @param		string	$page  page name to check
	 * @return		boolean	TRUE if page exists, FALSE otherwise
	 */
	function ExistsPage($page)
	{
		$where = "`tag` = '".mysql_real_escape_string($page)."'";
		$count = $this->getCount('pages',$where);
		$exists = ($count > 0);
		return $exists;
	}

	/**
	 * WIKI PING  -- Coded by DreckFehler
	 */

	/**
	 * WikiPing an external server.
	 *
	 * @todo	move to a dedicated class (plugin)
	 */
	function HTTPpost($host, $data, $contenttype='application/x-www-form-urlencoded', $maxAttempts = 5)
	{
		$attempt = 0;
		$status = 300;
		$result = '';
		while ($status >= 300 && $status < 400 && $attempt++ <= $maxAttempts)
		{
			$url = parse_url($host);
			if (isset($url['path']) == FALSE)
			{
				$url['path'] = '/';
			}
			if (isset($url["port"]) == FALSE)
			{
				$url['port'] = 80;
			}
			if ($socket = fsockopen ($url['host'], $url['port'], $errno, $errstr, 15))
			{
				$strQuery = 'POST '.$url['path'].' HTTP/1.1'."\n";
				$strQuery .= 'Host: '.$url['host']."\n";
				$strQuery .= 'Content-Length: '.strlen($data)."\n";
				$strQuery .= 'Content-Type: '.$contenttype."\n";
				$strQuery .= 'Connection: close'."\n\n";
				$strQuery .= $data;

				// send request & get response
				fputs($socket, $strQuery);
				$bHeader = TRUE;
				while (!feof($socket))
				{
					$strLine = trim(fgets($socket, 512));
					if (strlen($strLine) == 0)
					{
						$bHeader = FALSE; // first empty line ends header-info
					}
					if ($bHeader)
					{
						if (!$status)
						{
							$status = $strLine;
						}
						if (preg_match('/^Location:\s(.*)/', $strLine, $matches))
						{
							$location = $matches[1];
						}
					}
					else
					{
						$result .= trim($strLine)."\n";
					}
				}
				fclose ($socket);
			}
			else
			{
				$status = '999 timeout';
			}

			if ($status)
			{
				if (preg_match('/(\d){3}/', $status, $matches))
				{
					$status = $matches[1];
				}
			}
			else
			{
				$status = 999;
			}
			$host = $location;		// @@@ not used anywhere! (unless params are passed by reference - which they are not)
		}
		if (preg_match('/^[\da-fA-F]+(.*)$/', $result, $matches))
		{
			$result = $matches[1];
		}
		return $result;
	}
	/**
	 * Broadcast wiki changes to external servers.
	 *
	 * @uses	Wakka::HTTPpost()
	 * @todo	move to a dedicated class (plugin)
	 */
	function WikiPing($ping, $debug = FALSE)
	{
		if ($ping)
		{
			$rpcRequest = '';
			$rpcRequest .= "<methodCall>\n";
			$rpcRequest .= "<methodName>wiki.ping</methodName>\n";
			$rpcRequest .= "<params>\n";
			$rpcRequest .= "<param>\n<value>\n<struct>\n";
			$rpcRequest .= "<member>\n<name>tag</name>\n<value>".$ping['tag']."</value>\n</member>\n";
			$rpcRequest .= "<member>\n<name>url</name>\n<value>".$ping['taglink']."</value>\n</member>\n";
			$rpcRequest .= "<member>\n<name>wiki</name>\n<value>".$ping['wiki']."</value>\n</member>\n";
			if ($ping['author'])
			{
				$rpcRequest .= "<member>\n<name>author</name>\n<value>".$ping['author']."</value>\n</member>\n";
				if ($ping['authorpage'])
				{
					$rpcRequest .= "<member>\n<name>authorpage</name>\n<value>".$ping["authorpage"]."</value>\n</member>\n";
				}
			}
			if ($ping['history'])
			{
				$rpcRequest .= "<member>\n<name>history</name>\n<value>".$ping['history']."</value>\n</member>\n";
			}
			if ($ping['changelog'])
			{
				$rpcRequest .= "<member>\n<name>changelog</name>\n<value>".$this->htmlspecialchars_ent($ping['changelog'],ENT_COMPAT,'XML')."</value>\n</member>\n";
			}
			$rpcRequest .= "</struct>\n</value>\n</param>\n";
			$rpcRequest .= "</params>\n";
			$rpcRequest .= "</methodCall>\n";

			foreach (explode(' ', $ping['server']) as $server)
			{
				$response = $this->HTTPpost($server, $rpcRequest, 'text/xml');
				if ($debug)
				{
					print $response;
				}
			}
		}
	}
	/**
	 * Gather the necessary parameters for WikiPing.
	 *
	 * @uses	Wakka::Href()
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::LoadPage()
	 *
	 * @param	string	$server	mandatory:
	 * @param	string	$tag	mandatory:
	 * @param	string	$user	mandatory:
	 * @param	string	$changelog	optional:
	 * @return	mixed	either an array with the WikiPing-params or FALSE
	 *					if retrieving one of the required parameters failed
	 * @todo	move to a dedicated class (plugin)
	 */
	function GetPingParams($server, $tag, $user, $changelog = '')
	{
		// init
		$ping = array();
		$result = FALSE;
		// get params
		if ($server)
		{
			$ping['server'] = $server;
#			if ($tag)
#			{
#				$ping['tag'] = $tag; // set page-title
#			}
#			else
			if (!$ping['tag'] = $tag)									// set page-title
			{
				$result = FALSE;
			}
			elseif (!$ping['taglink'] = $this->Href('', $tag))			// set page-url
			{
				$result = FALSE;
			}
			elseif (!$ping['wiki'] = $this->GetConfigValue('wakka_name'))// set site-name
			{
				$result = FALSE;
			}
			else
			{
				$ping['history'] = $this->Href('revisions', $tag);		// set url to history

				if ($user)
				{
					$ping['author'] = $user; // set username
					if ($this->LoadPage($user))
					{
						$ping['authorpage'] = $this->Href('', $user);	// set link to user page
					}
				}
				if ($changelog)
				{
					$ping['changelog'] = $changelog;
				}
				$result = $ping;
			}
		}
		return $result;
	}

	/**
	 * COOKIE-related functions.
	 */

	/**
	 * Set a (session or persistent) cookie.
	 *
	 * This method implements both session and persistent cookies (only
	 * expiration differs). Cookie path is automatically set to the Wikka path.
	 * The default action is to set a persistent cookie.
	 *
	 * @uses	Config::$wiki_suffix
	 * @uses	WIKKA_COOKIE_PATH
	 * @uses	DEFAULT_COOKIE_EXPIRATION_HOURS
	 * @uses	Wakka::$cookie_sent
	 *
	 * @param	$name	string	mandatory: name of the cookie (will be supplemented
	 * 					with configured "wiki_suffix")
	 * @param	$value	mixed	mandatory: value to assign to the name (will
	 *					effectively be turned into a string when sent).
	 *					From php.net: "Because setting a cookie with a value of
	 *					FALSE will try to delete the cookie, you should not use
	 *					boolean values. Instead, use 0 for FALSE and 1 for TRUE."
	 * @param	$expires	integer	optional: time in <b>hours</b> after which the
	 *					cookie is to expire; default {@link DEFAULT_COOKIE_EXPIRATION_HOURS};
	 *					pass any <b>negative</b> value to create a session cookie
	 */
	function setWikkaCookie($name, $value, $expires=DEFAULT_COOKIE_EXPIRATION_HOURS)
	{
		// init
		$this->cookies_sent = FALSE;
		$valid_until = ($expires < 0) ? 0 : time() + ($expires * 60 * 60);
		// attempt to set cookie
		$rc = setcookie($name.$this->GetConfigValue('wiki_suffix'), $value, $valid_until, WIKKA_COOKIE_PATH);
		if ($rc)
		{
			$_COOKIE[$name.$this->GetConfigValue('wiki_suffix')] = $value;
			$this->cookies_sent = TRUE;
		}
	}
	/**
	 * Set a persistent Cookie - DEPRECATED.
	 *
	 * A wrapper for setWikkaCookie() - kept for backwards compatibility with third-party
	 * extensions that might use this.
	 *
	 * @param	$name	string	mandatory: name of the cookie (will be supplemented
	 * 					with configured "wiki_suffix")
	 * @param	$value	mixed	mandatory: value to assign to the name (will
	 *					effectively be turned into a string when sent).
	 * 					From php.net: "Because setting a cookie with a value of
	 *					FALSE will try to delete the cookie, you should not use
	 *					boolean values. Instead, use 0 for FALSE and 1 for TRUE."
	 * @param	$expires	integer	optional:	time in hours after which the
	 *					cookie is to expire; default {@link DEFAULT_COOKIE_EXPIRATION_HOURS}
	 */
	function SetPersistentCookie($name, $value, $expires=DEFAULT_COOKIE_EXPIRATION_HOURS)
	{
		$this->setWikkaCookie($name, $value, $expires);
	}
	/**
	 * Set a session Cookie - DEPRECATED.
	 *
	 *
	 * A wrapper for setWikkaCookie() - kept for backwards compatibility with third-party
	 * extensions that might use this.
	 *
	 * @param	$name	string	mandatory: name of the cookie (will be supplemented
	 * 					with configured "wiki_suffix")
	 * @param	$value	mixed	mandatory: value to assign to the name (will
	 *					effectively be turned into a string when sent). See also
	 *					{@link SetPersistentCookie()}
	 */
	function SetSessionCookie($name, $value)
	{
		// negative 'expires' tells setWikkaCookie() to make it a session cookie
		$this->setWikkaCookie($name, $value, -1);
	}
	/**
	 * Delete a Cookie.
	 *
	 * Technical note:<br/>
	 * Unless overridden with $internal set to FALSE, this function will also
	 * (attempt to) unset the current corresponding value in the $_COOKIE array.
	 * According to {@link http://php.net/unset} it is not possible to unset a
	 * global variable from within a function (and still have it unset after the
	 * function has run) but in my testing variables in teh $_COOKIE array as
	 * well as variables in the $_SESSION array can be unset from within a
	 * function and remain unset. This is also independent of the setting for
	 * register_globals. Just to be sure, we set the value to NULL (after which
	 * it still exists), before unsetting it; this is in case PHP in a different
	 * version or on a different platform behaves differently from mine. -- JW
	 *
	 * @uses	WIKKA_COOKIE_PATH
	 * @uses	Config::$wiki_suffix
	 * @uses	Wikka::$cookies_sent
	 *
	 * @param	$name	string	mandatory:	name of the cookie to delete
	 * @param	$path	string	optional: path the cookie was defined as valid for;
	 *					defaults to the current Wikka path
	 * @param	$internal	boolean	optional: if set to TRUE, value in $_COOKIE array
	 *					will be unset after setting the cookie to "deleted": this
	 *					is the normal case, deleting permanent cookies on logout,
	 *					while for clearing up old cookies we cannot always do this;
	 *					default: TRUE
	 * @param	$rawname	boolean	optional: if set to TRUE, will use only the
	 *					specified cookie name, if set to FALSE, the name is
	 *					"supplemented" with the configured "wiki_suffix";
	 *					default FALSE
	 * @return	void	(but if setcookie was successful, $this->cookies_sent will
	 *					be set to TRUE)
	 */
	function deleteWikkaCookie($name, $path='', $internal=TRUE, $rawname=FALSE)
	{
		// init
		$this->cookies_sent = FALSE;
		if ('' == trim($path))
		{
			$path = WIKKA_COOKIE_PATH;	// default
		}
		$cookiename = ($rawname) ? $name : $name.$this->GetConfigValue('wiki_suffix');

		if (isset($_COOKIE[$cookiename]))
		{
			// FALSE and time in past to immediately delete cookie
			$rc = setcookie($cookiename, FALSE, 1, $path);
			if ($rc && $internal)
			{
				$_COOKIE[$cookiename] = NULL;	// empties value, but key still exists
				unset($_COOKIE[$cookiename]);	// actually removes the key (but see docblock)
				$this->cookies_sent = TRUE;
			}
		}
	}
	/**
	 * Delete old cookies (old names, old paths).
	 *
	 * This will attempt to remove cookies with old, un-suffixed names (always
	 * root as path), and cookies with suffixed names that had root as path when
	 * the current cookie path is <b>not</b> root.
	 *
	 * There is a (very) slight risk this might remove a cookie of another Wikka
	 * installation running on the same server (precisely why we're now using a
	 * cookie path!) but once all Wikka installations are upgraded this becomes
	 * highly unlikely.
	 *
	 * @uses WIKKA_COOKIE_PATH
	 * @uses Config::$wiki_suffix
	 * @uses Wakka::deleteWikkaCookie()
	 *
	 */
	function deleteOldWikkaCookies()
	{
		// get debug flag from wikka.php
		global $debug;

		foreach($_COOKIE as $name => $value)
		{
			switch ($name)
			{
				// old name
				case 'wikka_user_name':
if ($debug) echo "deleting 'wikka_user_name' at root<br/>\n";
					// delete cookie using 'raw' name and also delete from $_COOKIE
					$this->deleteWikkaCookie('wikka_user_name','/',TRUE,TRUE);
					break;
				// old name
				case 'wikka_pass':
if ($debug) echo "deleting 'wikka_pass' at root<br/>\n";
					// delete cookie using 'raw' name and also delete from $_COOKIE
					$this->deleteWikkaCookie('wikka_pass','/',TRUE,TRUE);
					break;
				// old path
				case 'user_name'.$this->GetConfigValue('wiki_suffix'):
					if (WIKKA_COOKIE_PATH != '/')
					{
if ($debug) echo "deleting 'user_name".$this->GetConfigValue('wiki_suffix')."' at root<br/>\n";
						// do NOT delete from $_COOKIE since this cannot be path-specific
						$this->deleteWikkaCookie('user_name','/',FALSE);
					}
					break;
				// old path
				case 'pass'.$this->GetConfigValue('wiki_suffix'):
					if (WIKKA_COOKIE_PATH != '/')
					{
if ($debug) echo "deleting 'pass".$this->GetConfigValue('wiki_suffix')."' at root<br/>\n";
						// delete cookie but do NOT delete from $_COOKIE since this cannot be path-specific
						$this->deleteWikkaCookie('pass','/',FALSE);
					}
					break;
			}
		}
	}
	/**
	 * Get the value of a Cookie.
	 * 
	 * @param string $name Name of the cookie, used in {@link Wakka::SetPersistentCookie()} and {@link Wakka::SetSessionCookie()}
	 * @return mixed value of the cookie, or the boolean FALSE if the cookie is not present.
	 * @uses	Config::$wiki_suffix
	 */
	function getWikkaCookie($name)
	{
		// NOTE: see note in GetUser for why we use empty() here!
		if (empty($_COOKIE[$name.$this->GetConfigValue('wiki_suffix')]))
		{
			$cookie = FALSE;
		}
		else
		{
			$cookie = $_COOKIE[$name.$this->GetConfigValue('wiki_suffix')];
		}
		return $cookie;
	}
	/**
	 * @deprecated deprecated since version 1.1.7
	 * @see {@link Wakka::getWikkaCookie()}
	 * @uses	Wakka::getWikkaCookie()
	 */
	function GetCookie($name)
	{
		return $this->getWikkaCookie($name);
	}

	/**
	 * HTTP/REQUEST/LINK RELATED
	 */

	/**
	 * Store a message in the session to be displayed after redirection.
	 *
	 * @param	string	$message	text to be stored
	 */
	function SetRedirectMessage($message)
	{
		$_SESSION['redirectmessage'] = $message;
	}
	/**
	 * Get a message, if one was stored before redirection.
	 *
	 * @return string either the text of the message or an empty string.
	 */
	function GetRedirectMessage()
	{
		$message = '';
		if (isset($_SESSION['redirectmessage']))
		{
			$message = $_SESSION['redirectmessage'];
			$_SESSION['redirectmessage'] = '';
		}
		return $message;
	}
	/**
	 * Perform a redirection to another page.
	 *
	 * On IIS server, and if the page has sent any cookies, the redirection must not be performed
	 * by using the 'Location:' header. We use meta http-equiv OR javascript OR link (Credits MarceloArmonas).
	 *
	 * @author	{@link http://wikkawiki.org/DotMG Mahefa Randimbisoa} (added IIS support)
	 * @since	Wikka 1.0.0
	 *
	 * @access	public
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::tag
	 * @uses	Wakka::cookies_sent
	 *
	 * @param	string	$url optional: destination URL; if not specified redirect to the same page.
	 * @param	string	$message optional: message that will show as alert in the destination URL
	 * @todo	clean up the HTML: use a HEREDOC; lang/xml:lang should be taken from current or DEFAULT_LANGUAGE!
	 */
	function Redirect($url='', $message='')
	{
		if ($message != '')
		{
			$_SESSION['redirectmessage'] = $message;
		}
		$url = ($url == '' ) ? $this->wikka_url.$this->tag : $url;	// @@@ rewrite? TEST!
		if ((eregi('IIS', $_SERVER['SERVER_SOFTWARE'])) && ($this->cookies_sent))
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
			session_write_close(); # Always use session_write_close() before any header('Location: ...')
			header('Location: '.$url);
		}
		exit;
	}
	/**
	 * Return the pagename (with optional handler appended).
	 *
	 * @uses	Wakka::htmlspecialchars_ent()
	 */
	function MiniHref($handler='', $tag='')
	{
		$tag = trim($tag);
		if (empty($tag))
		{
			#$tag = $this->GetPageTag();
			$tag = $this->tag;
		}
		$href = $this->htmlspecialchars_ent($tag.($handler ? '/'.$handler : ''));
		return $href;
	}
	/**
	 * Return the full URL to a page/handler.
	 *
	 * @uses	Config::$rewrite_mode
	 * @uses	Wakka::MiniHref()
	 * @uses	Wakka::$wikka_url
	 */
	function Href($handler='', $tag='', $params='')
	{
		/*
		$href = $this->GetConfigValue('base_url');
		if ($this->GetConfigValue('rewrite_mode') == 0)
		{
			$href .= 'wikka.php?wakka=';
		}
		*/
		$href  = $this->wikka_url;
		$href .= $this->MiniHref($handler, $tag);
		if ($params)
		{
			$href .= ($this->GetConfigValue('rewrite_mode') ? '?' : '&amp;').$params;
		}
		return $href;
	}
	/**
	 * Creates a link from Wikka markup.
	 *
	 * Beware of the $title parameter: quotes and backslashes should be previously
	 * escaped before the title is passed to this method.
	 *
	 * @access	public
	 *
	 * @uses	Wakka::GetInterWikiUrl()
	 * @uses	Wakka::Href()
	 * @uses	Wakka::htmlspecialchars_ent()
	 * @uses	Wakka::LoadPage()
	 * @uses	Wakka::TrackLinkTo()
	 * @uses	Wakka::ExistsPage()
	 *
	 * @param	mixed	$tag		mandatory:
	 * @param	string	$handler	optional:
	 * @param	string	$text		optional:
	 * @param	boolean	$track		optional:
	 * @param	boolean	$escapeText	optional:
	 * @param	string	$title		optional:
	 * @param	string	$class		optional:
	 * @return	string	an HTML hyperlink (a href) element
	 * @todo	move regexps to regexp-library		#34
	 */
	function Link($tag, $handler='', $text='', $track=TRUE, $escapeText=TRUE, $title='', $class='')
	{
		global $debug;
if ($debug) echo 'Link - tag: '.$tag;
		// init
		if (!$text)
		{
			$text = $tag;
		}
		if ($escapeText)	// escape text?
		{
			$text = $this->htmlspecialchars_ent($text);
		}
		$tag = $this->htmlspecialchars_ent($tag); #142 & #148
		$handler = $this->htmlspecialchars_ent($handler);
		$title_attr = $title ? ' title="'.$this->htmlspecialchars_ent($title).'"' : '';
		$url = '';
		$wikilink = '';

		// is this an interwiki link?
		// before the : should be a WikiName; anything after can be (nearly) anything that's allowed in a URL
		if (preg_match('/^([A-ZÄÖÜ][A-Za-zÄÖÜßäöü]+)[:](\S*)$/', $tag, $matches))	// @@@ FIXME #34 (inconsistent with Formatter)
		{
if ($debug) echo ': interwiki';
			$url = $this->GetInterWikiUrl($matches[1], $matches[2]);
			$class = 'interwiki';
		}
		// fully-qualified URL? this uses the same pattern as StaticHref() does;
		// it's a recognizing pattern, not a validation pattern
		// @@@ move to regex libary!
		elseif (preg_match('/^(http|https|ftp|news|irc|gopher):\/\/([^\\s\"<>]+)$/', $tag))
		{
if ($debug) echo ': external';
			$url = $tag; // this is a valid external URL
			// add ext class only if URL is external
			if (!preg_match('/'.$_SERVER['SERVER_NAME'].'/', $tag))
			{
				$class = 'ext';
			}
		}
		// is this a full link? i.e., does it contain something *else* than valid WikiName characters?
		// FIXME just use (!IsWikiName($tag)) here (then fix the RE there!)
		// @@@ First move to regex library
		elseif (preg_match('/[^[:alnum:]ÄÖÜßäöü]/', $tag))		// FIXED #34 - removed commas
		{
if ($debug) echo ': uri missing scheme';
			// check for email addresses
			if (preg_match('/^.+\@.+$/', $tag))
			{
				$url = 'mailto:'.$tag;
				$class = 'mailto';
			}
			// check for protocol-less URLs
			elseif (!preg_match('/:/', $tag))
			{
				$url = 'http://'.$tag;
				$class = 'ext';
			}
		}
		else
		{
if ($debug) echo ': wikilink';
			// it's a wiki link
			if (isset($_SESSION['linktracking']) && $_SESSION['linktracking'] && $track)
			{
				$this->TrackLinkTo($tag);
			}
			//$linkedPage = $this->LoadPage($tag);
			// return ($linkedPage ? '<a class="'.$class.'" href="'.$this->Href($handler, $linkedPage['tag']).'"'.$title_attr.'>'.$text.'</a>' : '<a class="missingpage" href="'.$this->Href("edit", $tag).'" title="'.CREATE_THIS_PAGE_LINK_TITLE.'">'.$text.'</a>'); #i18n
			// MODIFIED to use existsPage() (more efficient!)
			if (!$this->existsPage($tag))
			{
				$link = '<a class="missingpage" href="'.$this->Href('edit', $tag).'" title="'.CREATE_THIS_PAGE_LINK_TITLE.'">'.$text.'</a>';
			}
			else
			{
				$link = '<a class="'.$class.'" href="'.$this->Href($handler, $tag).'"'.$title_attr.'>'.$text.'</a>';
			}
		}
if ($debug) echo "<br/>\n";

		//return $url ? '<a class="'.$class.'" href="'.$url.'">'.$text.'</a>' : $text;
		if ('' != $url)
		{
			$result = '<a class="'.$class.'" href="'.$url.'">'.$text.'</a>';
		}
		elseif ('' != $link)
		{
			$result = $link;
		}
		else
		{
			$result = $text;
		}
		return $result;
	}
	/**
	 * Create a href for a static file.
	 *
	 * It takes a parameter $filepath, the path of the static file, and returns
	 * a string representing a fully-qualified URL.
	 * This function should be used everywhere a static file should be attached
	 * to a wikkapage via XHTML tag attributes that expect a URL, such as href,
	 * src, or archive tags, or attributes in elements in XML/RSS.
	 *
	 * Its main purpose is to avoid "path confusion" when a relative URL would be
	 * attached to a <b>rewritten</b> (base) URL; without rewriting there's no
	 * problem, but when mod_rewrite is active, it's really necessary:
	 * a base_href doesn't help (and is in fact unnecessary when using
	 * fully-qualified paths as returned by this method).
	 *
	 * @access	public
	 * @uses WIKKA_BASE_DOMAIN_URL
	 * @uses WIKKA_BASE_URL
	 *
	 * @param	string	$filepath	path for a static file; this can be either:
	 *				- a relative path
	 *				- an absolute path (starting with a slash)
	 *				- a fully-qualified URL (in which case the input is simply returned)
	 * @return	string	a standardized fully-qualified URL
	 */
	function StaticHref($filepath)
	{
#echo "\n<!--StaticHref - in: ".$filepath."-->\n";
		/*
		$result = $this->Href('dummyhandler','dummypagename');
		$result = str_replace('wikka.php?wakka=', '', $result);
		$result = str_replace('dummypagename/dummyhandler', $filepath, $result);
		*/
		// fully-qualified URL? this uses the same pattern as Link() does;
		// it's a recognizing pattern, not a validation pattern
		// @@@ move to regex libary!
		if (preg_match('/^(http|https|ftp|news|irc|gopher):\/\/([^\\s\"<>]+)$/', $filepath))
		{
			$result = $filepath;
		}
		elseif ('/' == substr($filepath,0,1))	// absolute path
		{
			$result = WIKKA_BASE_DOMAIN_URL.$filepath;
		}
		else								// relative path
		{
			$result = WIKKA_BASE_URL.$filepath;
		}
#echo "<!--StaticHref - out: ".$result."-->\n";
		return $result;
	}

	// function PregPageLink($matches) { return $this->Link($matches[1]); }

	/**
	 * Check if a given string is in CamelCase format.
	 *
	 * @todo	remove the comma's in the RE!		#34
	 * @todo	move regexps to regexp-library		#34
	 */
	function IsWikiName($text)
	{
		$result = preg_match("/^[A-Z,ÄÖÜ][a-z,ßäöü]+[A-Z,0-9,ÄÖÜ][A-Z,a-z,0-9,ÄÖÜ,ßäöü]*$/", $text); // @@@ FIXME #34 (inconsistent with Formatter!) remove all ',' from RE (comma should not be allowed in WikiNames!)
		return $result;
	}

	/**
	 * LINK tracking
	 */

	function TrackLinkTo($tag)
	{
		$_SESSION['linktable'][] = $tag;
	}
	function GetLinkTable()
	{
		$linktable = $_SESSION['linktable'];
		return $linktable;
	}
	function ClearLinkTable()
	{
		$_SESSION['linktable'] = array();
	}
	function StartLinkTracking()
	{
		$_SESSION['linktracking'] = 1;
	}
	function StopLinkTracking()
	{
		$_SESSION['linktracking'] = 0;
	}
	function WriteLinkTable()
	{
		// delete entries for current page from link table
		$this->Query("
			DELETE
			FROM ".$this->GetConfigValue('table_prefix')."links
			WHERE from_tag = '".mysql_real_escape_string($this->tag)."'"
			);
		// build and insert new entries for current page in link table
		if ($linktable = $this->GetLinkTable())
		{
			$from_tag = $this->tag;
			$values = '';
			$written = array();
			// @@@ make into function! buildSQLValueList()
			foreach ($linktable as $to_tag)
			{
				$lower_to_tag = strtolower($to_tag);
				if ((!$written[$lower_to_tag]) && ($lower_to_tag != strtolower($from_tag)))
				{
					if ('' != $values)
					{
						$values .= ",\n";	// newline for better query layout
					}
					$values .= "('".mysql_real_escape_string($from_tag)."', '".mysql_real_escape_string($to_tag)."')";
					$written[$lower_to_tag] = 1;
				}
			}
			if ('' != $values)
			{
				$this->Query("
					INSERT INTO ".$this->GetConfigValue('table_prefix')."links
					VALUES ".$values
					);
			}
		}
	}

	/**
	 * OUTPUT generation
	 */

	/**
	 * Output the header for Wikka-pages.
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::IncludeBuffered()
	 */
	function Header()
	{
		$header = $this->IncludeBuffered('header.php', $this->GetConfigValue('wikka_template_path'), ERROR_HEADER_MISSING);
		return $header;
	}
	/**
	 * Output the footer for Wikka-pages.
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::IncludeBuffered()
	 */
	function Footer()
	{
		$footer = $this->IncludeBuffered('footer.php', $this->GetConfigValue('wikka_template_path'), ERROR_FOOTER_MISSING);
		return $footer;
	}

	/**
	 * FORMS
	 */

	/**
	 * Open form.
	 *
	 * @uses	Wakka::GetConfigValue()
	 *
	 * @todo	replace with advanced FormOpen (so IDs are generated, among other things!)
	 * @todo	check if the hidden field is still needed - Href() already provides
	 *			the wakka= part of the URL... everything seems to work fine with
	 *			or without rewrite mode, and without this hidden field!
	 */
	/* replaced by http://wikkawiki.org/AdvancedFormOpen
	function FormOpen($handler='', $tag='', $formMethod='post')
	{
		$result = '<form action="'.$this->Href($handler, $tag).'" method="'.$formMethod.'">'."\n";
		#if (!$this->GetConfigValue('rewrite_mode'))
		#{
		#	$result .= '<input type="hidden" name="wakka" value="'.$this->MiniHref($handler, $tag).'" />'."\n";
		#}
		return $result;
	}
	*/
	/**
	 * Build an opening form tag with specified or generated attributes.
	 *
	 * This method builds an opening form tag, taking care that the result is valid XHTML
	 * no matter where the parameters come from: invalid parameters are ignored and defaults used.
	 * This enables this method to be used with user-provided parameter values.
	 *
	 * The form will always have the required action attribute and an id attribute to provide
	 * a 'hook' for styling and scripting. This method tries its best to ensure the id attribute
	 * is unique, among other things by adding a 'form_' prefix to make it different from ids for
	 * other elements.
	 * For a file upload form ($file=TRUE) the appropriate method and enctype attributes are generated.
	 *
	 * @author		{@link http://wikkawiki.org/JavaWoman JavaWoman} (Advanced version: complete rewrite; 2005)
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 *
	 * @access	public
	 * @uses	makeId()
	 * @uses	ID_LENGTH
	 * @uses	existsHandler()
	 * @uses	existsPage()
	 * @uses	Href()
	 * @uses	MiniHref()	only for hidden field
	 *
	 * @param	string	$handler	optional: "handler" which consists of handler name and possibly a query string
	 *								to be used as part of action attribute
	 * @param	string	$tag		optional: page name to be used for action attribute;
	 *								if not specified, the current page will be used
	 * @param	string	$formMethod	optional: method attribute; must be POST (default) or GET;
	 *								anything but POST is ignored and considered as GET;
	 *								always converted to lowercase
	 * @param	string	$id			optional: id attribute
	 * @param	string	$class		optional: class attribute
	 * @param	boolean	$file		optional: specifies whether there will be a file upload field;
	 *								default: FALSE; if TRUE sets method attribute to POST and generates
	 *								appropriate enctype attribute
	 * @return	string opening form tag
	 * @todo	extend to handle a complete (external) URL instead of (handler+)pagename
	 * @todo	extend to allow extra attributes
	 */
	function FormOpen($handler='', $tag='', $formMethod='post', $id='', $class='', $file=FALSE)
	{
		// init
		$attrMethod = '';									// no method for HTML default 'get'
		$attrClass = '';
		$attrEnctype = '';									// default no enctype -> HTML default application/x-www-form-urlencoded
		$hidden = array();
		// derivations
		$handler = trim($handler);
		$tag = trim($tag);
		$id = trim($id);
		$class = trim($class);
		// validations
		#$validHandler = $this->existsHandler($handler);
		#$validPage = $this->existsPage($tag);
		// validation needed only if parameters are actually specified
		#$handler = ($validHandler) ? $handler : '';
		if (!empty($handler) && !$this->existsHandler($handler))
		{
			$handler = '';
		}
		#$tag = ($validPage) ? $tag : '';
		if (!empty($tag) && !$this->existspage($tag))
		{
			$tag = '';	// Href() will pick up current page name if none specified
		}

		// form action (action is a required attribute!)
		// !!! If rewrite mode is off, "tag" has to be passed as a hidden field
		// rather than part of the URL (where it gets ignored on submit!)
		if ($this->GetConfigValue('rewrite_mode'))
		{
			// @@@ add passed extra GET params here by passing them as extra
			// parameter to Href()
			$attrAction = ' action="'.$this->Href($handler, $tag).'"';
		}
		else
		{
			$attrAction = ' action="'.$this->Href($handler, $tag).'"';
			// #670: This value will short-circuit the value of wakka=... in URL.
			$hidden['wakka'] = $this->MiniHref($handler, ('' == $tag ? $this->GetPageTag(): $tag));
			// @@@ add passed extra GET params here by adding them as extra
			// entries to $hidden (probably not by adding them to Href()
			// but that needs to be tested when we get to it!)
		}
		// form method (ignore anything but post) and enctype
		if (TRUE === $file)
		{
			$attrMethod  = ' method="post"';				// required for file upload
			$attrEnctype = ' enctype="multipart/form-data"';// required for file upload
		}
		elseif (preg_match('/^post$/i',$formMethod))		// ignore case...
		{
			$attrMethod = ' method="post"';					// ...but generate lowercase
		}
		// form id
		if ('' == $id)										// if no id given, generate one based on other parameters
		{
			$id = substr(md5($handler.$tag.$formMethod.$class),0,ID_LENGTH);
		}
		$attrId = ' id="'.$this->makeId('form',$id).'"';	// make sure we have a unique id
		// form class
		if ('' != $class)
		{
			$attrClass = ' class="'.$class.'"';
		}

		// build HTML fragment
		$fragment = '<form'.$attrAction.$attrMethod.$attrEnctype.$attrId.$attrClass.'>'."\n";
		// construct and add hidden fields (necessary if we are NOT using rewrite mode)
		if (count($hidden) > 0)
		{
			$fragment .= '<fieldset class="hidden">'."\n";
			foreach ($hidden as $name => $value)
			{
				$fragment .= '	<input type="hidden" name="'.$name.'" value="'.$value.'" />'."\n";
			}
			$fragment .= '</fieldset>'."\n";
		}

		// return resulting HTML fragment
		return $fragment;
	}
	/**
	 * Close form
	 *
	 * @return	string	the XHTML tag to close a form and a newline.
	 */
	function FormClose()
	{
		$result = '</form>'."\n";
		return $result;
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
	 * @todo	allow multiple spaces and/or tabs as delimiter
	 */
	function ReadInterWikiConfig()
	{
		if ($lines = file('interwiki.conf'))
		{
			foreach ($lines as $line)
			{
				if ($line = trim($line))
				{
					list($wikiName, $wikiUrl) = explode(' ', trim($line));	// @@@ allow any tabs/spaces, not just single space
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
	 * @param  string $name	mandatory: the shortcut for the interWiki
	 * @param  string $tag	mandatory: name of a page in the other wiki
	 * @return string the full URL for $tag or an empty string
	 */
	function GetInterWikiUrl($name, $tag)	// MODIFIED: always returns result now
	{
		$url = '';
		if (isset($this->interWiki[strtolower($name)]))
		{
			$url = $this->interWiki[strtolower($name)].$tag;
		}
		return $url;
	}

	/**
	 * Log REFERRERS.
	 * Store external referrer into table wikka_referrers. The referrer's host is
	 * checked against a blacklist (table wikka_blacklist) and it will be ignored
	 * if it's present at this table.
	 *
	 * @uses Wakka::cleanUrl()
	 * @uses WIKKA_BASE_URL
	 * @uses Wakka::LoadSingle()
	 * @uses Config::$table_prefix
	 */

	function LogReferrer($tag = '', $referrer = '')
	{
		/* better fix farther on
		if (!isset($_SERVER['HTTP_REFERER']))
		{
			return; #38
		}
		*/

		// fill values
		if (!$tag = trim($tag))
		{
			#$tag = $this->GetPageTag();
			$tag = $this->tag;
		}
		if (empty($referrer))
		{
			$referrer = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';	#38
		}
		$referrer = trim($this->cleanUrl($referrer));			# secured JW 2005-01-20

		// check if it's coming from another site
		#if ($referrer && !preg_match('/^'.preg_quote($this->GetConfigValue('base_url'), '/').'/', $referrer))
		if (!empty($referrer) && !preg_match('/^'.preg_quote(WIKKA_BASE_URL, '/').'/', $referrer))
		{
			$parsed_url = parse_url($referrer);
			$spammer = $parsed_url['host'];
			$blacklist = $this->LoadSingle("
				SELECT *
				FROM ".$this->GetConfigValue('table_prefix')."referrer_blacklist
				WHERE spammer = '".mysql_real_escape_string($spammer)."'"
				);
			if (FALSE === $blacklist)
			{
				$this->Query("
					INSERT INTO ".$this->GetConfigValue('table_prefix')."referrers
					SET page_tag	= '".mysql_real_escape_string($tag)."',
						referrer	= '".mysql_real_escape_string($referrer)."',
						time		= now()"
					);
			}
		}
	}
	function LoadReferrers($tag = '')
	{
		$where = ($tag = trim($tag)) ? "			WHERE page_tag = '".mysql_real_escape_string($tag)."'" : '';
		$referrers = $this->LoadAll("
			SELECT referrer, COUNT(referrer) AS num
			FROM ".$this->GetConfigValue('table_prefix')."referrers".
			$where."
			GROUP BY referrer
			ORDER BY num DESC"
			);
		return $referrers;
	}

	/**
	 * ACTIONS / PLUGINS
	 */

	/**
	 * Handle the call to an action.
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::IncludeBuffered()
	 * @uses	Wakka::StartLinkTracking()
	 * @uses	Wakka::StopLinkTracking()
	 *
	 * @param	string	$actionspec	mandatory: the complete content of the action "tag"
	 * @param	int		$forcelinktracking	optional: set to TRUE (or something that evaluates to it...)
	 *					to ensure that the included content is tracked for links; default: 0
	 * @return	string	output produced by {@link Wakka::IncludeBuffered()} or an error message
	 * @todo	move regexes to central regex library			#34
	 * @todo	use action config files (e.g., pass only specified parameters)	#446
	 * @todo	don't use numbers when booleans are intended! TRUE and FALSE advertize their intention much clearer
	 */
	function Action($actionspec, $forceLinkTracking=0)	// @@@
	{
#echo 'Action - actionspec: |'.$actionspec."|<br/>\n";
		// parse action spec and check if we have a syntactically valid action name	[SEC]
		// the regex allows an action name consisting of letters and numbers ONLY
		// and thus provides defense against directory traversal or XSS (via action *name*)
		if (!preg_match('/^\s*([a-zA-Z0-9]+)(\s.+?)?\s*$/', $actionspec, $matches))	# see also #34
		{
			$out = '<em class="error">'.ACTION_UNKNOWN_SPECCHARS.'</em>';	# [SEC]
		}
		else
		{
			// valid action name, so we pull out the parts, and make the action name lowercase
			$action_name = strtolower($matches[1]);
			$paramlist = (isset($matches[2])) ? trim($matches[2]) : '';

			// search for parameters if there was more than just a (syntactically valid) action name
			$vars = array();
			if ('' != $paramlist)
			{
				// match all attributes (key and value)
				preg_match_all('/([a-zA-Z0-9]+)=(\"|\')(.*)\\2/U', $paramlist, $matches);	# [SEC] parameter name should not be empty #34
				// $matches[1] contains an array of parameter names
				// $matches[3] contains an array of corresponding parameter values

				// prepare an array for extract() to work with (in $this->IncludeBuffered())
				if (is_array($matches))
				{
					for ($a = 0; $a < count($matches[0]); $a++)
					{
						// The parameter value is sanitized using htmlspecialchars_ent();
						// if an action really needs "raw" HTML as input it can
						// still be "unescaped" by the action itself; otherwise,
						// any HTML will be displayed _as code_, but not interpreted.
						// For any other action htmlspecialchars_ent() guards against
						// XSS via user-supplied action parameters.
						// NOTE 1:	this may not provide *complete* protection against XSS!
						// NOTE 2:	It is still the responsibility of each action
						//			to validate its own parameters!
						//			That includes guarding against directory traversal.
						$vars[$matches[1][$a]] = $this->htmlspecialchars_ent($matches[3][$a]);	// parameter name = sanitized value [SEC]
					}
				}
				// add the complete parameter-string to the array:
				// this may be needed for parameters that don't have the keyword="value" format (DEPRECATED)
				$vars['wikka_vars'] = $paramlist;
			}

			// @@@ a little encapsulation here would be nice: move the conditions within the functions!
			// instead of stop & start maybe suspend and resume (with the beahvior here encapsulated) would be more appropriate here
			if (!$forceLinkTracking)
			{
				/**
				 * @var	boolean	holds previous state of LinkTracking before we StopLinkTracking().
				 *				It will then be used to test if we should StartLinkTracking() or not.
				 * @todo	it's not a boolean if we set it to zero! that's an integer.
				 * @todo	make this a object variable so we can <b>actually</b> document it phpDocumentor won't see thi
				 */
				// @@@ a little encapsulation here would be nice: move the variable within the functions! (can do if it's an object variable!)
				$link_tracking_state = (isset($_SESSION['linktracking']) && (bool) $_SESSION['linktracking']) ? $_SESSION['linktracking'] : 0; #38
				$this->StopLinkTracking();
			}
			// prepare variables
			$action_location		= $action_name.DIRECTORY_SEPARATOR.$action_name.'.php';
			$action_location_disp	= '<code>'.$this->htmlspecialchars_ent($action_location).'</code>';	// [SEC] make error (including (part of) request) safe to display
			$action_not_found		= sprintf(ACTION_UNKNOWN,$action_location_disp);
			// produce output
			#$out = $this->IncludeBuffered($action_location, $this->GetConfigValue('action_path'), $action_not_found, $vars);
			$out = $this->IncludeBuffered($action_location, $this->GetConfigValue('wikka_action_path'), $action_not_found, FALSE, $vars);
			// @@@ a little encapsulation here would be nice: move the conditions within the functions! (can do if it's an object variabl!)
			if ($link_tracking_state)
			{
				// we were tracking before, so start tracking again
				$this->StartLinkTracking();
			}
		}
		return $out;
	}
	/**
	 * Use a handler (on the current page).
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::IncludeBuffered()
	 *
	 * @param	string	$handler	mandatory: name of handler to execute
	 * @return	string	output produced by {@link Wakka::IncludeBuffered()} or an error message
	 * @todo	use templating class		JW: more likely to be used in handler itself!
	 * @todo	use handler config files;				#446
	 * @todo	move regexes to central regex library			#34
	 * @todo	implement further validation instead of simply extracting the part after the last slash
	 *			-OR- handle this in wikka.php through more intelligent parsing
	 */
	function Handler($handler)
	{
		global $debug;
if ($debug) echo 'Handler - handler specified: '.$handler."<br/>\n";
		if (strstr($handler, '/'))
		{
			// Observations - MK 2007-03-30
			// extract part after the last slash (if the whole request contained multiple slashes)
			// @@@
			// but should such requests be accepted in the first place?
			// at least it is a SORT of defense against directory traversal (but not necessarily XSS)
			$handler = substr($handler, strrpos($handler, '/')+1);
		}

		// check valid method name syntax (similar to Action())
		// the regex allows an action name consisting of letters, numbers, and
		// underscores, hyphens and dots ONLY and thus provides defense against
		// directory traversal or XSS (via handler *name*)
		if (!preg_match('/^([a-zA-Z0-9_.-]+)$/', $handler))	# see also #34
		{
			// @@@ this should be wrapped in <div class="page"> for consistent (and valid) layout
			$out = '<em class="error">'.HANDLER_UNKNOWN_SPECCHARS.'</em>';	# [SEC]
		}
		else
		{
			// valid method name; now make sure it's lower case
			$handler = strtolower($handler);
			// prepare variables
			$handler_location		= $handler.DIRECTORY_SEPARATOR.$handler.'.php';
			$handler_location_disp	= '<code>'.$this->htmlspecialchars_ent($handler_location).'</code>';	// [SEC] make error (including (part of) request) safe to display
			$handler_not_found		= sprintf(HANDLER_UNKNOWN,$handler_location_disp);
			// produce output
			#$out = $this->IncludeBuffered($handler_location, $this->GetConfigValue('handler_path'), $handler_error_body);
			#$out = $this->IncludeBuffered($handler_location, $this->GetConfigValue('wikka_handler_path'), $handler_error_body,TRUE);
			$out = $this->IncludeBuffered($handler_location, $this->GetConfigValue('wikka_handler_path'), $handler_not_found, TRUE);
		}
		return $out;
	}
	/**
	 * Check if a handler (specified after page name) really exists.
	 *
	 * May be passed as handler plus query string; we'll need to look at handler only
	 * so we strip off any querystring first.
	 *
	 * @author		{@link http://wikkawiki.org/JavaWoman JavaWoman} (created 2005; rewrite 2007)
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 *
	 * @access	public
	 * @uses	Wakka::GetConfigValue()
	 *
	 * @param	string 	$handler	handler name, optionally with appended parameters
	 * @return	boolean	TRUE if handler is found, FALSE otherwise
	 */
	function existsHandler($handler)
	{
		// first strip off any query string
		$parts = preg_split('/&/',$handler,1);				# return only one part
		$handler = $parts[0];
#echo 'handler: '.$handler.'<br/>';
		// now check if a handler by that name exists
#echo 'checking path: '.$this->GetConfigValue('wikka_handler_path').DIRECTORY_SEPARATOR.$handler.DIRECTORY_SEPARATOR.$handler.'.php'.'<br/>';
		$exists = file_exists($this->GetConfigValue('wikka_handler_path').DIRECTORY_SEPARATOR.$handler.DIRECTORY_SEPARATOR.$handler.'.php');
		// return conclusion
		return $exists;
	}
	/**
	 * Render a string using a given formatter or the standard Wakka by default.
	 *
	 * @uses	Config::$wikka_formatter_path
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::IncludeBuffered()
	 *
	 * @param	string	$text			the source text to format
	 * @param	string	$formatter		the name of the formatter. This name is linked to a file with the same name, located in the folder
	 *			specified by {@link Config::$wikka_formatter_path}, and with extension .php; which is called to process the text $text
	 * @param	string	$format_option	a comma separated list of string options, in the form of 'option1;option2;option3'
  *   this value is passed to compact() to re-create the variable on formatters/wakka.php
	 * @return	string	output produced by {@link Wakka::IncludeBuffered()} or an error message
	 * @todo	move regexes to central regex library			#34
	 */
	function Format($text, $formatter='wakka', $format_option='')
	{
#echo 'Format - formatter specified: '.$formatter."<br/>\n";
		// check valid formatter name syntax (same as Handler())
		// the regex allows an action name consisting of letters, numbers, and
		// underscores, hyphens and dots ONLY and thus provides defense against
		// directory traversal or XSS (via handler *name*)
		if (!preg_match('/^([a-zA-Z0-9_.-]+)$/', $formatter)) # see also #34
		{
			$out = '<em class="error">'.FORMATTER_UNKNOWN_SPECCHARS.'</em>';	# [SEC]
		}
		else
		{
			// valid formatter name; now make sure it's lower case
			$formatter = strtolower($formatter);
			// prepare variables
			$formatter_location			= $formatter.'.php';
			$formatter_location_disp	= '<code>'.$this->htmlspecialchars_ent($formatter_location).'</code>';	// [SEC] make error (including (part of) request) safe to display
			$formatter_not_found		= sprintf(FORMATTER_UNKNOWN,$formatter_location_disp);
			// produce output
			$out = $this->IncludeBuffered($formatter_location, $this->GetConfigValue('wikka_formatter_path'), $formatter_not_found, FALSE, compact('text', 'format_option')); // @@@
		}
		return $out;
	}

	/**
	 * Add a custom header(s) to be inserted inside the <head> section.
	 *
	 * @access	public
	 * @uses	Wakka::$additional_headers
	 *
	 * @param	string	$additional_headers	any valid XHTML code that is legal inside the <head> section.
	 * @param	string	$indent	optional: indent string, default is a tabulation. This will be inserted before $additional_headers
	 * @param	string	$sep	optional: separator string, this will separate your additional headers. This will be inserted after
	 *					$additional_headers, default value is a line feed.
	 * @return	void
	 * @todo	Let the "displayer" of these headers handle indent and separator - code layout doesn't belong here
	 */
	function AddCustomHeader($additional_headers, $indent = "\t", $sep = "\n")
	{
		$this->additional_headers[] = $indent.$additional_headers.$sep;
	}

	/**
	 * USERS
	 */

	/**
	 * Authenticate a user from (persistent) cookies.
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::LoadAll()
	 *
	 * @return	boolean	TRUE if user authenticated from cookie, FALSE if not
	 */
	function authenticateUserFromCookies()
	{
		// init
		$result = NULL;
		$c_username	= $this->getWikkaCookie('user_name');
		$c_pass		= $this->getWikkaCookie('pass');
		// find user(s)
		$users = $this->LoadAll("
			SELECT *
			FROM ".$this->GetConfigValue('table_prefix')."users
			WHERE name = '".mysql_real_escape_string($c_username)."'"
			);
		// evaluate result
		if (is_array($users))
		{
			$count = count($users);
		}
		switch (TRUE)
		{
			case (FALSE === $users):
				$result = FALSE;		// query failed!!	@@@ notify admin
				break;
			case ($count > 1):
				$result = FALSE;		// multiple users by same name: DB error!!	@@@ notify admin
				break;
			case ($count == 0):
				$result = FALSE;		// not a registered user
				break;
			default:					// $count == 1 - OK: one user found
				break;
		}
		// OK so far, check password
		if (NULL === $result)
		{
			$user_rec = $users[0];		// get first (single) row
			if (isset($user_rec['challenge']) && isset($user_rec['password']))
			{
				$pwd = md5($user_rec['challenge'].$user_rec['password']);
				if ($c_pass != $pwd)
				{
					$result = FALSE;	// "No, not authenticated"
				}
				else
				{
					// valid password supplied: $user data is authenticated:
					// cache username and login user
					$result = TRUE;
					$this->registered_users[] = $user_rec['name'];	// cache actual name as in DB
					$this->loginUser($user_rec);
				}
			}
			else
			{
				$result = FALSE;		// incomplete record: DB error!!
			}
		}
		return $result;					// will be either TRUE or FALSE
	}
	/**
	 * Load data for a given user (by name).
	 *
	 * Attempts to load the user data from the database, and if successful,
	 * adds the user name to the registered user name cache.
	 *
	 * If the data was successfully retrieved, the user data is returned
	 * in an array; if not, FALSE is returned.
	 *
	 * @uses	Wakka::registered_users
	 * @uses	Wakka::LoadSingle()
	 * @uses	Wakka::GetConfigValue()
	 *
	 * @param	string	$username	mandatory: user name to retrieve data for
	 * @return	mixed	array with user data if successful, FALSE otherwise
	 */
	function loadUserData($username)
	{
		// data retrieval by name: get from database
		$user = $this->LoadSingle("
			SELECT *
			FROM ".$this->GetConfigValue('table_prefix')."users
			WHERE name = '".mysql_real_escape_string($username)."'
			LIMIT 1"
			);
		if (is_array($user))
		{
			// store user name in cache
			$this->registered_users[] = $user['name'];	// cache actual name as in DB
		}
		// return results
		return $user;
	}
	/**
	 * Check whether a given (or implied) user is (currently) registered.
	 *
	 * If no username is supplied, it simply returns the current "registered"
	 * state from the object variable. It also maintains a "cache" of registered
	 * usernames which is checked before resorting to a database query.
	 *
	 * @uses	Wakka::registered
	 * @uses	Wakka::registered_users
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::LoadSingle()
	 *
	 * @param	string	$username	optional: when omitted, "registered" state
	 *					for current user is returned; when given, we check whether
	 *					the username occurs in the cache or database.
	 * @return	boolean	TRUE is user is registered, FALSE otherwise
	 */
	function existsUser($username=NULL)
	{
		global $debug;
		// init
		$result = FALSE;
		// looking for current user
		if (!is_string($username))
		{
if ($debug) echo 'existsUser - no parameter (current user): ';
			$result = $this->registered;
if ($debug) print(($result) ? 'TRUE' : 'FALSE');
		}
		// named user cached?
		elseif (in_array($username, $this->registered_users))
		{
			$result = TRUE;
if ($debug) echo 'existsUser - name '.$username.' cached: TRUE';
		}
		elseif (in_array($username,$this->anon_users))
		{
			$result = FALSE;
if ($debug) echo 'existsUser - anon. name '.$username.' cached: FALSE';
		}
		// look up named user in database & cache name
		else
		{
			$user = $this->LoadSingle("
				SELECT `name`
				FROM ".$this->GetConfigValue('table_prefix')."users
				WHERE `name` = '".mysql_real_escape_string($username)."'
				LIMIT 1"
				);
			if (is_array($user))
			{
if ($debug) echo 'existsUser - '.$username.' found in DB: TRUE';
				$result = TRUE;
				$this->registered_users[] = $user['name'];	// cache actual name as in DB
			}
			else
			{
if ($debug) echo 'existsUser - '.$username.' not found in DB: FALSE';
				// also cache UNregistered usernames
				$this->anon_users[] = $username;		// @@@ declare & document
			}
		}
if ($debug) echo "<br/>\n";
		return $result;
	}
	/**
	 * Load a given user - OBSOLETE.
	 *
	 * <b>Replaced by {@link Wakka::authenticateUserFromCookies()},
	 * {@link Wakka::existsUser()} or {@link Wakka::loadUserData()} depending on
	 * purpose!</b>
	 *
	 * ***Returns FALSE while still existing for reference.***
	 *
	 * <p>If a second parameter $password is supplied, this method checks if this password is valid, thus a FALSE return value would mean
	 * nonexistent user or invalid password. Note that this parameter is the <strong>hashed value</strong> of the password usually typed in
	 * by user plus a "challenge" known only to the system, and not the password itself.</p>
	 * <p>If the password parameter is not supplied, it checks only for existence of the username, and returns an array containing all information
	 * about the given user if it exists, or a FALSE value. In this latter case, result is cached in $this->specialCache in order to
	 * improve performance.</p>
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::LoadSingle()
	 *
	 * @param	string	$name	mandatory: name of the user
	 * @param	string	$password	optional: password of the user. default: 0 (=none)
	 * @return	mixed	the data of the user, or FALSE if non-existing user or invalid password supplied.
	 * @todo	use empty string (or NULL) for password default rather than 0 (no "magic numbers"!) - real passwords cannot be empty anyway
	 * @todo	this method has two different functionalities - split into separate methods! #542
	 */
	function LoadUser($name, $password = 0)
	{
		/*
		if (($password === 0) && (isset($this->specialCache['user'][strtolower($name)])))
		{
			// data retrieval by name: get from cache
			$user = $this->specialCache['user'][strtolower($name)];
		}
		else
		{
			// data retrieval by name: get from database - OR- authentication
			// @@@ don't use LIMIT but validate whether we really get one row back!
			$user = $this->LoadSingle("
				SELECT *
				FROM ".$this->GetConfigValue('table_prefix')."users
				WHERE name = '".mysql_real_escape_string($name)."'
				LIMIT 1"
				);
			// @@@ use LoadAll() instead and *set* to FALSE if 1) nothing was found (empty array) or 2) multiple rows are returned: that would be invalid!

			if (is_array($user))
			{
				// authentication of user: password must match username
				// @@@ do the password check only if we got a single, valid user!
				if ($password !== 0)
				{
					$pwd = md5($user['challenge'].$user['password']);	// @@@ this will error/notice if $user is an *empty* array!
					if ($password != $pwd)
					{
						#$user = NULL;
						$user = FALSE;	// "No, not authenticated"
					}
					else
					{
						// valid password supplied: $user data is authenticate
						// @@@ store validated user data in cache?
					}
				}
				else
				{
					// data retrieval by name only: store user data retrieved from database in cache
					$this->specialCache['user'][strtolower($name)] = $user;
				}
			}
		}
		return $user;
		*/
		return FALSE;
	}
	/**
	 * Load all users registered at the wiki from the database.
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::LoadAll()
	 *
	 * @return	array	contains data for all users
	 * $todo	add 'start' and 'max' parameters to support paging
	 */
	function LoadUsers()
	{
		$users = $this->LoadAll("
			SELECT *
			FROM ".$this->GetConfigValue('table_prefix')."users
			ORDER BY name"
			);
		return $users;
	}
	/**
	 * Get the name or (IP/hostname) address of the current user.
	 *
	 * If the user is not logged-in, the host name is only looked up if enabled
	 * in the config (since it can lead to long page generation times).
	 * Set 'enable_user_host_lookup' in wikka.config.php to 1 to do the look-up.
	 * Otherwise the ip-address is used.
	 *
	 * @uses	Wakka::reg_username
	 * @uses	Wakka::existsUser()
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::getAnonUserName()
	 *
	 * @return	string	name of registered user, or IP address or host name for
	 *			anonymous user
	 * @todo	return only IP address or host name if explicitly requested:
	 *			we may want IP address even if reverse DNS is allowed in config!
	 */
	function GetUserName()
	{
		global $debug;
		$name = '';
		#if ($user = $this->GetUser())
if ($debug) echo 'GetUserName calling... ';
		if ($this->existsUser())
		{
			#$name = $user['name'];
			$name = $this->reg_username;
		}
		else
		{
			/*
			$ip = $_SERVER['REMOTE_ADDR'];
			if ($this->GetConfigValue('enable_user_host_lookup') == 1)
			{
				$name = gethostbyaddr($ip) ? gethostbyaddr($ip) : $ip;
			}
			else
			{
				$name = $ip;
			}
			*/
			$name = $this->getAnonUserName();	// @@@
		}
		return $name;
	}
	/**
	 * Get (and cache) anonymous user "name" for current user.
	 *
	 * Depending on configuration settings, the "name" can be either an IP
	 * address pr a host name found by reverse DNS lookup.
	 *
	 * @uses	Wakka::GetConfigValue()
	 *
	 * @return	string	name found
	 * @todo	extend cache if we specify IP *or* hostname
	 */
	function getAnonUserName()
	{
		if (isset($this->anon_username))
		{
			// get name from cache
			$name = $this->anon_username;
		}
		else
		{
			// lookup name and cache it
			$ip = $_SERVER['REMOTE_ADDR'];
			if ((bool) $this->GetConfigValue('enable_user_host_lookup'))
			{
				$name = gethostbyaddr($ip) ? gethostbyaddr($ip) : $ip;
			}
			else
			{
				$name = $ip;
			}
			$this->anon_username = $name;
		}
		// return name found
		return $name;
	}
	/**
	 * Get data for logged-in user (FALSE if user is not logged in).
	 *
	 * @return	mixed	array with user data, or FALSE if user not logged in
	 */
	function GetUser()
	{
		// NOTE: we use empty() here which will return the same result whether
		// the session variable does not exist (isset) or whether it contains NULL,
		// without generating a warning.
		// this is to get round the possibility that unset() doesn't leave
		// a global variable unset if used from within a function. See technical
		// note in logoutUser() docblock.
		$user = (empty($_SESSION['user'])) ? FALSE : $_SESSION['user'];
		return $user;
	}
	/**
	 * Log-in a given user.
	 *
	 * The procedure starts with updating the user's record in the database with
	 * a new 'challenge'.
	 * If the database update is successful, the user data are stored in the
	 * session, whereas name and password (the latter using the new challenge)
	 * are stored in a cookie. In addition, registered user name is stored in an
	 * object variable, and the "registered" flag is set to TRUE, for fast state
	 * retrieval by other methods.
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::Query()
	 * @uses	Wakka::setWikkaCookie()
	 *
	 * @param	array	$user	mandatory: must contain the user data
	 * @return	boolean	result code, TRUE if DB successfully updated, FALSE
	 */
	function loginUser($user)
	{
		// init: Choosing an arbitrary challenge that the DB server only knows.
		$user['challenge'] = dechex(crc32(rand()));
		// login
		$rc = $this->Query("
			UPDATE ".$this->GetConfigValue('table_prefix')."users
			SET `challenge` = '".$user['challenge']."'
			WHERE `name` = '".mysql_real_escape_string($user['name'])."'"
			);
		if ($rc)
		{
			// set local data
			// NOTE: data comes from loadUserData() or authenticateUserFromCookies()
			// which already update registered_users cache
			$this->reg_username = $user['name'];
			$this->registered = TRUE;
			// set session
			$_SESSION['user'] = $user;
			// set cookies
			$this->setWikkaCookie('user_name', $user['name']);
			$this->setWikkaCookie('pass', md5($user['challenge'].$user['password']));
		}
		// return result code
		return $rc;
	}
	/**
	 * OBSOLETE: use {@link Wakka::loginUser()}.
	 *
	 * @param	array	$user
	 */
	function SetUser($user)
	{
		/*
		// init: Choosing an arbitrary challenge that the DB server only knows.
		$user['challenge'] = dechex(crc32(rand()));
		// login
		$_SESSION['user'] = $user;
		$this->setWikkaCookie('user_name', $user['name']);
		$this->Query("
			UPDATE ".$this->GetConfigValue('table_prefix')."users
			SET `challenge` = '".$user['challenge']."'
			WHERE `name` = '".mysql_real_escape_string($user['name'])."'"
			);
		$this->setWikkaCookie('pass', md5($user['challenge'].$user['password']));
		*/
		$this->loginUser($user);
	}
	/**
	 * Log-out the current user.
	 *
	 * We start by creating a new challenge and updating the user record in the
	 * database. If the update is successful, the "registered" flag is updated
	 * (note that we don't attempt to remove the username from the registered
	 * usernames cache: the user by this name is still a regitered user after
	 * all!). Also, user data are removed from the session and name and password
	 * cookies are deleted.
	 *
	 * Technical note:<br/>
	 * According to {@link http://php.net/unset} it is not possible to unset a
	 * global variable from within a function (and still have it unset after the
	 * function has run) but in my testing variables in the $_COOKIE array as
	 * well as variables in the $_SESSION array can be unset from within a
	 * function and remain unset. This is also independent of the setting for
	 * register_globals. Just to be sure, we set the value to NULL (after which
	 * it still exists), before unsetting it; this is in case PHP in a different
	 * version or on a different platform behaves differently from mine. -- JW
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::GetUserName()
	 * @uses	Wakka::Query()
	 * @uses	Wakka::deleteWikkaCookie()
	 *
	 * @return	boolean	result code: TRUE if database was successfully updated and
	 *					user logged out, FALSE otherwise (and user not logged out)
	 */
	function LogoutUser()
	{
		// init: Choosing an arbitrary challenge that the DB server only knows.
		$user['challenge'] = dechex(crc32(rand()));
		// database
		$rc = $this->Query("
			UPDATE ".$this->GetConfigValue('table_prefix')."users
			SET `challenge` = '".$user['challenge']."'
			WHERE `name` = '".mysql_real_escape_string($this->GetUserName())."'"
			);
		if ($rc)
		{
			// object data
			$this->registered = FALSE;
			// session
			$_SESSION['user'] = NULL;			// just in case
			unset($_SESSION['user']);
			$_SESSION['show_comments'] = NULL;	// just in case
			unset($_SESSION['show_comments']);	// is set in show handler
			// cookies
			$this->deleteWikkaCookie('user_name');
			$this->deleteWikkaCookie('pass');
		}
		return $rc;
	}
	/**
	 * Returns user comment default style.
	 *
	 * If the user is not logged-in, comments are hidden by default.
	 *
	 * Must test for false condition with
	 * "FALSE===UserWantsComments()" since this function may also
	 * legally return a zero value.
	 *
	 * @uses	Wakka::GetUser()
	 *
	 * @param	tag		Page title
	 * @return	mixed	threadtype if the user wants comments, FALSE otherwise
	 */
	function UserWantsComments($tag)
	{
		if (!$user = $this->GetUser())
		{
			$showcomments = FALSE;
		}
		elseif (!isset($user['show_comments'][$tag]))
		{
			if (isset($user['default_comment_display']))
			{
				$showcomments = $user['default_comment_display'];	// user's default comment display
			}
			elseif (isset($config['default_comment_display']))
			{
				$showcomments = $config['default_comment_display'];	// configured default comment display
			}
			else
			{
				$showcomments = COMMENT_ORDER_DATE_ASC;				// system default comment display
			}
		}
		else
		{
			$showcomments = $user['show_comments'][$tag];			// user's preference for the given page
		}
		return $showcomments;
	}
	 /**
	 * Formatter for user names.
	 *
	 * Renders usernames as links only when needed, avoiding the creation of
	 * missing page links for users without a userpage. Makes other options
	 * configurable (like truncating long hostnames or disabling link formatting).
	 *
	 * @author	{@link http://wikkawiki.org/DarTar Dario Taraborelli}
	 *
	 * @uses	Wakka::existsUser()
	 * @uses	Wakka::ExistsPage()
	 * @uses	Wakka::Link()
	 *
	 * @param	string	$username	mandatory: name of user or hostname retrieved from the DB;
	 * @param	boolean	$link	optional: enables/disables linking to userpage;
	 * @param	string	$maxhostlength	optional: max length for hostname, hostnames longer
	 *					then this will be truncated with an ellipsis;
	 * @param	string	$ellipsis	optional: character (or string) to be used at the end of truncated hosts;
	 * @return	string	$formatted_user: formatted username.
	 * @todo	use constant for ellipsis
	 * @todo	better title attribute text: a user page is not a 'profile'
	 * @todo	internationalization (marked with #i18n)
	 */
	function FormatUser($username, $link=TRUE, $maxhostlength=MAX_HOSTNAME_LENGTH_DISPLAY, $ellipsis='&#8230;')
	{
		global $debug;
		if (strlen($username) > 0)
		{
			// check if user is registered
			#if ($this->LoadUser($username))	// only checks if user is registered
if ($debug) echo 'FormatUser calling... ';
			if ($this->existsUser($username))
			{
				// check if userpage exists and if linking is enabled
				#$formatted_user = ($this->ExistsPage($username) && ($link == 1)) ? $this->Link($username,'','','','','Open user profile for '.$username,'user') : '<span class="user">'.$username.'</span>'; // @@@ #i18n
				$formatted_user = ($this->ExistsPage($username) && ((bool) $link)) ? $this->Link($username,'','','','','Open user profile for '.$username,'user') : '<span class="user">'.$username.'</span>'; // @@@ #i18n
			}
			else
			{
				// user is not registered (or no longer(!) e.g., user may have
				// edited a page but since "unregistered": then we have a user
				// name here, not a host name)
				// truncate long (host) names
				$formatted_user = (strlen($username) > $maxhostlength) ? '<span class="user_anonymous" title="'.$username.'">'.substr($username, 0, $maxhostlength).$ellipsis.'</span>' : '<span class="user_anonymous">'.$username.'</span>';
			}
		}
		else
		{
			// no user (page has empty user field)
			$formatted_user = 'anonymous'; // @@@ #i18n WIKKA_ANONYMOUS_AUTHOR_CAPTION or WIKKA_ANONYMOUS_USER
		}
		return $formatted_user;
	}

	/**
	 * COMMENTS
	 */

	/**
	 * Load the comments for a (given) page.
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::LoadAll()
	 * @uses	Wakka::TraverseComments()
	 *
	 * @param	string	$tag	mandatory: name of the page
	 * @param	integer	$order	optional: order of comments. Default: COMMENT_ORDER_DATE_ASC
	 * @return	array	All the comments for this page ordered by $order
	 * @todo	make single exit point to enable profiling
	 */
	function LoadComments($tag, $order=NULL)
	{
		// default
		if ($order == NULL)
		{
			if (isset($_SESSION['show_comments'][$tag]))
			{
				$order = $_SESSION['show_comments'][$tag];
			}
			else
			{
				$order = COMMENT_ORDER_DATE_ASC;
			}
		}
		// handle requested order
		if ($order == COMMENT_ORDER_DATE_ASC)	// Return ASC by date
		{
			// always returns an array, but it may be empty
			return $this->LoadAll("
				SELECT *
				FROM ".$this->GetConfigValue('table_prefix')."comments
				WHERE page_tag = '".mysql_real_escape_string($tag)."'
					AND (status IS NULL or status != 'deleted')
				ORDER BY time"
				);
		}
		elseif ($order == COMMENT_ORDER_DATE_DESC)
		{
			// always returns an array, but it may be empty
			return $this->LoadAll("
				SELECT *
				FROM ".$this->GetConfigValue('table_prefix')."comments
				WHERE page_tag = '".mysql_real_escape_string($tag)."'
					AND (status IS NULL or status != 'deleted')
				ORDER BY time DESC"
				);
		}
		elseif ($order == COMMENT_ORDER_THREADED)
		{
			$record = array();
			$this->TraverseComments($tag, $record);
			return $record;
		}
	}
	/**
	 * Traverse comments in threaded order
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::LoadAll()
	 * @uses	Wakka::CountAllComments()
	 * @uses	Wakka::LoadSingle()
	 * @uses	Wakka::TraverseComments()
	 *
	 * @param	string	$tag	mandatory: name of the page
	 * @param	array	&$graph	mandatory: empty array
	 * @return	array	Ordered graph of comments and indent levels (values) for this page
	 */
	function TraverseComments($tag, &$graph)
	{
		static $level = -1;
		static $visited = array();
		static $transformed_map = array();
		if (!$transformed_map)
		{
			array_push($visited, 'NULL');
			$count = $this->CountAllComments($tag);	// redundant: just count($initial_map) after the query
			// @@@ miss option for sort order here???
			$initial_map = $this->LoadAll("
				SELECT id, parent
				FROM ".$this->GetConfigValue('table_prefix')."comments
				WHERE page_tag = '".$tag."'
				ORDER BY id ASC"
				);
			// Create an array of arrays, with the (unique) key of
			// 'parent' pointing to an array of date-ordered
			// children.
			for ($i=0; $i<$count; ++$i)	// prefer to use $i++ here (even if equivalent)
			{
				$id = $initial_map[$i]['id'];
				$parent = $initial_map[$i]['parent'];
				if (!$parent)
				{
					$parent = 'NULL';
				}
				if (!array_key_exists($parent, $transformed_map))
				{
					$transformed_map[$parent] = array();
				}
				array_push($transformed_map[$parent], $id);
			}
		}
		if (is_array($transformed_map[end($visited)]))	// @@@ causes NOTICE
		{
			$id = array_shift($transformed_map[end($visited)]);
		}
		if (isset($id))
		{
			// Limit recursions to COMMENT_MAX_TRAVERSAL_DEPTH
			if ($level >= COMMENT_MAX_TRAVERSAL_DEPTH)
			{
				--$level;
				array_pop($visited);
				$this->TraverseComments($tag, $graph);
			}
			else
			{
				// Traverse children
				++$level;
				array_push($visited, $id);
				// @@@	should check first whether LoadSingle() actually returns an
				//		array, or FALSE in case the query fails (not found).
				//		most of the other statements should probably not be
				//		executed either if no result was returned from the database!
				// @@@	can't the records be retrieved from $transformed_map instead?
				$graph[] = $this->LoadSingle("
					SELECT *
					FROM ".$this->GetConfigValue('table_prefix')."comments
					WHERE id = ".$id
					);
				end($graph);
				$graph[key($graph)]['level'] = $level;
				$this->TraverseComments($tag, $graph);
			}
		}
		elseif ($level < 0)
		{
			// End traversal
			return;
		}
		else
		{
			// Step back to the parent to find next child
			--$level;
			array_pop($visited);
			$this->TraverseComments($tag, $graph);
		}
	}
	/**
	 * Count the undeleted comments for a (given) page.
	 *
	 * @uses	Wakka::getCount()
	 *
	 * @param	string $tag mandatory: name of the page
	 * @return	integer Count of comments
	 */
	function CountComments($tag)
	{
		$count = $this->getCount('comments', "page_tag = '".mysql_real_escape_string($tag)."' AND (status IS NULL OR status != 'deleted')");
		return $count;
	}
	/**
	 * Count all comments (deleted and undeleted) for a (given) page.
	 *
	 * @uses	Wakka::getCount()
	 *
	 * @param	string $tag mandatory: name of the page
	 * @return	integer Count of comments
	 */
	function CountAllComments($tag)
	{
		$count = $this->getCount('comments', "page_tag = '".mysql_real_escape_string($tag)."'");
		return $count;
	}
	/**
	 * Load the last comments on the wiki, or, if specified, the last comments on a specific page.
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::LoadAll()
	 *
	 * @param	integer	$limit	optional: number of last comments. default: 50
	 * @param	string	$tag	optional: name of page to retrieve comments for
	 * @return	array	the last x comments
	 * @todo	use constant for default limit value (no "magic numbers!")
	 */
	function LoadRecentComments($limit=50, $tag='')		// @@@
	{
		$limit = (int) $limit;
		if ($limit < 1)
		{
			$limit = 50;		// @@@
		}
		#$recentcomments = $this->LoadAll('SELECT * FROM '.$this->GetConfigValue('table_prefix').'comments'.$where.' AND (status IS NULL or status != \'deleted\') ORDER BY time DESC LIMIT '.$limit);
		$wheretag = ('' == $tag) ? '' : "tag = '".mysql_real_escape_string($tag)."'";
		$wherestatus = "(status IS NULL OR status != 'deleted')";
		$where = ('' == $wheretag) ? $wherestatus : $wheretag.' AND '.$status;
		$query = "
			SELECT *
			FROM ".$this->GetConfigValue('table_prefix')."comments
			WHERE ".$where."
			ORDER BY time DESC
			LIMIT ".$limit;
		$recentcomments = $this->LoadAll($query);
		return $recentcomments;
	}
	/**
	 * Load recently commented pages on the wiki.
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::LoadAll()
	 *
	 * @param	integer	$limit	optional: number of last comments on different pages. default: 50
	 * @return	array	the last comments on x different pages
	 * @todo	use constant for default limit value (no "magic numbers!")
	 */
	function LoadRecentlyCommented($limit = 50)	// @@@
	{
		$limit = (int) $limit;
		if ($limit < 1)
		{
			$limit = 50;		// @@@
		}
		$sql = "
			SELECT comments.id, comments.page_tag, comments.time, comments.comment, comments.user
			FROM ".$this->GetConfigValue('table_prefix')."comments AS comments
			LEFT JOIN ".$this->GetConfigValue('table_prefix')."comments AS c2
				ON comments.page_tag = c2.page_tag
					AND comments.id < c2.id
			WHERE c2.page_tag IS NULL
				AND (comments.status IS NULL OR comments.status != 'deleted')
			ORDER BY time DESC
			LIMIT ".$limit;
		$pages = $this->LoadAll($sql);
		return $pages;
	}
	/**
	 * Save a given comment posted on a given page.
	 *
	 * @uses	Wakka::GetUserName()
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::Query()
	 *
	 * @param	string	$page_tag	mandatory: name of the page
	 * @param	string	$comment	mandatory: text of the comment
	 * @param	mixed	$parent_id	optional:	integer id of parent comment
	 */
	function SaveComment($page_tag, $comment, $parent_id)
	{
		// get current user
		$user = $this->GetUserName();

		// add new comment
		#$parent_id = mysql_real_escape_string($parent_id);		# should be sanitized as integer, not string JW/2007-07-10
		if (!$parent_id)
		{
			$parent_id = 'NULL';
		}
		else
		{
			$parent_id = (int)$parent_id;
		}
		$this->Query("
			INSERT INTO ".$this->GetConfigValue('table_prefix')."comments
			SET page_tag = '".mysql_real_escape_string($page_tag)."',
				time = now(),
				comment = '".mysql_real_escape_string($comment)."',
				parent = ".$parent_id.",
				user = '".mysql_real_escape_string($user)."'"
			);
	}

	/**
	 * ACCESS CONTROL
	 */

	/**
	 * Check if current user is the owner of the current or a specified page.
	 *
	 * @access		public
	 * @uses		Wakka::existsUser()
	 * @uses		Wakka::IsAdmin()
	 * @uses		Wakka::GetUserName()
	 * @uses		Wakka::GetPageOwner()
	 *
	 * @param	string	$tag	optional: page to be checked. Default: current page.
	 * @return	boolean	TRUE if the user is the owner, FALSE otherwise.
	 */
	function UserIsOwner($tag='')
	{
		global $debug;
		$isowner = FALSE;		// default
		// if not logged in, user can't be owner!
		#if (!$this->GetUser())
if ($debug) echo 'UserIsOwner calling... ';
		if (!$this->existsUser())
		{
			$isowner = FALSE;
		}
		// if user is admin, return true. Admin can do anything!
		elseif ($this->IsAdmin())
		{
			$isowner = TRUE;
		}
		else
		{
			// set default tag
			$tag = trim($tag);
			if (empty($tag))
			{
				#$tag = $this->GetPageTag();
				$tag = $this->tag;
			}
			// check if user is owner
			#if ($this->GetPageOwner($tag) == $this->GetUserName())
			if ($this->GetPageOwner($tag) == $this->reg_username) // anon user can't be owner
			{
				$isowner = TRUE;
			}
		}
		return $isowner;
	}
	/**
	 * Check if currently logged in user is listed in configuration list as admin.
	 *
	 * @access	public
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::GetUserName()
	 *
	 * @return	boolean	TRUE if the user is an admin, FALSE otherwise
	 */
	function IsAdmin()
	{
		$isadmin = FALSE;
		// use preg_split to get an array with already-trimmed elements (no looping needed)
		$adminarray = preg_split('/\s*,\s*/', trim($this->GetConfigValue('admin_users')), -1, PREG_SPLIT_NO_EMPTY);

		// only a logged-in user can be admin; check if name occurs in the array
		if ($this->existsUser() && in_array($this->reg_username, $adminarray))
		{
			$isadmin = TRUE;
		}
		return $isadmin;
	}
	/**
	 * Return the owner for a given or the current page, with a given revision time or the current version.
	 *
	 * @uses	Wakka::GetPageTag()
	 * @uses	Wakka::LoadPage()
	 *
	 * @param	string	$tag	optional: name of the page. default: current page
	 * @param	string	$time	optional: time (datetime format) of the page-revision. default: current version
	 * @return	string	username of the owner of the page (empty if there is no owner)
	 * @todo	make a more efficient query: we only need the owner column, not the whole page!
	 */
	function GetPageOwner($tag='', $time='')
	{
		$owner = '';
		$tag = trim($tag);
		if (empty($tag))
		{
			#$tag = $this->GetPageTag();
			$tag = $this->tag;
		}
		if ($page = $this->LoadPage($tag, $time))		// @@@ we don't need the whole page!
		{
			$owner = $page['owner'];
		}
		return $owner;
	}
	/**
	 * Set page ownership of specified page to specified owner.
	 *
	 * @uses	Wakka::LoadUser()
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::Query()
	 *
	 * @param	string	$tag	mandatory: name of the page
	 * @param	string	$user	mandatory: name of the user
	 * @todo	see if "(Public)" and "(Nobody)" have to be replaced by constants to allow i18n
	 * 			JW: could keep these constants in the database but 'translate' them in the UI
	 */
	function SetPageOwner($tag, $user)
	{
		// check if user exists
		if ('' != $user && ($this->LoadUser($user) || $user == '(Public)' || $user == '(Nobody)'))
		{
			if ($user == '(Nobody)')
			{
				$user = '';
			}
			// update latest revision with new owner
			$this->Query("
				UPDATE ".$this->GetConfigValue('table_prefix')."pages
				SET owner = '".mysql_real_escape_string($user)."'
				WHERE tag = '".mysql_real_escape_string($tag)."'
					AND latest = 'Y'
				LIMIT 1"
				);
		}
	}
	/**
	 * Load the Access Control list for a given page and a given privilege.
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::LoadSingle()
	 *
	 * @param	string	$tag	mandatory:
	 * @param	string	$privilege	mandatory:
	 * @param	integer	$useDefaults	optional:
	 * @return	mixed	the page name and the acl or FALSE if not found
	 * @todo	don't use numbers when booleans are intended! TRUE and FALSE advertize their intention much clearer
	 * @todo	this should return a result in consistent form (no page_tag for
	 *			default, or included for DB result), with the ACL itself "normalized"
	 *			with only newline delimiters #226/comment8
	 * @todo	make this return JUST an acl (normalized), not an array!
	 */
	function LoadACL($tag, $privilege, $useDefaults = 1)	// @@@
	{
		$acl = $this->LoadSingle("
			SELECT ".mysql_real_escape_string($privilege)."_acl
			FROM ".$this->GetConfigValue('table_prefix')."acls
			WHERE `page_tag` = '".mysql_real_escape_string($tag)."'
			LIMIT 1"
			);
		if (FALSE === $acl && $useDefaults)
		{
			$acl = array(
				'page_tag' => $tag,			// @@@ when is this needed? NEVER
				$privilege.'_acl' => $this->GetConfigValue('default_'.$privilege.'_acl')
				);
		}
		// @@@ normalize ACL before returning
		return $acl;
	}
	/**
	 * Load all Access Control lists for a given page.
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::LoadSingle()
	 *
	 * @param	string	$tag	mandatory: page to load ACLs for
	 * @param	integer	$useDefaults	optional:
	 * @return	mixed	the page name and all acls or FALSE if not found
	 * @todo	don't use numbers when booleans are intended! TRUE and FALSE advertize their intention much clearer
	 * @todo	this should return a result with the ACLs "normalized" with only newline delimiters #226 comment 8
	 * @todo	review usage: is page_tag really needed?
	 * @todo	make function for retrieving (normalized!) defaults (using current list of ACLs)
	 */
	function LoadAllACLs($tag, $useDefaults=1)	// @@@
	{
		$acl = $this->LoadSingle("
			SELECT *
			FROM ".$this->GetConfigValue('table_prefix')."acls
			WHERE `page_tag` = '".mysql_real_escape_string($tag)."'
			LIMIT 1"
			);
		if (FALSE === $acl && $useDefaults)
		{
			$acl = array(
				'page_tag' => $tag,
				'read_acl' => $this->GetConfigValue('default_read_acl'),
				'write_acl' => $this->GetConfigValue('default_write_acl'),
				'comment_read_acl' => $this->GetConfigValue('default_comment_read_acl'),
				'comment_post_acl' => $this->GetConfigValue('default_comment_post_acl')
				);
				// @@@ normalize ACLs
		}
		return $acl;
	}
	/**
	 * Save an Access Control List for a given privilege on a given page to the database.
	 * If the ACL record doesn't already exist, it is first created with the
	 * config defaults and updated with the passed privilege values.
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::LoadAllACLs()
	 * @uses	Wakka::Query()
	 * @uses	Wakka::SaveACL()
	 *
	 * @param	string	$tag	mandatory: name of the page
	 * @param	string	$privilege	mandatory: name of the privilege
	 * @param	string	$list	mandatory: a string containing the AC-Syntax
	 * @todo	don't use numbers when booleans are intended! TRUE and FALSE advertize their intention much clearer
	 * @todo	make function for retrieving (normalized!) defaults (using current list of ACLs)
	 * @todo	rationalize combination with CloneACLs - too much duplication here
	 */
	function SaveACL($tag, $privilege, $list)
	{
		$insert = FALSE;
		if (!$acls = $this->LoadAllACLs($tag, 0))	// @@@
		{
			// Load defaults
			// @@@ JW: use LoadAllACLs() again here with useDefaults set to TRUE, to keep the list of necessary fields in one place only.
			//		better yet: use a function to get all defaults (to be used by LoadAllACLs as well)
			$insert = TRUE;
			$acls['read_acl'] = $this->GetConfigValue('default_read_acl');
			$acls['write_acl'] = $this->GetConfigValue('default_write_acl');
			$acls['comment_read_acl'] = $this->GetConfigValue('default_comment_read_acl');
			$acls['comment_post_acl'] = $this->GetConfigValue('default_comment_post_acl');
			// @@@ normalize ACLs
		}

		// update with specified privilege
		$priv = mysql_real_escape_string($privilege).'_acl';
		$acls[$priv] = mysql_real_escape_string(trim(str_replace('\r', '', $list)));	// @@@ normalize line endings
		if (!$insert)
		{
			// update record
			$this->Query("
				UPDATE ".$this->GetConfigValue('table_prefix')."acls
				SET `".$priv."` = '".$acls[$priv]."'
				WHERE `page_tag` = '".mysql_real_escape_string($tag)."'
				LIMIT 1"
				);
		}
		else
		{
			// @@@ make into function! buildSQLValueList()
			// build values list
			$acl_list = '';
			foreach ($acls as $acl => $value)
			{
				$acl_list .= ('' == $acl_list) ? '' : ",\n";	// use newline rather than space for more readable query layout
				$acl_list .= "`".$acl."` = '".mysql_real_escape_string($value)."'";
			}

			// add record
			$this->Query("
				INSERT INTO ".$this->GetConfigValue('table_prefix')."acls
				SET `page_tag` = '".mysql_real_escape_string($tag)."',".
					$acl_list
				);
		}
	}
	/**
	 * Clone all Access Control Lists from one page to another. If ACL
	 * lists aren't defined for the source page, use defaults from
	 * config for the destination page.	JW: why not leave it as defaults then??
	 *
	 * @uses	Wakka::LoadAllACLs()
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::Query()
	 *
	 * @param	string	$from_tag	mandatory: Source page for ACLs
	 * @param	string	$to_tag		mandatory: Target page for ACLs
	 * @todo	don't use numbers when booleans are intended! TRUE and FALSE advertize their intention much clearer
	 * @todo	rationalize combination with SaveACL - too much duplication here
	 */
	function CloneACLs($from_tag, $to_tag)
	{
		$acls = $this->LoadAllACLs($from_tag, 1);	// @@@

		// @@@ JW: make this into a function!
		// build values list
		$acl_list = '';
		foreach ($acls as $acl => $value)
		{
			if ($acl === 'page_tag') continue;		// @@@ would not be needed if LoadAllACLs would return just the ACLs, not the page_tag
			$acl_list .= ('' == $acl_list) ? '' : ",\n";	// use newline rather than space for more readable query layout
			$acl_list .= "`".$acl."` = '".mysql_real_escape_string($value)."'";
		}

		if ($this->LoadAllACLs($to_tag, 0))	// @@@
		{
			// update record
			$this->Query("
				UPDATE ".$this->GetConfigValue('table_prefix')."acls
				SET ".$acl_list."
				WHERE `page_tag` = '".mysql_real_escape_string($to_tag)."'
				LIMIT 1"
				);
		}
		else
		{
			// add record
			$this->Query("
				INSERT INTO ".$this->GetConfigValue('table_prefix')."acls
				SET `page_tag` = '".mysql_real_escape_string($to_tag)."',".
					$acl_list
				);
		}
	}

	/**
	 * Split ACL list on pipes or commas, then trim any
	 * whitespace. Return a pipe-delimited list. Used mainly
	 * to remove carriage returns.
	 *
	 * @param	string	$list	mandatory: List of ACLs to trim
	 * @todo	Using whitespace as delimiter will break things: standardize on
	 *			returning newline-delimited list;
	 *			see {@link http://wush.net/trac/wikka/ticket/226#comment:8}
	 * @todo	rename to NormalizeACL() (it treats only a single ACL)
	 */
	function TrimACLs($acl)
	{
		foreach (preg_split('/[|,]+/', $acl) as $line)
		{
			$line = trim($line);
			$trimmed_list .= $line.'|';
		}
		return substr($trimmed_list, 0, -1);
	}

	/**
	 * Determine if the (current) user has specified access for the specified page.
	 *
	 * Returns true if $username (defaults to current user) has $privilege
	 * access on $page (defaults to current page).
	 *
	 * @uses	Wakka::ACLs
	 * @uses	Wakka::existsUser()
	 * @uses	Wakka::UserIsOwner()
	 * @uses	Wakka::LoadACL()
	 *
	 * @param	string	$privilege	mandatory: privilege which shall be checked
	 * @param	string	$tag	optional: name of the page default: current page
	 * @param	string	$username	optional: name of the user default: current user
	 * @return	boolean	TRUE if user has access, FALSE if not.
	 * @todo	move regexps to regexp-library		#34
	 * @todo	the $username parameter is not currently used consistently; but it could be leveraged for allowing/denying access by IP address in ALCs #543
	 */
	function HasAccess($privilege, $tag='', $username='')
	{
		global $debug;
		// init & set defaults
		$hasaccess = FALSE;
if ($debug) echo 'HasAccess ('.$tag.') calling... ';
		$registered = $this->existsUser();
		$tag = trim($tag);
		if (empty($tag))
		{
			#$tag = $this->GetPageTag();
			$tag = $this->tag;
		}
		$username = trim($username);

		/*
		// see whether user is registered and logged in
		if ($user = $this->GetUser())
		{
			$username = $user['name'];		// ignore $username parameter
			$registered = TRUE;
		}
		elseif (!$username)	// needed only for anonymous user, and then only if we store IP addresses or hostnames in ALCls!
		{
			$username = $this->GetUserName();	// expensive if user not logged in and reverse DNS lookup allowed!  @@@ specify IP address #543
		}
		*/
		// get name (or IP/host) of current user if no username specified
		// (since we already know whether the user is registered, the following
		// is a fraction faster than just calling GetUserName()
		if (empty($username))
		{
			if ($registered)
			{
				$username = $this->reg_username;
			}
			else
			{
				$username = $this->anon_username;
			}
		}
		// we could actually look if *specified* username is registered

		// if current user is owner (or admin), return true. owner can do anything!
		// NOTE: this does NOT use (or need) the $username parameter!
if ($debug) echo 'HasAccess calling... ';
		if ($this->UserIsOwner($tag))
		{
			$hasaccess = TRUE;
		}
		else
		{
			// load acl
			#if ($tag == $this->GetPageTag())
			if ($tag == $this->tag)
			{
				$acl = $this->ACLs[$privilege.'_acl'];	// @@@ make sure this is already normalized
			}
			else
			{
				#$tag_ACLs = $this->LoadAllACLs($tag);
				$tag_ACL = $this->LoadACL($tag,$privilege);
				$acl = $tag_ACL[$privilege.'_acl'];	// should not be needed: see todo with LoadACL()
			}

			// fine fine... now go through acl
			foreach (preg_split('/[|,]+/', $acl) as $line)	// @@@ see @todo in TrimACLs() and other ACL methods!!! - we should already have a "normalized" list here!
			{
				// check for inversion character "!"
				if (preg_match('/^[!](.*)$/', $line, $matches))
				{
					$negate = TRUE;
					$line = $matches[1];
				}
				else
				{
					$negate = FALSE;
				}

				// if there's still anything left... lines with just a "!" don't count!
				if ($line)
				{
					switch ($line[0])
					{
						// @@@ JW: REs: store ACL "symbols" in a constant - and explicitly forbid these as initial username characters (see also #539)
						// comments
						case "#":
							break;
						// everyone
						case "*":
							$hasaccess = !$negate;
							break 2;
						// only registered users
						case "+":
							// return ($registered) ? !$negate : false;
							$hasaccess = ($registered) ? !$negate : $negate;
							break 2;
						// aha! a user entry. NOTE: *here* we do need (use) the username!!
						default:
							if ($line == $username)
							{
								$hasaccess = !$negate;
								break 2;
							}
					}
				}
			}
		}

		// return result
		return $hasaccess;
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
		if ($days = $this->GetConfigValue('referrers_purge_time'))
		{
			$this->Query("
				DELETE FROM ".$this->GetConfigValue('table_prefix')."referrers
				WHERE time < date_sub(now(), interval '".mysql_real_escape_string($days)."' day)"
				);
		}

		// purge old page revisions
		if ($days = $this->GetConfigValue('pages_purge_time'))
		{
			$this->Query("
				DELETE FROM ".$this->GetConfigValue('table_prefix')."pages
				WHERE time < date_sub(now(), interval '".mysql_real_escape_string($days)."' day)
					AND latest = 'N'"
				);
		}
	}

	/**
	 * THE BIG EVIL NASTY ONE!
	 *
	 * @uses	Wakka::$ACLs
	 * @uses	Wakka::$handler
	 * @uses	Wakka::$wikka_url
	 * @uses	getmicrotime()
	 * @uses	Wakka::authenticateUserFromCookies()
	 * @uses	Wakka::deleteOldWikkaCookies()
	 * @uses	Wakka::Footer()
	 * @uses	Wakka::getAnonUserName()
	 * @uses	Wakka::GetHandler()
	 * @uses	Wakka::Handler()
	 * @uses	Wakka::Header()
	 * @uses	Wakka::Href()
	 * @uses	Wakka::LoadAllACLs()
	 * @uses	Wakka::LoadPage()
	 * @uses	Wakka::LogReferrer()
	 * @uses	Wakka::Maintenance()
	 * @uses	Wakka::ReadInterWikiConfig()
	 * @uses	Wakka::Redirect()
	 * @uses	Wakka::SetPage()
	 * @uses	Config::$rewrite_mode
	 * @uses	Config::$root_page
	 *
	 * @param	string	$tag		mandatory: name of the single page/image/file etc. to be used
	 * @param	string	$handler	optional: the method which should be used. default: "show"
	 * @return	void
	 * @todo	rewrite the handler call routine and move handler specific settings to handler config files #446 #452
	 */
	function Run($tag, $handler='')
	{
		global $debug;
if ($debug) echo 'Run - tag: '.$tag."<br/>\n";
if ($debug)  echo 'Run - handler: '.$handler."<br/>\n";
		// get debug flag from wikka.php

		// do our stuff!

		// 1. wikka_url

		$this->wikka_url = ((bool) $this->GetConfigValue('rewrite_mode')) ? WIKKA_BASE_URL : WIKKA_BASE_URL.WIKKA_URL_EXTENSION;
		$this->config['base_url'] = $this->wikka_url; #backward compatibility

		// 2. page and handler

		// make sure we have a page name ("tag") - redirect if none given	@@@ is this redirect necessary? why not just set value?
		if ('' == ($this->tag = trim($tag)))	// $this->tag redundant here: will be set via SetPage() later on
		{
			$this->Redirect($this->Href('', $this->GetConfigValue('root_page')));
		}
		// if we get here, we have a page name
		// load requested page and store data in object variables
if ($debug) echo 'Run calling... ';
		$this->SetPage($this->LoadPage($tag, (isset($_GET['time']) ? $_GET['time'] :''))); #312
		// load ACLs for the page
		$this->ACLs = $this->LoadAllACLs($this->tag);
		// default handler if none specified; store in object variable
		if ('' == ($this->handler = trim($handler)))
		{
			$this->handler = 'show';
		}

		// 3. clean up old (OBSOLETE) cookies

		//if (isset($_COOKIE['wikka_user_name']) && (isset($_COOKIE['wikka_pass'])))
		//{
			/*
			// Old cookies (old names!): delete them
			// JW:	deleteWikkaCookie() will not work for this purpose:
			//		the old names didn't use a suffix
			//		(see [79])! deleteWikkaCookie() was introduced here in [413].
			SetCookie('wikka_user_name', '', 1, '/'); // do use root as path because that is what we used!
			$_COOKIE['wikka_user_name'] = '';	// better use unset()?
			SetCookie('wikka_pass', '', 1, '/'); // do use root as path because that is what we used!
			$_COOKIE['wikka_pass'] = '';	// better use unset()?
			*/
			/*
			$this->deleteWikkaCookie('wikka_pass');
			$this->deleteWikkaCookie('wikka_user_name');
			*/
		//}
		// clean up cookies with old names or with root path if that's not the
		// current path (they are OBSOLETE now)
		// JW:	Updated deleteWikkaCookie() can now handle old names and paths, so
		//		so we can now bundle all cleanup in one function call
if ($debug)
{
	echo 'cleaning up old cookies:'."<br/>\n";
	echo 'BEFORE DeleteOldCookies - current cookies:<pre>';
	print_r($_COOKIE);
	echo "</pre>\n";
}
		$this->deleteOldWikkaCookies();	// remove all old cookies in one go.
if ($debug)
{
	echo 'AFTER DeleteOldCookies - current cookies:<pre>';
	print_r($_COOKIE);
	echo "</pre>\n";
}

		// 4. user

		// authenticate user from persistent cookie; if authenticated, store data
		// in object variable.
		// NOTE: we do this after cleaning up old cookies so we can't authenticate
		// from an obsolete cookie!
		/*
		if ($user = $this->LoadUser($this->getWikkaCookie('user_name'), $this->getWikkaCookie('pass')))
		{
			$this->SetUser($user);	// LoginUser()
		}
		*/
		if (!$this->authenticateUserFromCookies())	// logs in user if authenticated
		{
			// look up (and cache) anononymous IP/hostname
			$this->getAnonUserName();
		}

		// 5. log referrers

		$this->LogReferrer();

		// 6. do occasional maintenance to purge old page revisions

		#if (!($this->GetMicroTime()%3))
		if (!(getmicrotime(TRUE) % 3))	// see #100
		{
			$this->Maintenance();
		}

		// 7. load interwiki data (used by most handlers)

		$this->ReadInterWikiConfig();

		// 8. the actual work after all this preparation:
		//    apply the requested (or implied) handler

		// XML-producing handlers
		// @@@ HTTP headers (to be moved to handler config files - #446)
		if (preg_match('/\.(xml|mm)$/', $this->GetHandler()))
		{
			header('Content-type: text/xml');		// @@@ #446; #406/comment7 not correct for all feed formats! Note that FeedCreator generates its own Content-type headers!
			print($this->Handler($this->GetHandler()));
		}
		// raw page handler
		// @@@ HTTP headers (to be moved to handler config files - #446)
		elseif ($this->GetHandler() == 'raw')
		{
			header('Content-type: text/plain');		// @@@ #446; #406/comment7
			print($this->Handler($this->GetHandler()));
		}
		// grabcode handler or fullscreen mindmap handler
		elseif (($this->GetHandler() == 'grabcode') || ($this->GetHandler() == 'mindmap_fullscreen'))
		{
			print($this->Handler($this->GetHandler()));
		}
		/*..leads to endless loop!
		 *  This WAS a workaround for relative paths and rewrite gone wrong
		 *  but with a correct .htaccess and using StaticHref() this is not
		 *  needed any more!
		// workaround for rewritten css or img references
		elseif (preg_match('/\.(gif|jpg|png)$/', $this->handler))
		{
			header('Location: images/' . $this->handler);
		}
		// workaround for rewritten css or img references
		elseif (preg_match('/\.css$/', $this->handler))
		{
			header('Location: css/' . $this->handler);
		}
		*/
		else	// all other handlers need page header and page footer
		{
			// handle body before header and footer: user may be logging in/out!
			$content_body = $this->Handler($this->GetHandler());
			print($this->Header().$content_body.$this->Footer());
		}
	}
}
?>
