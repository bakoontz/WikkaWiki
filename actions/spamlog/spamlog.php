<?php
/**
 * View and manage the spam log.
 *
 * long description - one or more paragraphs
 *
 * @package		Actions
 * @subpackage	action type (e.g., FormHandlers)
 * @name		page alias
 *
 * @author		{@link http://wikka.jsnx.com/JavaWoman JavaWoman}
 * @copyright	Copyright © 2005, Marjolein Katsma
 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @version		0.1
 * @since		Wikka 1.1.6.x
 * @todo		- move (some) functions to core
 *				- make summary data table more accessible
 *				- links for existing pages and registered users
 *				- allow selection of which columns to show
 *				- make sortable by column (requires multi-level sort class/routine)
 *				- make spamlog downloadable (extra button on full view)
 *
 * @uses		DEFAULT_SPAMLOG_PATH
 * @uses    Config::$spamlog_path
 * @uses    Wakka::FormClose()
 * @uses    Wakka::FormOpen()
 * @uses    Wakka::getSpamlogSummary()
 * @uses    Wakka::IsAdmin()
 * @uses    Wakka::makeId()
 * @uses    Wakka::readFile()
 *
 * @see			name of related element - creates link
 *
 * @input		datatype  $paramname  description
 * @output		description
 */

// escape & placeholder: use for action allowed only once per page
if (defined('HD_SPAMLOG'))
{
	echo '{{spamlog}}';
	return;
}

if (!function_exists('encode'))
{
	/**
	 * Encode special characters in string (so it may be passed as hidden form field).
	 *
	 * @author		{@link http://wikka.jsnx.com/JavaWoman JavaWoman}
	 * @copyright	Copyright © 2005, Marjolein Katsma
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 *
	 * @access		private
	 *
	 * @param		string	$string	required: string to encode
	 * @return		string			encoded string
	 */
	function encode($string)
	{
		return preg_replace(array('/>/','/</','/&/','/"/'),array('&gt;','&lt;','&amp;','&quot;'),$string);
	}
}
if (!function_exists('decode'))
{
	/**
	 * Decode special characters in string.
	 *
	 * @author		{@link http://wikka.jsnx.com/JavaWoman JavaWoman}
	 * @copyright	Copyright © 2005, Marjolein Katsma
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 *
	 * @access		private
	 *
	 * @param		string	$string	required: string to decode
	 * @return		string			decoded string
	 */
	function decode($string)
	{
		return preg_replace(array('/&gt;/','/&lt;/','/&amp;/','/&quot;/'),array('>','<','&','"'),$string);
	}
}
if (!function_exists('writeNewLog'))
{
	/**
	 * Write new spamlog file, making a backup when requested.
	 *
	 * @author		{@link http://wikka.jsnx.com/JavaWoman JavaWoman}
	 * @copyright	Copyright © 2005, Marjolein Katsma
	 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
	 *
	 * @access		private
	 * @uses		Wakka::writeFile()
	 *
	 * @param		string	$backup	required: 'Y' to backup, anything else to skip
	 * @param		string	$old	required: old file content (for backup); will be replaced by new content
	 * @param		string	$new	required: new file content
	 * @param		string	$path	required: path of current spamlog (backup path will be derived from this)
	 * @return		string			message with result
	 */
	function writeNewLog($backup,&$old,$new,$path)
	{
		global $wakka;
		$brc = TRUE;										# default for when no backup requested
		if ($backup == 'Y')
		{
			if (isset($_POST['bck'])) $bck = $_POST['bck'];
			$preext = $_POST['preext'];
			if ($preext == 'pre')							# using backup prefix
			{
				$dir = dirname($path);
				$dir = (isset($dir)) ? $dir.DIRECTORY_SEPARATOR : '';
				$backuppath = $dir.$bck.basename($path);
			}
			else # 'ext'
			{
				$ext = (!substr($bck,0,1) == '.') ? '.'.$bck : $bck;# make sure extension starts with dot
				$backuppath = $path.$ext;
			}
			$brc = $wakka->writeFile($backuppath,$old);		# write backup file
		}
		if ($brc)											# if backup successfully written or none requested
		{
			$rc = $wakka->writeFile($path,$new);			# write new file
			if (FALSE !== $rc)
			{
				$msgResult = sprintf(SPLOG_MSG_RESULT_GOOD,$rc);
				$old = $new;								# show new content
			}
			else
			{
				$msgResult = SPLOG_MSG_RESULT_BAD;
			}
		}
		else
		{
			$msgResult = SPLOG_MSG_RESULT_NO_BACKUP;
		}
		return $msgResult;
	}
}

