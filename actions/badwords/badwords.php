<?php
/**
 * Maintain a list of "bad words" for content filtering.
 *
 * Features:
 *	- Reads or creates a file with words to be filtered on
 *	- There should be one word per line but lines are split automatically on whitespace
 *	- Empty lines and duplicates will be removed
 *	- content will be sorted alphabetically
 * The location of the badwords file is assumed to be in the Wikka directory but can be
 * configured via the configuration file.
 *
 * Syntax:
 *	{{badwords}}
 *
 * @package		Actions
 * @subpackage	DatabaseAdmin
 * @name		BadWords
 *
 * @author		{@link http://wikka.jsnx.com/JavaWoman JavaWoman}
 * @copyright	Copyright © 2005, Marjolein Katsma
 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @since		Wikka 1.1.6.x
 * @version		0.5
 *
 * @todo	- option to integrate result in .htaccess file
 *			- move form styling to stylesheet
 *
 * @input	string	$bwFileContent	content for badwords file
 *
 * @uses	IsAdmin()
 * @uses	FormOpen()
 * @uses	FormClose()
 * @uses	readBadWords()
 * @uses	writeBadWords()
 * @uses	MakeId()
 */

// ----------------- constants and variables ------------------

// constants

// set defaults

// UI strings
define('HD_BADWORDS','Content filtering definition');
define('TXT_BWINFO_1','This utility allows you to build and maintain a file with "bad words".');
define('TXT_BWINFO_2',' These words will be used for content filtering if you have turned on content filtering in the configuration.');
define('TXT_BWINFO_3',' Make sure you use only actual "spam words" and avoid words that could cause false positives,');
define('TXT_BWINFO_4',' since the user will be asked to reformulate the submission but will not see the original back.');
define('FORM_BADWORDS_LEGEND','Words to filter on');
define('FORM_BADWORDS_LABEL','Bad words:');
define('FORM_SUBMIT_BADWORDS','Save to file');
define('FORM_SUBMIT_RELOAD','Reload');
define('MSG_RESULT_GOOD','%d bytes written');	# %d is place holder for number of bytes
define('MSG_RESULT_BAD','Could not write new bad words file');
define('BW_MSG_RESULT_NONE','No changes, nothing written');
define('MSG_BADWORDS_ONLY_ADMIN','Sorry, only administrators can view and maintain content filtering information');

$hdBadWords		= HD_BADWORDS;
$txtActionInfo	= TXT_BWINFO_1.TXT_BWINFO_2.TXT_BWINFO_3.TXT_BWINFO_4;
$msgOnlyAdmin	= MSG_BADWORDS_ONLY_ADMIN;

// variables

$isAdmin	= $this->IsAdmin();

// ---------------------- processsing --------------------------

// --------------- get parameters ----------------

// no parameters

// ------------------ get data -------------------

$msgResult = '';
if ($isAdmin)
{
	// (re)load file
	if (!isset($_POST['badwords']) || isset($_POST['reload']))			# initial or reload button pressed
	{
		$bwFileContent = $this->readBadWords();				# (re)read file
	}

	// new badwords content
	if (isset($_POST['badwords']))							# save badwords button pressed
	{
		if (isset($_POST['bwfile']))
		{
			$bwFileContent = $_POST['bwfile'];
		}
		else
		{
			$bwFileContent = '';							# empty file
		}
		$rc = $this->writeBadWords($bwFileContent);			# write file
		if (FALSE !== $rc)
		{
			$msgResult = sprintf(MSG_RESULT_GOOD,$rc);
			$bwFileContent = $this->readBadWords();			# read (sorted) result back
		}
		else
		{
			$msgResult = MSG_RESULT_BAD;
		}
	}
}

// ---------------- build forms ------------------

if ($isAdmin)
{
	$idBwFile = $this->makeId('form','bwfile');

	$bwform  = $this->FormOpen('','','post','badwords');
	$bwform .= '<fieldset>'."\n";
	$bwform .= '	<legend>'.FORM_BADWORDS_LEGEND.'</legend>'."\n";
	$bwform .= '	<label for="'.$idBwFile.'">'.FORM_BADWORDS_LABEL.'</label><br />'."\n";
	$bwform .= '	<textarea id="'.$idBwFile.'" name="bwfile" cols="50" rows="40">'.$bwFileContent.'</textarea><br />'."\n";
	$bwform .= '	<input type="submit" name="badwords" "value="'.FORM_SUBMIT_BADWORDS.'"><input type="submit" name="reload" "value="'.FORM_SUBMIT_RELOAD.'">'."\n";
	$bwform .= "</fieldset>\n";
	$bwform .= $this->FormClose();

	// ids - use constant for variable-content heading
}
$idDbInfo	= $this->makeId('div','badwords');

// ------------ show data and forms --------------

echo '<div id="'.$idDbInfo.'">'."\n";
echo '<h3>'.$hdBadWords.'</h3>'."\n";
if ($isAdmin)
{
	echo '<p>'.$txtActionInfo."</p>\n";
	echo $bwform;
	echo '<p>'.$msgResult."</p>\n";
}
else
{
	echo '<p>'.$msgOnlyAdmin."</p>\n";
}
echo "</div>\n";
?>