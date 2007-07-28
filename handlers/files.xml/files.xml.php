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
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses	Wakka::GetConfigValue()
 * @uses	Wakka::GetPageTag()
 * @uses	Wakka::HasAccess()
 * @uses	Wakka::Href()
 * @uses	Wakka::IsAdmin()
 * @uses	Wakka::redirect()
 * @uses	Config::$upload_path
 * @todo	make shared download code for this and grab code handler
 */

// upload path
if ('' == $this->GetConfigValue('upload_path'))
{
	$this->SetConfigValue('upload_path','files');
}
$upload_path = $this->GetConfigValue('upload_path').DIRECTORY_SEPARATOR.$this->GetPageTag(); #89
if (!is_dir($upload_path))
{
	mkdir_r($upload_path);
}

// do the action
switch ($_GET['action'])	#312
{
	// @@@ shared download code
	case 'download':
		header('Accept-Ranges: bytes');
		$_GET['file'] = basename($_GET['file']); #312
		$path = $upload_path.DIRECTORY_SEPARATOR.$_GET['file'];	#89, #312
		$filename = basename($path);
		header("Content-Type: application/x-download");
		header("Content-Disposition: attachment; filename=\"".urldecode($filename)."\"");
		if ($this->HasAccess('read')) 
		{
			if (isset($_SERVER['HTTP_RANGE']) &&
				(preg_match('/^.*bytes[= ]+(\d+)-(\d+)\s*$/', $_SERVER['HTTP_RANGE'], $range)) &&
				(intval($range[2]) >= intval($range[1])))
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
			@ob_end_clean();
			@ob_end_clean();
			$fp = fopen($path, 'rb');
			while (!feof($fp))
			{
				$data = fread($fp, 4096);
				echo $data;
			}
			fclose($fp);
			exit();
		}
	case 'delete':
		if ($this->IsAdmin()) 
		{
			@unlink($upload_path.DIRECTORY_SEPARATOR.$_GET['file']); #89, #312 // TODO if this is admin-only, why hide any errors?
		}
		print $this->redirect($this->Href());
}
?>