<?php
/*
 * Latest files action
 * Author: Brian Koontz <brian@pongonova.net>
 *
 * Format a floating block announcing stable and unstable releases.
 * Very specific action that probably has little use outside of the
 * context of the main wikkawiki.org site.
 *
 * Usage: {{latestfiles show="stable"|"unstable"|"both" (default: "both")
 *                      stableversion="w.x.y.z"
 *                      stablereleasedate="date"}}
 *     where show="stable" displays only stable releases
 *           show="unstable" displays only unstable releases
 *           show="both" displays both stable and unstable releases
 *           stableversion="w.x.y.z" displays the specified version
 *	           (only valid when show="stable"|"both")
 *           stablereleasedate="date" displays the stable date of release
 *	           (only valid when show="stable"|"both")
 */

	$showstable = false;
	$showunstable = false;
	$show = $this->htmlspecialchars_ent($vars['show']);
	if(empty($show) || $show=='both')
	{
		$showstable = true;
		$showunstable = true;
	}
	else if($show=='stable')
	{
		$showstable = true;
	}
	else if($show=='unstable')
	{
		$showunstable = true;
	}
	$stableversion = $this->htmlspecialchars_ent($vars['stableversion']);
	$stablereleasedate = $this->htmlspecialchars_ent($vars['stablereleasedate']);

	$unstabletar = basename(readlink('/home/wikkawik/public_html/downloads/wikka-latest-unstable/Wikka-latest-unstable.tar.gz'));
	$unstabletarmd5 = basename(readlink('/home/wikkawik/public_html/downloads/wikka-latest-unstable/Wikka-latest-unstable.tar.gz.md5'));
	$unstabletarsha1 = basename(readlink('/home/wikkawik/public_html/downloads/wikka-latest-unstable/Wikka-latest-unstable.tar.gz.sha1'));
	$unstablezip = basename(readlink('/home/wikkawik/public_html/downloads/wikka-latest-unstable/Wikka-latest-unstable.zip'));
	$unstablezipmd5 = basename(readlink('/home/wikkawik/public_html/downloads/wikka-latest-unstable/Wikka-latest-unstable.zip.md5'));
	$unstablezipsha1 = basename(readlink('/home/wikkawik/public_html/downloads/wikka-latest-unstable/Wikka-latest-unstable.zip.sha1'));
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
	href="http://wikkawiki.org/downloads/Wikka-<?php echo $stableversion; ?>.tar.gz">Wikka-<?php echo $stableversion; ?>.tar.gz</a>&nbsp;[<a
	href="http://wikkawiki.org/downloads/Wikka-<?php echo $stableversion; ?>.tar.gz.md5">md5</a>][<a
	href="http://wikkawiki.org/downloads/Wikka-<?php echo $stableversion; ?>.tar.gz.sha1">sha1</a>][<a
	href="http://wikkawiki.org/downloads/Wikka-<?php echo $stableversion; ?>.tar.gz.asc">sig</a>]</li>
	<li><a style="margin:10px 0px; padding: 10px 10px 10px 20px;
	font-size: 1.1em; font-weight:bold; text-decoration: none"
	href="http://wikkawiki.org/downloads/Wikka-<?php echo $stableversion; ?>.zip">Wikka-<?php echo $stableversion; ?>.zip</a>&nbsp;[<a
	href="http://wikkawiki.org/downloads/Wikka-<?php echo $stableversion; ?>.zip.md5">md5</a>][<a
	href="http://wikkawiki.org/downloads/Wikka-<?php echo $stableversion; ?>.zip.sha1">sha1</a>][<a
	href="http://wikkawiki.org/downloads/Wikka-<?php echo $stableversion; ?>.zip.asc">sig</a>]</li>
	</ul></p>
<?php endif ?>
<?php if(true===$showunstable): ?>
	<p><em>Latest unstable release (built nightly):</em></p>
	<p>
	<ul style="list-style-type:none; padding-left: 0; marker-offset:
	none;">
	<li><a style="margin:10px 0px; padding: 10px 10px 10px 20px;
	font-size: 1.1em; font-weight:bold; text-decoration: none"
	href="http://wikkawiki.org/downloads/wikka-latest-unstable/<?php echo $unstabletar ?>">
	<?php
		echo $unstabletar;
	?>
	</a>&nbsp;[<a
	href="http://wikkawiki.org/downloads/wikka-latest-unstable/<?php echo $unstabletarmd5 ?>">md5</a>][<a
	href="http://wikkawiki.org/downloads/wikka-latest-unstable/<?php echo $unstabletarsha1 ?>">sha1</a>]</li>
	<li><a style="margin:10px 0px; padding: 10px 10px 10px 20px;
	font-size: 1.1em; font-weight:bold; text-decoration: none"
	href="http://wikkawiki.org/downloads/wikka-latest-unstable/<?php echo $unstablezip ?>">
	<?php
		echo $unstablezip;
	?>
	</a>&nbsp;[<a
	href="http://wikkawiki.org/downloads/wikka-latest-unstable/<?php echo $unstablezipmd5 ?>">md5</a>][<a
	href="http://wikkawiki.org/downloads/wikka-latest-unstable/<?php echo $unstablezipsha1 ?>">sha1</a>]</li>
	</ul></p>
<?php endif ?>
<p>
<?php if(true===$showstable): ?>
	<a href="WhatsNew">what's new?</a> :: <a
	href="WikkaReleaseNotes">release notes</a> ::
<?php endif ?>
	<a href="WikkaSecurity">verifying files</a>
<?php if(true===$showunstable): ?>
 :: <a href="http://wikkawiki.org/WikkaUnstable">about unstable</a>
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
