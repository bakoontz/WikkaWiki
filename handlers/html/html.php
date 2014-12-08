<?php
if ($this->HasAccess("read") && $this->page) {
    // display page
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $this->GetPageTag(); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <style type="text/css">
		<?php include($this->GetThemePath('/','html')."/css/html.css"); ?>
    </style>
</head>
<body>
<div id="content">
<?php
    $this->config['external_link_tail'] = '';
    print($this->Format($this->page["body"], "html"));
}
?>
</div>
</body>
</html>