// ----------------- constants and variables ------------------

// constants
if (!defined('DEFAULT_TERMINATOR'))	define('DEFAULT_TERMINATOR','&#8230;');	# standard symbol replacing truncated text (ellipsis) JW 2005-07-19
if (!defined('MAX_COL_WIDTH'))		define('MAX_COL_WIDTH',25);				# @@@ make length configurable, possibly per column


// set defaults
$vsum = 1;
$vfull = 0;
$vstats = 0;
$ch_backup = 'Y';
$bck = '~';
$preext = 'pre';

// UI strings
define('HD_SPAMLOG','Spamlog management');
define('SPLOG_FORM_VIEW_LEGEND','Choose a view:');
define('SPLOG_FORM_VSUM_LABEL','Summary view (meta data only, no content)');
define('SPLOG_FORM_VFULL_LABEL','Full view (editable, with management options)');
define('SPLOG_FORM_VSTATS_LABEL','Statistics (not yet implemented)');
define('SPLOG_FORM_SUBMIT_SPAMVIEW','Show');

define('SPLOG_FORM_SPAMLOG_LEGEND','Current spam log:');
define('SPLOG_FORM_BACKUP_LABEL','Make backup when writing');
define('SPLOG_FORM_BCK_LABEL','using:');
define('SPLOG_FORM_PREEXT_TEXT','as');
define('SPLOG_FORM_PRE_LABEL','prefix');
define('SPLOG_FORM_EXT_LABEL','extension');
define('SPLOG_FORM_PRESUF_LABEL','as:');
define('SPLOG_FORM_SPAMLOG_LABEL','File contents:');
define('SPLOG_FORM_SUBMIT_SPAMLOG','Save to file');
define('SPLOG_FORM_SUBMIT_RELOAD','Reload');
define('SPLOG_FORM_SUBMIT_DOWNLOAD','Download');
define('SPLOG_FORM_SUBMIT_CLEAR','Clear file');
define('SPLOG_MSG_RESULT_GOOD','%d bytes written');	# %d is place holder for number of bytes
define('SPLOG_MSG_RESULT_BAD','Could not write new bad words file');
define('SPLOG_MSG_RESULT_NONE','No changes: nothing written');
define('SPLOG_MSG_RESULT_NO_BACKUP','Backup could not be written, file left unchanged');
define('SPLOG_MSG_SPAMLOG_EMPTY','Spam log is empty');
define('SPLOG_MSG_SPAMLOG_ONLY_ADMIN','Sorry, only administrators can maintain the spam log');

$hdSpamlog		= HD_SPAMLOG;
$msgOnlyAdmin	= SPLOG_MSG_SPAMLOG_ONLY_ADMIN;

// variables

$isAdmin	= $this->IsAdmin();
$prefix		= $this->GetConfigValue('table_prefix');

// initializations

$msgResult  = '';
$log = '';

// set path
$spamlogpath = $this->GetConfigValue('spamlog_path', DEFAULT_SPAMLOG_PATH);

// ---------------------- processsing --------------------------

// ----------------- get/write data -----------------

