<?php
/**
 * Display a form with file attachments to the current page.
 * 
 * This actions displays a form allowing users to download files uploaded to wiki pages. By default only 
 * wiki admins can upload and delete files. If the intranet mode option is enabled, any user with write access
 * to the current page can upload or remove file attachments. If the optional download parameter is set, a simple 
 * download link is displayed for the specified file.
 *
 * Usage: {{files [download="filename"] [text="download link text"]}}
 *
 * @package		Actions
 * @version		$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @author Victor Manuel Varela (original code)
 * @author {@link http://wikkawiki.org/CryDust CryDust} (code overhaul, stylesheet)
 * 
 * @input 	string 	$download  	optional: prints a link to the file specified in the string
 * 			string 	$text		optional: a text for the link provided with the download parameter
 * @output	a form for file uploading/downloading and a table with an overview of attached files
 *
 * @uses	Wakka::HasAccess()
 * @uses	Wakka::IsAdmin()
 * @uses	Wakka::MiniHref()
 * @uses	Wakka::href()
 * @uses	Wakka::FormClose()
 * @uses	Wakka::GetPageTag()
 * @uses	Wakka::htmlspecialchars_ent()
 *
 * @todo security: check file type, not only extension
 * @todo use buttons instead of links for file deletion; #72 comment 7
 * @todo similarly replace download link with POST form button -> files handler 
 * 		 can then use only $_POST instead of $_GET
 * @todo replace intranet mode with fine-grained file ownership/ACL;
 * @todo integrate with edit handler for easy insertion of file links;
 * @todo maybe move some internal utilities to Wakka class?
 * @todo make datetime format configurable;
 * @todo add support for file versioning;
 * @todo add (AJAX-powered?) confirmation check on file deletion;
 * @todo integrate file table in page template, a la Wacko;
 */

// $max_upload_size = "1048576"; // 1 Megabyte
$max_upload_size = "2097152"; // 2 Megabyte

