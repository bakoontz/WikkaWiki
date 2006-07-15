<?php
/**
 * Handle download/deletion of a file. 
 * 
 * @package		Handlers
 * @subpackage	Files
 * @version		$Id$
 * 
 * @uses		mkdir_r()
 * @uses		Wakka::GetPageTag()
 * @uses		Wakka::HasAccess()
 * @uses		Wakka::Href()
 * @uses		Wakka::IsAdmin()
 * @uses		Wakka::redirect()
 * @filesource
 */

// upload path
if ($this->config['upload_path'] == '') $this->config['upload_path'] = 'files';
$upload_path = $this->config['upload_path'].'/'.$this->GetPageTag();
if (! is_dir($upload_path)) mkdir_r($upload_path);

// do the action
switch ($_REQUEST['action']) {
    case 'download':
            $_REQUEST['file'] = basename(urldecode($_REQUEST['file']));
            if ($this->HasAccess('read')) {
                $path = "{$upload_path}/{$_REQUEST['file']}";
                $filename = basename($path);
		    Header("Content-Length: ".filesize($path));
		    Header("Content-Type: application/x-download");
		    Header("Content-Disposition: attachment; filename=".$filename);
    		    Header("Connection: close");
    		    @readfile($path);
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