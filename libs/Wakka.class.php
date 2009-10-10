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
 * @copyright Copyright 2006-2009 {@link http://wikkawiki.org/CreditsPage Wikka Development Team}
 */

/**
 * Time to live for client-side cookies in seconds (90 days)
 */ 
if(!defined('PERSISTENT_COOKIE_EXPIRY')) define('PERSISTENT_COOKIE_EXPIRY', 7776000);

// i18n TODO:move to language file
if(!defined('CREATE_THIS_PAGE_LINK_TITLE')) define('CREATE_THIS_PAGE_LINK_TITLE', 'Create this page');
if(!defined('DEFAULT_THEMES_TITLE')) define('DEFAULT_THEMES_TITLE', 'Default themes (%s)'); //%s: number of available themes
if(!defined('CUSTOM_THEMES_TITLE')) define('CUSTOM_THEMES_TITLE', 'Custom themes (%s)'); //%s: number of available themes

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
	 * Hold record for the current page.
	 *
	 * @access	private
	 * @var		array
	 */
	var $page;
	
	/**
	 * Hold the name ("tag) of the current page.
	 *
	 * @access	private
	 * @var		string
	 */
	var $tag;
	
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
	
	/**
	 * Hold the Wikka version.
	 *
	 * @var		string
	 */
	var $VERSION;
	
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
	
	/**
	 * Title of the page to insert in the <title> element.
	 *
	 * @access	public
	 * @var		string
	 */
	var $page_title = '';

	/**#@+
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

	/**
	 * Constructor.
	 * Database connection is established when the main class Wakka is constructed.
	 *
	 * @uses	Config::$mysql_database
	 * @uses	Config::$mysql_host
	 * @uses	Config::$mysql_password
	 * @uses	Config::$mysql_user
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
				$this->dblink = FALSE;
			}
		}
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
	 * @uses	Config::$sql_debugging
	 * @uses	Wakka::GetMicroTime()
	 *
	 * @param	string	$query	mandatory: the query to be executed.
	 * @param	resource $dblink optional: connection to the database
	 * @return	array	the result of the query.
	 * 
	 */
	function Query($query, $dblink='')
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
		if (!$result = mysql_query($query, $dblink))
		{
			ob_end_clean();
			die("Query failed: ".$query." (".mysql_error().")"); #i18n
		}
		if ($object && $this->config['sql_debugging'])
		{
			$time = $this->GetMicroTime() - $start;
			$this->queryLog[] = array(
				"query"		=> $query,
				"time"		=> $time);
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
	 * @todo	for 1.3: check if indeed false is returned (compare with trunk)
	 */
	function LoadSingle($query) 
	{ 
		if ($data = $this->LoadAll($query)) 
		return $data[0]; 
	}
	
	/**
	 * Return all results of a query executed on the database.
	 *
	 * @uses	Wakka::Query()
	 *
	 * @param	string $query mandatory: the query to be executed
	 * @return	array the result of the query.
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
	 * @return	integer	number of matches returned by MySQL
	 */
	function getCount($table, $where='')							# JW 2005-07-16
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
	 * 
	 * @param $major
	 * @param $minor
	 * @param $subminor
	 * @return unknown_type
	 * @todo	for 1.3: compare with trunk-version!
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

		if ($mysql_major > $major) 
		{
			return 1;
		} 
		else 
		{
			if (($mysql_major == $major) && ($mysql_minor >= $minor) && ($mysql_subminor >= $subminor)) 
			{
				return 1;
			} 
			else 
			{
				return 0;
			}
		}
	}
	
	/**#@-*/

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
	 * @author		{@link http://wikka.jsnx.com/JavaWoman JavaWoman}
	 * @copyright	Copyright (c) 2005, Marjolein Katsma
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
	 * @param	string $html mandatory: HTML to be secured
	 * @return	string sanitized HTML
	 */
	function ReturnSafeHTML($html)
	{
        $safehtml_classpath =
		$this->GetConfigValue('safehtml_path').DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'safehtml.php';
        require_once $safehtml_classpath;

		// Instantiate the handler
		$safehtml =& new safehtml();

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
	 * @copyright	Copyright (c) 2004, Marjolein Katsma
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 * @version		1.0
	 *
	 * @access		public
	 * @uses		Wakka::hsc_secure()
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
	 * @access	public
	 * @since	Wikka 1.1.6.0
	 * @version	1.2
	 *
	 * @uses	Wakka::hsc_secure()
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
	 * @todo	later - maybe) support full range of situations where (in SGML)
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
	 * @since		Wikka 1.1.7
	 * @version		1.0
	 * @license		http://www.gnu.org/copyleft/lgpl.html
	 * 				GNU Lesser General Public License
	 * @copyright	Copyright 2007, {@link http://wikkawiki.org/CreditsPage
	 * 				Wikka Development Team}
	 *
	 * @access	public
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
		return strtr($string,$aTransSpecchar);
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
	 * @return	string	sanitized value of $_GET[$varname] (or $_POST, $_COOKIE, depending on $gpc)
	 */
	function GetSafeVar($varname, $gpc='get')
	{
		$safe_var = null;
		if ($gpc == 'post')
		{
			$safe_var = isset($_POST[$varname]) ? $_POST[$varname] : null;
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
	 * Create and store a secret key ("session key").
	 *
	 * Creates a random value and a random field name to be used to pass on the value.
	 * The key,value pair is stored in the session as a serialized array.
	 *
	 * @author		{@link http://wikkawiki.org/JavaWoman JavaWoman}
	 * @copyright	Copyright (c) 2005, Marjolein Katsma
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 * @version		0.5
	 *
	 * @access		public
	 *
	 * @param		string	$keyname	required: name under which created secret key should be stored in the session
	 * @return		array				fieldname and key value.
	 */
	function createSessionKey($keyname)
	{
		// create key and field name for it
		$key = md5(getmicrotime());
		$field = 'f'.substr(md5($key.getmicrotime()),0,10);
		// store session key
		$_SESSION[$keyname] = serialize(array($field,$key));
		// return name, value pair
		return array($field,$key);
	}
	
	/**
	 * Retrieve a secret session key.
	 *
	 * Retrieves a named secret key and returns the result as an array with name,value pair.
	 * Returns FALSE if the key is not found.
	 *
	 * @author		{@link http://wikkawiki.org/JavaWoman JavaWoman}
	 * @copyright	Copyright (c) 2005, Marjolein Katsma
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 * @version		0.5
	 *
	 * @access		public
	 *
	 * @param		string	$keyname	required: name of secret key to retrieve from the session
	 * @return		mixed				array with name,value pair on success, FALSE if entry not found.
	 */
	function getSessionKey($keyname)
	{
		if (!isset($_SESSION[$keyname]))
		{
			return FALSE;
		}
		else
		{
			$aKey = unserialize($_SESSION[$keyname]);		# retrieve secret key data
			unset($_SESSION[$keyname]);						# clear secret key
			return $aKey;
		}
	}
	
	/**
	 * Check if a user-provided key/value matches the one stored in the server-provided "session key".
	 *
	 * <p>Used to defend against FormSpoofing: each form gets a unique key+value which are stored 
	 * on the server(session) as well as send to the user (hidden form fields). If the user $_POSTs data,
	 * there is a check if key+value are included and match those stored in the session. Otherwise the data is 
	 * discarded.</p>
	 * 
	 * Make sure to check for identity TRUE (TRUE === returnval), do not evaluate return value
	 * as boolean!
	 *
	 * @author		{@link http://wikkawiki.org/JavaWoman JavaWoman}
	 * @copyright	Copyright (c) 2005, Marjolein Katsma
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 * @version		0.5
	 *
	 * @access		public
	 * @param		array	$aKey	required: [0] fieldname, [1] key value.
	 * @param		string	$method	optional: form method; default post;
	 * @return		mixed	TRUE if correct name,value found; reason for failure otherwise.
	 * @todo	replace $method by $useGet=FALSE (easier since we only have two methods)
	 * @todo	do we need error messages here? If not, return FALSE instead (more logical). 
	 * @todo	prepare strings for internationalization 
	 */
	function hasValidSessionKey($aKey, $method='post')
	{
		// get pair to look for
		list($ses_field,$ses_key) = $aKey;
		// check method and prepare what to look for
		if (isset($method))
		{
			$aServervars = ($method == 'get') ? $_GET : $_POST;
		}
		else
		{
			$aServervars = $_POST;					# default
		}
	
		// check passed values
		if (!isset($aServervars[$ses_field]))
		{
			return 'form no key';					# key not present
		}
		elseif ($aServervars[$ses_field] != $ses_key)
		{
			return 'form bad key';					# incorrect value passed
		}
		else
		{
			return TRUE;							# all is well
		}
	}
	
	/**#@-*/

	/**#@+
	 * @category	Variable-related methods
	 */
	
	/**
	 * Get the name ("tag") of the current page.
	 *
	 * @uses	Wakka::$tag
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
	 * @return	string
	 */
	function GetPageTime() 
	{ 
		return $this->page["time"]; 
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
	function GetConfigValue($name) 
	{ 
		return (isset($this->config[$name])) ? $this->config[$name] : NULL; 
	}
	
	/**
	 * Get the name of the Wiki.
	 *
	 * @uses	Config::$wakka_name
	 * @return	string the name of the Wiki.
	 */
	function GetWakkaName() 
	{ 
		return $this->GetConfigValue("wakka_name"); 
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
	
	/**#@-*/
	
	/**#@+
	 * @category	Page
	 */
	
	/**
	 * 
	 * @param $tag
	 * @param $time
	 * @param $cache
	 * @return unknown_type
	 * @todo	for 1.3: compare with trunk
	 */
	function LoadPage($tag, $time = "", $cache = 1) 
	{
		// retrieve from cache
		if (!$time && $cache) {
			$page = isset($this->pageCache[$tag]) ? $this->pageCache[$tag] : null;
			if ($page=="cached_nonexistent_page") return null;
		}
		// load page
		if (!isset($page)) $page = $this->LoadSingle("select * from ".$this->config["table_prefix"]."pages where tag = '".mysql_real_escape_string($tag)."' ".($time ? "and time = '".mysql_real_escape_string($time)."'" : "and latest = 'Y'")." limit 1");
		// cache result
		if ($page && !$time) {
			$this->pageCache[$page["tag"]] = $page;
		} elseif (!$page) {
			$this->pageCache[$tag] = "cached_nonexistent_page";
		}
		return $page;
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function IsLatestPage() 
	{
		return $this->latest;
	}
	
	/**
	 * 
	 * @param $tag
	 * @return unknown_type
	 */
	function GetCachedPage($tag) 
	{ 
		return (isset($this->pageCache[$tag])) ? $this->pageCache[$tag] : null; 
	}
	
	/**
	 * 
	 * @param $page
	 * @return unknown_type
	 */
	function CachePage($page) 
	{ 
		$this->pageCache[$page["tag"]] = $page; 
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
	 * 
	 * @param $page
	 * @return unknown_type
	 */
	function SetPage($page) 
	{ 
		$this->page = $page; 
		if ($this->page["tag"]) $this->tag = $this->page["tag"]; 
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
	 * LoadPageById loads a page whose id is $id.
	 * 
	 * @uses	Wakka::LoadSingle()
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Config::$table_prefix
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
			WHERE id = '".mysql_real_escape_string($id)."'
			LIMIT 1"
			);
	}
	
	/**
	 * 
	 * @param $page
	 * @return unknown_type
	 */
	function LoadRevisions($page)
	{ 
		return $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where tag = '".mysql_real_escape_string($page)."' order by id desc"); 
	}
	
	/**
	 * 
	 * @param $tag
	 * @return unknown_type
	 */
	function LoadPagesLinkingTo($tag) 
	{ 
		return $this->LoadAll("select from_tag as tag from ".$this->config["table_prefix"]."links where to_tag = '".mysql_real_escape_string($tag)."' order by tag"); 
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function LoadRecentlyChanged()
	{
		if ($pages = $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where latest = 'Y' order by id desc"))
		{
			foreach ($pages as $page)
			{
				$this->CachePage($page);
			}
			return $pages;
		}
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function LoadWantedPages() 
	{ 
		return $this->LoadAll("select distinct ".$this->config["table_prefix"]."links.to_tag as tag,count(".$this->config["table_prefix"]."links.from_tag) as count from ".$this->config["table_prefix"]."links left join ".$this->config["table_prefix"]."pages on ".$this->config["table_prefix"]."links.to_tag = ".$this->config["table_prefix"]."pages.tag where ".$this->config["table_prefix"]."pages.tag is NULL group by ".$this->config["table_prefix"]."links.to_tag order by count desc"); 
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
		$pre = $this->config["table_prefix"];
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
	 * 
	 * @return unknown_type
	 * @todo	for 1.3:different in trunk: returns owner, too, to be used instead of LoadAllPages()
	 */
	function LoadPageTitles() 
	{ 
		return $this->LoadAll("select distinct tag from ".$this->config["table_prefix"]."pages order by tag"); 
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
		return $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where latest = 'Y' order by tag"); 
	}

	/**
	 * Save a page.
	 * 
	 * @uses	Config::$table_prefix
	 * @uses	Config::$wikiping_server
	 * @uses	Wakka::GetPingParams()
	 * @uses	Wakka::GetUser()
	 * @uses	Wakka::GetUserName()
	 * @uses	Wakka::HasAccess
	 * @uses	Wakka::LoadPage()
	 * @uses	Wakka::Query()
	 * @uses	Wakka::WikiPing()
	 * @param string $tag mandatory:name of the page
	 * @param string $body mandatory:content of the page
	 * @param string $note mandatory:edit-note
	 * @param $owner
	 * @todo for 1.3:in trunk the page-title is stored together with the page
	 */
	function SavePage($tag, $body, $note, $owner=null)
	{
		// get current user
		$user = $this->GetUserName();

		// TODO: check write privilege
		if ($this->HasAccess("write", $tag))
		{
			// If $owner is specified, don't do an owner check 
			if(empty($owner))
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
			}

			// set all other revisions to old
			$this->Query("
				UPDATE ".$this->config["table_prefix"]."pages
				SET latest = 'N'
				WHERE tag = '".mysql_real_escape_string($tag)."'"
				);
				
			// add new revision
			$this->Query("insert into ".$this->config["table_prefix"]."pages set ".
				"tag = '".mysql_real_escape_string($tag)."', ".
				"time = now(), ".
				"owner = '".mysql_real_escape_string($owner)."', ".
				"user = '".mysql_real_escape_string($user)."', ".
				"note = '".mysql_real_escape_string($note)."', ".
				"latest = 'Y', ".
				"body = '".mysql_real_escape_string($body)."'");

			// WikiPing
			if ($pingdata = $this->GetPingParams($this->config["wikiping_server"], $tag, $user, $note))
				$this->WikiPing($pingdata);
		}
	}
	
	/**
	 * 
	 * @return unknown_type
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
	
	/**#@-*/
	
	/**#@+
	 * @category	Search methods
	 */
		
	/**
	 * 
	 * @param $phrase
	 * @param $caseSensitive
	 * @return unknown_type
	 */
	function FullTextSearch($phrase, $caseSensitive = 0)
	{
		$id = '';
		// Should work with any browser/entity conversion scheme
		$search_phrase = mysql_real_escape_string($phrase);
		if ( 1 == $caseSensitive ) $id = ', id';
		$sql  = 'select * from '.$this->config['table_prefix'].'pages';
		$sql .= ' where latest = '.  "'Y'"  .' and match(tag, body'.$id.')';
		$sql .= ' against('.  "'$search_phrase'"  .' IN BOOLEAN MODE)';
		$sql .= ' order by time DESC';
		
		$data = $this->LoadAll($sql);

		return $data;
	}
		
	/**
	 * 
	 * @param $phrase
	 * @return unknown_type
	 */
	function FullCategoryTextSearch($phrase) 
	{ 
		return $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where latest = 'Y' and match(body) against('".mysql_real_escape_string($phrase)."' IN BOOLEAN MODE)"); 
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
		if (file_exists('config/'.$menu_file))
		{
			$menu_src = $this->IncludeBuffered($menu_file, '', '', 'config/');
			$menu_array = explode("\n", $menu_src);
			$menu_output = '<ul id="'.$menu.'">'."\n";
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
	 * @uses	Config::$geshi_path
	 * @uses	Config::$geshi_header
	 * @uses	Config::geshi_languages_path
	 * @uses	Config::$geshi_line_numbers
	 * @uses	Config::$geshi_tab_width
	 * @uses	GeShi
	 *
	 * @param	string	$sourcecode	required: source code to be highlighted
	 * @param	string	$language	required: language spec to select highlighter
	 * @param	integer	$start		optional: start line number; if supplied and >= 1 line numbering
	 * 			will be turned on if it is enabled in the configuration.
	 * @return	string	code block with syntax highlighting classes applied
	 * @todo	support for GeSHi line number styles
	 * @todo	enable error handling
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
		// comments added to make GeSHi-highlighted block visible in code JW/20070220
		return '<!--start GeSHi-->'."\n".$geshi->parse_code()."\n".'<!--end GeSHi-->'."\n";
	}
	
	/**#@-*/
	
	/**#@+
	 * @category WikiPing
	 * @author	DreckFehler
	 */
	
	/**
	 * 
	 * @param $host
	 * @param $data
	 * @param $contenttype
	 * @param $maxAttempts
	 * @return unknown_type
	 */ 
	function HTTPpost($host, $data, $contenttype="application/x-www-form-urlencoded", $maxAttempts = 5) 
	{
		$attempt =0; $status = 300; $result = "";
		while ($status >= 300 && $status < 400 && $attempt++ <= $maxAttempts) 
		{
			$url = parse_url($host);
			if (isset($url["path"]) == FALSE) $url["path"] = "/";
			if (isset($url["port"]) == FALSE) $url["port"] = 80;

			if ($socket = fsockopen ($url["host"], $url["port"], $errno, $errstr, 15)) 
			{
				$strQuery = "POST ".$url["path"]." HTTP/1.1\n";
				$strQuery .= "Host: ".$url["host"]."\n";
				$strQuery .= "Content-Length: ".strlen($data)."\n";
				$strQuery .= "Content-Type: ".$contenttype."\n";
				$strQuery .= "Connection: close\n\n";
				$strQuery .= $data;

				// send request & get response
				fputs($socket, $strQuery);
				$bHeader = TRUE;
				while (!feof($socket)) 
				{
					$strLine = trim(fgets($socket, 512));
					if (strlen($strLine) == 0) $bHeader = FALSE; // first empty line ends header-info
					if ($bHeader) 
					{
						if (!$status) $status = $strLine;
						if (preg_match("/^Location:\s(.*)/", $strLine, $matches)) $location = $matches[1];
					} 
					else $result .= trim($strLine)."\n";
				}
				fclose ($socket);
			} else $status = "999 timeout";

			if ($status) 
			{
				if(preg_match("/(\d){3}/", $status, $matches)) $status = $matches[1];
			} 
			else $status = 999;
			$host = $location;
		}
		if (preg_match("/^[\da-fA-F]+(.*)$/", $result, $matches)) $result = $matches[1];
		return $result;
	}
	
	/**
	 * 
	 * @uses	Wakka::htmlspecialchars_ent()
	 * @uses	Wakka::HTTPpost()
	 * @param $ping
	 * @param $debug
	 * @return unknown_type
	 */
	function WikiPing($ping, $debug = FALSE) 
	{
		if ($ping) 
		{
			$rpcRequest .= "<methodCall>\n";
			$rpcRequest .= "<methodName>wiki.ping</methodName>\n";
			$rpcRequest .= "<params>\n";
			$rpcRequest .= "<param>\n<value>\n<struct>\n";
			$rpcRequest .= "<member>\n<name>tag</name>\n<value>".$ping["tag"]."</value>\n</member>\n";
			$rpcRequest .= "<member>\n<name>url</name>\n<value>".$ping["taglink"]."</value>\n</member>\n";
			$rpcRequest .= "<member>\n<name>wiki</name>\n<value>".$ping["wiki"]."</value>\n</member>\n";
			if ($ping["author"]) 
			{
				$rpcRequest .= "<member>\n<name>author</name>\n<value>".$ping["author"]."</value>\n</member>\n";
				if ($ping["authorpage"]) $rpcRequest .= "<member>\n<name>authorpage</name>\n<value>".$ping["authorpage"]."</value>\n</member>\n";
			}
			if ($ping["history"]) $rpcRequest .= "<member>\n<name>history</name>\n<value>".$ping["history"]."</value>\n</member>\n";
			if ($ping["changelog"]) $rpcRequest .= "<member>\n<name>changelog</name>\n<value>".$this->htmlspecialchars_ent($ping['changelog'],ENT_COMPAT,'XML')."</value>\n</member>\n";
			$rpcRequest .= "</struct>\n</value>\n</param>\n";
			$rpcRequest .= "</params>\n";
			$rpcRequest .= "</methodCall>\n";

			foreach (explode(" ", $ping["server"]) as $server) 
			{
				$response = $this->HTTPpost($server, $rpcRequest, "text/xml");
				if ($debug) print $response;
			}
		}
	}
	
	/**
	 * 
	 * @uses	Wakka::Href()
	 * @uses	Wakka::LoadPage()
	 * @uses	Config::$wakka_name
	 * @param $server
	 * @param $tag
	 * @param $user
	 * @param $changelog
	 * @return unknown_type
	 */
	function GetPingParams($server, $tag, $user, $changelog = "") 
	{
		$ping = array();
		if ($server) 
		{
			$ping["server"] = $server;
			if ($tag) $ping["tag"] = $tag; else return FALSE; // set page-title
			if (!$ping["taglink"] = $this->Href("", $tag)) return FALSE; // set page-url
				if (!$ping["wiki"] = $this->config["wakka_name"]) return FALSE; // set site-name
			$ping["history"] = $this->Href("revisions", $tag); // set url to history

			if ($user) 
			{
				$ping["author"] = $user; // set username
				// @todo use existsPage instead
				if ($this->LoadPage($user)) $ping["authorpage"] = $this->Href("", $user); // set link to user page
			}
			if ($changelog) $ping["changelog"] = $changelog;
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
		SetCookie($name.$this->config['wiki_suffix'], $value, 0, $this->wikka_cookie_path); 
		$_COOKIE[$name.$this->config['wiki_suffix']] = $value; 
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
		SetCookie($name.$this->config['wiki_suffix'], $value, time() + $this->cookie_expiry, $this->wikka_cookie_path); 
		$_COOKIE[$name.$this->config['wiki_suffix']] = $value; 
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
		SetCookie($name.$this->config['wiki_suffix'], "", 1, $this->wikka_cookie_path); 
		$_COOKIE[$name.$this->config['wiki_suffix']] = ""; 
		$this->cookies_sent = TRUE; 
	}
	
	/**
	 * 
	 * @param $name
	 * @return unknown_type
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
		$_SESSION["redirectmessage"] = $message; 
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
	 * @author {@link http://wikkawiki.org/DotMG Mahefa Randimbisoa} (added IIS support)
	 * @access	public
	 * @since	Wikka 1.1.6.2
	 *
	 * @uses	Config::$base_url
	 * @param	string	$url: destination URL; if not specified redirect to the same page.
	 * @param	string	$message: message that will show as alert in the destination URL
	 */
	function Redirect($url='', $message='')
	{
		if ($message != '') $_SESSION["redirectmessage"] = $message;
		$url = ($url == '' ) ? $this->config['base_url'].$this->tag : $url;
		if ((eregi('IIS', $_SERVER["SERVER_SOFTWARE"])) && ($this->cookies_sent))
		{
			@ob_end_clean();
			die('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en"><head><title>Redirected to '.$this->Href($url).'</title>'.
'<meta http-equiv="refresh" content="0; url=\''.$url.'\'" /></head><body><div><script type="text/javascript">window.location.href="'.$url.'";</script>'.
'</div><noscript>If your browser does not redirect you, please follow <a href="'.$this->Href($url).'">this link</a></noscript></body></html>');
		}
		else
		{
			header("Location: ".$url);
		}
		exit;
	}

	/**
	 * Returns just PageName[/method].
	 * 
	 * @param $method
	 * @param $tag
	 * @return unknown_type
	 */
	function MiniHref($method = "", $tag = "") 
	{ 
		if (!$tag = trim($tag)) $tag = $this->tag; 
		return $tag.($method ? "/".$method : ""); 
	}
	
	/**
	 * Returns the full url to a page/method.
	 * 
	 * @uses	Wakka::MiniHref()
	 * @uses	Config::$base_url
	 * @uses	Config::$rewrite_mode
	 * @param $method
	 * @param $tag
	 * @param $params
	 * @return unknown_type
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
	 * @return	string	an HTML hyperlink (a href) element
	 * @todo	move regexps to regexp-library		#34
	 */
	function Link($tag, $handler='', $text='', $track=TRUE, $escapeText=TRUE, $title='', $class='')
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
		$wikilink = '';

		// is this an interwiki link?
		// before the : should be a WikiName; anything after can be (nearly) anything that's allowed in a URL
		if (preg_match('/^([A-ZÄÖÜ][A-Za-zÄÖÜßäöü]+)[:](\S*)$/', $tag, $matches))	// @@@ FIXME #34 (inconsistent with Formatter)
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
		// is this a full link? i.e., does it contain something *else* than valid WikiName characters?
		// FIXME just use (!IsWikiName($tag)) here (then fix the RE there!)
		// @@@ First move to regex library
		elseif (preg_match('/[^[:alnum:]ÄÖÜßäöü]/', $tag))		// FIXED #34 - removed commas
		{
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
	 * @param	string $text mandatory: 
	 * @return	integer 1 if $text is a wikiname, 0 otherwise
	 * @todo	remove the comma's in the RE!		#34
	 * @todo	move regexps to regexp-library		#34
	 * @todo	return a boolean
	 */
	function IsWikiName($text) 
	{ 
		return preg_match("/^[A-Z,ÄÖÜ][a-z,ßäöü]+[A-Z,0-9,ÄÖÜ][A-Z,a-z,0-9,ÄÖÜ,ßäöü]*$/", $text); 
	}
	
	/**
	 * 
	 * @param string $tag madatory: (wiki) pagename the link points to.
	 */
	function TrackLinkTo($tag) 
	{ 
		$_SESSION["linktable"][] = $tag; 
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function GetLinkTable() 
	{ 
		return $_SESSION["linktable"]; 
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function ClearLinkTable() 
	{ 
		$_SESSION["linktable"] = array(); 
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function StartLinkTracking() 
	{ 
		$_SESSION["linktracking"] = 1; 
	}
	
	/**
	 * 
	 * @return unknown_type
	 */
	function StopLinkTracking() 
	{ 
		$_SESSION["linktracking"] = 0; 
	}
	
	/**
	 * 
	 * @uses	Wakka::GetLinkTable()
	 * @uses	Wakka::Query()
	 * @uses	Wakka::GetPageTag()
	 * @uses	Config::$table_prefix
	 * @return unknown_type
	 */
	function WriteLinkTable()
	{
		// delete old link table
		$this->Query("delete from ".$this->config["table_prefix"]."links where from_tag = '".mysql_real_escape_string($this->GetPageTag())."'");
		// build new link table
		if ($linktable = $this->GetLinkTable())
		{
			$from_tag = mysql_real_escape_string($this->GetPageTag());
			$written = array();
			$sql = '';
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
			if($sql)
			{
				$this->Query("INSERT INTO {$this->config['table_prefix']}links VALUES $sql"); 
			}
		}
	}
	
	/**#@-*/
	
	/*#@+
	 * @category	Template methods
	 */
	
	/** 
	 * Add a custom header to be inserted inside the <meta> tag.  
	 *  
	 * @uses Wakka::$additional_headers 
	 * @param string $additional_headers any valid XHTML code that is legal inside the <meta> tag. 
	 * @param string $indent optional indent string, default is a tabulation. This will be inserted before $additional_headers 
	 * @param string $sep optional separator string, this will separate you additional headers. This will be inserted after 
	 *      $additional_headers, default value is a line feed. 
	 * @access public 
	 * @return void 
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
		$header = $this->IncludeBuffered($filename, ERROR_HEADER_MISSING, '', $path);
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
		$footer = $this->IncludeBuffered($filename, ERROR_FOOTER_MISSING, '', $path);
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
	 * DIRECTORY_SEPARATOR (usually used in conjunction with CSS path 
	 * generation). Default is DIRECTORY_SEPARATOR.
	 *
     * @return string A fully-qualified pathname or NULL if none found 
	 */
	 function GetThemePath($path_sep = DIRECTORY_SEPARATOR)
	 {
	 	//check if custom theme is set in user preferences
	 	if ($user = $this->GetUser())
		{
			$theme =  ($user['theme']!='')? $user['theme'] : $this->GetConfigValue('theme');
		}
		else
		{
			$theme = $this->GetConfigValue('theme');
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
		$output .= '<select id="select_theme" name="theme">';
		$output .= '<option disabled="disabled">'.sprintf(DEFAULT_THEMES_TITLE, count($core)).'</option>';
		foreach ($core as $c)
		{		
			$output .= "\n ".'<option value="'.$c.'"';
			if ($c == $default_theme) $output .= ' selected="selected"';
			$output .= '>'.$c.'</option>';
		}
		//display custom themes if any	
		if (count($plugin)>0)
		{
			$output .= '<option disabled="disabled">'.sprintf(CUSTOM_THEMES_TITLE, count($plugin)).'</option>';
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
	function FormOpen($handler='', $tag='', $formMethod='post', $id='', $class='', $file=FALSE)
	{
		// init
		$attrMethod = ''; // no method for HTML default 'get'
		$attrClass = '';
		$attrEnctype = ''; // default no enctype -> HTML default application/x-www-form-urlencoded
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

		// add validation key fields used against FormSpoofing
		if('post' == $formMethod) 
		{ 
			$tmp = $this->createSessionKey($id); 
			$hidden[$tmp[0]] = $tmp[1]; 
			unset($tmp); 
			$hidden['form_id'] = $id;        
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
	 * Close a form.
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
		if ($lines = file("interwiki.conf"))
		{
			foreach ($lines as $line)
			{
				if ($line = trim($line))
				{
					list($wikiName, $wikiUrl) = explode(" ", trim($line)); // @@@ allow any tabs/spaces, not just single space
					$this->AddInterWiki($wikiName, $wikiUrl);
				}
			}
		}
	}
	
	/**
	 * Add an interWiki to the interWiki list.
	 * 
	 * @param string $name mandatory: shortcut for the interWiki
	 * @param string $url mandatory: url for the interwiki
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
	 * 
	 * @uses	Wakka::cleanUrl()
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Wakka::LoadSingle()
	 * @uses	Wakka::Query()
	 * @uses	Config::$base_url
	 * @uses	Config::$table_prefix
	 * @param $tag
	 * @param $referrer
	 * @return unknown_type
	 */
	function LogReferrer($tag='', $referrer='')
	{
		// fill values
		if (!$tag = trim($tag))
		{
			#$tag = $this->GetPageTag();
			$tag = $this->tag;
		}
		#if (!$referrer = trim($referrer)) $referrer = $_SERVER["HTTP_REFERER"]; NOTICE
		if (empty($referrer))
		{
			$referrer = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';
		}
		$referrer = trim($this->cleanUrl($referrer));			# secured JW 2005-01-20

		// check if it's coming from another site
		#if ($referrer && !preg_match('/^'.preg_quote($this->GetConfigValue('base_url'), '/').'/', $referrer))
		if (!empty($referrer) && !preg_match('/^'.preg_quote($this->GetConfigValue('base_url'), '/').'/', $referrer))
		{
			$parsed_url = parse_url($referrer);
			$spammer = $parsed_url['host'];
			$blacklist = $this->LoadSingle("
				SELECT *
				FROM ".$this->GetConfigValue('table_prefix')."referrer_blacklist
				WHERE spammer = '".mysql_real_escape_string($spammer)."'"
				);
			if (FALSE == $blacklist)
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
	
	/**
	 * @uses	Wakka::LoadAll()
	 * @uses	Wakka::GetConfigValue()
	 * @uses	Config::$table_prefix
	 * @param $tag
	 * @return unknown_type
	 */
	function LoadReferrers($tag = "")
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

	/**#@-*/
	
	/**
	 * @category	SANITY CHECKS
	 */
	
	/**
	 * Check by name if a page exists.
	 *
	 * @author		{@link http://wikkawiki.org/JavaWoman JavaWoman}
	 * @copyright	Copyright (c) 2004, Marjolein Katsma
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
		// init
		$count = 0;
		$table_prefix = (empty($prefix) && isset($this)) ? $this->config['table_prefix'] : $prefix;
		if (is_null($dblink))
		{
			$dblink = $this->dblink;
		}
		// build query
		$query = "SELECT COUNT(tag)
				FROM ".$table_prefix."pages
				WHERE tag='".mysql_real_escape_string($page)."'";
		if ($active)
		{
			$query .= "		AND latest='Y'";
		}
		// do query
		if ($r = Wakka::Query($query, $dblink))
		{
			$count = mysql_result($r,0);
			mysql_free_result($r);
		}
		// report
		return ($count > 0) ? TRUE : FALSE;
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
#echo 'checking path: '.$this->GetConfigValue('handler_path').DIRECTORY_SEPARATOR.'page'.DIRECTORY_SEPARATOR.$handler.'.php'.'<br/>';
		$exists = $this->BuildFullpathFromMultipath($handler.DIRECTORY_SEPARATOR.$handler.'.php', $this->GetConfigValue('handler_path')); 
		// return conclusion
		if(TRUE===empty($exists)) 
		{ 
			return FALSE; 
		} 
		return TRUE; 
	}
	/**#@-*/

	/*#@+
	 * @category	PLUGINS: Actions/Handlers
	 */
	
	/**
	 * 
	 * @param $actionspec
	 * @param $forceLinkTracking
	 * @return unknown_type
	 */
	function Action($actionspec, $forceLinkTracking = 0)
	{
		// parse action spec and check if we have a syntactically valid action name	[SEC]
		// allows action name consisting of letters and numbers ONLY
		// and thus provides defense against directory traversal or XSS
		if (!preg_match('/^\s*([a-zA-Z0-9]+)(\s.+?)?\s*$/', $actionspec, $matches))	# see also #34
		{
			return '<em class="error">Unknown action; the action name must not contain special characters.</em>';	# [SEC]
		}
		else
		{
			// valid action name, so we pull out the parts
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
					// parameter value is sanitized using htmlspecialchars_ent(); if an
					// action really needs "raw" HTML as input it can still be "unescaped"by the action
					// itself; for any other action this guards against XSS or directory traversal
					// via user-supplied action parameters. Any HTML will be displayed _as code_,
					// but not interpreted.
					$vars[$matches[1][$a]] = $this->htmlspecialchars_ent($matches[3][$a]);	// parameter name = sanitized value [SEC]
				}
			}
			$vars['wikka_vars'] = $paramlist; // <<< add the complete parameter-string to the array
		}
		if (!$forceLinkTracking) 
		{ 
				/** 
				 * @var boolean holds previous state of LinkTracking before we StopLinkTracking(). It will then be used to test if we should StartLinkTracking() or not.   
				 */ 
				$link_tracking_state = $_SESSION['linktracking']; 
				$this->StopLinkTracking(); 
		} 
		$result =
		$this->IncludeBuffered(strtolower($action_name).DIRECTORY_SEPARATOR.strtolower($action_name).'.php', 'Unknown action "'.$action_name.'"', $vars, $this->config['action_path']);
		if ($link_tracking_state) 
		{ 
			$this->StartLinkTracking(); 
		} 
		return $result;
	}
	
	/**
	 * @uses	Wakka::IncludeBuffered()
	 * @uses	Config::$handler_path
	 * @param $handler
	 * @return unknown_type
	 */
	function Handler($handler)
	{
		if (strstr($handler, '/'))
		{
			# Observations - MK 2007-03-30
			# extract part after the last slash (if the whole request contained multiple slashes)
			# TODO:
			# but should such requests be accepted in the first place?
			# at least it is a SORT of defense against directory traversal (but not necessarily XSS)
			# NOTE that name syntax check now takes care of XSS
			$handler = substr($handler, strrpos($handler, '/')+1);
		}
		// check valid handler name syntax (similar to Action())
		// @todo move regexp to library
		if (!preg_match('/^([a-zA-Z0-9_.-]+)$/', $handler)) // allow letters, numbers, underscores, dashes and dots only (for now); see also #34
		{
			return '<em class="error">Unknown handler; the handler name must not contain special characters.</em>';	# [SEC]
		}
		else
		{
			// valid handler name; now make sure it's lower case
			$handler = strtolower($handler);
		}
		$handlerLocation = $handler.DIRECTORY_SEPARATOR.$handler.'.php';	#89
		return $this->IncludeBuffered($handlerLocation, 'Unknown handler "'.$handlerLocation.'"', '', $this->config['handler_path']);
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
	 *   		this value is passed to compact() to re-create the variable on formatters/wakka.php
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
			//$out = $this->IncludeBuffered($formatter_location, $this->GetConfigValue('wikka_formatter_path'), $formatter_not_found, FALSE, compact('text', 'format_option')); // @@@
			$out = $this->IncludeBuffered($formatter_location, $formatter_not_found, compact('text', 'format_option'), $this->GetConfigValue('wikka_formatter_path'));				
		}
		return $out;
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
	 *              construction of fully-qualified filepath  
	 * @param string $pathlist mandatory: list of 
	 *              paths (delimited by ":", ";", or ",") 
	 * @param  string path_sep Use this to override the OS default 
     *              DIRECTORY_SEPARATOR (usually used in conjunction with CSS path 
     *              generation). Default is DIRECTORY_SEPARATOR.
	 * @param  boolean $checkIfFileExists optional: if TRUE, returns 
	 *              only a pathname that points to a file that exists 
	 *              (default) 
	 * @return string A fully-qualified pathname or NULL if none found 
	 */ 
	function BuildFullpathFromMultipath($filename, $pathlist, $path_sep = DIRECTORY_SEPARATOR, $checkIfFileExists=TRUE) 
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

	/*#@+
	 *@category	User
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
	 * 
	 * in trunk: <b>Replaced by {@link Wakka::authenticateUserFromCookies()},
	 * {@link Wakka::existsUser()} or {@link Wakka::loadUserData()} depending on
	 * purpose!</b>
	 * 
	 * @param $name
	 * @param $password
	 * @return unknown_type
	 * @todo	see above
	 */
	function LoadUser($name, $password = 0) 
	{ 
		return $this->LoadSingle("select * from ".$this->config['table_prefix']."users where name = '".mysql_real_escape_string($name)."' ".($password === 0 ? "" : "and password = '".mysql_real_escape_string($password)."'")." limit 1"); 
	}
	
	/**
	 * Load all users registered at the wiki from the database.
	 *
	 * @uses	Config::$table_prefix
	 * @uses	Wakka::LoadAll()
	 *
	 * @return	array	contains data for all users
	 * $todo	add 'start' and 'max' parameters to support paging
	 */
	function LoadUsers()
	{
		$users = $this->LoadAll("
			SELECT *
			FROM ".$this->config['table_prefix']."users
			ORDER BY name"
			);
		return $users;
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
	 * 
	 * @uses	Wakka::GetUser()
	 * @uses	Config::$enable_user_host_lookup
	 * @return unknown_type
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
			if ($this->config['enable_user_host_lookup'] == 1)	// #240
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
	 * 
	 * @return unknown_type
	 */
	function GetUser() 
	{ 
		return (isset($_SESSION["user"])) ? $_SESSION["user"] : NULL; 
	}
	
	/**
	 * 
	 * @uses	Wakka::SetPersistentCookie()
	 * @param $user
	 * @return unknown_type
	 */
	function SetUser($user) 
	{ 
		$_SESSION["user"] = $user; 
		$this->SetPersistentCookie("user_name", $user["name"]); 
		$this->SetPersistentCookie("pass", $user["password"]); 
	}
	
	/**
	 * 
	 * @uses	Wakka::DeleteCookie()
	 * @uses	Wakka::GetUserName()
	 * @uses	Wakka::Query()
	 * @return unknown_type
	 */
	function LogoutUser() 
	{ 
		unset($_SESSION['show_comments']);
		$this->DeleteCookie("user_name"); 
		$this->DeleteCookie("pass"); 
		// Delete this session from sessions table
		$this->Query("DELETE FROM ".$this->config['table_prefix']."sessions WHERE userid='".$this->GetUserName()."' AND sessionid='".session_id()."'");
		$_SESSION["user"] = ""; 
		// This seems a good as place as any to purge all session records
		// older than PERSISTENT_COOKIE_EXPIRY, as this is not a
		// time-critical function for the user.  The assumption here
		// is that  server-side sessions have long ago been cleaned up by PHP.
		$this->Query("DELETE FROM ".$this->config['table_prefix']."sessions WHERE DATE_SUB(NOW(), INTERVAL ".PERSISTENT_COOKIE_EXPIRY." SECOND) > session_start");
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
			$formatted_user = 'anonymous'; // @@@ #i18n WIKKA_ANONYMOUS_AUTHOR_CAPTION or WIKKA_ANONYMOUS_USER
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
				WHERE `name` = '".mysql_real_escape_string($username)."'
				LIMIT 1"
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
	 * Load the last 50 comments on the wiki.
	 *
	 * @uses	Wakka::LoadAll()
	 * @uses	Wakka::GetUser()
	 * @uses	Wakka::IsAdmin()
	 * @uses	Config::$table_prefix
	 * @param	integer $limit optional: number of last comments. default: 50
	 * @param   string $user optional: list only comments by this user
	 * @return	array the last x comments
	 */
	function LoadRecentComments($limit = 50, $user = '') 
	{ 
		$where = '';
		if(!empty($user) && 
		   ($this->GetUser() || $this->IsAdmin()))
		{
			$where = " where user = '".mysql_real_escape_string($user)."' ";
		}
		return $this->LoadAll("SELECT * FROM ".$this->config["table_prefix"]."comments $where.' and (status IS NULL or status != \'deleted\') ORDER BY time DESC LIMIT ".intval($limit)); 
	}

	/**
	 * Load the last 50 comments on different pages on the wiki.
	 *
	 * @uses	Wakka::LoadAll()
	 * @uses	Wakka::GetUser()
	 * @uses	Wakka::IsAdmin()
	 * @uses	Config::$table_prefix
	 * @param	integer $limit optional: number of last comments on different pages. default: 50
	 * @param   string $user optional: list only comments by this user
	 * @return	array the last x comments on different pages
	 */
	function LoadRecentlyCommented($limit = 50, $user = '')
	{
		$where = ' and 1 ';
		if(!empty($user) && 
		   ($this->GetUser() || $this->IsAdmin()))
		{
			$where = " and comments.user = '".mysql_real_escape_string($user)."' ";
		}

		$sql = "SELECT comments.id, comments.page_tag, comments.time, comments.comment, comments.user"
			. " FROM ".$this->config["table_prefix"]."comments AS comments"
			. " LEFT JOIN ".$this->config["table_prefix"]."comments AS c2 ON comments.page_tag = c2.page_tag AND comments.id < c2.id"
			. " WHERE c2.page_tag IS NULL "
			. " and (comments.status IS NULL or comments.status != 'deleted') "
			. $where
			. " ORDER BY time DESC "
			. " LIMIT ".intval($limit);
		return $this->LoadAll($sql);
	}

	/**
	 * Save a (given) comment for a (given) page.
	 *
	 * @uses	Wakka::GetUserName()
	 * @uses	Wakka::Query()
	 * @uses	Wakka::LoadSingle()
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

	/**#@-*/
	
	/*#@+
	 * @category	ACCESS CONTROL
	 */

	/**
	 * Check if current user is the owner of the current or a specified page.
	 *
	 * @access		public
	 * @uses		Wakka::GetPageOwner()
	 * @uses		Wakka::GetPageTag()
	 * @uses		Wakka::GetUser()
	 * @uses		Wakka::GetUserName()
	 * @uses		Wakka::IsAdmin()
	 *
	 * @param		string  $tag optional: page to be checked. Default: current page.
	 * @return		boolean TRUE if the user is the owner, FALSE otherwise.
	 */
	function UserIsOwner($tag = "")
	{
		// if not logged in, user can't be owner!
		if (!$this->GetUser()) return FALSE;

		// if user is admin, return true. Admin can do anything!
		if ($this->IsAdmin()) return TRUE;

		// set default tag & check if user is owner
		if (!$tag = trim($tag)) $tag = $this->GetPageTag();
		if ($this->GetPageOwner($tag) == $this->GetUserName()) return TRUE;
	}
	
	/**
	 * returns true if user is listed in configuration list as admin
	 * 
	 * @param $user
	 * @return unknown_type
	 */
	function IsAdmin($user='') {
		$adminstring = $this->config["admin_users"];
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
	 * 
	 * @param $tag
	 * @param $time
	 * @return unknown_type
	 */
	function GetPageOwner($tag = "", $time = "") 
	{ 
		if (!$tag = trim($tag)) $tag = $this->GetPageTag(); 
		if ($page = $this->LoadPage($tag, $time)) 
		return $page["owner"]; 
	}
	
	/**
	 * 
	 * @param $tag
	 * @param $user
	 * @return unknown_type
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
	 * 
	 * @param $tag
	 * @param $privilege
	 * @param $useDefaults
	 * @return unknown_type
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
	 * 
	 * @param $tag
	 * @param $useDefaults
	 * @return unknown_type
	 */
	function LoadAllACLs($tag, $useDefaults = 1)
	{
		if ((!$acl = $this->LoadSingle("SELECT * FROM ".$this->config["table_prefix"]."acls WHERE page_tag = '".mysql_real_escape_string($tag)."' LIMIT 1")) && $useDefaults)
		{
			$acl = array(
				"page_tag" => $tag, 
			    "read_acl" => $this->GetConfigValue("default_read_acl"),
				"write_acl" => $this->GetConfigValue("default_write_acl"),
				"comment_read_acl" => $this->GetConfigValue("default_comment_read_acl"),
				"comment_post_acl" => $this->GetConfigValue("default_comment_post_acl")
			);
		}
		return $acl;
	}
	
	/**
	 * 
	 * @param $tag
	 * @param $privilege
	 * @param $list
	 * @return unknown_type
	 */
	function SaveACL($tag, $privilege, $list) 
	{
		// the $default will be put in the SET statement of the INSERT SQL for default values. It isn't used in UPDATE.
		$default = "read_acl = '', write_acl = '', comment_read_acl = '', comment_post_acl = '', ";
		// we strip the privilege_acl from default, to avoid redundancy
		$default = str_replace($privilege."_acl = '',", '', $default);
		if ($this->LoadACL($tag, $privilege, 0)) $this->Query("UPDATE ".$this->config["table_prefix"]."acls SET ".mysql_real_escape_string($privilege)."_acl = '".mysql_real_escape_string(trim(str_replace("\r", "", $list)))."' WHERE page_tag = '".mysql_real_escape_string($tag)."' LIMIT 1");
		else $this->Query("INSERT INTO ".$this->config["table_prefix"]."acls SET $default page_tag = '".mysql_real_escape_string($tag)."', ".mysql_real_escape_string($privilege)."_acl = '".mysql_real_escape_string(trim(str_replace("\r", "", $list)))."'");
	}
	
	/**
	 * 
	 * @param $list
	 * @return unknown_type
	 */
	function TrimACLs($list) 
	{
		foreach (explode("\n", $list) as $line)
		{
			$line = trim($line);
			$trimmed_list .= $line."\n";
		}
		return $trimmed_list;
	}

	/**
	 * returns true if $user (defaults to current user) has access to $privilege on $page_tag (defaults to current page)
	 * 
	 * @param $privilege
	 * @param $tag
	 * @param $user
	 * @return unknown_type
	 */
	function HasAccess($privilege, $tag = "", $user = "")
	{
		// set defaults
		if (!$tag) $tag = $this->GetPageTag();
		if (!$user) $user = $this->GetUserName();

		// if current user is owner, return true. owner can do anything!
		if ($this->UserIsOwner($tag)) return TRUE;

		// see whether user is registered and logged in
		$registered = FALSE;
		if ($this->GetUser()) $registered = TRUE;

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
		return FALSE;
	}

	/**#@-*/
	
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
	{
		// purge referrers
		if ($days = $this->GetConfigValue("referrers_purge_time")) 
		{
			$this->Query("
				DELETE FROM ".$this->GetConfigValue('table_prefix')."referrers
				WHERE time < date_sub(now(), interval '".mysql_real_escape_string($days)."' day)"
				);			
		}

		// purge old page revisions
		if ($days = $this->GetConfigValue("pages_purge_time")) 
		{
			$this->Query("
				DELETE FROM ".$this->GetConfigValue('table_prefix')."pages
				WHERE time < date_sub(now(), interval '".mysql_real_escape_string($days)."' day)
					AND latest = 'N'"
				);
			$this->Query("delete from ".$this->config["table_prefix"]."pages where time < date_sub(now(), interval '".mysql_real_escape_string($days)."' day) and latest = 'N'");
		}
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
	 * @param $tag
	 * @param $method
	 * @return unknown_type
	 */
	function Run($tag, $method = "")
	{
		// Set default cookie path
		$base_url_path = preg_replace('/wikka\.php/', '', $_SERVER['SCRIPT_NAME']);
		$this->wikka_cookie_path = ('/' == $base_url_path) ? '/' : substr($base_url_path,0,-1);

		// do our stuff!
		if (!$this->handler = trim($method)) $this->handler = "show";
		if (!$this->tag = trim($tag)) $this->Redirect($this->Href("", $this->config["root_page"]));
		if (!$this->GetUser() && ($user = $this->LoadUser($this->GetCookie('user_name'), $this->GetCookie('pass')))) $this->SetUser($user);
		if ((!$this->GetUser() && isset($_COOKIE["wikka_user_name"])) && ($user = $this->LoadUser($_COOKIE["wikka_user_name"], $_COOKIE["wikka_pass"])))
		{
			//Old cookies : delete them
			SetCookie('wikka_user_name', "", 1, $this->wikka_cookie_path);
			$_COOKIE['wikka_user_name'] = "";
			SetCookie('wikka_pass', '', 1, $this->wikka_cookie_path);
			$_COOKIE['wikka_pass'] = "";
			$this->SetUser($user);
		}
		$this->SetPage($this->LoadPage($tag, (isset($_GET['time']) ? $_GET['time'] :''))); #312

		$this->LogReferrer();
		$this->ACLs = $this->LoadAllACLs($this->tag);
		$this->ReadInterWikiConfig();
		if(!($this->GetMicroTime()%3)) $this->Maintenance();

		if (preg_match('/\.(xml|mm)$/', $this->handler))
		{
			header("Content-type: text/xml");
			print($this->handler($this->handler));
		}
		// raw page handler
		elseif ($this->handler == "raw")
		{
			header("Content-type: text/plain");
			print($this->handler($this->handler));
		}
		// grabcode page handler
		elseif ($this->handler == "grabcode")
		{
			print($this->handler($this->handler));
		}
		elseif (preg_match('/\.(gif|jpg|png)$/', $this->handler))		# should not be necessary
		{
			header('Location: images/' . $this->handler);
		}
		elseif (preg_match('/\.css$/', $this->handler))					# should not be necessary
		{
			header('Location: css/' . $this->handler);
		}
		else
		{
			//output page
			$content_body = $this->handler($this->handler);
			echo $this->Header();
			echo $content_body;
			echo $this->Footer();
		}
	}
}
?>