// File uploads permitted?
$can_upload_files = (bool) ini_get('file_uploads');
if(!defined('NO_FILE_UPLOADS')) define (('NO_FILE_UPLOADS'), "<em class='error'>File uploads are disallowed on this server</em>");
if(!defined('NO_FILE_UPLOADED')) define (('NO_FILE_UPLOADED'), "<em
class='error'>No file uploaded</em>");


if (! function_exists('mkdir_r')) 
{
	/**
	 * Create upload folder if it does not exist yet.
	 * 
	 * @param $dir
	 * @return bool
	 */
	function mkdir_r($dir)
	{
		if (strlen($dir) == 0) 
		{
			return 0;
		}
		if (is_dir($dir)) 
		{
			return 1;
		}
		elseif (dirname($dir) == $dir) 
		{
			return 1;
		}
		return (mkdir_r(dirname($dir)) and mkdir($dir,0755));
	}
}

if (! function_exists('bytesToHumanReadableUsage')) {
		/**
		* Converts bytes to a human readable string
		* @param int $bytes Number of bytes
		* @param int $precision Number of decimal places to include in return string
		* @param array $names Custom usage strings
		* @return string formatted string rounded to $precision
		*/
		function bytesToHumanReadableUsage($bytes, $precision = 2, $names = '')
		{
		   if (!is_numeric($bytes) || $bytes < 0) {
			   return false;
		   }
	   
		   for ($level = 0; $bytes >= 1024; $level++) {	
			   $bytes /= 1024;	  
		   }
   
		   switch ($level)
		   {
			   case 0:
				   $suffix = (isset($names[0])) ? $names[0] : 'Bytes';
				   break;
			   case 1:
				   $suffix = (isset($names[1])) ? $names[1] : 'KB';
				   break;
			   case 2:
				   $suffix = (isset($names[2])) ? $names[2] : 'MB';
				   break;
			   case 3:
				   $suffix = (isset($names[3])) ? $names[3] : 'GB';
				   break;	  
			   case 4:
				   $suffix = (isset($names[4])) ? $names[4] : 'TB';
				   break;							
			   default:
				   $suffix = (isset($names[$level])) ? $names[$level] : '';
				   break;
		   }
   
		   if (empty($suffix)) {
			   trigger_error('Unable to find suffix for case ' . $level);
			   return false;
		   }
   
		   return round($bytes, $precision) . ' ' . $suffix;
		}
}

if (isset($download) && $download <> '') 
{
	// link to download a file
	if ($text == '') $text = $download;
	//Although $output is passed to ReturnSafeHTML, it's better to sanitize $text here. At least it can avoid invalid XHTML.
	$text = $this->htmlspecialchars_ent($text);
	echo "<a href=\"".$this->href('files.xml',$this->GetPageTag(),'action=download&amp;file='.urlencode($download))."\">".$text."</a>";

// Show files to anyone with read access, we'll check for write access if they try to delete a file.
} 
elseif ($this->page && 
		$this->HasAccess('read') && 
		($this->method <> 'print.xml') && 
		($this->method <> 'edit'))
{

	// upload path 
	// (Needed to various read functions, regardless of php.ini file_uploads
	// setting)
	if ($this->config['upload_path'] == '') $this->config['upload_path'] = 'files';
	$upload_path = $this->config['upload_path'].'/'.$this->GetPageTag();
	if (! is_dir($upload_path)) mkdir_r($upload_path);

	if($this->IsAdmin() && $can_upload_files) { 
		// upload action
		if (isset($_POST['action']) && $_POST['action'] == 'upload') 
		{
			$uploaded = $_FILES['file']; 
			switch($_FILES['file']['error'])
			{
					case 0:
						if ($_FILES["file"]["size"] > $max_upload_size) 
						{
							echo "<b>Attempted file upload was too big.  Maximum allowed size is ".bytesToHumanReadableUsage($max_upload_size).".</b>"; 
							unlink($uploaded['tmp_name']);
						} 
						else 
						{	
							$strippedname=str_replace("'","",$uploaded['name']);
							$strippedname=stripslashes($strippedname);

							$destfile = $upload_path.'/'.$strippedname;

							if (!file_exists($destfile))
							{
								if (move_uploaded_file($uploaded['tmp_name'], $destfile))
								{
									// echo("<b>File was successfully uploaded.</b><br />\n");
								}
								else
								{
									echo("<b>There was an error uploading your file.</b><br />\n");
								}
							}
							else
							{
								echo("<b>There is already a file named \"" . $strippedname . "\".</b> <br />\nPlease rename before uploading or delete the existing file below.<br />\n");
							}
						}
						break;
					case 1:
					case 2: // File was too big.... as reported by the browser, respecting MAX_FILE_SIZE
						echo "<b>Attempted file upload was too big. Maximum allowed size is ".bytesToHumanReadableUsage($max_upload_size).".</b>"; 
						break;
					case 3:
						echo("<b>File upload incomplete! Please try again.</b><br />\n");
						break;
					case 4:
						echo NO_FILE_UPLOADED;
			}	
		}
	}

	// If no files exist, then there's no need to display an
	// attachment table
	$dir = opendir($upload_path);
	$numfiles = 0;
	$files = array();
	while ($file = readdir($dir)) 
	{
		if (!preg_match('/^\\./', $file)) 
		{
			array_push($files, $file);
			$numfiles++;
		}
	}
	closedir($dir);

	if($numfiles > 0)
	{
		// uploaded files
		echo '

						<table cellspacing="0" cellpadding="0">
						  <tr>
								<td>
								  &nbsp;
								</td>
								<td bgcolor="gray" valign="bottom" align="center">
								  <span style="color:white;font-size:x-small">
										Attachment
								  </span>
								</td>
								<td bgcolor="gray" valign="bottom" align="center">
								   <span style="color:white;font-size:x-small">
										Size
								  </span>
								</td>
								<td bgcolor="gray" valign="bottom" align="center">
								   <span style="color:white;font-size:x-small">
										Date Added
								  </span>
								</td>
						  </tr>

				';

		foreach ($files as $file) 
		{
				$num++;
				if ($this->IsAdmin()) 
				{
					$delete_link = "<a href=\"".$this->href('files.xml',$this->GetPageTag(),'action=delete&amp;file='.urlencode($file))."\">x</a>";
				} 
				else 
				{
					$delete_link = "";
				}
				$download_link = "<a href=\"".$this->href('files.xml',$this->GetPageTag(),'action=download&amp;file='.rawurlencode($file))."\">".$file."</a>";
				$size = bytesToHumanReadableUsage(filesize("$upload_path/$file"));
				$date = date("n/d/Y g:i a",filemtime("$upload_path/$file"));

						echo '

										<tr>
										  <td valign="top" align="center">
												&nbsp;&nbsp;
												'.$delete_link.'
												&nbsp;&nbsp;
										  </td>
										  <td valign="top">
												'.$download_link.'
										  </td>
										  <td valign="top">
												&nbsp;
												<span style="color:gray;font-size:small">
												  '.$size.'
												</span>
										  </td>
										  <td valign="top">
												&nbsp;
												<span style="color:gray;font-size:small">
												 '.$date.'
												</span>
										  </td>
										</tr>

								';
		}
	}

   if ($this->IsAdmin() && $can_upload_files) 
   {
		echo '
						  <tr>
								<td>
								  &nbsp;
								</td>
								<td colspan="4" valign="top" align="right" nowrap="nowrap">';  	   		
		echo $this->FormOpen('','','post','','file_upload', TRUE);
		echo '<input type="hidden" name="MAX_FILE_SIZE" value="'.$max_upload_size.'" />';
		echo '<input type="hidden" name="action" value="upload" />';
		echo '											<span style="color:gray;font-size:x-small;font-style:italic;">
										  add new attachment:
										  <input type="file" name="file" style="padding: 0px; margin: 0px; font-size: 8px; height: 15px" />
										  <input type="submit" value="+" style="padding: 0px; margin: 0px; font-size: 8px; height: 15px" />
									   </span>';
		echo $this->FormClose();
		echo "</td>\n</tr>";	
   }
   else
	{
	   echo NO_FILE_UPLOADS;
	}
   echo ' </table>  ';
}
?>
