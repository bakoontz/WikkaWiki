<?php
/**
 * A database compatibility library
 *
 * Since v1.4.0, WikkaWiki has implemented the PHP Data Objects (PDO) extension
 * due to the deprecation of the mysql_() commands in PHP 7.  PDO is
 * backwards-compatible through PHP 5, and provides a generic interface for
 * accessing relational databases. 
 *
 * This library of methods mainly focuses on incompatibilities that arise between DB
 * server types.  The only methods that should be here are those that depend upon the
 * the value of the "dbms_type" configuration variable.  There should be no instances
 * of conditionals using "dbms_type" in the core code outside of this library.
 *
 * @package		Wikka
 * @subpackage	Libs
 * @version		$Id$
 * @license		http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @see			docs/Wikka.LICENSE
 * @filesource
 *
 * @author		{@link http://wikkawiki.org/BrianKoontz Brian Koontz}
 * @author		{Oscar Munoz}
 * @since		Wikka 1.4.0
 *
 * @copyright	Copyright 2016-2017, Brian Koontz <brian@wikkawiki.org>
 * @copyright	Copyright 2016-2017, Oscar Munoz
 */



/**
 * Get max length of tag field
 *
 * @param	object	Wakka instance
 * @return	int		max length of tag field
 */

function db_getMaxTagLen($obj) {
	// derive maximum length for a page name from the table structure if possible
	if($obj->GetConfigValue('dbms_type') == "sqlite"){
		// For now this is the default on sqlite.
		$maxtaglen = MAX_TAG_LENGTH;
	} elseif ($result = $obj->Query("describe ".$obj->GetConfigValue('table_prefix')."pages tag")) { // mysql default
		$field = ($result->fetchAll())[0];
		if (preg_match("/varchar\((\d+)\)/", $field['Type'], $matches)) {
			$maxtaglen = $matches[1];
		}
	}
	else
	{
		$maxtaglen = MAX_TAG_LENGTH;
	}
	return $maxtaglen;
}

/**
  * Get PDO object 
  *
  * @param	object	Wakka instance
  * @return	PDO		reference to new PDO object or null if failure
  */
 
function db_getPDO($obj) {
	$dblink = null;
	if($obj->GetConfigValue('dbms_type') == "sqlite") {
		$dsn = $obj->GetConfigValue('dbms_type') . ':' .  $obj->GetConfigValue('dbms_database');
		try {
			$dblink = new PDO($dsn);
		} catch(PDOException $e) {
			die('<em class="error">'.T_("PDO connection error!").'</em>');
		}
	} else { //mysql default
		$dsn = $obj->GetConfigValue('dbms_type') . ':' .
			   'host=' . $obj->GetConfigValue('dbms_host') . ';' .
			   'dbname=' . $obj->GetConfigValue('dbms_database');
		$user = $obj->GetConfigValue('dbms_user');
		$pass = $obj->GetConfigValue('dbms_password');
		try {
			$dblink = new PDO($dsn, $user, $pass);
		} catch(PDOException $e) {
			die('<em class="error">'.T_("PDO connection error!").'</em>');
		}
		$dblink->query("SET NAMES 'utf8'");
	}
	return $dblink;
}

/**
  * Add new revision (called from handlers/edit/edit.php)
  *
  * @param	object	Wakka instance
  * @param	array	query parameters
  *	@return none
  */
