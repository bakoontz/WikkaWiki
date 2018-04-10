<?php
/**
 * Functions used by the installer/upgrader.
 * 
 * @package	Setup
 * @version	$Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 */

/**
 * Establish DB connection. (Modified version of Wakka::Wakka())
 *
 * @uses	Config::$dbms_database
 * @uses	Config::$dbms_host
 * @uses	Config::$dbms_password
 * @uses	Config::$dbms_user
 * @uses	Config::$dbms_type
 *
 * @return	PDO		connection handle
 */
function db_connect($config)
{
	// Set up PDO object
	$dblink = null;
	if($config['dbms_type'] == 'sqlite') {
		$dsn = $config['dbms_type'] . ':' . $config['dbms_database'];
		try {
			$dblink = new PDO($dsn);
		} catch(PDOException $e) {
			die('<em class="error">'.T_("PDO connection error!").'</em>');
		}
	} else { // mysql default
		$dsn = $config['dbms_type'] . ':' .
			   'host=' . $config['dbms_host'] . ';' .
			   'dbname=' . $config['dbms_database'];
		$user = $config['dbms_user'];
		$pass = $config['dbms_password'];
		try {
			$dblink = new PDO($dsn, $user, $pass);
			$dblink->exec("SET NAMES utf8");
		} catch(PDOException $e) {
			print 'Exeption code: '.$e->getCode()."\n<br/>";
			print 'Exception message: '.$e->getMessage()."\n<br/>";
			die('PDO connection error: '.$dsn);
		}
	}
	// Don't emulate prepare statements (to prevent injection attacks)
	$dblink->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	// Throw an exception on PDO::query calls
	$dblink->setAttribute(PDO::ATTR_ERRMODE,
								PDO::ERRMODE_EXCEPTION);
	return $dblink;
}

/**
 * Send a query to the database.  (Modified version of Wakka::Query())
 *
 * If the query fails, the function will simply die().
 *
 * To prevent SQL injection attacks, all queries must be
 * parameterized!
 *
 * @param	string	$query	mandatory: the query to be executed.
 * @param   array   $params optional:  parameters for query (NULL if none)
 * @param	resource $dblink mandatory: connection to the database
 * @return	PDOStatement	the result of the query.
 *
 */
function db_query($query, $params=NULL, $dblink)
{
	if ('' == $dblink)
	{
		die('Must have valid instance of dblink in db_query!');
	}
	try {
		$result = $dblink->prepare($query);
		$result->execute($params);
	} catch (PDOException $e) {
		print $e;
		return FALSE;
	}
	return $result;
}

/**
 * Convenience method to return a db-specific string for now()
 *
 */
function db_now($dblink) {
	print $dblink->getAttribute(PDO::ATTR_DRIVER_NAME);
	if($dblink->getAttribute(PDO::ATTR_DRIVER_NAME) == 'sqlite') {
		return 'time(\'now\')';
	} else { // mysql default
		return 'now()';
	}
}

/**
 * Brute force copy routine for those sites that have copy() disabled
 *
 * @param string $file1	source file
 * @param string $file2	destination file
 * @return boolean true if successful, false if not
 * @link {http://www.php.net/manual/en/function.copy.php}
 */
function brute_copy($src, $dest)
{
	$context = @file_get_contents($src);
	$newfile = fopen($dest, "w");
	fwrite($newfile, $context);
	fclose($newfile);
	if(FALSE === $context)
		$status = false;
	else
		$status = true;
	return $status;
}

/**
 * Update content of a default page.
 * 
 * If $tag parameter is an array, it just passes elements of this array one by one to itself.
 * The value 'HomePage' is a special one: it will be replaced by the configured value $config['root_page'].
 * The content of the page is read at a file named with $tag, located in setup/default_pages.
 * @param mixed $tag	string or array of strings
 * @param resource $dblink
 * @param mixed $config
 * @param string $lang_defaults_path	mandatory: validated directory for language-specific default pages
 * @param string $lang_defaults_fallback_path	mandatory: validated directory for default pages in system default language
 * @access public
 * @return void
 * @todo avoid recursion: make a single tag into an array of one and then just loop over the tags
 */
