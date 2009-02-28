<?php
/**
 * Download a code block as a file.
 *
 * When called by a grab button, forces the download of the associate code block.
 * 
 * @package	Handlers
 * @name	grabcode
 * @author	{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 * @version	0.2
 * @since	1.1.6.2
 * @todo	- add configurable filename max. length;
 			- use central regex library for filename validation;
 			- check time format for consistency
 */

// i18n strings
define('ERROR_NO_CODE', 'Sorry, there is no code to download.');

// defaults
define('DEFAULT_FILENAME', 'codeblock.txt'); # default name for code blocks
define('FILE_EXTENSION', '.txt'); # extension appended to code block name

// initialize variables
$code = '';
$filename = '';

// check if grabcode is allowed
if ($this->GetConfigValue('grabcode_button') == 1) {

	//get URL parameters
	$code = urldecode($_POST['code']);
	// TODO: use central regex library for filename validation
	$filename = (isset($_POST['filename']) && preg_match('/\w[-.\w]*/', $_POST['filename']))? urldecode($_POST['filename']).FILE_EXTENSION : DEFAULT_FILENAME;

	//set HTTP headers
	header('Content-type: text/plain');
	header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT'); //TODO: check for consistency with server time format
	header('Content-Length: '.strlen($code));
	header('Content-Description: '.$filename.' Download Data');
	header('Pragma: no-cache');
	header('Content-Disposition: attachment; filename="'.$filename.'"');

	//print code block
	echo $code;
} else
{
	echo ERROR_NO_CODE;
}
?>