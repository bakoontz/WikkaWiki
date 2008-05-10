<?php
/**
 * Display, filter and search a list of referrers or referring sites for the current page or the site as a whole.
 *
 * Usage: append /referrers to the URL of the page
 *		add global=1 to specify referrers for the site instead of the current page
 *		add sites=1 to specify referrerring domains instead of full URLs
 *
 * This handler allows logged-in users to display, filter and search the referrer list for
 * the current page and for the whole site. Current search criteria include strings,
 * number of hits, reference period.
 *
 * @package		Handlers
 *
 * @author		{@link http://wikka.jsnx.com/DarTar Dario Taraborelli} - code cleanup, search/filter functionality added.
 * @author		{@link http://wikka.jsnx.com/JavaWoman JavaWoman} - more code cleanup, accessibility, integration with referrers_sites
 * @version		0.8
 * @since		Wikka 1.1.6.X
 *
 * @todo		for 1.0:
 *				- clean up debug code
 *				- remove LoadReferrers() from core
 *				- configurable choice hostname (NAME_GLOBAL) or 'this site' (config, installer)
 *				- configurable parameters for building days dropdown (config, installer)
 *				- configurable limit to express days as hours (config, installer)
 *				- build an index on the referrer column in the referrers table (installer)
 *				later:
 *				- transfer filter parameters as well so we cen redirect to the exact view we came from
 *				- (global) icons to represent each of the five views, small and larger versions (menu/page)
 *				- adapt FormOpen() to accept id; then fix form kluge here and in stylesheet
 *				- adapt text definitions to take singular-plural into account
 *				- add paging
 *				- turn list into form with checkboxes to allow mass blacklisting
 *
 * @input		string  $q  optional: string used to filter the referrers;
 *				default: NULL;
 *				the default can be overridden by providing a POST parameter 'q'
 * @input		integer $qo optional: determines the kind of search to be performed for string $q:
 *				1: search for all referrers containing a given string
 *				0: search for all referrers not containing a given string
 *				default: 1;
 *				the default can be overridden by providing a POST parameter 'qo'
 * @input		integer $h  optional: number of hits used to filter the referrers;
 *				default: 1;
 *				the default can be overridden by providing a POST parameter 'h'
 * @input		integer $ho optional: determines the kind of filter to be applied to $h:
 *				1: search for referrers with at least $h hits;
 *				0: search for referrers with no more than $h hits;
 *				default: 1;
 *				the default can be overridden by providing a POST parameter 'ho'
 * @input		integer $days  optional: number of days used to filter the referrers;
 *				default: 1;
 *				the default can be overridden by providing a POST parameter 'h'
 * @input		integer $global optional: switches between local/global referrers:
 *				1: display referrers for the whole site;
 *				0: display referrers for the current page;
 *				default: 0;
 *				the default can be overridden by providing a GET/POST parameter 'global'
 * @input		integer $sites  optional: switches between referring urls and domains
 *				1: display referring sites (domains);
 *				0: display referrers (URLs);
 *				default: 0;
 *				the default can be overridden by providing a GET/POST parameter 'sites'
 * @input		integer	$refdel	optional: number of referrer records deleted
 * @input		integer	$bladd	optional: number of blacklist records added
 */

// Utilities

/**
 * Build an array of numbers consisting of 'ranges' with increasing step size in each 'range'.
 *
 * A list of numbers like this is useful for instance for a dropdown to choose
 * a period expressed in number of days: a difference between 2 and 5 days may
 * be significant while that between 92 and 95 may not be.
 *
 * @author		{@link http://wikkawiki.org/JavaWoman JavaWoman}
 * @copyright	Copyright © 2005, Marjolein Katsma
 * @license		http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @version		1.0
 *
 * @param	mixed	$limits	required: single integer or array of integers;
 *					defines the upper limits of the ranges as well as the next step size
 * @param	int		$max	required: upper limit for the whole list
 *					(will be included if smaller than the largest limit)
 * @param	int		$firstinc optional: increment for the first range; default 1
 * @return	array	resulting list of numbers
 * @todo 	find better name
 * @todo 	move to core
 */
