<?php

	/**
	 * Dummy registration module 
	 *
	 * This UR (user registration) verification module always verifies
	 * as true, and can be used for testing or as a template for other
	 * UR modules.
	 *
	 * To implement, set the following in wikka.config.php:
	 *		'UR_validation_modules' => 'URDummyModule'
	 * (If combined with other modules, separate each module with a
	 * comma.)
	 *
	 * @see libs/userregistration.class.php
	 * @see URAuth, URAuthTmpl
	 *
	 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
	 * @filesource
	 *
	 * @author {@link http://wikkawiki.org/BrianKoontz Brian Koontz} 
	 *
	 * @copyright Copyright 2007 {@link http://wikkawiki.org/CreditsPage Wikka Development Team}
	 */

	include_once('libs/userregistration.class.php');

	class URDummyModule extends URAuthTmpl
	{
		function URDummyModule(&$wakka)
		{
			$this->URAuthTmpl($wakka);	
		}

		function URAuthTmplDisplay()
		{
			echo "<em class='error'>Using URDummyModule module!</em>";
		}

		function URAuthTmplVerify()
		{
			return true;
		}
	}
?>
