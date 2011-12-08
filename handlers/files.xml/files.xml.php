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
 * @uses	T_("You are not allowed to access this information.")
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
	// todo: add an error message here: probably, T_("The parameters you supplied are incorrect, one of the two revisions may have been removed.") should be splitted.
	$this->Redirect();
}

// Sanitize the filename to prevent path traversal attacks
$file = $this->GetSafeVar('file','get');
$matches = '';
preg_match("/^.*?([^\.\/\\\]+\.[A-Za-z0-9]{2,4})$/", $file, $matches);
if(isset($matches[1]))
	$file = $matches[1];
else
{
	$this->SetRedirectMessage(T_("Invalid filename"));
	$this->Redirect();
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
			$this->Redirect($this->Href(), sprintf(T_("Sorry, a file named %s does not exist."), $file));
		}
		if (!$this->HasAccess('read'))
		{
			// The user may have followed a link from email or external site, but he has no access to the page.
			// We redirect this user to the HomePage.
			$this->Redirect($this->Href('', $this->GetConfigValue('root_page')), T_("You are not allowed to access this information."));
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
		if ($this->IsAdmin() && FALSE===empty($file) && T_("File deleted") == $_SESSION['redirectmessage'])
		{
			$delete_success = @unlink($upload_path.DIRECTORY_SEPARATOR.$file); # #89, #312 
			if (!$delete_success)
			{
				$this->SetRedirectMessage(T_("Sorry, the file could not be deleted!"));
			}
			print $this->Redirect($this->Href());
		}
}
?>
