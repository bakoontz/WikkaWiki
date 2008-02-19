<div class="page">
<?php

if ($this->IsAdmin())
{
    if ($_POST)
    {
        $tag = $this->GetPageTag();
        //  delete the page

        // delete from wakka_pages where tag=
        // delete from wakka_links where from_tag =
        // delete from wakka_acls where page_tag =
        // delete from wakka_referrers where page_tag =


        $delResult = $this->LoadSingle("delete from ".$this->config["table_prefix"]."pages where tag = '".mysql_escape_string($tag)."'");
        $delResult = $this->LoadSingle("delete from ".$this->config["table_prefix"]."links where from_tag = '".mysql_escape_string($tag)."'");
        $delResult = $this->LoadSingle("delete from ".$this->config["table_prefix"]."acls where page_tag = '".mysql_escape_string($tag)."'");
        $delResult = $this->LoadSingle("delete from ".$this->config["table_prefix"]."referrers where page_tag = '".mysql_escape_string($tag)."'");


        // also delete comments from the page
        // this is messy, because comments have acls, so we have to find the comment names and delete those too

        // finds comments for this page
        $comments = $this->LoadComments($this->tag);

        //  delete the acls for each comment
        if ($comments)
        {
            foreach ($comments as $comment)
            {
                $delResult = $this->LoadSingle("delete from ".$this->config["table_prefix"]."acls where page_tag = '".mysql_escape_string($comment["tag"])."'");
            }
        }

        // finally delete the comments themselves
        // delete from wakka_pages where comment_on= the current page
        $delResult = $this->LoadSingle("delete from ".$this->config["table_prefix"]."pages where comment_on = '".mysql_escape_string($tag)."'");


        $message = "Page has been deleted ";

        // redirect back to main page
        $this->SetMessage($message."!");
        $this->Redirect($this->config["base_url"]);
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
                <input type="button" value="Cancel" onClick="history.back();" style="width: 120px" /></td>
            </tr>
        </table>
        <?php
        print($this->FormClose());
    }
}
else
{
    print("<em>You are not allowed to delete pages.</em>");
}

?>
</div>
