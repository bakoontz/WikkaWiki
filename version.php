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
 * Used to generate WAKKA_VERSION value. Changes here might be
 * modified during SVN checkin.  Note to end users: Changing this
 * version number might result in problems upgrading...please use
 * caution.
 */
$git_version = '1.4.0';
if (!defined('WAKKA_VERSION')) define('WAKKA_VERSION', $git_version);

/**
 * Defines the current Wikka patch level. This should be 0 by default, 
 * and does not need to be changed for major/minor releases.
 */
if(!defined('WIKKA_PATCH_LEVEL')) define('WIKKA_PATCH_LEVEL', '0');
?>
