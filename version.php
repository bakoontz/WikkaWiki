<?php
/**
 * Versioning routines
 *
 * See http://wush.net/trac/wikka/ticket/719 for details.
 *
 * @package		Wikka
 * @subpackage	Core
 * @version		$Id: version.php 1481 2009-09-12 03:53:07Z BrianKoontz $
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @see			/docs/Wikka.LICENSE
 * @filesource
 *
 * @author	{@link http://www.wikkawiki.org/BrianKoontz Brian Koontz}
 *
 * @copyright	Copyright 2008, {@link http://wikkawiki.org/CreditsPage Wikka Development Team}
 *
 */

// ---------------------------- VERSIONING ------------------------------------
/**
 * Defines the current Wikka version.
 * Leave null except for release versions!
 */
$svn_version = '';

/**
 * Used to generate WAKKA_VERSION value. Changes here might be
 * modified during SVN checkin.
 */
$svn_revision = '$Rev$';
if(empty($svn_version))
{
	list($t1, $svn_version, $t2) = explode(' ', $svn_revision);
	$svn_version = 'trunk-'.trim($svn_version);
}
if (!defined('WAKKA_VERSION')) define('WAKKA_VERSION', $svn_version);
?>
