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
 * @version		$Id: files.xml.php 1370 2009-06-14 04:57:53Z BrianKoontz $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	mkdir_r()
 * @uses    Wakka::GetSafeVar()
 * @uses	Wakka::SetConfigValue()
 * @uses	Wakka::GetPageTag()
 * @uses	Wakka::HasAccess()
 * @uses	Wakka::Href()
 * @uses	Wakka::IsAdmin()
 * @uses	Wakka::SetRedirectMessage()
 * @uses	Wakka::Redirect()
 * @uses	Config::$upload_path
 * @uses	Config::$root_page
 * @uses	WIKKA_ERROR_ACL_READ_INFO
 *
 * @todo	make shared download code for this and grab code handler
 */

// upload path
if ('' == $this->GetConfigValue('upload_path'))
{
	$this->SetConfigValue('upload_path','files');
}
$upload_path = $this->GetConfigValue('upload_path').DIRECTORY_SEPARATOR.$this->GetPageTag(); # #89
if (!is_dir($upload_path))
{
	mkdir_r($upload_path);
}

if (!isset($_GET['file']) || !isset($_GET['action']) || !is_string($_GET['file']))
{
	// invocation of files.xml must provide $_GET['file'] and $_GET['action'].
	// todo: add an error message here: probably, ERROR_BAD_PARAMETERS should be splitted.
	$this->Redirect('');
}

$file = $this->GetSafeVar('file', 'get');
if ('.' == $file{0})
{
	$this->Redirect($this->Href(), ERROR_FILETYPE_NOT_ALLOWED);
}
// do the action
$action = $this->GetSafeVar('action', 'get');
switch ($action)	# #312
{
	// @@@ shared download code
	case 'download':
		header('Accept-Ranges: bytes');
		$_GET['file'] = basename($file); # #312
		$path = $upload_path.DIRECTORY_SEPARATOR.$file;	# #89, #312
		$filename = basename($path);
		header("Content-Type: application/x-download");
		header("Content-Disposition: attachment; filename=\"".urldecode($filename)."\"");
		header("Cache-control: must-revalidate");
		if (!file_exists($path))
		{
			$this->Redirect($this->Href(), sprintf(ERROR_NONEXISTENT_FILE, $file));
		}
		if (!$this->HasAccess('read'))
		{
			// The user may have followed a link from email or external site, but he has no access to the page.
			// We redirect this user to the HomePage.
			$this->Redirect($this->Href('', $this->GetConfigValue('root_page')), WIKKA_ERROR_ACL_READ_INFO);
		}
		if (isset($_SERVER['HTTP_RANGE']) &&
			(preg_match('/^.*bytes[= ]+(\d+)-(\d+)\s*$/', $_SERVER['HTTP_RANGE'], $range)) &&
			((int) $range[2] >= (int) $range[1])
		   )
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
	case 'delete':
		if ($this->IsAdmin() && FALSE===empty($file) && FILE_DELETED == $_SESSION['redirectmessage'])
		{
			$delete_success = @unlink($upload_path.DIRECTORY_SEPARATOR.$file); # #89, #312 
			if (!$delete_success)
			{
				$this->SetRedirectMessage(ERROR_FILE_NOT_DELETED);
			}
			print $this->Redirect($this->Href());
		}
}
?>
