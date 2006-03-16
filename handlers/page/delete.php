<div class="page">
<?php

// constant section
define('PAGE_DELETED', 'Page has been deleted!');
define('PAGE_DELETION_NOT_ALLOWED', 'You are not allowed to delete this page.');
define('PAGE_DELETION_FORM_HEADER', 'Delete %s?'); // %s - name of the page
define('PAGE_DELETION_FORM_LABEL', 'Completely delete this page, including all comments?');
define('PAGE_DELETION_DELETE_BUTTON', 'Delete Page');
define('PAGE_DELETION_CANCEL_BUTTON', 'Cancel');

if ($this->IsAdmin())
{
    if ($_POST)
    {
        $tag = $this->GetPageTag();

        //  delete the page, comments, related links, acls and referrers
        $this->Query("delete from ".$this->config["table_prefix"]."pages where tag = '".mysql_real_escape_string($tag)."'");
        $this->Query("delete from ".$this->config["table_prefix"]."comments where page_tag = '".mysql_real_escape_string($tag)."'");
        $this->Query("delete from ".$this->config["table_prefix"]."links where from_tag = '".mysql_real_escape_string($tag)."'");
        $this->Query("delete from ".$this->config["table_prefix"]."acls where page_tag = '".mysql_real_escape_string($tag)."'");
        $this->Query("delete from ".$this->config["table_prefix"]."referrers where page_tag = '".mysql_real_escape_string($tag)."'");

        // redirect back to main page
        $this->Redirect($this->config["base_url"], PAGE_DELETED);
    }
    else
    {
        // show form
        ?>
        <h3><?php printf(PAGE_DELETION_FORM_HEADER, $this->Link($this->GetPageTag())); ?></h3>
        <br />

        <?php echo $this->FormOpen("delete") ?>
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td><?php echo PAGE_DELETION_FORM_LABEL; ?></td>
            </tr>
            <tr>
                <td> <!-- nonsense input so form submission works with rewrite mode --><input type="hidden" value="" name="null"><input type="submit" value="<?php echo PAGE_DELETION_DELETE_BUTTON; ?>"  style="width: 120px"   />
                <input type="button" value="<?php echo PAGE_DELETION_CANCEL_BUTTON; ?>" onclick="history.back();" style="width: 120px" /></td>
            </tr>
        </table>
        <?php
        print($this->FormClose());
    }
}
else
{
    print('<em>'.PAGE_DELETION_NOT_ALLOWED.'</em>');
}

?>
</div>