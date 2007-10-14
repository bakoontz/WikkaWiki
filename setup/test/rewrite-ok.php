<?php
/**
 * This file is part of Wikka, a PHP wiki engine.
 *
 * It is processed when http://.../setup/test/test-mod-rewrite.php
 * is called <b>and</b> if mod_rewrite is available.
 * Later, the presence of $_SESSION['mod_rewrite'] is sufficient
 * to test the availability of mod_rewrite.
 *
 * @package		Setup
 * @version		$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author	{@link http://wikkawiki.org/DotMG Mahefa Randimbisoa}
 * @todo add @uses {@link setup/test/test-mod-rewrite.php}
 *
 * @copyright	Copyright 2007, {@link http://wikkawiki.org/CreditsPage Wikka Development Team}
 */

	@session_start();
	$_SESSION['mod_rewrite'] = 'ok';
	include('test-mod-rewrite.php');
	#die(); // session_write_close() and die() are in test-mod-rewrite.php
?>
