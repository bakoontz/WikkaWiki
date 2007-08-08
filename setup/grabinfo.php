<?php
/**
 * Allow download of generated information.
 * 
 * @package	Setup
 * @version	$Id$
 * @license	http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 */
	$code = '';
	foreach ($_SESSION['server_info'] as $k => $v)
	{
		$code .= "$k = $v\n";
	}

	@ob_end_clean();
	$filename = 'server_info.txt';
	//set HTTP headers
	header('Content-type: text/plain');
	header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT'); //TODO: check for consistency with server time format
	header('Content-Length: '.strlen($code));
	header('Content-Description: '.$filename.' Download Data');
	header('Pragma: no-cache');
	header('Content-Disposition: attachment; filename="'.$filename.'"');

	//print code block
	echo $code;
?>