<?php
/**
 * A compatibility library.
 *
 * <b>WARNING:</b> This file is <b>not</b> relocatable: it must always be present
 * in the 'libs' directory below the Wikka installation path. This is because
 * this library is actually used to validate any path definitions used! Even if
 * you share other library files between Wikka installations, each Wikka
 * installation requires its own copy of this file in the predetermined location.
 *
 * This file contains a number of defines and functions that provide compatibility
 * support in various situations: missing functions that are present in other
 * versions of PHP, functions to get around environmental differences (such as
 * settings in php.ini that a Wikka admin may not be able to touch), and similar
 * issues. Some small utilities that may be helpful during debugging (such as 
 * getmicrotime()) which might be helpful when dealing with different
 * implementations are also forced in here as there's no better place yet. ;)
 *
 * The @since tag identifies the first version in which this file appears, though
 * some of the functions have been around since Wikka 1.0.0.
 *
 * @package		Wikka
 * @subpackage	Libs
 * @version		$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @see			docs/Wikka.LICENSE
 * @filesource
 *
 * @author		{@link http://www.mornography.de/ Hendrik Mans}
 * @author		{@link http://wikkawiki.org/JsnX Jason Tourtelotte}
 * @author		{@link http://wikkawiki.org/JavaWoman Marjolein Katsma}
 * @since		Wikka 1.1.7
 *
 * @copyright	Copyright 2002-2003, Hendrik Mans <hendrik@mans.de>
 * @copyright	Copyright 2004-2005, Jason Tourtelotte <wikka-admin@jsnx.com>
 * @copyright	Copyright 2006-2007, {@link http://wikkawiki.org/CreditsPage Wikka Development Team}
 */

/**#@+
 * String constant introduced in later PHP versions than our baseline 4.1.
 */
/**
 * Platform-dependent EOL marker (introduced in PHP 5.0.2)
 */
if (!defined('PHP_EOL')) define ('PHP_EOL', strtoupper(substr(PHP_OS,0,3) == 'WIN') ? "\r\n" : "\n");
/**#@-*/

/**
 * Get a microtime, either as a string or as a float.
 *
 * Can be used to calculate page generation time, or SQL or function profiling.
 *
 * Serves a wrapper to replicate or use PHP 5 behavior:
 * Use getmicrotime(TRUE) to get a float for calculations, without a parameter
 * to get a string.
 * See {@link http://php.net/microtime microtime} and comments there for the
 * background of this implementation.
 *
 * @param	boolean	$get_as_float	optional: set to TRUE if you want a float;
 *					default FALSE which specifies a string
 * @return	mixed	microtime, in the form of a string (default) or a float
 */
function getmicrotime($get_as_float=FALSE)
{
	if (version_compare(phpversion(),'5','>='))		// >= PHP 5?
	{
		return microtime($get_as_float);
	}
	else
	{
		$time = strtok(microtime(), ' ') + strtok('');
		return (FALSE === $get_as_float) ? $time : (float) $time;
	}
}

if (!function_exists('mysql_real_escape_string'))
{
	/**
	 * Escape special characters in a string for use in a SQL statement.
	 *
	 * This function is added for back-compatibility with MySQL 3.23.
	 * @param	string	$string	the string to be escaped
	 * @return	string	a string with special characters escaped
	 */
	function mysql_real_escape_string($string)
	{
		return mysql_escape_string($string);
	}
}

/**
 * Workaround for the amazingly annoying magic quotes.
 *
 * Note that this function will only operate on an array; if a scalar is passed
 * it is left untouched.
 *
 * @param	array	$a	array to be cleaned of "magic quotes" (slashes); passed
 *					by reference
 */
function magicQuotesWorkaround(&$a)
{
	if (is_array($a))
	{
		foreach ($a as $k => $v)
		{
			if (is_array($v))
			{
				magicQuotesWorkaround($a[$k]);
			}
			else
			{
				$a[$k] = stripslashes($v);
			}
		}
	}
}

/**
 * Instantiate a class (PHP version-independent).
 *
 * For compatibility between PHP4 and PHP5, either an explicit assign by
 * reference is used (PHP4), or a simple assign (PHP5, where the "by reference"
 * is automatic and explicit asignment by reference is deprecated).
 * The function supports up to three variables to be passed to the class'
 * constuctor.
 *
 * @param	string	$class	mandatory: name of the class to be instantiated
 * @param	mixed	$par1	optional: first parameter to be passed to constructor
 * @param	mixed	$par2	optional: second parameter to be passed to constructor
 * @param	mixed	$par3	optional: third parameter to be passed to constructor
 * @return	object	a reference of an object resulting from instantiation of the
 *			specified class
 */
function instantiate($class, $par1=NULL, $par2=NULL, $par3=NULL)
{
	if (version_compare(phpversion(),'5','>='))		// >= PHP 5?
	{
		$obj =  new $class($par1, $par2, $par3);	// [558] / #496 - comment 3
	}
	else
	{
		$obj =& new $class($par1, $par2, $par3);	// reverting [558] see #496 - comment 4
	}
	return $obj;
}

