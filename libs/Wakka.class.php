<?php
/**
 * This file is part of Wikka, a PHP wiki engine.
 *
 * It includes the Wakka class, which provides the core functions
 * to run Wikka.
 *
 * @package		Wikka
 * @subpackage	Libs
 * @version		$Id: Wakka.class.php 1346 2009-03-03 03:38:17Z BrianKoontz $
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
 * @copyright Copyright 2002-2003, Hendrik Mans <hendrik@mans.de>
 * @copyright Copyright 2004-2005, Jason Tourtelotte <wikka-admin@jsnx.com>
 * @copyright Copyright 2006-2010 {@link http://wikkawiki.org/CreditsPage Wikka Development Team}
 */

include_once('libs/Database.lib.php');

/**
 * Time to live for client-side cookies in seconds (90 days)
 */
if(!defined('PERSISTENT_COOKIE_EXPIRY')) define('PERSISTENT_COOKIE_EXPIRY', 7776000);
/**
 * Maximum length for displayed hostnames
 */
if (!defined('MAX_HOSTNAME_LENGTH_DISPLAY')) define('MAX_HOSTNAME_LENGTH_DISPLAY', 50);
/**
 * Length to use for generated part of id attribute.
 */
if (!defined('ID_LENGTH')) define('ID_LENGTH',10);		// @@@ maybe make length configurable
/**#@-*/

/**
 * Signature for a spamlog metadata line; MUST look different than Wikka markup!
 */
