<div id="content">
<?php

if (!defined('CANCEL_ACL_LABEL')) define('CANCEL_ACL_LABEL', 'Cancel');

// cancel operation and return to the page
if (isset($_POST['cancel']) && ($_POST['cancel'] == CANCEL_ACL_LABEL))
{
	$this->Redirect($this->Href());
}

if ($this->IsAdmin())
{
	if (isset($_POST['form_id']))
    {
	    $delete = FALSE;
    	if (FALSE != ($aKey = $this->getSessionKey($_POST['form_id'])))	# check if form key was stored in session
		{
			if (TRUE == ($rc = $this->hasValidSessionKey($aKey)))	# check if correct name,key pair was passed
			{
				$delete = TRUE;
			}
		}
		if (TRUE == DELETE)
		{
			$tag = $this->GetPageTag();

	        //  delete the page, comments, related links, acls and referrers 
	        $this->Query("delete from ".$this->config["table_prefix"]."pages where tag = '".mysql_real_escape_string($tag)."'");
	        $this->Query("delete from ".$this->config["table_prefix"]."comments where page_tag = '".mysql_real_escape_string($tag)."'");
	        $this->Query("delete from ".$this->config["table_prefix"]."links where from_tag = '".mysql_real_escape_string($tag)."'");
	        $this->Query("delete from ".$this->config["table_prefix"]."acls where page_tag = '".mysql_real_escape_string($tag)."'");
	        $this->Query("delete from ".$this->config["table_prefix"]."referrers where page_tag = '".mysql_real_escape_string($tag)."'");
	
	        // redirect back to main page
	        $this->Redirect($this->config["base_url"], "Page has been deleted!");
    	}
    }
    else
    {
        // show form
        ?>
        <h3>Delete <?php echo $this->Link($this->GetPageTag()) ?></h3>
        <br />

        <?php echo $this->FormOpen("delete") ?>
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>Completely delete this page, including all comments?</td>
            </tr>
            <tr>
                <td> <!-- nonsense input so form submission works with rewrite mode --><input type="hidden" value="" name="null"><input type="submit" value="Delete Page"  style="width: 120px"   />
                <input type="submit" value="<?php echo CANCEL_ACL_LABEL?>" name="cancel" style="width: 120px" /></td>
            </tr>
        </table>
        <?php
        print($this->FormClose());
    }
}
else
{
    print("<em class='error'>You are not allowed to delete pages.</em>");
}

?>
</div>