function optionRanges($limits, $max, $firstinc = 1)
{
	// initializations
	if (is_int($limits)) $limits = array($limits);
	if ($firstinc < 1) $firstinc = 1;
	$opts = array();
	$inc = $firstinc;

	// first element is the first increment
	$opts[] = $inc;
	// each $limit is the upper limit of a 'range'
	foreach ($limits as $limit)
	{
		for ($i = $inc + $inc; $i <= $limit && $i < $max; $i += $inc)
		{
			$opts[] = $i;
		}
		// we quit at $max, even if there are more $limit elements
		if ($limit >= $max)
		{
			// add $max to the list; then break out of the loop
			$opts[] = $max;
			break;
		}
		// when $limit is reached, it becomes the new start and increment for the next 'range'
		$inc = $limit;
	}

	return $opts;
}

// constants

define('DEBUG',FALSE);		# @@@ set TRUE to generate debugging output

define('SEARCH_LIKE','LIKE');			# search string operator
define('SEARCH_UNLIKE','NOT LIKE');		# search string operator
define('HITS_DEFAULT', '1');			# (was 0 for referrers, 1 for sites)
define('HITS_MIN_OPTION', '>=');
define('HITS_MAX_OPTION', '<=');

define('HOURS_LIMIT',2);				# days expressed as hours				@@@ could be made configurable
define('DAYS_MAX', $this->GetConfigValue('referrers_purge_time'));
define('DAYS_DEFAULT', '7');					# default period to retrieve	@@@ make configurable

$days_limits = array(7,30,90,365);				# ranges for days dropdown 		@@@ make configurable

// -------------------------------------

// initialize parameters

$q = NULL;								# search string
$qo = 1;								# search string option
$h = HITS_DEFAULT;						# hits number
$ho = 1;								# hits option
$days = DAYS_DEFAULT;					# period selection
$global = FALSE;						# global (site) or this page only
$sites = FALSE;							# referrers or referring sites
$refdel = NULL;							# referrer records deleted
$bladd = NULL;							# blacklist records added

// -------------------------------------

// initialize internal variables

$string_option = SEARCH_LIKE;			# LIKE or NOT LIKE
$hits_option = HITS_MIN_OPTION;			# MIN (>=) or MAX (<=)
$tag = $this->GetPageTag();
$isAdmin = $this->IsAdmin();
$loggedin = ($isAdmin) ? TRUE : (bool)$this->GetUser();
$pre = $this->config['table_prefix'];
$par = '';

$query = '';
$rows = 0;

// -------------------------------------

// User-interface strings

define('NAME_GLOBAL',$this->GetConfigValue('wakka_name'));

define('TITLE_REFERRERS','External pages linking to %s');
define('TITLE_SITES','Domains linking to %s');

define('REPORT_BLACKLIST','Referrer records removed: %d; blacklist records added: %d');

define('TOTAL_REFERRERS','Total: %d referrers linking to %s');
define('TOTAL_SITES','Total: %d referrers linking to %s');

// current target
# you can use NAME_GLOBAL instead of 'this site' if the site name is short enough
# @@@ JW: choice between 'this site' and NAME_GLOBAL could be set via configuration (later)
define('TARGET_GLOBAL','this site');
define('TARGET_PAGE',$tag);

// menus don't use current target but *possible* targets
define('MENU_REFERRERS','Referrers to %s');
define('MENU_SITES','Domains linking to %s');
define('MENU_REFERRERS_PAGE',sprintf(MENU_REFERRERS,TARGET_PAGE));
define('MENU_SITES_PAGE',sprintf(MENU_SITES,TARGET_PAGE));
define('MENU_REFERRERS_GLOBAL',sprintf(MENU_REFERRERS,TARGET_GLOBAL));
define('MENU_SITES_GLOBAL',sprintf(MENU_SITES,TARGET_GLOBAL));
define('MENU_BLACKLIST','Blacklisted sites');

