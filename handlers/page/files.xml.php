<?php
/**
 * Handle download/deletion of a file. 
 * 
 * @package		Handlers
 * @subpackage	Files
 * @version		$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses		mkdir_r()
 * @uses		Wakka::GetPageTag()
 * @uses		Wakka::HasAccess()
 * @uses		Wakka::Href()
 * @uses		Wakka::IsAdmin()
 * @uses		Wakka::redirect()
	* @todo WikkaCodingGuidelinesHowTo
 */

// upload path
if ($this->config['upload_path'] == '') $this->config['upload_path'] = 'files';
$upload_path = $this->config['upload_path'].'/'.$this->GetPageTag();
if (! is_dir($upload_path)) mkdir_r($upload_path);

// do the action
switch ($_REQUEST['action']) {
    case 'download':
												header('Accept-Ranges: bytes');
            $_REQUEST['file'] = basename($_REQUEST['file']);
            $path = "{$upload_path}/{$_REQUEST['file']}";
                $filename = basename($path);
		    Header("Content-Type: application/x-download");
		    Header("Content-Disposition: attachment; filename=\"".urldecode($filename)."\"");
            if ($this->HasAccess('read')) {
																				if (isset($_SERVER['HTTP_RANGE']) && (preg_match('/^.*bytes[= ]+(\d+)-(\d+)\s*$/', $_SERVER['HTTP_RANGE'], $range)) && (intval($range[2]) >= intval($range[1])))
																				{
																												$rstart = $range[1];
																												$rend = $range[2];
																												$fp = fopen($path, 'rb');
																												fseek($fp, $rstart+SEEK_SET);
																												$data = fread($fp, $rend - $rstart + 1);
																												fclose($fp);
																												header('Content-Range: bytes '.$rstart.'-'.$rend.'/'.filesize($path));
																												header('HTTP/1.1 206 Partial content');
																												echo $data;
																												exit();
																				}
		    //Header("Content-Length: ".filesize($path));
    		    //Header("Connection: close");
										ob_end_clean();
										ob_end_clean();
										$fp = fopen($path, 'rb');
										while ($data = fread($fp, 4096))
										{
																		echo $data;
										}
										fclose($fp);
    		    //@readfile($path);
		    exit();
            }
    case 'delete':   
            // if ($this->HasAccess('write')) {
		if ($this->IsAdmin()) {
                @unlink("{$upload_path}/{$_REQUEST['file']}");
            }
            print $this->redirect($this->Href());
}
?>