function update_default_page($tag, $dblink, $config, $lang_defaults_path, $lang_defaults_fallback_path, $note='')
{
	if (is_array($tag))
	{
		foreach ($tag as $v)
		{
			update_default_page($v, $dblink, $config, $lang_defaults_path, $lang_defaults_fallback_path, $note);
		}
		return;
	}
	
	$filename = $tag;
	if ($tag == '_rootpage')
	{
		$tag = $config['root_page'];
		$filename = 'HomePage';
	}
	$admin_users = explode(',', $config['admin_users']);
	$admin_main_user = trim($admin_users[0]);
	//$txt_filepath = $lang_defaults_path.$filename.'.txt';
	$php_filepath = $lang_defaults_path.$filename.'.php';
	if (!file_exists($php_filepath) || !is_readable($php_filepath))
	{
		$php_filepath = $lang_defaults_fallback_path.$filename.'.php';
	}
	if (file_exists($php_filepath) && is_readable($php_filepath))
	{
		ob_start();
		include_once($php_filepath);
		$body = ob_get_contents();
		ob_end_clean();
		//$body = implode('', file($txt_filepath));
		db_query('update '.$config['table_prefix'].'pages set latest = "N" where tag = :tag', array(':tag' => $tag), $dblink);
		test (sprintf(__('Adding/Updating default page %s'.'...'), $tag),
			db_query('insert into '.$config['table_prefix'].'pages (tag, body, user, owner, time, latest, note) VALUES (:tag, :body, \'WikkaInstaller\', :owner, '.db_now($dblink).', \'Y\', :note)', array(':tag' => $tag, ':body' => $body, ':owner' => $admin_main_user, ':note' => $note), $dblink),
			'',
			0);
		// @@@ pick up any page-specific ACL here (look in both $lang_defaults_path and $lang_defaults_fallback_path)
	}
	else
	{
		test (sprintf(__('Adding/Updating default page %s'.'...'), $tag), false, sprintf(__('Default page not found or file not readable (%s, %s, %s)'), $tag, $php_filepath, $lang_defaults_path), 0);
	}
}

/**
 * __ .
 * i18n purpose: __() function is actually used to mark certain parts of the installer as translatable strings. This function doesn't echo
 * the string $s, it just returns it. If the string $s contains characters ---<, __() removes it and all strings after it, as if the 
 * serie ---< was a comment marker. Useful if you want to translate very little phrase like 'Do' in 2 situations where its translations may
 * be different! For example: __('Search---<Verb,action'); and __('Search---<Noun').
 * 
 * @param mixed $s 
 * @access public
 * @return void
 */
function __($s)
{
	return (preg_replace('/---<.*$/i', '', $s));
}

/**
 * Facility to echo a <select>...</select> for language packs availables. A simple check is performed on all 
 * subdirectories of the lang/ folder: if a file called xx.inc.php is found inside it, then, it's a valid
 * language pack subfolder. (To avoid treating some obscure system dependent special folders).
 * 
 * @access public
 * @return void
 */
function Language_selectbox($default_lang)
{
	echo '<select name="config[default_lang]">';
	/** @todo fill the array. */
	$human_lang = array (
		'en' => 'English',
		'fr' => 'Français',
		'de' => 'Deutsch',
		'vn' => 'Vietnamese',
		'pl' => 'Polski'
	);
	// use configured path
	$hdl = opendir('lang');
	while ($f = readdir($hdl))
	{
		if ($f[0] == '.') continue;
		if (file_exists('lang'.DIRECTORY_SEPARATOR.$f.DIRECTORY_SEPARATOR.$f.'.inc.php'))
		{
			echo "\n ".'<option value="'.$f.'"';
			if ($f == $default_lang) echo ' selected="selected"';
			echo '>'.(isset($human_lang[$f]) ? $human_lang[$f] : $f).'</option>';
		}
	}
	echo '</select>';
}
?>
