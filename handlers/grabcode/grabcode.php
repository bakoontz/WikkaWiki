<?php
/**
 * Download a code block as a file.
 *
 * When called by a grab button, forces the download of the associated code block.
 *
 * @package		Handlers
 * @subpackage	Files
 * @version		$Id: grabcode.php 655 2007-08-04 17:15:23Z JavaWoman $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author	{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 * @version	0.21
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

// initialize variables
$code = '';
$filename = '';

// check if grabcode is allowed
if (1 == (int) $this->GetConfigValue('grabcode_button'))
{

	//get URL parameters
	$code = urldecode($this->GetSafeVar('code', 'post'));
	// TODO: use central regex library for filename validation
	$filename = (preg_match('/\w[-.\w]*/', $this->GetSafeVar('filename', 'post'))) ?  urldecode($this->GetSafeVar('filename', 'post')).".txt" : T_("codeblock").".txt";

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
	echo T_("Sorry, there is no code to download.");
}
?>
