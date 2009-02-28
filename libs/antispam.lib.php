<?php
/**
 * Anti-spam routines 
 *
 * Various routines to detect, process and log spam content.
 *
 * @name	    antispam.lib.php 
 *
 * @package	    Lib	
 * @version		$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @since		Wikka 1.1.6.6
 * @filesource
 *
 * @author		{@link http://wikkawiki.org/JavaWoman Marjolein Katsma}
 *
 */
/**
 * Create and store a secret session key.
 *
 * Creates a random value and a random field name to be used to pass on the value.
 * The key,value pair is stored in the session as a serialized array.
 *
 * @author		{@link http://wikkawiki.org/JavaWoman JavaWoman}
 * @copyright	Copyright © 2005, Marjolein Katsma
 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @version		0.5
 *
 * @access		public
 *
 * @param		string	$keyname	required: name under which created secret key should be stored in the session
 * @return		array				fieldname and key value.
 */
function createSessionKey($wakka, $keyname)
{
	// create key and field name for it
	$key = md5(getmicrotime());
	$field = 'f'.substr(md5($key.getmicrotime()),0,10);
	// store session key
	$_SESSION[$keyname] = serialize(array($field,$key));
	# BEGIN DEBUG - do not activate on a production server!
	# echo '<div class="debug">'."\n";
	# echo 'Session key:<br/>';
	# echo 'name: '.$keyname.' - field: '.$field.' - key: '.$key.'<br/>';
	# echo '</div>'."\n";
	# END DEBUG
	// return name, value pair
	return array($field,$key);
}
/**
 * Creates a form fieldset with hidden field to pass on the secret session key.
 *
 * The passed key,value pair is used to create the hidden field.
 *
 * @author		{@link http://wikkawiki.org/JavaWoman JavaWoman}
 * @copyright	Copyright © 2005, Marjolein Katsma
 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @version		0.5
 *
 * @access		public
 *
 * @param		array	$aKey	required: fieldname, key value pair.
 * @return		string			fieldset for form with hidden field
 */
function createSessionKeyFieldset($wakka, $aKey)
{
	// get parameters
	list($field,$key) = $aKey;
	// create form fieldset
	$fieldset  = '<fieldset class="hidden">'."\n";
	$fieldset .= '	<input type="hidden" name="'.$field.'" value="'.$key.'" />'."\n";
	$fieldset .= '</fieldset>'."\n";
	// return fieldset
	return $fieldset;
}
/**
 * Retrieve the secret session key.
 *
 * Retrieves a named secret key and returns the result as an array with name,value pair.
 * Returns FALSE if the key is not found.
 *
 * @author		{@link http://wikkawiki.org/JavaWoman JavaWoman}
 * @copyright	Copyright © 2005, Marjolein Katsma
 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @version		0.5
 *
 * @access		public
 *
 * @param		string	$keyname	required: name of secret key to retrieve from the session
 * @return		mixed				array with name,value pair on success, FALSE if entry not found.
 */
function getSessionKey($wakka, $keyname)
{
	if (!isset($_SESSION[$keyname]))
	{
		return FALSE;
	}
	else
	{
		$aKey = unserialize($_SESSION[$keyname]);		# retrieve secret key data
		unset($_SESSION[$keyname]);						# clear secret key
		return $aKey;
	}
}
/**
 * Check hidden session key: it must be passed and it must have the correct name & value.
 *
 * Looks for a given name,value pair passed either in POST (default) or in GET request.
 * Returns TRUE if the correct field and value is found, a reason for failure otherwise.
 * Make sure to check for identity TRUE (TRUE === returnval), do not evaluate return value
 * as boolean!!
 *
 * @author		{@link http://wikkawiki.org/JavaWoman JavaWoman}
 * @copyright	Copyright © 2005, Marjolein Katsma
 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @version		0.5
 *
 * @access		public
 * @todo		- prepare strings for internationalization
 *
 * @param		array	$aKey	required: fieldname, key value pair.
 * @param		string	$method	optional: form method; default post;
 * @return		mixed			TRUE if correct name,value found; reason for failure otherwise.
 */
function hasValidSessionKey($wakka, $aKey,$method='post')
{
	// get pair to look for
	list($ses_field,$ses_key) = $aKey;
	// check method and prepare what to look for
	if (isset($method))
	{
		$aServervars = ($method == 'get') ? $_GET : $_POST;
	}
	else
	{
		$aServervars = $_POST;					# default
	}

	// check passed values
	if (!isset($aServervars[$ses_field]))
	{
		return 'form no key';					# key not present
	}
	elseif ($aServervars[$ses_field] != $ses_key)
	{
		return 'form bad key';					# incorrect value passed
	}
	else
	{
		return TRUE;							# all is well
	}
}
?>