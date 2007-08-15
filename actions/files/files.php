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
 * @version		$Id:files.php 369 2007-03-01 14:38:59Z DarTar $
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @author Victor Manuel Varela (original code)
 * @author {@link http://wikkawiki.org/CryDust CryDust} (code overhaul, stylesheet)
 * @author {@link http://wikkawiki.org/DarTar Dario Taraborelli} (code cleanup, defaults, i18n, added intranet mode)
 * @author {@link http://wikkawiki.org/NilsLindenberg Nils Lindenberg} (i18n)
 * 
 * @input 	string 	$download  	optional: prints a link to the file specified in the string
 * 			string 	$text		optional: a text for the link provided with the download parameter
 * @output	a form for file uploading/downloading and a table with an overview of attached files
 *
 * @uses	Wakka::Href()
 * @uses	Wakka::HasAccess()
 * @uses	Wakka::IsAdmin()
 * @uses	Wakka::MiniHref()
 * @uses	Wakka::htmlspecialchars_ent()
 *
 * @todo security: check file type, not only extension
 * @todo use buttons instead of links for file deletion; #72 comment 7
 * @todo similarly replace download link with POST form button -> files handler 
 * 		 can then use only $_POST instead of $_GET
 * @todo replace $_REQUEST in files handler with $_POST / $_GET; #72, #312
 * @todo replace intranet mode with fine-grained file ownership/ACL;
 * @todo integrate with edit handler for easy insertion of file links;
 * @todo maybe move some internal utilities to Wakka class?
 * @todo make datetime format configurable;
 * @todo add support for file versioning;
 * @todo add (AJAX-powered?) confirmation check on file deletion;
 * @todo integrate file table in page template, à la Wacko;
 */

//---- Global action settings ----
/**
 * Toggle intranet mode.
 *
 * Setting this mode to 1 allows anyone with write-access to the page to upload files.
 * WARNING: enabling this option on a public server will allow any user with write access to upload files to your wiki.
 * We strongly recommend enabling intranet mode in an intranet environment only to avoid major security issues.
 */
if(!defined('INTRANET_MODE')) define('INTRANET_MODE', 0);

/** Size limit for file uploads (in bites) */
if(!defined('MAX_UPLOAD_SIZE')) define('MAX_UPLOAD_SIZE', 2097152);
/** Pipe-separated list of allowed file extensions */
if(!defined('ALLOWED_FILE_EXTENSIONS')) define('ALLOWED_FILE_EXTENSIONS', 'gif|jpeg|jpg|jpe|png|doc|xls|csv|ppt|ppz|pps|pot|pdf|asc|txt|zip|gtar|gz|bz2|tar|rar|vpp|mpp|vsd|mm|htm|html'); #34
/** Displayed date format */
if(!defined('UPLOAD_DATE_FORMAT')) define('UPLOAD_DATE_FORMAT', 'Y-m-d H:i'); //TODO use general config settings for date format 
/** Sort routines */
if(!defined('SORT_BY_FILENAME')) define('SORT_BY_FILENAME', 'filename');
if(!defined('SORT_BY_DATE')) define('SORT_BY_DATE', 'date');
if(!defined('SORT_BY_SIZE')) define('SORT_BY_SIZE', 'size');

// ---- Error code constants ----
if (!defined('UPLOAD_ERR_OK')) define('UPLOAD_ERR_OK', 0);
if (!defined('UPLOAD_ERR_INI_SIZE')) define('UPLOAD_ERR_INI_SIZE', 1);
if (!defined('UPLOAD_ERR_FORM_SIZE')) define('UPLOAD_ERR_FORM_SIZE', 2);
if (!defined('UPLOAD_ERR_PARTIAL')) define('UPLOAD_ERR_PARTIAL', 3);
if (!defined('UPLOAD_ERR_NO_FILE')) define('UPLOAD_ERR_NO_FILE', 4);
if (!defined('UPLOAD_ERR_NO_TMP_DIR')) define('UPLOAD_ERR_NO_TMP_DIR', 6);

// ---- Initialize variables ----
$text = '';
$output = '';
$output_files = '';
$error_msg = '';
$notification_msg = '';
$is_writable = FALSE;
$is_readable = FALSE;
$max_upload_size = MAX_UPLOAD_SIZE;
$allowed_extensions = ALLOWED_FILE_EXTENSIONS;

// ---- Utilities ----
/**
 * Check if the current user can upload files
 */
if (!function_exists('userCanUpload'))
{
	function userCanUpload()
	{
		global $wakka;
		switch(TRUE) 
		{
			case ($wakka->IsAdmin()):
			case (INTRANET_MODE && $wakka->HasAccess('write')):
				return TRUE;
				break;

			default:
				return FALSE;
		}
	}
}

/**
 * Create upload folder if it does not exist
 */
if (!function_exists('mkdir_r'))
{
	function mkdir_r ($dir)
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

/**
 * Convert bytes to a human readable string
 *
 * @param int $bytes Number of bytes
 * @param int $precision Number of decimal places to include in return string
 * @param array $names Custom usage strings
 * @return string formatted string rounded to $precision
 */
if (!function_exists('bytesToHumanReadableUsage'))
{
	function bytesToHumanReadableUsage($bytes, $precision = 0, $names = '')
	{
		if (!is_numeric($bytes) || $bytes < 0)
		{
			$bytes = 0;
		}
		if (!is_numeric($precision) || $precision < 0)
		{
			$precision = 0;
		}
		if (!is_array($names))
		{
			$names = array('b','Kb','Mb','Gb','Tb','Pb','Eb');
		}
		$level = floor(log($bytes)/log(1024));
		$suffix = '';
		if ($level < count($names))
		{
			$suffix = $names[$level];
		}
		return round($bytes/pow(1024, $level), $precision) . $suffix;
	}
}

// ---- Run action ----

// 0. define upload path for the current page
if ($this->GetConfigValue('upload_path') == '')
{
	$this->SetConfigValue('upload_path','files');
}
$upload_path = $this->GetConfigValue('upload_path').DIRECTORY_SEPARATOR.$this->tag; #89

// 1. check if main upload path is writable
if (!is_writable($this->GetConfigValue('upload_path')))
{
	echo '<div class="alertbox">'.sprintf(ERROR_UPLOAD_DIRECTORY_NOT_WRITABLE, '<tt>'.$this->GetConfigValue('upload_path').'</tt>').'</div>'; #89
}
else
{
	$is_writable = TRUE;
}

// 2. print a simple download link for the specified file, if it exists
if (isset($vars['download']))
{
	if (file_exists($upload_path.DIRECTORY_SEPARATOR.$vars['download'])) #89
	{
		if (!isset($vars['text']))
		{
			$text = $vars['download'];
		} else
		{
			$text = $vars['text'];
		}
		//Although $output is passed to ReturnSafeHTML, it's better to sanitize $text here. At least it can avoid invalid XHTML.
		$text = $this->htmlspecialchars_ent($text);
		$output .=  '<a href="'
			. $this->Href('files.xml', $this->tag, 'action=download&amp;file='.rawurlencode($vars['download']))
			. '" title="'.sprintf(DOWNLOAD_LINK_TITLE, $text).'">'
			. urldecode($text)
			. '</a>';
	}
	else
	{
		echo '<em class="error">'.sprintf(ERROR_NONEXISTENT_FILE, '<tt>'.$vars['download'].'</tt>').'</em>';
	}
}

// 3. user is trying to upload
elseif ($this->page && $this->HasAccess('read') && $this->handler == 'show' && $is_writable)
{

	// create new folders if needed
	if ($is_writable && !is_dir($upload_path))
	{
		mkdir_r($upload_path);
	}

	// get upload results
	#if ($is_writable && isset($_POST['action']) && $_POST['action'] == 'upload' && userCanUpload()) #38
	if ($is_writable && isset($_POST['upload']) && $_POST['upload'] == 'Upload' && userCanUpload()) #38 #i18n
	{
		switch ($_FILES['file']['error'])
		{
			case UPLOAD_ERR_OK:
				if ($_FILES['file']['size'] > MAX_UPLOAD_SIZE)
				{
					$error_msg = sprintf(ERROR_FILE_TOO_BIG, bytesToHumanReadableUsage($max_upload_size));
					unlink($_FILES['file']['tmp_name']);
				}
				elseif (preg_match('/.+\.('.$allowed_extensions.')$/i', $_FILES['file']['name']))
				{
					$strippedname = str_replace('\'', '', $_FILES['file']['name']);
					$strippedname = rawurlencode($strippedname);
					$strippedname = stripslashes($strippedname);
					$destfile = $upload_path.DIRECTORY_SEPARATOR.$strippedname; #89

					if (!file_exists($destfile))
					{
						if (move_uploaded_file($_FILES['file']['tmp_name'], $destfile))
						{
							$notification_msg = SUCCESS_FILE_UPLOADED;
						}
						else
						{
							$error_msg = ERROR_UPLOADING_FILE;
						}
					}
					else
					{
						$error_msg = sprintf(ERROR_FILE_ALREADY_EXISTS, '<tt>'.$strippedname.'</tt>');
					}
				}
				else
				{
					$error_msg = ERROR_EXTENSION_NOT_ALLOWED;
					unlink($_FILES['file']['tmp_name']);
				}
				break;
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				$error_msg = sprintf(ERROR_FILE_TOO_BIG, bytesToHumanReadableUsage($max_upload_size)); 
				break;
			case UPLOAD_ERR_PARTIAL:
				$error_msg = ERROR_FILE_UPLOAD_INCOMPLETE;
				break;
			case UPLOAD_ERR_NO_FILE:
				$error_msg = ERROR_NO_FILE_SELECTED;
				break;
			case UPLOAD_ERR_NO_TMP_DIR:
				$error_msg = ERROR_FILE_UPLOAD_IMPOSSIBLE;
				break;
	}
	if ($error_msg != '')
	{
		 $output .= '<em class="error">'.$error_msg.'</em>';
	} else if ($notification_msg !='')
	{
		 $output .= '<em class="success">'.$notification_msg.'</em>';
	}
}

// 4. display attached files table
if (is_readable($upload_path))
{ 
	$is_readable = TRUE;
	$dir = opendir($upload_path);
	$n = 0;
	// Build file interface

	// Construct ordered array of filename, date, and size arrays for
	// sorting
	$filenameArr = array();
	$dateArr = array();
	$sizeArr = array();
	while (false !== ($file = readdir($dir)))
	{
		if($file{0} == '.')
		{
			continue;
		}
		array_push($filenameArr, $file);
		array_push($dateArr, filemtime($upload_path.DIRECTORY_SEPARATOR.$file));
		array_push($sizeArr, filesize($upload_path.DIRECTORY_SEPARATOR.$file));
	}
	closedir($dir);

	// Sort file array
	$sortby = SORT_BY_FILENAME;
	if(isset($_GET['sortby']))
	{
		$sortby = $_GET['sortby'];
	}
	switch($sortby) 
	{
		case SORT_BY_DATE : 
			array_multisort($dateArr, SORT_ASC, SORT_NUMERIC, $filenameArr, $sizeArr);
			break;
		case SORT_BY_SIZE : 
			array_multisort($sizeArr, SORT_ASC, SORT_NUMERIC, $filenameArr, $dateArr);
			break;
		case SORT_BY_FILENAME: 
		default:
			array_multisort($filenameArr, SORT_ASC, SORT_STRING, $dateArr, $sizeArr);
	}

	for($i=0; $i<count($filenameArr); ++$i)
	{
		$file = $filenameArr[$i];
		$filedate = $dateArr[$i];
		$filesize = $sizeArr[$i];

		$n++;
		$delete_link = '<!-- delete -->';
		if (userCanUpload())
		{
			// TODO #72
			$delete_link = '<a class="keys" href="'
			.$this->Href('files.xml',$this->tag,'action=delete&amp;file='.rawurlencode($file))	// @@@ should be POST form button, not link
			.'" title="'.sprintf(DELETE_LINK_TITLE, $file).'">x</a>';
		}
		$download_link = '<a href="' .$this->Href('files.xml',$this->tag,'action=download&amp;file='.rawurlencode($file))
			.'" title="'.sprintf(DOWNLOAD_LINK_TITLE, $file).'">'.urldecode($file).'</a>';
		$size = bytesToHumanReadableUsage($filesize); #89
		$date = date(UPLOAD_DATE_FORMAT, $filedate); #89

		$output_files .= '<tr>'."\n";
		if (userCanUpload())
		{
			$output_files .=	'<td>'.$delete_link.'</td>'."\n";	// TODO #72
		}
		$output_files .=	'<td>'.$download_link.'</td>'."\n"
			.'<td>'.$date.'</td>'."\n"
			.'<td align="right"><tt>'.$size.'</tt></td>'."\n"
			.'</tr>'."\n";
	}

	if ($n > 0)
	{
		$output .= '<div class="files">'."\n";
		// display uploaded files
		$output .= '<table class="files">'."\n"
			.'<caption>'.FILE_TABLE_CAPTION.'</caption>'."\n"
			.'<thead>'."\n"
			.'<tr>'."\n";
		if (userCanUpload())
		{
			$output .= '<th>&nbsp;</th>'."\n"; //For the delete link. Only needed when user has file upload privs.
		}
		$output .= '<th><a href="'.$this->Href('', $this->tag, 'sortby=filename').'">'.FILE_TABLE_HEADER_NAME.'</a></th>'."\n"
		.'<th><a href="'.$this->Href('', $this->tag, 'sortby=date').'">'.FILE_TABLE_HEADER_DATE.'</a></th>'."\n"
		.'<th><a href="'.$this->Href('', $this->tag, 'sortby=size').'">'.FILE_TABLE_HEADER_SIZE.'</a></th>'."\n"
			.'</tr>'."\n"
			.'</thead>'."\n"
			.'<tbody>'."\n";
		$output .= $output_files;
		$output .= '</tbody>'."\n"
			.'</table>'."\n";
	}
}
// cannot read the folder contents
else
{
	echo '<div class="alertbox">'.sprintf(ERROR_UPLOAD_DIRECTORY_NOT_READABLE, '<tt>'.$upload_path.'</tt>').'</div>'; #89

}

// print message if no files are available
if ($is_readable && $n < 1)
{
	$output .= '<em>'.NO_ATTACHMENTS.'</em>'."\n"; //
}

// 5. display upload form
if ($is_writable && userCanUpload())
{
	// upload form
/*
	// check if the hidden field is still needed - Href() already provides
	// the wakka= part of the URL - NOT needed!
	$input_for_no_rewrite_mode = '<!-- rewrite mode disabled -->';
	if (!$this->GetConfigValue('rewrite_mode'))
	{
		$input_for_no_rewrite_mode = '<input type="hidden" name="wakka" value="'.$this->MiniHref().'" />';
	}
	$href = $this->Href();
	// use (advanced) FormOpen() and FormClose()
	// make form accessible
	$output .=	'<form action="'.$href.'" method="post" enctype="multipart/form-data">'."\n"
		.$input_for_rewrite_mode."\n"
		.'<input type="hidden" name="action" value="upload" />'."\n"
		.'<fieldset><legend>'.FILE_UPLOAD_FORM_LABEL.'</legend>'."\n"
		.'<input type="file" name="file" /><br />'."\n"
		.'<input type="submit" value="Upload" />'."\n"		#i18n
		.'</fieldset>'."\n"
		.'</form>'."\n";
*/
	// build form
	$form = '';
	$form .= $this->FormOpen('', '', 'post', '', '', TRUE); // post form for current page with file upload
	$form .= '<fieldset><legend>'.FILE_UPLOAD_FORM_LEGEND.'</legend>'."\n";
	$form .= '<label for="fileupload">'.FILE_UPLOAD_FORM_LABEL.'</label> <input id="fileupload" type="file" name="file" /><br />'."\n";
	$form .= '<input name = "upload" type="submit" value="'.FILE_UPLOAD_FORM_BUTTON.'" />'."\n";
	$form .= '</fieldset>'."\n";
	$form .= $this->FormClose();
	// add to output
	$output .= $form;
	}
}
$output .= '</div>';

// 6. print output to screen
$output = $this->ReturnSafeHTML($output);
echo $output;
?>