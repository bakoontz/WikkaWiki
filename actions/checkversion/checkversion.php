<?php
/**
 * Compare this instance version to the latest version and display
 * notice if out of date. 
 *
 * Restricted to admins. Expects to find
 * wikkawiki.org/downloads/latest_wikka_version.txt; if not found, exits
 * gracefully. Disable by setting config param 'enable_version_check' to
 * 0.
 *
 * The time interval between checks can be set using the 
 * config param 'version_check_interval'.
 * Valid time interval units for 'version_check_interval':
 * h=hours
 * m=minutes
 * s=seconds
 * d=days
 * w=weeks (7 days)
 * M=months (30 days)
 * y=years (365 days)
 *
 *
 * Syntax: {{checkversion}}
 *
 * @package		Actions
 * @version		$Id:mychanges.php 369 2007-03-01 14:38:59Z DarTar $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * @author		{@link http://wikkawiki.org/BrianKoontz Brian Koontz}
 * @author		{@link http://wikkawiki.org/DarTar Dario Taraborelli}
 *
 * @todo	use core method to generate notes and badges
 * @todo	use error handler for debugging
 */

if(!defined('CHECKVERSION_HOST')) define('CHECKVERSION_HOST', 'wikkawiki.org');
if(!defined('CHECKVERSION_RELEASE_FILE')) define('CHECKVERSION_RELEASE_FILE', '/downloads/latest_wikka_version.txt');
if(!defined('CHECKVERSION_DOWNLOAD_URL')) define('CHECKVERSION_DOWNLOAD_URL', 'http://docs.wikkawiki.org/WhatsNew');
if(!defined('CHECKVERSION_CONNECTION_TIMEOUT')) define('CHECKVERSION_CONNECTION_TIMEOUT', 5);

