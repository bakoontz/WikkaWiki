<?php
/**
 * Displays definition data (DDL) for the database and tables Wikka uses.
 *
 * Features:
 *	By default shows creation DDL for the database Wikka uses, and a drill-down form to show
 *	creation DDL for each of the wikka tables.
 *	By specifying all='1' possibly more databases become visible (depending on the permissions of the Wikka database user);
 *	if multiple databases are visible, a selection form is shown to pick a database.
 *	By specifying prefix='0' the prefix configured for Wikka is ignored, allowing other tables in the same database (if any)
 *	to be inspected.
 *
 * NOTE: These calls are most likely MySQL-specific.  This action
 * needs some work to make it db-agnostic.
 *
 * Syntax:
 *	{{dbinfo [all="0|1"] [prefix="0|1"]}}
 *
 * @package		Actions
 * @version $Id$
 * @filesource
 *
 * @author		{@link http://wikkawiki.org/JavaWoman JavaWoman}
 * @copyright	Copyright � 2005, Marjolein Katsma
 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @since		Wikka 1.1.6.4
 *
 * @input		string	$all		optional: 0|1; default: 0
 *									- 0: show only the database Wikka's tables are in (if visible)
 *									- 1: show all (visible) databases
 * @input		integer	$prefix		optional: 0|1; default: 1
 *									- 0: all tables regardless of prefix
 *									- 1: show only tables with Wikka-configured name prefix
 *
 * @output		string	drill-down forms to show databases, tables and creation DDL for them
 *
 * @uses	IsAdmin()
 * @uses	FormOpen()
 * @uses	FormClose()
 * @uses	Format()
 * @uses	makeId()
 *
 * @todo 	Prevent multiple calls, #634
 */

//TODO the following check should be performed by the Action() method, see #634
// escape & placeholder: action allowed only once per page
if (defined('DBINFO_INSTANTIATED'))
{
	echo '{{dbinfo}}';
	return;
}

// ----------------- constants and variables ------------------

// constants

//TODO the following check should be performed by the Action() method, see #634
define('DBINFO_INSTANTIATED', TRUE);

// set defaults
$bAll		= FALSE;		# one column for columnar layout
$bPrefix	= TRUE;			# default display type

// UI strings
$hdDbInfo		= T_("Database Information");
$hdDatabase		= T_("Database");
$hdTables		= T_("Tables");
$txtActionInfo	= T_("This utility provides some information about the database(s). Depending on permissions for the Wikka database user, not all databases or tables may be visible. Where creation DDL is given, this reflects everything that would be needed to exactly recreate the same database and table definitions, including defaults that may not have been specified explicitly.");
$msgOnlyAdmin	= T_("Sorry, only administrators can view database information.");