define('FORM_LEGEND','Filter view:');
define('FORM_URL_OPT_REFERRERS','URL:');
define('FORM_URL_OPT_SITES','Domain:');
define('FORM_URL_OPT_TITLE','Select search option');
define('FORM_URL_OPT_1','containing');
define('FORM_URL_OPT_0','not containing');
define('FORM_URL_STRING_LABEL','string');
define('FORM_URL_STRING_TITLE','Enter a search string');
define('FORM_HITS_OPT_LABEL','Hits:');
define('FORM_HITS_OPT_TITLE','Select filter option');
define('FORM_HITS_OPT_1','at least');
define('FORM_HITS_OPT_0','no more than');
define('FORM_HITS_NUM_LABEL','hits');
define('FORM_HITS_NUM_TITLE','Enter number of hits');
define('FORM_DAYS_OPT_LABEL','Period:');
define('FORM_DAYS_OPT_TITLE','Select period in days');
define('FORM_DAYS_NUM_LABEL','days');
define('FORM_SUBMIT_URLS','Show referrers');
define('FORM_SUBMIT_SITES','Show referring domains');

define('LIST_PERIOD_HOURS',' (last %d hours)');
define('LIST_PERIOD_DAYS',' (last %d days)');
define('LIST_SUMMARY_REFERRERS','Filtered list of referrers, with hits%s, sorted by number of hits');
define('LIST_SUMMARY_SITES','Filtered list of referring sites, with hits%s, sorted by number of hits');
define('LIST_HEAD_HITS','Hits');
define('LIST_HEAD_ACTION','Action');
define('LIST_HEAD_LIST_REFERRERS','Referrers');
define('LIST_HEAD_LIST_SITES','Referring hosts');
define('LIST_REF_UNKNOWN','unknown');			# make sure the *exact* same string is used in the whitelist definition (delete_referrer.php)
define('LIST_ACTION_DESC',' and links to blacklist spammers');
define('LIST_ACTION_BLACKLIST','Blacklist');
define('LIST_ACTION_BLACKLIST_TITLE','Blacklist this domain');

define('LOGIN_NOTE','You need to login to see referring sites.');

// show result counts for target
define('LIST_RESULT_COUNTER_REFERRERS','Filtered result: %d referrers linking to %s');	# @@@ does not take account of singular
define('LIST_RESULT_COUNTER_SITES','Filtered result: %d domains linking to %s');		# @@@ does not take account of singular
define('LIST_RESULT_NONE','Filtered result:');
// show 'no result' summary for target
define('NONE_NOTE_REFERRERS','No referrers found linking to %s');
define('NONE_NOTE_SITES','No domains found linking to %s');


// -------------------------------------

// fetch and validate parameters

// get query string and comparison method
if (isset($_POST['q']))
{
	$tq = trim(strip_tags($_POST['q']));
	if ('' != $tq)
	{
		$q = mysql_real_escape_string($tq);
		if (isset($_POST['qo']))
		{
			$qo = ($_POST['qo'] == '1') ? 1 : 0;
			$string_option = ($qo == 1) ? SEARCH_LIKE : SEARCH_UNLIKE;
		}
	}
}
// get hits and min or max criteria
if (isset($_POST['h']))
{
	$h = (is_numeric($_POST['h'])) ? abs((int)$_POST['h']) : HITS_DEFAULT;	# cast to positive integer if numeric
}
if (isset($_POST['ho']))
{
	$ho = ($_POST['ho'] == '1') ? 1 : 0;
	$hits_option = ($ho == 1) ? HITS_MIN_OPTION : HITS_MAX_OPTION;
}
// get period, not longer than purge time
if (isset($_POST['days']))
{
	$days = (is_numeric($_POST['days'])) ? min(abs((int)$_POST['days']),DAYS_MAX) : DAYS_DEFAULT;
}
// get search target: page or site (global)
if (isset($_POST['global']))
{
	$global = (bool)$_POST['global'];
}
elseif (isset($_GET['global']))
{
	$global = (bool)$_GET['global'];
}
$iglobal = (int)$global;
// get precision: URLS (referrers) or referring sites (domains)
if (isset($_POST['sites']))
{
	$sites = (bool)$_POST['sites'];
}
elseif (isset($_GET['sites']))
{
	$sites = (bool)$_GET['sites'];
}
$isites = (int)$sites;
//get reported values (no validation needed, just cast to integer)
if (isset($_GET['refdel']))
{
	$refdel = (int)$_GET['refdel'];
	$bladd  = (isset($_GET['bladd'])) ? $bladd = (int)$_GET['bladd'] : 0;
}