function getMysqlVersion(&$mysql_errors)
{
	$mysql_version = FALSE;
	$result = @mysql_get_server_info();
	if (FALSE === $result)
	{
		// report error
		$mysql_errors['no'][]	= mysql_errno();
		$mysql_errors['txt'][]	= mysql_error();
		// try an alternative
		$result = @mysql_query("SHOW VARIABLES LIKE 'version'");
		if (FALSE === $result)
		{
			// report error
			$mysql_errors['no'][]	= mysql_errno();
			$mysql_errors['txt'][]	= mysql_error();
		}
		elseif (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_row($result);
			$mysql_version = $row['version'];
		}
	}
	#if ($result !== FALSE && mysql_num_rows($result) > 0)
	if (FALSE !== $result)
	{
		#$row   = mysql_fetch_array($result);
		#$mysql_version = $row['version'];
		$mysql_version = $result;
	}
	else
	{
	}
	return $mysql_version;
}

// The following functions are more utility functions than for compatibility
// although in validLocalPath() we do take into account that realpath() under
// PHP5 may evaluate a URI (which we don't want) while in PHP4 it doesn't. And
// filesys2uri() "translates" from Windows paths with backslashes to a URI path
// which requires forward slashes.
//
// These functions live here because they're used in wikka.php at a point where
// we don't have an instantiated core object yet.

/**
 * Do a sanity check on a whether a given path can be a valid URI path.
 * 
 * we're looking for a URI path that directly maps to a filesystem path:
 * so all path components also need to be valid directory (or file) names on
 * the file system.
 *
 * We check whether a given path contains only characters valid in <b>both</b>
 * a URI path and a filesystem path and does not consist of only dots (not valid
 * for a filesystem path).
 */
function validUriPath($path)
{
	if (is_uri($path))
	{
		$result = FALSE;				// $path was a URL: not allowed
		// @@@ we would need to allow this for different wikis sharing
		//     the same server but running on a different domain: in that
		//     case all filesystem paths would work, so a fully-qualified
		//     URL should be possible. Options:
		//     1. do not check at all (just return input as $result)
		//        (assume admin knows what (s)he's doing)
		//     2. check for valid URL syntax
		//     3. attempt to find out if they run on the same server - how??
		//     4. translate URL to filesystem path and check for match with
		//        other paths ???
	}
	else
	{
		// 1. valid characters in a URI path are:
		//		- a slash (component separator); and 
		//		- 0-9a-zA-Z_!~'().;@&=+$,%#-
		// 2. on Windows, these chars are forbidden in a file/dir name: *?:
		// @@@ move to regex library!
		$result = $path;				// assume it's correct; then check
		$pattern_pathfragment = "(/?)([0-9a-zA-Z_!~'.;@&=+,%#-$()]+)";	// escaped $, ( and )
		$pattern_path = '^(/)|('.$pattern_pathfragment.')+$';	// either a single '/' or one or more path components
		// check if the path matches the pattern
		if (!preg_match(':'.$pattern_path.':',$path))	// use a delimiter that is NOT a valid character!
		{
			$result = FALSE;
		}
		// check we don't have only dots (strictly allowed for a URI but not as
		// a filesystem path
		elseif (preg_match('/^\.*$/',$path))
		{
			$result = FALSE;
		}
		else
		{
			// attempt to remove .. components from path
		}
	}
	return $result;
}
/**
 * Converts a filesystem path to the equivalent URI path.
 * We do only conversion of any backslashes into forwared slashes
 */
function filesys2uri($path)
{
	$result = str_replace('\\','/',$path);
	return $result;
}

/**
 * Normalize line endings, optionally applying wordwrap.
 *
 * @param	string	$text	mandatory: the text to normalize line endings in
 * @param	string	$eol	optional: desired line ending; default a single linefeed
 * @param	int		$wrap	optional: position at which to wrap lines; 0 = don't wrap;
 *					if a value > 0 is supplied, a minimum of 25 and a maximum or 998 is
 *					applied; negative values are ignored (no wrap).
 * @return	string	text with normalized line endings, optionally word-wrapped
 * @todo	declare minimum and maximum line length in constants
 */
function normalizeEol($text, $eol="\n", $wrap=0)
{
	// replace CRLF with single LF, and replace any remaining single CR with single LF
	$text = str_replace("\r", "\n", str_replace("\r\n", "\n", trim($text)));
	// convert line endings to target (if still necessary)
	if ("\n" != $eol)
	{
		$text = str_replace("\n",$eol,$text);
	}
	// optionally wrap
	if ((int) $wrap > 0)
	{
		$wrap = max(min($wrap,RFC_MAX_EMAIL_LINE_LENGTH),25);			// @@@
		$text = wordwrap($text,$wrap,$eol);
		// cut lines left longer than 998 (RFC 29822 max message line length)
		$text = wordwrap($text,RFC_MAX_EMAIL_LINE_LENGTH,$eol,TRUE);
	}
	// return result
	return $text;
}
?>