if($this->IsAdmin() && TRUE == $this->GetConfigValue('enable_version_check'))
{
	
	$debug = FALSE;
	if(isset($vars['debug'])) $debug = TRUE;
		
	$do_version_check = TRUE;
	
	# Has version_check_interval expired?
	if(!isset($_SESSION['last_version_check']))
	{
		$_SESSION['last_version_check'] = time();
	}
	# default intervall if config param not set in wikka.config.php: 1h 
	elseif(isset($this->config['version_check_interval']))
	{
		$interval = $this->GetConfigValue('version_check_interval');
		
		if(preg_match("/^(\d+)([hmsdwMy])$/", $interval, $matches) > 0)
		{
			$scalar = $matches[1];
			$unit = $matches[2];
			switch($unit)
			{
				case "h": $scalar *= 3600; break;
				case "m": $scalar *= 60; break;
				case "s": break;
				case "d": $scalar *= 24 * 3600; break;
				case "w": $scalar *= 7 * 24 * 3600; break;
				case "M": $scalar *= 30 * 24 * 3600; break;
				case "y": $scalar *= 365 * 24 * 3600; break;
				default: $scalar = 0;
			}
			$elapsed_time = time() - $_SESSION['last_version_check']; 
			if($debug)
			{
				echo '<div class="debug">'.sprintf(T_("[elapsed time: %d]"),$elapsed_time).'</div>'."\n";
			}
			if($elapsed_time > $scalar)
			{
				$_SESSION['last_version_check'] = time();
			}
			else
			{
				$do_version_check = FALSE;
			}
		}
	}

	// Attempt to get latest_wikka_version.txt
	if($do_version_check)
	{
		$latest = '';
		//color scheme array (ported from {{since}})
		$c = array(
				'A' => array('#699', '#BFFFFF', '#303030', '#A0E0E0', '#90B0B0'),
				'B' => array('#996', '#FFFFBF', '#303030', '#E0E0A0', '#B0B090'),
				'C' => array('#969', '#FFBFFF', '#303030', '#E0A0E0', '#B090B0'),
				'D' => array('#966', '#FFBFBF', '#303030', '#E0A0A0', '#B09090'),
				'E' => array('#669', '#BFBFFF', '#303030', '#A0A0E0', '#9090B0'),
				'F' => array('#696', '#BFFFBF', '#303030', '#A0E0A0', '#90B090')
		);
		
		// The action won't work on Windows PHP <4.3.0
		$timeout = CHECKVERSION_CONNECTION_TIMEOUT;
		if(FALSE !== strpos(strtolower(PHP_OS), 'windows') &&
			TRUE === version_compare(PHP_VERSION, '4.3.0', '<'))
		{
			if($debug)
			{
				echo '<div class="debug">'.sprintf(T_("[%s PHP %s does not support this feature]"),PHP_OS,PHP_VERSION).'</div>'."\n";
			}
			return;
		}
		
		if(FALSE == ini_get('allow_url_fopen'))
		{
			if($debug)
			{
				echo '<div class="debug">'.T_("[allow_url_fopen disabled]").'</div>'."\n";
			}
			return;
		}

		$hostname = CHECKVERSION_HOST;
		$ip = gethostbyname($hostname);
		if($ip == $hostname)
		{
			// Probably no internet connection...
			if ($debug)
			{
				echo '<div class="debug">',sprintf(T_("[Cannot resolve %s]"),$hostname).'</div>'."\n";
			}
			return;
		}

		$fp = @fsockopen($ip, 80, $errno, $errstr, $timeout);
		if(!$fp)
		{
			if ($debug)
			{
				echo '<div class="debug">'.T_("[Cannot initiate socket connection]").'</div>'."\n";
			}
			else
			{
				// Display warning message
				$s = "D";
				echo sprintf(
					T_('<div title="Cannot initiate network connection" style="clear: both; text-align: center; float: left; width: 300px; border: 1px solid %s; background-color: %s; color: %s; margin: 10px 0">'."\n"
    .'<div style="padding: 0 3px 0 3px; background-color: %s; font-size: 85%%; font-weight: bold">CHECKVERSION FAILED</div>'."\n"
    .'<div style="padding: 0 3px 2px 3px; font-size: 85%%; line-height: 150%%; border-top: 1px solid %s;">'."\n"
    .'The network connection with the WikkaWiki server could not be established. To prevent delays in loading this page, please set enable_version_check to 0 in your wikka.config.php file.'."\n"
    .'</div>'."\n"
    .'</div>'."\n"
    .'<div class="clear"></div>'."\n"),
					$c[$s][0], $c[$s][1], $c[$s][2], $c[$s][3], $c[$s][4]);
			}
			return;
		}

		fwrite($fp, "GET " . CHECKVERSION_RELEASE_FILE . " HTTP/1.0\r\n");
		fwrite($fp, "Host: " . CHECKVERSION_HOST . "\r\n");
		fwrite($fp, "Connection: Close\r\n\r\n");
		stream_set_timeout($fp, $timeout);
		$data = fread($fp, 4096);
		$tmp = explode("\r\n", $data);
		$latest = trim(array_pop($tmp));
		fclose($fp);
		
		if(TRUE === version_compare($this->GetConfigValue('wakka_version'), $latest, "<"))
		{
			if($debug)
			{
				echo '<div class="debug">'.sprintf(T_("[%s from host %s]"),$latest,$ip).'</div>'."\n";	
			}
			if(isset($vars['display']) && "raw" == $vars['display'])
			{
				echo $latest;
			}
			else
			{
				$s = 'F'; //green badge
				echo sprintf(
	'<div title='.T_("A new version of WikkaWiki is available. Please upgrade!").' style="clear: both; text-align: center; float: left; width: 300px; border: 1px solid %s; background-color: %s; color: %s; margin: 10px 0">'."\n"
		.'<div style="padding: 0 3px 0 3px; background-color: %s; font-size: 85%%; font-weight: bold">'.T_("UPGRADE NOTE").'</div>'."\n"
		.'<div style="padding: 0 3px 2px 3px; font-size: 85%%; line-height: 150%%; border-top: 1px solid %s;">'."\n"
		.T_('<strong>WikkaWiki %s</strong> is available for <a href="%s">download</a>!')."\n"
		.'</div>'."\n"
		.'</div>'."\n"
		.'<div class="clear"></div>'."\n"
				, $c[$s][0], $c[$s][1], $c[$s][2], $c[$s][3], $c[$s][4], $latest, CHECKVERSION_DOWNLOAD_URL);
			}
		}
	}			
}
?>