function db_addNewRevision($obj, $params) {
	if($obj->GetConfigValue('dbms_type') == "sqlite"){
		$obj->Query("
			INSERT INTO ".$obj->GetConfigValue('table_prefix')."pages
			(  tag, title     , time                      , owner, user, note, latest, body ) VALUES
			( :tag,:page_title,datetime('now','localtime'),:owner,:user,:note,'Y'    ,:body )", $params);
	} else { // mysql default
	$obj->Query("
		INSERT INTO ".$obj->GetConfigValue('table_prefix')."pages
		SET tag     = :tag,
		  title = :page_title,
			time    = now(),
			owner   = :owner,
			user    = :user,
			note    = :note,
			latest  = 'Y',
			body    = :body", $params);
	}
}

/**
 * Purge old sessions (called from Wakka.config.php)
 *
 * @param 	object	Wakka instance
 * @return	none
 */
function db_purgeSessions($obj) {
	 // TODO: Set the mount from PERSISTENT_COOKIE_EXPIRY on the sqlite code.
    if($obj->GetConfigValue('dbms_type') == "sqlite"){
		$obj->Query("
			DELETE FROM ".$obj->GetConfigValue('table_prefix')."sessions WHERE DATETIME('now','-3 month') > session_start"
			);
	}else{ // mysql default
		$obj->Query("
			DELETE FROM ".$obj->GetConfigValue('table_prefix')."sessions WHERE DATE_SUB(DATETIME('now','localtime'), INTERVAL ".PERSISTENT_COOKIE_EXPIRY." SECOND) > session_start"
			);
	}
}

/**
  * Save comment (called from Wakka.config.php)
  *
  * @param	object	Wakka instance
  * @param	array	query parameters
  * @return none
  */
function db_saveComment($obj, $params) {
	if($obj->GetConfigValue('dbms_type') == "sqlite"){
		$obj->Query("
			INSERT INTO ".$obj->GetConfigValue('table_prefix')."comments
			(  page_tag, time                       , comment, parent   , user ) VALUES
			( :page_tag, datetime('now','localtime'),:comment,:parent_id,:user )",
			$params);
	} else { // mysql default
	$obj->Query("
		INSERT INTO ".$obj->GetConfigValue('table_prefix')."comments
		SET page_tag = :page_tag,
			time = now(),
			comment = :comment,
			parent = :parent_id,
			user = :user", $params);
	}
}

/**
 * Store/update session (called from wikka.php)
 *
 * @param	object	Wakka instance
 * @param	boolean	Update if true, create new if false	
 * @return	none
 */
function db_storeSession($obj, $update) {
	if(true == $update) {
		// Just update the session_start time
		if ($obj->config['dbms_type'] == 'sqlite'){
			$obj->Query("UPDATE ".$obj->config['table_prefix']."sessions SET session_start=datetime(".$obj->GetMicroTime().", 'unixepoch', 'localtime') WHERE sessionid=:sessionid AND userid=:userid",
					array(':sessionid' => $sessionid, ':userid' => $username));
		}else{
			$obj->Query("UPDATE ".$obj->config['table_prefix']."sessions SET session_start=FROM_UNIXTIME(".$obj->GetMicroTime().") WHERE sessionid=:sessionid AND userid=:userid",
				array(':sessionid' => $sessionid, ':userid' => $username));
		}
	} else { // mysql default
		// Create new session record
		if ($obj->config['dbms_type'] == 'sqlite'){
			$obj->Query("INSERT INTO ".$obj->config['table_prefix']."sessions (sessionid, userid, session_start) VALUES(:sessionid, :userid, datetime(".$obj->GetMicroTime().", 'unixepoch', 'localtime'))",
				array(':sessionid' => $sessionid, ':username' => $username));
		}else{
			$obj->Query("INSERT INTO ".$obj->config['table_prefix']."sessions (sessionid, userid, session_start) VALUES(:sessionid, :userid, FROM_UNIXTIME(".$obj->GetMicroTime()."))",
			array(':sessionid' => $sessionid, ':username' => $username));
		}
	}
}

/**
 * Generate DB-specific config options (called by wikka.php)
 *
 * @param	array	default configs
 * @param	array	existing (current) configs
 * @return	none
 */
function db_configOptions(&$default, &$current) {
/*
	if($current['dbms_type'] == 'sqlite') {
		if(isset($current['dbms_database'])) {
			$current['dbms_file'] = $current['dbms_database'];
			unset($current['dbms_database']);
			unset($default['dbms_database']);
		}
	} else { // mysql default
	}
*/
}
?>