if ($isAdmin)
{
	// choosing a view
	if (isset($_POST['spamview']))							# save spamlog button pressed
	{
		// get hidden fields
		$ch_backup	= $_POST['ch_backup'];
		$bck		= $_POST['bck'];
		$preext		= $_POST['preext'];

		// get/handle other data
		if (isset($_POST['vsum'])) $vsum = 1;
		if (isset($_POST['vfull'])) $vfull = 1;
		if (isset($_POST['vstats'])) $vstats = 1;
		if ($vfull)
		{
			$log = $this->readFile($spamlogpath);			# load file
		}
	}

	// (re)load file
	if (isset($_POST['reload']))							# initial or reload button pressed
	{
		// get hidden fields
		$vsum = $_POST['vsum'];
		$vfull = $_POST['vfull'];
		$vstats = $_POST['vstats'];

		// get/handle other data
		$log = $this->readFile($spamlogpath);				# (re)read file
	}

	// clear file
	if (isset($_POST['clear']))								# clear file button pressed
	{
		// get hidden fields
		$vsum = $_POST['vsum'];
		$vfull = $_POST['vfull'];
		$vstats = $_POST['vstats'];

		// get/handle other data
		$log = (isset($_POST['current'])) ? trim(decode($_POST['current'])).PHP_EOL.PHP_EOL : '';	# current file
		$ch_backup = (isset($_POST['ch_backup'])) ? 'Y' : '';
		$msgResult = writeNewLog($ch_backup,$log,'',$spamlogpath);
	}

	// new spamlog content
	if (isset($_POST['spamlog']))							# save spamlog button pressed
	{
		// get hidden fields
		$vsum = $_POST['vsum'];
		$vfull = $_POST['vfull'];
		$vstats = $_POST['vstats'];
		// NOTE: The posted value in a hidden field seems to be truncated so the
		// last two newlines get lost!. (This does not happen with a textarea.)
		// We append them back with PHP_EOL which holds the platform-specific newline
		// so the comparison will actually match when nothing was changed.
		// We trim the value first since the truncation might be browser-dependent.
		$log = (isset($_POST['current'])) ? trim(decode($_POST['current'])).PHP_EOL.PHP_EOL : '';	# current file

		// get/handle other data
		$newlog = (isset($_POST['spamlogfile'])) ? $_POST['spamlogfile'] : '';
		if ($newlog != $log)
		{
			$ch_backup = (isset($_POST['ch_backup'])) ? 'Y' : '';
			$msgResult = writeNewLog($ch_backup,$log,$newlog,$spamlogpath);
		}
		else
		{
			$msgResult = SPLOG_MSG_RESULT_NONE;
		}
	}

	// Summary
	if ($vsum == 1)
	{
		$aSummary = $this->getSpamlogSummary();
/*
echo '<pre>';
print_r($aSummary);
echo '</pre>';
*/
	}
}

// --------- build user interface elements ----------

