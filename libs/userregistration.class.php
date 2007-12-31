<?php

/**
 * Core support classes for User Registration validation modules
 *
 * Contains two classes: 
 *   - URAuth: All access to validation modules must be accomplished
 *   via these hooks. Module implementors should not modify or change
 *   these methods.
 *   - URAuthTmpl: Validation modules must extend this class and
 *   implement the appropriate URAuthTmpl<method>() calls. These
 *   methods are not intended to be used as public access methods;
 *   access to this class (and subclasses) is accomplished via the
 *   hooks in URAuth.
 *
 * @package Wikka
 * @subpackage Libs
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 *
 * @author {@link http://wikkawiki.org/BrianKoontz Brian Koontz} 
 *
 * @copyright Copyright 2007 {@link http://wikkawiki.org/CreditsPage Wikka Development Team}
 */

/**
 * Public access methods for all UR (user registration) validation
 * modules.
 *
 * All access to validation modules must be accomplished via "hooks"
 * provided in this class. Module implementors should not modify or
 * change these methods.
 *
 * @name URAuth 
 * @package Wikka
 * @subpackage Libs
 */
class URAuth
{
	var $classlist;
	var $wakka;
	var $serial;

	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	object	$wakka	Provides access to the main Wakka object
	 */
	function URAuth($wakka)
	{
		$this->wakka = $wakka;
		$this->classlist = array();
		if(isset($wakka->config['UR_validation_modules']))
		{
			$modules = explode(",", $wakka->config['UR_validation_modules']);
			foreach($modules as $module)
			{
				$module = trim($module);
				include_once("UR/$module/$module.php");
				if(class_exists($module))
				{
					eval("\$this->URAuthRegister(new ".$module."(\$this->wakka));");
				}
			}
		}
	}

	/**
     * Register a UR (user registration) validation module
	 *
	 * @access	public
	 * @param	object	Instance of URAuthTmpl
	 * @return	TRUE if registration successful; FALSE otherwise
	 * @todo	Duplicate registration check (classes and objects)
	 */
	function URAuthRegister(&$tmplObj)
	{
		array_push($this->classlist, $tmplObj);
		return TRUE;
	}

	/**
     * Deregister a UR (user registration) validation module
	 *
	 * @access	public
	 * @todo	Not yet implemented
	 */
	function URAuthDeregister(&$tmplObj)
	{
	}

	/**
	 * Display HTML elements for all registered UR (user registration)
	 * validation modules.
	 *
	 * This public interface method is used to generate form elements
	 * for each registered UR validation module, and is intended to be
	 * called within the context of the UserRegistration action form
	 * (which itself is called within the context of an output buffer).
	 * Elements in validation modules should be written to the output
	 * buffer using echo. 
	 *
	 * @access	public
	 * @param	none
	 * @return	TRUE
	 */
	function URAuthDisplay()
	{
		foreach($this->classlist as $obj)
		{
			$obj->URAuthTmplDisplay();
		}
		return true;
	}

	/**
	 * Calls each UR (user registration) validation module's
	 * verification method.
	 *
	 * This public interface method is used to verify whether or not
	 * each registered validation module has performed a successful
	 * verification. Currently, all module results are ANDed together
	 * (i.e., all modules must result in successful verification for
	 * this method to return as successful).
	 *
	 * @access	public
	 * @param	none
	 * @return	TRUE if successful; FALSE otherwise
	 */
	function URAuthVerify()
	{
		$result = true;
		foreach($this->classlist as $obj)
		{
			$result = $result && (bool)$obj->URAuthTmplVerify();
		}
		return $result; 
	}

	/**
	 * Internal method used to compare validation modules for duplication check.
	 *
	 * @access	private
	 * @param	object	Objects of type URAuthTmpl
	 * @return	TRUE is objects are the same; FALSE otherwise
	 */
	function _areObjsSame($obj1, $obj2)
	{
		if(!is_a($obj1, "URAuthTmpl") || !is_a($obj2, "URAuthTmpl"))
		{
			return NULL;
		}
		if($obj1->URTmplGetSerial() == $obj2->URTmplGetSerial())
		{
			return TRUE;
		}
		return FALSE;
	}
}

/**
 * Parent class for all UR (user registration) validation modules.
 *
 * All UR validation modules must be subclassed from this class.  The
 * subclass must override each URAuthTmpl<method> method with
 * validation-specific code.
 *
 * @name URAuthTmpl 
 * @package Wikka
 * @subpackage Libs
 */
class URAuthTmpl
{
	var $serial;
	var $wakka;

	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	object	$wakka	Provides access to the main Wakka object
	 */
	function URAuthTmpl(&$wakka)
	{
		$this->serial = time();
		$this->wakka = $wakka;
	}

	/**
     * Display validation elements
	 *
	 * @access	public
	 * @param	none
	 * @return	none
	 */
	function URAuthTmplDisplay()
	{
	}

	function URAuthTmplVerify()
	{
		return true;
	}

	/**
     * Return this object's unique id
	 *
	 * @access	public
	 * @param	none
	 * @return	int		id
	 */
	function URAuthTmplGetSerial()
	{
		return $this->serial;
	}
}

?>