// derive parameters for 'current' links
if (1 == $global)
{
	if ('' != $par) $par .= '&amp;';
	$par .= 'global=1';
}
if (1 == $sites)
{
	if ('' != $par) $par .= '&amp;';
	$par .= 'sites=1';
}

// -------------------------------------

// build query from chunks depending on criteria chosen

if ($loggedin)
{
	$query  = 'SELECT referrer';
	if ($sites)
	{
		// add 'host' = domain extracted from referrring URL using this algorithm:
		// find first char after http:// : LOCATE('//',referrer)+2
		// find first / after this: LOCATE('/',referrer,(LOCATE('//',referrer)+2)-1
		// calculate length: (LOCATE('/',referrer,(LOCATE('//',referrer)+2)-1) - (LOCATE('//',referrer)+2)
		// get host (standard): SUBSTRING(referrer FROM (LOCATE('//',referrer)+2) FOR ((LOCATE('/',referrer,(LOCATE('//',referrer)+2)-1) - (LOCATE('//',referrer)+2)))
		// *or*
		// get host (MySQL-specific): SUBSTRING(SUBSTRING_INDEX(referrer,'/',3) FROM (LOCATE('//',referrer)+1))
		$protocol_host = 'SUBSTRING_INDEX(referrer,"/",3)';		# protocol and host: everything before first single /
		$start_host = 'LOCATE("//",referrer)+2';				# start position of host: after //
		$query .= ', SUBSTRING('.$protocol_host.' FROM ('.$start_host.')) AS host';
		// NOTE: COUNT() cannot use a derived column name but it *can* take an expression
		$query .= ', COUNT(SUBSTRING('.$protocol_host.' FROM ('.$start_host.'))) AS num';
		$query .= ' FROM '.$pre.'referrers';
		if (!$global)
		{
			$query .= " WHERE page_tag = '".mysql_real_escape_string($tag)."'";
		}
		#if ($days != $max_days)
		if ($days != DAYS_MAX)
		{
			$query .= (!strpos($query,'WHERE')) ? ' WHERE' : ' AND';
			$query .= ' TO_DAYS(NOW()) - TO_DAYS(time) <= '.$days;			# filter by period
		}
		$query .= ' GROUP BY host ';
		if (isset($q))
		{
			$query .= ' HAVING host '.$string_option." '%".$q."%'";			# filter by string (derived column so we use HAVING)
		}
		if ($hits_option != HITS_MIN_OPTION || $h != 1)
		{
			$query .= (!strpos($query,'HAVING')) ? ' HAVING' : ' AND';
			$query .= ' num '.$hits_option.' '.$h;							# filter by hits number (derived column so we use HAVING)
		}
	}
	else
	{
		$query  = 'SELECT referrer';
		$query .= ', COUNT(referrer) AS num';
		$query .= ' FROM '.$pre.'referrers';
		if (!$global)
		{
			$query .= " WHERE page_tag = '".mysql_real_escape_string($tag)."'";
		}
		if (isset($q))
		{
			$query .= (!strpos($query,'WHERE')) ? ' WHERE' : ' AND';
			$query .= ' referrer '.$string_option." '%".$q."%'";			# filter by string
		}
		#if ($days != $max_days)
		if ($days != DAYS_MAX)
		{
			$query .= (!strpos($query,'WHERE')) ? ' WHERE' : ' AND';
			$query .= ' TO_DAYS(NOW()) - TO_DAYS(time) <= '.$days;			# filter by period
		}
		$query .= ' GROUP BY referrer ';
		if ($hits_option != HITS_MIN_OPTION || $h != 1)
		{
			$query .= ' HAVING num '.$hits_option.' '.$h;					# filter by hits number (derived column so we use HAVING)
		}
	}
	$query .= ' ORDER BY num DESC, referrer ASC';							# set order

	// get total number of referrers (NOT records!)
	$query_refcount  = 'SELECT COUNT(DISTINCT(referrer)) AS total';			# @@@ referrer column should be indexed to make this really efficient
	$query_refcount .= ' FROM '.$pre.'referrers';
	if (!$global)
	{
		$query_refcount .= " WHERE page_tag = '".mysql_real_escape_string($tag)."'";
	}
}