if ($isAdmin)
{
	// choose view form
	$idSpLogVfull	= $this->makeId('form','splogfull');
	$idSpLogVsum	= $this->makeId('form','splogsum');
	$idSpLogVstats	= $this->makeId('form','splogstats');
	$sumchecked		= ($vsum == 1) ? ' checked="checked"' : '';
	$fullchecked	= ($vfull == 1) ? ' checked="checked"' : '';
	$statschecked	= ($vstats == 1) ? ' checked="checked"' : '';

	$spviewform  = $this->FormOpen('','','post','spamlogview');
	$spviewform .= '<fieldset class="hidden">'."\n";
	$spviewform .= '	<input type="hidden" name="ch_backup" value="'.$ch_backup.'" />'."\n";
	$spviewform .= '	<input type="hidden" name="bck" value="'.$bck.'" />'."\n";
	$spviewform .= '	<input type="hidden" name="preext" value="'.$preext.'" />'."\n";
	$spviewform .= '</fieldset>'."\n";
	#
	$spviewform .= '<fieldset>'."\n";
	$spviewform .= '	<legend>'.SPLOG_FORM_VIEW_LEGEND.'</legend>'."\n";
	$spviewform .= '	<input type="checkbox" id="'.$idSpLogVsum.'" name="vsum" value="1"'.$sumchecked.' />';
	$spviewform .= ' <label for="'.$idSpLogVsum.'">'.SPLOG_FORM_VSUM_LABEL.'</label><br />'."\n";
	$spviewform .= '	<input type="checkbox" id="'.$idSpLogVfull.'" name="vfull" value="1"'.$fullchecked.' />';
	$spviewform .= ' <label for="'.$idSpLogVfull.'">'.SPLOG_FORM_VFULL_LABEL.'</label><br />'."\n";
	$spviewform .= '	<input type="checkbox" id="'.$idSpLogVstats.'" name="vstats" value="1"'.$statschecked.' disabled="disabled" />';
	$spviewform .= ' <label for="'.$idSpLogVstats.'">'.SPLOG_FORM_VSTATS_LABEL.'</label><br />'."\n";
	#
	$spviewform .= '	<input type="submit" name="spamview" "value="'.SPLOG_FORM_SUBMIT_SPAMVIEW.'">';
	$spviewform .= "\n";
	$spviewform .= "</fieldset>\n";
	$spviewform .= $this->FormClose();

	// summary table
	if ($vsum == 1)
	{
		if (count($aSummary) > 0)
		{
			$sumtab = '<table summary="Overview of spamlog metadata">'."\n";
			$first = TRUE;
			foreach ($aSummary as $row)
			{
				if ($first)					# build thead
				{
					$first = FALSE;
					$sumtab .= "<thead>\n";
					$sumtab .= "<tr>\n\t";
					foreach ($row as $key => $value)
					{
						$class = '';
						if ('day' != $key && 'time' != $key)				# @@@ temporary - make columns selectable later
						{
							if ('urls' == $key) $class = ' class="number"';
							if ('ua' == $key) $key = 'User Agent';
							$sumtab .= '<th scope="col"'.$class.'>'.ucfirst($key).'</th>';		# @@@ make into sort button later
						}
					}
					$sumtab .= "\n</tr>\n";
					$sumtab .= "</thead>\n";
					$sumtab .= "<tbody>\n";
				}
				$sumtab .= "<tr>\n";
				foreach ($row as $key => $value)
				{
					if ('day' != $key && 'time' != $key)					# @@@ temporary - make columns selectable later
					{
						$class = '';
						$title = '';
						if (in_array($key,array('date','day','time'))) $class = ' class="time"';
						if (('ua' == $key || 'reason' == $key || 'user' == $key) && strlen($value) > MAX_COL_WIDTH)
						{
							$title = ' title="'.$value.'"';
							$value = substr($value,0,MAX_COL_WIDTH).DEFAULT_TERMINATOR;
						}
						if ('urls' == $key) $class = ' class="number"';
						$sumtab .= '	<td'.$class.$title.'>'.$value.'</td>'."\n";
					}
				}
				$sumtab .= "</tr>\n";
			}
			$sumtab .= "</body>\n";
			$sumtab .= "</table>\n";
		}
		else
		{
			$sumtab = '<p class="notes">'.SPLOG_MSG_SPAMLOG_EMPTY."</p>\n";
		}
	}

	// full view form
	if ($vfull == 1)
    {
		$idSpLog		= $this->makeId('form','spamlogfile');
		$idSpLogBackup	= $this->makeId('form','splogbackup');
		$idSpLogBck		= $this->makeId('form','splogbck');
		$idSpLogPre		= $this->makeId('form','splogpre');
		$idSpLogExt		= $this->makeId('form','splogext');
		$bckchecked = ($ch_backup == 'Y') ? ' checked="checked"' : '';
		$prechecked = ($preext == 'pre') ? ' checked="checked"' : '';
		$extchecked = ($preext == 'ext') ? ' checked="checked"' : '';

		$splogform  = $this->FormOpen('','','post','spamlog');
		$splogform .= '<fieldset class="hidden">'."\n";
		$splogform .= '	<input type="hidden" name="vsum" value="'.$vsum.'" />'."\n";
		$splogform .= '	<input type="hidden" name="vfull" value="'.$vfull.'"/ >'."\n";
		$splogform .= '	<input type="hidden" name="vstats" value="'.$vstats.'" />'."\n";
		$splogform .= '	<input type="hidden" name="current" value="'.encode($log).'" />'."\n";
		$splogform .= '</fieldset>'."\n";
		#
		$splogform .= '<fieldset>'."\n";
		$splogform .= '	<legend>'.SPLOG_FORM_SPAMLOG_LEGEND.'</legend>'."\n";
		#
		$splogform .= '	<label for="'.$idSpLog.'">'.SPLOG_FORM_SPAMLOG_LABEL.'</label><br />'."\n";
		$splogform .= '	<textarea id="'.$idSpLog.'" name="spamlogfile" cols="90" rows="35" style="white-space: nowrap;">'.$log.'</textarea><br />'."\n";
		#
		$splogform .= '	<input type="checkbox" id="'.$idSpLogBackup.'" name="ch_backup" value="Y"'.$bckchecked.' />'."\n";
		$splogform .= ' <label for="'.$idSpLogBackup.'">'.SPLOG_FORM_BACKUP_LABEL.'</label>'."\n";
		$splogform .= '	<label for="'.$idSpLogBck.'">'.SPLOG_FORM_BCK_LABEL.'</label>'."\n";
		$splogform .= '	<input type="text" name="bck" id="'.$idSpLogBck.'" size="3" value="'.$bck.'" />'."\n";
		$splogform .= '	'.SPLOG_FORM_PREEXT_TEXT;
		$splogform .= '	<input type="radio" id="'.$idSpLogPre.'" name="preext" value="pre"'.$prechecked.' />';
		$splogform .= ' <label for="'.$idSpLogPre.'">'.SPLOG_FORM_PRE_LABEL.'</label>';
		$splogform .= '	<input type="radio" id="'.$idSpLogExt.'" name="preext" value="ext"'.$extchecked.' />';
		$splogform .= ' <label for="'.$idSpLogExt.'">'.SPLOG_FORM_EXT_LABEL.'</label><br />'."\n";
		#
		$splogform .= '	<input type="submit" name="spamlog" "value="'.SPLOG_FORM_SUBMIT_SPAMLOG.'">';
		$splogform .= '<input type="submit" name="reload" "value="'.SPLOG_FORM_SUBMIT_RELOAD.'">';
		$splogform .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		$splogform .= '<input type="submit" name="clear" "value="'.SPLOG_FORM_SUBMIT_CLEAR.'">';
		$splogform .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		$splogform .= '<input type="submit" name="download" "value="'.SPLOG_FORM_SUBMIT_DOWNLOAD.'" disabled="disabled">';
		$splogform .= "\n";
		$splogform .= "</fieldset>\n";
		$splogform .= $this->FormClose();
	}
}
$idSpamlog	= $this->makeId('div','spamlog');


// ------------ display user interface --------------

echo '<div id="'.$idSpamlog.'">'."\n";
echo '<h3>'.$hdSpamlog.'</h3>'."\n";
if ($isAdmin)
{
	#echo '<p>'.$txtActionInfo."</p>\n";
	echo $spviewform;
	if ($vsum == 1) echo $sumtab;
	if ($vfull == 1)
	{
		echo $splogform;
		echo '<p class="notes">'.$msgResult."</p>\n";
	}
}
else
{
	echo '<p>'.$msgOnlyAdmin."</p>\n";
}
echo "</div>\n";
?>
