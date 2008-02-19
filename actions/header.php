<?php
	$message = $this->GetMessage();
	$user = $this->GetUser();
      $site_base = $this->GetConfigValue("base_url");
      if ( substr_count($site_base, 'wakka.php?wakka=') > 0 ) $site_base = substr($site_base,0,-16);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
	<title><?php echo $this->AddSpaces($this->GetWakkaName())." : ".$this->AddSpaces($this->GetPageTag()); ?></title>
	<base href="<?php echo $site_base ?>" />
	<?php if ($this->GetMethod() != 'show') echo "<meta name=\"robots\" content=\"noindex, nofollow\"/>\n";?>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta name="keywords" content="<?php echo $this->GetConfigValue("meta_keywords") ?>" />
	<meta name="description" content="<?php echo $this->GetConfigValue("meta_description") ?>" />
	<link rel="stylesheet" type="text/css" href="css/<?php echo $this->GetConfigValue("stylesheet") ?>" />
	<link rel="stylesheet" type="text/css" href="css/print.css" media="print" /> 
	<script language="JavaScript" type="text/javascript">
		function fKeyDown()
		{
			if (event.keyCode == 9)
			{
				event.returnValue = false;
				document.selection.createRange().text = String.fromCharCode(9);
			}
		}
	</script>
</head>

<body
	<?php echo (!$user || ($user["doubleclickedit"] == 'Y')) && ($this->GetMethod() == "show") ? "ondblclick=\"document.location='".$this->href("edit")."';\" " : "" ?>
	<?php echo $message ? "onLoad=\"alert('".$message."');\" " : "" ?>
>

<div class="header">
	<h2><?php echo $this->AddSpaces($this->config["wakka_name"]) ?> : <a href="<?php echo $this->href("", "TextSearch", "phrase=").urlencode($this->GetPageTag()); ?>"><?php echo $this->AddSpaces($this->GetPageTag()); ?></a></h2>
	<?php echo $this->Link($this->config["root_page"]); ?> ::
	<?php 
		if ($this->GetUser()) {
			echo $this->config["logged_in_navigation_links"] ? $this->Format($this->config["logged_in_navigation_links"])." :: " : ""; 
			echo "You are ".$this->Format($this->UserName());
		} else { 
			// echo $this->config["navigation_links"] ? $this->Format($this->config["navigation_links"])." :: " : ""; 
			echo $this->config["navigation_links"] ? $this->Format($this->config["navigation_links"]) : ""; 
		} 
	?> 	
</div>

