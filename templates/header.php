<?php
/**
 * Generates the page header.
 *
 * @package		Templates
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
 * @uses	Wakka::existsUser()
 * @uses	Wakka::GetConfigValue()
 * @uses	Wakka::GetWakkaName()
 * @uses	Wakka::PageTitle()
 * @uses	Wakka::GetHandler()
 * @uses	Wakka::Href()
 * @uses	Wakka::htmlspecialchars_ent()
 * @uses	Wakka::Format()
 * @uses	Wakka::StaticHref()
 *
 * @todo Move header/footer to template
 * @todo Move rss autodiscovery to handlers/show/show.php
 *			JW: ??? Wikka does no autodiscovery
 *			- is this a reference to generating feed link tags? if so, why *not*
 *			generate them in the header?
 *			- config 'enable_rss_autodiscovery' is misnamed: we also generate
 *			RSS links in the body and some programs can "autodiscover" those as
 *			well, not just links in the head section.
 */

/**#@+
 * Default RSS version.
 */
if (!defined('RSS_REVISIONS_VERSION')) define('RSS_REVISIONS_VERSION','2.0');
if (!defined('RSS_RECENTCHANGES_VERSION')) define('RSS_RECENTCHANGES_VERSION','0.92');
/**#@-*/

// get "input" variables
$message = $this->GetRedirectMessage();

// init output variables
$onload = ('' != $message) ? " onload=\"alert('".$message."');\" " : '';
$doctitle = sprintf(GENERIC_DOCTITLE, $this->htmlspecialchars_ent($this->GetWakkaName(), ENT_NOQUOTES), $this->htmlspecialchars_ent($this->PageTitle(), ENT_NOQUOTES));
#$site_base = $this->GetConfigValue('base_url');
#$site_base = $this->base_url;	// base_url: no longer needed with StaticHref()
$href_main_stylesheet = $this->StaticHref('css/'.$this->GetConfigValue('stylesheet'));
$href_comment_stylesheet = $this->StaticHref('css/'.$this->GetConfigValue('comment_stylesheet'));
$href_print_stylesheet = $this->StaticHref('css/print.css');
$href_favicon = $this->StaticHref('images/favicon.ico');
$extra_meta = '';
if ($this->GetHandler() != 'show' || $this->page['latest'] == 'N' || $this->page['tag'] == 'SandBox')
{
	$extra_meta .= '	<meta name="robots" content="noindex, nofollow, noarchive" />'."\n";
}
if ('' != ($meta_keywords = $this->GetConfigValue('meta_keywords')))
{
	$extra_meta .= '	<meta name="keywords" content="'.$meta_keywords.'" />'."\n";
}
if ('' != ($meta_description = $this->GetConfigValue('meta_description')))
{
	$extra_meta .= '	<meta name="description" content="'.$meta_description.'" />'."\n";
}
$pagetag = $this->GetPageTag();
$pagetag_disp = $this->htmlspecialchars_ent($pagetag);
$rss_links = '';
if ($this->GetHandler() != 'edit' && $this->GetConfigValue('enable_rss_autodiscovery') != 0)	// @@@
{
	$wikiname = $this->htmlspecialchars_ent($this->GetWakkaName());	// default ENT_COMPAT: double quotes need to be escaped here: we use title attribute in double quotes!
	$rss_links .= '	<link rel="alternate" type="application/rss+xml" title="'.sprintf(RSS_REVISIONS_TITLE,$wikiname,$pagetag_disp).' (RSS '.RSS_REVISIONS_VERSION.')" href="'.$this->Href('revisions.xml', $pagetag).'" />'."\n";
	$rss_links .= '	<link rel="alternate" type="application/rss+xml" title="'.sprintf(RSS_RECENTCHANGES_TITLE,$wikiname).' (RSS '.RSS_RECENTCHANGES_VERSION.')" href="'.$this->Href('recentchanges.xml', $pagetag).'" />'."\n"; // @@@ pagetag needed for Href??
}
$homepage_url = $this->href('', $this->GetConfigValue('root_page'), '');
$backlinks_url = $this->href('backlinks', '', '');
$backlinks_title = sprintf(WIKKA_BACKLINKS_LINK_TITLE, $pagetag_disp);
$header_heading = '<a id="homepage_link" href="'.$homepage_url.'">'.$this->GetConfigValue('wakka_name').'</a> : '.'<a href="'.$backlinks_url.'" title="'.$backlinks_title.'">'.$pagetag_disp.'</a>';
$header_navlinks = '';
#if ($user = $this->GetUser())
if ($this->existsUser())
{
	$header_navlinks .= $this->GetConfigValue('logged_in_navigation_links') ? $this->Format($this->GetConfigValue('logged_in_navigation_links')).' :: ' : '';
	#$header_navlinks .= sprintf(YOU_ARE, $this->FormatUser($user['name']));
	$header_navlinks .= sprintf(YOU_ARE, $this->FormatUser($this->reg_username));
}
else
{
	$header_navlinks .= $this->GetConfigValue('navigation_links') ? $this->Format($this->GetConfigValue('navigation_links')) : '';
	// optionally display user's IP address here
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo $doctitle; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php echo $extra_meta; ?>
	<link rel="stylesheet" type="text/css" href="<?php echo $href_main_stylesheet; ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo $href_comment_stylesheet; ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo $href_print_stylesheet; ?>" media="print" />
	<link rel="icon" href="<?php echo $href_favicon; ?>" type="image/x-icon" />
	<link rel="shortcut icon" href="<?php echo $href_favicon; ?>" type="image/x-icon" />
<?php echo $rss_links; ?>
<?php
if (isset($this->additional_headers) && is_array($this->additional_headers) && count($this->additional_headers) > 0)
{
	foreach ($this->additional_headers as $additional_header)
	{
		echo $additional_header;
	}
}
?>
</head>
<body<?php echo $onload; ?>>
<div class="header">
	<h2><?php echo $header_heading; ?></h2>
	<?php echo $header_navlinks; ?>
</div>