if($this->GetConfigValue('dbms_type') == "sqlite"){

	$isAdmin	= $this->IsAdmin();
	$prefix		= $this->GetConfigValue('table_prefix');

	if ($isAdmin)
	{
		print("sqlite3 database: <br>");
		print("Path: ".$this->GetConfigValue('dbms_database'));
	}

} else { //mysql default
// variables

$isAdmin	= $this->IsAdmin();
$database	= $this->GetConfigValue('dbms_database');
$prefix		= $this->GetConfigValue('table_prefix');

// ---------------------- processsing --------------------------

// --------------- get parameters ----------------

if ($isAdmin)
{
	if (is_array($vars))
	{
		foreach ($vars as $param => $value)
		{
			$value = $this->htmlspecialchars_ent($value);
			switch ($param)
			{
				case 'all':
					if ($value == 1) $bAll = TRUE;
					break;
				case 'prefix':
					if ($value == 0) $bPrefix = FALSE;
					break;
			}
		}
	}
}

// ------------------ get data -------------------

if ($isAdmin)
{
	// list of databases to choose from
	$aDbList = array();
	if ($bAll)
	{
		$query = 'SHOW DATABASES';
		$tableresult = $this->Query($query)->fetchAll();
		if ($tableresult)
		{
			foreach ($tableresult as $row)
			{
				$aDbList[] = $row['Database'];
			}
		}
		else											# catch-all if no databases are / can be shown
		{
			$aDbList[] = $database;
		}
	}
	else
	{
		$aDbList[] = $database;
	}

	// data for selected database
	if ($bAll)
	{
		if (isset($_POST['dbselect']) || isset($_POST['tableselect']))				# form submitted
		{
			if (isset($_POST['seldb']) && in_array($_POST['seldb'],$aDbList))		# valid choice
			{
				$seldb = $this->GetSafeVar('seldb', 'post');
			}
			else										# ignore invalid choice
			{
				$seldb = $database;
			}
		}
	}
	else
	{
		$seldb = $database;								# no choice: wikka database
	}

	if (isset($seldb))
	{
		$query = 'SHOW CREATE DATABASE '.$this->pdo_quote_identifier($seldb);
		$dbcreateresult =
			$this->Query($query);
		if ($dbcreateresult)
		{
			$dbcreate = ($dbcreateresult->fetch())['Create Database'];
			$dbcreateresult->closeCursor();
		}
	}

	// table list
	$aTableList = array();
	if (isset($seldb))
	{
		$query = 'SHOW TABLES FROM '.$this->pdo_quote_identifier($seldb);
		if ($bPrefix)
		{
			$pattern = $prefix.'%';
			$query .= " LIKE '".$pattern."'";
		}
		$tablelistresult = $this->Query($query)->fetchAll();
		if ($tablelistresult)
		{
			$colname = 'Tables_in_'.$seldb;
			if ($bPrefix)
			{
				$colname .= ' ('.$pattern.')';
			}
			foreach($tablelistresult as $row)
			{
				$aTableList[] = $row[$colname];
			}
		}
	}

	// data for selected table
	if (isset($_POST['tableselect']))					# form submitted
	{
		if (isset($_POST['seltable']) && in_array($_POST['seltable'],$aTableList))	# valid choice
		{
			$seltable = $this->GetSafeVar('seltable', 'post');
			$seltable = $this->pdo_quote_identifier($seltable);
			$query = 'SHOW CREATE TABLE '.$seltable;
			$tablecreateresult =
				$this->Query($query);
			if ($tablecreateresult)
			{
				$row = $tablecreateresult->fetch();
				$tablecreate = $row['Create Table'];
				$tablecreateresult->closeCursor();
			}
		}
	}
}

// ---------------- build forms ------------------

if ($isAdmin)
{
	// build datatabase selection form if more than one database to show
	if (count($aDbList) > 1)
	{
		$dbselform  = $this->FormOpen('','','POST','dbsel');
		$dbselform .= '<fieldset>'."\n";
		$dbselform .= '	<legend>'.T_("Databases").'</legend>'."\n";
		$dbselform .= '	<label for="seldb" class="mainlabel">'.T_("Select a database:").'</label> '."\n";
		$dbselform .= '	<select name="seldb" id="seldb">'."\n";
		foreach ($aDbList as $db)
		{
			if (isset($seldb))
			{
				$dbselform .= '		<option value="'.$db.'"'.(($seldb == $db)? ' selected="selected"' : '').'>'.$db.'</option>'."\n";
			}
			else
			{
				$dbselform .= '		<option value="'.$db.'">'.$db.'</option>'."\n";
			}
		}
		$dbselform .= '	</select>'."\n";
		$dbselform .= '	<input type="submit" name="dbselect" value="'.T_("Select").'" />'."\n";
		$dbselform .= '</fieldset>'."\n";
		$dbselform .= $this->FormClose();
	}
	else
	{
		$dbselmsg = '<p>'.sprintf(T_("Information for the <tt>%s</tt> database."),$aDbList[0]).'</p>'."\n";
	}

	// build table selection form
	if (isset($seldb))
	{
		if (count($aTableList) > 0)
		{
			$tableselform  = $this->FormOpen('','','POST','tablesel');
			$tableselform .= '<fieldset class="hidden">'."\n";
			$tableselform .= '	<input type="hidden" name="seldb" value="'.$seldb.'" />'."\n";
			$tableselform .= '</fieldset>'."\n";
			$tableselform .= '<fieldset>'."\n";
			$tableselform .= '	<legend>'.T_("Tables").'</legend>'."\n";
			$tableselform .= '	<label for="seltable" class="mainlabel">'.T_("Select a table:").'</label> '."\n";
			$tableselform .= '	<select name="seltable" id="seltable">'."\n";
			foreach ($aTableList as $table)
			{
				if (isset($seltable))
				{
					$tableselform .= '		<option value="'.$table.'"'.(($seltable == $table)? ' selected="selected"' : '').'>'.$table.'</option>'."\n";
				}
				else
				{
					$tableselform .= '		<option value="'.$table.'">'.$table.'</option>'."\n";
				}
			}
			$tableselform .= '	</select>'."\n";
			$tableselform .= '	<input type="submit" name="tableselect" value="'.T_("Select").'" />'."\n";
			$tableselform .= '</fieldset>'."\n";
			$tableselform .= $this->FormClose();
		}
		else
		{
			$tableselmsg = '<p>'.sprintf(T_("No tables found in the <tt>%s</tt> database. Your MySQL user may not have sufficient privileges to access this database."),$seldb)."</p>\n";
		}
	}

	// build results
	if (isset($seldb))
	{
		$hdDbDdl = sprintf(T_("DDL to create database %s:"),$seldb);
		if (isset($dbcreate))
		{
			$dbresult = $this->Format('%%(sql)'.$dbcreate.'%%');
		}
		else
		{
			$dbresult = '<p>'.sprintf(T_("Creation DDL for <tt>%s</tt> could not be retrieved."),$seldb).'</p>'."\n";
		}
		if (isset($seltable))
		{
			$hdTableDdl = sprintf(T_("DDL to create table %s:"),$seltable);
			if (isset($tablecreate))
			{
				$tableresult = $this->Format('%%(sql)'.$tablecreate.'%%');
			}
			else
			{
				$tableresult = '<p>'.sprintf(T_("Creation DDL for <tt>%s</tt> could not be retrieved."),$seltable).'</p>'."\n";
			}
		}
	}
	// ids - use constant for variable-content heading
	$idDbInfo	= $this->makeId('div','dbinfo');
	$idDbDdl	= $this->makeId('hn','ddl_for_database');
	$idTableDdl	= $this->makeId('hn','ddl_for_table');
}


// ------------ show data and forms --------------

echo '<h3>'.$hdDbInfo.'</h3>'."\n";
if ($isAdmin)
{
	echo '<div id="'.$idDbInfo.'">'."\n";
	echo '<p>'.$txtActionInfo.'</p>'."\n";
	echo '<h4>'.$hdDatabase.'</h4>'."\n";
	if (isset($dbselform))
	{
		echo $dbselform;
	}
	elseif (isset($dbselmsg))
	{
		echo $dbselmsg;
	}
	if (isset($seldb))
	{
		echo '<br />'."\n";
		echo '<h5 id="'.$idDbDdl.'">'.$hdDbDdl.'</h5>'."\n";
		echo $dbresult;

		echo '<br />'."\n";
		echo '<h4>'.$hdTables.'</h4>'."\n";
		if (isset($tableselform))
		{
			echo $tableselform;
		}
		elseif (isset($tableselmsg))
		{
			echo $tableselmsg;
		}
		if (isset($seltable))
		{
			echo '<br />'."\n";
			echo '<h5 id="'.$idTableDdl.'">'.$hdTableDdl.'</h5>'."\n";
			echo $tableresult;
		}
	}
	echo '</div>'."\n";
}
else
{
	echo '<p class="error">'.$msgOnlyAdmin.'</p>'."\n";
}
}
?>
