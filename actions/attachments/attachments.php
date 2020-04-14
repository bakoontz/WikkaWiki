<?php
    // this is a menulet action relying on the files handler
    // upload path
    $upload_path = $this->GetConfigValue('upload_path').'/'.$this->GetPageTag();
    $AttachmentClass = "";
        if(is_dir($upload_path) ){
            $handle = opendir($upload_path);
            while( (gettype( $name = readdir($handle)) != "boolean")){
                $name_array[] = $name;
            }
            foreach($name_array as $temp) $folder_content .= $temp;
            closedir($handle);
            if($folder_content == "...") {
                $AttachmentClass ="emptyfolder"; // the upload path is empty
            } else {
                $AttachmentClass = "fullfolder"; // the upload path contains attachments
            }
        }
        else $AttachmentClass = "inexistingfolder"; // the upload path does not exist
       
    echo  "<a href=\"".$this->href("files")."\" title=\"Click to manage attachments\" class=\"".$AttachmentClass."\">Attachments</a>\n"; #i18n
?>
