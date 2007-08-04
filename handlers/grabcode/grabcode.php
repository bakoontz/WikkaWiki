<?php
/**
 * Download a code block as a file.
 *
 * When called by a grab button, forces the download of the associated code block.
 *
 * @package		Handlers
 * @subpackage	Files
 * @version		$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author	{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 * @since	Wikka 1.1.6.2
 *
 * @uses	Wakka::GetConfigValue()
 * @uses	Config::$grabcode_button
 * @todo	add configurable filename max. length;
 * @todo	use central regex library for filename validation	#34
 * @todo	check time format for consistency (& store format in constant!)
 * @todo	make shared download code for this and files handler
 * @todo	avoid adding extension when the provided filename already has one -
 *			see last issue on WikkaBugs (!)
 */

/**
 * defaults
 */
define('DEFAULT_FILENAME', 'codeblock.txt'); # default name for code blocks
define('FILE_EXTENSION', '.txt'); # extension appended to code block name for security reasons

// initialize variables
$code = '';
$filename = '';

// check if grabcode is allowed
if (1 == (int) $this->GetConfigValue('grabcode_button'))
{

	//get URL parameters
	$code = urldecode($_POST['code']);
	// TODO: use central regex library for filename validation
	$filename = (isset($_POST['filename']) && preg_match('/\w[-.\w]*/', $_POST['filename'])) ? urldecode($_POST['filename']).FILE_EXTENSION : DEFAULT_FILENAME;

	// @@@ shared download code
	//set HTTP headers
	header('Content-type: text/plain');
	header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // TODO: check for consistency with server time format
	header('Content-Length: '.strlen($code));
	header('Content-Description: '.$filename.' Download Data');
	header('Pragma: no-cache');
	header('Content-Disposition: attachment; filename="'.$filename.'"');

	//print code block
	echo $code;
}
else
{
	echo ERROR_NO_CODE;
}
?>