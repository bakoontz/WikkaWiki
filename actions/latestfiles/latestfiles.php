<?php
/*
 * Latest files action
 * Author: Brian Koontz <brian@pongonova.net>
 *
 * Format a floating block announcing stable and current releases.
 * Very specific action that probably has little use outside of the
 * context of the main wikkawiki.org site.
 *
 * Usage: {{latestfiles show="stable"|"current"|"both" (default: "both")
 *                      stableversion="w.x.y.z"
 *                      stablereleasedate="date"}}
 *     where show="stable" displays only stable releases
 *           show="current" displays only current releases
 *           show="both" displays both stable and current releases
 *           stableversion="w.x.y.z" displays the specified version
 *	           (only valid when show="stable"|"both")
 *           stablereleasedate="date" displays the stable date of release
 *	           (only valid when show="stable"|"both")
 *
 * Update JavaWoman / 2007-11-11:
 * Changed all links to relative ones so they'll remain working with IP address
 * instead of domain name.
 * Update DarTar / 2008-03-20
 * Changed links to point to docs
 */

	$base_url = "http://wikkawiki.org/downloads";
	$showstable = false;
	$showcurrent = false;
	$show = $this->htmlspecialchars_ent($vars['show']);
	if(empty($show) || $show=='both')
	{
		$showstable = true;
		$showcurrent = true;
	}
	else if($show=='stable')
	{
		$showstable = true;
	}
	else if($show=='current')
	{
		$showcurrent = true;
	}
	$stableversion = $this->htmlspecialchars_ent($vars['stableversion']);
	$stablereleasedate = $this->htmlspecialchars_ent($vars['stablereleasedate']);

	$currenttar = basename(readlink('/home/wikkawik/public_html/downloads/wikka-latest-current/Wikka-latest-current.tar.gz'));
	$currenttarmd5 = basename(readlink('/home/wikkawik/public_html/downloads/wikka-latest-current/Wikka-latest-current.tar.gz.md5'));
	$currenttarsha1 = basename(readlink('/home/wikkawik/public_html/downloads/wikka-latest-current/Wikka-latest-current.tar.gz.sha1'));
	$currentzip = basename(readlink('/home/wikkawik/public_html/downloads/wikka-latest-current/Wikka-latest-current.zip'));
	$currentzipmd5 = basename(readlink('/home/wikkawik/public_html/downloads/wikka-latest-current/Wikka-latest-current.zip.md5'));
	$currentzipsha1 = basename(readlink('/home/wikkawik/public_html/downloads/wikka-latest-current/Wikka-latest-current.zip.sha1'));
?>

<div style="float:right; margin: 10px 10px 10px 30px; padding: 4px 8px
2px 8px; width:auto; background: #EEE url('../images/arrow2.gif')
no-repeat top left; border: 1px solid #CCC">
<h4>Download </h4>
<?php if(true===$showstable): ?>
	<p><em>Latest stable release (released <?php echo $stablereleasedate; ?>):</em></p>
	<p>
	<ul style="list-style-type:none; padding-left: 0; marker-offset:
	none;">
	<li><a style="margin:10px 0px; padding: 10px 10px 10px 20px;
	font-size: 1.1em; font-weight:bold; text-decoration: none"
	href="<?php echo($base_url); ?>/Wikka-<?php echo $stableversion; ?>.tar.gz">Wikka-<?php echo $stableversion; ?>.tar.gz</a>&nbsp;[<a
	href="<?php echo($base_url); ?>/Wikka-<?php echo $stableversion; ?>.tar.gz.md5">md5</a>][<a
	href="<?php echo($base_url); ?>/Wikka-<?php echo $stableversion; ?>.tar.gz.sha1">sha1</a>][<a
	href="<?php echo($base_url); ?>/Wikka-<?php echo $stableversion; ?>.tar.gz.asc">sig</a>]</li>
	<li><a style="margin:10px 0px; padding: 10px 10px 10px 20px;
	font-size: 1.1em; font-weight:bold; text-decoration: none"
	href="<?php echo($base_url); ?>/Wikka-<?php echo $stableversion; ?>.zip">Wikka-<?php echo $stableversion; ?>.zip</a>&nbsp;[<a
	href="<?php echo($base_url); ?>/Wikka-<?php echo $stableversion; ?>.zip.md5">md5</a>][<a
	href="<?php echo($base_url); ?>/Wikka-<?php echo $stableversion; ?>.zip.sha1">sha1</a>][<a
	href="<?php echo($base_url); ?>/Wikka-<?php echo $stableversion; ?>.zip.asc">sig</a>]</li>
	</ul></p>
<?php endif ?>
<?php if(true===$showcurrent): ?>
	<p><em>Latest current release (built nightly):</em></p>
	<p>
	<ul style="list-style-type:none; padding-left: 0; marker-offset:
	none;">
	<li><a style="margin:10px 0px; padding: 10px 10px 10px 20px;
	font-size: 1.1em; font-weight:bold; text-decoration: none"
	href="<?php echo($base_url); ?>/wikka-latest-current/<?php echo $currenttar ?>">
	<?php
		echo $currenttar;
	?>
	</a>&nbsp;[<a
	href="<?php echo($base_url); ?>/wikka-latest-current/<?php echo $currenttarmd5 ?>">md5</a>][<a
	href="<?php echo($base_url); ?>/wikka-latest-current/<?php echo $currenttarsha1 ?>">sha1</a>]</li>
	<li><a style="margin:10px 0px; padding: 10px 10px 10px 20px;
	font-size: 1.1em; font-weight:bold; text-decoration: none"
	href="<?php echo($base_url); ?>/wikka-latest-current/<?php echo $currentzip ?>">
	<?php
		echo $currentzip;
	?>
	</a>&nbsp;[<a
	href="<?php echo($base_url); ?>/wikka-latest-current/<?php echo $currentzipmd5 ?>">md5</a>][<a
	href="<?php echo($base_url); ?>/wikka-latest-current/<?php echo $currentzipsha1 ?>">sha1</a>]</li>
	</ul></p>
<?php endif ?>
<p>
<?php if(true===$showstable): ?>
	<a href="http://docs.wikkawiki.org/WhatsNew">what's new?</a> :: <a
	href="http://docs.wikkawiki.org/WikkaReleaseNotes">release notes</a> ::
<?php endif ?>
	<a href="http://docs.wikkawiki.org/WikkaSecurity">verifying files</a>
<?php if(true===$showcurrent): ?>
 :: <a href="WikkaCurrent">about current</a>
<?php endif ?>
</p>
<p style="font-size:90%"><img style="vertical-align:middle"
src="images/icons/24x24/gnome-globe.png" alt="globe" title="Localized
packages" />
<a
href="WikkaLocalization#hn_Localized_packages">User-contributed
localized packages</a><br /><img style="vertical-align:middle"
src="images/icons/24x24/stock_connect.png" alt="plugin"
title="User-contributed plugins" />
<a href="CodeContributions">User-contributed
plugins</a></p>
</div>
