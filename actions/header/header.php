<?php
/**
 * Generates the page header.
 *
 * @package		Template
 * @version		$Id:header.php 369 2007-03-01 14:38:59Z DarTar $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @uses	Config::$base_url
 * @uses	Config::$enable_rss_autodiscovery
 * @uses	Config::$logged_in_navigation_links
 * @uses	Config::$meta_description
 * @uses	Config::$meta_keywords
 * @uses	Config::$navigation_links
 * @uses	Config::$root_page
 * @uses	Config::$stylesheet
 * @uses	Config::$wakka_name
 * @uses	Wakka::GetRedirectMessage()
 * @uses	Wakka::GetUser()
 * @uses	Wakka::GetConfigValue()
 * @uses	Wakka::GetWakkaName()
 * @uses	Wakka::PageTitle()
 * @uses	Wakka::GetHandler()
 * @uses	Wakka::Href()
 * @uses	Wakka::htmlspecialchars_ent()
 * @uses	Wakka::Format()
 * @uses	Wakka::StaticHref()
 *
 * @todo Move rss autodiscovery to handlers/show/show.php
 *			JW: ??? Wikka does no autodiscovery
 *			- is this a reference to generating feed link tags? if so, why *not*
 *			generate them in the header?
 *			- config 'enable_rss_autodiscovery' is misnamed: we also generate
 *			RSS links in the body and some programs can "autodiscover" those as
 *			well, not just links in the head section.
 */

/**
 * Defaults
 */
if (!defined('RSS_REVISIONS_VERSION')) define('RSS_REVISIONS_VERSION','2.0');
if (!defined('RSS_RECENTCHANGES_VERSION')) define('RSS_RECENTCHANGES_VERSION','0.92');

$message = $this->GetRedirectMessage();
$site_base = $this->GetConfigValue("base_url");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title><?php printf(GENERIC_DOCTITLE, $this->htmlspecialchars_ent($this->GetWakkaName(), ENT_NOQUOTES), $this->htmlspecialchars_ent($this->PageTitle(), ENT_NOQUOTES)); ?></title>
	<base href="<?php echo $site_base ?>" />
	<?php if ($this->GetHandler() != 'show' || $this->page["latest"] == 'N' || $this->page["tag"] == 'SandBox') echo "<meta name=\"robots\" content=\"noindex, nofollow, noarchive\" />\n"; ?>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta name="keywords" content="<?php echo $this->GetConfigValue("meta_keywords") ?>" />
	<meta name="description" content="<?php echo $this->GetConfigValue("meta_description") ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo $this->StaticHref('css/'.$this->GetConfigValue('stylesheet')); ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo $this->StaticHref('css/'.$this->GetConfigValue('comment_stylesheet')); ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo $this->StaticHref('css/print.css'); ?>" media="print" />
	<link rel="icon" href="<?php echo $this->StaticHref('images/favicon.ico'); ?>" type="image/x-icon" />
	<link rel="shortcut icon" href="<?php echo $this->StaticHref('images/favicon.ico') ?>" type="image/x-icon" />
<?php
$pagetag = $this->htmlspecialchars_ent($this->GetPageTag());
if ($this->GetHandler() != 'edit' && $this->config['enable_rss_autodiscovery'] != 0)
{
	$wikiname = $this->htmlspecialchars_ent($this->GetWakkaName());
	$rsslink  = '	<link rel="alternate" type="application/rss+xml" title="'.sprintf(RSS_REVISIONS_TITLE,$wikiname,$pagetag).' (RSS '.RSS_REVISIONS_VERSION.')" href="'.$this->Href('revisions.xml', $this->GetPageTag()).'" />'."\n";
	$rsslink .= '	<link rel="alternate" type="application/rss+xml" title="'.sprintf(RSS_RECENTCHANGES_TITLE,$wikiname).' (RSS '.RSS_RECENTCHANGES_VERSION.')" href="'.$this->Href('recentchanges.xml', $this->GetPageTag()).'" />'."\n";
	echo $rsslink;
}
if (isset($this->additional_headers) && is_array($this->additional_headers) && count($this->additional_headers))
{
	foreach ($this->additional_headers as $additional_headers)
	{
		echo $additional_headers;
	}
}
?>
</head>
<body <?php echo $message ? "onload=\"alert('".$message."');\" " : "" ?> >
<div class="header">
	<h2><?php echo $this->config["wakka_name"] ?> : <a href="<?php echo $this->href('backlinks', '', ''); ?>" title="<?php printf(WIKKA_BACKLINKS_LINK_TITLE, $pagetag); ?>"><?php echo $pagetag; ?></a></h2>
	<?php
		if ($this->GetUser()) {
			echo $this->config["logged_in_navigation_links"] ? $this->Format($this->config["logged_in_navigation_links"])." :: " : ""; 
			printf(YOU_ARE, $this->FormatUser($this->GetUserName()));
		} else {
			echo $this->config["navigation_links"] ? $this->Format($this->config["navigation_links"]) : ""; 
		}
	?>
</div>