// -------------------------------------

// execute query (if logged in)

// @@@ NOTE: we don't use LoadReferrers any more since the query is now completely dynamically built
if ($loggedin)
{
	// execute query
	$referrers = $this->LoadAll($query);
	$totalrefs = $this->LoadSingle($query_refcount);
}

// -------------------------------------

// build UI elements

// define current target
$target = ($global) ? TARGET_GLOBAL : TARGET_PAGE;

// title
$title  = ($sites) ? sprintf(TITLE_SITES,$target) : sprintf(TITLE_REFERRERS,$target);
$title .= ($days <= HOURS_LIMIT) ? sprintf(LIST_PERIOD_HOURS,24*$days) : sprintf(LIST_PERIOD_DAYS,$days);

if ($isAdmin)
{
	if (isset($refdel)) $rptblacklisted = sprintf(REPORT_BLACKLIST,$refdel,$bladd);
}

if ($loggedin)
{
	// results
	$tot = $totalrefs['total'];
	$total = ($sites) ? sprintf(TOTAL_SITES,$tot,$target) : sprintf(TOTAL_REFERRERS,$tot,$target);
	$creferrers = count($referrers);
	if ($creferrers > 0)
	{
		$result = ($sites) ? sprintf(LIST_RESULT_COUNTER_SITES,$creferrers,$target) : sprintf(LIST_RESULT_COUNTER_REFERRERS,$creferrers,$target);
	}
	else
	{
		$result = LIST_RESULT_NONE;
	}

	// menu elements: prevent wrapping within element (these *don't* use current target!
	$menu_referrers_page	= str_replace(' ','&nbsp;',MENU_REFERRERS_PAGE);
	$menu_sites_page 		= str_replace(' ','&nbsp;',MENU_SITES_PAGE);
	$menu_referrers_global	= str_replace(' ','&nbsp;',MENU_REFERRERS_GLOBAL);
	$menu_sites_global		= str_replace(' ','&nbsp;',MENU_SITES_GLOBAL);
	$menu_blacklist			= str_replace(' ','&nbsp;',MENU_BLACKLIST);

	//menu links
	$m_referrers_page = '<a href="'.$this->Href('referrers').'">'.$menu_referrers_page.'</a>';
	$m_sites_page ='<a href="'.$this->Href('referrers','','sites=1').'">'.$menu_sites_page.'</a>';
	$m_referrers_global = '<a href="'.$this->Href('referrers','','global=1').'">'.$menu_referrers_global.'</a>';
	$m_sites_global = '<a href="'.$this->Href('referrers','','global=1&sites=1').'">'.$menu_sites_global.'</a>';
	$m_blacklist = '<a href="'.$this->Href('review_blacklist').'">'.$menu_blacklist.'</a>';

	//build menu
	$menu = '<ul class="menu">'."\n";
	$menu .= '<li'.((!$global && !$sites)? ' class="active"' : '').'>'.$m_referrers_page.'</li>'."\n";
	$menu .= '<li'.((!$global && $sites)? ' class="active"' : '').'>'.$m_sites_page.'</li>'."\n";
	$menu .= '<li'.(($global && !$sites)? ' class="active"' : '').'>'.$m_referrers_global.'</li>'."\n";
	$menu .= '<li'.(($global && $sites)? ' class="active"' : '').'>'.$m_sites_global.'</li>'."\n";
	$menu .= '<li>'.$m_blacklist.'</li>'."\n";
	$menu .= '</ul>'."\n";
	
	// days dropdown content
	$daysopts = optionRanges($days_limits,DAYS_MAX);

	// form
	$form  = $this->FormOpen('referrers','','POST');		# @@@ add parameter for id
	$form .= '<fieldset class="hidden">'."\n";
	$form .= '<input type="hidden" name="global" value="'.$iglobal.'" />'."\n";
	$form .= '<input type="hidden" name="sites" value="'.$isites.'" />'."\n";
	$form .= '</fieldset>'."\n";
	$form .= '<fieldset>'."\n";
	$form .= '<legend>'.FORM_LEGEND.'</legend>'."\n";

	$form .= '<label for="qo" class="mainlabel">'.(($sites) ? FORM_URL_OPT_SITES : FORM_URL_OPT_REFERRERS).'</label> '."\n";
	$form .= '<select name="qo" id="qo" title="'.FORM_URL_OPT_TITLE.'">'."\n";
	$form .= '<option value="1"'.(($qo == '1')? ' selected="selected"' : '').'>'.FORM_URL_OPT_1.'</option>'."\n";
	$form .= '<option value="0"'.(($qo == '0')? ' selected="selected"' : '').'>'.FORM_URL_OPT_0.'</option>'."\n";
	$form .= '</select> '."\n";
	$form .= '<label for="q">'.FORM_URL_STRING_LABEL.'</label> '."\n";
	$form .= '<input type ="text" name="q" id="q" title="'.FORM_URL_STRING_TITLE.'" size="10" maxlength="50" value="'.$q.'" />';

	$form .= '<br />'."\n";

	$form .= '<label for="ho" class="mainlabel">'.FORM_HITS_OPT_LABEL.'</label> '."\n";
	$form .= '<select name="ho" id="ho" title="'.FORM_HITS_OPT_TITLE.'">'."\n";
	$form .= '<option value="1"'.(($ho == '1')? ' selected="selected"' : '').'>'.FORM_HITS_OPT_1.'</option>'."\n";
	$form .= '<option value="0"'.(($ho == '0')? ' selected="selected"' : '').'>'.FORM_HITS_OPT_0.'</option>'."\n";
	$form .= '</select> '."\n";
	$form .= '<input type ="text" name="h" id="h" title="'.FORM_HITS_NUM_TITLE.'" size="5" maxlength="5" value="'.$h.'" />'."\n";
	$form .= ' <label for="h">'.FORM_HITS_NUM_LABEL.'</label>';

	$form .= '<br />'."\n";

	$form .= '<label for="days" class="mainlabel">'.FORM_DAYS_OPT_LABEL.'</label> '."\n";
	$form .= '<select name="days" id="days" title="'.FORM_DAYS_OPT_TITLE.'">'."\n";
	// build drop-down
	foreach ($daysopts as $opt)
	{
		$selected = ($opt == $days) ? ' selected="selected"' : '';
		$form .= '<option value="'.$opt.'"'.$selected.'>'.$opt.'</option>';
	}
	$form .= '</select> '."\n";
	$form .= ' <label for="h">'.FORM_DAYS_NUM_LABEL.'</label>'."\n";

	$form .= '</fieldset>'."\n";

	$form .= '<input type="submit" value="'.(($sites) ? FORM_SUBMIT_SITES : FORM_SUBMIT_URLS).'" accesskey="r" />'."\n";
	$form .= $this->FormClose();

	// referrers list with admin link for blacklisting
	if ($sites)
	{
		$summary  = ($isAdmin) ? sprintf(LIST_SUMMARY_SITES,LIST_ACTION_DESC) : sprintf(LIST_SUMMARY_SITES,'');
		$refshead = LIST_HEAD_LIST_SITES;
	}
	else
	{
		$summary  = ($isAdmin) ? sprintf(LIST_SUMMARY_REFERRERS,LIST_ACTION_DESC) : sprintf(LIST_SUMMARY_REFERRERS,'');
		$refshead = LIST_HEAD_LIST_REFERRERS;
	}
	if ($isAdmin)
	{
		$redir = ($global||$sites) ? $this->GetHandler().'&amp;'.$par : $this->GetHandler();	# ensure we return to the same view
		$par = ($sites) ? 'spam_site' : 'spam_link';
		$blacklisturl = $this->Href('delete_referrer','',$par.'=').'%s&amp;redirect=%s';
		$blacklink = '<a class="keys" href="'.$blacklisturl.'" title="'.LIST_ACTION_BLACKLIST_TITLE.'">'.LIST_ACTION_BLACKLIST.'</a>';
	}

	// ids - use constant for variable-content heading
	$idTitle = $this->makeId('hn','title');
	$idTotal = $this->makeId('hn','total');
	$idResult = $this->makeId('hn','result');
}