define('SPAMLOG_SIG','-@-');

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
	 * @access	private
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
	 * Time to live for client-side cookies in seconds (90 days)
	 *
	 * @var integer
	 */
	var $cookie_expiry = PERSISTENT_COOKIE_EXPIRY;

	/**
	 *
	 * @var unknown_type
	 */
	var $wikka_cookie_path;

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
	 * Database connection is established when the main class Wakka is constructed.
	 *
	 * @uses	Config::$dbms_database
	 * @uses	Config::$dbms_host
	 * @uses	Config::$dbms_password
	 * @uses	Config::$dbms_user
	 * @uses	Config::$dbms_type
	 */
	function Wakka($config)
	{
		$this->config = $config;

		// Set up PDO object
		$this->dblink = db_getPDO($this);
		if($this->dblink == null) {
			die('<em class="error">'.T_("DB connection error in Wakka()").'</em>');
		}
		$this->dblink->query("SET NAMES 'utf8'");

		// Don't emulate prepare statements (to prevent injection attacks)
		$this->dblink->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		// Throw an exception on PDO::query calls
		$this->dblink->setAttribute(PDO::ATTR_ERRMODE,
		                            PDO::ERRMODE_EXCEPTION);

		// Set Wikka version, patch level (if present)
		$this->VERSION = WAKKA_VERSION;
		$this->PATCH_LEVEL = WIKKA_PATCH_LEVEL;
	}

	/**#@+
	 * @category	Database
	 * @todo	move into a database class.
	 */

	/**
	 * Send a query to the database.
	 *
	 * If the query fails, the function will simply die(). If SQL-
	 * Debugging is enabled, the query and the time it took to execute
	 * are added to the Query-Log.
	 *
	 * To prevent SQL injection attacks, all queries must be
	 * parameterized!
	 *
	 * @uses	Config::$sql_debugging
	 * @uses	Wakka::GetMicroTime()
	 *
	 * @param	string	$query	mandatory: the query to be executed.
	 * @param   array   $params optional:  parameters for query (NULL if none)
	 * @param	resource $dblink optional: connection to the database
	 * @return	PDOStatement	the result of the query.
	 *
	 */
	function Query($query, $params=NULL, $dblink='')
	{
		// init - detect if called from object or externally
		if ('' == $dblink)
		{
			$dblink = $this->dblink;
			$object = TRUE;
			$start = $this->GetMicroTime();
		}
		else
		{
			$object = FALSE;
		}
		try {
			$result = $dblink->prepare($query);
			if(NULL == $params) {
				$result->execute();
			} else {
				$result->execute($params);
			}
		} catch(PDOException $e) {
			ob_end_clean();
/*
			die('<em class="error">' .
			    T_("Query failed in Query(): ") .
				$e->getCode() .
				'</em>');
      */
			// DEBUG
			// Don't use this in production!
            /*
			print $e;
			print "<br/>";
			print "Query: ".$query;
			print "Params: ".var_dump($params);
			die('<em class="error">' .
			    T_("Query failed in Query(): ") .
				$e->getCode() .
				'</em>');
            */

		}
		if ($object && $this->GetConfigValue('sql_debugging'))
		{
			$time = $this->GetMicroTime() - $start;
			$this->queryLog[] = array(
				"query"		=> $query,
				"time"		=> $time);
		}
		return $result;
	}

	/**
	 * Replacement for mysql_real_escape_string() (wrapper around
	 * PDO::quote()).
	 *
	 * Note that the use of parameters using prepare()/execute() is
	 * the preferred method for santizing input.  Use PDO::quote()
	 * sparingly!
	 *
	 * @param	string	$val	mandatory: the string to be sanitized
	 * @param	resource $dblink optional: connection to the database
	 * @return	string	the sanitized string
	 *
	 */
	function pdo_quote($val, $dblink='')
	{
		// init - detect if called from object or externally
		if ('' == $dblink)
		{
			$dblink = $this->dblink;
		}
		return $dblink->quote($val);
	}

	/**
	 * Return "safe" identifiers (tables, fields, and database names)
	 * by enclosing in backticks.
	 *
	 * Note that this offers protection against SQL injection, but not
	 * against dynamic input of table/field/db names.  Best to check
	 * against a whitelist!
	 *
	 * Adapted from http://php.net/manual/en/pdo.quote.php#112169
	 *
	 * @param	string		$ident	mandatory: identifier
	 * @param	resource	$dblink	optional: connection to the database
	 * @return	string		the sanitized identifier
	 */
	function pdo_quote_identifier($ident, $dblink='') {
		if('' == $dblink) {
			$dblink = $this->dblink;
		}
		return "`".str_replace("`","``",$ident)."`";
	}

	/**
     * Return DB server version.
	 *
	 * @param	resource $dblink optional: connection to the database
	 * @return	string	the DB version
	 */
	function pdo_get_server_version($dblink='') {
		// init - detect if called from object or externally
		if ('' == $dblink)
		{
			$dblink = $this->dblink;
		}
		return $dblink->getAttribute(PDO::ATTR_SERVER_VERSION);
	}


	/**
	 * Return the first row of a query executed on the database.
	 *
	 * @uses	Wakka::LoadAll()
	 *
	 * @param	string	$query	mandatory: the query to be executed
	 * @param   array   $params optional: parameters for query (NULL if none)
	 * @return	mixed	an array with the first result row of the query, or FALSE if nothing was returned.
	 * @todo	for 1.3: check if indeed false is returned (compare with trunk)
	 */
	function LoadSingle($query, $params=NULL)
	{
		if ($data = $this->LoadAll($query, $params)) {
			return $data[0];
		}
		return FALSE;
	}

	/**
	 * Return all results of a query executed on the database.
	 *
	 * @uses	Wakka::Query()
	 *
	 * @param	string $query mandatory: the query to be executed
	 * @param   array   $params optional: parameters for query (NULL if none)
	 * @return	array the result of the query.
	 */
	function LoadAll($query, $params=NULL)
	{
		if ($r = $this->Query($query, $params))
		{
			$data = $r->fetchAll();
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
	 * @since		Wikka 1.1.6.4
	 * @version		1.1
	 *
	 * @access	public
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::Query()
	 * @uses	Config::$table_prefix
	 *
	 * @param	string	$table	required: (logical) table name to query;
	 *							prefix will be automatically added
	 * @param	string	$where	optional: criteria to be specified for a WHERE clause;
	 *							do not include WHERE
	 * @param   boolean $usePrefix optional: if true, append prefix defined in wikka.config.php file; if false, do not append prefix
	 * @return	integer	number of matches returned by query
	 */
	function getCount($table, $where='', $params = NULL, $usePrefix=TRUE)
	{
		// build query
		$prefix = '';
		if(TRUE===$usePrefix)
		{
			$prefix = $this->GetConfigValue('table_prefix');
		}
		$where = ('' != $where) ? ' WHERE '.$where : '';
		$query = "
			SELECT COUNT(*)
			FROM ".$prefix.$table.
			$where;

		// get and return the count as an integer
		$r = $this->Query($query, $params);
		$count = $r->fetch($cursor_offset = 0);
		$r->closeCursor();
		return $count[0];
	}

	/**
	 * Check if the DB version is higher or equal to a given (minimum) one.
	 *
	 * @param $major
	 * @param $minor
	 * @param $subminor
	 * @return unknown_type
	 * @todo	for 1.3: compare with trunk-version!
	 */
	function CheckDBVersion($major, $minor, $subminor)
	{
		$result = $this->pdo_get_server_version();
		if ($result !== FALSE)
		{
			$match = explode('.', $result);
		} else {
			return -1;
		}


		$db_major = $match[0];
		$db_minor = $match[1];
		$db_subminor = $match[2][0].$match[2][1];

		if ($db_major > $major)
		{
			return 1;
		}
		else
		{
			if (($db_major == $major) && ($db_minor >= $minor) && ($db_subminor >= $subminor))
			{
				return 1;
			}
			else
			{
				return 0;
			}
		}
	}

	/**#@+
	 * @category	Misc methods
	 */

	/**
	 * @todo	replace by getmicrotime() in Compatibility library!
	 * @return unknown_type
	 */
	function GetMicroTime()
	{
		list($usec, $sec) = explode(" ",microtime());
		return ((float)$usec + (float)$sec);
	}

	/**
	 * Calculates the difference between two microtimes.
	 *
	 * @uses	Wakka::getmicrotime()
	 * @param	$from mandatory: start time
	 * @param	$to	optional: end time (default: now)
	 * @return	unknown_type
	 */
	function microTimeDiff($from, $to ='') {
		if (strlen($to) == 0) $to = getmicrotime();
		$totaltime = ($to - $from);
		return $totaltime;
	}

	/**
	 *
	 * @param $filename
	 * @param $notfoundText
	 * @param $vars
	 * @param $path
	 * @return unknown_type
	 * @todo	for 1.3: compare with trunk-version!
	 */
	function IncludeBuffered($filename, $notfoundText='', $vars='', $path='')
	{
		# TODO: change parameter order, so $path (no default,. it's required)
		# comes after $filename and only $notfoundtext and $vars will actually
		# be optional with a default of ''. MK/2007-03-31

		// check if required parameter $path is supplied (see TODO)
		if ('' != trim($path))
		{
			// build full (relative) path to requested plugin (method/action/formatter)
			$fullfilepath = $this->BuildFullpathFromMultipath($filename, $path);
			// check if requested file (handler/action/formatter) actually exists
			if (FALSE===empty($fullfilepath))
			{
				if (is_array($vars))
				{
					// make the parameters also available by name (apart from the array itself):
					// some callers rely on these separate values, so we extract them, too
					// taking care not to overwrite any already-existing variable
					extract($vars, EXTR_SKIP);	# [SEC] EXTR_SKIP avoids collision with existing filenames
				}
				ob_start();
				include($fullfilepath);
				$output = ob_get_contents();
				ob_end_clean();
				return $output;
			}
		}
		if ('' != trim($notfoundText))
		{
			return '<em class="error">'.$this->htmlspecialchars_ent(trim($notfoundText)).'</em>';	# [SEC] make error (including (part of) request) safe to display
		}
		else
		{
			return FALSE;
		}
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
	 * @copyright	Copyright � 2005, Marjolein Katsma
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 * @since		Wikka 1.1.6.4
	 * @version		1.0
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

	/**#@-*/

	/**#@+
	 * @category	Security methods
	 */

	/**
	 * Strip potentially dangerous tags from embedded HTML.
	 *
	 * @uses	Config::$safehtml_path
	 * @uses	instantiate()
	 * @uses	SafeHTML::parse()
	 *
	 * @param	string $html mandatory: HTML to be secured
	 * @return	string sanitized HTML
	 */
	function ReturnSafeHTML($html)
	{
		$safehtml_classpath = $this->GetConfigValue('safehtml_path').'/classes/safehtml.php';
		require_once $safehtml_classpath;

		// Instantiate the handler
		$safehtml = instantiate('safehtml');

		$filtered_output = $safehtml->parse($html);

		return $filtered_output;
	}

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
	 * @copyright	Copyright � 2004, Marjolein Katsma
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
		#return htmlspecialchars(preg_replace('/&amp;/','&',$url));
		return $this->hsc_secure(preg_replace('/&amp;/','&',$url));
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
	 *
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
	static function hsc_secure($string, $quote_style=ENT_COMPAT)
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
		return strtr($string,$aTransSpecchar);
	}

	/**
	 * Get a value provided by user (by get, post or cookie) and sanitize it.
	 * The method is also helpful to disable warning when the value was absent.
	 *
	 * Note that form token checks are enforced for all POST
	 * operations to prevent CSRF attacks.
	 *
	 * @version	1.0
	 *
	 * @uses	Wakka::htmlspecialchars_ent()
	 *
	 * @access	public
	 * @since	Wikka 1.3
	 *
	 * @param	string	$varname required: field name on get or post or cookie name
	 * @param	string	$gpc one of 'get', 'post', or 'cookie'. Optional,
	 *			defaults to 'get'.
	 * @param   string  $authenticate one of TRUE (use for sensitive
				 POSTs or FALSE (do not check form token). Optional, defaults to
				 TRUE (most secure option).
	 * @return	string	sanitized value of $_GET[$varname] (or $_POST,
	 *          $_COOKIE, depending on $gpc).  Redirects with error message upon
	 *          authentication error.
	 */
	function GetSafeVar($varname, $gpc='get', $authenticate=TRUE)
	{
		$safe_var = NULL;
		if ($gpc == 'post')
		{
			// Is this a posted form?
			if(NULL != $_POST)
			{
				if(TRUE == $authenticate)
				{
					if(!isset($_POST['CSRFToken']))
					{
						$this->SetRedirectMessage('Authentication failed: NoCSRFToken');
						$this->Redirect();
					}
					$CSRFToken = $this->htmlspecialchars_ent($_POST['CSRFToken']);
					if($CSRFToken != $_SESSION['CSRFToken'])
					{
						$this->SetRedirectMessage('Authentication failed: CSRFToken mismatch');
						$this->Redirect();
					}
				}
				$safe_var = isset($_POST[$varname]) ? $_POST[$varname] : NULL;
			}
			else
			{
				$safe_var = NULL;
			}
		}
		elseif ($gpc == 'get')
		{
			$safe_var = isset($_GET[$varname]) ? $_GET[$varname] : NULL;
		}
		elseif ($gpc == 'cookie')
		{
			$safe_var = isset($_COOKIE[$varname]) ? $_COOKIE[$varname] : NULL;
		}
		return ($this->htmlspecialchars_ent($safe_var));
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
	 * @uses	Config::$geshi_path
	 * @uses	Config::$geshi_languages_path
	 * @uses	Config::$geshi_header
	 * @uses	Config::$geshi_line_numbers
	 * @uses	Config::$geshi_tab_width
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
		include_once($this->GetConfigValue('geshi_path').'/geshi.php');
		$geshi = instantiate('GeSHi', $sourcecode, $language, $this->GetConfigValue('geshi_languages_path'));				# create object by reference

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
		return '<!--start GeSHi-->'."\n".$geshi->parse_code()."\n".'<!--end GeSHi-->'."\n";
	}

	/**
	 * Normalizes line endings to "*nix style" ("\n") in a string; handles both Dos/Win and Mac.
	 *
	 * @author		{@link http://wikka.jsnx.com/JavaWoman JavaWoman}
	 * @copyright	Copyright � 2005, Marjolein Katsma
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 * @version		0.5
	 *
	 * @access		public
	 *
	 * @param		string	$content	required: string to be normalized
	 * @return		string				content with normalized line endings
	 */
	function normalizeLines($content)
	{
		return str_replace("\r","\n",str_replace("\r\n","\n",$content));
	}



	/**#@-*/

	/**#@+
	 * @category	Variable-related methods
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
		return preg_replace('/_+/', ' ', $this->tag);
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
	 * Get the handler used on the page.
	 *
	 * @uses	Wakka::$handler
	 * @return string name of the handler.
	 */
	function GetHandler()
	{
		return $this->handler;
	}

	/**
	 * Get the value of a given item from the wikka config.
	 *
	 * @uses	Wakka::$config
	 *
	 * @param	$name	mandatory: name of a key in the config array
	 * @return	mixed	the value of the configuration item, or NULL if not found
	 */
	function GetConfigValue($name, $default=NULL)
	{
		$val = (isset($this->config[$name])) ? $this->config[$name] : $default;
		return $val;
	}
	/**
	 * Set the value of a given item from the wikka config.
	 *
	 * @uses	Wakka::$config
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
	 * @uses	Config::$wakka_name
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
	 *
	 * @return unknown_type
	 */
	function GetWikkaPatchLevel()
	{
		return $this->PATCH_LEVEL;
	}

	/**
	 * Log probably spammy comment.
	 *
	 * @author		{@link http://wikka.jsnx.com/JavaWoman JavaWoman}
	 * @copyright	Copyright � 2005, Marjolein Katsma
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 * @version		0.7
	 *
	 * @access		public
	 * @todo		- prepare strings for internationalization
	 *
	 * @uses		logSpam()
	 *
	 * @param		string	$tag		required: string page name
	 * @param		string	$body		required: string containing comment body
	 * @param		string	$reason		required: why attempt failed (urls|filter|nokey|badkey...)
	 * @param		integer	$urlcount	optional: number of (new) URLs
	 * @param		integer	$user		optional: original user/origin (rather than current user)
	 * @param		integer	$time		optional: original time (rather than time of logging)
	 * @return		mixed				bytes written if successful, FALSE otherwise.
	 */
	function logSpamComment($tag,$body,$reason,$urlcount=0,$user='',$time='')
	{
		$type		= 'comment ';
		return $this->logSpam($type,$tag,$body,$reason,$urlcount,$user,$time);
	}

	/**
	 * Log probably spammy document.
	 *
	 * @author		{@link http://wikka.jsnx.com/JavaWoman JavaWoman}
	 * @copyright	Copyright � 2005, Marjolein Katsma
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 * @version		0.7
	 *
	 * @access		public
	 * @todo		- prepare strings for internationalization
	 *
	 * @uses		logSpam()
	 *
	 * @param		string	$tag		required: string page name
	 * @param		string	$body		required: string containing comment body
	 * @param		string	$reason		required: why attempt failed (urls|filter|nokey|badkey)
	 * @param		integer	$urlcount	optional: number of (new) URLs
	 * @param		integer	$user		optional: original user/origin (rather than current user)
	 * @param		integer	$time		optional: original time (rather than time of logging)
	 * @return		mixed				bytes written if successful, FALSE otherwise.
	 */
	function logSpamDocument($tag,$body,$reason,$urlcount=0,$user='',$time='')
	{
		$type		= 'document';
		return $this->logSpam($type,$tag,$body,$reason,$urlcount,$user,$time);
	}

	/**
	 * Log probably spammy feedback.
	 *
	 * @author		{@link http://wikka.jsnx.com/JavaWoman JavaWoman}
	 * @copyright	Copyright � 2005, Marjolein Katsma
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 * @version		0.7
	 *
	 * @access		public
	 * @todo		- prepare strings for internationalization
	 *
	 * @uses		logSpam()
	 *
	 * @param		string	$tag		required: string page name
	 * @param		string	$body		required: string containing feedback text
	 * @param		string	$reason		required: why attempt failed (urls|filter|nokey|badkey)
	 * @param		integer	$urlcount	optional: number of (new) URLs
	 * @return		mixed				bytes written if successful, FALSE otherwise.
	 */
	function logSpamFeedback($tag,$body,$reason,$urlcount=0)
	{
		$type		= 'feedback';
		return $this->logSpam($type,$tag,$body,$reason,$urlcount);
	}

	/**
	 * Log probable spam (comment, document or feedback).
	 *
	 * @author		{@link http://wikka.jsnx.com/JavaWoman JavaWoman}
	 * @copyright	Copyright � 2005, Marjolein Katsma
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 * @version		0.7
	 *
	 * @access		private
	 *
	 * @uses      DEFAULT_SPAMLOG_PATH
	 * @uses      Config::$spamlog_path
	 * @uses      Wakka::appendFile()
	 * @uses      Wakka::GetUserName()
	 * @uses      Wakka::htmlspecialchars_ent()
	 *
	 * @todo		- make recognition of mass delete i18n-proof
	 *				- use configured (later!) timezone
	 *				- use configured (later!) date/time format
	 *
	 * @param		string	$type		required: string containing type (document|comment)
	 * @param		string	$tag		required: string page name
	 * @param		string	$body		required: string containing content (document or comment)
	 * @param		string	$reason		required: why attempt failed (urls|filter|nokey|badkey)
	 * @param		integer	$urlcount	required: number of (new) URLs
	 * @param		integer	$user		optional: user/origin - default current user/origin
	 * @param		integer	$time		optional: time - default current time
	 * @return		mixed				bytes written if successful, FALSE otherwise.
	 */
	function logSpam($type,$tag,$body,$reason,$urlcount,$user='',$time='')
	{
		// set path
		$spamlogpath = $this->GetConfigValue('spamlog_path', DEFAULT_SPAMLOG_PATH);
		// gather data
		if ($user == '')
		{
			$user = $this->GetUserName();					# defaults to REMOTE_HOST to domain for anonymous user
		}
		if ($time == '')
		{
			$time = date('Y-m-d H:i:s');					# current date/time
		}
		if (preg_match('/^mass delete/',$reason))			# @@@ i18n
		{
			$originip = '0.0.0.0';							# don't record deleter's IP address!
		}
		else
		{
			$originip = $_SERVER['REMOTE_ADDR'];
		}
		$ua			= (isset($_SERVER['HTTP_USER_AGENT'])) ? '['.$_SERVER['HTTP_USER_AGENT'].']' : '[?]';
		$ua = $this->htmlspecialchars_ent($ua);
		$body		= $this->htmlspecialchars_ent(trim($body));
		$sig		= SPAMLOG_SIG.' '.$type.' '.$time.' '.$tag.' - '.$originip.' - '.$user.' '.$ua.' - '.$reason.' - '.$urlcount."\n";
		$content	= $sig.$body."\n\n";

		// add data to log
		return $this->appendFile($spamlogpath,$content);	# nr. of bytes written if successful, FALSE otherwise
	}

	/**
	 * Get all meta data lines from the spamlog and return the data in an array.
	 *
	 * @author		{@link http://wikka.jsnx.com/JavaWoman JavaWoman}
	 * @copyright	Copyright � 2005, Marjolein Katsma
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 * @version		0.3
	 *
	 * @uses      DEFAULT_SPAMLOG_PATH
	 * @uses      Config::$spamlog_path
	 *
	 * @access		public
	 *
	 * @return		array		array with associative array for each metadata item in a line
	 */
	function getSpamlogSummary()
	{
		// set path
		$spamlogpath = $this->GetConfigValue('spamlog_path', DEFAULT_SPAMLOG_PATH);
		$aSummary = array();
		$aLines = file($spamlogpath);						# get file as array so we can...
		foreach ($aLines as $line)							# ... select the metadata
		{
			if (preg_match('/^'.SPAMLOG_SIG.'/',$line))
			{
				// gather data
				list($header,$originIp,$userAgent,$reason,$urls) = explode(' - ',$line);
				list(,$type,$day,$time,$page) = preg_split('/\s+/',$header);

				$rc = preg_match('/^([^ ]+) \[([^\]]+)\]$/',$userAgent,$aMatches);
				$user = (isset($aMatches[1])) ? $aMatches[1] : '?';
				$ua   = (isset($aMatches[2])) ? $aMatches[2] : '?';

				// write data
				$aSummary[] = array('type'	=> $type,
									'date'	=> $day.' '.$time,
									'day'	=> $day,
									'time'	=> $time,
									'page'	=> $page,
									'origin'=> $originIp,
									'user'	=> $user,
									'ua'	=> $ua,
									'reason'=> $reason,
									'urls'	=> $urls
									);
			}
		}
		return $aSummary;
	}

	// FILES (handling of text files)
	/**
	 * Read a local file, normalizing line endings.
	 *
	 * Reads a local file (not bothering to read in packets as would be needed
	 * for network or remote files). Returns the content as a string with
	 * normalized line endings ("\n"), or FALSE if the process failed for some
	 * reason. Thus it is the caller's responsibility to provide a correct path
	 * to an existing file.
	 *
	 * @author		{@link http://wikka.jsnx.com/JavaWoman JavaWoman}
	 * @copyright	Copyright � 2005, Marjolein Katsma
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 * @version		0.5
	 * @todo
	 *
	 * @access		public
	 * @uses		normalizeLines()
	 *
	 * @param		string	$file  required: relative or absolute file path
	 * @return		mixed		normalized file content if found, FALSE if not
	 */
	function readFile($file)
	{
		#if version_compare(PHP_VERSION >= '4.3.0')
		if (function_exists('file_get_contents'))			# gives best performance
		{
			$content = file_get_contents($file);
		}
		else												# alternative
		{
			$fh = @fopen($file,'r');						# suppress warning with @
			if (!$fh)
			{
				$content = FALSE;
			}
			else
			{
				$content = fread($fh,filesize($file));
				fclose($fh);
			}
		}
		if (FALSE !== $content)
		{
			$content = $this->normalizeLines($content);		# normalize line endings
		}
		return $content;
	}

	/**
	 * Writes new content to a (text) file.
	 *
	 * The content is normalized for line endings ("\n") before writing; this
	 * implies this method CANNOT be used for binary files.
	 * Returns the number of bytes written if successful, FALSE otherwise.
	 *
	 * @author		{@link http://wikka.jsnx.com/JavaWoman JavaWoman}
	 * @copyright	Copyright � 2005, Marjolein Katsma
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 * @version		0.5
	 * @todo
	 *
	 * @access		public
	 * @uses		normalizeLines()
	 *
	 * @param		string	$file		required: relative or absolute file path
	 * @param		string	$content	required: contents be written to the file
	 * @return		mixed				bytes written if successful, FALSE otherwise.
	 */
	function writeFile($file,$content)
	{
		$rc = FALSE;
		$content = $this->normalizeLines($content);		# normalize line endings
		if (function_exists('file_put_contents'))		# most efficient
		{
			$rc = file_put_contents($file,$content);
#if (FALSE === $rc) echo 'file_put_contents FALSE!<br/>';
			if (strlen($content) > 0 && $rc == 0) $rc = FALSE;		# for compatibility with fwrite() @@@ needed?
		}
		else											# alternative
		{
			$fh = @fopen($file,'w');					# open file for writing; suppress warning with @
			if (FALSE !== $fh)
			{
				$rc = @fwrite($fh,$content);
				fclose($fh);
			}
		}
		return $rc;										# number of bytes written or FALSE if writing failed
	}

	/**
	 * Appends new content to an existing file.
	 *
	 * The content is normalized for line endings ("\n") before writing; this
	 * implies this method CANNOT be used for binary files.
	 * Returns the number of bytes written if successful, FALSE otherwise.
	 *
	 * @author		{@link http://wikka.jsnx.com/JavaWoman JavaWoman}
	 * @copyright	Copyright � 2005, Marjolein Katsma
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 * @version		0.5
	 * @todo
	 *
	 * @access		public
	 * @uses		normalizeLines()
	 *
	 * @param		string	$file		required: relative or absolute file path
	 * @param		string	$content	required: contents be written to the file
	 * @return		mixed				bytes written if successful, FALSE otherwise.
	 */
	function appendFile($file,$content)
	{
		$rc = FALSE;
		$content = $this->normalizeLines($content);		# normalize line endings
		if (function_exists('file_put_contents'))		# most efficient
		{
			$rc = file_put_contents($file,$content,FILE_APPEND);
			if (strlen($content) > 0 && $rc == 0) $rc = FALSE;		# for compatibility with fwrite() @@@ needed?
		}
		else											# alternative
		{
			$fh = @fopen($file,'a');				# open file for appending/writing; suppress warning with @
			if (FALSE !== $fh)
			{
				$rc = @fwrite($fh,$content);
				fclose($fh);
			}
		}
		return $rc;										# number of bytes written or FALSE if writing failed
	}



	/**#@-*/

	/**#@+
	 * @category	Page
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
	 * @uses	Config::$table_prefix
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
	 * @todo	for 1.3: compare with trunk
	 */
	function LoadPage($tag, $time='', $cache=TRUE)
	{
		// Always replace '_' with ws
		$tag = preg_replace('/_+/', ' ', $tag);
		// retrieve from cache
		if (!$time && $cache) {
			$page = isset($this->pageCache[$tag]) ? $this->pageCache[$tag] : null;
			if ($page=="cached_nonexistent_page") return null;
		}
		// load page
		if(!isset($page)) {
			$params = NULL;
			if('' != $time) {
				$params = array(':time'=>$time);
			}
			$params[':tag'] = $tag;
			$query = "SELECT * FROM " . $this->GetConfigValue('table_prefix') .
			         "pages WHERE tag=:tag " .
					 ($time ? "AND time=:time " : "AND latest='Y' ") .
					 "LIMIT 1";
			$page = $this->LoadSingle($query, $params);
		}
		// cache result
		if ($page && !$time) {
			$this->pageCache[$page["tag"]] = $page;
		} elseif (!$page) {
			$this->pageCache[$tag] = "cached_nonexistent_page";
		}
		return $page;
	}

	/**
	 * GetCachedPage gets a page from cache whose name is $tag.
	 *
	 * @access	public
	 * @uses	Wakka::$pageCache
	 *
	 * @param	mixed	$tag	the name of the page to retrieve from cache.
	 * @return	mixed	an array as returned by LoadPage(), or FALSE if absent from cache.
	 */
	function GetCachedPage($tag)
	{
		return (isset($this->pageCache[$tag])) ? $this->pageCache[$tag] : null;
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
	 *
	 * @param	mixed	$page
	 * @return	void
	 */
	function CachePage($page)
	{
		$this->pageCache[$page['tag']] = $page;
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
	 * @param	string	$page
	 * @return	void
	 */
	function SetPage($page)
	{
		$this->page = $page;
		if ($this->page['tag'])
		{
			$this->tag = $this->page['tag'];
		}
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
	 * @return  string  formatted $page_title	
	 * @todo	probably better to use the already-existing Wakka::$page array to store this?
	 */
	function SetPageTitle($page_title)
	{
		$stripped_page_title = trim(strip_tags($page_title));
		if(null != $stripped_page_title)
		{
			$this->page_title = (strlen($stripped_page_title) > 75) ? 
                substr($stripped_page_title, 0, 75) : $stripped_page_title;
		}
		return $this->page_title;
	}

	/**
	 * LoadPageById loads a page whose id is $id.
	 *
	 * @access	public
	 * @uses	Wakka::GetCachedPageById()
	 * @uses	Wakka::LoadSingle()
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Config::$table_prefix
	 *
	 * @param	int		$id		mandatory: Id of the page to load.
	 * @return	array with page structure identified by $id, or ? if no page could be retrieved
	 * @todo	for 1.3: compare and add caching ability
	 * @todo	for 1.3: check LoadSingle for return value
	 */
	function LoadPageById($id)
	{
		return $this->LoadSingle("
			SELECT *
			FROM ".$this->GetConfigValue('table_prefix')."pages
			WHERE id = :id LIMIT 1", array(':id' => $id)
			);
	}

	/**
	 * LoadRevisions: Load revisions of a page.
	 *
	 * @access	public
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::LoadAll()
	 *
	 * @param	string	$page Name of the page to view revisions of
	 * @return	array	This value contains * from page.
	 */
	function LoadRevisions($page)
	{
		return $this->LoadAll("select * from ".$this->GetConfigValue('table_prefix')."pages where tag = :page order by id desc", array(':page' => $page));
	}

	/**
	 * LoadOldestRevision: Load the oldest known revision of a page.
	 *
	 * @access	public
	 * @uses	Config::$pagename_case_sensitive
	 * @uses	Config::$table_prefix
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::LoadSingle()
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
			$oldest_revision = $this->LoadSingle("
				SELECT note, id, time, user
				FROM ".$this->GetConfigValue('table_prefix')."pages
				WHERE tag = :tag
				ORDER BY time
				LIMIT 1", array(':tag' => $tag)
				);
		return $oldest_revision;
	}

	/**
	 * Load pages linking to a given page.
	 *
	 * @uses	Config::$table_prefix
	 * @uses	Wakka::LoadAll()
	 * @param	string	$tag	mandatory: name of page to find referring links to
	 * @return	array	one record with a page name for each page found (empty array if none found).
	 */
	function LoadPagesLinkingTo($tag)	// #410
	{
		return $this->LoadAll("
			SELECT from_tag AS page_tag
			FROM ".$this->GetConfigValue('table_prefix')."links
			WHERE to_tag = :tag
			ORDER BY page_tag", array(':tag' => $tag)
			);
	}

	/**
	 * Load the last x edited pages on the wiki.
	 *
	 * @uses	Config::$table_prefix
	 * @uses	Wakka::LoadAll()
	 * @uses	Wakka::CachePage()
	 *
	 * @return	array	the last x pages that were changed (empty array if none found)
	 * @todo	use constant for default limit value (no "magic numbers!")
	 * @todo	do we need the whole page for each, or only specific fields?
	 */
	function LoadRecentlyChanged()
	{
		$pages = $this->LoadAll("
			SELECT *
			FROM ".$this->GetConfigValue('table_prefix')."pages
			WHERE latest = 'Y'
			ORDER BY id DESC"
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
	 * @access	public
	 * @uses	Config::$table_prefix
	 * @uses	Wakka::LoadAll()
	 *
	 * @return	array
	 * @todo	it would be useful to set a LIMIT ($max) here as well
	 */
	function LoadWantedPages()
	{
		$pre = $this->GetConfigValue('table_prefix');
		return $this->LoadAll("
			SELECT DISTINCT
				".$pre."links.to_tag AS tag,
				COUNT(".$pre."links.from_tag) AS count
			FROM ".$pre."links
			LEFT JOIN ".$pre."pages
				ON ".$pre."links.to_tag = ".$pre."pages.tag
			WHERE ".$pre."pages.tag is NULL
			GROUP BY ".$pre."links.to_tag
			ORDER BY count desc");
	}

	/**
	 * Ask if a pagename needs to be created.
	 *
	 * When an existing page links to a page that hasn't yet been created, this latter needs
	 * to be created, or the reference needs to be deleted.
	 *
	 * @access	public
	 * @uses	Wakka::LoadWantedPages()
	 *
	 * @param	string	$tag	Name of the page to ask if it needs to be created
	 * @return	boolean	TRUE if $tag needs to be created
	 * @todo	exmine old comment: '#410 - but function not used in 1.1.6.3 -OR- trunk?'
	 * @todo page_tag or tag?
	 */
	function IsWantedPage($tag)
	{
		if ($pages = $this->LoadWantedPages())
		{
			foreach ($pages as $page)
			{
				if ($page['page_tag'] == $tag)
				{
					return TRUE;
				}
			}
		}
		return FALSE;
	}

	/**
	 * Load all orphaned pages.
	 *
	 * Orphaned pages are existing pages that no others page on the wiki links to.
	 * Thus, the only chance this page could be reached may be from search or
	 * special pages like PageIndex. A good quality wiki should not have any orphaned page.
	 *
	 * @uses	Config::$table_prefix
	 * @uses	Wakka::LoadAll()
	 * @access	public
	 * @return	array	List of orphaned pages
	 */
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

	/**
	 * Load all active page names of the wiki and their respective owners.
	 *
	 * @uses	Config::$table_prefix
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::LoadAll()
	 * @access	public
	 * @return	array	List of all page titles (and the page owner), ordered by page name
	 */
	function LoadPageTitles()		// @@@ name no longer matches function
	{
		return $this->LoadAll("
			SELECT DISTINCT tag, owner
			FROM ".$this->GetConfigValue('table_prefix')."pages
			WHERE latest = 'Y'
			ORDER BY tag"
			);
	}

	/**
	 * Get names of pages (tags) owned by the specified user.
	 *
	 * @uses	Config::$table_prefix
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::LoadAll()
	 *
	 * @param	string	$owner
	 * @return	array	one row for each page owned by $owner
	 */
	function LoadPagesByOwner($owner)
	{
		return $this->LoadAll("
			SELECT tag
			FROM ".$this->GetConfigValue('table_prefix')."pages
			WHERE `latest` = 'Y'
				AND `owner` = :owner
			ORDER BY `tag`", array(':owner' => $owner)
			);
	}

	/**
	 * Load all pages in the wiki.
	 *
	 * Using this function should be avoided since it really loads everything from the pages table!
	 *
	 * @return unknown_type
	 * @todo	for 1.3:see trunk and comment above on LoadPageTitles()
	 */
	function LoadAllPages()
	{
		return $this->LoadAll("
			SELECT * FROM ".$this->GetConfigValue('table_prefix')."pages
			WHERE latest = 'Y'
			ORDER BY tag"
			);
	}

	/**
	 * Save a page.
	 *
	 * @uses	Config::$table_prefix
	 * @uses	Config::$wikiping_server
	 * @uses	Wakka::GetPingParams()
	 * @uses	Wakka::existsUser()
	 * @uses	Wakka::GetUserName()
	 * @uses	Wakka::HasAccess()
	 * @uses	Wakka::LoadPage()
	 * @uses	Wakka::Query()
	 * @uses	Wakka::WikiPing()
	 *
	 * @param	string	$tag mandatory:name of the page
	 * @param	string	$body mandatory:content of the page
	 * @param	string	$note mandatory:edit-note
	 * @param	string	$owner
	 * @todo for 1.3:in trunk the page-title is stored together with the page
	 */
	function SavePage($tag, $body, $note, $owner=null)
	{
		// Always replace '_' with ws
		$tag = preg_replace('/_+/', ' ', $tag);
		// get name of current user
		$user = $this->GetUserName();

		// TODO: check write privilege
		if ($this->HasAccess('write', $tag))
		{
			// If $owner is specified, don't do an owner check
			if(empty($owner))
			{
				// is page new?
				if (!$oldPage = $this->LoadPage($tag))
				{
					// current user is owner if user is logged in, otherwise, no owner.
					if ($this->existsUser())
					{
						$owner = $user;
					}
				}
				else
				{
					// aha! page isn't new. keep owner!
					$owner = $oldPage['owner'];
				}
			}
			// Parse page title
			$page_title = $this->ParsePageTitle($body);

			// set all other revisions to old
			$this->Query("
				UPDATE ".$this->GetConfigValue('table_prefix')."pages
				SET latest = 'N'
				WHERE tag = :tag", array(':tag' => $tag)
				);

			// add new revision
			$params = array(':tag' => $tag,
			                ':page_title' => $page_title,
							':owner' => $owner,
							':user' => $user,
							':note' => $note,
							':body' => $body);
			db_addNewRevision($this, $params);

			// WikiPing
			if ($pingdata = $this->GetPingParams($this->GetConfigValue('wikiping_server'), $tag, $user, $note))
			{
				$this->WikiPing($pingdata);
			}
		}
	}

	/**#@-*/

	/**#@+
	 * @category	Search methods
	 */

	/**
	 * Full text search, case-sensitive
	 *
	 * @access	public
	 *
	 * @param	string	$phrase	the text to be searched for
     * @param   string  $caseSensitive	optional: 0 for case-insensitive search (default), 1 for case-sensitive search
	 * @param   string $utf8Compatible optional: 0 for legacy search (case sensitive, wildcards, but incompatible with some character codings), 1 for UTF-8 compatible searches (non-case-sensitive, no wildcards)
	 * @return	string  Search results
	 */
	function FullTextSearch($phrase, $caseSensitive=0, $utf8Compatible=0)
	{
		if(empty($phrase))
		{
			return NULL;
		}
		$sql = '';
		if(0 == $utf8Compatible)
		{
			$id = '';
			// Convert &quot; entity to actual quotes for exact phrase match
			$search_phrase = stripslashes(str_replace("&quot;", "\"", $phrase));
			if ( 1 == $caseSensitive ) $id = ', id';
			$sql  = "select * from ".$this->GetConfigValue('table_prefix')."pages where latest = ".  "'Y'" ." and match(tag, body".$id.") against(:search_phrase IN BOOLEAN MODE) order by time DESC";
			$data = $this->LoadAll($sql, array(':search_phrase' => $search_phrase));
		}
		else
		{
			$sql  = "select * from ".$this->GetConfigValue('table_prefix')."pages WHERE latest = ". "'Y'";
			foreach( explode(' ', $phrase) as $term )
				$sql .= " AND ((`tag` LIKE '%'.$this->quote($term).'%') OR (body LIKE '%'.$this->quote($term).'%'))";
			$data = $this->LoadAll($sql, NULL);
		}

		return $data;
	}


	/**
	 *
	 * @param $phrase
	 * @return unknown_type
	 */
	function FullCategoryTextSearch($phrase)
	{
		return $this->LoadAll("select * from ".$this->GetConfigValue('table_prefix')."pages where latest = 'Y' and match(body) against(':phrase' IN BOOLEAN MODE)",
		array(':phrase' => $phrase));
	}

	/**#@-*/

	/**#@+
	 * @category	Content-related methods
	 */

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
	function CleanTextNode($textvalue, $pattern_prohibited_chars = '/[^A-Za-z0-9_:.\s-]/', $decode_html_entities = TRUE)
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
	 *
	 * @uses	Wakka::IsAdmin()
	 * @uses	Wakka::GetUser()
	 * @uses	Wakka::IncludeBuffered()
	 * @uses	Wakka::Format()
	 *
	 * @param $menu
	 * @return string menu items as an unordered list
	 */
	function MakeMenu($menu)
	{
		switch(TRUE)
		{
			case $this->IsAdmin():
			$menu_file = $menu.'.admin.inc';
			break;

			case $this->GetUser():
			$menu_file = $menu.'.user.inc';
			break;

			default:
			$menu_file = $menu.'.inc';
			break;
		}
		if ($this->BuildFullpathFromMultipath($menu_file,$this->GetConfigValue('menu_config_path'))) #878
		{
			$menu_src = $this->IncludeBuffered($menu_file, '', '', $this->GetConfigValue('menu_config_path')); #878
			$menu_array = explode("\n", trim($menu_src)); #951
			$menu_output = '<ul class="menu" id="'.$menu.'">'."\n";
			foreach ($menu_array as $menu_item)
			{
				$menu_output .= '<li>'.$this->Format($menu_item).'</li>'."\n";
			}
			$menu_output .= '</ul>'."\n";
		}
		else
		{
			$menu_output = '<ul id="'.$menu.'">'."\n";
			$menu_output .= '<li>no menu defined</li>'."\n";
			$menu_output .= '</ul>'."\n";
		}
		return $menu_output;
	}

	/**
	 * Return the title of the current page.
	 *
	 * The page title is cleaned and trimmed. See {@link
	 *	Wakka::wakka3callback()} to find how the title is derived.
	 * If SetPageTitle() was unable to choose a title for the page,
	 *	the page name is used by default.
	 * Attempts to retrieve page title from DB if $tag is specified
	 *	and is not the current page that's loaded
	 *
	 * @uses	Wakka::GetPageTag()
	 * @uses	Wakka::HasPageTitle()
	 * @uses	Wakka::$page_title
	 * @uses	Wakka::LoadSingle()
	 *
	 *
	 * @param	string	@tag	optional: page to get title for (default current page)
	 * @return	mixed	the title of the current page or the page name if none found, trimmed
	 */
	function PageTitle($tag=null)
	{
		if ($tag === null)
		{
			$tag = $this->GetPageTag();
		}
		if ($this->HasPageTitle() && $tag == $this->GetPageTag())
		{
			$page_title = $this->page_title;
		}
		else {
			$query = "SELECT title FROM ".
					$this->GetConfigValue('table_prefix').
					"pages WHERE tag = :tag
					AND LATEST = 'Y'";
			$res = $this->LoadSingle($query, array(':tag' => $tag));
			$page_title = trim($res['title']) !== '' ? $res['title'] : $tag;
			$page_title = strip_tags($page_title);
		}
		$handler = $this->GetHandler();
		if($handler != 'show' &&
		   $handler != NULL) {
			$page_title = $handler." \"".trim($page_title)."\"";
		}
		return $page_title;
	}

	/**
	 * Parses the body of a page for a page title
	 *
	 * Searches for first instance of header markup in page body and
	 * returns this string as the page title, or empty if none found
	 *
	 * @param body string page body
	 * @return string the title of the current page, or empty string
	 */
	function ParsePageTitle($body)
	{
		$page_title = '';
		if (preg_match("#(={3,6})([^=].*?)\\1#s", $body, $matches))
		{
			list($h_fullmatch, $h_markup, $h_heading) = $matches;
			$page_title = $this->SetPageTitle($h_heading);
		}
		// We need trim because $this->Format() appends a carriage return
		return trim(strip_tags($this->Format($page_title)));
	}

	/**
	 * Check by name if a page exists.
	 *
	 * @author		{@link http://wikkawiki.org/JavaWoman JavaWoman}
	 * @copyright	Copyright � 2004, Marjolein Katsma
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 * @version		1.1
	 *
	 * NOTE: v. 1.0 -> 1.1
	 *		- name changed from ExistsPage() to existsPage() !!!
	 *		- added $prefix param so it can be used from installer
	 *		- added $current param so it checks by default for a current page only
	 *
	 * @access	public
	 * @uses	Query()
	 *
	 * @param	string	$page  page name to check
	 * @param	string	$prefix	optional: table prefix to use
	 *					pass NULL if you need to override the $active parameter
	 *					default: prefix as in configuration file
	 * @param	mixed	$dblink	optional: connection resource, or NULL to get
	 *					object's connection
	 * @param	string	$active	optional: if TRUE, check for actgive page only
	 *					default: TRUE
	 * @return	boolean	TRUE if page exists, FALSE otherwise
	 */
	function existsPage($page, $prefix='', $dblink=NULL, $active=TRUE)
	{
		// Always replace '_' with ws
		$page = preg_replace('/_+/', ' ', $page);
        // Strip anchors if they are there
        $page = preg_replace('/^(.*?)(#.*)$/', "$1", $page);
		// init
		$count = 0;
		$table_prefix = (empty($prefix) && isset($this)) ? $this->GetConfigValue('table_prefix') : $prefix;
		if (is_null($dblink))
		{
			$dblink = $this->dblink;
		}
		// build query
		$query = "SELECT COUNT(tag)
				FROM ".$table_prefix."pages
				WHERE tag=:page";
		if ($active)
		{
			$query .= "		AND latest='Y'";
		}
		// do query
		if($r = $this->Query($query, array(':page' => $page))) {
			$count = $r->fetch($cursor_offset = 0);
			$r->closeCursor();
		}
		// report
		return ($count[0] > 0) ? TRUE : FALSE;
	}

	/**#@-*/

	/**#@+
	 * @category WikiPing
	 * @author	DreckFehler
	 */

	/**
	 * WikiPing an external server.
	 *
	 * @param $host
	 * @param $data
	 * @param $contenttype
	 * @param $maxAttempts
	 * @return unknown_type
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
	 * @uses	Wakka::htmlspecialchars_ent()
	 * @uses	Wakka::HTTPpost()
	 * @param	$ping
	 * @param	$debug
	 * @return	unknown_type
	 *
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
					$rpcRequest .= "<member>\n<name>authorpage</name>\n<value>".$ping['authorpage']."</value>\n</member>\n";
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
	 * @uses	Wakka::GetWakkaName()
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
		if ($server)
		{
			$ping['server'] = $server;
			if ($tag) // set page-title
			{
				$ping["tag"] = $tag;
			}
			else
			{
				return FALSE;
			}
			if (!$ping['taglink'] = $this->Href('', $tag)) // set page-url
			{
				return FALSE;
			}
			if (!$ping['wiki'] = $this->GetWakkaName()) // set site-name
			{
				return FALSE;
			}
				$ping['history'] = $this->Href('revisions', $tag); // set url to history

				if ($user)
				{
					$ping['author'] = $user; // set username
					// @todo use existsPage instead
					if ($this->LoadPage($user))
					{
						$ping['authorpage'] = $this->Href('', $user);	// set link to user page
					}
				}
				if ($changelog)
				{
					$ping['changelog'] = $changelog;
				}
			return $ping;
		}
		else return FALSE;
	}

	/**#@-*/

	/**#@+
	 * @category	Cookie-related methods
	 *
	 * Note: Be sure to check the auto login functionality in
	 * setup/install.php if any changes are made to the way session
	 * cookies are set. Since these functions are not yet available
	 * when install.php is called, they must be duplicated in that
	 * file. Changes here without appropriate changes in install.php
	 * may result in login/logout failures! See ticket #800 for more
	 * info.
	 */

	/**
	 *
	 * @uses	Wakka::SetCookie()
	 * @param $name
	 * @param $value
	 * @return unknown_type
	 */
	function SetSessionCookie($name, $value)
	{
		SetCookie($name.$this->GetConfigValue('wiki_suffix'), $value, 0, $this->wikka_cookie_path);
		$_COOKIE[$name.$this->GetConfigValue('wiki_suffix')] = $value;
		$this->cookies_sent = TRUE;
	}

	/**
	 *
	 * @uses	Wakka::SetCookie()
	 * @param $name
	 * @param $value
	 * @return unknown_type
	 */
	function SetPersistentCookie($name, $value)
	{
		SetCookie($name.$this->GetConfigValue('wiki_suffix'), $value, time() + $this->cookie_expiry, $this->wikka_cookie_path);
		$_COOKIE[$name.$this->GetConfigValue('wiki_suffix')] = $value;
		$this->cookies_sent = TRUE;
	}

	/**
	 *
	 * @uses	Wakka::SetCookie()
	 * @param $name
	 * @return unknown_type
	 */
	function DeleteCookie($name)
	{
		SetCookie($name.$this->GetConfigValue('wiki_suffix'), "", 1, $this->wikka_cookie_path);
		$_COOKIE[$name.$this->GetConfigValue('wiki_suffix')] = "";
		$this->cookies_sent = TRUE;
	}

	/**
	 *
	 * @param $name
	 * @return unknown_type
	 */
	function GetCookie($name)
	{
		if (isset($_COOKIE[$name.$this->GetConfigValue('wiki_suffix')]))
		{
			return $_COOKIE[$name.$this->GetConfigValue('wiki_suffix')];
		}
		else
		{
			return FALSE;
		}
	}

	/**#@-*/


	/**#@+
	 * @category	HTTP/GET/POST/LINK related
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
	 * To set the message, either use {@link Wakka::SetRedirectMessage()} or the second parameter
	 * of the {@link Wakka::Redirect()} method.
	 * The message is passed transparently between {@link Wakka::SetRedirectMessage()} and
	 * GetRedirectMessage(). It is the responsibility of any code setting and getting that
	 * message to perform any validation against the message (quotes handling, XHTML validation, ...)
	 *
	 * @see	Wakka::Redirect()
	 * @see	Wakka::SetRedirectMessage()
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
	 * Performs a redirection to another page.
	 *
	 * On IIS server, and if the page had sent any cookies, the redirection must not be performed
	 * by using the 'Location:' header: We use meta http-equiv OR javascript OR link (Credits MarceloArmonas)
	 *
	 * @author	{@link http://wikkawiki.org/DotMG Mahefa Randimbisoa} (added IIS support)
	 *
	 * @access	public
	 * @since	Wikka 1.1.6.2
	 *
	 * @param	string	$url: destination URL; if not specified redirect to the same page.
	 * @param	string	$message: message that will show as alert in the destination URL
	 */
	function Redirect($url='', $message='')
	{
		if ($message != '')
		{
			$_SESSION['redirectmessage'] = $message;
		}
		$url = ($url == '' ) ? $this->wikka_url.$this->GetPageTag() : $url;
		if ((preg_match('/IIS/i', $_SERVER['SERVER_SOFTWARE'])) && ($this->cookies_sent))
		{
			@ob_end_clean();
			die('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en"><head><title>Redirected to '.$this->Href($url).'</title>'.
'<meta http-equiv="refresh" content="0; url=\''.$url.'\'" /></head><body><div><script type="text/javascript">window.location.href="'.$url.'";</script>'.
'</div><noscript>If your browser does not redirect you, please follow <a href="'.$this->Href($url).'">this link</a></noscript></body></html>');
		}
		else
		{
			header('Location: '.$url);
		}
		exit;
	}

	/**
	 * Returns the name of the referring page, if internal page
	 *
	 * @author	{@link http://wikkawiki.org/TormodHaugen Tormod Haugen}
	 *
	 * @access	public
	 * @since	Wikka 1.3.7
	 *
	 * @return string name of refering page if internal, or NULL
	 */
	function GetReferrerPage()
	{
		preg_match('/^(.*)ReferrerMarker/', $this->Href('', 'ReferrerMarker'), $match);	// @@@ use wikka_url here!
		$regex_referrer = '@^'.preg_quote($match[1], '@').'([^\/\?&]*)@';
		if (isset($_SERVER['HTTP_REFERER']) && preg_match($regex_referrer, $_SERVER['HTTP_REFERER'], $match))
		{
			return $match[1];
		}
		else
		{
			return NULL;
		}
	}


	/**
	 * Return the pagename (with optional handler appended).
	 *
	 * @param $handler
	 * @param $tag
	 * @return unknown_type
	 */
	function MiniHref($handler='', $tag='')
	{
		if (!$tag = trim($tag)) $tag = $this->GetPageTag();
		$tag = preg_replace('/\s+/', '_', $tag);
		return $tag.($handler ? "/".$handler : "");
	}

	/**
	 * Returns the full URL to a page/handler.
	 *
	 * @uses	Config::$rewrite_mode
	 * @uses	Wakka::MiniHref()
	 * @param	$method
	 * @param	$tag
	 * @param	$params
	 * @return	unknown_type
	 */
	function Href($method='', $tag='', $params='')
	{
		$href = $this->wikka_url.$this->MiniHref($method, $tag);
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
	 * @uses	Wakka::existsPage()
	 *
	 * @param	mixed	$tag		mandatory:
	 * @param	string	$handler	optional:
	 * @param	string	$text		optional:
	 * @param	boolean	$track		optional:
	 * @param	boolean	$escapeText	optional:
	 * @param	string	$title		optional:
	 * @param	string	$class		optional:
	 * @param   boolean $assumePageExists	optional:
	 * @return	string	an HTML hyperlink (a href) element
	 * @todo	move regexps to regexp-library		#34
	 */
	function Link($tag, $handler='', $text='', $track=TRUE, $escapeText=TRUE, $title='', $class='', $assumePageExists=TRUE)
	{
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
		$link = '';

		// is this an interwiki link?
		// before the : should be a WikiName; anything after can be (nearly) anything that's allowed in a URL
		if (preg_match('/^([[:upper:]][[:alpha:]]+)[:](\S*)$/', $tag, $matches))	// @@@ FIXME #34 (inconsistent with Formatter)
		{
			$url = $this->GetInterWikiUrl($matches[1], $matches[2]);
			$class = 'interwiki';
		}
		// fully-qualified URL? this uses the same pattern as StaticHref() does;
		// it's a recognizing pattern, not a validation pattern
		// @@@ move to regex libary!
		elseif (preg_match('/^(http|https|ftp|news|irc|gopher):\/\/([^\\s\"<>]+)$/', $tag))
		{
			$url = $tag; // this is a valid external URL
			// add ext class only if URL is external
			if (!preg_match('/'.$_SERVER['SERVER_NAME'].'/', $tag))
			{
				$class = 'ext';
			}
		}
		// Is this an e-mail address?
		elseif (preg_match('/^.+\@.+$/', $tag))
		{
			$url = 'mailto:'.$tag;
			$class = 'mailto';
		}
		/*
		// check for protocol-less URLs
		elseif (!preg_match('/:/', $tag))
		{
			$url = 'http://'.$tag;
			$class = 'ext';
		}
		*/
		else
		{
			// it's a wiki link
			if (isset($_SESSION['linktracking']) && $_SESSION['linktracking'] && $track)
			{
				$this->TrackLinkTo($tag);
			}
			if (!$assumePageExists && !$this->existsPage($tag))
			{
				$link = '<a class="missingpage" href="'.$this->Href('edit', $tag).'" title="'.T_("Create this page").'">'.$text.'</a>';
			}
			else
			{
				$link = '<a class="'.$class.'" href="'.$this->Href($handler, $tag).'"'.$title_attr.'>'.$text.'</a>';
			}
		}

		//return $url ? '<a class="'.$class.'" href="'.$url.'">'.$text.'</a>' : $text;
		if ('' != $url)
		{
			$result = '<a class="'.$class.'" href="'.$url.'"'.$title_attr.'>'.$text.'</a>';
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
	 * Takes an array of pages returned by LoadAll() and renders it as a table or unordered list.
	 *
	 * @author		{@link http://wikkawiki.org/DotMG DotMG}
	 *
	 * @access		public
	 * @uses	Wakka::Link()
	 * @uses	Wakka::PageTitle()
	 *
	 * @param	mixed	$pages			required: Array of pages returned by LoadAll
	 * @param	array $array_param An associative array representing the options to pass to the function ListPages
	 *         The following keys are currently supported :
	 *            nopagesText: Text to display when the list is empty, default: empty string
	 *            class: a space separated list of classNames used for styling the enclosing div or table tag
	 *            compact: If 0, use table; if 1: use unordered list. Default: 0
	 *            columns: If compact = 0, number of columns of the table. Default: 3
	 *            show_edit_link: If true, each page is followed by an edit link. Default: false.
	 *            show_page_title: If true, show the title of the page after the page name. Default: true.
	 *            sort: Should the data be sorted before being listed? Default: no (no sorting)
	 *  Other possible values for sort:
	 *   ignore_case ou ksort: sort page names, ignoring case
	 *   reverse ou rsort: sort in reverse order, not ignoring case
	 *   ignore_case_reverse ou krsort: sort in reverse order, not ignoring case
	 * @return	string	formated array contents
	 * @todo	Use as a wrapper for the new array functions - avoiding table layout and enhancing scannability of the result!!!
	 */
	function ListPages($pages, $array_param=array())
	{
		$defaut_options = array(
				'nopagesText' => '',
				'class' => '',
				'compact' => 0,
				'columns' => 3,
				'show_edit_link' => false,
				'show_page_title' => true,
				'sort' => 'no');
		$options = array_merge($defaut_options, $array_param);

		$output_edit_link = '';
		$output_page_title = '';
		$output_body = '';
		$class = '';

		if (!$pages)
		{
			return ($options['nopagesText']);
		}
		if ($options['class'])
		{
			$class = ' class="'.str_replace('"', '', $options['class']).'"';
		}
		if ($options['compact'])
		{
			$output_start = "\n<div".$class.'><ul>';
			$output_end = '</ul></div>';
		}
		else
		{
			$output_start = '<table '.$class.'><tr>';
			$output_end = '</tr></table>';
		}
		// sorting
		foreach ($pages as $page)
		{
			$k = strtolower($page['page_tag']);
			$list[strtolower($k)] = $page['page_tag'];
		}
		switch ($options['sort'])
		{
			case 'ignore_case': case 'ksort':
				ksort($list);
				break;
			case 'no': case false: case 0:
				break;
			case 'reverse': case 'rsort':
				rsort($list);
				break;
			case 'ignore_case_reverse': case 'krsort':
				krsort($list);
				break;
			default:
				sort($list);
		}
		$count = 0;
		foreach ($list as $val)
		{
			if ($options['show_edit_link'])
			{
				$output_edit_link = ' <small>['.$this->Link($val, 'edit', WIKKA_PAGE_EDIT_LINK_DESC, false, true, sprintf(WIKKA_PAGE_EDIT_LINK_TITLE, $val)).']</small>';
			}
			if ($options['show_page_title'])
			{
				$output_page_title = ' <span class="pagetitle">['.$this->PageTitle($val).']</span>';
			}
			if ($options['compact'])
			{
				$text = preg_replace('!^Category!i', '', $val);
				$output_link = $this->Link($val, '', $text);
				$output_item_sep_begin = "\n <li>";
				$output_item_sep_end = "</li>";
				$output_body .= $output_item_sep_begin.$output_link.$output_edit_link.$output_page_title.$output_item_sep_end;
			}
			else
			{
				if ($count == intval($options['columns']))
				{
					$output_body .= '</tr><tr>';
					$count = 0;
				}
				$output_link = $this->Link($val);
				$output_item_sep_begin = "\n <td>";
				$output_item_sep_end = "</td>";
				$output_body .= $output_item_sep_begin.$output_link.$output_edit_link.$output_page_title.$output_item_sep_end;
			}
			$count ++;
		}
		return $output_start.$output_body.$output_end;
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
	 * Check if a given string contains prohibited characters.
	 * Currently, these prohibited characters are:
	 *   [ ] { } % + | ? = < > ' " / 0x00-0x1f 0x7f ,
	 *
	 * @param	string $text mandatory:
	 * @return	integer 1 if $text is a wikiname, 0 otherwise
	 * @todo	move regexps to regexp-library		#34
	 * @todo	return a boolean
	 */
	function IsWikiName($text)
	{
		$result = preg_match("/[\[\]\{\}%\+\|\?=<>\'\"\/\\x00-\\x1f\\x7f,]/", html_entity_decode($text));
		return !$result;
	}

	/**
	 *
	 * @param	string	$tag	mandatory: (wiki) pagename the link points to.
	 */
	function TrackLinkTo($tag)
	{
		$_SESSION['linktable'][] = $tag;
	}

	/**
	 *
	 * @return	array
	 */
	function GetLinkTable()
	{
		return $_SESSION['linktable'];
	}

	/**
	 *
	 * @return	void
	 */
	function ClearLinkTable()
	{
		$_SESSION['linktable'] = array();
	}

	/**
	 *
	 * @return	void
	 */
	function StartLinkTracking()
	{
		$_SESSION['linktracking'] = 1;
	}

	/**
	 *
	 * @return	void
	 */
	function StopLinkTracking()
	{
		$_SESSION['linktracking'] = 0;
	}

	/**
	 *
	 * @uses	Wakka::GetLinkTable()
	 * @uses	Wakka::Query()
	 * @uses	Wakka::GetPageTag()
	 * @uses	Config::$table_prefix
	 * @return	void
	 */
	function WriteLinkTable()
	{
		// delete entries for current page from link table
		$tag = $this->GetPageTag();
		$this->Query("
			DELETE
			FROM ".$this->GetConfigValue('table_prefix')."links
			WHERE from_tag = :tag", array(':tag' => $tag)
			);
		// build and insert new entries for current page in link table
		if ($linktable = $this->GetLinkTable())
		{
			$from_tag = $this->GetPageTag();
			$written = array();
			$sql = '';
			$params = array();
			foreach ($linktable as $to_tag)
			{
				$lower_to_tag = strtolower($to_tag);
				if ((!isset($written[$lower_to_tag])) && ($lower_to_tag != strtolower($from_tag)))
				{
					if ($sql)
					{
						$sql .= ', ';
					}
					//$sql .= "(:from_tag, :to_tag)";
					$sql .= "(?, ?)";
					array_push($params, $from_tag);
					array_push($params, $to_tag);
					$written[$lower_to_tag] = 1;
				}
			}
			if($sql)
			{
				$this->Query("
					INSERT INTO ".$this->GetConfigValue('table_prefix')."links VALUES ".$sql, $params);
			}
		}
	}

	/**#@-*/

	/*#@+
	 * @category	Template methods
	 */

	/**
	 * Add a custom header to be inserted inside the <head> section.
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
	 * Output the header for Wikka-pages.
	 *
	 * @uses	Wakka::GetThemePath()
	 * @uses	Wakka::IncludeBuffered()
	 * @return	mixed string with the header of a wikka-page, string with an error-message or FALSE.
	 */
	function Header()
	{
		$filename = 'header.php';
		$path = $this->GetThemePath();
		$header = $this->IncludeBuffered($filename, T_("A header template could not be found. Please make sure that a file called <code>header.php</code> exists in the templates directory."), '', $path);
		return $header;
	}

	/**
	 * Output the footer for Wikka-pages.
	 *
	 * @uses	Wakka::GetThemePath()
	 * @uses	Wakka::IncludeBuffered()
	 * @uses	mixed string with the footer of a wikka-page, string with an error-message or FALSE.
	 */
	function Footer()
	{
		$filename = 'footer.php';
		$path = $this->GetThemePath();
		$footer = $this->IncludeBuffered($filename, T_("A footer template could not be found. Please make sure that a file called <code>footer.php</code> exists in the templates directory."), '', $path);
		return $footer;
	}

	/**
     * Returns a valid template path (defaults to 'default' if theme
	 * does not exist)
	 *
	 * Tries to resolve valid pathname given a 'theme' param in
	 * wikka.config.php.  Failing that, tries to revert to a
	 * "fallback" default theme path (currently 'templates/default').
	 * Failing that, returns NULL.
	 *
	 * @uses	Wakka::GetUser()
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::BuildFullpathFromMultipath()
	 * @uses	Config::$theme
	 * @uses	Config::wikka_template_path
	 * @param  string path_sep Use this to override the OS default
	 * @param  string theme_override Specify a specific theme. Default is NULL (use configuration theme).
	 *
     * @return string A fully-qualified pathname or NULL if none found
	 */
	 function GetThemePath($path_sep = '/', $theme_override = NULL)
	 {
	 	//check if custom theme is set in user preferences
	 	if ($user = $this->GetUser())
		{
			$theme =  (isset($user['theme']) && $user['theme']!='')? $user['theme'] : $this->GetConfigValue('theme');
		}
		else
		{
			$theme = $this->GetConfigValue('theme');
		}
		if(NULL !== $theme_override)
		{
			$theme = $theme_override;
		}
		$path = $this->BuildFullpathFromMultipath($theme, $this->GetConfigValue('wikka_template_path'), $path_sep);
	 	if(FALSE===file_exists($path))
		{
			// Check on fallback theme dir...
			if(FALSE===file_exists('templates'.$path_sep.'default'))
			{
				return NULL;
			}
			else
			{
				return 'templates'.$path_sep.'default';
			}
		}
		return $path;
	}

	/**
	* Build a drop-down menu with a list of available themes
	*
	* This function reads the content of the templates/ and plugins/templates paths and builds
	* a list of available themes. Themes in the plugin tree override default themes with the same
	* name.
	* @since
	* @param string $default_theme optional: marks a specific theme as selected by default
	*/
	function SelectTheme($default_theme='default')
	{
		$plugin = array();
		$core = array();
		// plugin path
		$hdl = opendir('plugins/templates');
		while ($g = readdir($hdl))
		{
			if ($g[0] == '.') continue;
			else
			{
				$plugin[] = $g;
			}
		}
		// default path
		$hdl = opendir('templates');
		while ($f = readdir($hdl))
		{
			if ($f[0] == '.') continue;
			// theme override
			else if (!in_array($f, $plugin))
			{
				$core[] = $f;
			}
		}
		$output = '<select id="select_theme" name="theme">';
		$output .= '<option disabled="disabled">'.sprintf(T_("Default themes (%s)"), count($core)).'</option>';
		foreach ($core as $c)
		{
			$output .= "\n ".'<option value="'.$c.'"';
			if ($c == $default_theme) $output .= ' selected="selected"';
			$output .= '>'.$c.'</option>';
		}
		//display custom themes if any
		if (count($plugin)>0)
		{
			$output .= '<option disabled="disabled">'.sprintf(T_("Custom themes (%s)"), count($plugin)).'</option>';
			foreach ($plugin as $p)
			{
				$output .= "\n ".'<option value="'.$p.'"';
				if ($p == $default_theme) $output .= ' selected="selected"';
				$output .= '>'.$p.'</option>';
			}
		}
		$output .= '</select>';
		echo $output;
	}

	/**#@-*/

	/**
	 * @category	Form methods
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
	function FormOpen($handler='', $tag='', $formMethod='post', $id='', $class='', $file=FALSE, $anchor='')
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
		$anchor = trim($anchor);
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
		if (!empty($tag) && !$this->existsPage($tag))
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
			$attrAction = ' action="'.$this->Href($handler, $tag).$anchor.'"';
		}
		else
		{
			$attrAction = ' action="'.$this->Href($handler, $tag).$anchor.'"';
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

		// add validation key fields used against FormSpoofing
		if('post' == strtolower($formMethod))
		{
			$hidden['CSRFToken'] = $_SESSION['nextCSRFToken'];
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

	/**#@-*/

	/**#@+
	 * @category	Interwiki
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
	 *
	 * @param	string	$name	mandatory: shortcut for the interWiki
	 * @param	string	$url	mandatory: url for the interwiki
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
	 * @todo	for 1.3: in trunk the function returns an empty string if the IW is not in the list
	 */
	function GetInterWikiUrl($name, $tag)
	{
		if (isset($this->interWiki[strtolower($name)]))
		{
			return $this->interWiki[strtolower($name)].$tag;
		}
	}

	/**#@-*/

	/*#@+
	 * @category	Referrers
	 */

	/**
	 * Log REFERRERS.
	 * Store external referrer into table wikka_referrers. The referrer's host is
	 * checked against a blacklist (table wikka_blacklist) and it will be ignored
	 * if it's present at this table.
	 *
	 * @uses	Wakka::cleanUrl()
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::LoadSingle()
	 * @uses	Wakka::Query()
	 * @uses	Config::$table_prefix
	 * @param	$tag
	 * @param	$referrer
	 * @return	void
	 */
	function LogReferrer($tag = '', $referrer = '')
	{
		// fill values
		if (!$tag = trim($tag))
		{
			#$tag = $this->GetPageTag();
			$tag = $this->GetPageTag();
		}
		#if (!$referrer = trim($referrer)) $referrer = $_SERVER["HTTP_REFERER"]; NOTICE
		if (empty($referrer))
		{
			$referrer = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';	#38
		}
		$referrer = trim($this->cleanUrl($referrer));			# secured JW 2005-01-20

		// check if it's coming from another site
		if (!empty($referrer) && !preg_match('/^'.preg_quote(WIKKA_BASE_URL, '/').'/', $referrer))
		{
			$parsed_url = parse_url($referrer);
			$spammer = $parsed_url['host'];
			$blacklist = $this->LoadSingle("
				SELECT *
				FROM ".$this->GetConfigValue('table_prefix')."referrer_blacklist
				WHERE spammer = :spammer", array(':spammer' => $spammer)
				);
			if (FALSE == $blacklist)
			{
				$this->Query("
					INSERT INTO ".$this->GetConfigValue('table_prefix')."referrers
					SET page_tag	= :tag,
						referrer	= :referrer,
						time		= now()",
					array(':tag' => $tag, ':referrer' => $referrer)
					);
				$this->Query("
					INSERT INTO ".$this->GetConfigValue('table_prefix')."referrers (page_tag, referrer, time) VALUES (:tag, :referrer, now())",
					array(':tag' => $tag, ':referrer' => $referrer)
					);
			}
		}
	}

	/**
	 * @uses	Wakka::LoadAll()
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Config::$table_prefix
	 * @param $tag
	 * @return unknown_type
	 */
	function LoadReferrers($tag = '')
	{
		$where = ($tag = trim($tag)) ? "			WHERE page_tag = :tag" : '';
		if('' == $where) {
			$referrers = $this->LoadAll("
				SELECT referrer, COUNT(referrer) AS num
				FROM ".$this->GetConfigValue('table_prefix')."referrers".
				$where."
				GROUP BY referrer
				ORDER BY num DESC", NULL
				);
		} else {
			$referrers = $this->LoadAll("
				SELECT referrer, COUNT(referrer) AS num
				FROM ".$this->GetConfigValue('table_prefix')."referrers".
				$where."
				GROUP BY referrer
				ORDER BY num DESC", array(':tag' => $tag)
				);
		}
		return $referrers;
	}

	/**#@-*/

	/*#@+
	 * @category	PLUGINS: Actions/Handlers
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
	function Action($actionspec, $forceLinkTracking = 0)	// @@@
	{
		// parse action spec and check if we have a syntactically valid action name	[SEC]
		// the regex allows an action name consisting of letters and numbers ONLY
		// and thus provides defense against directory traversal or XSS (via action *name*)
		if (!preg_match('/^\s*([a-zA-Z0-9]+)(\s.+?)?\s*$/', $actionspec, $matches))	# see also #34
		{
			return '<em class="error">'.T_("Unknown action; the action name must not contain special characters.").'</em>';	# [SEC]
		}
		else
		{
			// valid action name, so we pull out the parts, and make the action name lowercase
			$action_name	= strtolower($matches[1]);
			$paramlist		= (isset($matches[2])) ? trim($matches[2]) : '';
		}

		// prepare an array for extract() (in $this->IncludeBuffered()) to work with
		$vars = array();
		// search for parameters if there was more than just a (syntactically valid) action name
		if ('' != $paramlist)
		{
			// match all attributes (key and value)
			preg_match_all('/([a-zA-Z0-9]+)=(\"|\')(.*)\\2/U', $paramlist, $matches);	# [SEC] parameter name should not be empty

			// prepare an array for extract() (in $this->IncludeBuffered()) to work with
			#$vars = array();
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
					// Check to see if linktracking is desired (for
					// instance, when using {{image}} tags to link to
					// other wiki pages
					if(FALSE !== strpos($matches[1][$a], "forceLinkTracking"))
					{
						if(TRUE == $this->htmlspecialchars_ent($matches[3][$a]))
						{
							$forceLinkTracking = 1;
						}
						else
						{
							$forceLinkTracking = 0;
						}
					}
					else
					{
						$vars[$matches[1][$a]] = $this->htmlspecialchars_ent($matches[3][$a]);	// parameter name = sanitized value [SEC]
					}
				}
			}
			$vars['wikka_vars'] = $paramlist; // <<< add the complete parameter-string to the array
		}
		if (!$forceLinkTracking)
		{
				/**
				 * @var boolean holds previous state of LinkTracking before we StopLinkTracking(). It will then be used to test if we should StartLinkTracking() or not.
				 */
				$link_tracking_state = (isset($_SESSION['linktracking'])) ? $_SESSION['linktracking'] : 0;
				$this->StopLinkTracking();
		}
		$result =
		$this->IncludeBuffered(strtolower($action_name).'/'.strtolower($action_name).'.php',
		sprintf(T_("Unknown action \"%s\""), '"'.$action_name.'"'), $vars, $this->GetConfigValue('action_path'));
		if ($link_tracking_state)
		{
			$this->StartLinkTracking();
		}
		return $result;
	}

	/**
	 * Use a handler (on the current page).
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::IncludeBuffered()
         * @uses        Wakka::wrapHandlerError()
	 * @uses	Config::$handler_path
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
		if (strstr($handler, '/'))
		{
			// Observations - MK 2007-03-30
			// extract part after the last slash (if the whole request contained multiple slashes)
			// @@@
			// but should such requests be accepted in the first place?
			// at least it is a SORT of defense against directory traversal (but not necessarily XSS)
			// NOTE that name syntax check now takes care of XSS
			$handler = substr($handler, strrpos($handler, '/')+1);
		}
		// check valid handler name syntax (similar to Action())
		// @todo move regexp to library
		if (!preg_match('/^([a-zA-Z0-9_.-]+)$/', $handler)) // allow letters, numbers, underscores, dashes and dots only (for now); see also #34
		{
			return $this->wrapHandlerError(T_("Unknown handler; the handler name must not contain special characters."));	# [SEC]
		}
		else
		{
			// valid handler name; now make sure it's lower case
			$handler = strtolower($handler);
		}
		$handlerLocation = $handler.'/'.$handler.'.php';	#89
                $tempOutput = $this->IncludeBuffered($handlerLocation, '', '', $this->GetConfigValue('handler_path'));
                if (FALSE===$tempOutput)
                {
                        return $this->wrapHandlerError(sprintf(T_("Sorry, %s is an unknown handler."), '"'.$handlerLocation.'"'));
                }
                return $tempOutput;
	}

        /**
         * Wrap a error message in a content div and an em tag, to avoid breaking the layout on handler errors.
         *
         * @author              {@link http://wikkawiki.org/TormodHaugen Tormod Haugen} (created 2010)
         *
         * @uses        Wakka::htmlspecialchars_ent
         *
         * @param       string $errorMessage    Localized error message to be wrapped to avoid breaking layout
         * @return      string The wrapped error message
         */
        function wrapHandlerError($errorMessage)
        {
                $errorMessage = $this->htmlspecialchars_ent(trim($errorMessage));
                $errorMessage = '<div id="content"><em class="error">'.$errorMessage.'</em></div>';

                return $errorMessage;
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
#echo 'checking path: '.$this->GetConfigValue('handler_path').'/page'.'/'.$handler.'.php'.'<br/>';
		$exists = $this->BuildFullpathFromMultipath($handler.'/'.$handler.'.php', $this->GetConfigValue('handler_path'));
		// return conclusion
		if(TRUE===empty($exists))
		{
			return FALSE;
		}
		return TRUE;
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
	 *			this value is passed to compact() to re-create the variable on formatters/wakka.php
	 * @return	string	output produced by {@link Wakka::IncludeBuffered()} or an error message
	 * @todo	move regexes to central regex library			#34
	 */
	function Format($text, $formatter='wakka', $format_option='')
	{
		// check valid formatter name syntax (same as Handler())
		// the regex allows an action name consisting of letters, numbers, and
		// underscores, hyphens and dots ONLY and thus provides defense against
		// directory traversal or XSS (via handler *name*)
		if (!preg_match('/^([a-zA-Z0-9_.-]+)$/', $formatter)) # see also #34
		{
			$out = '<em class="error">'.T_("Unknown formatter; the formatter name must not contain special characters.").'</em>';	# [SEC]
		}
		else
		{
			// valid formatter name; now make sure it's lower case
			$formatter = strtolower($formatter);
			// prepare variables
			$formatter_location			= $formatter.'.php';
			$formatter_location_disp	= '<code>'.$this->htmlspecialchars_ent($formatter_location).'</code>';	// [SEC] make error (including (part of) request) safe to display
			$formatter_not_found		= sprintf(T_("Formatter \"%s\" not found"),$formatter_location_disp);
			// produce output
			//$out = $this->IncludeBuffered($formatter_location, $this->GetConfigValue('wikka_formatter_path'), $formatter_not_found, FALSE, compact('text', 'format_option')); // @@@
			$out = $this->IncludeBuffered($formatter_location, $formatter_not_found, compact('text', 'format_option'), $this->GetConfigValue('wikka_formatter_path'));
		}
		return $out;
	}

	/**#@-*/

	/*#@+
	 *@category	User
	 */

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
			WHERE name = :username
			LIMIT 1", array(':username' => $username)
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
	 * Load a given user.
	 *
     * in trunk: <b>Replaced by {@link Wakka::existsUser()} or 
     * {@link Wakka::loadUserData()} depending on
	 * purpose!</b>
	 *
	 * @param $name
	 * @param $password
	 * @return unknown_type
	 * @todo	see above
	 */
	function LoadUser($name, $password = 0)
	{
		if(0 === $password) {
			return $this->LoadSingle("
				SELECT *
				FROM ".$this->GetConfigValue('table_prefix')."users
				WHERE name = :name LIMIT 1", array(':name' => $name)
				);
		} else {
			return $this->LoadSingle("
				SELECT *
				FROM ".$this->GetConfigValue('table_prefix')."users
				WHERE name = :name and md5_password = :password
				LIMIT 1",
				array(':name' => $name, ':password' => $password)
				);
		}
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
	 * @uses	Wakka::GetUser()
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Config::$enable_user_host_lookup
	 *
	 * @return	string	name of registered user, or IP address or host name for
	 *			anonymous user
	 * @todo	return only IP address or host name if explicitly requested:
	 *			we may want IP address even if reverse DNS is allowed in config!
	 */
	function GetUserName()
	{
		if ($user = $this->GetUser())
		{
			return $name = $user['name'];
		}

		$ip = $_SERVER['REMOTE_ADDR'];

		if ($this->GetConfigValue('enable_user_host_lookup') == 1)	// #240
		{
			$ip = gethostbyaddr($ip) ? gethostbyaddr($ip) : $ip;
		}

		return $this->anon_username = $ip;
	}

	/**
	 * Get data for logged-in user (NULL if user is not logged in).
	 *
	 * @return	mixed	array with user data, or FALSE if user not logged in
	 */
	function GetUser()
	{
		return (isset($_SESSION['user'])) ? $_SESSION['user'] : NULL;
	}

	/**
	 *
	 * @uses	Wakka::SetPersistentCookie()
	 * @param	$user
	 * @return	void
	 */
	function SetUser($user)
	{
		$_SESSION['user'] = $user;
		$this->SetPersistentCookie('user_name', $user['name']);
		$this->SetPersistentCookie('pass', $user['md5_password']);
		$this->registered = true;
	}

	/**
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::DeleteCookie()
	 * @uses	Wakka::GetUserName()
	 * @uses	Wakka::Query()
	 * @return unknown_type
	 */
	function LogoutUser()
	{
		//unset($_SESSION['show_comments']);
		$csrfToken = $_SESSION['CSRFToken'];
        #$_SESSION = [];
        session_destroy();
        session_id('');
        unset($_SESSION['user']);
		$_SESSION['CSRFToken'] = $csrfToken;
		$this->DeleteCookie('user_name');
		$this->DeleteCookie('pass');
		// Delete this session from sessions table
		$this->Query("DELETE FROM ".$this->GetConfigValue('table_prefix')."sessions WHERE userid='".$this->GetUserName()."' AND sessionid='".session_id()."'");
		// This seems a good as place as any to purge all session records
		// older than PERSISTENT_COOKIE_EXPIRY, as this is not a
		// time-critical function for the user.  The assumption here
		// is that server-side sessions have long ago been cleaned up by PHP.
		db_purgeSessions($this);
		$this->registered = false;
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
	 * @uses	Config::$default_comment_display
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
	 * @uses	Wakka::existsPage()
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
			if ($this->existsUser($username))
			{
				// check if userpage exists and if linking is enabled
				#$formatted_user = ($this->existsPage($username) && ($link == 1)) ? $this->Link($username,'','','','','Open user profile for '.$username,'user') : '<span class="user">'.$username.'</span>'; // @@@ #i18n
				$formatted_user = ($this->existsPage($username) && ((bool) $link)) ? $this->Link($username,'','','','','Open user profile for '.$username,'user') : '<span class="user">'.$username.'</span>'; // @@@ #i18n
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
			$formatted_user = 'anonymous'; // @@@ #i18n T_("(.T_("unregistered user").'") or T_("anonymous")
		}
		return $formatted_user;
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
			$result = $this->registered;
		}
		// named user cached?
		elseif (in_array($username, $this->registered_users))
		{
			$result = TRUE;
		}
		elseif (in_array($username,$this->anon_users))
		{
			$result = FALSE;
		}
		// look up named user in database & cache name
		else
		{
			$user = $this->LoadSingle("
				SELECT `name`
				FROM ".$this->GetConfigValue('table_prefix')."users
				WHERE `name` = :name
				LIMIT 1", array(':name' => $name)
				);
			if (is_array($user))
			{
				$result = TRUE;
				$this->registered_users[] = $user['name'];	// cache actual name as in DB
			}
			else
			{
				// also cache UNregistered usernames
				$this->anon_users[] = $username;		// @@@ declare & document
			}
		}
		return $result;
	}

	/**#@-*/

	/*#@+
	 * @category Comments
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
		// default ($user['show_comments'] overrides COMMENT_NO_DISPLAY in session)
		if ($order === NULL)
		{
            $user = $this->GetUser();
            if(isset($user['show_comments']) && $user['show_comments'] == 'Y') 
            {
                $order = $user['default_comment_display'];
            }
            else if (isset($_SESSION['show_comments'][$tag]))
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
				WHERE page_tag = :tag
					AND (status IS NULL or status != 'deleted')
				ORDER BY time", array(':tag' => $tag)
				);
		}
		elseif ($order == COMMENT_ORDER_DATE_DESC)
		{
			// always returns an array, but it may be empty
			return $this->LoadAll("
				SELECT *
				FROM ".$this->GetConfigValue('table_prefix')."comments
				WHERE page_tag = :tag
					AND (status IS NULL or status != 'deleted')
				ORDER BY time DESC", array(':tag' => $tag)
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
	 * Select and load a single comment.
	 *
	 * @author		{@link http://wikka.jsnx.com/JavaWoman JavaWoman}
	 * @copyright	Copyright � 2005, Marjolein Katsma
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 *
	 * @access	public
	 * @uses	LoadAll()
	 *
	 * @param	integer	$comment_id	required: id of comment to be deleted
	 * @return	array 				associative array with comment data.
	 */
	function loadCommentId($comment_id)
	{
		return $this->LoadSingle("SELECT * FROM ".$this->GetConfigValue('table_prefix')."comments WHERE id = '".$comment_id."'");
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
		if (array_key_exists(end($visited), $transformed_map) && is_array($transformed_map[end($visited)]))
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
		$count = $this->getCount('comments', "page_tag = :tag AND (status IS NULL OR status != 'deleted')", array(':tag' => $tag));
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
		$count = $this->getCount('comments', "page_tag = :tag", $params = array(':tag' => $tag));
		return $count;
	}
	/**
	 * Load the last comments on the wiki, or, if specified, the last comments on a specific page.
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::LoadAll()
	 * @uses	Wakka::GetUser()
	 * @uses	Wakka::IsAdmin()
	 * @uses	Config::$table_prefix
	 *
	 * @param	integer	$limit	optional: number of last comments. default: 50
	 * @param	string	$user	optional: name of user to retrieve comments for
	 * @return	array	the last x comments
	 * @todo	use constant for default limit value (no "magic numbers!")
	 */
	function LoadRecentComments($limit=50, $user='')		// @@@
	{
		$where = 'WHERE';
		if(!empty($user) &&
		   ($this->GetUser() || $this->IsAdmin()))
		{
			$where = " WHERE user = :user AND ";
			return $this->LoadAll("
				SELECT *
				FROM ".$this->GetConfigValue('table_prefix')."comments
				".$where." (status IS NULL or status != 'deleted')
				ORDER BY time DESC
				LIMIT ".intval($limit), array(':user' => $user));
		} else {
			return $this->LoadAll("
				SELECT *
				FROM ".$this->GetConfigValue('table_prefix')."comments
				".$where." (status IS NULL or status != 'deleted')
				ORDER BY time DESC
				LIMIT ".intval($limit));
		}
	}

	/**
	 * Load recently commented pages on the wiki.
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::LoadAll()
	 * @uses	Wakka::GetUser()
	 * @uses	Wakka::IsAdmin()
	 * @uses	Config::$table_prefix
	 *
	 * @param	integer	$limit	optional: number of last comments on different pages. default: 50
	 * @param   string $user optional: list only comments by this user
	 * @return	array	the last comments on x different pages
	 * @todo	use constant for default limit value (no "magic numbers!")
	 */
	function LoadRecentlyCommented($limit = 50, $user = '')	// @@@
	{
		$where = ' AND 1 ';
		$params = NULL;
		if(!empty($user) &&
		   ($this->GetUser() || $this->IsAdmin()))
		{
			$where = " AND comments.user = :user ";
			$params = array(':user' => $user);
		}

		$sql = "
			SELECT comments.id, comments.page_tag, comments.time, comments.comment, comments.user
			FROM ".$this->GetConfigValue('table_prefix')."comments AS comments
			LEFT JOIN ".$this->GetConfigValue('table_prefix')."comments AS c2
				ON comments.page_tag = c2.page_tag
					AND comments.id < c2.id
			WHERE c2.page_tag IS NULL
				AND (comments.status IS NULL or comments.status != 'deleted')
					".$where."
			ORDER BY comments.time DESC
			LIMIT ".intval($limit);
		return $this->LoadAll($sql, $params);
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
		if (!$parent_id)
		{
			$parent_id = 'NULL';
		}
		$params = array(':page_tag' => $page_tag,
		                ':comment' => $comment,
						':parent_id' => ($parent_id == 'NULL') ? null : $parent_id,
						':user' => $user);
		db_saveComment($this, $params);
	}

	/**
	 * Delete a comment.
	 *
	 * @author		{@link http://wikka.jsnx.com/JavaWoman JavaWoman}
	 * @copyright	Copyright � 2005, Marjolein Katsma
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 *
	 * @access	public
	 * @uses	Query()
	 *
	 * @param	integer	$comment_id	required: id of comment to be deleted
	 * @return	boolean 			TRUE if successful, FALSE otherwise.
	 */
	function deleteComment($comment_id)
	{
		$rc = $this->Query("DELETE FROM ".$this->GetConfigValue('table_prefix')."comments ".
							"WHERE id = '".$comment_id."'");
		return $rc;
	}

	/**#@-*/

	/*#@+
	 * @category	ACCESS CONTROL
	 */

	/**
	 * Check if current user is the owner of the current or a specified page.
	 *
	 * @access		public
	 * @uses		Wakka::existsUser()
	 * @uses		Wakka::IsAdmin()
	 * @uses		Wakka::GetUserName()
	 * @uses		Wakka::GetPageOwner()
	 * @uses		Wakka::GetPageTag()
	 *
	 * @param	string	$tag	optional: page to be checked. Default: current page.
	 * @return	boolean	TRUE if the user is the owner, FALSE otherwise.
	 */
	function UserIsOwner($tag = '')
	{

		// if not logged in, user can't be owner!
		if (!$this->GetUser())
		{
			return FALSE;
		}
		// if user is admin, return true. Admin can do anything!
		if ($this->IsAdmin())
		{
			return TRUE;
		}

		// set default tag & check if user is owner
		if (!$tag = trim($tag)) $tag = $this->GetPageTag();
		if ($this->GetPageOwner($tag) == $this->GetUserName()) return TRUE;
	}

	/**
	 * Check if currently logged in user is listed in configuration list as admin.
	 *
	 * @access	public
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::GetUserName()
	 *
	 * @param	string	$user
	 * @return	boolean	TRUE if the user is an admin, FALSE otherwise
	 */
	function IsAdmin($user='')
	{
		$adminstring = $this->GetConfigValue('admin_users');
		$adminarray = explode(',' , $adminstring);
		if(TRUE===empty($user))
		{
			$user = $this->GetUserName();
		}
		else if(is_array($user))
		{
			$user = $user['name'];
		}
		foreach ($adminarray as $admin) {
			if (trim($admin) == $user) return TRUE;
		}
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
	function GetPageOwner($tag = '', $time = '')
	{
		if (!$tag = trim($tag)) $tag = $this->GetPageTag();
		if ($page = $this->LoadPage($tag, $time))
		return $page['owner'];
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
				SET owner = :user
				WHERE tag = :tag
					AND latest = 'Y'
				LIMIT 1", array(':user' => $user, ':tag' => $tag)
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
		$allowed_privs = array('read', 'write', 'comment_read', 'comment_post');
		if(!in_array($privilege, $allowed_privs)) {
			die('<em class="error">'.T_("Invalid ACL privilege!").'</em>');
		}
		$privs = $privilege."_acl";
		if ((!$acl = $this->LoadSingle("
			SELECT $privs
			FROM ".$this->GetConfigValue('table_prefix')."acls
			WHERE `page_tag` = :tag
			LIMIT 1", array(':tag' => $tag)
			)) && $useDefaults)
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
	function LoadAllACLs($tag, $useDefaults = 1)	// @@@
	{
		if ((!$acl = $this->LoadSingle("
			SELECT *
			FROM ".$this->GetConfigValue('table_prefix')."acls
			WHERE `page_tag` = :tag
			LIMIT 1", array(':tag' => $tag)
		)) && $useDefaults)
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
		$allowed_privs = array('read', 'write', 'comment_read', 'comment_post');
		if(!in_array($privilege, $allowed_privs)) {
			die('<em class="error">'.T_("Invalid ACL privilege!").'</em>');
		}
		// the $default will be put in the SET statement of the INSERT SQL for default values. It isn't used in UPDATE.
		$default = " read_acl = '', write_acl = '', comment_read_acl = '', comment_post_acl = '', ";
		// we strip the privilege_acl from default, to avoid redundancy
		$default = str_replace(" ".$privilege."_acl = '',", ' ', $default);
		$privs = $privilege."_acl";
		if ($this->LoadACL($tag, $privilege, 0))
		{
			$list = trim(str_replace("\r", "", $list));
			$this->Query("
				UPDATE ".$this->GetConfigValue('table_prefix')."acls
				SET $privs = :list
				WHERE page_tag = :tag",
				array(':list' => $list, ':tag' => $tag)
				);
		}
		else
		{
			$default_arr = preg_split('/,/', $default);
			array_pop($default_arr);
			foreach($default_arr as $default_value) {
				$fields = preg_split('/=/', $default_value);
				$default_privs .= $fields[0].', ';
				$default_values .= $fields[1].', ';
			}
			$this->Query("
				INSERT INTO ".$this->GetConfigValue('table_prefix')."acls
				(".$default_privs." `page_tag`, ".$privs.") VALUES
				($default_values :tag, :list)",
				array(':tag' => $tag, ':list' => $list)
				);
		}
	}

	/**
	 * Split ACL list on pipes or commas, then trim any
	 * whitespace. Return a pipe-delimited list. Used mainly
	 * to remove carriage returns.
	 *
	 * @param	string	$list	mandatory: List of ACLs to trim
	 * @return unknown_type
	 */
	function TrimACLs($list)
	{
		$trimmed_list = '';
		foreach (explode("\n", $list) as $line)
		{
			$line = trim($line);
			$trimmed_list .= $line."\n";
		}
		return $trimmed_list;
	}

	/**
	 * Check to see if a user is a member of an ACL usergroup (i.e.,
	 * the username appears within a set of "+" symbols).
	 *
	 * @param string $who	mandatory: Username
	 * @param string $group mandatory: Name of page with list of users
	 * @return boolean true if $who is member of $group
	 */
    function isGroupMember($who, $group)
    {
        $thegroup=$this->LoadPage($group);
        if (isset($thegroup)) {
            $search = "+".$who."+"; // In the GroupListPages, the participants logins have to be embbeded inside '+' signs
            return (boolean)(substr_count($thegroup["body"], $search));
        }
        else return false;
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
		// set defaults
		if (!$tag) $tag = $this->GetPageTag();
		if (!$username) $username = $this->GetUserName();

        // Get a user object for the named user
        $user = ($username == $this->GetUserName()) ? $this->GetUser() : $this->LoadUser($username);

		// If user is owner or admin, return true.
		// Owner and admin can do anything!
        if ($user != FALSE) {
           if ($this->IsAdmin($username) || $this->GetPageOwner($tag) == $username) return TRUE;
        }

		// see whether user is registered
        $registered = $user != FALSE;

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
		foreach (explode("\n", $acl) as $line)
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
					// return ($this->registered) ? !$negate : false;
					return ($registered) ? !$negate : $negate;
				// aha! a user entry.
				default:
					if (strtolower($line) == strtolower($username))
					{
						return !$negate;
					}
                    // this may be a UserGroup so we check if $user
					// is part of the group
                    else if (($this->isGroupMember($username, $line)))
                    {
                        return !$negate;
                    }
				}
			}
		}

		// tough luck.
		return FALSE;
	}

	// ANTI-SPAM
	/**
	 * Read contents of badwords file.
	 *
	 * Reads the content of the badwords file. Return contents as string if found, FALSE if not.
	 *
	 * @author		{@link http://wikka.jsnx.com/JavaWoman JavaWoman}
	 * @copyright	Copyright � 2005, Marjolein Katsma
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 * @version		0.5
	 *
	 * @uses      DEFAULT_BADWORDS_PATH
	 * @uses      Config::$badwords_path
	 * @uses      Wakka::normalizeLines()
	 *
	 * @access		public
	 *
	 * @return		mixed		normalized file content (sorted) if found, FALSE if not
	 */
	function readBadWords()
	{
		$badwordspath = $this->GetConfigValue('badwords_path', DEFAULT_BADWORDS_PATH);
		if (file_exists($badwordspath))
		{
			$aBadWords = file($badwordspath);				# get file as array so we can...
			$aBadWords = array_unique($aBadWords);			# ...remove duplicates...
			function _rot13($val) {
				return str_rot13($val);
			};
			$aBadWords = array_map("_rot13", $aBadWords);

			natcasesort($aBadWords);						# ...and sort
			$badwords = $this->normalizeLines(implode('',$aBadWords));	# turn back into string
		}
		else
		{
			$badwords = FALSE;
		}
		return $badwords;
	}

	/**
	 * Writes or rewrites the badwords file from a string with one word per line.
	 *
	 * Input must be a string with (preferably) one word per line; empty lines are filtered.
	 * If the file exists, it is overwritten with the new content.
	 * Returns TRUE if successful, FALSE otherwise.
	 *
	 * @author		{@link http://wikka.jsnx.com/JavaWoman JavaWoman}
	 * @copyright	Copyright � 2005, Marjolein Katsma
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 * @version		0.6
	 *
	 * @uses      DEFAULT_BADWORDS_PATH
	 * @uses      Config::$badwords_path
	 * @uses      Wakka::writeFile()
	 *
	 * @access		public
	 * @uses		writeFile()
	 *
	 * @param		string	$lines	lines with one bad word on each
	 * @return		mixed			bytes written if successful, FALSE otherwise.
	 */
	function writeBadWords($lines)
	{
		$badwordspath = $this->GetConfigValue('badwords_path', DEFAULT_BADWORDS_PATH);
		$rc = FALSE;
		if (file_exists($badwordspath))
		{
			// build content
			$lines = $this->normalizeLines($lines);			# normalize line endings (needed for explode!)
			$lines = preg_replace('/[ \t]+/',"\n",$lines);	# split any multiple-word lines
			$aBadWords = explode("\n",$lines);				# turn into array so we can...
			$aBadWords = array_unique($aBadWords);			# ...remove duplicates
			natcasesort($aBadWords);						# ...and sort
			$badwords = '';
			foreach ($aBadWords as $word)
			{
				if ('' !== $word) $badwords .= str_rot13($word)."\n";	# get rid of empty lines
			}
			$content = trim($badwords);
			// write to file
			$rc = $this->writeFile($badwordspath,$content);
		}
		return $rc;											# number of bytes written or FALSE if writing failed
	}

	/**
	 * Retrieves badwords in a format ready for a RegEx, with '|' between each word.
	 *
	 * Turns the content of the badwords file into a RegEx (minus delimiters).
	 * Return contents as a string if there is any; FALSE if file not found or empty.
	 *
	 * @author		{@link http://wikka.jsnx.com/JavaWoman JavaWoman}
	 * @copyright	Copyright � 2005, Marjolein Katsma
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 * @version		0.5
	 *
	 * @access		public
	 * @uses		readBadWords()
	 *
	 * @return		mixed		RegEx with words if found and not empty, FALSE otherwise
	 */
	function getBadWords()
	{
		$badwords = $this->readBadWords();
		if (FALSE === $badwords || '' == $badwords)
		{
			return FALSE;
		}
		else
		{
			return '('.str_replace("\n",'|',$badwords).')';
		}
	}

	/**
	 * Check content to see if it contains any bad words.
	 *
	 * Uses a RegEx built by getBadWords() to check the given content.
	 * Returns TRUE if teh content contains any of the bad words, FALSE otherwise.
	 *
	 * @author		{@link http://wikka.jsnx.com/JavaWoman JavaWoman}
	 * @copyright	Copyright � 2005, Marjolein Katsma
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 * @version		0.5
	 *
	 * @access		public
	 * @uses		getBadWords()
	 * @todo
	 *
	 * @param		string $content	string to check for occurrence of bad words
	 * @return		boolean			TRUE if content contains badwords, FALSE otherwise
	 */
	function hasBadWords($content)
	{
		$re = $this->getBadWords();
		if (FALSE === $re)
		{
			return FALSE;					# no match since no words are defined
		}
		else
		{
			return preg_match('/'.$re.'/i',$content);		# case-insensitive comparison
		}
	}

	/**#@-*/

	/**
	 * Build a (possibly valid) filepath from a delimited list of paths
	 *
	 * This function takes a list of paths delimited by ":"
	 * (Unix-style), ";" (Window-style), or "," (Wikka-style)  and
	 * attempts to construct a fully-qualified pathname to a specific
	 * file.  By default, this function checks to see if the file
	 * pointed to by the fully-qualified pathname exists.  First valid
	 * match wins.  Disabling this feature will return the first valid
	 * constructed path (i.e, a path containing a valid directory, but
	 * not necessarily pointing to an existant file).
	 *
	 * @param string $filename mandatory: filename to be used in
	 *			construction of fully-qualified filepath
	 * @param string $pathlist mandatory: list of
	 *			paths (delimited by ":", ";", or ",")
	 * @param  boolean $checkIfFileExists optional: if TRUE, returns
	 *			only a pathname that points to a file that exists
	 *			(default)
	 * @return string A fully-qualified pathname or NULL if none found
	 */
	function BuildFullpathFromMultipath($filename, $pathlist, $path_sep = '/', $checkIfFileExists=TRUE)
	{
		$paths = preg_split('/;|:|,/', $pathlist);
		if(empty($paths[0])) return NULL;
		if(FALSE === $checkIfFileExists)
		{
			// Just return first directory that exists
			foreach($paths as $path)
			{
				$path = trim($path);
				if(file_exists($path))
				{
					return $path.$path_sep.$filename;
				}
			}
			return NULL;
		}
		foreach($paths as $path)
		{
			$path = trim($path);
			$fqfn = $path.$path_sep.$filename;
			if(file_exists($fqfn)) return $fqfn;
		}
		return NULL;
	}

	/**
	 * MAINTENANCE
	 */

	/**
	 * Purge referrers and old page revisions.
	 *
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::Query()
	 * @uses	Config::$referrers_purge_time
	 * @uses	Config::$pages_purge_time
	 * @uses	Config::$table_prefix
	 *
	 */
	function Maintenance()
	{ /*
		// purge referrers
		if ($days = $this->GetConfigValue("referrers_purge_time"))
		{
			$this->Query("
				DELETE FROM ".$this->GetConfigValue('table_prefix')."referrers
				WHERE time < date_sub(now(), interval :days day)",
				array(':days' => $days)
			);
		}

		// purge old page revisions
		if ($days = $this->GetConfigValue("pages_purge_time"))
		{
			$this->Query("
				DELETE FROM ".$this->GetConfigValue('table_prefix')."pages
				WHERE time < date_sub(now(), interval :days day)
					AND latest = 'N'", array(':days' => $days)
				);
			$this->Query("delete from ".$this->GetConfigValue('table_prefix')."pages where time < date_sub(now(), interval :days day) and latest = 'N'", array(':days' => days));
		} */
	}

	/**
	 * Add page to breadcrumb queue
	 *
	 * @uses $_[SESSION]
	 * @uses session_id()
	 * @uses Wakka::GetConfigValue()
	 * @uses Config::$num_breadcrumb_nodes
	 * @uses Config::$enable_breadcrumbs
	 */
	function AddBreadcrumb($page) {
		if(0 != $this->GetConfigValue('enable_breadcrumbs')) {
			if(isset($_SESSION['breadcrumbs'])) {
				$q = new SplQueue();
				$q->unserialize($_SESSION['breadcrumbs']);
				if($page != $q->top()) {
					while($q->count() >= $this->GetConfigValue('num_breadcrumb_nodes')) {
						$q->dequeue();
					}
					$q->enqueue($page);
					$_SESSION['breadcrumbs'] = $q->serialize();
				}
			}
			else if (isset($_SESSION['user'])) {
					$q = new SplQueue();
					$q->enqueue($page);
					$_SESSION['breadcrumbs'] = $q->serialize();
			}
		}
	}

	/**
	 * Return breadcrumb string
	 **/
	 function StringifyBreadcrumbs() {
		$output = "<ul class=\"breadcrumb\" id=\"breadcrumb\">";
		if(0 != $this->GetConfigValue('enable_breadcrumbs') &&
		   isset($_SESSION['user']) &&
		   isset($_SESSION['breadcrumbs'])) {
			$delimiter = $this->GetConfigValue('breadcrumb_node_delimiter');
			$q = new SplQueue();
			$q->unserialize($_SESSION['breadcrumbs']);
			$q->rewind();
			$output .= "<li>";
			$output .= "<a href=\"".$this->Href('', $q->current())."\">".$q->current()."</a>";
			$q->next();
			while($q->valid()) {
				$output .= " $delimiter ";
				$output .= "</li><li>";
				$output .= "<a href=\"".$this->Href('', $q->current())."\">".$q->current()."</a>";
				$q->next();
			}
			$output .= "</li></ul>";
		}
		return $output;
	}


	/**
	 * THE BIG EVIL NASTY ONE!
	 *
	 * @uses	Wakka::Footer()
	 * @uses	Wakka::GetUser()
	 * @uses	Wakka::GetCookie()
	 * @uses	Wakka::GetMicroTime()
	 * @uses	Wakka::Handler()
	 * @uses	Wakka::Header()
	 * @uses	Wakka::Href()
	 * @uses	Wakka::LoadAllACLs()
	 * @uses	Wakka::LoadPage()
	 * @uses	Wakka::LoadUser()
	 * @uses	Wakka::LogReferrer()
	 * @uses	Wakka::ReadInterWikiConfig()
	 * @uses	Wakka::Redirect()
	 * @uses	Wakka::SetCookie()
	 * @uses	Wakka::SetUser()
	 * @uses	Wakka::SetPage()
	 * @uses	Config::$root_page
	 * @param $tag
	 * @param $method
	 * @return unknown_type
	 */
	function Run($tag, $method = '')
	{
		$newtag = '';
		// Set default cookie path
		$base_url_path = preg_replace('/wikka\.php/', '', $_SERVER['SCRIPT_NAME']);
		$this->wikka_cookie_path = ('/' == $base_url_path) ? '/' : substr($base_url_path,0,-1);

		// do our stuff!
		$this->wikka_url = ((bool) $this->GetConfigValue('rewrite_mode')) ? WIKKA_BASE_URL : WIKKA_BASE_URL.WIKKA_URL_EXTENSION;
		$this->config['base_url'] = $this->wikka_url; #backward compatibility

		if (!$this->handler = trim($method)) $this->handler = 'show';
		if (!$this->tag = trim($tag)) $this->Redirect($this->Href('', $this->GetConfigValue('root_page')));
		if ($this->GetUser())
		{
			$this->registered = true;
		}
		else
		{
			if ($user = $this->LoadUser($this->GetCookie('user_name'), $this->GetCookie('pass'))) $this->SetUser($user);
			if ((isset($_COOKIE['wikka_user_name'])) && ($user = $this->LoadUser($_COOKIE['wikka_user_name'], $_COOKIE['wikka_pass'])))
			{
				//Old cookies : delete them
				SetCookie('wikka_user_name', '', 1, $this->wikka_cookie_path);
				$_COOKIE['wikka_user_name'] = '';
				SetCookie('wikka_pass', '', 1, $this->wikka_cookie_path);
				$_COOKIE['wikka_pass'] = '';
				$this->SetUser($user);
			}
		}
		$this->SetPage($this->LoadPage($tag, $this->GetSafeVar('time', 'get'))); #312

		$this->LogReferrer();
		$this->ACLs = $this->LoadAllACLs($this->GetPageTag());
		$this->ReadInterWikiConfig();
		if(!($this->GetMicroTime()%3)) $this->Maintenance();

        $content = '';
		if (preg_match('/\.(xml|mm)$/', $this->GetHandler()))
		{
			header('Content-type: text/xml');
			$content = $this->Handler($this->GetHandler());
		}
		// raw page handler
		elseif ($this->GetHandler() == "raw")
		{
			header('Content-type: text/plain');
			$content = $this->Handler($this->GetHandler());
		}
		// grabcode page handler
		elseif ($this->GetHandler() == 'grabcode')
		{
			$content = $this->Handler($this->GetHandler());
		}
		elseif (preg_match('/\.(gif|jpg|png)$/', $this->GetHandler()))		# should not be necessary
		{
			header('Location: images/' . $this->GetHandler());
            exit;
		}
		elseif (preg_match('/\.css$/', $this->GetHandler()))					# should not be necessary
		{
			header('Location: css/' . $this->GetHandler());
            exit;
		}
		elseif(0 !== strcmp($newtag = preg_replace('/\s+/', '_', $tag), $tag))
		{
			header("Location: ".$this->Href('', $newtag));
            exit;
		}
		elseif($this->GetHandler() == 'html')
		{
			header('Content-type: text/html');
			$content = $this->Handler($this->GetHandler());
		}
		elseif($this->GetHandler() == 'csv')
		{
			header('Content-type: text/html');
			$content = $this->Handler($this->GetHandler());
		}
		elseif($this->GetHandler() == 'reveal')
		{
			$content = $this->Handler($this->GetHandler());
		}
		elseif( $this->GetHandler() == 'show' && pathinfo($this->GetPageTag(), PATHINFO_EXTENSION) == 'md' && $this->page['body'] != '' )
		{
			$this->Handler($this->handler = 'md');
			$content = $this->Header();
			$content .= $this->Handler($this->GetHandler());
		    $content .= $this->Footer();
		}
		else
		{
			$content = $this->Header();
			$content .= $this->Handler($this->GetHandler());
			$content .= $this->Footer();
		}

        /**
         * Use gzip compression if possible.
         */
        /*
        if ( isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strstr ($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') && function_exists('gzencode') ) #38
        {
            // Tell the browser the content is compressed with gzip
            header ("Content-Encoding: gzip");
            $page_output = gzencode($content);
            $page_length = strlen($page_output);
        } else {
            $page_output = $content;
            $page_length = strlen($page_output);
        }
        */

        $etag =  md5($content);
        header('ETag: '.$etag);
    	$page_length = strlen($content);
        header('Content-Length: '.$page_length);

        echo $content;
	}
}
?>
