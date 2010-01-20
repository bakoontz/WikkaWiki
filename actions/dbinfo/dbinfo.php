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
 * Syntax:
 *	{{dbinfo [all="0|1"] [prefix="0|1"]}}
 *
 * @package		Actions
 * @version $Id$
 * @filesource
 *
 * @author		{@link http://wikkawiki.org/JavaWoman JavaWoman}
 * @copyright	Copyright © 2005, Marjolein Katsma
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
$hdDbInfo		= HD_DBINFO;
$hdDatabase		= HD_DBINFO_DB;
$hdTables		= HD_DBINFO_TABLES;
$txtActionInfo	= TXT_INFO_1.TXT_INFO_2.TXT_INFO_3.TXT_INFO_4;
$msgOnlyAdmin	= MSG_ONLY_ADMIN;

// variables

$isAdmin	= $this->IsAdmin();
$database	= $this->config['mysql_database'];
$prefix		= $this->config['table_prefix'];

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
		$tableresult = mysql_query($query);
		if ($tableresult)
		{
			while ($row = mysql_fetch_assoc($tableresult))
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
		$query = 'SHOW CREATE DATABASE '.$seldb;
		$dbcreateresult = mysql_query($query);
		if ($dbcreateresult)
		{
			$row = mysql_fetch_assoc($dbcreateresult);
			$dbcreate = $row['Create Database'];
		}
	}

	// table list
	$aTableList = array();
	if (isset($seldb))
	{
		$query = 'SHOW TABLES FROM '.$seldb;
		if ($bPrefix)
		{
			$pattern = $prefix.'%';
			$query .= " LIKE '".$pattern."'";
		}
		$tablelistresult = mysql_query($query);
		if ($tablelistresult)
		{
			$colname = 'Tables_in_'.$seldb;
			if ($bPrefix)
			{
				$colname .= ' ('.$pattern.')';
			}
			while ($row = mysql_fetch_assoc($tablelistresult))
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
			$query = 'SHOW CREATE TABLE '.$seltable;
			$tablecreateresult = mysql_query($query);
			if ($tablecreateresult)
			{
				$row = mysql_fetch_assoc($tablecreateresult);
				$tablecreate = $row['Create Table'];
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
		$dbselform .= '	<legend>'.FORM_SELDB_LEGEND.'</legend>'."\n";
		$dbselform .= '	<label for="seldb" class="mainlabel">'.FORM_SELDB_OPT_LABEL.'</label> '."\n";
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
		$dbselform .= '	<input type="submit" name="dbselect" value="'.FORM_SUBMIT_SELDB.'" />'."\n";
		$dbselform .= '</fieldset>'."\n";
		$dbselform .= $this->FormClose();
	}
	else
	{
		$dbselmsg = '<p>'.sprintf(MSG_SINGLE_DB,$aDbList[0]).'</p>'."\n";
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
			$tableselform .= '	<legend>'.FORM_SELTABLE_LEGEND.'</legend>'."\n";
			$tableselform .= '	<label for="seltable" class="mainlabel">'.FORM_SELTABLE_OPT_LABEL.'</label> '."\n";
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
			$tableselform .= '	<input type="submit" name="tableselect" value="'.FORM_SUBMIT_SELTABLE.'" />'."\n";
			$tableselform .= '</fieldset>'."\n";
			$tableselform .= $this->FormClose();
		}
		else
		{
			$tableselmsg = '<p>'.sprintf(MSG_NO_TABLES,$seldb)."</p>\n";
		}
	}

	// build results
	if (isset($seldb))
	{
		$hdDbDdl = sprintf(HD_DB_CREATE_DDL,$seldb);
		if (isset($dbcreate))
		{
			$dbresult = $this->Format('%%(sql)'.$dbcreate.'%%');
		}
		else
		{
			$dbresult = '<p>'.sprintf(MSG_NO_DB_DDL,$seldb).'</p>'."\n";
		}
		if (isset($seltable))
		{
			$hdTableDdl = sprintf(HD_TABLE_CREATE_DDL,$seltable);
			if (isset($tablecreate))
			{
				$tableresult = $this->Format('%%(sql)'.$tablecreate.'%%');
			}
			else
			{
				$tableresult = '<p>'.sprintf(MSG_NO_TABLE_DDL,$seltable).'</p>'."\n";
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
?>