// -------------------------------------

// show user interface (pre-template)

echo '<div class="page">'."\n";
echo '<h3 id="'.$idTitle.'">'.$title.'</h3>'."\n";
# debug
if (DEBUG)
{
	echo 'Query (ref): '.$query.'<br />';
	echo 'Query (sites): '.$query_sites.'<br />';
	echo ($global) ? 'Global: TRUE<br />' : 'Global: FALSE<br />';
	echo ($sites)  ? 'Sites: TRUE<br />' : 'Sites: FALSE<br />';
}
# debug

if ($loggedin)
{
	if ($isAdmin && isset($refdel)) echo '<p class="notes">'.$rptblacklisted.'</p>';
	echo $menu."\n";
	echo '<div id="refbody">'."\n";
	echo '<h4 id="'.$idTotal.'">'.$total.'</h4>'."\n";
	echo '<div id="refform">'.$form.'</div>'."\n";	# @@@ kluge until FormOpen() is adapted: id should actually be on form itself and div not necessary!

	if ($creferrers != 0)
	{
		echo '<h4 id="'.$idResult.'">'.$result.'</h4>'."\n";
		echo '<table id="reflist" class="data" summary="'.$summary.'">'."\n";
		echo '<thead>';
		echo '<tr><th class="hits" scope="col">'.LIST_HEAD_HITS.'</th>';
		if ($isAdmin) echo '<th class="action" scope="col">'.LIST_HEAD_ACTION.'</th>';
		echo '<th class="refs" scope="col">'.$refshead.'</th></tr>'."\n";
		echo '</thead>'."\n";
		echo '<tbody>'."\n";

		foreach ($referrers as $referrer)
		{
			$hits	= $referrer['num'];
			if ($sites)
			{
				$ref	= $this->htmlspecialchars_ent($referrer['host']);
			}
			else
			{
				$ref	= $this->htmlspecialchars_ent($referrer['referrer']);
			}
			echo '<tr>';
			echo '<td class="hits">'.$hits.'</td>';
			if ($isAdmin) echo '<td class="action">'.sprintf($blacklink,$ref,$redir).'</td>';
			if ($sites)
			{
				echo '<td class="refs">'.$ref.'</td>';
			}
			else
			{
				echo '<td class="refs"><a href="'.$ref.'">'.$ref.'</a></td>';
			}
			echo '</tr>'."\n";
		}

		echo '</tbody>'."\n";
		echo '</table>'."\n";
	}
	else
	{
		echo '<h4 id="'.$idResult.'">'.$result.'</h4>'."\n";
		echo '<p><em class="error">'.(($sites) ? sprintf(NONE_NOTE_SITES,$target) : sprintf(NONE_NOTE_REFERRERS,$target)).'</em></p>'."\n";
	}
}
else
{
	echo '<p><strong>'.LOGIN_NOTE.'</strong></p>'."\n";
}
echo '</div>'."\n";
echo '</div>'."\n";
?>