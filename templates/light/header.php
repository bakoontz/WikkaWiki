<?php
//init
$message = $this->GetRedirectMessage();
$user = $this->GetUser();
$site_base = WIKKA_BASE_URL; 
//UniversalEditButton (http://universaleditbutton.org/Universal_Edit_Button) #779
$ueb = '';
if ($this->GetHandler() != 'edit' && $this->HasAccess("write", $this->page["tag"]))
{
	$ueb .= '	<link rel="alternate" type="application/x-wiki" title="'.sprintf(T_("Click to edit %s"), $this->page['tag']).'" href="'.$this->Href('edit', $this->page["tag"]).'"/>'."\n";
}
if ( substr_count($site_base, 'wikka.php?wakka=') > 0 ) $site_base = substr($site_base,0,-16);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo $this->PageTitle().":".$this->GetWakkaName(); ?></title>
	<base href="<?php echo $site_base ?>" />
<?php if ($this->GetHandler() != 'show' || $this->page["latest"] == 'N' || $this->page["tag"] == 'SandBox') echo "<meta name=\"robots\" content=\"noindex, nofollow, noarchive\" />\n"; ?>
	<meta name="generator" content="WikkaWiki" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="<?php echo $this->htmlspecialchars_ent($this->GetConfigValue("meta_keywords")) ?>" />
	<meta name="description" content="<?php echo $this->htmlspecialchars_ent($this->GetConfigValue("meta_description")) ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo $this->GetThemePath('/') ?>/css/light.css?<?php echo $this->htmlspecialchars_ent($this->GetConfigValue('stylesheet_hash')) ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo $this->GetThemePath('/') ?>/css/print.css" media="print" />
	<link rel="icon" href="<?php echo $this->GetThemePath('/') ?>/images/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="<?php echo $this->GetThemePath('/') ?>/images/favicon.ico" type="image/x-icon" />
<?php
if ($this->GetHandler() != 'edit')
{
	$rsslink  = '	<link rel="alternate" type="application/rss+xml" title="'.$this->GetWakkaName().': revisions for '.$this->GetPageTag().' (RSS)" href="'.$this->Href('revisions.xml', $this->GetPageTag()).'" />'."\n";
	$rsslink .= '	<link rel="alternate" type="application/rss+xml" title="'.$this->GetWakkaName().': recently edited pages (RSS)" href="'.$this->Href('recentchanges.xml', $this->GetPageTag()).'" />'."\n";
	echo $rsslink;
}
if (isset($this->additional_headers) && is_array($this->additional_headers) && count($this->additional_headers))
{
		foreach ($this->additional_headers as $additional_headers)
		{
				echo $additional_headers;
		}
}
//UniversalEditButton
echo $ueb;
?>
</head>
<body>
<!-- BEGIN PAGE WRAPPER -->
<div id="page">
<?php
//display system messages
if (isset($message) && strlen($message)>0)
{
	echo '<div class="success">'.$message.'</div>';
}
?>
<!-- BEGIN PAGE HEADER -->
<div id="header">
<h2><a id="homepage_link" href="<?php echo $this->href('', $this->GetConfigValue('root_page'), ''); ?>"><?php echo $this->GetWakkaName();?></a> : <a href="<?php echo $this->href('backlinks', '', ''); ?>" title="Display a list of pages linking to <?php echo $this->GetPageTag() ?>"><?php echo $this->GetPageTag(); ?></a></h2>
<?php echo $this->MakeMenu('main_menu'); ?>
</div>
<div>
<?php
	if($this->GetConfigValue('enable_breadcrumbs'))
		echo $this->StringifyBreadcrumbs();
?>	
</div>
<?php if ($this->IsAdmin()) echo $this->MakeMenu('dashboard'); ?>
<!-- END PAGE HEADER -->
