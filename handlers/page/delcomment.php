<?php

//print("<xmp>"); print_r($_REQUEST); exit;

        //select comment and delete it
        $comment_number = intval(trim($_POST["comment_number"]));
        $Commenttodel = $this->LoadSingle("select tag, owner from ".$this->config["table_prefix"]."pages where comment_on != '' AND id = '".$comment_number."' order by id desc limit 1");
        $current_user = $this->GetUser();
        if ($this->UserIsOwner() || $Commenttodel["owner"]==$current_user['name'] || $this->IsAdmin())
        {
        	$deleted = $this->LoadSingle("delete from ".$this->config["table_prefix"]."pages where comment_on != '' AND id = '".$comment_number."' limit 1");
            $deleted = $this->LoadSingle("delete from ".$this->config["table_prefix"]."acls where page_tag = '".$Commenttodel["tag"]."' limit 3");
	        // redirect to page
       		$this->redirect($this->href());
        }
		else
		{
        	print("<div class=\"page\"><em>Sorry, you're not allowed to delete this comment!</em></div>\n");
		}

?>