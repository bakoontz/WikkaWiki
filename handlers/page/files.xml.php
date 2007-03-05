<?php
/**
 * Handle download/deletion of a file. 
 *
 * Only files uploaded using the Wiki can be downloaded/deleted using this handler, 
 * and every user who has read access to the page to which the files are attached
 * can download them. For the deletion, only administrators can delete files.
 * Range: and Accept-Range: headers are supported, so advanced downloader tools can
 * be used to download heavy size files.
 * 
 * See also {@link files.php}, {@link Config::$upload_path}.
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
 * @uses		Config::$upload_path
 */

// upload path
if ($this->config['upload_path'] == '') $this->config['upload_path'] = 'files';
$upload_path = $this->config['upload_path'].DIRECTORY_SEPARATOR.$this->GetPageTag(); #89
if (! is_dir($upload_path)) mkdir_r($upload_path);

// do the action
#switch ($_REQUEST['action']) 
switch ($_GET['action'])	#312 
{
    case 'download':
			#$_REQUEST['file'] = basename($_REQUEST['file']);
			$_GET['file'] = basename($_GET['file']); #312
            if ($this->HasAccess('read')) {
				#$path = "{$upload_path}/{$_REQUEST['file']}";
				$path = $upload_path.DIRECTORY_SEPARATOR.$_GET['file'];	#89, #312
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
				#@unlink("{$upload_path}/{$_REQUEST['file']}");
				@unlink($upload_path.DIRECTORY_SEPARATOR.$_GET['file']); #89, #312 // TODO if this is admin-only, why hide any errors?
            }
            print $this->redirect($this->Href());
}
?>
